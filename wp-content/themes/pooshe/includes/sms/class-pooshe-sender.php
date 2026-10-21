<?php

class Pooshe_Sender {

    public function EnglishNumberMobile( $mobile ) {
        if ( is_array( $mobile ) ) {
            return array_map( array( $this, __FUNCTION__ ), $mobile );
        }

        $mobile = sanitize_text_field( $mobile );
        $mobile = str_ireplace(
            array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ),
            array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
            $mobile
        );
        $mobile = str_ireplace(
            array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ),
            array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
            $mobile
        );

        return $mobile;
    }

    public function modifyMobile( $mobile ) {
        if ( is_array( $mobile ) ) {
            return array_map( array( $this, __FUNCTION__ ), $mobile );
        }

        $mobile   = $this->EnglishNumberMobile( $mobile );
        $modified = preg_replace( '/\D/is', '', (string) $mobile );

        if ( substr( $modified, 0, 1 ) === '+' ) {
            return $modified;
        }

        if ( substr( $modified, 0, 2 ) === '00' ) {
            return '+' . substr( $modified, 2 );
        }

        if ( substr( $modified, 0, 1 ) === '0' ) {
            return $modified;
        }

        if ( ! empty( $modified ) ) {
            return '0' . $modified;
        }

        return str_replace( '+980', '0', $modified );
    }

    public function send( $data ) {
        $message = ! empty( $data['message'] ) ? esc_textarea( $data['message'] ) : '';
        $mobile  = ! empty( $data['mobile'] ) ? $data['mobile'] : '';
        $token   = ! empty( $data['token'] ) ? $data['token'] : '';

        if ( ! is_array( $mobile ) ) {
            $mobile = explode( ',', (string) $mobile );
        }

        $mobile = $this->modifyMobile( $mobile );
        $mobile = explode( ',', implode( ',', (array) $mobile ) );
        $mobile = array_map( 'trim', $mobile );
        $mobile = array_unique( array_filter( $mobile ) );

        if ( empty( $mobile ) ) {
            return 'شماره موبایل خالی است.';
        }

        if ( empty( $message ) && empty( $token ) ) {
            return 'متن پیامک خالی است.';
        }

        $gateway_method = pooshe_opt( 'pooshe_theme_settings_sms', 'sms_gateway' );

        if ( empty( $gateway_method ) || $gateway_method === 'none' ) {
            return 'تنظیمات درگاه پیامک انجام نشده است.';
        }

        $result = $this->try_new_gateway( $gateway_method, $message, $mobile, $token );

        if ( $result !== null ) {
            return $result;
        }

        return $this->try_legacy_gateway( $gateway_method, $message, $mobile, $token );
    }

    private function try_new_gateway( string $method, string $message, array $mobiles, ?string $token ) {
        if ( ! Pooshe_SMS_Gateway::has_gateway( $method ) ) {
            return null;
        }

        $gateway = Pooshe_SMS_Gateway::create( $method );

        if ( ! $gateway ) {
            return null;
        }

        try {
            return $gateway->send( $message, $mobiles, $token );
        } catch ( Exception $e ) {
            return 'خطا: ' . $e->getMessage();
        }
    }

    private function try_legacy_gateway( string $method, string $message, array $mobiles, ?string $token ) {
        $gateway_object = Pooshe_SMS_Gateways::init();

        if ( ! method_exists( $gateway_object, $method ) ) {
            return 'تابع درگاه پیامکی شما داخل کلاس درگاه های پیامکی وجود ندارد.';
        }

        try {
            $gateway_object->mobile  = $mobiles;
            $gateway_object->message = $message;
            $gateway_object->token   = $token;

            return $gateway_object->$method( [
                'message' => $message,
                'mobile'  => implode( ',', $mobiles ),
                'token'   => $token,
            ] );

        } catch ( Exception $e ) {
            return $e->getMessage();
        }
    }
}
