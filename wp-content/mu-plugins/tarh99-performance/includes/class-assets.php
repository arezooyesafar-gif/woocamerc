<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Tarh99_Perf_Assets {

	private static $removed = array();

	public static function registry( $type ) {
		return ( 'css' === $type ) ? wp_styles() : wp_scripts();
	}

	public static function is_critical( $handle ) {
		return (bool) preg_match( '/^(jquery|jquery-core|jquery-migrate|wp-polyfill.*|wp-hooks|wp-i18n|wp-api-fetch|wp-dom-ready|regenerator-runtime|elementor-frontend|elementor-frontend-modules|elementor-webpack-runtime|elementor-pro-frontend|elementor-pro-webpack-runtime|woocommerce|wc-checkout|wc-cart|wc-country-select|wc-address-i18n|selectWoo|select2|js-cookie|pooshe-main|pooshe-app|pooshe-swiper)$|zarinpal|payment|gateway|recaptcha|captcha|login|rocket/i', (string) $handle );
	}

	public static function has_dependents( $registry, $handle ) {
		if ( ! $registry || empty( $registry->registered ) ) {
			return true;
		}
		$queue = array_unique( array_merge( (array) $registry->queue, (array) $registry->to_do, (array) $registry->done ) );
		foreach ( $queue as $queued ) {
			if ( $queued === $handle ) {
				continue;
			}
			if ( self::depends_on( $registry, $queued, $handle, array() ) ) {
				return true;
			}
		}
		return false;
	}

	private static function depends_on( $registry, $handle, $target, $seen ) {
		if ( isset( $seen[ $handle ] ) || ! isset( $registry->registered[ $handle ] ) ) {
			return false;
		}
		$seen[ $handle ] = true;
		foreach ( (array) $registry->registered[ $handle ]->deps as $dep ) {
			if ( $dep === $target || self::depends_on( $registry, $dep, $target, $seen ) ) {
				return true;
			}
		}
		return false;
	}

	public static function safe_dequeue( $type, $handle, $allow_critical = false ) {
		$registry = self::registry( $type );
		if ( ! $registry || ! isset( $registry->registered[ $handle ] ) ) {
			return false;
		}
		if ( in_array( $handle, (array) $registry->done, true ) ) {
			return false;
		}
		if ( ! $allow_critical && self::is_critical( $handle ) ) {
			return false;
		}
		if ( self::has_dependents( $registry, $handle ) ) {
			return false;
		}
		if ( 'css' === $type ) {
			wp_dequeue_style( $handle );
		} else {
			wp_dequeue_script( $handle );
		}
		self::$removed[ $type . ':' . $handle ] = true;
		return true;
	}

	public static function removed() {
		return array_keys( self::$removed );
	}
}
