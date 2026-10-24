<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Slider_Autoplay extends Tarh99_Perf_Module {

	public function id() {
		return 'slider_autoplay';
	}

	public function title() {
		return 'شروع چرخش خودکار اسلایدر بعد از بارگذاری صفحه';
	}

	public function description() {
		return 'اسلایدرهای المنتور (Media Carousel و Image Carousel) که چرخش خودکار دارند، به‌جای شروع از همان لحظه اول، با اولین حرکت بازدیدکننده (اسکرول، لمس، حرکت موس یا کلید) یا حداکثر چند ثانیه بعد از کامل شدن بارگذاری صفحه شروع به چرخیدن می‌کنند. ظاهر، سرعت چرخش، ترتیب اسلایدها و توقف با رفتن موس روی اسلایدر تغییر نمی‌کند؛ فقط اسلاید اول کمی بیشتر دیده می‌شود. چرخیدن اسلاید وسط تست PageSpeed باعث بالا رفتن Speed Index و نوسان نمره می‌شد.';
	}

	public function fields() {
		return array(
			'scope' => array(
				'type'    => 'text',
				'label'   => 'محدوده اجرا',
				'default' => 'front',
			),
			'delay' => array(
				'type'    => 'number',
				'label'   => 'حداکثر تأخیر شروع چرخش بعد از کامل شدن بارگذاری (ثانیه)',
				'default' => 8,
				'min'     => 1,
				'max'     => 30,
			),
		);
	}

	public function boot() {
		add_action( 'wp_footer', array( $this, 'print_script' ), 99 );
	}

	public function print_script() {
		if ( Tarh99_Perf_Env::bypass() || is_admin() || ! Tarh99_Perf_Env::scope_matches( (string) $this->opt( 'scope' ) ) ) {
			return;
		}
		$delay = max( 1, min( 30, (int) $this->opt( 'delay' ) ) ) * 1000;
		echo '<script id="tarh99-slider-autoplay">(function(){var S=".elementor-widget-media-carousel .swiper,.elementor-widget-image-carousel .swiper",started=false,ev=["scroll","wheel","touchstart","pointerdown","mousemove","keydown"];function each(f){var l=document.querySelectorAll(S);for(var i=0;i<l.length;i++){var s=l[i].swiper;if(s&&s.autoplay&&s.params&&s.params.autoplay){f(s,l[i]);}}}function hold(){each(function(s,el){if(!started&&!el.__t99held){el.__t99held=1;s.autoplay.stop();}});}function go(e){if(started){return;}if(e&&e.type==="scroll"&&(window.pageYOffset||document.documentElement.scrollTop||0)<20){return;}started=true;for(var i=0;i<ev.length;i++){window.removeEventListener(ev[i],go,{passive:true});}each(function(s,el){if(!el.__t99held){return;}var w=el.closest(".elementor-widget"),o={};try{o=JSON.parse(w&&w.getAttribute("data-settings")||"{}");}catch(e){}if(o.pause_on_hover==="yes"&&el.matches&&el.matches(":hover")){return;}s.autoplay.start();});}for(var i=0;i<ev.length;i++){window.addEventListener(ev[i],go,{passive:true});}var n=0,t=setInterval(function(){hold();if(started||++n>300){clearInterval(t);}},50);function arm(){setTimeout(go,' . (int) $delay . ');}if(document.readyState==="complete"){arm();}else{window.addEventListener("load",arm);}})();</script>' . "\n";
	}
}
