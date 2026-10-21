<?php

defined( 'ABSPATH' ) || exit;

class Pooshe_SMS_Gateways {



	private static $_instance;
	public $mobile       = array();
	public $message      = '';
	public $token        = '';
	public $senderNumber = '';
	private $username    = '';
	private $password    = '';

	public function __construct() {
		$this->username     = pooshe_opt( 'pooshe_theme_settings_sms' , 'sms_gateway_username' );
		$this->password     = pooshe_opt( 'pooshe_theme_settings_sms' , 'sms_gateway_password' );
		$this->senderNumber = pooshe_opt( 'pooshe_theme_settings_sms' , 'sms_gateway_sender' );
	}

	public static function init() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	protected static function _soap_send_sms( string $endpoint ) {
		$self = self::init();
		if ( empty( $self->username ) || empty( $self->password ) ) {
			return false;
		}

		try {
			$client = new SoapClient( $endpoint, array( 'encoding' => 'UTF-8', 'connection_timeout' => 10 ) );
			$result = $client->SendSms( array(
				'username' => $self->username,
				'password' => $self->password,
				'from'     => $self->senderNumber,
				'to'       => $self->mobile,
				'text'     => $self->message,
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			) )->SendSmsResult;

			return (int) $result === 1 ?: $result;
		} catch ( SoapFault $ex ) {
			return $ex->getMessage();
		}
	}

	protected static function _send_via_soap( string $endpoint, string $method, array $params, bool $is_pattern = false ) {
		if ( ! class_exists( 'SoapClient' ) ) {
			return 'SoapClient در سرور فعال نیست';
		}

		try {
			$client = new SoapClient( $endpoint, array( 'encoding' => 'UTF-8', 'connection_timeout' => 10 ) );
			$result = $client->$method( $params );

			if ( $is_pattern && isset( $result->{$method . 'Result'} ) ) {
				$code = (int) $result->{$method . 'Result'};
				return $code > 20 ?: "SOAP Error $code";
			}

			if ( isset( $result->{$method . 'Result'} ) ) {
				$code = (int) $result->{$method . 'Result'};
				return $code > 20 ?: "SOAP Error $code";
			}

			return $result;
		} catch ( SoapFault $ex ) {
			return $ex->getMessage();
		}
	}

	protected static function _build_soap_send( string $endpoint, bool $is_pattern = false ) {
		$self = self::init();
		$to   = implode( ',', $self->mobile );

		if ( $is_pattern ) {
			$pattern = pooshe_opt( 'pooshe_theme_settings_sms', 'sms_gateways_pattern' );
			return $self->_send_via_soap(
				$endpoint,
				'SendByBaseNumber',
				array(
					'username' => $self->username,
					'password' => $self->password,
					'to'       => $to,
					'text'     => array( $self->token ),
					'bodyId'   => $pattern,
				),
				true
			);
		}

		return $self->_send_via_soap(
			$endpoint,
			'SendSimpleSMS2',
			array(
				'username' => $self->username,
				'password' => $self->password,
				'to'       => $to,
				'from'     => $self->senderNumber,
				'text'     => $self->message,
				'isflash'  => false,
			)
		);
	}

	public static function get_sms_gateway() {

		$gateway = array(
			'sunwaysms'     => 'SunwaySMS.com',
			'parandsms'     => 'ParandSMS.ir',
			'gamapayamak'   => 'GAMAPayamak.com',
			'smsfa'         => 'SMSFa.ir',
			'aradsms'       => 'Arad-SMS.ir',
			'farapayamak'   => 'FaraPayamak.ir',
			'payamafraz'    => 'PayamAfraz.com',
			'niazpardaz'    => 'SMS.NiazPardaz.com',
			'niazpardaz_'   => 'Login.NiazPardaz.ir',
			'yektasms'      => 'Yektatech.ir',
			'smsbefrest'    => 'SmsBefrest.ir',
			'relax'         => 'Relax.ir',
			'paaz'          => 'Paaz.ir',
			'postgah'       => 'Postgah.info',
			'idehpayam'     => 'IdehPayam.com',
			'azaranpayamak' => 'Azaranpayamak.ir',
			'smsir'         => 'SMS.ir',
			'manirani'      => 'Manirani.ir',
			'tjp'           => 'TJP.ir',
			'websms'        => 'S1.Websms.ir',
			'payamresan'    => 'Payam-Resan.com',
			'bakhtarpanel'  => 'Bakhtar.xyz',
			'parsgreen'     => 'ParsGreen.com',
			'avalpayam'     => 'Avalpayam.com',
			'iransmsserver' => 'IranSmsServer.com',
			'melipayamak'   => 'MeliPayamak.com',
			'loginpanel'    => 'LoginPanel.ir',
			'smshooshmand'  => 'SmsHooshmand.com',
			'smsfor'        => 'SMSFor.ir',
			'chaparpanel'   => 'ChaparPanel.IR',
			'firstpayamak'  => 'FirstPayamak.ir',
			'netpaydar'     => 'SMS.Netpaydar.com',
			'smspishgaman'  => 'Panel.SmsPishgaman.com',
			'parsianpayam'  => 'ParsianPayam.ir',
			'hostiran'      => 'Hostiran.com',
			'iransms'       => 'IranSMS.co',
			'negins'        => 'Negins.com',
			'kavenegar'     => 'Kavenegar.com',
			'afe'           => 'Afe.ir',
			'aradpayamak'   => 'Aradpayamak.net',
			'isms'          => 'ISms.ir',
			'razpayamak'    => 'RazPayamak.com',
			'_0098'         => '0098SMS.com',
			'sefidsms'      => 'SefidSMS.ir',
			'chapargah'     => 'Chapargah.com',
			'hafezpayam'    => 'HafezPayam.com',
			'mehrpanel'     => 'MehrPanel.ir',
			'kianartpanel'  => 'KianArtPanel.ir',
			'farstech'      => 'Sms.FarsTech.ir',
			'berandet'      => 'Berandet.ir',
			'nicsms'        => 'NikSms.com',
			'asanak'        => 'Asanak.ir',
			'ssmss'         => 'SSMSS.ir',
			'hiro_sms'      => 'Hiro-Sms.com',
			'sabanovin'     => 'SabaNovin.com',
			'trez'          => 'SmsPanel.Trez.ir',
			'raygansms'     => 'RayganSms.com',
			'sepahansms'    => 'SepahanSms.com (SepahanGostar.com)',
			'_3300'         => 'Sms.3300.ir',
			'smsnegar'      => 'Sms.SmsNegar.com',
			'behsadade'     => 'Sms.BehsaDade.com',
			'flashsms'      => 'FlashSms.ir (AdminPayamak.ir)',
			'payamsms'      => 'PayamSms.com',
			'hadafwp'       => 'sms.hadafwp.Com',
			'mehrafraz'     => 'mehrafraz.com',
			'irpayamak'     => 'IRPayamak.Com',
			'gamasystems'   => 'Gama.systems',
			'smsmelli'      => 'SMSMelli.com',
			'smsmeli'       => 'SMS-Meli.com',
			'atlaspayamak'  => 'Atlaspayamak.ir',
			'parsiansms'    => 'Parsian-SMS.ir',
			'panelsms20'    => 'panelsms20.ir',
			'newsms'        => 'NewSMS.ir',
			'parsiantd'     => 'sms.parsiantd.com',
			'sahandsms'     => 'sahandsms.com',
			'aryana'        => 'PayamKotah.com',
			'npsms'         => 'npsms.com',
			'sornasms'      => 'sornasms.net',
			'TSMS'          => 'tsms.ir',
			'pardissms'     => 'pardis.ssmss.ir',
			'karenkart'     => 'karenkart.com',
			'nh1ir'         => 'nh1.ir',
			'b1ir'          => '1b1.ir',
			'samait'        => 'samait.ir',
			'jarin'         => 'w.jarin.ir',
			'candoo'        => 'candoosms.com',
			'satsms'        => 'satsms.ir',
			'webone'        => 'webone-sms.com',
			'asiasms'       => 'asiasms.ir',
			'signalads'     => 'panel.signalads.com',
			'payamakyab'    => 'payamakyab.com',
			'araditc'       => 'Arad ITC',
			'smsnegarir'    => 'SMSNegar.ir',
		);

		return apply_filters( 'nsms_sms_gateways', $gateway );
	}

