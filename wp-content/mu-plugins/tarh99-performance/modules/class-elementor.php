<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Elementor extends Tarh99_Perf_Module {

	public function id() {
		return 'elementor';
	}

	public function title() {
		return 'المنتور';
	}

	public function description() {
		return 'font-display: swap برای فونت‌هایی که المنتور لود می‌کند (متن هنگام لود فونت نامرئی نمی‌ماند)، کوتاه کردن متن تکراری توضیحات دسته/محصول در تنظیمات جاوااسکریپت المنتور و در صورت نیاز توقف لود Google Fonts المنتور.';
	}

	public function fields() {
		return array(
			'font_display' => array(
				'type'    => 'checkbox',
				'label'   => 'font-display: swap برای فونت‌های المنتور',
				'default' => 1,
			),
			'trim_excerpt' => array(
				'type'    => 'checkbox',
				'label'   => 'المنتور کل متن توضیحات دسته یا محصول را (تا ۱۳۰ کیلوبایت در دسته‌های دارای مقاله سئو) یک بار دیگر داخل کد جاوااسکریپت صفحه تکرار می‌کند که فقط برای متن دکمه‌های اشتراک‌گذاری است؛ این نسخه تکراری به خلاصه ۱۶۰ کاراکتری تبدیل شود. متن نمایش‌داده‌شده در صفحه دست نمی‌خورد.',
				'default' => 1,
			),
			'motion_hidden' => array(
				'type'    => 'checkbox',
				'label'   => 'افکت‌های حرکتی (Motion Effects) بخش‌هایی که در یک دستگاه مخفی هستند، در همان دستگاه اجرا نشوند (مثلاً بخش «حرکت پس‌زمینه با موس» صفحه اصلی که در موبایل مخفی است ولی محاسباتش در موبایل هم اجرا می‌شد). در دستگاه‌هایی که بخش دیده می‌شود هیچ چیز عوض نمی‌شود.',
				'default' => 1,
			),
			'carousel_preinit' => array(
				'type'    => 'checkbox',
				'label'   => 'اسلایدر Media Carousel قبل از اجرای جاوااسکریپت هم با همان تعداد اسلاید و فاصله نهایی (مثلاً ۴ تایی در دسکتاپ) نمایش داده شود، نه یک اسلاید تمام‌عرض که بعد ناگهان چند تایی می‌شود',
				'default' => 1,
			),
			'google_fonts' => array(
				'type'    => 'checkbox',
				'label'   => 'توقف لود Google Fonts توسط المنتور (فقط اگر در سورس صفحه fonts.googleapis.com دیده می‌شود و فونت سایت از قالب است)',
				'default' => 0,
			),
		);
	}

	public function boot() {
		if ( $this->opt( 'font_display' ) ) {
			add_filter( 'pre_option_elementor_font_display', array( $this, 'font_display' ) );
		}
		if ( $this->opt( 'google_fonts' ) ) {
			add_filter( 'elementor/frontend/print_google_fonts', array( $this, 'google_fonts' ) );
		}
		if ( $this->opt( 'carousel_preinit' ) ) {
			add_filter( 'elementor/frontend/the_content', array( $this, 'carousel_preinit' ), 21 );
		}
		if ( $this->opt( 'motion_hidden' ) ) {
			add_filter( 'elementor/frontend/the_content', array( $this, 'motion_hidden' ), 20 );
		}
		if ( $this->opt( 'trim_excerpt' ) ) {
			add_action( 'wp_print_scripts', array( $this, 'trim_excerpt' ), 1 );
			add_action( 'wp_print_footer_scripts', array( $this, 'trim_excerpt' ), 1 );
		}
	}

	public function motion_hidden( $content ) {
		if ( ! is_string( $content ) || false === strpos( $content, 'motion_fx' ) || false === strpos( $content, 'elementor-hidden-' ) || ! Tarh99_Perf_Env::front_ok() ) {
			return $content;
		}
		$out = preg_replace_callback( '#<[a-z][a-z0-9]*\b[^>]*\sdata-settings\s*=\s*"[^"]*motion_fx[^"]*"[^>]*>#i', array( $this, 'motion_tag' ), $content );
		return is_string( $out ) ? $out : $content;
	}

	public function motion_tag( $m ) {
		$tag = $m[0];
		if ( ! preg_match( '#\sclass\s*=\s*"([^"]*)"#i', $tag, $class ) || ! preg_match_all( '#(?:^|\s)elementor-hidden-([a-z_]+)(?=\s|$)#', $class[1], $hidden ) ) {
			return $tag;
		}
		if ( ! preg_match( '#\sdata-settings\s*=\s*"([^"]*)"#i', $tag, $attr ) ) {
			return $tag;
		}
		$settings = json_decode( html_entity_decode( $attr[1], ENT_QUOTES, 'UTF-8' ), true );
		if ( ! is_array( $settings ) ) {
			return $tag;
		}
		$devices = array_values( array_diff( $this->devices(), $hidden[1] ) );
		$changed = false;
		foreach ( array( 'motion_fx', 'background_motion_fx' ) as $prefix ) {
			$mouse  = isset( $settings[ $prefix . '_motion_fx_mouse' ] ) && 'yes' === $settings[ $prefix . '_motion_fx_mouse' ];
			$scroll = isset( $settings[ $prefix . '_motion_fx_scrolling' ] ) && 'yes' === $settings[ $prefix . '_motion_fx_scrolling' ];
			if ( ( ! $mouse && ! $scroll ) || isset( $settings[ $prefix . '_devices' ] ) ) {
				continue;
			}
			$settings[ $prefix . '_devices' ] = $devices;
			$changed                          = true;
		}
		if ( ! $changed ) {
			return $tag;
		}
		$json = wp_json_encode( $settings );
		return is_string( $json ) ? str_replace( $attr[0], ' data-settings="' . esc_attr( $json ) . '"', $tag ) : $tag;
	}

	public function carousel_preinit( $content ) {
		if ( ! is_string( $content ) || false === strpos( $content, 'media-carousel.default' ) || ! Tarh99_Perf_Env::front_ok() ) {
			return $content;
		}
		$breakpoints = $this->carousel_breakpoints();
		if ( null === $breakpoints || ! preg_match_all( '#<div\b[^>]*\sdata-widget_type\s*=\s*"media-carousel\.default"[^>]*>#i', $content, $tags, PREG_OFFSET_CAPTURE ) ) {
			return $content;
		}
		$side   = is_rtl() ? 'left' : 'right';
		$insert = array();
		$count  = count( $tags[0] );
		for ( $i = 0; $i < $count; $i++ ) {
			$tag   = $tags[0][ $i ][0];
			$start = $tags[0][ $i ][1];
			$end   = $i + 1 < $count ? $tags[0][ $i + 1 ][1] : strlen( $content );
			$css   = $this->carousel_css( $tag, substr_count( substr( $content, $start, $end - $start ), 'class="swiper-slide"' ), $breakpoints, $side );
			if ( '' !== $css ) {
				$insert[ $start ] = '<style class="tarh99-carousel-preinit">' . $css . '</style>';
			}
		}
		krsort( $insert );
		foreach ( $insert as $pos => $style ) {
			$content = substr_replace( $content, $style, $pos, 0 );
		}
		return $content;
	}

	private function carousel_breakpoints() {
		if ( ! class_exists( '\Elementor\Plugin' ) || ! isset( \Elementor\Plugin::$instance->breakpoints ) || ! is_object( \Elementor\Plugin::$instance->breakpoints ) || ! method_exists( \Elementor\Plugin::$instance->breakpoints, 'get_active_breakpoints' ) ) {
			return null;
		}
		$out = array();
		foreach ( (array) \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints() as $key => $breakpoint ) {
			if ( ! in_array( $key, array( 'mobile', 'tablet' ), true ) || ! is_object( $breakpoint ) || ! method_exists( $breakpoint, 'get_value' ) ) {
				return null;
			}
			$out[ $key ] = (int) $breakpoint->get_value();
		}
		return isset( $out['mobile'], $out['tablet'] ) && $out['mobile'] > 0 && $out['tablet'] > $out['mobile'] ? $out : null;
	}

	private function carousel_css( $tag, $slides, $breakpoints, $side ) {
		if ( $slides < 1 || ! preg_match( '#\sdata-id\s*=\s*"([a-z0-9]+)"#i', $tag, $id ) || ! preg_match( '#\sdata-settings\s*=\s*"([^"]*)"#i', $tag, $attr ) ) {
			return '';
		}
		$settings = json_decode( html_entity_decode( $attr[1], ENT_QUOTES, 'UTF-8' ), true );
		if ( ! is_array( $settings ) || ( isset( $settings['skin'] ) && 'carousel' !== $settings['skin'] ) || ( isset( $settings['effect'] ) && 'slide' !== $settings['effect'] ) ) {
			return '';
		}
		$defaults = array(
			'desktop' => 3,
			'tablet'  => 2,
			'mobile'  => 1,
		);
		$rules    = array();
		foreach ( $defaults as $device => $default ) {
			$suffix = 'desktop' === $device ? '' : '_' . $device;
			$view   = isset( $settings[ 'slides_per_view' . $suffix ] ) && (int) $settings[ 'slides_per_view' . $suffix ] > 0 ? (int) $settings[ 'slides_per_view' . $suffix ] : $default;
			$view   = max( 1, min( $slides, $view ) );
			$gap    = isset( $settings[ 'space_between' . $suffix ]['size'] ) && is_numeric( $settings[ 'space_between' . $suffix ]['size'] ) ? max( 0, (float) $settings[ 'space_between' . $suffix ]['size'] ) : 0;
			$rule   = '.elementor-element-' . $id[1] . ' .swiper:not(.swiper-initialized)>.swiper-wrapper>.swiper-slide{width:calc((100% - ' . round( $gap * ( $view - 1 ), 2 ) . 'px)/' . $view . ');margin-' . $side . ':' . round( $gap, 2 ) . 'px}';
			$rules[ $device ] = 'desktop' === $device ? $rule : '@media (max-width:' . (int) $breakpoints[ $device ] . 'px){' . $rule . '}';
		}
		$arrows = '';
		if ( 'left' === $side ) {
			$scope  = '.elementor-element-' . $id[1] . ' .swiper:not(.swiper-initialized)>';
			$arrows = $scope . '.elementor-swiper-button-prev{left:unset;right:10px}' . $scope . '.elementor-swiper-button-next{left:10px;right:unset}';
		}
		return $rules['desktop'] . $rules['tablet'] . $rules['mobile'] . $arrows;
	}

	private function devices() {
		$devices = array( 'desktop' );
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->breakpoints ) && is_object( \Elementor\Plugin::$instance->breakpoints ) && method_exists( \Elementor\Plugin::$instance->breakpoints, 'get_active_breakpoints' ) ) {
			$devices = array_merge( $devices, array_keys( (array) \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints() ) );
		} else {
			$devices = array_merge( $devices, array( 'tablet', 'mobile' ) );
		}
		return array_values( array_unique( $devices ) );
	}

	public function trim_excerpt() {
		if ( is_admin() || Tarh99_Perf_Env::bypass() || ! function_exists( 'wp_scripts' ) ) {
			return;
		}
		$scripts = wp_scripts();
		$before  = $scripts->get_data( 'elementor-frontend', 'before' );
		if ( ! is_array( $before ) ) {
			return;
		}
		$prefix  = 'var elementorFrontendConfig = ';
		$changed = false;
		foreach ( $before as $index => $code ) {
			if ( ! is_string( $code ) || 0 !== strpos( $code, $prefix ) ) {
				continue;
			}
			$json = rtrim( substr( $code, strlen( $prefix ) ) );
			if ( ';' !== substr( $json, -1 ) ) {
				continue;
			}
			$config = json_decode( substr( $json, 0, -1 ) );
			if ( ! is_object( $config ) || ! isset( $config->post ) || ! is_object( $config->post ) || ! isset( $config->post->excerpt ) || ! is_string( $config->post->excerpt ) ) {
				continue;
			}
			$excerpt = $config->post->excerpt;
			if ( strlen( $excerpt ) <= 400 ) {
				continue;
			}
			$old = '"excerpt":' . wp_json_encode( $excerpt );
			if ( 1 !== substr_count( $code, $old ) ) {
				continue;
			}
			$short = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $excerpt, true ), ENT_QUOTES, 'UTF-8' ) ) );
			$short = wp_html_excerpt( $short, 160, '…' );
			$new   = wp_json_encode( $short );
			if ( ! is_string( $new ) ) {
				continue;
			}
			$before[ $index ] = str_replace( $old, '"excerpt":' . $new, $code );
			$changed          = true;
		}
		if ( $changed ) {
			$scripts->add_data( 'elementor-frontend', 'before', $before );
		}
	}

	public function font_display( $value ) {
		if ( ( is_admin() && ! wp_doing_ajax() ) || Tarh99_Perf_Env::bypass() ) {
			return $value;
		}
		return 'swap';
	}

	public function google_fonts( $print ) {
		if ( Tarh99_Perf_Env::bypass() ) {
			return $print;
		}
		return false;
	}
}
