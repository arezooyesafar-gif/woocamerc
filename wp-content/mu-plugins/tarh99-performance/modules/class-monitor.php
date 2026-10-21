<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Monitor extends Tarh99_Perf_Module {

	const CACHE = 'tarh99_perf_metrics';

	public function id() {
		return 'monitor';
	}

	public function group() {
		return 'stability';
	}

	public function title() {
		return 'مانیتورینگ و هشدار';
	}

	public function description() {
		return 'حجم autoload، تعداد ترنزینت‌ها، حجم سشن‌ها، وضعیت Action Scheduler، revisionها، Object Cache، OPcache و نسخه PHP در تب «مانیتورینگ» نمایش داده می‌شود. اگر یکی از حدها رد شود، در داشبورد به مدیر هشدار داده می‌شود. اندازه‌گیری حداکثر ساعتی یک‌بار انجام می‌شود.';
	}

	public function fields() {
		return array(
			'notice'        => array(
				'type'    => 'checkbox',
				'label'   => 'نمایش هشدار در داشبورد',
				'default' => 1,
			),
			'autoload_kb'   => array(
				'type'    => 'number',
				'label'   => 'حد autoload (KB)',
				'default' => 800,
				'min'     => 100,
				'max'     => 20000,
			),
			'transients'    => array(
				'type'    => 'number',
				'label'   => 'حد تعداد ردیف ترنزینت',
				'default' => 5000,
				'min'     => 100,
				'max'     => 1000000,
			),
			'sessions'      => array(
				'type'    => 'number',
				'label'   => 'حد تعداد سشن ووکامرس',
				'default' => 50000,
				'min'     => 100,
				'max'     => 5000000,
			),
			'as_pending'    => array(
				'type'    => 'number',
				'label'   => 'حد اکشن‌های عقب‌افتاده Action Scheduler',
				'default' => 500,
				'min'     => 10,
				'max'     => 1000000,
			),
			'as_complete'   => array(
				'type'    => 'number',
				'label'   => 'حد اکشن‌های complete',
				'default' => 50000,
				'min'     => 100,
				'max'     => 10000000,
			),
			'revisions'     => array(
				'type'    => 'number',
				'label'   => 'حد تعداد revision',
				'default' => 10000,
				'min'     => 100,
				'max'     => 10000000,
			),
		);
	}

	public function boot() {
		add_action( 'admin_notices', array( $this, 'notice' ) );
		add_action( 'tarh99_perf_collect_metrics', array( $this, 'collect' ) );
	}

	public function collect() {
		$this->metrics( true );
	}

	private function table_exists( $table ) {
		global $wpdb;
		return $table === $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
	}

	public function metrics( $force = false ) {
		if ( ! $force ) {
			$cached = get_transient( self::CACHE );
			if ( is_array( $cached ) ) {
				return $cached;
			}
		}
		global $wpdb;
		$m = array(
			'time' => current_time( 'mysql' ),
		);

		$row = $wpdb->get_row( "SELECT COUNT(*) AS c, COALESCE(SUM(LENGTH(option_value)),0) AS s FROM {$wpdb->options} WHERE autoload IN ('yes','on','auto-on','auto')" );
		$m['autoload_count'] = $row ? (int) $row->c : 0;
		$m['autoload_kb']    = $row ? round( (int) $row->s / 1024, 1 ) : 0;
		$m['autoload_top']   = $wpdb->get_results( "SELECT option_name, ROUND(LENGTH(option_value)/1024,1) AS kb FROM {$wpdb->options} WHERE autoload IN ('yes','on','auto-on','auto') ORDER BY LENGTH(option_value) DESC LIMIT 10", ARRAY_A );

		$m['transients']         = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s", $wpdb->esc_like( '_transient_' ) . '%', $wpdb->esc_like( '_site_transient_' ) . '%' ) );
		$m['transients_expired'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->options} WHERE ( option_name LIKE %s OR option_name LIKE %s ) AND option_value < %d", $wpdb->esc_like( '_transient_timeout_' ) . '%', $wpdb->esc_like( '_site_transient_timeout_' ) . '%', time() ) );

		$sessions = $wpdb->prefix . 'woocommerce_sessions';
		if ( $this->table_exists( $sessions ) ) {
			$row                  = $wpdb->get_row( $wpdb->prepare( "SELECT COUNT(*) AS c, COALESCE(SUM(session_expiry < %d),0) AS e FROM {$sessions}", time() ) );
			$m['sessions']         = $row ? (int) $row->c : 0;
			$m['sessions_expired'] = $row ? (int) $row->e : 0;
			$m['sessions_mb']      = round( (float) $wpdb->get_var( $wpdb->prepare( 'SELECT (DATA_LENGTH + INDEX_LENGTH) / 1048576 FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s', DB_NAME, $sessions ) ), 2 );
		}

		$actions = $wpdb->prefix . 'actionscheduler_actions';
		if ( $this->table_exists( $actions ) ) {
			$m['as_status']  = $wpdb->get_results( "SELECT status, COUNT(*) AS c FROM {$actions} GROUP BY status", ARRAY_A );
			$m['as_pastdue'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$actions} WHERE status = 'pending' AND scheduled_date_gmt < %s", gmdate( 'Y-m-d H:i:s', time() - 600 ) ) );
		}

		$m['revisions']  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'revision'" );
		$m['db_mb']      = round( (float) $wpdb->get_var( $wpdb->prepare( 'SELECT SUM(DATA_LENGTH + INDEX_LENGTH) / 1048576 FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s', DB_NAME ) ), 1 );
		$m['big_tables'] = $wpdb->get_results( $wpdb->prepare( 'SELECT TABLE_NAME AS t, ROUND((DATA_LENGTH + INDEX_LENGTH) / 1048576, 1) AS mb, TABLE_ROWS AS r FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC LIMIT 8', DB_NAME ), ARRAY_A );

		$crons   = function_exists( '_get_cron_array' ) ? _get_cron_array() : array();
		$overdue = 0;
		$events  = 0;
		foreach ( (array) $crons as $timestamp => $hooks ) {
			foreach ( (array) $hooks as $hook => $instances ) {
				$events += count( (array) $instances );
				if ( (int) $timestamp < time() - 900 ) {
					$overdue += count( (array) $instances );
				}
			}
		}
		$m['cron_events']  = $events;
		$m['cron_overdue'] = $overdue;

		set_transient( self::CACHE, $m, HOUR_IN_SECONDS );
		return $m;
	}

	public function environment() {
		global $wpdb, $wp_version;
		$opcache = array(
			'loaded'  => function_exists( 'opcache_get_status' ),
			'enabled' => false,
		);
		if ( $opcache['loaded'] ) {
			$status = @opcache_get_status( false );
			if ( is_array( $status ) ) {
				$opcache['enabled'] = ! empty( $status['opcache_enabled'] );
				if ( isset( $status['memory_usage'] ) ) {
					$opcache['used_mb'] = round( $status['memory_usage']['used_memory'] / 1048576, 1 );
					$opcache['free_mb'] = round( $status['memory_usage']['free_memory'] / 1048576, 1 );
				}
				if ( isset( $status['opcache_statistics']['opcache_hit_rate'] ) ) {
					$opcache['hit_rate'] = round( $status['opcache_statistics']['opcache_hit_rate'], 1 );
				}
			}
		}
		return array(
			'php'             => PHP_VERSION,
			'wp'              => $wp_version,
			'wc'              => defined( 'WC_VERSION' ) ? WC_VERSION : '-',
			'mysql'           => $wpdb->db_server_info(),
			'memory_limit'    => ini_get( 'memory_limit' ),
			'wp_memory_limit' => defined( 'WP_MEMORY_LIMIT' ) ? WP_MEMORY_LIMIT : '-',
			'max_execution'   => ini_get( 'max_execution_time' ),
			'opcache'         => $opcache,
			'object_cache'    => wp_using_ext_object_cache(),
			'redis_ext'       => class_exists( 'Redis' ),
			'memcached_ext'   => class_exists( 'Memcached' ),
			'dropin'          => file_exists( WP_CONTENT_DIR . '/object-cache.php' ),
			'ioncube'         => function_exists( 'ioncube_loader_version' ) ? ioncube_loader_version() : '-',
			'wp_cron'         => defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ? 'cron واقعی (DISABLE_WP_CRON)' : 'روی بازدید کاربران',
			'rocket'          => Tarh99_Perf_Env::rocket_active() ? WP_ROCKET_VERSION : '-',
			'tps'             => Tarh99_Perf_Env::tps_active(),
			'hpos'            => 'yes' === get_option( 'woocommerce_custom_orders_table_enabled' ),
			'post_revisions'  => ( ! defined( 'WP_POST_REVISIONS' ) || true === WP_POST_REVISIONS || (int) WP_POST_REVISIONS < 0 ) ? 'نامحدود (پیش‌فرض وردپرس)' : (string) (int) WP_POST_REVISIONS,
			'debug'           => defined( 'WP_DEBUG' ) && WP_DEBUG,
		);
	}

	public function warnings( $m ) {
		$w = array();
		if ( isset( $m['autoload_kb'] ) && $m['autoload_kb'] > (float) $this->opt( 'autoload_kb' ) ) {
			$w[] = sprintf( 'حجم autoload %s کیلوبایت است (حد: %s).', $m['autoload_kb'], $this->opt( 'autoload_kb' ) );
		}
		if ( isset( $m['transients'] ) && $m['transients'] > (int) $this->opt( 'transients' ) ) {
			$w[] = sprintf( 'تعداد ردیف‌های ترنزینت %s است (حد: %s).', number_format_i18n( $m['transients'] ), number_format_i18n( $this->opt( 'transients' ) ) );
		}
		if ( isset( $m['sessions'] ) && $m['sessions'] > (int) $this->opt( 'sessions' ) ) {
			$w[] = sprintf( 'تعداد سشن‌های ووکامرس %s است (حد: %s).', number_format_i18n( $m['sessions'] ), number_format_i18n( $this->opt( 'sessions' ) ) );
		}
		if ( isset( $m['as_pastdue'] ) && $m['as_pastdue'] > (int) $this->opt( 'as_pending' ) ) {
			$w[] = sprintf( '%s اکشن عقب‌افتاده در Action Scheduler (کران یا صف گیر کرده است).', number_format_i18n( $m['as_pastdue'] ) );
		}
		if ( ! empty( $m['as_status'] ) ) {
			foreach ( $m['as_status'] as $row ) {
				if ( 'complete' === $row['status'] && (int) $row['c'] > (int) $this->opt( 'as_complete' ) ) {
					$w[] = sprintf( '%s اکشن complete در Action Scheduler.', number_format_i18n( (int) $row['c'] ) );
				}
			}
		}
		if ( isset( $m['revisions'] ) && $m['revisions'] > (int) $this->opt( 'revisions' ) ) {
			$w[] = sprintf( '%s revision در دیتابیس.', number_format_i18n( $m['revisions'] ) );
		}
		if ( isset( $m['cron_overdue'] ) && $m['cron_overdue'] > 20 ) {
			$w[] = sprintf( '%s رویداد کران عقب‌افتاده (بیش از ۱۵ دقیقه).', number_format_i18n( $m['cron_overdue'] ) );
		}
		return $w;
	}

	public function notice() {
		if ( ! $this->opt( 'notice' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'toplevel_page_tarh99-performance' ), true ) ) {
			return;
		}
		$m = get_transient( self::CACHE );
		if ( ! is_array( $m ) ) {
			if ( ! wp_next_scheduled( 'tarh99_perf_collect_metrics' ) ) {
				wp_schedule_single_event( time(), 'tarh99_perf_collect_metrics' );
			}
			return;
		}
		$warnings = $this->warnings( $m );
		if ( empty( $warnings ) ) {
			return;
		}
		echo '<div class="notice notice-warning"><p><strong>سرعت طرح۹۹:</strong></p><ul style="list-style:disc;padding-right:20px">';
		foreach ( $warnings as $warning ) {
			echo '<li>' . esc_html( $warning ) . '</li>';
		}
		echo '</ul><p><a href="' . esc_url( admin_url( 'admin.php?page=tarh99-performance&tab=monitor' ) ) . '">مشاهده جزئیات</a></p></div>';
	}
}
