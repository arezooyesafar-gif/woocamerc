<?php if (is_active_sidebar('drawer')) : ?>
<?php
	$rooten_class             = ['tm-drawer', 'bdt-section'];
	$rooten_container_class   = [];
	$rooten_grid_class        = ['bdt-grid'];
	$rooten_background_style  = get_theme_mod( 'rooten_drawer_bg_style', 'secondary' );
	$rooten_width             = get_theme_mod( 'rooten_drawer_width', 'default');
	$rooten_padding           = get_theme_mod( 'rooten_drawer_padding', 'small' );
	$rooten_text              = get_theme_mod( 'rooten_drawer_txt_style' );
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
	$rooten_wrapper_bg = ($rooten_background_style) ? ' bdt-background-'.$rooten_background_style : '';
?>

<div class="drawer-wrapper<?php echo esc_url($rooten_wrapper_bg); ?>">
	<div id="tm-drawer" <?php rooten_helper::attrs(['class' => $rooten_class]) ?> hidden>
		<div <?php rooten_helper::attrs(['class' => $rooten_container_class]) ?>>
			<div <?php rooten_helper::attrs(['class' => $rooten_grid_class]) ?> bdt-grid>
				<?php dynamic_sidebar('drawer'); ?>
			</div>
		</div>
	</div>
	<a href="javascript:void(0);" class="drawer-toggle bdt-position-top-right bdt-margin-small-right" bdt-toggle="target: #tm-drawer; animation: bdt-animation-slide-top; queued: true"><span bdt-icon="icon: chevron-down"></span></a>
</div>
<?php endif; ?>