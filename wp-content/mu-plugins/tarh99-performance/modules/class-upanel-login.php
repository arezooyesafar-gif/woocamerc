<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Upanel_Login extends Tarh99_Perf_Module {

	private $libs = array(
		'angular'          => array( 'AngularJS v1.8.2', 'angular-1.8.2.min.js' ),
		'angular-route'    => array( 'AngularJS v1.8.3', 'angular-route-1.8.3.min.js' ),
		'angular-sanitize' => array( 'AngularJS v1.8.3', 'angular-sanitize-1.8.3.min.js' ),
		'jquery'           => array( 'jQuery JavaScript Library v3.7.1', 'jquery-3.7.1.min.js' ),
	);

	public function id() {
		return 'upanel_login';
	}

	public function title() {
		return 'سرعت صفحه ورود و پنل کاربری (uPanel)';
	}

	public function description() {
		return 'صفحه ورود uPanel بعد از آماده شدن، پیش‌بارگذار (لوگو و دایره چرخان) را عمداً ۱ ثانیه دیگر نگه می‌دارد و قبل از نمایش هر چیزی حدود ۱٫۸ مگابایت جاوااسکریپت فشرده‌نشده (angular.js به‌تنهایی ۱٫۴ مگابایت) دانلود و اجرا می‌کند. این ماژول نسخه فشرده همان نسخه‌ها (AngularJS 1.8.2، angular-route/sanitize 1.8.3 و jQuery 3.7.1) را جایگزین می‌کند و پیش‌بارگذار صفحه ورود را به‌محض آماده شدن فرم، تصویر پس‌زمینه، لوگو و فونت‌ها محو می‌کند (حداکثر همان ۱ ثانیه قبلی). فایل‌های uPanel دست نمی‌خورند و اگر uPanel نسخه کتابخانه‌ها را عوض کند، جایگزینی خودکار انجام نمی‌شود.';
	}

	public function fields() {
		return array(
			'paths'     => array(
				'type'    => 'textarea',
				'label'   => 'مسیرهای صفحه ورود و پنل (هر خط ابتدای یک مسیر؛ صفحه حساب کاربری ووکامرس همیشه شامل است)',
				'default' => "/my-account/\n/panel/",
			),
			'minified'  => array(
				'type'    => 'checkbox',
				'label'   => 'نسخه فشرده همان نسخه AngularJS و jQuery بارگذاری شود',
				'default' => 1,
			),
			'fast_loader' => array(
				'type'    => 'checkbox',
				'label'   => 'پیش‌بارگذار صفحه ورود بدون انتظار اضافه ۱ ثانیه‌ای محو شود',
				'default' => 1,
			),
		);
	}

	public function boot() {
		add_action( 'template_redirect', array( $this, 'maybe_buffer' ), 0 );
	}

	public function maybe_buffer() {
		if ( Tarh99_Perf_Env::bypass() || is_admin() || ! Tarh99_Perf_Env::is_get() || ! defined( 'HD_UPANEL_VERSION' ) ) {
			return;
		}
		if ( ! $this->opt( 'minified' ) && ! $this->opt( 'fast_loader' ) ) {
			return;
		}
		if ( ! $this->matches_request() ) {
			return;
		}
		ob_start( array( $this, 'filter_html' ) );
	}

	private function matches_request() {
		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			return true;
		}
		$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
		$home = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		if ( '' !== $home && '/' !== $home && 0 === strpos( $path, rtrim( $home, '/' ) ) ) {
			$path = substr( $path, strlen( rtrim( $home, '/' ) ) );
		}
		$path = trailingslashit( '/' . ltrim( rawurldecode( $path ), '/' ) );
		foreach ( $this->opt_lines( 'paths' ) as $prefix ) {
			$prefix = trailingslashit( '/' . ltrim( $prefix, '/' ) );
			if ( '/' !== $prefix && 0 === strpos( $path, $prefix ) ) {
				return true;
			}
		}
		return false;
	}

	public function filter_html( $html ) {
		if ( ! is_string( $html ) || false === strpos( $html, 'hd-upanel' ) || false === stripos( $html, '</body>' ) ) {
			return $html;
		}
		if ( $this->opt( 'minified' ) ) {
			$html = $this->swap_libraries( $html );
		}
		if ( $this->opt( 'fast_loader' ) && false !== strpos( $html, 'id="hd-upanel-loading"' ) && false !== strpos( $html, 'hd-upanel-login' ) ) {
			$html = $this->add_loader_script( $html );
		}
		return $html;
	}

	private function swap_libraries( $html ) {
		$out = preg_replace_callback(
			'#(<script\b[^>]*\bsrc\s*=\s*["\'])([^"\']*/uPanel/templates/(theme-[0-9]+)/upanel/assets/js/(angular|angular-route|angular-sanitize|jquery)\.js(?:\?[^"\']*)?)(["\'])#i',
			array( $this, 'swap_match' ),
			$html
		);
		return is_string( $out ) ? $out : $html;
	}

	public function swap_match( $m ) {
		$lib = strtolower( $m[4] );
		if ( ! isset( $this->libs[ $lib ] ) || ! $this->original_matches( $m[3], $lib ) ) {
			return $m[0];
		}
		$file = TARH99_PERF_DIR . 'assets/vendor/' . $this->libs[ $lib ][1];
		if ( ! is_readable( $file ) ) {
			return $m[0];
		}
		$url = TARH99_PERF_URL . 'assets/vendor/' . $this->libs[ $lib ][1] . '?v=' . TARH99_PERF_VERSION;
		return $m[1] . esc_url( $url ) . $m[5];
	}

	private function original_matches( $theme, $lib ) {
		static $checked = array();
		$key = $theme . '/' . $lib;
		if ( isset( $checked[ $key ] ) ) {
			return $checked[ $key ];
		}
		$path = WP_PLUGIN_DIR . '/uPanel/templates/' . $theme . '/upanel/assets/js/' . $lib . '.js';
		$head = is_readable( $path ) ? (string) file_get_contents( $path, false, null, 0, 400 ) : '';
		$checked[ $key ] = '' !== $head && 1 === preg_match( '/' . preg_quote( $this->libs[ $lib ][0], '/' ) . '(?![0-9.])/', $head );
		return $checked[ $key ];
	}

	private function add_loader_script( $html ) {
		$script = '<script id="tarh99-upanel-loader">(function(){if(!window.jQuery){return;}jQuery(function($){var l=$("#hd-upanel-loading");if(!l.length||!$(".hd-upanel-login").length){return;}var done=false,fonts=!(document.fonts&&document.fonts.ready),imgs=$(".hd-upanel-login img.login-background,.hd-upanel-login img.logo").filter(":visible").toArray();function check(){if(done||!fonts){return;}for(var i=0;i<imgs.length;i++){if(!imgs[i].complete){return;}}done=true;l.fadeOut();}for(var i=0;i<imgs.length;i++){if(!imgs[i].complete){imgs[i].addEventListener("load",check);imgs[i].addEventListener("error",check);}}if(!fonts){document.fonts.ready.then(function(){fonts=true;check();});}check();});})();</script>';
		$out = preg_replace( '#(<script\b[^>]*\bsrc\s*=\s*["\'][^"\']*/upanel/assets/js/hd-upanel-public\.js[^"\']*["\'][^>]*>\s*</script>)#i', '$1' . str_replace( '$', '\\$', $script ), $html, 1, $count );
		if ( is_string( $out ) && 1 === $count ) {
			return $out;
		}
		$pos = strripos( $html, '</body>' );
		return false === $pos ? $html : substr_replace( $html, $script, $pos, 0 );
	}
}
