<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Preload_Order extends Tarh99_Perf_Module {

	private $done = false;

	public function id() {
		return 'preload_order';
	}

	public function group() {
		return 'server';
	}

	public function title() {
		return 'اول ساخته شدن کش صفحه اصلی، فروشگاه و دسته‌بندی‌ها بعد از پاک شدن کش راکت';
	}

	public function description() {
		return 'بعد از پاک شدن کل کش WP Rocket (مثلاً بعد از ذخیره تنظیمات، آپدیت افزونه یا دکمه پاکسازی)، پیش‌بارگذاری راکت حدود ۳۰۰۰ صفحه را به ترتیب دلخواه خودش دوباره می‌سازد و ممکن است صفحه اصلی و دسته‌بندی‌ها ساعت‌ها بعد نوبتشان برسد؛ تا آن موقع هر بازدیدکننده‌ای که به این صفحات بیاید ۴ تا ۶ ثانیه منتظر ساخته شدن صفحه می‌ماند. این ماژول فقط ترتیب صف پیش‌بارگذاری خود راکت را عوض می‌کند تا صفحه اصلی، صفحه فروشگاه و صفحات دسته‌بندی محصولات (و آدرس‌های دلخواه شما) اول ساخته شوند. بعد از پاک شدن کش یک محصول هم صفحه اصلی که راکت دوباره در صف می‌گذارد، جلوی صف می‌رود. چیزی حذف یا اضافه نمی‌شود.';
	}

	public function fields() {
		return array(
			'targets' => array(
				'type'    => 'multicheck',
				'label'   => 'صفحاتی که اول ساخته شوند',
				'options' => array(
					'home' => 'صفحه اصلی',
					'shop' => 'صفحه فروشگاه',
					'cats' => 'دسته‌بندی‌های محصولات (دسته‌های اصلی قبل از زیردسته‌ها)',
				),
				'default' => array( 'home', 'shop', 'cats' ),
			),
			'extra'   => array(
				'type'    => 'textarea',
				'label'   => 'آدرس‌های دیگر (هر خط یک آدرس کامل)',
				'default' => '',
			),
		);
	}

	public function boot() {
		add_action( 'rocket_after_clean_domain', array( $this, 'prioritize' ), 20 );
		add_action( 'after_rocket_clean_post', array( $this, 'prioritize_home' ), 20 );
		add_action( 'after_rocket_clean_term', array( $this, 'prioritize_home' ), 20 );
	}

	private function table() {
		global $wpdb;
		if ( ! Tarh99_Perf_Env::rocket_active() || ! Tarh99_Perf_Env::rocket_option( 'manual_preload' ) ) {
			return '';
		}
		$table = $wpdb->prefix . 'wpr_rocket_cache';
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) ) !== $table ) {
			return '';
		}
		return $table;
	}

	public function prioritize() {
		if ( $this->done ) {
			return;
		}
		$this->done = true;
		$table      = $this->table();
		if ( '' === $table ) {
			return;
		}
		foreach ( $this->groups() as $second => $urls ) {
			$this->update( $table, $urls, $second );
		}
	}

	public function prioritize_home() {
		if ( $this->done || ! in_array( 'home', $this->opt_list( 'targets' ), true ) ) {
			return;
		}
		$table = $this->table();
		if ( '' !== $table ) {
			$this->update( $table, array( home_url( '/' ) ), 0 );
		}
	}

	private function groups() {
		$targets = $this->opt_list( 'targets' );
		$groups  = array(
			0 => array(),
			1 => array(),
			2 => array(),
			3 => array(),
		);
		if ( in_array( 'home', $targets, true ) ) {
			$groups[0][] = home_url( '/' );
		}
		if ( in_array( 'shop', $targets, true ) && function_exists( 'wc_get_page_permalink' ) ) {
			$groups[1][] = wc_get_page_permalink( 'shop' );
		}
		foreach ( $this->opt_lines( 'extra' ) as $url ) {
			if ( 0 === strpos( $url, 'http' ) ) {
				$groups[1][] = $url;
			}
		}
		if ( in_array( 'cats', $targets, true ) && taxonomy_exists( 'product_cat' ) ) {
			$terms = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => true,
					'number'     => 2000,
				)
			);
			if ( is_array( $terms ) ) {
				foreach ( $terms as $term ) {
					$link = get_term_link( $term );
					if ( is_string( $link ) ) {
						$groups[ $term->parent ? 3 : 2 ][] = $link;
					}
				}
			}
		}
		return $groups;
	}

	private function update( $table, $urls, $second ) {
		global $wpdb;
		$clean = array();
		foreach ( $urls as $url ) {
			$url = apply_filters( 'rocket_preload_format_url', (string) $url );
			$url = untrailingslashit( (string) strtok( $url, '?' ) );
			if ( '' !== $url ) {
				$clean[ $url ] = true;
			}
		}
		$date = sprintf( '2000-01-01 00:00:%02d', (int) $second );
		foreach ( array_chunk( array_keys( $clean ), 100 ) as $chunk ) {
			$in = implode( ',', array_fill( 0, count( $chunk ), '%s' ) );
			$wpdb->query( $wpdb->prepare( "UPDATE `{$table}` SET modified = %s WHERE status = 'pending' AND url IN ({$in})", array_merge( array( $date ), $chunk ) ) );
		}
	}
}
