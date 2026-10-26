<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Cache_Refresh extends Tarh99_Perf_Module {

	const HOOK     = 'tarh99_perf_cache_refresh';
	const SCHEDULE = 'tarh99_perf_10min';
	const STATE    = 'tarh99_perf_cache_refresh';
	const INTERVAL = 600;
	const FILE     = '#^index.*\.html(_gzip)?$#';

	private $deadline = 0;

	public function id() {
		return 'cache_refresh';
	}

	public function group() {
		return 'server';
	}

	public function risk() {
		return 'medium';
	}

	public function title() {
		return 'تازه‌سازی تدریجی کش راکت به‌جای پاک شدن یکجا';
	}

	public function description() {
		return 'راکت هر ساعت همه صفحه‌هایی را که از طول عمر کش (Cache Lifespan) قدیمی‌تر شده‌اند یکجا پاک می‌کند و پیش‌بارگذاری دقیقه‌ای حدود ۴۵ صفحه را دوباره می‌سازد؛ با چند هزار محصول این کار بیش از یک ساعت طول می‌کشد و در این مدت هر صفحه‌ای که هنوز ساخته نشده برای مهمان ۴ تا ۵ ثانیه طول می‌کشد. این ماژول هر ۱۰ دقیقه تعداد کمی از قدیمی‌ترین صفحه‌ها را، قبل از رسیدن به پایان عمرشان، پاک می‌کند و همان رویداد خود راکت را صدا می‌زند تا پیش‌بارگذاری راکت فوراً آن‌ها را دوباره بسازد؛ پس در هر لحظه فقط چند صفحه بدون کش هستند. تا وقتی این کار مرتب اجرا شود پاک‌سازی یکجای راکت کنار می‌رود و اگر دو ساعت اجرا نشود خودبه‌خود برمی‌گردد. کش کاربران لاگین مثل قبل بعد از پایان عمر پاک می‌شود. آمار پوشش کش (چند درصد محصولات و دسته‌ها کش دارند) هم در همین‌جا نمایش داده می‌شود. فقط وقتی کار می‌کند که پیش‌بارگذاری راکت روشن و طول عمر کش بیشتر از صفر باشد.';
	}

	public function fields() {
		return array(
			'max_per_run' => array(
				'type'    => 'number',
				'label'   => 'حداکثر صفحه‌ای که در هر اجرا (هر ۱۰ دقیقه) تازه می‌شود',
				'default' => 200,
				'min'     => 20,
				'max'     => 1000,
			),
		);
	}

	public function notes() {
		$reason = $this->unavailable();
		if ( $reason ) {
			return $reason;
		}
		$state = get_option( self::STATE, array() );
		if ( ! is_array( $state ) || empty( $state['last_run'] ) ) {
			return 'هنوز اجرا نشده است (اولین اجرا حداکثر ۱۰ دقیقه بعد از فعال شدن).';
		}
		$lines   = array();
		$lines[] = sprintf( 'آخرین اجرا: %s (%s ثانیه) — طول عمر کش راکت: %s ساعت', wp_date( 'Y/m/d H:i', (int) $state['last_run'] ), number_format_i18n( (float) $state['seconds'], 1 ), number_format_i18n( (float) $state['lifespan'] / HOUR_IN_SECONDS, 1 ) );
		$lines[] = sprintf( 'صفحه‌های کش‌شده مهمان: %s (فایل دسکتاپ %s، موبایل %s)', number_format_i18n( (int) $state['urls'] ), number_format_i18n( (int) $state['desktop'] ), number_format_i18n( (int) $state['mobile'] ) );
		$lines[] = sprintf( 'سن کش نسبت به طول عمر: زیر ۲۵٪: %s | ۲۵ تا ۵۰٪: %s | ۵۰ تا ۷۵٪: %s | ۷۵ تا ۱۰۰٪: %s | بیشتر: %s', number_format_i18n( (int) $state['ages'][0] ), number_format_i18n( (int) $state['ages'][1] ), number_format_i18n( (int) $state['ages'][2] ), number_format_i18n( (int) $state['ages'][3] ), number_format_i18n( (int) $state['ages'][4] ) );
		$lines[] = sprintf( 'محصولات دارای کش: %s از %s (%s٪) — دسته‌های محصول دارای کش: %s از %s (%s٪)', number_format_i18n( (int) $state['products'] ), number_format_i18n( (int) $state['products_total'] ), number_format_i18n( $this->percent( $state['products'], $state['products_total'] ) ), number_format_i18n( (int) $state['cats'] ), number_format_i18n( (int) $state['cats_total'] ), number_format_i18n( $this->percent( $state['cats'], $state['cats_total'] ) ) );
		$lines[] = sprintf( 'در آخرین اجرا: %s صفحه برای ساخت دوباره فرستاده شد، صف پیش‌بارگذاری راکت: %s، فایل‌های قدیمی کاربران لاگین پاک‌شده: %s', number_format_i18n( (int) $state['refreshed'] ), number_format_i18n( (int) $state['pending'] ), number_format_i18n( (int) $state['user_files'] ) );
		return implode( ' • ', $lines );
	}

	private function percent( $part, $total ) {
		return (int) $total > 0 ? round( 100 * (int) $part / (int) $total ) : 0;
	}

	public function boot() {
		add_filter( 'cron_schedules', array( $this, 'schedules' ) );
		add_action( self::HOOK, array( $this, 'run' ) );
		add_action( 'admin_init', array( $this, 'ensure_schedule' ) );
		add_filter( 'rocket_automatic_cache_purge_urls', array( $this, 'skip_bulk_purge' ), 99 );
	}

	public function schedules( $schedules ) {
		$schedules[ self::SCHEDULE ] = array(
			'interval' => self::INTERVAL,
			'display'  => 'Tarh99 every 10 minutes',
		);
		return $schedules;
	}

	public function ensure_schedule() {
		if ( ! wp_next_scheduled( self::HOOK ) ) {
			wp_schedule_event( time() + MINUTE_IN_SECONDS, self::SCHEDULE, self::HOOK );
		}
	}

	public static function unschedule() {
		$timestamp = wp_next_scheduled( self::HOOK );
		while ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::HOOK );
			$timestamp = wp_next_scheduled( self::HOOK );
		}
	}

	private function lifespan() {
		$interval = (int) Tarh99_Perf_Env::rocket_option( 'purge_cron_interval' );
		if ( $interval <= 0 ) {
			return 0;
		}
		$unit = (string) Tarh99_Perf_Env::rocket_option( 'purge_cron_unit' );
		if ( '' === $unit || ! defined( $unit ) ) {
			return 10 * HOUR_IN_SECONDS;
		}
		return $interval * (int) constant( $unit );
	}

	private function unavailable() {
		if ( ! Tarh99_Perf_Env::rocket_active() || ! defined( 'WP_ROCKET_CACHE_PATH' ) || ! function_exists( 'get_rocket_parse_url' ) ) {
			return 'WP Rocket فعال نیست؛ این ماژول کاری نمی‌کند.';
		}
		if ( ! Tarh99_Perf_Env::rocket_option( 'manual_preload' ) ) {
			return 'پیش‌بارگذاری راکت (Preload) خاموش است؛ این ماژول کاری نمی‌کند و پاک‌سازی خود راکت مثل قبل انجام می‌شود.';
		}
		if ( $this->lifespan() < HOUR_IN_SECONDS ) {
			return 'طول عمر کش راکت صفر (نامحدود) یا کمتر از یک ساعت است؛ این ماژول کاری نمی‌کند.';
		}
		return '';
	}

	public function skip_bulk_purge( $urls ) {
		if ( '' !== $this->unavailable() ) {
			return $urls;
		}
		$state = get_option( self::STATE, array() );
		if ( is_array( $state ) && ! empty( $state['last_run'] ) && ( time() - (int) $state['last_run'] ) < 2 * HOUR_IN_SECONDS ) {
			return array();
		}
		return $urls;
	}

	private function home() {
		$url  = get_rocket_parse_url( home_url() );
		$host = (string) $url['host'];
		if ( apply_filters( 'rocket_url_no_dots', false ) ) {
			$host = str_replace( '.', '_', $host );
		}
		return array(
			'host' => $host,
			'path' => rtrim( (string) $url['path'], '/' ),
		);
	}

	private function time_left() {
		return microtime( true ) < $this->deadline;
	}

	private function scan( $root ) {
		$dirs = array();
		try {
			$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
			foreach ( $iterator as $item ) {
				if ( ! $this->time_left() ) {
					return null;
				}
				if ( ! $item->isFile() || ! preg_match( self::FILE, $item->getFilename() ) ) {
					continue;
				}
				$dir   = $item->getPath();
				$mtime = (int) $item->getMTime();
				if ( ! isset( $dirs[ $dir ] ) ) {
					$dirs[ $dir ] = array(
						'oldest' => $mtime,
						'files'  => array(),
					);
				}
				$dirs[ $dir ]['oldest']  = min( $dirs[ $dir ]['oldest'], $mtime );
				$dirs[ $dir ]['files'][] = $item->getFilename();
			}
		} catch ( Exception $e ) {
			return null;
		}
		return $dirs;
	}

	private function pending() {
		global $wpdb;
		$table = $wpdb->prefix . 'wpr_rocket_cache';
		if ( $table !== $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) ) ) {
			return 0;
		}
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'pending'" );
	}

	private function base_slug( $key, $fallback ) {
		$structure = function_exists( 'wc_get_permalink_structure' ) ? wc_get_permalink_structure() : array();
		$slug      = ( isset( $structure[ $key ] ) && '' !== $structure[ $key ] ) ? $structure[ $key ] : $fallback;
		$slug      = trim( (string) $slug, '/' );
		$slug      = trim( (string) preg_replace( '#%.*$#', '', $slug ), '/' );
		return '' === $slug ? '' : strtolower( implode( '/', array_map( 'rawurlencode', explode( '/', rawurldecode( $slug ) ) ) ) );
	}

	private function under( $relative, $slug ) {
		return '' !== $slug && 0 === strpos( $relative . '/', $slug . '/' ) && strlen( $relative ) > strlen( $slug ) && false === strpos( $relative, '#' ) && 1 !== preg_match( '#/page/\d+$#', $relative );
	}

	private function url_of( $dir, $base ) {
		$relative = substr( $dir, strlen( $base ) );
		if ( false !== strpos( $relative, '#' ) ) {
			return untrailingslashit( home_url() ) . preg_replace( '/#/', '?', $relative, 1 );
		}
		return untrailingslashit( home_url() ) . untrailingslashit( $relative ) . ( '/' === substr( (string) get_option( 'permalink_structure' ), -1 ) ? '/' : '' );
	}

	private function preloadable( $url ) {
		global $wp_rewrite;
		$url = user_trailingslashit( (string) strtok( $url, '?' ) );
		if ( apply_filters( 'rocket_preload_exclude', false, $url ) ) {
			return false;
		}
		$pagination = ( is_object( $wp_rewrite ) && ! empty( $wp_rewrite->pagination_base ) ) ? $wp_rewrite->pagination_base : 'page';
		foreach ( (array) apply_filters( 'rocket_preload_exclude_urls', array( '/' . $pagination . '/\d+' ), $url ) as $regex ) {
			if ( is_string( $regex ) && 1 === @preg_match( '@' . user_trailingslashit( $regex ) . '$@m', $url ) ) {
				return false;
			}
		}
		return true;
	}

	private function purge_users( $prefix, $lifespan ) {
		$deleted = 0;
		$limit   = time() - $lifespan;
		try {
			foreach ( new DirectoryIterator( WP_ROCKET_CACHE_PATH ) as $entry ) {
				if ( ! $this->time_left() ) {
					break;
				}
				if ( $entry->isDot() || ! $entry->isDir() || 0 !== strpos( $entry->getFilename(), $prefix ) ) {
					continue;
				}
				$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $entry->getPathname(), FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
				foreach ( $iterator as $item ) {
					if ( ! $this->time_left() ) {
						break;
					}
					if ( $item->isDir() ) {
						@rmdir( $item->getPathname() );
					} elseif ( preg_match( self::FILE, $item->getFilename() ) && (int) $item->getMTime() < $limit && @unlink( $item->getPathname() ) ) {
						++$deleted;
					}
				}
				@rmdir( $entry->getPathname() );
			}
		} catch ( Exception $e ) {
			return $deleted;
		}
		return $deleted;
	}

	public function run() {
		if ( '' !== $this->unavailable() ) {
			return;
		}
		$start          = microtime( true );
		$this->deadline = $start + 25;
		$lifespan       = $this->lifespan();
		$home           = $this->home();
		$root           = WP_ROCKET_CACHE_PATH . $home['host'];
		$base           = $root . $home['path'];
		$dirs           = is_dir( $base ) ? $this->scan( $base ) : array();
		if ( null === $dirs ) {
			return;
		}
		$now      = time();
		$ages     = array( 0, 0, 0, 0, 0 );
		$desktop  = 0;
		$mobile   = 0;
		$products = 0;
		$cats     = 0;
		$due      = array();
		$overdue  = array();
		$product  = $this->base_slug( 'product_rewrite_slug', 'product' );
		$category = $this->base_slug( 'category_rewrite_slug', 'product-category' );
		foreach ( $dirs as $dir => $info ) {
			$age = $now - $info['oldest'];
			++$ages[ max( 0, min( 4, (int) floor( 4 * $age / $lifespan ) ) ) ];
			foreach ( $info['files'] as $file ) {
				if ( false !== strpos( $file, '-mobile' ) ) {
					++$mobile;
				} else {
					++$desktop;
				}
			}
			$relative = trim( substr( $dir, strlen( $base ) ), '/' );
			if ( $this->under( $relative, $product ) ) {
				++$products;
			} elseif ( $this->under( $relative, $category ) ) {
				++$cats;
			}
			if ( $age > $lifespan ) {
				$overdue[ $dir ] = $age;
			} elseif ( $age >= 0.6 * $lifespan ) {
				$due[ $dir ] = $age;
			}
		}
		$pending = $this->pending();
		$max     = (int) $this->opt( 'max_per_run' );
		$quota   = (int) min( $max, max( 1, ceil( 1.2 * count( $dirs ) * self::INTERVAL / ( 0.4 * $lifespan ) ) ) );
		arsort( $due );
		$pick = array_keys( $overdue );
		if ( $pending < $max ) {
			$taken = 0;
			foreach ( array_keys( $due ) as $dir ) {
				if ( $taken >= $quota || ! $this->time_left() ) {
					break;
				}
				if ( $this->preloadable( $this->url_of( $dir, $base ) ) ) {
					$pick[] = $dir;
					++$taken;
				}
			}
		}
		$refreshed = array();
		foreach ( $pick as $dir ) {
			if ( ! $this->time_left() ) {
				break;
			}
			$removed = false;
			foreach ( $dirs[ $dir ]['files'] as $file ) {
				if ( @unlink( $dir . '/' . $file ) ) {
					$removed = true;
				}
			}
			if ( $removed ) {
				$refreshed[] = $dir;
			}
		}
		if ( $refreshed ) {
			do_action(
				'rocket_after_automatic_cache_purge',
				array(
					array(
						'home_url'  => untrailingslashit( home_url() ),
						'home_path' => $base,
						'logged_in' => false,
						'files'     => $refreshed,
					),
				),
				array(
					'lifespan'       => $lifespan,
					'file_age_limit' => $now - $lifespan,
				)
			);
		}
		$user_files = $this->purge_users( $home['host'] . '-', $lifespan );
		$counts     = wp_count_posts( 'product' );
		$cat_total  = wp_count_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
		update_option(
			self::STATE,
			array(
				'last_run'       => $now,
				'seconds'        => round( microtime( true ) - $start, 2 ),
				'lifespan'       => $lifespan,
				'urls'           => count( $dirs ),
				'desktop'        => $desktop,
				'mobile'         => $mobile,
				'ages'           => $ages,
				'products'       => $products,
				'products_total' => isset( $counts->publish ) ? (int) $counts->publish : 0,
				'cats'           => $cats,
				'cats_total'     => is_wp_error( $cat_total ) ? 0 : (int) $cat_total,
				'refreshed'      => count( $refreshed ),
				'pending'        => $pending,
				'user_files'     => $user_files,
			),
			false
		);
	}
}
