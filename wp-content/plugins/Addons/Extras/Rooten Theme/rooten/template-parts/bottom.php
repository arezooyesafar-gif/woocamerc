<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package rooten
 */

if(get_theme_mod('rooten_footer_widgets', 1) && get_post_meta( get_the_ID(), 'rooten_footer_widgets', true ) != 'hide') {

	$rooten_id                  = 'tm-bottom';
	$rooten_class               = ['tm-bottom', 'bdt-section'];
	$rooten_section             = '';
	$rooten_section_media       = [];
	$rooten_section_image       = '';
	$rooten_container_class     = [];
	$rooten_grid_class          = ['bdt-grid', 'bdt-margin'];
	$rooten_bottom_width        = get_theme_mod( 'rooten_bottom_width', 'default');
	$rooten_breakpoint          = get_theme_mod( 'rooten_bottom_breakpoint', 'm' );
	$rooten_vertical_align      = get_theme_mod( 'rooten_bottom_vertical_align' );
	$rooten_match_height        = get_theme_mod( 'rooten_bottom_match_height' );
	$rooten_column_divider      = get_theme_mod( 'rooten_bottom_column_divider' );
	$rooten_gutter              = get_theme_mod( 'rooten_bottom_gutter' );
	$rooten_columns             = get_theme_mod( 'rooten_footer_columns', 4);
	$rooten_first_column_expand = get_theme_mod( 'rooten_footer_fce');
	
	
	$rooten_layout         = get_post_meta( get_the_ID(), 'rooten_bottom_layout', true );
	$rooten_metabox_layout = (!empty($rooten_layout) and $rooten_layout != 'default') ? true : false;
	$rooten_position       = (get_post_meta( get_the_ID(), 'rooten_page_layout', true )) ? get_post_meta( get_the_ID(), 'rooten_page_layout', true ) : get_theme_mod( 'rooten_page_layout', 'sidebar-right' );

	if ($rooten_metabox_layout) {
	    $rooten_bg_style = get_post_meta( get_the_ID(), 'rooten_bottom_bg_style', true );
	    $rooten_bg_style = ( !empty($rooten_bg_style) ) ? $rooten_bg_style : get_theme_mod( 'rooten_bottom_bg_style' );
	    $rooten_padding  = get_post_meta( get_the_ID(), 'rooten_bottom_padding', true );
	    $rooten_text     = get_post_meta( get_the_ID(), 'rooten_bottom_txt_style', true );
	} else {
	    $rooten_bg_style = get_theme_mod( 'rooten_bottom_bg_style', 'secondary' );
	    $rooten_padding  = get_theme_mod( 'rooten_bottom_padding', 'medium' );
	    $rooten_text     = get_theme_mod( 'rooten_bottom_txt_style' );
	}

     
	    
    if ($rooten_metabox_layout) {
        $rooten_section_images = rwmb_meta( 'rooten_bottom_bg_img', "type=image_advanced&size=standard" );
        foreach ( $rooten_section_images as $rooten_image ) { 
            $rooten_section_image = esc_url($rooten_image["url"]);
        }
        $rooten_section_bg_img_pos    = get_post_meta( get_the_ID(), 'rooten_bottom_bg_img_position', true );
        $rooten_section_bg_img_attach = get_post_meta( get_the_ID(), 'rooten_bottom_bg_img_fixed', true );
        $rooten_section_bg_img_vis    = get_post_meta( get_the_ID(), 'rooten_bottom_bg_img_visibility', true );
    } else {
        $rooten_section_image         = get_theme_mod( 'rooten_bottom_bg_img' );
        $rooten_section_bg_img_pos    = get_theme_mod( 'rooten_bottom_bg_img_position' );
        $rooten_section_bg_img_attach = get_theme_mod( 'rooten_bottom_bg_img_fixed' );
        $rooten_section_bg_img_vis    = get_theme_mod( 'rooten_bottom_bg_img_visibility' );
    }

    // Image
    if ($rooten_section_image &&  $rooten_bg_style == 'media') {
        $rooten_section_media['style'][] = "background-image: url('{$rooten_section_image}');";
        // Settings
        $rooten_section_media['class'][] = 'bdt-background-norepeat';
        $rooten_section_media['class'][] = $rooten_section_bg_img_pos ? "bdt-background-{$rooten_section_bg_img_pos}" : '';
        $rooten_section_media['class'][] = $rooten_section_bg_img_attach ? "bdt-background-fixed" : '';
        $rooten_section_media['class'][] = $rooten_section_bg_img_vis ? "bdt-background-image@{$rooten_section_bg_img_vis}" : '';
    }

	$rooten_class[] = ($rooten_position == 'full' and $name == 'tm-main') ? 'bdt-padding-remove-vertical' : ''; // section spacific override

	$rooten_class[] = ($rooten_bg_style) ? 'bdt-section-'.$rooten_bg_style : '';

	$rooten_class[] = ($rooten_text) ? 'bdt-'.$rooten_text : '';
	if ($rooten_padding != 'none') {
	    $rooten_class[]       = ($rooten_padding) ? 'bdt-section-'.$rooten_padding : '';
	} elseif ($rooten_padding == 'none') {
	    $rooten_class[]       = ($rooten_padding) ? 'bdt-padding-remove-vertical' : '';
	}



	$rooten_container_class[] = ($rooten_bottom_width) ? 'bdt-container bdt-container-'.$rooten_bottom_width : '';
	
	$rooten_grid_class[]      = ($rooten_gutter) ? 'bdt-grid-'.$rooten_gutter : '';
	$rooten_grid_class[]      = ($rooten_column_divider && $rooten_gutter != 'collapse') ? 'bdt-grid-divider' : '';
	$rooten_grid_class[]      = ($rooten_breakpoint) ? 'bdt-child-width-expand@'.$rooten_breakpoint : '';
	$rooten_grid_class[]      = ($rooten_vertical_align) ? 'bdt-flex-middle' : '';
	$rooten_match_height = (!$rooten_vertical_align && $rooten_match_height) ? ' bdt-height-match="target: > div > div > .bdt-card"' : '';
	
	$rooten_expand_columns    = intval($rooten_columns)+1;
	$rooten_column_class      = ($rooten_first_column_expand) ? ' bdt-width-1-'.$rooten_expand_columns.'@l' : '';

	if (is_active_sidebar('footer-widgets') || is_active_sidebar('footer-widgets-2') || is_active_sidebar('footer-widgets-3') || is_active_sidebar('footer-widgets-4')) : ?>
		<div<?php rooten_helper::attrs(['id' => $rooten_id, 'class' => $rooten_class], $rooten_section_media); ?>>
			<div<?php rooten_helper::attrs(['class' => $rooten_container_class]) ?>>
				
				<?php if (is_active_sidebar('bottom-widgets')) : ?>
					<div class="bottom-widgets bdt-child-width-expand@s" bdt-grid><?php if (dynamic_sidebar('bottom-widgets')); ?></div>
					<hr class="bdt-margin-medium">
				<?php endif; ?>
				
				<div<?php rooten_helper::attrs(['class' => $rooten_grid_class]) ?> bdt-grid<?php echo esc_attr($rooten_match_height); ?>>

					<?php if (is_active_sidebar('footer-widgets') && $rooten_columns) : ?>
						<div class="bottom-columns bdt-width-1-3@m"><?php if (dynamic_sidebar('Footer Widgets 1')); ?></div>
					<?php endif; ?>
					<?php if (is_active_sidebar('footer-widgets-2') && $rooten_columns > 1) : ?>
						<div class="bottom-columns<?php echo esc_attr($rooten_column_class); ?>"><?php if (dynamic_sidebar('Footer Widgets 2')); ?></div>
					<?php endif; ?>
					<?php if (is_active_sidebar('footer-widgets-3') && $rooten_columns > 2) : ?>
						<div class="bottom-columns<?php echo esc_attr($rooten_column_class); ?>"><?php if (dynamic_sidebar('Footer Widgets 3')); ?></div>
					<?php endif; ?>
					<?php if (is_active_sidebar('footer-widgets-4') && $rooten_columns > 3) : ?>
						<div class="bottom-columns<?php echo esc_attr($rooten_column_class); ?>"><?php if (dynamic_sidebar('Footer Widgets 4')); ?></div>	
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif;
}