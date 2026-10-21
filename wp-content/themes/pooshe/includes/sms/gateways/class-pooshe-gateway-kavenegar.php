<?php

class Pooshe_Gateway_Kavenegar extends Pooshe_SMS_Gateway {
    public function get_id(): string {
        return 'kavenegar';
    }

    public function get_name(): string {
        return 'کاوه نگار';
    }

    public function send( string $message, array $mobiles, ?string $token = null ) {
        if ( empty( $this->username ) ) {
            return $this->error( 'API Key تنظیم نشده است' );
        }

        $mobiles = $this->normalize_mobiles( $mobiles );

        $pattern = pooshe_opt( 'pooshe_theme_settings_sms', 'sms_gateways_pattern' );

        if ( ! empty( $pattern ) && ! empty( $token ) ) {
            return $this->send_lookup( $pattern, $mobiles, $token );
        }

        return $this->send_simple( $message, $mobiles );
    }

    private function send_lookup( string $pattern, array $mobiles, string $token ) {
        $to   = implode( ',', $mobiles );
        $url  = "https://api.kavenegar.com/v1/{$this->username}/verify/lookup.json";
        $url .= "?receptor=$to&template=$pattern&token=" . rawurlencode( $token );

        $response = $this->http_get( $url );

        if ( is_string( $response ) ) {
            return $this->error( $response );
        }

        $body = json_decode( $response );

        if ( ! empty( $body->return->status ) && (int) $body->return->status === 200 ) {
            return $this->success();
        }

        return $this->error( $body->return->message ?? 'خطا در ارسال' );
    }

    private function send_simple( string $message, array $mobiles ) {
        $to  = implode( ',', $mobiles );
        $url = "https://api.kavenegar.com/v1/{$this->username}/sms/send.json";
        $url .= "?sender=" . rawurlencode( $this->senderNumber ) . "&receptor=$to&message=" . rawurlencode( $message );

        $response = $this->http_get( $url );

        if ( is_string( $response ) ) {
            return $this->error( $response );
        }

        $body = json_decode( $response );

        if ( ! empty( $body->return->status ) && (int) $body->return->status === 200 ) {
            return $this->success();
        }

        return $this->error( $body->return->message ?? 'خطا در ارسال' );
    }
}

Pooshe_SMS_Gateway::register( 'kavenegar', 'Pooshe_Gateway_Kavenegar' );
Pooshe_SMS_Gateway::register( 'kavenegar_lookUp', 'Pooshe_Gateway_Kavenegar' );
