<?php
/**
 * Batch 13 — Update Hero Image URLs from Mapping File
 *
 * Reads a source-agnostic JSON mapping of article slugs to valid image URLs,
 * and updates _summit_hero_url (and optionally _summit_hero_alt and
 * _summit_hero_credit) on the 21 Batch 11 cornerstone articles.
 *
 * Run via WP-CLI:
 *   wp eval-file plugins/summit-core/update-hero-urls.php --path=app/public
 *
 * Override mapping file path:
 *   wp eval-file plugins/summit-core/update-hero-urls.php /path/to/mapping.json --path=app/public
 *
 * Idempotent — safe to re-run. Skips posts where no field needs changing.
 * Aborts entirely if any target post cannot be resolved or validated.
 *
 * Dev-only. Local-only. Do not deploy to production.
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

echo "\n══════════════════════════════════════════════════════════\n";
echo "Batch 13 — Update Hero Image URLs\n";
echo "══════════════════════════════════════════════════════════\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 1: Load mapping file
// ─────────────────────────────────────────────────────────────────────────────
echo "── Preflight checks ──────────────────────────────────────\n\n";

// Allow CLI arg override; default to co-located data file.
$mapping_path = isset( $args[0] ) && ! empty( $args[0] )
	? $args[0]
	: dirname( __FILE__ ) . '/data/batch-13-hero-sources.json';

if ( ! file_exists( $mapping_path ) || ! is_readable( $mapping_path ) ) {
	echo "[ABORT] Mapping file missing or unreadable: {$mapping_path}\n";
	return;
}

$mapping_raw = file_get_contents( $mapping_path );
$mapping     = json_decode( $mapping_raw, true );

if ( ! is_array( $mapping ) || empty( $mapping ) ) {
	echo "[ABORT] Mapping file is empty or invalid JSON.\n";
	return;
}

echo "[OK] Mapping file loaded: " . count( $mapping ) . " entries from {$mapping_path}\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 2: Validate mapping entries
// ─────────────────────────────────────────────────────────────────────────────
$invalid_entries = [];
$mapping_by_slug = [];

foreach ( $mapping as $i => $entry ) {
	$wp_slug   = $entry['wp_slug'] ?? null;
	$image_url = $entry['image_url'] ?? null;

	if ( ! $wp_slug || ! $image_url ) {
		$invalid_entries[] = "Entry {$i}: missing wp_slug or image_url";
		continue;
	}

	if ( filter_var( $image_url, FILTER_VALIDATE_URL ) === false ) {
		$invalid_entries[] = "Entry {$i} ({$wp_slug}): invalid image_url";
		continue;
	}

	$mapping_by_slug[ $wp_slug ] = $entry;
}

if ( ! empty( $invalid_entries ) ) {
	echo "[ABORT] Mapping file contains invalid entries:\n";
	foreach ( $invalid_entries as $err ) {
		echo "  - {$err}\n";
	}
	return;
}

echo "[OK] All " . count( $mapping_by_slug ) . " mapping entries valid.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 3: Cross-reference against Batch 11 manifest
// ─────────────────────────────────────────────────────────────────────────────
$manifest_path = dirname( __FILE__ ) . '/data/batch-11-launch-set.json';
if ( ! file_exists( $manifest_path ) || ! is_readable( $manifest_path ) ) {
	echo "[ABORT] Batch 11 manifest missing or unreadable: {$manifest_path}\n";
	return;
}

$manifest = json_decode( file_get_contents( $manifest_path ), true );
if ( ! is_array( $manifest ) || empty( $manifest ) ) {
	echo "[ABORT] Batch 11 manifest is empty or invalid JSON.\n";
	return;
}

$manifest_slugs = [];
foreach ( $manifest as $entry ) {
	$manifest_slugs[] = $entry['wp_slug'];
}

// Every manifest slug must appear in the mapping.
$missing_in_mapping = array_diff( $manifest_slugs, array_keys( $mapping_by_slug ) );
if ( ! empty( $missing_in_mapping ) ) {
	echo "[ABORT] Mapping file is missing entries for these manifest slugs:\n";
	foreach ( $missing_in_mapping as $slug ) {
		echo "  - {$slug}\n";
	}
	return;
}

// Mapping must not contain slugs outside the manifest.
$extra_in_mapping = array_diff( array_keys( $mapping_by_slug ), $manifest_slugs );
if ( ! empty( $extra_in_mapping ) ) {
	echo "[ABORT] Mapping file contains slugs not in the Batch 11 manifest:\n";
	foreach ( $extra_in_mapping as $slug ) {
		echo "  - {$slug}\n";
	}
	return;
}

echo "[OK] Mapping covers all " . count( $manifest_slugs ) . " manifest slugs (no extras).\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 4: Resolve all target posts
// ─────────────────────────────────────────────────────────────────────────────
$targets       = []; // wp_slug => [ 'post_id' => int, 'import_slug' => string ]
$resolve_fails = [];

foreach ( $manifest as $m_entry ) {
	$wp_slug     = $m_entry['wp_slug'];
	$import_slug = $m_entry['import_slug'];

	$q = new WP_Query( [
		'post_type'      => 'article',
		'post_status'    => 'any',
		'post_name__in'  => [ $wp_slug ],
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	] );

	if ( empty( $q->posts ) ) {
		$resolve_fails[] = "{$wp_slug}: post not found";
		continue;
	}

	$post_id = $q->posts[0]->ID;

	// Confirm post type.
	if ( get_post_type( $post_id ) !== 'article' ) {
		$resolve_fails[] = "{$wp_slug} (ID {$post_id}): not an article post type";
		continue;
	}

	// Confirm _summit_import_slug meta exists.
	$stored_import_slug = get_post_meta( $post_id, '_summit_import_slug', true );
	if ( ! $stored_import_slug ) {
		$resolve_fails[] = "{$wp_slug} (ID {$post_id}): missing _summit_import_slug meta";
		continue;
	}

	// Confirm _summit_import_slug matches the expected value from the manifest.
	if ( $stored_import_slug !== $import_slug ) {
		$resolve_fails[] = "{$wp_slug} (ID {$post_id}): _summit_import_slug mismatch — expected '{$import_slug}', got '{$stored_import_slug}'";
		continue;
	}

	$targets[ $wp_slug ] = [
		'post_id'     => $post_id,
		'import_slug' => $import_slug,
	];
}

if ( ! empty( $resolve_fails ) ) {
	echo "[ABORT] Could not resolve all target posts:\n";
	foreach ( $resolve_fails as $fail ) {
		echo "  - {$fail}\n";
	}
	echo "  All " . count( $manifest_slugs ) . " manifest posts must exist, be article type, and carry matching _summit_import_slug meta.\n";
	return;
}

echo "[OK] All " . count( $targets ) . " target posts resolved and validated.\n";
echo "\n── All preflight checks passed ───────────────────────────\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// UPDATE PASS
// ─────────────────────────────────────────────────────────────────────────────
echo "── Updating hero image metadata ───────────────────────────\n\n";

$updated = 0;
$skipped = 0;
$failed  = 0;
$num     = 0;

foreach ( $targets as $wp_slug => $data ) {
	$post_id = $data['post_id'];
	$entry   = $mapping_by_slug[ $wp_slug ];
	$num++;

	echo "  [{$num}/" . count( $targets ) . "] {$wp_slug} (ID {$post_id})\n";

	$new_url    = $entry['image_url'];
	$new_alt    = isset( $entry['alt_text'] ) && $entry['alt_text'] !== '' ? $entry['alt_text'] : null;
	$new_credit = isset( $entry['credit'] ) && $entry['credit'] !== '' ? $entry['credit'] : null;

	$current_url    = get_post_meta( $post_id, '_summit_hero_url', true );
	$current_alt    = get_post_meta( $post_id, '_summit_hero_alt', true );
	$current_credit = get_post_meta( $post_id, '_summit_hero_credit', true );

	$changes = [];

	// Check URL.
	if ( $current_url !== $new_url ) {
		$changes[] = 'url';
	}

	// Check alt text.
	if ( $new_alt !== null && $current_alt !== $new_alt ) {
		$changes[] = 'alt';
	}

	// Check credit.
	if ( $new_credit !== null && $current_credit !== $new_credit ) {
		$changes[] = 'credit';
	}

	if ( empty( $changes ) ) {
		echo "    [skip] No fields need changing.\n";
		$skipped++;
		continue;
	}

	// Apply changes.
	if ( in_array( 'url', $changes, true ) ) {
		update_post_meta( $post_id, '_summit_hero_url', $new_url );
		echo "    [updated] _summit_hero_url\n";
	}

	if ( in_array( 'alt', $changes, true ) ) {
		update_post_meta( $post_id, '_summit_hero_alt', $new_alt );
		echo "    [updated] _summit_hero_alt\n";
	}

	if ( in_array( 'credit', $changes, true ) ) {
		update_post_meta( $post_id, '_summit_hero_credit', $new_credit );
		echo "    [updated] _summit_hero_credit\n";
	}

	echo "    [done] Changed: " . implode( ', ', $changes ) . "\n";
	$updated++;
}

// ─────────────────────────────────────────────────────────────────────────────
// SUMMARY
// ─────────────────────────────────────────────────────────────────────────────
echo "\n══════════════════════════════════════════════════════════\n";
echo "Batch 13 Hero URL Update Summary\n";
echo "══════════════════════════════════════════════════════════\n";
echo "  Processed:  {$num}\n";
echo "  Updated:    {$updated}\n";
echo "  Skipped:    {$skipped}\n";
echo "  Failed:     {$failed}\n";
echo "══════════════════════════════════════════════════════════\n\n";
