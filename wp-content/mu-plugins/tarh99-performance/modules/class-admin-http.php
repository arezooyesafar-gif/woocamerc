<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Admin_Http extends Tarh99_Perf_Module {

	const LOG_OPTION = 'tarh99_perf_http_log';

	private $starts = array();

	public function id() {
		return 'admin_http';
	}

	public function group() {
		return 'server';
	}

	public function title() {
		return 'درخواست‌های بیرونی کند و محافظ فرانت‌اند';
	}

	public function description() {
		return 'زمان درخواست‌های HTTP بیرونی (چک لایسنس و آپدیت افزونه‌ها، API ها) اندازه‌گیری و موارد کند در تب «ابزارها» نمایش داده می‌شوند (بدون پارامترهای آدرس که ممکن است شامل کلید باشند). محافظ فرانت‌اند جلوی درخواست‌های بیرونی‌ای را که هیچ‌وقت نباید بازدیدکننده را منتظر بگذارند می‌گیرد. اختیاری: سقف زمان انتظار هنگام باز کردن صفحات پیشخوان، و مسدودکردن دامنه‌هایی که از ایران جواب نمی‌دهند فقط در صفحات پیشخوان (کران دست‌نخورده می‌ماند).';
	}

	public function fields() {
		return array(
			'log'         => array(
				'type'    => 'checkbox',
				'label'   => 'ثبت درخواست‌های کند',
				'default' => 1,
			),
			'threshold'   => array(
				'type'    => 'number',
				'label'   => 'حد کند بودن (ثانیه)',
				'default' => 2,
				'min'     => 0.5,
				'max'     => 30,
				'step'    => 0.5,
			),
			'timeout_cap' => array(
				'type'    => 'number',
				'label'   => 'حداکثر زمان انتظار درخواست بیرونی هنگام باز کردن صفحات پیشخوان (ثانیه، ۰ = بدون تغییر)',
				'default' => 0,
				'min'     => 0,
				'max'     => 30,
			),
			'front_guard' => array(
				'type'    => 'textarea',
				'label'   => 'دامنه‌هایی که هرگز نباید بازدیدکننده سایت را منتظر بگذارند (در فرانت‌اند و ایجکس مهمان مسدود می‌شوند؛ پیشخوان و کران آزادند)',
				'default' => 'files.rtl-theme.com',
				'help'    => 'اگر افزونه مدیریت لایسنس راست‌چین (RTL-CareUnit) غیرفعال یا خراب شود، فایل‌های لایسنس قالب و افزونه‌های راست‌چین در هر بازدید تلاش می‌کنند rtlCareUnit.zip را با زمان انتظار ۳۰۰ ثانیه دانلود کنند. این خط جلوی قفل شدن صفحات را می‌گیرد.',
			),
			'block_hosts' => array(
				'type'    => 'textarea',
				'label'   => 'دامنه‌هایی که در صفحات پیشخوان مسدود شوند (هر خط یک دامنه؛ کران و ایجکس مسدود نمی‌شوند)',
				'default' => '',
			),
		);
	}

	public function boot() {
		add_filter( 'pre_http_request', array( $this, 'pre' ), PHP_INT_MAX, 3 );
		add_action( 'http_api_debug', array( $this, 'after' ), 10, 5 );
		if ( (int) $this->opt( 'timeout_cap' ) > 0 ) {
			add_filter( 'http_request_args', array( $this, 'cap_timeout' ), PHP_INT_MAX, 2 );
		}
	}

	private function admin_page_load() {
		return is_admin() && ! wp_doing_ajax() && ! wp_doing_cron();
	}

	private function visitor_request() {
		if ( wp_doing_cron() || Tarh99_Perf_Env::is_cli() ) {
			return false;
		}
		if ( wp_doing_ajax() ) {
			return ! is_user_logged_in();
		}
		return ! is_admin() && ! Tarh99_Perf_Env::is_rest();
	}

	private function host_listed( $host, $field ) {
		foreach ( $this->opt_lines( $field ) as $listed ) {
			$listed = strtolower( trim( $listed ) );
			if ( '' !== $listed && ( $host === $listed || substr( $host, -strlen( '.' . $listed ) ) === '.' . $listed ) ) {
				return true;
			}
		}
		return false;
	}

	private function is_local( $host ) {
		$home = (string) wp_parse_url( home_url(), PHP_URL_HOST );
		return '' === $host || $host === $home || 'localhost' === $host || '127.0.0.1' === $host;
	}

	public function pre( $preempt, $args, $url ) {
		if ( false !== $preempt ) {
			return $preempt;
		}
		$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
		if ( $this->visitor_request() && ! $this->is_local( $host ) && $this->host_listed( $host, 'front_guard' ) ) {
			return new WP_Error( 'tarh99_perf_front_guard', 'Blocked during visitor request by Tarh99 Performance' );
		}
		if ( $this->admin_page_load() && ! $this->is_local( $host ) && $this->host_listed( $host, 'block_hosts' ) ) {
			return new WP_Error( 'tarh99_perf_blocked', 'Blocked during admin page load by Tarh99 Performance' );
		}
		if ( $this->opt( 'log' ) ) {
			$this->starts[ $url ][] = microtime( true );
		}
		return $preempt;
	}

	public function cap_timeout( $args, $url ) {
		if ( ! $this->admin_page_load() || ! is_array( $args ) ) {
			return $args;
		}
		$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
		if ( $this->is_local( $host ) ) {
			return $args;
		}
		$cap = (int) $this->opt( 'timeout_cap' );
		if ( ! isset( $args['timeout'] ) || (float) $args['timeout'] > $cap ) {
			$args['timeout'] = $cap;
		}
		return $args;
	}

	public function after( $response, $context, $class, $args, $url ) {
		if ( 'response' !== $context || empty( $this->starts[ $url ] ) ) {
			return;
		}
		$start   = array_pop( $this->starts[ $url ] );
		$elapsed = microtime( true ) - $start;
		if ( $elapsed < (float) $this->opt( 'threshold' ) ) {
			return;
		}
		$parts  = wp_parse_url( $url );
		$result = is_wp_error( $response ) ? 'error: ' . $response->get_error_message() : (string) wp_remote_retrieve_response_code( $response );
		$entry  = array(
			'time'    => current_time( 'mysql' ),
			'host'    => isset( $parts['host'] ) ? $parts['host'] : '',
			'path'    => isset( $parts['path'] ) ? substr( $parts['path'], 0, 120 ) : '',
			'seconds' => round( $elapsed, 2 ),
			'context' => Tarh99_Perf_Env::current_context(),
			'page'    => isset( $_SERVER['REQUEST_URI'] ) ? substr( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ), 0, 120 ) : '',
			'result'  => substr( sanitize_text_field( $result ), 0, 120 ),
		);
		$log = get_option( self::LOG_OPTION, array() );
		$log = is_array( $log ) ? $log : array();
		array_unshift( $log, $entry );
		update_option( self::LOG_OPTION, array_slice( $log, 0, 60 ), false );
	}
}
