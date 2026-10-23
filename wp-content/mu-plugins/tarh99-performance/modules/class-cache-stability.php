<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Cache_Stability extends Tarh99_Perf_Module {

	public function id() {
		return 'cache_stability';
	}

	public function group() {
		return 'stability';
	}

	public function title() {
		return 'ماندگاری کش صفحات (جلوگیری از «بعد از چند صفحه یهو کند شد»)';
	}

	public function description() {
		return 'هر صفحه‌ای که در کش راکت نباشد روی این هاست ۴ تا ۵ ثانیه ساخته می‌شود. با طول عمر ۱۰ ساعته، راکت هر ۱۰ ساعت همه صفحات (موبایل و دسکتاپ جدا) را دوباره می‌سازد و پیش‌بارگذاری بیشتر وقتش را صرف هزاران برچسب می‌کند؛ در این فاصله محصولاتی که هنوز ساخته نشده‌اند برای اولین بازدیدکننده کند باز می‌شوند. این ماژول برچسب‌ها را از صف پیش‌بارگذاری بیرون می‌گذارد (با اولین بازدید کش می‌شوند) و اعتبار nonce بازدیدکننده‌های مهمان را طولانی‌تر می‌کند تا بتوان طول عمر کش راکت را بیشتر کرد بدون اینکه فرم ورود، سبد و ویجت‌ها در صفحات کش‌شده قدیمی خطا بدهند. برای کاربران لاگین‌شده هیچ چیز عوض نمی‌شود.';
	}

	public function default_enabled() {
		return false;
	}

	public function fields() {
		return array(
			'preload_skip' => array(
				'type'    => 'textarea',
				'label'   => 'آدرس‌هایی که پیش‌بارگذاری راکت نسازد (هر خط بخشی از مسیر؛ این صفحات با اولین بازدید کش می‌شوند)',
				'default' => "/product-tag/\n/tag/",
			),
			'guest_nonce'  => array(
				'type'    => 'checkbox',
				'label'   => 'اعتبار nonce بازدیدکننده‌های مهمان طولانی‌تر شود (لازم برای طول عمر کش بیشتر از ۱۰ ساعت)',
				'default' => 1,
			),
			'nonce_days'   => array(
				'type'    => 'number',
				'label'   => 'اعتبار nonce مهمان (روز). طول عمر کش راکت باید حداکثر نصف این عدد باشد.',
				'default' => 7,
				'min'     => 1,
				'max'     => 30,
			),
		);
	}

	public function notes() {
		$span = $this->lifespan();
		if ( null === $span ) {
			return '';
		}
		$days = max( 1, (int) $this->opt( 'nonce_days' ) );
		if ( 0 === $span ) {
			return 'طول عمر کش راکت: نامحدود (0). پیشنهاد: ۷۲ ساعت.';
		}
		$hours = (int) round( $span / HOUR_IN_SECONDS );
		if ( $this->opt( 'guest_nonce' ) && $span * 2 > $days * DAY_IN_SECONDS ) {
			return sprintf( 'هشدار: طول عمر کش راکت %d ساعت است و اعتبار nonce مهمان %d روز؛ طول عمر کش را حداکثر %d ساعت بگذارید یا عدد روز را بیشتر کنید.', $hours, $days, (int) floor( $days * 24 / 2 ) );
		}
		return sprintf( 'طول عمر فعلی کش راکت: %d ساعت. با اعتبار nonce مهمان %d روز، طول عمر کش تا %d ساعت امن است (پیشنهاد: ۷۲ ساعت).', $hours, $days, (int) floor( $days * 24 / 2 ) );
	}

	public function boot() {
		if ( array() !== $this->opt_lines( 'preload_skip' ) ) {
			add_filter( 'rocket_preload_exclude', array( $this, 'preload_exclude' ), 10, 2 );
		}
		if ( $this->opt( 'guest_nonce' ) ) {
			add_filter( 'nonce_life', array( $this, 'nonce_life' ), 20 );
		}
	}

	private function lifespan() {
		if ( ! function_exists( 'get_rocket_option' ) ) {
			return null;
		}
		$interval = (int) get_rocket_option( 'purge_cron_interval', 10 );
		$units    = array(
			'MINUTE_IN_SECONDS' => MINUTE_IN_SECONDS,
			'HOUR_IN_SECONDS'   => HOUR_IN_SECONDS,
			'DAY_IN_SECONDS'    => DAY_IN_SECONDS,
		);
		$unit     = (string) get_rocket_option( 'purge_cron_unit', 'HOUR_IN_SECONDS' );
		return isset( $units[ $unit ] ) ? max( 0, $interval ) * $units[ $unit ] : null;
	}

	public function preload_exclude( $excluded, $url ) {
		if ( $excluded || ! is_string( $url ) ) {
			return $excluded;
		}
		$path    = (string) wp_parse_url( $url, PHP_URL_PATH );
		$decoded = rawurldecode( $path );
		foreach ( $this->opt_lines( 'preload_skip' ) as $fragment ) {
			if ( false !== stripos( $path, $fragment ) || false !== stripos( $decoded, $fragment ) ) {
				return true;
			}
		}
		return $excluded;
	}

	public function nonce_life( $life ) {
		if ( ! function_exists( 'is_user_logged_in' ) || is_user_logged_in() ) {
			return $life;
		}
		return max( (int) $life, max( 1, (int) $this->opt( 'nonce_days' ) ) * DAY_IN_SECONDS );
	}
}
