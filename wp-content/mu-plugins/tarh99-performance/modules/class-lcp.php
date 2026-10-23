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
				'help'    => 'محدوده‌ها: all, front, shop, product, product_cat, product_archive, page, post, page:ID, url:/path. اگر آدرس کامل (https://...) بنویسید، در هد هم preload می‌شود. برای تصویر پس‌زمینه (مثل اسلاید کاروسل المنتور) می‌توانید بخشی از نام فایل یا متن aria-label همان المان را که PageSpeed نشان می‌دهد بنویسید؛ آدرس تصویر خودکار پیدا و در ابتدای هد preload می‌شود.',
			),
			'preload_custom'  => array(
				'type'    => 'checkbox',
				'label'   => 'برای قوانین دلخواه با آدرس کامل و تصاویر پس‌زمینه، تگ preload در هد اضافه شود',
				'default' => 1,
			),
			'rocket_atf_off'  => array(
				'type'    => 'checkbox',
				'label'   => 'وقتی تصویر پس‌زمینه دلخواه preload شد، preload خودکار LCP راکت (data-rocket-preload) در همان صفحه حذف شود تا با تصویر اشتباه رقابت نکند',
				'default' => 1,
			),
			'gallery_visible' => array(
				'type'    => 'checkbox',
				'label'   => 'گالری تصاویر صفحه محصول از اول دیده شود: قالب پوشه کل گالری را با display:none و ووکامرس با opacity:0 تا اجرای جاوااسکریپت (آخر بارگذاری صفحه) مخفی می‌کنند و تصویر اصلی محصول در موبایل ۵ تا ۷ ثانیه دیر ظاهر می‌شود. با این گزینه تصویر اول همان لحظه نمایش داده می‌شود و بقیه تصاویر و بندانگشتی‌ها مثل قبل بعد از اجرای اسکریپت می‌آیند؛ ظاهر نهایی تغییر نمی‌کند.',
				'default' => 1,
			),
			'rocket_stale_off' => array(
				'type'    => 'checkbox',
				'label'   => 'در فروشگاه، دسته‌ها و صفحه محصول، preload تصویری که راکت قبلاً ذخیره کرده ولی دیگر در صفحه وجود ندارد (مثلاً بعد از اضافه شدن محصول جدید به دسته) حذف شود تا با تصویر واقعی رقابت نکند',
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
		if ( $this->opt( 'gallery_visible' ) ) {
			add_action( 'wp_head', array( $this, 'print_gallery_style' ), 8 );
		}
		add_filter( 'rocket_buffer', array( $this, 'strip_rocket_preload' ), 100010 );
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

	public function strip_rocket_preload( $html ) {
		if ( ! is_string( $html ) || ! Tarh99_Perf_Env::front_ok() ) {
			return $html;
		}
		$html = $this->preload_background( $html );
		if ( $this->opt( 'rocket_stale_off' ) ) {
			$html = $this->strip_stale_rocket_preload( $html );
		}
		if ( ! $this->opt( 'rocket_atf_off' ) || false === strpos( $html, 'data-tarh99-lcp-bg' ) || false === strpos( $html, 'data-rocket-preload' ) ) {
			return $html;
		}
		$out = preg_replace( '#<link\b(?=[^>]*\sdata-rocket-preload\b)(?=[^>]*\sas\s*=\s*["\']?image\b)[^>]*>\s*#i', '', $html );
		return is_string( $out ) ? $out : $html;
	}

	private function strip_stale_rocket_preload( $html ) {
		if ( false === strpos( $html, 'data-rocket-preload' ) || ! function_exists( 'is_product' ) ) {
			return $html;
		}
		if ( ! ( is_shop() || is_product_taxonomy() || is_product() ) ) {
			return $html;
		}
		if ( ! preg_match_all( '#<link\b(?=[^>]*\sdata-rocket-preload\b)(?=[^>]*\sas\s*=\s*["\']?image\b)[^>]*>\s*#i', $html, $links ) ) {
			return $html;
		}
		foreach ( array_unique( $links[0] ) as $tag ) {
			if ( ! preg_match( '#\shref\s*=\s*(["\'])([^"\']+)\1#i', $tag, $href ) ) {
				continue;
			}
			$file = rawurldecode( basename( (string) wp_parse_url( html_entity_decode( $href[2], ENT_QUOTES ), PHP_URL_PATH ) ) );
			$stem = preg_replace( '/(-\d+x\d+)?(-scaled)?\.[a-z0-9]+$/i', '', $file );
			if ( ! is_string( $stem ) || strlen( $stem ) < 4 ) {
				continue;
			}
			$rest = str_replace( $tag, '', $html );
			if ( false !== strpos( $rest, $stem ) || false !== stripos( $rest, rawurlencode( $stem ) ) ) {
				continue;
			}
			$html = $rest;
		}
		return $html;
	}

	public function print_gallery_style() {
		if ( Tarh99_Perf_Env::bypass() || ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}
		echo '<style id="tarh99-product-gallery">div.woocommerce-product-gallery--with-images{display:block}.woocommerce-product-gallery{opacity:1!important}.woocommerce-product-gallery>.woocommerce-product-gallery__wrapper>.woocommerce-product-gallery__image:nth-child(n+2){display:none}</style>' . "\n";
	}

	public function filter_html( $html ) {
		if ( ! is_string( $html ) ) {
			return $html;
		}
		$html = $this->preload_background( $html );
		if ( false === stripos( $html, '<img' ) ) {
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

	private function preload_background( $html ) {
		if ( ! $this->opt( 'preload_custom' ) || false !== strpos( $html, 'data-tarh99-lcp-bg' ) || false === stripos( $html, 'background' ) ) {
			return $html;
		}
		$fragments = array();
		foreach ( $this->active_rules() as $rule ) {
			$fragment = trim( html_entity_decode( (string) $rule['fragment'], ENT_QUOTES, 'UTF-8' ) );
			if ( '' !== $fragment && 0 !== strpos( $fragment, 'http' ) ) {
				$fragments[] = $fragment;
			}
		}
		if ( empty( $fragments ) || ! preg_match_all( '#<[a-zA-Z][a-zA-Z0-9-]*\s[^>]*(?:background|data-bg)[^>]*>#i', $html, $tags ) ) {
			return $html;
		}
		foreach ( $tags[0] as $tag ) {
			$decoded = html_entity_decode( $tag, ENT_QUOTES, 'UTF-8' );
			$url     = '';
			if ( preg_match( '/background(?:-image)?\s*:[^;>]*?url\(\s*["\']?([^"\')]+)["\']?\s*\)/i', $decoded, $m ) ) {
				$url = trim( $m[1] );
			} elseif ( preg_match( '/\sdata-(?:background|bg)\s*=\s*["\']([^"\']+)["\']/i', $decoded, $m ) ) {
				$url = trim( $m[1] );
				if ( preg_match( '/url\(\s*["\']?([^"\')]+)/i', $url, $inner ) ) {
					$url = trim( $inner[1] );
				}
			}
			if ( '' === $url || 0 === strpos( $url, 'data:' ) ) {
				continue;
			}
			$haystack = $decoded . ' ' . rawurldecode( $url );
			foreach ( $fragments as $fragment ) {
				if ( false === stripos( $haystack, $fragment ) && false === stripos( $haystack, rawurldecode( $fragment ) ) ) {
					continue;
				}
				$link = '<link rel="preload" as="image" href="' . esc_attr( $url ) . '" fetchpriority="high" data-tarh99-lcp-bg>';
				$anchor = preg_match( '#<meta\s+charset\s*=[^>]*>#i', $html ) ? '#<meta\s+charset\s*=[^>]*>#i' : '#<head\b[^>]*>#i';
				$out    = preg_replace( $anchor, '$0' . "\n" . str_replace( '$', '\\$', $link ), $html, 1, $count );
				if ( ! is_string( $out ) || 1 !== $count ) {
					return $html;
				}
				return $out;
			}
		}
		return $html;
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
