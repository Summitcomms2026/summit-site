<?php
/**
 * Navigation functions placeholder.
 *
 * Required by functions.php. Stub file to prevent fatal errors
 * until navigation logic is implemented in a future batch.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check if the current page is the What We Do page or one of its child
 * service pages (Brand Strategy, Experience Design, Digital Transformation).
 *
 * Resolves the What We Do page by slug — does not hard-code an ID.
 *
 * @return bool
 */
function summit_is_what_we_do_section(): bool {
	if ( ! is_page() ) {
		return false;
	}

	if ( is_page( 'what-we-do' ) ) {
		return true;
	}

	$what_we_do = get_page_by_path( 'what-we-do' );
	if ( ! $what_we_do ) {
		return false;
	}

	return (int) wp_get_post_parent_id( get_the_ID() ) === (int) $what_we_do->ID;
}
