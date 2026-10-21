<?php
namespace PoosheElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_footer_copyright extends \Elementor\Widget_Base {
    public function get_name() {
        return 'pooshe_footer_copyright';
    }
    public function get_title() {
        return 'کپی رایت';
    }
    public function get_icon() {
        return 'eicon-shortcode';
    }
    public function get_categories() {
        return [ 'pooshe_footer' ];
    }
    protected function _register_controls() {

        $this->start_controls_section(
            'cp_content_section',
            [
                'label' => 'اطلاعات',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'text',
            [
                'label' => 'متن کپی رایت',
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => ' تمامی حقوق مادی و معنوی این وب سایت متعلق به پوشه می باشد. / <a href="https://coderboy.ir">طراح: پسرک کدنویس</a>',
            ]
        );

        $this->end_controls_section();


    }
    protected function render() {
        $settings = $this->get_settings_for_display();

        ?>
        <div class="pooshe-elementor-widget">
            <div class="footer-copyright"><?php echo $settings['text']; ?></div>
        </div>  
<?php
    }

}