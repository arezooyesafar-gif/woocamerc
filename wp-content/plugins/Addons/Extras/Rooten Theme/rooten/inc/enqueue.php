<?php

/**
 * Admin related stylesheets
 * @return [type] [description]
 */
function rooten_admin_style() {
	wp_register_style( 'admin-setting', get_template_directory_uri() . '/admin/css/admin-settings.css' );
	wp_enqueue_style( 'admin-setting' );
}
add_action( 'admin_enqueue_scripts', 'rooten_admin_style' );


/**
 * Admin related scripts
 * @return [type] [description]
 */
function rooten_admin_script() {
	wp_register_script('admin-setting', get_template_directory_uri() . '/admin/js/admin-settings.js', array( 'jquery' ), ROOTEN_VER, true);

	wp_enqueue_script('admin-setting');
}
add_action( 'admin_enqueue_scripts', 'rooten_admin_script' );


/**
 * Site Stylesheets
 * @return [type] [description]
 */
function rooten_styles() {

	$rtl_enabled = is_rtl();

	// Load Primary Stylesheet
	
	if ($rtl_enabled) {
		wp_enqueue_style( 'bdt-uikit', ROOTEN_URL .'/css/bdt-uikit.rtl.css', array(), ROOTEN_VER, 'all' );
	} else {
		wp_enqueue_style( 'bdt-uikit', ROOTEN_URL .'/css/bdt-uikit.css', array(), ROOTEN_VER, 'all' );
	}
	
	if (class_exists('Woocommerce')) {
		wp_enqueue_style( 'theme-woocommerce-style', ROOTEN_URL .'/css/woocommerce.css', array(), ROOTEN_VER, 'all' );
	}

	if (get_theme_mod( 'rooten_header_txt_style', false )) {
		wp_enqueue_style( 'theme-inverse-style', ROOTEN_URL .'/css/inverse.css', array(), ROOTEN_VER, 'all' );
	}

	//TODO google dynamic font
	//wp_enqueue_style( 'theme-fonts-google', rooten_fonts_url(), array(), null );
	// google font load locally for faster load
	wp_enqueue_style( 'theme-style', ROOTEN_URL .'/css/default-fonts.css', array(), ROOTEN_VER, 'all' );
	
	wp_enqueue_style( 'rooten-style', get_stylesheet_uri(), array(), ROOTEN_VER, 'all' );
	wp_style_add_data( 'rooten-style', 'rtl', 'replace' );

}  
add_action( 'wp_enqueue_scripts', 'rooten_styles' );


function rooten_fonts_url() {
	$fonts_url     = '';
	$font_families = [];

	// TODO
	// $heading_font  = get_theme_mod( 'base_heading_font_family', 'Roboto' );
	// $menu_font     = get_theme_mod( 'base_menu_font_family', 'Roboto' );
	// $body_font     = get_theme_mod( 'base_body_font_family', 'Open Sans' );
	
	// $heading_fw    = get_theme_mod( 'base_heading_font_weight', 700 );
	// $menu_fw       = get_theme_mod( 'base_menu_font_weight', 700 );
	// $body_fw       = get_theme_mod( 'base_body_font_weight', 400 );

	// if (isset($heading_font) or isset($menu_font) or isset($body_font)) {
	// 	if (isset($heading_font) and isset($heading_fw)) {
	// 		$font_families[] = $heading_font . ':' . $heading_fw;
	// 	}
	// 	if (isset($menu_font) and isset($menu_fw)) {
	// 		$font_families[] = $menu_font . ':' . $menu_fw;
	// 	}
	// 	if (isset($body_font) and isset($body_fw)) {
	// 		$font_families[] = $body_font . ':' . $body_fw;
	// 	}
	
	// } else {
		// $font_families[] = 'Roboto:400,400i,700';
		// $font_families[] = 'Open Sans:300,300i,400,400i,700';
	//}
	
	// $query_args = array(
	// 	'family' => urlencode( implode( '|', $font_families ) ),
	// 	'subset' => urlencode( 'latin-ext' ),
	// );

	// $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );

	// return esc_url_raw( $fonts_url );



}


/**
 * Site Scripts
 * @return [type] [description]
 */
function rooten_scripts() {

	$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$preloader = get_theme_mod('rooten_preloader');
	$cookie    = get_theme_mod('rooten_cookie');

	if ($preloader) {
		wp_enqueue_script('please-wait', ROOTEN_URL . '/js/please-wait.min.js', array(), ROOTEN_VER, false);
	}
	
	wp_enqueue_script( 'bdt-uikit', ROOTEN_URL . '/js/bdt-uikit' . $suffix . '.js', ['jquery'], '3.2', true );
	wp_enqueue_script( 'bdt-uikit-icons', ROOTEN_URL . '/js/bdt-uikit-icons' . $suffix . '.js', ['jquery', 'bdt-uikit'], '3.2', true );
	
	if ($cookie) {
		wp_register_script('cookie-bar', ROOTEN_URL . '/js/jquery.cookiebar.js', array( 'jquery' ), ROOTEN_VER, true);
		wp_enqueue_script('cookie-bar');
	}
	
	wp_register_script('ease-scroll', ROOTEN_URL . '/js/jquery.easeScroll.js', array( 'jquery' ), ROOTEN_VER, true);
	wp_register_script('rooten-script', ROOTEN_URL . '/js/theme.js', array( 'jquery' ), ROOTEN_VER, true);

	wp_enqueue_script('ease-scroll');
	wp_enqueue_script('rooten-script');

  	// Load WP Comment Reply JS
  	if(is_singular()) { wp_enqueue_script( 'comment-reply' ); }
}

add_action( 'wp_enqueue_scripts', 'rooten_scripts' );  