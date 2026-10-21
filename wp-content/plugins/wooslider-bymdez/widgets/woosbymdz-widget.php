<?php 

use Woosbymdz\Widgets\Controls\controls as conts;
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

require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'controls'.DIRECTORY_SEPARATOR.'wooslider-controls.php';
require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'main.php';

/**
 * WOOSBYMDZ_SLIDER_Widget
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
if(!class_exists('WOOSBYMDZ_SLIDER_Widget'))
{
class WOOSBYMDZ_SLIDER_Widget extends \Elementor\Widget_Base 
{
    //PROPS
    //private $class_helper_obj;

    /*public function __construct()
    {
        $this->class_helper_obj = new help();
    }*/

	public function get_name() 
    {
		return 'mdz_woo_slider';
	}

	public function get_title() 
    {
		return esc_html__( 'Woo Modern Slider', 'wooslider-bymdez' );
	}

	public function get_icon() 
    {
		return 'woosbymdz-cion-slider';
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
		return [ 'product', 'carousel', 'slider', 'mdz', 'ووکامرس', 'اسلایدر محصولات', 'محصولات', 'اسلایدر مدرن', 'اسلایدر', 'کاروسل', 'کاروسل محصولات' ];
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
        $class_controlls = new conts();
        $class_helper = new help();
        /*Get Themes Configs*/
        //include_once WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'this-theme-confs.php';//TMP

        //Fetch-Settings

        /*$use_all_impo = get_option('woosbymdz_elem_allcss_impo');
        $use_pros_impo = get_option('woosbymdz_elem_procss_impo');

        $all_important = $pros_important = '';

        if($use_all_impo == '1'){
            $all_important = '!important';
            $pros_important = $all_important;
        }

        if($use_pros_impo == '1'){
            $pros_important = '!important';
        }*///Moved to class `wooslider-controls.php`



        /*GENERAL*/
        $class_controlls->main_controls($this, 1);

		/*$this->start_controls_section(//tab_content
			'content_section',
			[
				'label' => esc_html__( 'General', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control( //Just Refresh the frame
            'refresh_frame',
            [
                'label' => esc_html__('Update Frame', 'wooslider-bymdez'),
                'description' => esc_html__('no effects on the functionality, just re-fresh the live view', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

		$this->add_control( //Loading
            'loading_enabled',
            [
                'label' => esc_html__('Primary Loading', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'yes',
            ]
        );

        $this->add_control( //Header
            'header_enabled',
            [
                'label' => esc_html__('Show Header', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'yes',
            ]
        );

        $this->add_control( //Show to logged in users or not
            'showonly_tologgedin',
            [
                'label' => esc_html__('Just show to members', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control( //Show header to logged in users or not
            'showonly_tologgedin_header',
            [
                'label' => esc_html__('Just show header to members', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'description' => esc_html__('upon activation, only show header to logged-in users', 'wooslider-bymdez'),
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'header_enabled',
                            'operator' => '===',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'showonly_tologgedin',
                            'operator' => '===',
                            'value' => 'yes'
                        ]
                    ]
                ],
                'label_on' => esc_html__( 'NO', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Yes', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control( //do not load on mobile
            'dont_load_mob',
            [
                'label' => esc_html__('Dont load on mobiles', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'description' => esc_html__('if you want to show something else instead of this elem/widget in mobile devices', 'wooslider-bymdez'),
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control( //Disable Swiper on mobile devices
            'dont_swipe_on_mob',
            [
                'label' => esc_html__('Disable swiper on mobile', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'description' => esc_html__('This option will disable swiper on mobile devices, therefore user can use the slider very freely without any lags', 'wooslider-bymdez'),
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control( //Disable Swiper (General)
            'dont_swipe',
            [
                'label' => esc_html__('Disable swiper (General)', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'description' => esc_html__('Using this option, you can display your products in a grid (non-slider) format, but it may require CSS code.', 'wooslider-bymdez'),
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

		$this->end_controls_section();*/

        //TEMPLATE
        $this->start_controls_section(
			'template',
			[
				'label' => esc_html__( 'Template', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(//def source of products
			'products_template',
			[
				'label' => esc_html__( 'Products Template-Type', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'description' => esc_html__('Note: To use other custom templates for product cards, on the plugin settings page, enable the option to `custom templates for product cards` and save the settings to add additional CSS codes for this to the main file. Using this option, you can display the card-products inside the slider in different modes!', 'wooslider-bymdez'),
				'options' => [
					'default_temp'   => esc_html__('Based on your Active-Theme', 'wooslider-bymdez'),
                    'temp1' => esc_html__('Template-1', 'wooslider-bymdez'),
                    'temp2' => esc_html__('Template-2', 'wooslider-bymdez'),
                    'temp3' => esc_html__('Template-3', 'wooslider-bymdez'),
                    'exc-template' => esc_html__('Exclusive Template Design', 'wooslider-bymdez'),
				],
                'default' => 'default_temp',
			]
		);

        $this->add_control( //Just Refresh the frame
            'refresh_frame2',
            [
                'label' => esc_html__('Need Exclusive Design?', 'wooslider-bymdez'),
                'description' => esc_html__('If you need to design your sites product card professionally and very accurately, we can do it for you, please contact WooSlider plugin support.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_template',
                            'operator' => '==',
                            'value' => 'exc-template'
                        ],
                    ],

                ],
                
            ]
        );


        $this->add_control( //Translate woo-curr sign text!
            'products_template_opt1',
            [
                'label' => esc_html__('Translate Prices-Currency', 'wooslider-bymdez'),
                'description' => esc_html__('Translates the product price unit text based on your sites current language.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'on', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'off', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'products_template',
                            'operator' => '==',
                            'value' => 'temp1'
                        ],
                        [
                            'name' => 'products_template',
                            'operator' => '==',
                            'value' => 'temp2'
                        ],
                        [
                            'name' => 'products_template',
                            'operator' => '==',
                            'value' => 'temp3'
                        ],
                    ],

                ],
            ]
        );

        $this->add_control( //Products _blank link
            'products_template_opt2',
            [
                'label' => esc_html__('Open product-links in new tab?', 'wooslider-bymdez'),
                'description' => esc_html__('Makes product-cards links (a-tags) open in new window (taget="_blank")', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'on', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'off', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'products_template',
                            'operator' => '==',
                            'value' => 'temp1'
                        ],
                        [
                            'name' => 'products_template',
                            'operator' => '==',
                            'value' => 'temp2'
                        ],
                        [
                            'name' => 'products_template',
                            'operator' => '==',
                            'value' => 'temp3'
                        ],
                    ],

                ],
            ]
        );

        $this->add_control(//align-vertical card temp-1 & temp-3
			'products_template_opt3',
			[
				'label' => esc_html__( 'Custom-Card Vertical-Align', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'description' => esc_html__('For this option to take effect, enable the Equalize Slides option in the Style tab of the Products section.', 'wooslider-bymdez'),
				'options' => [
					'default'         => esc_html__('default', 'wooslider-bymdez'),
                    'start'           => esc_html__('Start', 'wooslider-bymdez'),
					'end'             =>	esc_html__('End', 'wooslider-bymdez'),
                    'center'          =>	esc_html__('Center', 'wooslider-bymdez'),
                    'around'          =>	esc_html__('Around', 'wooslider-bymdez'),
                    'evenly'          =>	esc_html__('Evenly', 'wooslider-bymdez'),
                    'between'         =>	esc_html__('Between', 'wooslider-bymdez'),
				],
                'default' => 'default',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'products_template',
                            'operator' => '==',
                            'value' => 'temp1'
                        ],
                        [
                            'name' => 'products_template',
                            'operator' => '==',
                            'value' => 'temp3'
                        ],
                    ],

                ],
			]
		);

        $this->add_control(//align-vertical card temp-2
			'products_template_opt4',
			[
				'label' => esc_html__( 'Custom-Card Vertical-Align', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'description' => esc_html__('For this option to take effect, enable the Equalize Slides option in the Style tab of the Products section.', 'wooslider-bymdez'),
				'options' => [
					'default'              => esc_html__('default', 'wooslider-bymdez'),
                    'start'              => esc_html__('Start', 'wooslider-bymdez'),
					'end'          =>	esc_html__('End', 'wooslider-bymdez'),
                    'baseline'          =>	esc_html__('Baseline', 'wooslider-bymdez'),
                    'center'          =>	esc_html__('Center', 'wooslider-bymdez'),
                    'normal'          =>	esc_html__('Normal', 'wooslider-bymdez'),
				],
                'default' => 'default',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_template',
                            'operator' => '==',
                            'value' => 'temp2'
                        ],
                    ],

                ],
			]
		);

        $this->add_control( //Remove self-main-pro name from child names
            'products_template_opt5',
            [
                'label' => esc_html__('Remove main-product name from the variation-names', 'wooslider-bymdez'),
                'description' => esc_html__('If the product inside the slider is variable, in the Variable Products section under the main product category, it removes the main product name from the variable names.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'on', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'off', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_template',
                            'operator' => '==',
                            'value' => 'temp3'
                        ],
                    ],

                ],
            ]
        );


        $this->end_controls_section();
        /***/ 

        /*PRODUCTS*/
		$this->start_controls_section(
			'products',
			[
				'label' => esc_html__( 'Products', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control( //Just Refresh the frame
            'refresh_frame2',
            [
                'label' => esc_html__('Update Frame', 'wooslider-bymdez'),
                'description' => esc_html__('no effects on the functionality, just re-fresh the live view', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                
            ]
        );


        $this->add_control(//def source of products
			'products_src',
			[
				'label' => esc_html__( 'Source', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'description' => esc_html__('Up-sells, Cross-sells and Related just have functionality in the single products-page, Before using Charged-Products source, go to plugin-settings page and activate charged-products system', 'wooslider-bymdez'),
				'options' => [
					'news'              => esc_html__('News', 'wooslider-bymdez'),
                    'olds'              => esc_html__('Olds', 'wooslider-bymdez'),
					'featured'          =>	esc_html__('Featured', 'wooslider-bymdez'),
					'sale'              =>	esc_html__('Sale', 'wooslider-bymdez'),
					'recently_viewed'	=>	esc_html__('Last seen (by user)', 'wooslider-bymdez'),
					'bestsellings'		=>	esc_html__('Most sales', 'wooslider-bymdez'),
                    'lowsellings'		=>	esc_html__('Lowest sales', 'wooslider-bymdez'),
                    'lastedited'		=>	esc_html__('Last modified (by admins)', 'wooslider-bymdez'),
                    'rand'		        =>	esc_html__('random', 'wooslider-bymdez'),
                    'upsells'		    =>	esc_html__('Up-sells', 'wooslider-bymdez'),
                    'crossells'		    =>	esc_html__('Cross-sells', 'wooslider-bymdez'),
                    'related'		    =>	esc_html__('Related', 'wooslider-bymdez'),
                    'chargeds'          =>	esc_html__('Charged-Products', 'wooslider-bymdez'),
				],
                'default' => 'news',
			]
		);

        $this->add_control(//stock ? in_stock
			'products_stock',
			[
				'label' => esc_html__( 'Stock', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'all'                        => esc_html__('dont matter', 'wooslider-bymdez'),
                    'only_instocks'              => esc_html__('only in-stocks', 'wooslider-bymdez'),
					'only_not_instocks'          =>	esc_html__('only outof-stocks', 'wooslider-bymdez'),
				],
                'default' => 'all',
			]
		);
        
        $this->add_control( //products max number
			'products_number',
			[
				'label' 		=> esc_html__( 'Number', 'wooslider-bymdez' ),
				'type'			=> \Elementor\Controls_Manager::NUMBER,
				'description' 	=> esc_html__( 'to show all the products, leave this blank', 'wooslider-bymdez' ),
				'return_value' 	=> 'true',
				'default' 		=> 15,
			]
		);

        $this->add_control(//get products from what ?
			'products_fetch',
			[
				'label' => esc_html__( 'Fetch from ?', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'cat'                 => esc_html__('Product Categories', 'wooslider-bymdez'),
                    'tag'                 => esc_html__('Product Tags', 'wooslider-bymdez'),
					'custom_tax'          => esc_html__('Custom Taxonomy', 'wooslider-bymdez'),
				],
                'default' => 'cat',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'yoast_main_cat',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
			]
		);

        $this->add_control(//cats
			'products_cats',
			[
				'label' 		=>	esc_html__( 'Categories', 'wooslider-bymdez' ),
				'type' 			=>	\Elementor\Controls_Manager::SELECT2,
				'description'   =>  esc_html__( 'Choose and Search', 'wooslider-bymdez' ),
				'label_block'	=>  'true',
				'multiple' 	    =>	true,
				'options' 		=>  $class_helper->get_terms_mdz('product_cat'),
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'yoast_main_cat',
                            'operator' => '!==',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'products_fetch',
                            'operator' => '===',
                            'value' => 'cat'
                        ],
                    ],

                ],
			]
		);

        $this->add_control(//Tags
			'products_tags',
			[
				'label' 		=>	esc_html__( 'Product Tags', 'wooslider-bymdez' ),
				'type' 			=>	\Elementor\Controls_Manager::SELECT2,
				'description'   =>  esc_html__( 'Choose and Search', 'wooslider-bymdez' ),
				'label_block'	=>  'true',
				'multiple' 	    =>	true,
				'options' 		=>  $class_helper->get_terms_mdz('product_tag'),
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'yoast_main_cat',
                            'operator' => '!==',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'products_fetch',
                            'operator' => '===',
                            'value' => 'tag'
                        ],
                    ],

                ],
			]
		);

        $this->load_all_custom_taxs();//Load Custom-Taxs

        $this->add_control( //use attrs ?
            'products_use_attrs',
            [
                'label' => esc_html__('Products Attributes', 'wooslider-bymdez'),
                'description' => esc_html__('Lets you to filter products by their custom global attributes', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'yoast_main_cat',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
            ]
        );

        $this->load_all_custom_attrs();//Load all custom Attrs

        $this->add_control(//Query Operator
			'products_query_operator',
			[
				'label' => esc_html__( 'Query Operator', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'AND'                => esc_html__('AND', 'wooslider-bymdez'),
					'OR'                 =>	esc_html__('OR', 'wooslider-bymdez'),
				],
                'default' => 'OR',
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'yoast_main_cat',
                            'operator' => '!==',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'products_use_attrs',
                            'operator' => '===',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'products_fetch',
                            'operator' => '!==',
                            'value' => 'custom_tax',
                        ],     
                    ]
                ],
			]
		);

        $this->add_control(//Query Operator-2 (custom_tax)
			'products_query_operator2',
			[
				'label' => esc_html__( 'Query Operator', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'AND'                => esc_html__('AND', 'wooslider-bymdez'),
					'OR'                 =>	esc_html__('OR', 'wooslider-bymdez'),
				],
                'default' => 'OR',
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'yoast_main_cat',
                            'operator' => '!==',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'products_fetch',
                            'operator' => '===',
                            'value' => 'custom_tax',
                        ],     
                    ]
                ],
			]
		);

        $this->add_control( //get yoast main cat ?
            'yoast_main_cat',
            [
                'label' => esc_html__('Yoast Main-Cat', 'wooslider-bymdez'),
                'description' => esc_html__('Note: The Product Main-Cat mode only works if you have Yoast-Seo Plugin installed and only works on the Product Pages, for more info about this Advanced option please visit plugin settings-page (Advanced) section', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//Extra products
			'products_extra',
			[
				'label' 		=>	__( 'Show these products too', 'wooslider-bymdez' ),
				'type' 			=>	\Elementor\Controls_Manager::TEXTAREA,
                'placeholder'   => esc_html__('Example: 123,45', 'wooslider-bymdez'),
				'description'   =>  esc_html__( 'adds entered products to the slider (enter product id and seperate with ,)', 'wooslider-bymdez' ),
                'label_block'	=> 'true',
			]
		);

        $this->add_control(//Exclude products
			'products_exclude',
			[
				'label' 		=>	__( 'Exclude these products', 'wooslider-bymdez' ),
				'type' 			=>	\Elementor\Controls_Manager::TEXTAREA,
                'placeholder'   => esc_html__('Example: 7,123,12', 'wooslider-bymdez'),
				'description'   =>  esc_html__( 'Removes entered products from the slider (enter product id and seperate with ,)', 'wooslider-bymdez' ),
                'label_block'	=> 'true',
			]
		);
        
        $this->add_control( //allcats btn
            'products_allcats_show',
            [
                'label' => esc_html__('See all button', 'wooslider-bymdez'),
                'description' => esc_html__('adds an extra block to the end of the slider, use this to create a link to the whole category page', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//see all cats title
			'products_allcats_title', [
				'label' => esc_html__( 'Title', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'condition' 	=> ['products_allcats_show' => 'yes'],
				'default' => esc_html__( 'see all' , 'wooslider-bymdez' ),
				'label_block' => true,
			]
		);
        
        $this->add_control(//see all cats title color
			'products_allcats_titlecolor',
			[
				'label' => esc_html__( 'Title color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'condition' 	=> ['products_allcats_show' => 'yes'],
                'default' => '#000',
			]
		);

        $this->add_control(//see all cats background
			'products_allcats_backtype',
			[
				'label' => esc_html__( 'Background type', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'condition' 	=> ['products_allcats_show' => 'yes'],
				'options' => [
                    'color'             => esc_html__('color', 'wooslider-bymdez'),
                    'wallpaper'        => esc_html__('picture', 'wooslider-bymdez'),
				],
                'default' => 'color'
			]
		);

        $this->add_control(//see all cats background color
			'products_allcats_backcolor',
			[
				'label' => esc_html__( 'Background color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'products_allcats_show',
                            'operator' => '===',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'products_allcats_backtype',
                            'operator' => '===',
                            'value' => 'color'
                        ]
                    ]
                ],
                'default' => '#fff'
			]
		);

        $this->add_control(//see all cats background img
			'products_allcats_backimg',
			[
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => esc_html__( 'Choose a pic', 'wooslider-bymdez' ),
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'products_allcats_show',
                            'operator' => '===',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'products_allcats_backtype',
                            'operator' => '===',
                            'value' => 'wallpaper'
                        ]
                    ]
                ],
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
			]
		);

        $this->add_control(//see all cats link
			'products_allcats_url',
			[
				'label' => esc_html__( 'URL', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::URL,
                'condition' 	=> ['products_allcats_show' => 'yes'],
				'placeholder' => esc_html__( 'https://your-site.com/category/test', 'wooslider-bymdez' ),
			]
		);

        $this->add_control(//see all cats link (Yoast)
            'products_allcats_yoast_link',
            [
                'label' => esc_html__('Link to the Yoast-main Category?', 'wooslider-bymdez'),
                'description' => esc_html__('Use this option only inside the single-products page and if you have Yoast-Seo plugin', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'condition' 	=> ['products_allcats_show' => 'yes'],
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );


		$this->end_controls_section();
        
        /*SWIPER*/
        $class_controlls->main_controls($this, 2);
        /*$this->start_controls_section(
			'slider',
			[
				'label' => esc_html__( 'Slider', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control( //Just Refresh the frame
            'refresh_frame3',
            [
                'label' => esc_html__('Update Frame', 'wooslider-bymdez'),
                'description' => esc_html__('no effects on the functionality, just re-fresh the live view', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//def swiper type
			'swiper_type',
			[
				'label' => esc_html__( 'Slider type', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'description' => esc_html__('To use the cube display mode, set the distance of all slides to zero and display only one slide per device.', 'wooslider-bymdez'),
				'options' => [
                    'default'             => esc_html__('Default', 'wooslider-bymdez'),
                    'center'              => esc_html__('Centered', 'wooslider-bymdez'),
                    'freemode'            => esc_html__('Free-hand', 'wooslider-bymdez'),
                    'coverflow'            => esc_html__('Coverflow', 'wooslider-bymdez'),
                    'loop'            => esc_html__('Loop', 'wooslider-bymdez'),
                    'skipper'            => esc_html__('Skipper', 'wooslider-bymdez'),
                    'rewind'            => esc_html__('Re-wind', 'wooslider-bymdez'),
                    'cube'            => esc_html__('Cube(3D)', 'wooslider-bymdez'),
				],
                'default' => 'default'
			]
		);

        $this->add_control( //Coverflow-Effect Rotation-degree
			'swiper_cflow_rotate',
			[
				'label' 		=> esc_html__( 'Coverflow Rotate Degree', 'wooslider-bymdez' ),
				'type'			=> \Elementor\Controls_Manager::NUMBER,
				'description' 	=> esc_html__( 'Adjusts the degree of rotation of slides in cover flow mode, default value is 50', 'wooslider-bymdez' ),
				'return_value' 	=> 'true',
				'default' 		=> 50,
                'condition' 	=> ['swiper_type' => 'coverflow'],
			]
		);

        $this->add_control(//Swiper sliding effect
			'swiper_slide_effect',
			[
				'label' => esc_html__( 'Sliding Effect', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'default'         => esc_html__('default', 'wooslider-bymdez'),
                    'easein'          => esc_html__('ease', 'wooslider-bymdez'),
                    'linar'           => esc_html__('normal', 'wooslider-bymdez'),
                    'stepend'         => esc_html__('no move', 'wooslider-bymdez'),
                    'stepstart'       => esc_html__('fast move', 'wooslider-bymdez'),
                    'funky'           => esc_html__('funky', 'wooslider-bymdez'),
                    'hammer'          => esc_html__('hammer', 'wooslider-bymdez'),
                    'steps'           => esc_html__('steps', 'wooslider-bymdez'),
                    'light'           => esc_html__('light speed', 'wooslider-bymdez'),
				],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'swiper_type',
                            'operator' => '!==',
                            'value' => 'freemode'
                        ],
                    ]
                ],
                'default' => 'default',
			]
		);

        $this->add_control( //swiper auto height
            'swiper_autoheight',
            [
                'label' => esc_html__('Auto-Height', 'wooslider-bymdez'),
                'description' => esc_html__('after moving each slide, set its height automatically', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'swiper_type',
                            'operator' => '===',
                            'value' => 'default'
                        ],
                        [
                            'name' => 'swiper_type',
                            'operator' => '===',
                            'value' => 'loop'
                        ],
                    ]
                ],
            ]
        );

        $this->add_control( //highlight centered slide
            'swiper_centered_highlight',
            [
                'label' => esc_html__('Highlight centered slide', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'condition' 	=> ['swiper_type' => 'center'],
            ]
        );

        $this->add_control( //rotate centered slide
            'swiper_centered_rotate',
            [
                'label' => esc_html__('rotate centered slide', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'condition' 	=> ['swiper_type' => 'center'],
            ]
        );

        $this->add_control(//Navigation
			'swiper_navs',
			[
				'label' => esc_html__( 'Navigation', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'none'              => esc_html__('None', 'wooslider-bymdez'),
                    'dots'              => esc_html__('Dots', 'wooslider-bymdez'),
                    'fraction'          => esc_html__('Numbers', 'wooslider-bymdez'),
                    'progress'          => esc_html__('Progress bar', 'wooslider-bymdez'),
                    'bullets'           => esc_html__('Bullets', 'wooslider-bymdez'),
				],
                'default' => 'dots'
			]
		);

        $this->add_control(//Navigation Active Style
			'swiper_navs_active',
			[
				'label' => esc_html__( 'Navigation Active Style', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'description' => esc_html__('Just usable on dots and bullets mode', 'wooslider-bymdez'),
				'options' => [
                    'none'            => esc_html__('None', 'wooslider-bymdez'),
                    'grow'            => esc_html__('Grow', 'wooslider-bymdez'),
                    'shrink'          => esc_html__('Shrink', 'wooslider-bymdez'),
                    'sharp'           => esc_html__('Sharp', 'wooslider-bymdez'),
                    'modern'          => esc_html__('Modern', 'wooslider-bymdez'),
                    'moveup'          => esc_html__('Go up', 'wooslider-bymdez'),
                    'movedown'        => esc_html__('Go down', 'wooslider-bymdez'),
                    'alone'           => esc_html__('alone', 'wooslider-bymdez'),
				],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'swiper_navs',
                            'operator' => '===',
                            'value' => 'dots'
                        ],
                        [
                            'name' => 'swiper_navs',
                            'operator' => '===',
                            'value' => 'bullets'
                        ],
                    ]
                ],
                'default' => 'none'
			]
		);

        $this->add_control(//Arrows
			'swiper_arrows',
			[
				'label' => esc_html__( 'Showing arrows', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'none'                    => esc_html__('Never', 'wooslider-bymdez'),
                    'always'                  => esc_html__('Always', 'wooslider-bymdez'),
                    'dynamic'                 => esc_html__('While using', 'wooslider-bymdez'),
                    'desktop'                 => esc_html__('Only on desktop', 'wooslider-bymdez'),
                    'mobile'                  => esc_html__('Only on mobile', 'wooslider-bymdez'),
                    'mobile_tablet'           => esc_html__('Only on mobile & tablets', 'wooslider-bymdez'),
				],
                'default' => 'dynamic'
			]
		);

        $this->add_control( //Autoplay
            'swiper_autoplay',
            [
                'label' => esc_html__('Auto-play', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'description' => esc_html__('Note: In order to further optimize resources and prevent lag, autoplay is not displayed and applied in Elementor editor mode.', 'wooslider-bymdez'),
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control( //Autoplay pause on mouse hover
            'swiper_autoplay_pause',
            [
                'label' => esc_html__('Stop on hover', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'condition' 	=> ['swiper_autoplay' => 'yes'],
            ]
        );

        $this->add_control( //Autoplay delay
			'swiper_autoplay_delay',
			[
				'label' 		=> esc_html__( 'Play speed', 'wooslider-bymdez' ),
				'type'			=> \Elementor\Controls_Manager::NUMBER,
				'description' 	=> esc_html__( 'in milli-seconds', 'wooslider-bymdez' ),
				'return_value' 	=> 'true',
				'default' 		=> 3000,
				'condition' 	=> ['swiper_autoplay' => 'yes'],
			]
		);

        $this->add_control( //Mouse Wheel control
			'swiper_mousewheel',
			[
				'label' 		=> esc_html__( 'Control by Mousewheel', 'wooslider-bymdez' ),
				'type'			=> \Elementor\Controls_Manager::SWITCHER,
				'description' 	=> esc_html__( 'no effects on touch devices', 'wooslider-bymdez' ),
				'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

        $this->add_control( //cursor
			'swiper_cursorgrab',
			[
				'label' 		=> esc_html__( 'Grab Cursor', 'wooslider-bymdez' ),
				'type'			=> \Elementor\Controls_Manager::SWITCHER,
				'description' 	=> esc_html__( 'no effects on touch devices', 'wooslider-bymdez' ),
				'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

        $this->add_control(//wide slides number
			'swiper_slides_wide',
			[
				'label' 	=>	esc_html__( 'Slides in wide monitors', 'wooslider-bymdez' ),
				'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                'description' 	=> esc_html__( 'you can change wide break-point in plugin settings page', 'wooslider-bymdez' ),
				'default' 	=>	4,
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
				'default' 	=>	4,
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

        $this->add_control( //auto mover after construct
			'swiper_automover',
			[
				'label' 		=> esc_html__( 'Auto Mover', 'wooslider-bymdez' ),
				'type'			=> \Elementor\Controls_Manager::SWITCHER,
				'description' 	=> esc_html__( 'Automatically moves to the next slide after construction slider or after ajax calls', 'wooslider-bymdez' ),
				'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

        $this->add_control( //keyboard controll
			'swiper_keyboard',
			[
				'label' 		=> esc_html__( 'Control by Keyboard', 'wooslider-bymdez' ),
				'type'			=> \Elementor\Controls_Manager::SWITCHER,
				'description' 	=> esc_html__( 'This option does not have any effects on touch devices', 'wooslider-bymdez' ),
				'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

        $this->add_control( //Forced-Loop
            'swiper_forcedloop',
            [
                'label' => esc_html__('Forced Loop', 'wooslider-bymdez'),
                'description' => esc_html__('Forces slider slides to loop infinitely but may interfere with some slider effects and modes', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );


        $this->end_controls_section();*/
        
        /*HEADER*/
        $class_controlls->main_controls($this, 3);
        /*$this->start_controls_section(
			'header',
			[
				'label' => esc_html__( 'Header', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' 	=> ['header_enabled' => 'yes'],
			]
		);

        $this->add_control( //Just Refresh the frame
            'refresh_frame4',
            [
                'label' => esc_html__('Update Frame', 'wooslider-bymdez'),
                'description' => esc_html__('no effects on the functionality, just re-fresh the live view', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//def alignment of header elements
			'header_elems_align',
			[
				'label' => esc_html__( 'Header elements alignment', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'center'              => esc_html__('Center', 'wooslider-bymdez'),
                    'right'              => esc_html__('Right', 'wooslider-bymdez'),
					'left'          =>	esc_html__('Left', 'wooslider-bymdez'),
				],
                'default' => 'center',
			]
		);

        $this->add_control( //back to first slide after ajax
            'header_backtofirst_ajax',
            [
                'label' => esc_html__('Back to first-slide after Ajax', 'wooslider-bymdez'),
                'description' => esc_html__('after changing the category, jumps to the first-slide', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'yes',
            ]
        );*/
    
		$repeater = new \Elementor\Repeater();
        $ext_opts = $class_helper->get_terms_mdz('product_cat');
        $class_controlls->main_controls($this, 4, $repeater, $ext_opts);

        /*$repeater->add_control(//header make title
            'header_elems_maketitle',
            [
                'label' => esc_html__('Turn to Title', 'wooslider-bymdez'),
                'description' => esc_html__('When activated turns this button to an static title, and it will not have functionality anymore', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $repeater->add_control(//header make title theme
			'header_elems_maketitle_theme',
			[
				'label' => esc_html__( 'Title Theme', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'theme-1'              =>   esc_html__('Theme-1', 'wooslider-bymdez'),
                    'theme-2'              =>   esc_html__('Theme-2', 'wooslider-bymdez'),
					'theme-3'              =>	esc_html__('Theme-3', 'wooslider-bymdez'),
                    'theme-4'              =>	esc_html__('Theme-4', 'wooslider-bymdez'),
                    'theme-5'              =>	esc_html__('Theme-5', 'wooslider-bymdez'),
                    'theme-6'              =>	esc_html__('Theme-6', 'wooslider-bymdez'),
                    'none'                 =>	esc_html__('none', 'wooslider-bymdez'),
				],
                'condition' 	=> ['header_elems_maketitle' => 'yes'],
                'default' => 'theme-1',
			]
		);

		$repeater->add_control(//add or remove header elems
			'header_elems_title', [
				'label' => esc_html__( 'Title', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'cat-name' , 'wooslider-bymdez' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(//Ajax cats
			'header_ajax_cats',
			[
				'label' 		=>	esc_html__( 'Categories', 'wooslider-bymdez' ),
				'type' 			=>	\Elementor\Controls_Manager::SELECT2,
				'description'   =>  esc_html__( 'Pick one cat', 'wooslider-bymdez' ),
				'label_block'	=>  'true',
				'multiple' 	    =>	false,
				'options' 		=>  $this->get_terms_mdz('product_cat'),
			]
		);

        $repeater->add_control( //ajax max number
			'header_products_number',
			[
				'label' 		=> esc_html__( 'Number', 'wooslider-bymdez' ),
				'type'			=> \Elementor\Controls_Manager::NUMBER,
				'return_value' 	=> 'true',
				'default' 		=> 15,
			]
		);

        $repeater->add_control(//def source of ajax products
			'header_products_src',
			[
				'label' => esc_html__( 'Source', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'description'   =>  esc_html__( 'Before using Charged-Products source, please go to the plugin settings page and activate charged-products system', 'wooslider-bymdez' ),
                'label_block'	=>  'true',
				'options' => [
					'news'              => esc_html__('News', 'wooslider-bymdez'),
                    'olds'              => esc_html__('Olds', 'wooslider-bymdez'),
					'featured'          =>	esc_html__('Featured', 'wooslider-bymdez'),
					'sale'              =>	esc_html__('Sale', 'wooslider-bymdez'),
					'bestsellings'		=>	esc_html__('Most sales', 'wooslider-bymdez'),
                    'lowsellings'		=>	esc_html__('Lowest sales', 'wooslider-bymdez'),
                    'lastedited'		=>	esc_html__('Last modified', 'wooslider-bymdez'),
                    'rand'		        =>	esc_html__('random', 'wooslider-bymdez'),
                    'chargeds'		        =>	esc_html__('Charged-Products', 'wooslider-bymdez'),
				],
                'default' => 'news',
			]
		);

        $repeater->add_control(//Ajax stock ? in_stock
			'header_products_stock',
			[
				'label' => esc_html__( 'Stock', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'label_block'	=>  'true',
				'options' => [
					'all'                        => esc_html__('no matter', 'wooslider-bymdez'),
                    'only_instocks'              => esc_html__('only in-stocks', 'wooslider-bymdez'),
					'only_not_instocks'          =>	esc_html__('only outof-stocks', 'wooslider-bymdez'),
				],
                'default' => 'only_instocks',
			]
		);

        $repeater->add_control(//header icons show?
			'header_icons_type',
			[
				'label' => esc_html__( 'showing buttons', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'none'              => esc_html__('No icon', 'wooslider-bymdez'),
                    'elem_icons'        => esc_html__('Elementor icons', 'wooslider-bymdez'),
                    'custom_icons'      => esc_html__('Your icon', 'wooslider-bymdez'),
				],
                'default' => 'none'
			]
		);

        $repeater->add_control(//sefl elem icons selector
			'header_icons_select',
			[
				'label' => esc_html__( 'Choose icon', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::ICONS,
                'condition' 	=> ['header_icons_type' => 'elem_icons'],
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
			]
		);

        $repeater->add_control(//sefl elem icons color
			'header_icons_color',
			[
				'label' => esc_html__( 'icon color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'condition' 	=> ['header_icons_type' => 'elem_icons'],
				'default' => '#000',
			]
		);

        $repeater->add_control(//custom icon img selector
			'header_iconscustom_select',
			[
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => esc_html__( 'Choose your icon', 'wooslider-bymdez' ),
                'description'   =>  esc_html__( 'prefered formats: svg & webp', 'wooslider-bymdez' ),
                'condition'  => ['header_icons_type' => 'custom_icons'],
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
			]
		);

        $repeater->add_control( //custom icon img width
			'header_iconscustom_width',
			[
				'label' 		=> esc_html__( 'width', 'wooslider-bymdez' ),
				'type'			=> \Elementor\Controls_Manager::NUMBER,
                'description'   =>  esc_html__( 'in pixels (px)', 'wooslider-bymdez' ),
                'condition'  => ['header_icons_type' => 'custom_icons'],
				'return_value' 	=> 'true',
				'default' 		=> 45,
			]
		);

        $repeater->add_control(//text and icon alignment
			'header_texticon_alginment',
			[
				'label' => esc_html__( 'text & icon position', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'label_block'	=>  'true',
				'options' => [
                    'dir1'                 => esc_html__('default', 'wooslider-bymdez'),
                    'dir2'                 => esc_html__('icon left-text right', 'wooslider-bymdez'),
                    'dir3'                 => esc_html__('icon up-text down', 'wooslider-bymdez'),
                    'dir4'                 => esc_html__('icon down-text up', 'wooslider-bymdez'),
				],
                'default' => 'dir1',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'header_icons_type',
                            'operator' => '===',
                            'value' => 'elem_icons'
                        ],
                        [
                            'name' => 'header_icons_type',
                            'operator' => '===',
                            'value' => 'custom_icons'
                        ]
                    ]
                ]
			]
		);

        $repeater->add_control(//ajax btn text color
			'header_text_color',
			[
				'label' => esc_html__( 'text color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#000',
			]
		);

        $repeater->add_control( //header allcats btn
            'header_allcats_show',
            [
                'label' => esc_html__('Show see all slide?', 'wooslider-bymdez'),
                'description' => esc_html__('adds and extra slide to the end of slider', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $repeater->add_control(//header see all cats title
			'header_allcats_title', [
				'label' => esc_html__( 'Title (see all)', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'condition' 	=> ['header_allcats_show' => 'yes'],
				'default' => esc_html__( 'see all' , 'wooslider-bymdez' ),
				'label_block' => true,
			]
		);
        $repeater->add_control(//header see all cats title color
			'header_allcats_titlecolor',
			[
				'label' => esc_html__( 'Title color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'condition' 	=> ['header_allcats_show' => 'yes'],
                'default' => '#000',
			]
		);

        $repeater->add_control(//header see all cats background
			'header_allcats_backtype',
			[
				'label' => esc_html__( 'Background type', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'condition' 	=> ['header_allcats_show' => 'yes'],
				'options' => [
                    'color'             => esc_html__('color', 'wooslider-bymdez'),
                    'wallpaper'        => esc_html__('picture', 'wooslider-bymdez'),
				],
                'default' => 'color'
			]
		);

        $repeater->add_control(//header see all cats background color
			'header_allcats_backcolor',
			[
				'label' => esc_html__( 'Background color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'header_allcats_show',
                            'operator' => '===',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'header_allcats_backtype',
                            'operator' => '===',
                            'value' => 'color'
                        ]
                    ]
                ],
                'default' => '#fff',
			]
		);

        $repeater->add_control(//header see all cats background pic
			'header_allcats_backimg',
			[
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => esc_html__( 'Choose a pic', 'wooslider-bymdez' ),
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'header_allcats_show',
                            'operator' => '===',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'header_allcats_backtype',
                            'operator' => '===',
                            'value' => 'wallpaper'
                        ]
                    ]
                ],
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
			]
		);

        $repeater->add_control(
			'header_allcats_url',
			[
				'label' => esc_html__( 'URL', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::URL,
                'condition' 	=> ['header_allcats_show' => 'yes'],
				'placeholder' => esc_html__( 'https://your-site.com/category/test', 'wooslider-bymdez' ),
			]
		);

        $repeater->add_control( //header bg changer on or?
            'header_bgchanger',
            [
                'label' => esc_html__('Header bg-color change?', 'wooslider-bymdez'),
                'description' => esc_html__('After Ajax, change the back-color of the header based on your design', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'No', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'yes',
            ]
        );

        $repeater->add_control(//header see all cats title color
			'header_bgchanger_color',
			[
				'label' => esc_html__( 'Choose a color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'condition' 	=> ['header_bgchanger' => 'yes'],
                'default' => '#52ff68',
			]
		);


		$this->add_control(//the main repeater
			'header_elements',
			[
				'label' => esc_html__( 'Header Buttons', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'header_elems_title' => esc_html__( 'Category-1' , 'wooslider-bymdez' ),
                        'header_icons_type' => 'elem_icons',
                        'header_icons_select' => [
                            'value' => 'fas fa-heart',
                            'library' => 'solid',
                        ],
					],
                    [
						'header_elems_title' => esc_html__( 'star-cat', 'wooslider-bymdez' ),
                        'header_icons_type' => 'elem_icons',
                        'header_icons_select' => [
                            'value' => 'fas fa-star',
                            'library' => 'solid',
                        ],
                        'header_texticon_alginment' => 'dir3',
					],
                    [
						'header_elems_title' => esc_html__( 'your-cat', 'wooslider-bymdez' ),
                        'header_icons_type' => 'elem_icons',
                        'header_icons_select' => [
                            'value' => 'fas fa-heart',
                            'library' => 'solid',
                        ],
                        'header_texticon_alginment' => 'dir2',
					],
				],
			]
		);

		$this->end_controls_section();*/

        /*Not Found Massage*/
        $class_controlls->main_controls($this, 5);
        /*$this->start_controls_section(
			'notfound',
			[
				'label' => esc_html__( 'Not Found Message', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control( //show message ?
            'notfound_show',
            [
                'label' => esc_html__('Show not found message ?', 'wooslider-bymdez'),
                'description' => esc_html__('Shows a nothing found message when the slider could not find any products to show', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'yes',
            ]
        );

        $this->add_control(//not found Txt
			'notfound_txt', [
				'label' => esc_html__( 'Message Body', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'condition' 	=> ['notfound_show' => 'yes'],
				'default' => esc_html__( 'Nothing found to show!' , 'wooslider-bymdez' ),
				'label_block' => true,
			]
		);

        $this->add_control(//not found btn txt
			'notfound_btn_txt', [
				'label' => esc_html__( 'Button text', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'condition' 	=> ['notfound_show' => 'yes'],
				'default' => esc_html__( 'Go Shop' , 'wooslider-bymdez' ),
				'label_block' => true,
			]
		);

        $this->add_control(//not found btn url
			'notfound_btn_url',
			[
				'label' => esc_html__( 'Button Link', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::URL,
                'description' => esc_html__('Link this button to your shop page if you dont want to loose the customer', 'wooslider-bymdez'),
                'condition' 	=> ['notfound_show' => 'yes'],
				'placeholder' => esc_html__( 'https://your-site.com/shop/', 'wooslider-bymdez' ),
			]
		);

        $this->add_control(//not found btn classes
			'notfound_btn_classes', 
            [
				'label' => esc_html__( 'Css clases (button)', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__('woodmart-btn wd-primary-btn', 'wooslider-bymdez'),
                'description' => esc_html__('use your theme buttons css clases (seperate with *space*)', 'wooslider-bymdez'),
				'default' => 'woosbymdz-404btns',
                'condition' 	=> ['notfound_show' => 'yes'],
				'label_block' => true,
			]
		);

        $this->add_control(//not found block space form the top
			'notfound_space_top',
			[
				'label' 		=> esc_html__( 'Space from the top', 'wooslider-bymdez' ),
				'type'			=> \Elementor\Controls_Manager::NUMBER,
                'description'   =>  esc_html__( 'in pixels (px)', 'wooslider-bymdez' ),
                'condition'  => ['notfound_show' => 'yes'],
				'return_value' 	=> 'true',
				'default' 		=> 65,
			]
		);

        $this->end_controls_section();*/

        $class_controlls->main_controls($this, 6);
        /*$this->start_controls_section(//404 content
			'404_content',
			[
				'label' => esc_html__( '404', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' 	=> ['showonly_tologgedin' => 'yes'],
			]	
		);

        $this->add_control( //Just Refresh the frame
            'refresh_frame5',
            [
                'label' => esc_html__('Update Frame', 'wooslider-bymdez'),
                'description' => esc_html__('no effects on the functionality, just re-fresh the live view', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'refresh', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//404 title
			'404_title', [
				'label' => esc_html__( 'Title (404)', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'description' => esc_html__('All contents of this section will be shown to users who not logged-in', 'wooslider-bymdez'),
				'default' => esc_html__( 'We are so sorry!' , 'wooslider-bymdez' ),
				'label_block' => true,
			]
		);

        $this->add_control(//404 desc
			'404_desc',
			[
				'label' => esc_html__( 'Description', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'You are not able to see these products at the moment, please log-in or register', 'wooslider-bymdez' ),
				'placeholder' => esc_html__( 'write here!', 'wooslider-bymdez' ),
			]
		);

        $this->add_control( //404 login btn
            '404_loginbtn',
            [
                'label' => esc_html__('Login button', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'yes',
            ]
        );

        $this->add_control(//404 login-btn text
			'404_loginbtn_text', 
            [
				'label' => esc_html__( 'Login text', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Login' , 'wooslider-bymdez' ),
                'condition' 	=> ['404_loginbtn' => 'yes'],
				'label_block' => true,
			]
		);

        $this->add_control(//404 login-btn url
			'404_loginbtn_url',
			[
				'label' => esc_html__( 'URL (login)', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::URL,
                'condition' 	=> ['404_loginbtn' => 'yes'],
				'placeholder' => esc_html__( 'https://your-site.com/login', 'wooslider-bymdez' ),
			]
		);

        $this->add_control(//404 login-btn css classes
			'404_loginbtn_class', 
            [
				'label' => esc_html__( 'Css clases (login)', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__('woodmart-btn wd-primary-btn', 'wooslider-bymdez'),
                'description' => esc_html__('use your theme buttons css clases (seperate with *space*)', 'wooslider-bymdez'),
				'default' => 'woosbymdz-404btns',
                'condition' 	=> ['404_loginbtn' => 'yes'],
				'label_block' => true,
			]
		);

        $this->add_control( //404 register btn
            '404_registerbtn',
            [
                'label' => esc_html__('Register button', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//404 register-btn text
			'404_registerbtn_text', 
            [
				'label' => esc_html__( 'Register text', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Register' , 'wooslider-bymdez' ),
                'condition' 	=> ['404_registerbtn' => 'yes'],
				'label_block' => true,
			]
		);

        $this->add_control(//404 register-btn url
			'404_registerbtn_url',
			[
				'label' => esc_html__( 'URL (register)', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::URL,
                'condition' 	=> ['404_registerbtn' => 'yes'],
				'placeholder' => esc_html__( 'https://your-site.com/login', 'wooslider-bymdez' ),
			]
		);

        $this->add_control(//404 register-btn css classes
			'404_registerbtn_class', 
            [
				'label' => esc_html__( 'Css clases (register)', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__('woodmart-btn wd-primary-btn', 'wooslider-bymdez'),
                'description' => esc_html__('use your theme buttons css clases (seperate with *space*', 'wooslider-bymdez'),
				'default' => 'woosbymdz-404btns',
                'condition' 	=> ['404_registerbtn' => 'yes'],
				'label_block' => true,
			]
		);

        $this->end_controls_section();*/

        $class_controlls->main_controls($this, 7);
        /*$this->start_controls_section(//Advanced Options
			'adv_options',
			[
				'label' => esc_html__( 'Advanced', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]	
		);

        $this->add_control( //Header Ajax cache
            'adv_ajax_cache',
            [
                'label' => esc_html__('Cache Header Ajax Requests', 'wooslider-bymdez'),
                'description' => esc_html__('Header Slider caches each product load request locally, minimizing your server usage for future repeat requests.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'condition' 	=> ['header_enabled' => 'yes'],
            ]
        );

        $this->add_control( //Pros Reseter
            'adv_pros_reset',
            [
                'label' => esc_html__('Reset Products Button', 'wooslider-bymdez'),
                'description' => esc_html__('Displaying this button gives the user the ability to reset the products inside the slider to the initial products ​​at any time.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'condition' 	=> ['header_enabled' => 'yes'],
            ]
        );

        $this->add_control(//Pros Reseter pos
			'adv_pros_reset_pos',
			[
				'label' => esc_html__( 'Button Position', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'description' => esc_html__('Changes the position of the product reset button', 'wooslider-bymdez'),
				'options' => [
					'default'              => esc_html__('Default', 'wooslider-bymdez'),
                    'topright'              => esc_html__('Top-Right', 'wooslider-bymdez'),
                    'topleft'              => esc_html__('Top-Left', 'wooslider-bymdez'),
                    'bottomright'              => esc_html__('Bottom-Right', 'wooslider-bymdez'),'bottomleft'              => esc_html__('Bottom-Left', 'wooslider-bymdez'),
                    'bottommid'              => esc_html__('Bottom-Middle', 'wooslider-bymdez'),
                    'man'              => esc_html__('Manual', 'wooslider-bymdez'),
				],
                'default' => 'default',
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'header_enabled',
                            'operator' => '===',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'adv_pros_reset',
                            'operator' => '===',
                            'value' => 'yes'
                        ]
                    ]
                ],
			]
		);

        $this->add_responsive_control(//Pros Reseter pos manual
            'adv_pros_reset_pos2', 
            [
            'label' => esc_html__( 'Button Position', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'description' => esc_html__('To see and set button position manually, click-on one of the header buttons', 'wooslider-bymdez'),
            'size_units' => [ 'px', 'em', '%' ],
			'placeholder' => [
				'top' => '',
				'right' => '',
				'bottom' => 'auto',
				'left' => 'auto',
			],
            'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'header_enabled',
                        'operator' => '===',
                        'value' => 'yes'
                    ],
                    [
                        'name' => 'adv_pros_reset',
                        'operator' => '===',
                        'value' => 'yes'
                    ],
                    [
                        'name' => 'adv_pros_reset_pos',
                        'operator' => '===',
                        'value' => 'man'
                    ]
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-reseter-button' => 'top: {{TOP}}{{UNIT}} '.$all_important.'; right: {{RIGHT}}{{UNIT}} '.$all_important.';',
            ],
        ]);

        $this->add_control(//Pros Reseter icon
			'adv_pros_reset_icon',
			[
				'label' => esc_html__( 'Choose icon', 'wooslider-bymdez' ),
                'description' => esc_html__('Choose a suitable icon for the reset button', 'wooslider-bymdez'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-history',
					'library' => 'solid',
				],
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'header_enabled',
                            'operator' => '===',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'adv_pros_reset',
                            'operator' => '===',
                            'value' => 'yes'
                        ]
                    ]
                ],
			]
		);

        $this->add_control(//Pros Reseter tooltip text
			'adv_pros_reset_txt', 
            [
				'label' => esc_html__( 'Tooltip Text', 'wooslider-bymdez' ),
                'description' => esc_html__('Help text for product reset button', 'wooslider-bymdez'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Reset Products' , 'wooslider-bymdez' ),
				'label_block' => true,
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'header_enabled',
                            'operator' => '===',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'adv_pros_reset',
                            'operator' => '===',
                            'value' => 'yes'
                        ]
                    ]
                ]
			]
		);

        $this->add_control(//Pros Reseter tooltip pos
			'adv_pros_reset_txt_pos',
			[
				'label' => esc_html__( 'Tooltip Position', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'description' => esc_html__('Changes the position of the product reset button tooltip text', 'wooslider-bymdez'),
				'options' => [
					'left'              => esc_html__('Left', 'wooslider-bymdez'),
                    'right'              => esc_html__('Right', 'wooslider-bymdez'),
				],
                'default' => 'left',
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'header_enabled',
                            'operator' => '===',
                            'value' => 'yes'
                        ],
                        [
                            'name' => 'adv_pros_reset',
                            'operator' => '===',
                            'value' => 'yes'
                        ]
                    ]
                ],
			]
		);


        $this->add_control( //infinite slider
            'adv_infinital',
            [
                'label' => esc_html__('Infinite Slider (Experimental)', 'wooslider-bymdez'),
                'description' => esc_html__('By activating this option, the slider products will be loaded by Ajax, and in fact the slider will have no end!.---Note: Only works when you want to show more products form some product-categories CAN-NOT load more products from Yoast-Main cat/upsells/crossells/related/recently_viewed products.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//infinite slider Mode
			'adv_infinital_mode',
			[
				'label' => esc_html__( 'Methode', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'description' => esc_html__('Determine how the infinity slider works.Note: If you use slide show mode or both mode, to adjust the text and view of the last slide (load-more-products) in the products section, activate the `See all button` and change the settings there.', 'wooslider-bymdez'),
				'options' => [
					'nextarrow'              => esc_html__('Only on Next Arrow', 'wooslider-bymdez'),
                    'slide'              => esc_html__('Only Extra Slide', 'wooslider-bymdez'),
                    'both'              => esc_html__('Next Arrow & Extra Slide', 'wooslider-bymdez'),
				],
                'default' => 'nextarrow',
                'condition' 	=> ['adv_infinital' => 'yes'],
			]
		);

        $this->add_control(//infinite slider number to load
			'adv_infinital_num',
			[
				'label' 		=> esc_html__( 'Products Number', 'wooslider-bymdez' ),
				'type'			=> \Elementor\Controls_Manager::NUMBER,
                'description'   =>  esc_html__( 'Select the number of products to reload and add to the slider', 'wooslider-bymdez' ),
				'return_value' 	=> 'true',
				'default' 		=> 8,
                'condition' 	=> ['adv_infinital' => 'yes'],
			]
		);

        $this->add_control(//infinite slider not-found txt
			'adv_infinital_txt', 
            [
				'label' => esc_html__( 'Not Found message-text', 'wooslider-bymdez' ),
                'description' => esc_html__('When there are no products to display, it informs the user by a message', 'wooslider-bymdez'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'No More Products Found!' , 'wooslider-bymdez' ),
				'label_block' => true,
                'condition' 	=> ['adv_infinital' => 'yes'],
			]
		);

        $this->end_controls_section();*/

        $class_controlls->main_controls($this, 9);//Grid Layout

        /*------------*/
        /*TAB_STYLE*/
        /*------------*/
        $class_controlls->style_controls($this, 1);

        /*$this->start_controls_section(//woosbymdz Main Con
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
				'selector' => '{{WRAPPER}} .woosbymdz-carousel-con',
			]
		);

        $this->add_control( //main con transparent bg
            'maincon_style_bgtransparent',
            [
                'label' => esc_html__('No BG', 'wooslider-bymdez'),
                'description' => esc_html__('Fades the background', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_responsive_control(//main-con max-width
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
                '{{WRAPPER}} .woosbymdz-carousel-con' => 'max-width: {{SIZE}}{{UNIT}} '.$all_important.';',
            ],
        ]);

        $this->add_responsive_control(//main-con min-height
            'maincon_style_minheight', 
            [
            'label' => esc_html__( 'Height (Min-Height)', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 3000,
                ],
            ],
            'size_units' => [ 'px' ],
            'default' => [
                'size' => '355',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-carousel-con' => 'min-height: {{SIZE}}{{UNIT}} '.$all_important.';',
            ],
        ]);

        $this->add_responsive_control(//main-con padding
            'maincon_style_padding', 
            [
            'label' => esc_html__( 'Padding', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-carousel-con' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
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
                '{{WRAPPER}} .woosbymdz-carousel-con' => 'margin-top: {{TOP}}{{UNIT}} '.$all_important.'; margin-bottom: {{BOTTOM}}{{UNIT}} '.$all_important.';',
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
				'selector' => '{{WRAPPER}} .woosbymdz-carousel-con',
			]
		);

        $this->add_control(
			'maincon_style_borderradius',
			[
				'label' => esc_html__( 'Border-Radius', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .woosbymdz-carousel-con' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
				],
			]
		);

        $this->add_control(//main-con overflow
            'maincon_style_overflow',
            [
                'label' => esc_html__('Overflow Visible', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'description' => esc_html__('Makes Main-Container overflow property visible', 'wooslider-bymdez'),
                'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );
	
		$this->end_controls_section();*/

        $class_controlls->style_controls($this, 2);
        /*$this->start_controls_section(//woosbymdz Header
			'header_style',
			[
				'label' => esc_html__( 'Header', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' 	=> ['header_enabled' => 'yes'],
			]
		);

        $this->add_group_control(//header bg
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'header_style_bg',
				'label' => esc_html__( 'Background', 'wooslider-bymdez' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .woosbymdz-header',
			]
		);

        $this->add_responsive_control(//header height
            'header_style_height', 
            [
            'label' => esc_html__( 'Height (px)', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                ],
            ],
            'size_units' => [ 'px' ],
            'default' => [
                'size' => '52',
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-header' => 'height: {{SIZE}}{{UNIT}} '.$all_important.';',
            ],
        ]);

        $this->add_responsive_control(//header scroll-bar height
            'header_style_scrollbar_height', 
            [
            'label' => esc_html__( 'Scroll-bar height (%)', 'wooslider-bymdez' ),
            'description' => esc_html__('shows the scroll bar if header elements width are more than the maximum width of the device', 'wooslider-bymdez'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                '%' => [
                    'min' => 0,
                    'max' => 300,
                ],
            ],
            'size_units' => [ '%' ],
            'default' => [
                'size' => '117',
                'unit' => '%',
            ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-header-wrapper' => 'height: {{SIZE}}{{UNIT}} '.$all_important.';',
            ],
        ]);

        $this->add_responsive_control(//header margin
            'header_style_margin', 
            [
            'label' => esc_html__( 'Margin', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
            ],
        ]);

        $this->add_control( //header hover bg change?
            'header_style_hoverbgchanger',
            [
                'label' => esc_html__('Fade Header on hover', 'wooslider-bymdez'),
                'description' => esc_html__('if you choose no, the header never fades on hover', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'No', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'yes',
            ]
        );
		$this->end_controls_section();*/

        $class_controlls->style_controls($this, 3);
        /*$this->start_controls_section(//woosbymdz Header Elements
			'header_elsstyle',
			[
				'label' => esc_html__( 'Header Buttons', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' 	=> ['header_enabled' => 'yes'],
			]
		);

        $this->add_group_control(//header els typo
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'header_elsstyle_typo',
                'label' => esc_html__( 'Header elements typography', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} .woosbymdz-header-divs span',
			]
		);

        $this->add_control(//header els txt color
			'header_elsstyle_color',
			[
				'label' => esc_html__( 'Elements text color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
					'{{WRAPPER}} .woosbymdz-header-divs span, {{WRAPPER}} .woosbymdz-header-divs:before, {{WRAPPER}} .woosbymdz-header-divs:after' => 'color: {{VALUE}} !important;border-color: {{VALUE}} '.$all_important.';',
				],
			]
		);

        $this->add_control(//header els bg color on hover
			'header_elsstyle_bgonhover',
			[
				'label' => esc_html__( 'On hover BG', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
					'{{WRAPPER}} .woosbymdz-header-divs:hover' => 'background-color: {{VALUE}} '.$all_important.'',
				],
			]
		);

        $this->add_control(//header els shadow color on hover
			'header_elsstyle_shadowonhover',
			[
				'label' => esc_html__( 'On hover shadow', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
					'{{WRAPPER}} .woosbymdz-header-divs:hover' => 'box-shadow: 0px 5px 10px -4px {{VALUE}} '.$all_important.';',
				],
			]
		);

        $this->add_control(//header els bg color on active
			'header_elsstyle_bgonactive',
			[
				'label' => esc_html__( 'Active BG', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
					'{{WRAPPER}} .woosbymdz-header-divs-active' => 'background-color: {{VALUE}} '.$all_important.'',
				],
			]
		);

        $this->add_control(//header els shadow color on active
			'header_elsstyle_shadowonactive',
			[
				'label' => esc_html__( 'Active shadow', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
					'{{WRAPPER}} .woosbymdz-header-divs-active' => 'box-shadow: 0px 5px 10px -4px {{VALUE}} '.$all_important.';',
				],
			]
		);

        $this->end_controls_section();*/

        $class_controlls->style_controls($this, 4);
        /*$this->start_controls_section(//woosbymdz products wrapper
			'sliderwrapper_style',
			[
				'label' => esc_html__( 'Products container', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(//products wrapper padding
            'sliderwrapper_style_padding', 
            [
            'label' => esc_html__( 'Padding', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-swiper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
            ],
        ]);

        $this->add_responsive_control(//products wrapper margin
            'sliderwrapper_margin', 
            [
            'label' => esc_html__( 'Margin', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .swiper-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
            ],
        ]);
		

		$this->end_controls_section();*/

        $class_controlls->style_controls($this, 5);
        $class_controlls->style_controls($this, 9);
        $class_controlls->style_controls($this, 10);
        $class_controlls->style_controls($this, 11);
        $class_controlls->style_controls($this, 12);
        $class_controlls->style_controls($this, 13);
        /*$this->start_controls_section(//woosbymdz products
			'products_style',
			[
				'label' => esc_html__( 'Products', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_group_control(//products bg-color
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'products_style_bg',
				'label' => esc_html__( 'Background', 'wooslider-bymdez' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide:not(.woosbymdz-seeallcat)',
			]
		);

        $this->add_responsive_control(//products padding
            'products_padding', 
            [
            'label' => esc_html__( 'Padding', 'wooslider-bymdez' ),
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
                '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide' => 'padding-top: {{TOP}}{{UNIT}} '.$pros_important.'; padding-bottom: {{BOTTOM}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control(//products min-height
            'products_minheight', 
            [
            'label' => esc_html__( 'Products Height (Min-Height)', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'description' => esc_html__('Usefull for Synchronization product-blocks height with each other', 'wooslider-bymdez'),
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
                '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide' => 'min-height: {{SIZE}}{{UNIT}} '.$pros_important.';',
            ],
        ]);

        $this->add_group_control(//products border options
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'products_border',
				'label' => esc_html__( 'Products Border', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide',
			]
		);

        $this->add_responsive_control(//products border-radius
            'products_borderradius', 
            [
            'label' => esc_html__( 'border-radius', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$pros_important.';',
            ],
        ]);

        $this->add_group_control(//products-image bg-color
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'products_imgs_style_bg',
				'label' => esc_html__( 'Image Background', 'wooslider-bymdez' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide img',
			]
		);

        $this->add_responsive_control(//products-image border-radius
            'products_imgs_borderradius', 
            [
            'label' => esc_html__( 'Image border-radius', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$pros_important.';',
            ],
        ]);

        $this->add_responsive_control(//products-image padding
            'products_imgs_padding', 
            [
            'label' => esc_html__( 'Image Padding', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide img' => 'padding-top: {{TOP}}{{UNIT}} '.$pros_important.';padding-left: {{LEFT}}{{UNIT}}'.$pros_important.'; padding-bottom: {{BOTTOM}}{{UNIT}}'.$pros_important.';padding-right: {{RIGHT}}{{UNIT}}'.$pros_important.';',
            ],
        ]);


        $this->add_group_control(//products box-shadow
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'products_boxshadow',
				'label' => esc_html__( 'Products Box-Shadow', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide',
			]
		);

        $this->add_control(//Hidden products Title ?
            'products_hide_title',
            [
                'label' => esc_html__('Hide Products Title', 'wooslider-bymdez'),
                'description' => esc_html__('Upon activation it will hide the product title, if this does not work you may hide them with CSS code', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'No', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//Hidden products cats ?
            'products_hide_cats',
            [
                'label' => esc_html__('Hide Products Categories', 'wooslider-bymdez'),
                'description' => esc_html__('Upon activation it will hide the product categories, if this does not work you may hide them with CSS code', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'No', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//Hidden products Price ?
            'products_hide_price',
            [
                'label' => esc_html__('Hide Products Price', 'wooslider-bymdez'),
                'description' => esc_html__('Upon activation it will hide the product Price, if this does not work you may hide them with CSS code', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'No', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//Hidden products Labels ?
            'products_hide_labels',
            [
                'label' => esc_html__('Hide Products labels', 'wooslider-bymdez'),
                'description' => esc_html__('Upon activation it will hide the product Labels, if this does not work you may hide them with CSS code', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'No', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//Hidden products add-to-cart btn ?
            'products_hide_addbtn',
            [
                'label' => esc_html__('Hide Products Add btn', 'wooslider-bymdez'),
                'description' => esc_html__('Upon activation it will hide the product add-to-cart button, if this does not work you may hide them with CSS code', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'No', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control(//Products Title Color
			'products_style_title_color',
			[
				'label' => esc_html__( 'Products Title Color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_title',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
                'selectors' => [
					'{{WRAPPER}} '.$theme_prod_title.'' => 'color: {{VALUE}} '.$pros_important.'',
                    '{{WRAPPER}} '.$theme_prod_title.' *' => 'color: {{VALUE}} '.$pros_important.'',
				],
			]
		);

        $this->add_group_control(//Products Title BG changer
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'products_style_title_bg',
				'label' => esc_html__( 'Products Title Background', 'wooslider-bymdez' ),
				'types' => [ 'classic', 'gradient' ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_title',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selector' => '{{WRAPPER}} '.$theme_prod_title.'',
                
			]
		);

        $this->add_group_control(//Products Title Typography
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'products_style_title_typo',
                'label' => esc_html__( 'Products Title typography', 'wooslider-bymdez' ),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_title',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selector' => '{{WRAPPER}} '.$theme_prod_title.'',
			]
		);

        $this->add_group_control(//Products Title Shadow
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'products_style_title_shadow',
				'label' => esc_html__( 'Products Title shadow', 'wooslider-bymdez' ),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_title',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selector' => '{{WRAPPER}} '.$theme_prod_title.'',
			]
		);

        $this->add_responsive_control(//Products Title padding
            'products_style_title_padding', 
            [
            'label' => esc_html__( 'Products Title Padding', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} '.$theme_prod_title.'' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$pros_important.';',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'products_hide_title',
                        'operator' => '!==',
                        'value' => 'yes',
                    ],
                ]
            ],
        ]);

        $this->add_control(//Products Title Border-radius
			'products_style_title_borderradius',
			[
				'label' => esc_html__( 'Products Title Border-Radius', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_title',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selectors' => [
					'{{WRAPPER}} '.$theme_prod_title.'' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$pros_important.';',
				],
			]
		);

        $this->add_control(//Products Cats Color
			'products_style_cats_color',
			[
				'label' => esc_html__( 'Products Cats Color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_cats',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
                'selectors' => [
					'{{WRAPPER}} '.$theme_prod_cats.'' => 'color: {{VALUE}} '.$pros_important.'',
					'{{WRAPPER}} '.$theme_prod_cats.' *' => 'color: {{VALUE}} '.$pros_important.'',
				],
			]
		);

        $this->add_group_control(//Products Cats BG changer
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'products_style_cats_bg',
				'label' => esc_html__( 'Products Cats Background', 'wooslider-bymdez' ),
				'types' => [ 'classic', 'gradient' ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_cats',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selector' => '{{WRAPPER}} '.$theme_prod_cats.'',
                
			]
		);

        $this->add_group_control(//Products Cats Typography
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'products_style_cats_typo',
                'label' => esc_html__( 'Products Cats typography', 'wooslider-bymdez' ),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_cats',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selector' => '{{WRAPPER}} '.$theme_prod_cats.'',
			]
		);

        $this->add_group_control(//Products Cats Shadow
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'products_style_cats_shadow',
				'label' => esc_html__( 'Products Cats shadow', 'wooslider-bymdez' ),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_cats',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selector' => '{{WRAPPER}} '.$theme_prod_cats.'',
			]
		);

        $this->add_control(//Products Cats Border-radius
			'products_style_cats_borderradius',
			[
				'label' => esc_html__( 'Products Cats Border-Radius', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_cats',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selectors' => [
					'{{WRAPPER}} '.$theme_prod_cats.'' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$pros_important.';',
				],
			]
		);

        $this->add_control(//Products Price normal Color
			'products_style_price_normal_color',
			[
				'label' => esc_html__( 'Price Normal Color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_price',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
                'selectors' => [
					'{{WRAPPER}} '.$theme_prod_price_normal.'' => 'color: {{VALUE}} '.$pros_important.'',
				],
			]
		);

        $this->add_control(//Products Price del Color
			'products_style_price_del_color',
			[
				'label' => esc_html__( 'Price Offer Color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_price',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
                'selectors' => [
					'{{WRAPPER}} '.$theme_prod_price_del.'' => 'color: {{VALUE}} '.$pros_important.'',
				],
			]
		);

        $this->add_group_control(//Products Price BG changer
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'products_style_price_bg',
				'label' => esc_html__( 'Products Price Background', 'wooslider-bymdez' ),
				'types' => [ 'classic', 'gradient' ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_price',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selector' => '{{WRAPPER}} '.$theme_prod_price.'',
                
			]
		);

        $this->add_control(//Products Price Border-radius
			'products_style_price_borderradius',
			[
				'label' => esc_html__( 'Products Price Border-Radius', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_price',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selectors' => [
					'{{WRAPPER}} '.$theme_prod_price.'' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$pros_important.';',
				],
			]
		);

        $this->add_group_control(//Products Price Typography
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'products_style_price_typo',
                'label' => esc_html__( 'Products Price typography', 'wooslider-bymdez' ),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_price',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selector' => '{{WRAPPER}} '.$theme_prod_price.'',
			]
		);

        $this->add_responsive_control(//Products Price padding
            'products_style_price_padd', 
            [
            'label' => esc_html__( 'Products Price Padding', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} '.$theme_prod_price.'' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$pros_important.';',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'products_hide_price',
                        'operator' => '!==',
                        'value' => 'yes',
                    ],
                ]
            ],
        ]);

        $this->add_responsive_control(//Products Price margin
            'products_style_price_margin', 
            [
            'label' => esc_html__( 'Products Price Margin', 'wooslider-bymdez' ),
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
                '{{WRAPPER}} '.$theme_prod_price.'' => 'margin: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}} '.$pros_important.';',
            ],
        ]);

        $this->add_control(//Product label Absolute Position
            'products_style_label_pos',
            [
                'label' => esc_html__('Products Label Position Absolute', 'wooslider-bymdez'),
                'description' => esc_html__('When activated change products label wrapper position to Absolute, therefore you can change its position freely', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'No', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_labels',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],

            ]
        );

        $this->add_responsive_control(//Products labels position_top
            'products_style_label_position_top', 
            [
            'label' => esc_html__( 'Products-Label Top-Position', 'wooslider-bymdez' ),
            'description' => esc_html__('Do not Enter here the bottom position and if you enter it will not work', 'wooslider-bymdez'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'allowed_dimensions' => 'vertical',
			'placeholder' => [
				'top' => '',
				'right' => 'auto',
				'bottom' => 'auto',
				'left' => 'auto',
			],
            'selectors' => [
                '{{WRAPPER}} '.$theme_prod_label_con.'' => 'top: {{TOP}}{{UNIT}} !important;',
            ],
            'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'products_hide_labels',
                        'operator' => '!==',
                        'value' => 'yes',
                    ],
                    [
                        'name' => 'products_style_label_pos',
                        'operator' => '===',
                        'value' => 'yes',
                    ],
                ]
            ],
        ]);

        $this->add_responsive_control(//Products labels position_bottom
            'products_style_label_position_bottom', 
            [
            'label' => esc_html__( 'Products-Label Bottom-Position', 'wooslider-bymdez' ),
            'description' => esc_html__('Do not Enter here the top position and if you enter it will not work', 'wooslider-bymdez'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'allowed_dimensions' => 'vertical',
			'placeholder' => [
				'top' => 'auto',
				'right' => 'auto',
				'bottom' => '',
				'left' => 'auto',
			],
            'selectors' => [
                '{{WRAPPER}} '.$theme_prod_label_con.'' => 'bottom: {{BOTTOM}}{{UNIT}} !important;',
            ],
            'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'products_hide_labels',
                        'operator' => '!==',
                        'value' => 'yes',
                    ],
                    [
                        'name' => 'products_style_label_pos',
                        'operator' => '===',
                        'value' => 'yes',
                    ],
                ]
            ],
        ]);

        $this->add_responsive_control(//Products labels position_right
            'products_style_label_position_right', 
            [
            'label' => esc_html__( 'Products-Label Right-Position', 'wooslider-bymdez' ),
            'description' => esc_html__('Do not Enter here the left position and if you enter it will not work', 'wooslider-bymdez'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'allowed_dimensions' => 'horizontal',
			'placeholder' => [
				'top' => 'auto',
				'right' => '',
				'bottom' => 'auto',
				'left' => 'auto',
			],
            'selectors' => [
                '{{WRAPPER}} '.$theme_prod_label_con.'' => 'right: {{RIGHT}}{{UNIT}} !important;',
            ],
            'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'products_hide_labels',
                        'operator' => '!==',
                        'value' => 'yes',
                    ],
                    [
                        'name' => 'products_style_label_pos',
                        'operator' => '===',
                        'value' => 'yes',
                    ],
                ]
            ],
        ]);

        $this->add_responsive_control(//Products labels position_left
            'products_style_label_position_left', 
            [
            'label' => esc_html__( 'Products-Label Left-Position', 'wooslider-bymdez' ),
            'description' => esc_html__('Do not Enter here the right position and if you enter it will not work', 'wooslider-bymdez'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'allowed_dimensions' => 'horizontal',
			'placeholder' => [
				'top' => 'auto',
				'right' => 'auto',
				'bottom' => 'auto',
				'left' => '',
			],
            'selectors' => [
                '{{WRAPPER}} '.$theme_prod_label_con.'' => 'left: {{LEFT}}{{UNIT}} !important;',
            ],
            'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'products_hide_labels',
                        'operator' => '!==',
                        'value' => 'yes',
                    ],
                    [
                        'name' => 'products_style_label_pos',
                        'operator' => '===',
                        'value' => 'yes',
                    ],
                ]
            ],
        ]);

        $this->add_group_control(//Products labels BG changer
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'products_style_label_bg',
				'label' => esc_html__( 'Products Label Background', 'wooslider-bymdez' ),
				'types' => [ 'classic', 'gradient' ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_labels',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selector' => '{{WRAPPER}} '.$theme_prod_label_txt.'',
                
			]
		);

        $this->add_responsive_control(//Products labels padding
            'products_style_label_padding', 
            [
            'label' => esc_html__( 'Products Label Padding', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} '.$theme_prod_label_txt.'' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$pros_important.';',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'products_hide_labels',
                        'operator' => '!==',
                        'value' => 'yes',
                    ],
                ]
            ],
        ]);

        $this->add_control(//Products Label Border-radius
			'products_style_label_borderradius',
			[
				'label' => esc_html__( 'Products Label Border-Radius', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_labels',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selectors' => [
					'{{WRAPPER}} '.$theme_prod_label_txt.'' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$pros_important.';',
				],
			]
		);

        $this->add_control(//Products Label txt Color
			'products_style_label_color',
			[
				'label' => esc_html__( 'Products Label Color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_labels',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
                'selectors' => [
					'{{WRAPPER}} '.$theme_prod_label_txt.'' => 'color: {{VALUE}} '.$pros_important.'',
				],
			]
		);

        $this->add_group_control(//Products Label Typography
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'products_style_label_typo',
                'label' => esc_html__( 'Products Label typography', 'wooslider-bymdez' ),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_labels',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selector' => '{{WRAPPER}} '.$theme_prod_label_txt.'',
			]
		);

        $this->add_group_control(//Products Label Box-Shadow
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'products_style_label_boxshadow',
				'label' => esc_html__( 'Products Label Box-Shadow', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} '.$theme_prod_label_txt.'',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_labels',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
			]
		);

        $this->add_control(//Products Add-Btn Color
			'products_style_addbtn_color',
			[
				'label' => esc_html__( 'Products Add Button Color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_addbtn',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
                'selectors' => [
					'{{WRAPPER}} '.$theme_prod_add_btn.'' => 'color: {{VALUE}} '.$pros_important.'',
				],
			]
		);

        $this->add_group_control(//Products Add-Btn BG
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'products_style_addbtn_bg',
				'label' => esc_html__( 'Products Add Button Background', 'wooslider-bymdez' ),
				'types' => [ 'classic', 'gradient' ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_addbtn',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selector' => '{{WRAPPER}} '.$theme_prod_add_btn.'',
                
			]
		);

        $this->add_group_control(//Products Add btn Typography
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'products_style_addbtn_typo',
                'label' => esc_html__( 'Products Add button typography', 'wooslider-bymdez' ),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_addbtn',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selector' => '{{WRAPPER}} '.$theme_prod_add_btn.'',
			]
		);

        $this->add_control(//Products Add-Btn Border-radius
			'products_style_addbtn_borderradius',
			[
				'label' => esc_html__( 'Products Add button Border-Radius', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'products_hide_addbtn',
                            'operator' => '!==',
                            'value' => 'yes',
                        ],
                    ]
                ],
				'selectors' => [
					'{{WRAPPER}} '.$theme_prod_add_btn.'' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$pros_important.';',
				],
			]
		);

        $this->add_responsive_control(//Products Add-Btn padding
            'products_style_addbtn_padding', 
            [
            'label' => esc_html__( 'Products Add button padding', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} '.$theme_prod_add_btn.'' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$pros_important.';',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'products_hide_addbtn',
                        'operator' => '!==',
                        'value' => 'yes',
                    ],
                ]
            ],
        ]);

        $this->add_responsive_control(//Products Add-Btn Margin
            'products_style_addbtn_margin', 
            [
            'label' => esc_html__( 'Products Add button Margin', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'allowed_dimensions' => 'vertical',
			'placeholder' => [
				'top' => '',
				'right' => 'auto',
				'bottom' => '',
				'left' => 'auto',
			],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'products_hide_addbtn',
                        'operator' => '!==',
                        'value' => 'yes',
                    ],
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} '.$theme_prod_add_btn.'' => 'margin-top: {{TOP}}{{UNIT}} '.$pros_important.'; margin-bottom: {{BOTTOM}}{{UNIT}} '.$pros_important.';',
            ],
        ]);

        $this->end_controls_section();*/

        $class_controlls->style_controls($this, 6);
        /*$this->start_controls_section(//arrows style
			'arrows_style',
			[
				'label' => esc_html__( 'Arrows', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'swiper_arrows',
                            'operator' => '===',
                            'value' => 'always'
                        ],
                        [
                            'name' => 'swiper_arrows',
                            'operator' => '===',
                            'value' => 'dynamic'
                        ],
                        [
                            'name' => 'swiper_arrows',
                            'operator' => '===',
                            'value' => 'desktop'
                        ],
                        [
                            'name' => 'swiper_arrows',
                            'operator' => '===',
                            'value' => 'mobile'
                        ],
                        [
                            'name' => 'swiper_arrows',
                            'operator' => '===',
                            'value' => 'mobile_tablet'
                        ]
                    ]
                ]
			]	
		);

        $this->add_control(//Left arrow color
			'arrows_style_leftcolor',
			[
				'label' => esc_html__( 'Left arrow color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#007aff',
                'selectors' => [
					'{{WRAPPER}} .swiper-button-next' => 'color: {{VALUE}} '.$all_important.'',
				],
			]
		);

        $this->add_group_control(//Left arrow text-shadow
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'arrows_style_left_textshadow',
				'label' => esc_html__( 'Left arrow shine', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} .swiper-button-next',
			]
		);

        $this->add_group_control(//Left arrow typo
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'arrows_style_left_typo',
                'label' => esc_html__( 'Left arrow typo', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} .swiper-button-next:after',
			]
		);

        $this->add_control(//right arrow color
			'arrows_style_rightcolor',
			[
				'label' => esc_html__( 'Right arrow color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#007aff',
                'selectors' => [
					'{{WRAPPER}} .swiper-button-prev' => 'color: {{VALUE}} '.$all_important.'',
				],
			]
		);

        $this->add_group_control(//Right arrow text-shadow
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'arrows_style_right_textshadow',
				'label' => esc_html__( 'Right arrow shine', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} .swiper-button-prev',
			]
		);

        $this->add_group_control(//Right arrow typo
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'arrows_style_right_typo',
                'label' => esc_html__( 'Right arrow typo', 'wooslider-bymdez' ),
				'selector' => '{{WRAPPER}} .swiper-button-prev:after',
			]
		);

        $this->add_control(//left arrow bg
			'arrows_style_leftbg',
			[
				'label' => esc_html__( 'Left arrow BG-type', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'none'             => esc_html__('None', 'wooslider-bymdez'),
                    'sharped'        => esc_html__('Sharped', 'wooslider-bymdez'),
                    'ease_sharped'        => esc_html__('ease-sharped', 'wooslider-bymdez'),
                    'circle'        => esc_html__('Round', 'wooslider-bymdez'),
                    'leave1'        => esc_html__('Leaves-1', 'wooslider-bymdez'),
                    'leave2'        => esc_html__('Leaves-2', 'wooslider-bymdez'),
				],
                'default' => 'none'
			]
		);
        $this->add_group_control(//left arrow bg color
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'arrows_style_leftbg_selector',
				'label' => esc_html__( 'Left arrow bg-color', 'wooslider-bymdez' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} .swiper-button-next',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'arrows_style_leftbg',
                            'operator' => '===',
                            'value' => 'sharped'
                        ],
                        [
                            'name' => 'arrows_style_leftbg',
                            'operator' => '===',
                            'value' => 'ease_sharped'
                        ],
                        [
                            'name' => 'arrows_style_leftbg',
                            'operator' => '===',
                            'value' => 'circle'
                        ],
                        [
                            'name' => 'arrows_style_leftbg',
                            'operator' => '===',
                            'value' => 'leave1'
                        ],
                        [
                            'name' => 'arrows_style_leftbg',
                            'operator' => '===',
                            'value' => 'leave2'
                        ]
                    ]
                ]
			]
		);

        $this->add_responsive_control(//left arrow padding
            'arrows_style_left_padding', 
            [
            'label' => esc_html__( 'Padding Left-Arrow', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'arrows_style_leftbg',
                        'operator' => '===',
                        'value' => 'sharped'
                    ],
                    [
                        'name' => 'arrows_style_leftbg',
                        'operator' => '===',
                        'value' => 'ease_sharped'
                    ],
                    [
                        'name' => 'arrows_style_leftbg',
                        'operator' => '===',
                        'value' => 'circle'
                    ],
                    [
                        'name' => 'arrows_style_leftbg',
                        'operator' => '===',
                        'value' => 'leave1'
                    ],
                    [
                        'name' => 'arrows_style_leftbg',
                        'operator' => '===',
                        'value' => 'leave2'
                    ]
                ]
            ]
        ]);

        $this->add_responsive_control(
            'arrows_style_left_margin', 
            [
            'label' => esc_html__( 'Margin Left Arrow', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
            ],
        ]);

        $this->add_control(//Right arrow bg
			'arrows_style_rightbg',
			[
				'label' => esc_html__( 'Right arrow BG-type', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'none'             => esc_html__('None', 'wooslider-bymdez'),
                    'sharped'        => esc_html__('Sharped', 'wooslider-bymdez'),
                    'ease_sharped'        => esc_html__('ease-sharped', 'wooslider-bymdez'),
                    'circle'        => esc_html__('Round', 'wooslider-bymdez'),
                    'leave1'        => esc_html__('Leaves-1', 'wooslider-bymdez'),
                    'leave2'        => esc_html__('Leaves-2', 'wooslider-bymdez'),
				],
                'default' => 'none'
			]
		);

        $this->add_group_control(//right arrow bg color
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'arrows_style_rightbg_selector',
				'label' => esc_html__( 'Left arrow bg-color', 'wooslider-bymdez' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} .swiper-button-prev',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'arrows_style_rightbg',
                            'operator' => '===',
                            'value' => 'sharped'
                        ],
                        [
                            'name' => 'arrows_style_rightbg',
                            'operator' => '===',
                            'value' => 'ease_sharped'
                        ],
                        [
                            'name' => 'arrows_style_rightbg',
                            'operator' => '===',
                            'value' => 'circle'
                        ],
                        [
                            'name' => 'arrows_style_rightbg',
                            'operator' => '===',
                            'value' => 'leave1'
                        ],
                        [
                            'name' => 'arrows_style_rightbg',
                            'operator' => '===',
                            'value' => 'leave2'
                        ]
                    ]
                ]
			]
		);

        $this->add_responsive_control(//right arrow padding
            'arrows_style_right_padding', 
            [
            'label' => esc_html__( 'Padding Right-Arrow', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
            ],
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => 'arrows_style_rightbg',
                        'operator' => '===',
                        'value' => 'sharped'
                    ],
                    [
                        'name' => 'arrows_style_rightbg',
                        'operator' => '===',
                        'value' => 'ease_sharped'
                    ],
                    [
                        'name' => 'arrows_style_rightbg',
                        'operator' => '===',
                        'value' => 'circle'
                    ],
                    [
                        'name' => 'arrows_style_rightbg',
                        'operator' => '===',
                        'value' => 'leave1'
                    ],
                    [
                        'name' => 'arrows_style_rightbg',
                        'operator' => '===',
                        'value' => 'leave2'
                    ]
                ]
            ]
        ]);

        $this->add_responsive_control(//right arrow margin
            'arrows_style_right_margin', 
            [
            'label' => esc_html__( 'Margin Right Arrow', 'wooslider-bymdez' ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-prev' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
            ],
        ]);

        $this->add_control(//Arrows Hover effect
			'arrows_style_hover',
			[
				'label' => esc_html__( 'Arrows hover effect', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
                    'none'             => esc_html__('None', 'wooslider-bymdez'),
                    'out_mover'        => esc_html__('Out-mover', 'wooslider-bymdez'),
                    'in_mover'        => esc_html__('In-mover', 'wooslider-bymdez'),
                    'bigger'        => esc_html__('Approaching', 'wooslider-bymdez'),
                    'shrink'        => esc_html__('Shrink', 'wooslider-bymdez'),
                    'highlight'        => esc_html__('High-Light', 'wooslider-bymdez'),
				],
                'default' => 'out_mover'
			]
		);
		
        $this->end_controls_section();*/


        $class_controlls->style_controls($this, 7);
        /*$this->start_controls_section(//pagination style
			'pags_style',
			[
				'label' => esc_html__( 'Pagination', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'swiper_navs',
                            'operator' => '===',
                            'value' => 'dots'
                        ],
                        [
                            'name' => 'swiper_navs',
                            'operator' => '===',
                            'value' => 'fraction'
                        ],
                        [
                            'name' => 'swiper_navs',
                            'operator' => '===',
                            'value' => 'progress'
                        ],
                        [
                            'name' => 'swiper_navs',
                            'operator' => '===',
                            'value' => 'bullets'
                        ],
                    ]
                ]
			]	
		);

        $this->add_control(//pags color ِ Dots or Bullets
			'pags_style_dotscolor',
			[
				'label' => esc_html__( 'Dots color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#007aff',
                'selectors' => [
					'{{WRAPPER}} .swiper-pagination .swiper-pagination-bullet' => 'background: {{VALUE}} '.$all_important.'',
				],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'swiper_navs',
                            'operator' => '===',
                            'value' => 'dots'
                        ],
                        [
                            'name' => 'swiper_navs',
                            'operator' => '===',
                            'value' => 'bullets'
                        ],
                    ]
                ]
			]
		);

        $this->add_control(//pags color Fraction
			'pags_style_fractioncolor',
			[
				'label' => esc_html__( 'Numbers color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#007aff',
                'condition' 	=> ['swiper_navs' => 'fraction'],
                'selectors' => [
					'{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}} '.$all_important.'',
				],
			]
		);
 
        $this->add_group_control(//pags Fraction text-shadow
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'pags_style_fraction_textshadow',
				'label' => esc_html__( 'Numbers shine', 'wooslider-bymdez' ),
                'condition' 	=> ['swiper_navs' => 'fraction'],
				'selector' => '{{WRAPPER}} .swiper-pagination span, swiper-pagination',
			]
		);

        $this->add_group_control(//pags Fraction typo
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'pags_style_fractiontypo',
                'label' => esc_html__( 'Numbers typo', 'wooslider-bymdez' ),
                'condition' 	=> ['swiper_navs' => 'fraction'],
				'selector' => '{{WRAPPER}} .swiper-pagination span, swiper-pagination',
			]
		);

        $this->add_control(//pags color Progress
			'pags_style_progresscolor',
			[
				'label' => esc_html__( 'Progress bar color', 'wooslider-bymdez' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#007aff',
                'condition' 	=> ['swiper_navs' => 'progress'],
                'selectors' => [
					'{{WRAPPER}} .swiper-pagination-progressbar .swiper-pagination-progressbar-fill' => 'background: {{VALUE}} '.$all_important.'',
				],
			]
		);

        $this->add_control( //pags direction
            'pags_style_fraction_direction',
            [
                'label' => esc_html__('Change direction', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'No', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
                'condition' 	=> ['swiper_navs' => 'fraction'],
            ]
        );

        $this->add_group_control(//pags bg
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'pags_style_bg_selector',
				'label' => esc_html__( 'Background ', 'wooslider-bymdez' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} .swiper-pagination',
			]
		);

        $this->add_control( //pags margin just on swiper coveflow type
			'pags_style_margin',
			[
				'label' 		=> esc_html__( 'Margin', 'wooslider-bymdez' ),
				'type'			=> \Elementor\Controls_Manager::NUMBER,
                'description'   =>  esc_html__( 'negative values do the up-margin & positive values do the down-margin', 'wooslider-bymdez' ),
                'condition' 	=> ['swiper_type' => 'coverflow'],
				'return_value' 	=> 'true',
				'default' 		=> 0,
			]
		);

        $this->end_controls_section();*/

        $class_controlls->style_controls($this, 8);

        /*$this->start_controls_section(//404 styles
			'404_style',
			[
				'label' => esc_html__( '404', 'wooslider-bymdez' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' 	=> ['showonly_tologgedin' => 'yes'],
			]	
		);

        $this->add_control(//custom icon img selector
			'404_style_bg',
			[
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => esc_html__( 'Choose a proper picture', 'wooslider-bymdez' ),
                'description'   =>  esc_html__( 'Best picture ratio: 1024:355', 'wooslider-bymdez' ),
				'default' => [
					'url' => WOOSBYMDZ_URL . 'front/img/woosbymdz-notfound.webp',
                ],
			]
		);

        $this->end_controls_section();*/
        

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

            if($dont_import_swiper != '1'){//if Dont add Swiper.js is not activated

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
            
            
            if($dont_import_swiper != '1'){//if Dont add Swiper.js is not activated

                return [
                    'woosbymdz-swiperjs',
                    'woosbymdz-frontjs',
                ];

            }else if($dont_import_swiper == '1') {

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

            if($dont_import_swiper != '1'){//if Dont add Swiper.js is not activated

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

        //get active-theme tags (needed in the `products-title-selector` html-attr)
        include WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'this-theme-confs.php';
        /***/

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
        
        

        //We will send this to getandshowprods.php Class 
        $all_settings = array(

            'products_fetch' => $settings['products_fetch'],
            'products_query_operator' => $settings['products_query_operator'],
            'products_query_operator2' => $settings['products_query_operator2'],
            'cat' => '', 
            'products_tags' => $settings['products_tags'],
            'products_use_attrs' => $settings['products_use_attrs'], 
            'products_number' => $settings['products_number'],
            'src' => $settings['products_src'],
            'yoast_main_cat' => $settings['yoast_main_cat'],
            'only_instock' => $settings['products_stock'],
            'dont_load_mob' => $settings['dont_load_mob'],
            'loading_enabled' => $settings['loading_enabled'],
            'showonly_tologgedin' => $settings['showonly_tologgedin'],
            'header_enabled' => $settings['header_enabled'],
            'products_extra' => '',
            'products_exclude' => '',
            'products_allcats_show' => $settings['products_allcats_show'],
            'products_allcats_title' => sanitize_text_field($settings['products_allcats_title']),
            'products_allcats_titlecolor' => $settings['products_allcats_titlecolor'],
            'products_allcats_backtype' => $settings['products_allcats_backtype'],
            'products_allcats_backcolor' => $settings['products_allcats_backcolor'],
            'products_allcats_backimg' => $settings['products_allcats_backimg'],
            'products_allcats_url' => $settings['products_allcats_url'],
            'products_allcats_yoast_link' => $settings['products_allcats_yoast_link'],
            'swiper_type' => $settings['swiper_type'],
            'swiper_navs' => $settings['swiper_navs'],
            'swiper_arrows' => $settings['swiper_arrows'],
            'arrows_style_leftbg' => $settings['arrows_style_leftbg'],
            'arrows_style_rightbg' => $settings['arrows_style_rightbg'],
            'arrows_style_hover' => $settings['arrows_style_hover'],
            'pags_style_fraction_direction' => $settings['pags_style_fraction_direction'],
            'pags_style_margin' => $settings['pags_style_margin'],
            'notfound_show' => $settings['notfound_show'],
            'notfound_txt' => $settings['notfound_txt'],
            'notfound_btn_txt' => $settings['notfound_btn_txt'],
            'notfound_btn_url' => $settings['notfound_btn_url'],
            'notfound_space_top' => $settings['notfound_space_top'],  
            'notfound_btn_classes' => $settings['notfound_btn_classes'],    
            'adv_infinital' => $settings['adv_infinital'],
            'adv_infinital_txt' => $settings['adv_infinital_txt'],
            'adv_infinital_num' => $settings['adv_infinital_num'],
            'adv_infinital_mode' => $settings['adv_infinital_mode'],
            'products_template' => $settings['products_template'],
            'products_template_opt1' => $settings['products_template_opt1'],
            'products_template_opt2' => $settings['products_template_opt2'],
            'products_template_opt3' => $settings['products_template_opt3'],
            'products_template_opt4' => $settings['products_template_opt4'],
            'products_template_opt5' => $settings['products_template_opt5'],
            

            
            //extra
            'reload_ajax_comp' => 'no',
        );
        /***/


        //Add Dynamic Settings
        if($settings['products_use_attrs'] == 'yes') {

            foreach($settings as $name => $setting) {
		
                if(str_contains($name, 'products_attr_')){
                
                    $all_settings[$name] = $setting;
                }
                
            }

        }
        if($settings['products_fetch'] == 'custom_tax') {

            foreach($settings as $name => $setting) {
		
                if(str_contains($name, 'products_customtax_')){
                
                    $all_settings[$name] = $setting;
                }
                
            }

        }
        /***/
        
        //products
        $all_settings['cat'] = $settings['products_cats'] != '' ? $settings['products_cats'] : '';
        $all_settings['products_extra'] = $settings['products_extra'] != '' ? sanitize_text_field($settings['products_extra']) : '';
        $all_settings['products_exclude'] = $settings['products_exclude'] != '' ? sanitize_text_field($settings['products_exclude']) : '';
        /***/

        //Grid-Layout confs
        if($settings['grid_slider_activate'] == 1 || $settings['grid_slider_activate'] == 'yes'){

            $settings['dont_swipe'] = 'no';
            $settings['dont_swipe_on_mob'] = 'no';

            $settings['swiper_slides_wide'] = $settings['grid_slides_wide'];
            $settings['swiper_slides_desk'] = $settings['grid_slides_desk'];
            $settings['swiper_slides_tab'] = $settings['grid_slides_tab'];
            $settings['swiper_slides_mob'] = $settings['grid_slides_mob'];
            $settings['swiper_slides_mob2'] = $settings['grid_slides_mob2'];
        }
        /***/
        
        //create the main container ?>
        <?php 
        $is_mob = wp_is_mobile();
        if($all_settings['dont_load_mob'] == 'yes' && $is_mob == true) { return; }
        ?>

        <?php 
        switch($settings['maincon_style_horizontalpos']){
            case 'left':
                $main_con_horizontalPos = 'woosbymdz-left';
                break;
            case 'center':
                $main_con_horizontalPos = 'woosbymdz-middle';
                break;
            case 'right': 
                $main_con_horizontalPos = 'woosbymdz-right';
                break;
        } ?>
        <div style="width: 100%; <?php if($settings['maincon_style_bgtransparent'] == 'yes'){echo esc_attr('background: none !important;'); } ?> <?php if($all_settings['showonly_tologgedin'] == 'yes' && !is_user_logged_in()){echo esc_attr('background: url("'.$settings['404_style_bg']['url'].'"); background-size: cover;'); } ?>" class="woosbymdz-carousel-con <?php if($settings['dont_load_mob'] == 'yes') {echo esc_attr('no-mobile ');} ?> <?php if($all_settings['loading_enabled'] == 'yes') { echo esc_attr('woosbymdz-loading'); } ?> <?php echo esc_attr($main_con_horizontalPos); ?>" dont-swipe-onmobile="<?php echo esc_attr($settings['dont_swipe_on_mob']); ?>" active-nav="<?php echo esc_attr($settings['swiper_navs_active']); ?>" sliding-eff="<?php echo esc_attr($settings['swiper_slide_effect']); ?>" overflow-vis="<?php echo esc_attr($settings['maincon_style_overflow']); ?>" cache-header="<?php echo esc_attr($settings['adv_ajax_cache']); ?>" reseter="<?php echo esc_attr($settings['adv_pros_reset']); ?>" infinital="<?php echo esc_attr($settings['adv_infinital']); ?>" inf-mode="<?php echo esc_attr($settings['adv_infinital_mode']); ?>" grid-layout="<?php echo esc_attr($settings['grid_slider_activate']); ?>" is-masonry="<?php echo esc_attr($settings['grid_slider_masonry']); ?>" masonry-c-gap="<?php echo esc_attr($settings['grid_masonry_cgap']); ?>" pros-card-temp="<?php echo esc_attr($settings['products_template']) ?>">

        <!--Reset Button-->
        <?php if($settings['adv_pros_reset'] == 'yes'): ?>
            <div onclick="woosbymdz_reset_products(this)" class="woosbymdz-reseter-button woosbymdz-hidden" has-setted="no" pos="<?php echo esc_attr($settings['adv_pros_reset_pos']); ?>">
                <div>
                    <span><i class="<?php echo esc_attr($settings['adv_pros_reset_icon']['value']); ?>"></i></span>
                    <div style="display: none !important;" class="htmlcon"></div>
                    <div class="reseter-tooltip" pos="<?php echo esc_attr($settings['adv_pros_reset_txt_pos']); ?>">
                        <?php echo esc_html($settings['adv_pros_reset_txt']); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!--Endof Reset Button-->
    
        <?php 
        if ($all_settings['header_enabled'] == 'yes'){ 

            if ($settings['showonly_tologgedin'] == 'yes' && $settings['showonly_tologgedin_header'] == 'yes' && !is_user_logged_in()){
                //dont show header to not_logged in users
            }else{

        ?>
            <?php
            $auth = '1';
            if ($all_settings['showonly_tologgedin'] == 'yes' && !is_user_logged_in()){
                $auth = '0';
            }
            ?>
            <div style="display: none;" class="woosbymdz-header woosbymdz-hidden <?php if($settings['header_style_hoverbgchanger'] == 'yes'){echo 'woosbymdz-header-fadebg'; } ?>" el-align="<?php echo esc_attr($settings['header_elems_align']); ?>">
             <!-- header buttons -->
            <div class="woosbymdz-header-wrapper">
            <?php
           
            //start rendering HEADER elements
            $header_indexer = 0;
            foreach ( $settings['header_elements'] as $index => $item ) {

                switch($settings['header_elements'][$index]['header_texticon_alginment']){//prepare header text & icon alignment
                    case 'dir1':
                        $header_txticon_align = 'woosbymdz-texticon-wrapeer1';
                        break;
                    case 'dir2':
                        $header_txticon_align = 'woosbymdz-texticon-wrapeer2';
                        break;
                    case 'dir3':
                        $header_txticon_align = 'woosbymdz-texticon-wrapeer3';
                        break;
                    case 'dir4':
                        $header_txticon_align = 'woosbymdz-texticon-wrapeer4';
                        break;
                    default: 
                        $header_txticon_align = 'woosbymdz-texticon-wrapeer1';
                        break;
                }
                
                ?>
                <div cat="<?php echo esc_attr($settings['header_elements'][$index]['header_ajax_cats'])?>" number="<?php echo esc_attr($settings['header_elements'][$index]['header_products_number']) ?>" stock="<?php echo esc_attr($settings['header_elements'][$index]['header_products_stock']) ?>" type="<?php echo esc_attr($settings['header_elements'][$index]['header_products_src']) ?>" seeallcat="<?php echo esc_attr($settings['header_elements'][$index]['header_allcats_show']) ?>" <?php if($settings['header_elements'][$index]['header_allcats_backtype'] == 'color'){
                    echo esc_attr('seeallcat_bgcolor='.$settings['header_elements'][$index]['header_allcats_backcolor']);
                }else if($settings['header_elements'][$index]['header_allcats_backtype'] == 'wallpaper') { echo esc_attr('seeallcat_bgwalp='.$settings['header_elements'][$index]['header_allcats_backimg']['url']);}?> seeallcat_title="<?php echo esc_attr($settings['header_elements'][$index]['header_allcats_title'])?>"
                seeallcat_titlecolor="<?php echo esc_attr($settings['header_elements'][$index]['header_allcats_titlecolor'])?>" seeallcat_link="<?php echo esc_attr($settings['header_elements'][$index]['header_allcats_url']['url'])?>" seeallcat_link_ext="<?php echo esc_attr($settings['header_elements'][$index]['header_allcats_url']['is_external'])?>" seeallcat_link_follow="<?php echo esc_attr($settings['header_elements'][$index]['header_allcats_url']['nofollow'])?>" auth="<?php echo esc_attr($auth); ?>" bgchanger="<?php echo esc_attr($settings['header_elements'][$index]['header_bgchanger']) ?>" bgchanger_color="<?php echo esc_attr($settings['header_elements'][$index]['header_bgchanger_color']) ?>" has-cached="no" cache-elem="woosbymdz-hcacher-<?php echo esc_attr($header_indexer); ?>" infinital="<?php echo esc_attr($settings['adv_infinital']); ?>" inf-mode="<?php echo esc_attr($settings['adv_infinital_mode']); ?>" inf-num="<?php echo esc_attr($settings['adv_infinital_num']); ?>" inf-txt="<?php echo esc_attr($settings['adv_infinital_txt']); ?>" class="woosbymdz-header-divs" <?php if($settings['header_elements'][$index]['header_elems_maketitle'] == 'yes'){echo 'no-btn'.' title-theme='.$settings['header_elements'][$index]['header_elems_maketitle_theme'];} ?>>

                <!--Header Cache Elements-->
                <?php if($settings['adv_ajax_cache'] == 'yes'): ?>
                    <div style="display: none !important;" id="woosbymdz-hcacher-<?php echo esc_attr($header_indexer); ?>"></div>
                <?php endif; ?>
                <!--Endof Header Cache Elements-->
                
                <div class="<?php echo esc_attr($header_txticon_align); ?>">
                
                <?php if($settings['header_elements'][$index]['header_icons_type'] == 'elem_icons'){
                ?><i style="color: <?php echo esc_attr($settings['header_elements'][$index]['header_icons_color']) ?> ;" class="<?php echo esc_attr($settings['header_elements'][$index]['header_icons_select']['value'])?>"></i> <?php
                }else if($settings['header_elements'][$index]['header_icons_type'] == 'custom_icons'){
                    ?><img style="width:<?php echo esc_attr($settings['header_elements'][$index]['header_iconscustom_width']) ?>px; height: auto;" src="<?php echo esc_attr($settings['header_elements'][$index]['header_iconscustom_select']['url']) ?>" alt="..."><?php
                }?>
                <span style="color:<?php echo esc_attr($settings['header_elements'][$index]['header_text_color']); ?> ;"><?php echo esc_html($settings['header_elements'][$index]['header_elems_title']); ?></span>

                </div>
                </div> 
                <?php
                $header_indexer++;

            }
            ?>
            </div>
            </div>
            <?php } //show header to not_loggen in users or not? ?> 
            <?php } else { ?> <div class="woosbymdz-noheader"></div> <?php } ?>

        <?php //prepare swiper configs

        $arrow_next_class = $arrows_js = $arrow_prev_class = $swiper_pergroup_wide = $swiper_pergroup_desk = $swiper_pergroup_tablet = $swiper_pergroup_mob = $swiper_pergroup_mob2 = '';

        $rand_id = 'mdz' . wp_date('smdyih') . rand(123, 123123) . 'con';

        if($settings['swiper_cflow_rotate'] === NULL || $settings['swiper_cflow_rotate'] == 'null' || $settings['swiper_cflow_rotate'] == "" || $settings['swiper_cflow_rotate'] == " "){
            $settings['swiper_cflow_rotate'] = 50;
        }
        ?>

        <div id="<?php echo esc_attr($rand_id); ?>" style="display: none;" isdisswiper="<?php echo esc_attr($swiper_noeff); ?>" is-elem-editor="<?php echo $is_editor; ?>" class="woosbymdz-swiper woosbymdz-hidden <?php if($settings['swiper_arrows'] == 'dynamic'){echo esc_attr('dynamic-arrows ');} if($settings['swiper_autoplay_pause'] == 'yes'){echo esc_attr('pauseonhover ');} if($settings['swiper_centered_highlight'] == 'yes') {echo esc_attr('center-highlight ');} ?> <?php if($settings['swiper_centered_rotate'] == 'yes'){echo esc_attr('centered-rotate');} ?>" backtofirstslide="<?php echo esc_attr($settings['header_backtofirst_ajax']) ?>" type="<?php echo esc_attr($settings['swiper_type']); ?>" navs="<?php echo esc_attr($settings['swiper_navs']); ?>" arrows="<?php echo esc_attr($settings['swiper_arrows']); ?>" autoplay="<?php echo esc_attr($settings['swiper_autoplay']); ?>" autoplay-pause="<?php echo esc_attr($settings['swiper_autoplay_pause']); ?>" autoplay-time="<?php echo esc_attr($settings['swiper_autoplay_delay']) ?>" autoheight="<?php echo esc_attr($settings['swiper_autoheight']); ?>" mousewheel="<?php echo esc_attr($settings['swiper_mousewheel']); ?>" grab="<?php echo esc_attr($settings['swiper_cursorgrab']); ?>" wide-pp ="<?php echo esc_attr($settings['swiper_slides_wide']); ?>" wide-s ="<?php echo esc_attr($settings['swiper_space_wide']); ?>" desk-pp="<?php echo esc_attr($settings['swiper_slides_desk']); ?>" desk-s="<?php echo esc_attr($settings['swiper_space_desk']); ?>" tab-pp="<?php echo esc_attr($settings['swiper_slides_tablet']); ?>" tab-s="<?php echo esc_attr($settings['swiper_space_tablet']); ?>" mob-pp="<?php echo esc_attr($settings['swiper_slides_mob']); ?>" mob-s="<?php echo esc_attr($settings['swiper_space_mob']); ?>" mob2-pp="<?php echo esc_attr($settings['swiper_slides_mob2']); ?>" mob2-s="<?php echo esc_attr($settings['swiper_space_mob2']); ?>" grid-sp-up="<?php echo esc_attr($settings['grid_slides_space_up']); ?>" grid-sp-down="<?php echo esc_attr($settings['grid_slides_space_down']); ?>" is-automover="<?php echo esc_attr($settings['swiper_automover']); ?>" keyboard="<?php echo esc_attr($settings['swiper_keyboard']); ?>" dont-swipe="<?php echo esc_attr($settings['dont_swipe']); ?>" dont-swipe-onmobile="<?php echo esc_attr($settings['dont_swipe_on_mob']); ?>" forced-loop="<?php echo esc_attr($settings['swiper_forcedloop']); ?>" cflow-rotate ="<?php echo esc_attr($settings['swiper_cflow_rotate']); ?>" is-grid-swiper="<?php echo esc_attr($settings['swiper_is_grid']); ?>" grid-rows="<?php echo esc_attr($settings['swiper_grid_rows']); ?>" grid-rows-mob="<?php echo esc_attr($settings['swiper_grid_rows_mob']) ?>">


        <div class="swiper-wrapper" no-products-title="<?php echo esc_attr($settings['products_hide_title']); ?>" no-products-cats="<?php echo esc_attr($settings['products_hide_cats']); ?>" no-products-price="<?php echo esc_attr($settings['products_hide_price']) ?>" no-products-labels="<?php echo esc_attr($settings['products_hide_labels']); ?>" no-products-addbtn="<?php echo esc_attr($settings['products_hide_addbtn']); ?>" products-label-abs="<?php echo esc_attr($settings['products_style_label_pos']); ?>" products-same-height="<?php echo esc_attr($settings['products_auto_sameh']); ?>" products-center-title="<?php echo esc_attr($settings['products_title_center']) ?>" products-title-selector="<?php echo esc_attr($theme_prod_title); ?>">
        <!-- here goes the slides -->
        <?php
    
        //get and show products  
        if ($all_settings['showonly_tologgedin'] == 'yes'){
            if (is_user_logged_in()){

                include_once WOOSBYMDZ_PATH . 'widgets'.DIRECTORY_SEPARATOR.'class-getandshowprods.php' ;
                $class_fetch = new woosbymdz_getandshow();
                $class_fetch->getandshow_mdz($all_settings);

            }else {
                ?> </div></div>
                <div class="woosbymdz-404wrapper">
                    <?php
                    if(!isset($settings['404_title'])){
                        $settings['404_title'] =  esc_html__('We are so sorry!' , 'wooslider-bymdez');
                    }
                    ?>
                    <h5><?php echo esc_html($settings['404_title']); ?></h5>
                    <div><p><?php echo esc_html($settings['404_desc']) ?></p></div>
                    <?php 
                    if ($settings['404_loginbtn']=='yes' || $settings['404_registerbtn']=='yes'){

                        ?><div class="woosbymdz-404btns-wrapper">

                            <?php if($settings['404_loginbtn']=='yes'){

                                switch($settings['404_loginbtn_url']['is_external']){
                                    case 'on':
                                        $loginbtn_url_ext = '_blank';
                                        break;
                                    default:
                                        $loginbtn_url_ext = '_self';
                                        break;
                                }
                                switch($settings['404_loginbtn_url']['nofollow']){
                                    case 'on':
                                        $loginbtn_url_follow = 'nofollow';
                                        break;
                                    default:
                                        $loginbtn_url_follow = 'follow';
                                        break;
                                }

                                ?><a href="<?php echo esc_url($settings['404_loginbtn_url']['url']) ?>" target="<?php echo esc_attr($loginbtn_url_ext) ?>" rel="<?php echo esc_attr($loginbtn_url_follow) ?>"><button class="<?php echo esc_attr($settings['404_loginbtn_class']) ?>"><?php echo esc_html($settings['404_loginbtn_text']) ?></button></a> <?php
                            } ?>

                            <?php if($settings['404_registerbtn']=='yes'){

                                switch($settings['404_registerbtn_url']['is_external']){
                                    case 'on':
                                      $registerbtn_url_ext = '_blank';
                                      break;
                                    default:
                                      $registerbtn_url_ext = '_self';
                                      break;
                                }
                                switch($settings['404_registerbtn_url']['nofollow']){
                                    case 'on':
                                      $registerbtn_url_follow = 'nofollow';
                                      break;
                                    default:
                                      $registerbtn_url_follow = 'follow';
                                      break;
                                 }

                                ?><a href="<?php echo esc_url($settings['404_registerbtn_url']['url']) ?>" target="<?php echo esc_attr($registerbtn_url_ext) ?>" rel="<?php echo esc_attr($registerbtn_url_follow) ?>"><button class="<?php echo esc_attr($settings['404_registerbtn_class']) ?>"><?php echo esc_html($settings['404_registerbtn_text']) ?></button></a> <?php
                            } ?>

                        </div> <?php
                    }
                    ?>
                </div><!--End of 404 wrapper-->
                </div> 
                <?php 
            }
        }else{
    
            include_once WOOSBYMDZ_PATH . 'widgets'.DIRECTORY_SEPARATOR.'class-getandshowprods.php' ;
            $class_fetch = new woosbymdz_getandshow();
            $class_fetch->getandshow_mdz($all_settings);

        }/*show only to logged in users is off*/
        
    }

    //render editor live preview 
    protected function content_template() {}
    

    

    protected function load_all_custom_attrs()
    {

        $class_helper = help::get_instance();

        $args = array(); 
        $output = 'names'; // or objects
        $operator = 'and'; // 'and' or 'or'
        
        $taxonomies = get_taxonomies( $args, $output, $operator ); 
        
        foreach($taxonomies as $tax){
        
            $tax_arr = explode('pa_', $tax);
        
            if(isset($tax_arr[1]) && ($tax_arr[1] != '' || $tax_arr[1] != NULL)) {
        
                $this->add_control(//Attrs
                    'products_attr_'.$tax.'',
                    [
                        'label' 		=>	$tax_arr[1],
                        'type' 			=>	\Elementor\Controls_Manager::SELECT2,
                        'description'   =>  esc_html__( 'Choose and Search Terms', 'wooslider-bymdez' ),
                        'label_block'	=>  true,
                        'multiple' 	    =>	true,
                        'options' 		=>  $class_helper->get_terms_mdz($tax),
                        'conditions' => [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'yoast_main_cat',
                                    'operator' => '!==',
                                    'value' => 'yes'
                                ],
                                [
                                    'name' => 'products_use_attrs',
                                    'operator' => '===',
                                    'value' => 'yes'
                                ],
                            ],
        
                        ],
                    ]
                );
            }
        }
        
        
        
    }

    protected function load_all_custom_taxs()
    {

        $class_helper = help::get_instance();

        $args = array(
            '_builtin' => false,
        ); 
        $output = 'names'; // or objects
        $operator = 'and'; // 'and' or 'or'
        
        $taxonomies = get_taxonomies( $args, $output, $operator ); 

        
        foreach($taxonomies as $tax){
        
            if($tax == 'product_cat' || $tax == 'product_tag' || $tax == 'elementor_library_type' || $tax == 'elementor_library_category'){

                continue;

            }//Ignore this Custom_tax

            if(str_contains($tax, 'pa_')){
                continue;
            }//Ignore this Custom_ta
        
        
            $this->add_control(//Attrs
                'products_customtax_'.$tax.'',
                [
                    'label' 		=>	$tax,
                    'type' 			=>	\Elementor\Controls_Manager::SELECT2,
                    'description'   =>  esc_html__( 'Choose and Search Terms, Avoid using and selecting Custom-Taxonomies that are not related to woocomerce products', 'wooslider-bymdez' ),
                    'label_block'	=>  true,
                    'multiple' 	    =>	true,
                    'options' 		=>  $class_helper->get_terms_mdz($tax),
                    'conditions' => [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'yoast_main_cat',
                                'operator' => '!==',
                                'value' => 'yes'
                            ],
                            [
                                'name' => 'products_fetch',
                                'operator' => '===',
                                'value' => 'custom_tax'
                            ],
                        ],
        
                    ],
                ]
            );
        }   
    }
}//End of class
}//close the PHP tag to reduce the blank spaces ?>