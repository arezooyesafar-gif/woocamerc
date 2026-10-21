<?php

abstract class Pooshe_SMS_Gateway {
    protected string $username;
    protected string $password;
    protected string $senderNumber;
    protected array $config;

    private static array $registry = [];

    public function __construct( string $username, string $password, string $senderNumber, array $config = [] ) {
        $this->username      = $username;
        $this->password      = $password;
        $this->senderNumber  = $senderNumber;
        $this->config        = $config;
    }

    abstract public function get_id(): string;

    abstract public function get_name(): string;

    abstract public function send( string $message, array $mobiles, ?string $token = null );

    public static function register( string $id, string $class_name ): void {
        self::$registry[ $id ] = $class_name;
    }

    public static function get_registered(): array {
        return self::$registry;
    }

    public static function has_gateway( string $id ): bool {
        return isset( self::$registry[ $id ] );
    }

    public static function create( string $id ): ?self {
        if ( ! isset( self::$registry[ $id ] ) ) {
            return null;
        }

        $username = pooshe_opt( 'pooshe_theme_settings_sms', 'sms_username' );
        $password = pooshe_opt( 'pooshe_theme_settings_sms', 'sms_password' );
        $sender   = pooshe_opt( 'pooshe_theme_settings_sms', 'sms_sender_number' );

        $class = self::$registry[ $id ];
        return new $class( $username, $password, $sender );
    }

    protected function normalize_mobiles( array $mobiles ): array {
        $sender = new Pooshe_Sender();
        $result = $sender->modifyMobile( $mobiles );
        return is_array( $result ) ? $result : [ $result ];
    }

    protected function success(): bool {
        return true;
    }

    protected function error( string $message ): string {
        return $message;
    }

    protected function http_get( string $url, array $headers = [], int $timeout = 30 ) {
        $response = wp_remote_get( $url, [
            'headers' => $headers,
            'timeout' => $timeout,
        ] );
        return $this->handle_response( $response );
    }

    protected function http_post( string $url, $body, array $headers = [], int $timeout = 30 ) {
        $response = wp_remote_post( $url, [
            'body'    => $body,
            'headers' => $headers,
            'timeout' => $timeout,
        ] );
        return $this->handle_response( $response );
    }

    protected function handle_response( $response ) {
        if ( is_wp_error( $response ) ) {
            return $this->error( $response->get_error_message() );
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );

        if ( $code < 200 || $code >= 300 ) {
            return $this->error( "HTTP $code: $body" );
        }

        return $body;
    }
}
