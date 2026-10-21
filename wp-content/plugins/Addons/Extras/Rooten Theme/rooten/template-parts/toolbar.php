<?php 
$rooten_classes            = ['bdt-container', 'bdt-flex bdt-flex-middle'];
$rooten_mb_toolbar         = (get_post_meta( get_the_ID(), 'rooten_toolbar', true ) != null) ? get_post_meta( get_the_ID(), 'rooten_toolbar', true ) : false;
$rooten_tm_toolbar         = (get_theme_mod( 'rooten_toolbar', 0)) ? 1 : 0;
$rooten_toolbar_left       = get_theme_mod( 'rooten_toolbar_left', 'tagline' );
$rooten_toolbar_right      = get_theme_mod( 'rooten_toolbar_right', 'social' );
$rooten_toolbar_cart       = get_theme_mod( 'rooten_woocommerce_cart' );
$rooten_classes[]          = (get_theme_mod( 'rooten_toolbar_fullwidth' )) ? 'bdt-container-expand' : '';
$rooten_toolbar_left_hide  = (get_theme_mod( 'rooten_toolbar_left_hide_mobile' )) ? ' bdt-visible@s' : '';
$rooten_toolbar_right_hide = (get_theme_mod( 'rooten_toolbar_right_hide_mobile' )) ? ' bdt-visible@s' : '';
$rooten_toolbar_full_hide  = ( $rooten_toolbar_left_hide and $rooten_toolbar_right_hide ) ? ' bdt-visible@s' : '';

?>

<?php if ($rooten_tm_toolbar and $rooten_mb_toolbar != true) : ?>
	<div class="tm-toolbar<?php echo esc_attr($rooten_toolbar_full_hide); ?>">
		<div<?php rooten_helper::attrs(['class' => $rooten_classes]) ?>>

			<?php if (!empty($rooten_toolbar_left)) : ?>
			<div class="tm-toolbar-l<?php echo esc_attr($rooten_toolbar_left_hide); ?>"><?php get_template_part( 'template-parts/toolbars/'.$rooten_toolbar_left ); ?></div>
			<?php endif; ?>

			<?php if (!empty($rooten_toolbar_right) or $rooten_toolbar_cart == 'toolbar') : ?>
			<div class="tm-toolbar-r bdt-margin-auto-left bdt-flex<?php echo esc_attr($rooten_toolbar_right_hide); ?>">
				<?php if ($rooten_toolbar_cart == 'toolbar') : ?>
					<div class="bdt-display-inline-block">
						<?php get_template_part( 'template-parts/toolbars/'.$rooten_toolbar_right ); ?>
					</div>
					<div class="bdt-display-inline-block bdt-margin-small-left">
						<?php get_template_part('template-parts/woocommerce-cart'); ?>
					</div>
				<?php else: ?>
					<?php get_template_part( 'template-parts/toolbars/'.$rooten_toolbar_right ); ?>
				<?php endif; ?>
			</div>
			<?php endif; ?>

		</div>
	</div>
<?php endif; ?>