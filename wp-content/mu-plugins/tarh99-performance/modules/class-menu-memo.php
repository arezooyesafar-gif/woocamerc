<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Menu_Memo extends Tarh99_Perf_Module {

	private $memo = array();
	private $served = array();
	private $ctx = array();
	private $hooks = array( 'wp_nav_menu_args', 'wp_get_nav_menu_items', 'wp_setup_nav_menu_item', 'wp_nav_menu_objects', 'wp_nav_menu_items', 'walker_nav_menu_start_el', 'nav_menu_css_class', 'nav_menu_item_id', 'nav_menu_link_attributes', 'nav_menu_item_title', 'nav_menu_submenu_css_class', 'nav_menu_item_args', 'nav_menu_attr_title', 'nav_menu_description', 'the_title', 'wp_nav_menu' );

	public function id() {
		return 'menu_memo';
	}

	public function group() {
		return 'server';
	}

	public function title() {
		return 'یک بار ساخته شدن منوهای تکراری در هر صفحه';
	}

	public function description() {
		return 'ابزارک منوی موبایل و تبلت قالب پوشه دو بار در هر صفحه آمده و هر بار کل منوی ۲۶۶ آیتمی دسته‌بندی‌ها را از اول می‌سازد. طبق جدول صفحات کند، این دو بار ساختن روی سرور ۰٫۳۵ تا ۰٫۴۶ ثانیه از هر صفحه بدون کش (کاربر لاگین، سبد، پرداخت، ساخت کش راکت) را می‌گیرد. این ماژول منو را یک بار می‌سازد و نسخه دوم را از همان، با همان تغییرهایی که خود وردپرس در نسخه دوم یک منو می‌دهد (شناسه یکتای منو با پسوند و حذف شناسه آیتم‌های تکراری)، می‌سازد. فقط وقتی استفاده می‌شود که تنظیمات و فیلترهای منو در هر دو بار یکسان باشند؛ در غیر این صورت منو مثل قبل ساخته می‌شود. بین صفحات چیزی ذخیره نمی‌شود.';
	}

	public function boot() {
		add_filter( 'pre_wp_nav_menu', array( $this, 'pre' ), 1000, 2 );
		add_filter( 'wp_nav_menu', array( $this, 'store' ), PHP_INT_MAX - 1, 2 );
	}

	private function active() {
		return Tarh99_Perf_Env::front_ok() && function_exists( '_nav_menu_item_id_use_once' ) && function_exists( 'wp_nav_menu' );
	}

	private function statics( $function, $name ) {
		try {
			$vars = ( new ReflectionFunction( $function ) )->getStaticVariables();
		} catch ( Throwable $e ) {
			return null;
		}
		return ( isset( $vars[ $name ] ) && is_array( $vars[ $name ] ) ) ? $vars[ $name ] : ( array_key_exists( $name, $vars ) ? array() : null );
	}

	private function callback_name( $function ) {
		if ( is_string( $function ) ) {
			return $function;
		}
		if ( is_array( $function ) && isset( $function[0], $function[1] ) ) {
			return ( is_object( $function[0] ) ? get_class( $function[0] ) . '#' . spl_object_id( $function[0] ) : (string) $function[0] ) . '::' . (string) $function[1];
		}
		if ( $function instanceof Closure ) {
			return 'closure#' . spl_object_id( $function );
		}
		return is_object( $function ) ? get_class( $function ) . '#' . spl_object_id( $function ) : '?';
	}

	private function hooks_signature( $slug ) {
		global $wp_filter;
		$out = array();
		foreach ( array_merge( $this->hooks, array( 'wp_nav_menu_' . $slug . '_items' ) ) as $hook ) {
			if ( empty( $wp_filter[ $hook ] ) ) {
				continue;
			}
			foreach ( $wp_filter[ $hook ]->callbacks as $priority => $callbacks ) {
				foreach ( $callbacks as $callback ) {
					if ( is_array( $callback['function'] ) && $callback['function'][0] === $this ) {
						continue;
					}
					$out[] = $hook . '|' . $priority . '|' . $this->callback_name( $callback['function'] ) . '|' . (int) $callback['accepted_args'];
				}
			}
		}
		return $out;
	}

	private function id_filters_supported() {
		global $wp_filter;
		if ( empty( $wp_filter['nav_menu_item_id'] ) ) {
			return true;
		}
		foreach ( $wp_filter['nav_menu_item_id']->callbacks as $callbacks ) {
			foreach ( $callbacks as $callback ) {
				if ( ! is_string( $callback['function'] ) || ! in_array( $callback['function'], array( '_nav_menu_item_id_use_once', '__return_empty_string' ), true ) ) {
					return false;
				}
			}
		}
		return true;
	}

	private function key( $args, $menu ) {
		$data = array();
		foreach ( get_object_vars( $args ) as $name => $value ) {
			if ( 'echo' === $name || 'menu' === $name ) {
				continue;
			}
			if ( is_object( $value ) ) {
				$value = 'object:' . get_class( $value );
			}
			$data[ $name ] = $value;
		}
		ksort( $data );
		return md5( serialize( array( (int) $menu->term_id, $data, $this->hooks_signature( $menu->slug ) ) ) );
	}

	private function eligible( $args ) {
		return is_object( $args )
			&& empty( $args->depth )
			&& isset( $args->items_wrap ) && '<ul id="%1$s" class="%2$s">%3$s</ul>' === $args->items_wrap
			&& $this->id_filters_supported();
	}

	private function menu( $args ) {
		$menu = wp_get_nav_menu_object( $args->menu );
		if ( ! $menu && ! empty( $args->theme_location ) ) {
			$locations = get_nav_menu_locations();
			if ( isset( $locations[ $args->theme_location ] ) ) {
				$menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );
			}
		}
		return ( $menu && ! is_wp_error( $menu ) && isset( $menu->term_id, $menu->slug ) ) ? $menu : null;
	}

	private function next_id( $wrap_id, $taken ) {
		while ( in_array( $wrap_id, $taken, true ) ) {
			if ( preg_match( '#-(\d+)$#', $wrap_id, $matches ) ) {
				$wrap_id = preg_replace( '#-(\d+)$#', '-' . ++$matches[1], $wrap_id );
			} else {
				$wrap_id = $wrap_id . '-1';
			}
		}
		return $wrap_id;
	}

	private function replace_wrap_id( $html, $from, $to ) {
		$search = '<ul id="' . esc_attr( $from ) . '" class="';
		$pos    = strpos( $html, $search );
		if ( false === $pos ) {
			return null;
		}
		return substr_replace( $html, '<ul id="' . esc_attr( $to ) . '" class="', $pos, strlen( $search ) );
	}

	public function pre( $pre, $args ) {
		if ( null !== $pre || ! $this->eligible( $args ) || ! $this->active() ) {
			return $pre;
		}
		$menu = $this->menu( $args );
		if ( ! $menu ) {
			return $pre;
		}
		$key = $this->key( $args, $menu );
		if ( ! isset( $this->memo[ $key ] ) ) {
			$this->ctx[ spl_object_id( $args ) ] = array( $key, $menu->slug );
			return $pre;
		}
		$memo  = $this->memo[ $key ];
		$used  = $this->statics( '_nav_menu_item_id_use_once', '_used_ids' );
		$slugs = $this->statics( 'wp_nav_menu', 'menu_id_slugs' );
		if ( null === $used || null === $slugs ) {
			return $pre;
		}
		foreach ( $memo['items'] as $item_id ) {
			if ( ! in_array( $item_id, $used, true ) ) {
				return $pre;
			}
		}
		$wrap = empty( $args->menu_id ) ? $this->next_id( 'menu-' . $menu->slug, array_merge( $slugs, $this->served ) ) : (string) $args->menu_id;
		$html = $memo['html'];
		if ( $memo['items'] ) {
			$html = preg_replace( '# id="menu-item-(?:' . implode( '|', array_map( 'intval', $memo['items'] ) ) . ')"#', '', $html );
			if ( ! is_string( $html ) ) {
				return $pre;
			}
		}
		if ( $wrap !== $memo['wrap'] ) {
			$html = $this->replace_wrap_id( $html, $memo['wrap'], $wrap );
			if ( null === $html ) {
				return $pre;
			}
		}
		$this->served[] = $wrap;
		return $html;
	}

	public function store( $html, $args ) {
		if ( ! is_object( $args ) ) {
			return $html;
		}
		$oid = spl_object_id( $args );
		if ( ! isset( $this->ctx[ $oid ] ) ) {
			return $html;
		}
		list( $key, $slug ) = $this->ctx[ $oid ];
		unset( $this->ctx[ $oid ] );
		if ( ! is_string( $html ) || '' === $html || ! preg_match( '#<ul id="([^"]*)" class="#', $html, $m ) ) {
			return $html;
		}
		$wrap = html_entity_decode( $m[1], ENT_QUOTES, 'UTF-8' );
		if ( empty( $args->menu_id ) && in_array( $wrap, $this->served, true ) ) {
			$slugs = $this->statics( 'wp_nav_menu', 'menu_id_slugs' );
			if ( null === $slugs ) {
				return $html;
			}
			$fixed = $this->next_id( 'menu-' . $slug, array_merge( array_diff( $slugs, array( $wrap ) ), $this->served ) );
			$new   = $this->replace_wrap_id( $html, $wrap, $fixed );
			if ( null === $new ) {
				return $html;
			}
			$this->served[] = $fixed;
			$html           = $new;
			$wrap           = $fixed;
		}
		$items = preg_match_all( '# id="menu-item-(\d+)"#', $html, $ids ) ? array_map( 'intval', $ids[1] ) : array();
		$this->memo[ $key ] = array(
			'html'  => $html,
			'wrap'  => $wrap,
			'items' => $items,
		);
		return $html;
	}
}
