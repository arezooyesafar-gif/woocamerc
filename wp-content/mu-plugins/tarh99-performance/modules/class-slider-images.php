<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Slider_Images extends Tarh99_Perf_Module {

	public function id() {
		return 'slider_images';
	}

	public function title() {
		return 'دانلود دیرتر تصاویر اسلایدهای پنهان اسلایدر در موبایل';
	}

	public function description() {
		return 'در اسلایدر Media Carousel المنتور، همه تصاویر اسلایدها همان لحظه اول با هم دانلود می‌شوند، در حالی که در موبایل فقط اسلاید اول دیده می‌شود. این دانلودها با تصویر اصلی صفحه (LCP) رقابت می‌کنند و PageSpeed موبایل آن‌ها را در محاسبه LCP حساب می‌کند. با این ماژول در عرض‌های موبایل، تصاویر اسلایدهایی که دیده نمی‌شوند بلافاصله بعد از نمایش صفحه (رویداد load)، با اولین لمس صفحه یا حداکثر ۴ ثانیه بعد دانلود می‌شوند. اسلاید اول، ظاهر، ترتیب و چرخش اسلایدر تغییر نمی‌کند و دسکتاپ دست نمی‌خورد.';
	}

	public function fields() {
		return array(
			'scope'     => array(
				'type'    => 'text',
				'label'   => 'محدوده اجرا',
				'default' => 'front',
			),
			'max_width' => array(
				'type'    => 'number',
				'label'   => 'حداکثر عرض صفحه (پیکسل) که اسلایدر در آن فقط یک اسلاید نشان می‌دهد',
				'default' => 767,
				'min'     => 320,
				'max'     => 1024,
			),
		);
	}

	public function boot() {
		add_action( 'wp_head', array( $this, 'print_assets' ), 8 );
	}

	public function print_assets() {
		if ( Tarh99_Perf_Env::bypass() || is_admin() || ! Tarh99_Perf_Env::scope_matches( (string) $this->opt( 'scope' ) ) ) {
			return;
		}
		$max  = max( 320, min( 1024, (int) $this->opt( 'max_width' ) ) );
		$base = 'html:not(.t99-slides) .elementor-widget-media-carousel ';
		echo '<style id="tarh99-slider-images">@media (max-width:' . (int) $max . 'px){' . $base . '.swiper:not(.swiper-initialized) .swiper-slide:nth-child(n+2) .elementor-carousel-image,' . $base . '.swiper-initialized .swiper-slide:not(.swiper-slide-active) .elementor-carousel-image{background-image:none!important}}</style>' . "\n";
		echo '<script id="tarh99-slider-images-js">(function(){var d=document.documentElement,done=false,ev=["touchstart","pointerdown","keydown"];function on(){if(done){return;}done=true;d.classList.add("t99-slides");for(var i=0;i<ev.length;i++){window.removeEventListener(ev[i],on,{passive:true});}}for(var i=0;i<ev.length;i++){window.addEventListener(ev[i],on,{passive:true});}function later(){requestAnimationFrame(function(){setTimeout(on,0);});}if(document.readyState==="complete"){later();}else{window.addEventListener("load",later);}setTimeout(on,4000);})();</script>' . "\n";
	}
}
