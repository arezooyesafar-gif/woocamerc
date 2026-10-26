<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Login_Warmup extends Tarh99_Perf_Module {

	const HEADER = 'X-Tarh99-Warmup';

	private $cookie = '';

	public function id() {
		return 'login_warmup';
	}

	public function group() {
		return 'server';
	}

	public function title() {
		return 'آماده شدن صفحه‌های اصلی بلافاصله بعد از ورود کاربر';
	}

	public function description() {
		return 'راکت برای کاربر لاگین از هر صفحه یک نسخه جدا مخصوص همان کاربر نگه می‌دارد؛ پس هر صفحه‌ای که کاربر بار اول باز می‌کند کامل روی سرور ساخته می‌شود (۴ تا ۵ ثانیه) و از بار دوم سریع است. این ماژول بلافاصله بعد از ورود موفق (فرم uPanel، ووکامرس یا وردپرس) در پس‌زمینه همان نسخه مخصوص کاربر را از صفحه‌های فهرست زیر می‌سازد، با همان مرورگر (موبایل یا دسکتاپ) کاربر. وقتی کاربر بعد از ورود به این صفحه‌ها می‌رود، از کش باز می‌شوند. پاسخ ورود منتظر این کار نمی‌ماند. فقط وقتی کار می‌کند که در راکت «کش برای کاربران لاگین» روشن باشد.';
	}

	public function fields() {
		return array(
			'paths' => array(
				'type'    => 'textarea',
				'label'   => 'صفحه‌هایی که بعد از ورود آماده شوند (هر خط یک مسیر یا آدرس کامل از همین سایت؛ حداکثر ۶)',
				'default' => "/\n/shop/",
			),
		);
	}

	public function notes() {
		if ( ! Tarh99_Perf_Env::rocket_active() ) {
			return 'WP Rocket فعال نیست؛ این ماژول کاری نمی‌کند.';
		}
		return Tarh99_Perf_Env::rocket_option( 'cache_logged_user' ) ? 'کش راکت برای کاربران لاگین روشن است.' : 'در راکت «کش برای کاربران لاگین» خاموش است؛ این ماژول کاری نمی‌کند.';
	}

	public function boot() {
		if ( self::verify_request() ) {
			ignore_user_abort( true );
			add_filter( 'add_user_metadata', array( $this, 'keep_cart_meta' ), 10, 3 );
			add_filter( 'update_user_metadata', array( $this, 'keep_cart_meta' ), 10, 3 );
			add_filter( 'delete_user_metadata', array( $this, 'keep_cart_meta' ), 10, 3 );
			add_action( 'shutdown', array( $this, 'keep_session' ), 19 );
		}
		add_action( 'set_logged_in_cookie', array( $this, 'remember' ), 10, 5 );
		add_action( 'shutdown', array( $this, 'fire' ), 1000 );
	}

	public function keep_cart_meta( $check, $object_id, $meta_key ) {
		$meta_key = (string) $meta_key;
		return ( '_woocommerce_load_saved_cart_after_login' === $meta_key || 0 === strpos( $meta_key, '_woocommerce_persistent_cart_' ) ) ? true : $check;
	}

	public function keep_session() {
		if ( function_exists( 'WC' ) && WC()->session ) {
			remove_action( 'shutdown', array( WC()->session, 'save_data' ), 20 );
		}
	}

	public static function verify_request() {
		if ( empty( $_SERVER['HTTP_X_TARH99_WARMUP'] ) || ! is_string( $_SERVER['HTTP_X_TARH99_WARMUP'] ) ) {
			return false;
		}
		$parts = explode( '.', (string) $_SERVER['HTTP_X_TARH99_WARMUP'], 2 );
		if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) || abs( time() - (int) $parts[0] ) > 300 ) {
			return false;
		}
		foreach ( $_COOKIE as $name => $value ) {
			if ( 0 === strpos( (string) $name, 'wordpress_logged_in_' ) && is_string( $value ) && hash_equals( self::sign( (int) $parts[0], $value ), $parts[1] ) ) {
				return true;
			}
		}
		return false;
	}

	private static function sign( $time, $cookie ) {
		return hash_hmac( 'sha256', $time . '|' . $cookie, wp_salt( 'nonce' ) );
	}

	public function remember( $cookie, $expire, $expiration, $user_id, $scheme = 'logged_in' ) {
		if ( 'logged_in' === $scheme && is_string( $cookie ) && '' !== $cookie ) {
			$this->cookie = $cookie;
		}
	}

	private function urls() {
		$urls = array();
		$host = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );
		foreach ( $this->opt_lines( 'paths' ) as $line ) {
			$url = 0 === strpos( $line, 'http' ) ? $line : home_url( '/' . ltrim( $line, '/' ) );
			if ( strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) ) === $host && ! in_array( $url, $urls, true ) ) {
				$urls[] = $url;
			}
			if ( count( $urls ) >= 6 ) {
				break;
			}
		}
		return $urls;
	}

	public function fire() {
		if ( '' === $this->cookie || ! defined( 'LOGGED_IN_COOKIE' ) || ! Tarh99_Perf_Env::rocket_active() || ! Tarh99_Perf_Env::rocket_option( 'cache_logged_user' ) ) {
			return;
		}
		$cookie       = $this->cookie;
		$this->cookie = '';
		$agent        = isset( $_SERVER['HTTP_USER_AGENT'] ) ? (string) wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : '';
		if ( '' === $agent || Tarh99_Perf_Env::is_bot() || ! empty( $_COOKIE['woocommerce_items_in_cart'] ) ) {
			return;
		}
		$urls = $this->urls();
		if ( ! $urls ) {
			return;
		}
		$time    = time();
		$headers = array(
			self::HEADER => $time . '.' . self::sign( $time, $cookie ),
			'Accept'     => 'text/html,application/xhtml+xml',
		);
		if ( function_exists( 'fastcgi_finish_request' ) || function_exists( 'litespeed_finish_request' ) ) {
			ignore_user_abort( true );
			if ( function_exists( 'fastcgi_finish_request' ) ) {
				fastcgi_finish_request();
			} else {
				litespeed_finish_request();
			}
			foreach ( $urls as $url ) {
				wp_remote_get(
					$url,
					array(
						'timeout'     => 25,
						'redirection' => 0,
						'sslverify'   => apply_filters( 'https_local_ssl_verify', false ),
						'user-agent'  => $agent,
						'cookies'     => array( LOGGED_IN_COOKIE => $cookie ),
						'headers'     => $headers,
					)
				);
			}
			return;
		}
		$headers['User-Agent'] = $agent;
		$headers['Cookie']     = LOGGED_IN_COOKIE . '=' . $cookie;
		foreach ( $urls as $url ) {
			$this->send( $url, $headers );
		}
	}

	private function send( $url, $headers ) {
		$parts = wp_parse_url( $url );
		if ( empty( $parts['host'] ) ) {
			return;
		}
		$https   = isset( $parts['scheme'] ) && 'https' === strtolower( $parts['scheme'] );
		$port    = isset( $parts['port'] ) ? (int) $parts['port'] : ( $https ? 443 : 80 );
		$context = stream_context_create(
			array(
				'ssl' => array(
					'verify_peer'      => false,
					'verify_peer_name' => false,
					'peer_name'        => $parts['host'],
				),
			)
		);
		$socket  = @stream_socket_client( ( $https ? 'ssl://' : 'tcp://' ) . $parts['host'] . ':' . $port, $errno, $errstr, 2, STREAM_CLIENT_CONNECT, $context );
		if ( ! $socket ) {
			return;
		}
		$host    = $parts['host'] . ( isset( $parts['port'] ) ? ':' . $port : '' );
		$request = 'GET ' . ( isset( $parts['path'] ) ? $parts['path'] : '/' ) . ( isset( $parts['query'] ) ? '?' . $parts['query'] : '' ) . " HTTP/1.1\r\nHost: " . $host . "\r\nConnection: close\r\n";
		foreach ( $headers as $name => $value ) {
			$request .= $name . ': ' . str_replace( array( "\r", "\n" ), '', (string) $value ) . "\r\n";
		}
		fwrite( $socket, $request . "\r\n" );
		fclose( $socket );
	}
}
