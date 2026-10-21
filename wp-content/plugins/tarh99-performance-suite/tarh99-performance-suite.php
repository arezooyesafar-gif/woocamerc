<?php
/**
 * Plugin Name: Tarh99 Performance Suite
 * Description: Safe, modular performance optimizations and asset diagnostics for WooCommerce, Elementor, and WP Rocket.
 * Version: 1.4.0
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Author: Tarh99 Performance Team
 * License: GPL-2.0-or-later
 * Text Domain: tarh99-performance-suite
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'TPS_VERSION', '1.4.0' );
define( 'TPS_PATH', plugin_dir_path( __FILE__ ) );
define( 'TPS_URL', plugin_dir_url( __FILE__ ) );

require_once TPS_PATH . 'includes/class-tps-plugin.php';
require_once TPS_PATH . 'includes/class-tps-admin.php';

register_activation_hook( __FILE__, array( 'TPS_Plugin', 'activate' ) );
add_action( 'plugins_loaded', array( 'TPS_Plugin', 'boot' ), 20 );
