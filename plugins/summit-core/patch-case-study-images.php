<?php
/**
 * Batch 15 Patch — Attach Case Study Hero Images + Archive Curation
 *
 * Attaches featured images and populates cs_og_image for the 3
 * published case studies. Moves maison-example to draft to keep
 * the archive curated.
 *
 * Run via WP-CLI:
 *   wp eval-file plugins/summit-core/patch-case-study-images.php --path=app/public
 *
 * Dev-only. Local-only.
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

echo "\n══════════════════════════════════════════════════════════\n";
echo "Batch 15 Patch — Case Study Images + Archive Curation\n";
echo "══════════════════════════════════════════════════════════\n\n";

// ── Media functions.
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

if ( ! function_exists( 'media_handle_sideload' ) ) {
	echo "[ABORT] media_handle_sideload() not available.\n";
	return;
}

if ( ! function_exists( 'update_field' ) ) {
	echo "[ABORT] ACF not active.\n";
	return;
}

$images_dir = realpath( ABSPATH . '/../../local-assets/batch-15-case-study-images' );
if ( ! $images_dir || ! is_dir( $images_dir ) ) {
	echo "[ABORT] Image directory not found: expected local-assets/batch-15-case-study-images/\n";
	return;
}

echo "[OK] Image directory: {$images_dir}\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// 1. ATTACH IMAGES TO 3 PUBLISHED CASE STUDIES
// ─────────────────────────────────────────────────────────────────────────────
echo "── Attaching hero images ──────────────────────────────────\n\n";

$case_slugs = [
	'kaleida-studios',
	'tastemakers-podcast',
	'leading-plastic-surgeons-films',
];

foreach ( $case_slugs as $slug ) {
	$post = get_page_by_path( $slug, OBJECT, 'case_study' );
	if ( ! $post ) {
		echo "  [FAIL] {$slug}: post not found\n";
		continue;
	}

	$post_id   = $post->ID;
	$has_thumb = has_post_thumbnail( $post_id );
	$has_og    = (bool) get_field( 'cs_og_image', $post_id );

	if ( $has_thumb && $has_og ) {
		echo "  [skip] {$slug}: already has featured image and OG image\n";
		continue;
	}

	$image_file = $slug . '.jpg';
	$source     = $images_dir . '/' . $image_file;

	if ( ! file_exists( $source ) ) {
		echo "  [FAIL] {$slug}: image not found at {$source}\n";
		continue;
	}

	// Copy to temp (media_handle_sideload moves the file).
	$tmp_dir  = get_temp_dir();
	$tmp_file = $tmp_dir . wp_unique_filename( $tmp_dir, $image_file );

	if ( ! copy( $source, $tmp_file ) ) {
		echo "  [FAIL] {$slug}: could not copy to temp\n";
		continue;
	}

	$file_array = [
		'name'     => sanitize_file_name( $image_file ),
		'tmp_name' => $tmp_file,
	];

	$attachment_id = media_handle_sideload( $file_array, $post_id );

	if ( is_wp_error( $attachment_id ) ) {
		echo "  [FAIL] {$slug}: " . $attachment_id->get_error_message() . "\n";
		@unlink( $tmp_file );
		continue;
	}

	// Set as featured image.
	set_post_thumbnail( $post_id, $attachment_id );

	// Set OG image.
	update_field( 'cs_og_image', $attachment_id, $post_id );

	echo "  [OK]   {$slug}: attachment {$attachment_id} → featured image + cs_og_image\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// 2. MOVE MAISON-EXAMPLE TO DRAFT
// ─────────────────────────────────────────────────────────────────────────────
echo "\n── Archive curation ─────────────────────────────────────\n\n";

$maison = get_page_by_path( 'maison-example', OBJECT, 'case_study' );
if ( $maison ) {
	if ( $maison->post_status === 'draft' ) {
		echo "  [skip] maison-example: already draft\n";
	} else {
		$result = wp_update_post( [ 'ID' => $maison->ID, 'post_status' => 'draft' ], true );
		if ( is_wp_error( $result ) ) {
			echo "  [FAIL] maison-example: " . $result->get_error_message() . "\n";
		} else {
			echo "  [OK]   maison-example (ID {$maison->ID}): moved to draft\n";
			echo "  Approach: moved to draft (not deleted). Can be re-published when\n";
			echo "  it has full editorial content and hero imagery.\n";
		}
	}
} else {
	echo "  [skip] maison-example: not found\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// 3. VERIFICATION
// ─────────────────────────────────────────────────────────────────────────────
echo "\n── Verification ─────────────────────────────────────────\n\n";

foreach ( $case_slugs as $slug ) {
	$post = get_page_by_path( $slug, OBJECT, 'case_study' );
	if ( ! $post ) { continue; }

	$post_id   = $post->ID;
	$has_thumb = has_post_thumbnail( $post_id ) ? 'YES' : 'MISSING';

	$og = get_field( 'cs_og_image', $post_id );
	$og_url    = '';
	$og_status = 'MISSING';
	$og_source = 'none';

	if ( is_array( $og ) && ! empty( $og['url'] ) ) {
		$og_url    = $og['url'];
		$og_source = 'cs_og_image';
		$og_status = ( strpos( $og_url, '/wp-content/uploads/' ) !== false ) ? 'LOCAL' : 'EXTERNAL';
	} elseif ( has_post_thumbnail( $post_id ) ) {
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
		if ( $thumb ) {
			$og_url    = $thumb[0];
			$og_source = 'fallback';
			$og_status = 'FALLBACK';
		}
	}

	// Simulate rendered og:image tag.
	$rendered = $og_url
		? '<meta property="og:image" content="' . esc_url( $og_url ) . '">'
		: '(none)';

	echo "  {$slug}:\n";
	echo "    thumbnail:  {$has_thumb}\n";
	echo "    og source:  {$og_source}\n";
	echo "    og status:  {$og_status}\n";
	echo "    og url:     {$og_url}\n";
	echo "    rendered:   {$rendered}\n\n";
}

// Archive composition.
echo "── Final /work-showcase composition ───────────────────────\n\n";

$aq = new WP_Query( [
	'post_type'      => 'case_study',
	'post_status'    => 'publish',
	'posts_per_page' => 20,
	'orderby'        => 'date',
	'order'          => 'DESC',
] );

$i = 0;
while ( $aq->have_posts() ) {
	$aq->the_post();
	$i++;
	echo "  {$i}. " . get_post_field( 'post_name' ) . "\n";
}
wp_reset_postdata();
echo "  Total published case studies: {$aq->found_posts}\n";

echo "\n══════════════════════════════════════════════════════════\n";
echo "Done.\n";
echo "══════════════════════════════════════════════════════════\n\n";
