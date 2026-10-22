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
		if ( $this->opt( 'trim_excerpt' ) ) {
			add_action( 'wp_print_scripts', array( $this, 'trim_excerpt' ), 1 );
			add_action( 'wp_print_footer_scripts', array( $this, 'trim_excerpt' ), 1 );
		}
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
