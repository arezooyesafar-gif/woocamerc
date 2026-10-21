<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Lcp extends Tarh99_Perf_Module {

	private $product_done = false;
	private $archive_count = 0;
	private $rules = null;

	public function id() {
		return 'lcp';
	}

	public function title() {
		return 'اولویت تصویر LCP';
	}

	public function description() {
		return 'حذف lazy اجباری قالب از تصاویر، بارگذاری فوری (eager) و fetchpriority=high برای تصویر اصلی محصول، چند تصویر اول آرشیو و تصاویری که خودتان مشخص می‌کنید؛ همراه با استثنا از LazyLoad راکت. اگر «Optimized Image Loading» المنتور روشن باشد، تعیین fetchpriority به المنتور سپرده می‌شود تا ویژگی تکراری ساخته نشود. ظاهر تغییر نمی‌کند، فقط ترتیب دانلود.';
	}

	public function fields() {
		return array(
			'pooshe_lazy_fix' => array(
				'type'    => 'checkbox',
				'label'   => 'حذف lazy اجباری قالب پوشه از همه تصاویر (وردپرس و راکت خودشان تصمیم می‌گیرند)',
				'default' => 1,
			),
			'product_main'    => array(
				'type'    => 'checkbox',
				'label'   => 'تصویر اصلی صفحه محصول: eager + fetchpriority=high',
				'default' => 1,
			),
			'archive_eager'   => array(
				'type'    => 'number',
				'label'   => 'تعداد تصاویر اول فروشگاه/دسته‌بندی که lazy نشوند',
				'default' => 2,
				'min'     => 0,
				'max'     => 8,
			),
			'archive_high'    => array(
				'type'    => 'checkbox',
				'label'   => 'به اولین تصویر آرشیو fetchpriority=high هم بده (فقط اگر PageSpeed آن را LCP نشان داد)',
				'default' => 0,
			),
			'logo_fragment'   => array(
				'type'    => 'text',
				'label'   => 'بخشی از نام فایل لوگوی هدر (eager و استثنا از lazy راکت)',
				'default' => 'Tarh99.com-logo-original',
			),
			'custom_rules'    => array(
				'type'    => 'textarea',
				'label'   => 'تصاویر LCP دلخواه — هر خط: محدوده | بخشی از آدرس تصویر. مثال: front | banner-mobile.webp',
				'default' => '',
				'help'    => 'محدوده‌ها: all, front, shop, product, product_cat, product_archive, page, post, page:ID, url:/path. اگر آدرس کامل (https://...) بنویسید، در هد هم preload می‌شود.',
			),
			'preload_custom'  => array(
				'type'    => 'checkbox',
				'label'   => 'برای قوانین دلخواه با آدرس کامل، تگ preload در هد اضافه شود',
				'default' => 1,
			),
		);
	}

	public function boot() {
		if ( $this->opt( 'pooshe_lazy_fix' ) ) {
			add_action( 'init', array( $this, 'unhook_pooshe_lazy' ), 1 );
		}
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'attachment_attributes' ), 40, 3 );
		if ( $this->opt( 'product_main' ) ) {
			add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'gallery_html' ), 99, 2 );
		}
		add_filter( 'rocket_lazyload_excluded_src', array( $this, 'rocket_excluded_src' ) );
		add_filter( 'rocket_lazyload_excluded_attributes', array( $this, 'rocket_excluded_attributes' ) );
		add_action( 'template_redirect', array( $this, 'maybe_buffer' ), 2 );
		add_action( 'wp_head', array( $this, 'print_preload' ), 1 );
	}

	public function unhook_pooshe_lazy() {
		if ( Tarh99_Perf_Env::tps( 'pooshe_lazy_fix' ) || Tarh99_Perf_Env::bypass() ) {
			return;
		}
		remove_filter( 'wp_get_attachment_image_attributes', 'pooshe_lazy_loading_attributes', 10 );
		remove_filter( 'the_post_thumbnail', 'pooshe_lazy_loading_post_thumbnail', 10 );
		remove_filter( 'the_content', 'pooshe_lazy_loading_content_images', 100 );
	}

	private function custom_rules() {
		if ( null !== $this->rules ) {
			return $this->rules;
		}
		$this->rules = array();
		foreach ( $this->opt_lines( 'custom_rules' ) as $line ) {
			$parts = array_map( 'trim', explode( '|', $line, 2 ) );
			if ( 2 !== count( $parts ) || '' === $parts[1] ) {
				continue;
			}
			$this->rules[] = array(
				'scope'    => $parts[0],
				'fragment' => $parts[1],
			);
		}
		return $this->rules;
	}

	private function active_rules() {
		$out = array();
		foreach ( $this->custom_rules() as $rule ) {
			if ( Tarh99_Perf_Env::scope_matches( $rule['scope'] ) ) {
				$out[] = $rule;
			}
		}
		return $out;
	}

	private function product_handled_by_tps() {
		return Tarh99_Perf_Env::tps( 'product_lcp' );
	}

	public function attachment_attributes( $attr, $attachment, $size ) {
		if ( ! is_array( $attr ) || ! Tarh99_Perf_Env::front_ok() || ! did_action( 'wp' ) ) {
			return $attr;
		}
		$classes = isset( $attr['class'] ) ? ' ' . (string) $attr['class'] . ' ' : '';
		$src     = isset( $attr['src'] ) ? (string) $attr['src'] : '';
		$thumb   = false !== strpos( $classes, 'attachment-woocommerce_thumbnail' ) || false !== strpos( $classes, 'size-woocommerce_thumbnail' );
		$wc      = Tarh99_Perf_Env::wc();

		$logo = trim( (string) $this->opt( 'logo_fragment' ) );
		if ( '' !== $logo && '' !== $src && false !== stripos( $src, $logo ) ) {
			$attr['loading'] = 'eager';
			return $attr;
		}

		if ( $this->opt( 'product_main' ) && ! $this->product_done && ! $this->product_handled_by_tps() && $wc && is_product() && ! $thumb && false !== strpos( $classes, ' wp-post-image ' ) ) {
			$this->product_done = true;
			$attr['loading']    = 'eager';
			$attr['decoding']   = 'async';
			if ( ! Tarh99_Perf_Env::elementor_image_optimization() ) {
				$attr['fetchpriority'] = 'high';
			}
			return $attr;
		}

		$limit = (int) $this->opt( 'archive_eager' );
		if ( $limit > 0 && $thumb && $wc && ( is_shop() || is_product_taxonomy() ) && in_the_loop() && $this->archive_count < $limit ) {
			++$this->archive_count;
			$attr['loading'] = 'eager';
			if ( 1 === $this->archive_count && $this->opt( 'archive_high' ) && ! Tarh99_Perf_Env::elementor_image_optimization() ) {
				$attr['fetchpriority'] = 'high';
			}
		}
		return $attr;
	}

	public function gallery_html( $html, $attachment_id = 0 ) {
		if ( $this->product_handled_by_tps() || ! Tarh99_Perf_Env::front_ok() || ! is_string( $html ) || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
			return $html;
		}
		if ( ! Tarh99_Perf_Env::wc() || ! is_product() || false === strpos( $html, 'wp-post-image' ) ) {
			return $html;
		}
		$processor = new WP_HTML_Tag_Processor( $html );
		while ( $processor->next_tag( array( 'tag_name' => 'IMG' ) ) ) {
			$classes = ' ' . (string) $processor->get_attribute( 'class' ) . ' ';
			if ( false === strpos( $classes, ' wp-post-image ' ) ) {
				continue;
			}
			$processor->set_attribute( 'loading', 'eager' );
			if ( ! Tarh99_Perf_Env::elementor_image_optimization() ) {
				$processor->set_attribute( 'fetchpriority', 'high' );
			}
			$this->product_done = true;
			return $processor->get_updated_html();
		}
		return $html;
	}

	public function rocket_excluded_src( $excluded ) {
		if ( ! is_array( $excluded ) ) {
			$excluded = array();
		}
		$logo = trim( (string) $this->opt( 'logo_fragment' ) );
		if ( '' !== $logo ) {
			$excluded[] = $logo;
		}
		foreach ( $this->custom_rules() as $rule ) {
			$excluded[] = $this->fragment_key( $rule['fragment'] );
		}
		return array_values( array_unique( array_filter( $excluded ) ) );
	}

	public function rocket_excluded_attributes( $attributes ) {
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}
		$attributes[] = 'fetchpriority="high"';
		$attributes[] = 'data-tarh99-lcp';
		return array_values( array_unique( $attributes ) );
	}

	private function fragment_key( $fragment ) {
		$fragment = trim( (string) $fragment );
		if ( 0 === strpos( $fragment, 'http' ) || 0 === strpos( $fragment, '//' ) ) {
			$path = (string) wp_parse_url( $fragment, PHP_URL_PATH );
			return '' !== $path ? wp_basename( $path ) : $fragment;
		}
		return $fragment;
	}

	public function maybe_buffer() {
		if ( ! Tarh99_Perf_Env::front_ok() || ! Tarh99_Perf_Env::is_get() || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
			return;
		}
		if ( Tarh99_Perf_Env::wc() && ( is_cart() || is_checkout() ) ) {
			return;
		}
		if ( empty( $this->active_rules() ) ) {
			return;
		}
		ob_start( array( $this, 'filter_html' ) );
	}

	public function filter_html( $html ) {
		if ( ! is_string( $html ) || false === stripos( $html, '<img' ) ) {
			return $html;
		}
		$fragments = array();
		foreach ( $this->active_rules() as $rule ) {
			$key = $this->fragment_key( $rule['fragment'] );
			if ( '' !== $key && false !== stripos( $html, $key ) ) {
				$fragments[] = $key;
			}
		}
		if ( empty( $fragments ) ) {
			return $html;
		}
		$processor = new WP_HTML_Tag_Processor( $html );
		$changed   = false;
		$seen      = array();
		while ( $processor->next_tag( array( 'tag_name' => 'IMG' ) ) ) {
			$haystack = (string) $processor->get_attribute( 'src' ) . ' ' . (string) $processor->get_attribute( 'data-src' ) . ' ' . (string) $processor->get_attribute( 'data-lazy-src' ) . ' ' . (string) $processor->get_attribute( 'srcset' );
			foreach ( $fragments as $key ) {
				if ( isset( $seen[ $key ] ) || false === stripos( $haystack, $key ) ) {
					continue;
				}
				$seen[ $key ] = true;
				$processor->set_attribute( 'loading', 'eager' );
				$processor->set_attribute( 'fetchpriority', 'high' );
				$processor->set_attribute( 'data-tarh99-lcp', '1' );
				$changed = true;
				break;
			}
			if ( count( $seen ) === count( $fragments ) ) {
				break;
			}
		}
		return $changed ? $processor->get_updated_html() : $html;
	}

	public function print_preload() {
		if ( ! $this->opt( 'preload_custom' ) || ! Tarh99_Perf_Env::front_ok() ) {
			return;
		}
		$printed = 0;
		foreach ( $this->active_rules() as $rule ) {
			$fragment = trim( $rule['fragment'] );
			if ( 0 !== strpos( $fragment, 'http' ) || $printed >= 1 ) {
				continue;
			}
			printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n", esc_url( $fragment ) );
			++$printed;
		}
	}
}
