<?php

defined( 'ABSPATH' ) || exit;

function pooshe_dequeue_assets() {
	if ( function_exists( 'WC' ) ) {

		if ( ! is_checkout() ) {
			wp_dequeue_script( 'selectWoo' );
			wp_dequeue_style( 'select2' );
			wp_deregister_style( 'select2' );
		}

		if ( ! is_product() && ! is_shop() && ! is_product_category() && ! is_product_tag() ) {
			wp_dequeue_script( 'wc-add-to-cart' );
			wp_dequeue_script( 'wc-add-to-cart-variation' );
		}

		if ( ! is_account_page() ) {
			wp_dequeue_script( 'wc-password-strength-meter' );
		}

		if ( ! is_cart() && ! is_checkout() ) {
			wp_dequeue_script( 'wc-cart-fragments' );
		}

		if ( ! is_checkout() ) {
			wp_dequeue_script( 'wc-checkout' );
			wp_dequeue_script( 'wc-country-select' );
			wp_dequeue_script( 'wc-credit-card-form' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'pooshe_dequeue_assets', 100 );
