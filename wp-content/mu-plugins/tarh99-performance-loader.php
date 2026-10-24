<?php
/**
 * Plugin Name: Tarh99 Performance
 * Description: بهینه‌سازی ماژولار سرعت فروشگاه طرح۹۹ (فرانت‌اند، سرور، پیشخوان و پایداری).
 * Version: 1.9.7
 * Requires PHP: 7.4
 * Author: Tarh99
 * License: GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'TARH99_PERF_DISABLE' ) && TARH99_PERF_DISABLE ) {
	return;
}

if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	return;
}

$tarh99_perf_main = __DIR__ . '/tarh99-performance/tarh99-performance.php';

if ( is_readable( $tarh99_perf_main ) ) {
	require_once $tarh99_perf_main;
}

unset( $tarh99_perf_main );
