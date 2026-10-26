<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Gateway_Redirect extends Tarh99_Perf_Module {

	public function id() {
		return 'gateway_redirect';
	}

	public function title() {
		return 'انتقال سریع‌تر به درگاه پرداخت (زرین‌پال)';
	}

	public function description() {
		return 'بعد از «ثبت سفارش»، ووکامرس مشتری را به صفحه «پرداخت سفارش» می‌فرستد. این صفحه کامل ساخته می‌شود (هدر، فوتر، قالب و المنتور) و افزونه زرین‌پال تازه وسط ساختن آن به سرور زرین‌پال وصل می‌شود و مشتری را به درگاه منتقل می‌کند؛ مرورگر هیچ‌وقت این صفحه را نشان نمی‌دهد ولی مشتری منتظر ساخته شدن آن می‌ماند. این ماژول همان کار افزونه زرین‌پال را (با همان کد خود افزونه و همان شرط‌های ووکامرس: کلید سفارش درست و سفارش در انتظار پرداخت) قبل از شروع ساختن صفحه انجام می‌دهد. اگر اتصال به زرین‌پال خطا بدهد، صفحه مثل قبل نمایش داده می‌شود و پیام خطای زرین‌پال همان‌جا دیده می‌شود (قبلاً در صفحه بعدی دیده می‌شد)؛ اتصال دوباره در همان صفحه انجام نمی‌شود. فقط برای درگاه‌هایی است که در این مرحله مشتری را به سایت درگاه منتقل می‌کنند.';
	}

	public function group() {
		return 'server';
	}

	public function risk() {
		return 'medium';
	}

	public function fields() {
		return array(
			'methods' => array(
				'type'    => 'textarea',
				'label'   => 'شناسه درگاه‌ها (هر خط یک شناسه؛ زرین‌پال: WC_ZPal)',
				'default' => 'WC_ZPal',
			),
		);
	}

	public function boot() {
		add_action( 'template_redirect', array( $this, 'maybe_redirect' ), 1 );
	}

	public function maybe_redirect() {
		if ( ! Tarh99_Perf_Env::wc() || ! Tarh99_Perf_Env::is_get() || ! function_exists( 'is_wc_endpoint_url' ) || ! is_wc_endpoint_url( 'order-pay' ) ) {
			return;
		}
		if ( isset( $_GET['pay_for_order'] ) || ! isset( $_GET['key'] ) || ! is_string( $_GET['key'] ) ) {
			return;
		}
		$order_id = absint( get_query_var( 'order-pay' ) );
		$key      = wc_clean( wp_unslash( $_GET['key'] ) );
		if ( ! $order_id || '' === $key ) {
			return;
		}
		$order = wc_get_order( $order_id );
		if ( ! $order || $order->get_id() !== $order_id || ! hash_equals( (string) $order->get_order_key(), $key ) || ! $order->needs_payment() ) {
			return;
		}
		$method = (string) $order->get_payment_method();
		if ( '' === $method || ! in_array( $method, $this->opt_lines( 'methods' ), true ) ) {
			return;
		}
		WC()->payment_gateways();
		$hook = 'woocommerce_receipt_' . $method;
		if ( ! has_action( $hook ) ) {
			return;
		}
		do_action( $hook, $order_id );
		remove_all_actions( $hook );
	}
}
