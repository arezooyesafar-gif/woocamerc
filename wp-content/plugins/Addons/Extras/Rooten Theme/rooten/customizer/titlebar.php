<?php
function rooten_customize_register_titlebar($wp_customize) {
	//header section
	$wp_customize->add_section('header', array(
		'title' => esc_attr__('Titlebar', 'rooten'),
		'priority' => 31
	));

	$wp_customize->add_setting('rooten_global_header', array(
		'default' => 'title',
		'sanitize_callback' => 'rooten_sanitize_choices'
	));
	$wp_customize->add_control('rooten_global_header', array(
		'label'    => esc_attr__('Titlebar Layout', 'rooten'),
		'section'  => 'header',
		'settings' => 'rooten_global_header', 
		'type'     => 'select',
		'priority' => 1,
		'choices'  => array(
			'title'               => esc_attr__('Titlebar (Left Align)', 'rooten'),
			'featuredimagecenter' => esc_attr__('Titlebar (Center Align)', 'rooten'),
			'notitle'             => esc_attr__('No Titlebar', 'rooten')
		)
	));


	$wp_customize->add_setting('rooten_titlebar_style', array(
		'default' => 'titlebar-dark',
		'sanitize_callback' => 'rooten_sanitize_choices'
	));
	$wp_customize->add_control('rooten_titlebar_style', array(
		'label'    => esc_attr__('Titlebar Style', 'rooten'),
		'section'  => 'header',
		'settings' => 'rooten_titlebar_style', 
		'type'     => 'select',
		'priority' => 1,
		'choices'  => array(
			'titlebar-dark' => esc_attr__('Dark (for dark backgrounds)', 'rooten'),
			'titlebar-light' => esc_attr__('Light (for light backgrounds)', 'rooten')
		)
	));

	$wp_customize->add_setting( 'rooten_titlebar_bg_image' , array(
		'sanitize_callback' => 'esc_url'
	));
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'rooten_titlebar_bg_image', array(
		'priority' => 1,
	    'label'    => esc_attr__( 'Titlebar Background', 'rooten' ),
	    'section'  => 'header',
	    'settings' => 'rooten_titlebar_bg_image'
	)));

	$wp_customize->add_setting('rooten_blog_title', array(
		'default' => esc_attr__('Blog', 'rooten'),
		'sanitize_callback' => 'esc_attr'
	));
	$wp_customize->add_control('rooten_blog_title', array(
		'priority' => 2,
	    'label'    => esc_attr__('Blog Title: ', 'rooten'),
	    'section'  => 'header',
	    'settings' => 'rooten_blog_title'
	));

	if (class_exists('Woocommerce')){
		$wp_customize->add_setting('rooten_woocommerce_title', array(
			'default' => esc_attr__('Shop', 'rooten'),
			'sanitize_callback' => 'esc_attr'
		));
		$wp_customize->add_control('rooten_woocommerce_title', array(
			'priority' => 3,
		    'label'    => esc_attr__('WooCommerce Title: ', 'rooten'),
		    'section'  => 'header',
		    'settings' => 'rooten_woocommerce_title'
		));
	}
	
	$wp_customize->add_setting('rooten_right_element', array(
		'default' => 'back_button',
		'sanitize_callback' => 'rooten_sanitize_choices'
	));
	$wp_customize->add_control('rooten_right_element', array(
		'label'    => esc_attr__('Right Element', 'rooten'),
		'section'  => 'header',
		'settings' => 'rooten_right_element', 
		'type'     => 'select',
		'priority' => 4,
		'choices'  => array(
			0             => esc_attr__('Nothing', 'rooten'),
			'back_button' => esc_attr__('Back Button', 'rooten'),
			'breadcrumb'  => esc_attr__('Breadcrumb', 'rooten')
		)
	));

}

add_action('customize_register', 'rooten_customize_register_titlebar');