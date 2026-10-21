<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package rooten
 */

	$rooten_layout            = get_theme_mod('rooten_global_layout', 'full');
	$rooten_cookie_bar        = get_theme_mod('rooten_cookie');
	$rooten_totop_show        = get_theme_mod('rooten_totop_show', 1);
	$rooten_totop_align       = get_theme_mod('rooten_totop_align', 'left');
	$rooten_totop_radius      = get_theme_mod('rooten_totop_radius', 'circle');
	$rooten_totop_bg          = get_theme_mod('rooten_totop_bg_style', 'secondary'); // TODO
	$rooten_totop_class       = ['tm-totop-scroller', 'bdt-totop', 'bdt-position-medium', 'bdt-position-fixed'];
	$rooten_totop_class[]     = ($rooten_totop_align) ? 'bdt-position-bottom-'.$rooten_totop_align : 'bdt-position-bottom-left';
	$rooten_totop_class[]     = ($rooten_totop_radius) ? 'bdt-border-'.$rooten_totop_radius : '';
	$rooten_totop_class[]     = ($rooten_totop_bg) ? 'bdt-background-'.$rooten_totop_bg : 'bdt-background-secondary';
	$rooten_totop_class[]     = ($rooten_totop_bg == 'default' or $rooten_totop_bg == 'muted') ? 'bdt-dark' : 'bdt-light';
	
	$rooten_mb_custom_footer  = get_post_meta( get_the_ID(), 'rooten_custom_footer', true );
	$rooten_mb_footer_widgets = get_post_meta( get_the_ID(), 'rooten_footer_widgets', true );

	$rooten_tm_custom_footer  = get_theme_mod('rooten_custom_footer');
	$rooten_tm_footer_widgets = get_theme_mod('rooten_footer_widgets');
	
	$rooten_custom_footer     = '';

	if ( 'custom' == $rooten_mb_footer_widgets and  isset($rooten_mb_custom_footer) ) {
		$rooten_custom_footer =  $rooten_mb_custom_footer;
	} elseif ( 'custom' == $rooten_tm_footer_widgets and  isset($rooten_tm_custom_footer) ) {
		$rooten_custom_footer =  $rooten_tm_custom_footer;
	}

	?>
	
	<?php if (! function_exists( 'elementor_theme_do_location' ) or ! elementor_theme_do_location( 'footer' ) ) : ?>
		<?php if (!is_page_template( 'page-blank.php' ) and !is_404() ) : ?>
			<?php  if ( $rooten_custom_footer ) : ?>
				<?php echo Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $rooten_custom_footer ); ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/bottom' ); ?>
				<?php get_template_part( 'template-parts/copyright' ); ?>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
	

	<?php if ($rooten_layout == 'boxed') : ?>
		</div><!-- #margin -->
	</div><!-- #tm-page -->
	<?php endif; ?>

	<?php get_template_part( 'template-parts/fixed-left' ); ?>	
	<?php get_template_part( 'template-parts/fixed-right' ); ?>


	<?php if( ! function_exists( 'elementor_theme_do_location' ) or ! elementor_theme_do_location( 'footer' ) ): ?>
		<?php if($rooten_totop_show and !is_page_template( 'page-blank.php' ) ): ?>
			<a <?php rooten_helper::attrs(['class' => $rooten_totop_class]); ?> href="#"  bdt-totop bdt-scroll></a>
		<?php endif; ?>
	<?php endif; ?>

    <?php if ($rooten_cookie_bar) : ?>
		<?php get_template_part('template-parts/cookie-bar'); ?>
	<?php endif ?>

	<?php wp_footer(); ?>

</body>
</html>
