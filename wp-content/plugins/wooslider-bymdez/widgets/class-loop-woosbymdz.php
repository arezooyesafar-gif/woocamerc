<?php
namespace ElementorPro\Modules\LoopBuilder\Widgets;

use ElementorPro\Modules\LoopBuilder\Documents\Loop as LoopDocument;
use Elementor\Controls_Manager;
use ElementorPro\Plugin;
use ElementorPro\Base\Base_Carousel_Trait;

use Woosbymdz\Widgets\Controls\controls as conts;
use Woosbymdz\Helpers\Main\main as help;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'controls'.DIRECTORY_SEPARATOR.'wooslider-controls.php';
require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'main.php';

if(class_exists('ElementorPro\Modules\LoopBuilder\Widgets\Base')){

	class Loop_Woosbymdz extends Base {

		use Base_Carousel_Trait;

		public function get_name() {
			return 'loop-slider-mdz';
		}

		public function get_title() {
			return esc_html__( 'Loop Wooslider', 'wooslider-bymdez' );
		}

		public function get_keywords() {
			return [ 'loop', 'build', 'builder', 'loop-builder', 'carousel', 'dynamic', 'listing', 'archive', 'blog', 'repeater', 'grid', 'products', 'posts', 'portfolio', 'cpt ', 'query', 'custom post type', 'سازنده', 'حلقه', 'سازنده حلقه', 'حلقه ساز', 'اسلایدر حلقه' ];
		}

		public function get_icon() {
			return 'woosbymdz-cion-loop';
		}

		/**
		 * Get style dependencies.
		 *
		 * Retrieve the list of style dependencies the widget requires.
		 *
		 * @since 3.24.0
		 * @access public
		 *
		 * @return array Widget style dependencies.
		 */
		public function get_style_depends(): array {

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
						'widget-loop-carousel',
					];

				}else if($dont_import_swiper == '1'){

					return [
						'woosbymdz-frontcss',
						'widget-loop-carousel',
					];

				}
				
			}else return ['widget-loop-carousel'];*/

			$class_helper = help::get_instance();
			$tmps_arr = $class_helper->print_slider_css_deps();

			return $tmps_arr;


			//return [ 'widget-loop-carousel' ];
		}

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

		protected function get_initial_config() {
			$config = parent::get_initial_config();

			$config['add_parent_render_footer'] = false;

			return $config;
		}

		public function register_settings_section_controls() {
			$this->add_carousel_settings_controls( [
				'css_prefix' => '',
			] );
		}

		public function register_navigation_section_controls() {
			$this->add_carousel_navigation_controls( [
				'css_prefix' => '',
				'navigation_custom_settings' => [
					'condition' => [
						'template_id!' => '',
					],
				],
			] );
		}

		public function register_pagination_section_controls() {
			$this->add_carousel_pagination_controls( [
				'css_prefix' => '',
				'section_carousel_pagination' => [
					'condition' => [
						'template_id!' => '',
					],
				],
			] );
		}

		public function register_design_layout_controls() {
			$this->start_controls_section(
				'section_layout_style',
				[
					'label' => esc_html__( 'Layout', 'elementor-pro' ),
					'tab' => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'image_spacing_custom',
				[
					'label' => esc_html__( 'Gap between slides', 'elementor-pro' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'max' => 400,
						],
					],
					'default' => [
						'size' => 10,
					],
					'frontend_available' => true,
					'render_type' => 'none',
					'selectors' => [
						'{{WRAPPER}}' => '--swiper-slides-gap: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->end_controls_section();

			
		}

		protected function register_design_navigation_controls() {
			$this->add_carousel_navigation_styling_controls( [
				'css_prefix' => '',
				'navigation_styling_custom_settings' => [
					'condition' => [
						'arrows' => 'yes',
						'template_id!' => '',
					],
				],
			] );
		}

		public function register_design_pagination_controls() {
			$this->add_carousel_pagination_style_controls( [
				'css_prefix' => '',
			] );
		}

		public function render_loop_header() {

			$has_autoplay_enabled = 'yes' === $this->get_settings_for_display( 'autoplay' );

			

			$this->add_render_attribute( 'swiper-wrapper', [
				'class' => 'swiper-wrapper',
				'aria-live' => $has_autoplay_enabled ? 'off' : 'polite',

				//Start Woosbymdz attrs
				/*'isdisswiper' => esc_attr($swiper_noeff),
				'is-elem-editor' => esc_attr($is_editor),
				'backtofirstslide' => esc_attr($settings['header_backtofirst_ajax']),
				'type' => esc_attr($settings['swiper_type']),
				'navs' => esc_attr($settings['swiper_navs']),
				'arrows' => esc_attr($settings['swiper_arrows']),
				'autoplay' => esc_attr($settings['swiper_autoplay']), 
				'autoplay-pause' => esc_attr($settings['swiper_autoplay_pause']),
				'autoplay-time' => esc_attr($settings['swiper_autoplay_delay']),
				'autoheight' => esc_attr($settings['swiper_autoheight']),
				'mousewheel' => esc_attr($settings['swiper_mousewheel']),
				'grab' => esc_attr($settings['swiper_cursorgrab']),
				'wide-pp' => esc_attr($settings['swiper_slides_wide']),
				'wide-s' => esc_attr($settings['swiper_space_wide']),
				'desk-pp' => esc_attr($settings['swiper_slides_desk']),
				'desk-s' => esc_attr($settings['swiper_space_desk']),
				'tab-pp' => esc_attr($settings['swiper_slides_tablet']),
				'tab-s' => esc_attr($settings['swiper_space_tablet']),
				'mob-pp' => esc_attr($settings['swiper_slides_mob']),
				'mob-s' => esc_attr($settings['swiper_space_mob']),
				'mob2-pp' => esc_attr($settings['swiper_slides_mob2']),
				'mob2-s' => esc_attr($settings['swiper_space_mob2']),
				'is-automover' => esc_attr($settings['swiper_automover']),
				'keyboard' => esc_attr($settings['swiper_keyboard']),
				'dont-swipe' => esc_attr($settings['dont_swipe']),
				'dont-swipe-onmobile' => esc_attr($settings['dont_swipe_on_mob']),
				'forced-loop' => esc_attr($settings['swiper_forcedloop']),
				'cflow-rotate' => esc_attr($settings['swiper_cflow_rotate']),
				/***/
			] );


			

			?>
			<div <?php $this->print_render_attribute_string( 'swiper-wrapper' ); ?>>
			<?php
		}

		public function render_loop_footer() {
			?>
			</div>
			</div>
			<?php
			$settings = $this->get_settings_for_display();
			$this->render_carousel_footer( $settings );
		}

		public function add_swiper_slide_attributes_to_loop_item( $attributes, $document ) {
			if ( LoopDocument::DOCUMENT_TYPE === $document::get_type() ) {
				$attributes['class'] .= ' swiper-slide';
				$attributes['role'] = 'group';
				$attributes['aria-roledescription'] = 'slide';
			}

			return $attributes;
		}

		public function add_loop_header_attributes( $render_attributes ) {

			$settings = $this->get_settings_for_display();

			//Fetch Our-Settings

			$is_editor = 'no';
			$swiper_noeff = 0;
			if (is_admin()){ 
				$swiper_noeff = get_option('woosbymdz_swipernoeff'); 
				$is_editor = 'yes';
			}

			$rand_id = 'mdz' . wp_date('smdyih') . rand(123, 123123) . 'con';
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

			//Dynamic arrows confs
			$use_dyna_arrows = 0;
			if($settings['swiper_arrows'] == 'dynamic'){
				$use_dyna_arrows = 1;
			}
			/***/

			if ( ! empty( $settings['direction'] ) ) {
				$render_attributes['dir'] = $settings['direction'];

				//Start Woosbymdz attrs
				$render_attributes['id'] = esc_attr($rand_id);
				$render_attributes['isdisswiper'] = esc_attr($swiper_noeff);
				$render_attributes['is-elem-editor22222'] = esc_attr($is_editor);//tmp
				$render_attributes['backtofirstslide'] = esc_attr($settings['header_backtofirst_ajax']);
				$render_attributes['type'] = esc_attr($settings['swiper_type']);
				$render_attributes['navs'] = esc_attr($settings['swiper_navs']);
				$render_attributes['arrows'] = esc_attr($settings['swiper_arrows']);
				$render_attributes['autoplay'] = esc_attr($settings['swiper_autoplay']);
				$render_attributes['autoplay-pause'] = esc_attr($settings['swiper_autoplay_pause']);
				$render_attributes['autoplay-time'] = esc_attr($settings['swiper_autoplay_delay']);
				$render_attributes['autoheight'] = esc_attr($settings['swiper_autoheight']);
				$render_attributes['mousewheel'] = esc_attr($settings['swiper_mousewheel']);
				$render_attributes['grab'] = esc_attr($settings['swiper_cursorgrab']);
				$render_attributes['wide-pp'] = esc_attr($settings['swiper_slides_wide']);
				$render_attributes['wide-s'] = esc_attr($settings['swiper_space_wide']);
				$render_attributes['desk-pp'] = esc_attr($settings['swiper_slides_desk']);
				$render_attributes['desk-s'] = esc_attr($settings['swiper_space_desk']);
				$render_attributes['tab-pp'] = esc_attr($settings['swiper_slides_tablet']);
				$render_attributes['tab-s'] = esc_attr($settings['swiper_space_tablet']);
				$render_attributes['mob-pp'] = esc_attr($settings['swiper_slides_mob']);
				$render_attributes['mob-s'] = esc_attr($settings['swiper_space_mob']);
				$render_attributes['mob2-pp'] = esc_attr($settings['swiper_slides_mob2']);
				$render_attributes['mob2-s'] = esc_attr($settings['swiper_space_mob2']);
				$render_attributes['grid-sp-up'] = esc_attr($settings['grid_slides_space_up']);
				$render_attributes['grid-sp-down'] = esc_attr($settings['grid_slides_space_down']);
				$render_attributes['is-automover'] = esc_attr($settings['swiper_automover']);
				$render_attributes['keyboard'] = esc_attr($settings['swiper_keyboard']);
				$render_attributes['dont-swipe'] = esc_attr($settings['dont_swipe']);
				$render_attributes['dont-swipe-onmobile'] = esc_attr($settings['dont_swipe_on_mob']);
				$render_attributes['forced-loop'] = esc_attr($settings['swiper_forcedloop']);
				$render_attributes['cflow-rotate'] = esc_attr($settings['swiper_cflow_rotate']);
				$render_attributes['is-grid-swiper'] = esc_attr($settings['swiper_is_grid']);
				$render_attributes['grid-rows'] = esc_attr($settings['swiper_grid_rows']);
				$render_attributes['grid-rows-mob'] = esc_attr($settings['swiper_grid_rows_mob']);

				$render_attributes['is-dyna-arrows'] = $use_dyna_arrows;
				/***/
			}

			/*$this->add_render_attribute( 'woosbymdz-swiper', [

				//Start Woosbymdz attrs
				'isdisswiper' => esc_attr($swiper_noeff),
				'is-elem-editor' => esc_attr($is_editor),
				'backtofirstslide' => esc_attr($settings['header_backtofirst_ajax']),
				'type' => esc_attr($settings['swiper_type']),
				'navs' => esc_attr($settings['swiper_navs']),
				'arrows' => esc_attr($settings['swiper_arrows']),
				'autoplay' => esc_attr($settings['swiper_autoplay']), 
				'autoplay-pause' => esc_attr($settings['swiper_autoplay_pause']),
				'autoplay-time' => esc_attr($settings['swiper_autoplay_delay']),
				'autoheight' => esc_attr($settings['swiper_autoheight']),
				'mousewheel' => esc_attr($settings['swiper_mousewheel']),
				'grab' => esc_attr($settings['swiper_cursorgrab']),

				'wide-pp' => esc_attr($settings['swiper_slides_wide']),
				'wide-s' => esc_attr($settings['swiper_space_wide']),
				'desk-pp' => esc_attr($settings['swiper_slides_desk']),
				'desk-s' => esc_attr($settings['swiper_space_desk']),
				'tab-pp' => esc_attr($settings['swiper_slides_tablet']),
				'tab-s' => esc_attr($settings['swiper_space_tablet']),
				'mob-pp' => esc_attr($settings['swiper_slides_mob']),
				'mob-s' => esc_attr($settings['swiper_space_mob']),
				'mob2-pp' => esc_attr($settings['swiper_slides_mob2']),
				'mob2-s' => esc_attr($settings['swiper_space_mob2']),
				'is-automover' => esc_attr($settings['swiper_automover']),
				'keyboard' => esc_attr($settings['swiper_keyboard']),
				'dont-swipe' => esc_attr($settings['dont_swipe']),
				'dont-swipe-onmobile' => esc_attr($settings['dont_swipe_on_mob']),
				'forced-loop' => esc_attr($settings['swiper_forcedloop']),
				'cflow-rotate' => esc_attr($settings['swiper_cflow_rotate']),
			] );*/

			return $render_attributes;
		}


		public function get_loop_header_widget_classes(): array 
		{
			// TODO: Remove conditional logic in v3.28 [ED-15983].
			//$swiper_class = $this->is_swiper_upgrade_experiment_state_inactive() ? 'swiper-container' : 'swiper';

			$swiper_class = 'woosbymdz-swiper';
			return [ $swiper_class ];
		}

		public function before_skin_render() {

			$settings = $this->get_settings_for_display();

			$is_elem_editor = 0;
			if(isset($_GET['action']) && $_GET['action'] =='elementor'){
				$is_elem_editor = 1;
			}

			$is_loop_editor = 0;
			if(isset($_GET['active-document']) && $is_elem_editor == 1){
				$is_loop_editor = 1;
			}

			/*if(isset($_GET['active-document'])){
				//wp_die('تست شد!');
			}*/

			$load_class = '';
			if($settings['loading_enabled'] == '1' || $settings['loading_enabled'] == 'yes'){
				$load_class = 'woosbymdz-loading';
			}

			if($is_elem_editor == 1){
				$load_class = '';
			}

			$not_found_inf_txt = esc_html__('No more Posts Found!', 'wooslider-bymdez');
			$inf_slide_inner_txt = esc_html__('+ Show More', 'wooslider-bymdez');


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
			}

			?>
			<div class="woosbymdz-carousel-con <?php echo esc_attr($load_class); ?> <?php echo esc_attr($main_con_horizontalPos); ?>" style="<?php if($settings['maincon_style_bgtransparent'] == 'yes'){echo esc_attr('background: none !important;'); } ?>" is-woos-loop-slider="1"  is-loop-editor="<?php echo esc_attr($is_loop_editor); ?>" dont-swipe-onmobile="<?php echo esc_attr($settings['dont_swipe_on_mob']); ?>" active-nav="<?php echo esc_attr($settings['swiper_navs_active']); ?>" sliding-eff="<?php echo esc_attr($settings['swiper_slide_effect']); ?>" overflow-vis="<?php echo esc_attr($settings['maincon_style_overflow']); ?>" cache-header="<?php echo esc_attr($settings['adv_ajax_cache']); ?>" reseter="<?php echo esc_attr($settings['adv_pros_reset']); ?>" infinital="<?php echo esc_attr($settings['adv_infinital']); ?>" inf-mode="<?php echo esc_attr($settings['adv_infinital_mode']); ?>" elem-slider-direction="<?php echo esc_attr($settings['direction']); ?>" num-to-load="<?php echo esc_attr($settings['posts_per_page']) ?>" inf-num-to-load="<?php echo esc_attr($settings['adv_infinital_num']); ?>" elem-postq-orderby="<?php echo esc_attr($settings['post_query_orderby']); ?>" elem-postq-order="<?php echo esc_attr($settings['post_query_order']); ?>" elem-q-post-type="<?php echo esc_attr($settings['post_query_post_type']) ?>" notfound-inf-txt="<?php echo esc_attr($not_found_inf_txt); ?>" inf-slide-txt="<?php echo esc_attr($inf_slide_inner_txt); ?>" arrows="<?php echo esc_attr($settings['swiper_arrows']); ?>" loop-autoh-slides="<?php echo esc_attr($settings['loop_extsets_autoh_slides']); ?>" loop-items-vr-align="<?php echo esc_attr($settings['loop_extsets_vr_align']) ?>">
			<?php

			?>
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

			//Start-Header
			if (($settings['header_enabled'] == 'yes' || $settings['header_enabled'] == 1) && strtolower($settings['post_query_post_type']) == 'product'){ 

				if ($settings['showonly_tologgedin'] == 'yes' && $settings['showonly_tologgedin_header'] == 'yes' && !is_user_logged_in()){
					//dont show header to not_logged in users
				}else{

				$auth = '1';
				if ($settings['showonly_tologgedin'] == 'yes' && !is_user_logged_in()){
					$auth = '0';
				}
				?>
				<div style="display: none;" class="woosbymdz-header woosbymdz-hidden <?php if($settings['header_style_hoverbgchanger'] == 'yes'){echo esc_attr('woosbymdz-header-fadebg'); } ?>" el-align="<?php echo esc_attr($settings['header_elems_align']); ?>">
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
					<div cat="<?php echo esc_attr($settings['header_elements'][$index]['header_ajax_cats'])?>" number="<?php echo esc_attr($settings['header_elements'][$index]['header_products_number']) ?>" stock="<?php echo esc_attr($settings['header_elements'][$index]['header_products_stock']) ?>" type="<?php echo esc_attr($settings['header_elements'][$index]['header_products_src']) ?>" seeallcat="<?php echo esc_attr($settings['header_elements'][$index]['header_allcats_show']) ?>" is-loop-btn="1" <?php if($settings['header_elements'][$index]['header_allcats_backtype'] == 'color'){
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

			<?php
			/***/


			add_filter( 'elementor/document/wrapper_attributes', [ $this, 'add_swiper_slide_attributes_to_loop_item' ], 10, 2 );
			add_filter( 'elementor/skin/loop_header_attributes', [ $this, 'add_loop_header_attributes' ], 10, 1 );
		}

		public function after_skin_render() {
			
			remove_filter( 'elementor/document/wrapper_attributes', [ $this, 'add_swiper_slide_attributes_to_loop_item' ] );
			remove_filter( 'elementor/skin/loop_header_attributes', [ $this, 'add_loop_header_attributes' ] );

			?>
			</div>

			<?php if(is_admin()): ?><script type="text/javascript">woosbymdz_construct_all_page_swipers();</script><?php endif; ?>

			<?php
		}

		protected function register_layout_section() {

			parent::register_layout_section();
			
			$class_controlls = new conts();
			$class_helper = help::get_instance();
			

			$this->start_injection( [
				'of' => 'template_id',
			] );


			$class_controlls->main_controls($this, 8);//Special posts-number for the loop-slider

			$class_controlls->main_controls($this, 1, '', [], 1);
			$class_controlls->main_controls($this, 2);
			

			$class_controlls->main_controls($this, 3, '', [], 1);
				
			$repeater = new \Elementor\Repeater();
			$ext_opts = $class_helper->get_terms_mdz('product_cat');
			$class_controlls->main_controls($this, 4, $repeater, $ext_opts);
			
			$class_controlls->main_controls($this, 5);
			$class_controlls->main_controls($this, 6);
			$class_controlls->main_controls($this, 7);
			$class_controlls->main_controls($this, 9);//Grid Layout

			//Loop Special opts
			$this->start_controls_section(
				'loop_extra_settings',
				[
					'label' => esc_html__( 'Extra Settings', 'wooslider-bymdez' ),
					'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control( //auto-height all-slides
				'loop_extsets_autoh_slides',
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

			$this->add_control(//align-vertical items
				'loop_extsets_vr_align',
				[
					'label' => esc_html__( 'Items Vertical-Align', 'wooslider-bymdez' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'description' => esc_html__('For this option to take effect, enable the Same Height Slides option in this section', 'wooslider-bymdez'),
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

				]
			);


			$this->end_controls_section();
			/***/

			//Start Style options
			$class_controlls->style_controls($this, 1);
			$class_controlls->style_controls($this, 2);
			$class_controlls->style_controls($this, 3);
			$class_controlls->style_controls($this, 14);//Loop arrow style
			$class_controlls->style_controls($this, 7);
			/***/

			$this->add_control(
				'posts_per_page',
				[
					'label' => esc_html__( 'Number of slides', 'elementor-pro' ),
					'type' => Controls_Manager::NUMBER,
					'default' => 6,
					'min' => 1,
					'condition' => [
						'template_id!' => '',
					],
					'separator' => 'before',
				]
			);

			$this->add_carousel_layout_controls( [
				'css_prefix' => '',
				'slides_to_show_custom_settings' => [
					'default' => '3',
					'widescreen_default' => '3',
					'laptop_default' => '3',
					'tablet_extra_default' => '3',
					'tablet_default' => '2',
					'mobile_extra_default' => '2',
					'mobile_default' => '1',
					'condition' => [
						'posts_per_page!' => 1,
						'template_id!' => '',
					],
					'selectors' => [
						'{{WRAPPER}}' => '--swiper-slides-to-display: {{VALUE}}',
					],
				],
				'slides_to_scroll_custom_settings' => [
					'default' => '1',
					'condition' => [
						'posts_per_page!' => 1,
						'template_id!' => '',
					],
				],
				'equal_height_custom_settings' => [
					'condition' => [
						'posts_per_page!' => 1,
						'template_id!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .swiper-slide > .elementor-element' => 'height: 100%',
					],
				],
				'slides_on_display' => 8,
			] );

			// Location for the Edit handle.
			$this->add_control(
				'edit_handle_selector',
				[
					'label' => esc_html__( 'Edit Handle Selector', 'elementor-pro' ),
					'type' => Controls_Manager::HIDDEN,
					'default' => '.elementor-widget-container',
					'render_type' => 'none',
					'frontend_available' => true,
				]
			);

			$this->end_injection();
		}
	}
}
//close the php tag to reduce the blank spaces ?>