<?php

if (!defined('ABSPATH')) {
    exit;
}

function summit_core_register_post_types() {

    register_post_type('case_study', array(
        'labels' => array(
            'name' => 'Case Studies',
            'singular_name' => 'Case Study',
        ),
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => false,
        'rewrite' => array('slug' => 'work'),
        'menu_icon' => 'dashicons-portfolio',
        'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'custom-fields'),
    ));

    register_post_type('article', array(
        'labels' => array(
            'name' => 'Articles',
            'singular_name' => 'Article',
        ),
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => false,
        'rewrite' => array('slug' => 'future-of-luxury'),
        'menu_icon' => 'dashicons-edit-large',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions', 'custom-fields'),
    ));

    register_post_type('podcast_season', array(
        'labels' => array(
            'name' => 'Podcast Seasons',
            'singular_name' => 'Podcast Season',
        ),
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => false,
        'rewrite' => array('slug' => 'tastemakers'),
        'menu_icon' => 'dashicons-playlist-audio',
        'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'custom-fields'),
    ));

    register_post_type('podcast_episode', array(
        'labels' => array(
            'name' => 'Podcast Episodes',
            'singular_name' => 'Podcast Episode',
        ),
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => false,
        'rewrite' => array('slug' => 'tastemakers/episodes'),
        'menu_icon' => 'dashicons-microphone',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'),
    ));

    register_post_type('download', array(
        'labels' => array(
            'name' => 'Downloads',
            'singular_name' => 'Download',
        ),
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => false,
        'rewrite' => array('slug' => 'downloads'),
        'menu_icon' => 'dashicons-media-document',
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
    ));

    register_post_type('enquiry', array(
        'labels' => array(
            'name' => 'Enquiries',
            'singular_name' => 'Enquiry',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => false,
        'menu_icon' => 'dashicons-email',
        'supports' => array('title', 'custom-fields'),
    ));
}
add_action('init', 'summit_core_register_post_types');