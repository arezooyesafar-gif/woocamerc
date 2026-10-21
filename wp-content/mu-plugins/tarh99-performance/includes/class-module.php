<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Tarh99_Perf_Module {

	abstract public function id();

	abstract public function title();

	abstract public function description();

	abstract public function boot();

	public function group() {
		return 'front';
	}

	public function default_enabled() {
		return true;
	}

	public function risk() {
		return 'low';
	}

	public function fields() {
		return array();
	}

	public function notes() {
		return '';
	}

	public function enabled() {
		return Tarh99_Perf_Settings::module_enabled( $this->id(), $this->default_enabled() );
	}

	public function opt( $field ) {
		$fields  = $this->fields();
		$default = isset( $fields[ $field ]['default'] ) ? $fields[ $field ]['default'] : null;
		return Tarh99_Perf_Settings::value( $this->id(), $field, $default );
	}

	public function opt_lines( $field ) {
		return Tarh99_Perf_Env::parse_lines( (string) $this->opt( $field ) );
	}

	public function opt_list( $field ) {
		$value = $this->opt( $field );
		return is_array( $value ) ? $value : array();
	}

	public function sanitize_field( $field, $raw ) {
		$fields = $this->fields();
		if ( ! isset( $fields[ $field ] ) ) {
			return null;
		}
		$def = $fields[ $field ];
		switch ( $def['type'] ) {
			case 'checkbox':
				return empty( $raw ) ? 0 : 1;
			case 'number':
				$value = is_numeric( $raw ) ? (float) $raw : (float) $def['default'];
				if ( isset( $def['min'] ) ) {
					$value = max( (float) $def['min'], $value );
				}
				if ( isset( $def['max'] ) ) {
					$value = min( (float) $def['max'], $value );
				}
				return ( isset( $def['step'] ) && (float) $def['step'] < 1 ) ? $value : (int) round( $value );
			case 'select':
				$raw = (string) $raw;
				return isset( $def['options'][ $raw ] ) ? $raw : $def['default'];
			case 'multicheck':
				$raw = is_array( $raw ) ? array_map( 'strval', $raw ) : array();
				return array_values( array_intersect( array_keys( $def['options'] ), $raw ) );
			case 'textarea':
				return sanitize_textarea_field( (string) $raw );
			case 'text':
			default:
				return sanitize_text_field( (string) $raw );
		}
	}
}
