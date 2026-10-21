<?php

namespace PoosheElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Query;
use PoosheElementor\Pooshe_Current_Query_Renderer;
use PoosheElementor\Pooshe_Products_Renderer;

class Elementor_learn_carousel_Widget extends \Elementor\Widget_Base
{
    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
    }

    public function get_name()
    {
        return 'pooshe_learn_carousel';
    }

    public function get_title()
    {
        return 'کارت دوره';
    }

    public function get_icon()
    {
        return 'eicon-product-categories';
    }

    public function get_categories()
    {
        return ['pooshe_main'];
    }

    protected function _register_controls()
    {

        $this->start_controls_section(
            'carousel_section',
            [
                'label' => 'تنظیمات',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_responsive_control(
            'slides_per_view', [
                'label' => 'تعداد محصول در هر ردیف',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'frontend_available' => true,
                'desktop_default' => 4,
                'tablet_default' => 2,
                'mobile_default' => 1
            ]
        );

        $this->add_control(
            'is_carousel',
            [
                'label' => 'حالت کریوسل',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => 'حالت بی نهایت',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'is_carousel' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => 'پخش خودکار',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'is_carousel' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'navs',
            [
                'label' => 'اشاره گرها',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'is_carousel' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'navs_style',
            [
                'label' => 'طرح اشاره گرها',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'middle',
                'options' => [
                    'top' => 'بالای اسلایدر',
                    'middle' => 'روی اسلایدر',
                ],
                'condition' => [
                    'navs' => 'yes',
                    'is_carousel' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'titles',
            [
                'label' => 'عنوان ابزارک',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'features',
            [
                'label' => 'ویژگی های محصول',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'card_section',
            [
                'label' => 'کارت محصول',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'card_image',
            [
                'label' => 'تصویر محصول',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'block',
                'options' => [
                    'block' => 'نمایش',
                    'none' => 'مخفی',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .product-card .thumbnail' => 'display: {{VALUE}} !important',
                ]
            ]
        );
        $this->add_control(
            'card_title',
            [
                'label' => 'عنوان محصول',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'block',
                'options' => [
                    'block' => 'نمایش',
                    'none' => 'مخفی',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .product-card .woocommerce-loop-product__title' => 'display: {{VALUE}} !important',
                ]
            ]
        );
        $this->add_control(
            'card_star',
            [
                'label' => 'امتیاز محصول',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'block',
                'options' => [
                    'block' => 'نمایش',
                    'none' => 'مخفی',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .product-card .product-meta .star-rating' => 'display: {{VALUE}} !important',
                ]
            ]
        );
        $this->add_control(
            'card_cat',
            [
                'label' => 'دسته بندی محصول',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'block',
                'options' => [
                    'block' => 'نمایش',
                    'none' => 'مخفی',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .product-card .product-meta .posted_in' => 'display: {{VALUE}} !important',
                ]
            ]
        );
        $this->add_control(
            'card_time',
            [
                'label' => 'زمان فروش فوق العاده محصول',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'flex',
                'options' => [
                    'flex' => 'نمایش',
                    'none' => 'مخفی',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .product-card .product-time' => 'display: {{VALUE}} !important',
                ]
            ]
        );
        $this->add_control(
            'card_footer',
            [
                'label' => 'دکمه های محصول',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'flex',
                'options' => [
                    'flex' => 'نمایش',
                    'none' => 'مخفی',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .product-card .cardFooter' => 'display: {{VALUE}} !important',
                ]
            ]
        );
        $this->add_control(
            'show_badges',
            [
                'label' => 'نشان‌های محصول',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'block',
                'options' => [
                    'block' => 'نمایش',
                    'none' => 'مخفی',
                ],
                'selectors' => [
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .product-card .pooshe-badges' => 'display: {{VALUE}} !important',
                ]
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'style_section',
            [
                'label' => 'طراحی',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'card_style',
            [
                'label' => 'طرح کارت محصول',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => 'پیشفرض',
                    'modern' => 'مدرن',
                ],
            ]
        );
        $this->add_control(
            'dark',
            [
                'label' => 'حالت تیره',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $colors = pooshe_get_colors();
        extract($colors);
        $this->add_control(
            'color',
            [
                'label' => 'رنگ کلی',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => $colors->$color_main,
                'selectors' => [
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .swiper-container:not(.carousel-navs-top) .pooshe-carousel-buttons.carousel-archive-link:not(:hover)' => 'background-color: {{VALUE}} !important',
                    '{{WRAPPER}} .product-card .cardFooter .cardFooter-left .addtocart a' => 'background-color: {{VALUE}} !important',
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .carousel-navs-top .pooshe-carousel-buttons:hover' => 'background-color: {{VALUE}} !important',
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .carousel-navs-middle .pooshe-carousel-buttons:not(:hover)' => 'background-color: {{VALUE}} !important',
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .swipper-loading' => 'background-color: {{VALUE}} !important',
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .swipper-loading .pooshe-loader.front' => 'border-color: {{VALUE}} !important',
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .swipper-loading .pooshe-loader.back' => 'border-color: {{VALUE}} !important',
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .widget-title ul li::before' => 'background-color: {{VALUE}} !important',
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .widget-title ul li.active' => 'color: {{VALUE}} !important',
                    '{{WRAPPER}} .product-card .product-time ul' => 'color: {{VALUE}} !important',
                    '{{WRAPPER}} .product-card .woocommerce-loop-product__title:hover' => 'color: {{VALUE}} !important',
                    '{{WRAPPER}} .product-card .product-meta .posted_in a:hover' => 'color: {{VALUE}} !important',
                    '{{WRAPPER}} .product-card .cardFooter .button.liked' => 'color: {{VALUE}} !important',
                ],
            ]
        );
        $this->add_control(
            'color_hover',
            [
                'label' => 'رنگ کلی حالت هاور',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => $colors->$color_btn_hover,
                'selectors' => [
                    '{{WRAPPER}} .product-card .cardFooter .cardFooter-left .addtocart a:hover' => 'background-color: {{VALUE}} !important',
                ],
            ]
        );
        $this->add_control(
            'color_tab',
            [
                'label' => 'رنگ تب',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .widget-title ul li' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'color_tab_active',
            [
                'label' => 'رنگ تب فعال',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => $colors->$color_main,
                'selectors' => [
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .widget-title ul li::before' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .pooshe-elementor-shop-carousel .widget-title ul li.active' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'content_section',
            [
                'label' => 'محتوا',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'title', [
                'label' => 'عنوان',
                'type' => \Elementor\Controls_Manager::TEXT,
            ]
        );
        $repeater->add_control(
            'archive', [
                'label' => 'لینک آرشیو',
                'type' => \Elementor\Controls_Manager::TEXT,
            ]
        );
        $repeater->add_group_control(
            Group_Control_Query::get_type(),
            [
                'name' => Pooshe_Products_Renderer::QUERY_CONTROL_NAME,
                'post_type' => 'product',
                'presets' => ['full'],
                'fields_options' => [
                    'post_type' => [
                        'default' => 'product',
                        'options' => [
                            'product' => __('Latest Products', 'elementor-pro'),
                            'sale' => __('Sale', 'elementor-pro'),
                            'featured' => __('Featured', 'elementor-pro'),
                            'by_id' => _x('Manual Selection', 'Posts Query Control', 'elementor-pro'),
                        ],
                    ],
                    'orderby' => [
                        'default' => 'date',
                        'options' => [
                            'date' => __('Date', 'elementor-pro'),
                            'title' => __('Title', 'elementor-pro'),
                            'price' => __('Price', 'elementor-pro'),
                            'popularity' => __('Popularity', 'elementor-pro'),
                            'rating' => __('Rating', 'elementor-pro'),
                            'rand' => __('Random', 'elementor-pro'),
                            'menu_order' => __('Menu Order', 'elementor-pro'),
                        ],
                    ],
                    'exclude' => [
                        'options' => [
                            'current_post' => __('Current Post', 'elementor-pro'),
                            'manual_selection' => __('Manual Selection', 'elementor-pro'),
                            'terms' => __('Term', 'elementor-pro'),
                        ],
                    ],
                    'include' => [
                        'options' => [
                            'terms' => __('Term', 'elementor-pro'),
                        ],
                    ],
                ],
                'exclude' => [
                    'posts_per_page',
                    'exclude_authors',
                    'authors',
                    'offset',
                    'related_fallback',
                    'related_ids',
                    'query_id',
                    'avoid_duplicates',
                    'ignore_sticky_posts',
                ],
            ]
        );
        $repeater->add_control(
            Pooshe_Products_Renderer::QUERY_CONTROL_NAME . '_ppp', [
                'label' => 'تعداد محصول',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 8
            ]
        );
        $this->add_control(
            'tabs',
            [
                'label' => 'زبانه ها',
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'prevent_empty' => false,
                'default' => [
                    [
                        'title' => 'جدیدترین ها',
                    ],
                ],
            ]
        );


        $this->end_controls_section();


    }

    protected function get_shortcode_object($settings, $repeater, $index)
    {
        $type = 'products';
        return new Pooshe_Products_Renderer($settings[$repeater][$index], $type);
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $carousel = [
            "slidesPerView" => $settings['slides_per_view_mobile'],
            "loop" => wc_string_to_bool($settings['loop']),
            "spaceBetween" => 0,
            "breakpoints" => [
                480 => [
                    "slidesPerView" => $settings['slides_per_view_mobile']
                ],
                768 => [
                    "slidesPerView" => $settings['slides_per_view_tablet']
                ],
                992 => [
                    "slidesPerView" => $settings['slides_per_view']
                ]
            ]
        ];
        if (wc_string_to_bool($settings['navs'])) {
            $carousel['navigation'] = [
                'nextEl' => '.swiper-button-next',
                'prevEl' => '.swiper-button-prev',
            ];
        }
        if (wc_string_to_bool($settings['autoplay'])) {
            $carousel['autoplay'] = [
                'delay' => 5000,
            ];
        }
        $classes = ['widget-container'];
        if (wc_string_to_bool($settings['is_carousel'])) {
            $classes[] = 'swiper-container';
        }
        if (wc_string_to_bool($settings['navs'])) {
            $classes[] = 'carousel-navs-' . $settings['navs_style'];
        } else {
            $classes[] = 'carousel-navs-disabled';
        }
        if (wc_string_to_bool($settings['dark'])) {
            $classes[] = 'card-black-style';
        }
        if (wc_string_to_bool($settings['features'])) {
            $classes[] = 'card-has-hover';
        }
        $wigtet_id = $this->get_id_int();
        $columns = 'col-' . (int)(12 / round($settings['slides_per_view_mobile'], 0)) . ' col-lg-' . (int)(12 / round($settings['slides_per_view'], 0)) . ' col-sm-' . (int)(12 / round($settings['slides_per_view_tablet'], 0));
        ?>
        <div class="pooshe-elementor-widget pooshe-elementor-shop-carousel">
            <div class="<?php echo implode(' ', $classes); ?>" data-options='<?php echo json_encode($carousel); ?>'>
                <?php if (wc_string_to_bool($settings['titles'])) { ?>
                    <div class="widget-title">
                        <button class="btn btn-secondary d-block d-lg-none responsive-btn" data-bs-toggle="modal"
                                data-bs-target="#carouselWidgetTabs-<?php echo $wigtet_id; ?>"><i class="far fa-bars"></i>
                        </button>
                        <!-- CarouselResponsiveTabs -->
                        <div class="modal fade carouselWidgetTabs" id="carouselWidgetTabs-<?php echo $wigtet_id; ?>"
                             tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"></h5>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span class="btn-over"><?php _e('بستن پنجره', 'pooshe'); ?></span>
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <ul>
                                            <?php
                                            $tabs = $settings['tabs'];
                                            $options = $tabs;
                                            foreach ($tabs as $tab) {
                                                $opts = $tab;
                                                $opts['is_carousel'] = wc_string_to_bool($settings['is_carousel']);
                                                $opts['columns'] = $columns;
                                                echo '<li data-options=\'' . addslashes(string_compress_encode(json_encode($opts))) . '\' class="' . ($tabs[0] == $tab ? 'active' : '') . '">' . $tab['title'] . '</li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul class="d-none d-lg-flex">
                            <?php
                            $tabs = $settings['tabs'];
                            $options = $tabs;
                            foreach ($tabs as $tab) {
                                $opts = $tab;
                                $opts['is_carousel'] = wc_string_to_bool($settings['is_carousel']);
                                $opts['columns'] = $columns;
                                echo '<li data-options=\'' . addslashes(string_compress_encode(json_encode($opts))) . '\' class="' . ($tabs[0] == $tab ? 'active' : '') . '">' . $tab['title'] . '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <a href="<?php echo $tabs[0]['archive']; ?>"
                       class="pooshe-carousel-buttons carousel-archive-link"><?php _e('دیدن همه', 'pooshe'); ?></a>
                <?php } ?>
                <?php if (wc_string_to_bool($settings['is_carousel']) and wc_string_to_bool($settings['navs'])) { ?>
                    <div class="swiper-button-prev pooshe-carousel-buttons">
                        <i class="far fa-arrow-right"></i>
                    </div>
                    <div class="swiper-button-next pooshe-carousel-buttons">
                        <i class="far fa-arrow-left"></i>
                    </div>
                <?php } ?>
                <?php
                if (wc_string_to_bool($settings['is_carousel'])) {
                    echo $this->get_shortcode_object($settings, 'tabs', '0')->get_content('<div class="swiper-wrapper">', '</div>', '<div class="swiper-slide"><ul>', '</ul></div>', $settings['card_style']);
                } else {
                    echo $this->get_shortcode_object($settings, 'tabs', '0')->get_content('<div class="row">', '</div>', '<div class="' . $columns . '"><ul>', '</ul></div>');
                }
                ?>
                <div class="swipper-loading">
                    <div class="pooshe-loader back"></div>
                    <div class="pooshe-loader front"></div>
                    <span class="text"><?php _e('لطفا صبر کنید...'); ?></span>
                    <i class="far fa-spin fa-spinner"></i>
                </div>
            </div>
        </div>
        <?php
    }

}
