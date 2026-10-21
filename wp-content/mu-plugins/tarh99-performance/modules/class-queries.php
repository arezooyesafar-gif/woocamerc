<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Queries extends Tarh99_Perf_Module {

	const ESHTERAKYAR_STATE = 'tarh99_perf_eshterakyar_schema';

	private $eshterakyar = null;

	public function id() {
		return 'queries';
	}

	public function group() {
		return 'server';
	}

	public function title() {
		return 'حذف کوئری‌های تکراری در هر درخواست';
	}

	public function description() {
		return 'افزونه اشتراک‌یار در هر درخواست غیرکش (فرانت، پیشخوان، ایجکس و Heartbeat) حدود ۱۱ کوئری SHOW TABLES/DESC/SHOW INDEX برای بررسی ساختار جداولش اجرا می‌کند. این ماژول همان بررسی را فقط هر چند ساعت یک‌بار و بلافاصله بعد از تغییر نسخه افزونه اجرا می‌کند؛ بقیه رفتار افزونه دقیقاً همان می‌ماند. فقط روی نسخه‌های تست‌شده اشتراک‌یار فعال می‌شود.';
	}

	public function fields() {
		return array(
			'eshterakyar'       => array(
				'type'    => 'checkbox',
				'label'   => 'محدودکردن بررسی ساختار جداول اشتراک‌یار',
				'default' => 1,
			),
			'eshterakyar_hours' => array(
				'type'    => 'number',
				'label'   => 'فاصله بررسی (ساعت)',
				'default' => 12,
				'min'     => 1,
				'max'     => 168,
			),
		);
	}

	public function boot() {
		if ( $this->opt( 'eshterakyar' ) ) {
			add_action( 'plugins_loaded', array( $this, 'swap_eshterakyar' ), 50 );
		}
	}

	private function eshterakyar_supported() {
		return class_exists( 'Eshterakyar' ) && defined( 'ESHTERAKYAR_VERSION' ) && in_array( ESHTERAKYAR_VERSION, array( '1.5.0' ), true ) && method_exists( 'Eshterakyar', 'get_instance' );
	}

	private function eshterakyar_classes() {
		return array(
			'Eshterakyar_Admin',
			'Eshterakyar_Frontend',
			'Eshterakyar_Shortcodes',
			'Eshterakyar_WooCommerce_Integration',
			'Eshterakyar_Notifications',
			'Eshterakyar_Ajax',
			'Eshterakyar_Subscription_Page',
			'Eshterakyar_Content_Access',
			'Eshterakyar_Auto_Membership',
		);
	}

	public function swap_eshterakyar() {
		if ( ! $this->eshterakyar_supported() ) {
			return;
		}
		$instance = Eshterakyar::get_instance();
		$priority = has_action( 'init', array( $instance, 'load_classes' ) );
		if ( false === $priority ) {
			return;
		}
		foreach ( $this->eshterakyar_classes() as $class ) {
			if ( ! class_exists( $class ) || ! method_exists( $class, 'get_instance' ) ) {
				return;
			}
		}
		$this->eshterakyar = $instance;
		remove_action( 'init', array( $instance, 'load_classes' ), $priority );
		add_action( 'init', array( $this, 'eshterakyar_load_classes' ), $priority );
	}

	public function eshterakyar_load_classes() {
		if ( ! defined( 'ESHTERAKYAR_PLUGIN_DIR' ) || ! is_readable( ESHTERAKYAR_PLUGIN_DIR . 'includes/class-database.php' ) ) {
			if ( $this->eshterakyar ) {
				$this->eshterakyar->load_classes();
			}
			return;
		}
		require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-database.php';

		$signature = ESHTERAKYAR_VERSION . '|' . ( defined( 'ESHTERAKYAR_DB_REVISION' ) ? ESHTERAKYAR_DB_REVISION : '0' ) . '|' . (string) get_option( 'eshterakyar_db_revision', '' );
		$state     = get_option( self::ESHTERAKYAR_STATE );
		$interval  = max( 1, (int) $this->opt( 'eshterakyar_hours' ) ) * HOUR_IN_SECONDS;
		$due       = ! is_array( $state ) || ! isset( $state['sig'], $state['time'] ) || $state['sig'] !== $signature || ( time() - (int) $state['time'] ) > $interval;

		if ( $due && class_exists( 'Eshterakyar_Database' ) && method_exists( 'Eshterakyar_Database', 'update_tables' ) ) {
			Eshterakyar_Database::update_tables();
			$signature = ESHTERAKYAR_VERSION . '|' . ( defined( 'ESHTERAKYAR_DB_REVISION' ) ? ESHTERAKYAR_DB_REVISION : '0' ) . '|' . (string) get_option( 'eshterakyar_db_revision', '' );
			update_option(
				self::ESHTERAKYAR_STATE,
				array(
					'sig'  => $signature,
					'time' => time(),
				),
				true
			);
		}

		foreach ( $this->eshterakyar_classes() as $class ) {
			call_user_func( array( $class, 'get_instance' ) );
		}
	}
}
