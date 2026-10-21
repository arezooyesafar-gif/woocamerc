<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Asset_Rules extends Tarh99_Perf_Module {

	const SCAN_PREFIX = 'tarh99_perf_scan_';

	private $scan = false;
	private $parsed = null;

	public function id() {
		return 'asset_rules';
	}

	public function title() {
		return 'لود شرطی CSS/JS';
	}

	public function description() {
		return 'با قوانین ساده مشخص کنید هر فایل CSS/JS افزونه یا قالب فقط در کدام صفحات لود شود. فایل‌های حیاتی (jQuery، المنتور، ووکامرس، پرداخت) و هر فایلی که فایل دیگری به آن وابسته است هرگز حذف نمی‌شوند. در سبد، تسویه و حساب کاربری هیچ قانونی اجرا نمی‌شود. برای پیدا کردن نام فایل‌ها از «اسکن منابع این صفحه» در نوار مدیریت استفاده کنید.';
	}

	public function risk() {
		return 'medium';
	}

	public function fields() {
		return array(
			'rules' => array(
				'type'    => 'textarea',
				'label'   => 'قوانین — هر خط: محدوده | css یا js | handle',
				'default' => '',
				'help'    => 'محدوده‌ها با کاما و ! برای نفی: all, front, shop, product, product_cat, product_archive, woo, not_woo, page, post, search, archive, page:ID, url:/path. مثال: !product | css | eshterakyar-content-paywall  یعنی این CSS را در همه صفحات به‌جز صفحه محصول حذف کن.',
			),
			'scan'  => array(
				'type'    => 'checkbox',
				'label'   => 'نمایش «اسکن منابع این صفحه» در نوار مدیریت برای مدیر',
				'default' => 1,
			),
		);
	}

	public function boot() {
		add_action( 'wp_enqueue_scripts', array( $this, 'apply_js' ), 9999 );
		add_action( 'wp_print_styles', array( $this, 'apply_css' ), 999 );
		add_action( 'wp_print_footer_scripts', array( $this, 'apply_late' ), 1 );
		if ( $this->opt( 'scan' ) ) {
			add_action( 'admin_bar_menu', array( $this, 'admin_bar' ), 120 );
			add_action( 'template_redirect', array( $this, 'maybe_start_scan' ), 0 );
		}
	}

	private function rules() {
		if ( null !== $this->parsed ) {
			return $this->parsed;
		}
		$this->parsed = array();
		foreach ( $this->opt_lines( 'rules' ) as $line ) {
			$parts = array_map( 'trim', explode( '|', $line ) );
			if ( 3 !== count( $parts ) ) {
				continue;
			}
			$type = strtolower( $parts[1] );
			if ( ! in_array( $type, array( 'css', 'js' ), true ) || ! preg_match( '/^[A-Za-z0-9_\-.\/]{1,120}$/', $parts[2] ) ) {
				continue;
			}
			$this->parsed[] = array(
				'scope'  => $parts[0],
				'type'   => $type,
				'handle' => $parts[2],
			);
			if ( count( $this->parsed ) >= 150 ) {
				break;
			}
		}
		return $this->parsed;
	}

	private function page_allowed() {
		if ( ! Tarh99_Perf_Env::front_ok() ) {
			return false;
		}
		if ( Tarh99_Perf_Env::wc() && ( is_cart() || is_checkout() || is_account_page() ) ) {
			return false;
		}
		return true;
	}

	private function run( $type ) {
		if ( empty( $this->rules() ) || ! $this->page_allowed() ) {
			return;
		}
		foreach ( $this->rules() as $rule ) {
			if ( $rule['type'] !== $type || ! Tarh99_Perf_Env::scope_matches( $rule['scope'] ) ) {
				continue;
			}
			Tarh99_Perf_Assets::safe_dequeue( $type, $rule['handle'] );
		}
	}

	public function apply_js() {
		$this->run( 'js' );
		$this->run( 'css' );
	}

	public function apply_css() {
		$this->run( 'css' );
	}

	public function apply_late() {
		$this->run( 'js' );
		$this->run( 'css' );
	}

	public function admin_bar( $bar ) {
		if ( is_admin() || ! current_user_can( 'manage_options' ) || ! Tarh99_Perf_Env::is_get() ) {
			return;
		}
		$url = add_query_arg(
			array(
				'tarh99_scan' => '1',
				'_wpnonce'    => wp_create_nonce( 'tarh99_scan' ),
			)
		);
		$bar->add_node(
			array(
				'id'    => 'tarh99-perf-scan',
				'title' => 'سرعت طرح۹۹: اسکن منابع این صفحه',
				'href'  => $url,
			)
		);
	}

	public function maybe_start_scan() {
		if ( ! isset( $_GET['tarh99_scan'], $_GET['_wpnonce'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'tarh99_scan' ) ) {
			return;
		}
		$this->scan = true;
		add_action( 'wp_footer', array( $this, 'save_scan' ), PHP_INT_MAX );
	}

	private function asset_size( $src ) {
		$src = (string) $src;
		if ( '' === $src ) {
			return 0;
		}
		$path    = (string) wp_parse_url( $src, PHP_URL_PATH );
		$content = (string) wp_parse_url( content_url(), PHP_URL_PATH );
		$include = (string) wp_parse_url( includes_url(), PHP_URL_PATH );
		if ( '' !== $content && 0 === strpos( $path, $content ) ) {
			$file = WP_CONTENT_DIR . substr( $path, strlen( $content ) );
		} elseif ( '' !== $include && 0 === strpos( $path, $include ) ) {
			$file = ABSPATH . WPINC . substr( $path, strlen( $include ) );
		} elseif ( 0 === strpos( $src, '/wp-includes/' ) ) {
			$file = ABSPATH . ltrim( $src, '/' );
		} else {
			return 0;
		}
		$file = rawurldecode( $file );
		return is_file( $file ) ? (int) filesize( $file ) : 0;
	}

	public function save_scan() {
		if ( ! $this->scan ) {
			return;
		}
		$uri  = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( remove_query_arg( array( 'tarh99_scan', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';
		$data = array(
			'date'    => current_time( 'mysql' ),
			'page'    => $uri,
			'styles'  => array(),
			'scripts' => array(),
			'removed' => Tarh99_Perf_Assets::removed(),
		);
		foreach ( array(
			'styles'  => wp_styles(),
			'scripts' => wp_scripts(),
		) as $type => $registry ) {
			$handles = array_unique( array_merge( (array) $registry->done, (array) $registry->queue ) );
			foreach ( array_slice( $handles, 0, 300 ) as $handle ) {
				if ( ! isset( $registry->registered[ $handle ] ) ) {
					continue;
				}
				$dep                    = $registry->registered[ $handle ];
				$data[ $type ][ $handle ] = array(
					'src'      => is_string( $dep->src ) ? $dep->src : '',
					'deps'     => implode( ', ', array_slice( (array) $dep->deps, 0, 12 ) ),
					'size'     => $this->asset_size( is_string( $dep->src ) ? $dep->src : '' ),
					'footer'   => ! empty( $dep->extra['group'] ),
					'strategy' => isset( $dep->extra['strategy'] ) ? (string) $dep->extra['strategy'] : '',
				);
			}
		}
		set_transient( self::SCAN_PREFIX . get_current_user_id(), $data, DAY_IN_SECONDS );
	}
}
