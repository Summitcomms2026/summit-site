<?php
/**
 * Summit Theme Functions
 */

defined( 'ABSPATH' ) || exit;

require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/nav-functions.php';

add_action( 'wp_enqueue_scripts', 'summit_enqueue_styles' );

function summit_enqueue_styles(): void {

    $v = wp_get_theme()->get( 'Version' );
    $p = get_template_directory_uri() . '/assets/css/';

    wp_enqueue_style( 'summit-tokens',     $p . 'tokens.css',     [],                      $v );
    wp_enqueue_style( 'summit-base',       $p . 'base.css',       [ 'summit-tokens' ],     $v );
    wp_enqueue_style( 'summit-layout',     $p . 'layout.css',     [ 'summit-base' ],       $v );
    wp_enqueue_style( 'summit-components', $p . 'components.css', [ 'summit-layout' ],     $v );
    wp_enqueue_style( 'summit-cards',      $p . 'cards.css',      [ 'summit-components' ], $v );
    wp_enqueue_style( 'summit-singles',    $p . 'singles.css',    [ 'summit-cards' ],      $v );
    wp_enqueue_style( 'summit-utilities',  $p . 'utilities.css',  [ 'summit-singles' ],    $v );
    wp_enqueue_style( 'summit-navigation', $p . 'navigation.css', [ 'summit-utilities' ],  $v );

    if ( is_front_page() ) {
        wp_enqueue_style( 'summit-homepage', $p . 'homepage.css', [ 'summit-components' ], $v );
    }

}