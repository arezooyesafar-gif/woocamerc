<?php
define('HEADER_OPTS', 'pooshe_theme_settings_header');
define('PAGE_OPTS', 'pooshe_theme_settings_page');
define('ARCHIVE_OPTS', 'pooshe_theme_settings_archive');
define('BLOG_OPTS', 'pooshe_theme_settings_blog');
define('SHOP_OPTS', 'pooshe_theme_settings_shop');
define('PRODUCT_OPTS', 'pooshe_theme_settings_product');
define('FOOTER_OPTS', 'pooshe_theme_settings_footer');
define('STYLE_OPTS', 'pooshe_theme_settings_style');
define('SMS_OPTS', 'pooshe_theme_settings_sms');
define('ACCOUNT_OPTS', 'pooshe_theme_settings_account');
function pooshe_customize_register($wp_customize)
{

    $wp_customize->add_panel('pooshe_theme_settings', [
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'title' => 'تنظیمات قالب',
        'description' => 'تنظیمات و سفارشی سازی های قالب پوشه',
    ]);
    $wp_customize->add_section('pooshe_section_header', [
        'title' => 'سربرگ',
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'panel' => 'pooshe_theme_settings',
    ]);
    $wp_customize->add_section('pooshe_section_archive', [
        'title' => 'آرشیو وبلاگ',
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'panel' => 'pooshe_theme_settings',
    ]);
    $wp_customize->add_section('pooshe_section_page', [
        'title' => 'برگه',
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'panel' => 'pooshe_theme_settings',
    ]);
    $wp_customize->add_section('pooshe_section_blog', [
        'title' => 'وبلاگ',
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'panel' => 'pooshe_theme_settings',
    ]);
    $wp_customize->add_section('pooshe_section_shop', [
        'title' => 'فروشگاه',
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'panel' => 'pooshe_theme_settings',
    ]);
    $wp_customize->add_section('pooshe_section_product', [
        'title' => 'صفحه محصول',
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'panel' => 'pooshe_theme_settings',
    ]);
    $wp_customize->add_section('pooshe_section_footer', [
        'title' => 'پابرگ',
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'panel' => 'pooshe_theme_settings',
    ]);
    $wp_customize->add_section('pooshe_section_style', [
        'title' => 'استایل و رنگ',
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'panel' => 'pooshe_theme_settings',
    ]);
    $wp_customize->add_section('pooshe_section_sms', [
        'title' => 'پنل پیامکی',
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'panel' => 'pooshe_theme_settings',
    ]);
    $wp_customize->add_section('pooshe_section_account', [
        'title' => 'حساب کاربری',
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'panel' => 'pooshe_theme_settings',
    ]);

    $wp_customize->add_setting(ACCOUNT_OPTS . '[user_avatar]', [
        'default' => true,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('user-avatar', [
        'settings' => ACCOUNT_OPTS . '[user_avatar]',
        'label' => 'انتخاب تصویر کاربری (آواتار)',
        'section' => 'pooshe_section_account',
        'description' => 'امکان انتخاب آیکون به عنوان تصویر کاربری در صفحه ویرایش حساب کاربری',
        'type' => 'checkbox',
    ]);

    /*-------------------------------- blog settings -----------------------*/
    $wp_customize->add_setting(BLOG_OPTS . '[columns]', [
        'default' => 'left',
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('blog-columns', [
        'settings' => BLOG_OPTS . '[columns]',
        'label' => 'ستون بندی',
        'section' => 'pooshe_section_blog',
        'type' => 'select',
        'choices' => [
            'right' => 'با ستون سمت راست',
            'left' => 'با ستون سمت چپ',
            'full' => 'بدون ستون',
        ],
    ]);
    $wp_customize->add_setting(BLOG_OPTS . '[style]', [
        'default' => 'grid',
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('blog-style', [
        'settings' => BLOG_OPTS . '[style]',
        'label' => 'طرح کارت محصول',
        'section' => 'pooshe_section_blog',
        'type' => 'select',
        'choices' => [
            'list' => 'لیست',
            'grid' => 'گرید',
        ],
    ]);
    $wp_customize->add_setting(BLOG_OPTS . '[comment]', [
        'default' => 'advanced',
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('blog-comment', [
        'settings' => BLOG_OPTS . '[comment]',
        'label' => 'نوع دیدگاه',
        'section' => 'pooshe_section_blog',
        'type' => 'select',
        'choices' => [
            'advanced' => 'پیشرفته',
            'simple' => 'ساده',
        ],
    ]);

    /*-------------------------------- page settings -----------------------*/
    $wp_customize->add_setting(PAGE_OPTS . '[columns]', [
        'default' => 'left',
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('page-columns', [
        'settings' => PAGE_OPTS . '[columns]',
        'label' => 'ستون بندی',
        'section' => 'pooshe_section_page',
        'type' => 'select',
        'choices' => [
            'right' => 'با ستون سمت راست',
            'left' => 'با ستون سمت چپ',
            'full' => 'بدون ستون',
        ],
    ]);

    /*-------------------------------- archive settings -----------------------*/
    $wp_customize->add_setting(ARCHIVE_OPTS . '[columns]', [
        'default' => 'left',
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('archive-columns', [
        'settings' => ARCHIVE_OPTS . '[columns]',
        'label' => 'ستون بندی',
        'section' => 'pooshe_section_archive',
        'type' => 'select',
        'choices' => [
            'right' => 'با ستون سمت راست',
            'left' => 'با ستون سمت چپ',
            'full' => 'بدون ستون',
        ],
    ]);

    /*-------------------------------- shop settings -----------------------*/
    $wp_customize->add_setting(SHOP_OPTS . '[cards]', [
        'default' => 'grid',
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_setting(SHOP_OPTS . '[page]', [
        'default' => '0',
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('shop-cards', [
        'settings' => SHOP_OPTS . '[cards]',
        'label' => 'طرح پیشفرض کارت ها',
        'section' => 'pooshe_section_shop',
        'type' => 'select',
        'choices' => [
            'list' => 'لیست',
            'grid' => 'گرید',
        ],
    ]);
    $wp_customize->add_setting(SHOP_OPTS . '[card-rate]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-card-rate', [
        'settings' => SHOP_OPTS . '[card-rate]',
        'label' => 'امتیاز',
        'section' => 'pooshe_section_shop',
        'description' => 'وضعیت نمایش',
        'type' => 'checkbox',
    ]);
    $wp_customize->add_setting(SHOP_OPTS . '[card-cats]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-card-cats', [
        'settings' => SHOP_OPTS . '[card-cats]',
        'label' => 'دسته بندی',
        'section' => 'pooshe_section_shop',
        'description' => 'وضعیت نمایش',
        'type' => 'checkbox',
    ]);
    $wp_customize->add_setting(SHOP_OPTS . '[card-add]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-card-add', [
        'settings' => SHOP_OPTS . '[card-add]',
        'label' => 'دکمه افزودن به سبد خرید',
        'section' => 'pooshe_section_shop',
        'description' => 'وضعیت نمایش',
        'type' => 'checkbox',
    ]);
    $wp_customize->add_setting(SHOP_OPTS . '[card-bookmark]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-card-bookmark', [
        'settings' => SHOP_OPTS . '[card-bookmark]',
        'label' => 'دکمه افزودن به علاقه مندی ها',
        'section' => 'pooshe_section_shop',
        'description' => 'وضعیت نمایش',
        'type' => 'checkbox',
    ]);
    $wp_customize->add_setting(SHOP_OPTS . '[card-preview]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-card-preview', [
        'settings' => SHOP_OPTS . '[card-preview]',
        'label' => 'دکمه پیش نمایش',
        'section' => 'pooshe_section_shop',
        'description' => 'وضعیت نمایش',
        'type' => 'checkbox',
    ]);
    $wp_customize->add_setting(SHOP_OPTS . '[card-price]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-card-price', [
        'settings' => SHOP_OPTS . '[card-price]',
        'label' => 'قیمت محصول',
        'section' => 'pooshe_section_shop',
        'description' => 'وضعیت نمایش',
        'type' => 'checkbox',
    ]);


    $wp_customize->add_control('shop-redirect', [
        'settings' => SHOP_OPTS . '[page]',
        'label' => 'برگه انتقال کننده به دمو',
        'section' => 'pooshe_section_shop',
        'type' => 'dropdown-pages'
    ]);
    $wp_customize->add_setting(SHOP_OPTS . '[checkout-fields]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-checkout-fields', [
        'settings' => SHOP_OPTS . '[checkout-fields]',
        'label' => 'تسویه حساب ساده',
        'section' => 'pooshe_section_shop',
        'description' => 'حالت تسویه حساب ساده',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(SHOP_OPTS . '[comment]', [
        'default' => 'advanced',
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('shop-comment', [
        'settings' => SHOP_OPTS . '[comment]',
        'label' => 'نوع دیدگاه',
        'section' => 'pooshe_section_shop',
        'type' => 'select',
        'choices' => [
            'advanced' => 'پیشرفته',
            'simple' => 'ساده',
        ],
    ]);

    /*-------------------------------- style settings -----------------------*/
    $wp_customize->add_setting(STYLE_OPTS . '[main_color]', [
        'default' => '#ff9e22',
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'colors-main', [
        'label' => 'رنگ اصلی',
        'section' => 'pooshe_section_style',
        'settings' => STYLE_OPTS . '[main_color]'
    ]));
    $wp_customize->add_setting(STYLE_OPTS . '[main_btn]', [
        'default' => '#ff9e22',
        'capability' => 'edit_theme_options',

    ]);


    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'colors-btn', [
        'label' => 'رنگ دکمه ها',
        'section' => 'pooshe_section_style',
        'settings' => STYLE_OPTS . '[main_btn]'
    ]));
    $wp_customize->add_setting(STYLE_OPTS . '[main_btn_hover]', [
        'default' => '#ff8d00',
        'capability' => 'edit_theme_options',

    ]);


    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'colors-btn-hover', [
        'label' => 'رنگ دکمه ها حالت اشاره گر',
        'section' => 'pooshe_section_style',
        'settings' => STYLE_OPTS . '[main_btn_hover]'
    ]));
    $wp_customize->add_setting(STYLE_OPTS . '[container_size]', [
        'default' => 1470,
        'capability' => 'edit_theme_options',

    ]);

    $wp_customize->add_control('container_size', [
        'settings' => STYLE_OPTS . '[container_size]',
        'label' => 'اندازه کادر صفحه',
        'section' => 'pooshe_section_style',
        'description' => 'بر اساس پیکسل PX',
        'type' => 'number',
    ]);


    $wp_customize->add_setting(STYLE_OPTS . '[dark_mode]', [
        'default' => true,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('dark-mode-toggle', [
        'settings' => STYLE_OPTS . '[dark_mode]',
        'label' => 'حالت تاریک (دارک مود)',
        'section' => 'pooshe_section_style',
        'description' => 'فعال یا غیرفعال کردن حالت تاریک سایت',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[download_subscription]', [
        'default' => false,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('download-subscription-toggle', [
        'settings' => STYLE_OPTS . '[download_subscription]',
        'label' => 'فروش اشتراک با سقف دانلود',
        'section' => 'pooshe_section_style',
        'description' => 'فعال کردن سیستم فروش بسته‌های دانلود با تعداد مشخص',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[download_library]', [
        'default' => false,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('download-library-toggle', [
        'settings' => STYLE_OPTS . '[download_library]',
        'label' => 'کتابخانه دانلود اختصاصی',
        'section' => 'pooshe_section_style',
        'description' => 'فعال کردن کتابخانه دانلود با نمایش جزئیات فایل، نسخه، حجم و تاریخ',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[hide_woo_downloads]', [
        'default' => false,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('hide-woo-downloads-toggle', [
        'settings' => STYLE_OPTS . '[hide_woo_downloads]',
        'label' => 'مخفی کردن بخش دانلودهای ووکامرس',
        'section' => 'pooshe_section_style',
        'description' => 'بخش پیش‌فرض "دانلودها" را از حساب کاربری حذف می‌کند (در صورت استفاده از کتابخانه اختصاصی)',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[tiered_discount]', [
        'default' => false,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('tiered-discount-toggle', [
        'settings' => STYLE_OPTS . '[tiered_discount]',
        'label' => 'تخفیف پله‌ای خودکار',
        'section' => 'pooshe_section_style',
        'description' => 'به ازای تعداد محصول در سبد خرید تخفیف درصدی اعمال می‌کند',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[tiered_discount_rules]', [
        'default' => "2:5\n3:10\n5:15",
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('tiered-discount-rules', [
        'settings' => STYLE_OPTS . '[tiered_discount_rules]',
        'label' => 'قوانین تخفیف پله‌ای',
        'section' => 'pooshe_section_style',
        'description' => 'هر خط: تعداد محصول : درصد تخفیف (پیش‌فرض: 5:2 و 10:3 و 15:5)',
        'type' => 'textarea',
        'input_attrs' => ['dir' => 'ltr'],
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[watermark_enabled]', [
        'default' => false,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('watermark-enabled-toggle', [
        'settings' => STYLE_OPTS . '[watermark_enabled]',
        'label' => 'واترمارک هوشمند روی فایل‌های دانلودی',
        'section' => 'pooshe_section_style',
        'description' => 'روی تصاویر قابل دانلود واترمارک شامل نام خریدار و شماره سفارش اعمال کند',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[watermark_text]', [
        'default' => 'خریدار: {name} - سفارش: {order}',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('watermark-text', [
        'settings' => STYLE_OPTS . '[watermark_text]',
        'label' => 'متن واترمارک',
        'section' => 'pooshe_section_style',
        'description' => 'متغیرها: {name} {email} {order} {date}',
        'type' => 'text',
        'input_attrs' => ['dir' => 'ltr'],
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[watermark_opacity]', [
        'default' => '15',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('watermark-opacity', [
        'settings' => STYLE_OPTS . '[watermark_opacity]',
        'label' => 'شفافیت واترمارک (درصد)',
        'section' => 'pooshe_section_style',
        'description' => 'مقدار ۱ تا ۱۰۰ (پیش‌فرض ۱۵)',
        'type' => 'number',
        'input_attrs' => ['min' => 1, 'max' => 100, 'step' => 1],
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[affiliate_enabled]', [
        'default' => false,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('affiliate-enabled-toggle', [
        'settings' => STYLE_OPTS . '[affiliate_enabled]',
        'label' => 'سیستم همکاری در فروش',
        'section' => 'pooshe_section_style',
        'description' => 'کاربران با لینک اختصاصی خود سایرین را معرفی کرده و کمیسیون دریافت کنند',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[affiliate_commission_type]', [
        'default' => 'percentage',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('affiliate-commission-type', [
        'settings' => STYLE_OPTS . '[affiliate_commission_type]',
        'label' => 'نوع کمیسیون',
        'section' => 'pooshe_section_style',
        'type' => 'select',
        'choices' => [
            'percentage' => 'درصدی از مبلغ سفارش',
            'fixed' => 'مبلغ ثابت به ازای هر سفارش',
        ],
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[affiliate_commission_value]', [
        'default' => '10',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('affiliate-commission-value', [
        'settings' => STYLE_OPTS . '[affiliate_commission_value]',
        'label' => 'مقدار کمیسیون',
        'section' => 'pooshe_section_style',
        'description' => 'اگر درصدی: عدد بدون علامت درصد (مثلاً ۱۰ یعنی ۱۰٪). اگر ثابت: مبلغ به تومان',
        'type' => 'number',
        'input_attrs' => ['min' => 0, 'step' => '0.1'],
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[affiliate_cookie_days]', [
        'default' => '30',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('affiliate-cookie-days', [
        'settings' => STYLE_OPTS . '[affiliate_cookie_days]',
        'label' => 'مدت اعتبار کوکی (روز)',
        'section' => 'pooshe_section_style',
        'description' => 'چند روز پس از کلیک روی لینک، فروش به معرف تعلق می‌گیرد',
        'type' => 'number',
        'input_attrs' => ['min' => 1, 'max' => 365],
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[affiliate_min_order]', [
        'default' => '0',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('affiliate-min-order', [
        'settings' => STYLE_OPTS . '[affiliate_min_order]',
        'label' => 'حداقل مبلغ سفارش',
        'section' => 'pooshe_section_style',
        'description' => 'حداقل مبلغ سفارش برای تعلق کمیسیون (صفر = بدون محدودیت)',
        'type' => 'number',
        'input_attrs' => ['min' => 0, 'step' => 1000],
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[affiliate_menu_label]', [
        'default' => '',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('affiliate-menu-label', [
        'settings' => STYLE_OPTS . '[affiliate_menu_label]',
        'label' => 'عنوان منوی همکاری در فروش',
        'section' => 'pooshe_section_style',
        'description' => 'متن نمایشی در حساب کاربری',
        'type' => 'text',
        'input_attrs' => ['dir' => 'rtl'],
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[ajax_add_to_cart]', [
        'default' => true,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('ajax-add-to-cart-toggle', [
        'settings' => STYLE_OPTS . '[ajax_add_to_cart]',
        'label' => 'افزودن به سبد خرید با AJAX',
        'section' => 'pooshe_section_style',
        'description' => 'در صفحه محصول، افزودن به سبد خرید بدون رفرش صفحه انجام شود',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[post_views_counter]', [
        'default' => true,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('post-views-counter-toggle', [
        'settings' => STYLE_OPTS . '[post_views_counter]',
        'label' => 'آمار بازدید مطالب',
        'section' => 'pooshe_section_style',
        'description' => 'فعال یا غیرفعال کردن شمارش بازدید مطالب و محصولات',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(STYLE_OPTS . '[font]', [
        'default' => '',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('font', [
        'settings' => STYLE_OPTS . '[font]',
        'section' => 'pooshe_section_style',
        'label' => 'فونت سایت',
        'type' => 'select',
        'choices' => array(
            'yekan' =>  'ایران یکان',
            'iransans' =>  'ایران سانس',
            'kalameh' =>  'کلمه',
            'yekanbakh' =>  'یکان باخ',
        )
    ]);


    /*-------------------------------- product settings -----------------------*/

    $wp_customize->add_setting(PRODUCT_OPTS . '[product_layout]', [
        'default' => 'v1',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('product_layout', [
        'settings' => PRODUCT_OPTS . '[product_layout]',
        'label' => 'چیدمان صفحه محصول',
        'section' => 'pooshe_section_product',
        'type' => 'radio',
        'choices' => [
            'v1' => 'نسخه ۱ (پیش‌فرض)',
            'v2' => 'نسخه ۲ (مینیمال، بدون سایدبار)',
        ],
    ]);

    $wp_customize->add_setting(PRODUCT_OPTS . '[easy-download]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-download', [
        'settings' => PRODUCT_OPTS . '[easy-download]',
        'label' => 'دانلود سریع محصول',
        'section' => 'pooshe_section_product',
        'description' => 'امکان دانلود سریع محصولات خریداری شده در صفحه خود محصول',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(PRODUCT_OPTS . '[product-bookmark]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-bookmark', [
        'settings' => PRODUCT_OPTS . '[product-bookmark]',
        'label' => 'افزودن به علاقه مندی های محصول',
        'section' => 'pooshe_section_product',
        'description' => 'امکان افزودن به علاقه مندی های محصول',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(PRODUCT_OPTS . '[product-share]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-share', [
        'settings' => PRODUCT_OPTS . '[product-share]',
        'label' => 'اشتراک گذاری محصول',
        'section' => 'pooshe_section_product',
        'description' => 'امکان اشتراک گذاری محصول',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(PRODUCT_OPTS . '[product-link]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-link', [
        'settings' => PRODUCT_OPTS . '[product-link]',
        'label' => 'لینک کوتاه محصول',
        'section' => 'pooshe_section_product',
        'description' => 'لینک کوتاه محصول',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(PRODUCT_OPTS . '[product-sale]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-sale', [
        'settings' => PRODUCT_OPTS . '[product-sale]',
        'label' => 'تعداد فروش محصول',
        'section' => 'pooshe_section_product',
        'description' => 'نمایش تعداد فروش محصول',
        'type' => 'checkbox',
    ]);
    $wp_customize->add_setting(PRODUCT_OPTS . '[product-comment]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-comment', [
        'settings' => PRODUCT_OPTS . '[product-comment]',
        'label' => 'دیدگاههای محصول',
        'section' => 'pooshe_section_product',
        'description' => 'نمایش دیدگاه های محصول',
        'type' => 'checkbox',
    ]);
    $wp_customize->add_setting(PRODUCT_OPTS . '[product-view]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-view', [
        'settings' => PRODUCT_OPTS . '[product-view]',
        'label' => 'بازدیدهای محصول',
        'section' => 'pooshe_section_product',
        'description' => 'نمایش بازدیدهای محصول',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(PRODUCT_OPTS . '[product-comment-rate]', [
        'default' => true,
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-comment-rate', [
        'settings' => PRODUCT_OPTS . '[product-comment-rate]',
        'label' => 'نقاط قوت و ضعف نظرات',
        'section' => 'pooshe_section_product',
        'description' => 'وضعیت نمایش نقاط قوت و ضعف',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(PRODUCT_OPTS . '[product-comment]', [
        'default' => 'ویدیوی محصول',
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('product-comment', [
        'settings' => PRODUCT_OPTS . '[product-comment]',
        'label' => 'متن ویدیوی محصول',
        'section' => 'pooshe_section_product',
        'description' => '',
        'type' => 'text',
    ]);


    /*-------------------------------- sms settings -----------------------*/

    $wp_customize->add_setting(SMS_OPTS . '[sms_gateway]', [
        'default' => '',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('sms_gateway', [
        'settings' => SMS_OPTS . '[sms_gateway]',
        'section' => 'pooshe_section_sms',
        'label' => 'پنل پیامکی',
        'type' => 'select',
        'choices' => array_merge(array(
            '' => 'غیرفعال',
        ), Pooshe_SMS_Gateways::get_tested_sms_gateway())
    ]);

    $wp_customize->add_setting(SMS_OPTS . '[sms_gateway_username]', [
        'default' => '',
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('sms_gateway_username', [
        'settings' => SMS_OPTS . '[sms_gateway_username]',
        'section' => 'pooshe_section_sms',
        'label' => 'نام کاربری پنل پیامکی',
        'description' => 'در صورتی که کلید API دارید در این قسمت قرار دهید.',
        'type' => 'text',
    ]);

    $wp_customize->add_setting(SMS_OPTS . '[sms_gateway_password]', [
        'default' => '',
        'capability' => 'edit_theme_options',

    ]);
    $wp_customize->add_control('sms_gateway_password', [
        'settings' => SMS_OPTS . '[sms_gateway_password]',
        'section' => 'pooshe_section_sms',
        'label' => 'رمز عبور پنل پیامکی',
        'description' => 'اگر از کلید API استفاده می کنید نیازی به پر کردن این قسمت نیست.',
        'type' => 'text',
    ]);

    $wp_customize->add_setting(SMS_OPTS . '[sms_gateway_sender]', [
        'default' => '',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('sms_gateway_sender', [
        'settings' => SMS_OPTS . '[sms_gateway_sender]',
        'section' => 'pooshe_section_sms',
        'label' => 'شماره ارسالی',
        'type' => 'text',
    ]);

    $wp_customize->add_setting(SMS_OPTS . '[sms_gateways_pattern]', [
        'default' => '',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('sms_gateways_pattern', [
        'settings' => SMS_OPTS . '[sms_gateways_pattern]',
        'section' => 'pooshe_section_sms',
        'label' => 'کد پترن',
        'type' => 'text',
    ]);

    /*----------------------- order sms notification ----------------------*/

    $wp_customize->add_setting(SMS_OPTS . '[order_sms_enabled]', [
        'default' => false,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('order_sms_enabled', [
        'settings' => SMS_OPTS . '[order_sms_enabled]',
        'section' => 'pooshe_section_sms',
        'label' => 'پیامک اطلاع رسانی سفارش',
        'description' => 'ارسال پیامک به مدیر و مشتری هنگام ثبت و تغییر وضعیت سفارش',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(SMS_OPTS . '[order_sms_admin_mobile]', [
        'default' => '',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('order_sms_admin_mobile', [
        'settings' => SMS_OPTS . '[order_sms_admin_mobile]',
        'section' => 'pooshe_section_sms',
        'label' => 'شماره موبایل مدیر',
        'description' => 'موبایل دریافت اطلاع رسانی سفارش (چند شماره با کاما جدا کنید)',
        'type' => 'text',
    ]);

    $wp_customize->add_setting(SMS_OPTS . '[order_sms_admin_new_order]', [
        'default' => false,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('order_sms_admin_new_order', [
        'settings' => SMS_OPTS . '[order_sms_admin_new_order]',
        'section' => 'pooshe_section_sms',
        'label' => 'اعلان سفارش جدید به مدیر',
        'description' => 'هنگام ثبت سفارش جدید به شماره مدیر پیامک ارسال شود',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(SMS_OPTS . '[order_sms_customer_completed]', [
        'default' => false,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('order_sms_customer_completed', [
        'settings' => SMS_OPTS . '[order_sms_customer_completed]',
        'section' => 'pooshe_section_sms',
        'label' => 'اعلان تکمیل سفارش به مشتری',
        'description' => 'هنگام تغییر وضعیت سفارش به "تکمیل شده" به مشتری پیامک ارسال شود',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting(SMS_OPTS . '[order_sms_admin_template]', [
        'default' => '',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('order_sms_admin_template', [
        'settings' => SMS_OPTS . '[order_sms_admin_template]',
        'section' => 'pooshe_section_sms',
        'label' => 'قالب پیامک مدیر',
        'description' => 'متغیرها: {order_id} {total} {customer_name} {customer_mobile} {items_count} {payment_method}',
        'type' => 'textarea',
        'input_attrs' => ['dir' => 'rtl'],
    ]);

    $wp_customize->add_setting(SMS_OPTS . '[order_sms_customer_template]', [
        'default' => '',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('order_sms_customer_template', [
        'settings' => SMS_OPTS . '[order_sms_customer_template]',
        'section' => 'pooshe_section_sms',
        'label' => 'قالب پیامک مشتری',
        'description' => 'متغیرها: {order_id} {total} {status}',
        'type' => 'textarea',
        'input_attrs' => ['dir' => 'rtl'],
    ]);

}

add_action('customize_register', 'pooshe_customize_register');

add_action('customize_controls_print_footer_scripts', 'pooshe_order_sms_conditional_controls');
function pooshe_order_sms_conditional_controls()
{
    $dependent = [
        'order_sms_admin_mobile',
        'order_sms_admin_new_order',
        'order_sms_customer_completed',
        'order_sms_admin_template',
        'order_sms_customer_template',
    ];
    $setting = SMS_OPTS . '[order_sms_enabled]';
?>
    <script>
        jQuery(function($) {
            var ids = <?php echo json_encode($dependent); ?>;
            wp.customize('<?php echo esc_js($setting); ?>', function(settings) {
                function toggle() {
                    var show = !!settings.get();
                    $.each(ids, function(i, id) {
                        var ctrl = wp.customize.control(id);
                        if (ctrl) ctrl.active.set(show);
                    });
                }
                settings.bind(toggle);
                toggle();
            });
        });
    </script>
<?php
}

add_action('customize_controls_print_footer_scripts', 'pooshe_post_views_conditional_controls');
function pooshe_post_views_conditional_controls() {
    $dependent = [
        'post-views-counter-toggle',
    ];
    $setting = STYLE_OPTS . '[post_views_counter]';
?>
    <script>
        jQuery(function($) {
            var ids = <?php echo json_encode($dependent); ?>;
            wp.customize('<?php echo esc_js($setting); ?>', function(settings) {
                function toggle() {
                    var show = !!settings.get();
                }
                settings.bind(toggle);
                toggle();
            });
        });
    </script>
<?php
}

add_action('customize_controls_print_footer_scripts', 'pooshe_affiliate_conditional_controls');
function pooshe_affiliate_conditional_controls()
{
    $dependent = [
        'affiliate-commission-type',
        'affiliate-commission-value',
        'affiliate-cookie-days',
        'affiliate-min-order',
        'affiliate-menu-label',
    ];
    $setting = STYLE_OPTS . '[affiliate_enabled]';
?>
    <script>
        jQuery(function($) {
            var ids = <?php echo json_encode($dependent); ?>;
            wp.customize('<?php echo esc_js($setting); ?>', function(settings) {
                function toggle() {
                    var show = !!settings.get();
                    $.each(ids, function(i, id) {
                        var ctrl = wp.customize.control(id);
                        if (ctrl) ctrl.active.set(show);
                    });
                }
                settings.bind(toggle);
                toggle();
            });
        });
    </script>
<?php
}


