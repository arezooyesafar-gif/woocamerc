<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Static_404 extends Tarh99_Perf_Module {

	const EXT = 'css|js|mjs|map|png|jpe?g|gif|webp|avif|svg|ico|bmp|woff2?|ttf|eot|otf|mp4|webm|mp3|ogg|wav';

	public function id() {
		return 'static_404';
	}

	public function group() {
		return 'server';
	}

	public function title() {
		return 'پاسخ سریع به فایل‌های ناموجود (تصویر، CSS، JS، فونت)';
	}

	public function description() {
		return 'وقتی مرورگر یا ربات فایلی را می‌خواهد که روی سرور نیست (مثلاً تصویری که حذف شده یا آدرس اشتباه select2.min.css که قالب برای مدیر می‌سازد)، هاست آن را به وردپرس می‌دهد و وردپرس با همه افزونه‌ها (از جمله حدود ۲ ثانیه RTL-CareUnit) بالا می‌آید تا صفحه ۴۰۴ بسازد؛ در جدول صفحات کند هر کدام ۳ تا ۴ ثانیه پردازنده گرفته‌اند. این ماژول برای آدرس‌های داخل wp-content و wp-includes با پسوند فایل ثابت (تصویر، CSS، JS، فونت، ویدیو) و برای صفحه خطای ۴۰۴.shtml هاست وقتی خطا مربوط به چنین فایلی است، قبل از بارگذاری بقیه افزونه‌ها همان پاسخ ۴۰۴ را با یک متن کوتاه می‌دهد. صفحه‌های سایت و آدرس‌های اشتباه صفحات مثل قبل صفحه ۴۰۴ قالب را نشان می‌دهند.';
	}

	public function boot() {
	}

	public function early() {
		$method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) : '';
		if ( ( 'GET' !== $method && 'HEAD' !== $method ) || ( defined( 'WP_CLI' ) && WP_CLI ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || is_admin() ) {
			return;
		}
		$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) parse_url( (string) $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
		if ( '' === $path ) {
			return;
		}
		if ( preg_match( '#/(?:404|403|500)\.shtml$#i', $path ) ) {
			$original = '';
			foreach ( array( 'REDIRECT_URL', 'REDIRECT_REQUEST_URI' ) as $key ) {
				if ( ! empty( $_SERVER[ $key ] ) ) {
					$original = (string) parse_url( (string) $_SERVER[ $key ], PHP_URL_PATH );
					break;
				}
			}
			if ( '' === $original || ! $this->is_static( $original ) ) {
				return;
			}
		} elseif ( ! $this->is_static( $path ) ) {
			return;
		}
		if ( $this->has_redirect( isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : $path ) ) {
			return;
		}
		$this->send();
	}

	private function has_redirect( $uri ) {
		$plain = get_option( 'wpseo-premium-redirects-export-plain', false );
		$regex = get_option( 'wpseo-premium-redirects-export-regex', false );
		if ( empty( $plain ) && empty( $regex ) ) {
			return false;
		}
		$candidates = array();
		foreach ( array( $uri, (string) parse_url( $uri, PHP_URL_PATH ) ) as $value ) {
			$candidates[] = $value;
			$candidates[] = rawurldecode( $value );
		}
		if ( is_array( $plain ) ) {
			foreach ( $candidates as $value ) {
				$value = '/' === $value ? $value : trim( $value, '/' );
				if ( isset( $plain[ $value ] ) || isset( $plain[ '/' . $value ] ) || isset( $plain[ $value . '/' ] ) || isset( $plain[ '/' . $value . '/' ] ) ) {
					return true;
				}
			}
		}
		if ( is_array( $regex ) ) {
			foreach ( array_keys( $regex ) as $pattern ) {
				$pattern = str_replace( '`', '\\`', (string) $pattern );
				foreach ( $candidates as $value ) {
					if ( 1 === @preg_match( '`' . $pattern . '`', $value ) ) {
						return true;
					}
				}
			}
		}
		return false;
	}

	private function is_static( $path ) {
		$path    = rawurldecode( $path );
		$content = (string) parse_url( content_url( '/' ), PHP_URL_PATH );
		$include = (string) parse_url( includes_url( '/' ), PHP_URL_PATH );
		$inside  = ( '' !== $content && 0 === strpos( $path, $content ) ) || ( '' !== $include && 0 === strpos( $path, $include ) );
		return $inside && 1 === preg_match( '#\.(?:' . self::EXT . ')$#i', $path );
	}

	private function send() {
		if ( headers_sent() ) {
			return;
		}
		status_header( 404 );
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
		header( 'X-Robots-Tag: noindex' );
		echo 'Not Found';
		exit;
	}
}
