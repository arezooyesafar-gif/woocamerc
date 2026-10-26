<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Jquery_Ready extends Tarh99_Perf_Module {

	public function id() {
		return 'jquery_ready';
	}

	public function title() {
		return 'راه‌اندازی کامل المنتور پرو (منوی هدر) در اینترنت کند';
	}

	public function description() {
		return 'وقتی اسکریپت‌ها با defer بارگذاری می‌شوند (تنظیم Load JavaScript deferred راکت)، jQuery رویداد «آماده بودن صفحه» را همان لحظه‌ای اعلام می‌کند که بین رسیدن دو فایل اسکریپت مکث کوتاهی پیش بیاید. در اینترنت کند این مکث تقریباً همیشه پیش می‌آید: المنتور قبل از رسیدن فایل‌های المنتور پرو راه‌اندازی می‌شود و المنتور پرو دیگر هیچ‌وقت راه‌اندازی نمی‌شود؛ یعنی زیرمنوهای منوی هدر تا آخر همان بازدید باز نمی‌شوند و قابلیت‌های دیگر المنتور پرو هم کار نمی‌کنند. در تست با تأخیر ۳ ثانیه‌ای فقط یک فایل (smartmenus یا frontend المنتور پرو) منوی هدر اصلاً فعال نشد. این ماژول اعلام آماده بودن jQuery را تا رویداد DOMContentLoaded (یعنی بعد از اجرای همه فایل‌ها) نگه می‌دارد؛ این همان رفتار پیش‌فرض وردپرس بدون defer است. فایل‌ها و ترتیب دانلود تغییری نمی‌کند.';
	}

	public function fields() {
		return array(
			'scope' => array(
				'type'    => 'text',
				'label'   => 'محدوده اجرا',
				'default' => 'all',
			),
		);
	}

	public function boot() {
		add_action( 'wp_head', array( $this, 'print_script' ), 0 );
	}

	public function print_script() {
		if ( ! Tarh99_Perf_Env::front_ok() || ! Tarh99_Perf_Env::scope_matches( (string) $this->opt( 'scope' ) ) ) {
			return;
		}
		echo '<script id="tarh99-jquery-ready">(function(w,d){var J="jQ"+"uery",done=false,held=null;if(w[J]||!Object.defineProperty){return;}function rel(){done=true;if(held){var h=held;held=null;try{h.holdReady(false);}catch(e){}}}d.addEventListener("DOMContentLoaded",rel);w.addEventListener("load",rel);try{Object.defineProperty(w,J,{configurable:true,enumerable:true,get:function(){return void 0;},set:function(v){Object.defineProperty(w,J,{configurable:true,enumerable:true,writable:true,value:v});if(!done&&v&&typeof v.holdReady==="function"&&d.readyState!=="loading"){try{v.holdReady(true);held=v;}catch(e){}}}});}catch(e){}})(window,document);</script>' . "\n";
	}
}
