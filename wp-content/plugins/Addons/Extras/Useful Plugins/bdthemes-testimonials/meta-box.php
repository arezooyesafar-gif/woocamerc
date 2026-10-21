<?php

if ( ! function_exists('is_plugin_active')){ include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); }

$rooten_theme_check = wp_get_theme();

if ('Rooten' == $rooten_theme_check->name or is_plugin_active('meta-box/meta-box.php')) {

	add_filter( 'rwmb_meta_boxes', 'bdthemes_testimonials_register_meta_boxes' );

	function bdthemes_testimonials_register_meta_boxes($meta_boxes) {
		//global $meta_boxes;

		$prefix = 'bdthemes_';

		$meta_boxes[] = array(
			'id'      => 'testimonial_info',
			'title'   => esc_html_x( 'Additional Information', 'backend', 'bdthemes-testimonials'),
			'pages'   => array( 'bdthemes-testimonial' ),
			'fields'  => array(
				array(
					'name'		=> esc_html_x( 'Company Name/Address', 'backend', 'bdthemes-testimonials'),
					'id'		=> $prefix . 'tm_company_name',
					'desc'		=> esc_html_x( 'Please type client company name for example: BdThemes Limited.', 'backend', 'bdthemes-testimonials'),
					'clone'		=> false,
					'type'		=> 'text',
					'std'		=> '',
					'placeholder' => esc_html_x('BdThemes Limited', 'backend', 'bdthemes-testimonials' ),
				),
				array(
					'name'		=> 'Rating',
					'id'		=> $prefix . "tm_rating",
					'type'		=> 'select',
					'options'	=> array(
						'1' => esc_html_x('1 Star', 'backend', 'bdthemes-testimonials'),
						'2' => esc_html_x('2 Stars', 'backend', 'bdthemes-testimonials'),
						'3' => esc_html_x('3 Stars', 'backend', 'bdthemes-testimonials'),
						'4' => esc_html_x('4 Stars', 'backend', 'bdthemes-testimonials'),
						'5' => esc_html_x('5 Stars', 'backend', 'bdthemes-testimonials'),
					),
					'multiple' => false,
					'std'      => array( '' ),
					'desc'     => 'Select your client rating what he/she gives you.',
				),
			)
		);

		return $meta_boxes;
	}
} else {
	require_once BDTTM_PATH . "class-tgm-plugin-activation.php";
	
	add_action( 'tgmpa_register', 'bdthemes_required_metabox' );
	function bdthemes_required_metabox() {
	    $plugins = array(
	        array(
	            'name'     => 'Meta Box',
	            'slug'     => 'meta-box',
	            'required' => true,
	        ),
	        // More plugins
	    );
	    $config  = array(
	        'id' => 'your-id',
	    );
	    tgmpa( $plugins, $config );
	}
}