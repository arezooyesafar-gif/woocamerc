<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Fonts extends Tarh99_Perf_Module {

	private $printed = false;

	public function id() {
		return 'fonts';
	}

	public function title() {
		return 'پیش‌بارگذاری هوشمند فونت';
	}

	public function description() {
		return 'سه preload ثابت قالب (۱۰۰KB) حذف و فقط وزن‌های اصلی فونت فعال قالب preload می‌شوند تا با تصویر LCP رقابت نکنند. فونت‌ها خودشان font-display: swap دارند و ظاهر تغییر نمی‌کند.';
	}

	public function fields() {
		return array(
			'replace_theme' => array(
				'type'    => 'checkbox',
				'label'   => 'حذف preload سه‌تایی functions.php قالب (pooshe_preload_yekanbakh_fonts)',
				'default' => 1,
			),
			'weights'       => array(
				'type'    => 'multicheck',
				'label'   => 'وزن‌هایی که preload شوند',
				'options' => array(
					'regular'  => 'Regular (400)',
					'semibold' => 'SemiBold/Medium',
					'bold'     => 'Bold (700)',
				),
				'default' => array( 'regular', 'bold' ),
			),
			'extra'         => array(
				'type'    => 'textarea',
				'label'   => 'فونت‌های اضافی برای preload (هر خط یک آدرس کامل یا مسیر از ریشه سایت، فقط woff2)',
				'default' => '',
			),
			'skip_rocket'   => array(
				'type'    => 'checkbox',
				'label'   => 'اگر همان فونت در «Preload Fonts» راکت ثبت شده، دوباره preload نکن',
				'default' => 1,
			),
		);
	}

	public function boot() {
		add_action( 'template_redirect', array( $this, 'unhook_theme' ), 1 );
		add_action( 'wp_head', array( $this, 'print_preloads' ), 2 );
	}

	public function unhook_theme() {
		if ( ! Tarh99_Perf_Env::front_ok() || ! $this->opt( 'replace_theme' ) ) {
			return;
		}
		remove_action( 'wp_head', 'pooshe_preload_yekanbakh_fonts', 1 );
	}

	private function font_map() {
		return array(
			'pooshe-yekanbakh' => array(
				'dir'      => '/dist/font/YekanBakh/fonts/woff2/',
				'regular'  => 'YekanBakh-Regular.woff2',
				'semibold' => 'YekanBakh-SemiBold.woff2',
				'bold'     => 'YekanBakh-Bold.woff2',
			),
			'pooshe-yekan'     => array(
				'dir'      => '/dist/font/IRANYekan/fonts/woff2/',
				'regular'  => 'IRANYekanWebRegular.woff2',
				'semibold' => 'IRANYekanWebMedium.woff2',
				'bold'     => 'IRANYekanWebBold.woff2',
			),
			'pooshe-iransans'  => array(
				'dir'      => '/dist/font/iransans/fonts/woff2/',
				'regular'  => 'IRANSansWeb(FaNum).woff2',
				'semibold' => 'IRANSansWeb(FaNum)_Medium.woff2',
				'bold'     => 'IRANSansWeb(FaNum)_Bold.woff2',
			),
			'pooshe-Kalameh'   => array(
				'dir'      => '/dist/font/Kalameh/fonts/woff2/',
				'regular'  => 'KalamehWeb_Regular.woff2',
				'bold'     => 'KalamehWeb_Bold.woff2',
			),
		);
	}

	private function theme_urls() {
		$urls = array();
		if ( ! Tarh99_Perf_Env::is_pooshe() ) {
			return $urls;
		}
		$weights = $this->opt_list( 'weights' );
		foreach ( $this->font_map() as $handle => $font ) {
			if ( ! wp_style_is( $handle, 'enqueued' ) ) {
				continue;
			}
			foreach ( $weights as $weight ) {
				if ( empty( $font[ $weight ] ) ) {
					continue;
				}
				$relative = $font['dir'] . $font[ $weight ];
				if ( ! file_exists( get_theme_file_path( $relative ) ) ) {
					continue;
				}
				$urls[] = get_theme_file_uri( $relative );
			}
			break;
		}
		return $urls;
	}

	private function extra_urls() {
		$urls = array();
		foreach ( $this->opt_lines( 'extra' ) as $line ) {
			if ( '.woff2' !== strtolower( substr( (string) wp_parse_url( $line, PHP_URL_PATH ), -6 ) ) ) {
				continue;
			}
			$urls[] = ( 0 === strpos( $line, 'http' ) || 0 === strpos( $line, '//' ) ) ? $line : home_url( '/' . ltrim( $line, '/' ) );
		}
		return $urls;
	}

	private function rocket_has( $url ) {
		if ( ! $this->opt( 'skip_rocket' ) ) {
			return false;
		}
		$list = Tarh99_Perf_Env::rocket_option( 'preload_fonts' );
		if ( empty( $list ) || ! is_array( $list ) ) {
			return false;
		}
		$path = (string) wp_parse_url( $url, PHP_URL_PATH );
		foreach ( $list as $entry ) {
			$entry_path = (string) wp_parse_url( trim( (string) $entry ), PHP_URL_PATH );
			if ( '' !== $entry_path && rawurldecode( $entry_path ) === rawurldecode( $path ) ) {
				return true;
			}
		}
		return false;
	}

	public function print_preloads() {
		if ( $this->printed || ! Tarh99_Perf_Env::front_ok() ) {
			return;
		}
		$this->printed = true;
		$theme_urls    = ( false === has_action( 'wp_head', 'pooshe_preload_yekanbakh_fonts' ) ) ? $this->theme_urls() : array();
		$urls          = array_unique( array_merge( $theme_urls, $this->extra_urls() ) );
		foreach ( $urls as $url ) {
			if ( $this->rocket_has( $url ) ) {
				continue;
			}
			printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( $url ) );
		}
	}
}
