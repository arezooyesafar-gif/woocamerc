<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Tarh99_Perf_Env {

	private static $bypass = null;
	private static $tps = null;
	private static $bot = null;

	public static function is_rest() {
		return defined( 'REST_REQUEST' ) && REST_REQUEST;
	}

	public static function is_cli() {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	public static function is_frontend() {
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || self::is_rest() || self::is_cli() ) {
			return false;
		}
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
			return false;
		}
		if ( defined( 'IFRAME_REQUEST' ) && IFRAME_REQUEST ) {
			return false;
		}
		return true;
	}

	public static function is_builder_preview() {
		if ( isset( $_GET['elementor-preview'] ) || isset( $_GET['elementor_library'] ) || isset( $_GET['ct_builder'] ) ) {
			return true;
		}
		if ( isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) {
			return true;
		}
		if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) {
			return true;
		}
		return false;
	}

	public static function bypass() {
		if ( null !== self::$bypass ) {
			return self::$bypass;
		}
		$requested = isset( $_GET['tarh99_perf'] ) && 'off' === sanitize_key( wp_unslash( $_GET['tarh99_perf'] ) );
		if ( ! $requested ) {
			self::$bypass = false;
			return false;
		}
		if ( ! did_action( 'init' ) ) {
			return false;
		}
		self::$bypass = is_user_logged_in() && current_user_can( 'manage_options' );
		return self::$bypass;
	}

	public static function front_ok() {
		if ( ! self::is_frontend() || self::is_builder_preview() || self::bypass() ) {
			return false;
		}
		if ( did_action( 'wp' ) && ( is_feed() || is_robots() || is_trackback() || is_embed() ) ) {
			return false;
		}
		return true;
	}

	public static function is_get() {
		return isset( $_SERVER['REQUEST_METHOD'] ) && 'GET' === strtoupper( (string) $_SERVER['REQUEST_METHOD'] );
	}

	public static function is_pooshe() {
		return 'pooshe' === get_template();
	}

	public static function wc() {
		return function_exists( 'WC' ) && class_exists( 'WooCommerce' );
	}

	public static function rocket_active() {
		return defined( 'WP_ROCKET_VERSION' );
	}

	public static function rocket_option( $key ) {
		if ( ! self::rocket_active() ) {
			return null;
		}
		if ( function_exists( 'get_rocket_option' ) ) {
			return get_rocket_option( $key );
		}
		$options = get_option( 'wp_rocket_settings' );
		return ( is_array( $options ) && isset( $options[ $key ] ) ) ? $options[ $key ] : null;
	}

	public static function elementor_image_optimization() {
		return class_exists( '\\Elementor\\Modules\\ImageLoadingOptimization\\Module' ) && '1' === (string) get_option( 'elementor_optimized_image_loading', '1' );
	}

	public static function tps_active() {
		return class_exists( 'TPS_Plugin' );
	}

	public static function tps( $key ) {
		if ( ! self::tps_active() ) {
			return false;
		}
		if ( null === self::$tps ) {
			$stored    = get_option( 'tps_settings', array() );
			$stored    = is_array( $stored ) ? $stored : array();
			$defaults  = method_exists( 'TPS_Plugin', 'defaults' ) ? TPS_Plugin::defaults() : array();
			self::$tps = wp_parse_args( $stored, $defaults );
		}
		if ( empty( self::$tps['enabled'] ) ) {
			return false;
		}
		return ! empty( self::$tps[ $key ] );
	}

	public static function is_bot() {
		if ( null !== self::$bot ) {
			return self::$bot;
		}
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
		if ( '' === trim( $ua ) ) {
			self::$bot = true;
			return true;
		}
		self::$bot = (bool) preg_match( '/bot\b|bot\/|crawl|spider|slurp|facebookexternalhit|bingpreview|headless|python-requests|python-urllib|curl\/|wget|semrush|ahrefs|mj12|dotbot|petalbot|yandex|baidu|bytespider|gptbot|claudebot|ccbot|amazonbot|applebot|dataforseo|serpstat|go-http-client|java\/|okhttp|axios|scrapy|httpclient|libwww|feedfetcher|mediapartners|adsbot|seokicks|blexbot|megaindex|barkrowler/i', $ua );
		return self::$bot;
	}

	public static function parse_lines( $text ) {
		$out = array();
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
			$line = trim( $line );
			if ( '' === $line || 0 === strpos( $line, '#' ) ) {
				continue;
			}
			$out[] = $line;
		}
		return $out;
	}

	public static function scope_matches( $scope_text ) {
		$tokens   = array_filter( array_map( 'trim', explode( ',', (string) $scope_text ) ) );
		$positive = array();
		$negative = array();
		foreach ( $tokens as $token ) {
			if ( 0 === strpos( $token, '!' ) ) {
				$negative[] = substr( $token, 1 );
			} else {
				$positive[] = $token;
			}
		}
		foreach ( $negative as $token ) {
			if ( self::token_matches( $token ) ) {
				return false;
			}
		}
		if ( empty( $positive ) ) {
			return ! empty( $negative );
		}
		foreach ( $positive as $token ) {
			if ( self::token_matches( $token ) ) {
				return true;
			}
		}
		return false;
	}

	public static function is_woo_page() {
		if ( ! self::wc() ) {
			return false;
		}
		return is_woocommerce() || is_cart() || is_checkout() || is_account_page();
	}

	public static function token_matches( $token ) {
		$token = strtolower( trim( (string) $token ) );
		$wc    = self::wc();
		if ( 0 === strpos( $token, 'page:' ) ) {
			$value = substr( $token, 5 );
			if ( ctype_digit( $value ) ) {
				return is_page( (int) $value ) || ( is_singular() && (int) get_queried_object_id() === (int) $value );
			}
			return is_page( $value );
		}
		if ( 0 === strpos( $token, 'url:' ) ) {
			$path = '/' . ltrim( substr( $token, 4 ), '/' );
			$uri  = isset( $_SERVER['REQUEST_URI'] ) ? rawurldecode( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) ) : '';
			return '' !== $uri && 0 === strpos( strtolower( $uri ), $path );
		}
		switch ( $token ) {
			case 'all':
				return true;
			case 'front':
				return is_front_page();
			case 'blog':
				return is_home();
			case 'shop':
				return $wc && is_shop();
			case 'product':
				return $wc && is_product();
			case 'product_cat':
				return $wc && is_product_category();
			case 'product_tag':
				return $wc && is_product_tag();
			case 'product_archive':
				return $wc && ( is_shop() || is_product_taxonomy() );
			case 'cart':
				return $wc && is_cart();
			case 'checkout':
				return $wc && is_checkout();
			case 'account':
				return $wc && is_account_page();
			case 'woo':
				return self::is_woo_page();
			case 'not_woo':
				return ! self::is_woo_page();
			case 'page':
				return is_page() && ! is_front_page();
			case 'post':
				return is_singular( 'post' );
			case 'search':
				return is_search();
			case 'archive':
				return is_archive();
			case '404':
				return is_404();
		}
		return false;
	}

	public static function current_context() {
		if ( wp_doing_cron() ) {
			return 'cron';
		}
		if ( wp_doing_ajax() ) {
			return 'ajax';
		}
		if ( self::is_rest() ) {
			return 'rest';
		}
		if ( is_admin() ) {
			return 'admin';
		}
		if ( self::is_cli() ) {
			return 'cli';
		}
		return 'front';
	}
}
