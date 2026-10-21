<?php

class Pooshe_Gateway_Ippanel extends Pooshe_SMS_Gateway {
    public function get_id(): string {
        return 'ippanel';
    }

    public function get_name(): string {
        return 'ایپی پنل (فراز اس ام اس)';
    }

    public function send( string $message, array $mobiles, ?string $token = null ) {
        if ( empty( $this->username ) ) {
            return $this->error( 'API Key تنظیم نشده است' );
        }

        $mobiles = $this->normalize_mobiles( $mobiles );

        $pattern = pooshe_opt( 'pooshe_theme_settings_sms', 'sms_gateways_pattern' );

        if ( ! empty( $pattern ) && ! empty( $token ) ) {
            return $this->send_pattern( $pattern, $mobiles, $token );
        }

        return $this->send_simple( $message, $mobiles );
    }

    private function send_pattern( string $pattern, array $mobiles, string $token ) {
        $data = [
            'code'      => $pattern,
            'sender'    => $this->senderNumber,
            'recipient' => $mobiles[0],
            'variable'  => [ 'token' => $token ],
        ];

        $response = $this->http_post(
            'https://api2.ippanel.com/api/v1/sms/pattern/normal/send',
            json_encode( $data ),
            [
                'Apikey'       => $this->username,
                'Content-Type' => 'application/json',
                'accept'       => '*/*',
            ]
        );

        if ( is_string( $response ) ) {
            return $this->error( $response );
        }

        $body = json_decode( $response, true );

        if ( is_array( $body ) && ! empty( $body['code'] ) && (int) $body['code'] !== 200 ) {
            return $this->error( $body['error_message'] ?? 'خطا در ارسال' );
        }

        return $this->success();
    }

    private function send_simple( string $message, array $mobiles ) {
        $data = [
            'from'      => $this->senderNumber,
            'to'        => implode( ',', $mobiles ),
            'message'   => $message,
        ];

        $response = $this->http_post(
            'https://api2.ippanel.com/api/v1/sms/send/webservice/single',
            json_encode( $data ),
            [
                'Apikey'       => $this->username,
                'Content-Type' => 'application/json',
                'accept'       => '*/*',
            ]
        );

        if ( is_string( $response ) ) {
            return $this->error( $response );
        }

        $body = json_decode( $response, true );

        if ( is_array( $body ) && ! empty( $body['code'] ) && (int) $body['code'] !== 200 ) {
            return $this->error( $body['error_message'] ?? 'خطا در ارسال' );
        }

        return $this->success();
    }
}

Pooshe_SMS_Gateway::register( 'ippanel', 'Pooshe_Gateway_Ippanel' );
