<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Menu_Cache extends Tarh99_Perf_Module {

	const VERSION_OPT = 'tarh99_perf_menu_ver';
	const BUCKET      = 'menu_items';

	private $path   = array();
	private $serve  = array();
	private $count  = array();
	private $bumped = false;
	private $hooks  = array( 'wp_setup_nav_menu_item', 'get_term', 'get_product_cat', 'get_product_tag', 'get_category', 'get_post_tag', 'get_terms', 'term_link', 'post_link', 'post_type_link', 'page_link', 'attachment_link', 'post_type_archive_link', 'home_url', 'the_title', 'nav_menu_attr_title', 'nav_menu_description', 'get_post_metadata', 'wp_get_nav_menu_object' );

	public function id() {
		return 'menu_cache';
	}

	public function group() {
		return 'server';
	}

	public function title() {
		return 'نگه‌داشتن آیتم‌های منوهای بزرگ بین صفحه‌ها';
	}

	public function description() {
		return 'برای ساختن منوی ۲۶۶ آیتمی دسته‌بندی‌ها، وردپرس در هر صفحه بدون کش (کاربر لاگین، ساخت کش راکت) همه آیتم‌ها را از دیتابیس می‌خواند و آدرس و نام هر دسته را جدا حساب می‌کند؛ این بخش بیشتر زمان ساخت منو است. این ماژول نتیجه همین مرحله (فهرست آیتم‌ها با آدرس و عنوان) را در پوشه کش افزونه نگه می‌دارد و صفحه‌های بعدی از آن استفاده می‌کنند. ساختن HTML منو، کلاس‌های صفحه فعلی و همه فیلترهای قالب و افزونه‌ها مثل قبل در هر صفحه اجرا می‌شوند، پس خروجی منو همان است. با هر تغییر در منوها، دسته‌ها و برچسب‌ها، نوشته‌ها و برگه‌ها و محصولات، تنظیمات پیوند یکتا، یا فعال و غیرفعال کردن افزونه، این کش خودکار کنار گذاشته می‌شود.';
	}

	public function fields() {
		return array(
			'min_items' => array(
				'type'    => 'number',
				'label'   => 'فقط منوهایی که حداقل این تعداد آیتم دارند',
				'default' => 40,
				'min'     => 5,
				'max'     => 5000,
			),
			'hours'     => array(
				'type'    => 'number',
				'label'   => 'حداکثر عمر کش (ساعت)',
				'default' => 12,
				'min'     => 1,
				'max'     => 168,
			),
		);
	}

	public function notes() {
		return 'نسخه فعلی کش منو: ' . (int) get_option( self::VERSION_OPT, 1 );
	}

	public function boot() {
		add_filter( 'wp_get_nav_menu_object', array( $this, 'menu_object' ), PHP_INT_MAX, 2 );
		add_filter( 'wp_get_nav_menu_items', array( $this, 'items' ), PHP_INT_MIN, 3 );
		foreach ( array( 'wp_update_nav_menu', 'wp_update_nav_menu_item', 'wp_add_nav_menu_item', 'wp_delete_nav_menu', 'created_term', 'edited_term', 'delete_term', 'switch_theme', 'activated_plugin', 'deactivated_plugin', 'upgrader_process_complete', 'elementor/editor/after_save' ) as $hook ) {
			add_action( $hook, array( $this, 'bump' ), 99, 0 );
		}
		foreach ( array( 'save_post', 'deleted_post', 'trashed_post', 'untrashed_post' ) as $hook ) {
			add_action( $hook, array( $this, 'post_changed' ), 99, 1 );
		}
		add_action( 'set_object_terms', array( $this, 'terms_set' ), 99, 4 );
	}

	public function bump() {
		if ( $this->bumped ) {
			return;
		}
		$this->bumped = true;
		update_option( self::VERSION_OPT, (int) get_option( self::VERSION_OPT, 1 ) + 1, true );
	}

	public function post_changed( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'auto-draft' === $post->post_status || wp_is_post_revision( $post ) || wp_is_post_autosave( $post ) ) {
			return;
		}
		if ( 'nav_menu_item' === $post->post_type || ( 'attachment' !== $post->post_type && is_post_type_viewable( $post->post_type ) ) ) {
			$this->bump();
		}
	}

	public function terms_set( $object_id, $terms, $tt_ids, $taxonomy ) {
		if ( 'nav_menu' === $taxonomy ) {
			$this->bump();
		}
	}

	private function active() {
		return Tarh99_Perf_Env::front_ok() && ! is_admin() && ! is_customize_preview() && ! wp_doing_ajax();
	}

	private function caller_args() {
		$trace = debug_backtrace( 0, 10 );
		foreach ( $trace as $i => $frame ) {
			if ( isset( $frame['function'] ) && 'wp_get_nav_menu_items' === $frame['function'] && empty( $frame['class'] ) ) {
				if ( ! isset( $trace[ $i + 1 ]['function'] ) || 'wp_nav_menu' !== $trace[ $i + 1 ]['function'] || ! empty( $trace[ $i + 1 ]['class'] ) ) {
					return null;
				}
				return ( isset( $frame['args'][1] ) && is_array( $frame['args'][1] ) ) ? $frame['args'][1] : array();
			}
		}
		return null;
	}

	private function callback_name( $function ) {
		if ( is_string( $function ) ) {
			return $function;
		}
		if ( is_array( $function ) && isset( $function[0], $function[1] ) ) {
			return ( is_object( $function[0] ) ? get_class( $function[0] ) : (string) $function[0] ) . '::' . (string) $function[1];
		}
		if ( $function instanceof Closure ) {
			try {
				$reflection = new ReflectionFunction( $function );
				return 'closure:' . $reflection->getFileName() . ':' . $reflection->getStartLine();
			} catch ( Throwable $e ) {
				return 'closure';
			}
		}
		return is_object( $function ) ? get_class( $function ) : '?';
	}

	private function hooks_signature() {
		global $wp_filter;
		$out = array();
		foreach ( $this->hooks as $hook ) {
			if ( empty( $wp_filter[ $hook ] ) ) {
				continue;
			}
			foreach ( $wp_filter[ $hook ]->callbacks as $priority => $callbacks ) {
				foreach ( $callbacks as $callback ) {
					if ( is_array( $callback['function'] ) && $callback['function'][0] === $this ) {
						continue;
					}
					$out[] = $hook . '|' . $priority . '|' . $this->callback_name( $callback['function'] );
				}
			}
		}
		return $out;
	}

	private function key( $menu, $args ) {
		$user  = wp_get_current_user();
		$roles = ( $user && $user->exists() ) ? (array) $user->roles : array( 'guest' );
		sort( $roles );
		$options = array();
		foreach ( array( 'permalink_structure', 'category_base', 'tag_base', 'woocommerce_permalinks', 'show_on_front', 'page_on_front', 'page_for_posts', 'woocommerce_shop_page_id', 'home', 'siteurl' ) as $option ) {
			$options[ $option ] = get_option( $option );
		}
		return md5(
			serialize(
				array(
					(int) get_option( self::VERSION_OPT, 1 ),
					isset( $GLOBALS['wp_version'] ) ? $GLOBALS['wp_version'] : '',
					defined( 'WC_VERSION' ) ? WC_VERSION : '',
					TARH99_PERF_VERSION,
					(int) $menu->term_id,
					(int) $menu->term_taxonomy_id,
					(int) $menu->count,
					$args,
					is_ssl(),
					home_url( '/' ),
					determine_locale(),
					$roles,
					$options,
					$this->hooks_signature(),
				)
			)
		);
	}

	private function ttl() {
		return (int) $this->opt( 'hours' ) * HOUR_IN_SECONDS;
	}

	private function load( $key ) {
		$stored = Tarh99_Perf_Cache_Store::get( self::BUCKET, $key, $this->ttl() );
		if ( ! is_array( $stored ) || ! isset( $stored['z'], $stored['d'] ) || ! is_string( $stored['d'] ) ) {
			return null;
		}
		$data = $stored['d'];
		if ( $stored['z'] ) {
			$data = function_exists( 'gzinflate' ) ? @gzinflate( $data ) : false;
			if ( false === $data ) {
				return null;
			}
		}
		$rows = @unserialize( $data, array( 'allowed_classes' => false ) );
		if ( ! is_array( $rows ) || ! $rows ) {
			return null;
		}
		$items = array();
		foreach ( $rows as $index => $row ) {
			if ( ! is_array( $row ) ) {
				return null;
			}
			$items[ $index ] = new WP_Post( (object) $row );
		}
		return $items;
	}

	private function plain( $value ) {
		if ( is_object( $value ) || is_resource( $value ) ) {
			return false;
		}
		if ( is_array( $value ) ) {
			foreach ( $value as $item ) {
				if ( ! $this->plain( $item ) ) {
					return false;
				}
			}
		}
		return true;
	}

	private function save( $key, $items ) {
		$rows = array();
		foreach ( $items as $index => $item ) {
			if ( ! is_object( $item ) || 'WP_Post' !== get_class( $item ) ) {
				return;
			}
			$row = get_object_vars( $item );
			if ( ! $this->plain( $row ) ) {
				return;
			}
			$rows[ $index ] = $row;
		}
		$data = serialize( $rows );
		$zip  = function_exists( 'gzdeflate' );
		Tarh99_Perf_Cache_Store::set(
			self::BUCKET,
			$key,
			array(
				'z' => $zip,
				'd' => $zip ? gzdeflate( $data, 6 ) : $data,
			),
			$this->ttl()
		);
	}

	public function menu_object( $menu_obj, $menu ) {
		if ( ! is_object( $menu_obj ) || ! isset( $menu_obj->term_id, $menu_obj->term_taxonomy_id, $menu_obj->count ) || (int) $menu_obj->count < (int) $this->opt( 'min_items' ) || ! $this->active() ) {
			return $menu_obj;
		}
		$args = $this->caller_args();
		if ( null === $args ) {
			return $menu_obj;
		}
		$id                = (int) $menu_obj->term_id;
		$key               = $this->key( $menu_obj, $args );
		$this->path[ $id ] = $key;
		unset( $this->serve[ $id ] );
		$items = $this->load( $key );
		if ( null === $items ) {
			return $menu_obj;
		}
		$this->serve[ $id ] = $items;
		$this->count[ $id ] = $menu_obj->count;
		$clone              = clone $menu_obj;
		$clone->count       = 0;
		return $clone;
	}

	public function items( $items, $menu, $args ) {
		$id = ( is_object( $menu ) && isset( $menu->term_id ) ) ? (int) $menu->term_id : 0;
		if ( ! $id || ! isset( $this->path[ $id ] ) ) {
			return $items;
		}
		$key = $this->path[ $id ];
		unset( $this->path[ $id ] );
		if ( isset( $this->serve[ $id ] ) ) {
			$cached = $this->serve[ $id ];
			unset( $this->serve[ $id ] );
			$menu->count = $this->count[ $id ];
			return array() === $items ? $cached : $items;
		}
		if ( is_array( $items ) && count( $items ) >= (int) $this->opt( 'min_items' ) ) {
			$this->save( $key, $items );
		}
		return $items;
	}
}
