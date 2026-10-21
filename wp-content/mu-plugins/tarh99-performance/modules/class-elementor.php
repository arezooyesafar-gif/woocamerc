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
		return 'font-display: swap برای فونت‌هایی که المنتور لود می‌کند (متن هنگام لود فونت نامرئی نمی‌ماند) و در صورت نیاز توقف لود Google Fonts المنتور.';
	}

	public function fields() {
		return array(
			'font_display' => array(
				'type'    => 'checkbox',
				'label'   => 'font-display: swap برای فونت‌های المنتور',
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
