<?php
/*
Plugin Name: BdThemes FAQ
Plugin URI: http://themeforest.net/user/bdthemes
Description: This plugin will create a faq custom post type for bdthemes wordpress theme.
Version: 1.0.0
Author: bdthemes
Author URI: http://bdthemes.com
License: Custom
License URI: http://themeforest.net/licenses 
*/

/* ----------------------------------------------------- */
/* Add FAQ Custom Post Type
/* ----------------------------------------------------- */
function bdthemes_faq_register() {  

	$faq_slug = get_theme_mod('bdt_faq_slug');

	if(isset($faq_slug) && $faq_slug != ''){
		$faq_slug = $faq_slug;
	} else {
		$faq_slug = 'faq-item';
	}
	
	$labels = array(
		'name'               => __( 'FAQ', 'bdthemes-faq' ),
		'singular_name'      => __( 'FAQ Item', 'bdthemes-faq' ),
		'add_new'            => __( 'Add New Item', 'bdthemes-faq' ),
		'add_new_item'       => __( 'Add New FAQ Item', 'bdthemes-faq' ),
		'edit_item'          => __( 'Edit FAQ Item', 'bdthemes-faq' ),
		'new_item'           => __( 'Add New FAQ Item', 'bdthemes-faq' ),
		'view_item'          => __( 'View Item', 'bdthemes-faq' ),
		'search_items'       => __( 'Search FAQ', 'bdthemes-faq' ),
		'not_found'          => __( 'No faq items found', 'bdthemes-faq' ),
		'not_found_in_trash' => __( 'No faq items found in trash', 'bdthemes-faq' )
	);
	
    $args = array(  
		'labels'          => $labels,
		'public'          => true,  
		'show_ui'         => true,  
		'capability_type' => 'post',  
		'hierarchical'    => false,  
		'menu_icon'       => 'dashicons-editor-help',
		'rewrite'         => array('slug' => $faq_slug), // Permalinks format
		'supports'        => array('title', 'editor', 'thumbnail', 'comments', 'excerpt')  
       );  
  
    register_post_type( 'faq' , $args );  
}
add_action('init', 'bdthemes_faq_register', 1);   

/* ----------------------------------------------------- */
/* Register Taxonomy
/* ----------------------------------------------------- */
function bdthemes_faq_taxonomy() {
	
	register_taxonomy("faq_filter", array("faq"), array("hierarchical" => true, "label" => "FAQ Filter", "singular_label" => "Faq Filter", "rewrite" => true));

}
add_action('init', 'bdthemes_faq_taxonomy', 1);   

function bdthemes_faq_column_display( $faq_columns, $post_id ) {
	
	switch ( $faq_columns ) {
			
		// Display the faq tags in the column view
		case "faq_filter":
		
		if ( $category_list = get_the_term_list( $post_id, 'faq_filter', '', ', ', '' ) ) {
			echo $category_list; // No need to escape
		} else {
			echo __('None', 'bdthemes-faq');
		}
		break;			
	}
}
add_action( 'manage_posts_custom_column', 'bdthemes_faq_column_display', 10, 2 );