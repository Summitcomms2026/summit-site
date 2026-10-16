<?php
/**
 * Image Helpers — Summit Theme
 *
 * Article hero rendering, and the site's one missing-image fallback.
 *
 * A featured image whose file is not on this server (an attachment whose
 * upload is missing) is never shown as a broken image. Wherever a template
 * prints the thumbnail — get_the_post_thumbnail(), summit_article_hero(),
 * or summit_image_fallback() directly (the mega menu) — the image is
 * replaced by an intentional fallback inside the same frame: a dark tonal
 * field with a fine compass-star line, sized by the frame it fills, so no
 * component's geometry changes. Styles: assets/css/components.css.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render an article hero image, falling back to external URL meta.
 *
 * @param int|null $post_id Post ID (defaults to current post).
 * @param string   $size    WordPress image size for attached thumbnails.
 * @return string  HTML img tag or empty string.
 */
function summit_article_hero( $post_id = null, $size = 'full' ) {
	$post_id = $post_id ?: get_the_ID();

	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail( $post_id, $size ); // falls back via the post_thumbnail_html filter
	}

	$url = get_post_meta( $post_id, '_summit_hero_url', true );
	$alt = get_post_meta( $post_id, '_summit_hero_alt', true );

	if ( $url ) {
		return sprintf(
			'<img src="%s" alt="%s" loading="lazy" />',
			esc_url( $url ),
			esc_attr( $alt ?: '' )
		);
	}

	return '';
}

/**
 * Check whether an article has any hero image (attached or external URL).
 *
 * @param int|null $post_id Post ID (defaults to current post).
 * @return bool
 */
function summit_has_article_hero( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();

	if ( has_post_thumbnail( $post_id ) ) {
		return true;
	}

	return (bool) get_post_meta( $post_id, '_summit_hero_url', true );
}

/**
 * Does an attachment's file exist on this server?
 *
 * @param int $attachment_id Attachment ID.
 * @return bool
 */
function summit_attachment_file_exists( $attachment_id ) {
	$attachment_id = (int) $attachment_id;
	if ( ! $attachment_id ) {
		return false;
	}
	static $known = [];
	if ( ! isset( $known[ $attachment_id ] ) ) {
		$file                    = (string) get_attached_file( $attachment_id );
		$known[ $attachment_id ] = '' !== $file && is_readable( $file );
	}
	return $known[ $attachment_id ];
}

/**
 * The missing-image fallback: fills whatever frame it is placed in.
 *
 * @return string
 */
function summit_image_fallback() {
	return '<span class="summit-image-fallback" aria-hidden="true">'
		. '<svg class="summit-image-fallback__mark" viewBox="0 0 40 40" focusable="false">'
		. '<path d="M20 2v36M2 20h36M7.3 7.3l25.4 25.4M32.7 7.3 7.3 32.7"/>'
		. '<circle cx="20" cy="20" r="6.5"/>'
		. '</svg></span>';
}

/**
 * Replace a featured image whose file is missing with the fallback.
 */
add_filter( 'post_thumbnail_html', static function ( $html, $post_id, $thumbnail_id ) {
	if ( $thumbnail_id && ! summit_attachment_file_exists( $thumbnail_id ) ) {
		return summit_image_fallback();
	}
	return $html;
}, 10, 3 );
