<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Theme_Guard extends Tarh99_Perf_Module {

	public function id() {
		return 'theme_guard';
	}

	public function group() {
		return 'stability';
	}

	public function title() {
		return 'محافظ تغییرات دستی قالب';
	}

	public function description() {
		return 'تغییراتی که قبلاً دستی در functions.php قالب پوشه اضافه شده (تکمیل خودکار سفارش‌های پرداخت‌شده، عنوان «طرح 99» در breadcrumb، حذف پیام «لطفا با حساب کاربری خود وارد شوید...»، حذف منوی حساب کاربری قالب و نمایش اشتراک مورد نیاز) با آپدیت قالب پاک می‌شوند. این ماژول فقط وقتی آن کدها در قالب وجود نداشته باشند، همان رفتار را دوباره فعال می‌کند؛ تا وقتی در قالب هستند هیچ کاری نمی‌کند.';
	}

	public function fields() {
		return array(
			'autocomplete' => array(
				'type'    => 'checkbox',
				'label'   => 'وضعیت سفارش پرداخت‌شده = تکمیل‌شده',
				'default' => 1,
			),
			'breadcrumb'   => array(
				'type'    => 'checkbox',
				'label'   => 'متن خانه در breadcrumb ووکامرس',
				'default' => 1,
			),
			'home_text'    => array(
				'type'    => 'text',
				'label'   => 'متن خانه',
				'default' => 'طرح 99',
			),
			'login_notice' => array(
				'type'    => 'checkbox',
				'label'   => 'حذف پیام «لطفا با حساب کاربری خود وارد شوید تا لینک دانلود به شما نمایش داده شود.» برای مهمان',
				'default' => 1,
			),
			'account_menu' => array(
				'type'    => 'checkbox',
				'label'   => 'حذف فیلتر منوی حساب کاربری قالب (pooshe_woocommerce_account_menu_items)',
				'default' => 1,
			),
		);
	}

	public function boot() {
		add_action( 'after_setup_theme', array( $this, 'restore' ), 1000 );
	}

	private function customized_functions_present() {
		return function_exists( 'tarh99_disable_wp_emojis' );
	}

	public function restore() {
		if ( ! Tarh99_Perf_Env::is_pooshe() ) {
			return;
		}
		if ( $this->opt( 'autocomplete' ) && ! function_exists( 'woocommerceir_autocomplete_processing_orders' ) ) {
			add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'completed' ), 9999 );
		}
		if ( $this->opt( 'breadcrumb' ) && ! function_exists( 'prefix_change_breadcrumb_home_text' ) ) {
			add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'breadcrumb' ) );
		}
		if ( $this->customized_functions_present() ) {
			return;
		}
		if ( $this->opt( 'login_notice' ) ) {
			add_action( 'template_redirect', array( $this, 'buffer_login_notice' ), 0 );
		}
		if ( $this->opt( 'account_menu' ) ) {
			remove_filter( 'woocommerce_account_menu_items', 'pooshe_woocommerce_account_menu_items', 99 );
			add_action( 'init', array( $this, 'remove_account_menu_filter' ), 999 );
		}
	}

	public function completed( $status ) {
		return 'completed';
	}

	public function breadcrumb( $defaults ) {
		if ( is_array( $defaults ) ) {
			$defaults['home'] = (string) $this->opt( 'home_text' );
		}
		return $defaults;
	}

	public function remove_account_menu_filter() {
		remove_filter( 'woocommerce_account_menu_items', 'pooshe_woocommerce_account_menu_items', 99 );
	}

	public function buffer_login_notice() {
		if ( is_admin() || is_user_logged_in() || ! Tarh99_Perf_Env::is_frontend() ) {
			return;
		}
		ob_start( array( $this, 'strip_login_notice' ) );
	}

	public function strip_login_notice( $buffer ) {
		if ( ! is_string( $buffer ) || false === strpos( $buffer, 'لطفا با حساب کاربری خود وارد شوید' ) ) {
			return $buffer;
		}
		$result = preg_replace(
			'#<div[^>]*>\s*<p>\s*لطفا با حساب کاربری خود وارد شوید تا لینک دانلود به شما نمایش داده شود\.\s*</p>\s*</div>#u',
			'',
			$buffer,
			1
		);
		return is_string( $result ) ? $result : $buffer;
	}
}
