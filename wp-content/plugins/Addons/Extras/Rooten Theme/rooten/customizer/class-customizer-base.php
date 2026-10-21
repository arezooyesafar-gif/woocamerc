<?php
/**
 * Contains methods for customizing the theme customization screen.
 *
 * @package Rooten
 * @link http://codex.wordpress.org/Theme_Customization_API
 */

class rooten_Customizer_Base {
	/**
	 * The singleton manager instance
	 *
	 * @see wp-includes/class-wp-customize-manager.php
	 * @var WP_Customize_Manager
	 */
	protected $wp_customize;

	public function __construct( WP_Customize_Manager $wp_manager ) {
		// set the private propery to instance of wp_manager
		$this->wp_customize = $wp_manager;

		// register the settings/panels/sections/controls, main method
		$this->register();

		/**
		 * Action and filters
		 */

		// render the CSS and cache it to the theme_mod when the setting is saved
		add_action( 'customize_save_after' , array( $this, 'cache_rendered_css' ) );

		// save logo width/height dimensions
		add_action( 'customize_save_logo_img' , array( $this, 'save_logo_dimensions' ), 10, 1 );

		// flush the rewrite rules after the customizer settings are saved
		add_action( 'customize_save_after', 'flush_rewrite_rules' );

		// handle the postMessage transfer method with some dynamically generated JS in the footer of the theme
		add_action( 'wp_footer', array( $this, 'customize_footer_js' ), 30 );
		add_action('wp_head',array( $this, 'hook_custom_css' ));


	}

	/**
	* This hooks into 'customize_register' (available as of WP 3.4) and allows
	* you to add new sections and controls to the Theme Customize screen.
	*
	* Note: To enable instant preview, we have to actually write a bit of custom
	* javascript. See live_preview() for more.
	*
	* @see add_action('customize_register',$func)
	*/
	public function register () {
		/**
		 * Settings
		 */

		//$this->wp_customize->remove_section( 'colors');
		$this->wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$this->wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';



		$this->wp_customize->add_setting( 'rooten_logo_default' , array(
			'sanitize_callback' => 'esc_url'
		));
		$this->wp_customize->add_control( new WP_Customize_Image_Control( $this->wp_customize, 'rooten_logo_default', array(
			'priority' => 101,
		    'label'    => esc_html_x( 'Default Logo', 'backend', 'rooten' ),
		    'section'  => 'title_tagline',
		    'settings' => 'rooten_logo_default'
		)));

		$this->wp_customize->add_setting('rooten_logo_width_default', array(
			'sanitize_callback' => 'rooten_sanitize_text'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_logo_width_default', array(
			'label'       => esc_html_x('Default Logo Width', 'backend', 'rooten'),
			'description' => esc_html_x('This is an optional width (example: 150px) settings. maybe this not need if you use 150px x 32px logo.' , 'backend', 'rooten'),
			'priority' => 102,
			'section'     => 'title_tagline',
			'settings'    => 'rooten_logo_width_default', 
			'type'        => 'text',
		)));

		$this->wp_customize->add_setting( 'rooten_logo_mobile' , array(
			'sanitize_callback' => 'esc_url'
		));
		$this->wp_customize->add_control( new WP_Customize_Image_Control( $this->wp_customize, 'rooten_logo_mobile', array(
			'priority' => 103,
		    'label'    => esc_html_x( 'Mobile Logo', 'backend', 'rooten' ),
		    'section'  => 'title_tagline',
		    'settings' => 'rooten_logo_mobile'
		)));


