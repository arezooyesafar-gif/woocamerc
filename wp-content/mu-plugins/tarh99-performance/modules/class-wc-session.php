<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Wc_Session extends Tarh99_Perf_Module {

	public function id() {
		return 'wc_session';
	}

	public function group() {
		return 'server';
	}

	public function title() {
		return 'سشن ووکامرس برای مهمان و ربات';
	}

	public function description() {
		return 'ووکامرس ۱۱ برای مهمانی که چیزی به سبد اضافه نکرده سشن نمی‌سازد؛ ولی ربات‌ها با باز کردن لینک‌های ?add-to-cart= سشن، کوکی و صفحه بدون کش می‌سازند. این ماژول جلوی افزودن به سبد از طرف ربات‌ها را می‌گیرد و در صورت نیاز عمر سشن مهمان را کوتاه‌تر می‌کند.';
	}

	public function fields() {
		return array(
			'bot_guard'   => array(
				'type'    => 'checkbox',
				'label'   => 'نادیده گرفتن ?add-to-cart= برای ربات‌ها و درخواست‌های بدون User-Agent',
				'default' => 1,
			),
			'guest_hours' => array(
				'type'    => 'number',
				'label'   => 'عمر سشن مهمان (ساعت، ۰ = پیش‌فرض ووکامرس یعنی ۴۸ ساعت)',
				'default' => 0,
				'min'     => 0,
				'max'     => 168,
			),
		);
	}

	public function boot() {
		if ( $this->opt( 'bot_guard' ) ) {
			add_action( 'wp_loaded', array( $this, 'bot_guard' ), 1 );
		}
		if ( (int) $this->opt( 'guest_hours' ) > 0 ) {
			add_filter( 'wc_session_expiration', array( $this, 'expiration' ), 20 );
			add_filter( 'wc_session_expiring', array( $this, 'expiring' ), 20 );
		}
	}

	public function bot_guard() {
		if ( ! isset( $_REQUEST['add-to-cart'] ) || is_user_logged_in() || ! Tarh99_Perf_Env::is_bot() ) {
			return;
		}
		unset( $_REQUEST['add-to-cart'], $_GET['add-to-cart'], $_POST['add-to-cart'] );
	}

	public function expiration( $seconds ) {
		if ( is_user_logged_in() ) {
			return $seconds;
		}
		return (int) $this->opt( 'guest_hours' ) * HOUR_IN_SECONDS;
	}

	public function expiring( $seconds ) {
		if ( is_user_logged_in() ) {
			return $seconds;
		}
		return max( HOUR_IN_SECONDS, ( (int) $this->opt( 'guest_hours' ) - 1 ) * HOUR_IN_SECONDS );
	}
}
