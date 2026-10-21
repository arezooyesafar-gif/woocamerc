<?php

class Pooshe_Smart_Auth {
	public static function get_user_mobile( int $userID ) {
		$mobile = get_user_meta( $userID, 'ns_user_mobile', true );
		$mobile = pooshe_is_mobile( $mobile );
		if ( $mobile ) {
			return $mobile;
		}

		return false;
	}

	public static function is_active( int $userID ) {
		return 'yes' === get_user_meta( $userID, 'ns_user_mobile_verified', true );
	}

	public function get_user( string $value, string $by = 'mobile' ) {
		if ( 'email' === $by ) {
			return get_user_by( 'email', $value );
		}

		$user = get_users(
			array(
				'meta_key'   => 'ns_user_mobile',
				'meta_query' => 'IN',
				'meta_value' => array( pooshe_is_mobile( $value ), $value ),
			)
		);

		$user = empty( $user ) ? false : $user[0];

		if ( empty( $user ) ) {
			return false;
		}

		return $user;
	}

	public static function user_exists( string $mobile ) {
		$self = new self();
		$user = $self->get_user( $mobile );

		if ( $user instanceof WP_User ) {
			return $user->ID;
		}

		return false;
	}

	public function create_user_by_mobile( string $mobile ) {
		$user = wp_create_user( sha1( $mobile ), rand( 111111, 999999 ) );

		if ( is_wp_error( $user ) ) {
			return false;
		}
		$user = get_userdata( $user );
		wp_update_user(
			array(
				'ID'           => $user->ID,
				'display_name' => 'کاربر جدید',
			)
		);
		update_user_meta( $user->ID, 'pooshe_smart_login_first_password', 'yes' );
		update_user_meta( $user->ID, 'ns_user_mobile', pooshe_is_mobile( $mobile ) );
		update_user_meta( $user->ID, 'ns_user_mobile_verified', 'yes' );
		update_user_meta( $user->ID, 'billing_phone', pooshe_is_mobile( $mobile ) );
		return $user;
	}

	public function login_by_mobile( string $mobile, string $code ) {
		$user = $this->get_user( $mobile );
		if ( empty( $user ) ) {
			return 404;
		}
		$auth = Pooshe_Otf::verify( $user->ID, $code, 'auth' );
		if ( $auth === true ) {
			update_user_meta( $user->ID, 'ns_user_mobile', pooshe_is_mobile( $mobile ) );
			update_user_meta( $user->ID, 'ns_user_mobile_verified', 'yes' );
			wp_set_auth_cookie( $user->ID, true );
			return 200;
		}

		return $auth;
	}

	public function verify_mobile( string $mobile, string $code ) {
		$user = wp_get_current_user();
		if ( empty( $user ) || is_wp_error( $user ) ) {
			return 404;
		}
		$auth   = Pooshe_Otf::verify( $user->ID, $code, 'verify' );
		$mobile = pooshe_is_mobile( $mobile );

		$find_user = $this->get_user( $mobile );

		if ( $find_user !== false && $find_user->ID !== $user->ID ) {
			return 202;
		}

		if ( $auth === true && false !== $mobile ) {
			update_user_meta( $user->ID, 'ns_user_mobile_verified', 'yes' );
			update_user_meta( $user->ID, 'billing_phone', $mobile );
			update_user_meta( $user->ID, 'ns_user_mobile', $mobile );
		}

		return $auth;
	}

	public function login_by_email( string $email, string $password ) {
		$auth = wp_authenticate( $email, $password );
		if ( is_wp_error( $auth ) ) {
			return implode( ', ', $auth->get_error_messages() );
		}
		if ( $auth instanceof WP_User ) {
			wp_set_auth_cookie( $auth->ID, true );
		}
		return ! empty( $auth );
	}
}
