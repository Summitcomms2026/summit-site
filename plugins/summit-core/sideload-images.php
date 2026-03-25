<?php
/**
 * Batch 12 — Cornerstone Image Sideload + OG Hardening
 *
 * Downloads external hero images for the Batch 11 cornerstone articles,
 * attaches them as WordPress featured images, and populates art_og_image.
 *
 * Targets only the 21 articles defined in batch-11-launch-set.json.
 *
 * Run via WP-CLI:
 *   wp eval-file plugins/summit-core/sideload-images.php --path=app/public
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
echo "Batch 12 — Cornerstone Image Sideload\n";
echo "══════════════════════════════════════════════════════════\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 1: Manifest
// ─────────────────────────────────────────────────────────────────────────────
echo "── Preflight checks ──────────────────────────────────────\n\n";

$manifest_path = dirname( __FILE__ ) . '/data/batch-11-launch-set.json';
if ( ! file_exists( $manifest_path ) || ! is_readable( $manifest_path ) ) {
	echo "[ABORT] Manifest missing or unreadable: {$manifest_path}\n";
	return;
}

$manifest = json_decode( file_get_contents( $manifest_path ), true );
if ( ! is_array( $manifest ) || empty( $manifest ) ) {
	echo "[ABORT] Manifest is empty or invalid JSON.\n";
	return;
}
echo "[OK] Manifest loaded: " . count( $manifest ) . " entries.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 2: ACF
// ─────────────────────────────────────────────────────────────────────────────
if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
	echo "[ABORT] ACF is not active.\n";
	return;
}
echo "[OK] ACF is active.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 3: Media functions
// ─────────────────────────────────────────────────────────────────────────────
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

if ( ! function_exists( 'media_handle_sideload' ) ) {
	echo "[ABORT] media_handle_sideload() not available after loading includes.\n";
	return;
}
if ( ! function_exists( 'download_url' ) ) {
	echo "[ABORT] download_url() not available after loading includes.\n";
	return;
}
echo "[OK] Media functions loaded.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 4: Resolve all target posts
// ─────────────────────────────────────────────────────────────────────────────
$targets = []; // wp_slug => [ 'post_id' => int, 'entry' => array ]

$missing_posts = [];
foreach ( $manifest as $entry ) {
	$wp_slug = rtrim( $entry['wp_slug'] ?? $entry['import_slug'], '-' );

	$q = new WP_Query( [
		'post_type'      => 'article',
		'post_status'    => 'any',
		'post_name__in'  => [ $wp_slug ],
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	] );
	$posts = $q->posts;

	if ( empty( $posts ) ) {
		$missing_posts[] = $wp_slug;
		continue;
	}

	$post_id = $posts[0]->ID;

	// Confirm this is a Batch 11 import (has _summit_import_slug meta).
	$import_slug = get_post_meta( $post_id, '_summit_import_slug', true );
	if ( ! $import_slug ) {
		$missing_posts[] = $wp_slug . ' (no _summit_import_slug meta)';
		continue;
	}

	$targets[ $wp_slug ] = [
		'post_id' => $post_id,
		'entry'   => $entry,
	];
}

if ( ! empty( $missing_posts ) ) {
	echo "[ABORT] Could not resolve all manifest target posts:\n";
	foreach ( $missing_posts as $m ) {
		echo "  - {$m}\n";
	}
	echo "  All 21 manifest posts must exist and carry _summit_import_slug meta.\n";
	return;
}

echo "[OK] All " . count( $targets ) . " target posts resolved.\n";
echo "\n── All preflight checks passed ───────────────────────────\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// SIDELOAD PASS
// ─────────────────────────────────────────────────────────────────────────────
echo "── Processing articles ───────────────────────────────────\n\n";

$processed     = 0;
$sideloaded    = 0;
$og_backfilled = 0;
$skipped       = 0;
$failed        = 0;
$failed_slugs  = [];

foreach ( $targets as $wp_slug => $data ) {
	$post_id = $data['post_id'];
	$processed++;
	$num = $processed;

	echo "  [{$num}/" . count( $targets ) . "] {$wp_slug} (ID {$post_id})\n";

	$has_thumbnail = has_post_thumbnail( $post_id );
	$has_og        = (bool) get_field( 'art_og_image', $post_id );

	// ── State 1: Both exist → skip.
	if ( $has_thumbnail && $has_og ) {
		echo "    [skip] Featured image and OG image already set.\n";
		$skipped++;
		continue;
	}

	// ── State 2: Thumbnail exists but no OG → backfill.
	if ( $has_thumbnail && ! $has_og ) {
		$thumb_id = get_post_thumbnail_id( $post_id );
		if ( $thumb_id ) {
			update_field( 'art_og_image', $thumb_id, $post_id );
			echo "    [og_backfill] art_og_image set from existing thumbnail (attachment {$thumb_id}).\n";
			$og_backfilled++;
		} else {
			echo "    [failed] has_post_thumbnail true but no thumbnail ID.\n";
			$failed++;
			$failed_slugs[] = $wp_slug . ': thumbnail ID missing';
		}
		continue;
	}

	// ── State 3: No thumbnail → sideload.
	$hero_url = get_post_meta( $post_id, '_summit_hero_url', true );
	$hero_alt = get_post_meta( $post_id, '_summit_hero_alt', true );

	if ( ! $hero_url ) {
		echo "    [failed] No _summit_hero_url meta. Cannot sideload.\n";
		$failed++;
		$failed_slugs[] = $wp_slug . ': no hero URL';
		continue;
	}

	// Download the image to a temp file.
	$tmp_file = download_url( $hero_url, 30 );
	if ( is_wp_error( $tmp_file ) ) {
		echo "    [failed] Download error: {$tmp_file->get_error_message()}\n";
		$failed++;
		$failed_slugs[] = $wp_slug . ': ' . $tmp_file->get_error_message();
		continue;
	}

	// Determine filename from URL.
	$url_path  = wp_parse_url( $hero_url, PHP_URL_PATH );
	$filename  = $url_path ? basename( $url_path ) : 'hero-image.jpg';
	// Ensure the filename has an image extension.
	$extension = pathinfo( $filename, PATHINFO_EXTENSION );
	if ( ! in_array( strtolower( $extension ), [ 'jpg', 'jpeg', 'png', 'gif', 'webp' ], true ) ) {
		// Try to detect from file content.
		$mime = wp_check_filetype( $tmp_file );
		if ( ! empty( $mime['ext'] ) ) {
			$filename .= '.' . $mime['ext'];
		} else {
			$filename .= '.jpg';
		}
	}

	$file_array = [
		'name'     => sanitize_file_name( $filename ),
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

	// Attach as featured image.
	set_post_thumbnail( $post_id, $attachment_id );

	// Set alt text.
	if ( $hero_alt ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $hero_alt );
	}

	// Set OG image.
	update_field( 'art_og_image', $attachment_id, $post_id );

	echo "    [sideloaded] Attachment {$attachment_id} → featured image + OG image.\n";
	$sideloaded++;
}

// ─────────────────────────────────────────────────────────────────────────────
// SUMMARY
// ─────────────────────────────────────────────────────────────────────────────
echo "\n══════════════════════════════════════════════════════════\n";
echo "Batch 12 Sideload Summary\n";
echo "══════════════════════════════════════════════════════════\n";
echo "  Processed:       {$processed}\n";
echo "  Sideloaded:      {$sideloaded}\n";
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
