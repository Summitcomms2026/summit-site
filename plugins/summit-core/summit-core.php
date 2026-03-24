<?php
/**
 * Plugin Name: Summit Core
 * Description: Custom post types, taxonomies and ACF field groups for Summit Communication Group.
 * Version: 1.0.0
 * Author: Summit Communication Group
 * Text Domain: summit-core
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

// CPTs — must register before taxonomies and ACF fields.
require_once __DIR__ . '/post-types.php';

// Taxonomies.
require_once __DIR__ . '/taxonomies.php';

// ACF field groups — file registers its own acf/init hook internally.
require_once __DIR__ . '/acf-fields.php';
