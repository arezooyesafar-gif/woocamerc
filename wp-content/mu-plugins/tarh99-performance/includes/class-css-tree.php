<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Tarh99_Perf_Css_Tree {

	const GROUPING = array( 'media', 'supports', 'layer', 'container', 'document', '-moz-document', 'scope', 'starting-style' );

	private $css;
	private $len;
	private $pos;

	public static function parse( $css ) {
		$parser      = new self();
		$parser->css = (string) $css;
		$parser->len = strlen( $parser->css );
		$parser->pos = 0;
		return $parser->block( false );
	}

	private function block( $nested ) {
		$nodes = array();
		while ( $this->pos < $this->len ) {
			$this->skip_space();
			if ( $this->pos >= $this->len ) {
				break;
			}
			$char = $this->css[ $this->pos ];
			if ( '}' === $char ) {
				++$this->pos;
				if ( $nested ) {
					return $nodes;
				}
				continue;
			}
			if ( ';' === $char ) {
				++$this->pos;
				continue;
			}
			if ( '<' === $char && 0 === substr_compare( $this->css, '<!--', $this->pos, 4 ) ) {
				$this->pos += 4;
				continue;
			}
			if ( '-' === $char && 0 === substr_compare( $this->css, '-->', $this->pos, 3 ) ) {
				$this->pos += 3;
				continue;
			}
			if ( '@' === $char ) {
				$node = $this->at_rule();
			} else {
				$node = $this->style_rule();
			}
			if ( null !== $node ) {
				$nodes[] = $node;
			}
		}
		return $nodes;
	}

	private function skip_space() {
		while ( $this->pos < $this->len ) {
			$char = $this->css[ $this->pos ];
			if ( ' ' === $char || "\n" === $char || "\r" === $char || "\t" === $char || "\f" === $char ) {
				++$this->pos;
				continue;
			}
			if ( '/' === $char && $this->pos + 1 < $this->len && '*' === $this->css[ $this->pos + 1 ] ) {
				$end       = strpos( $this->css, '*/', $this->pos + 2 );
				$this->pos = false === $end ? $this->len : $end + 2;
				continue;
			}
			break;
		}
	}

	private function read_until( $stops ) {
		$out   = '';
		$depth = 0;
		while ( $this->pos < $this->len ) {
			$char = $this->css[ $this->pos ];
			if ( '"' === $char || "'" === $char ) {
				$out .= $this->read_string( $char );
				continue;
			}
			if ( '/' === $char && $this->pos + 1 < $this->len && '*' === $this->css[ $this->pos + 1 ] ) {
				$end       = strpos( $this->css, '*/', $this->pos + 2 );
				$this->pos = false === $end ? $this->len : $end + 2;
				$out      .= ' ';
				continue;
			}
			if ( '\\' === $char && $this->pos + 1 < $this->len ) {
				$out       .= substr( $this->css, $this->pos, 2 );
				$this->pos += 2;
				continue;
			}
			if ( '(' === $char || '[' === $char ) {
				++$depth;
			} elseif ( ( ')' === $char || ']' === $char ) && $depth > 0 ) {
				--$depth;
			} elseif ( 0 === $depth && false !== strpos( $stops, $char ) ) {
				return $out;
			}
			$out .= $char;
			++$this->pos;
		}
		return $out;
	}

	private function read_string( $quote ) {
		$start = $this->pos;
		++$this->pos;
		while ( $this->pos < $this->len ) {
			$char = $this->css[ $this->pos ];
			if ( '\\' === $char ) {
				$this->pos += 2;
				continue;
			}
			++$this->pos;
			if ( $char === $quote || "\n" === $char ) {
				break;
			}
		}
		return substr( $this->css, $start, $this->pos - $start );
	}

	private function read_body() {
		$out   = '';
		$depth = 0;
		while ( $this->pos < $this->len ) {
			$char = $this->css[ $this->pos ];
			if ( '"' === $char || "'" === $char ) {
				$out .= $this->read_string( $char );
				continue;
			}
			if ( '/' === $char && $this->pos + 1 < $this->len && '*' === $this->css[ $this->pos + 1 ] ) {
				$end       = strpos( $this->css, '*/', $this->pos + 2 );
				$this->pos = false === $end ? $this->len : $end + 2;
				continue;
			}
			if ( '\\' === $char && $this->pos + 1 < $this->len ) {
				$out       .= substr( $this->css, $this->pos, 2 );
				$this->pos += 2;
				continue;
			}
			++$this->pos;
			if ( '{' === $char ) {
				++$depth;
			} elseif ( '}' === $char ) {
				if ( 0 === $depth ) {
					return $out;
				}
				--$depth;
			}
			$out .= $char;
		}
		return $out;
	}

	private function at_rule() {
		$prelude = trim( $this->read_until( '{;}' ) );
		if ( $this->pos >= $this->len ) {
			return '' === $prelude ? null : array(
				't' => 'x',
				'v' => $prelude . ';',
			);
		}
		$char = $this->css[ $this->pos ];
		if ( ';' === $char || '}' === $char ) {
			if ( ';' === $char ) {
				++$this->pos;
			}
			return array(
				't' => 'x',
				'v' => $prelude . ';',
				'i' => (bool) preg_match( '/^@import\b/i', $prelude ),
			);
		}
		++$this->pos;
		$name = strtolower( (string) preg_replace( '/^@([\w-]+).*$/s', '$1', $prelude ) );
		if ( in_array( $name, self::GROUPING, true ) ) {
			return array(
				't' => 'g',
				'p' => $prelude,
				'c' => $this->block( true ),
			);
		}
		return array(
			't' => 'x',
			'v' => $prelude . '{' . trim( $this->read_body() ) . '}',
		);
	}

	private function style_rule() {
		$selector = trim( $this->read_until( '{}' ) );
		if ( $this->pos >= $this->len || '}' === $this->css[ $this->pos ] ) {
			return null;
		}
		++$this->pos;
		$body = trim( $this->read_body() );
		if ( '' === $selector ) {
			return null;
		}
		$node = array(
			't' => 'r',
			's' => $selector,
			'b' => $body,
			'k' => null,
		);
		if ( false !== strpos( $body, '{' ) ) {
			return $node;
		}
		$list      = self::split_list( $selector );
		$node['k'] = self::requirements_of( $list );
		if ( null !== $node['k'] && count( $list ) > 1 && ! preg_match( '/::?-|:focus-visible|:user-|:popover|:modal|:autofill|:placeholder-shown/i', $selector ) ) {
			$node['l'] = $list;
		}
		return $node;
	}

	public static function split_list( $selector ) {
		$parts   = array();
		$current = '';
		$depth   = 0;
		$quote   = '';
		$length  = strlen( $selector );
		for ( $i = 0; $i < $length; $i++ ) {
			$char = $selector[ $i ];
			if ( '' !== $quote ) {
				$current .= $char;
				if ( '\\' === $char && $i + 1 < $length ) {
					$current .= $selector[ ++$i ];
				} elseif ( $char === $quote ) {
					$quote = '';
				}
				continue;
			}
			if ( '\\' === $char && $i + 1 < $length ) {
				$current .= $char . $selector[ ++$i ];
				continue;
			}
			if ( '"' === $char || "'" === $char ) {
				$quote    = $char;
				$current .= $char;
				continue;
			}
			if ( '(' === $char || '[' === $char ) {
				++$depth;
			} elseif ( ( ')' === $char || ']' === $char ) && $depth > 0 ) {
				--$depth;
			} elseif ( ',' === $char && 0 === $depth ) {
				$parts[] = trim( $current );
				$current = '';
				continue;
			}
			$current .= $char;
		}
		$parts[] = trim( $current );
		return array_values( array_filter( $parts, 'strlen' ) );
	}

	private static function strip_groups( $selector, $open, $close ) {
		$out    = '';
		$depth  = 0;
		$length = strlen( $selector );
		for ( $i = 0; $i < $length; $i++ ) {
			$char = $selector[ $i ];
			if ( $char === $open ) {
				++$depth;
				continue;
			}
			if ( $char === $close && $depth > 0 ) {
				--$depth;
				continue;
			}
			if ( 0 === $depth ) {
				$out .= $char;
			}
		}
		return $out;
	}

	public static function requirements( $selector ) {
		return self::requirements_of( self::split_list( $selector ) );
	}

	private static function requirements_of( $selectors ) {
		$list = array();
		foreach ( $selectors as $single ) {
			$need = self::single_requirements( $single );
			if ( null === $need ) {
				return null;
			}
			$list[] = $need;
		}
		return $list;
	}

	private static function single_requirements( $selector ) {
		if ( preg_match( '/:(?:is|where|has|matches|-webkit-any|-moz-any|host|host-context|global|local|slotted|deep)\s*\(/i', $selector ) ) {
			return null;
		}
		if ( preg_match( '/\\\\[0-9a-fA-F]/', $selector ) ) {
			return null;
		}
		$clean = self::strip_groups( $selector, '[', ']' );
		$clean = (string) preg_replace( '/:not\s*\(/i', ':not(', $clean );
		while ( false !== ( $at = stripos( $clean, ':not(' ) ) ) {
			$depth = 0;
			$end   = strlen( $clean );
			for ( $i = $at + 4; $i < strlen( $clean ); $i++ ) {
				if ( '(' === $clean[ $i ] ) {
					++$depth;
				} elseif ( ')' === $clean[ $i ] ) {
					--$depth;
					if ( 0 === $depth ) {
						$end = $i + 1;
						break;
					}
				}
			}
			$clean = substr( $clean, 0, $at ) . substr( $clean, $end );
		}
		$clean = (string) preg_replace( '/::?[a-zA-Z-]+(?:\([^()]*(?:\([^()]*\)[^()]*)*\))?/', '', $clean );
		$need  = array();
		if ( preg_match_all( '/\.((?:[-_a-zA-Z0-9\x80-\xff]|\\\\.)+)/', $clean, $m ) ) {
			foreach ( $m[1] as $name ) {
				$need[ 'c:' . stripslashes( $name ) ] = true;
			}
		}
		if ( preg_match_all( '/#((?:[-_a-zA-Z0-9\x80-\xff]|\\\\.)+)/', $clean, $m ) ) {
			foreach ( $m[1] as $name ) {
				$need[ 'i:' . stripslashes( $name ) ] = true;
			}
		}
		$tags = (string) preg_replace( '/[.#](?:[-_a-zA-Z0-9\x80-\xff]|\\\\.)+/', ' ', $clean );
		if ( preg_match_all( '/(?:^|[\s>+~|])([a-zA-Z][a-zA-Z0-9-]*)/', $tags, $m ) ) {
			foreach ( $m[1] as $name ) {
				$need[ 't:' . strtolower( $name ) ] = true;
			}
		}
		return array_keys( $need );
	}

	public static function render( $nodes, $keep, $base_url ) {
		$out = '';
		foreach ( $nodes as $node ) {
			if ( 'r' === $node['t'] ) {
				$selector = call_user_func( $keep, $node );
				if ( null !== $selector && '' !== $selector ) {
					$out .= $selector . '{' . $node['b'] . '}';
				}
				continue;
			}
			if ( 'g' === $node['t'] ) {
				$inner = self::render( $node['c'], $keep, $base_url );
				if ( '' !== $inner ) {
					$out .= $node['p'] . '{' . $inner . '}';
				}
				continue;
			}
			$out .= $node['v'];
		}
		return '' === $base_url ? $out : self::rewrite_urls( $out, $base_url );
	}

	public static function rewrite_urls( $css, $base_url ) {
		$css = preg_replace_callback(
			'/url\(\s*([\'"]?)([^\'")]*)\1\s*\)/i',
			function ( $m ) use ( $base_url ) {
				return 'url(' . $m[1] . self::resolve( $m[2], $base_url ) . $m[1] . ')';
			},
			$css
		);
		if ( null === $css ) {
			throw new RuntimeException( 'css url rewrite failed' );
		}
		$css = preg_replace_callback(
			'/@import\s+([\'"])([^\'"]+)\1/i',
			function ( $m ) use ( $base_url ) {
				return '@import ' . $m[1] . self::resolve( $m[2], $base_url ) . $m[1];
			},
			$css
		);
		if ( null === $css ) {
			throw new RuntimeException( 'css import rewrite failed' );
		}
		return $css;
	}

	public static function resolve( $url, $base_url ) {
		$url = trim( $url );
		if ( '' === $url || preg_match( '#^(?:[a-z][a-z0-9+.-]*:|//|/|\#|%23)#i', $url ) ) {
			return $url;
		}
		$parts = wp_parse_url( $base_url );
		if ( empty( $parts['host'] ) ) {
			return $url;
		}
		$origin = ( isset( $parts['scheme'] ) ? $parts['scheme'] . ':' : '' ) . '//' . $parts['host'] . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' );
		$dir    = isset( $parts['path'] ) ? preg_replace( '#/[^/]*$#', '/', $parts['path'] ) : '/';
		$suffix = '';
		if ( preg_match( '/^([^?#]*)([?#].*)$/', $url, $m ) ) {
			$url    = $m[1];
			$suffix = $m[2];
		}
		$segments = array();
		foreach ( explode( '/', $dir . $url ) as $segment ) {
			if ( '..' === $segment ) {
				array_pop( $segments );
			} elseif ( '.' !== $segment ) {
				$segments[] = $segment;
			}
		}
		$path = implode( '/', $segments );
		if ( 0 !== strpos( $path, '/' ) ) {
			$path = '/' . $path;
		}
		return $origin . $path . $suffix;
	}
}
