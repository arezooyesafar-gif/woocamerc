<?php 

use Woosbymdz\Helpers\Main\main as help;

if (!function_exists('add_action'))
{
    echo '<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>';
    exit();
}
else if (!defined('ABSPATH'))
{
    echo '<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>';
    exit();
}
/**/

require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'main.php';

/**
 * WOOSBYMDZ_categoryslider_Widget
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
if(!class_exists('WOOSBYMDZ_categoryslider_Widget')) 
{
class WOOSBYMDZ_categoryslider_Widget extends \Elementor\Widget_Base 
{
  
	public function get_name() 
    {
		return 'mdz_cats_slider';
	}

	public function get_title() 
    {
		return esc_html__( 'Woo cats slider', 'wooslider-bymdez' );
	}

	public function get_icon() 
    {
		return 'woosbymdz-cion-catslider';
	}

	public function get_custom_help_url() 
    {

        if(WOOSBYMDZ_IS_WPHOOLU != 1){
            return 'https://www.rtl-theme.com/';
        }else return 'https://wphoolu.com/panel/?tab=tickets';
		
	}

	public function get_categories() 
    {
		
        $add_widgets_cat = get_option('woosbymdz_addnewwidgets_category');

        if($add_widgets_cat == '1'){

            return [ 'woosbymdz-widgets-category' ];

        }else return [ 'woocommerce-elements' ];
        
	}

	public function get_keywords() 
    {

		return [ 'category', 'carousel', 'slider', 'mdz', 'mdesign', 'cats', 'ووکامرس', 'اسلایدر دسته بندی', 'دسته بندی', 'اسلایدر مدرن', 'اسلایدر', 'کاروسل', 'کاروسل دسته بندی ها', 'دسته بندی ها', 'دسته بندی های ووکامرس' ];

	}

	/**
	 * Register list widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() 
    {

        /*GENERAL*/
		$this->start_controls_section(//tab_content
			'content_section',
			[
				'label' => esc_html__( 'General', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(//Just Refresh the frame
            'refresh_frame',
            [
                'label' => esc_html__('Update Frame', 'wooslider-bymdez'),
                'description' => esc_html__('If you are not able to see the live-view of this widget just toggle this', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control( //No-Editor-Render
            'dont_render',
            [
                'label' => esc_html__('Do not Render (on editor)', 'wooslider-bymdez'),
                'description' => esc_html__('This option stops the rendering of this slider when you are in the Elementor editor and does not show you any preview, using this option is useful for pages that have a lot of sliders and the design in them is slow and laggy.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
                'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );


        $this->add_control(//Images with no-lazy (Exclude images from Lazy-Load)
            'no_lazy',
            [
                'label' => esc_html__('Exclude form Lazy-Load (images)', 'wooslider-bymdez'),
                'description' => esc_html__('By enabling this option slider will add no-lazy CSS class to all the images inside it', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//no-lazy Custom css class
			'no_lazy_css_class', [
				'label' => esc_html__( 'No-Lazy CSS Class', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'description'   =>  esc_html__( 'For the best configuration with your optimization plugins, you are able to set the KEY CSS class to exclude all images inside the slider from Lazy-Load', 'wooslider-bymdez' ),
				'default' => esc_html__( 'no-lazy' , 'wooslider-bymdez' ),
				'label_block' => true,
                'condition' 	=> ['no_lazy' => 'yes'],
			]
		);

        $this->add_control(//Slides auto-height
            'auto_height',
            [
                'label' => esc_html__('Auto-Height Slides', 'wooslider-bymdez'),
                'description' => esc_html__('By activating this option, the height of all your slides will be the same and coordinated.Note: this option has no effect if the grid-slider is actived', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->end_controls_section();

        /*Slides*/
        $this->start_controls_section(//tab_slides
			'slides_section',
			[
				'label' => esc_html__( 'Slides', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(//slides icon or img?
			'slides_img_type',
			[
				'label' => esc_html__( 'Icon or image?', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'image'             => esc_html__('Media image', 'wooslider-bymdez'),
                    'elem_icons'        => esc_html__('Elementor icons', 'wooslider-bymdez'),
                    'custom_icons'      => esc_html__('Your icon', 'wooslider-bymdez'),
				],
                'default' => 'elem_icons'
			]
		);

        $repeater->add_control(//Responsive images
            'slides_resp_images',
            [
                'label' => esc_html__('Responsive Images', 'wooslider-bymdez'),
                'description' => esc_html__('By activating this option, you can show other images for other devices in the category slider, this does not work on icons.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'condition' 	=> ['slides_img_type' => 'image'],
            ]
        );

        $repeater->add_control(//custom icon img selector
			'slides_img_type_2',
			[
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => esc_html__( 'Choose your image or icon', 'wooslider-bymdez' ),
                'description'   =>  esc_html__( 'prefered formats: svg & webp', 'wooslider-bymdez' ),
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'custom_icons'
                        ],
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'image'
                        ]
                    ]
                ]
			]
		);

        $repeater->add_control(//wide monitors image
			'slides_img_type_2_wide',
			[
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => esc_html__( 'Choose your image for wide devices', 'wooslider-bymdez' ),
                'description'   =>  esc_html__( 'prefered formats: svg & webp', 'wooslider-bymdez' ),
				'default' => [
					'url' => 'woosbymdznone-url',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'slides_resp_images',
                            'operator' => '===',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'image'
                        ]
                    ]
                ]
			]
		);

        $repeater->add_control(//tablets image
			'slides_img_type_2_tab',
			[
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => esc_html__( 'Choose your image for tablet devices', 'wooslider-bymdez' ),
                'description'   =>  esc_html__( 'prefered formats: svg & webp', 'wooslider-bymdez' ),
				'default' => [
					'url' => 'woosbymdznone-url',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'slides_resp_images',
                            'operator' => '===',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'image'
                        ]
                    ]
                ]
			]
		);

        $repeater->add_control(//mobile image
			'slides_img_type_2_mob',
			[
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => esc_html__( 'Choose your image for mobile devices', 'wooslider-bymdez' ),
                'description'   =>  esc_html__( 'prefered formats: svg & webp', 'wooslider-bymdez' ),
				'default' => [
					'url' => 'woosbymdznone-url',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'slides_resp_images',
                            'operator' => '===',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'image'
                        ]
                    ]
                ]
			]
		);

        $repeater->add_control(//mobile2 image
			'slides_img_type_2_mob2',
			[
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => esc_html__( 'Choose your image for small-mobile devices', 'wooslider-bymdez' ),
                'description'   =>  esc_html__( 'prefered formats: svg & webp', 'wooslider-bymdez' ),
				'default' => [
					'url' => 'woosbymdznone-url',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'slides_resp_images',
                            'operator' => '===',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'image'
                        ]
                    ]
                ]
			]
		);

        $repeater->add_control(//custom img/icon width
			'slides_img_type_2_width',
			[
				'label' 	=>	esc_html__( 'Custom icon width', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                'description'   =>  esc_html__( 'In pixels (PX)', 'wooslider-bymdez' ),
				'default' 	=>	100,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'custom_icons'
                        ],
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'image'
                        ]
                    ]
                ]
			]
		);

        $repeater->add_control(//slides image alt
			'slides_img_type_2_alt', [
				'label' => esc_html__( 'Image Alt', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'description'   =>  esc_html__( 'For better SEO rating always define alt attribute for your images inside your web-site', 'wooslider-bymdez' ),
				'default' => esc_html__( 'image alt' , 'wooslider-bymdez' ),
				'label_block' => true,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'custom_icons'
                        ],
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'image'
                        ]
                    ]
                ]
			]
		);

        $repeater->add_control(//slides image width (attr)
			'slides_img_type_2_width_attr', [
				'label' => esc_html__( 'Image Width (Attribute)', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'description'   =>  esc_html__( 'For better SEO rating always define width attribute for your images inside your web-site', 'wooslider-bymdez' ),
				'default' => esc_html__( '' , 'wooslider-bymdez' ),
                'placeholder' => esc_html__('Enter number like: 300', 'wooslider-bymdez'),
				'label_block' => true,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'custom_icons'
                        ],
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'image'
                        ]
                    ]
                ]
			]
		);

        $repeater->add_control(//slides image height (attr)
			'slides_img_type_2_height', [
				'label' => esc_html__( 'Image Height (Attribute)', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'description'   =>  esc_html__( 'For better SEO rating always define height attribute for your images inside your web-site', 'wooslider-bymdez' ),
				'default' => esc_html__( '' , 'wooslider-bymdez' ),
                'placeholder' => esc_html__('Enter number like: 100', 'wooslider-bymdez'),
				'label_block' => true,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'custom_icons'
                        ],
                        [
                            'name' => 'slides_img_type',
                            'operator' => '===',
                            'value' => 'image'
                        ]
                    ]
                ]
			]
		);

        $repeater->add_control(//sefl elem icons selector
			'slides_img_type_1',
			[
				'label' => esc_html__( 'Choose icon', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::ICONS,
                'condition' 	=> ['slides_img_type' => 'elem_icons'],
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
			]
		);

        $repeater->add_control(//elem icons color
			'slides_img_type_1_color',
			[
				'label' => esc_html__( 'icon color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'condition' 	=> ['slides_img_type' => 'elem_icons'],
				'default' => '#000',
			]
		);

        $repeater->add_control( //title ?
            'slides_title_show',
            [
                'label' => esc_html__('Use title', 'wooslider-bymdez'),
                'description' => esc_html__('Adds title to your slides', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'yes',
            ]
        );

        $repeater->add_control(//slides title
			'slides_title', [
				'label' => esc_html__( 'Title', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'condition' 	=> ['slides_title_show' => 'yes'],
				'default' => esc_html__( 'cat or brand' , 'wooslider-bymdez' ),
				'label_block' => true,
			]
		);

        $repeater->add_control(//Title color
			'slides_title_color',
			[
				'label' => esc_html__( 'Title color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'condition' 	=> ['slides_title_show' => 'yes'],
                'default' => '#000',
			]
		);

        $repeater->add_control(//relative pos of the title and img or icon
			'slides_template',
			[
				'label' => esc_html__( 'slide template', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'imagetop'       => esc_html__('Image top', 'wooslider-bymdez'),
                    'imagedown'      => esc_html__('Image below', 'wooslider-bymdez'),
                    'imageright'     => esc_html__('Image right', 'wooslider-bymdez'),
                    'imageleft'      => esc_html__('Image left', 'wooslider-bymdez'),
                    'imagehover'     => esc_html__('Image hover', 'wooslider-bymdez'),
                    'titlehover'     => esc_html__('Title hover', 'wooslider-bymdez'),
                    'imagerotate'    => esc_html__('Image dance', 'wooslider-bymdez'),
                    'metro'          => esc_html__('Metro', 'wooslider-bymdez'),
				],
                'condition' 	=> ['slides_title_show' => 'yes'],
                'default' => 'imagetop',
			]
		);

        $repeater->add_control(//link
			'slides_url',
			[
				'label' => esc_html__( 'URL', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-site.com/category/test', 'wooslider-bymdez' ),
			]
		);

        $repeater->add_control( //Extra html?
            'slides_extrahtml',
            [
                'label' => esc_html__('Extra HTML after block', 'wooslider-bymdez'),
                'description' => esc_html__('By using this option you can add your own extra html code, after this block', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $repeater->add_control(//Extra html code
			'slides_extrahtml_html',
			[
				'label' => esc_html__( 'Enter your HTML Code', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Type here...', 'wooslider-bymdez' ),
				'placeholder' => esc_html__( 'write here!', 'wooslider-bymdez' ),
                'condition' 	=> ['slides_extrahtml' => 'yes'],
			]
		);

        $repeater->add_control(//extra css class
			'slides_extracss', [
				'label' => esc_html__( 'Extra CSS classes', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'description' => esc_html__('Seperate classes by |. Adds extra css classes to the swiper-slide of this slider block', 'wooslider-bymdez'),
                'placeholder' => esc_html__( 'class1|class2', 'wooslider-bymdez' ),
				'default' => '',
				'label_block' => true,
			]
		);


        $this->add_control(//the main repeater
			'slides_elements',
			[
				'label' => esc_html__( 'Slides', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'slides_title' => esc_html__( 'Category-1' , 'wooslider-bymdez' ),
                        'slides_img_type' => 'elem_icons',
                        'slides_img_type_1' => [
                            'value' => 'fas fa-heart',
                            'library' => 'solid',
                        ],
					],
                    [
						'slides_title' => esc_html__( 'Car kits' , 'wooslider-bymdez' ),
                        'slides_img_type' => 'elem_icons',
                        'slides_img_type_1' => [
                            'value' => 'fas fa-car',
                            'library' => 'solid',
                        ],
					],
                    [
						'slides_title' => esc_html__( 'Brand-1' , 'wooslider-bymdez' ),
                        'slides_img_type' => 'elem_icons',
                        'slides_img_type_1' => [
                            'value' => 'fab fa-sketch',
                            'library' => 'solid',
                        ],
					],
                    [
						'slides_title' => esc_html__( 'Samsung' , 'wooslider-bymdez' ),
                        'slides_img_type' => 'elem_icons',
                        'slides_img_type_1' => [
                            'value' => 'fas fa-mobile-alt',
                            'library' => 'solid',
                        ],
					],
                    [
						'slides_title' => esc_html__( 'Kiss Beauty' , 'wooslider-bymdez' ),
                        'slides_img_type' => 'elem_icons',
                        'slides_img_type_1' => [
                            'value' => 'fas fa-hand-holding-heart',
                            'library' => 'solid',
                        ],
					],
                    [
						'slides_title' => esc_html__( 'پارس خزر' , 'wooslider-bymdez' ),
                        'slides_img_type' => 'elem_icons',
                        'slides_img_type_1' => [
                            'value' => 'fas fa-holly-berry',
                            'library' => 'solid',
                        ],
					],
                    [
						'slides_title' => esc_html__( 'نوکیا' , 'wooslider-bymdez' ),
                        'slides_img_type' => 'elem_icons',
                        'slides_img_type_1' => [
                            'value' => 'fas fa-mobile',
                            'library' => 'solid',
                        ],
					],
                    [
						'slides_title' => esc_html__( 'لوازم پزشکی' , 'wooslider-bymdez' ),
                        'slides_img_type' => 'elem_icons',
                        'slides_img_type_1' => [
                            'value' => 'fas fa-briefcase-medical',
                            'library' => 'solid',
                        ],
					],
                    [
						'slides_title' => esc_html__( 'آرایشی/بهداشتی' , 'wooslider-bymdez' ),
                        'slides_img_type' => 'elem_icons',
                        'slides_img_type_1' => [
                            'value' => 'fas fa-air-freshener',
                            'library' => 'solid',
                        ],
					],
                    [
						'slides_title' => esc_html__( 'لوازم خانگی' , 'wooslider-bymdez' ),
                        'slides_img_type' => 'elem_icons',
                        'slides_img_type_1' => [
                            'value' => 'fas fa-cash-register',
                            'library' => 'solid',
                        ],
					],
				],
			]
		);

        $this->add_control( //images lazy loading
            'slides_img_type_2_lazy',
            [
                'label' => esc_html__('Load images Lazy', 'wooslider-bymdez'),
                'description' => esc_html__('If you dont use Lazy Load plugins for your images, can be useful', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );


        $this->end_controls_section();

        /*Swiper options*/
		$this->start_controls_section(//tab_content
			'swiper_section',
			[
				'label' => esc_html__( 'Slider options', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control( //show dots?
            'swiper_navs',
            [
                'label' => esc_html__('Use navs', 'wooslider-bymdez'),
                'description' => esc_html__('Shows or hide the slider navigation', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control( //show arrows?
            'swiper_arrows',
            [
                'label' => esc_html__('Use Arrows', 'wooslider-bymdez'),
                'description' => esc_html__('Shows or hide the slider Arrows', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'yes',
            ]
        );

        $this->add_control( //grid style?
            'swiper_grids',
            [
                'label' => esc_html__('Use Grids', 'wooslider-bymdez'),
                'description' => esc_html__('Shows slider in the grids style', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'yes',
            ]
        );

        $this->add_control( //Centered Slider?
            'swiper_centered',
            [
                'label' => esc_html__('Centered Slider?', 'wooslider-bymdez'),
                'description' => esc_html__('This option creates a central slider, but it may interfere with some features such as the 3D cube slider or grid-slider.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//swiper grid rows
			'swiper_grid_rows',
			[
				'label' 	=>	esc_html__( 'Grid rows number', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                'description'   =>  esc_html__( 'max = 4 rows', 'wooslider-bymdez' ),
				'default' 	=>	2,
                'condition' 	=> ['swiper_grids' => 'yes'],
                'min' => 2,
                'max' => 4,

			]
		);

        $this->add_control( //Loop?
            'swiper_loop',
            [
                'label' => esc_html__('Loop', 'wooslider-bymdez'),
                'description' => esc_html__('Activate the loop mode, Cant be used with grid at the same time', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            
            ]
        );

        $this->add_control( //Auto-Height
            'swiper_autoheight',
            [
                'label' => esc_html__('Auto Height', 'wooslider-bymdez'),
                'description' => esc_html__('Use this option if you have slides with different heights', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            
            ]
        );

        $this->add_control( //Autoplay?
            'swiper_autoplay',
            [
                'label' => esc_html__('Autoplay', 'wooslider-bymdez'),
                'description' => esc_html__('Turns on the autoplay for the slider => Note: In order to further optimize resources and prevent lag, autoplay is not displayed and applied in Elementor editor mode.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control( //Autoplay delay
			'swiper_autoplay_speed',
			[
				'label' 		=> esc_html__( 'Play speed', 'wooslider-bymdez' ),
				'type'			=> \Elementor\Controls_Manager::NUMBER,
				'description' 	=> esc_html__( 'in milli-seconds', 'wooslider-bymdez' ),
				'return_value' 	=> 'true',
				'default' 		=> 1775,
				'condition' 	=> ['swiper_autoplay' => 'yes'],
			]
		);

        $this->add_control(//Swiper move effects
			'swiper_move_effect',
			[
				'label' => esc_html__( 'Move effect', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'default'         => esc_html__('default', 'wooslider-bymdez'),
                    'easein'         => esc_html__('ease', 'wooslider-bymdez'),
                    'linar'           => esc_html__('normal', 'wooslider-bymdez'),
                    'stepend'        => esc_html__('no move', 'wooslider-bymdez'),
                    'stepstart'      => esc_html__('fast move', 'wooslider-bymdez'),
                    'funky'           => esc_html__('funky', 'wooslider-bymdez'),
                    'hammer'          => esc_html__('hammer', 'wooslider-bymdez'),
                    'steps'           => esc_html__('steps', 'wooslider-bymdez'),
                    'light'           => esc_html__('light speed', 'wooslider-bymdez'),
				],
                'default' => 'default',
			]
		);

        $this->add_control(//Swiper rotate effects
			'swiper_rotate_effect',
			[
				'label' => esc_html__( 'Rotation effect', 'wooslider-bymdez' ),
                'description' => esc_html__('Cube, fade, cards, overflow, rotate, creative1 to creative5 modes just have functionallity when you just show a single slide(desk,tablet,mob)', 'wooslider-bymdez'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'default'         => esc_html__('default', 'wooslider-bymdez'),
                    'fade'         => esc_html__('fade', 'wooslider-bymdez'),
                    'css'           => esc_html__('css mode', 'wooslider-bymdez'),
                    'coverflow'        => esc_html__('coverflow', 'wooslider-bymdez'),
                    'cards'        => esc_html__('cards', 'wooslider-bymdez'),
                    'cube'        => esc_html__('cube(3D)', 'wooslider-bymdez'),
                    'rotate'        => esc_html__('rotate', 'wooslider-bymdez'),
                    'rewind'        => esc_html__('rewind', 'wooslider-bymdez'),
                    'overflow'        => esc_html__('overflow', 'wooslider-bymdez'),
                    'creative1'        => esc_html__('creative-1', 'wooslider-bymdez'),
                    'creative2'        => esc_html__('creative-2', 'wooslider-bymdez'),
                    'creative3'        => esc_html__('creative-3', 'wooslider-bymdez'),
                    'creative4'        => esc_html__('creative-4', 'wooslider-bymdez'),
                    'creative5'        => esc_html__('creative-5', 'wooslider-bymdez'),
				],
                'default' => 'default',
			]
		);

        $this->add_control( //rounded images ?
            'swiper_no_shadow',
            [
                'label' => esc_html__('Disable 3D shadow?', 'wooslider-bymdez'),
                'description' => esc_html__('By activating this option, it disables the 3D shadow for this slider', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//Swiper active slide effect
			'swiper_active_effect',
			[
				'label' => esc_html__( 'Active slide effect', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'description' 	=> esc_html__( 'Some effects like "blur" only will be affected on the images not icons', 'wooslider-bymdez' ),
				'options' => [
                    'none'             => esc_html__('none', 'wooslider-bymdez'),
                    'grow'        => esc_html__('Grow', 'wooslider-bymdez'),
                    'shrink'      => esc_html__('Shrink', 'wooslider-bymdez'),
                    'grow_rotate'      => esc_html__('Grow-Flip', 'wooslider-bymdez'),
                    'shrink_rotate'      => esc_html__('Shrink-Flip', 'wooslider-bymdez'),
                    'rotate'      => esc_html__('Rotate', 'wooslider-bymdez'),
                    '3d'      => esc_html__('3D', 'wooslider-bymdez'),
                    'ghost'      => esc_html__('Ghost', 'wooslider-bymdez'),
                    'blur'      => esc_html__('Blur', 'wooslider-bymdez'),
                    'x_ray'      => esc_html__('X-ray', 'wooslider-bymdez'),
                    'fantasy'      => esc_html__('Fantasy', 'wooslider-bymdez'),
				],
                'default' => 'none'
			]
		);

        //Swiper per page
        $this->add_control(//wide slides number
			'swiper_slides_wide',
			[
				'label' 	=>	esc_html__( 'Slides in wide monitors', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                'description' 	=> esc_html__( 'you can change wide break-point in plugin settings page', 'wooslider-bymdez' ),
				'default' 	=>	3,
				'min' => 0,
                'max' => 8,
			]
		);

        $this->add_control(//wide slides space
			'swiper_space_wide',
			[
				'label' 	=>	esc_html__( 'Space in wide monitors', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
				'default' 	=>	30,
				'min' => 0,
                'max' => 200,
			]
        );
        
        $this->add_control(//desk slides number
			'swiper_slides_desk',
			[
				'label' 	=>	esc_html__( 'Slides in desktop', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
				'default' 	=>	3,
				'min' => 0,
                'max' => 8,
			]
		);

        $this->add_control(//desk slides space
			'swiper_space_desk',
			[
				'label' 	=>	esc_html__( 'Space in desktop', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
				'default' 	=>	30,
				'min' => 0,
                'max' => 200,
			]
        );

        $this->add_control(//tablet slides number
			'swiper_slides_tablet',
			[
				'label' 	=>	esc_html__( 'Slides in tablet', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
				'default' 	=>	3,
				'min' => 0,
                'max' => 5,
			]
		);

        $this->add_control(//tablet slides space
			'swiper_space_tablet',
			[
				'label' 	=>	esc_html__( 'Space in tablet', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
				'default' 	=>	25,
				'min' => 0,
                'max' => 200,
			]
        );

        $this->add_control(//mobile slides number
			'swiper_slides_mob',
			[
				'label' 	=>	esc_html__( 'Slides in mobile', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
				'default' 	=>	2,
				'min' => 0,
                'max' => 3,
			]
		);

        $this->add_control(//mobile slides space
			'swiper_space_mob',
			[
				'label' 	=>	esc_html__( 'Space in mobile', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
				'default' 	=>	20,
				'min' => 0,
                'max' => 200,
			]
        );

        $this->add_control(//mobile2 slides number
			'swiper_slides_mob2',
			[
				'label' 	=>	esc_html__( 'Slides in mobile-2 (below 300px width)', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                'description' 	=> esc_html__( 'you can change mobile-2 break-point in plugin settings page', 'wooslider-bymdez' ),
				'default' 	=>	1,
				'min' => 0,
                'max' => 2,
			]
		);

        $this->add_control(//mobile2 slides space
			'swiper_space_mob2',
			[
				'label' 	=>	esc_html__( 'Space in mobile-2', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
				'default' 	=>	10,
				'min' => 0,
                'max' => 200,
			]
        );



        $this->end_controls_section();

        /*------------*/
        /*TAB_STYLE*/
        /*------------*/
        $this->start_controls_section(//General Styles
			'general_style',
			[
				'label' => esc_html__( 'General Styles', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control( //rounded images ?
            'general_style_roundedimages',
            [
                'label' => esc_html__('Rounded images', 'wooslider-bymdez'),
                'description' => esc_html__('Adds border radius to all images within this slider', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control( //Circle images ?
            'general_style_circleimages',
            [
                'label' => esc_html__('Circle Frame images', 'wooslider-bymdez'),
                'description' => esc_html__('Shifts all the images inside the slider into a full circle frame', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control( //Auto width images
            'general_style_autowimages',
            [
                'label' => esc_html__('Auto width images', 'wooslider-bymdez'),
                'description' => esc_html__('Forces all the images (not icons) inside the slider to fit the maximum width inside their cells', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control( //100% width images
            'general_style_100images',
            [
                'label' => esc_html__('100% width images', 'wooslider-bymdez'),
                'description' => esc_html__('Forces ALL the images (include icons) inside the slider to fit the maximum width inside their cells (100%) if the above option not worked turn it off & use this option', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//all icons color
			'general_style_iconscolor',
			[
				'label' => esc_html__( 'All icons color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'description' => esc_html__('color change will be affected along all the icons', 'wooslider-bymdez'),
                'default' => null,
                'selectors' => [
					'{{WRAPPER}} .woosbymdz-catslider i' => 'color: {{VALUE}} !important',
				],
			]
		);

        $this->add_control(//all text color
			'general_style_titlecolor',
			[
				'label' => esc_html__( 'All title color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'description' => esc_html__('color change will be affected along all the titles', 'wooslider-bymdez'),
                'default' => null,
                'selectors' => [
					'{{WRAPPER}} .woosbymdz-catslider h6' => 'color: {{VALUE}} !important',
				],
			]
		);

        $this->add_group_control(//all elem icons typo
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'general_style_elemicons_typo',
                'label' => esc_html__( 'Elementor Icons Typography', 'wooslider-bymdez' ),
                'selector' => '{{WRAPPER}} .woosbymdz-catslider i.woosbymdz-elem-icon',
			]
            
		);

        $this->add_group_control(//all text typo
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'general_style_title_typo',
                'label' => esc_html__( 'Titles Typography', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} .woosbymdz-catslider h6',
			]
		);


        $this->end_controls_section();

        $this->start_controls_section(//Main con Styles
			'maincon_style',
			[
				'label' => esc_html__( 'Main Container', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'maincon_style_bg',
				'label' => esc_html__( 'Background', 'wooslider-bymdez' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .woosbymdz-catslider-con',
			]
		);


        $this->add_responsive_control(//main-con width
            'maincon_style_maxwidth', 
            [
            'label' => esc_html__( 'Width (Max-width)', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'size_units' => [ '%', 'px' ],
            'default' => [
                'size' => '100',
                'unit' => '%',
            ],
            'tablet_default' => [
                'unit' => '%',
            ],
            'mobile_default' => [
                'unit' => '%',
            ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-catslider-con' => 'max-width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control(//main-con padding
            'maincon_style_padding', 
            [
            'label' => esc_html__( 'Padding', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-catslider-con' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control(//main-con margin
            'maincon_style_margin', 
            [
            'label' => esc_html__( 'Margin', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'allowed_dimensions' => 'vertical',
			'placeholder' => [
				'top' => '',
				'right' => 'auto',
				'bottom' => '',
				'left' => 'auto',
			],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-catslider-con' => 'margin-top: {{TOP}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
            ],
        ]);

        $this->add_control(//main-con horizontal position
            'maincon_style_horizontalpos', 
            [
            'label' => esc_html__( 'Horizontal position', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'label_block' => false,
            'default' => 'center',
            'options' => [
                'left' => [
                    'title' => esc_html__( 'Left', 'wooslider-bymdez' ),
                    'icon' => 'eicon-h-align-left',
                ],
                'center' => [
                    'title' => esc_html__( 'Center', 'wooslider-bymdez' ),
                    'icon' => 'eicon-h-align-center',
                ],
                'right' => [
                    'title' => esc_html__( 'Right', 'wooslider-bymdez' ),
                    'icon' => 'eicon-h-align-right',
                ],
            ],
        ]);

        $this->add_group_control(//main-con border styles
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'maincon_style_border',
				'label' => esc_html__( 'Border', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} .woosbymdz-catslider-con',
			]
		);

        $this->add_control(
			'maincon_style_borderradius',
			[
				'label' => esc_html__( 'Border-Radius', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'description' => esc_html__('Pick a back-ground color/image to see the border-radius effect', 'wooslider-bymdez'),
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .woosbymdz-catslider-con' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(//Blocks Container (swiper wrapper)
			'blocks_container_style',
			[
				'label' => esc_html__( 'Blocks Container Style', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(//Blocks Container padding
            'blocks_container_style_padding', 
            [
            'label' => esc_html__( 'Padding', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .swiper-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control(//Blocks Container margin
            'blocks_container_style_margin', 
            [
            'label' => esc_html__( 'Margin', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'allowed_dimensions' => 'vertical',
			'placeholder' => [
				'top' => '',
				'right' => 'auto',
				'bottom' => '',
				'left' => 'auto',
			],
            'selectors' => [
                '{{WRAPPER}} .swiper-wrapper' => 'margin-top: {{TOP}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section(//Blocks Styles
			'blocks_style',
			[
				'label' => esc_html__( 'Block Style', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_group_control(//Blocks BG
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'blocks_style_bg',
				'label' => esc_html__( 'Background', 'wooslider-bymdez' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .woosbymdz-catslider',
			]
		);

        $this->add_responsive_control(//Blocks min-height
            'blocks_style_minheight', 
            [
            'label' => esc_html__( 'Blocks Height (Min-Height)', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'description' => esc_html__('Usefull for grid-style', 'wooslider-bymdez'),
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 5000,
                ],
            ],
            'size_units' => [ 'px' ],
            'default' => [
                'size' => '',
            ],
            'tablet_default' => [
                'size' => '',
            ],
            'mobile_default' => [
                'size' => '',
            ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-catslider-con .swiper-slide .woosbymdz-catslider' => 'min-height: {{SIZE}}{{UNIT}} '.$pros_important.';',
            ],
        ]);

        $this->add_control(//Slides auto-height
            'blocks_style_fullwid',
            [
                'label' => esc_html__('Full width Blocks?', 'wooslider-bymdez'),
                'description' => esc_html__('By activating this option, the slider blocks occupy the largest possible width, this is mostly used for grid type sliders.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );


        $this->add_responsive_control(//Blocks padding
            'blocks_style_padding', 
            [
            'label' => esc_html__( 'Padding', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-catslider' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control(//Blocks margin
            'blocks_style_margin', 
            [
            'label' => esc_html__( 'Margin', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'allowed_dimensions' => 'vertical',
			'placeholder' => [
				'top' => '',
				'right' => 'auto',
				'bottom' => '',
				'left' => 'auto',
			],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-catslider' => 'margin-top: {{TOP}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(//Blocks border styles
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'blocks_style_border',
				'label' => esc_html__( 'Border', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} .woosbymdz-catslider',
			]
		);

        $this->add_control(
			'blocks_style_borderradius',
			[
				'label' => esc_html__( 'Border-Radius', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'description' => esc_html__('Pick a back-ground color/image to see the border-radius effect', 'wooslider-bymdez'),
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .woosbymdz-catslider' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(//Blocks box-shadow
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'blocks_style_boxshadow',
				'label' => esc_html__( 'Box-Shadow', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} .woosbymdz-catslider',
			]
		);

        $this->add_responsive_control(//Blocks title width
            'blocks_style_title_width', 
            [
            'label' => esc_html__( 'Title Width', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'description' => esc_html__('Usefull for Synchronization all-blocks titles width with each other', 'wooslider-bymdez'),
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 3000,
                ],
            ],
            'size_units' => [ 'px' ],
            'default' => [
                'size' => '',
            ],
            'tablet_default' => [
                'size' => '',
            ],
            'mobile_default' => [
                'size' => '',
            ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-catslider h6' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section(//Arrows Style
			'arrows_style',
			[
				'label' => esc_html__( 'Arrows style', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' 	=> ['swiper_arrows' => 'yes'],
			]
		);

        $this->add_control(//arrows color
			'arrows_style_color',
			[
				'label' => esc_html__( 'Arrows color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'description' => esc_html__('color change will be affected along both the arrows', 'wooslider-bymdez'),
                'default' => null,
                'selectors' => [
					'{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'color: {{VALUE}} !important',
				],
			]
		);

        $this->add_group_control(//arrows typo
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'arrows_style_typo',
                'label' => esc_html__( 'Arrows Typography', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after',
			]
		);

        $this->add_group_control(//arrows BG
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'arrows_style_bg',
				'label' => esc_html__( 'Background', 'wooslider-bymdez' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev',
			]
		);

        $this->add_responsive_control(//arrows padding
            'arrows_style_padding', 
            [
            'label' => esc_html__( 'Padding', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control(//arrows margin
            'arrows_style_margin', 
            [
            'label' => esc_html__( 'Margin', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
			'placeholder' => [
				'top' => '',
				'right' => 'auto',
				'bottom' => '',
				'left' => 'auto',
			],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'margin-top: {{TOP}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}',
            ],
        ]);

        $this->add_group_control(//arrows border styles
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'arrows_style_border',
				'label' => esc_html__( 'Border', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev',
			]
		);

        $this->add_control(//arrows border-radius
			'arrows_style_borderradius',
			[
				'label' => esc_html__( 'Border-Radius', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'description' => esc_html__('Pick a back-ground color to see the border-radius effect', 'wooslider-bymdez'),
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(//nav Style
			'nav_style',
			[
				'label' => esc_html__( 'Navigation style', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' 	=> ['swiper_navs' => 'yes'],
			]
		);

        $this->add_control(//navs color
			'nav_style_color',
			[
				'label' => esc_html__( 'Navs color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'description' => esc_html__('color change will be affected along all the navigation dots', 'wooslider-bymdez'),
                'default' => null,
                'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background: {{VALUE}}',
				],
			]
		);

        $this->add_control(//navs color active
			'nav_style_active_color',
			[
				'label' => esc_html__( 'Navs active color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'description' => esc_html__('Selected color will just affect the active dot', 'wooslider-bymdez'),
                'default' => null,
                'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background: {{VALUE}} !important',
				],
			]
		);

        $this->add_control(//navs margin top
			'nav_style_top_margin',
			[
				'label' 	=>	esc_html__( 'Navs top margin', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                'description'   =>  esc_html__( 'In pixels (PX), negative values also accepted', 'wooslider-bymdez' ),
				'default' 	=>	0,
			]
		);

        $this->add_control(//slides icon or img?
			'nav_style_active_eff',
			[
				'label' => esc_html__( 'Active dot effect', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'none'          => esc_html__('none', 'wooslider-bymdez'),
                    'bigger'        => esc_html__('zoom out', 'wooslider-bymdez'),
                    'shrink'        => esc_html__('zoom in', 'wooslider-bymdez'),
                    'sharped'       => esc_html__('sharp', 'wooslider-bymdez'),
                    'modern'        => esc_html__('modern', 'wooslider-bymdez'),
                    'upper'         => esc_html__('up', 'wooslider-bymdez'),
                    'downer'        => esc_html__('down', 'wooslider-bymdez'),
                    'alone'         => esc_html__('alone', 'wooslider-bymdez'),
				],
                'default' => 'none'
			]
		);


        $this->end_controls_section();
        

	}

    //register css and js files
    public function get_script_depends() 
    {

        /*$is_global_assts = get_option('woosbymdz_globalimport');
        if($is_global_assts != '1'){
        
        $is_browsernocache = get_option('woosbymdz_isbrowsernocache');
        $dont_import_swiper = get_option('woosbymdz_donimport_swiper');

        switch ($is_browsernocache) {
            case '1':
                $dynamic_ver = get_option('woosbymdz_tmpversion');
                break;
            default:
                $dynamic_ver = '';
                break;
        }

        if($dont_import_swiper != '1'){

            wp_register_script( 'woosbymdz-swiperjs', WOOSBYMDZ_URL.'front/js/swiper-min.js', array(), WOOSBYMDZ_SW_VER);

        }

        
  
        $final_version = WOOSBYMDZ_VER.'.'.$dynamic_ver;
        $is_old_theme = get_option('woosbymdz_oldthemesdebuger');
        if($is_old_theme == '1'){
            wp_register_script( 'woosbymdz-frontjs', WOOSBYMDZ_URL.'front/js/frontjs.js', array('woosbymdz-swiperjs'), $final_version);
        }else wp_register_script( 'woosbymdz-frontjs', WOOSBYMDZ_URL.'front/js/frontjs.js', array('jquery', 'woosbymdz-swiperjs'), $final_version);

        wp_localize_script(
            'woosbymdz-frontjs',
            'woosbymdz_frnt_ajx_obj',
            [
                'wwkxssjk'  => wp_create_nonce( 'woosbymdz-uio2899nb-w144wkxs' ),
            ],
        );//For Ajax-call Security
        
        
        if($dont_import_swiper != '1'){

            return [
                'woosbymdz-swiperjs',
                'woosbymdz-frontjs',
            ];

        }else if($dont_import_swiper == '1'){

            return [
                'woosbymdz-frontjs',
            ];

        }	

        }else return [];*/
        
        $class_helper = help::get_instance();
		$tmps_arr = $class_helper->print_slider_js_deps();

		return $tmps_arr;

	}

	public function get_style_depends() 
    {

        /*$is_global_assts = get_option('woosbymdz_globalimport');
        if($is_global_assts != '1'){

        $is_browsernocache = get_option('woosbymdz_isbrowsernocache');
        $dont_import_swiper = get_option('woosbymdz_donimport_swiper');

        switch ($is_browsernocache) {
            case '1':
                $dynamic_ver = get_option('woosbymdz_tmpversion');
                break;
            default:
                $dynamic_ver = '';
                break;
        }

        if($dont_import_swiper != '1'){

            wp_register_style( 'woosbymdz-swipercss', WOOSBYMDZ_URL.'front/css/swiper-min.css', array(), WOOSBYMDZ_SW_VER);

        }

		$final_version = WOOSBYMDZ_VER.'.'.$dynamic_ver;
		wp_register_style( 'woosbymdz-frontcss', WOOSBYMDZ_URL.'front/css/frontcss.css', array(), $final_version);

        if($dont_import_swiper != '1'){

            return [
                'woosbymdz-swipercss',
                'woosbymdz-frontcss',
            ];

        }else if($dont_import_swiper == '1'){

            return [
                'woosbymdz-frontcss',
            ];


        }
        }else return [];*/


        $class_helper = help::get_instance();
		$tmps_arr = $class_helper->print_slider_css_deps();

		return $tmps_arr;
		

	}

	/**
	 * Render list widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() 
    {

        $settings = $this->get_settings_for_display();
        $is_admin = is_admin();

        if($settings['dont_render'] == 'yes' && $is_admin){
            return;//Do Not render this elementor-widget
        }

        //add js if is editor
        $is_editor = 'no';
        $swiper_noeff = 0;
        if ($is_admin){
            
            ?>
            <script type="text/javascript">
            jQuery(document).ready(function () { woosbymdz_mainjs_run(); });
            </script>
            <?php

            $is_editor = 'yes';

            $swiper_noeff = get_option('woosbymdz_swipernoeff');

        }
 
        

        //HTML

        //set general options
        switch($settings['general_style_roundedimages']){
            case 'yes':
                $images_borderradius_class = 'woosbymdz-allimages-rounded';
                break;
            default:
                $images_borderradius_class = '';
                break;
        }
        switch($settings['general_style_circleimages']){
            case 'yes':
                $images_circle_class = 'woosbymdz-allimages-circled';
                break;
            default:
                $images_circle_class = '';
                break;
        }
        switch($settings['maincon_style_horizontalpos']){
            case 'center':
                $maincon_side_margins = 'margin-left: auto;margin-right: auto';
                break;
            case 'left':
                $maincon_side_margins = 'margin-right: auto;';
                break;
            case 'right':
                $maincon_side_margins = 'margin-left: auto;';
                break;
        }
        switch($settings['swiper_arrows']){
            case 'yes':
                $no_arrows = 'no';
                break;
            default:
                $no_arrows = 'yes';
                break;
        }

        if($settings['swiper_navs'] == 'yes'){
            $nav_active_style = $settings['nav_style_active_eff'];
        }else $nav_active_style = 'none';

        switch($settings['general_style_autowimages']){
            case 'yes':
                $maincon_autowimages = 'woosbymdz-autowidth-images';
                break;
            default:
                $maincon_autowimages = '';
                break;
        }
     
        switch($settings['swiper_active_effect']){
            case 'grow':
                $active_slide_eff = 'grow';
                break;
            case 'shrink':
                $active_slide_eff = 'shrink';
                break;
            case 'grow_rotate':
                $active_slide_eff = 'grow-rotate';
                break;
            case 'shrink_rotate':
                $active_slide_eff = 'shrink-rotate';
                break;
            case 'rotate':
                $active_slide_eff = 'rotate';
                break;
            case '3d':
                $active_slide_eff = '3d';
                break;
            case 'ghost':
                $active_slide_eff = 'ghost';
                break;
            case 'blur':
                $active_slide_eff = 'blur';
                break;
            case 'x_ray':
                $active_slide_eff = 'x-ray';
                break;
            case 'fantasy':
                $active_slide_eff = 'fantasy';
                break;
            default:
                $active_slide_eff = 'none';
                break;
        }

        ?>
        <div style="<?php echo esc_attr($maincon_side_margins); ?>" class="woosbymdz-catslider-con <?php echo esc_attr($maincon_autowimages); ?>" move-effect="<?php echo esc_attr($settings['swiper_move_effect']); ?>" no-arrows="<?php echo esc_attr($no_arrows); ?>" nav-active="<?php echo esc_attr($nav_active_style); ?>" active-slide-eff="<?php echo esc_attr($active_slide_eff); ?>" img100="<?php echo esc_attr($settings['general_style_100images']) ?>" no3dsshdow="<?php echo esc_attr($settings['swiper_no_shadow']); ?>">

        <div class="swiper woosbymdz-catslider-swiper" isdisswiper="<?php echo esc_attr($swiper_noeff); ?>" is-elem-editor="<?php echo $is_editor; ?>" loop="<?php echo esc_attr($settings['swiper_loop']); ?>" navs="<?php echo esc_attr($settings["swiper_navs"]); ?>" arrows="<?php echo esc_attr($settings["swiper_arrows"]); ?>" grids="<?php echo esc_attr($settings['swiper_grids']); ?>" <?php if($settings['swiper_grids'] == 'yes') { echo esc_attr('grid-rows= '.$settings["swiper_grid_rows"].''); }?> autoplay="<?php echo esc_attr($settings["swiper_autoplay"]); ?>" <?php if($settings['swiper_autoplay'] == 'yes') { echo esc_attr('play-speed= '.$settings["swiper_autoplay_speed"].''); }?> wide-pp ="<?php echo esc_attr($settings['swiper_slides_wide']); ?>" wide-s ="<?php echo esc_attr($settings['swiper_space_wide']); ?>" desk-pp="<?php echo esc_attr($settings['swiper_slides_desk']); ?>" desk-s="<?php echo esc_attr($settings['swiper_space_desk']); ?>" tab-pp="<?php echo esc_attr($settings['swiper_slides_tablet']); ?>" tab-s="<?php echo esc_attr($settings['swiper_space_tablet']); ?>" mob-pp="<?php echo esc_attr($settings['swiper_slides_mob']); ?>" mob-s="<?php echo esc_attr($settings['swiper_space_mob']); ?>" mob2-pp="<?php echo esc_attr($settings['swiper_slides_mob2']); ?>" mob2-s="<?php echo esc_attr($settings['swiper_space_mob2']);?>" rotate-effect="<?php echo esc_attr($settings['swiper_rotate_effect']); ?>" auto-h="<?php echo esc_attr($settings['swiper_autoheight']); ?>" is-centered-slider="<?php echo esc_attr($settings['swiper_centered']) ?>">

        <div class="swiper-wrapper <?php echo esc_attr($images_borderradius_class); ?> <?php echo esc_attr($images_circle_class); ?>" auto-h-slides="<?php echo esc_attr($settings['auto_height']); ?>" full-wid-slides="<?php echo esc_attr($settings['blocks_style_fullwid']) ?>">

        <?php

        //set repeater options
        switch($settings['slides_img_type_2_lazy']){
            case 'yes':
                $lazy_images_attr = 'loading= "lazy"';
                break;
            default:
                $lazy_images_attr = '';
                break;
        }

        foreach ($settings['slides_elements'] as $index => $item) {//Start the Loop on slides

            if(!empty($settings['slides_elements'][$index]['slides_url']['url'])){

                $url = $settings['slides_elements'][$index]['slides_url']['url'];

            }else $url = '#';

            switch($settings['slides_elements'][$index]['slides_url']['is_external']){
                case 'on':
                    $is_ext = '_blank';
                    break;
                default:
                    $is_ext = '_self';
                    break;
            }

            switch($settings['slides_elements'][$index]['slides_url']['nofollow']){
                case 'on':
                    $is_nofollow = 'rel= "nofollow"';
                    break;
                default:
                    $is_nofollow = '';
                    break;
            }

            switch($settings['slides_elements'][$index]['slides_template']){

                case 'imagetop':
                    $image_class = 'woosbymdz-catslider-imagetop';
                    $h6_class = '';
                    break;
                case 'imagedown':
                    $image_class = 'woosbymdz-catslider-imagedown';
                    $h6_class = '';
                    break;
                case 'imageright':
                    $image_class = 'woosbymdz-catslider-imageright';
                    $h6_class = '';
                    break;
                case 'imageleft':
                    $image_class = 'woosbymdz-catslider-imageleft';
                    $h6_class = '';
                    break;
                case 'imagehover':
                    $h6_class = 'woosbymdz-catslider-hidden-imagehover';
                    $image_class = 'woosbymdz-catslider-imagehover';
                    break;
                case 'titlehover':
                    $h6_class = 'woosbymdz-catslider-titlehover';
                    $image_class = 'woosbymdz-catslider-hidden-titlehover';
                    break;
                case 'imagerotate':
                    $h6_class = 'woosbymdz-catslider-hidden-imagerotate';
                    $image_class = 'woosbymdz-catslider-imagerotate';
                    break;
                case 'metro':
                    $h6_class = 'woosbymdz-catslider-metro';
                    $image_class = 'woosbymdz-catslider-hidden-metro';
                    break;
                default:
                    $h6_class = '';
                    $image_class = '';
                    break;
            }
            $has_extracss_classes = false;
            if ($settings['slides_elements'][$index]['slides_extracss'] != ''){//Extra css classes
                $has_extracss_classes = true;
                $block_extracss_classes = explode('|', $settings['slides_elements'][$index]['slides_extracss']);
            }

            if($settings['slides_elements'][$index]['slides_img_type_2_width_attr'] != ''){
                $width_attr = 'width= '.$settings['slides_elements'][$index]['slides_img_type_2_width_attr'].'';
            }else $width_attr = '';

            if($settings['slides_elements'][$index]['slides_img_type_2_height'] != ''){
                $height_attr = 'height= '.$settings['slides_elements'][$index]['slides_img_type_2_height'].'';
            }else $height_attr = '';


            $resp_img_sign = '';
            $resp_img_sign2 = '';
            if($settings['slides_elements'][$index]['slides_resp_images'] == 'yes'){
                $resp_img_sign = 'resp-img';
                $resp_img_sign2 = 'resp-img-imgtag';
            }
            
            ?>
            <div class="swiper-slide <?php echo $resp_img_sign; ?> <?php if($has_extracss_classes) {foreach($block_extracss_classes as $block_extracss_classe) {echo esc_attr($block_extracss_classe.' '); } } ?>">

            <a href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($is_ext); ?>" <?php echo $is_nofollow; ?>>
            <div class="woosbymdz-catslider" template="<?php echo esc_attr($settings['slides_elements'][$index]['slides_template']); ?>">
            
            <?php if($settings['slides_elements'][$index]['slides_img_type'] == 'image') {

            ?>
            <div class="woosbymdz-catslider-innerimg-con">
                <img <?php echo esc_attr($width_attr); ?> <?php echo esc_attr($height_attr); ?> class="catsslider-img woosbymdz-imgcanautowidth <?php echo $resp_img_sign2; ?> <?php echo esc_attr($image_class); if($settings['no_lazy'] == 'yes') {echo ' '.$settings['no_lazy_css_class'];}?>" style="height: auto;width: <?php echo esc_attr($settings['slides_elements'][$index]['slides_img_type_2_width'])?>px" src="<?php echo esc_url($settings['slides_elements'][$index]['slides_img_type_2']['url']); ?>" alt="<?php echo esc_attr($settings['slides_elements'][$index]['slides_img_type_2_alt']); ?>" <?php if($settings['slides_elements'][$index]['slides_resp_images'] == 'yes'){?>wide-src="<?php echo esc_url($settings['slides_elements'][$index]['slides_img_type_2_wide']['url']); ?>" tab-src="<?php echo esc_url($settings['slides_elements'][$index]['slides_img_type_2_tab']['url']); ?>" mob-src="<?php echo esc_url($settings['slides_elements'][$index]['slides_img_type_2_mob']['url']); ?>" mob2-src="<?php echo esc_url($settings['slides_elements'][$index]['slides_img_type_2_mob2']['url']); ?>" default-src="<?php echo esc_url($settings['slides_elements'][$index]['slides_img_type_2']['url']); ?>"<?php } ?> <?php echo $lazy_images_attr; ?>>
            </div>
            <?php

            }else if ($settings['slides_elements'][$index]['slides_img_type'] == 'custom_icons'){

            ?>
            <div class="woosbymdz-catslider-innerimg-con">
                <i class="<?php echo esc_attr($image_class); ?>"><img <?php echo esc_attr($width_attr); ?> <?php echo esc_attr($height_attr); ?> class="<?php if($settings['no_lazy'] == 'yes') {echo ' '.$settings['no_lazy_css_class'];}?>" style="height: auto;width: <?php echo esc_attr($settings['slides_elements'][$index]['slides_img_type_2_width'])?>px" src="<?php echo esc_url($settings['slides_elements'][$index]['slides_img_type_2']['url']); ?>" alt="<?php echo esc_attr($settings['slides_elements'][$index]['slides_img_type_2_alt']); ?>" <?php echo $lazy_images_attr; ?>></i>
            </div>
            <?php

            }else if ($settings['slides_elements'][$index]['slides_img_type'] == 'elem_icons'){
                ?>
                <div class="woosbymdz-catslider-innerimg-con">
                    <i style="color: <?php echo esc_attr($settings['slides_elements'][$index]['slides_img_type_1_color']); ?>;" class="woosbymdz-elem-icon <?php echo esc_attr($settings['slides_elements'][$index]['slides_img_type_1']['value']); ?> <?php echo esc_attr($image_class); ?>"></i>
                </div>
                <?php
            }
            
            ?>

            <?php if($settings['slides_elements'][$index]['slides_title_show'] == 'yes') { ?>
            <h6 style="color: <?php echo esc_attr($settings['slides_elements'][$index]['slides_title_color']); ?>" class="<?php echo esc_attr($h6_class); ?>"><?php echo esc_html($settings['slides_elements'][$index]['slides_title']); ?></h6>
            <?php }//end of title if ?>
            </div>
            </a>

            <?php 
            if($settings['slides_elements'][$index]['slides_extrahtml'] == 'yes'){

                ?>
                <div class="woosbymdz-catslider-extrahtml-con"><?php echo $settings['slides_elements'][$index]['slides_extrahtml_html']; ?></div>
                <?php

            }
            ?>

            </div>
        <?php }//End of slides Loop ?>
        </div><!--End of swiper inner-->

        <div style="transform: translateY(<?php echo esc_attr($settings['nav_style_top_margin']); ?>px)" class="swiper-pagination"></div>

        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>

        </div>
        </div><!--End of main con--> 
        <?php
        
    }

    //render editor live preview 
    protected function content_template() {}

}//End of Class
}//close the PHP tag to reduce the blank spaces ?>