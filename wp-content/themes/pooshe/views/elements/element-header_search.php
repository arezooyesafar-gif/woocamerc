<?php
namespace PoosheElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_header_search_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'pooshe_header_search';
    }
    public function get_title() {
        return 'جستجوی پیشرفته';
    }
    public function get_icon() {
        return 'eicon-site-search';
    }
    public function get_categories() {
        return [ 'pooshe_header' ];
    }
    protected function _register_controls() {
        $this->start_controls_section(
            'search_content_section',
            [
                'label' => 'محتوا',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'is_ajax',
            [
                'label' => 'جستجوی زنده',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'has_filter',
            [
                'label' => 'فیلتر دسته بندی',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'parent_categories',
            [
                'label' => 'فقط دسته بندی های مادر',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'has_filter' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'placeholder',
            [
                'label' => 'متن جایگزین',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'جستجو در بین محصولات...',
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'search_style_section',
            [
                'label' => 'رنگ ها',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'background',
            [
                'label' => 'رنگ جعبه',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f5f5f5',
                'selectors' => [
                    '{{WRAPPER}} .header-search .search-field' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'background_focus',
            [
                'label' => 'رنگ جعبه حالت کلیک',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .header-search .search-field:focus' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'text_color',
            [
                'label' => 'رنگ متون',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .header-search .form-actions .action-item' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .header-search .form-control::placeholder' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .header-search .form-control' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .header-search .form-actions .categories .header-search-select2 + .select2-container .select2-selection--single .select2-selection__rendered' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__arrow b' => 'border-color: {{VALUE}} transparent transparent transparent',
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'search_design_section',
            [
                'label' => 'طرح ها',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'search_design',
            [
                'label' => 'انتخاب طرح',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => 'پیشفرض',
                    'solid'  => 'ساده',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'search_style_custom',
            [
                'label' => 'استایل ها',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'width',
            [
                'label' => "ارتفاع",
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .search-field' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        $args = [
            'orderby' => 'name',
            'taxonomy'  =>  'product_cat'
        ];
        if($settings['parent_categories'] == "yes"){$args['parent'] = 0;}
        $cat_tmp = get_categories($args);
        $categories = [[
            'id'    =>  '',
            'name'  =>  __('همه دسته ها','pooshe')
        ]];
        if(!empty($cat_tmp) and !cb_is_empty($settings['has_filter']) and $settings['has_filter'] == "yes"){
            foreach ($cat_tmp as $item){
                $categories[] = [
                        'id'    =>  $item->term_id,
                        'name'    =>  esc_html($item->name),
                ];
            }
        }

        ?>
        <div class="pooshe-elementor-widget">
            <div class="header-search <?php echo $settings['is_ajax'] == "yes"?"is-ajax":""; ?> <?php echo $settings['search_design'] == "solid"?"design_basic":""; ?>">
                <form class="search_form" action="<?php echo site_url(); ?>">
                    <input type="hidden" name="post_type" value="product">
                    <div class="inner">
                        <input type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" class="search-field form-control"
                               placeholder="<?php echo esc_attr($settings['placeholder']); ?>" autocomplete="off">
                        <div class="form-actions">
                            <button type="submit" class="action-item search-btn">
                                <span class="search-icon"><i class="far fa-search"></i></span>
                                <span class="loading"><i class="fa-spin far fa-spinner"></i></span>
                                <span class="typing-animated"><span></span><span></span><span></span></span>
                            </button>
                            <?php if($settings['has_filter'] == "yes" and !empty($categories)){ ?>
                            <div class="categories">
                                <select name="pcat" class="header-search-select2">
                                    <?php foreach ($categories as $category){
                                        echo '<option value="'.$category['id'].'" ';
                                        selected(isset($_GET['pcat'])&&$_GET['pcat']==$category['id']);
                                        echo '>'.$category['name'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php } ?>
                        </div>
                        <?php if($settings['is_ajax'] == "yes"){ ?>
                        <div class="search-results">
                            <div class="csb-outer">
                                <ul class="csb">

                                </ul>
                            </div>
                            <div class="search-not-found">
                                <i class="fal fa-info"></i>
                                <?php _e('نتیجه ای یافت نشد!','pooshe'); ?>
                            </div>
                            <div class="search-result-archive">
                                <button type="submit"><i class="fal fa-search"></i>
                                    <?php _e(' دیدن همه نتایج','pooshe'); ?>
                                </button>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </form>
            </div>









            <!--product-search
                <?php echo !empty($categories) ?'v-bind:filter="true" v-bind:cats="'.esc_html(json_encode($categories)).'"':''; ?>
                <?php echo $settings['is_ajax'] == 'yes' ?'v-bind:ajax="true"':''; ?>
                <?php echo get_search_query()?'text="'.get_search_query().'"':''; ?>
                    holder="<?php echo esc_attr($settings['placeholder']); ?>"
            ></product-search-->
        </div>
<?php
    }

}
