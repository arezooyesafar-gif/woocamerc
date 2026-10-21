<?php
namespace PoosheElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_dark_mode_toggle_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'pooshe_dark_mode_toggle';
    }
    public function get_title() {
        return 'حالت تاریک/روشن';
    }
    public function get_icon() {
        return 'eicon-adjust';
    }
    public function get_categories() {
        return [ 'pooshe_header' ];
    }
    protected function _register_controls() {
        $this->start_controls_section('style_section', [
            'label' => 'استایل',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('icon_size', [
            'label'   => 'سایز آیکون',
            'type'    => Controls_Manager::NUMBER,
            'default' => 20,
            'min'     => 12,
            'max'     => 40,
        ]);

        $this->add_control('icon_color_light', [
            'label'   => 'رنگ آیکون (حالت روشن)',
            'type'    => Controls_Manager::COLOR,
            'default' => '#f59e0b',
        ]);

        $this->add_control('icon_color_dark', [
            'label'   => 'رنگ آیکون (حالت تاریک)',
            'type'    => Controls_Manager::COLOR,
            'default' => '#94a3b8',
        ]);

        $this->add_control('bg_color', [
            'label'   => 'رنگ پس‌زمینه',
            'type'    => Controls_Manager::COLOR,
            'default' => '#1e293b',
        ]);

        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        $size = !empty($settings['icon_size']) ? intval($settings['icon_size']) : 20;
        ?>
        <div class="pooshe-dark-mode-toggle-widget">
            <button class="pooshe-dark-mode-toggle" type="button"
                data-icon-size="<?php echo esc_attr($size); ?>"
                data-color-light="<?php echo esc_attr($settings['icon_color_light']); ?>"
                data-color-dark="<?php echo esc_attr($settings['icon_color_dark']); ?>"
                data-bg="<?php echo esc_attr($settings['bg_color']); ?>"
                aria-label="حالت تاریک/روشن">
                <span class="toggle-track">
                    <span class="toggle-sun"><i class="fas fa-sun"></i></span>
                    <span class="toggle-thumb"></span>
                    <span class="toggle-moon"><i class="fas fa-moon"></i></span>
                </span>
            </button>
        </div>
        <?php
    }
}
