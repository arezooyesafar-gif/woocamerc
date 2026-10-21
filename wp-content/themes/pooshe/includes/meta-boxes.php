<?php
if (is_admin()) {
    $prefix = 'pooshe_';
    $config = array(
        'id' => 'page_columns',
        'title' => 'ساختار برگه',
        'pages' => array('page'),
        'context' => 'side',
        'priority' => 'high',
        'fields' => array(),
        'local_images' => true,
        'use_with_theme' => get_stylesheet_directory_uri() . '/includes/vendor/meta-box-class'
    );

    $my_meta = new AT_Meta_Box($config);
    $my_meta->addSelect($prefix . 'page_column',
        array(
            'default' => 'پیش فرض تنظیمات قالب',
            'right' => 'با ستون سمت راست',
            'left' => 'با ستون سمت چپ',
            'full' => 'بدون ستون',
        ),
        array(
            'name' => 'ستون بندی صفحه',
            'std' => array('default')
        )
    );

    $my_meta->Finish();

    $config_styles = array(
        'id' => 'page_styles',
        'title' => 'استایل و رنگ ها',
        'pages' => array('page'),
        'context' => 'side',
        'priority' => 'high',
        'fields' => array(),
        'local_images' => true,
        'use_with_theme' => get_stylesheet_directory_uri() . '/includes/vendor/meta-box-class'
    );

    $my_meta = new AT_Meta_Box($config_styles);
    $my_meta->addSelect($prefix . 'page_style_active',
        array(
            'default' => 'پیش فرض تنظیمات قالب',
            'custom' => 'سفارشی',
        ),
        array(
            'name' => 'رنگ بندی صفحه',
            'std' => array('default')
        )
    );
    $my_meta->addColor($prefix.'page_style_color_main',
        array(
            'name'  =>  'رنگ اصلی',
            'std'   =>  '#ff9e22'
        )
    );
    $my_meta->addColor($prefix.'page_style_color_btn',
        array(
            'name'  =>  'رنگ دکمه ها',
            'std'   =>  '#ff9e22'
        )
    );
    $my_meta->addColor($prefix.'page_style_color_btn_hover',
        array(
            'name'  =>  'رنگ دکمه ها حالت اشاره گر',
            'std'   =>  '#ff9e22'
        )
    );
    $my_meta->Finish();

    $config2 = array(
        'id' => 'product_info',
        'title' => 'اطلاعات اضافی',
        'pages' => array('product'),
        'context' => 'side',
        'priority' => 'high',
        'fields' => array(),
        'local_images' => true,
        'use_with_theme' => get_stylesheet_directory_uri() . '/includes/vendor/meta-box-class'
    );
    $my_meta2 = new AT_Meta_Box($config2);
    $my_meta2->addImage($prefix.'product_logo',array('name'=> 'لوگو محصول'));
    $my_meta2->addText($prefix.'product_subtitle',array('name'=> 'توضیح عنوان'));
    $my_meta2->addText($prefix.'product_preview',array('name'=> 'آدرس پیش نمایش'));

    $my_meta2->Finish();

    $shipping_time = array(
        'id' => 'product_delivery_time',
        'title' => ' زمان تحویل',
        'pages' => array('product'),
        'context' => 'side',
        'priority' => 'high',
        'default' => '0',
        'fields' => array(),
        'use_with_theme' => get_stylesheet_directory_uri() . '/includes/vendor/meta-box-class'
    );
    $time_meta = new AT_Meta_Box($shipping_time);
    $time_meta->addNumber($prefix.'product_delivery_time',array('name'=> 'زمان تحویل ( پیشفرض 1 روز میباشد و اگر صفر باشد آماده تحویل میشود)'));
    $time_meta->Finish();

    $shipping_time = array(
        'id' => 'product_slogan',
        'title' => 'شعار محصول',
        'pages' => array('product'),
        'context' => 'side',
        'default' => '0',
        'fields' => array(),
        'use_with_theme' => get_stylesheet_directory_uri() . '/includes/vendor/meta-box-class'
    );
    $time_meta = new AT_Meta_Box($shipping_time);
    $time_meta->addTextarea($prefix.'product_slogan',array('name'=> 'تنها در کارت محصول 4 قابل نمایش هست'));
    $time_meta->Finish();

    $config3 = array(
        'id' => 'product_attrs',
        'title' => 'ویژگی های محصول',
        'pages' => array('product'),
        'context' => 'normal',
        'priority' => 'high',
        'fields' => array(),
        'local_images' => true,
        'use_with_theme' => get_stylesheet_directory_uri() . '/includes/vendor/meta-box-class'
    );
    $my_meta3 = new AT_Meta_Box($config3);
    $my_meta3->addCheckbox($prefix.'new_version',array('name'=> 'نسخه جدید محصول'));
    $repeater_fields = [];
    $repeater_fields[] = $my_meta3->addText($prefix.'features_title',array('name'=> 'عنوان'),true);
    $repeater_fields[] = $my_meta3->addIcon($prefix . 'features_icon',
        pooshe_fontawesome_options(),
        array(
            'name' => 'آیکن'
        ),
        true
    );
    $repeater_fields[] = $my_meta3->addText($prefix.'features_value',array('name'=> 'مقدار'),true);
    /*
     * Then just add the fields to the repeater block
     */
    //repeater block
    $my_meta3->addRepeaterBlock($prefix.'attr_items',array(
        'inline'   => true,
        'name'     => 'اطلاعات',
        'fields'   => $repeater_fields,
        'sortable' => true
    ));

    $my_meta3->Finish();


	$config4 = array(
		'id' => 'product_videos',
		'title' => 'فیلم های محصول',
		'pages' => array('product'),
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(),
		'local_images' => true,
		'use_with_theme' => get_stylesheet_directory_uri() . '/includes/vendor/meta-box-class'
	);
	$my_meta4 = new AT_Meta_Box($config4);
	$repeater_fields = [];
	$repeater_fields[] = $my_meta4->addText($prefix.'video_title',array('name'=> 'عنوان'),true);
	$repeater_fields[] = $my_meta4->addFile($prefix.'video_file',array('name'=> 'فایل','desc'=>'فقط فرمت های mp4, m4v, webm, ogv, wmv, flv'),true);
	$repeater_fields[] = $my_meta4->addTextarea($prefix.'video_script',array('name'=> 'کد ویدئو','desc'=>'برای مثال: کد آپارات'),true);
	/*
	 * Then just add the fields to the repeater block
	 */
	//repeater block
	$my_meta4->addRepeaterBlock($prefix.'product_videos',array(
		'inline'   => true,
		'name'     => 'فیلم ها',
		'fields'   => $repeater_fields,
		'sortable' => true
	));

	$my_meta4->Finish();



	$config5 = array(
		'id' => 'product_free_files',
		'title' => 'فایل های رایگان',
		'pages' => array('product'),
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(),
		'local_images' => true,
		'use_with_theme' => get_stylesheet_directory_uri() . '/includes/vendor/meta-box-class'
	);
	$my_meta5 = new AT_Meta_Box($config5);
	$repeater_fields = [];
	$repeater_fields[] = $my_meta5->addText($prefix.'video_title',array('name'=> 'عنوان'),true);
	$repeater_fields[] = $my_meta5->addFile($prefix.'video_file',array('name'=> 'فایل'),true);
	/*
	 * Then just add the fields to the repeater block
	 */
	//repeater block
	$my_meta5->addRepeaterBlock($prefix.'product_files',array(
		'inline'   => true,
		'name'     => 'فایل های رایگان',
		'fields'   => $repeater_fields,
		'sortable' => true
	));
	$my_meta5->addCheckbox($prefix.'product_is_free',array('name'=> 'این محصول رایگان می باشد؟'));

	$my_meta5->Finish();
}
