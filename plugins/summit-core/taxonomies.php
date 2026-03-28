<?php

if (!defined('ABSPATH')) {
    exit;
}

function summit_core_register_taxonomies() {

    register_taxonomy('sector', array('case_study', 'download'), array(
        'labels' => array(
            'name' => 'Sectors',
            'singular_name' => 'Sector',
        ),
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'sector'),
    ));

    register_taxonomy('service_type', array('case_study'), array(
        'labels' => array(
            'name' => 'Service Types',
            'singular_name' => 'Service Type',
        ),
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'service-type'),
    ));

    register_taxonomy('article_category', array('article'), array(
        'labels' => array(
            'name' => 'Article Categories',
            'singular_name' => 'Article Category',
        ),
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'article-category'),
    ));

    register_taxonomy('format', array('case_study', 'download'), array(
        'labels' => array(
            'name' => 'Formats',
            'singular_name' => 'Format',
        ),
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'format'),
    ));

    register_taxonomy('podcast_theme', array('podcast_season', 'podcast_episode'), array(
        'labels' => array(
            'name' => 'Podcast Themes',
            'singular_name' => 'Podcast Theme',
        ),
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'podcast-theme'),
    ));
}
add_action('init', 'summit_core_register_taxonomies');