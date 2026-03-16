<?php
/**
 * Plugin Name: Summit Core
 * Plugin URI: https://summitcommunication.com
 * Description: Core content architecture for Summit Communication Group.
 * Version: 1.0.0
 * Author: Gregory Gray
 * Text Domain: summit-core
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SUMMIT_CORE_PATH', plugin_dir_path(__FILE__));
define('SUMMIT_CORE_URL', plugin_dir_url(__FILE__));

require_once SUMMIT_CORE_PATH . 'post-types.php';
require_once SUMMIT_CORE_PATH . 'taxonomies.php';
require_once plugin_dir_path(__FILE__) . 'acf-fields.php';