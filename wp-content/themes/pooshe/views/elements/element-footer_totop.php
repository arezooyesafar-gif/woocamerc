<?php
namespace PoosheElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_footer_totop extends \Elementor\Widget_Base {
    public function get_name() {
        return 'pooshe_footer_toptop';
    }
    public function get_title() {
        return 'بالابر';
    }
    public function get_icon() {
        return 'eicon-arrow-up';
    }
    public function get_categories() {
        return [ 'pooshe_footer' ];
    }
    protected function _register_controls() {

    }
    protected function render() {
        $settings = $this->get_settings_for_display();

        ?>
        <div class="pooshe-elementor-widget">
            <div class="to-top"><i class="far fa-arrow-up"></i></div>
        </div>  
<?php
    }

}
