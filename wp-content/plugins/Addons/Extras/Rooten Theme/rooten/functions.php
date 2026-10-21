<?php
/**
 * rooten functions and definitions.
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package rooten
 */

define( 'ROOTEN_VER', wp_get_theme()->get('Version') );
define( 'ROOTEN__FILE__', __FILE__ );
define( 'ROOTEN_PNAME', basename( dirname(ROOTEN__FILE__)) );
define( 'ROOTEN_PATH', get_template_directory() );
define( 'ROOTEN_URL', get_template_directory_uri() );
define( 'ROOTEN_MB_URL', trailingslashit( ROOTEN_URL . '/inc/meta-box' ) );
define( 'ROOTEN_MB_DIR', trailingslashit( ROOTEN_PATH . '/inc/meta-box' ) );

// Check if in the admin
$rooten_is_admin = is_admin();

require_once( ROOTEN_PATH . '/inc/helper-functions.php' );
require_once( ROOTEN_PATH . '/admin/dom-helper.php' );
require_once( ROOTEN_PATH . '/inc/nav_walker.php' );
require_once( ROOTEN_PATH . '/inc/menu_options.php' );

// Custom Widgets declare here
require_once( ROOTEN_PATH . '/inc/sidebars.php' );

// Theme customizer integration
require_once( ROOTEN_PATH . '/customizer/theme-customizer.php' );

// Revolution slider related tweak
require_once( ROOTEN_PATH . '/admin/revolution-slider.php' );

//TGM Plugin Activation all required plugin install by this script
require_once( ROOTEN_PATH . '/inc/plugin-activation.php' );

// Sidebar Generator plugin this gives you make custom sidebar for your page
require_once( ROOTEN_PATH . '/inc/sidebar-generator.php' );

/* 
Meta Box Plugin + related addon loaded here.
This plugin and addons gives you some extra facelities for your page
*/
if ( defined( 'RWMB_VER' ) ) {
	require_once( ROOTEN_MB_DIR . 'meta-box-tabs/meta-box-tabs.php' ); // Include Tabs Extension
	require_once( ROOTEN_MB_DIR . 'meta-box-conditional-logic/meta-box-conditional-logic.php' ); // Include Conditional Logic Extension
	require_once( ROOTEN_MB_DIR . 'meta-box-yoast-seo/mb-yoast-seo.php' ); // Include Conditional Logic Extension
	require_once( ROOTEN_PATH . '/inc/meta-boxes.php' );
}

// Breadcrumb functionalities you get here
require_once( ROOTEN_PATH.'/inc/breadcrumbs.php' );

// WooCommerce integration
if (class_exists('Woocommerce')) {
	require_once( ROOTEN_PATH . '/inc/woocommerce.php' );
}

// enqueue style and script from this file
require_once( ROOTEN_PATH.'/inc/enqueue.php' );



if ( ! function_exists( 'rooten_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function rooten_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on rooten, use a find and replace
	 * to change 'rooten' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'rooten', ROOTEN_PATH . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	// Post Formats
	add_theme_support( 'post-formats', array('gallery', 'link', 'quote', 'audio', 'video'));

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	if ( function_exists('add_image_size')) {
		add_image_size( 'rooten_blog', 1200, 800, true); // Standard Blog Image
	}

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary'   => esc_html_x('Primary Menu', 'backend', 'rooten'),
		'offcanvas' => esc_html_x('Offcanvas Menu', 'backend', 'rooten'),
		'toolbar'   => esc_html_x('Toolbar Menu', 'backend', 'rooten'),
		'copyright' => esc_html_x('Copyright Menu', 'backend', 'rooten'),
	));

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background');

	/**
	 * Set the content width in pixels, based on the theme's design and stylesheet.
	 * Priority 0 to make it available to lower priority callbacks.
	 * @global int $content_width
	 */
	$GLOBALS['content_width'] = apply_filters( 'content_width', 1200 );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( 'admin/css/editor-style.css' );

	add_theme_support( 'customize-selective-refresh-widgets' );
}
endif;

add_action( 'after_setup_theme', 'rooten_setup' );

/**
 * Custom template tags for this theme.
 */
require ROOTEN_PATH . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require ROOTEN_PATH . '/inc/extras.php';

/**
 * Load Jetpack compatibility file.
 */
require ROOTEN_PATH . '/inc/jetpack.php';