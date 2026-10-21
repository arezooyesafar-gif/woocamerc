<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package rooten
 */
	
	$rooten_loading_html   = [];
	$rooten_logo_default   = get_theme_mod('rooten_logo_default');
	$rooten_custom_logo    = get_theme_mod('rooten_preloader_custom_logo');
	$rooten_logo           = get_theme_mod('rooten_preloader_logo', 1);
	$rooten_final_logo     = ($rooten_logo == 'custom') ? $rooten_custom_logo : $rooten_logo_default;
	$rooten_text           = get_theme_mod('rooten_preloader_text', 1);
	$rooten_custom_text    = get_theme_mod('rooten_preloader_custom_text');
	$rooten_site_name      = get_bloginfo( 'name' );
	$rooten_default_text   = sprintf(esc_html__( 'Please Wait, %s is Loading...', 'rooten' ), $rooten_site_name );
	$rooten_animation      = get_theme_mod('rooten_preloader_animation', 1);
	$rooten_animation_html = '<div class="tm-spinner tm-spinner-three-bounce"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';
	if ($rooten_animation) {
		$rooten_loading_html[] = $rooten_animation_html;
	}
	
	if ($rooten_text) {
		$rooten_loading_html[]   = $rooten_default_text;
	} elseif ($rooten_text == 'custom') {
		$rooten_loading_html[]   = $rooten_custom_text;
	}


	$rooten_settings = [
		'logo'        => ($rooten_logo) ? $rooten_final_logo : '',
		'loadingHtml' => implode( " ", $rooten_loading_html ),
	];


?>
<script type="text/javascript">
	window.loading_screen = window.pleaseWait(<?php echo json_encode($rooten_settings); ?>);
	window.onload=function(){
		window.setTimeout(function(){
		    loading_screen.finish();
		},3000);
	}
</script>

<?php

function rooten_pwa_splash_screen() {
	?>
	<link rel="apple-touch-startup-image" href="<?php echo esc_url($rooten_final_logo); ?>" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
	<link rel="apple-touch-startup-image" href="<?php echo esc_url($rooten_final_logo); ?>" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
	<link rel="apple-touch-startup-image" href="<?php echo esc_url($rooten_final_logo); ?>" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
	<link rel="apple-touch-startup-image" href="<?php echo esc_url($rooten_final_logo); ?>" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
	<link rel="apple-touch-startup-image" href="<?php echo esc_url($rooten_final_logo); ?>" media="(min-device-width: 768px) and (max-device-width: 1024px) and (-webkit-min-device-pixel-ratio: 2) and (orientation: portrait)">
	<link rel="apple-touch-startup-image" href="<?php echo esc_url($rooten_final_logo); ?>" media="(min-device-width: 834px) and (max-device-width: 834px) and (-webkit-min-device-pixel-ratio: 2) and (orientation: portrait)">
	<link rel="apple-touch-startup-image" href="<?php echo esc_url($rooten_final_logo); ?>" media="(min-device-width: 1024px) and (max-device-width: 1024px) and (-webkit-min-device-pixel-ratio: 2) and (orientation: portrait)">

	<?php
}
add_action( 'wp_head' , array( $this, 'rooten_pwa_splash_screen' ) );