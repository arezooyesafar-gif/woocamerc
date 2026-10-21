<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Tarh99_Perf_Cache_Store {

	const GROUP       = 'tarh99_perf';
	const VERSION_OPT = 'tarh99_perf_cache_ver';
	const GUARD       = "<?php exit; ?>\n";

	private static $bumped = false;

	public static function external() {
		return function_exists( 'wp_using_ext_object_cache' ) && wp_using_ext_object_cache();
	}

	public static function version() {
		return (int) get_option( self::VERSION_OPT, 1 );
	}

	public static function bump() {
		if ( self::$bumped ) {
			return;
		}
		self::$bumped = true;
		update_option( self::VERSION_OPT, self::version() + 1, true );
	}

	public static function base_dir() {
		return trailingslashit( WP_CONTENT_DIR ) . 'cache/tarh99-perf/';
	}

	private static function bucket_dir( $bucket ) {
		return self::base_dir() . sanitize_key( $bucket ) . '/';
	}

	private static function ensure_dir( $dir ) {
		if ( is_dir( $dir ) ) {
			return is_writable( $dir );
		}
		if ( ! wp_mkdir_p( $dir ) ) {
			return false;
		}
		$base = self::base_dir();
		if ( ! file_exists( $base . 'index.php' ) ) {
			@file_put_contents( $base . 'index.php', "<?php\n" );
		}
		if ( ! file_exists( $base . '.htaccess' ) ) {
			@file_put_contents( $base . '.htaccess', "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n" );
		}
		if ( ! file_exists( $dir . 'index.php' ) ) {
			@file_put_contents( $dir . 'index.php', "<?php\n" );
		}
		return is_writable( $dir );
	}

	private static function file_key( $key ) {
		return md5( self::version() . '|' . $key );
	}

	public static function get( $bucket, $key, $ttl ) {
		$hash = self::file_key( $key );
		if ( self::external() ) {
			$value = wp_cache_get( $bucket . '_' . $hash, self::GROUP );
			return false === $value ? null : $value;
		}
		$file = self::bucket_dir( $bucket ) . substr( $hash, 0, 2 ) . '/' . $hash . '.php';
		if ( ! is_file( $file ) ) {
			return null;
		}
		$mtime = @filemtime( $file );
		if ( ! $mtime || ( time() - $mtime ) > (int) $ttl ) {
			return null;
		}
		$raw = @file_get_contents( $file );
		if ( false === $raw || 0 !== strpos( $raw, self::GUARD ) ) {
			return null;
		}
		$value = @unserialize( substr( $raw, strlen( self::GUARD ) ), array( 'allowed_classes' => false ) );
		return false === $value ? null : $value;
	}

	public static function set( $bucket, $key, $value, $ttl ) {
		$hash = self::file_key( $key );
		if ( self::external() ) {
			return wp_cache_set( $bucket . '_' . $hash, $value, self::GROUP, (int) $ttl );
		}
		$dir = self::bucket_dir( $bucket ) . substr( $hash, 0, 2 ) . '/';
		if ( ! self::ensure_dir( $dir ) ) {
			return false;
		}
		$file = $dir . $hash . '.php';
		$tmp  = $file . '.' . wp_generate_password( 6, false ) . '.tmp';
		if ( false === @file_put_contents( $tmp, self::GUARD . serialize( $value ), LOCK_EX ) ) {
			@unlink( $tmp );
			return false;
		}
		if ( ! @rename( $tmp, $file ) ) {
			@unlink( $tmp );
			return false;
		}
		return true;
	}

	public static function purge_files( $max_age, $deadline = 0 ) {
		$base = self::base_dir();
		if ( ! is_dir( $base ) ) {
			return 0;
		}
		$removed = 0;
		$now     = time();
		try {
			$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $base, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
			foreach ( $iterator as $item ) {
				if ( $deadline && microtime( true ) > $deadline ) {
					break;
				}
				$path = $item->getPathname();
				if ( $item->isDir() ) {
					$entries = @scandir( $path );
					if ( is_array( $entries ) && count( $entries ) <= 2 ) {
						@rmdir( $path );
					}
					continue;
				}
				$name = $item->getFilename();
				if ( 'index.php' === $name || '.htaccess' === $name ) {
					continue;
				}
				if ( ( $now - (int) $item->getMTime() ) > (int) $max_age ) {
					if ( @unlink( $path ) ) {
						++$removed;
					}
				}
			}
		} catch ( Exception $e ) {
			return $removed;
		}
		return $removed;
	}

	public static function stats() {
		$base  = self::base_dir();
		$count = 0;
		$size  = 0;
		if ( self::external() || ! is_dir( $base ) ) {
			return array(
				'files' => 0,
				'bytes' => 0,
			);
		}
		try {
			$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $base, FilesystemIterator::SKIP_DOTS ) );
			foreach ( $iterator as $item ) {
				if ( $item->isFile() && '.php' === substr( $item->getFilename(), -4 ) && 'index.php' !== $item->getFilename() ) {
					++$count;
					$size += (int) $item->getSize();
				}
				if ( $count > 50000 ) {
					break;
				}
			}
		} catch ( Exception $e ) {
			return array(
				'files' => $count,
				'bytes' => $size,
			);
		}
		return array(
			'files' => $count,
			'bytes' => $size,
		);
	}
}
