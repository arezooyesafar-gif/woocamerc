<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'TARH99_PERF_VERSION' ) ) {
	return;
}

define( 'TARH99_PERF_VERSION', '1.6.0' );
define( 'TARH99_PERF_DIR', __DIR__ . '/' );
define( 'TARH99_PERF_URL', trailingslashit( WPMU_PLUGIN_URL ) . 'tarh99-performance/' );

require_once TARH99_PERF_DIR . 'includes/class-env.php';
require_once TARH99_PERF_DIR . 'includes/class-settings.php';
require_once TARH99_PERF_DIR . 'includes/class-cache-store.php';
require_once TARH99_PERF_DIR . 'includes/class-assets.php';
require_once TARH99_PERF_DIR . 'includes/class-css-tree.php';
require_once TARH99_PERF_DIR . 'includes/class-module.php';
require_once TARH99_PERF_DIR . 'includes/class-plugin.php';

Tarh99_Perf_Plugin::instance();
