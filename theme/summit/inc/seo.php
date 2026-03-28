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

/**
 * Remove WordPress default canonical when our SEO layer provides one.
 *
 * WordPress outputs its own canonical via rel_canonical() on singular pages.
 * Our layer outputs a canonical for CPTs and standard pages, creating duplicates.
 */
add_action( 'wp', function () {
    if ( ! summit_seo_should_run() ) {
        return;
    }

    if ( is_singular() ) {
        remove_action( 'wp_head', 'rel_canonical' );
    }
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

function summit_seo_get_taxonomy_data(): array {
    static $cache = null;

    if ( $cache !== null ) {
        return $cache;
    }

    if ( ! summit_seo_should_run() ) {
        $cache = [];
        return $cache;
    }

    if ( ! is_tax( 'article_category' ) && ! is_tax( 'podcast_theme' ) ) {
        $cache = [];
        return $cache;
    }

    $term = get_queried_object();
    if ( ! $term instanceof WP_Term ) {
        $cache = [];
        return $cache;
    }

    $term_link = get_term_link( $term );
    if ( is_wp_error( $term_link ) ) {
        $cache = [];
        return $cache;
    }

    if ( $term->description ) {
        $description = wp_strip_all_tags( $term->description );
    } elseif ( $term->taxonomy === 'article_category' ) {
        $description = 'Insights on ' . $term->name . ' from Summit';
    } else {
        $description = 'Episodes exploring ' . $term->name . ' — Tastemakers';
    }

    if ( mb_strlen( $description ) > 160 ) {
        $description = mb_substr( $description, 0, 157 ) . '…';
    }

    $cache = [
        'term_name'   => $term->name,
        'description' => $description,
        'url'         => $term_link,
    ];

    return $cache;
}

/**
 * Strategic page description map.
 *
 * Hardcoded descriptions for key pages whose content is baked into
 * templates rather than editable via the editor.
 */
function summit_seo_page_descriptions(): array {
    return [
        'front-page'            => 'Summit Communication Group — brand strategy, experience design and digital transformation for luxury brands.',
        'who-we-are'            => 'The people, philosophy and operating model behind Summit Communication Group.',
        'what-we-do'            => 'Brand strategy, experience design and digital transformation for luxury brands.',
        'brand-strategy'        => 'Strategic brand positioning and identity for luxury and premium brands.',
        'experience-design'     => 'Customer experience design for luxury brands — from concept to execution.',
        'digital-transformation' => 'Digital transformation strategy and implementation for luxury brands.',
        'design-tomorrow'       => 'Start a conversation with Summit about your next brand, experience or digital project.',
    ];
}

/**
 * SEO data for standard pages and the front page.
 *
 * Priority: strategic page map → excerpt → trimmed content.
 */
function summit_seo_get_page_data(): array {
    static $cache = null;

    if ( $cache !== null ) {
        return $cache;
    }

    if ( ! summit_seo_should_run() ) {
        $cache = [];
        return $cache;
    }

    if ( ! is_page() && ! is_front_page() ) {
        $cache = [];
        return $cache;
    }

    // Skip pages that are handled by the CPT field map.
    $post_id   = get_queried_object_id();
    $post_type = get_post_type( $post_id );
    if ( $post_id && isset( summit_seo_field_map()[ $post_type ] ) ) {
        $cache = [];
        return $cache;
    }

    $page_map    = summit_seo_page_descriptions();
    $meta_desc   = '';
    $page_slug   = is_front_page() ? 'front-page' : get_post_field( 'post_name', $post_id );

    // 1. Strategic page map.
    if ( isset( $page_map[ $page_slug ] ) ) {
        $meta_desc = $page_map[ $page_slug ];
    }

    // 2. Excerpt fallback.
    if ( ! $meta_desc ) {
        $post_obj = get_post( $post_id );
        if ( $post_obj && ! empty( $post_obj->post_excerpt ) ) {
            $meta_desc = wp_strip_all_tags( $post_obj->post_excerpt );
        } elseif ( $post_obj ) {
            // 3. Trimmed content fallback.
            $meta_desc = wp_trim_words( wp_strip_all_tags( $post_obj->post_content ), 25, '…' );
        }
    }

    if ( mb_strlen( $meta_desc ) > 160 ) {
        $meta_desc = mb_substr( $meta_desc, 0, 157 ) . '…';
    }

    $og_image_url = '';
    if ( $post_id && has_post_thumbnail( $post_id ) ) {
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
        if ( $thumb ) {
            $og_image_url = $thumb[0];
        }
    }

    $cache = [
        'title'        => get_the_title( $post_id ),
        'meta_desc'    => $meta_desc,
        'url'          => is_front_page() ? home_url( '/' ) : get_permalink( $post_id ),
        'og_image_url' => $og_image_url,
    ];

    return $cache;
}

/**
 * SEO data for CPT archive pages.
 */
function summit_seo_get_archive_data(): array {
    static $cache = null;

    if ( $cache !== null ) {
        return $cache;
    }

    if ( ! summit_seo_should_run() || ! is_post_type_archive() ) {
        $cache = [];
        return $cache;
    }

    $post_type = get_queried_object()->name ?? '';

    $archive_descriptions = [
        'case_study'     => 'Selected work from Summit — brand strategy, experience design and digital transformation for luxury brands.',
        'article'        => 'Insights on luxury branding, experience design and digital transformation from Summit Communication Group.',
        'podcast_season' => 'Tastemakers — conversations with the people shaping luxury, culture and craft.',
        'download'       => 'Guides and resources from Summit Communication Group.',
    ];

    $description = $archive_descriptions[ $post_type ] ?? '';
    $title       = post_type_archive_title( '', false );
    $url         = get_post_type_archive_link( $post_type );

    if ( ! $title || ! $url ) {
        $cache = [];
        return $cache;
    }

    $cache = [
        'title'       => $title,
        'description' => $description,
        'url'         => $url,
    ];

    return $cache;
}

add_filter( 'pre_get_document_title', function ( $title ) {
    if ( ! summit_seo_should_run() ) {
        return $title;
    }

    $tax_data = summit_seo_get_taxonomy_data();
    if ( ! empty( $tax_data ) ) {
        return $tax_data['term_name'] . ' | ' . get_bloginfo( 'name' );
    }

    $data = summit_seo_get_data();
    if ( ! empty( $data ) ) {
        $page_title = $data['seo_title'] ?: get_the_title( $data['post_id'] );
        if ( $page_title ) {
            return $page_title . ' | ' . get_bloginfo( 'name' );
        }
    }

    $archive_data = summit_seo_get_archive_data();
    if ( ! empty( $archive_data ) ) {
        return $archive_data['title'] . ' | ' . get_bloginfo( 'name' );
    }

    return $title;
} );

add_action( 'wp_head', function () {
    if ( ! summit_seo_should_run() ) {
        return;
    }

    // Taxonomy archive SEO.
    $tax_data = summit_seo_get_taxonomy_data();
    if ( ! empty( $tax_data ) ) {
        echo '<meta name="description" content="' . esc_attr( $tax_data['description'] ) . '">' . "\n";
        echo '<link rel="canonical" href="' . esc_url( $tax_data['url'] ) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $tax_data['term_name'] ) . '">' . "\n";
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $tax_data['url'] ) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( $tax_data['description'] ) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
        return;
    }

    // Single CPT SEO (article, case_study, etc.).
    $data = summit_seo_get_data();
    if ( ! empty( $data ) ) {
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
        return;
    }

    // CPT archive SEO.
    $archive_data = summit_seo_get_archive_data();
    if ( ! empty( $archive_data ) ) {
        if ( $archive_data['description'] ) {
            echo '<meta name="description" content="' . esc_attr( $archive_data['description'] ) . '">' . "\n";
        }

        echo '<link rel="canonical" href="' . esc_url( $archive_data['url'] ) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $archive_data['title'] ) . '">' . "\n";
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $archive_data['url'] ) . '">' . "\n";

        if ( $archive_data['description'] ) {
            echo '<meta property="og:description" content="' . esc_attr( $archive_data['description'] ) . '">' . "\n";
        }

        echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
        return;
    }

    // Standard page / front page SEO.
    $page_data = summit_seo_get_page_data();
    if ( ! empty( $page_data ) ) {
        if ( $page_data['meta_desc'] ) {
            echo '<meta name="description" content="' . esc_attr( $page_data['meta_desc'] ) . '">' . "\n";
        }

        echo '<link rel="canonical" href="' . esc_url( $page_data['url'] ) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $page_data['title'] ) . '">' . "\n";
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $page_data['url'] ) . '">' . "\n";

        if ( $page_data['meta_desc'] ) {
            echo '<meta property="og:description" content="' . esc_attr( $page_data['meta_desc'] ) . '">' . "\n";
        }

        if ( ! empty( $page_data['og_image_url'] ) ) {
            echo '<meta property="og:image" content="' . esc_url( $page_data['og_image_url'] ) . '">' . "\n";
        }

        echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
        return;
    }
}, 2 );