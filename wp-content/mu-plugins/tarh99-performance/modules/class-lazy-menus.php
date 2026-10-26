<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Lazy_Menus extends Tarh99_Perf_Module {

	public function id() {
		return 'lazy_menus';
	}

	public function title() {
		return 'ساخت دیرتر منوهای بزرگ المنتور که در دستگاه فعلی دیده نمی‌شوند';
	}

	public function description() {
		return 'هدر سایت منوی کامل دسته‌بندی‌ها (۲۶۶ آیتم) را چهار بار در HTML دارد: منوی افقی دسکتاپ و نسخه کشویی همان منو (ابزارک منوی المنتور) و منوهای تبلت و موبایل قالب پوشه. در موبایل هر دو نسخه منوی المنتور مخفی هستند، ولی مرورگر در هر بار محاسبه ظاهر صفحه و در اجرای اسکریپت‌ها با حدود ۱۱۰۰ المان آن‌ها هم سر و کار دارد. این ماژول این منوها را داخل تگ template نگه می‌دارد تا جزو صفحه زنده نباشند: هر منویی که در اندازه فعلی صفحه دیده می‌شود همان لحظه ساخته می‌شود و بقیه درست قبل از شروع اسکریپت‌های المنتور یا با تغییر عرض صفحه (مثلاً چرخاندن گوشی). لینک‌ها، ترتیب، زیرمنوها و ظاهر منوها تغییر نمی‌کند و منوهای قالب پوشه دست نمی‌خورند.';
	}

	public function fields() {
		return array(
			'scope'     => array(
				'type'    => 'text',
				'label'   => 'محدوده اجرا',
				'default' => 'all',
			),
			'devices'   => array(
				'type'    => 'select',
				'label'   => 'دستگاه‌ها',
				'options' => array(
					'mobile' => 'فقط موبایل و تبلت (نسخه موبایل کش WP Rocket)',
					'all'    => 'همه دستگاه‌ها',
				),
				'default' => 'mobile',
			),
			'min_items' => array(
				'type'    => 'number',
				'label'   => 'حداقل تعداد آیتم منو برای اعمال',
				'default' => 40,
				'min'     => 5,
				'max'     => 5000,
			),
		);
	}

	public function boot() {
		add_filter( 'elementor/frontend/the_content', array( $this, 'filter_content' ), 22 );
	}

	private function active() {
		if ( ! Tarh99_Perf_Env::front_ok() || ! Tarh99_Perf_Env::scope_matches( (string) $this->opt( 'scope' ) ) ) {
			return false;
		}
		if ( 'all' === $this->opt( 'devices' ) ) {
			return true;
		}
		if ( ! wp_is_mobile() ) {
			return false;
		}
		return ! ( Tarh99_Perf_Env::rocket_active() && Tarh99_Perf_Env::rocket_option( 'cache_mobile' ) && ! Tarh99_Perf_Env::rocket_option( 'do_caching_mobile_files' ) );
	}

	public function filter_content( $content ) {
		if ( ! is_string( $content ) || false === strpos( $content, 'elementor-nav-menu' ) || ! $this->active() ) {
			return $content;
		}
		return $this->wrap( $content );
	}

	private function wrap( $content ) {
		if ( ! preg_match_all( '#<ul\b[^>]*\sclass\s*=\s*"([^"]*)"[^>]*>#i', $content, $tags, PREG_OFFSET_CAPTURE ) ) {
			return $content;
		}
		$min   = max( 5, (int) $this->opt( 'min_items' ) );
		$wraps = array();
		$after = -1;
		foreach ( $tags[0] as $i => $tag ) {
			$start = $tag[1];
			if ( $start < $after || ! preg_match( '#(?:^|\s)elementor-nav-menu(?:\s|$)#', $tags[1][ $i ][0] ) ) {
				continue;
			}
			$end = $this->closing( $content, $start );
			if ( false === $end ) {
				continue;
			}
			$after = $end;
			$html  = substr( $content, $start, $end - $start );
			if ( substr_count( $html, '<li' ) < $min || false !== stripos( $html, '<script' ) || false !== stripos( $html, '<template' ) ) {
				continue;
			}
			$wraps[] = array( $start, $end );
		}
		for ( $i = count( $wraps ) - 1; $i >= 0; $i-- ) {
			list( $start, $end ) = $wraps[ $i ];
			$content = substr_replace( $content, '</template>' . $this->script(), $end, 0 );
			$content = substr_replace( $content, '<template class="tarh99-menu">', $start, 0 );
		}
		if ( $wraps ) {
			$content = substr_replace( $content, $this->definition(), strpos( $content, '<template class="tarh99-menu">' ), 0 );
		}
		return $content;
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

	private function script() {
		return '<script>window.t99Menu&&t99Menu();</script>';
	}

	private function definition() {
		return '<script class="tarh99-lazy-menus">(function(w,d){if(w.t99Menu){return;}var list=[],on=false,lw=w.innerWidth,tm;function shown(e){if(!e){return true;}if(e.checkVisibility){return e.checkVisibility();}for(var n=e;n&&n.nodeType===1;n=n.parentNode){if(w.getComputedStyle(n).display==="none"){return false;}}return true;}function open(t){if(t.parentNode){t.parentNode.replaceChild(t.content,t);}}function stop(){if(on){on=false;w.removeEventListener("elementor/frontend/init",all);w.removeEventListener("resize",rs);}}function all(){var l=list;list=[];for(var i=0;i<l.length;i++){open(l[i]);}stop();}function check(){var k=[];for(var i=0;i<list.length;i++){if(shown(list[i].parentNode)){open(list[i]);}else{k.push(list[i]);}}list=k;if(!list.length){stop();}}function rs(){if(w.innerWidth===lw){return;}lw=w.innerWidth;clearTimeout(tm);tm=setTimeout(check,100);}w.t99Menu=function(){var s=d.currentScript,t=s&&s.previousElementSibling;if(s&&s.parentNode){s.parentNode.removeChild(s);}if(!t||t.tagName!=="TEMPLATE"||!t.content){return;}if(shown(t.parentNode)){open(t);return;}list.push(t);if(!on){on=true;w.addEventListener("elementor/frontend/init",all);w.addEventListener("resize",rs);}};})(window,document);</script>';
	}
}
