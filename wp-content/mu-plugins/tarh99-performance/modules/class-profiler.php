<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Profiler extends Tarh99_Perf_Module {

	const LOG = 'tarh99_perf_profile_log';

	private $marks = array();
	private $plugins = array();
	private $last = 0;
	private $http = 0.0;
	private $http_count = 0;
	private $http_start = array();
	private $detailed = false;
	private $stack = array();
	private $widgets = array();
	private $docs = array();
	private $menus = array();
	private $menu_ms = 0.0;
	private $menu_n = 0;

	public function id() {
		return 'profiler';
	}

	public function group() {
		return 'stability';
	}

	public function title() {
		return 'ثبت زمان ساخت صفحات کند (عیب‌یابی سرور)';
	}

	public function description() {
		return 'برای هر درخواستی که سرور برای ساختنش بیشتر از حد تعیین‌شده وقت گذاشته (صفحاتی که از کش راکت سرو نشده‌اند، کاربران لاگین، سبد، پرداخت، جستجو و ایجکس)، زمان هر مرحله وردپرس، زمان بارگذاری هر افزونه، تعداد کوئری‌ها و زمان درخواست‌های بیرونی ثبت می‌شود. نتیجه در تب «ابزارها و لاگ‌ها». روی ظاهر و رفتار سایت اثری ندارد.';
	}

	public function default_enabled() {
		return false;
	}

	public function fields() {
		return array(
			'threshold' => array(
				'type'    => 'number',
				'label'   => 'حداقل زمان برای ثبت (ثانیه)',
				'default' => 1,
				'min'     => 0.2,
				'max'     => 20,
				'step'    => 0.1,
			),
			'skip_background' => array(
				'type'    => 'checkbox',
				'label'   => 'درخواست‌های پس‌زمینه (پیش‌بارگذاری راکت، کران و Action Scheduler) ثبت نشوند تا لاگ با درخواست‌های واقعی بازدیدکننده‌ها و مدیر (ورود، سبد، پرداخت، صفحات لاگین) پر بماند',
				'default' => 1,
			),
			'sample'    => array(
				'type'    => 'number',
				'label'   => 'ثبت جزئیات کوئری‌ها در ۱ از هر چند درخواست (۰ = فقط با ?tarh99_profile=1)',
				'default' => 10,
				'min'     => 0,
				'max'     => 1000,
			),
		);
	}

	public function boot() {
	}

	public function early() {
		$sample         = (int) $this->opt( 'sample' );
		$this->detailed = isset( $_GET['tarh99_profile'] ) || ( $sample > 0 && 0 === mt_rand( 0, $sample - 1 ) );
		if ( $this->detailed && ! defined( 'SAVEQUERIES' ) ) {
			define( 'SAVEQUERIES', true );
		}
		$this->mark( 'mu-plugins' );
		$this->last = microtime( true );
		add_action( 'plugin_loaded', array( $this, 'plugin_loaded' ), PHP_INT_MAX );
		foreach ( array( 'plugins_loaded', 'after_setup_theme', 'init', 'wp_loaded', 'wp', 'template_redirect', 'wp_head', 'wp_footer' ) as $hook ) {
			add_action(
				$hook,
				function () use ( $hook ) {
					$this->mark( $hook );
				},
				PHP_INT_MAX
			);
		}
		add_filter( 'pre_http_request', array( $this, 'http_before' ), 0, 3 );
		add_action( 'http_api_debug', array( $this, 'http_after' ), 10, 5 );
		add_action( 'elementor/frontend/widget/before_render', array( $this, 'widget_before' ), PHP_INT_MIN );
		add_action( 'elementor/frontend/widget/after_render', array( $this, 'widget_after' ), PHP_INT_MAX );
		add_action( 'elementor/frontend/before_get_builder_content', array( $this, 'doc_before' ), PHP_INT_MIN );
		add_action( 'elementor/frontend/get_builder_content', array( $this, 'doc_after' ), PHP_INT_MAX );
		add_filter( 'pre_wp_nav_menu', array( $this, 'menu_before' ), PHP_INT_MIN, 2 );
		add_filter( 'wp_nav_menu', array( $this, 'menu_after' ), PHP_INT_MAX, 2 );
		add_action( 'shutdown', array( $this, 'finish' ), PHP_INT_MAX );
	}

	public function widget_before( $widget ) {
		$this->stack[] = array( is_object( $widget ) && method_exists( $widget, 'get_name' ) ? (string) $widget->get_name() : '?', microtime( true ) );
	}

	public function widget_after( $widget ) {
		$item = array_pop( $this->stack );
		if ( ! $item ) {
			return;
		}
		if ( ! isset( $this->widgets[ $item[0] ] ) ) {
			$this->widgets[ $item[0] ] = array( 0.0, 0 );
		}
		$this->widgets[ $item[0] ][0] += microtime( true ) - $item[1];
		++$this->widgets[ $item[0] ][1];
	}

	public function doc_before( $document ) {
		$this->docs[] = array( $this->doc_label( $document ), microtime( true ), null );
	}

	public function doc_after( $document ) {
		$label = $this->doc_label( $document );
		for ( $i = count( $this->docs ) - 1; $i >= 0; $i-- ) {
			if ( null === $this->docs[ $i ][2] && $label === $this->docs[ $i ][0] ) {
				$this->docs[ $i ][2] = microtime( true ) - $this->docs[ $i ][1];
				return;
			}
		}
	}

	private function doc_label( $document ) {
		if ( ! is_object( $document ) || ! method_exists( $document, 'get_main_id' ) ) {
			return '?';
		}
		return ( method_exists( $document, 'get_name' ) ? $document->get_name() : 'doc' ) . '#' . (int) $document->get_main_id();
	}

	public function menu_before( $pre, $args ) {
		if ( is_object( $args ) ) {
			$this->menus[ spl_object_id( $args ) ] = microtime( true );
		}
		return $pre;
	}

	public function menu_after( $html, $args ) {
		$key = is_object( $args ) ? spl_object_id( $args ) : 0;
		if ( isset( $this->menus[ $key ] ) ) {
			$this->menu_ms += microtime( true ) - $this->menus[ $key ];
			++$this->menu_n;
			unset( $this->menus[ $key ] );
		}
		return $html;
	}

	private function elementor_summary() {
		$widgets = array();
		foreach ( $this->widgets as $name => $row ) {
			$widgets[ $name ] = array( (int) round( $row[0] * 1000 ), $row[1] );
		}
		uasort(
			$widgets,
			function ( $a, $b ) {
				return $b[0] - $a[0];
			}
		);
		$docs = array();
		foreach ( $this->docs as $doc ) {
			if ( null !== $doc[2] ) {
				$docs[] = array( $doc[0], (int) round( $doc[2] * 1000 ) );
			}
		}
		return array(
			'widgets' => array_slice( $widgets, 0, 8, true ),
			'docs'    => array_slice( $docs, 0, 6 ),
			'menus'   => array( (int) round( $this->menu_ms * 1000 ), $this->menu_n ),
		);
	}

	private function role() {
		if ( ! is_user_logged_in() ) {
			return '';
		}
		$user = wp_get_current_user();
		return ( $user && ! empty( $user->roles ) ) ? (string) reset( $user->roles ) : 'user';
	}

	private function mark( $name ) {
		if ( ! isset( $this->marks[ $name ] ) ) {
			$this->marks[ $name ] = microtime( true );
		}
	}

	public function plugin_loaded( $file ) {
		$now  = microtime( true );
		$slug = basename( dirname( (string) $file ) );
		if ( '.' === $slug || '' === $slug ) {
			$slug = basename( (string) $file );
		}
		$this->plugins[ $slug ] = round( ( $now - $this->last ) * 1000 );
		$this->last             = $now;
	}

	public function http_before( $pre, $args, $url ) {
		$this->http_start[] = microtime( true );
		return $pre;
	}

	public function http_after( $response, $context, $class, $args, $url ) {
		$start = array_pop( $this->http_start );
		if ( $start ) {
			$this->http += microtime( true ) - $start;
			++$this->http_count;
		}
	}

	private function request_start() {
		return isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (float) $_SERVER['REQUEST_TIME_FLOAT'] : ( isset( $this->marks['mu-plugins'] ) ? $this->marks['mu-plugins'] : microtime( true ) );
	}

	public function finish() {
		if ( Tarh99_Perf_Env::is_cli() || wp_doing_cron() || ( $this->opt( 'skip_background' ) && $this->background() ) ) {
			return;
		}
		$start = $this->request_start();
		$total = microtime( true ) - $start;
		if ( $total < (float) $this->opt( 'threshold' ) && ! isset( $_GET['tarh99_profile'] ) ) {
			return;
		}
		$phases = array();
		$prev   = $start;
		foreach ( $this->marks as $name => $time ) {
			$phases[ $name ] = (int) round( ( $time - $prev ) * 1000 );
			$prev            = $time;
		}
		$phases['render'] = (int) round( ( microtime( true ) - $prev ) * 1000 );
		arsort( $this->plugins );
		global $wpdb;
		$entry = array(
			't'       => time(),
			'url'     => isset( $_SERVER['REQUEST_URI'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 0, 200 ) : '',
			'method'  => isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_key( $_SERVER['REQUEST_METHOD'] ) : '',
			'ctx'     => Tarh99_Perf_Env::current_context() . ( wp_doing_ajax() && isset( $_REQUEST['action'] ) ? ':' . sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '' ),
			'user'    => is_user_logged_in() ? 1 : 0,
			'role'    => $this->role(),
			'el'      => $this->elementor_summary(),
			'total'   => (int) round( $total * 1000 ),
			'phases'  => $phases,
			'plugins' => array_slice( $this->plugins, 0, 6, true ),
			'queries' => (int) $wpdb->num_queries,
			'http'    => (int) round( $this->http * 1000 ),
			'http_n'  => $this->http_count,
			'mem'     => (int) round( memory_get_peak_usage( true ) / 1048576 ),
			'slow_q'  => $this->slow_queries(),
		);
		$log = get_option( self::LOG, array() );
		$log = is_array( $log ) ? $log : array();
		array_unshift( $log, $entry );
		update_option( self::LOG, array_slice( $log, 0, 150 ), false );
	}

	private function background() {
		$agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
		if ( 0 === strpos( $agent, 'WP Rocket/' ) || isset( $_SERVER['HTTP_X_TARH99_WARMUP'] ) ) {
			return true;
		}
		$action = isset( $_REQUEST['action'] ) ? (string) $_REQUEST['action'] : '';
		return wp_doing_ajax() && 0 === strpos( $action, 'as_async_request' );
	}

	private function slow_queries() {
		global $wpdb;
		if ( ! $this->detailed || empty( $wpdb->queries ) || ! is_array( $wpdb->queries ) ) {
			return array();
		}
		$sum   = 0.0;
		$items = array();
		foreach ( $wpdb->queries as $q ) {
			$sum    += (float) $q[1];
			$items[] = array(
				'ms'  => (int) round( (float) $q[1] * 1000 ),
				'sql' => substr( preg_replace( '/\s+/', ' ', (string) $q[0] ), 0, 160 ),
				'by'  => substr( (string) $q[2], -160 ),
			);
		}
		usort(
			$items,
			function ( $a, $b ) {
				return $b['ms'] - $a['ms'];
			}
		);
		return array(
			'sum' => (int) round( $sum * 1000 ),
			'top' => array_slice( $items, 0, 5 ),
		);
	}

	public function render_log() {
		$log = get_option( self::LOG, array() );
		echo '<div class="t99-box"><h2>صفحات کند ثبت‌شده (زمان ساخت روی سرور)</h2>';
		if ( ! $this->enabled() ) {
			echo '<p>ماژول «ثبت زمان ساخت صفحات کند» خاموش است.</p></div>';
			return;
		}
		if ( empty( $log ) || ! is_array( $log ) ) {
			echo '<p>هنوز درخواست کندی ثبت نشده است.</p></div>';
			return;
		}
		echo '<table class="widefat striped" style="direction:ltr;text-align:left;font-size:12px"><thead><tr><th>time</th><th>url / context</th><th>total ms</th><th>phases ms</th><th>slowest plugins (load ms)</th><th>elementor / menus (ms)</th><th>queries</th><th>http</th><th>mem MB</th></tr></thead><tbody>';
		foreach ( array_slice( $log, 0, 40 ) as $e ) {
			$phases  = array();
			foreach ( (array) $e['phases'] as $k => $v ) {
				$phases[] = $k . ':' . $v;
			}
			$plugins = array();
			foreach ( (array) $e['plugins'] as $k => $v ) {
				$plugins[] = $k . ':' . $v;
			}
			$queries = (int) $e['queries'];
			if ( ! empty( $e['slow_q']['sum'] ) ) {
				$queries .= ' (' . (int) $e['slow_q']['sum'] . 'ms)';
				foreach ( (array) $e['slow_q']['top'] as $q ) {
					$queries .= '<br><small>' . (int) $q['ms'] . 'ms ' . esc_html( $q['sql'] ) . ' — ' . esc_html( $q['by'] ) . '</small>';
				}
			}
			$el = array();
			if ( ! empty( $e['el'] ) && is_array( $e['el'] ) ) {
				foreach ( (array) $e['el']['docs'] as $d ) {
					$el[] = $d[0] . ':' . (int) $d[1];
				}
				foreach ( (array) $e['el']['widgets'] as $k => $v ) {
					$el[] = $k . ':' . (int) $v[0] . 'x' . (int) $v[1];
				}
				$el[] = 'wp_nav_menu:' . (int) $e['el']['menus'][0] . 'x' . (int) $e['el']['menus'][1];
			}
			$who = $e['user'] ? ' logged-in' . ( ! empty( $e['role'] ) ? ' (' . $e['role'] . ')' : '' ) : ' guest';
			echo '<tr><td>' . esc_html( wp_date( 'm/d H:i:s', (int) $e['t'] ) ) . '</td><td>' . esc_html( $e['url'] ) . '<br><small>' . esc_html( $e['method'] . ' ' . $e['ctx'] . $who ) . '</small></td><td><b>' . (int) $e['total'] . '</b></td><td><small>' . esc_html( implode( ' ', $phases ) ) . '</small></td><td><small>' . esc_html( implode( ' ', $plugins ) ) . '</small></td><td><small>' . esc_html( implode( ' ', $el ) ) . '</small></td><td>' . $queries . '</td><td>' . (int) $e['http'] . 'ms/' . (int) $e['http_n'] . '</td><td>' . (int) $e['mem'] . '</td></tr>';
		}
		echo '</tbody></table></div>';
	}
}
