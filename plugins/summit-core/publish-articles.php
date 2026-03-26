<?php
/**
 * Batch 14 — Publish Cornerstone Articles
 *
 * All-or-nothing publication of the 21 Batch 11 cornerstone articles.
 * Reads the Batch 11 manifest, validates every slug, then publishes
 * the entire set or aborts if any target fails validation.
 *
 * Also enforces the featured article flag (art_featured) on the
 * manifest-designated featured article, clearing it from all others.
 *
 * Run via WP-CLI:
 *   wp eval-file plugins/summit-core/publish-articles.php --path=app/public
 *
 * Revert to draft:
 *   wp eval-file plugins/summit-core/publish-articles.php --unpublish --path=app/public
 *
 * Idempotent — safe to re-run. Already-published articles are counted
 * but not modified.
 *
 * Dev-only. Local-only. Do not deploy to production.
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

echo "\n══════════════════════════════════════════════════════════\n";
echo "Batch 14 — Publish Cornerstone Articles\n";
echo "══════════════════════════════════════════════════════════\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 1: Parse CLI flags
// ─────────────────────────────────────────────────────────────────────────────
echo "── Preflight checks ──────────────────────────────────────\n\n";

$mode = 'publish';
if ( isset( $args ) && is_array( $args ) ) {
	foreach ( $args as $arg ) {
		if ( $arg === '--unpublish' ) {
			$mode = 'unpublish';
		}
	}
}
echo "[OK] Mode: {$mode}\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 2: Load Batch 11 manifest
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

echo "[OK] Manifest loaded: " . count( $manifest ) . " entries.\n";

// Identify featured slug from the manifest.
$featured_slug = null;
foreach ( $manifest as $entry ) {
	if ( ! empty( $entry['featured'] ) ) {
		$featured_slug = $entry['wp_slug'];
		break;
	}
}

if ( ! $featured_slug ) {
	echo "[ABORT] No featured article designated in manifest.\n";
	return;
}

echo "[OK] Featured article: {$featured_slug}\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 3: ACF availability
// ─────────────────────────────────────────────────────────────────────────────
if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
	echo "[ABORT] ACF is not active.\n";
	return;
}
echo "[OK] ACF is active.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 4: Confirm archive hero uses art_featured
// ─────────────────────────────────────────────────────────────────────────────
$archive_template = realpath( ABSPATH . '/../../theme/summit/archive-article.php' );
if ( $archive_template && is_readable( $archive_template ) ) {
	$archive_src = file_get_contents( $archive_template );
	if ( strpos( $archive_src, "'art_featured'" ) !== false || strpos( $archive_src, '"art_featured"' ) !== false ) {
		echo "[OK] Archive hero template queries art_featured field.\n";
	} else {
		echo "[ABORT] Archive hero template does NOT query art_featured.\n";
		echo "  Cannot safely enforce featured flag without confirming the control path.\n";
		echo "  File checked: {$archive_template}\n";
		return;
	}
} else {
	echo "[WARN] Could not read archive template to confirm art_featured usage.\n";
	echo "  Proceeding — but verify archive hero manually after publication.\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 5: Snapshot pre-existing published articles (for final QA)
// ─────────────────────────────────────────────────────────────────────────────
$pre_existing_q = new WP_Query( [
	'post_type'      => 'article',
	'post_status'    => 'publish',
	'posts_per_page' => 200,
	'no_found_rows'  => true,
	'fields'         => 'ids',
] );
$pre_existing_published_ids = $pre_existing_q->posts ?: [];
wp_reset_postdata();

echo "[OK] Pre-existing published articles: " . count( $pre_existing_published_ids ) . "\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 6: Resolve all target posts
// ─────────────────────────────────────────────────────────────────────────────
$targets       = [];
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

	$post    = $q->posts[0];
	$post_id = $post->ID;

	if ( get_post_type( $post_id ) !== 'article' ) {
		$resolve_fails[] = "{$wp_slug} (ID {$post_id}): not an article post type";
		continue;
	}

	$stored_import_slug = get_post_meta( $post_id, '_summit_import_slug', true );
	if ( ! $stored_import_slug ) {
		$resolve_fails[] = "{$wp_slug} (ID {$post_id}): missing _summit_import_slug meta";
		continue;
	}

	if ( $stored_import_slug !== $import_slug ) {
		$resolve_fails[] = "{$wp_slug} (ID {$post_id}): _summit_import_slug mismatch — expected '{$import_slug}', got '{$stored_import_slug}'";
		continue;
	}

	$targets[ $wp_slug ] = [
		'post_id'        => $post_id,
		'import_slug'    => $import_slug,
		'current_status' => $post->post_status,
	];
}

if ( ! empty( $resolve_fails ) ) {
	echo "[ABORT] Could not resolve all target posts:\n";
	foreach ( $resolve_fails as $fail ) {
		echo "  - {$fail}\n";
	}
	echo "  All " . count( $manifest ) . " manifest posts must exist, be article type, and carry matching _summit_import_slug meta.\n";
	return;
}

echo "[OK] All " . count( $targets ) . " target posts resolved and validated.\n";

echo "\n── All preflight checks passed ───────────────────────────\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// PUBLICATION / UNPUBLICATION PASS
// ─────────────────────────────────────────────────────────────────────────────
if ( $mode === 'publish' ) {
	echo "── Publishing articles ───────────────────────────────────\n\n";
} else {
	echo "── Reverting articles to draft ───────────────────────────\n\n";
}

$changed     = 0;
$already_ok  = 0;
$failed      = 0;
$failed_list = [];

$target_status = ( $mode === 'publish' ) ? 'publish' : 'draft';

$i = 0;
foreach ( $targets as $wp_slug => $data ) {
	$i++;
	$post_id = $data['post_id'];
	$current = $data['current_status'];
	$label   = "[{$i}/" . count( $targets ) . "] {$wp_slug} (ID {$post_id})";

	if ( $current === $target_status ) {
		echo "  {$label} → already {$target_status}\n";
		$already_ok++;
		continue;
	}

	$result = wp_update_post( [
		'ID'          => $post_id,
		'post_status' => $target_status,
	], true );

	if ( is_wp_error( $result ) ) {
		echo "  {$label} → FAILED: {$result->get_error_message()}\n";
		$failed++;
		$failed_list[] = "{$wp_slug}: {$result->get_error_message()}";
	} else {
		echo "  {$label} → {$target_status}\n";
		$changed++;
	}
}

// ─────────────────────────────────────────────────────────────────────────────
// FEATURED FLAG PASS
// ─────────────────────────────────────────────────────────────────────────────
echo "\n── Featured article enforcement ──────────────────────────\n\n";

$featured_set   = false;
$cleared_count  = 0;

foreach ( $targets as $wp_slug => $data ) {
	$post_id     = $data['post_id'];
	$current_val = get_field( 'art_featured', $post_id );

	if ( $wp_slug === $featured_slug ) {
		if ( $current_val ) {
			echo "  [featured] {$wp_slug} (ID {$post_id}) — already set.\n";
		} else {
			update_field( 'art_featured', 1, $post_id );
			echo "  [featured] {$wp_slug} (ID {$post_id}) — SET.\n";
		}
		$featured_set = true;
	} else {
		if ( $current_val ) {
			update_field( 'art_featured', 0, $post_id );
			echo "  [cleared]  {$wp_slug} (ID {$post_id}) — was featured, now cleared.\n";
			$cleared_count++;
		}
	}
}

if ( ! $featured_set ) {
	echo "[WARNING] Featured slug '{$featured_slug}' was not found in targets.\n";
}

if ( $cleared_count > 0 ) {
	echo "  Cleared stale featured flags from {$cleared_count} article(s).\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// POST-RUN VERIFICATION
// ─────────────────────────────────────────────────────────────────────────────
echo "\n── Post-run status verification ──────────────────────────\n\n";

$verify_ok     = 0;
$verify_issues = [];

foreach ( $targets as $wp_slug => $data ) {
	$post_id  = $data['post_id'];
	$status   = get_post_status( $post_id );
	$featured = get_field( 'art_featured', $post_id ) ? 'YES' : '   ';
	$slug_pad = str_pad( $wp_slug, 65 );

	echo "  {$slug_pad} {$status}  featured: {$featured}\n";

	if ( $status === $target_status ) {
		$verify_ok++;
	} else {
		$verify_issues[] = "{$wp_slug} (ID {$post_id}): expected '{$target_status}', got '{$status}'";
	}
}

echo "\n";
if ( ! empty( $verify_issues ) ) {
	echo "[WARNING] Status verification issues:\n";
	foreach ( $verify_issues as $issue ) {
		echo "  - {$issue}\n";
	}
} else {
	echo "[OK] All " . count( $targets ) . " posts verified as '{$target_status}'.\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// POST-RUN SAFETY: Check pre-existing articles untouched
// ─────────────────────────────────────────────────────────────────────────────
echo "\n── Pre-existing article safety check ─────────────────────\n\n";

$target_ids = array_column( $targets, 'post_id' );

// Re-query current published articles.
$post_run_q = new WP_Query( [
	'post_type'      => 'article',
	'post_status'    => 'publish',
	'posts_per_page' => 200,
	'no_found_rows'  => true,
	'fields'         => 'ids',
] );
$post_run_published_ids = $post_run_q->posts ?: [];
wp_reset_postdata();

// Check that every pre-existing published ID is still published.
$pre_existing_untouched = [];
$pre_existing_changed   = [];
foreach ( $pre_existing_published_ids as $pre_id ) {
	if ( in_array( $pre_id, $target_ids, true ) ) {
		continue; // This is one of our 21 targets; expected to change.
	}
	$pre_post = get_post( $pre_id );
	if ( $pre_post ) {
		if ( $pre_post->post_status === 'publish' ) {
			$pre_existing_untouched[] = $pre_post->post_name;
		} else {
			$pre_existing_changed[] = $pre_post->post_name . " (now: {$pre_post->post_status})";
		}
	}
}

if ( ! empty( $pre_existing_untouched ) ) {
	echo "Pre-existing published articles (untouched):\n";
	foreach ( $pre_existing_untouched as $slug ) {
		echo "  - {$slug}\n";
	}
}

if ( ! empty( $pre_existing_changed ) ) {
	echo "\n[WARNING] Pre-existing articles with changed status:\n";
	foreach ( $pre_existing_changed as $info ) {
		echo "  - {$info}\n";
	}
} else {
	echo "[OK] No pre-existing published articles were affected.\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// SUMMARY
// ─────────────────────────────────────────────────────────────────────────────
echo "\n══════════════════════════════════════════════════════════\n";
echo "Batch 14 Publication Summary\n";
echo "══════════════════════════════════════════════════════════\n";
echo "  Mode:              {$mode}\n";
echo "  Target status:     {$target_status}\n";
echo "  Total targets:     " . count( $targets ) . "\n";
echo "  Changed:           {$changed}\n";
echo "  Already {$target_status}:" . str_repeat( ' ', max( 1, 7 - strlen( $target_status ) ) ) . "{$already_ok}\n";
echo "  Failed:            {$failed}\n";
echo "  Featured article:  {$featured_slug}\n";
echo "  Pre-existing safe: " . count( $pre_existing_untouched ) . "\n";
echo "══════════════════════════════════════════════════════════\n";

if ( ! empty( $failed_list ) ) {
	echo "\nFailed slugs:\n";
	foreach ( $failed_list as $f ) {
		echo "  - {$f}\n";
	}
}

echo "\n── Published slugs ──────────────────────────────────────\n\n";
foreach ( $targets as $wp_slug => $data ) {
	$status = get_post_status( $data['post_id'] );
	if ( $status === 'publish' ) {
		echo "  {$wp_slug}\n";
	}
}

echo "\n── Draft slugs ──────────────────────────────────────────\n\n";
$draft_found = false;
foreach ( $targets as $wp_slug => $data ) {
	$status = get_post_status( $data['post_id'] );
	if ( $status !== 'publish' ) {
		echo "  {$wp_slug} ({$status})\n";
		$draft_found = true;
	}
}
if ( ! $draft_found ) {
	echo "  (none)\n";
}

echo "\n══════════════════════════════════════════════════════════\n";
echo "Done.\n";
echo "══════════════════════════════════════════════════════════\n\n";
