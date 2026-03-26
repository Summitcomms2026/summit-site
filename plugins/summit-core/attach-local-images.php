<?php
/**
 * Batch 13 — Attach Local Hero Images to Cornerstone Articles
 *
 * Reads a JSON mapping of article slugs to local image filenames,
 * imports each image into the WordPress media library, and attaches
 * it as the featured image + art_og_image for the target article.
 *
 * Targets only the 21 articles defined in batch-11-launch-set.json.
 *
 * Run via WP-CLI:
 *   wp eval-file plugins/summit-core/attach-local-images.php --path=app/public
 *
 * Override mapping file path (arg 0) and/or image directory (arg 1):
 *   wp eval-file plugins/summit-core/attach-local-images.php /path/to/mapping.json /path/to/images/ --path=app/public
 *
 * Idempotent — safe to re-run. Skips posts that already have both
 * a featured image and a populated art_og_image field.
 *
 * Dev-only. Local-only. Do not deploy to production.
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

echo "\n══════════════════════════════════════════════════════════\n";
echo "Batch 13 — Attach Local Hero Images\n";
echo "══════════════════════════════════════════════════════════\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 1: Load mapping file
// ─────────────────────────────────────────────────────────────────────────────
echo "── Preflight checks ──────────────────────────────────────\n\n";

// Allow CLI arg overrides; default to co-located paths.
$mapping_path = isset( $args[0] ) && ! empty( $args[0] )
	? $args[0]
	: dirname( __FILE__ ) . '/data/batch-13-hero-sources.json';

$images_dir = isset( $args[1] ) && ! empty( $args[1] )
	? rtrim( $args[1], '/' )
	: realpath( ABSPATH . '/../../local-assets/batch-13-hero-images' );

if ( ! $mapping_path || ! file_exists( $mapping_path ) || ! is_readable( $mapping_path ) ) {
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
// PREFLIGHT 2: Validate image directory
// ─────────────────────────────────────────────────────────────────────────────
if ( ! $images_dir || ! is_dir( $images_dir ) || ! is_readable( $images_dir ) ) {
	echo "[ABORT] Image directory missing or unreadable: {$images_dir}\n";
	return;
}

echo "[OK] Image directory: {$images_dir}\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 3: Validate mapping entries + local files
// ─────────────────────────────────────────────────────────────────────────────
$invalid_entries  = [];
$missing_files    = [];
$mapping_by_slug  = [];

foreach ( $mapping as $i => $entry ) {
	$wp_slug    = $entry['wp_slug'] ?? null;
	$image_file = $entry['image_file'] ?? null;

	if ( ! $wp_slug || ! $image_file ) {
		$invalid_entries[] = "Entry {$i}: missing wp_slug or image_file";
		continue;
	}

	$full_path = $images_dir . '/' . $image_file;
	if ( ! file_exists( $full_path ) || ! is_readable( $full_path ) ) {
		$missing_files[] = "{$wp_slug}: {$image_file} not found at {$full_path}";
		continue;
	}

	$mapping_by_slug[ $wp_slug ] = $entry;
	$mapping_by_slug[ $wp_slug ]['_full_path'] = $full_path;
}

if ( ! empty( $invalid_entries ) ) {
	echo "[ABORT] Mapping file contains invalid entries:\n";
	foreach ( $invalid_entries as $err ) {
		echo "  - {$err}\n";
	}
	return;
}

if ( ! empty( $missing_files ) ) {
	echo "[ABORT] Local image files missing:\n";
	foreach ( $missing_files as $err ) {
		echo "  - {$err}\n";
	}
	return;
}

echo "[OK] All " . count( $mapping_by_slug ) . " mapping entries valid, all local files exist.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 4: Cross-reference against Batch 11 manifest
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
$manifest_by_slug = [];
foreach ( $manifest as $entry ) {
	$manifest_slugs[] = $entry['wp_slug'];
	$manifest_by_slug[ $entry['wp_slug'] ] = $entry;
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
// PREFLIGHT 5: Resolve all target posts
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

	$post_id = $q->posts[0]->ID;

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

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 6: Media functions
// ─────────────────────────────────────────────────────────────────────────────
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

if ( ! function_exists( 'media_handle_sideload' ) ) {
	echo "[ABORT] media_handle_sideload() not available.\n";
	return;
}
echo "[OK] Media functions loaded.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 7: ACF
// ─────────────────────────────────────────────────────────────────────────────
if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
	echo "[ABORT] ACF is not active.\n";
	return;
}
echo "[OK] ACF is active.\n";

echo "\n── All preflight checks passed ───────────────────────────\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// ATTACHMENT PASS
// ─────────────────────────────────────────────────────────────────────────────
echo "── Processing articles ───────────────────────────────────\n\n";

$processed     = 0;
$attached      = 0;
$og_backfilled = 0;
$skipped       = 0;
$failed        = 0;
$failed_slugs  = [];

foreach ( $targets as $wp_slug => $data ) {
	$post_id = $data['post_id'];
	$entry   = $mapping_by_slug[ $wp_slug ];
	$processed++;

	echo "  [{$processed}/" . count( $targets ) . "] {$wp_slug} (ID {$post_id})\n";

	$has_thumbnail = has_post_thumbnail( $post_id );
	$has_og        = (bool) get_field( 'art_og_image', $post_id );

	// Metadata from mapping (applied in all non-skip states).
	$new_alt    = isset( $entry['alt_text'] ) && $entry['alt_text'] !== '' ? $entry['alt_text'] : null;
	$new_credit = isset( $entry['credit'] ) && $entry['credit'] !== '' ? $entry['credit'] : null;

	// ── State 1: Both exist → skip (but still update alt/credit if provided).
	if ( $has_thumbnail && $has_og ) {
		$meta_updated = false;

		if ( $new_alt !== null ) {
			$thumb_id    = get_post_thumbnail_id( $post_id );
			$current_alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
			if ( $current_alt !== $new_alt ) {
				update_post_meta( $thumb_id, '_wp_attachment_image_alt', $new_alt );
				$meta_updated = true;
			}
		}

		if ( $new_credit !== null ) {
			$current_credit = get_post_meta( $post_id, '_summit_hero_credit', true );
			if ( $current_credit !== $new_credit ) {
				update_post_meta( $post_id, '_summit_hero_credit', $new_credit );
				$meta_updated = true;
			}
		}

		if ( $meta_updated ) {
			echo "    [skip] Featured image and OG already set. Updated metadata.\n";
		} else {
			echo "    [skip] Featured image and OG already set.\n";
		}
		$skipped++;
		continue;
	}

	// ── State 2: Thumbnail exists but no OG → backfill.
	if ( $has_thumbnail && ! $has_og ) {
		$thumb_id = get_post_thumbnail_id( $post_id );
		if ( $thumb_id ) {
			update_field( 'art_og_image', $thumb_id, $post_id );
			echo "    [og_backfill] art_og_image set from existing thumbnail (attachment {$thumb_id}).\n";

			// Update alt/credit if provided.
			if ( $new_alt !== null ) {
				$current_alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
				if ( $current_alt !== $new_alt ) {
					update_post_meta( $thumb_id, '_wp_attachment_image_alt', $new_alt );
				}
			}
			if ( $new_credit !== null ) {
				$current_credit = get_post_meta( $post_id, '_summit_hero_credit', true );
				if ( $current_credit !== $new_credit ) {
					update_post_meta( $post_id, '_summit_hero_credit', $new_credit );
				}
			}

			$og_backfilled++;
		} else {
			echo "    [failed] has_post_thumbnail true but no thumbnail ID.\n";
			$failed++;
			$failed_slugs[] = $wp_slug . ': thumbnail ID missing';
		}
		continue;
	}

	// ── State 3: No thumbnail → attach from local file.
	$source_path = $entry['_full_path'];
	$image_file  = $entry['image_file'];

	// Copy to temp file (media_handle_sideload moves the file, so we must not
	// hand it our original asset).
	$tmp_dir  = get_temp_dir();
	$tmp_file = $tmp_dir . wp_unique_filename( $tmp_dir, $image_file );

	if ( ! copy( $source_path, $tmp_file ) ) {
		echo "    [failed] Could not copy {$image_file} to temp directory.\n";
		$failed++;
		$failed_slugs[] = $wp_slug . ': copy to temp failed';
		continue;
	}

	$file_array = [
		'name'     => sanitize_file_name( $image_file ),
		'tmp_name' => $tmp_file,
	];

	$attachment_id = media_handle_sideload( $file_array, $post_id );

	if ( is_wp_error( $attachment_id ) ) {
		echo "    [failed] Sideload error: {$attachment_id->get_error_message()}\n";
		@unlink( $tmp_file );
		$failed++;
		$failed_slugs[] = $wp_slug . ': ' . $attachment_id->get_error_message();
		continue;
	}

	// Set as featured image.
	set_post_thumbnail( $post_id, $attachment_id );

	// Set alt text.
	if ( $new_alt !== null ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $new_alt );
	}

	// Set credit on the article.
	if ( $new_credit !== null ) {
		update_post_meta( $post_id, '_summit_hero_credit', $new_credit );
	}

	// Set OG image.
	update_field( 'art_og_image', $attachment_id, $post_id );

	echo "    [attached] Attachment {$attachment_id} → featured image + OG image.\n";
	$attached++;
}

// ─────────────────────────────────────────────────────────────────────────────
// SUMMARY
// ─────────────────────────────────────────────────────────────────────────────
echo "\n══════════════════════════════════════════════════════════\n";
echo "Batch 13 Local Image Attachment Summary\n";
echo "══════════════════════════════════════════════════════════\n";
echo "  Processed:       {$processed}\n";
echo "  Attached:        {$attached}\n";
echo "  OG backfilled:   {$og_backfilled}\n";
echo "  Skipped:         {$skipped}\n";
echo "  Failed:          {$failed}\n";
echo "══════════════════════════════════════════════════════════\n";

if ( ! empty( $failed_slugs ) ) {
	echo "\nFailed slugs:\n";
	foreach ( $failed_slugs as $fs ) {
		echo "  - {$fs}\n";
	}
	echo "\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// POST-RUN SAFETY: Confirm no post status changed
// ─────────────────────────────────────────────────────────────────────────────
echo "── Post status verification ──────────────────────────────\n\n";

$status_issues = [];
foreach ( $targets as $wp_slug => $data ) {
	$post_id = $data['post_id'];
	$status  = get_post_status( $post_id );
	$label   = str_pad( $wp_slug, 65 );
	echo "  {$label} {$status}\n";
	if ( $status !== 'draft' ) {
		$status_issues[] = "{$wp_slug} (ID {$post_id}): status is '{$status}', expected 'draft'";
	}
}

echo "\n";
if ( ! empty( $status_issues ) ) {
	echo "[WARNING] Some posts are not in draft status:\n";
	foreach ( $status_issues as $issue ) {
		echo "  - {$issue}\n";
	}
} else {
	echo "[OK] All " . count( $targets ) . " posts remain in draft status.\n";
}

echo "\n══════════════════════════════════════════════════════════\n";
echo "Done.\n";
echo "══════════════════════════════════════════════════════════\n\n";
