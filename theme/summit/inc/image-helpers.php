<?php
/**
 * Image Helpers — Summit Theme
 *
 * Provides fallback hero image rendering for articles with
 * external image URLs stored in _summit_hero_url meta.
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
		return get_the_post_thumbnail( $post_id, $size );
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
