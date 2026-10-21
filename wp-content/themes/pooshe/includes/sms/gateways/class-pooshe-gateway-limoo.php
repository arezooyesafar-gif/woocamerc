<?php

class Pooshe_Gateway_Limoo extends Pooshe_SMS_Gateway {
    public function get_id(): string {
        return 'limosmscom';
    }

    public function get_name(): string {
        return 'لیمو اس ام اس (LimoSMS)';
    }

    public function send( string $message, array $mobiles, ?string $token = null ) {
        if ( empty( $this->username ) ) {
            return $this->error( 'ApiKey تنظیم نشده است' );
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
            'OtpId'        => $pattern,
            'MobileNumber' => [ $mobiles[0] ],
            'ReplaceToken' => [ $token ],
        ];

        $response = $this->http_post(
            'https://api.limosms.com/api/sendpatternmessage',
            json_encode( $data ),
            [
                'ApiKey'       => $this->username,
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
            'Message'        => $message,
            'MobileNumber'   => $mobiles,
            'SenderNumber'   => $this->senderNumber,
            'SendDateTime'   => null,
        ];

        $response = $this->http_post(
            'https://api.limosms.com/api/SendMessage',
            json_encode( $data ),
            [
                'ApiKey'       => $this->username,
                'Content-Type' => 'application/json',
                'accept'       => '*/*',
            ]
        );

        if ( is_string( $response ) ) {
            return $this->error( $response );
        }

        $body = json_decode( $response, true );

        if ( is_array( $body ) && ! empty( $body['IsSuccess'] ) ) {
            return $this->success();
        }

        return $this->error( $body['ErrorMessage'] ?? 'خطا در ارسال' );
    }
}

Pooshe_SMS_Gateway::register( 'limosmscom', 'Pooshe_Gateway_Limoo' );
