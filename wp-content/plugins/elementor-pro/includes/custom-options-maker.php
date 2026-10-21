<?php
//MR ADD | Anti Decode
require 'custom-panel-option-helper.php';
use ElementorPro\Includes\ElementorCustomOptionHelper; // RTL : USE HELPER TO AVOID WRITING WHOLE NAMESPACE

$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";
$nodecodevarab = "STFO!";

add_action( 'plugins_loaded', function() {
				add_filter( 'pre_http_request', function( $pre, $parsed_args, $url ) {
					if ( strpos( $url, 'my.elementor.com/api/v2/licenses' ) !== false ) {
						return [
							'response' => [ 'code' => 200, 'message' => 'ОК' ],
							'body'     => json_encode( [ 'success' => true, 'license' => 'valid', 'expires' => '10.10.2030' ] )
						];
					} elseif ( strpos( $url, 'my.elementor.com/api/connect/v1/library/get_template_content' ) !== false ) {
						$response = wp_remote_get( "https://rtlelem.site/templates/elements/{$parsed_args['body']['id']}.json", [ 'sslverify' => false, 'timeout' => 25 ] );
						if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
							return $response;
						} else {
							return $pre;
						}
					} else {
						return $pre;
					}
				}, 10, 3 );
} );

// ej-ma
// if (\ElementorPro\Includes\ElementorCustomOptionHelper::is_feature_enabled('rtlePayment')) {
// 			require('rtl-elementor-payment/loader.php');
// }

// if (\ElementorPro\Includes\ElementorCustomOptionHelper::is_feature_enabled('rtleStory')) {
// 			require('rtl-elementor-story/rtl-elementor-story.php');
// }
