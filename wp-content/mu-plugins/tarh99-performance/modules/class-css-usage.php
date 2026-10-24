<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Css_Usage extends Tarh99_Perf_Module {

	const VERSION   = 3;
	const BUCKET    = 'css_usage';
	const TTL       = 2592000;
	const MAX_BYTES = 1572864;

	private $preview = false;
	private $masks = array();
	private $words = array();
	private $classes = array();
	private $tags = array();
	private $safelist = null;
	private $stats = array();

	public function id() {
		return 'css_usage';
	}

	public function title() {
		return 'CSS استفاده‌شده هر صفحه (جایگزین محلی Remove Unused CSS راکت)';
	}

	public function description() {
		return 'برای هر صفحه فقط قوانین CSS که در همان صفحه به کار می‌روند داخل هد قرار می‌گیرند و فایل‌های CSS کامل بدون مسدود کردن نمایش، بعد از بارگذاری صفحه در همان جای قبلی اعمال می‌شوند. همه چیز روی هاست خودتان انجام می‌شود و به سرور بیرونی نیازی ندارد. فقط برای مهمان‌ها اجرا می‌شود؛ برای صفحاتی که راکت کش می‌کند فقط یک بار هنگام ساخت کش (حدود ۵۰ میلی‌ثانیه). قبل از روشن کردن، با حساب مدیر آدرس هر صفحه را با ?tarh99_css=preview باز کنید و با صفحه عادی مقایسه کنید.';
	}

	public function default_enabled() {
		return false;
	}

	public function risk() {
		return 'medium';
	}

	public function fields() {
		return array(
			'scope'      => array(
				'type'    => 'text',
				'label'   => 'محدوده اجرا',
				'default' => 'all, !cart, !checkout, !account',
				'help'    => 'مثال فقط صفحه اصلی: front — صفحه اصلی و دسته‌ها و محصولات: front, product_archive, product',
			),
			'full_css'   => array(
				'type'    => 'checkbox',
				'label'   => 'فایل‌های CSS کامل بعد از نمایش صفحه هم بارگذاری شوند (برای حفظ کامل ظاهر؛ خاموش نکنید)',
				'default' => 1,
			),
			'full_after_load' => array(
				'type'    => 'checkbox',
				'label'   => 'فایل‌های CSS کامل بعد از رویداد load صفحه دانلود شوند (در رقابت با تصویر LCP نباشند)',
				'default' => 1,
			),
			'js_tokens'  => array(
				'type'    => 'checkbox',
				'label'   => 'کلاس‌هایی که جاوااسکریپت‌های همان صفحه اضافه می‌کنند هم نگه داشته شوند',
				'default' => 1,
			),
			'safelist'   => array(
				'type'    => 'textarea',
				'label'   => 'قوانینی که همیشه نگه داشته شوند — هر خط بخشی از سلکتور',
				'default' => "swiper\ndark-mode\nlight-mode\nbs-\nlazyload\nlazyloading\nentered\nexited\ne-lazyloaded\nelementor-invisible\nanimated",
				'help'    => 'کلاس‌هایی که جاوااسکریپت با ترکیب رشته‌ها می‌سازد (مثل اسلایدر Swiper) یا اسکریپت‌هایی که بعد از ساخت صفحه اضافه می‌شوند (LazyLoad راکت). کلاس‌هایی که در فایل‌های JS همان صفحه نوشته شده‌اند خودکار نگه داشته می‌شوند.',
			),
			'exclude'    => array(
				'type'    => 'textarea',
				'label'   => 'فایل‌های CSS که دست نخورند — هر خط بخشی از آدرس فایل',
				'default' => '',
			),
		);
	}

	public function boot() {
		add_action( 'template_redirect', array( $this, 'maybe_buffer' ), 3 );
		add_filter( 'rocket_buffer', array( $this, 'defer_full_css' ), 200000 );
		add_filter( 'rocket_delay_js_exclusions', array( $this, 'delay_js_exclusions' ) );
	}

	public function delay_js_exclusions( $exclusions ) {
		$exclusions   = is_array( $exclusions ) ? $exclusions : array();
		$exclusions[] = 'tarh99-full-css';
		return $exclusions;
	}

	public function defer_full_css( $html ) {
		if ( ! is_string( $html ) || ! $this->opt( 'full_css' ) || ! $this->opt( 'full_after_load' ) || false === strpos( $html, 'data-tarh99-full' ) || false !== strpos( $html, 'tarh99-full-css' ) ) {
			return $html;
		}
		$changed = 0;
		$out     = preg_replace_callback(
			'#<link\b[^>]*\sdata-tarh99-full\b[^>]*>#i',
			function ( $m ) use ( &$changed ) {
				$tag = preg_replace( '#\shref\s*=\s*(["\'])#i', ' data-tarh99-href=$1', $m[0], 1, $count );
				if ( ! is_string( $tag ) || 1 !== $count ) {
					return $m[0];
				}
				++$changed;
				return $tag;
			},
			$html
		);
		if ( ! is_string( $out ) || 0 === $changed ) {
			return $html;
		}
		$script = '<script id="tarh99-full-css">(function(){var d=document;function f(){var l=d.querySelectorAll("link[data-tarh99-href]");for(var i=0;i<l.length;i++){l[i].setAttribute("href",l[i].getAttribute("data-tarh99-href"));l[i].removeAttribute("data-tarh99-href");}}function s(){setTimeout(f,0);}if(d.readyState==="complete"){s();}else{window.addEventListener("load",s);}})();</script>';
		$pos    = strripos( $out, '</body>' );
		if ( false === $pos ) {
			return $html;
		}
		return substr( $out, 0, $pos ) . $script . substr( $out, $pos );
	}

	public function preview_boot() {
		add_action( 'template_redirect', array( $this, 'maybe_buffer' ), 3 );
	}

	private function admin_request( $value ) {
		if ( ! isset( $_GET['tarh99_css'] ) || $value !== sanitize_key( wp_unslash( $_GET['tarh99_css'] ) ) ) {
			return false;
		}
		return is_user_logged_in() && current_user_can( 'manage_options' );
	}

	public function maybe_buffer() {
		if ( ! Tarh99_Perf_Env::front_ok() || ! Tarh99_Perf_Env::is_get() ) {
			return;
		}
		$this->preview = $this->admin_request( 'preview' );
		if ( ! $this->preview ) {
			if ( ! $this->enabled() || is_user_logged_in() || $this->admin_request( 'off' ) ) {
				return;
			}
			if ( ! Tarh99_Perf_Env::scope_matches( (string) $this->opt( 'scope' ) ) ) {
				return;
			}
		}
		ob_start( array( $this, 'filter_html' ) );
	}

	public function filter_html( $html ) {
		if ( ! is_string( $html ) || false === stripos( $html, '</head>' ) || false === stripos( $html, 'stylesheet' ) ) {
			return $html;
		}
		try {
			return $this->process( $html );
		} catch ( Throwable $e ) {
			return $html;
		}
	}

	private function process( $html ) {
		$start       = microtime( true );
		$this->stats = array(
			'files'    => 0,
			'original' => 0,
			'used'     => 0,
		);
		$this->collect_tokens( $html );
		$masked = $this->mask( $html );
		if ( ! preg_match_all( '#<link\b[^>]*>#i', $masked, $links, PREG_OFFSET_CAPTURE ) ) {
			return $html;
		}
		$out    = '';
		$cursor = 0;
		$done   = 0;
		foreach ( $links[0] as $link ) {
			$tag         = $link[0];
			$offset      = $link[1];
			$replacement = $this->replace_link( $tag );
			if ( null === $replacement ) {
				continue;
			}
			$out   .= substr( $masked, $cursor, $offset - $cursor ) . $replacement;
			$cursor = $offset + strlen( $tag );
			++$done;
		}
		if ( 0 === $done ) {
			return $html;
		}
		$out .= substr( $masked, $cursor );
		$out  = $this->unmask( $out );
		if ( $this->preview ) {
			$note = sprintf(
				'<!-- tarh99 css_usage: %d files, %s KB -> %s KB, %d ms -->',
				$this->stats['files'],
				number_format( $this->stats['original'] / 1024, 1 ),
				number_format( $this->stats['used'] / 1024, 1 ),
				(int) round( ( microtime( true ) - $start ) * 1000 )
			);
			$out .= "\n" . $note;
		}
		return $out;
	}

	private function mask( $html ) {
		$this->masks = array();
		$masked      = preg_replace_callback(
			'#<!--.*?-->|<noscript\b.*?</noscript>|<script\b.*?</script>|<template\b.*?</template>#is',
			function ( $m ) {
				$key                 = '<!--tarh99-mask-' . count( $this->masks ) . '-->';
				$this->masks[ $key ] = $m[0];
				return $key;
			},
			$html
		);
		if ( ! is_string( $masked ) ) {
			throw new RuntimeException( 'mask failed' );
		}
		return $masked;
	}

	private function unmask( $html ) {
		if ( empty( $this->masks ) ) {
			return $html;
		}
		return strtr( $html, $this->masks );
	}

	private function collect_tokens( $html ) {
		$this->words   = array();
		$this->classes = array();
		$this->tags    = array();
		if ( preg_match_all( '/-?[_a-zA-Z\x80-\xff][-_a-zA-Z0-9\x80-\xff]*/', $html, $m ) ) {
			$this->words = array_flip( $m[0] );
		}
		if ( preg_match_all( '/\sclass\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $html, $m, PREG_SET_ORDER ) ) {
			foreach ( $m as $match ) {
				$value = isset( $match[3] ) ? $match[3] : ( isset( $match[2] ) && '' !== $match[2] ? $match[2] : $match[1] );
				foreach ( preg_split( '/\s+/', html_entity_decode( $value, ENT_QUOTES ) ) as $name ) {
					if ( '' !== $name ) {
						$this->classes[ $name ] = true;
					}
				}
			}
		}
		if ( preg_match_all( '/<([a-zA-Z][a-zA-Z0-9-]*)/', $html, $m ) ) {
			foreach ( $m[1] as $name ) {
				$this->tags[ strtolower( $name ) ] = true;
			}
		}
		if ( $this->opt( 'js_tokens' ) && preg_match_all( '#<script\b[^>]*\ssrc\s*=\s*["\']?([^"\'\s>]+)#i', $html, $m ) ) {
			foreach ( $m[1] as $src ) {
				$file = $this->local_file( html_entity_decode( $src, ENT_QUOTES ), 'js' );
				if ( null === $file ) {
					continue;
				}
				foreach ( $this->js_words( $file ) as $word ) {
					$this->words[ $word ] = true;
				}
			}
		}
	}

	private function js_words( $file ) {
		$key    = 'js|' . self::VERSION . '|' . $file . '|' . (int) @filemtime( $file ) . '|' . (int) @filesize( $file );
		$cached = Tarh99_Perf_Cache_Store::get( self::BUCKET, $key, self::TTL );
		if ( is_array( $cached ) ) {
			return $cached;
		}
		$code  = (string) @file_get_contents( $file );
		$words = array();
		if ( '' !== $code && preg_match_all( '/-?[_a-zA-Z\x80-\xff][-_a-zA-Z0-9\x80-\xff]*/', $code, $m ) ) {
			$words = array_keys( array_flip( $m[0] ) );
		}
		Tarh99_Perf_Cache_Store::set( self::BUCKET, $key, $words, self::TTL );
		return $words;
	}

	private function tree( $file ) {
		$key    = 'css|' . self::VERSION . '|' . $file . '|' . (int) @filemtime( $file ) . '|' . (int) @filesize( $file );
		$cached = Tarh99_Perf_Cache_Store::get( self::BUCKET, $key, self::TTL );
		if ( is_string( $cached ) && function_exists( 'gzuncompress' ) ) {
			$raw    = @gzuncompress( $cached );
			$cached = false === $raw ? null : @unserialize( $raw, array( 'allowed_classes' => false ) );
		}
		if ( is_array( $cached ) ) {
			return $cached;
		}
		$css = @file_get_contents( $file );
		if ( false === $css ) {
			return null;
		}
		$tree   = Tarh99_Perf_Css_Tree::parse( $css );
		$packed = function_exists( 'gzcompress' ) ? gzcompress( serialize( $tree ), 3 ) : $tree;
		Tarh99_Perf_Cache_Store::set( self::BUCKET, $key, $packed, self::TTL );
		return $tree;
	}

	private static function attr( $tag, $name ) {
		if ( preg_match( '/\s' . preg_quote( $name, '/' ) . '\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $tag, $m ) ) {
			if ( isset( $m[3] ) ) {
				return html_entity_decode( $m[3], ENT_QUOTES );
			}
			return html_entity_decode( isset( $m[2] ) && '' !== $m[2] ? $m[2] : $m[1], ENT_QUOTES );
		}
		return preg_match( '/\s' . preg_quote( $name, '/' ) . '(?=[\s>\/])/i', $tag ) ? '' : null;
	}

	private function replace_link( $tag ) {
		$rel = self::attr( $tag, 'rel' );
		if ( null === $rel || ! in_array( 'stylesheet', preg_split( '/\s+/', strtolower( trim( $rel ) ) ), true ) || false !== stripos( $rel, 'alternate' ) ) {
			return null;
		}
		foreach ( array( 'onload', 'disabled', 'integrity', 'data-tarh99-full', 'title' ) as $skip ) {
			if ( null !== self::attr( $tag, $skip ) ) {
				return null;
			}
		}
		$media = self::attr( $tag, 'media' );
		$media = null === $media ? '' : trim( $media );
		if ( '' !== $media && 'all' !== strtolower( $media ) && ! preg_match( '/\b(?:screen|all)\b|\(/i', $media ) ) {
			return null;
		}
		$href = (string) self::attr( $tag, 'href' );
		if ( '' === $href ) {
			return null;
		}
		foreach ( $this->opt_lines( 'exclude' ) as $fragment ) {
			if ( false !== stripos( $href, $fragment ) ) {
				return null;
			}
		}
		$file = $this->local_file( $href, 'css' );
		if ( null === $file ) {
			return null;
		}
		$tree = $this->tree( $file );
		if ( ! is_array( $tree ) ) {
			return null;
		}
		$used = Tarh99_Perf_Css_Tree::render( $tree, array( $this, 'keep' ), $this->absolute( $href ) );
		$used = str_ireplace( '</style', '<\/style', $used );
		$this->stats['files']++;
		$this->stats['original'] += (int) @filesize( $file );
		$this->stats['used']     += strlen( $used );

		$id    = (string) self::attr( $tag, 'id' );
		$style = '<style' . ( '' !== $id ? ' id="' . esc_attr( $id ) . '-tarh99"' : '' ) . ( '' !== $media ? ' media="' . esc_attr( $media ) . '"' : '' ) . ' data-tarh99-used>' . $used . '</style>';
		if ( ! $this->opt( 'full_css' ) ) {
			return $style;
		}
		$restore  = '' === $media ? 'all' : $media;
		$deferred = (string) preg_replace( '/\smedia\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $tag );
		$deferred = (string) preg_replace( '#\s*/?>$#', '', $deferred ) . ' media="print" onload="this.onload=null;this.media=\'' . esc_attr( str_replace( "'", '', $restore ) ) . '\'" data-tarh99-full>';
		return $style . $deferred . '<noscript>' . $tag . '</noscript>';
	}

	public function keep( $node ) {
		if ( null === $node['k'] ) {
			return $node['s'];
		}
		$prune = isset( $node['l'] );
		$kept  = array();
		foreach ( $node['k'] as $index => $need ) {
			if ( ! $this->satisfied( $need ) && ! ( $prune && $this->safelisted( $node['l'][ $index ] ) ) ) {
				continue;
			}
			if ( ! $prune ) {
				return $node['s'];
			}
			$kept[] = $node['l'][ $index ];
		}
		if ( ! $prune && $this->safelisted( $node['s'] ) ) {
			return $node['s'];
		}
		return empty( $kept ) ? null : implode( ',', $kept );
	}

	private function satisfied( $need ) {
		foreach ( $need as $token ) {
			$type = $token[0];
			$name = substr( $token, 2 );
			if ( 't' === $type ) {
				$found = isset( $this->tags[ $name ] );
			} elseif ( 'c' === $type ) {
				$found = isset( $this->classes[ $name ] ) || isset( $this->words[ $name ] );
			} else {
				$found = isset( $this->words[ $name ] );
			}
			if ( ! $found ) {
				return false;
			}
		}
		return true;
	}

	private function safelisted( $selector ) {
		foreach ( $this->safelist() as $fragment ) {
			if ( false !== stripos( $selector, $fragment ) ) {
				return true;
			}
		}
		return false;
	}

	private function safelist() {
		if ( null === $this->safelist ) {
			$this->safelist = array_values( array_unique( array_merge( $this->opt_lines( 'safelist' ), array( 'flexslider', 'flex-viewport', 'flex-direction-nav', 'flex-control', 'flex-active', 'flex-nav-', 'flex-prev', 'flex-next', 'flex-disabled', 'flex-pauseplay' ) ) ) );
		}
		return $this->safelist;
	}

	private function absolute( $url ) {
		if ( 0 === strpos( $url, '//' ) ) {
			return ( is_ssl() ? 'https:' : 'http:' ) . $url;
		}
		if ( 0 === strpos( $url, '/' ) ) {
			return untrailingslashit( home_url() ) . $url;
		}
		return $url;
	}

	private function local_file( $url, $ext ) {
		$url  = $this->absolute( trim( $url ) );
		$host = wp_parse_url( $url, PHP_URL_HOST );
		$path = wp_parse_url( $url, PHP_URL_PATH );
		if ( ! $host || ! $path ) {
			return null;
		}
		$path = rawurldecode( $path );
		if ( strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) !== $ext || false !== strpos( $path, '..' ) ) {
			return null;
		}
		$roots = array(
			array( content_url(), WP_CONTENT_DIR ),
			array( includes_url(), ABSPATH . WPINC ),
			array( site_url(), ABSPATH ),
		);
		foreach ( $roots as $root ) {
			$base_host = wp_parse_url( $root[0], PHP_URL_HOST );
			$base_path = untrailingslashit( (string) wp_parse_url( $root[0], PHP_URL_PATH ) );
			if ( strtolower( (string) $base_host ) !== strtolower( $host ) ) {
				continue;
			}
			if ( '' !== $base_path && 0 !== strpos( $path, $base_path . '/' ) ) {
				continue;
			}
			$file = untrailingslashit( $root[1] ) . substr( $path, strlen( $base_path ) );
			if ( is_file( $file ) && is_readable( $file ) && (int) @filesize( $file ) <= self::MAX_BYTES ) {
				return $file;
			}
			return null;
		}
		return null;
	}
}
