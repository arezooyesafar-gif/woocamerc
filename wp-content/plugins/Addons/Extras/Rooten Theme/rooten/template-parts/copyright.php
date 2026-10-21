<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package rooten
 */

if(get_post_meta( get_the_ID(), 'rooten_copyright', true ) != 'hide') {

	$rooten_class             = ['tm-copyright', 'bdt-section'];
	$rooten_container_class   = [];
	$rooten_grid_class        = ['bdt-grid', 'bdt-flex', 'bdt-flex-middle'];
	$rooten_background_style  = get_theme_mod( 'rooten_copyright_bg_style', 'secondary' );
	$rooten_width             = get_theme_mod( 'rooten_copyright_width', 'default');
	$rooten_padding           = get_theme_mod( 'rooten_copyright_padding', 'small' );
	$rooten_text              = get_theme_mod( 'rooten_copyright_txt_style' );
	$rooten_breakpoint        = get_theme_mod( 'rooten_bottom_breakpoint', 'm' );
	
	$rooten_class[]           = ($rooten_background_style) ? 'bdt-section-'.$rooten_background_style : '';
	$rooten_class[]           = ($rooten_text) ? 'bdt-'.$rooten_text : '';
	if ($rooten_padding != 'none') {
		$rooten_class[]       = ($rooten_padding) ? 'bdt-section-'.$rooten_padding : '';
	} elseif ($rooten_padding == 'none') {
		$rooten_class[]       = ($rooten_padding) ? 'bdt-padding-remove-vertical' : '';
	}
	
	$rooten_container_class[] = ($rooten_width) ? 'bdt-container bdt-container-'.$rooten_width : '';
	
	$rooten_grid_class[]      = ($rooten_breakpoint) ? 'bdt-child-width-expand@'.$rooten_breakpoint : '';

	?>

	<div id="tmCopyright"<?php rooten_helper::attrs(['class' => $rooten_class]) ?>>
		<div<?php rooten_helper::attrs(['class' => $rooten_container_class]) ?>>
			<div<?php rooten_helper::attrs(['class' => $rooten_grid_class]) ?> bdt-grid>
				<div class="bdt-width-expand@m">	
					<?php									 
					if (has_nav_menu('copyright')) { echo wp_nav_menu( array( 'theme_location' => 'copyright', 'container_class' => 'tm-copyright-menu bdt-display-inline-block', 'menu_class' => 'bdt-subnav bdt-subnav-line bdt-subnav-divider bdt-margin-small-bottom', 'depth' => 1 ) ); }
					
					if(get_theme_mod('rooten_copyright_text_custom_show')) : ?>
						<div class="copyright-txt"><?php echo wp_kses_post(get_theme_mod('rooten_copyright_text_custom')); ?></div>
					<?php else : ?>								
						<div class="copyright-txt">&copy; <?php esc_html_e('Copyright', 'rooten') ?> <?php echo esc_html(date("Y ")); ?> <?php esc_html_e('All Rights Reserved by', 'rooten') ?> <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php bloginfo( 'name' );?>"> <?php echo esc_html(bloginfo('name')); ?> </a></div>
					<?php endif; ?>
				</div>
				<div class="bdt-width-auto@m">
					<?php get_template_part( 'template-parts/copyright-social'); ?>
				</div>
			</div>
		</div>
	</div>

	<?php 
}