<?php

defined( 'ABSPATH' ) || exit;

function pooshe_product_badges() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$badges = array();

	$total_sales = (int) $product->get_total_sales();
	if ( $total_sales > 30 ) {
		$badges[] = array( 'best-seller', 'پرفروش‌ترین' );
	}

	$rating = (float) $product->get_average_rating();
	if ( $rating >= 4.5 ) {
		$badges[] = array( 'popular', 'محبوب‌ترین' );
	}

	$post_date = (int) get_the_date( 'U' );
	if ( $post_date > strtotime( '-7 days' ) ) {
		$badges[] = array( 'new-badge', 'جدید' );
	}

	if ( empty( $badges ) ) {
		return;
	}

	echo '<div class="pooshe-badges">';
	foreach ( $badges as $badge ) {
		echo '<span class="pooshe-badge pooshe-badge--' . esc_attr( $badge[0] ) . '">' . esc_html( $badge[1] ) . '</span>';
	}
	echo '</div>';
}

add_action( 'woocommerce_before_shop_loop_item_title', 'pooshe_product_badges', 9 );
