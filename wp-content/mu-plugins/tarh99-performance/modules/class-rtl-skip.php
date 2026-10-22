<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Rtl_Skip extends Tarh99_Perf_Module {

	const PLUGIN = 'RTL-CareUnit/RTL-CareUnit.php';

	public function id() {
		return 'rtl_skip';
	}

	public function group() {
		return 'server';
	}

	public function title() {
		return 'عدم بارگذاری «مدیریت هوشمند راست‌چین» (RTL-CareUnit) برای بازدیدکننده‌ها';
	}

	public function description() {
		return 'این افزونه فقط کار لایسنس و به‌روزرسانی محصولات راست‌چین را در پیشخوان انجام می‌کند ولی روی هاست شما در هر بار ساخت صفحه ۱.۳ تا ۲.۳ ثانیه وقت می‌گیرد. با این ماژول در صفحات سایت (نه پیشخوان، نه کران، نه REST) بارگذاری نمی‌شود؛ در پیشخوان مثل قبل کامل کار می‌کند و غیرفعال یا حذف نمی‌شود.';
	}

	public function default_enabled() {
		return false;
	}

	public function risk() {
		return 'medium';
	}

	public function fields() {
		return array(
			'logged_in' => array(
				'type'    => 'checkbox',
				'label'   => 'برای کاربران لاگین‌شده هم (در صفحات سایت؛ پیشخوان همیشه کامل است)',
				'default' => 1,
			),
			'ajax'      => array(
				'type'    => 'checkbox',
				'label'   => 'برای درخواست‌های ایجکس مهمان‌ها از صفحات سایت (جستجو، اسلایدرها، سبد)',
				'default' => 1,
			),
		);
	}

	public function boot() {
	}

	public function early() {
		if ( $this->should_skip() ) {
			add_filter( 'option_active_plugins', array( $this, 'filter_plugins' ), 1 );
			add_filter( 'pre_http_request', array( $this, 'block_download' ), 1, 3 );
		}
	}

	public function block_download( $preempt, $args, $url ) {
		if ( false !== $preempt ) {
			return $preempt;
		}
		$host = strtolower( (string) wp_parse_url( (string) $url, PHP_URL_HOST ) );
		if ( 'files.rtl-theme.com' === $host || '.rtl-theme.com' === substr( $host, -14 ) ) {
			return new WP_Error( 'tarh99_rtl_skip', 'Skipped during visitor request' );
		}
		return $preempt;
	}

	public function filter_plugins( $plugins ) {
		if ( ! is_array( $plugins ) ) {
			return $plugins;
		}
		$key = array_search( self::PLUGIN, $plugins, true );
		if ( false !== $key ) {
			unset( $plugins[ $key ] );
			$plugins = array_values( $plugins );
		}
		return $plugins;
	}

	private function logged_in_cookie() {
		foreach ( array_keys( $_COOKIE ) as $name ) {
			if ( 0 === strpos( (string) $name, 'wordpress_logged_in_' ) ) {
				return true;
			}
		}
		return false;
	}

	private function should_skip() {
		if ( isset( $_GET['tarh99_rtl'] ) && 'on' === $_GET['tarh99_rtl'] ) {
			return false;
		}
		if ( ( defined( 'WP_CLI' ) && WP_CLI ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {
			return false;
		}
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? strtolower( (string) $_SERVER['REQUEST_URI'] ) : '';
		if ( '' === $uri || preg_match( '#/wp-json/|rest_route=|rtl|rsm|careunit|/wp-login\.php|/wp-cron\.php|/xmlrpc\.php#', $uri ) ) {
			return false;
		}
		$logged_in = $this->logged_in_cookie();
		if ( $logged_in && ! $this->opt( 'logged_in' ) ) {
			return false;
		}
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			if ( ! $this->opt( 'ajax' ) || $logged_in ) {
				return false;
			}
			$referer = isset( $_SERVER['HTTP_REFERER'] ) ? (string) $_SERVER['HTTP_REFERER'] : '';
			$action  = isset( $_REQUEST['action'] ) ? strtolower( (string) $_REQUEST['action'] ) : '';
			return '' !== $action && false === strpos( $referer, '/wp-admin/' ) && ! preg_match( '/rtl|rsm|care|license|update/', $action );
		}
		if ( is_admin() ) {
			return false;
		}
		$script = isset( $_SERVER['SCRIPT_NAME'] ) ? (string) $_SERVER['SCRIPT_NAME'] : '';
		return '' === $script || 'index.php' === basename( $script );
	}
}
