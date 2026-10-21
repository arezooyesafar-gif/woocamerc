<?php

class Pooshe_Otf {
	private static string $meta_key = 'pooshe_user_login';
	public static int $time         = 120;

	private const RATE_LIMIT_KEY  = 'pooshe_otp_rate_';
	private const RATE_LIMIT_MAX  = 3;
	private const RATE_LIMIT_WINDOW = 600; // 10 minutes

	public static function create_code( int $userID, string $target, string $mobile = '' ) {
		if ( ! self::can_create( $userID ) ) {
			return false;
		}

		$ip = self::get_client_ip();
		if ( ! self::check_rate_limit( $ip ) ) {
			return false;
		}

		$code = rand( 10000, 99999 );
		if ( empty( $mobile ) ) {
			$mobile = Pooshe_Smart_Auth::get_user_mobile( $userID );
		}

		if ( ! pooshe_is_mobile( $mobile ) ) {
			return false;
		}

		$sender      = new Pooshe_Sender();
		$sender->send(
			array(
				'message' => sprintf( 'کد ورود شما:%s', $code ),
				'token'   => $code,
				'mobile'  => $mobile,
			)
		);

		update_user_meta(
			$userID,
			self::$meta_key,
			array(
				'code'   => md5( $code ),
				'target' => $target,
				'expire' => time() + self::$time,
			)
		);

		self::increment_rate_limit( $ip );

		return true;
	}

	private static function can_create( int $userID ) {
		$meta = get_user_meta( $userID, self::$meta_key, true );

		return empty( $meta['expire'] ) || time() > $meta['expire'];
	}

	private static function get_client_ip(): string {
		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
			return trim( $ip[0] );
		}

		if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}

		return '127.0.0.1';
	}

	private static function check_rate_limit( string $ip ): bool {
		$key    = self::RATE_LIMIT_KEY . md5( $ip );
		$data   = get_transient( $key );

		if ( ! is_array( $data ) ) {
			return true;
		}

		$window_start = $data['time'] ?? 0;
		$count        = $data['count'] ?? 0;

		if ( time() - $window_start > self::RATE_LIMIT_WINDOW ) {
			delete_transient( $key );
			return true;
		}

		return $count < self::RATE_LIMIT_MAX;
	}

	private static function increment_rate_limit( string $ip ): void {
		$key  = self::RATE_LIMIT_KEY . md5( $ip );
		$data = get_transient( $key );

		if ( ! is_array( $data ) || time() - ( $data['time'] ?? 0 ) > self::RATE_LIMIT_WINDOW ) {
			$data = [
				'time'  => time(),
				'count' => 0,
			];
		}

		$data['count']++;
		set_transient( $key, $data, self::RATE_LIMIT_WINDOW );
	}

	public static function verify( int $userID, string $code, string $target ) {
		if ( pooshe_is_demo() && $code === '12345' ) {
			return true;
		}

		$code = str_replace(
			array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ),
			array(
				0,
				1,
				2,
				3,
				4,
				5,
				6,
				7,
				8,
				9,
			),
			$code
		);
		$meta = get_user_meta( $userID, self::$meta_key, true );
		if ( ! is_array( $meta ) || empty( $meta ) ) {
			return false;
		}

		if ( empty( $meta['code'] ) || $meta['code'] !== md5( $code ) ) {
			return 0;
		}

		if ( empty( $meta['target'] ) || $meta['target'] !== $target ) {
			return - 1;
		}

		if ( empty( $meta['expire'] ) || time() > $meta['expire'] ) {
			return - 2;
		}

		update_user_meta(
			$userID,
			self::$meta_key,
			array(
				'code'   => false,
				'target' => false,
				'expire' => 0,
			)
		);

		return true;
	}
}
