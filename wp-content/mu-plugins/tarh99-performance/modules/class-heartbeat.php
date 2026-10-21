<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Heartbeat extends Tarh99_Perf_Module {

	public function id() {
		return 'heartbeat';
	}

	public function group() {
		return 'server';
	}

	public function title() {
		return 'کنترل Heartbeat';
	}

	public function description() {
		return 'Heartbeat هر ۱۵ تا ۶۰ ثانیه یک درخواست کامل به admin-ajax می‌فرستد. در فرانت خاموش و در پیشخوان کندتر می‌شود. در صفحه ویرایش، ذخیره خودکار و قفل ویرایش همچنان کار می‌کنند. اگر کنترل Heartbeat در WP Rocket روشن است، یکی از دو مورد را خاموش کنید.';
	}

	public function fields() {
		return array(
			'front'           => array(
				'type'    => 'select',
				'label'   => 'فرانت‌اند',
				'options' => array(
					'disable' => 'غیرفعال',
					'slow'    => 'هر ۱۲۰ ثانیه',
					'default' => 'پیش‌فرض وردپرس',
				),
				'default' => 'disable',
			),
			'admin_interval'  => array(
				'type'    => 'number',
				'label'   => 'فاصله در پیشخوان (ثانیه، ۰ = پیش‌فرض)',
				'default' => 120,
				'min'     => 0,
				'max'     => 120,
			),
			'editor_interval' => array(
				'type'    => 'number',
				'label'   => 'فاصله در صفحه ویرایش نوشته/محصول (ثانیه، ۰ = پیش‌فرض)',
				'default' => 60,
				'min'     => 0,
				'max'     => 120,
			),
		);
	}

	public function boot() {
		add_filter( 'heartbeat_settings', array( $this, 'settings' ), 99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'front_dequeue' ), 9999 );
		add_action( 'wp_print_footer_scripts', array( $this, 'front_dequeue' ), 1 );
	}

	public function settings( $settings ) {
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}
		if ( ! is_admin() ) {
			if ( 'slow' === $this->opt( 'front' ) ) {
				$settings['interval'] = 120;
			}
			return $settings;
		}
		global $pagenow;
		$editor   = in_array( $pagenow, array( 'post.php', 'post-new.php' ), true );
		$interval = (int) ( $editor ? $this->opt( 'editor_interval' ) : $this->opt( 'admin_interval' ) );
		if ( $interval > 0 ) {
			$settings['interval'] = max( 15, min( 120, $interval ) );
		}
		return $settings;
	}

	public function front_dequeue() {
		if ( 'disable' !== $this->opt( 'front' ) || ! Tarh99_Perf_Env::front_ok() ) {
			return;
		}
		if ( wp_script_is( 'heartbeat', 'enqueued' ) ) {
			Tarh99_Perf_Assets::safe_dequeue( 'js', 'heartbeat' );
		}
	}
}
