<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * File Name: Dedicated functions and commands
 * Description: Additional php code for specialty products at RTL-Theme.com
 * Author URI: https://www.rtl-theme.com/author/syntax/products/
 * Author: Syntax (Sajjad Rasti)
 */

function wp_rocket_rtl_css_panel(){
    wp_enqueue_style( 'SR-RTL-WPRocket', plugins_url( 'css/Syntax-RTL.min.css', __FILE__ ) );
}

add_filter('rocket_plugin_name',function(){

    if (get_locale() == 'fa_IR') {
      return "راکت راست چین";
    }
    return "WP Rocket";
});

if (get_locale() == 'fa_IR') {
    add_action('admin_print_styles', 'wp_rocket_rtl_css_panel');
}

function wp_rocket_l_t_d(){
	$plugin_dir = SYNTAX_B_WPROCKET;
	load_plugin_textdomain( 'rocket', false, $plugin_dir . '/languages' );
}
add_action( 'plugins_loaded', 'wp_rocket_l_t_d' );
