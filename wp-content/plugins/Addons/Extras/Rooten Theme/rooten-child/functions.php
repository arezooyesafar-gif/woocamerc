<?php


// Load the main stylesheet
add_action( 'wp_enqueue_scripts', 'rooten_theme_style' );
function rooten_theme_style() {
	wp_enqueue_style( 'rooten-theme-style', get_template_directory_uri() . '/style.css' );
}


// Load child theme's textdomain.
add_action( 'after_setup_theme', 'rooten_child_textdomain' );
function rooten_child_textdomain(){
   load_child_theme_textdomain( 'rooten', get_stylesheet_directory().'/languages' );
}


// Example code loading JS in footer. Uncomment to use.
 

/* ====== REMOVE COMMENT

add_action('wp_footer', 'rootenChildFooterScript' );
function rootenChildFooterScript(){

	echo '
	<script type="text/javascript">

	// Your JS code here

	</script>';

}
 ====== REMOVE COMMENT */

/* ======================================================== */