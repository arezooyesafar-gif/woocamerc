<?php

if ( ! function_exists('is_plugin_active')){ include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); }

if (is_plugin_active('meta-box/meta-box.php')) {

	add_filter( 'rwmb_meta_boxes', 'bdthemes_portfolio_metabox_register' );

	function bdthemes_portfolio_metabox_register($meta_boxes) {
		//global $meta_boxes;

		$prefix = 'bdthemes_';

		$meta_boxes[] = array(
			'id'     => 'testimonial_info',
			'title'  => esc_html_x( 'Additional Information', 'backend', 'bdthemes-testimonials'),
			'pages'  => array( 'portfolio' ),
			'fields' => array(
				array(
					'name'        => esc_html_x( 'Company/Owner Name', 'backend', 'bdthemes-testimonials'),
					'id'          => $prefix . 'company_name',
					'desc'        => esc_html_x( 'Please type company or owner name of this project, for example: BdThemes Limited.', 'backend', 'bdthemes-testimonials'),
					'clone'       => false,
					'type'        => 'text',
					'std'         => '',
				),
				array(
					'name'        => esc_html_x( 'Gallery', 'backend', 'bdthemes-testimonials'),
					'id'          => $prefix . 'gallery',
					'desc'        => esc_html_x( 'Please choose images for this project', 'backend', 'bdthemes-testimonials'),
					'clone'       => false,
					'type'        => 'image_advanced',
					'std'         => '',
				),
				array(
					'name'        => esc_html_x( 'Complition Date', 'backend', 'bdthemes-testimonials'),
					'id'          => $prefix . 'complition_date',
					'desc'        => esc_html_x( 'Please write complition date of this project, for example December 31, 2022', 'backend', 'bdthemes-testimonials'),
					'clone'       => false,
					'type'        => 'text',
					'std'         => '',
				),
				array(
					'name'        => esc_html_x( 'Project Link', 'backend', 'bdthemes-testimonials'),
					'id'          => $prefix . 'project_link',
					'desc'        => esc_html_x( 'Please keep project link here if have', 'backend', 'bdthemes-testimonials'),
					'clone'       => false,
					'type'        => 'text',
					'std'         => '',
				),
			)
		);

		return $meta_boxes;
	}
}