	public static function get_tested_sms_gateway() {
		$gateway = array(
			'melipayamakpattern' => 'MeliPayamak.com - ملی پیامک',
			'ippanel'            => 'ippanel.com - فراز اس ام اس',
			'kavenegar_lookUp'   => 'کاوه نگار - اعتبار سنجی',
			'limosmscom'         => 'لیمو اس ام اس',
			'smsir_pattern'      => 'SMS.IR الگو',
		);

		return apply_filters( 'nsms_tested_sms_gateways', $gateway );
	}

	public function tjp() {

		$username = $this->username;
		$password = $this->password;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		// $from     = $this->senderNumber;
		$to      = $this->mobile;
		$massage = $this->message;

		try {

			$client = new SoapClient(
				'http://sms-login.tjp.ir/webservice/?WSDL',
				array(
					'login'    => $username,
					'password' => $password,
				)
			);

			$client->sendToMany( $to, $massage );

		} catch ( SoapFault $sf ) {
			$sms_response = $sf->getMessage();
		}

		if ( empty( $sms_response ) ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function hostiran() {
		return self::_soap_send_sms( 'http://37.228.138.118/post/send.asmx?wsdl' );
	}

	public function mehrpanel() {
		return self::_soap_send_sms( 'http://87.107.121.52/post/send.asmx?wsdl' );
	}

	public function hadafwp() {
		return self::_soap_send_sms( 'http://sms.hadafwp.com/Post/Send.asmx?wsdl' );
	}

	public function parandsms() {
		return self::_soap_send_sms( 'http://87.107.121.52/post/send.asmx?wsdl' );
	}

	public function aradsms() {
		return self::_soap_send_sms( 'http://arad-sms.ir/post/send.asmx?wsdl' );
	}

	public function smsbefrest() {
		return self::_soap_send_sms( 'http://87.107.121.52/post/send.asmx?wsdl' );
	}

	public function relax() {
		return self::_soap_send_sms( 'http://onlinepanel.ir/post/send.asmx?wsdl' );
	}

	public function farstech() {
		return self::_soap_send_sms( 'http://sms.farstech.ir/post/send.asmx?wsdl' );
	}

	public function kianartpanel() {
		return self::_soap_send_sms( 'http://onlinepanel.ir/post/send.asmx?wsdl' );
	}



	public function smspishgaman() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;
		$to       = $this->mobile;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$i = sizeOf( $to );
		while ( $i-- ) {
			$uNumber = trim( $to[ $i ] );
			$ret     = &$uNumber;
			if ( substr( $uNumber, 0, 3 ) == '%2B' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '%2b' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 4 ) == '0098' ) {
				$ret = substr( $uNumber, 4 );
			}
			if ( substr( $uNumber, 0, 3 ) == '098' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '+98' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 2 ) == '98' ) {
				$ret = substr( $uNumber, 2 );
			}
			if ( substr( $uNumber, 0, 1 ) == '0' ) {
				$ret = substr( $uNumber, 1 );
			}
			$to[ $i ] = '98' . $ret;
		}

		try {
			$client                   = new nusoap_client( 'http://82.99.216.45/services/?wsdl', true );
			$client->soap_defencoding = 'UTF-8';
			$client->decode_utf8      = false;

			$results = $client->call(
				'Send',
				array(
					'username'  => $username,
					'password'  => $password,
					'srcNumber' => $from,
					'body'      => $massage,
					'destNo'    => $to,
					'flash'     => '0',
				)
			);

			$error = array();
			foreach ( $results as $result ) {
				if ( ! isset( $result['Mobile'] ) || stripos( $result['ID'], 'e' ) !== false ) {
					$error[] = $result;
				}
			}

			if ( empty( $error ) ) {
				return true; // Success
			}
		} catch ( Exception $e ) {
			$response = $e->getMessage();
		}

