<?php
/**
 * Set up the WordPress core custom header feature.
 * @uses rooten_header_style() fuzzy function for validation
 */
function rooten_custom_header_setup() {
	add_theme_support( 'custom-header');
}
add_action( 'after_setup_theme', 'rooten_custom_header_setup' );
