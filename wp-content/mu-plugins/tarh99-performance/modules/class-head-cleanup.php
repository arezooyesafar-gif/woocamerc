<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Head_Cleanup extends Tarh99_Perf_Module {

	public function id() {
		return 'head_cleanup';
	}

	public function title() {
		return 'پاکسازی هد و فایل‌های اضافه';
	}

	public function description() {
		return 'حذف ایموجی، لینک‌های embed، generator، RSD و shortlink و dashicons برای مهمان. هیچ‌کدام تأثیری روی ظاهر ندارند.';
	}

	public function fields() {
		return array(
			'emoji'          => array(
				'type'    => 'checkbox',
				'label'   => 'غیرفعال‌سازی اسکریپت و استایل ایموجی',
				'default' => 1,
			),
			'embeds'         => array(
				'type'    => 'checkbox',
				'label'   => 'حذف لینک‌های oEmbed discovery از هد',
				'default' => 1,
			),
			'generator'      => array(
				'type'    => 'checkbox',
				'label'   => 'حذف متای generator (نسخه وردپرس)',
				'default' => 1,
			),
			'rsd'            => array(
				'type'    => 'checkbox',
				'label'   => 'حذف لینک RSD و wlwmanifest',
				'default' => 1,
			),
			'shortlink'      => array(
				'type'    => 'checkbox',
				'label'   => 'حذف shortlink از هد و هدر HTTP',
				'default' => 1,
			),
			'dashicons'      => array(
				'type'    => 'checkbox',
				'label'   => 'حذف dashicons برای مهمان‌ها (فقط اگر هیچ فایلی به آن وابسته نباشد)',
				'default' => 1,
			),
			'rest_link'      => array(
				'type'    => 'checkbox',
				'label'   => 'حذف لینک REST API از هد',
				'default' => 0,
			),
			'feed_extra'     => array(
				'type'    => 'checkbox',
				'label'   => 'حذف لینک فیدهای اضافی (دسته/دیدگاه)',
				'default' => 0,
			),
			'jquery_migrate' => array(
				'type'    => 'checkbox',
				'label'   => 'حذف jquery-migrate در فرانت (فقط بعد از تست کامل)',
				'default' => 0,
			),
			'global_styles'  => array(
				'type'    => 'checkbox',
				'label'   => 'حذف global-styles و classic-theme-styles (فقط بعد از تست ظاهری)',
				'default' => 0,
			),
			'xmlrpc'         => array(
				'type'    => 'checkbox',
				'label'   => 'غیرفعال‌سازی XML-RPC',
				'default' => 0,
			),
		);
	}

	public function boot() {
		add_action( 'init', array( $this, 'on_init' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'on_enqueue' ), 9999 );
		if ( $this->opt( 'xmlrpc' ) ) {
			add_filter( 'xmlrpc_enabled', '__return_false' );
		}
		if ( $this->opt( 'jquery_migrate' ) ) {
			add_action( 'wp_default_scripts', array( $this, 'drop_jquery_migrate' ), 20 );
		}
	}

	public function on_init() {
		if ( is_admin() || Tarh99_Perf_Env::bypass() ) {
			return;
		}
		if ( $this->opt( 'emoji' ) ) {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
			remove_action( 'embed_head', 'print_emoji_detection_script' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			add_filter( 'emoji_svg_url', '__return_false' );
			add_filter( 'wp_resource_hints', array( $this, 'drop_emoji_hint' ), 10, 2 );
		}
		if ( $this->opt( 'embeds' ) ) {
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		}
		if ( $this->opt( 'generator' ) ) {
			remove_action( 'wp_head', 'wp_generator' );
			add_filter( 'the_generator', '__return_empty_string' );
		}
		if ( $this->opt( 'rsd' ) ) {
			remove_action( 'wp_head', 'rsd_link' );
			remove_action( 'wp_head', 'wlwmanifest_link' );
		}
		if ( $this->opt( 'shortlink' ) ) {
			remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
			remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
		}
		if ( $this->opt( 'rest_link' ) ) {
			remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
			remove_action( 'template_redirect', 'rest_output_link_header', 11 );
		}
		if ( $this->opt( 'feed_extra' ) ) {
			remove_action( 'wp_head', 'feed_links_extra', 3 );
		}
		if ( $this->opt( 'global_styles' ) ) {
			remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
			remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
			remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
		}
	}

	public function on_enqueue() {
		if ( ! Tarh99_Perf_Env::front_ok() ) {
			return;
		}
		if ( $this->opt( 'dashicons' ) && ! is_user_logged_in() && ! Tarh99_Perf_Env::tps( 'remove_dashicons' ) ) {
			Tarh99_Perf_Assets::safe_dequeue( 'css', 'dashicons' );
		}
		if ( $this->opt( 'global_styles' ) ) {
			Tarh99_Perf_Assets::safe_dequeue( 'css', 'classic-theme-styles' );
		}
	}

	public function drop_emoji_hint( $urls, $relation ) {
		if ( 'dns-prefetch' !== $relation || ! is_array( $urls ) ) {
			return $urls;
		}
		$out = array();
		foreach ( $urls as $url ) {
			$href = is_array( $url ) ? ( isset( $url['href'] ) ? $url['href'] : '' ) : (string) $url;
			if ( false !== strpos( $href, 's.w.org' ) ) {
				continue;
			}
			$out[] = $url;
		}
		return $out;
	}

	public function drop_jquery_migrate( $scripts ) {
		if ( is_admin() || Tarh99_Perf_Env::bypass() || ! isset( $scripts->registered['jquery'] ) ) {
			return;
		}
		$jquery = $scripts->registered['jquery'];
		if ( is_array( $jquery->deps ) ) {
			$jquery->deps = array_values( array_diff( $jquery->deps, array( 'jquery-migrate' ) ) );
		}
	}
}
