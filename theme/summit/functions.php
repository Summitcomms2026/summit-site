<?php

if (!defined('ABSPATH')) {
    exit;
}

function summit_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');

    register_nav_menus([
        'primary' => __('Primary Menu', 'summit'),
        'footer'  => __('Footer Menu', 'summit'),
    ]);
}
add_action('after_setup_theme', 'summit_theme_setup');

function summit_enqueue_assets() {
    wp_enqueue_style(
        'summit-style',
        get_stylesheet_uri(),
        [],
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'summit_enqueue_assets');