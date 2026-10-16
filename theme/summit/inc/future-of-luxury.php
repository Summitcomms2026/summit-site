<?php
/**
 * The Future of Luxury — editorial helpers
 * Location: inc/future-of-luxury.php
 *
 * Shared by the archive (archive-article.php), the reading template
 * (single-article.php), the topic view (taxonomy-article_category.php) and
 * their parts in parts/fol/. Read-only: nothing here alters the article
 * content model or the queries used by the homepage and the mega-menu.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is this request part of The Future of Luxury?
 */
function summit_fol_is_view(): bool {
    return is_post_type_archive( 'article' ) || is_singular( 'article' ) || is_tax( 'article_category' );
}

/**
 * An article's image, only when its file is actually present.
 *
 * Uses the featured image alone, and only if the attachment's file exists
 * on this server, so a missing upload never becomes a broken frame. Remote
 * hero URLs (_summit_hero_url) are not used here.
 *
 * @return string <img> markup or ''.
 */
function summit_fol_image( int $post_id, string $size = 'large', array $attr = [] ): string {
    $thumb_id = (int) get_post_thumbnail_id( $post_id );
    if ( ! $thumb_id ) {
        return '';
    }
    $file = (string) get_attached_file( $thumb_id );
    if ( '' === $file || ! is_readable( $file ) ) {
        return '';
    }
    return (string) wp_get_attachment_image( $thumb_id, $size, false, array_merge( [ 'alt' => '' ], $attr ) );
}

/**
 * The article's first category, or null.
 */
function summit_fol_category( int $post_id ): ?WP_Term {
    $terms = get_the_terms( $post_id, 'article_category' );
    return ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms[0] : null;
}

/**
 * The standfirst: the article's own, else its excerpt.
 */
function summit_fol_standfirst( int $post_id ): string {
    $standfirst = function_exists( 'get_field' ) ? (string) get_field( 'art_standfirst', $post_id ) : '';
    if ( '' === trim( $standfirst ) ) {
        $standfirst = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : '';
    }
    return trim( wp_strip_all_tags( $standfirst ) );
}

/**
 * Reading time in minutes, from the article's own field only.
 */
function summit_fol_read_time( int $post_id ): int {
    return function_exists( 'get_field' ) ? absint( get_field( 'art_read_time', $post_id ) ) : 0;
}

/**
 * The author's public name, or '' where the article has no author.
 */
function summit_fol_author( int $post_id ): string {
    $author_id = (int) get_post_field( 'post_author', $post_id );
    if ( ! $author_id || ! get_userdata( $author_id ) ) {
        return '';
    }
    $display = (string) get_the_author_meta( 'display_name', $author_id );
    $login   = (string) get_the_author_meta( 'user_login', $author_id );
    if ( $display && $display !== $login ) {
        return $display;
    }
    $full = trim( get_the_author_meta( 'first_name', $author_id ) . ' ' . get_the_author_meta( 'last_name', $author_id ) );
    return $full;
}

/**
 * The lead article: the newest flagged as featured (art_featured), else
 * the newest published.
 */
function summit_fol_lead(): ?WP_Post {
    $base = [
        'post_type'      => 'article',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => [ 'date' => 'DESC', 'ID' => 'DESC' ],
        'no_found_rows'  => true,
    ];
    $lead = get_posts( $base + [ 'meta_key' => 'art_featured', 'meta_value' => '1' ] );
    if ( ! $lead ) {
        $lead = get_posts( $base );
    }
    return $lead[0] ?? null;
}

/**
 * Two or three related articles: the editor's own choices first
 * (art_related_articles), then the same category, then the most recent.
 *
 * @return WP_Post[]
 */
function summit_fol_related( int $post_id, int $count = 3 ): array {
    $chosen = function_exists( 'get_field' ) ? get_field( 'art_related_articles', $post_id ) : [];
    $chosen = is_array( $chosen ) ? $chosen : ( $chosen ? [ $chosen ] : [] );

    $related = [];
    foreach ( $chosen as $item ) {
        $item = get_post( $item );
        if ( $item && 'article' === $item->post_type && 'publish' === $item->post_status && $item->ID !== $post_id ) {
            $related[ $item->ID ] = $item;
        }
    }

    $fill = static function ( array $extra ) use ( &$related, $post_id, $count ): void {
        if ( count( $related ) >= $count ) {
            return;
        }
        $more = get_posts( array_merge( [
            'post_type'      => 'article',
            'post_status'    => 'publish',
            'posts_per_page' => $count - count( $related ),
            'post__not_in'   => array_merge( [ $post_id ], array_keys( $related ) ),
            'orderby'        => [ 'date' => 'DESC', 'ID' => 'DESC' ],
            'no_found_rows'  => true,
        ], $extra ) );
        foreach ( $more as $item ) {
            $related[ $item->ID ] = $item;
        }
    };

    $category = summit_fol_category( $post_id );
    if ( $category ) {
        $fill( [ 'tax_query' => [ [ 'taxonomy' => 'article_category', 'terms' => $category->term_id ] ] ] );
    }
    $fill( [] );

    return array_slice( array_values( $related ), 0, $count );
}

/**
 * Enqueue the section's stylesheet and script on its views.
 */
add_action( 'wp_enqueue_scripts', static function (): void {
    if ( ! summit_fol_is_view() ) {
        return;
    }
    $v   = wp_get_theme()->get( 'Version' );
    $uri = get_template_directory_uri();
    wp_enqueue_style( 'summit-fol', $uri . '/assets/css/fol.css', [ 'summit-navigation' ], $v );
    wp_enqueue_script( 'summit-fol', $uri . '/assets/js/fol.js', [], $v, true );
    wp_localize_script( 'summit-fol', 'summitFol', [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ] );
} );

/**
 * Body classes for the section's views.
 */
add_filter( 'body_class', static function ( array $classes ): array {
    if ( summit_fol_is_view() ) {
        $classes[] = 'summit-fol';
    }
    return $classes;
} );

/**
 * Mark the document as scripted before first paint, so inline essay images
 * can stay invisible until they have loaded (see assets/js/fol.js).
 */
add_action( 'wp_head', static function (): void {
    if ( is_singular( 'article' ) ) {
        echo "<script>document.documentElement.classList.add('fol-js');</script>\n";
    }
}, 1 );
