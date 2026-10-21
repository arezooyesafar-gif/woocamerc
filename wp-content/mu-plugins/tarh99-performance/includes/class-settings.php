<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Tarh99_Perf_Settings {

	const OPTION = 'tarh99_perf_settings';

	private static $data = null;

	public static function all() {
		if ( null === self::$data ) {
			$stored     = get_option( self::OPTION, array() );
			$stored     = is_array( $stored ) ? $stored : array();
			self::$data = array(
				'modules' => ( isset( $stored['modules'] ) && is_array( $stored['modules'] ) ) ? $stored['modules'] : array(),
				'values'  => ( isset( $stored['values'] ) && is_array( $stored['values'] ) ) ? $stored['values'] : array(),
			);
		}
		return self::$data;
	}

	public static function constant_override( $module_id ) {
		$name = 'TARH99_PERF_MODULE_' . strtoupper( $module_id );
		if ( defined( $name ) ) {
			return (bool) constant( $name );
		}
		return null;
	}

	public static function safe_mode() {
		return defined( 'TARH99_PERF_SAFE_MODE' ) && TARH99_PERF_SAFE_MODE;
	}

	public static function stored_flag( $module_id ) {
		$data = self::all();
		return array_key_exists( $module_id, $data['modules'] ) ? (bool) $data['modules'][ $module_id ] : null;
	}

	public static function module_enabled( $module_id, $default ) {
		$constant = self::constant_override( $module_id );
		if ( null !== $constant ) {
			return $constant;
		}
		if ( self::safe_mode() ) {
			return false;
		}
		$stored = self::stored_flag( $module_id );
		return null === $stored ? (bool) $default : $stored;
	}

	public static function value( $module_id, $field, $default = null ) {
		$data = self::all();
		if ( isset( $data['values'][ $module_id ] ) && is_array( $data['values'][ $module_id ] ) && array_key_exists( $field, $data['values'][ $module_id ] ) ) {
			return $data['values'][ $module_id ][ $field ];
		}
		return $default;
	}

	public static function save( array $modules, array $values ) {
		$data = array(
			'modules' => $modules,
			'values'  => $values,
		);
		update_option( self::OPTION, $data, true );
		self::$data = $data;
	}

	public static function reset() {
		delete_option( self::OPTION );
		self::$data = null;
	}
}
