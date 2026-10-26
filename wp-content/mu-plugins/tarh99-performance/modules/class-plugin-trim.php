<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Plugin_Trim extends Tarh99_Perf_Module {

	const DIAG  = 'tarh99_perf_trim_diag';
	const TOKEN = 'tarh99_perf_trim_token';

	private $protected = array( 'woocommerce', 'RTL-CareUnit', 'elementor', 'elementor-pro', 'uPanel', 'eshterakyar', 'zarinpal-woocommerce-payment-gateway', 'wp-mail-smtp', 'wp-rocket', 'tarh99-performance-suite' );
	private $dirs = array();
	private $trimmed = array();
	private $marks = array();
	private $includes = array();
	private $last = 0.0;
	private $diag_trim = false;

	public function id() {
		return 'plugin_trim';
	}

	public function group() {
		return 'server';
	}

	public function title() {
		return 'بارگذاری نکردن افزونه‌های غیرلازم در ایجکس سبد خرید، ثبت سفارش و بازگشت از درگاه';
	}

	public function description() {
		return 'هر درخواست سبد خرید و پرداخت (افزودن به سبد، به‌روزرسانی سبد و صفحه پرداخت با ایجکس، ثبت سفارش و بازگشت از زرین‌پال) از کش راکت عبور نمی‌کند و همه افزونه‌ها برایش بارگذاری می‌شوند. RTL-CareUnit برای هر محصول فعال راست‌چین (۹ محصول روی سایت) لایسنس را رمزگشایی می‌کند (هر کدام حدود ۰٫۲ ثانیه روی این هاست). این ماژول فقط برای همین درخواست‌ها، افزونه‌های فهرست زیر را بارگذاری نمی‌کند (پیش‌فرض: پاکسازی دیتابیس، Yoast پرمیوم، Yoast ووکامرس و اسلایدر ووکامرس؛ هیچ‌کدام در این درخواست‌ها کاری ندارند). فهرست افزونه‌های فعال در دیتابیس هرگز تغییر نمی‌کند و در این درخواست‌ها هم اجازه ذخیره شدن ندارد. افزونه‌های حیاتی (ووکامرس، RTL-CareUnit، المنتور، uPanel، اشتراک‌یار، درگاه زرین‌پال، ارسال ایمیل، راکت) حتی اگر در فهرست نوشته شوند حذف نمی‌شوند. اگر افزودن به سبد با فرم خطا بدهد (مثلاً ناموجود شدن محصول)، صفحه با همه افزونه‌ها دوباره باز می‌شود و پیام خطا نمایش داده می‌شود. قبل از روشن کردن، از تب ابزارها تست زمان را اجرا کنید.';
	}

	public function default_enabled() {
		return false;
	}

	public function risk() {
		return 'medium';
	}

	public function fields() {
		return array(
			'plugins'  => array(
				'type'    => 'textarea',
				'label'   => 'نام پوشه افزونه‌هایی که در این درخواست‌ها بارگذاری نشوند (هر خط یک پوشه)',
				'default' => "advanced-database-cleaner-premium\nwordpress-seo-premium\nwpseo-woocommerce\nwooslider-bymdez",
			),
			'contexts' => array(
				'type'    => 'multicheck',
				'label'   => 'درخواست‌ها',
				'options' => array(
					'wc_ajax'     => 'ایجکس ووکامرس (افزودن به سبد، به‌روزرسانی سبد، صفحه پرداخت، ثبت سفارش)',
					'wc_api'      => 'بازگشت از درگاه پرداخت (wc-api)',
					'add_to_cart' => 'افزودن به سبد با فرم یا لینک (وقتی بعد از افزودن به سبد خرید منتقل می‌شود)',
				),
				'default' => array( 'wc_ajax', 'wc_api', 'add_to_cart' ),
			),
		);
	}

	public function boot() {
	}

	public function early( $enabled ) {
		if ( isset( $_GET['tarh99_trim_diag'] ) ) {
			$this->start_diag();
			return;
		}
		if ( ! $enabled ) {
			return;
		}
		$context = $this->context();
		if ( '' === $context ) {
			return;
		}
		$this->apply();
		if ( 'add_to_cart' === $context ) {
			add_action( 'template_redirect', array( $this, 'fallback' ), -1000 );
		}
	}

	private function context() {
		if ( is_admin() || wp_doing_cron() || ( defined( 'WP_CLI' ) && WP_CLI ) || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {
			return '';
		}
		$contexts = $this->opt_list( 'contexts' );
		if ( isset( $_GET['wc-ajax'] ) && is_string( $_GET['wc-ajax'] ) && '' !== $_GET['wc-ajax'] ) {
			return in_array( 'wc_ajax', $contexts, true ) ? 'wc_ajax' : '';
		}
		$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) parse_url( (string) $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
		if ( ( isset( $_GET['wc-api'] ) && is_string( $_GET['wc-api'] ) && '' !== $_GET['wc-api'] ) || preg_match( '#/wc-api/[^/]+#', $path ) ) {
			return in_array( 'wc_api', $contexts, true ) ? 'wc_api' : '';
		}
		if ( isset( $_REQUEST['add-to-cart'] ) && is_numeric( $_REQUEST['add-to-cart'] ) && in_array( 'add_to_cart', $contexts, true ) && 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
			return 'add_to_cart';
		}
		return '';
	}

	private function apply() {
		$this->dirs = array();
		foreach ( $this->opt_lines( 'plugins' ) as $dir ) {
			$dir = trim( $dir, " /\t" );
			if ( '' !== $dir && ! in_array( strtolower( $dir ), array_map( 'strtolower', $this->protected ), true ) ) {
				$this->dirs[] = $dir;
			}
		}
		if ( ! $this->dirs ) {
			return;
		}
		add_filter( 'option_active_plugins', array( $this, 'filter_active' ), 1 );
		add_filter( 'pre_update_option_active_plugins', array( $this, 'block_update' ), PHP_INT_MAX, 2 );
	}

	public function filter_active( $plugins ) {
		if ( ! is_array( $plugins ) ) {
			return $plugins;
		}
		$out = array();
		foreach ( $plugins as $plugin ) {
			$dir = strtok( (string) $plugin, '/' );
			if ( in_array( $dir, $this->dirs, true ) ) {
				$this->trimmed[ $plugin ] = true;
				continue;
			}
			$out[] = $plugin;
		}
		return $out;
	}

	public function block_update( $value, $old_value ) {
		return $old_value;
	}

	public function fallback() {
		if ( empty( $this->trimmed ) || headers_sent() || empty( $_SERVER['HTTP_HOST'] ) || empty( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}
		$url = ( is_ssl() ? 'https://' : 'http://' ) . wp_unslash( $_SERVER['HTTP_HOST'] ) . wp_unslash( $_SERVER['REQUEST_URI'] );
		if ( function_exists( 'WC' ) && WC()->session && method_exists( WC()->session, 'has_session' ) && ! WC()->session->has_session() && method_exists( WC()->session, 'set_customer_session_cookie' ) ) {
			WC()->session->set_customer_session_cookie( true );
		}
		nocache_headers();
		wp_safe_redirect( remove_query_arg( 'add-to-cart', $url ), 303 );
		exit;
	}

	private function start_diag() {
		$token = get_transient( self::TOKEN );
		$given = is_string( $_GET['tarh99_trim_diag'] ) ? (string) $_GET['tarh99_trim_diag'] : '';
		if ( ! is_string( $token ) || '' === $token || ! hash_equals( $token, $given ) ) {
			return;
		}
		$this->marks['mu'] = microtime( true );
		$this->last        = $this->marks['mu'];
		$this->diag_trim   = ! empty( $_GET['trim'] );
		if ( $this->diag_trim ) {
			$this->apply();
		}
		add_action( 'plugin_loaded', array( $this, 'diag_plugin' ), PHP_INT_MAX );
		add_action( 'plugins_loaded', array( $this, 'diag_includes' ), PHP_INT_MIN );
		foreach ( array( 'plugins_loaded', 'after_setup_theme', 'init' ) as $hook ) {
			add_action(
				$hook,
				function () use ( $hook ) {
					$this->marks[ $hook ] = microtime( true );
				},
				PHP_INT_MAX
			);
		}
		add_action( 'wp_loaded', array( $this, 'diag_finish' ), PHP_INT_MAX );
	}

	public function diag_plugin( $file ) {
		$now = microtime( true );
		$this->includes[ dirname( plugin_basename( $file ) ) ] = round( ( $now - $this->last ) * 1000, 1 );
		$this->last = $now;
	}

	public function diag_includes() {
		$this->marks['includes'] = microtime( true );
	}

	public function diag_finish() {
		$end   = microtime( true );
		$start = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (float) $_SERVER['REQUEST_TIME_FLOAT'] : $this->marks['mu'];
		$seg   = array();
		$prev  = $start;
		foreach ( array( 'mu', 'includes', 'plugins_loaded', 'after_setup_theme', 'init' ) as $key ) {
			$at          = isset( $this->marks[ $key ] ) ? $this->marks[ $key ] : $prev;
			$seg[ $key ] = round( ( $at - $prev ) * 1000, 1 );
			$prev        = $at;
		}
		$seg['wp_loaded'] = round( ( $end - $prev ) * 1000, 1 );
		while ( ob_get_level() > 0 ) {
			ob_end_clean();
		}
		nocache_headers();
		wp_send_json(
			array(
				'ok'       => 1,
				'trim'     => $this->diag_trim ? 1 : 0,
				'trimmed'  => array_keys( $this->trimmed ),
				'total'    => round( ( $end - $start ) * 1000, 1 ),
				'segments' => $seg,
				'includes' => $this->includes,
			)
		);
	}

	public function run_diag() {
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 120 );
		}
		$token = wp_generate_password( 32, false );
		set_transient( self::TOKEN, $token, 300 );
		$rounds = array( 0, 0, 1, 0, 1, 0, 1 );
		$runs   = array( array(), array() );
		$error  = '';
		foreach ( $rounds as $i => $trim ) {
			$url      = add_query_arg(
				array(
					'tarh99_trim_diag' => $token,
					'trim'             => $trim,
					'r'                => $i . wp_rand( 1000, 9999 ),
				),
				home_url( '/' )
			);
			$response = wp_remote_get(
				$url,
				array(
					'timeout'   => 25,
					'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
					'headers'   => array( 'Cache-Control' => 'no-cache' ),
				)
			);
			if ( is_wp_error( $response ) ) {
				$error = $response->get_error_message();
				break;
			}
			$body = (string) wp_remote_retrieve_body( $response );
			$at   = strpos( $body, '{"ok"' );
			$data = false === $at ? null : json_decode( substr( $body, $at ), true );
			if ( ! is_array( $data ) || empty( $data['ok'] ) ) {
				$error = 'HTTP ' . (int) wp_remote_retrieve_response_code( $response );
				break;
			}
			if ( 0 === $i ) {
				continue;
			}
			$runs[ $trim ][] = $data;
		}
		delete_transient( self::TOKEN );
		return array(
			'time'  => time(),
			'error' => $error,
			'full'  => $this->median_run( $runs[0] ),
			'trim'  => $this->median_run( $runs[1] ),
			'list'  => $this->opt_lines( 'plugins' ),
		);
	}

	private function median_run( $runs ) {
		if ( ! $runs ) {
			return null;
		}
		usort(
			$runs,
			function ( $a, $b ) {
				return $a['total'] <=> $b['total'];
			}
		);
		$run          = $runs[ (int) floor( ( count( $runs ) - 1 ) / 2 ) ];
		$run['count'] = count( $runs );
		return $run;
	}

	public function render_diag() {
		$d = get_transient( self::DIAG );
		if ( ! is_array( $d ) ) {
			return;
		}
		echo '<p>آخرین اجرا: ' . esc_html( wp_date( 'Y-m-d H:i:s', (int) $d['time'] ) ) . '</p>';
		if ( '' !== $d['error'] ) {
			echo '<p style="color:#b32d2e">درخواست آزمایشی سرور به خودش انجام نشد: ' . esc_html( $d['error'] ) . '</p>';
		}
		if ( empty( $d['full'] ) || empty( $d['trim'] ) ) {
			return;
		}
		$full  = $d['full'];
		$trim  = $d['trim'];
		$names = array(
			'total'             => 'کل زمان تا آماده شدن وردپرس',
			'mu'                => 'شروع PHP تا افزونه‌های mu',
			'includes'          => 'بارگذاری فایل افزونه‌ها',
			'plugins_loaded'    => 'plugins_loaded',
			'after_setup_theme' => 'قالب (after_setup_theme)',
			'init'              => 'init',
			'wp_loaded'         => 'wp_loaded',
		);
		echo '<table class="widefat striped" style="max-width:900px"><thead><tr><th>مرحله</th><th>همه افزونه‌ها (ms)</th><th>بدون افزونه‌های فهرست (ms)</th><th>صرفه‌جویی (ms)</th></tr></thead><tbody>';
		foreach ( $names as $key => $label ) {
			$a = 'total' === $key ? (float) $full['total'] : (float) ( isset( $full['segments'][ $key ] ) ? $full['segments'][ $key ] : 0 );
			$b = 'total' === $key ? (float) $trim['total'] : (float) ( isset( $trim['segments'][ $key ] ) ? $trim['segments'][ $key ] : 0 );
			$s = 'total' === $key ? ' style="font-weight:700"' : '';
			echo '<tr' . $s . '><td>' . esc_html( $label ) . '</td><td>' . esc_html( number_format_i18n( $a, 0 ) ) . '</td><td>' . esc_html( number_format_i18n( $b, 0 ) ) . '</td><td>' . esc_html( number_format_i18n( $a - $b, 0 ) ) . '</td></tr>';
		}
		echo '</tbody></table>';
		echo '<p>افزونه‌هایی که در تست حذف شدند: <code>' . esc_html( implode( '، ', (array) $trim['trimmed'] ) ) . '</code> — هر ستون میانه ' . (int) $full['count'] . ' اجرا است.</p>';
		$inc = (array) $full['includes'];
		arsort( $inc );
		echo '<p>زمان بارگذاری فایل هر افزونه (همه افزونه‌ها): ';
		$parts = array();
		foreach ( array_slice( $inc, 0, 12, true ) as $name => $ms ) {
			$parts[] = esc_html( $name ) . ' ' . esc_html( number_format_i18n( (float) $ms, 0 ) ) . 'ms';
		}
		echo implode( ' — ', $parts ) . '</p>';
	}
}
