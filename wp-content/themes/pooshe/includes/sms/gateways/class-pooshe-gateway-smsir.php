<?php

class Pooshe_Gateway_SmsIr extends Pooshe_SMS_Gateway {
    public function get_id(): string {
        return 'smsir';
    }

    public function get_name(): string {
        return 'SMS.ir';
    }

    public function send( string $message, array $mobiles, ?string $token = null ) {
        if ( empty( $this->username ) ) {
            return $this->error( 'x-api-key تنظیم نشده است' );
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
            'message'   => $message,
            'sender'    => $this->senderNumber,
            'recipient' => $mobiles[0],
            'variable'  => [ 'token' => $token ],
        ];

        $response = $this->http_post(
            'https://api.sms.ir/v1/send/verify',
            json_encode( $data ),
            [
                'x-api-key'    => $this->username,
                'Content-Type' => 'application/json',
                'Accept'       => 'text/plain',
            ]
        );

        if ( is_string( $response ) ) {
            return $this->error( $response );
        }

        $body = json_decode( $response, true );

        if ( is_array( $body ) && ! empty( $body['status'] ) && (int) $body['status'] === 1 ) {
            return $this->success();
        }

        return $this->error( $body['message'] ?? 'خطا در ارسال' );
    }

    private function send_simple( string $message, array $mobiles ) {
        $data = [
            'lineNumber' => $this->senderNumber,
            'messageTexts' => [ $message ],
            'mobiles'    => $mobiles,
        ];

        $response = $this->http_post(
            'https://api.sms.ir/v1/send/bulk',
            json_encode( $data ),
            [
                'x-api-key'    => $this->username,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ]
        );

        if ( is_string( $response ) ) {
            return $this->error( $response );
        }

        $body = json_decode( $response, true );

        if ( is_array( $body ) && ! empty( $body['status'] ) && (int) $body['status'] === 1 ) {
            return $this->success();
        }

        return $this->error( $body['message'] ?? 'خطا در ارسال' );
    }
}

Pooshe_SMS_Gateway::register( 'smsir', 'Pooshe_Gateway_SmsIr' );
Pooshe_SMS_Gateway::register( 'smsir_pattern', 'Pooshe_Gateway_SmsIr' );
