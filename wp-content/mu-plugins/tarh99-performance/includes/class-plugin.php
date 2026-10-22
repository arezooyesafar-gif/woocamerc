<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Tarh99_Perf_Plugin {

	private static $instance = null;
	private $modules = array();
	private $booted = array();

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->load_modules();
		$this->early();
		add_action( 'plugins_loaded', array( $this, 'boot_modules' ), 1 );
		if ( is_admin() ) {
			require_once TARH99_PERF_DIR . 'includes/class-admin.php';
			new Tarh99_Perf_Admin( $this );
		}
		add_action( 'admin_init', array( $this, 'ensure_options' ) );
	}

	private function load_modules() {
		$files = array(
			'class-head-cleanup.php'   => 'Tarh99_Perf_Module_Head_Cleanup',
			'class-fonts.php'          => 'Tarh99_Perf_Module_Fonts',
			'class-lcp.php'            => 'Tarh99_Perf_Module_Lcp',
			'class-pooshe-compat.php'  => 'Tarh99_Perf_Module_Pooshe_Compat',
			'class-css-usage.php'      => 'Tarh99_Perf_Module_Css_Usage',
			'class-icon-fonts.php'     => 'Tarh99_Perf_Module_Icon_Fonts',
			'class-elementor.php'      => 'Tarh99_Perf_Module_Elementor',
			'class-cart-fragments.php' => 'Tarh99_Perf_Module_Cart_Fragments',
			'class-asset-rules.php'    => 'Tarh99_Perf_Module_Asset_Rules',
			'class-defer-js.php'       => 'Tarh99_Perf_Module_Defer_Js',
			'class-heartbeat.php'      => 'Tarh99_Perf_Module_Heartbeat',
			'class-ajax-cache.php'     => 'Tarh99_Perf_Module_Ajax_Cache',
			'class-search.php'         => 'Tarh99_Perf_Module_Search',
			'class-wc-session.php'     => 'Tarh99_Perf_Module_Wc_Session',
			'class-queries.php'        => 'Tarh99_Perf_Module_Queries',
			'class-wc-admin.php'       => 'Tarh99_Perf_Module_Wc_Admin',
			'class-admin-http.php'     => 'Tarh99_Perf_Module_Admin_Http',
			'class-cleanup.php'        => 'Tarh99_Perf_Module_Cleanup',
			'class-image-compress.php' => 'Tarh99_Perf_Module_Image_Compress',
			'class-revisions.php'      => 'Tarh99_Perf_Module_Revisions',
			'class-monitor.php'        => 'Tarh99_Perf_Module_Monitor',
			'class-profiler.php'       => 'Tarh99_Perf_Module_Profiler',
			'class-theme-guard.php'    => 'Tarh99_Perf_Module_Theme_Guard',
		);
		foreach ( $files as $file => $class ) {
			$path = TARH99_PERF_DIR . 'modules/' . $file;
			if ( ! is_readable( $path ) ) {
				continue;
			}
			require_once $path;
			if ( class_exists( $class ) ) {
				$module                          = new $class();
				$this->modules[ $module->id() ] = $module;
			}
		}
	}

	private function early() {
		$profiler = $this->module( 'profiler' );
		if ( $profiler && $profiler->enabled() && method_exists( $profiler, 'early' ) ) {
			try {
				$profiler->early();
			} catch ( Throwable $e ) {
				unset( $e );
			}
		}
		$ajax = $this->module( 'ajax_cache' );
		if ( ! $ajax || ! $ajax->enabled() || ! method_exists( $ajax, 'early_serve' ) ) {
			return;
		}
		try {
			$ajax->early_serve();
		} catch ( Throwable $e ) {
			return;
		}
	}

	public function boot_modules() {
		foreach ( $this->modules as $id => $module ) {
			if ( isset( $this->booted[ $id ] ) ) {
				continue;
			}
			if ( ! $module->enabled() ) {
				if ( method_exists( $module, 'preview_boot' ) && ! Tarh99_Perf_Settings::safe_mode() ) {
					try {
						$module->preview_boot();
					} catch ( Throwable $e ) {
						continue;
					}
				}
				continue;
			}
			try {
				$module->boot();
				$this->booted[ $id ] = true;
			} catch ( Throwable $e ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'Tarh99 Performance: module ' . $id . ' failed: ' . $e->getMessage() );
				}
			}
		}
	}

	public function modules() {
		return $this->modules;
	}

	public function module( $id ) {
		return isset( $this->modules[ $id ] ) ? $this->modules[ $id ] : null;
	}

	public function is_booted( $id ) {
		return isset( $this->booted[ $id ] );
	}

	public function ensure_options() {
		if ( false === get_option( Tarh99_Perf_Cache_Store::VERSION_OPT, false ) ) {
			add_option( Tarh99_Perf_Cache_Store::VERSION_OPT, 1, '', true );
		}
	}
}
