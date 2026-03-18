<?php
/**
 * Summit SEO Layer
 */

defined( 'ABSPATH' ) || exit;

function summit_seo_should_run(): bool {
    if ( is_admin() ) {
        return false;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return false;
    }

    if ( wp_doing_ajax() ) {
        return false;
    }

    return true;
}

function summit_seo_field_map(): array {
    return [
        'article' => [
            'prefix'    => 'art',
            'canonical' => true,
            'noindex'   => true,
            'og_type'   => 'article',
        ],
        'case_study' => [
            'prefix'    => 'cs',
            'canonical' => true,
            'noindex'   => true,
            'og_type'   => 'website',
        ],
        'podcast_episode' => [
            'prefix'    => 'ep',
            'canonical' => true,
            'noindex'   => false,
            'og_type'   => 'website',
        ],
        'podcast_season' => [
            'prefix'    => 'ps',
            'canonical' => false,
            'noindex'   => false,
            'og_type'   => 'website',
        ],
        'download' => [
            'prefix'    => 'dl',
            'canonical' => false,
            'noindex'   => false,
            'og_type'   => 'website',
        ],
    ];
}

add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
} );

function summit_seo_get_data(): array {
    static $cache = null;

    if ( $cache !== null ) {
        return $cache;
    }

    if ( ! summit_seo_should_run() || ! is_singular() ) {
        $cache = [];
        return $cache;
    }

    $post_id   = get_queried_object_id();
    $post_type = get_post_type( $post_id );
    $field_map = summit_seo_field_map();

    if ( ! $post_id || ! isset( $field_map[ $post_type ] ) ) {
        $cache = [];
        return $cache;
    }

    $config = $field_map[ $post_type ];
    $prefix = $config['prefix'];

    $seo_title = function_exists( 'get_field' ) ? (string) ( get_field( $prefix . '_seo_title', $post_id ) ?? '' ) : '';
    $meta_desc = function_exists( 'get_field' ) ? (string) ( get_field( $prefix . '_meta_desc', $post_id ) ?? '' ) : '';
    $og_image  = function_exists( 'get_field' ) ? get_field( $prefix . '_og_image', $post_id ) : false;

    $canonical = ( $config['canonical'] && function_exists( 'get_field' ) )
        ? (string) ( get_field( $prefix . '_canonical', $post_id ) ?? '' )
        : '';

    $noindex = ( $config['noindex'] && function_exists( 'get_field' ) )
        ? (bool) get_field( $prefix . '_noindex', $post_id )
        : false;

    if ( ! $meta_desc ) {
        $post_obj = get_post( $post_id );

        if ( $post_obj && ! empty( $post_obj->post_excerpt ) ) {
            $meta_desc = wp_strip_all_tags( $post_obj->post_excerpt );
        } elseif ( $post_obj ) {
            $meta_desc = wp_trim_words( wp_strip_all_tags( $post_obj->post_content ), 25, '…' );
        }
    }

    if ( mb_strlen( $meta_desc ) > 160 ) {
        $meta_desc = mb_substr( $meta_desc, 0, 157 ) . '…';
    }

    $og_image_url = '';
    if ( is_array( $og_image ) && ! empty( $og_image['url'] ) ) {
        $og_image_url = $og_image['url'];
    } elseif ( has_post_thumbnail( $post_id ) ) {
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
        if ( $thumb ) {
            $og_image_url = $thumb[0];
        }
    }

    $cache = [
        'post_id'      => $post_id,
        'post_type'    => $post_type,
        'seo_title'    => $seo_title,
        'meta_desc'    => $meta_desc,
        'og_image_url' => $og_image_url,
        'canonical'    => $canonical,
        'noindex'      => $noindex,
        'og_type'      => $config['og_type'],
    ];

    return $cache;
}

add_filter( 'pre_get_document_title', function ( $title ) {
    if ( ! summit_seo_should_run() ) {
        return $title;
    }

    $data = summit_seo_get_data();
    if ( empty( $data ) ) {
        return $title;
    }

    $page_title = $data['seo_title'] ?: get_the_title( $data['post_id'] );

    if ( ! $page_title ) {
        return $title;
    }

    return $page_title . ' | ' . get_bloginfo( 'name' );
} );

add_action( 'wp_head', function () {
    if ( ! summit_seo_should_run() ) {
        return;
    }

    $data = summit_seo_get_data();
    if ( empty( $data ) ) {
        return;
    }

    $title       = $data['seo_title'] ?: get_the_title( $data['post_id'] );
    $description = $data['meta_desc'];
    $canonical   = $data['canonical'] ?: get_permalink( $data['post_id'] );
    $image       = $data['og_image_url'];
    $url         = get_permalink( $data['post_id'] );

    if ( $description ) {
        echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    }

    if ( $data['noindex'] ) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }

    echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr( $data['og_type'] ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";

    if ( $description ) {
        echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
    }

    if ( $image ) {
        echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
    }

    echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
}, 2 );