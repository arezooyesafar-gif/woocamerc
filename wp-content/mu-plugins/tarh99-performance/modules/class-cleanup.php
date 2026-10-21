<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Cleanup extends Tarh99_Perf_Module {

	const HOOK       = 'tarh99_perf_daily_cleanup';
	const LOG_OPTION = 'tarh99_perf_cleanup_log';
	const LOCK       = 'tarh99_perf_cleanup_lock';

	private $deadline = 0;

	public function id() {
		return 'cleanup';
	}

	public function group() {
		return 'stability';
	}

	public function title() {
		return 'پاکسازی خودکار روزانه';
	}

	public function description() {
		return 'هر شب (ساعت ۳:۳۰ به وقت سایت) به‌صورت دسته‌ای و با سقف زمان: ترنزینت‌های منقضی، سشن‌های منقضی ووکامرس، اکشن‌های تمام‌شده و شکست‌خورده قدیمی Action Scheduler و لاگ‌های یتیمشان، revisionهای اضافه هر نوشته/محصول و فایل‌های کش قدیمی این افزونه پاک می‌شوند. سفارش، محصول، کاربر و تنظیمات هرگز حذف نمی‌شوند.';
	}

	public function fields() {
		return array(
			'transients'       => array(
				'type'    => 'checkbox',
				'label'   => 'حذف ترنزینت‌های منقضی',
				'default' => 1,
			),
			'wc_sessions'      => array(
				'type'    => 'checkbox',
				'label'   => 'حذف سشن‌های منقضی ووکامرس',
				'default' => 1,
			),
			'as_complete_days' => array(
				'type'    => 'number',
				'label'   => 'حذف اکشن‌های complete/canceled قدیمی‌تر از (روز، ۰ = خاموش)',
				'default' => 14,
				'min'     => 0,
				'max'     => 365,
			),
			'as_failed_days'   => array(
				'type'    => 'number',
				'label'   => 'حذف اکشن‌های failed قدیمی‌تر از (روز، ۰ = خاموش)',
				'default' => 60,
				'min'     => 0,
				'max'     => 365,
			),
			'as_orphan_logs'   => array(
				'type'    => 'checkbox',
				'label'   => 'حذف لاگ‌های یتیم Action Scheduler',
				'default' => 1,
			),
			'as_retention'     => array(
				'type'    => 'checkbox',
				'label'   => 'هماهنگ‌کردن پاکسازی داخلی Action Scheduler با همین تعداد روز و دسته‌های بزرگ‌تر',
				'default' => 1,
			),
			'revisions_keep'   => array(
				'type'    => 'number',
				'label'   => 'نگه‌داشتن فقط N نسخه آخر revision هر نوشته/محصول (۰ = خاموش)',
				'default' => 10,
				'min'     => 0,
				'max'     => 100,
			),
			'file_cache'       => array(
				'type'    => 'checkbox',
				'label'   => 'حذف فایل‌های کش قدیمی این افزونه',
				'default' => 1,
			),
			'time_limit'       => array(
				'type'    => 'number',
				'label'   => 'حداکثر زمان هر اجرا (ثانیه)',
				'default' => 20,
				'min'     => 5,
				'max'     => 120,
			),
		);
	}

	public function boot() {
		add_action( self::HOOK, array( $this, 'run' ) );
		add_action( 'admin_init', array( $this, 'ensure_schedule' ) );
		if ( $this->opt( 'as_retention' ) && (int) $this->opt( 'as_complete_days' ) > 0 ) {
			add_filter( 'action_scheduler_retention_period', array( $this, 'retention' ), 99 );
			add_filter( 'action_scheduler_cleanup_batch_size', array( $this, 'batch_size' ), 99 );
		}
	}

	public function retention() {
		return (int) $this->opt( 'as_complete_days' ) * DAY_IN_SECONDS;
	}

	public function batch_size( $size ) {
		return max( (int) $size, 100 );
	}

	public static function next_run_timestamp() {
		try {
			$tz   = wp_timezone();
			$next = new DateTimeImmutable( 'today 03:30', $tz );
			if ( $next->getTimestamp() <= time() ) {
				$next = new DateTimeImmutable( 'tomorrow 03:30', $tz );
			}
			return $next->getTimestamp();
		} catch ( Exception $e ) {
			return time() + HOUR_IN_SECONDS;
		}
	}

	public function ensure_schedule() {
		if ( ! wp_next_scheduled( self::HOOK ) ) {
			wp_schedule_event( self::next_run_timestamp(), 'daily', self::HOOK );
		}
	}

	public static function unschedule() {
		$timestamp = wp_next_scheduled( self::HOOK );
		while ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::HOOK );
			$timestamp = wp_next_scheduled( self::HOOK );
		}
	}

	private function time_left() {
		return microtime( true ) < $this->deadline;
	}

	private function table_exists( $table ) {
		global $wpdb;
		return $table === $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
	}

	public function run( $manual = false ) {
		if ( get_transient( self::LOCK ) ) {
			return array();
		}
		set_transient( self::LOCK, 1, 10 * MINUTE_IN_SECONDS );
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( max( 60, (int) $this->opt( 'time_limit' ) + 30 ) );
		}
		$started        = microtime( true );
		$this->deadline = $started + (int) $this->opt( 'time_limit' );
		$result         = array();

		try {
			if ( $this->opt( 'transients' ) && $this->time_left() ) {
				$result['transients'] = $this->clean_transients();
			}
			if ( $this->opt( 'wc_sessions' ) && $this->time_left() ) {
				$result['wc_sessions'] = $this->clean_sessions();
			}
			if ( (int) $this->opt( 'as_complete_days' ) > 0 && $this->time_left() ) {
				$result['as_complete'] = $this->clean_actions( array( 'complete', 'canceled' ), (int) $this->opt( 'as_complete_days' ) );
			}
			if ( (int) $this->opt( 'as_failed_days' ) > 0 && $this->time_left() ) {
				$result['as_failed'] = $this->clean_actions( array( 'failed' ), (int) $this->opt( 'as_failed_days' ) );
			}
			if ( $this->opt( 'as_orphan_logs' ) && $this->time_left() ) {
				$result['as_orphan_logs'] = $this->clean_orphan_logs();
			}
			if ( (int) $this->opt( 'revisions_keep' ) > 0 && $this->time_left() ) {
				$result['revisions'] = $this->clean_revisions( (int) $this->opt( 'revisions_keep' ) );
			}
			if ( $this->opt( 'file_cache' ) && $this->time_left() ) {
				$result['cache_files'] = Tarh99_Perf_Cache_Store::purge_files( 2 * DAY_IN_SECONDS, $this->deadline );
			}
		} catch ( Throwable $e ) {
			$result['error'] = substr( $e->getMessage(), 0, 200 );
		}

		$result['seconds'] = round( microtime( true ) - $started, 2 );
		$result['manual']  = $manual ? 1 : 0;
		$result['time']    = current_time( 'mysql' );

		$log = get_option( self::LOG_OPTION, array() );
		$log = is_array( $log ) ? $log : array();
		array_unshift( $log, $result );
		update_option( self::LOG_OPTION, array_slice( $log, 0, 15 ), false );
		delete_transient( self::LOCK );
		return $result;
	}

	private function clean_transients() {
		global $wpdb;
		$now     = time();
		$deleted = 0;
		$deleted += (int) $wpdb->query(
			$wpdb->prepare(
				"DELETE a, b FROM {$wpdb->options} a INNER JOIN {$wpdb->options} b ON b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) ) WHERE a.option_name LIKE %s AND a.option_name NOT LIKE %s AND b.option_value < %d",
				$wpdb->esc_like( '_transient_' ) . '%',
				$wpdb->esc_like( '_transient_timeout_' ) . '%',
				$now
			)
		);
		$deleted += (int) $wpdb->query(
			$wpdb->prepare(
				"DELETE a, b FROM {$wpdb->options} a INNER JOIN {$wpdb->options} b ON b.option_name = CONCAT( '_site_transient_timeout_', SUBSTRING( a.option_name, 17 ) ) WHERE a.option_name LIKE %s AND a.option_name NOT LIKE %s AND b.option_value < %d",
				$wpdb->esc_like( '_site_transient_' ) . '%',
				$wpdb->esc_like( '_site_transient_timeout_' ) . '%',
				$now
			)
		);
		$deleted += (int) $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s AND option_value < %d",
				$wpdb->esc_like( '_transient_timeout_' ) . '%',
				$now
			)
		);
		return $deleted;
	}

	private function clean_sessions() {
		global $wpdb;
		$table = $wpdb->prefix . 'woocommerce_sessions';
		if ( ! $this->table_exists( $table ) ) {
			return 0;
		}
		$total = 0;
		do {
			$deleted = (int) $wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE session_expiry < %d LIMIT 2000", time() ) );
			$total  += $deleted;
		} while ( $deleted >= 2000 && $this->time_left() );
		return $total;
	}

	private function clean_actions( array $statuses, $days ) {
		global $wpdb;
		$actions = $wpdb->prefix . 'actionscheduler_actions';
		$logs    = $wpdb->prefix . 'actionscheduler_logs';
		if ( ! $this->table_exists( $actions ) ) {
			return 0;
		}
		$has_logs     = $this->table_exists( $logs );
		$cutoff       = gmdate( 'Y-m-d H:i:s', time() - $days * DAY_IN_SECONDS );
		$placeholders = implode( ',', array_fill( 0, count( $statuses ), '%s' ) );
		$total        = 0;
		do {
			$ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT action_id FROM {$actions} WHERE status IN ({$placeholders}) AND scheduled_date_gmt < %s LIMIT 1000",
					array_merge( $statuses, array( $cutoff ) )
				)
			);
			$ids = array_map( 'intval', (array) $ids );
			if ( empty( $ids ) ) {
				break;
			}
			$in = implode( ',', $ids );
			if ( $has_logs ) {
				$wpdb->query( "DELETE FROM {$logs} WHERE action_id IN ({$in})" );
			}
			$total += (int) $wpdb->query( "DELETE FROM {$actions} WHERE action_id IN ({$in})" );
		} while ( count( $ids ) >= 1000 && $this->time_left() );
		return $total;
	}

	private function clean_orphan_logs() {
		global $wpdb;
		$actions = $wpdb->prefix . 'actionscheduler_actions';
		$logs    = $wpdb->prefix . 'actionscheduler_logs';
		if ( ! $this->table_exists( $actions ) || ! $this->table_exists( $logs ) ) {
			return 0;
		}
		$total = 0;
		do {
			$ids = $wpdb->get_col( "SELECT l.log_id FROM {$logs} l LEFT JOIN {$actions} a ON a.action_id = l.action_id WHERE a.action_id IS NULL LIMIT 2000" );
			$ids = array_map( 'intval', (array) $ids );
			if ( empty( $ids ) ) {
				break;
			}
			$total += (int) $wpdb->query( "DELETE FROM {$logs} WHERE log_id IN (" . implode( ',', $ids ) . ')' );
		} while ( count( $ids ) >= 2000 && $this->time_left() );
		return $total;
	}

	private function clean_revisions( $keep ) {
		global $wpdb;
		$parents = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT post_parent FROM {$wpdb->posts} WHERE post_type = 'revision' AND post_parent > 0 GROUP BY post_parent HAVING COUNT(*) > %d LIMIT 200",
				$keep
			)
		);
		$deleted = 0;
		foreach ( (array) $parents as $parent_id ) {
			if ( ! $this->time_left() ) {
				break;
			}
			$ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'revision' AND post_parent = %d ORDER BY post_date DESC, ID DESC",
					(int) $parent_id
				)
			);
			foreach ( array_slice( (array) $ids, $keep ) as $revision_id ) {
				if ( ! $this->time_left() ) {
					break 2;
				}
				if ( wp_delete_post_revision( (int) $revision_id ) ) {
					++$deleted;
				}
			}
		}
		return $deleted;
	}
}
