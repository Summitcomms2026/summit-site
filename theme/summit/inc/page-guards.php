<?php
/**
 * Page Dependency Guards
 *
 * Displays an admin notice when critical slug-dependent pages are missing.
 * These pages are required for page-{slug}.php templates to resolve.
 *
 * Informational only — no redirects, no auto-creation, no blocking.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_notices', function () {

    $required_pages = [
        'tastemakers'      => 'Tastemakers (podcast landing)',
        'future-of-luxury' => 'The Future of Luxury (editorial landing)',
        'work'             => 'Work Showcase (case study landing)',
    ];

    $missing = [];
    foreach ( $required_pages as $slug => $label ) {
        $page = get_page_by_path( $slug );
        if ( ! $page || $page->post_status !== 'publish' ) {
            $missing[] = $label . ' <code>/' . esc_html( $slug ) . '</code>';
        }
    }

    if ( empty( $missing ) ) {
        return;
    }

    printf(
        '<div class="notice notice-warning"><p><strong>Summit:</strong> The following landing pages are missing or unpublished. Their URLs will return 404 until a Page with the correct slug is created and published.</p><ul style="list-style:disc;margin-left:1.5em;">%s</ul></div>',
        '<li>' . implode( '</li><li>', $missing ) . '</li>'
    );
} );
