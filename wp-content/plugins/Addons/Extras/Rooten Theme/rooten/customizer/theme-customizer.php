<?php
/**
 * Load the Customizer with some custom extended addons
 *
 * @package Rooten
 * @link http://codex.wordpress.org/Theme_Customization_API
 */

load_template( get_template_directory() . '/customizer/class-customizer-control.php' );

/**
 * This funtion is only called when the user is actually on the customizer page
 * @param  WP_Customize_Manager $wp_customize
 */
if ( ! function_exists( 'rooten_customizer' ) ) {
	function rooten_customizer( $wp_customize ) {
		
		// add required files
		load_template( get_template_directory() . '/customizer/class-customizer-base.php' );
		load_template( get_template_directory() . '/customizer/class-customizer-dynamic-css.php' );

		new rooten_Customizer_Base( $wp_customize );
	}
	add_action( 'customize_register', 'rooten_customizer' );
}


/**
 * Takes care for the frontend output from the customizer and nothing else
 */
if ( ! function_exists( 'rooten_customizer_frontend' ) && ! class_exists( 'Rooten_Customize_Frontent' ) ) {
	function rooten_customizer_frontend() {
		load_template( get_template_directory() . '/customizer/class-customizer-frontend.php' );
		new rooten_Customize_Frontent();
	}
	add_action( 'init', 'rooten_customizer_frontend' );
}