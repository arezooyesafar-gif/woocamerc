<?php

class Pooshe_Gateway_Melipayamak extends Pooshe_SMS_Gateway {
    public function get_id(): string {
        return 'melipayamak';
    }

    public function get_name(): string {
        return 'ملی پیامک (Payamak-Panel)';
    }

    public function send( string $message, array $mobiles, ?string $token = null ) {
        if ( empty( $this->username ) || empty( $this->password ) ) {
            return $this->error( 'نام کاربری یا رمز عبور تنظیم نشده است' );
        }

        $mobiles = $this->normalize_mobiles( $mobiles );

        $pattern = pooshe_opt( 'pooshe_theme_settings_sms', 'sms_gateways_pattern' );

        if ( ! empty( $pattern ) && ! empty( $token ) ) {
            return $this->send_pattern( $pattern, $mobiles, $token );
        }

        return $this->send_simple( $message, $mobiles );
    }

    private function send_pattern( string $pattern, array $mobiles, string $token ) {
        if ( class_exists( 'SoapClient' ) ) {
            return $this->send_pattern_soap( $pattern, $mobiles, $token );
        }

        return $this->error( 'SoapClient در سرور فعال نیست' );
    }

    private function send_pattern_soap( string $pattern, array $mobiles, string $token ) {
        try {
            $client = new SoapClient(
                'https://api.payamak-panel.com/post/send.asmx?wsdl',
                [ 'encoding' => 'UTF-8', 'connection_timeout' => 10 ]
            );

            $parameters = [
                'username' => $this->username,
                'password' => $this->password,
                'to'       => implode( ',', $mobiles ),
                'text'     => [ $token ],
                'bodyId'   => $pattern,
            ];

            $result = $client->SendByBaseNumber( $parameters )->SendByBaseNumberResult;

            if ( (int) $result > 20 ) {
                return $this->success();
            }

            return $this->error( "کد خطا: $result" );
        } catch ( SoapFault $ex ) {
            return $this->error( $ex->getMessage() );
        }
    }

    private function send_simple( string $message, array $mobiles ) {
        if ( class_exists( 'SoapClient' ) ) {
            return $this->send_simple_soap( $message, $mobiles );
        }

        return $this->send_simple_rest( $message, $mobiles );
    }

    private function send_simple_soap( string $message, array $mobiles ) {
        try {
            $client = new SoapClient(
                'https://api.payamak-panel.com/post/send.asmx?wsdl',
                [ 'encoding' => 'UTF-8', 'connection_timeout' => 10 ]
            );

            $parameters = [
                'username' => $this->username,
                'password' => $this->password,
                'to'       => implode( ',', $mobiles ),
                'from'     => $this->senderNumber,
                'text'     => $message,
                'isflash'  => false,
            ];

            $result = $client->SendSimpleSMS2( $parameters )->SendSimpleSMS2Result;

            if ( (int) $result > 20 ) {
                return $this->success();
            }

            return $this->error( "کد خطا: $result" );
        } catch ( SoapFault $ex ) {
            return $this->error( $ex->getMessage() );
        }
    }

    private function send_simple_rest( string $message, array $mobiles ) {
        $data = [
            'username' => $this->username,
            'password' => $this->password,
            'to'       => implode( ',', $mobiles ),
            'from'     => $this->senderNumber,
            'text'     => $message,
            'isflash'  => 'false',
        ];

        $response = $this->http_post(
            'https://api.payamak-panel.com/post/send.asmx/SendSimpleSMS2',
            http_build_query( $data ),
            [ 'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8' ]
        );

        if ( is_string( $response ) ) {
            return $this->error( $response );
        }

        return $this->success();
    }
}

Pooshe_SMS_Gateway::register( 'melipayamak', 'Pooshe_Gateway_Melipayamak' );
Pooshe_SMS_Gateway::register( 'melipayamakpattern', 'Pooshe_Gateway_Melipayamak' );
