<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Image_Compress extends Tarh99_Perf_Module {

	const HOOK     = 'tarh99_perf_image_compress';
	const STATE    = 'tarh99_perf_ic_state';
	const META     = '_tarh99_ic';
	const SCHEDULE = 'tarh99_perf_5min';
	const BACKUP   = 'tarh99-originals';

	private $deadline = 0;

	public function id() {
		return 'image_compress';
	}

	public function group() {
		return 'stability';
	}

	public function title() {
		return 'فشرده‌سازی تصاویر کارت محصولات';
	}

	public function description() {
		return 'هر ۵ دقیقه چند تصویر را روی همین هاست با کیفیت تعیین‌شده دوباره ذخیره می‌کند (همان فرمت، همان ابعاد و همان آدرس) و فقط اگر حجم واقعاً کمتر شد جایگزین می‌کند. از هر فایل قبل از جایگزینی نسخه پشتیبان در uploads/tarh99-originals نگه داشته می‌شود و از تب «ابزارها و لاگ‌ها» با یک دکمه برمی‌گردد. تصویر اصلی آپلودشده دست نمی‌خورد؛ فقط اندازه‌های انتخاب‌شده.';
	}

	public function default_enabled() {
		return false;
	}

	public function risk() {
		return 'medium';
	}

	public function fields() {
		return array(
			'sizes'      => array(
				'type'    => 'multicheck',
				'label'   => 'اندازه‌هایی که فشرده شوند',
				'options' => array(
					'woocommerce_thumbnail'         => 'کارت محصولات (woocommerce_thumbnail)',
					'tps_card_420'                  => 'کارت ۴۲۰ پیکسلی (tps_card_420)',
					'woocommerce_single'            => 'تصویر صفحه محصول (woocommerce_single)',
					'woocommerce_gallery_thumbnail' => 'بندانگشتی گالری محصول',
					'medium'                        => 'medium',
					'medium_large'                  => 'medium_large',
					'large'                         => 'large',
				),
				'default' => array( 'woocommerce_thumbnail', 'tps_card_420' ),
			),
			'quality'    => array(
				'type'    => 'number',
				'label'   => 'کیفیت (۷۵ تا ۹۰؛ ۸۵ از نظر چشم با اصل یکسان است)',
				'default' => 85,
				'min'     => 75,
				'max'     => 90,
			),
			'min_saving' => array(
				'type'    => 'number',
				'label'   => 'حداقل کاهش حجم برای جایگزینی (درصد)',
				'default' => 15,
				'min'     => 5,
				'max'     => 60,
			),
			'batch'      => array(
				'type'    => 'number',
				'label'   => 'تعداد محصول/تصویر در هر نوبت',
				'default' => 20,
				'min'     => 5,
				'max'     => 100,
			),
			'time_limit' => array(
				'type'    => 'number',
				'label'   => 'حداکثر زمان هر نوبت (ثانیه)',
				'default' => 15,
				'min'     => 5,
				'max'     => 40,
			),
			'backup'     => array(
				'type'    => 'checkbox',
				'label'   => 'از هر فایل قبل از جایگزینی نسخه پشتیبان نگه داشته شود',
				'default' => 1,
			),
		);
	}

	public function notes() {
		$state = $this->state();
		$total = $this->total_attachments();
		$text  = sprintf(
			'پیشرفت: %s تصویر از %s بررسی شده، %s فایل فشرده شده، %s مگابایت کم شده%s',
			number_format_i18n( (int) $state['checked'] ),
			number_format_i18n( $total ),
			number_format_i18n( (int) $state['files'] ),
			number_format_i18n( (int) $state['saved'] / 1048576, 1 ),
			$state['last_run'] ? '، آخرین اجرا: ' . wp_date( 'Y/m/d H:i', (int) $state['last_run'] ) : ''
		);
		if ( ! $this->editor_supported() ) {
			$text .= ' — هشدار: کتابخانه GD/Imagick با پشتیبانی WebP روی هاست پیدا نشد.';
		}
		return $text;
	}

	public function boot() {
		add_filter( 'cron_schedules', array( $this, 'schedules' ) );
		add_action( self::HOOK, array( $this, 'run' ) );
		add_action( 'admin_init', array( $this, 'ensure_schedule' ) );
	}

	public function schedules( $schedules ) {
		$schedules[ self::SCHEDULE ] = array(
			'interval' => 5 * MINUTE_IN_SECONDS,
			'display'  => 'Tarh99 every 5 minutes',
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

	private function state() {
		$state = get_option( self::STATE, array() );
		return wp_parse_args(
			is_array( $state ) ? $state : array(),
			array(
				'last_id'  => 0,
				'checked'  => 0,
				'files'    => 0,
				'saved'    => 0,
				'errors'   => 0,
				'last_run' => 0,
			)
		);
	}

	private function save_state( $state ) {
		update_option( self::STATE, $state, false );
	}

	private function total_attachments() {
		global $wpdb;
		return (int) $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_mime_type IN ('image/jpeg','image/webp')" );
	}

	private function editor_supported() {
		return wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) && wp_image_editor_supports( array( 'mime_type' => 'image/jpeg' ) );
	}

	private function time_left() {
		return microtime( true ) < $this->deadline;
	}

	private function upload_base() {
		$uploads = wp_get_upload_dir();
		return trailingslashit( $uploads['basedir'] );
	}

	public function run() {
		if ( get_transient( 'tarh99_perf_ic_lock' ) || ! $this->editor_supported() ) {
			return;
		}
		set_transient( 'tarh99_perf_ic_lock', 1, 5 * MINUTE_IN_SECONDS );
		$this->deadline = microtime( true ) + (int) $this->opt( 'time_limit' );
		$state          = $this->state();
		try {
			global $wpdb;
			$ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_mime_type IN ('image/jpeg','image/webp') AND ID > %d ORDER BY ID ASC LIMIT %d",
					(int) $state['last_id'],
					(int) $this->opt( 'batch' )
				)
			);
			foreach ( $ids as $id ) {
				if ( ! $this->time_left() ) {
					break;
				}
				$result = $this->process_attachment( (int) $id );
				$state['files']  += $result['files'];
				$state['saved']  += $result['saved'];
				$state['errors'] += $result['errors'];
				$state['last_id'] = (int) $id;
				++$state['checked'];
			}
		} catch ( Throwable $e ) {
			++$state['errors'];
		}
		$state['last_run'] = time();
		$this->save_state( $state );
		delete_transient( 'tarh99_perf_ic_lock' );
	}

	private function process_attachment( $id ) {
		$result = array(
			'files'  => 0,
			'saved'  => 0,
			'errors' => 0,
		);
		$meta = wp_get_attachment_metadata( $id );
		$file = get_attached_file( $id );
		if ( ! is_array( $meta ) || empty( $meta['sizes'] ) || ! $file ) {
			return $result;
		}
		$done  = get_post_meta( $id, self::META, true );
		$done  = is_array( $done ) ? $done : array();
		$dir   = trailingslashit( dirname( $file ) );
		$sizes = $this->opt_list( 'sizes' );
		$seen  = array();
		foreach ( $sizes as $size ) {
			if ( empty( $meta['sizes'][ $size ]['file'] ) ) {
				continue;
			}
			$name = basename( (string) $meta['sizes'][ $size ]['file'] );
			if ( isset( $seen[ $name ] ) ) {
				continue;
			}
			$seen[ $name ] = true;
			$path          = $dir . $name;
			if ( ! is_file( $path ) || ! is_writable( $path ) ) {
				continue;
			}
			$mtime = (int) filemtime( $path );
			if ( isset( $done[ $name ] ) && (int) $done[ $name ] === $mtime ) {
				continue;
			}
			$outcome = $this->compress_file( $path );
			if ( null === $outcome ) {
				++$result['errors'];
				continue;
			}
			if ( $outcome > 0 ) {
				++$result['files'];
				$result['saved'] += $outcome;
			}
			clearstatcache( true, $path );
			$done[ $name ] = (int) filemtime( $path );
			if ( ! $this->time_left() ) {
				break;
			}
		}
		update_post_meta( $id, self::META, $done );
		return $result;
	}

	private function compress_file( $path ) {
		$type = wp_check_filetype( $path );
		$mime = isset( $type['type'] ) ? $type['type'] : '';
		if ( ! in_array( $mime, array( 'image/jpeg', 'image/webp' ), true ) ) {
			return 0;
		}
		$before = (int) filesize( $path );
		if ( $before < 6144 ) {
			return 0;
		}
		$editor = wp_get_image_editor( $path );
		if ( is_wp_error( $editor ) ) {
			return null;
		}
		$editor->set_quality( (int) $this->opt( 'quality' ) );
		$tmp   = $path . '.t99tmp.' . ( 'image/webp' === $mime ? 'webp' : 'jpg' );
		$saved = $editor->save( $tmp, $mime );
		if ( is_wp_error( $saved ) || empty( $saved['path'] ) || ! is_file( $saved['path'] ) ) {
			return null;
		}
		$tmp = $saved['path'];
		if ( ( isset( $saved['mime-type'] ) && $mime !== $saved['mime-type'] ) || $mime !== wp_get_image_mime( $tmp ) ) {
			@unlink( $tmp );
			return null;
		}
		$after = (int) filesize( $tmp );
		$dims  = @getimagesize( $tmp );
		$orig  = @getimagesize( $path );
		$same  = is_array( $dims ) && is_array( $orig ) && $dims[0] === $orig[0] && $dims[1] === $orig[1];
		if ( ! $same || $after <= 0 || $after > $before * ( 1 - (int) $this->opt( 'min_saving' ) / 100 ) ) {
			@unlink( $tmp );
			return 0;
		}
		if ( $this->opt( 'backup' ) && ! $this->backup( $path ) ) {
			@unlink( $tmp );
			return null;
		}
		if ( ! @rename( $tmp, $path ) ) {
			@unlink( $tmp );
			return null;
		}
		return $before - $after;
	}

	private function backup( $path ) {
		$base = $this->upload_base();
		if ( 0 !== strpos( $path, $base ) ) {
			return false;
		}
		$target = $base . self::BACKUP . '/' . substr( $path, strlen( $base ) );
		if ( is_file( $target ) ) {
			return true;
		}
		if ( ! wp_mkdir_p( dirname( $target ) ) ) {
			return false;
		}
		$root = $base . self::BACKUP . '/';
		if ( ! file_exists( $root . 'index.php' ) ) {
			@file_put_contents( $root . 'index.php', "<?php\n" );
		}
		return @copy( $path, $target );
	}

	public function restore( $seconds = 20 ) {
		$deadline = microtime( true ) + $seconds;
		$base     = $this->upload_base();
		$root     = $base . self::BACKUP . '/';
		$restored = 0;
		$left     = 0;
		if ( ! is_dir( $root ) ) {
			return array( 0, 0 );
		}
		try {
			$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
			foreach ( $iterator as $item ) {
				$path = $item->getPathname();
				if ( $item->isDir() ) {
					$entries = @scandir( $path );
					if ( is_array( $entries ) && count( $entries ) <= 2 ) {
						@rmdir( $path );
					}
					continue;
				}
				if ( 'index.php' === $item->getFilename() && dirname( $path ) . '/' === $root ) {
					continue;
				}
				if ( microtime( true ) > $deadline ) {
					++$left;
					continue;
				}
				$target = $base . substr( $path, strlen( $root ) );
				if ( @copy( $path, $target ) ) {
					@unlink( $path );
					++$restored;
				}
			}
		} catch ( Exception $e ) {
			return array( $restored, $left );
		}
		if ( 0 === $left ) {
			global $wpdb;
			$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => self::META ) );
			delete_option( self::STATE );
		}
		return array( $restored, $left );
	}
}
