<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Ajax_Cache extends Tarh99_Perf_Module {

	const BUCKET = 'ajax';

	private $pending = null;
	private $config = null;

	public function id() {
		return 'ajax_cache';
	}

	public function group() {
		return 'server';
	}

	public function title() {
		return 'کش پاسخ‌های ایجکس عمومی';
	}

	public function description() {
		return 'پاسخ درخواست‌های ایجکس فقط‌خواندنی (جستجوی زنده قالب، اسلایدرهای Wooslider، کاروسل و نمایش سریع محصول) برای مهمان‌ها کش می‌شود؛ پاسخ کش‌شده دقیقاً همان خروجی قبلی است. با ذخیره/ویرایش محصول، تغییر موجودی، دسته‌بندی یا تنظیمات Wooslider کل کش خودکار باطل می‌شود. پاسخ‌های خطا و درخواست‌های دارای ترتیب تصادفی کش نمی‌شوند.';
	}

	public function fields() {
		return array(
			'actions'      => array(
				'type'    => 'textarea',
				'label'   => 'اکشن‌ها — هر خط: نام اکشن | مدت کش (ثانیه) | فیلد نانس:اکشن نانس (اختیاری) | گزینه‌ای که بررسی نانس را خاموش می‌کند (اختیاری)',
				'default' => "pooshe_ajax_search|900\npooshe_ajax_carousel|1800\npooshe_quick_view|1800\nwoosbymdz_fetchpro|1800|wwkxssjk:woosbymdz-uio2899nb-w144wkxs|woosbymdz_noajaxrefchecker\nwoosbymdz_fetchpro_infinite|1800|wwkxssjk:woosbymdz-uio2899nb-w144wkxs|woosbymdz_noajaxrefchecker",
			),
			'logged_in'    => array(
				'type'    => 'checkbox',
				'label'   => 'برای کاربران لاگین‌شده هم کش شود (یک نسخه مشترک برای همه کاربران لاگین؛ فقط اگر خروجی این اکشن‌ها به کاربر وابسته نیست)',
				'default' => 0,
			),
			'skip_rand'    => array(
				'type'    => 'checkbox',
				'label'   => 'درخواست‌هایی که ترتیب تصادفی (rand) دارند کش نشوند',
				'default' => 1,
			),
			'max_kb'       => array(
				'type'    => 'number',
				'label'   => 'حداکثر حجم هر پاسخ قابل کش (KB)',
				'default' => 512,
				'min'     => 16,
				'max'     => 4096,
			),
		);
	}

	public function boot() {
		foreach ( $this->config() as $action => $conf ) {
			add_action( 'wp_ajax_nopriv_' . $action, array( $this, 'serve' ), 0 );
			if ( $this->opt( 'logged_in' ) ) {
				add_action( 'wp_ajax_' . $action, array( $this, 'serve' ), 0 );
			}
		}
		$this->register_invalidation();
	}

	public function config() {
		if ( null !== $this->config ) {
			return $this->config;
		}
		$this->config = array();
		foreach ( $this->opt_lines( 'actions' ) as $line ) {
			$parts  = array_map( 'trim', explode( '|', $line ) );
			$action = isset( $parts[0] ) ? sanitize_key( $parts[0] ) : '';
			if ( '' === $action ) {
				continue;
			}
			$ttl          = isset( $parts[1] ) ? max( 60, min( DAY_IN_SECONDS, (int) $parts[1] ) ) : 900;
			$nonce_field  = '';
			$nonce_action = '';
			if ( ! empty( $parts[2] ) && false !== strpos( $parts[2], ':' ) ) {
				list( $nonce_field, $nonce_action ) = array_map( 'trim', explode( ':', $parts[2], 2 ) );
			}
			$this->config[ $action ] = array(
				'ttl'          => $ttl,
				'nonce_field'  => $nonce_field,
				'nonce_action' => $nonce_action,
				'bypass_opt'   => isset( $parts[3] ) ? sanitize_key( $parts[3] ) : '',
			);
		}
		return $this->config;
	}

	private function register_invalidation() {
		$bump = array( 'Tarh99_Perf_Cache_Store', 'bump' );
		$hooks = array(
			'save_post_product',
			'woocommerce_update_product',
			'woocommerce_new_product',
			'woocommerce_delete_product',
			'woocommerce_trash_product',
			'woocommerce_product_set_stock_status',
			'woocommerce_variation_set_stock_status',
			'woocommerce_product_set_visibility',
			'woocommerce_save_product_variation',
			'wc_after_products_starting_sales',
			'wc_after_products_ending_sales',
			'created_product_cat',
			'edited_product_cat',
			'delete_product_cat',
			'created_product_tag',
			'edited_product_tag',
			'delete_product_tag',
			'elementor/editor/after_save',
			'switch_theme',
			'activated_plugin',
			'deactivated_plugin',
		);
		foreach ( $hooks as $hook ) {
			add_action( $hook, $bump, 99 );
		}
		add_action( 'updated_option', array( $this, 'maybe_bump_option' ), 10, 1 );
		add_action( 'added_option', array( $this, 'maybe_bump_option' ), 10, 1 );
	}

	public function maybe_bump_option( $option ) {
		$option = (string) $option;
		if ( 0 === strpos( $option, 'woosbymdz_' ) || 0 === strpos( $option, 'woocommerce_currency' ) || 0 === strpos( $option, 'woocommerce_price_' ) || 0 === strpos( $option, 'woocommerce_tax_display' ) ) {
			Tarh99_Perf_Cache_Store::bump();
		}
	}

	private function normalize( $value ) {
		if ( is_array( $value ) ) {
			ksort( $value );
			foreach ( $value as $k => $v ) {
				$value[ $k ] = $this->normalize( $v );
			}
		}
		return $value;
	}

	private function request_params( $conf ) {
		$params = array_merge( (array) wp_unslash( $_GET ), (array) wp_unslash( $_POST ) );
		unset( $params['action'], $params['_wpnonce'], $params['_'], $params['security'], $params['nonce'] );
		if ( '' !== $conf['nonce_field'] ) {
			unset( $params[ $conf['nonce_field'] ] );
		}
		return $this->normalize( $params );
	}

	private function nonce_ok( $conf ) {
		if ( '' === $conf['nonce_field'] ) {
			return true;
		}
		if ( '' !== $conf['bypass_opt'] && '1' === (string) get_option( $conf['bypass_opt'] ) ) {
			return true;
		}
		$nonce = isset( $_REQUEST[ $conf['nonce_field'] ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $conf['nonce_field'] ] ) ) : '';
		return '' !== $nonce && false !== wp_verify_nonce( $nonce, $conf['nonce_action'] );
	}

	private function cache_key( $action, $params, $logged_in ) {
		$parts = array(
			$action,
			wp_json_encode( $params ),
			(string) get_option( 'WPLANG', '' ),
			(string) get_option( 'woocommerce_currency', '' ),
			$logged_in ? 'user' : 'guest',
			empty( $_COOKIE['woocommerce_items_in_cart'] ) ? 'empty' : 'cart',
		);
		return implode( '|', $parts );
	}

	private function has_login_cookie() {
		foreach ( array_keys( (array) $_COOKIE ) as $name ) {
			if ( 0 === strpos( (string) $name, 'wordpress_logged_in_' ) ) {
				return true;
			}
		}
		return false;
	}

	private function cacheable_params( $conf ) {
		$params = $this->request_params( $conf );
		$json   = (string) wp_json_encode( $params );
		if ( strlen( $json ) > 20000 ) {
			return null;
		}
		if ( $this->opt( 'skip_rand' ) && preg_match( '/(^|[^a-z])rand([^a-z]|$)/i', $json ) ) {
			return null;
		}
		return $params;
	}

	private function send_cached( $cached, $early ) {
		if ( ! is_array( $cached ) || ! isset( $cached['body'] ) || ! is_string( $cached['body'] ) ) {
			return false;
		}
		if ( ! headers_sent() ) {
			header( 'Content-Type: ' . ( ! empty( $cached['type'] ) ? $cached['type'] : 'text/html; charset=' . get_option( 'blog_charset' ) ) );
			header( 'X-Tarh99-Ajax-Cache: ' . ( $early ? 'HIT-EARLY' : 'HIT' ) );
			if ( $early ) {
				header( 'X-Content-Type-Options: nosniff' );
				header( 'X-Robots-Tag: noindex' );
				header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
				header( 'Cache-Control: no-cache, must-revalidate, max-age=0, no-store, private' );
			}
		}
		echo $cached['body'];
		return true;
	}

	public function early_serve() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || empty( $_REQUEST['action'] ) || ! is_string( $_REQUEST['action'] ) ) {
			return;
		}
		if ( isset( $_GET['tarh99_perf'] ) || $this->has_login_cookie() ) {
			return;
		}
		$action = sanitize_key( wp_unslash( $_REQUEST['action'] ) );
		$config = $this->config();
		if ( ! isset( $config[ $action ] ) ) {
			return;
		}
		$conf = $config[ $action ];
		if ( '' !== $conf['nonce_field'] && ( '' === $conf['bypass_opt'] || '1' !== (string) get_option( $conf['bypass_opt'] ) ) ) {
			return;
		}
		$params = $this->cacheable_params( $conf );
		if ( null === $params ) {
			return;
		}
		$cached = Tarh99_Perf_Cache_Store::get( self::BUCKET, $this->cache_key( $action, $params, false ), $conf['ttl'] );
		if ( $this->send_cached( $cached, true ) ) {
			exit;
		}
	}

	public function serve() {
		if ( Tarh99_Perf_Env::bypass() || ! wp_doing_ajax() ) {
			return;
		}
		$hook   = current_action();
		$action = (string) preg_replace( '/^wp_ajax_(nopriv_)?/', '', (string) $hook );
		$config = $this->config();
		if ( ! isset( $config[ $action ] ) ) {
			return;
		}
		$conf = $config[ $action ];
		if ( is_user_logged_in() && ! $this->opt( 'logged_in' ) ) {
			return;
		}
		$params = $this->cacheable_params( $conf );
		if ( null === $params || ! $this->nonce_ok( $conf ) ) {
			return;
		}
		$key    = $this->cache_key( $action, $params, is_user_logged_in() );
		$cached = Tarh99_Perf_Cache_Store::get( self::BUCKET, $key, $conf['ttl'] );
		if ( $this->send_cached( $cached, false ) ) {
			exit;
		}
		$this->pending = array(
			'key'   => $key,
			'ttl'   => $conf['ttl'],
			'level' => ob_get_level() + 1,
		);
		ob_start();
		add_action( 'shutdown', array( $this, 'store' ), 0 );
	}

	private function response_type() {
		foreach ( headers_list() as $header ) {
			if ( 0 === stripos( $header, 'Content-Type:' ) ) {
				return trim( substr( $header, 13 ) );
			}
		}
		return '';
	}

	public function store() {
		if ( null === $this->pending ) {
			return;
		}
		$pending       = $this->pending;
		$this->pending = null;
		if ( ob_get_level() !== $pending['level'] ) {
			return;
		}
		$body = ob_get_contents();
		if ( ! is_string( $body ) || '' === $body ) {
			return;
		}
		$status = function_exists( 'http_response_code' ) ? (int) http_response_code() : 200;
		if ( 200 !== $status && 0 !== $status ) {
			return;
		}
		if ( strlen( $body ) > (int) $this->opt( 'max_kb' ) * 1024 ) {
			return;
		}
		$type    = $this->response_type();
		$trimmed = trim( $body );
		if ( in_array( $trimmed, array( '', '0', '-1' ), true ) ) {
			return;
		}
		if ( false !== stripos( $type, 'json' ) || in_array( substr( $trimmed, 0, 1 ), array( '{', '[' ), true ) ) {
			$decoded = json_decode( $trimmed, true );
			if ( null === $decoded ) {
				return;
			}
			if ( is_array( $decoded ) && ( ( array_key_exists( 'success', $decoded ) && true !== $decoded['success'] ) || ( array_key_exists( 'status', $decoded ) && false === $decoded['status'] ) ) ) {
				return;
			}
		} elseif ( '' !== $type && false === stripos( $type, 'text/html' ) ) {
			return;
		}
		Tarh99_Perf_Cache_Store::set(
			self::BUCKET,
			$pending['key'],
			array(
				'body' => $body,
				'type' => $type,
				'time' => time(),
			),
			$pending['ttl']
		);
	}
}
