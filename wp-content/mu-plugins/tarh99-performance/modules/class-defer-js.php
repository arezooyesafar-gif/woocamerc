<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Defer_Js extends Tarh99_Perf_Module {

	public function id() {
		return 'defer_js';
	}

	public function title() {
		return 'Defer اسکریپت‌های غیرضروری';
	}

	public function description() {
		return 'با API رسمی وردپرس (strategy=defer) اسکریپت‌های مستقل را defer می‌کند. اگر «Load JavaScript deferred» یا «Delay JavaScript execution» در WP Rocket روشن باشد این ماژول خودکار کنار می‌رود تا کار تکراری انجام نشود. وردپرس اگر defer کردن یک اسکریپت باعث خرابی وابستگی‌ها شود آن را بلاک‌کننده نگه می‌دارد.';
	}

	public function fields() {
		return array(
			'handles' => array(
				'type'    => 'textarea',
				'label'   => 'handle اسکریپت‌ها (هر خط یکی)',
				'default' => "sourcebuster-js\nwc-order-attribution\ncomment-reply",
			),
			'force'   => array(
				'type'    => 'checkbox',
				'label'   => 'حتی وقتی defer/delay راکت روشن است اجرا شود',
				'default' => 0,
			),
		);
	}

	public function boot() {
		add_action( 'wp_enqueue_scripts', array( $this, 'apply' ), 9999 );
		add_action( 'wp_print_footer_scripts', array( $this, 'apply' ), 1 );
	}

	private function rocket_handles_js() {
		return (bool) Tarh99_Perf_Env::rocket_option( 'defer_all_js' ) || (bool) Tarh99_Perf_Env::rocket_option( 'delay_js' );
	}

	public function apply() {
		global $wp_version;
		if ( ! Tarh99_Perf_Env::front_ok() || version_compare( $wp_version, '6.3', '<' ) ) {
			return;
		}
		if ( ! $this->opt( 'force' ) && $this->rocket_handles_js() ) {
			return;
		}
		$scripts = wp_scripts();
		foreach ( $this->opt_lines( 'handles' ) as $handle ) {
			if ( ! isset( $scripts->registered[ $handle ] ) || in_array( $handle, (array) $scripts->done, true ) ) {
				continue;
			}
			if ( Tarh99_Perf_Assets::is_critical( $handle ) ) {
				continue;
			}
			$current = $scripts->get_data( $handle, 'strategy' );
			if ( 'async' === $current || 'defer' === $current ) {
				continue;
			}
			$scripts->add_data( $handle, 'strategy', 'defer' );
		}
	}
}
