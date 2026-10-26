<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Elementor_Delay extends Tarh99_Perf_Module {

	public function id() {
		return 'elementor_delay';
	}

	public function title() {
		return 'اجرای اسکریپت‌های المنتور بعد از اولین تعامل کاربر';
	}

	public function description() {
		return 'المنتور بعد از بارگذاری صفحه برای تک‌تک المان‌های صفحه (در صفحه اصلی حدود ۱۲۰ المان در هدر، محتوا و فوتر) یک کار جداگانه پشت سر هم اجرا می‌کند. اگر مرورگر قبل از شروع این صف فرصت نکند صفحه را نمایش دهد، نمایش اول تا پایان کل صف عقب می‌افتد؛ این همان «Element render delay» حدود ۲ ثانیه در PageSpeed موبایل و علت نوسان نمره صفحه اصلی و دسته‌بندی بین ۷۵ و ۹۵ است. با این ماژول، شروع المنتور تا اولین لمس، اسکرول، حرکت موس یا کلید بازدیدکننده (یا حداکثر چند ثانیه بعد از کامل شدن بارگذاری) عقب می‌افتد. فایل‌ها همان لحظه اول دانلود می‌شوند و فقط اجرای آن‌ها صبر می‌کند. اسلایدر صفحه اصلی و منوها قبل از اجرا هم با همان ظاهر نهایی نمایش داده می‌شوند. در حالت «بعد از اولین نمایش»، المنتور بلافاصله بعد از اولین نمایش صفحه اجرا می‌شود که فقط نوسان را برطرف می‌کند.';
	}

	public function risk() {
		return 'medium';
	}

	public function fields() {
		return array(
			'scope'    => array(
				'type'    => 'text',
				'label'   => 'محدوده اجرا (front = صفحه اصلی، product_archive = فروشگاه و دسته‌بندی‌ها، product = صفحه محصول)',
				'default' => 'front, product_archive',
			),
			'devices'  => array(
				'type'    => 'select',
				'label'   => 'دستگاه‌ها',
				'options' => array(
					'mobile' => 'فقط موبایل و تبلت (نسخه موبایل کش WP Rocket)',
					'all'    => 'همه دستگاه‌ها',
				),
				'default' => 'mobile',
			),
			'mode'     => array(
				'type'    => 'select',
				'label'   => 'زمان اجرای المنتور',
				'options' => array(
					'interaction' => 'اولین تعامل کاربر (لمس، اسکرول، حرکت موس یا کلید)',
					'paint'       => 'بلافاصله بعد از اولین نمایش صفحه',
				),
				'default' => 'interaction',
			),
			'fallback' => array(
				'type'    => 'number',
				'label'   => 'اگر کاربر هیچ تعاملی نداشت، چند ثانیه بعد از کامل شدن بارگذاری صفحه اجرا شود',
				'default' => 8,
				'min'     => 1,
				'max'     => 30,
			),
		);
	}

	public function boot() {
		add_action( 'wp_head', array( $this, 'print_script' ), 1 );
	}

	public function print_script() {
		if ( ! Tarh99_Perf_Env::front_ok() || ! defined( 'ELEMENTOR_VERSION' ) || ! Tarh99_Perf_Env::scope_matches( (string) $this->opt( 'scope' ) ) ) {
			return;
		}
		if ( 'all' !== $this->opt( 'devices' ) ) {
			if ( ! wp_is_mobile() || ( Tarh99_Perf_Env::rocket_active() && Tarh99_Perf_Env::rocket_option( 'cache_mobile' ) && ! Tarh99_Perf_Env::rocket_option( 'do_caching_mobile_files' ) ) ) {
				return;
			}
		}
		echo $this->script( 'paint' === $this->opt( 'mode' ), max( 1, min( 30, (int) $this->opt( 'fallback' ) ) ) * 1000 ) . "\n";
	}

	private function script( $paint, $fallback ) {
		return '<script id="tarh99-elementor-delay">(function(w,d){if(w.elementorFrontend){return;}var v,q=null,go=false,P=' . ( $paint ? 'true' : 'false' ) . ',ev=["touchstart","pointerdown","mousedown","keydown","wheel","mousemove","scroll"],o={passive:true,capture:true};function run(e){if(go||(e&&e.type==="scroll"&&!(w.pageYOffset>0))){return;}go=true;for(var i=0;i<ev.length;i++){w.removeEventListener(ev[i],run,o);}if(q){var f=q;q=null;f();}}try{Object.defineProperty(w,"elementorFrontend",{configurable:true,enumerable:true,get:function(){return v;},set:function(n){v=n;if(n&&typeof n.init==="function"&&!n.t99d){var init=n.init;n.t99d=1;n.init=function(){var s=this,a=arguments;if(go){return init.apply(s,a);}q=function(){init.apply(s,a);};if(P){w.requestAnimationFrame(function(){setTimeout(run,0);});}};}}});}catch(e){return;}for(var i=0;i<ev.length;i++){w.addEventListener(ev[i],run,o);}function arm(){setTimeout(run,' . (int) $fallback . ');}if(d.readyState==="complete"){arm();}else{w.addEventListener("load",arm);}})(window,document);</script>';
	}
}
