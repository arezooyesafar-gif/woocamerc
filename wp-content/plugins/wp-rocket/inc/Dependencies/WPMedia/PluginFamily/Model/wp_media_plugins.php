<?php
/**
 * WP Media plugin family data.
 */

return [
	'optimize_performance' => [
		'title'   => __( 'بهینه سازی عملکرد', 'rocket' ),
		'plugins' => [
			'wp-rocket/wp-rocket' => [
				'logo'  => [
					'file'  => 'logo-wp-rocket.svg',
					'width' => '50%',
				],
				'title' => __( 'Speed Up Your Website, Instantly', 'rocket' ),
				'desc'  => __( 'WP Rocket is the easiest way to make your WordPress website faster and boost your Google PageSpeed score. Get more traffic, better engagement, and higher conversions effortlessly.', 'rocket' ),
				'link'  => '',
			],
			'imagify/imagify'     => [
				'logo'  => [
					'file'  => 'logo-imagify.svg',
					'width' => '50%',
				],
				'title' => __( 'سرعت وب سایت خود را با تصاویر سبک تر افزایش دهید', 'rocket' ),
				'desc'  => __( 'Imagify ساده ترین بهینه ساز تصویر وردپرس است. این به طور خودکار تصاویر را فشرده می کند، آنها را به فرمت های WebP و AVIF تبدیل می کند و به شما امکان می دهد تنها با یک کلیک اندازه و بهینه سازی کنید!', 'rocket' ),
				'link'  => 'https://imagify.io/',
			],
		],
	],
	'boost_traffic'        => [
		'title'   => __( 'افزایش ترافیک', 'rocket' ),
		'plugins' => [
			'seo-by-rank-math/rank-math' => [
				'logo'  => [
					'file'  => 'logo-rank-math.svg',
					'width' => '60%',
				],
				'title' => __( 'چاقوی ارتش سوئیس ابزارهای سئو', 'rocket' ),
				'desc'  => __( 'Rank Math SEO بهترین افزونه سئو وردپرس با ویژگی های بسیاری از ابزارهای SEO و AI SEO در یک بسته واحد است که به افزایش ترافیک سئوی شما کمک می کند.', 'rocket' ),
				'link'  => 'https://rankmath.com/wordpress/plugin/seo-suite/',
			],
		],
	],
	'protect_secure'       => [
		'title'   => __( 'محافظت و ایمن کردن', 'rocket' ),
		'plugins' => [
			'backwpup/backwpup'                   => [
				'logo'  => [
					'file'  => 'logo-backwpup.svg',
					'width' => '60%',
				],
				'title' => __( 'ساده ترین راه برای محافظت از وب سایت شما', 'rocket' ),
				'desc'  => __( 'BackWPup جامع ترین و کاربرپسندترین افزونه پشتیبان گیری و بازیابی برای وردپرس است. به راحتی پشتیبان گیری خودکار را برنامه ریزی کنید، به طور ایمن ذخیره کنید و تنها با چند کلیک بازیابی کنید!', 'rocket' ),
				'link'  => 'https://backwpup.com/',
			],
			'uk-cookie-consent/uk-cookie-consent' => [
				'logo'  => [
					'file'  => 'logo-termly.svg',
					'width' => '50%',
				],
				'title' => __( 'بنر رضایت کوکی GDPR/CCPA', 'rocket' ),
				'desc'  => __( 'یکی از آسان‌ترین، جامع‌ترین و محبوب‌ترین افزونه‌های رضایت کوکی موجود است. Google Gold Certified Partner برای پیروی سریع از قوانین حفظ حریم خصوصی داده ها از سراسر جهان.', 'rocket' ),
				'link'  => 'https://termly.io/resources/articles/wordpress-cookies-guide/',
			],
		],
	],
];
