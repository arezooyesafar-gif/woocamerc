<?php
/*
Plugin Name: BdThemes Testimonials
Plugin URI: http://themeforest.net/user/bdthemes
Description: This plugin will create a testimonial custom post type for bdthemes wordpress theme.
Version: 1.1.1
Author: bdthemes
Author URI: http://bdthemes.com
License: Custom
License URI: http://themeforest.net/licenses 
*/

/* ----------------------------------------------------- */
/* Add Testimonial Custom Post Type
/* ----------------------------------------------------- */

define( 'BDTTM_PATH', plugin_dir_path( __FILE__ ));

function bdthemes_testimonial_register() {  

	$testimonial_slug = get_theme_mod('bdthemes_testimonial_slug');

	if(isset($testimonial_slug) && $testimonial_slug != ''){
		$testimonial_slug = $testimonial_slug;
	} else {
		$testimonial_slug = 'testimonial';
	}
	
	$labels = array(
		'name'               => esc_html__( 'Testimonials', 'bdthemes-testimonials' ),
		'singular_name'      => esc_html__( 'Testimonial', 'bdthemes-testimonials' ),
		'add_new'            => esc_html__( 'Add New', 'bdthemes-testimonials' ),
		'add_new_item'       => esc_html__( 'Add New Testimonial', 'bdthemes-testimonials' ),
		'all_items'          => esc_html__( 'All Testimonials', 'bdthemes-testimonials' ),
		'edit_item'          => esc_html__( 'Edit Testimonial', 'bdthemes-testimonials' ),
		'new_item'           => esc_html__( 'Add New Testimonial', 'bdthemes-testimonials' ),
		'view_item'          => esc_html__( 'View Item', 'bdthemes-testimonials' ),
		'search_items'       => esc_html__( 'Search Testimonial', 'bdthemes-testimonials' ),
		'not_found'          => esc_html__( 'No testimonial(s) found', 'bdthemes-testimonials' ),
		'not_found_in_trash' => esc_html__( 'No testimonial(s) found in trash', 'bdthemes-testimonials' )
	);
	
    $args = array(  
		'labels'          => $labels,
		'public'          => true,  
		'show_ui'         => true,  
		'capability_type' => 'post',  
		'hierarchical'    => false,  
		'menu_icon'       => 'dashicons-testimonial',
		'rewrite'         => array('slug' => $testimonial_slug), // Permalinks format
		'supports'        => array('title', 'editor', 'excerpt', 'thumbnail')  
       );

    add_filter( 'enter_title_here',  'change_default_title'); 
  
    register_post_type( 'bdthemes-testimonial' , $args );  
}
add_action('init', 'bdthemes_testimonial_register', 1);


function bdthemes_testimonials_taxonomy() {
	
	register_taxonomy(
		"testimonial_categories", 
		array("bdthemes-testimonial"), 
		array(
			"hierarchical"   => true, 
			"label"          => "Categories", 
			"singular_label" => "Category", 
			"rewrite"        => true
		)
	);

	register_taxonomy( 
        'testimonial_tag', 
        'bdthemes-testimonial', 
        array( 
            'hierarchical'  => false, 
            'label'         => __( 'Tags', 'bdthemes-testimonials' ), 
            'singular_name' => __( 'Tag', 'bdthemes-testimonials' ), 
            'rewrite'       => true, 
            'query_var'     => true 
        )  
    );

}
add_action('init', 'bdthemes_testimonials_taxonomy', 1);


function change_default_title( $title ) {
	$screen = get_current_screen();

	if ( 'bdthemes-testimonial' == $screen->post_type )
		$title = esc_html__( "Enter the customer's name here", 'bdthemes-testimonials' );

	return $title;
}   

/* ----------------------------------------------------- */
/* Add Columns to Testimonial Edit Screen
 * http://wptheming.com/2010/07/column-edit-pages/
/* ----------------------------------------------------- */
function bdthemes_testimonial_edit_columns( $testimonial_columns ) {
	$testimonial_columns = array(
		"cb"                     => "<input type=\"checkbox\" />",
		"title"                  => esc_html__('Title', 'bdthemes-testimonials'),
		"thumbnail"              => esc_html__('Thumbnail', 'bdthemes-testimonials'),
		"testimonial_categories" => esc_html__('Categories', 'bdthemes-testimonials'),
		"date"                   => esc_html__('Date', 'bdthemes-testimonials'),
	);
	return $testimonial_columns;
}
add_filter( 'manage_edit-bdthemes-testimonial_columns', 'bdthemes_testimonial_edit_columns' );

function bdthemes_testimonial_column_display( $testimonial_columns, $post_id ) {
	
	switch ( $testimonial_columns ) {
		
		// Display the thumbnail in the column view
		case "thumbnail":
			$width = (int) 64;
			$height = (int) 64;
			$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
			
			// Display the featured image in the column view if possible
			if ( $thumbnail_id ) {
				$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
			}
			if ( isset( $thumb ) ) {
				echo $thumb; // No need to escape
			} else {
				echo esc_html__('None', 'bdthemes-testimonials');
			}
			break;	
			
		// Display the testimonial tags in the column view
		case "testimonial_categories":
		
		if ( $category_list = get_the_term_list( $post_id, 'testimonial_categories', '', ', ', '' ) ) {
			echo $category_list; // No need to escape
		} else {
			echo esc_html__('None', 'bdthemes-testimonials');
		}
		break;			
	}
}
add_action( 'manage_bdthemes-testimonial_posts_custom_column', 'bdthemes_testimonial_column_display', 10, 2 );


require_once BDTTM_PATH . "meta-box.php";


