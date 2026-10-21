<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Cart_Fragments extends Tarh99_Perf_Module {

	public function id() {
		return 'cart_fragments';
	}

	public function title() {
		return 'مدیریت wc-cart-fragments';
	}

	public function description() {
		return 'اسکریپت cart-fragments (یک درخواست ایجکس غیرقابل‌کش در هر بازدید) فقط در صفحاتی که لازم است لود می‌شود. قالب پوشه همین کار را انجام می‌دهد؛ این ماژول تضمین می‌کند که بعد از آپدیت/تغییر قالب هم همین‌طور بماند. شمارنده سبد هدر توسط پاسخ افزودن به سبد ایجکسی قالب به‌روز می‌شود.';
	}

	public function fields() {
		return array(
			'allow'       => array(
				'type'    => 'text',
				'label'   => 'محدوده‌هایی که cart-fragments مجاز است (با کاما)',
				'default' => 'cart,checkout',
			),
			'enqueue_all' => array(
				'type'    => 'checkbox',
				'label'   => 'بارگذاری cart-fragments در همه صفحات (فقط اگر کوکی‌های سبد را از «Never Cache Cookies» راکت حذف کرده‌اید و می‌خواهید شمارنده سبد روی صفحات کش‌شده درست باشد)',
				'default' => 0,
			),
		);
	}

	public function boot() {
		if ( $this->opt( 'enqueue_all' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 1000 );
			return;
		}
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue' ), 999 );
		add_action( 'wp_print_footer_scripts', array( $this, 'dequeue' ), 1 );
	}

	public function enqueue() {
		if ( ! Tarh99_Perf_Env::front_ok() || ! Tarh99_Perf_Env::wc() ) {
			return;
		}
		if ( wp_script_is( 'wc-cart-fragments', 'registered' ) ) {
			wp_enqueue_script( 'wc-cart-fragments' );
		}
	}

	public function dequeue() {
		if ( ! Tarh99_Perf_Env::front_ok() || ! Tarh99_Perf_Env::wc() ) {
			return;
		}
		if ( Tarh99_Perf_Env::scope_matches( (string) $this->opt( 'allow' ) ) ) {
			return;
		}
		if ( wp_script_is( 'wc-cart-fragments', 'enqueued' ) ) {
			Tarh99_Perf_Assets::safe_dequeue( 'js', 'wc-cart-fragments' );
		}
	}
}
