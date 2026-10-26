<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Nav_Menu extends Tarh99_Perf_Module {

	private $used = false;

	public function id() {
		return 'nav_menu';
	}

	public function title() {
		return 'کار کردن منوی هدر قبل از اجرای اسکریپت‌ها';
	}

	public function description() {
		return 'زیرمنوهای منوی افقی هدر (ابزارک منوی المنتور) فقط بعد از اجرای همه اسکریپت‌های صفحه باز می‌شوند و فلش کنار آیتم‌ها هم همان موقع اضافه می‌شود؛ در اینترنت کند بازدید اول این چند ثانیه طول می‌کشد و منو در این مدت «هنگ» به نظر می‌رسد. این ماژول فلش‌ها را از قبل در HTML قرار می‌دهد (همان آیکن و همان جای نهایی، پس منو بعد از اجرای اسکریپت جابه‌جا نمی‌شود) و تا وقتی اسکریپت منو اجرا نشده، زیرمنوها با نگه داشتن موس یا فوکوس کیبورد با همان اندازه و جای نهایی باز می‌شوند. به‌محض اجرای اسکریپت المنتور، همه چیز مثل قبل در اختیار خود المنتور است. فایل اسکریپت منوی المنتور هم در نسخه دسکتاپ زودتر دانلود می‌شود.';
	}

	public function fields() {
		return array(
			'scope'   => array(
				'type'    => 'text',
				'label'   => 'محدوده اجرا',
				'default' => 'all',
			),
			'arrows'  => array(
				'type'    => 'checkbox',
				'label'   => 'فلش زیرمنوها از ابتدا در HTML باشد',
				'default' => 1,
			),
			'hover'   => array(
				'type'    => 'checkbox',
				'label'   => 'زیرمنوها قبل از اجرای اسکریپت با موس و کیبورد باز شوند',
				'default' => 1,
			),
			'preload' => array(
				'type'    => 'checkbox',
				'label'   => 'پیش‌بارگذاری فایل اسکریپت منوی المنتور در نسخه دسکتاپ',
				'default' => 1,
			),
		);
	}

	public function boot() {
		add_filter( 'elementor/frontend/the_content', array( $this, 'filter_content' ), 21 );
		add_action( 'wp_head', array( $this, 'print_head' ), 8 );
		add_action( 'wp_footer', array( $this, 'print_footer' ), 1 );
	}

	private function active() {
		return defined( 'ELEMENTOR_PRO_VERSION' ) && Tarh99_Perf_Env::front_ok() && Tarh99_Perf_Env::scope_matches( (string) $this->opt( 'scope' ) );
	}

	public function print_head() {
		if ( ! $this->active() ) {
			return;
		}
		if ( $this->opt( 'hover' ) ) {
			echo '<style id="tarh99-nav-menu">' . $this->css() . "</style>\n" . $this->script() . "\n";
		}
		if ( $this->opt( 'preload' ) ) {
			$url = $this->chunk_url();
			if ( '' !== $url ) {
				echo '<link rel="preload" as="script" href="' . esc_url( $url ) . '" id="tarh99-nav-menu-preload">' . "\n";
			}
		}
	}

	public function print_footer() {
		if ( $this->used && ! $this->opt( 'hover' ) && $this->active() ) {
			echo '<style id="tarh99-nav-menu">' . $this->arrow_css() . "</style>\n";
		}
	}

	private function arrow_css() {
		return '.elementor-nav-menu[data-smartmenus-id] .t99-sub-arrow{display:none!important}';
	}

	private function css() {
		$base = '.elementor-widget-nav-menu .elementor-nav-menu--main.elementor-nav-menu--layout-horizontal>ul.elementor-nav-menu:not([data-smartmenus-id])';
		$gap  = 'calc(var(--e-nav-menu-horizontal-menu-item-margin,0px) + var(--e-nav-menu-divider-width,0px))';
		$css  = $this->arrow_css()
			. $base . ' li:hover>ul,' . $base . ' li:focus-within>ul{display:block;width:auto;min-width:10em;max-width:1000px;z-index:3}'
			. $base . ' ul li:hover>ul,' . $base . ' ul li:focus-within>ul{top:0!important;' . ( is_rtl() ? 'right:100%;left:auto' : 'left:100%;right:auto' ) . ';z-index:4}';
		if ( is_rtl() ) {
			$css .= $base . '>li:first-child:not(:last-child)>ul{right:' . $gap . '}'
				. $base . '>li:not(:first-child):not(:last-child)>ul{min-width:calc(10em + ' . substr( $gap, 5 ) . '}'
				. $base . '>li:not(:first-child):not(:last-child)>ul>li>a{border-left:' . $gap . ' solid transparent}';
		}
		return $css;
	}

	private function script() {
		return '<script id="tarh99-nav-menu-js">(function(w,d){var M=".elementor-nav-menu--main>ul.elementor-nav-menu:not([data-smartmenus-id])",J="jQ"+"uery",mo=null;function off(r){var l=r.querySelectorAll("a.t99-hl");for(var i=0;i<l.length;i++){l[i].classList.remove("highlighted","t99-hl");}}function sync(e){var t=e.target,n=t&&t.closest?t.closest(M):null,l,i,s;if(!n&&!d.querySelector("a.t99-hl")){return;}l=d.querySelectorAll("a.t99-hl");for(i=0;i<l.length;i++){if(!l[i].parentNode.matches(":hover")){l[i].classList.remove("highlighted","t99-hl");}}if(!n){return;}l=n.querySelectorAll("li:hover>a");for(i=0;i<l.length;i++){s=l[i].nextElementSibling;if(s&&s.tagName==="UL"&&!l[i].classList.contains("highlighted")){l[i].classList.add("highlighted","t99-hl");}}}function stop(){d.removeEventListener("mouseover",sync,true);d.removeEventListener("mouseout",sync,true);if(mo){mo.disconnect();}}d.addEventListener("mouseover",sync,true);d.addEventListener("mouseout",sync,true);if(!w.MutationObserver){return;}mo=new MutationObserver(function(rs){for(var i=0;i<rs.length;i++){var u=rs[i].target;if(!u.classList||!u.classList.contains("elementor-nav-menu")||!u.hasAttribute("data-smartmenus-id")){continue;}off(u);var Q=w[J];if(!Q||!Q.fn||!Q.fn.smartmenus){continue;}var ls=u.querySelectorAll("li:hover");for(var j=0;j<ls.length;j++){var a=ls[j].firstElementChild;if(a&&a.tagName==="A"&&a.nextElementSibling&&a.nextElementSibling.tagName==="UL"){try{Q(u).smartmenus("itemActivate",Q(a));}catch(e){}}}}if(!d.querySelector(M)){stop();}});mo.observe(d.documentElement,{attributes:true,subtree:true,attributeFilter:["data-smartmenus-id"]});})(window,document);</script>';
	}

	private function chunk_url() {
		if ( wp_is_mobile() || ! defined( 'ELEMENTOR_PRO_URL' ) || ! defined( 'ELEMENTOR_PRO_PATH' ) || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) {
			return '';
		}
		if ( Tarh99_Perf_Env::rocket_active() && ! ( Tarh99_Perf_Env::rocket_option( 'cache_mobile' ) && Tarh99_Perf_Env::rocket_option( 'do_caching_mobile_files' ) ) ) {
			return '';
		}
		$runtime = ELEMENTOR_PRO_PATH . 'assets/js/webpack-pro.runtime.min.js';
		if ( ! is_readable( $runtime ) ) {
			return '';
		}
		$key  = 'tarh99_nav_chunk_' . md5( ELEMENTOR_PRO_VERSION . '|' . (int) @filemtime( $runtime ) );
		$file = get_transient( $key );
		if ( false === $file ) {
			$file = '';
			if ( preg_match( '#"(nav-menu\.[0-9a-f]+\.bundle\.min\.js)"#', (string) file_get_contents( $runtime ), $m ) && is_readable( ELEMENTOR_PRO_PATH . 'assets/js/' . $m[1] ) ) {
				$file = $m[1];
			}
			set_transient( $key, $file, WEEK_IN_SECONDS );
		}
		return '' === $file ? '' : ELEMENTOR_PRO_URL . 'assets/js/' . $file;
	}

	public function filter_content( $content ) {
		if ( ! is_string( $content ) || false === strpos( $content, 'nav-menu.default' ) || ! $this->opt( 'arrows' ) || ! $this->active() ) {
			return $content;
		}
		try {
			return $this->add_arrows( $content );
		} catch ( Throwable $e ) {
			return $content;
		}
	}

	private function add_arrows( $content ) {
		if ( ! preg_match_all( '#<div\b[^>]*\sdata-widget_type="nav-menu\.default"[^>]*>#', $content, $tags, PREG_OFFSET_CAPTURE ) ) {
			return $content;
		}
		$edits = array();
		foreach ( $tags[0] as $tag ) {
			$icon = $this->icon( $tag[0] );
			if ( '' === $icon ) {
				continue;
			}
			$from = $tag[1] + strlen( $tag[0] );
			if ( ! preg_match( '#<nav\b[^>]*\sclass="[^"]*\belementor-nav-menu--main\b[^"]*\belementor-nav-menu--layout-horizontal\b[^"]*"[^>]*>\s*<ul\b[^>]*\sclass="elementor-nav-menu"[^>]*>#', $content, $nav, PREG_OFFSET_CAPTURE, $from ) ) {
				continue;
			}
			$next = strpos( $content, 'data-widget_type=', $from );
			if ( false !== $next && $nav[0][1] > $next ) {
				continue;
			}
			$start = $nav[0][1] + strlen( $nav[0][0] ) - strlen( strrchr( $nav[0][0], '<' ) );
			$end   = $this->closing( $content, $start );
			if ( false === $end ) {
				continue;
			}
			$menu = substr( $content, $start, $end - $start );
			if ( false !== strpos( $menu, 't99-sub-arrow' ) || false !== strpos( $menu, 'class="sub-arrow' ) ) {
				continue;
			}
			$new = preg_replace( '#</a>(\s*<ul\b[^>]*\sclass="sub-menu\b)#', '<span class="sub-arrow t99-sub-arrow">' . str_replace( '$', '\\$', $icon ) . '</span></a>$1', $menu );
			if ( is_string( $new ) && $new !== $menu ) {
				$edits[] = array( $start, $end, $new );
			}
		}
		for ( $i = count( $edits ) - 1; $i >= 0; $i-- ) {
			$content = substr_replace( $content, $edits[ $i ][2], $edits[ $i ][0], $edits[ $i ][1] - $edits[ $i ][0] );
		}
		if ( $edits ) {
			$this->used = true;
		}
		return $content;
	}

	private function icon( $tag ) {
		if ( ! preg_match( '#\sdata-settings="([^"]*)"#', $tag, $m ) ) {
			return '';
		}
		$settings = json_decode( html_entity_decode( $m[1], ENT_QUOTES, 'UTF-8' ), true );
		if ( ! is_array( $settings ) || ( isset( $settings['layout'] ) && 'horizontal' !== $settings['layout'] ) ) {
			return '';
		}
		if ( empty( $settings['submenu_icon']['value'] ) || ! is_string( $settings['submenu_icon']['value'] ) ) {
			return '';
		}
		$value = trim( $settings['submenu_icon']['value'] );
		if ( false === strpos( $value, '<' ) ) {
			return preg_match( '#^[A-Za-z0-9 _-]+$#', $value ) ? '<i class="' . esc_attr( $value ) . '" aria-hidden="true"></i>' : '';
		}
		return preg_match( '#^<i class="[A-Za-z0-9 _-]+"(?: aria-hidden="true")?></i>$#', $value ) ? $value : '';
	}

	private function closing( $content, $start ) {
		$depth  = 0;
		$offset = $start;
		while ( preg_match( '#<(/?)ul\b[^>]*>#i', $content, $m, PREG_OFFSET_CAPTURE, $offset ) ) {
			$offset = $m[0][1] + strlen( $m[0][0] );
			if ( '' === $m[1][0] ) {
				$depth++;
				continue;
			}
			$depth--;
			if ( 0 === $depth ) {
				return $offset;
			}
		}
		return false;
	}
}
