<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Icon_Fonts extends Tarh99_Perf_Module {

	const MARKER = 'data-tarh99-icon-fonts';

	private $faces = null;
	private $active = array();

	public function id() {
		return 'icon_fonts';
	}

	public function title() {
		return 'فونت‌های آیکن سبک (Font Awesome و eicons)';
	}

	public function description() {
		return 'برای آیکن‌هایی که در صفحه اصلی دیده می‌شوند نسخه‌های چند کیلوبایتی از همان فونت‌های آیکن (همان طرح و اندازه) با unicode-range تعریف می‌شود؛ مرورگر برای این آیکن‌ها فقط فایل کوچک را می‌گیرد و فایل کامل (۶۰ تا ۱۰۰ کیلوبایت هر کدام) فقط وقتی دانلود می‌شود که آیکن دیگری روی صفحه باشد. اگر قالب یا المنتور فایل فونت را عوض کند، همان فونت خودکار کنار گذاشته می‌شود.';
	}

	public function default_enabled() {
		return false;
	}

	public function fields() {
		return array(
			'scope'           => array(
				'type'    => 'text',
				'label'   => 'محدوده اجرا',
				'default' => 'all, !cart, !checkout, !account',
			),
			'rewrite_preload' => array(
				'type'    => 'checkbox',
				'label'   => 'پیش‌بارگذاری فایل کامل همین فونت‌ها (مثلاً از تنظیمات Preload fonts راکت) به نسخه سبک تبدیل شود',
				'default' => 1,
			),
			'restrict_theme'  => array(
				'type'    => 'checkbox',
				'label'   => 'تعریف فونت کامل قالب (در CSS داخل صفحه) فقط برای آیکن‌های دیگر باشد تا فایرفاکس هم فایل کامل را بی‌دلیل دانلود نکند',
				'default' => 1,
			),
		);
	}

	public function notes() {
		$faces = $this->faces();
		$ok    = 0;
		foreach ( $faces as $face ) {
			if ( $face['valid'] ) {
				++$ok;
			}
		}
		return sprintf( 'فونت‌های سبک قابل استفاده: %d از %d', $ok, count( $faces ) );
	}

	public function boot() {
		add_action( 'template_redirect', array( $this, 'maybe_buffer' ), 4 );
		add_filter( 'rocket_buffer', array( $this, 'rocket_buffer' ), 100020 );
	}

	private function faces() {
		if ( null !== $this->faces ) {
			return $this->faces;
		}
		$this->faces = array();
		$manifest    = TARH99_PERF_DIR . 'assets/fonts/manifest.json';
		if ( ! is_readable( $manifest ) ) {
			return $this->faces;
		}
		$data = json_decode( (string) file_get_contents( $manifest ), true );
		if ( ! is_array( $data ) || empty( $data['faces'] ) || ! is_array( $data['faces'] ) ) {
			return $this->faces;
		}
		foreach ( $data['faces'] as $face ) {
			if ( ! is_array( $face ) || empty( $face['handle'] ) || empty( $face['file'] ) || empty( $face['range'] ) ) {
				continue;
			}
			$root     = 'theme' === $face['root'] ? trailingslashit( get_template_directory() ) : trailingslashit( WP_PLUGIN_DIR );
			$original = $root . ltrim( (string) $face['original'], '/' );
			$subset   = TARH99_PERF_DIR . 'assets/fonts/' . basename( (string) $face['file'] );
			$face['original_path'] = $original;
			$face['original_name'] = basename( (string) $face['original'] );
			$face['url']           = TARH99_PERF_URL . 'assets/fonts/' . rawurlencode( basename( (string) $face['file'] ) ) . '?v=' . (int) @filemtime( $subset );
			$face['valid']         = is_readable( $subset ) && $this->same_file( $original, (string) $face['md5'] );
			$this->faces[]         = $face;
		}
		return $this->faces;
	}

	private function same_file( $path, $md5 ) {
		if ( '' === $md5 || ! is_readable( $path ) ) {
			return false;
		}
		$key    = 'tarh99_if_' . md5( $path . '|' . (int) @filemtime( $path ) . '|' . (int) @filesize( $path ) );
		$cached = get_transient( $key );
		if ( false !== $cached ) {
			return $md5 === $cached;
		}
		$hash = (string) md5_file( $path );
		set_transient( $key, $hash, WEEK_IN_SECONDS );
		return $md5 === $hash;
	}

	public function maybe_buffer() {
		if ( ! Tarh99_Perf_Env::front_ok() || ! Tarh99_Perf_Env::is_get() ) {
			return;
		}
		if ( ! Tarh99_Perf_Env::scope_matches( (string) $this->opt( 'scope' ) ) ) {
			return;
		}
		ob_start( array( $this, 'filter_html' ) );
	}

	public function filter_html( $html ) {
		if ( ! is_string( $html ) || false === stripos( $html, '</head>' ) || false !== strpos( $html, self::MARKER ) ) {
			return $html;
		}
		try {
			$html = $this->insert_faces( $html );
			$html = $this->restrict_theme_faces( $html );
			return $this->rewrite_preloads( $html );
		} catch ( Throwable $e ) {
			return $html;
		}
	}

	public function rocket_buffer( $html ) {
		if ( ! is_string( $html ) || false === strpos( $html, self::MARKER ) ) {
			return $html;
		}
		try {
			$html = $this->restrict_theme_faces( $html );
			return $this->rewrite_preloads( $html );
		} catch ( Throwable $e ) {
			return $html;
		}
	}

	private function insert_faces( $html ) {
		$by_handle = array();
		foreach ( $this->faces() as $face ) {
			if ( $face['valid'] ) {
				$by_handle[ $face['handle'] ][] = $face;
			}
		}
		foreach ( $by_handle as $handle => $faces ) {
			$pattern = '#<link\b[^>]*\sid\s*=\s*["\']' . preg_quote( $handle, '#' ) . '-css["\'][^>]*>(?:\s*<noscript>.*?</noscript>)?#is';
			if ( ! preg_match( $pattern, $html, $m, PREG_OFFSET_CAPTURE ) ) {
				continue;
			}
			$css = '';
			foreach ( $faces as $face ) {
				$css .= '@font-face{font-family:"' . str_replace( '"', '', $face['family'] ) . '";font-style:' . sanitize_key( $face['style'] ) . ';font-weight:' . (int) $face['weight'] . ';src:url("' . esc_url( $face['url'] ) . '") format("woff2");unicode-range:' . preg_replace( '/[^U+0-9A-Fa-f,\-]/', '', $face['range'] ) . '}';
				$this->active[ $face['original_name'] ] = $face['url'];
			}
			$style = '<style id="tarh99-icon-fonts-' . esc_attr( $handle ) . '" ' . self::MARKER . '>' . $css . '</style>';
			$end   = $m[0][1] + strlen( $m[0][0] );
			$html  = substr( $html, 0, $end ) . $style . substr( $html, $end );
		}
		return $html;
	}

	private function restrict_theme_faces( $html ) {
		if ( ! $this->opt( 'restrict_theme' ) || false === stripos( $html, '@font-face' ) ) {
			return $html;
		}
		$faces = array();
		foreach ( $this->faces() as $face ) {
			if ( ! $face['valid'] || false === strpos( $html, 'tarh99-icon-fonts-' . $face['handle'] ) ) {
				continue;
			}
			$complement = $this->complement( (string) $face['range'] );
			if ( '' === $complement ) {
				continue;
			}
			$faces[] = array(
				'family'     => $this->norm_family( (string) $face['family'] ),
				'weight'     => $this->norm_weight( (string) $face['weight'] ),
				'style'      => strtolower( (string) $face['style'] ),
				'stem'       => strtolower( (string) preg_replace( '/\.[a-z0-9]+$/i', '', $face['original_name'] ) ) . '.',
				'complement' => $complement,
			);
		}
		if ( ! $faces ) {
			return $html;
		}
		$self = $this;
		$out  = preg_replace_callback(
			'#@font-face\s*\{([^{}]*)\}#i',
			function ( $m ) use ( $faces, $self ) {
				$body = $m[1];
				if ( false !== stripos( $body, 'unicode-range' ) || ! preg_match( '#(?:^|;)\s*font-family\s*:\s*([^;]+)#i', $body, $family ) || ! preg_match( '#(?:^|;)\s*src\s*:[^;]*#i', $body ) ) {
					return $m[0];
				}
				$weight = preg_match( '#(?:^|;)\s*font-weight\s*:\s*([^;]+)#i', $body, $w ) ? $self->norm_weight( $w[1] ) : '400';
				$style  = preg_match( '#(?:^|;)\s*font-style\s*:\s*([^;]+)#i', $body, $st ) ? strtolower( trim( $st[1] ) ) : 'normal';
				$name   = $self->norm_family( $family[1] );
				$src    = strtolower( $body );
				foreach ( $faces as $face ) {
					if ( $face['family'] === $name && $face['weight'] === $weight && $face['style'] === $style && false !== strpos( $src, $face['stem'] ) ) {
						return '@font-face{' . rtrim( rtrim( $body ), ';' ) . ';unicode-range:' . $face['complement'] . '}';
					}
				}
				return $m[0];
			},
			$html
		);
		return is_string( $out ) ? $out : $html;
	}

	public function norm_family( $family ) {
		$family = trim( str_replace( '\\', '', $family ) );
		return strtolower( trim( $family, " \t\"'" ) );
	}

	public function norm_weight( $weight ) {
		$weight = strtolower( trim( $weight ) );
		if ( 'normal' === $weight ) {
			return '400';
		}
		if ( 'bold' === $weight ) {
			return '700';
		}
		return (string) (int) $weight;
	}

	private function complement( $range ) {
		$spans = array();
		foreach ( explode( ',', $range ) as $token ) {
			$token = strtoupper( trim( $token ) );
			if ( '' === $token ) {
				continue;
			}
			if ( ! preg_match( '/^U\+([0-9A-F]{1,6})(?:-([0-9A-F]{1,6}))?$/', $token, $m ) ) {
				return '';
			}
			$from = hexdec( $m[1] );
			$to   = isset( $m[2] ) ? hexdec( $m[2] ) : $from;
			if ( $to < $from || $to > 0x10FFFF ) {
				return '';
			}
			$spans[] = array( $from, $to );
		}
		if ( ! $spans ) {
			return '';
		}
		usort(
			$spans,
			function ( $a, $b ) {
				return $a[0] - $b[0];
			}
		);
		$out  = array();
		$next = 0;
		foreach ( $spans as $span ) {
			if ( $span[0] > $next ) {
				$out[] = $this->span( $next, $span[0] - 1 );
			}
			$next = max( $next, $span[1] + 1 );
		}
		if ( $next <= 0x10FFFF ) {
			$out[] = $this->span( $next, 0x10FFFF );
		}
		return implode( ',', $out );
	}

	private function span( $from, $to ) {
		return $from === $to ? sprintf( 'U+%X', $from ) : sprintf( 'U+%X-%X', $from, $to );
	}

	private function rewrite_preloads( $html ) {
		if ( ! $this->opt( 'rewrite_preload' ) ) {
			return $html;
		}
		if ( empty( $this->active ) ) {
			foreach ( $this->faces() as $face ) {
				if ( $face['valid'] && false !== strpos( $html, 'tarh99-icon-fonts-' . $face['handle'] ) ) {
					$this->active[ $face['original_name'] ] = $face['url'];
				}
			}
		}
		if ( empty( $this->active ) ) {
			return $html;
		}
		$active = $this->active;
		$out    = preg_replace_callback(
			'#<link\b[^>]*\srel\s*=\s*["\']?preload\b[^>]*>#i',
			function ( $m ) use ( $active ) {
				$tag = $m[0];
				if ( ! preg_match( '#\shref\s*=\s*(["\'])([^"\']+)\1#i', $tag, $href ) ) {
					return $tag;
				}
				$file = basename( (string) wp_parse_url( html_entity_decode( $href[2] ), PHP_URL_PATH ) );
				if ( ! isset( $active[ $file ] ) ) {
					return $tag;
				}
				return str_replace( $href[0], ' href=' . $href[1] . esc_url( $active[ $file ] ) . $href[1], $tag );
			},
			$html
		);
		return is_string( $out ) ? $out : $html;
	}
}
