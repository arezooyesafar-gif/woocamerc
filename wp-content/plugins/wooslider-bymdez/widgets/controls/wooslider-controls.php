<?php 

namespace Woosbymdz\Widgets\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class controls {

    //PROPS
    private $use_all_impo;
    private $use_pros_impo;

    public function __construct()
    {

        $this->use_all_impo = get_option('woosbymdz_elem_allcss_impo');
        $this->use_pros_impo = get_option('woosbymdz_elem_procss_impo');

        
    }

    public function main_controls($ins='', $mode=1, $repeater='', $ext_opts=[], $is_loop=0)
    {

        /**
         * Note: mode-3 `header-controls` does not ends the controll section! `end_controls_section()`
         */

        if($ins == NULL || $ins == ''){
            return;
        }

        include WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'this-theme-confs.php';


        $use_all_impo = $this->use_all_impo;
        $use_pros_impo = $this->use_pros_impo;
        

        $all_important = $pros_important = '';

        if($use_all_impo == '1'){
            $all_important = '!important';
            $pros_important = $all_important;
        }

        if($use_pros_impo == '1'){
            $pros_important = '!important';
        }

        /*Mode 8 is for the loop-slider `Loop_Woosbymdz`*/

        switch($mode){
            case '1':
            case 1: 
                $ins->start_controls_section(//tab_content
                    'content_section',
                    [
                        'label' => esc_html__( 'General', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                    ]
                );
        
                $ins->add_control( //Just Refresh the frame
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

                if($is_loop == 0){
                    $ins->add_control( //No-Editor-Render
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
                }
        
                $ins->add_control( //Loading
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
        
                if($is_loop == 0) {

                    $ins->add_control( //Header
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

                }else {
                    $ins->add_control( //Header (Loop-widget)
                        'header_enabled',
                        [
                            'label' => esc_html__('Show Header', 'wooslider-bymdez'),
                            'type' => \Elementor\Controls_Manager::SWITCHER,
                            'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
                            'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
                            'description' => esc_html__('In the Loop-Widget header only shows when your slider query is product (Woocommerce Products post-type)', 'wooslider-bymdez'),
                            'return_value' => 'yes',
                            'default' => 'yes',
                        ]
                    );
                }
                
        
                $ins->add_control( //Show to logged in users or not
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
        
                $ins->add_control( //Show header to logged in users or not
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
        
                $ins->add_control( //do not load on mobile
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
        
                $ins->add_control( //Disable Swiper on mobile devices
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
        
                $ins->add_control( //Disable Swiper (General)
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
        
                $ins->end_controls_section();
                break;

            case '2':
            case 2:
                $ins->start_controls_section(
                    'slider',
                    [
                        'label' => esc_html__( 'Slider', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                    ]
                );
        
                $ins->add_control( //Just Refresh the frame
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
        
                $ins->add_control(//def swiper type
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

                $ins->add_control( //swiper is grid?
                    'swiper_is_grid',
                    [
                        'label' => esc_html__('Grid Slider', 'wooslider-bymdez'),
                        'description' => esc_html__('This option will change your slider type to grid and grid slides', 'wooslider-bymdez'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
                        'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
                        'return_value' => 'yes',
                        'default' => 'no',
                    ]
                );

                $ins->add_control(//swiper grid rows
                    'swiper_grid_rows',
                    [
                        'label' 	=>	esc_html__( 'Grid rows number', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'description'   =>  esc_html__( 'max = 4 rows', 'wooslider-bymdez' ),
                        'default' 	=>	2,
                        'condition' 	=> ['swiper_is_grid' => 'yes'],
                        'min' => 2,
                        'max' => 4,
        
                    ]
                );

                $ins->add_control(//swiper grid rows (on Moblie)
                    'swiper_grid_rows_mob',
                    [
                        'label' 	=>	esc_html__( 'Mobile grid rows number', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'description'   =>  esc_html__( 'max = 4 rows', 'wooslider-bymdez' ),
                        'default' 	=>	2,
                        'condition' 	=> ['swiper_is_grid' => 'yes'],
                        'min' => 2,
                        'max' => 4,
        
                    ]
                );
        
        
                $ins->add_control( //Coverflow-Effect Rotation-degree
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
        
                $ins->add_control(//Swiper sliding effect
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
        
                $ins->add_control( //swiper auto height
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
        
                $ins->add_control( //highlight centered slide
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
        
                $ins->add_control( //rotate centered slide
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
        
                $ins->add_control(//Navigation
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
        
                $ins->add_control(//Navigation Active Style
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
        
                $ins->add_control(//Arrows
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
        
                $ins->add_control( //Autoplay
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
        
                $ins->add_control( //Autoplay pause on mouse hover
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
        
                $ins->add_control( //Autoplay delay
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
        
                $ins->add_control( //Mouse Wheel control
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
        
                $ins->add_control( //cursor
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
        
                $ins->add_control(//wide slides number
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
        
                $ins->add_control(//wide slides space
                    'swiper_space_wide',
                    [
                        'label' 	=>	esc_html__( 'Space in wide monitors', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'default' 	=>	30,
                        'min' => 0,
                        'max' => 200,
                    ]
                );
                
                $ins->add_control(//desk slides number
                    'swiper_slides_desk',
                    [
                        'label' 	=>	esc_html__( 'Slides in desktop', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'default' 	=>	4,
                        'min' => 0,
                        'max' => 8,
                    ]
                );
        
                $ins->add_control(//desk slides space
                    'swiper_space_desk',
                    [
                        'label' 	=>	esc_html__( 'Space in desktop', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'default' 	=>	30,
                        'min' => 0,
                        'max' => 200,
                    ]
                );
        
                $ins->add_control(//tablet slides number
                    'swiper_slides_tablet',
                    [
                        'label' 	=>	esc_html__( 'Slides in tablet', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'default' 	=>	3,
                        'min' => 0,
                        'max' => 5,
                    ]
                );
        
                $ins->add_control(//tablet slides space
                    'swiper_space_tablet',
                    [
                        'label' 	=>	esc_html__( 'Space in tablet', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'default' 	=>	25,
                        'min' => 0,
                        'max' => 200,
                    ]
                );
        
                $ins->add_control(//mobile slides number
                    'swiper_slides_mob',
                    [
                        'label' 	=>	esc_html__( 'Slides in mobile', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'default' 	=>	2,
                        'min' => 0,
                        'max' => 3,
                    ]
                );
        
                $ins->add_control(//mobile slides space
                    'swiper_space_mob',
                    [
                        'label' 	=>	esc_html__( 'Space in mobile', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'default' 	=>	20,
                        'min' => 0,
                        'max' => 200,
                    ]
                );
        
                $ins->add_control(//mobile2 slides number
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
        
                $ins->add_control(//mobile2 slides space
                    'swiper_space_mob2',
                    [
                        'label' 	=>	esc_html__( 'Space in mobile-2', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'default' 	=>	10,
                        'min' => 0,
                        'max' => 200,
                    ]
                );
        
                $ins->add_control( //auto mover after construct
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
        
                $ins->add_control( //keyboard controll
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
        
                $ins->add_control( //Forced-Loop
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
        
        
                $ins->end_controls_section();
                break;

            case '3':
            case 3:

                if($is_loop==0){

                    $ins->start_controls_section(
                        'header',
                        [
                            'label' => esc_html__( 'Header', 'wooslider-bymdez' ),
                            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                            'condition' 	=> ['header_enabled' => 'yes'],
                        ]
                    );
                }else {
                    $ins->start_controls_section(
                        'header',
                        [
                            'label' => esc_html__( 'Header', 'wooslider-bymdez' ),
                            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                            'conditions' => [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'header_enabled',
                                    'operator' => '==',
                                    'value' => 'yes'
                                ],
                                [
                                    'name' => 'post_query_post_type',
                                    'operator' => '==',
                                    'value' => 'product'
                                ],
                            ],

                            ],
			
                        ]
                    );
                }
                

                $ins->add_control( //Just Refresh the frame
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

                $ins->add_control(//def alignment of header elements
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

                $ins->add_control( //back to first slide after ajax
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
                );

                //$ins->end_controls_section();
                break;

            case '4':
            case 4:
                $repeater->add_control(//header make title
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
                        'options' 		=>  $ext_opts,
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
        
        
                $ins->add_control(//the main repeater
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
        
                $ins->end_controls_section();
                break;

            case '5':
            case 5:
                $ins->start_controls_section(
                    'notfound',
                    [
                        'label' => esc_html__( 'Not Found Message', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                    ]
                );
        
                $ins->add_control( //show message ?
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
        
                $ins->add_control(//not found Txt
                    'notfound_txt', [
                        'label' => esc_html__( 'Message Body', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'condition' 	=> ['notfound_show' => 'yes'],
                        'default' => esc_html__( 'Nothing found to show!' , 'wooslider-bymdez' ),
                        'label_block' => true,
                    ]
                );
        
                $ins->add_control(//not found btn txt
                    'notfound_btn_txt', [
                        'label' => esc_html__( 'Button text', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'condition' 	=> ['notfound_show' => 'yes'],
                        'default' => esc_html__( 'Go Shop' , 'wooslider-bymdez' ),
                        'label_block' => true,
                    ]
                );
        
                $ins->add_control(//not found btn url
                    'notfound_btn_url',
                    [
                        'label' => esc_html__( 'Button Link', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::URL,
                        'description' => esc_html__('Link this button to your shop page if you dont want to loose the customer', 'wooslider-bymdez'),
                        'condition' 	=> ['notfound_show' => 'yes'],
                        'placeholder' => esc_html__( 'https://your-site.com/shop/', 'wooslider-bymdez' ),
                    ]
                );
        
                $ins->add_control(//not found btn classes
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
        
                $ins->add_control(//not found block space form the top
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
        
                $ins->end_controls_section();
                break;

            case '6':
            case 6:
                $ins->start_controls_section(//404 content
                    '404_content',
                    [
                        'label' => esc_html__( '404', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                        'condition' 	=> ['showonly_tologgedin' => 'yes'],
                    ]	
                );
        
                $ins->add_control( //Just Refresh the frame
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
        
                $ins->add_control(//404 title
                    '404_title', [
                        'label' => esc_html__( 'Title (404)', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'description' => esc_html__('All contents of this section will be shown to users who not logged-in', 'wooslider-bymdez'),
                        'default' => esc_html__( 'We are so sorry!' , 'wooslider-bymdez' ),
                        'label_block' => true,
                    ]
                );
        
                $ins->add_control(//404 desc
                    '404_desc',
                    [
                        'label' => esc_html__( 'Description', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::WYSIWYG,
                        'default' => esc_html__( 'You are not able to see these products at the moment, please log-in or register', 'wooslider-bymdez' ),
                        'placeholder' => esc_html__( 'write here!', 'wooslider-bymdez' ),
                    ]
                );
        
                $ins->add_control( //404 login btn
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
        
                $ins->add_control(//404 login-btn text
                    '404_loginbtn_text', 
                    [
                        'label' => esc_html__( 'Login text', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'default' => esc_html__( 'Login' , 'wooslider-bymdez' ),
                        'condition' 	=> ['404_loginbtn' => 'yes'],
                        'label_block' => true,
                    ]
                );
        
                $ins->add_control(//404 login-btn url
                    '404_loginbtn_url',
                    [
                        'label' => esc_html__( 'URL (login)', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::URL,
                        'condition' 	=> ['404_loginbtn' => 'yes'],
                        'placeholder' => esc_html__( 'https://your-site.com/login', 'wooslider-bymdez' ),
                    ]
                );
        
                $ins->add_control(//404 login-btn css classes
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
        
                $ins->add_control( //404 register btn
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
        
                $ins->add_control(//404 register-btn text
                    '404_registerbtn_text', 
                    [
                        'label' => esc_html__( 'Register text', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'default' => esc_html__( 'Register' , 'wooslider-bymdez' ),
                        'condition' 	=> ['404_registerbtn' => 'yes'],
                        'label_block' => true,
                    ]
                );
        
                $ins->add_control(//404 register-btn url
                    '404_registerbtn_url',
                    [
                        'label' => esc_html__( 'URL (register)', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::URL,
                        'condition' 	=> ['404_registerbtn' => 'yes'],
                        'placeholder' => esc_html__( 'https://your-site.com/login', 'wooslider-bymdez' ),
                    ]
                );
        
                $ins->add_control(//404 register-btn css classes
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
        
                $ins->end_controls_section();
                break;

            case '7':
            case 7:
                $ins->start_controls_section(//Advanced Options
                    'adv_options',
                    [
                        'label' => esc_html__( 'Advanced', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                    ]	
                );
        
                $ins->add_control( //Header Ajax cache
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
        
                $ins->add_control( //Pros Reseter
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
        
                $ins->add_control(//Pros Reseter pos
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
        
                $ins->add_responsive_control(//Pros Reseter pos manual
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
        
                $ins->add_control(//Pros Reseter icon
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
        
                $ins->add_control(//Pros Reseter tooltip text
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
        
                $ins->add_control(//Pros Reseter tooltip pos
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
        
        
                $ins->add_control( //infinite slider
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
        
                $ins->add_control(//infinite slider Mode
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
        
                $ins->add_control(//infinite slider number to load
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
        
                $ins->add_control(//infinite slider not-found txt
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
        
                $ins->end_controls_section();
                break;

            case '8':
            case 8:

                $ins->start_controls_section(//Start Number option
                    'mdzslider_loop_options',
                    [
                        'label' => esc_html__( 'Posts Number', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                    ]	
                );

                $ins->add_control(//Loop-Slider number to load
                    'posts_per_page',
                    [
                        'label' 		=> esc_html__( 'Slides Number to Show', 'wooslider-bymdez' ),
                        'type'			=> \Elementor\Controls_Manager::NUMBER,
                        'description'   =>  esc_html__( 'Select the number of posts that you want to show inside the slider', 'wooslider-bymdez' ),
                        'return_value' 	=> 'true',
                        'default' 		=> 8,
                    ]
                );

                $ins->end_controls_section();
                break;
            case '9':
            case 9:
                $ins->start_controls_section(//Start Grid-Layout
                    'grid_slider',
                    [
                        'label' => esc_html__( 'Grid Layout', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                    ]	
                );

                $ins->add_control( //Use grid?
                    'grid_slider_activate',
                    [
                        'label' => esc_html__('Use grid layout?', 'wooslider-bymdez'),
                        'description' => esc_html__('In the grid slider mode, it will display the products in a grid layout and will disable the slider attributes.', 'wooslider-bymdez'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
                        'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
                        'return_value' => 'yes',
                        'default' => 'no',
                    ]
                );

                $ins->add_control(//wide slides number
                    'grid_slides_wide',
                    [
                        'label' 	=>	esc_html__( 'Slides in wide monitors', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'description' 	=> esc_html__( 'you can change wide break-point in plugin settings page', 'wooslider-bymdez' ),
                        'default' 	=>	4,
                        'min' => 0,
                        'max' => 8,
                        'condition' 	=> ['grid_slider_activate' => 'yes'],
                    ]
                );
                
                $ins->add_control(//desk slides number
                    'grid_slides_desk',
                    [
                        'label' 	=>	esc_html__( 'Slides in desktop', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'default' 	=>	4,
                        'min' => 0,
                        'max' => 8,
                        'condition' 	=> ['grid_slider_activate' => 'yes'],
                    ]
                );
        
                $ins->add_control(//tablet slides number
                    'grid_slides_tablet',
                    [
                        'label' 	=>	esc_html__( 'Slides in tablet', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'default' 	=>	3,
                        'min' => 0,
                        'max' => 5,
                        'condition' 	=> ['grid_slider_activate' => 'yes'],
                    ]
                );
        
                $ins->add_control(//mobile slides number
                    'grid_slides_mob',
                    [
                        'label' 	=>	esc_html__( 'Slides in mobile', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'default' 	=>	2,
                        'min' => 0,
                        'max' => 3,
                        'condition' 	=> ['grid_slider_activate' => 'yes'],
                    ]
                );
        
                $ins->add_control(//mobile2 slides number
                    'grid_slides_mob2',
                    [
                        'label' 	=>	esc_html__( 'Slides in mobile-2 (below 300px width)', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'description' 	=> esc_html__( 'you can change mobile-2 break-point in plugin settings page', 'wooslider-bymdez' ),
                        'default' 	=>	1,
                        'min' => 0,
                        'max' => 2,
                        'condition' 	=> ['grid_slider_activate' => 'yes'],
                    ]
                );

                $ins->add_control(//grid slides space up
                    'grid_slides_space_up',
                    [
                        'label' 	=>	esc_html__( 'Slides up space (px)', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'default' 	=>	0,
                        'min' => 0,
                        'max' => 1000,
                        'condition' 	=> ['grid_slider_activate' => 'yes'],
                    ]
                );

                $ins->add_control(//grid slides space down
                    'grid_slides_space_down',
                    [
                        'label' 	=>	esc_html__( 'Slides down space (px)', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'default' 	=>	0,
                        'min' => 0,
                        'max' => 1000,
                        'condition' 	=> ['grid_slider_activate' => 'yes'],
                    ]
                );

                $ins->add_control( //Use Masonry-grid?
                    'grid_slider_masonry',
                    [
                        'label' => esc_html__('Use Grid-Masonry layout?', 'wooslider-bymdez'),
                        'description' => esc_html__('Activating this option is useful when you have different slide heights or product images are not cut in the same way.', 'wooslider-bymdez'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => esc_html__( 'Active', 'wooslider-bymdez' ),
                        'label_off' => esc_html__( 'Inactive', 'wooslider-bymdez' ),
                        'return_value' => 'yes',
                        'default' => 'no',
                    ]
                );

                $ins->add_control(//grid-masonry column-gap
                    'grid_masonry_cgap',
                    [
                        'label' 	=>	esc_html__( 'Column Gap', 'wooslider-bymdez' ),
                        'type' 		=>	\Elementor\Controls_Manager::NUMBER,
                        'description' 	=> esc_html__( 'It sets the empty space between the columns in grid-masonry mode', 'wooslider-bymdez' ),
                        'default' 	=>	10,
                        'min' => 0,

                        'conditions' => [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'grid_slider_activate',
                                    'operator' => '==',
                                    'value' => 'yes'
                                ],
                                [
                                    'name' => 'grid_slider_masonry',
                                    'operator' => '==',
                                    'value' => 'yes'
                                ],
                            ]
                        ]
                    ]
                );
        

                $ins->end_controls_section();
                break;
        }
    }

    public function style_controls($ins='', $mode=1)
    {


        /**
         * Mode-1 => returns General-Container styles
         * Mode-2 => returns Header-Container styles
         * etc...
         */

        if($ins == NULL || $ins == ''){
            return;
        }

        include WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'this-theme-confs.php';

        $use_all_impo = $this->use_all_impo;
        $use_pros_impo = $this->use_pros_impo;
        

        $all_important = $pros_important = '';

        if($use_all_impo == '1'){
            $all_important = '!important';
            $pros_important = $all_important;
        }

        if($use_pros_impo == '1'){
            $pros_important = '!important';
        }

        switch($mode){
            case '1':
            case 1:
                $ins->start_controls_section(//woosbymdz Main Con
                    'maincon_style',
                    [
                        'label' => esc_html__( 'Main Container', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    ]
                );
        
                $ins->add_group_control(
                    \Elementor\Group_Control_Background::get_type(),
                    [
                        'name' => 'maincon_style_bg',
                        'label' => esc_html__( 'Background', 'wooslider-bymdez' ),
                        'types' => [ 'classic', 'gradient', 'video' ],
                        'selector' => '{{WRAPPER}} .woosbymdz-carousel-con',
                    ]
                );
        
                $ins->add_control( //main con transparent bg
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
        
                $ins->add_responsive_control(//main-con max-width
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
        
                $ins->add_responsive_control(//main-con min-height
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
        
                $ins->add_responsive_control(//main-con padding
                    'maincon_style_padding', 
                    [
                    'label' => esc_html__( 'Padding', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .woosbymdz-carousel-con' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                    ],
                ]);
        
                $ins->add_responsive_control(//main-con margin
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
        
                $ins->add_control(//main-con horizontal position
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
        
                $ins->add_group_control(//main-con border styles
                    \Elementor\Group_Control_Border::get_type(),
                    [
                        'name' => 'maincon_style_border',
                        'label' => esc_html__( 'Border', 'wooslider-bymdez' ),
                        'selector' => '{{WRAPPER}} .woosbymdz-carousel-con',
                    ]
                );
        
                $ins->add_control(
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
        
                $ins->add_control(//main-con overflow
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
            
                $ins->end_controls_section();
                break;

            case '2':
            case 2:
                $ins->start_controls_section(//woosbymdz Header
                    'header_style',
                    [
                        'label' => esc_html__( 'Header', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                        'condition' 	=> ['header_enabled' => 'yes'],
                    ]
                );
        
                $ins->add_group_control(//header bg
                    \Elementor\Group_Control_Background::get_type(),
                    [
                        'name' => 'header_style_bg',
                        'label' => esc_html__( 'Background', 'wooslider-bymdez' ),
                        'types' => [ 'classic', 'gradient', 'video' ],
                        'selector' => '{{WRAPPER}} .woosbymdz-header',
                    ]
                );
        
                $ins->add_responsive_control(//header height
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
        
                $ins->add_responsive_control(//header scroll-bar height
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

                $ins->add_group_control(//header border styles
                    \Elementor\Group_Control_Border::get_type(),
                    [
                        'name' => 'header_style_border',
                        'label' => esc_html__( 'Border', 'wooslider-bymdez' ),
                        'selector' => '{{WRAPPER}} .woosbymdz-header',
                    ]
                );
        
                $ins->add_control(//header border-radius
                    'header_style_borderradius',
                    [
                        'label' => esc_html__( 'Border-Radius', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%' ],
                        'selectors' => [
                            '{{WRAPPER}} .woosbymdz-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                        ],
                    ]
                );
        
                $ins->add_responsive_control(//header padding
                    'header_style_padding', 
                    [
                    'label' => esc_html__( 'Padding', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .woosbymdz-header-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                    ],
                ]);

                $ins->add_responsive_control(//header margin
                    'header_style_margin', 
                    [
                    'label' => esc_html__( 'Margin', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .woosbymdz-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                    ],
                ]);
        
                $ins->add_control( //header hover bg change?
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
                $ins->end_controls_section();
                break;

            case '3':
            case 3:
                $ins->start_controls_section(//woosbymdz Header Elements
                    'header_elsstyle',
                    [
                        'label' => esc_html__( 'Header Buttons', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                        'condition' 	=> ['header_enabled' => 'yes'],
                    ]
                );
        
                $ins->add_group_control(//header els typo
                    \Elementor\Group_Control_Typography::get_type(),
                    [
                        'name' => 'header_elsstyle_typo',
                        'label' => esc_html__( 'Header elements typography', 'wooslider-bymdez' ),
                        'selector' => '{{WRAPPER}} .woosbymdz-header-divs span',
                    ]
                );
        
                $ins->add_control(//header els txt color
                    'header_elsstyle_color',
                    [
                        'label' => esc_html__( 'Elements text color', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .woosbymdz-header-divs span, {{WRAPPER}} .woosbymdz-header-divs:before, {{WRAPPER}} .woosbymdz-header-divs:after' => 'color: {{VALUE}} !important;border-color: {{VALUE}} '.$all_important.';',
                        ],
                    ]
                );
        
                $ins->add_control(//header els bg color on hover
                    'header_elsstyle_bgonhover',
                    [
                        'label' => esc_html__( 'On hover BG', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .woosbymdz-header-divs:hover' => 'background-color: {{VALUE}} '.$all_important.'',
                        ],
                    ]
                );
        
                $ins->add_control(//header els shadow color on hover
                    'header_elsstyle_shadowonhover',
                    [
                        'label' => esc_html__( 'On hover shadow', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .woosbymdz-header-divs:hover' => 'box-shadow: 0px 5px 10px -4px {{VALUE}} '.$all_important.';',
                        ],
                    ]
                );
        
                $ins->add_control(//header els bg color on active
                    'header_elsstyle_bgonactive',
                    [
                        'label' => esc_html__( 'Active BG', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .woosbymdz-header-divs-active' => 'background-color: {{VALUE}} '.$all_important.'',
                        ],
                    ]
                );
        
                $ins->add_control(//header els shadow color on active
                    'header_elsstyle_shadowonactive',
                    [
                        'label' => esc_html__( 'Active shadow', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .woosbymdz-header-divs-active' => 'box-shadow: 0px 5px 10px -4px {{VALUE}} '.$all_important.';',
                        ],
                    ]
                );
        
                $ins->end_controls_section();
        
                break;

            case '4':
            case 4:
                $ins->start_controls_section(//woosbymdz products wrapper
                    'sliderwrapper_style',
                    [
                        'label' => esc_html__( 'Products container', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    ]
                );
        
                $ins->add_responsive_control(//products wrapper padding
                    'sliderwrapper_style_padding', 
                    [
                    'label' => esc_html__( 'Padding', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .woosbymdz-swiper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                    ],
                ]);
        
                $ins->add_responsive_control(//products wrapper margin
                    'sliderwrapper_margin', 
                    [
                    'label' => esc_html__( 'Margin', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .swiper-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                    ],
                ]);
                
        
                $ins->end_controls_section();
                break;

            case '5':
            case 5:
                $ins->start_controls_section(//woosbymdz products
                    'products_style',
                    [
                        'label' => esc_html__( 'Products', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    ]
                );
        
                $ins->add_group_control(//products bg-color
                    \Elementor\Group_Control_Background::get_type(),
                    [
                        'name' => 'products_style_bg',
                        'label' => esc_html__( 'Background', 'wooslider-bymdez' ),
                        'types' => [ 'classic', 'gradient' ],
                        'selector' => '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide:not(.woosbymdz-seeallcat)',
                    ]
                );
        
                $ins->add_responsive_control(//products padding
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

                $ins->add_control(//Auto same-height slides?
                    'products_auto_sameh',
                    [
                        'label' => esc_html__('Same Height Slides?', 'wooslider-bymdez'),
                        'description' => esc_html__('Quickly and fully automatically, it matches the height of all the slides inside this slider', 'wooslider-bymdez'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => esc_html__( 'Yes', 'wooslider-bymdez' ),
                        'label_off' => esc_html__( 'No', 'wooslider-bymdez' ),
                        'return_value' => 'yes',
                        'default' => 'no',
                    ]
                );
        
                $ins->add_responsive_control(//products min-height
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
        
                $ins->add_group_control(//products border options
                    \Elementor\Group_Control_Border::get_type(),
                    [
                        'name' => 'products_border',
                        'label' => esc_html__( 'Products Border', 'wooslider-bymdez' ),
                        'selector' => '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide',
                    ]
                );
        
                $ins->add_responsive_control(//products border-radius
                    'products_borderradius', 
                    [
                    'label' => esc_html__( 'border-radius', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$pros_important.';',
                    ],
                ]);
        
                $ins->add_group_control(//products-image bg-color
                    \Elementor\Group_Control_Background::get_type(),
                    [
                        'name' => 'products_imgs_style_bg',
                        'label' => esc_html__( 'Image Background', 'wooslider-bymdez' ),
                        'types' => [ 'classic', 'gradient' ],
                        'selector' => '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide img',
                    ]
                );
        
                $ins->add_responsive_control(//products-image border-radius
                    'products_imgs_borderradius', 
                    [
                    'label' => esc_html__( 'Image border-radius', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$pros_important.';',
                    ],
                ]);
        
                $ins->add_responsive_control(//products-image padding
                    'products_imgs_padding', 
                    [
                    'label' => esc_html__( 'Image Padding', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide img' => 'padding-top: {{TOP}}{{UNIT}} '.$pros_important.';padding-left: {{LEFT}}{{UNIT}}'.$pros_important.'; padding-bottom: {{BOTTOM}}{{UNIT}}'.$pros_important.';padding-right: {{RIGHT}}{{UNIT}}'.$pros_important.';',
                    ],
                ]);
        
        
                $ins->add_group_control(//products box-shadow
                    \Elementor\Group_Control_Box_Shadow::get_type(),
                    [
                        'name' => 'products_boxshadow',
                        'label' => esc_html__( 'Products Box-Shadow', 'wooslider-bymdez' ),
                        'selector' => '{{WRAPPER}} .woosbymdz-carousel-con .swiper-slide',
                    ]
                );
        
                
        
                
        
                
        
                
        
                
        
                
        
                
        
                
        
                
        
                
        
                $ins->end_controls_section();
                break;

            case '6':
            case 6:
                $ins->start_controls_section(//arrows style
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
        
                $ins->add_control(//Left arrow color
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
        
                $ins->add_group_control(//Left arrow text-shadow
                    \Elementor\Group_Control_Text_Shadow::get_type(),
                    [
                        'name' => 'arrows_style_left_textshadow',
                        'label' => esc_html__( 'Left arrow shine', 'wooslider-bymdez' ),
                        'selector' => '{{WRAPPER}} .swiper-button-next',
                    ]
                );
        
                $ins->add_group_control(//Left arrow typo
                    \Elementor\Group_Control_Typography::get_type(),
                    [
                        'name' => 'arrows_style_left_typo',
                        'label' => esc_html__( 'Left arrow typo', 'wooslider-bymdez' ),
                        'selector' => '{{WRAPPER}} .swiper-button-next:after',
                    ]
                );
        
                $ins->add_control(//right arrow color
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
        
                $ins->add_group_control(//Right arrow text-shadow
                    \Elementor\Group_Control_Text_Shadow::get_type(),
                    [
                        'name' => 'arrows_style_right_textshadow',
                        'label' => esc_html__( 'Right arrow shine', 'wooslider-bymdez' ),
                        'selector' => '{{WRAPPER}} .swiper-button-prev',
                    ]
                );
        
                $ins->add_group_control(//Right arrow typo
                    \Elementor\Group_Control_Typography::get_type(),
                    [
                        'name' => 'arrows_style_right_typo',
                        'label' => esc_html__( 'Right arrow typo', 'wooslider-bymdez' ),
                        'selector' => '{{WRAPPER}} .swiper-button-prev:after',
                    ]
                );
        
                $ins->add_control(//left arrow bg
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
                $ins->add_group_control(//left arrow bg color
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
        
                $ins->add_responsive_control(//left arrow padding
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
        
                $ins->add_responsive_control(
                    'arrows_style_left_margin', 
                    [
                    'label' => esc_html__( 'Margin Left Arrow', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .swiper-button-next' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                    ],
                ]);
        
                $ins->add_control(//Right arrow bg
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
        
                $ins->add_group_control(//right arrow bg color
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
        
                $ins->add_responsive_control(//right arrow padding
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
        
                $ins->add_responsive_control(//right arrow margin
                    'arrows_style_right_margin', 
                    [
                    'label' => esc_html__( 'Margin Right Arrow', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .swiper-button-prev' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                    ],
                ]);
        
                $ins->add_control(//Arrows Hover effect
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
                
                $ins->end_controls_section();
                break;

            case '7':
            case 7:
                $ins->start_controls_section(//pagination style
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
        
                $ins->add_control(//pags color ِ Dots or Bullets
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
        
                $ins->add_control(//pags color Fraction
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
         
                $ins->add_group_control(//pags Fraction text-shadow
                    \Elementor\Group_Control_Text_Shadow::get_type(),
                    [
                        'name' => 'pags_style_fraction_textshadow',
                        'label' => esc_html__( 'Numbers shine', 'wooslider-bymdez' ),
                        'condition' 	=> ['swiper_navs' => 'fraction'],
                        'selector' => '{{WRAPPER}} .swiper-pagination span, swiper-pagination',
                    ]
                );
        
                $ins->add_group_control(//pags Fraction typo
                    \Elementor\Group_Control_Typography::get_type(),
                    [
                        'name' => 'pags_style_fractiontypo',
                        'label' => esc_html__( 'Numbers typo', 'wooslider-bymdez' ),
                        'condition' 	=> ['swiper_navs' => 'fraction'],
                        'selector' => '{{WRAPPER}} .swiper-pagination span, swiper-pagination',
                    ]
                );
        
                $ins->add_control(//pags color Progress
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
        
                $ins->add_control( //pags direction
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
        
                $ins->add_group_control(//pags bg
                    \Elementor\Group_Control_Background::get_type(),
                    [
                        'name' => 'pags_style_bg_selector',
                        'label' => esc_html__( 'Background ', 'wooslider-bymdez' ),
                        'types' => [ 'classic' ],
                        'selector' => '{{WRAPPER}} .swiper-pagination',
                    ]
                );
        
                $ins->add_control( //pags margin just on swiper coveflow type
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
        
                $ins->end_controls_section();
                break;

            case '8':
            case 8:
                $ins->start_controls_section(//404 styles
                    '404_style',
                    [
                        'label' => esc_html__( '404', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                        'condition' 	=> ['showonly_tologgedin' => 'yes'],
                    ]	
                );
        
                $ins->add_control(//custom icon img selector
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
        
                $ins->end_controls_section();
                break;
            case '9':
            case 9:

                $ins->start_controls_section(//woosbymdz products-title
                    'products_style_title',
                    [
                        'label' => esc_html__( 'Products Titles', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    ]
                );

                $ins->add_control(//Hidden products Title ?
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

                $ins->add_control(//centeralaized pro-title
                    'products_title_center',
                    [
                        'label' => esc_html__('Centeralaized Title (Horizontally)', 'wooslider-bymdez'),
                        'description' => esc_html__('Makes The title of the products inside the slider horizontally centered', 'wooslider-bymdez'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => esc_html__( 'Yes', 'wooslider-bymdez' ),
                        'label_off' => esc_html__( 'No', 'wooslider-bymdez' ),
                        'return_value' => 'yes',
                        'default' => 'no',
                    ]
                );

                $ins->add_control(//Products Title Color
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
        
                $ins->add_group_control(//Products Title BG changer
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
        
                $ins->add_group_control(//Products Title Typography
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
        
                $ins->add_group_control(//Products Title Shadow
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
        
                $ins->add_responsive_control(//Products Title padding
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
        
                $ins->add_control(//Products Title Border-radius
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


                $ins->end_controls_section();
                break;

            case '10':
            case 10:

                $ins->start_controls_section(//woosbymdz products-cats
                    'products_style_cats',
                    [
                        'label' => esc_html__( 'Products Categories', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    ]
                );

                $ins->add_control(//Hidden products cats ?
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

                $ins->add_control(//Products Cats Color
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
        
                $ins->add_group_control(//Products Cats BG changer
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
        
                $ins->add_group_control(//Products Cats Typography
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
        
                $ins->add_group_control(//Products Cats Shadow
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
        
                $ins->add_control(//Products Cats Border-radius
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

                $ins->end_controls_section();
                break;

            case '11':
            case 11:

                $ins->start_controls_section(//woosbymdz products-price
                    'products_style_price',
                    [
                        'label' => esc_html__( 'Products Prices', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    ]
                );

                $ins->add_control(//Hidden products Price ?
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

                $ins->add_control(//Products Price normal Color
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
        
                $ins->add_control(//Products Price del Color
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
        
                $ins->add_group_control(//Products Price BG changer
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
        
                $ins->add_control(//Products Price Border-radius
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
        
                $ins->add_group_control(//Products Price Typography
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
        
                $ins->add_responsive_control(//Products Price padding
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
        
                $ins->add_responsive_control(//Products Price margin
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

                $ins->end_controls_section();

                break;

            case '12':
            case 12:

                $ins->start_controls_section(//woosbymdz products-label
                    'products_style_label',
                    [
                        'label' => esc_html__( 'Products Labels', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    ]
                );

                $ins->add_control(//Hidden products Labels ?
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

                $ins->add_control(//Product label Absolute Position
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
        
                
        
                $ins->add_control(//Products Label Border-radius
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
        
                $ins->add_control(//Products Label txt Color
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
        
                $ins->add_group_control(//Products Label Typography
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
        
                $ins->add_group_control(//Products Label Box-Shadow
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

                $ins->add_responsive_control(//Products labels position_top
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
        
                $ins->add_responsive_control(//Products labels position_bottom
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
        
                $ins->add_responsive_control(//Products labels position_right
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
        
                $ins->add_responsive_control(//Products labels position_left
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
        
                $ins->add_group_control(//Products labels BG changer
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
        
                $ins->add_responsive_control(//Products labels padding
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

                $ins->end_controls_section();
                break;

            case '13':
            case 13:

                $ins->start_controls_section(//woosbymdz products-add-btn
                    'products_style_addbtn',
                    [
                        'label' => esc_html__( 'Products Add-Cart Button', 'wooslider-bymdez' ),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    ]
                );

                $ins->add_control(//Hidden products add-to-cart btn ?
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

                $ins->add_control(//Products Add-Btn Color
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
        
                $ins->add_group_control(//Products Add-Btn BG
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
        
                $ins->add_group_control(//Products Add btn Typography
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
        
                $ins->add_control(//Products Add-Btn Border-radius
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
        
                $ins->add_responsive_control(//Products Add-Btn padding
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
        
                $ins->add_responsive_control(//Products Add-Btn Margin
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

                $ins->end_controls_section();
                break;

            case 14:
            case '14':
                $ins->start_controls_section(//arrows style (Loop builder)
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
        
                $ins->add_control(//Left arrow color
                    'arrows_style_leftcolor',
                    [
                        'label' => esc_html__( 'Left arrow color', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'default' => '#007aff',
                        'selectors' => [
                            '{{WRAPPER}} .elementor-swiper-button-next svg path' => 'fill: {{VALUE}} '.$all_important.'',
                        ],
                    ]
                );
        
        
                $ins->add_responsive_control(//Left arrow svg width
                    'arrows_style_leftwid', 
                    [
                    'label' => esc_html__( 'Width', 'wooslider-bymdez' ),
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
                        '{{WRAPPER}} .elementor-swiper-button-next svg' => 'width: {{SIZE}}{{UNIT}} '.$all_important.';',
                    ],
                ]);
        
                $ins->add_control(//right arrow color
                    'arrows_style_rightcolor',
                    [
                        'label' => esc_html__( 'Right arrow color', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'default' => '#007aff',
                        'selectors' => [
                            '{{WRAPPER}} .elementor-swiper-button-prev svg path' => 'fill: {{VALUE}} '.$all_important.'',
                        ],
                    ]
                );
        
        
                $ins->add_responsive_control(//right arrow svg width
                    'arrows_style_rightwid', 
                    [
                    'label' => esc_html__( 'Width', 'wooslider-bymdez' ),
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
                        '{{WRAPPER}} .elementor-swiper-button-prev svg' => 'width: {{SIZE}}{{UNIT}} '.$all_important.';',
                    ],
                ]);
        
                $ins->add_group_control(//left arrow bg color
                    \Elementor\Group_Control_Background::get_type(),
                    [
                        'name' => 'arrows_style_leftbg_selector',
                        'label' => esc_html__( 'Left arrow bg-color', 'wooslider-bymdez' ),
                        'types' => [ 'classic' ],
                        'selector' => '{{WRAPPER}} .elementor-swiper-button-next',
                    ]
                );

                $ins->add_group_control(//left arrow border
                    \Elementor\Group_Control_Border::get_type(),
                    [
                        'name' => 'arrows_style_leftborder',
                        'label' => esc_html__( 'Border', 'wooslider-bymdez' ),
                        'selector' => '{{WRAPPER}} .elementor-swiper-button-next',
                    ]
                );
        
                $ins->add_control(//left arrwo border-radius
                    'arrows_style_leftrad',
                    [
                        'label' => esc_html__( 'Border-Radius', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%' ],
                        'selectors' => [
                            '{{WRAPPER}} .elementor-swiper-button-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                        ],
                    ]
                );
        
                $ins->add_responsive_control(//left arrow padding
                    'arrows_style_left_padding', 
                    [
                    'label' => esc_html__( 'Padding Left-Arrow', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-swiper-button-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                    ],
                ]);
        
                $ins->add_responsive_control(//left arrow margin
                    'arrows_style_left_margin', 
                    [
                    'label' => esc_html__( 'Margin Left Arrow', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-swiper-button-next' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                    ],
                ]);
        

                $ins->add_group_control(//right arrow bg color
                    \Elementor\Group_Control_Background::get_type(),
                    [
                        'name' => 'arrows_style_rightbg_selector',
                        'label' => esc_html__( 'Right arrow bg-color', 'wooslider-bymdez' ),
                        'types' => [ 'classic' ],
                        'selector' => '{{WRAPPER}} .elementor-swiper-button-prev',
                    ]
                );

                $ins->add_group_control(//right arrow border
                    \Elementor\Group_Control_Border::get_type(),
                    [
                        'name' => 'arrows_style_rightborder',
                        'label' => esc_html__( 'Border', 'wooslider-bymdez' ),
                        'selector' => '{{WRAPPER}} .elementor-swiper-button-prev',
                    ]
                );
        
                $ins->add_control(//right arrwo border-radius
                    'arrows_style_rightrad',
                    [
                        'label' => esc_html__( 'Border-Radius', 'wooslider-bymdez' ),
                        'type' => \Elementor\Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%' ],
                        'selectors' => [
                            '{{WRAPPER}} .elementor-swiper-button-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                        ],
                    ]
                );
        
                $ins->add_responsive_control(//right arrow padding
                    'arrows_style_right_padding', 
                    [
                    'label' => esc_html__( 'Padding Right-Arrow', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-swiper-button-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                    ],
                ]);
        
                $ins->add_responsive_control(//right arrow margin
                    'arrows_style_right_margin', 
                    [
                    'label' => esc_html__( 'Margin Right Arrow', 'wooslider-bymdez' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .elementor-swiper-button-prev' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} '.$all_important.';',
                    ],
                ]);
                
                $ins->end_controls_section();
                break;

        }

    }

}//close the PHP tag to reduce the blank spaces ?>