<?php
/**
 * Navigation Functions
 * Location: inc/nav-functions.php
 * Required from: functions.php
 *
 * Provides:
 *   summit_get_menu_articles()  — cached WP_Query for mega menu editorial feed
 *   summit_body_classes()       — additional body classes for nav state context
 *   summit_nav_enqueue_script() — enqueues the navigation JS
 *
 * Governance:
 *   THEME_ARCHITECTURE.md     — inc/ is for supporting theme logic
 *   summit_build_system.md    — content model belongs in plugin; helpers here
 *   summit_component_library.md — mega menu editorial feed spec
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;


// ─────────────────────────────────────────────────────────────────────────────
// Mega menu editorial feed
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Retrieve 3 recent published articles for the mega menu editorial column.
 *
 * Cached with the WordPress object cache for the duration of the page request.
 * Cache key: 'summit_menu_articles'
 * Cache group: 'summit_nav'
 *
 * Returns an array of WP_Post objects, or an empty array if none exist.
 * An empty array is safe — header.php checks before rendering the column.
 *
 * @return WP_Post[]
 */
function summit_get_menu_articles(): array {
    $cached = wp_cache_get( 'summit_menu_articles', 'summit_nav' );
    if ( is_array( $cached ) ) {
        return $cached;
    }

    $query = new WP_Query( [
        'post_type'      => 'article',
        'post_status'    => 'publish',
        'posts_per_page' => 3,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,  // Skip COUNT(*) — we don't need pagination
    ] );

    $articles = $query->posts ?: [];
    wp_reset_postdata();

    wp_cache_set( 'summit_menu_articles', $articles, 'summit_nav' );

    return $articles;
}

function summit_estimated_read_time( $post_id ): int {
    $content = get_post_field( 'post_content', $post_id );

    if ( ! $content ) {
        return 1;
    }

    $word_count = str_word_count( wp_strip_all_tags( $content ) );

    return max( 1, (int) ceil( $word_count / 200 ) );
}


// ─────────────────────────────────────────────────────────────────────────────
// Body class additions
// ─────────────────────────────────────────────────────────────────────────────

add_filter( 'body_class', 'summit_body_classes' );

/**
 * Add contextual body classes to support nav state CSS.
 *
 * summit-has-articles — added when the mega menu editorial feed has content.
 *                       Allows CSS to adjust nav column layout.
 *
 * @param  string[] $classes  Existing body classes from WordPress
 * @return string[]           Modified body classes
 */
function summit_body_classes( array $classes ): array {
    $articles = summit_get_menu_articles();
    if ( ! empty( $articles ) ) {
        $classes[] = 'summit-has-articles';
    }
    return $classes;
}


// ─────────────────────────────────────────────────────────────────────────────
// Navigation script enqueue
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'summit_nav_enqueue_script' );

/**
 * Enqueue the navigation JavaScript.
 *
 * The script handles:
 *   - Menu button open / close toggle
 *   - aria-expanded / aria-hidden state management
 *   - Focus trap inside the mega menu overlay
 *   - Escape key to close
 *   - Backdrop click to close
 *   - Scroll-lock on body when menu is open
 *
 * The script is loaded in the footer (5th parameter = true) to avoid
 * render-blocking. It has no external dependencies.
 *
 * @return void
 */
function summit_nav_enqueue_script(): void {
    wp_enqueue_script(
        'summit-nav',
        get_template_directory_uri() . '/assets/js/nav.js',
        [],
        wp_get_theme()->get( 'Version' ),
        true  // Load in footer
    );
}