		$this->wp_customize->add_setting('rooten_logo_width_mobile', array(
			'sanitize_callback' => 'rooten_sanitize_text'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_logo_width_mobile', array(
			'label'       => esc_html_x('Mobile Logo Width', 'backend', 'rooten'),
			'description' => esc_html_x('This is an optional width (example: 150px) settings. maybe this not need if you use 150px x 32px logo.' , 'backend', 'rooten'),
			'priority' => 104,
			'section'     => 'title_tagline',
			'settings'    => 'rooten_logo_width_mobile', 
			'type'        => 'text',
		)));

		$this->wp_customize->add_setting('rooten_mobile_logo_align', array(
			'default' => 'center',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_mobile_logo_align', array(
			'label'    => esc_html_x('Mobile Logo Align', 'backend', 'rooten'),
			'section'  => 'title_tagline',
			'settings' => 'rooten_mobile_logo_align', 
			'type'     => 'select',
			'choices'  => array(
				'' => esc_html_x('Hide', 'backend', 'rooten'),
				'left' => esc_html_x('Left', 'backend', 'rooten'),
				'right' => esc_html_x('Right', 'backend', 'rooten'),
				'center' => esc_html_x('Center', 'backend', 'rooten'),
			),
			'priority' => 106,
		)));


		$this->wp_customize->add_section('toolbar', array(
			'title' => esc_html_x('Toolbar', 'backend', 'rooten'),
			'priority' => 28
		));

		$this->wp_customize->add_setting('rooten_toolbar', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_toolbar', array(
			'label'    => esc_html_x('Show Toolbar', 'backend', 'rooten'),
			'section'  => 'toolbar',
			'settings' => 'rooten_toolbar', 
			'type'     => 'select',
			'choices'  => array(
				1 => esc_html_x('Yes', 'backend', 'rooten'),
				0 => esc_html_x('No', 'backend', 'rooten'),
			)
		)));

		$this->wp_customize->add_setting('rooten_toolbar_fullwidth', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_toolbar_fullwidth', array(
			'label'       => esc_html_x('Toolbar Fullwidth', 'backend', 'rooten'),
			'description' => esc_html_x('(Make your tolbar full width like fluid width.)', 'backend', 'rooten'),
			'section'     => 'toolbar',
			'settings'    => 'rooten_toolbar_fullwidth', 
			'type'        => 'checkbox',
			'active_callback' => 'rooten_toolbar_check',
		)));


		// Add footer text color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'toolbar_text_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'.tm-toolbar',
					'.tm-toolbar a',
					'.tm-toolbar .bdt-subnav>*>:first-child',
				),
				'color|lighten(30)' => array(
					'.tm-toolbar a:hover',
					'.tm-toolbar .bdt-subnav>*>a:hover', 
					'.tm-toolbar .bdt-subnav>*>a:focus',
					'.tm-toolbar .bdt-subnav>.bdt-active>a',
				),
			)
		)));

		// Add toolbar text color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'toolbar_text_color', array(
			'label'       => esc_html_x( 'Text Color', 'backend', 'rooten' ),
			'section'     => 'toolbar',
			'active_callback' => 'rooten_toolbar_check',
		)));

		// Add toolbar background color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'toolbar_background_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'background-color' => array(
					'.tm-toolbar',
				),
				'border-color|lighten(15)' => array(
					'.tm-toolbar .bdt-subnav-divider>:nth-child(n+2):not(.bdt-first-column)::before',
				),
			)
		)));

		// Add toolbar background color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'toolbar_background_color', array(
			'label'       => esc_html_x( 'Background Color', 'backend', 'rooten' ),
			'section'     => 'toolbar',
			'active_callback' => 'rooten_toolbar_check',
		)));


		$this->wp_customize->add_setting('rooten_toolbar_left', array(
			'default' => 'tagline',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_toolbar_left', array(
			'label'           => esc_html_x('Toolbar Left Area', 'backend', 'rooten'),
			'section'         => 'toolbar',
			'settings'        => 'rooten_toolbar_left', 
			'active_callback' => 'rooten_toolbar_check',
			'type'            => 'select',
			'choices'         => $this->rooten_toolbar_left_elements()
		)));

		$this->wp_customize->add_setting('rooten_toolbar_left_hide_mobile', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_toolbar_left_hide_mobile', array(
			'label'           => esc_html_x('Hide in mobile mode', 'backend', 'rooten'),
			'section'         => 'toolbar',
			'settings'        => 'rooten_toolbar_left_hide_mobile', 
			'type'            => 'checkbox',
			'active_callback' => 'rooten_toolbar_check',
		)));


		$this->wp_customize->add_setting('rooten_toolbar_left_custom', array(
			'sanitize_callback' => 'rooten_sanitize_textarea'
		));
		$this->wp_customize->add_control( new rooten_Customize_Textarea_Control( $this->wp_customize, 'rooten_toolbar_left_custom', array(
			'label'           => esc_html_x('Toolbar Left Custom Text', 'backend', 'rooten'),
			'section'         => 'toolbar',
			'settings'        => 'rooten_toolbar_left_custom',
			'active_callback' => 'rooten_toolbar_left_custom_check',
			'type'            => 'textarea',
		)));

		$this->wp_customize->add_setting('rooten_toolbar_right', array(
			'default' => 'social',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_toolbar_right', array(
			'label'           => esc_html_x('Toolbar Right Area', 'backend', 'rooten'),
			'description' 	  => (get_theme_mod( 'rooten_woocommerce_cart' ) == 'toolbar') ? esc_html_x('This element automatically hide on mobile mode, for better preview shopping cart.', 'backend', 'rooten') : '',
			'section'         => 'toolbar',
			'settings'        => 'rooten_toolbar_right', 
			'active_callback' => 'rooten_toolbar_check',
			'type'            => 'select',
			'choices'         => $this->rooten_toolbar_right_elements()
		)));

		$this->wp_customize->add_setting('rooten_toolbar_right_hide_mobile', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_toolbar_right_hide_mobile', array(
			'label'           => esc_html_x('Hide in mobile mode', 'backend', 'rooten'),
			'section'         => 'toolbar',
			'settings'        => 'rooten_toolbar_right_hide_mobile', 
			'type'            => 'checkbox',
			'active_callback' => 'rooten_toolbar_check',
		)));


		$this->wp_customize->add_setting('rooten_toolbar_right_custom', array(
			'sanitize_callback' => 'rooten_sanitize_textarea'
		));
		$this->wp_customize->add_control( new rooten_Customize_Textarea_Control( $this->wp_customize, 'rooten_toolbar_right_custom', array(
			'label'           => esc_html_x('Toolbar Right Custom Text', 'backend', 'rooten'),
			'section'         => 'toolbar',
			'settings'        => 'rooten_toolbar_right_custom',
			'active_callback' => 'rooten_toolbar_right_custom_check',
			'type'            => 'textarea',
		)));


		$this->wp_customize->add_setting('rooten_toolbar_social', array(
			'sanitize_callback' => 'esc_html'
		));
		$this->wp_customize->add_control( new rooten_Customize_Social_Control( $this->wp_customize, 'rooten_toolbar_social', array(
			'label'             => esc_html_x('Social Link', 'backend', 'rooten'),
			'description'       => esc_html_x('Enter up to 5 links to your social profiles.', 'backend', 'rooten'),
			'section'           => 'toolbar',
			'settings'          => 'rooten_toolbar_social',
			'type'              => 'social',
			'active_callback' => 'rooten_toolbar_social_check',
		)));

		$this->wp_customize->add_setting('rooten_toolbar_social_style', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_toolbar_social_style', array(
			'label'           => esc_html_x('Social link as button', 'backend', 'rooten'),
			'section'         => 'toolbar',
			'settings'        => 'rooten_toolbar_social_style', 
			'type'            => 'checkbox',
			'active_callback' => 'rooten_toolbar_social_check',
		)));
		
		

		/**
		 * General Customizer Settings
		 */

		//general section
		$this->wp_customize->add_section('general', array(
			'title' => esc_html_x('General', 'backend', 'rooten'),
			'priority' => 21
		));

		$this->wp_customize->add_setting('rooten_global_layout', array(
			'default' => 'full',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_global_layout', array(
			'label'    => esc_html_x('Global Layout', 'backend', 'rooten'),
			'section'  => 'general',
			'settings' => 'rooten_global_layout', 
			'type'     => 'select',
			'choices'  => array(
				'full'  => esc_html_x('Fullwidth', 'backend', 'rooten'),
				'boxed' => esc_html_x('Boxed', 'backend', 'rooten'),
			)
		)));

		$this->wp_customize->add_setting('rooten_comment_show', array(
			'default' => 1,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_comment_show',
	        array(
				'label'       => esc_html_x('Show Global Page Comment', 'backend', 'rooten'),
				'description' => esc_html_x('Enable / Disable global page comments (not post comment).', 'backend', 'rooten'),
				'section'     => 'general',
				'settings'    => 'rooten_comment_show',
				'type'        => 'select',
				'choices'     => array(
					1 => esc_html_x('Yes', 'backend', 'rooten'),
					0 => esc_html_x('No', 'backend', 'rooten')
				)
	        )
		));

		$this->wp_customize->add_setting('rooten_offcanvas_search', array(
			'default' => 1,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_offcanvas_search',
	        array(
				'label'       => esc_html_x('Offcanvas Search', 'backend', 'rooten'),
				'description' => esc_html_x('Enable / Disable Offcanvas search display', 'backend', 'rooten'),
				'section'     => 'general',
				'settings'    => 'rooten_offcanvas_search',
				'type'        => 'select',
				'choices'     => array(
					1 => esc_html_x('Yes', 'backend', 'rooten'),
					0 => esc_html_x('No', 'backend', 'rooten')
				)
	        )
		));


		//titlebar section
		$this->wp_customize->add_section('titlebar', array(
			'title' => esc_html_x('Titlebar', 'backend', 'rooten'),
			'priority' => 32,
			'active_callback' => 'rooten_titlebar_check'
		));

		$this->wp_customize->add_setting('rooten_titlebar_layout', array(
			'default' => 'left',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control('rooten_titlebar_layout', array(
			'label'    => esc_html_x('Titlebar Layout', 'backend', 'rooten'),
			'section'  => 'titlebar',
			'settings' => 'rooten_titlebar_layout', 
			'type'     => 'select',
			'priority' => 1,
			'choices'  => array(
				'left'   => esc_html_x('Left Align', 'backend', 'rooten'),
				'center'  => esc_html_x('Center Align', 'backend', 'rooten'),
				'right'  => esc_html_x('Right Align', 'backend', 'rooten'),
				'notitle' => esc_html_x('No Titlebar', 'backend', 'rooten')
			)
		));


		$this->wp_customize->add_setting('rooten_titlebar_bg_style', array(
			'default' => 'muted',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_titlebar_bg_style', array(
			'label'    => esc_html_x('Background Style', 'backend', 'rooten'),
			'section'  => 'titlebar',
			'settings' => 'rooten_titlebar_bg_style', 
			'type'     => 'select',
			'choices'  => array(
				'default'   => esc_html_x('Default', 'backend', 'rooten'),
				'muted'     => esc_html_x('Muted', 'backend', 'rooten'),
				'primary'   => esc_html_x('Primary', 'backend', 'rooten'),
				'secondary' => esc_html_x('Secondary', 'backend', 'rooten'),
				'media'     => esc_html_x('Image', 'backend', 'rooten'),
				//'video'     => esc_html_x('Video', 'backend', 'rooten'),
			)
		));
		

		$this->wp_customize->add_setting( 'rooten_titlebar_bg_img' , array(
			'sanitize_callback' => 'esc_url'
		));
		$this->wp_customize->add_control( new WP_Customize_Image_Control( $this->wp_customize, 'rooten_titlebar_bg_img', array(
			'label'           => esc_html_x( 'Background Image', 'backend', 'rooten' ),
			'section'         => 'titlebar',
			'settings'        => 'rooten_titlebar_bg_img',
			'active_callback' => 'rooten_titlebar_bg_check',
		)));


		$this->wp_customize->add_setting('rooten_titlebar_txt_style', array(
			'default'           => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_titlebar_txt_style', array(
			'label'    => esc_html_x('Text Color', 'backend', 'rooten'),
			'section'  => 'titlebar',
			'settings' => 'rooten_titlebar_txt_style', 
			'type'     => 'select',
			'choices'  => array(
				0       => esc_html_x('Default', 'backend', 'rooten'),
				'light' => esc_html_x('Light', 'backend', 'rooten'),
				'dark'  => esc_html_x('Dark', 'backend', 'rooten'),
			)
		));


		$this->wp_customize->add_setting('rooten_titlebar_padding', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_titlebar_padding', array(
			'label'    => esc_html_x('Padding', 'backend', 'rooten'),
			'section'  => 'titlebar',
			'settings' => 'rooten_titlebar_padding', 
			'type'     => 'select',
			'choices'  => array(
				0        => esc_html_x('Default', 'backend', 'rooten'),
				'medium' => esc_html_x('Medium', 'backend', 'rooten'),
				'small'  => esc_html_x('Small', 'backend', 'rooten'),
				'large'  => esc_html_x('Large', 'backend', 'rooten'),
				'none'   => esc_html_x('None', 'backend', 'rooten'),
			)
		));


		$this->wp_customize->add_setting('rooten_blog_title', array(
			'default' => esc_html_x('Blog', 'backend', 'rooten'),
			'sanitize_callback' => 'esc_attr'
		));
		$this->wp_customize->add_control('rooten_blog_title', array(
		    'label'    => esc_html_x('Blog Title: ', 'backend', 'rooten'),
		    'section'  => 'titlebar',
		    'settings' => 'rooten_blog_title'
		));

		if (class_exists('Woocommerce')){
			$this->wp_customize->add_setting('rooten_woocommerce_title', array(
				'default' => esc_html_x('Shop', 'backend', 'rooten'),
				'sanitize_callback' => 'esc_attr'
			));
			$this->wp_customize->add_control('rooten_woocommerce_title', array(
			    'label'    => esc_html_x('WooCommerce Title: ', 'backend', 'rooten'),
			    'section'  => 'titlebar',
			    'settings' => 'rooten_woocommerce_title'
			));
		}



		//blog section
		$this->wp_customize->add_section('blog', array(
			'title' => esc_html_x('Blog', 'backend', 'rooten'),
			'priority' => 35
		));


		$this->wp_customize->add_setting('rooten_blog_layout', array(
			'default' => 'sidebar-right',
			'sanitize_callback' => 'rooten_sanitize_choices',
		));
		$this->wp_customize->add_control(new rooten_Customize_Layout_Control( $this->wp_customize, 'rooten_blog_layout', 
			array(
				'label'       => esc_html_x('Blog Page Layout', 'backend', 'rooten'),
				'description' => esc_html_x('If you select static blog page so you need to select your blog page layout from here.', 'backend', 'rooten'),
				'section'     => 'blog',
				'settings'    => 'rooten_blog_layout', 
				'type'        => 'layout',
				'choices' => array(
					"sidebar-left"  => esc_html_x('Sidebar Left', 'backend', 'rooten'), 
					"full"          => esc_html_x('Fullwidth', 'backend', 'rooten'),
					"sidebar-right" => esc_html_x('Sidebar Right', 'backend', 'rooten'),
				),
				//'active_callback' => 'is_front_page',
			)
		));



		$this->wp_customize->add_setting('rooten_blog_readmore', array(
			'default' => 1,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_blog_readmore',
	        array(
				'label'       => esc_html_x('Read More Button in Blog Posts', 'backend', 'rooten'),
				'description' => esc_html_x('Enable / Disable read more button on blog posts.', 'backend', 'rooten'),
				'section'     => 'blog',
				'settings'    => 'rooten_blog_readmore',
				'type'        => 'select',
				'choices'     => array(
					1 => esc_html_x('Yes', 'backend', 'rooten'),
					0  => esc_html_x('No', 'backend', 'rooten')
				)
	        )
		));

		$this->wp_customize->add_setting('rooten_blog_meta', array(
			'default' => 1,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_blog_meta',
	        array(
				'label'       => esc_html_x('Metadata on Blog Posts', 'backend', 'rooten'),
				'description' => esc_html_x('Enable / Disable metadata on blog post.', 'backend', 'rooten'),
				'section'     => 'blog',
				'settings'    => 'rooten_blog_meta',
				'type'        => 'select',
				'choices'     => array(
					1 => esc_html_x('Yes', 'backend', 'rooten'),
					0  => esc_html_x('No', 'backend', 'rooten')
				)
	        )
		));

		$this->wp_customize->add_setting('rooten_blog_next_prev', array(
			'default' => 1,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_blog_next_prev',
	        array(
				'label'       => esc_html_x('Previous / Next Pagination', 'backend', 'rooten'),
				'description' => esc_html_x('Enable / Disable next previous button on blog posts.', 'backend', 'rooten'),
				'section'     => 'blog',
				'settings'    => 'rooten_blog_next_prev',
				'type'        => 'select',
				'choices'     => array(
					1 => esc_html_x('Yes', 'backend', 'rooten'),
					0  => esc_html_x('No', 'backend', 'rooten')
				)
	        )
		));

		$this->wp_customize->add_setting('rooten_author_info', array(
			'default' => 1,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_author_info',
	        array(
				'label'       => esc_html_x('Author Info in Blog Details', 'backend', 'rooten'),
				'description' => esc_html_x('Enable / Disable author info from underneath of blog posts.', 'backend', 'rooten'),
				'section'     => 'blog',
				'settings'    => 'rooten_author_info',
				'type'        => 'select',
				'choices'     => array(
					1 => esc_html_x('Yes', 'backend', 'rooten'),
					0  => esc_html_x('No', 'backend', 'rooten')
				)
	        )
		));

		$this->wp_customize->add_setting('rooten_blog_align', array(
			'default' => 'left',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control('rooten_blog_align', array(
			'label'    => esc_html_x('Titlebar Layout', 'backend', 'rooten'),
			'section'  => 'blog',
			'settings' => 'rooten_blog_align', 
			'type'     => 'select',
			'priority' => 1,
			'choices'  => array(
				'left'   => esc_html_x('Left Align', 'backend', 'rooten'),
				'center'  => esc_html_x('Center Align', 'backend', 'rooten'),
				'right'  => esc_html_x('Right Align', 'backend', 'rooten'),
			)
		));

		$this->wp_customize->add_setting('rooten_related_post', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_related_post',
	        array(
				'label'       => esc_html_x('Related Posts in Blog Details', 'backend', 'rooten'),
				'description' => esc_html_x('Enable / Disable related post underneath of blog posts.', 'backend', 'rooten'),
				'section'     => 'blog',
				'settings'    => 'rooten_related_post',
				'type'        => 'select',
				'choices'     => array(
					1 => esc_html_x('Yes', 'backend', 'rooten'),
					0  => esc_html_x('No', 'backend', 'rooten')
				)
	        )
		));



		
		/**
		 * Layout Customizer Settings
		 */

		//Header section
		$this->wp_customize->add_section('header', array(
			'title' => esc_html_x('Header', 'backend', 'rooten'),
			'priority' => 31
		));


		$this->wp_customize->add_setting('rooten_header_layout', array(
			'default'           => 'horizontal-right',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new rooten_Customize_Header_Layout_Control( $this->wp_customize, 'rooten_header_layout', 
			array(
			'label'    => esc_html_x('Header Layout', 'backend', 'rooten'),
			'description' => esc_html_x('Select header layout from here. This header layout for global usage but you can change it from your page setting for specific page.', 'backend', 'rooten'),
			'section'  => 'header',
			'settings' => 'rooten_header_layout', 
			'type'     => 'layout_header',
			'choices'  => array(
				'horizontal-left'      => esc_html_x('Horizontal Left', 'backend', 'rooten'),
				'horizontal-center'    => esc_html_x('Horizontal Center', 'backend', 'rooten'),
				'horizontal-right'     => esc_html_x('Horizontal Right', 'backend', 'rooten'),
				'stacked-center-a'     => esc_html_x('Stacked Center A', 'backend', 'rooten'),
				'stacked-center-b'     => esc_html_x('Stacked Center B', 'backend', 'rooten'),
				'stacked-center-split' => esc_html_x('Stacked Center Split', 'backend', 'rooten'),
				'stacked-left-a'       => esc_html_x('Stacked Left A', 'backend', 'rooten'),
				'toggle-offcanvas'     => esc_html_x('Toggle Offcanvas', 'backend', 'rooten'),
				'toggle-modal'         => esc_html_x('Toggle Modal', 'backend', 'rooten'),
				'side-left'            => esc_html_x('Side Left', 'backend', 'rooten'),
				'side-right'           => esc_html_x('Side Right', 'backend', 'rooten'),
				'custom'        	   => esc_html_x('Custom Header', 'backend', 'rooten'),
			)
		)));


		$this->wp_customize->add_setting('rooten_custom_header', array(
			'default'           => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_custom_header', 
			array(
			'label'           => esc_html_x('Custom Header', 'backend', 'rooten'),
			'description'     => esc_html_x('Select your custom header which you made in rooten custom template section by elementor.', 'backend', 'rooten'),
			'section'         => 'header',
			'settings'        => 'rooten_custom_header',
			'type'            => 'select',
			'choices'         => rooten_custom_template_list(),
			'active_callback' => 'rooten_custom_header_yes_check',
		)));



		
		$this->wp_customize->add_setting('rooten_header_fullwidth', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_header_fullwidth', array(
			'label'       => esc_html_x('Header Fullwidth', 'backend', 'rooten'),
			'description' => esc_html_x('(Make your header full width like fluid width.)', 'backend', 'rooten'),
			'section'     => 'header',
			'settings'    => 'rooten_header_fullwidth', 
			'type'        => 'checkbox',
			'active_callback' => 'rooten_header_layout_check',
		)));

		$this->wp_customize->add_setting('rooten_header_bg_style', array(
			'default'           => 'default',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_header_bg_style', array(
			'label'    => esc_html_x('Background Style', 'backend', 'rooten'),
			'section'  => 'header',
			'settings' => 'rooten_header_bg_style',
			'type'     => 'select',
			'choices'  => array(
				'default'   => esc_html_x('Default', 'backend', 'rooten'),
				'muted'     => esc_html_x('Muted', 'backend', 'rooten'),
				'primary'   => esc_html_x('Primary', 'backend', 'rooten'),
				'secondary' => esc_html_x('Secondary', 'backend', 'rooten'),
				'media'     => esc_html_x('Image', 'backend', 'rooten'),
				//'video'     => esc_html_x('Video', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_header_transparent_check',
		));

		$this->wp_customize->add_setting( 'rooten_header_bg_img' , array(
			'sanitize_callback' => 'esc_url'
		));
		$this->wp_customize->add_control( new WP_Customize_Image_Control( $this->wp_customize, 'rooten_header_bg_img', array(
			'label'           => esc_html_x( 'Background Image', 'backend', 'rooten' ),
			'section'         => 'header',
			'settings'        => 'rooten_header_bg_img',
			'active_callback' => 'rooten_header_bg_style_check',
		)));

		$this->wp_customize->add_setting('rooten_header_bg_img_position', array(
			'default' => '',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_header_bg_img_position', array(
			'label'    => esc_html_x('Background Position', 'backend', 'rooten'),
			'section'  => 'header',
			'settings' => 'rooten_header_bg_img_position', 
			'type'     => 'select',
			'choices'  => array(
				'top-left'      => esc_html_x('Top Left', 'backend', 'rooten'),
				'top-center'    => esc_html_x('Top Center', 'backend', 'rooten'),
				'top-right'     => esc_html_x('Top Right', 'backend', 'rooten'),
				'center-left'   => esc_html_x('Center Left', 'backend', 'rooten'),
				''              => esc_html_x('Center Center', 'backend', 'rooten'),
				'center-right'  => esc_html_x('Center Right', 'backend', 'rooten'),
				'bottom-left'   => esc_html_x('Bottom Left', 'backend', 'rooten'),
				'bottom-center' => esc_html_x('Bottom Center', 'backend', 'rooten'),
				'bottom-right'  => esc_html_x('Bottom Right', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_header_bg_img_check',
		)));

		$this->wp_customize->add_setting('rooten_header_txt_style', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_header_txt_style', array(
			'label'    => esc_html_x('Header Text Color', 'backend', 'rooten'),
			'section'  => 'header',
			'settings' => 'rooten_header_txt_style', 
			'type'     => 'select',
			'choices'  => array(
				0       => esc_html_x('Default', 'backend', 'rooten'),
				'light' => esc_html_x('Light', 'backend', 'rooten'),
				'dark'  => esc_html_x('Dark', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_header_no_check',
		)));


		$this->wp_customize->add_setting('rooten_header_shadow', array(
			'default' => 'small',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_header_shadow', array(
			'label'    => esc_html_x('Shadow', 'backend', 'rooten'),
			'section'  => 'header',
			'settings' => 'rooten_header_shadow', 
			'type'     => 'select',
			'choices'  => array(
				0          => esc_html_x('No Shadow', 'backend', 'rooten'),
				'small'    => esc_html_x('Small', 'backend', 'rooten'),
				'medium'   => esc_html_x('Medium', 'backend', 'rooten'),
				'large'    => esc_html_x('Large', 'backend', 'rooten'),
				'xlarge' => esc_html_x('Extra Large', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_header_no_check',
		));


		$this->wp_customize->add_setting('rooten_header_transparent', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_header_transparent', array(
			'label'    => esc_html_x('Header Transparent', 'backend', 'rooten'),
			'section'  => 'header',
			'settings' => 'rooten_header_transparent', 
			'type'     => 'select',
			'choices'  => array(
				0       => esc_html_x('No', 'backend', 'rooten'),
				'light' => esc_html_x('Overlay (Light)', 'backend', 'rooten'),
				'dark'  => esc_html_x('Overlay (Dark)', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_header_layout_check',
		)));


		$this->wp_customize->add_setting('rooten_header_sticky', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_header_sticky', array(
			'label'    => esc_html_x('Header Sticky', 'backend', 'rooten'),
			'section'  => 'header',
			'settings' => 'rooten_header_sticky', 
			'type'     => 'select',
			'choices'  => array(
				0        => esc_html_x('No', 'backend', 'rooten'),
				'sticky' => esc_html_x('Sticky', 'backend', 'rooten'),
				'smart'  => esc_html_x('Smart Sticky', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_header_layout_check',
		)));

        // Add global color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'rooten_header_sticky_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
        		'background-color' => array(
        			'.bdt-navbar-container.bdt-sticky.bdt-active',
        		),
        	)
        )));

        
		$this->wp_customize->add_control(new WP_Customize_Color_Control( $this->wp_customize, 'rooten_header_sticky_color', array(
			'label'       => esc_html_x('Sticky Active Color', 'backend', 'rooten'),
			'section'     => 'header',
			'active_callback' => 'rooten_custom_header_no_check',
        )));
        


		$this->wp_customize->add_setting('rooten_navbar_style', array(
			'default' => 'style1',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_navbar_style', array(
			'label'    => esc_html_x('Main Menu Style', 'backend', 'rooten'),
			'section'  => 'header',
			'settings' => 'rooten_navbar_style', 
			'type'     => 'select',
			'choices'  => array(
				'style1' => esc_html_x('Top Line', 'backend', 'rooten'),
				'style2' => esc_html_x('Bottom Line', 'backend', 'rooten'),
				'style3'  => esc_html_x('Top Edge Line', 'backend', 'rooten'),
				'style4'  => esc_html_x('Bottom Edge Line', 'backend', 'rooten'),
				'style5'  => esc_html_x('Markar Mark', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_header_layout_check',
		)));


		$this->wp_customize->add_setting('rooten_search_position', array(
			'default' => 'header',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_search_position', array(
			'label'    => esc_html_x('Search', 'backend', 'rooten'),
			'description'    => esc_html_x('Select the position that will display the search.', 'backend', 'rooten'),
			'section'  => 'header',
			'settings' => 'rooten_search_position', 
			'type'     => 'select',
			'choices'  => array(
				0        => esc_html_x('Hide', 'backend', 'rooten'),
				'header' => esc_html_x('Header', 'backend', 'rooten'),
				'menu'   => esc_html_x('With Menu', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_header_no_check',
		)));

		$this->wp_customize->add_setting('rooten_search_style', array(
			'default' => 'default',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_search_style', array(
			'label'       => esc_html_x('Search Style', 'backend', 'rooten'),
			'description' => esc_html_x('Select search style from here.', 'backend', 'rooten'),
			'section'     => 'header',
			'settings'    => 'rooten_search_style', 
			'type'        => 'select',
			'choices'     => array(
				'default'  => esc_html_x('Default', 'backend', 'rooten'),
				'modal'    => esc_html_x('Modal', 'backend', 'rooten'),
				'dropdown' => esc_html_x('Dropdown', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_header_layout_check',
		)));

		$this->wp_customize->add_setting('rooten_mobile_offcanvas_style', array(
			'default' => 'offcanvas',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_mobile_offcanvas_style', array(
			'label'       => esc_html_x('Mobile Menu Style', 'backend', 'rooten'),
			'description' => 'Select the menu style displayed in the mobile position.',
			'section'     => 'header',
			'settings'    => 'rooten_mobile_offcanvas_style', 
			'type'        => 'select',
			'choices'     => array(
				'offcanvas' => esc_html_x('Offcanvas', 'backend', 'rooten'),
				'modal'     => esc_html_x('Modal', 'backend', 'rooten'),
				'dropdown'  => esc_html_x('Dropdown', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_header_no_check',
		)));


		$this->wp_customize->add_setting('rooten_mobile_offcanvas_mode', array(
			'default' => 'slide',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_mobile_offcanvas_mode', array(
			'label'       => esc_html_x('Offcanvas Mode', 'backend', 'rooten'),
			'section'     => 'header',
			'settings'    => 'rooten_mobile_offcanvas_mode', 
			'type'        => 'select',
			'choices'     => array(
				'slide'  => esc_html_x('Slide', 'backend', 'rooten'),
				'reveal' => esc_html_x('Reveal', 'backend', 'rooten'),
				'push'   => esc_html_x('Push', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_offcanvas_mode_check',
		)));

		
		$this->wp_customize->add_setting('rooten_mobile_break_point', array(
			'default' => 'm',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_mobile_break_point', array(
			'label'    => esc_html_x('Mobile Break Point', 'backend', 'rooten'),
			'section'  => 'header',
			'settings' => 'rooten_mobile_break_point', 
			'type'     => 'select',
			'choices'  => array(
				's' => esc_html_x('Small', 'backend', 'rooten'),
				'm' => esc_html_x('Medium', 'backend', 'rooten'),
				'l' => esc_html_x('Large', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_header_no_check',
		)));


		$this->wp_customize->add_setting('rooten_mobile_menu_align', array(
			'default' => 'left',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_mobile_menu_align', array(
			'label'    => esc_html_x('Mobile Menu Align', 'backend', 'rooten'),
			'section'  => 'header',
			'settings' => 'rooten_mobile_menu_align', 
			'type'     => 'select',
			'choices'  => array(
				''      => esc_html_x('Hide', 'backend', 'rooten'),
				'left'  => esc_html_x('Left', 'backend', 'rooten'),
				'right' => esc_html_x('Right', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_header_no_check',
		)));

		
		$this->wp_customize->add_setting('rooten_mobile_menu_text', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control('rooten_mobile_menu_text', array(
			'label'       => esc_html_x('Display the menu text next to the icon.', 'backend', 'rooten'),
			'section'     => 'header',
			'settings'    => 'rooten_mobile_menu_text',
			'type'        => 'checkbox',
			'active_callback' => 'rooten_custom_header_no_check',
		));
		



		// Main Body Settings
		$this->wp_customize->add_section('mainbody', array(
			'title'       => esc_html_x('Main Body', 'backend', 'rooten'),
			'description' => esc_html_x( 'Default body settings here.', 'backend', 'rooten' ),
			'priority'    => 33
		));

		$this->wp_customize->add_setting('rooten_sidebar_position', array(
			'default' => 'sidebar-right',
			'sanitize_callback' => 'rooten_sanitize_choices',
		));
		$this->wp_customize->add_control(new rooten_Customize_Layout_Control( $this->wp_customize, 'rooten_sidebar_position', 
			array(
				'label'       => esc_html_x('Sidebar Layout', 'backend', 'rooten'),
				'description' => esc_html_x('Select global page sidebar position from here. If you already set any sidebar setting from specific page so it will not applicable for that page.', 'backend', 'rooten'),
				'section'     => 'mainbody',
				'settings'    => 'rooten_sidebar_position', 
				'type'        => 'layout',
				'choices' => array(
					"sidebar-left"  => esc_html_x('Sidebar Left', 'backend', 'rooten'), 
					"full"          => esc_html_x('No Sidebar', 'backend', 'rooten'),
					"sidebar-right" => esc_html_x('Sidebar Right', 'backend', 'rooten'),
				),
				'active_callback' => 'rooten_homepage_check',
			)
		));


		$this->wp_customize->add_setting('rooten_main_bg_style', array(
			'default' => 'default',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_main_bg_style', array(
			'label'    => esc_html_x('Background Style', 'backend', 'rooten'),
			'section'  => 'mainbody',
			'settings' => 'rooten_main_bg_style', 
			'type'     => 'select',
			'choices'  => array(
				'default'   => esc_html_x('Default', 'backend', 'rooten'),
				'muted'     => esc_html_x('Muted', 'backend', 'rooten'),
				'primary'   => esc_html_x('Primary', 'backend', 'rooten'),
				'secondary' => esc_html_x('Secondary', 'backend', 'rooten'),
				'media'     => esc_html_x('Image', 'backend', 'rooten'),
				//'video'     => esc_html_x('Video', 'backend', 'rooten'),
			)
		));
		

		$this->wp_customize->add_setting( 'rooten_main_bg_img' , array(
			'sanitize_callback' => 'esc_url'
		));
		$this->wp_customize->add_control( new WP_Customize_Image_Control( $this->wp_customize, 'rooten_main_bg_img', array(
			'label'           => esc_html_x( 'Background Image', 'backend', 'rooten' ),
			'section'         => 'mainbody',
			'settings'        => 'rooten_main_bg_img',
			'active_callback' => 'rooten_main_bg_check',
		)));

		$this->wp_customize->add_setting('rooten_main_txt_style', array(
			'default'           => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_main_txt_style', array(
			'label'    => esc_html_x('Text Color', 'backend', 'rooten'),
			'section'  => 'mainbody',
			'settings' => 'rooten_main_txt_style', 
			'type'     => 'select',
			'choices'  => array(
				0       => esc_html_x('Default', 'backend', 'rooten'),
				'light' => esc_html_x('Light', 'backend', 'rooten'),
				'dark'  => esc_html_x('Dark', 'backend', 'rooten'),
			)
		));

		$this->wp_customize->add_setting('rooten_sidebar_width', array(
			'default' => '1-4',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_sidebar_width', array(
			'label'    => esc_html_x('Sidebar Width', 'backend', 'rooten'),
			'description' => esc_html_x('Set a sidebar width in percent and the content column will adjust accordingly. The width will not go below the Sidebar\'s min-width, which you can set in the Style section.', 'backend', 'rooten'),
			'section'  => 'mainbody',
			'settings' => 'rooten_sidebar_width', 
			'type'     => 'select',
			'choices'  => array(
				'1-5' => esc_html_x('20%', 'backend', 'rooten'),
				'1-4' => esc_html_x('25%', 'backend', 'rooten'),
				'1-3' => esc_html_x('33%', 'backend', 'rooten'),
				'2-5' => esc_html_x('40%', 'backend', 'rooten'),
				'1-2' => esc_html_x('50%', 'backend', 'rooten'),
			)
		));


		$this->wp_customize->add_setting('rooten_sidebar_gutter', array(
			'default' => 'large',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_sidebar_gutter', array(
			'label'    => esc_html_x('Gutter', 'backend', 'rooten'),
			'section'  => 'mainbody',
			'settings' => 'rooten_sidebar_gutter', 
			'type'     => 'select',
			'choices'  => array(
				'small'    => esc_html_x('Small', 'backend', 'rooten'),
				'medium'   => esc_html_x('Medium', 'backend', 'rooten'),
				0          => esc_html_x('Default', 'backend', 'rooten'),
				'large'    => esc_html_x('Large', 'backend', 'rooten'),
				'collapse' => esc_html_x('Collapse', 'backend', 'rooten'),
			)
		));

		$this->wp_customize->add_setting('rooten_sidebar_divider', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control('rooten_sidebar_divider', array(
			'label'           => esc_html_x('Display dividers between body and sidebar', 'backend', 'rooten'),
			'description'     => esc_html_x('(Set the grid gutter width and display dividers between grid cells.)', 'backend', 'rooten'),
			'section'         => 'mainbody',
			'settings'        => 'rooten_sidebar_divider',
			//'active_callback' => 'rooten_bottom_gutter_collapse_check',
			'type'            => 'checkbox'
		));


		$this->wp_customize->add_setting('rooten_sidebar_breakpoint', array(
			'default' => 'm',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_sidebar_breakpoint', array(
			'label'       => esc_html_x('Breakpoint', 'backend', 'rooten'),
			'description' => esc_html_x('Set the breakpoint from which the sidebar and content will stack.', 'backend', 'rooten'),
			'section'     => 'mainbody',
			'settings'    => 'rooten_sidebar_breakpoint', 
			'type'        => 'select',
			'choices'     => array(
				's'  => esc_html_x('Small (Phone Landscape)', 'backend', 'rooten'),
				'm'  => esc_html_x('Medium (Tablet Landscape)', 'backend', 'rooten'),
				'l'  => esc_html_x('Large (Desktop)', 'backend', 'rooten'),
				'xl' => esc_html_x('X-Large (Large Screens)', 'backend', 'rooten'),
			)
		));






		$this->wp_customize->add_setting('rooten_main_padding', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_main_padding', array(
			'label'    => esc_html_x('Padding', 'backend', 'rooten'),
			'section'  => 'mainbody',
			'settings' => 'rooten_main_padding', 
			'type'     => 'select',
			'choices'  => array(
				0        => esc_html_x('Default', 'backend', 'rooten'),
				'small'  => esc_html_x('Small', 'backend', 'rooten'),
				'medium' => esc_html_x('Medium', 'backend', 'rooten'),
				'large'  => esc_html_x('Large', 'backend', 'rooten'),
				'none'   => esc_html_x('None', 'backend', 'rooten'),
			)
		));



		// Background image for body tag
		$this->wp_customize->add_setting('rooten_bg_note', array(
				'default'           => '',
				'sanitize_callback' => 'esc_attr'
		    )
		);
		$this->wp_customize->add_control( new rooten_Customize_Alert_Control( $this->wp_customize, 'rooten_bg_note', array(
			'label'       => 'Background Alert',
			'section'     => 'background_image',
			'settings'    => 'rooten_bg_note',
			'type'        => 'alert',
			'priority'    => 1,
			'text'        => esc_html_x('You must set your layout mode Boxed for use this feature. Otherwise you can\'t see what happening in background', 'backend', 'rooten'),
			'alert_type' => 'warning',
		    )) 
		);

		$this->wp_customize->add_panel('colors', array(
			'title' => esc_html_x('Colors', 'backend', 'rooten'),
			'priority' => 45
		));

		$this->wp_customize->add_section('colors_global', array(
			'title' => esc_html_x('Global Colors', 'backend', 'rooten'),
			'panel' => 'colors',
		));

		$this->wp_customize->add_section('colors_button', array(
			'title' => esc_html_x('Button Colors', 'backend', 'rooten'),
			'panel' => 'colors',
		));

		$this->wp_customize->add_section('colors_header', array(
			'title' => esc_html_x('Header Colors', 'backend', 'rooten'),
			'panel' => 'colors',
		));

		$this->wp_customize->add_section('colors_menu', array(
			'title' => esc_html_x('Menu Colors', 'backend', 'rooten'),
			'panel' => 'colors',
		));

		$this->wp_customize->add_section('colors_offcanvas', array(
			'title' => esc_html_x('Offcanvas Colors', 'backend', 'rooten'),
			'panel' => 'colors',
		));

		$this->wp_customize->add_section('colors_footer', array(
			'title' => esc_html_x('Footer Colors', 'backend', 'rooten'),
			'panel' => 'colors',
		));

		$this->wp_customize->add_control(new WP_Customize_Color_Control( $this->wp_customize, 'background_color', array(
			'label'       => esc_html_x('Global Background Color', 'backend', 'rooten'),
			'section'     => 'colors_global',
			'description' => esc_html_x('Please select layout boxed for check your global page background.', 'backend', 'rooten'),
        )));
        

        // Add global color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'global_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
        		'color' => array(
        			'body',
        		),
        	)
        )));

        // Add global color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'global_color', array(
        	'label'       => esc_html_x( 'Global Text Color', 'backend', 'rooten' ),
        	'section'     => 'colors_global',
        )));


        // Add global link color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'primary_background_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
				'background-color' => array(
					'.bdt-button-primary',
					'.bdt-section-primary',
					'.bdt-background-primary',
					'.bdt-card-primary',
					
				),
        	)
        )));

        // Add global link color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'primary_background_color', array(
        	'label'       => esc_html_x( 'Primary Background Color', 'backend', 'rooten' ),
        	'section'     => 'colors_global',
        )));


        // Add global link color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'primary_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
        		'color' => array(
					'a',
					'.bdt-link',
					'.bdt-text-primary',
					'.bdt-alert-primary',
					'.we-are-open li div.bdt-width-expand span',
					'.woocommerce .star-rating span',
				),
				'border-color' => array(
					'.bdt-input:focus', 
					'.bdt-select:focus', 
					'.bdt-textarea:focus',
					'.tm-bottom.bdt-section-custom .bdt-button-default:hover',
				),
				'background-color' => array(
					'.bdt-label',
					'.bdt-subnav-pill > .bdt-active > a',
					'.tm-header .bdt-navbar-nav>li.bdt-active>a::before',
					'.tm-header .bdt-navbar-nav>li:hover>a::before', 
					'.tm-header .bdt-navbar-nav>li>a:focus::before', 
					'.tm-header .bdt-navbar-nav>li>a.bdt-open::before',
					'.tm-header .bdt-navbar-nav>li.bdt-active>a::after',
					'.tm-header .bdt-navbar-nav>li:hover>a::after', 
					'.tm-header .bdt-navbar-nav>li>a:focus::after', 
					'.tm-header .bdt-navbar-nav>li>a.bdt-open::after',
					'.tm-header:not(.bdt-light) .bdt-navbar-nav>li.bdt-active>a::before',
					'.tm-header:not(.bdt-light) .bdt-navbar-nav>li.bdt-active>a::after',
					'.tm-header .bdt-navbar-dropdown-nav>li.bdt-active>a:after',
					'.tm-header .bdt-navbar-dropdown ul.bdt-navbar-dropdown-nav ul li.bdt-active a:after',
					'[class*=\'navbar-style\'] .tm-header .bdt-navbar .bdt-navbar-nav > li:hover > a::before', 
					'[class*=\'navbar-style\'] .tm-header .bdt-navbar .bdt-navbar-nav > li:hover > a::after', 
					'.tm-header .bdt-navbar-dropdown ul.bdt-navbar-dropdown-nav li.bdt-parent > a:after',
				),
				'background-color|lighten(5)' => array(
					'.woocommerce-page .widget_price_filter .ui-slider .ui-slider-handle', 
					'.woocommerce .widget_price_filter .ui-slider .ui-slider-handle',
				),
        	)
        )));

        // Add global link color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'primary_color', array(
        	'label'       => esc_html_x( 'Primary Color', 'backend', 'rooten' ),
        	'section'     => 'colors_global',
        )));



        // Add global link hover color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'primary_hover_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
        		'color' => array(
        			'a:hover',
        			'.bdt-link:hover',
        			'.bdt-text-primary:hover',
        			'.bdt-alert-primary:hover',
        		),
        		'background-color' => array(
        			'.tm-header .bdt-navbar-dropdown ul.bdt-navbar-dropdown-nav li.bdt-active > a:after',
        			'[class*=\'navbar-style\'] .bdt-navbar .bdt-navbar-nav > li.bdt-active > a::before',
        			'[class*=\'navbar-style\'] .bdt-navbar .bdt-navbar-nav > li.bdt-active > a::after',
        		),
        		'border-color' => array(
        			
        		),
        	)
        )));

        // Add global link hover color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'primary_hover_color', array(
        	'label'       => esc_html_x( 'Primary Hover Color', 'backend', 'rooten' ),
        	'section'     => 'colors_global',
        )));


        // Add secondary color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'secondary_background_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
				'background-color' => array(
					'.bdt-button-secondary',
					'.bdt-section-secondary',
					'.bdt-background-secondary',
					'.bdt-card-secondary',
				),
        	)
        )));

        // Add secondary color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'secondary_background_color', array(
        	'label'       => esc_html_x( 'Secondary Background Color', 'backend', 'rooten' ),
        	'section'     => 'colors_global',
        )));


        // Add secondary color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'secondary_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
        		'color' => array(
					'.bdt-text-secondary',
					'.bdt-alert-secondary',
				),
        	)
        )));

        // Add secondary color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'secondary_color', array(
        	'label'       => esc_html_x( 'Secondary Color', 'backend', 'rooten' ),
        	'section'     => 'colors_global',
        )));


        // Add muted color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'muted_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
        		'color' => array(
					'.bdt-text-muted',
					'.bdt-alert-muted',
				),
        	)
        )));

        // Add muted color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'muted_color', array(
        	'label'       => esc_html_x( 'Muted Text Color', 'backend', 'rooten' ),
        	'section'     => 'colors_global',
        )));

        // Add muted color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'muted_background_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
				'background-color' => array(
					'.bdt-button-muted',
					'.bdt-section-muted',
					'.bdt-background-muted',
					'.bdt-card-muted',
				),
        	)
        )));

        // Add muted color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'muted_background_color', array(
        	'label'       => esc_html_x( 'Muted Background Color', 'backend', 'rooten' ),
        	'section'     => 'colors_global',
        )));


         // Add button default color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'button_default_background_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
				'background-color' => array(
					'.bdt-button-default',
				),
				'background-color|lighten(5)' => array(
					'.bdt-button-default:hover',
				),
        	)
        )));

        // Add button default color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'button_default_background_color', array(
        	'label'       => esc_html_x( 'Default Background Color', 'backend', 'rooten' ),
        	'section'     => 'colors_button',
        )));


         // Add button default color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'button_default_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
				'color' => array(
					'.bdt-button-default',
				),
				'color|lighten(5)' => array(
					'.bdt-button-default:hover',
				),
        	)
        )));

        // Add button default color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'button_default_color', array(
        	'label'       => esc_html_x( 'Default Color', 'backend', 'rooten' ),
        	'section'     => 'colors_button',
        )));


         // Add button primary color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'button_primary_background_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
				'background-color' => array(
					'.bdt-button-primary',
        			'.bdt-button-primary:active', 
        			'.bdt-button-primary.bdt-active',
        			'.bdt-button-primary:focus',
				),
				'background-color|lighten(5)' => array(
					'.bdt-button-primary:hover',
				),
        	)
        )));

        // Add button primary color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'button_primary_background_color', array(
        	'label'       => esc_html_x( 'Primary Background Color', 'backend', 'rooten' ),
        	'section'     => 'colors_button',
        )));


         // Add button primary color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'button_primary_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
				'color' => array(
					'.bdt-button-primary',
				),
				'color|lighten(5)' => array(
					'.bdt-button-primary:hover',
				),
        	)
        )));

        // Add button primary color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'button_primary_color', array(
        	'label'       => esc_html_x( 'Primary Color', 'backend', 'rooten' ),
        	'section'     => 'colors_button',
        )));


         // Add button secondary color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'button_secondary_background_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
				'background-color' => array(
					'.bdt-button-secondary',
				),
				'background-color|lighten(5)' => array(
					'.bdt-button-secondary:hover',
				),
        	)
        )));

        // Add button secondary color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'button_secondary_background_color', array(
        	'label'       => esc_html_x( 'Secondary Background Color', 'backend', 'rooten' ),
        	'section'     => 'colors_button',
        )));


         // Add button secondary color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'button_secondary_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
				'color' => array(
					'.bdt-button-secondary',
				),
				'color|lighten(5)' => array(
					'.bdt-button-secondary:hover',
				),
        	)
        )));

        // Add button secondary color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'button_secondary_color', array(
        	'label'       => esc_html_x( 'Secondary Color', 'backend', 'rooten' ),
        	'section'     => 'colors_button',
        )));


         // Add button danger color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'button_danger_background_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
				'background-color' => array(
					'.bdt-button-danger',
				),
				'background-color|lighten(5)' => array(
					'.bdt-button-danger:hover',
				),
        	)
        )));

        // Add button danger color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'button_danger_background_color', array(
        	'label'       => esc_html_x( 'Danger Background Color', 'backend', 'rooten' ),
        	'section'     => 'colors_button',
        )));


         // Add button danger color setting.
        $this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'button_danger_color', array(
        	'sanitize_callback' => 'sanitize_hex_color',
        	'css_map' => array(
				'color' => array(
					'.bdt-button-danger',
				),
				'color|lighten(5)' => array(
					'.bdt-button-danger:hover',
				),
        	)
        )));

        // Add button danger color control.
        $this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'button_muted_color', array(
        	'label'       => esc_html_x( 'Danger Color', 'backend', 'rooten' ),
        	'section'     => 'colors_button',
        )));

        // Add page background color setting and control.
		$this->wp_customize->add_setting( 'browser_header_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
		));

		$this->wp_customize->add_control(new WP_Customize_Color_Control( $this->wp_customize, 'browser_header_color', array(
			'label'       => esc_html_x('Browser Header Color', 'backend', 'rooten'),
			'section'     => 'colors_global',
			'description' => esc_html_x('This color for mobile browser header. This color works only mobile view.' , 'backend', 'rooten'),
        )));



		/**
		 * Footer Customizer Settings
		 */

		// footer appearance
		$this->wp_customize->add_section('footer', array(
			'title' => esc_html_x('Footer', 'backend', 'rooten'),
			'description' => esc_html_x( 'All Rooten theme specific settings.', 'backend', 'rooten' ),
			'priority' => 140
		));

		$this->wp_customize->add_setting('rooten_footer_widgets', array(
			'default' => 1,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control($this->wp_customize, 'rooten_footer_widgets',
	        array(
				'priority'    => 1,
				'label'       => esc_html_x('Footer Widgets', 'backend', 'rooten'),
				'section'     => 'footer',
				'settings'    => 'rooten_footer_widgets',
				'type'        => 'select',
				'choices'     => array(
					1        => esc_html_x('Default', 'backend', 'rooten'),
					0        => esc_html_x('Hide', 'backend', 'rooten'),
					'custom' => esc_html_x('Custom Footer', 'backend', 'rooten')
				)
	        )
		));


		$this->wp_customize->add_setting('rooten_custom_footer', array(
			'default'           => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_custom_footer', 
			array(
			'label'    => esc_html_x('Custom Footer', 'backend', 'rooten'),
			'description' => esc_html_x('Select your custom footer which you made in rooten custom template section by elementor.', 'backend', 'rooten'),
			'section'  => 'footer',
			'settings' => 'rooten_custom_footer', 
			'type'     => 'select',
			'choices'  => rooten_custom_template_list('footer'),
			'active_callback' => 'rooten_custom_footer_yes_check',
		)));


		$this->wp_customize->add_setting('rooten_footer_columns', array(
			'default' => 4,
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_footer_columns', array(
			'label'           => esc_html_x('Footer Columns:', 'backend', 'rooten'),
			'section'         => 'footer',
			'settings'        => 'rooten_footer_columns',
			'type'            => 'select',
			'choices'         => array(
				1 => esc_html_x('1 Column', 'backend', 'rooten'),
				2 => esc_html_x('2 Columns', 'backend', 'rooten'),
				3 => esc_html_x('3 Columns', 'backend', 'rooten'),
				4 => esc_html_x('4 Columns', 'backend', 'rooten')
			),
			'active_callback' => 'rooten_custom_footer_no_check',
		));
		
		$this->wp_customize->add_setting('rooten_footer_fce', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control('rooten_footer_fce', array(
			'label'       => esc_html_x('First Column Double Width', 'backend', 'rooten'),
			'description' => esc_html_x('some times your need first footer column double size so you can checked it.', 'backend', 'rooten'),
			'section'     => 'footer',
			'settings'    => 'rooten_footer_fce',
			'type'        => 'checkbox',
			'active_callback' => 'rooten_custom_footer_no_check',
		));

		//header section
		if (class_exists('Woocommerce')){
			$this->wp_customize->add_section('woocommerce_layout', array(
				'title'    => esc_html_x('Layout', 'backend', 'rooten'),
				'priority' => 2,
				'panel'    => 'woocommerce',
			));


			$this->wp_customize->add_setting('rooten_woocommerce_sidebar', array(
				'default' => 'sidebar-left',
				'sanitize_callback' => 'rooten_sanitize_choices',
			));
			$this->wp_customize->add_control(new rooten_Customize_Layout_Control( $this->wp_customize, 'rooten_woocommerce_sidebar', 
				array(
					'label'       => esc_html_x('Shop Page Sidebar', 'backend', 'rooten'),
					'description' => esc_html_x('Make sure you add your widget in shop widget position.', 'backend', 'rooten'),
					'section'     => 'woocommerce_layout',
					'settings'    => 'rooten_woocommerce_sidebar', 
					'choices' => array(
						"sidebar-left"  => esc_html_x('Sidebar Left', 'backend', 'rooten'), 
						"full"          => esc_html_x('Fullwidth', 'backend', 'rooten'),
						"sidebar-right" => esc_html_x('Sidebar Right', 'backend', 'rooten'),
					),
					//'active_callback' => 'is_front_page',
				)
			));

			//avatar shape
			$this->wp_customize->add_setting('rooten_woocommerce_cart', array(
				'default'           => 'no',
				'sanitize_callback' => 'rooten_sanitize_choices'
			));
			$this->wp_customize->add_control('rooten_woocommerce_cart', array(
				'label'       => esc_html_x('Shopping Cart Icon in Header:', 'backend', 'rooten'),
				'description' => esc_html_x('Enable / Disable Shopping Cart Icon', 'backend', 'rooten'),
				'section'     => 'woocommerce_layout',
				'settings'    => 'rooten_woocommerce_cart', 
				'type'        => 'select',
				'choices'     => array(
					'no'      => esc_html_x('No Need', 'backend', 'rooten'),
					'header'  => esc_html_x('Yes (in header)', 'backend', 'rooten'),
					'toolbar' => esc_html_x('Yes (in toolbar)', 'backend', 'rooten'),
				)
			));


			$this->wp_customize->add_setting('rooten_woocommerce_columns', array(
				'default' => 3,
				'sanitize_callback' => 'rooten_sanitize_choices'
			) );
			$this->wp_customize->add_control('rooten_woocommerce_columns', array(
				'label'    => esc_html_x('Product Columns:', 'backend', 'rooten'),
				'section'  => 'woocommerce_layout',
				'settings' => 'rooten_woocommerce_columns', 
				'type'     => 'select',
				'choices'  => array(
					2 => esc_html_x('2 Columns', 'backend', 'rooten'),
					3 => esc_html_x('3 Columns', 'backend', 'rooten'),
					4 => esc_html_x('4 Columns', 'backend', 'rooten')
				)
			));


			$this->wp_customize->add_setting('rooten_woocommerce_limit', array(
				'default' => 9,
				'sanitize_callback' => 'esc_attr'
			));
			$this->wp_customize->add_control('rooten_woocommerce_limit', array(
				'label'       => esc_html_x('Items per Shop Page: ', 'backend', 'rooten'),
				'description' => esc_html_x('Enter how many items you want to show on Shop pages & Categorie Pages before Pagination shows up (Default: 9)', 'backend', 'rooten'),
				'section'     => 'woocommerce_layout',
				'settings'    => 'rooten_woocommerce_limit'
			));

			$this->wp_customize->add_setting('rooten_woocommerce_sort', array(
				'default' => 1,
				'sanitize_callback' => 'rooten_sanitize_checkbox'
			));
			$this->wp_customize->add_control('rooten_woocommerce_sort', array(
				'label'       => esc_html_x('Shop Sort', 'backend', 'rooten'),
				'description' => esc_html_x('(Enable / Disable sort-by function on Shop Pages)', 'backend', 'rooten'),
				'section'     => 'woocommerce_layout',
				'settings'    => 'rooten_woocommerce_sort',
				'type'        => 'checkbox'
			));


			$this->wp_customize->add_setting('rooten_woocommerce_result_count', array(
				'default' => 1,
				'sanitize_callback' => 'rooten_sanitize_checkbox'
			));
			$this->wp_customize->add_control('rooten_woocommerce_result_count', array(
				'label'       => esc_html_x('Shop Result Count', 'backend', 'rooten'),
				'description' => esc_html_x('(Enable / Disable Result Count on Shop Pages)', 'backend', 'rooten'),
				'section'     => 'woocommerce_layout',
				'settings'    => 'rooten_woocommerce_result_count',
				'type'        => 'checkbox'
			));

			$this->wp_customize->add_setting('rooten_woocommerce_cart_button', array(
				'default' => 1,
				'sanitize_callback' => 'rooten_sanitize_checkbox'
			));
			$this->wp_customize->add_control('rooten_woocommerce_cart_button', array(
				'label'       => esc_html_x('Add to Cart Button', 'backend', 'rooten'),
				'description' => esc_html_x('(Enable / Disable "Add to Cart"-Button on Shop Pages)', 'backend', 'rooten'),
				'section'     => 'woocommerce_layout',
				'settings'    => 'rooten_woocommerce_cart_button',
				'type'        => 'checkbox'
			));

			$this->wp_customize->add_setting('rooten_woocommerce_upsells', array(
				'default' => 0,
				'sanitize_callback' => 'rooten_sanitize_checkbox'
			));
			$this->wp_customize->add_control('rooten_woocommerce_upsells', array(
				'label'       => esc_html_x('Upsells Products', 'backend', 'rooten'),
				'description' => esc_html_x('(Enable / Disable to show upsells Products on Product Item Details)', 'backend', 'rooten'),
				'section'     => 'woocommerce_layout',
				'settings'    => 'rooten_woocommerce_upsells',
				'type'        => 'checkbox'
			));
			$this->wp_customize->add_setting('rooten_woocommerce_related', array(
				'default' => 1,
				'sanitize_callback' => 'rooten_sanitize_checkbox'
			));
			$this->wp_customize->add_control('rooten_woocommerce_related', array(
				'label'       => esc_html_x('Related Products', 'backend', 'rooten'),
				'description' => esc_html_x('(Enable / Disable to show related Products on Product Item Details)', 'backend', 'rooten'),
				'section'     => 'woocommerce_layout',
				'settings'    => 'rooten_woocommerce_related',
				'type'        => 'checkbox'
			));
		}

		// Add dropdown background color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'menu_link_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'.tm-header .bdt-navbar-nav > li > a',
				),
			)
		)));

		// Add dropdown background color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'menu_link_color', array(
			'label'       => esc_html_x( 'Menu Link Color', 'backend', 'rooten' ),
			'section'     => 'colors_menu',
		)));


		// Add dropdown background color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'menu_link_hover_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'.tm-header .bdt-navbar-nav > li:hover > a',
				),
			)
		)));

		// Add dropdown background color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'menu_link_hover_color', array(
			'label'       => esc_html_x( 'Menu Hover Color', 'backend', 'rooten' ),
			'section'     => 'colors_menu',
		)));


		// Add menu active color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'menu_link_active_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'.tm-header .bdt-navbar-nav > li.bdt-active > a',
				),
			)
		)));

		// Add menu active color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'menu_link_active_color', array(
			'label'       => esc_html_x( 'Menu Active Color', 'backend', 'rooten' ),
			'section'     => 'colors_menu',
		)));


		// Add style active color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'menu_style_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'background-color' => array(
					'.navbar-style2 .tm-header .bdt-navbar .bdt-navbar-nav > li.bdt-active > a::after', 
					'.navbar-style4 .tm-header .bdt-navbar .bdt-navbar-nav > li.bdt-active > a::after',
				),
			)
		)));

		// Add style active color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'menu_style_color', array(
			'label'       => esc_html_x( 'Menu Style Color', 'backend', 'rooten' ),
			'section'     => 'colors_menu',
		)));


		// Add dropdown background color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'dropdown_background_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'background-color' => array(
					'.tm-header .bdt-navbar-dropbar',
					'.tm-header .bdt-navbar-dropdown:not(.bdt-navbar-dropdown-dropbar)',
					'.tm-header .bdt-navbar-dropdown:not(.bdt-navbar-dropdown-dropbar) .sub-dropdown>ul',
				),
			)
		)));

		// Add dropdown background color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'dropdown_background_color', array(
			'label'       => esc_html_x( 'Dropdown Background Color', 'backend', 'rooten' ),
			'section'     => 'colors_menu',
		)));


		// Add dropdown link color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'dropdown_link_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'.tm-header .bdt-navbar-dropdown-nav>li>a',
					'.tm-header .bdt-nav li>a',
					'.tm-header .bdt-navbar-dropdown-nav .bdt-nav-sub a',
				),
			)
		)));

		// Add dropdown link color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'dropdown_link_color', array(
			'label'       => esc_html_x( 'Dropdown Link Color', 'backend', 'rooten' ),
			'section'     => 'colors_menu',
		)));


		// Add dropdown link hover color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'dropdown_link_hover_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'.tm-header .bdt-navbar-dropdown ul.bdt-navbar-dropdown-nav li > a:hover', 
					'.tm-header .bdt-navbar-dropdown ul.bdt-navbar-dropdown-nav li > a:focus',
					'.tm-header .bdt-navbar-dropdown-nav .bdt-nav-sub a:hover', 
					'.tm-header .bdt-navbar-dropdown-nav .bdt-nav-sub a:focus',
					'.tm-header .bdt-navbar-dropdown-nav li.bdt-active > a',
					'.tm-header .bdt-navbar-dropdown .sub-dropdown > ul li.bdt-active > a',
				),
			)
		)));

		// Add dropdown link hover color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'dropdown_link_hover_color', array(
			'label'       => esc_html_x( 'Dropdown Link Hover Color', 'backend', 'rooten' ),
			'section'     => 'colors_menu',
		)));

		// Add dropdown link hover color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'dropdown_link_active_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'.tm-header .bdt-navbar-dropdown ul.bdt-navbar-dropdown-nav li.bdt-active > a', 
					'.tm-header .bdt-navbar-dropdown ul.bdt-navbar-dropdown-nav li.bdt-active > a',
					'.tm-header .bdt-navbar-dropdown .sub-dropdown > ul li.bdt-active > a',
				),
			)
		)));

		// Add dropdown link hover color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'dropdown_link_active_color', array(
			'label'       => esc_html_x( 'Dropdown Link Active Color', 'backend', 'rooten' ),
			'section'     => 'colors_menu',
		)));


		// Add offcanvas background color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'offcanvas_background_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'background-color' => array(
					'.bdt-offcanvas-bar',
				),
			)
		)));
		// Add offcanvas background color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'offcanvas_background_color', array(
			'label'       => esc_html_x( 'Offcanvas Background Color', 'backend', 'rooten' ),
			'section'     => 'colors_offcanvas',
		)));

		
		// Add offcanvas text color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'offcanvas_text_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'.bdt-offcanvas-bar',
					'.bdt-offcanvas-bar .bdt-search-input',
					'.bdt-offcanvas-bar .bdt-search-icon.bdt-icon',
					'.bdt-offcanvas-bar .bdt-search-input::placeholder',
				),
			)
		)));
		// Add offcanvas text color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'offcanvas_text_color', array(
			'label'           => esc_html_x( 'Text Color', 'backend', 'rooten' ),
			'section'         => 'colors_offcanvas',
		)));


		// Add offcanvas link color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'offcanvas_link_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'.bdt-offcanvas-bar .bdt-icon',
					'.bdt-offcanvas-bar #nav-offcanvas li a',
				),
			)
		)));
		// Add offcanvas link color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'offcanvas_link_color', array(
			'label'           => esc_html_x( 'Link Color', 'backend', 'rooten' ),
			'section'         => 'colors_offcanvas',
		)));


		// Add offcanvas link active color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'offcanvas_link_active_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'.bdt-offcanvas-bar #nav-offcanvas li.bdt-active a',
				),
			)
		)));
		// Add offcanvas link active color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'offcanvas_link_active_color', array(
			'label'           => esc_html_x( 'Link Active Color', 'backend', 'rooten' ),
			'section'         => 'colors_offcanvas',
		)));


		// Add offcanvas link hover color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'offcanvas_link_hover_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'.bdt-offcanvas-bar .bdt-icon:hover',
					'.bdt-offcanvas-bar #nav-offcanvas li a:hover',
				),
			)
		)));
		// Add offcanvas link hover color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'offcanvas_link_hover_color', array(
			'label'           => esc_html_x( 'Link Hover Color', 'backend', 'rooten' ),
			'section'         => 'colors_offcanvas',
		)));

		
		// Add offcanvas border color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'offcanvas_border_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'border-color' => array(
					'.bdt-offcanvas-bar .offcanvas-search .bdt-search .bdt-search-input',
					'.bdt-offcanvas-bar hr',
				),
			)
		)));
		// Add offcanvas border color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'offcanvas_border_color', array(
			'label'           => esc_html_x( 'Border Color', 'backend', 'rooten' ),
			'section'         => 'colors_offcanvas',
		)));


		// Bottom bg style setting
		$this->wp_customize->add_setting('rooten_bottom_bg_style', array(
			'default' => 'secondary',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));

		$this->wp_customize->add_control('rooten_bottom_bg_style', array(
			'label'    => esc_html_x('Background Style', 'backend', 'rooten'),
			'section'  => 'footer',
			'settings' => 'rooten_bottom_bg_style', 
			'type'     => 'select',
			'choices'  => array(
				'default'   => esc_html_x('Default', 'backend', 'rooten'),
				'muted'     => esc_html_x('Muted', 'backend', 'rooten'),
				'primary'   => esc_html_x('Primary', 'backend', 'rooten'),
				'secondary' => esc_html_x('Secondary', 'backend', 'rooten'),
				'media'     => esc_html_x('Image', 'backend', 'rooten'),
				'custom'    => esc_html_x('Custom Color', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_footer_no_check',
		));

		// Add footer background color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'bottom_background_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'background-color' => array(
					'.tm-bottom.bdt-section-custom',
				),
				'border-color|lighten(5)' => array(
					'.tm-bottom.bdt-section-custom .bdt-grid-divider > :not(.bdt-first-column)::before',
					'.tm-bottom.bdt-section-custom hr', 
					'.tm-bottom.bdt-section-custom .bdt-hr',
					'.tm-bottom.bdt-section-custom .bdt-grid-divider.bdt-grid-stack>.bdt-grid-margin::before',
				),
				'background-color|lighten(2)' => array(
					'.tm-bottom.bdt-section-custom .widget_tag_cloud a',
				),
			)
		)));

		// Add footer background color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'bottom_background_color', array(
			'label'           => esc_html_x( 'Custom Background Color', 'backend', 'rooten' ),
			'section'         => 'footer',
			'active_callback' => 'rooten_bottom_bg_custom_color_check',
		)));


		$this->wp_customize->add_setting( 'rooten_bottom_bg_img' , array(
			'sanitize_callback' => 'esc_url'
		));
		$this->wp_customize->add_control( new WP_Customize_Image_Control( $this->wp_customize, 'rooten_bottom_bg_img', array(
		    'label'    => esc_html_x( 'Background Image', 'backend', 'rooten' ),
		    'section'  => 'footer',
		    'settings' => 'rooten_bottom_bg_img',
		    'active_callback' => 'rooten_bottom_bg_style_check',
		)));


		$this->wp_customize->add_setting('rooten_bottom_bg_img_position', array(
			'default' => '',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_bottom_bg_img_position', array(
			'label'    => esc_html_x('Background Position', 'backend', 'rooten'),
			'description' => esc_html_x('Set the initial background position, relative to the section layer.', 'backend', 'rooten'),
			'section'  => 'footer',
			'settings' => 'rooten_bottom_bg_img_position', 
			'type'     => 'select',
			'choices'  => array(
				'top-left'      => esc_html_x('Top Left', 'backend', 'rooten'),
				'top-center'    => esc_html_x('Top Center', 'backend', 'rooten'),
				'top-right'     => esc_html_x('Top Right', 'backend', 'rooten'),
				'center-left'   => esc_html_x('Center Left', 'backend', 'rooten'),
				''              => esc_html_x('Center Center', 'backend', 'rooten'),
				'center-right'  => esc_html_x('Center Right', 'backend', 'rooten'),
				'bottom-left'   => esc_html_x('Bottom Left', 'backend', 'rooten'),
				'bottom-center' => esc_html_x('Bottom Center', 'backend', 'rooten'),
				'bottom-right'  => esc_html_x('Bottom Right', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_bottom_bg_img_check',
		)));

		$this->wp_customize->add_setting('rooten_bottom_bg_img_fixed', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control('rooten_bottom_bg_img_fixed', array(
			'label'           => esc_html_x('Fix the background with regard to the viewport.', 'backend', 'rooten'),
			'section'         => 'footer',
			'settings'        => 'rooten_bottom_bg_img_fixed',
			'type'            => 'checkbox',
			'active_callback' => 'rooten_bottom_bg_img_check',
		));

		$this->wp_customize->add_setting('rooten_bottom_bg_img_visibility', array(
			'default' => 'm',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_bottom_bg_img_visibility', array(
			'label'       => esc_html_x('Background Visibility', 'backend', 'rooten'),
			'description' => esc_html_x('Display the image only on this device width and larger.', 'backend', 'rooten'),
			'section'     => 'footer',
			'settings'    => 'rooten_bottom_bg_img_visibility', 
			'type'        => 'select',
			'choices'     => array(
				's'  => esc_html_x('Small (Phone Landscape)', 'backend', 'rooten'),
				'm'  => esc_html_x('Medium (Tablet Landscape)', 'backend', 'rooten'),
				'l'  => esc_html_x('Large (Desktop)', 'backend', 'rooten'),
				'xl' => esc_html_x('X-Large (Large Screens)', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_bottom_bg_img_check',
		));


		$this->wp_customize->add_setting('rooten_bottom_txt_style', array(
			'default'           => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_bottom_txt_style', array(
			'label'    => esc_html_x('Text Color', 'backend', 'rooten'),
			'section'  => 'footer',
			'settings' => 'rooten_bottom_txt_style', 
			'type'     => 'select',
			'choices'  => array(
				0        => esc_html_x('Default', 'backend', 'rooten'),
				'light'  => esc_html_x('Light', 'backend', 'rooten'),
				'dark'   => esc_html_x('Dark', 'backend', 'rooten'),
				'custom' => esc_html_x('Custom', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_footer_no_check',
		));

		// Add footer text color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'footer_text_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'.tm-bottom.bdt-section-custom',
					'.tm-bottom a', 
					'.tm-bottom .bdt-link', 
					'.tm-bottom .bdt-text-primary', 
					'.tm-bottom .bdt-alert-primary',
				),
				'color|lighten(30)' => array(
					'.tm-bottom.bdt-section-custom .bdt-card-title',
					'.tm-bottom.bdt-section-custom h3',
					'.tm-bottom a:hover', 
					'.tm-bottom .bdt-link:hover', 
					'.tm-bottom .bdt-text-primary:hover', 
					'.tm-bottom .bdt-alert-primary:hover',
				),
				'color|darken(5)' => array(
					'.tm-bottom.bdt-section-custom .widget_tag_cloud a',
				),
				'border-color' => array(
					'.tm-bottom.bdt-section-custom .bdt-button-default',
				),
			)
		)));

		// Add footer text color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'footer_text_color', array(
			'label'       => esc_html_x( 'Custom Text Color', 'backend', 'rooten' ),
			'section'     => 'footer',
			'active_callback' => 'rooten_bottom_txt_custom_color_check'
		)));


		$this->wp_customize->add_setting('rooten_bottom_width', array(
			'default' => 'default',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_bottom_width', array(
			'label'    => esc_html_x('Width', 'backend', 'rooten'),
			'section'  => 'footer',
			'settings' => 'rooten_bottom_width', 
			'type'     => 'select',
			'choices'  => array(
				'default' => esc_html_x('Default', 'backend', 'rooten'),
				'small'   => esc_html_x('Small', 'backend', 'rooten'),
				'large'   => esc_html_x('Large', 'backend', 'rooten'),
				'expand'  => esc_html_x('Expand', 'backend', 'rooten'),
				0        => esc_html_x('Full', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_footer_no_check',
		));


		$this->wp_customize->add_setting('rooten_bottom_padding', array(
			'default' => 'medium',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_bottom_padding', array(
			'label'    => esc_html_x('Padding', 'backend', 'rooten'),
			'section'  => 'footer',
			'settings' => 'rooten_bottom_padding', 
			'type'     => 'select',
			'choices'  => array(
				0 		 => esc_html_x('Default', 'backend', 'rooten'),
				'small'  => esc_html_x('Small', 'backend', 'rooten'),
				'medium' => esc_html_x('Medium', 'backend', 'rooten'),
				'large'  => esc_html_x('Large', 'backend', 'rooten'),
				'none'   => esc_html_x('None', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_footer_no_check',
		));

		$this->wp_customize->add_setting('rooten_bottom_gutter', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_bottom_gutter', array(
			'label'    => esc_html_x('Gutter', 'backend', 'rooten'),
			'section'  => 'footer',
			'settings' => 'rooten_bottom_gutter', 
			'type'     => 'select',
			'choices'  => array(
				'small'    => esc_html_x('Small', 'backend', 'rooten'),
				'medium'   => esc_html_x('Medium', 'backend', 'rooten'),
				0          => esc_html_x('Default', 'backend', 'rooten'),
				'large'    => esc_html_x('Large', 'backend', 'rooten'),
				'collapse' => esc_html_x('Collapse', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_footer_no_check',
		));



		$this->wp_customize->add_setting('rooten_bottom_column_divider', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control('rooten_bottom_column_divider', array(
			'label'           => esc_html_x('Display dividers between grid cells', 'backend', 'rooten'),
			'description'     => esc_html_x('(Set the grid gutter width and display dividers between grid cells.)', 'backend', 'rooten'),
			'section'         => 'footer',
			'settings'        => 'rooten_bottom_column_divider',
			'active_callback' => 'rooten_bottom_gutter_collapse_check',
			'type'            => 'checkbox'
		));

		$this->wp_customize->add_setting('rooten_bottom_vertical_align', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control('rooten_bottom_vertical_align', array(
			'label'       => esc_html_x('Vertically center grid cells.', 'backend', 'rooten'),
			'section'     => 'footer',
			'settings'    => 'rooten_bottom_vertical_align',
			'type'        => 'checkbox',
			'active_callback' => 'rooten_custom_footer_no_check',
		));


		$this->wp_customize->add_setting('rooten_bottom_match_height', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control('rooten_bottom_match_height', array(
			'label'       => esc_html_x('Stretch the panel to match the height of the grid cell.', 'backend', 'rooten'),
			'section'     => 'footer',
			'settings'    => 'rooten_bottom_match_height',
			'type'        => 'checkbox',
			'active_callback' => 'rooten_custom_footer_no_check',
		));

		$this->wp_customize->add_setting('rooten_bottom_breakpoint', array(
			'default' => 'm',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_bottom_breakpoint', array(
			'label'       => esc_html_x('Breakpoint', 'backend', 'rooten'),
			'description' => esc_html_x('Set the breakpoint from which grid cells will stack.', 'backend', 'rooten'),
			'section'     => 'footer',
			'settings'    => 'rooten_bottom_breakpoint', 
			'type'        => 'select',
			'choices'     => array(
				's'  => esc_html_x('Small (Phone Landscape)', 'backend', 'rooten'),
				'm'  => esc_html_x('Medium (Tablet Landscape)', 'backend', 'rooten'),
				'l'  => esc_html_x('Large (Desktop)', 'backend', 'rooten'),
				'xl' => esc_html_x('X-Large (Large Screens)', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_footer_no_check',
		));

		// Copyright Section
		$this->wp_customize->add_section('copyright', array(
			'title' => esc_html_x('Copyright', 'backend', 'rooten'),
			'description' => esc_html_x( 'Copyright section settings here.', 'backend', 'rooten' ),
			'priority' => 142
		));


		$this->wp_customize->add_setting('rooten_copyright_bg_style', array(
			'default' => 'secondary',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_copyright_bg_style', array(
			'label'    => esc_html_x('Background Style', 'backend', 'rooten'),
			'section'  => 'copyright',
			'settings' => 'rooten_copyright_bg_style', 
			'type'     => 'select',
			'choices'  => array(
				'default'   => esc_html_x('Default', 'backend', 'rooten'),
				'muted'     => esc_html_x('Muted', 'backend', 'rooten'),
				'primary'   => esc_html_x('Primary', 'backend', 'rooten'),
				'secondary' => esc_html_x('Secondary', 'backend', 'rooten'),
				'media'     => esc_html_x('Image', 'backend', 'rooten'),
				'custom'    => esc_html_x('Custom Color', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_footer_no_check',
		));


		// Add footer background color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'rooten_copyright_background_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'background-color' => array(
					'.tm-copyright.bdt-section-custom',
				),
			)
		)));

		// Add footer background color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'rooten_copyright_background_color', array(
			'label'           => esc_html_x( 'Custom Background Color', 'backend', 'rooten' ),
			'section'         => 'copyright',
			'active_callback' => 'rooten_copyright_bg_custom_color_check',
		)));


		$this->wp_customize->add_setting( 'rooten_copyright_bg_img' , array(
			'sanitize_callback' => 'esc_url'
		));
		$this->wp_customize->add_control( new WP_Customize_Image_Control( $this->wp_customize, 'rooten_copyright_bg_img', array(
		    'label'    => esc_html_x( 'Background Image', 'backend', 'rooten' ),
		    'section'  => 'copyright',
		    'settings' => 'rooten_copyright_bg_img',
		    'active_callback' => 'rooten_copyright_bg_style_check',
		)));


		$this->wp_customize->add_setting('rooten_copyright_txt_style', array(
			'default'           => 'light',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_copyright_txt_style', array(
			'label'    => esc_html_x('Text Color', 'backend', 'rooten'),
			'section'  => 'copyright',
			'settings' => 'rooten_copyright_txt_style', 
			'type'     => 'select',
			'choices'  => array(
				0       => esc_html_x('Default', 'backend', 'rooten'),
				'light' => esc_html_x('Light', 'backend', 'rooten'),
				'dark'  => esc_html_x('Dark', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_footer_no_check',
		));

		$this->wp_customize->add_setting('rooten_copyright_width', array(
			'default' => 'default',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_copyright_width', array(
			'label'    => esc_html_x('Width', 'backend', 'rooten'),
			'section'  => 'copyright',
			'settings' => 'rooten_copyright_width', 
			'type'     => 'select',
			'choices'  => array(
				'default' => esc_html_x('Default', 'backend', 'rooten'),
				'small'   => esc_html_x('Small', 'backend', 'rooten'),
				'large'   => esc_html_x('Large', 'backend', 'rooten'),
				'expand'  => esc_html_x('Expand', 'backend', 'rooten'),
				0        => esc_html_x('Full', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_footer_no_check',
		));


		$this->wp_customize->add_setting('rooten_copyright_padding', array(
			'default' => 'small',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_copyright_padding', array(
			'label'    => esc_html_x('Padding', 'backend', 'rooten'),
			'section'  => 'copyright',
			'settings' => 'rooten_copyright_padding', 
			'type'     => 'select',
			'choices'  => array(
				'small'  => esc_html_x('Small', 'backend', 'rooten'),
				'medium' => esc_html_x('Medium', 'backend', 'rooten'),
				'large'  => esc_html_x('Large', 'backend', 'rooten'),
				'none'   => esc_html_x('None', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_custom_footer_no_check',
		));



		$this->wp_customize->add_setting('rooten_copyright_text_custom_show', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control(new WP_Customize_Control($this->wp_customize, 'rooten_copyright_text_custom_show',
	        array(
				'label'    => esc_html_x('Show Custom Copyright Text', 'backend', 'rooten'),
				'section'  => 'copyright',
				'settings' => 'rooten_copyright_text_custom_show',
				'type'     => 'checkbox',
				'active_callback' => 'rooten_custom_footer_no_check',
	        )
		));
		
		//copyright Content
		$this->wp_customize->add_setting('rooten_copyright_text_custom', array(
			'default'           => 'Theme Designed by <a href="'.esc_url( esc_html_x( 'https://www.bdthemes.com', 'backend', 'rooten')).' ">BdThemes Ltd</a>',
			'sanitize_callback' => 'rooten_sanitize_textarea'
		));
		$this->wp_customize->add_control( new rooten_Customize_Textarea_Control( $this->wp_customize, 'rooten_copyright_text_custom', array(
			'label'           => esc_html_x('Copyright Text', 'backend', 'rooten'),
			'section'         => 'copyright',
			'settings'        => 'rooten_copyright_text_custom',
			'active_callback' => 'rooten_copyright_text_custom_show_check',
			'type'            => 'textarea',
		)));


		// Copyright Section
		$this->wp_customize->add_section('totop', array(
			'title' => esc_html_x('Go To Top', 'backend', 'rooten'),
			'description' => esc_html_x( 'Go to top show/hide, layout and style here.', 'backend', 'rooten' ),
			'priority' => 143
		));

		/*
		 * "go to top" link
		 */
		$this->wp_customize->add_setting('rooten_totop_show', array(
			'default' => 1,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control(new WP_Customize_Control($this->wp_customize, 'rooten_totop_show',
	        array(
				'label'    => esc_html_x('Show "Go to top" link', 'backend', 'rooten'),
				'section'  => 'totop',
				'settings' => 'rooten_totop_show',
				'type'     => 'checkbox'
	        )
		));

		$this->wp_customize->add_setting('rooten_totop_bg_style', array(
			'default' => 'secondary',
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_totop_bg_style', array(
			'label'    => esc_html_x('Background Style', 'backend', 'rooten'),
			'section'  => 'totop',
			'settings' => 'rooten_totop_bg_style', 
			'type'     => 'select',
			'choices'  => array(
				'default'   => esc_html_x('Default (White)', 'backend', 'rooten'),
				'muted'     => esc_html_x('Muted', 'backend', 'rooten'),
				'primary'   => esc_html_x('Primary', 'backend', 'rooten'),
				'secondary' => esc_html_x('Secondary', 'backend', 'rooten'),
				//'media'     => esc_html_x('Image', 'backend', 'rooten'),
				//'custom'    => esc_html_x('Custom Color', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_totop_check',
		));

		$this->wp_customize->add_setting('rooten_totop_align', array(
			'default' => 'left',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_totop_align', array(
			'label'    => esc_html_x('Alignment', 'backend', 'rooten'),
			'description' => esc_html_x('Set go to top alignment from here.', 'backend', 'rooten'),
			'section'  => 'totop',
			'settings' => 'rooten_totop_align', 
			'type'     => 'select',
			'choices'  => array(
				'left'      => esc_html_x('Bottom Left', 'backend', 'rooten'),
				'center'    => esc_html_x('Bottom Center', 'backend', 'rooten'),
				'right'     => esc_html_x('Bottom Right', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_totop_check',
		)));

		$this->wp_customize->add_setting('rooten_totop_radius', array(
			'default' => 'circle',
			'sanitize_callback' => 'rooten_sanitize_choices'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_totop_radius', array(
			'label'    => esc_html_x('Alignment', 'backend', 'rooten'),
			'description' => esc_html_x('Set go to top alignment from here.', 'backend', 'rooten'),
			'section'  => 'totop',
			'settings' => 'rooten_totop_radius', 
			'type'     => 'select',
			'choices'  => array(
				0        => esc_html_x('Squire', 'backend', 'rooten'),
				'rounded' => esc_html_x('Rounded', 'backend', 'rooten'),
				'circle' => esc_html_x('Circle', 'backend', 'rooten'),
			),
			'active_callback' => 'rooten_totop_check',
		)));




		//Cookie bar section and settings
		$this->wp_customize->add_section('cookie', array(
			'title' => esc_html_x('Cookie Bar', 'backend', 'rooten'),
			'description' => esc_html_x( 'Show cookie accept notification on your website.', 'backend', 'rooten' ),
			'priority' => 150
		));


		$this->wp_customize->add_setting('rooten_cookie', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_cookie', array(
			'label'    => esc_html_x('Show Cookie Notification', 'backend', 'rooten'),
			'section'  => 'cookie',
			'settings' => 'rooten_cookie', 
			'type'     => 'select',
			'choices'  => array(
				1  => esc_html_x('Yes please!', 'backend', 'rooten'),
				0 => esc_html_x('No Need', 'backend', 'rooten'),
			)
		));


		// Add cookie background color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'rooten_cookie_background', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'background-color' => array(
					'#cookie-bar',
				),
			)
		)));

		// Add cookie background color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'rooten_cookie_background', array(
			'label'           => esc_html_x( 'Background Color', 'backend', 'rooten' ),
			'section'         => 'cookie',
			//'active_callback' => 'rooten_bottom_bg_custom_color_check',
		)));

		// Add cookie text color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'rooten_cookie_text_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'#cookie-bar',
				),
			)
		)));

		// Add cookie text color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'rooten_cookie_text_color', array(
			'label'           => esc_html_x( 'Text Color', 'backend', 'rooten' ),
			'section'         => 'cookie',
			//'active_callback' => 'rooten_bottom_bg_custom_color_check',
		)));

		$this->wp_customize->add_setting('rooten_cookie_message', array(
			'default' => esc_html__( 'We use cookies to ensure that we give you the best experience on our website.', 'rooten' ),
			'sanitize_callback' => 'rooten_sanitize_text'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_cookie_message', array(
			'label'       => esc_html_x('Message', 'backend', 'rooten'),
			'description' => esc_html_x('Set cookie message from here.', 'backend', 'rooten'),
			'section'     => 'cookie',
			'settings'    => 'rooten_cookie_message', 
			'type'        => 'text',
		)));

		$this->wp_customize->add_setting('rooten_cookie_expire_days', array(
			'default' => 365,
			'sanitize_callback' => 'rooten_sanitize_text'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_cookie_expire_days', array(
			'label'       => esc_html_x('Expire Days', 'backend', 'rooten'),
			'description' => esc_html_x('Set how many days to keep the cookies', 'backend', 'rooten'),
			'section'     => 'cookie',
			'settings'    => 'rooten_cookie_expire_days', 
			'type'        => 'text',
		)));


		$this->wp_customize->add_setting('rooten_cookie_accept_button', array(
			'default' => 1,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_cookie_accept_button', array(
			'label'       => esc_html_x('Accept Button', 'backend', 'rooten'),
			'description' => esc_html_x('(Show accept button in cookie message area)', 'backend', 'rooten'),
			'section'     => 'cookie',
			'settings'    => 'rooten_cookie_accept_button', 
			'type'        => 'checkbox',
		)));

		$this->wp_customize->add_setting('rooten_cookie_decline_button', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_cookie_decline_button', array(
			'label'       => esc_html_x('Decline Button', 'backend', 'rooten'),
			'description' => esc_html_x('(Show decline button in cookie message area)', 'backend', 'rooten'),
			'section'     => 'cookie',
			'settings'    => 'rooten_cookie_decline_button', 
			'type'        => 'checkbox',
		)));


		$this->wp_customize->add_setting('rooten_cookie_policy_button', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_cookie_policy_button', array(
			'label'       => esc_html_x('Policy Button', 'backend', 'rooten'),
			'description' => esc_html_x('(Show policy button in cookie message area)', 'backend', 'rooten'),
			'section'     => 'cookie',
			'settings'    => 'rooten_cookie_policy_button', 
			'type'        => 'checkbox',
		)));

		$this->wp_customize->add_setting('rooten_cookie_policy_url', array(
			'default' => '/privacy-policy/',
			'sanitize_callback' => 'esc_url'
		));

		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_cookie_policy_url', array(
			'label'           => esc_html_x('Policy Page URL', 'backend', 'rooten'),
			'description'     => esc_html_x('Set how many days to keep the cookies', 'backend', 'rooten'),
			'section'         => 'cookie',
			'settings'        => 'rooten_cookie_policy_url', 
			'type'            => 'text',
			'active_callback' => 'rooten_cookie_policy_button_check',
		)));

		$this->wp_customize->add_setting('rooten_cookie_position', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_cookie_position', array(
			'label'       => esc_html_x('Show Message at Button', 'backend', 'rooten'),
			'section'     => 'cookie',
			'settings'    => 'rooten_cookie_position', 
			'type'        => 'checkbox',
		)));


		$this->wp_customize->add_setting('rooten_cookie_debug', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_checkbox'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_cookie_debug', array(
			'label'       => esc_html_x('Debug Mode', 'backend', 'rooten'),
			'description' => esc_html_x('(If you checked so cookie will not hide even you click accept button.)', 'backend', 'rooten'),
			'section'     => 'cookie',
			'settings'    => 'rooten_cookie_debug', 
			'type'        => 'checkbox',
		)));



		//Preloader section and settings
		$this->wp_customize->add_section('preloader', array(
			'title' => esc_html_x('Preloader', 'backend', 'rooten'),
			'description' => esc_html_x( 'Show preloader for pre-loading your website.', 'backend', 'rooten' ),
			'priority' => 150
		));


		$this->wp_customize->add_setting('rooten_preloader', array(
			'default' => 0,
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_preloader', array(
			'label'    => esc_html_x('Show Preloader', 'backend', 'rooten'),
			'section'  => 'preloader',
			'settings' => 'rooten_preloader', 
			'type'     => 'select',
			'choices'  => array(
				1  => esc_html_x('Yes please!', 'backend', 'rooten'),
				0 => esc_html_x('No Need', 'backend', 'rooten'),
			)
		));
		

		$this->wp_customize->add_setting('rooten_preloader_logo', array(
			'default' => 1,
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_preloader_logo', array(
			'label'    => esc_html_x('Show Logo', 'backend', 'rooten'),
			'section'  => 'preloader',
			'settings' => 'rooten_preloader_logo', 
			'type'     => 'select',
			'choices'  => array(
				1        => esc_html_x('Yes please!', 'backend', 'rooten'),
				0        => esc_html_x('No Need', 'backend', 'rooten'),
				'custom' => esc_html_x('Custom Logo', 'backend', 'rooten'),
			)
		));

		$this->wp_customize->add_setting( 'rooten_preloader_custom_logo' , array(
			'sanitize_callback' => 'esc_url'
		));
		$this->wp_customize->add_control( new WP_Customize_Image_Control( $this->wp_customize, 'rooten_preloader_custom_logo', array(
			'label'           => esc_html_x( 'Logo', 'backend', 'rooten' ),
			'section'         => 'preloader',
			'settings'        => 'rooten_preloader_custom_logo',
			'active_callback' => 'rooten_preloader_logo_check',
		)));


		$this->wp_customize->add_setting('rooten_preloader_text', array(
			'default' => 1,
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_preloader_text', array(
			'label'    => esc_html_x('Show Text', 'backend', 'rooten'),
			'section'  => 'preloader',
			'settings' => 'rooten_preloader_text', 
			'type'     => 'select',
			'choices'  => array(
				1        => esc_html_x('Yes please!', 'backend', 'rooten'),
				0        => esc_html_x('No Need', 'backend', 'rooten'),
				'custom' => esc_html_x('Custom Text', 'backend', 'rooten'),
			)
		));



		$this->wp_customize->add_setting('rooten_preloader_custom_text', array(
			'sanitize_callback' => 'rooten_sanitize_text'
		));
		$this->wp_customize->add_control(new WP_Customize_Control( $this->wp_customize, 'rooten_preloader_custom_text', array(
			'label'           => esc_html_x('Text', 'backend', 'rooten'),
			'section'         => 'preloader',
			'settings'        => 'rooten_preloader_custom_text', 
			'type'            => 'text',
			'active_callback' => 'rooten_preloader_text_check',
		)));

		// Add preloader background color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'rooten_preloader_background', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'background-color' => array(
					'.pg-loading-screen',
				),
			)
		)));


		// Add preloader background color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'rooten_preloader_background', array(
			'label'           => esc_html_x( 'Background Color', 'backend', 'rooten' ),
			'section'         => 'preloader',
			//'active_callback' => 'rooten_bottom_bg_custom_color_check',
		)));

		// Add preloader text color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'rooten_preloader_text_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'color' => array(
					'.pg-loading-screen',
				),
			)
		)));

		// Add preloader text color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'rooten_preloader_text_color', array(
			'label'           => esc_html_x( 'Text Color', 'backend', 'rooten' ),
			'section'         => 'preloader',
			//'active_callback' => 'rooten_bottom_bg_custom_color_check',
		)));

		$this->wp_customize->add_setting('rooten_preloader_animation', array(
			'default' => 1,
			'sanitize_callback' => 'rooten_sanitize_choices'
		) );
		$this->wp_customize->add_control('rooten_preloader_animation', array(
			'label'    => esc_html_x('Show Animation', 'backend', 'rooten'),
			'section'  => 'preloader',
			'settings' => 'rooten_preloader_animation', 
			'type'     => 'select',
			'choices'  => array(
				1        => esc_html_x('Yes please!', 'backend', 'rooten'),
				0        => esc_html_x('No Need', 'backend', 'rooten'),
			)
		));

		// Add preloader text color setting.
		$this->wp_customize->add_setting( new Rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'rooten_preloader_animation_color', array(
			'sanitize_callback' => 'sanitize_hex_color',
			'css_map' => array(
				'background-color' => array(
					'.bdt-spinner > div',
				),
			)
		)));

		// Add preloader text color control.
		$this->wp_customize->add_control( new WP_Customize_Color_Control( $this->wp_customize, 'rooten_preloader_animation_color', array(
			'label'           => esc_html_x( 'Animation Color', 'backend', 'rooten' ),
			'section'         => 'preloader',
			'active_callback' => 'rooten_preloader_animation_check',
		)));



		//typography section
		$this->wp_customize->add_panel('typography', array(
			'title' => esc_html_x('Typography', 'backend', 'rooten'),
			'priority' => 41
		));
		$this->wp_customize->add_section('typography_heading', array(
			'title' => esc_html_x('Heading', 'backend', 'rooten'),
			'panel' => 'typography',
		));
		$this->wp_customize->add_section('typography_menu', array(
			'title' => esc_html_x('Menu', 'backend', 'rooten'),
			'panel' => 'typography',
		));
		$this->wp_customize->add_section('typography_body', array(
			'title' => esc_html_x('Body', 'backend', 'rooten'),
			'panel' => 'typography',
		));
		$this->wp_customize->add_section('typography_global', array(
			'title' => esc_html_x('Global Settings', 'backend', 'rooten'),
			'panel' => 'typography',
		));

		//Add setting Heading font family settings
		$this->wp_customize->add_setting( new rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'base_heading_font_family', array(
			'default'           => 'Roboto',
			'transport'         => 'postMessage',
			'sanitize_callback' => false,
		        'css_map' => array(
					'font-family' => array(
	                   'h1, h2, h3, h4, h5, h6',
					)
				)
		)));

		// Add Heading Font Control
		$this->wp_customize->add_control( 'base_heading_font_family', array(
			'label'    => esc_html_x( 'Heading Font Family', 'backend', 'rooten' ),
			'section'  => 'typography_heading',
			'settings' => 'base_heading_font_family',
			'type'     => 'text',
		));
                
        $this->wp_customize->add_setting( new rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'base_heading_font_weight', array(
			'default'           => '600',
			'transport'         => 'postMessage',
			'sanitize_callback' => false,
		        'css_map' => array(
					'font-weight' => array(
	                    'h1, h2, h3, h4, h5, h6',
					)
				)
		)));

		$this->wp_customize->add_control( 'base_heading_font_weight', array(
			'label'    => esc_html_x( 'Heading Font Weight', 'backend', 'rooten' ),
			'section'  => 'typography_heading',
			'settings' => 'base_heading_font_weight',
			'type'     => 'text',
			'description' => esc_html_x( 'Important: Not all fonts support every font-weight.', 'backend', 'rooten' ),
		));


		//Add setting Heading font family settings
		$this->wp_customize->add_setting( new rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'base_body_font_family', array(
			'default'           => 'Open Sans',
			'transport'         => 'postMessage',
			'sanitize_callback' => false,
		        'css_map' => array(
					'font-family' => array(
	                    'body'
					)
				)
		)));

		// Add Heading Font Control
		$this->wp_customize->add_control( 'base_body_font_family', array(
			'label'    => esc_html_x( 'Body Font Family', 'backend', 'rooten' ),
			'section'  => 'typography_body',
			'settings' => 'base_body_font_family',
			'type'     => 'text',
		));
                
        $this->wp_customize->add_setting( new rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'base_body_font_weight', array(
			'default'           => '400',
			'transport'         => 'postMessage',
			'sanitize_callback' => false,
		        'css_map' => array(
					'font-weight' => array(
	                    'body'
					)
				)
		)));

		$this->wp_customize->add_control( 'base_body_font_weight', array(
			'label'    => esc_html_x( 'Body Font Weight', 'backend', 'rooten' ),
			'section'  => 'typography_body',
			'settings' => 'base_body_font_weight',
			'type'     => 'text',
			'description' => esc_html_x( 'Important: Not all fonts support every font-weight.', 'backend', 'rooten' ),
		));

		
		//Add setting Heading font family settings
		$this->wp_customize->add_setting( new rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'base_menu_font_family', array(
			'default'           => 'Roboto',
			'transport'         => 'postMessage',
			'sanitize_callback' => false,
		        'css_map' => array(
					'font-family' => array(
	                    '.bdt-navbar-nav > li > a'
					)
				)
		)));

		// Add Menu Font Control
		$this->wp_customize->add_control( 'base_menu_font_family', array(
			'label'    => esc_html_x( 'Menu Font Family', 'backend', 'rooten' ),
			'section'  => 'typography_menu',
			'settings' => 'base_menu_font_family',
			'type'     => 'text',
		));
                
        $this->wp_customize->add_setting( new rooten_Customizer_Dynamic_CSS( $this->wp_customize, 'base_menu_font_weight', array(
			'default'           => '700',
			'transport'         => 'postMessage',
			'sanitize_callback' => false,
		        'css_map' => array(
					'font-weight' => array(
	                    '.bdt-navbar-nav > li > a'
					)
				)
		)));

		$this->wp_customize->add_control( 'base_menu_font_weight', array(
			'label'       => esc_html_x( 'Menu Font Weight', 'backend', 'rooten' ),
			'section'     => 'typography_menu',
			'settings'    => 'base_menu_font_weight',
			'type'        => 'text',
			'description' => esc_html_x( 'Important: Not all fonts support every font-weight.', 'backend', 'rooten' ),
		));


		// Font subset
		// $this->wp_customize->add_setting( 'google_font_subsets', array(
		// 	'default' => 'latin-ext',
		// 	'sanitize_callback' => false,
		// ) );

		// $this->wp_customize->add_control( new rooten_Customize_Multicheck_Control( $this->wp_customize, 'google_font_subsets', array(
		// 	'label'    => esc_html_x( 'Font Subsets', 'backend', 'rooten' ),
		// 	'section'  => 'typography_global',
		// 	'settings' => 'google_font_subsets',
		// 	'choices'  => array(
		// 		'latin'        => 'latin',
		// 		'latin-ext'    => 'latin-ext',
		// 		'cyrillic'     => 'cyrillic',
		// 		'cyrillic-ext' => 'cyrillic-ext',
		// 		'greek'        => 'greek',
		// 		'greek-ext'    => 'greek-ext',
		// 		'vietnamese'   => 'vietnamese',
		// 	),
		// )));


		if ( isset( $this->wp_customize->selective_refresh ) ) {
			$this->wp_customize->selective_refresh->add_partial( 'blogname', array(
				'selector' => '.tm-header a.tm-logo-text',
				'container_inclusive' => false,
				'render_callback' => 'rooten_customize_partial_blogname',
			));
			$this->wp_customize->selective_refresh->add_partial( 'rooten_logo_default', array(
				'selector' => '.tm-header a.tm-logo-img',
				'container_inclusive' => false,
			));
			$this->wp_customize->selective_refresh->add_partial( 'blogdescription', array(
				'selector' => '.site-description',
				'container_inclusive' => false,
				'render_callback' => 'rooten_customize_partial_blogdescription',
			));

			$this->wp_customize->selective_refresh->add_partial( 'rooten_show_copyright_text', array(
				'selector' => '.copyright-txt',
				'container_inclusive' => false,
			));

			$this->wp_customize->selective_refresh->add_partial( 'rooten_copyright_text_custom_show', array(
				'selector' => '.copyright-txt',
				'container_inclusive' => false,
			));

			$this->wp_customize->selective_refresh->add_partial( 'rooten_search_position', array(
				'selector' => '.tm-header .bdt-search',
				'container_inclusive' => false,
			));

			$this->wp_customize->selective_refresh->add_partial( 'rooten_search_position', array(
				'selector' => '.tm-header a.bdt-search-icon',
				'container_inclusive' => false,
			));

			$this->wp_customize->selective_refresh->add_partial( 'rooten_toolbar_social', array(
				'selector' => '.tm-toolbar .social-link',
				'container_inclusive' => false,
			));

			$this->wp_customize->selective_refresh->add_partial( 'rooten_toolbar_left_custom', array(
				'selector' => '.tm-toolbar-l .custom-text',
				'container_inclusive' => false,
			));

			$this->wp_customize->selective_refresh->add_partial( 'rooten_toolbar_right_custom', array(
				'selector' => '.tm-toolbar-r .custom-text',
				'container_inclusive' => false,
			));

			$this->wp_customize->selective_refresh->add_partial( 'rooten_woocommerce_cart', array(
				'selector' => '.tm-cart-popup',
				'container_inclusive' => false,
			));

			$this->wp_customize->selective_refresh->add_partial( 'nav_menu_locations[primary]', array(
				'selector' => '.tm-header .bdt-navbar-nav',
				'container_inclusive' => false,
			));

			$this->wp_customize->selective_refresh->add_partial( 'nav_menu_locations[toolbar]', array(
				'selector' => '.tm-toolbar .tm-toolbar-menu',
				'container_inclusive' => false,
			));

			$this->wp_customize->selective_refresh->add_partial( 'nav_menu_locations[footer]', array(
				'selector' => '.tm-copyright .tm-copyright-menu',
				'container_inclusive' => false,
			));

			$this->wp_customize->selective_refresh->add_partial( 'rooten_top_link', array(
				'selector' => '.tm-totop-scroller',
				'container_inclusive' => false,
			));


			$this->wp_customize->selective_refresh->add_partial( 'rooten_mobile_offcanvas_style', array(
				'selector' => '.tm-header-mobile .bdt-navbar-toggle',
				'container_inclusive' => false,
			));

			$this->wp_customize->selective_refresh->add_partial( 'rooten_titlebar_layout', array(
				'selector' => '.tm-titlebar h1',
				'container_inclusive' => false,
			));
		}
	}


	public function rooten_toolbar_left_elements() {
		$toolbar_elements = array();
		$description      = get_bloginfo( 'description', 'display' );
		if (function_exists('icl_object_id')) {
			$toolbar_elements['wpml'] = esc_html_x( 'Language Switcher', 'backend', 'rooten' );
		}
		if ($description) {
			$toolbar_elements['tagline'] = esc_html_x('Tagline', 'backend', 'rooten');
		}
		if (has_nav_menu('toolbar')) {
			$toolbar_elements['menu'] = esc_html_x('Toolbar Menu', 'backend', 'rooten');
		}
		$toolbar_elements['social'] = esc_html_x('Social Link', 'backend', 'rooten');
		$toolbar_elements['custom-left'] = esc_html_x('Custom Text', 'backend', 'rooten');
		return $toolbar_elements;
	}

	public function rooten_toolbar_right_elements() {
		$toolbar_elements = array();
		$description      = get_bloginfo( 'description', 'display' );
		if (function_exists('icl_object_id')) {
			$toolbar_elements['wpml'] = esc_html_x( 'Language Switcher', 'backend', 'rooten' );
		}
		if ($description) {
			$toolbar_elements['tagline'] = esc_html_x('Tagline', 'backend', 'rooten');
		}
		if (has_nav_menu('toolbar')) {
			$toolbar_elements['menu'] = esc_html_x('Toolbar Menu', 'backend', 'rooten');
		}
		$toolbar_elements['social'] = esc_html_x('Social Link', 'backend', 'rooten');
		$toolbar_elements['custom-right'] = esc_html_x('Custom Text', 'backend', 'rooten');
		return $toolbar_elements;
	}

	public function rooten_font_weight() {
		$font_weight = array(
				''    => esc_html_x( 'Default', 'backend', 'rooten' ),
				'100' => esc_html_x( 'Extra Light: 100', 'backend', 'rooten' ),
				'200' => esc_html_x( 'Light: 200', 'backend', 'rooten' ),
				'300' => esc_html_x( 'Book: 300', 'backend', 'rooten' ),
				'400' => esc_html_x( 'Normal: 400', 'backend', 'rooten' ),
				'600' => esc_html_x( 'Semibold: 600', 'backend', 'rooten' ),
				'700' => esc_html_x( 'Bold: 700', 'backend', 'rooten' ),
				'800' => esc_html_x( 'Extra Bold: 800', 'backend', 'rooten' ),
			);
		return $font_weight;
	}

	/**
	 * Render the site title for the selective refresh partial.
	 *
	 * @since Rooten 1.0
	 * @see rooten_customize_register_colors()
	 *
	 * @return void
	 */
	public function rooten_customize_partial_blogname() {
		bloginfo( 'name' );
	}

	/**
	 * Render the site tagline for the selective refresh partial.
	 *
	 * @since Rooten 1.0
	 * @see rooten_customize_register_colors()
	 *
	 * @return void
	 */
	public function rooten_customize_partial_blogdescription() {
		bloginfo( 'description' );
	}

	/**
	 * Cache the rendered CSS after the settings are saved in the DB.
	 * This is purely a performance improvement.
	 *
	 * Used by hook: add_action( 'customize_save_after' , array( $this, 'cache_rendered_css' ) );
	 *
	 * @return void
	 */
	public function cache_rendered_css() {
 		set_theme_mod( 'cached_css', $this->render_css() );
	}

	/**
	 * Get the dimensions of the logo image when the setting is saved
	 * This is purely a performance improvement.
	 *
	 * Used by hook: add_action( 'customize_save_logo_img' , array( $this, 'save_logo_dimensions' ), 10, 1 );
	 *
	 * @return void
	 */
	public static function save_logo_dimensions( $setting ) {
		$logo_width_height = '';
		$img_data          = getimagesize( esc_url( $setting->post_value() ) );

		if ( is_array( $img_data ) ) {
			$logo_width_height = $img_data[3];
		}

		set_theme_mod( 'logo_width_height', $logo_width_height );
	}

	/**
	 * Render the CSS from all the settings which are of type `Rooten_Customizer_Dynamic_CSS`
	 *
	 * @return string text/css
	 */
	public function render_css() {
		$out = '';
		foreach ( $this->get_dynamic_css_settings() as $setting ) {
			$out .= $setting->render_css();
		}

		return $out;
	}

        /**
	 * Get only the CSS settings of type `Rooten_Customizer_Dynamic_CSS`.
	 *
	 * @see is_dynamic_css_setting
	 * @return array
	 */
	public function get_dynamic_css_settings() {
		return array_filter( $this->wp_customize->settings(), array( $this, 'is_dynamic_css_setting' ) );
	}

	/**
	 * Helper conditional function for filtering the settings.
	 *
	 * @see
	 * @param  mixed  $setting
	 * @return boolean
	 */
	protected static function is_dynamic_css_setting( $setting ) {
		return is_a( $setting, 'Rooten_Customizer_Dynamic_CSS' );
	}

	/**
	 * Dynamically generate the JS for previewing the settings of type `Rooten_Customizer_Dynamic_CSS`.
	 *
	 * This function is better for the UX, since all the color changes are transported to the live
	 * preview frame using the 'postMessage' method. Since the JS is generated on-the-fly and we have a single
	 * entry point of entering settings along with related css properties and classes, we cannnot forget to
	 * include the setting in the customizer itself. Neat, man!
	 *
	 * @return string text/javascript
	 */
	public function customize_footer_js() {
		$settings = $this->get_dynamic_css_settings();

		ob_start();
		?>

			<script type="text/javascript">
				'use strict';
				//rooten customizer color live preview
				( function( $ ) {
				    var style = []
			
				<?php
					foreach ( $settings as $key_id => $setting ) :
				?>
					style['<?php echo esc_attr($key_id) ?>'] = '';
					wp.customize( '<?php echo esc_attr($key_id); ?>', function( value ) {
					   
						value.bind( function( newval ) {
						     style['<?php echo esc_attr($key_id) ?>'] = '';
						<?php
							foreach ( $setting->get_css_map() as $css_prop_raw => $css_selectors ) {
								
								extract( $setting->filter_css_property( $css_prop_raw ) );
                                if($lighten){
                                    echo 'newval = LightenDarkenColor(newval,'.esc_attr($lighten).' ); ';
                                }
								// background image needs a little bit different treatment
								if ( 'background-image' === $css_prop ) {
									echo 'newval = "url(\'" + newval + "\')";' . PHP_EOL;
								}
								printf( 'style["%1$s"]  += "%2$s{ %3$s: "+ newval + " }" %4$s ' .  '+"\r\n"; '."\r\n",$key_id, $setting->plain_selectors_for_all_groups( $css_prop_raw ), $css_prop, PHP_EOL);
							}
						?>
						   add_style(style); 	    
						});
						
					} );
					<?php
					foreach ($setting->get_css_map() as $css_prop_raw => $css_selectors) {
	                                      
					    extract($setting->filter_css_property($css_prop_raw));
						if($lighten){
							$value = $value;
						} else {
							$value = $setting->render_css_save();
						}
					   
					    if ( 'background-image' === $css_prop ) {
							$value = 'url(\''.$value.'\');';
					    }
					    printf('style["%1$s"]  += "%2$s{ %3$s: %5$s }" %4$s ' . '+"\r\n"; ' . "\r\n", $key_id, $setting->plain_selectors_for_all_groups($css_prop_raw),
						    $css_prop, PHP_EOL, $value);
					}
					?>
					add_style(style);
				<?php
					endforeach;
					?>
				    function add_style(style){
					    var str_style = '';
					    var key;
					    for(key in style){
							if(style[key]){
							    str_style += '/*' + key + "*/\r\n";
							    str_style += style[key] + "\r\n";
							}
					    }
					    $('#custome_live_preview').html(str_style)

				    }
	                                
                    function LightenDarkenColor(col, amt) {  
                        var usePound = false;
                        if (col[0] == "#") {
                            col = col.slice(1);
                            usePound = true;
                        }
                        var num = parseInt(col,16);
                        var r = (num >> 16) + amt;
                        if (r > 255) r = 255;
                        else if  (r < 0) r = 0;
                        var b = ((num >> 8) & 0x00FF) + amt;
                        if (b > 255) b = 255;
                        else if  (b < 0) b = 0;
                        var g = (num & 0x0000FF) + amt;
                        if (g > 255) g = 255;
                        else if (g < 0) g = 0;
                        return (usePound?"#":"") + (g | (b << 8) | (r << 16)).toString(16);
                    }
				} )( jQuery );
			</script>
		<?php
		echo ob_get_clean();
	}
	
    public function hook_custom_css() { ?>
		<style id='custome_live_preview'></style>
    	<?php
    }

}