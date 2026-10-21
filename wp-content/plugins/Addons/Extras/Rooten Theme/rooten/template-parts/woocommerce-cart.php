<?php if (class_exists('Woocommerce')) { 
	
	$rooten_layout_c        = get_theme_mod('header_layout', 'horizontal-left');
	$rooten_layout_m        = get_post_meta( get_the_ID(), 'header_layout', true );
	$rooten_layout          = (!empty($rooten_layout_m) and $rooten_layout_m != 'default') ? $rooten_layout_m : $rooten_layout_c;

	$rooten_cart = get_theme_mod('woocommerce_cart');

	if($rooten_cart !== 'no') { 
	global $woocommerce; 
	$rooten_wcrtl = (is_rtl()) ? 'left' : 'right';
	$rooten_offset = ( $rooten_cart == 'toolbar') ? 15 : 30;
	?>
	
	<div class="tm-cart-popup">
		<a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="tm-shopping-cart" title="<?php esc_html_e('View Cart', 'rooten'); ?>">
			<span bdt-icon="icon: cart"></span>
			<?php
				$rooten_product_bumber = $woocommerce->cart->cart_contents_count; 
				if ($rooten_cart == 'header') {
					if ( sizeof( $woocommerce->cart->cart_contents ) != 0 ) {
						echo '<span class="pcount">'.esc_html($rooten_product_bumber).'</span>';
					} 
				}
				if ($rooten_cart == 'toolbar') {
					echo '<div class="bdt-hidden-small bdt-display-inline">';
					if ( sizeof( $woocommerce->cart->cart_contents ) == 0 ) {
						esc_html_e('Cart is Empty', 'rooten');
					} else {
						echo sprintf( _n( '%s Item in cart', '%s Items in cart', $rooten_product_bumber, 'rooten' ), $rooten_product_bumber );
					}
					echo '</div>';
				} 
			?>
		</a>
		
		<?php if (!in_array($rooten_layout, ['side-left', 'side-right'])) : ?>
			<?php if ( sizeof( $woocommerce->cart->cart_contents ) != 0 and !is_checkout() and !is_cart()) : ?>
				<div class="cart-dropdown bdt-drop" bdt-drop="mode: hover; offset: <?php echo esc_attr($rooten_offset); ?>; pos: bottom-right;">
					<div class="bdt-card bdt-card-body bdt-card-default">
						<?php the_widget( 'WC_Widget_Cart', '' ); ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>

	</div>
	<?php }
} ?>