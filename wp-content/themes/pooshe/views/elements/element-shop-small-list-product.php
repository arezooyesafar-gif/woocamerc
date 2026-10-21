<?php
namespace PoosheElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Query;
use PoosheElementor\Pooshe_Current_Query_Renderer;
use PoosheElementor\Pooshe_Products_Renderer;

class Elementor_shop_small_list_product_Widget extends \Elementor\Widget_Base {

    public function get_script_depends() {
        return [ 'script-pooshe_shop_small-product' ];
    }
    public function get_name() {
        return 'pooshe_shop_small-product';
    }
    public function get_title() {
        return 'محصولات لیست کوچک';
    }
    public function get_icon() {
        return 'eicon-bullet-list';
    }
    public function get_categories() {
        return [ 'pooshe_main' ];
    }
    protected function _register_controls() {


        $this->start_controls_section(
            'content_section',
            [
                'label' => 'محتوا',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_group_control(
            Group_Control_Query::get_type(),
            [
                'name' => Pooshe_Products_Renderer::QUERY_CONTROL_NAME,
                'post_type' => 'product',
                'presets' => [ 'full' ],
                'fields_options' => [
                    'post_type' => [
                        'default' => 'product',
                        'options' => [
                            'product' => __( 'Latest Products', 'elementor-pro' ),
                            'sale' => __( 'Sale', 'elementor-pro' ),
                            'featured' => __( 'Featured', 'elementor-pro' ),
                            'by_id' => _x( 'Manual Selection', 'Posts Query Control', 'elementor-pro' ),
                        ],
                    ],
                    'orderby' => [
                        'default' => 'date',
                        'options' => [
                            'date' => __( 'Date', 'elementor-pro' ),
                            'title' => __( 'Title', 'elementor-pro' ),
                            'price' => __( 'Price', 'elementor-pro' ),
                            'popularity' => __( 'Popularity', 'elementor-pro' ),
                            'rating' => __( 'Rating', 'elementor-pro' ),
                            'rand' => __( 'Random', 'elementor-pro' ),
                            'menu_order' => __( 'Menu Order', 'elementor-pro' ),
                        ],
                    ],
                    'exclude' => [
                        'options' => [
                            'current_post' => __( 'Current Post', 'elementor-pro' ),
                            'manual_selection' => __( 'Manual Selection', 'elementor-pro' ),
                            'terms' => __( 'Term', 'elementor-pro' ),
                        ],
                    ],
                    'include' => [
                        'options' => [
                            'terms' => __( 'Term', 'elementor-pro' ),
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
        $this->add_control(
            Pooshe_Products_Renderer::QUERY_CONTROL_NAME.'_ppp', [
                'label' => 'تعداد محصول',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default'   =>  8
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
            'items_width',
            [
                'label' => 'اندازه آیکن ها',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 80,
                'selectors' => [
                    '{{WRAPPER}} .post-item figure.thumbnail' => 'height: {{VALUE}}px; width: {{VALUE}}px',
                ],
            ]
        );
        $this->add_control(
            'items_margin',
            [
                'label' => 'فاصله آیتم ها',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 5,
                'selectors' => [
                    '{{WRAPPER}} .post-item' => 'margin: {{VALUE}}px 0',
                ],
            ]
        );
        $this->end_controls_section();


    }

    protected function get_shortcode_object( $settings ) {
        $type = 'products';
        return new Pooshe_Products_Renderer( $settings, $type );
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="pooshe-elementor-widget pooshe-elementor-shop-small-products-list">
            <?php echo $this->get_shortcode_object($settings)->get_custom_content(); ?>
        </div>
        <?php
    }

}