		return $response;
	}

	public function candoo() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;
		$to       = $this->mobile;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$i = sizeOf( $to );
		while ( $i-- ) {
			$uNumber = trim( $to[ $i ] );
			$ret     = &$uNumber;
			if ( substr( $uNumber, 0, 3 ) == '%2B' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '%2b' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 4 ) == '0098' ) {
				$ret = substr( $uNumber, 4 );
			}
			if ( substr( $uNumber, 0, 3 ) == '098' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '+98' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 2 ) == '98' ) {
				$ret = substr( $uNumber, 2 );
			}
			if ( substr( $uNumber, 0, 1 ) == '0' ) {
				$ret = substr( $uNumber, 1 );
			}
			$to[ $i ] = '98' . $ret;
		}

		try {
			$client                   = new nusoap_client( 'http://my.candoosms.com/services/?wsdl', true );
			$client->soap_defencoding = 'UTF-8';
			$client->decode_utf8      = false;

			$results = $client->call(
				'Send',
				array(
					'username'  => $username,
					'password'  => $password,
					'srcNumber' => $from,
					'body'      => $massage,
					'destNo'    => $to,
					'flash'     => '0',
				)
			);

			$error = array();
			foreach ( $results as $result ) {
				if ( ! isset( $result['Mobile'] ) || stripos( $result['ID'], 'e' ) !== false ) {
					$error[] = $result;
				}
			}

			if ( empty( $error ) ) {
				return true; // Success
			}
		} catch ( Exception $e ) {
			$response = $e->getMessage();
		}

		return $response;
	}

	public function paaz() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://sms.paaz.ir/post/send.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function farapayamak() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client       = new SoapClient( 'http://37.228.138.118/post/send.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function smsmeli() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client       = new SoapClient( 'http://37.228.138.118/post/send.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function parsianpayam() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client     = new SoapClient( 'http://onepayam.ir/API/Send.asmx?wsdl' );
			$encoding   = 'UTF-8';
			$parameters = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'flash'    => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);

			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 0 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function iransmsserver() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://sms.iransmsserver.ir/post/send.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function niazpardaz() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://37.228.138.118/post/send.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function niazpardaz_() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://payamak-service.ir/SendService.svc?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'userName'       => $username,
				'password'       => $password,
				'fromNumber'     => $from,
				'toNumbers'      => $to,
				'messageContent' => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'        => false,
				'udh'            => '',
				'recId'          => array( 0 ),
				'status'         => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSMSResult;

		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( strval( $sms_response ) == '0' ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function payamafraz() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://payamafraz.ir/post/send.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function yektasms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://37.228.138.118/post/send.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function gamapayamak() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://37.228.138.118/post/send.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}


	public function smsfa() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://smsfa.net/API/Send.asmx?WSDL' );
			$sms_response = $client->SendSms(
				array(
					'username' => $username,
					'password' => $password,
					'from'     => $from,
					'to'       => $to,
					'text'     => $massage,
					'flash'    => false,
					'udh'      => '',
				)
			)->SendSmsResult;
		} catch ( SoapFault $sf ) {
			$sms_response = $sf->getMessage();
		}
		if ( intval( $sms_response ) > 0 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function postgah() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://postgah.net/API/Send.asmx?WSDL' );
			$sms_response = $client->SendSms(
				array(
					'username' => $username,
					'password' => $password,
					'from'     => $from,
					'to'       => $to,
					'text'     => $massage,
					'flash'    => false,
					'udh'      => '',
				)
			)->SendSmsResult;
		} catch ( SoapFault $sf ) {
			$sms_response = $sf->getMessage();
		}
		if ( strval( $sms_response ) == '0' ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function azaranpayamak() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://azaranpayamak.ir/API/Send.asmx?WSDL' );
			$sms_response = $client->SendSms(
				array(
					'username' => $username,
					'password' => $password,
					'from'     => $from,
					'to'       => $to,
					'text'     => $massage,
					'flash'    => false,
					'udh'      => '',
				)
			)->SendSmsResult;
		} catch ( SoapFault $sf ) {
			$sms_response = $sf->getMessage();
		}
		if ( strval( $sms_response ) == '0' ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function manirani() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://sms.manirani.ir/API/Send.asmx?WSDL' );
			$sms_response = $client->SendSms(
				array(
					'username' => $username,
					'password' => $password,
					'from'     => $from,
					'to'       => $to,
					'text'     => $massage,
					'flash'    => false,
					'udh'      => '',
				)
			)->SendSmsResult;
		} catch ( SoapFault $sf ) {
			$sms_response = $sf->getMessage();
		}

		if ( intval( $sms_response ) > 0 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function smsir() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$content = 'user=' . rawurlencode( $username ) .
			'&pass=' . rawurlencode( $password ) .
			'&to=' . rawurlencode( $to ) .
			'&lineNo=' . rawurlencode( $from ) .
			'&text=' . $massage;

		$remote = wp_remote_get( 'https://ip.sms.ir/SendMessage.ashx?' . $content );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) == 'ok' || stripos( $response, 'ارسال با موفقیت انجام شد' ) !== false ) {
			return true; // Success
		}

		return $response;
	}

	public function parsiansms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$content = 'uname=' . rawurlencode( $username ) .
			'&pass=' . rawurlencode( $password ) .
			'&to=' . rawurlencode( $to ) .
			'&from=' . rawurlencode( $from ) .
			'&msg=' . $massage;

		$remote = wp_remote_get( 'http://185.4.31.182/class/sms/webservice/send_url.php?' . $content );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) == 'ok' || stripos( $response, 'done' ) !== false ) {
			return true; // Success
		}

		return $response;
	}

	public function smsmelli() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$content = 'uname=' . rawurlencode( $username ) .
			'&pass=' . rawurlencode( $password ) .
			'&to=' . rawurlencode( $to ) .
			'&from=' . rawurlencode( $from ) .
			'&msg=' . $massage;

		$remote = wp_remote_get( 'http://185.4.31.182/class/sms/webservice/send_url.php?' . $content );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) == 'ok' || stripos( $response, 'done' ) !== false ) {
			return true; // Success
		}

		return $response;
	}

	public function netpaydar() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$content = 'user=' . rawurlencode( $username ) .
			'&pass=' . rawurlencode( $password ) .
			'&to=' . rawurlencode( $to ) .
			'&lineNo=' . rawurlencode( $from ) .
			'&text=' . $massage;

		$remote = wp_remote_get( 'http://sms.netpaydar.com/SendMessage.ashx?' . $content );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) == '1' || stripos( $response, 'ارسال با موفقیت انجام شد' ) !== false ) {
			return true; // Success
		}

		return $response;
	}

	public function afe() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = array();

		foreach ( $this->mobile as $mobile ) {

			$remote = wp_remote_get( 'http://www.afe.ir/Url/SendSMS?username=' . $username . '&Password=' . $password . '&Number=' . $from . '&mobile=' . $mobile . '&sms=' . $massage );

			$response = wp_remote_retrieve_body( $remote );

			if ( empty( $response ) || stripos( $response, 'success' ) === false ) {
				$errors[] = $response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		}

		return $errors;
	}

	public function iransms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = array();

		foreach ( $this->mobile as $mobile ) {

			$remote = wp_remote_get( 'http://www.iransms.co/URLSend.aspx?Username=' . $username . '&Password=' . $password . '&PortalCode=' . $from . '&Mobile=' . $mobile . '&Message=' . $massage . '&Flash=0' );

			$response = wp_remote_retrieve_body( $remote );

			if ( abs( $response ) < 30 ) {
				$errors[] = $response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		} else {
			$response = $errors;
		}

		return $response;
	}

	public function negins() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = array();

		foreach ( $this->mobile as $mobile ) {

			$remote = wp_remote_get( 'http://negins.com/URLSend.aspx?Username=' . $username . '&Password=' . $password . '&PortalCode=' . $from . '&Mobile=' . $mobile . '&Message=' . urlencode( $massage ) . '&Flash=0' );

			$response = wp_remote_retrieve_body( $remote );

			if ( abs( $response ) < 30 ) {
				$errors[] = $response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		} else {
			$response = $errors;
		}

		return $response;
	}

	public function hafezpayam() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = array();

		foreach ( $this->mobile as $mobile ) {

			$remote = wp_remote_get( 'http://hafezpayam.com/URLSend.aspx?Username=' . $username . '&Password=' . $password . '&PortalCode=' . $from . '&Mobile=' . $mobile . '&Message=' . urlencode( $massage ) . '&Flash=0' );

			$response = wp_remote_retrieve_body( $remote );

			if ( abs( $response ) < 30 ) {
				$errors[] = $response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		} else {
			$response = $errors;
		}

		return $response;
	}

	public function smshooshmand() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = $this->mobile;

		$client = new nusoap_client( 'http://smswbs.ir/class/sms/webservice/server.php?wsdl' );

		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8      = true;
		$client->setCredentials( $username, $password, 'basic' );

		$i = sizeOf( $to );
		while ( $i-- ) {
			$uNumber = trim( $to[ $i ] );
			$ret     = &$uNumber;
			if ( substr( $uNumber, 0, 3 ) == '%2B' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '%2b' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 4 ) == '0098' ) {
				$ret = substr( $uNumber, 4 );
			}
			if ( substr( $uNumber, 0, 3 ) == '098' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '+98' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 2 ) == '98' ) {
				$ret = substr( $uNumber, 2 );
			}
			if ( substr( $uNumber, 0, 1 ) == '0' ) {
				$ret = substr( $uNumber, 1 );
			}
			$to[ $i ] = '+98' . $ret;
		}

		$parameters = array(
			'from'       => $from,
			'rcpt_array' => $to,
			'msg'        => $massage,
			'type'       => 'normal',
		);

		$result = $client->call( 'enqueue', $parameters );
		if ( ( isset( $result['state'] ) && $result['state'] == 'done' ) && ( isset( $result['errnum'] ) && ( $result['errnum'] == '100' || $result['errnum'] == 100 ) ) ) {
			return true; // Success
		} else {
			$response = $result;
		}

		return $response;
	}

	public function smsfor() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}
		$to = $this->mobile;

		$i = sizeOf( $to );
		while ( $i-- ) {
			$uNumber = trim( $to[ $i ] );
			$ret     = &$uNumber;
			if ( substr( $uNumber, 0, 3 ) == '%2B' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '%2b' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 4 ) == '0098' ) {
				$ret = substr( $uNumber, 4 );
			}
			if ( substr( $uNumber, 0, 3 ) == '098' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '+98' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 2 ) == '98' ) {
				$ret = substr( $uNumber, 2 );
			}
			if ( substr( $uNumber, 0, 1 ) == '0' ) {
				$ret = substr( $uNumber, 1 );
			}
			$to[ $i ] = '0' . $ret;
		}

		$client                   = new nusoap_client( 'http://www.smsfor.ir/webservice/soap/smsService.php?wsdl', 'wsdl' );
		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8      = false;

		$params = array(
			'username'         => $username,
			'password'         => $password,
			'sender_number'    => array( $from ),
			'receiver_number'  => $to,
			'note'             => array( $massage ),
			'date'             => array(),
			'request_uniqueid' => array(),
			'flash'            => false,
			'onlysend'         => 'ok',
		);
		$md_res = $client->call( 'send_sms', $params );

		if ( empty( $md_res['getMessage()'] ) && empty( $md_res['getMessage()'] ) && is_numeric( str_ireplace( ',', '', $md_res[0] ) ) ) {
			return true; // Success
		} else {
			$response = $md_res;
		}

		return $response;
	}

	public function idehpayam() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$soap = new SoapClient( 'http://panel.idehpayam.com/webservice/send.php?wsdl' );

			$soap->Username = $username;
			$soap->Password = $password;
			$soap->fromNum  = $from;
			$soap->toNum    = $to;
			$soap->Content  = $massage;
			$soap->Type     = '0';

			$result = $soap->SendSMS( $soap->fromNum, $soap->toNum, $soap->Content, $soap->Type, $soap->Username, $soap->Password );

			if ( ! empty( $result[0] ) && $result[0] > 100 ) {
				return true; // Success
			} else {
				$response = $result;
			}

			return $response;

		} catch ( SoapFault $e ) {
			return $e->getMessage();
		}
	}

	public function websms() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$content = 'cusername=' . rawurlencode( $username ) .
			'&cpassword=' . rawurlencode( $password ) .
			'&cmobileno=' . rawurlencode( $to ) .
			'&csender=' . rawurlencode( $from ) .
			'&cbody=' . $massage;

		$remote = wp_remote_get( 'http://s1.websms.ir/wservice.php?' . $content );

		$sms_response = wp_remote_retrieve_body( $remote );

		if ( strlen( $sms_response ) > 8 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function bakhtarpanel() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$client = new nusoap_client( 'http://login.bakhtar.xyz/webservice/server.asmx?wsdl' );

		$status = explode(
			',',
			( $client->call(
				'Sendsms',
				array(
					'4',
					$from,
					$username,
					$password,
					'98',
					$massage,
					$to,
					false,
				)
			) )
		);

		if ( count( $status ) > 1 && $status[0] == 1 ) {
			return true; // Success
		} else {
			$response = $status;
		}

		return $response;
	}

	public function melipayamakpattern() {
		$username = $this->username;
		$password = $this->password;
		$to       = $this->mobile;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$pattern = pooshe_opt( 'pooshe_theme_settings_sms' , 'sms_gateways_pattern' );

		try {

			$client = new SoapClient( 'https://api.payamak-panel.com/post/send.asmx?wsdl', array( 'encoding' => 'UTF-8' ) );
			// $encoding     = "UTF-8";
			$parameters = array(
				'username' => $username,
				'password' => $password,
				'to'       => implode( ',', $to ),
				'text'     => array( $this->token ),
				'bodyId'   => $pattern,

			);
			$sms_response = $client->SendByBaseNumber( $parameters )->SendByBaseNumberResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}
		if ( $sms_response > 20 ) {
			return true; // Success
		}

		return $sms_response;
	}

	public function melipayamak() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://37.228.138.118/post/send.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function payamresan() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$url = 'http://www.payam-resan.com/APISend.aspx?UserName=' . rawurlencode( $username ) .
			'&Password=' . rawurlencode( $password ) .
			'&To=' . rawurlencode( $to ) .
			'&From=' . rawurlencode( $from ) .
			'&Text=' . $massage;

		$remote = wp_remote_get( $url );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) == '1' || $response == 1 ) {
			return true; // Success
		}

		return $response;
	}

	public function newsms() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$url = 'http://newsms.ir/api/?action=SMS_SEND&username=' . rawurlencode( $username ) .
			'&password=' . rawurlencode( $password ) .
			'&API_CHANGE_ALLOW=true&to=' . rawurlencode( $to ) .
			'&api=1&from=' . rawurlencode( $from ) .
			'&FLASH=0&text=' . $massage;

		$remote = wp_remote_get( $url );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) == '1' || $response == 1 ) {
			return true; // Success
		}

		return $response;
	}


	public function signalads() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$url = "https://panel.signalads.com/webservice/url/send.php?method=sendsms?from=$from&to=$to&text=$massage&username=$username&password=$password&type=0&format=json";

		$remote = wp_remote_get( $url );

		$response = wp_remote_retrieve_body( $remote );

		if ( intval( $response ) > 1 ) {
			return true; // Success
		} else {
			return $response;
		}

		return $response;
	}

	public function kavenegar() {

		$username = $this->username;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) ) {
			return false;
		}

		$messages = urlencode( $massage );
		$to       = implode( ',', $this->mobile );

		$url = "https://api.kavenegar.com/v1/$username/sms/send.json?sender=$from&receptor=$to&message=$messages";

		$remote = wp_remote_get( $url );

		$response = wp_remote_retrieve_body( $remote );

		if ( false !== $response ) {
			$json_response = json_decode( $response );
			if ( ! empty( $json_response->return->status ) && $json_response->return->status == 200 ) {
				return true; // Success
			}
		}

		return $response;
	}

	public function parsgreen() {

		$username = $this->username;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) ) {
			return false;
		}

		$to = $this->mobile;

		$body = array(
			'SmsBody' => $massage,
			'Mobiles' => $to,
		);

		$args = array(
			'body'        => json_encode( $body ),
			'timeout'     => '45',
			'headers'     => array(
				'Content-Type'  => 'application/json; charset=utf-8',
				'Accept'        => 'application/json',
				'Authorization' => 'basic apikey:' . $username,
			),
			'data_format' => 'body',
		);

		try {

			$remote = wp_remote_post( 'http://sms.parsgreen.ir/Apiv2/Message/SendSms', $args );

			$response = json_decode( wp_remote_retrieve_body( $remote ) );

		} catch ( Exception $ex ) {
			return $response = 'error';
		}

		if ( $response->R_Success ) {
			return $response = true;
		} else {
			$response = $response->R_Message;
		}

		return $response;
	}

	public function araditc() {

		$baseurl = $this->username;
		$apikey  = $this->password;
		$from    = $this->senderNumber;
		$massage = $this->message;

		if ( empty( $username ) ) {
			return false;
		}
		$to = implode( '', $this->mobile );
		$to = preg_replace( '#^(\+98|0)?#', '', $to );

		$body = array(
			'SourceAddress'      => $from,
			'MessageText'        => $massage,
			'DestinationAddress' => $to,
		);

		$args = array(
			'body'        => json_encode( $body ),
			'timeout'     => '45',
			'headers'     => array(
				'Content-Type'  => 'application/json',
				'Accept'        => 'application/json',
				'Authorization' => 'Bearer 4|' . $apikey,
			),
			'data_format' => 'body',
		);

		try {

			$remote = wp_remote_post( $baseurl, $args );

			$response = json_decode( wp_remote_retrieve_body( $remote ) );

		} catch ( Exception $ex ) {
			return $response = 'error';
		}

		if ( $response->message == 'عملیات با موفقیت انجام شد' ) {
			return $response = true;
		} else {
			$response = $response->message;
		}

		return $response;
	}


	public function kavenegar_lookUp() {

		$response = false;
		$username = $this->username;
		// $password = $this->password;
		$from  = $this->senderNumber;
		$to    = implode( ',', $this->mobile );
		$token = $this->token;

		$pattern      = pooshe_opt( 'pooshe_theme_settings_sms' , 'sms_gateways_pattern' );
		$templateName = $pattern;

		$url = "http://api.kavenegar.com/v1/$username/verify/lookup.json?receptor=$to&template=$templateName&token=$token";

		$remote = wp_remote_get( $url );

		$sms_response = wp_remote_retrieve_body( $remote );

		if ( false !== $sms_response ) {
			$json_response = json_decode( $sms_response );
			if ( ! empty( $json_response->return->status ) && $json_response->return->status == 200 ) {
				return true; // Success
			}
		}

		if ( $response !== true ) {
			$response = $sms_response;
		}

		return $response;
	}

	public function avalpayam() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}
		$to = $this->mobile;

		$client = new nusoap_client( 'http://www.avalpayam.com/class/sms/webservice/server.php?wsdl' );

		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8      = true;
		$client->setCredentials( $username, $password, 'basic' );

		$i = sizeOf( $to );
		while ( $i-- ) {
			$uNumber = trim( $to[ $i ] );
			$ret     = &$uNumber;
			if ( substr( $uNumber, 0, 3 ) == '%2B' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '%2b' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 4 ) == '0098' ) {
				$ret = substr( $uNumber, 4 );
			}
			if ( substr( $uNumber, 0, 3 ) == '098' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '+98' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 2 ) == '98' ) {
				$ret = substr( $uNumber, 2 );
			}
			if ( substr( $uNumber, 0, 1 ) == '0' ) {
				$ret = substr( $uNumber, 1 );
			}
			$to[ $i ] = '+98' . $ret;
		}

		$parameters = array(
			'from'       => $from,
			'rcpt_array' => $to,
			'msg'        => $massage,
			'type'       => 'normal',
		);

		$result = $client->call( 'enqueue', $parameters );
		if ( ( isset( $result['state'] ) && $result['state'] == 'done' ) && ( isset( $result['errnum'] ) && ( $result['errnum'] == '100' || $result['errnum'] == 100 ) ) ) {
			return true; // Success
		} else {
			$response = $result;
		}

		return $response;
	}

	public function loginpanel() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://87.107.121.52/post/send.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function atlaspayamak() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://4646.ir/post/send.php?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function chaparpanel() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://87.107.121.52/post/send.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function firstpayamak() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client = new SoapClient( 'http://ui.firstpayamak.ir/webservice/v2.asmx?WSDL' );
			$params = array(
				'username'         => $username,
				'password'         => $password,
				'recipientNumbers' => $to,
				'senderNumbers'    => array( $from ),
				'messageBodies'    => array( $massage ),
			);

			$sms_response = $client->SendSMS( $params );
			$sms_response = (array) $sms_response->SendSMSResult->long;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( is_array( $sms_response ) ) {
			foreach ( array_filter( $sms_response ) as $send ) {
				if ( $send > 1000 ) {
					return true; // Success
				}
			}
		}

		if ( $response !== true ) {
			$response = $sms_response;
		}

		return $response;
	}

	public function aradpayamak() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;

		$massage = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ';', $this->mobile );

		try {

			$client             = new SoapClient( 'http://aradpayamak.net/APPs/SMS/WebService.php?wsdl' );
			$sendsms_parameters = array(
				'domain'   => 'aradpayamak.net',
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => $massage,
				'isflash'  => 0,
			);

			$sms_response = call_user_func_array( array( $client, 'sendSMS' ), $sendsms_parameters );

			if ( ! empty( $sms_response ) ) {
				return true; // Success
			}
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $response !== true ) {
			$response = $sms_response;
		}

		return $response;
	}

	public function b1ir() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;

		$massage = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ';', $this->mobile );

		try {

			$client             = new SoapClient( 'http://1b1.ir/APPs/SMS/WebService.php?wsdl' );
			$sendsms_parameters = array(
				'domain'   => '1b1.ir',
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => $massage,
				'isflash'  => 0,
			);

			$sms_response = call_user_func_array( array( $client, 'sendSMS' ), $sendsms_parameters );

			if ( ! empty( $sms_response ) ) {
				return true; // Success
			}
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $response !== true ) {
			$response = $sms_response;
		}

		return $response;
	}

	public function isms() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;

		$massage = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$data = array(
			'username' => $username,
			'password' => $password,
			'mobiles'  => $this->mobile,
			'body'     => $massage,
			'sender'   => $from,
		);

		$remote = wp_remote_get( 'http://ws3584.isms.ir/sendWS?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		$result = json_decode( $response, true );

		if ( ! empty( $result['code'] ) && ! empty( $result['message'] ) ) {
			$response = $result;
		} else {
			return true; // Success
		}

		return $response;
	}

	public function asiasms() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;

		$massage = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$data = array(
			'Username'  => $username,
			'password'  => $password,
			'Receivers' => implode( ',', $this->mobile ),
			'SmsText'   => $massage,
			'SenderId'  => $from,
		);

		$remote = wp_remote_get( 'http://api.asiasms.ir:8080/Messages/SendViaURL?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		$result = json_decode( $response, true );

		if ( $result['IsSuccessful'] == true ) {
			$response = true;
		} else {
			return $response; // Success
		}

		return $response;
	}

	public function nh1ir() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;

		$massage = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$data = array(
			'Username' => $username,
			'Password' => $password,
			'To'       => $this->mobile,
			'Text'     => $massage,
			'From'     => $from,
		);

		$remote = wp_remote_get( 'http://ws.nh1.ir/Api/SMS/Send?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		$result = json_decode( $response, true );

		if ( ! empty( $result['code'] ) && ! empty( $result['message'] ) ) {
			return true;
		} else {
			return true; // Success
		}

		return $response;
	}

	public function razpayamak() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://37.228.138.118/post/send.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function _0098() {

		$username  = $this->username;
		$password  = $this->password;
		$from      = $this->senderNumber;
		$recievers = $this->mobile;
		$massage   = $this->message;
		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = array();

		foreach ( (array) $recievers as $to ) {

			$url = 'http://www.0098sms.com/sendsmslink.aspx?DOMAIN=0098' .
				'&USERNAME=' . rawurlencode( $username ) .
				'&PASSWORD=' . rawurlencode( $password ) .
				'&FROM=' . rawurlencode( $from ) .
				'&TO=' . rawurlencode( $to ) .
				'&TEXT=' . $massage;

			$remote = wp_remote_get( $url );

			$sms_response = intval( wp_remote_retrieve_body( $remote ) );

			if ( $sms_response !== 0 ) {
				$errors[ $to ] = $sms_response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		} else {
			$response = $errors;
		}

		return $response;
	}

	public function parsiantd() {

		$username  = $this->username;
		$password  = $this->password;
		$from      = $this->senderNumber;
		$recievers = $this->mobile;
		$massage   = $this->message;
		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = array();

		foreach ( (array) $recievers as $to ) {

			$content = 'http://sms.parsiantd.com/Api-Services/sms_sender_url.php?' .
				'&username=' . rawurlencode( $username ) .
				'&password=' . rawurlencode( $password ) .
				'&from=' . rawurlencode( $from ) .
				'&to=' . rawurlencode( $to ) .
				'&text=' . $massage;

			$remote = wp_remote_get( $content );

			$sms_response = intval( wp_remote_retrieve_body( $remote ) );

			if ( $sms_response < 12 ) {
				$errors[ $to ] = $sms_response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		} else {
			$response = $errors;
		}

		return $response;
	}

	public function sefidsms() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client     = new SoapClient( 'http://api.sefidsms.ir/post/send.asmx?wsdl' );
			$encoding   = 'UTF-8';
			$parameters = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);

			$sms_response = $client->SendSms( $parameters )->SendSmsResult;

		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function chapargah() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$massage = iconv( 'UTF-8', 'UTF-8//TRANSLIT', $massage );

		try {
			$client       = new SoapClient( 'http://chapargah.com/API/Send.asmx?WSDL' );
			$sms_response = $client->SendSms(
				array(
					'username' => $username,
					'password' => $password,
					'from'     => $from,
					'to'       => $to,
					'text'     => $massage,
					'flash'    => false,
					'recId'    => array( 0 ),
					'status'   => 0,
				)
			)->SendSmsResult;
		} catch ( SoapFault $sf ) {
			$sms_response = $sf->getMessage();
		}
		if ( strval( $sms_response ) == '0' ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function berandet() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$i = sizeOf( $to );
		while ( $i-- ) {
			$uNumber = trim( $to[ $i ] );
			$ret     = &$uNumber;
			if ( substr( $uNumber, 0, 3 ) == '%2B' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '%2b' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 4 ) == '0098' ) {
				$ret = substr( $uNumber, 4 );
			}
			if ( substr( $uNumber, 0, 3 ) == '098' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '+98' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 2 ) == '98' ) {
				$ret = substr( $uNumber, 2 );
			}
			if ( substr( $uNumber, 0, 1 ) == '0' ) {
				$ret = substr( $uNumber, 1 );
			}
			$to[ $i ] = '+98' . $ret;
		}

		$timeout                  = 1800;
		$response_timeout         = 180;
		$client                   = new nusoap_client( 'http://berandet.ir/Modules/DevelopmentTools/Groups/Messaging/MessagingWbs.php?wsdl', true, false, false, false, false, $timeout, $response_timeout, '' );
		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8      = false;
		$client->response_timeout = $response_timeout;
		$client->timeout          = $timeout;

		$parameter = array(
			'request' => array(
				'username'   => $username,
				'password'   => $password,
				'fromNumber' => $from,
				'message'    => $massage,
				'recieptor'  => $to,
			),
		);

		$result = $client->call( 'sendMessageOneToMany', $parameter );
		$result = json_decode( $result, true );

		if ( ( isset( $result['errCode'] ) && $result['errCode'] < 0 ) || ! empty( $result['err'] ) ) {
			$response = $result;
		} else {
			return true; // Success
		}

		return $response;
	}

	public function nicsms() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$param = array(
			'username'     => $username,
			'password'     => $password,
			'message'      => $massage,
			'numbers'      => implode( ',', $to ),
			'senderNumber' => $from,
			'sendOn'       => date( 'yyyy/MM/dd-hh:mm' ),
			'sendType'     => 1,
		);

		$remote = wp_remote_post(
			'http://niksms.com/fa/PublicApi/GroupSms',
			array(
				'body' => $param,
			)
		);

		$_response = wp_remote_retrieve_body( $remote );

		$_response = json_decode( $_response );
		$_response = ! empty( $_response->Status ) ? $_response->Status : 2;

		if ( $_response === 1 || strtolower( $_response ) == 'successful' ) {
			return true; // Success
		} else {
			$response = $_response;
		}

		return $response;
	}

	public function jarin() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$param = array(
			'phoneNumber'      => $username,
			'passWord'         => $password,
			'text'             => $massage,
			'destPhoneNumbers' => implode( ',', $to ),
			'lines'            => $from,
		);

		$remote = wp_remote_post(
			'http://wp.jarin.ir/Api/SendMessage.php',
			array(
				'body' => $param,
			)
		);

		$_response = wp_remote_retrieve_body( $remote );

		$response = json_decode( $response );

		if ( $response->status == 100 ) {
			return true;
		} else {
			return 'error : ' . $response->status;
		}

		return $response;
	}

	public function irpayamak() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $to );
		$to = str_ireplace( '+98', '0', $to );

		$url = 'http://irpayamak.com/API/SendSms.ashx?username=' . $username . '&password=' . $password . '&from=' . $from . '&to=' . $to . '&message=' . urlencode( trim( $massage ) );

		$remote = wp_remote_get( $url );

		$response = wp_remote_retrieve_body( $remote );

		if ( preg_match( '/\[.*\]/is', (string) $response ) ) {
			return true; // Success
		}

		return $response;
	}

	public function karenkart() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$remote = wp_remote_get( 'http://www.karenkart.com/Home/send_via_get?note=' . $massage . '&username=' . $username . '&password=' . $password . '&receiver_number=' . $to . '&sender_number=' . $from . '' );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 1 ) {
			return true; // Success
		}

		return $response;
	}

	public function sahandsms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $to );
		$to = str_ireplace( '+98', '0', $to );

		$url = 'http://webservice.sahandsms.com/NewSMSWebService.asmx/SendFromUrl?username=' . $username . '&password=' . $password . '&fromNumber=' . $from . '&toNumber=' . $to . '&message=' . urlencode( trim( $massage ) );

		wp_remote_get( $url );

		return true;
	}

	public function aryana() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = array(
			'user'    => rawurlencode( $username ),
			'pass'    => rawurlencode( $password ),
			'mobile'  => rawurlencode( $to ),
			'line'    => rawurlencode( $from ),
			'message' => $massage,
			'flash'   => 1,
		);

		$remote = wp_remote_get( 'http://www.payamkotah.ir/FastSendSMS.ashx?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 2000 ) {
			return true; // Success
		}

		return $response;
	}

	public function npsms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = array(
			'userName'      => rawurlencode( $username ),
			'password'      => rawurlencode( $password ),
			'reciverNumber' => rawurlencode( $to ),
			'senderNumber'  => rawurlencode( $from ),
			'smsText'       => $massage,
			'domainName'    => 'npsms',
		);

		$remote = wp_remote_get( 'https://npsms.com/sendSmsViaURL.aspx?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 1 ) {
			return true; // Success
		}

		return $response;
	}

	public function sornasms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = array(
			'username'     => $username,
			'pass'         => $password,
			'mobile'       => $to,
			'senderNumber' => $from,
			'message'      => $massage,
			'code'         => 10260,
		);

		$remote = wp_remote_get( 'https://sornasms.net/getCustomer.aspx?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 1 ) {
			return true; // Success
		}

		return $response;
	}

	public function asanak() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $to );
		$to = str_ireplace( '+98', '0', $to );

		$data = array(
			'username'    => $username,
			'password'    => $password,
			'destination' => $to,
			'source'      => $from,
			'message'     => $massage,
		);

		$remote = wp_remote_get( 'http://panel.asanak.ir/webservice/v1rest/sendsms?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( preg_match( '/\[.*\]/is', (string) $response ) ) {
			return true; // Success
		}

		return $response;
	}

	public function ssmss() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = array(
			'username'        => rawurlencode( $username ),
			'password'        => rawurlencode( $password ),
			'receiver_number' => rawurlencode( $to ),
			'sender_number'   => rawurlencode( $from ),
			'note'            => $massage,
		);

		$remote = wp_remote_get( 'http://ssmss.ir/send_via_get/send_sms.php?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 1 ) {
			return true; // Success
		}

		return true;
	}

	public function webone() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://payamakapi.ir/SendService.svc?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'userName'       => $username,
				'password'       => $password,
				'fromNumber'     => $from,
				'toNumbers'      => $to,
				'messageContent' => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'        => false,
				'udh'            => '',
				'recId'          => array( 0 ),
				'status'         => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSMSResult;

		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( strval( $sms_response ) == '0' ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function payamakyab() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client       = new SoapClient( 'http://smspanel.payamakyab.com/Post/Send.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}


	public function satsms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://payamakapi.ir/SendService.svc?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'userName'       => $username,
				'password'       => $password,
				'fromNumber'     => $from,
				'toNumbers'      => $to,
				'messageContent' => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'        => false,
				'udh'            => '',
				'recId'          => array( 0 ),
				'status'         => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSMSResult;

		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( strval( $sms_response ) == '0' ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function hiro_sms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$soap = new SoapClient( 'http://my.hiro-sms.com/wbs/send.php?wsdl' );

			$soap->Username = $username;
			$soap->Password = $password;
			$soap->fromNum  = $from;
			$soap->toNum    = $to;
			$soap->Content  = $massage;
			$soap->Type     = '0';

			$result = $soap->SendSMS( $soap->fromNum, $soap->toNum, $soap->Content, $soap->Type, $soap->Username, $soap->Password );

			if ( ! empty( $result[0] ) && $result[0] > 100 ) {
				return true; // Success
			} else {
				$response = $result;
			}

			return $response;

		} catch ( SoapFault $e ) {
			return $e->getMessage();
		}
	}

	public function sabanovin() {

		$api_key = $this->username;
		$from    = $this->senderNumber;
		$to      = $this->mobile;
		$massage = $this->message;

		if ( empty( $api_key ) ) {
			return false;
		}

		$data = array(
			'gateway' => $from,
			'to'      => implode( ',', $to ),
			'text'    => urlencode( $massage ),
		);

		$remote = wp_remote_get( "https://api.sabanovin.com/v1/{$api_key}/sms/send.json?" . http_build_query( $data ) );

		$response = json_decode( wp_remote_retrieve_body( $remote ) );

		if ( ! empty( $response->status->code ) && $response->status->code == 200 ) {
			return true; // Success
		} elseif ( ! empty( $response->status->message ) ) {
			$response = $response->status->code . ':' . $response->status->message;
		}

		return $response;
	}

	public function trez() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = array(
			'Smsclass'    => 1,
			'Username'    => rawurlencode( $username ),
			'Password'    => rawurlencode( $password ),
			'RecNumber'   => rawurlencode( $to ),
			'PhoneNumber' => rawurlencode( $from ),
			'MessageBody' => $massage,
		);

		$remote = wp_remote_get( 'http://smspanel.trez.ir/SendGroupMessageWithUrl.ashx?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 2000 ) {
			return true; // Success
		}

		return $response;
	}

	public function raygansms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = array(
			'Smsclass'    => 1,
			'Username'    => rawurlencode( $username ),
			'Password'    => rawurlencode( $password ),
			'RecNumber'   => rawurlencode( $to ),
			'PhoneNumber' => rawurlencode( $from ),
			'MessageBody' => $massage,
		);

		$remote = wp_remote_get( 'http://smspanel.trez.ir/SendGroupMessageWithUrl.ashx?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 2000 ) {
			return true; // Success
		}

		return $response;
	}

	public function sepahansms() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client = new SoapClient( 'http://www.sepahansms.com/smsSendWebServiceforphp.asmx?wsdl' );

			$sms_response = $client->SendSms(
				array(
					'UserName'     => $username,
					'Pass'         => $password,
					'Domain'       => 'sepahansms',
					'SmsText'      => array( $massage ),
					'MobileNumber' => $to,
					'SenderNumber' => $from,
					'sendType'     => 'StaticText',
					'smsMode'      => 'SaveInPhone',
				)
			)->SendSmsResult->long;

			if ( is_array( $sms_response ) || $sms_response > 1000 ) {
				return true; // Success
			}
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $response !== true ) {
			$response = $sms_response;
		}

		return $response;
	}

	public function _3300() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = $this->mobile;

		try {
			$client                   = new nusoap_client( 'http://sms.3300.ir/almassms.asmx?wsdl', 'wsdl', '', '', '', '' );
			$client->soap_defencoding = 'UTF-8';
			$client->decode_utf8      = true;

			$param  = array(
				'pUsername' => $username,
				'pPassword' => $password,
				'line'      => $from,
				'messages'  => array( 'string' => ( $massage ) ),
				'mobiles'   => array( 'string' => $to ),
				'Encodings' => array( 'int' => 2 ),
				'mclass'    => array( 'int' => 1 ),
			);
			$result = $client->call( 'Send', $param );
			$result = isset( $result['SendResult'] ) ? $result['SendResult'] : 0;

			if ( $result < 0 ) {
				return true; // Success
			} else {
				$response = $result;
			}
		} catch ( Exception $ex ) {
			$response = $ex->getMessage();
		}

		return $response;
	}

	public function pardissms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = array(
			'username'        => rawurlencode( $username ),
			'password'        => rawurlencode( $password ),
			'receiver_number' => rawurlencode( $to ),
			'sender_number'   => rawurlencode( $from ),
			'note'            => $massage,
		);

		$remote = wp_remote_get( 'http://pardis.ssmss.ir/send_via_get/send_sms.php?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 8 ) {
			return true; // Success
		}

		return $response;
	}

	public function smsnegarir() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = array(
			'username'      => rawurlencode( $username ),
			'password'      => rawurlencode( $password ),
			'domain'        => 'sms.smsnegar',
			'reciverNumber' => rawurlencode( $to ),
			'senderNumber'  => rawurlencode( $from ),
			'smsText'       => $massage,
		);

		$remote = wp_remote_get( 'http://sms.smsnegar.ir/sendSMSURL.aspx?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 8 ) {
			return true; // Success
		}

		return $response;
	}

	public function samait() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = array(
			'username'        => rawurlencode( $username ),
			'password'        => rawurlencode( $password ),
			'receiver_number' => rawurlencode( $to ),
			'sender_number'   => rawurlencode( $from ),
			'note'            => $massage,
		);

		$remote = wp_remote_get( 'http://samait.ir/send_via_get/send_sms.php?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 8 ) {
			return true; // Success
		}

		return $response;
	}

	public function gamasystems() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = array(
			'username' => rawurlencode( $username ),
			'password' => rawurlencode( $password ),
			'to'       => rawurlencode( $to ),
			'from'     => rawurlencode( $from ),
			'text'     => $massage,
		);

		$remote = wp_remote_get( 'http://sms.gama.systems/url/post/SendSMS.ashx?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 11 ) {
			return true; // Success
		}

		return $response;
	}

	public function smsnegar() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '', $this->mobile );
		$to = preg_replace( '#^(\+98|0)?#', '', $to );

		try {

			$client = new SoapClient( 'http://sms.smsnegar.com/webservice/Service.asmx?wsdl' );

			$result = $client->SendSms(
				array(
					'cUserName'     => $username,
					'cPassword'     => $password,
					'cBody'         => $massage,
					'cSmsnumber'    => $to,
					'cGetid'        => '0',
					'nCMessage'     => '1',
					'nTypeSent'     => '1',
					'm_SchedulDate' => '',
					'cDomainname'   => 'yazd',
					'nSpeedsms'     => '0',
					'nPeriodmin'    => '0',
					'cstarttime'    => '',
					'cEndTime'      => '',
				)
			);

			if ( ! empty( $result->SendSmsResult ) ) {

				return true;

				$results = explode( ',', $result );
				unset( $result );

				foreach ( $results as $result ) {
					if ( intval( $result ) > 1000 ) {
						$result       = $client->ShowError(
							array(
								'cErrorCode' => $result,
								'cLanShow'   => 'FA',
							)
						);
						$sms_response = ! empty( $result->ShowErrorResult ) ? $result->ShowErrorResult : $results;
						break;
					}
				}
			} else {
				$sms_response = 'unknown';
			}
		} catch ( Exception $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( empty( $sms_response ) ) {
			return true; // Success
		}

		return $sms_response;
	}

	public function mehrafraz() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client = new SoapClient( 'http://www.mehrafraz.com/webservice/service.asmx?wsdl' );

			$result = $client->SendSms(
				array(
					'cUserName'     => $username,
					'cPassword'     => $password,
					'cDomainname'   => $from,
					'cBody'         => $massage,
					'cSmsnumber'    => implode( ',', $to ),
					'cGetid'        => '0',
					'nCMessage'     => '1',
					'nTypeSent'     => '1',
					'm_SchedulDate' => '',
					'nSpeedsms'     => '0',
					'nPeriodmin'    => '0',
					'cstarttime'    => '',
					'cEndTime'      => '',
				)
			);

			if ( ! empty( $result->SendSmsResult ) ) {

				$result  = $result->SendSmsResult;
				$results = explode( ',', $result );
				unset( $result );

				foreach ( $results as $result ) {
					if ( intval( $result ) < 1000 ) {
						$result       = $client->ShowError(
							array(
								'cErrorCode' => $result,
								'cLanShow'   => 'FA',
							)
						);
						$sms_response = ! empty( $result->ShowErrorResult ) ? $result->ShowErrorResult : $results;
						break;
					}
				}
			} else {
				$sms_response = 'unknown';
			}
		} catch ( Exception $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( empty( $sms_response ) ) {
			return true; // Success
		}

		return $sms_response;
	}

	public function behsadade() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to       = implode( ',', $to );
		$massage  = urlencode( $massage );
		$username = urlencode( $username );
		$password = urlencode( $password );
		$from     = urlencode( $from );

		try {

			$data = array(
				'login_username'  => $username,
				'login_password'  => $password,
				'receiver_number' => $to,
				'note_arr'        => $massage,
				'sender_number'   => $from,
			);

			$remote = wp_remote_get( 'http://sms.behsadade.com/webservice/rest/sms_send?' . http_build_query( $data ) );

			$response = json_decode( wp_remote_retrieve_body( $remote ) );

			if ( isset( $results->error ) ) {
				$response = $results->error;
			} elseif ( ! empty( $results->result ) && $results->result && ! empty( $results->list ) ) {
				return true; // Success
			}

			return $response;

		} catch ( Exception $ex ) {
			return $ex->getMessage();
		}
	}

	public function panelsms20() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			foreach ( $to as $key => $value ) {
				// $arr[3] will be updated with each value from $arr...

				$param    = array(
					'userName'    => $username,
					'password'    => $password,
					'msg'         => $massage,
					'from'        => $from,
					'to'          => $value,
					'isFlashSend' =>
						false,
				);
				$client   = new SoapClient( 'http://panelsms20.ir/services/SMSServices.asmx?WSDL' );
				$response = $client->Send( $param );
			}

			$sms_response = $response;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function flashsms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client       = new SoapClient( 'http://flashsms.ir/smssendwebserviceforcms.asmx?wsdl' );
			$encoding     = 'UTF-8';
			$parameters   = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => '',
				'recId'    => array( 0 ),
				'status'   => 0,
			);
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function payamsms() {

		$response   = false;
		$username   = $this->username;
		$password   = $this->password;
		$from       = $this->senderNumber;
		$massage    = $this->message;
		$to         = $this->mobile;
		$orginpayam = 'sazmansht';

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$i = sizeOf( $to );
		while ( $i-- ) {
			$uNumber = trim( $to[ $i ] );
			$ret     = &$uNumber;
			if ( substr( $uNumber, 0, 3 ) == '%2B' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '%2b' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 4 ) == '0098' ) {
				$ret = substr( $uNumber, 4 );
			}
			if ( substr( $uNumber, 0, 3 ) == '098' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '+98' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 2 ) == '98' ) {
				$ret = substr( $uNumber, 2 );
			}
			if ( substr( $uNumber, 0, 1 ) == '0' ) {
				$ret = substr( $uNumber, 1 );
			}
			$to[ $i ] = '98' . $ret;
		}

		try {
			$client                   = new nusoap_client( 'https://new.payamsms.com/services/v2/?wsdl', true );
			$client->soap_defencoding = 'UTF-8';
			$client->decode_utf8      = false;

			$results = $client->call(
				'Send',
				array(
					'organization' => $orginpayam,
					'username'     => $username,
					'password'     => $password,
					'srcNumber'    => $from,
					'body'         => $massage,
					'destNo'       => $to,
					'flash'        => '0',
				)
			);

			$error = array();
			foreach ( $results as $result ) {
				if ( ! isset( $result['Mobile'] ) || stripos( $result['ID'], 'e' ) !== false ) {
					$error[] = $result;
				}
			}

			if ( empty( $error ) ) {
				return true; // Success
			} else {
				foreach ( $results as $value ) {

					print_r( $value );
				}
			}
		} catch ( Exception $e ) {
			$response = $e->getMessage();
		}

		return $response;
	}

	public function tsms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$data = array(
			'from'     => rawurlencode( $from ),
			'to'       => rawurlencode( $to ),
			'username' => rawurlencode( $username ),
			'password' => rawurlencode( $password ),
			'message'  => $massage,
		);

		$remote = wp_remote_get( 'http://tsms.ir/url/tsmshttp.php?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) > '20' ) {
			return true; // Success
		}

		return $response;
	}

	public function sunwaysms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = array();

		foreach ( $this->mobile as $mobile ) {

			$data = array(
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $mobile,
				'message'  => urlencode( $massage ),
			);

			$remote = wp_remote_get( 'http://sms.sunwaysms.com/SMSWS/HttpService.ashx?' . http_build_query( $data ) );

			$response = wp_remote_retrieve_body( $remote );

			if ( empty( $response ) || $response < 10000 ) {
				$errors[] = $response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		}

		return $errors;
	}

	public function ippanel() {

		$username = $this->username;
		$from     = $this->senderNumber;

		if ( empty( $username ) ) {
			return false;
		}

		$errors = array();

		$pattern = pooshe_opt( 'pooshe_theme_settings_sms' , 'sms_gateways_pattern' );

		$data = array(
			'code'      => $pattern,
			'sender'    => $from,
			'recipient' => $this->mobile[0],
			'variable'  => array(
				'token' => $this->token,
			),
		);

		$remote = wp_remote_post(
			'https://api2.ippanel.com/api/v1/sms/pattern/normal/send',
			array(
				'body'    => json_encode( $data ),
				'headers' => array(
					'Apikey'       => $username,
					'Content-Type' => 'application/json',
					'accept'       => '*/*',
				),
			)
		);

		$response = wp_remote_retrieve_body( $remote );

		$response = json_decode( $response, true );

		if ( is_array( $response ) && ! empty( $response['code'] ) && $response['code'] !== 200 ) {
			$errors[] = $response['error_message'];
		}

		if ( empty( $errors ) ) {
			return true; // Success
		}

		return $errors;
	}

	public function limosmscom() {

		$username = $this->username;

		if ( empty( $username ) ) {
			return false;
		}

		$errors = array();

		$pattern = pooshe_opt( 'pooshe_theme_settings_sms' , 'sms_gateways_pattern' );

		$data = array(
			'OtpId'        => $pattern,
			'MobileNumber' => array( $this->mobile[0] ),
			'ReplaceToken' => array( $this->token ),
		);

		$remote = wp_remote_post(
			'https://api.limosms.com/api/sendpatternmessage',
			array(
				'body'    => json_encode( $data ),
				'headers' => array(
					'ApiKey'       => $username,
					'Content-Type' => 'application/json',
					'accept'       => '*/*',
				),
			)
		);

		$response = wp_remote_retrieve_body( $remote );

		$response = json_decode( $response, true );

		if ( is_array( $response ) && ! empty( $response['code'] ) && $response['code'] !== 200 ) {
			$errors[] = $response['error_message'];
		}

		if ( empty( $errors ) ) {
			return true; // Success
		}

		return $errors;
	}

	public function smsir_pattern() {

		$username = $this->username;

		if ( empty( $username ) ) {
			return false;
		}

		$errors = array();

		$pattern = pooshe_opt( 'pooshe_theme_settings_sms' , 'sms_gateways_pattern' );

		$data = array(
			'templateId' => $pattern,
			'mobile'     => $this->mobile[0],
			'parameters' => array(
				'name'  => 'token',
				'value' => $this->token,
			),
		);

		$remote = wp_remote_post(
			'https://api.sms.ir/v1/send/verify',
			array(
				'body'    => json_encode( $data ),
				'headers' => array(
					'x-api-key'    => $username,
					'Content-Type' => 'application/json',
					'Accept'       => 'text/plain',
				),
			)
		);

		$response = wp_remote_retrieve_body( $remote );

		$response = json_decode( $response, true );

		if ( is_array( $response ) && ! empty( $response['status'] ) && $response['status'] !== 1 ) {
			$errors[] = $response['message'];
		}

		if ( empty( $errors ) ) {
			return true; // Success
		}

		return $errors;
	}
}
