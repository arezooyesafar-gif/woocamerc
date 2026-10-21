<?php
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

if (!class_exists('woosbymdz_mainconfigs'))
{
    
    class woosbymdz_mainconfigs 
    {

        //PROPS
        private static $instance;

        //construct
        public function __construct() 
        {
          
            $nomain_widget = get_option('woosbymdz_nomainwidget');
            //register main elem widget
            if (defined('ELEMENTOR_VERSION') && $nomain_widget == '0'){
                add_action( 'elementor/widgets/register', array($this, 'register_widget'), 10, 1 );
            }

            //last seen products
            $last_seen_cookie_js = get_option('woosbymdz_productstracker_js');
            if (get_option('woosbymdz_last_seen_cookie')=='1' && $last_seen_cookie_js != '1' ){
                add_action( 'template_redirect', array($this, 'track_products'), 20 );
            }

            //Short-codes
            if (get_option('woosbymdz_shortcode1') == '1'){
                add_action( 'plugins_loaded', array($this, 'registershortcode1') ); //register shortcode 1
            }

            if (get_option('woosbymdz_shortcode2') == '1'){
                add_action( 'plugins_loaded', array($this, 'registershortcode_2') ); //register shortcode 2
            }
           
            //Extra Widgets
            $extra_widget1 = get_option('woosbymdz_extrawidget1');
            if(defined('ELEMENTOR_VERSION') && $extra_widget1 == '1'){
                add_action( 'elementor/widgets/register', array($this, 'register_extrawidget1'), 10, 1 );
            }

            //New Widgets-Category
            $add_widgets_cat = get_option('woosbymdz_addnewwidgets_category');
            if($add_widgets_cat == '1'){

                //Check Elementor plugin
                $all_plugins = apply_filters('active_plugins', get_option('active_plugins'));

                if (stripos(implode($all_plugins), 'elementor.php')) {

                    add_action( 'elementor/elements/categories_registered', array($this, 'add_wig_cat') );

                }  
            }

            //Charged-Products
            $use_charged = get_option('woosbymdz_charged_pros');
            if($use_charged == '1'){
                add_action('woocommerce_update_product', array($this, 'charged_core'), 10, 2);
            }

            //Elementor dyna-tags
            $is_dyna_tags_disabled = get_option('woosbymdz_elem_dis_dynatags');
            if(defined('ELEMENTOR_VERSION') && defined('ELEMENTOR_PRO_PATH') && $is_dyna_tags_disabled != 1){
                require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'dynamic-tags'.DIRECTORY_SEPARATOR.'custom-dynamic-tags.php';

                $dyna_tags_class = Woosbymdz\Helpers\DynamicTags\dyna_tags::get_instance();
            }
            /***/
            
        }

        //Methods
        public static function get_instance() 
        {
            if ( ! isset( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function add_wig_cat($elements_manager)
        {

            $elements_manager->add_category(
                'woosbymdz-widgets-category',
                [
                    'title' => esc_html__( 'WooSlider', 'wooslider-bymdez' ),
                    'icon' => 'fa fa-plug',
                ]
            );

        }

        //elem widget
        public function register_widget($widgets_manager)
        {

            include_once WOOSBYMDZ_PATH . 'widgets'.DIRECTORY_SEPARATOR.'woosbymdz-widget.php' ;

	        $widgets_manager->register( new \WOOSBYMDZ_SLIDER_Widget() );

            //Woosbymdz Loop Carousel
            if(1==1){
                include_once WOOSBYMDZ_PATH . 'widgets'.DIRECTORY_SEPARATOR.'class-loop-woosbymdz.php' ;
            }
            /***/

        }

        //Extra Widgets
        public function register_extrawidget1($widgets_manager) 
        {//category slider widget

            include_once WOOSBYMDZ_PATH . 'widgets'.DIRECTORY_SEPARATOR.'categoty-slider-widget.php' ;

	        $widgets_manager->register( new \WOOSBYMDZ_categoryslider_Widget() );

        }


        //set recently viewd cookie for woo
        function track_products() 
        {
            if ( ! is_singular( 'product' ) && ! get_query_var( 'recently_viewed', 0 ) ) {
                return;
            }
            
            global $post;
            
            if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) { // @codingStandardsIgnoreLine.
                $viewed_products = array();
            } else {
                $viewed_products = wp_parse_id_list( (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) ); // @codingStandardsIgnoreLine.
            }
            
            // Unset if already in viewed products list.
            $keys = array_flip( $viewed_products );
            
            if ( isset( $keys[ $post->ID ] ) ) {
                unset( $viewed_products[ $keys[ $post->ID ] ] );
            }
            
            $viewed_products[] = $post->ID;
            
            if ( count( $viewed_products ) > 15 ) {
                array_shift( $viewed_products );
            }
            
            // Store for session only.
            wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
        }

        //Charged-Core
        public function charged_core($post_id, $product)
        {

            //Get options
            $cancel_disc = get_option('woosbymdz_charged_cancdisc');
            $is_add_cat = get_option('woosbymdz_charged_isaddcat');
            $add_cat_id = get_option('woosbymdz_charged_addcat');

            $pro_id = $post_id;



            $editing_pro = get_product($pro_id);
            $pro_type = $editing_pro->get_type();

            switch($pro_type){
                case 'simple':

                    $has_meta = metadata_exists('post', $pro_id, 'woosbymdz_pro_quan');
                    $this_quantity = $editing_pro->stock_quantity;

                    if(!$has_meta){

                        add_post_meta($pro_id, 'woosbymdz_pro_quan', $this_quantity);

                    }else if($has_meta){

                        $last_quan = get_post_meta($pro_id, 'woosbymdz_pro_quan', true);

                        if(($last_quan == $this_quantity) || ($last_quan == '' || $last_quan == ' ' || $last_quan == NULL)){
                            update_post_meta($pro_id, 'woosbymdz_pro_quan', $this_quantity);
                            return;
                        }

                        if($last_quan < $this_quantity && $this_quantity > 0){

                            update_post_meta($pro_id, 'woosbymdz_is_charged', '1');
                            update_post_meta($pro_id, 'woosbymdz_pro_quan', $this_quantity);

                            if($cancel_disc == '1'){

                                $editing_pro->set_sale_price('');
                                $editing_pro->set_date_on_sale_from('');
                                $editing_pro->set_date_on_sale_to('');

                            }

                            if($is_add_cat == '1'){
                                wp_add_object_terms( $pro_id, $add_cat_id, 'product_cat' );
                            }
                            $editing_pro->save();
                            break;


                        }else if($this_quantity <= 0) {

                            update_post_meta($pro_id, 'woosbymdz_is_charged', '0');
                            update_post_meta($pro_id, 'woosbymdz_pro_quan', $this_quantity);

                            if($is_add_cat == '1'){
                                wp_remove_object_terms( $pro_id, $add_cat_id, 'product_cat' );
                            }
                            break;

                        }else {
                            update_post_meta($pro_id, 'woosbymdz_pro_quan', $this_quantity);
                            break;
                        }

                    }
                    break;
                case 'variable':

                    $variations = $editing_pro->get_available_variations();
                    $totals_sharjed = 0;
                    $totals_not_changed = 0;
                    $totals_dis_sharjed = 0;
                    $totals_outofstock = 0;
                    $childs_num = 0;

                    foreach($variations as $this_var){

                        $var_id = $this_var['variation_id'];
                        $var_pro = get_product($var_id);
                        $childs_num++;
                            
                        $has_meta = metadata_exists('post', $var_id, 'woosbymdz_pro_quan');
                        $this_quantity = $var_pro->stock_quantity;

                        if($this_quantity == 0){
                            $totals_outofstock++;
                        }

                        if(!$has_meta){

                            add_post_meta($var_id, 'woosbymdz_pro_quan', $this_quantity);

                        }else if($has_meta){

                            $last_quan = get_post_meta($var_id, 'woosbymdz_pro_quan', true);

                            if(($last_quan == $this_quantity) || ($last_quan == "" || $last_quan == " " || $last_quan == NULL)){

                                $totals_not_changed++;
                                update_post_meta($var_id, 'woosbymdz_pro_quan', $this_quantity);
                                continue;
                            }

                            if($last_quan < $this_quantity && $this_quantity > 0 && ($last_quan != "" || $last_quan != " " || $last_quan != NULL)){

                                update_post_meta($var_id, 'woosbymdz_pro_quan', $this_quantity);
                                $totals_sharjed++;
                                continue;   

                            }else if($this_quantity <= 0) {

                                update_post_meta($var_id, 'woosbymdz_pro_quan', $this_quantity);
                                $totals_dis_sharjed++;
                                continue;

                            }else {

                                update_post_meta($var_id, 'woosbymdz_pro_quan', $this_quantity);
                                continue;

                            }

                        }
                    }

                    if($totals_not_changed == $childs_num){

                        if($totals_outofstock == $childs_num){
                            update_post_meta($pro_id, 'woosbymdz_is_charged', '0');

                            if($is_add_cat == '1'){
                                wp_remove_object_terms( $pro_id, $add_cat_id, 'product_cat' );
                            }
                        }
                        break;
                    }
  
                    if(($totals_sharjed > 0  && $totals_sharjed == $totals_dis_sharjed) || $totals_sharjed > $totals_dis_sharjed) {

                        update_post_meta($pro_id, 'woosbymdz_is_charged', '1');
                        
                        if($cancel_disc == '1'){
                        $variations2 = $editing_pro->get_available_variations();
                        foreach($variations2 as $this_var2){

                            $var_id2 = $this_var2['variation_id'];
                            $var_pro2 = get_product($var_id2);
                            $var_pro2->set_sale_price('');
                            $var_pro2->set_date_on_sale_from('');
                            $var_pro2->set_date_on_sale_to('');
                            $var_pro2->save();

                        }
                        }

                        if($is_add_cat == '1'){
                            wp_add_object_terms( $pro_id, $add_cat_id, 'product_cat' );
                        }
                        $editing_pro->save();

                        if($totals_outofstock == $childs_num){
                            update_post_meta($pro_id, 'woosbymdz_is_charged', '0');
                            if($is_add_cat == '1'){
                                wp_remove_object_terms( $pro_id, $add_cat_id, 'product_cat' );
                            }
                        }
                        break;
                            
                    }else if($totals_dis_sharjed > 1){

                        update_post_meta($pro_id, 'woosbymdz_is_charged', '0');
                        if($is_add_cat == '1'){
                            wp_remove_object_terms( $pro_id, $add_cat_id, 'product_cat' );
                        }
                        break;

                    }
                    if($totals_outofstock == $childs_num){
                        update_post_meta($pro_id, 'woosbymdz_is_charged', '0');
                        if($is_add_cat == '1'){
                            wp_remove_object_terms( $pro_id, $add_cat_id, 'product_cat' );
                        }
                    }
                    break;
            }
                
            
        }

        //Short-Codes

        //shortcode1
        public function registershortcode1($atts, $content='')
        {
            add_shortcode('woo_modern_slider', array($this, 'shortcode1_func'));
        }

        public function registershortcode_2($atts, $content='')
        {
            add_shortcode('woo_cats_slider', array($this, 'shortcode2_func'));
        }
        

        public function shortcode1_func($atts, $content='')
        {

            //get active-theme tags (needed in the `products-title-selector` html-attr) (from ver: 6.0)
            include WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'this-theme-confs.php';
            /***/

            extract( shortcode_atts( array(

                //General settings
                'loading' => '1',
                'header' => '1',
                'header_hoverchanger' => '1',
                'showonlyto_members' => '0',
                'showheaderonlyto_members' => '0',
                'no_mobile' => '0',
                'class' => '', //Rep
                'width' => '100',
                'height' => '355',
                'dont_swipe_on_mob' => 'no',
                'dont_swipe' => 'no',
                'no_bg' => 'no',
                'align' => 'center',
                'products_template' => 'default_temp',

                //Products
                'products_fetch' => 'cat',
                'products_query_operator' => 'OR',
                'products_query_operator2' => 'OR',
                'src' => 'news', 
                'stock' => '0', 
                'number' => '15',
                'cats' => '', //Rep
                'products_tags' => '', //Rep
                'products_use_attrs' => 'no',
                'yoast_main_cat' => 'no', 
                'show_these_pros' => '', //Rep
                'notshow_these_pros' => '', //Rep
                'see_allcats' => 'no',
                'seeallcats_title' => 'نمایش همه',
                'seeallcats_title_color' => '#000',
                'seeallcats_bgtype' => 'color',
                'seeallcats_bg_color' => '#fff',
                'seeallcats_bg_image' => '',
                'seeallcats_link' => '#',
                'seeallcats_link_ext' => '0',
                'seeallcats_link_nofollow' => '0',
                'seeallcats_link_yoast' => 'no',

                //Slider
                'slider_type' => 'default',
                'slider_grid' => 'no',
                'grid_rows' => 2,
                'grid_rows_mob' => 1,
                'slider_autoheight' => 'no',
                'navigation' => 'dots',
                'slider_arrows_show' => '1',
                'slider_autoplay' => 'no',
                'slider_noplay_whileusing' => 'yes',
                'slider_playspeed' => 3000,
                'slider_mousewheel' => 'no',
                'slider_grabcursor' => 'no',
                'slider_automover' => 'yes',
                'perpage_wide' => 4, 
                'space_wide' => 30,
                'perpage_desk' => 4, 
                'space_desk' => 30,
                'perpage_tab' => 3, 
                'space_tab' => 25,
                'perpage_mob' => 2, 
                'space_mob' => 20,
                'perpage_mob2' => 1, 
                'space_mob2' => 10,
                'right_arrow_bg' => '0',
                'left_arrow_bg' => '0',
                'arrows_onhover' => '0',
                'fraction_dir' => 'no',
                'pags_style_margin' => 0,
                'slider_centered_highlight' => 'no',
                'slider_centered_rotate' => 'no',
                'slider_keyboard' => 'no',
                'swiper_slide_effect' => 'default',
                'swiper_navs_active' => 'none',
                'forced_loop' => 'no',
                'cflow_rotate' => 50,
                
                //Header elements
                'header_elems_align' => 'center',
                'header_ajax_fslide' => 'yes',
                'header_titles' => 'دسته بندی شما, دسته بندی دلخواه, دسته بندی', //Rep
                'header_cats' => ' , , ', //Rep
                'header_nums' => '15, 15, 15', //Rep
                'header_srcs' => 'news, news, news', //Rep
                'header_stocks' => '1, 1, 1', //Rep
                'header_types' => '0, 0, 0', //Rep
                'header_icons_color' => '#000, #000, #000', //Rep
                'header_icons_class' => 'fas|fa,as|fa-icon,fas|fa-dental', //2 layered Rep!
                'header_imgs_url' => '', //Rep
                'header_imgs_width' => '45, 45, 45', //Rep
                'header_txticon_pos' => '0, 1, 2', //Rep
                'header_titles_color' => '#000, #000, #000', //Rep 
                'header_seeallcat' => 'no, no, no', //Rep 
                'header_seeallcat_title' => 'مشاهده همه, نمایش همه, نمایش همه محصولات', //Rep
                'header_seeallcat_titlescolor' => '#000, #000, #000', //Rep
                'header_seeallcat_bgtype' => 'color, color, color', //Rep 
                'header_seeallcat_bgcolor' => '#fff, #fff, #fff', //Rep 
                'header_seeallcat_bgimg' => '', //Rep   
                'header_seeallcat_links' => '#, #, #', //Rep
                'header_seeallcat_linkext' => 'off, on, off', //Rep
                'header_seeallcat_linknofollow' => 'on, off, on', //Rep
                'header_is_change_headerbg' => 'no, no, no',
                'header_change_headerbg' => '#52FF68, #52FF68, #52FF68', //Rep
                'header_elems_maketitle' => 'no, no, no', //Rep
                'header_elems_maketitle_theme' => 'theme-1, theme-1, theme-1', //Rep

                //Not Found Message
                'notfound_show' => 'yes',
                'notfound_txt' => 'محصولی پیدا نشد!',
                'notfound_btn_txt' => 'بریم فروشگاه ؟',
                'notfound_btn_url' => '#',
                'notfound_btn_urlext' => 'off',
                'notfound_btn_urlnofollow' => 'off',
                'notfound_space_top' => '65',
                'notfound_btn_classes' => 'woosbymdz-404btns', //Rep (seperate with *space*)

                //404
                'title_404' => 'با عرض پوزش',
                'desc_404' => 'شما نمی توانید این محصولات را مشاهده کنید، برای ادامه وارد حساب کاربری خود شوید و اگر حساب ندارید حساب جدید بسازید',
                'bg_404_url' => WOOSBYMDZ_URL.'front/img/woosbymdz-notfound.webp',
                'login_btn' => 'yes',
                'login_btn_title' => 'ورود',
                'login_btn_link' => '#',
                'login_btn_linkext' => 'off',
                'login_btn_linknofolow' => 'off',
                'login_btn_classes' => 'woosbymdz-404btns', //Rep

                'register_btn' => 'yes',
                'register_btn_title' => 'ثبت نام',
                'register_btn_link' => '#',
                'register_btn_linkext' => 'off',
                'register_btn_linknofolow' => 'off',
                'register_btn_classes' => 'woosbymdz-404btns', //Rep

                //Grid-Layout
                'grid_layout' => 'no',
                'grid_slides_wide' => 4,
                'grid_slides_desk' => 4,
                'grid_slides_tab' => 3,
                'grid_slides_mob' => 2,
                'grid_slides_mob2' => 1,
                'grid_slides_up_sp' => 0,
                'grid_slides_down_sp' => 0,
                'grid_masonry' => 'no',
                'grid_masonry_gap' => '10',
                /***/

                //adv
                'reseter' => 'no',
                'reseter_pos' => 'default',
                'reseter_icon_type' => 'icon',//img
                'reseter_icon_classes' => 'fa fa-rocket',
                'reseter_img_src' => '',//img URL
                'reseter_txt' => 'بازنشانی محصولات',
                'reseter_txt_pos' => 'left',//or right
                'cache_header' => 'no',
                'adv_infinital' => 'no',
                'adv_infinital_txt' => 'No more products found!',
                'adv_infinital_num' => 8,
                'adv_infinital_mode' => 'nextarrow',

                //other style-settings
                'overflow_vis' => 'no',
                'auto_same_h' => 'no',
                'no_pros_title' => 'no',
                'no_pros_cat' => 'no',
                'no_pros_price' => 'no',
                'no_pros_label' => 'no',
                'no_pros_addbtn' => 'no',
                'pros_centered_title' => 'no',

                //Extra (only works if elementor is not exist)
                'tablet_breakpoint' => 1025,
                'mobile_breakpoint' => 768,
                'reload_ajax_comp' => 'no',

                //Sh-generator plugin
                'madeby-sh-generator' => '0.0.00',
            
            ), $atts ) );

            //add css & js files
            $is_global_assets = get_option('woosbymdz_globalimport');
            if($is_global_assets != '1'){

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

                    wp_enqueue_style('woosbymdz-swiper', WOOSBYMDZ_URL . 'front/css/swiper-min.css', array(), WOOSBYMDZ_SW_VER);

                    wp_enqueue_script('woosbymdz-swiperjs', WOOSBYMDZ_URL . 'front/js/swiper-min.js', array(), WOOSBYMDZ_SW_VER, true);

                }
            
                $final_version = WOOSBYMDZ_VER.'.'.$dynamic_ver;
                wp_enqueue_style('woosbymdz-front', WOOSBYMDZ_URL . 'front/css/frontcss.css', array(), $final_version);

                $is_old_theme = get_option('woosbymdz_oldthemesdebuger');
                if($is_old_theme == '1'){
                    wp_enqueue_script('woosbymdz-front', WOOSBYMDZ_URL . 'front/js/frontjs.js', array('woosbymdz-swiperjs'), $final_version, true);

                }else wp_enqueue_script('woosbymdz-front', WOOSBYMDZ_URL . 'front/js/frontjs.js', array('jquery', 'woosbymdz-swiperjs'), $final_version, true);

                wp_localize_script(
                    'woosbymdz-front',
                    'woosbymdz_frnt_ajx_obj',
                    [
                        'wwkxssjk'  => wp_create_nonce( 'woosbymdz-uio2899nb-w144wkxs' ),
                    ],
                );//For Ajax-call Security
                
            }

            //Woodmart-Theme Assets!
            $active_theme = get_option('woosbymdz_active_theme');
            switch($active_theme){
                case 'woodmart':
                case 'قالب وودمارت':
                case 'woodmart child':
                case 'woodmart-child':
                case 'woodmart-see5':
                    $wood_settings['product_hover'] = woodmart_get_opt( 'products_hover' );
                    woodmart_enqueue_product_loop_styles( $wood_settings['product_hover'] );
                    woodmart_enqueue_js_script( 'quick-shop' );
			        woodmart_enqueue_js_script( 'swatches-variations' );
			        woodmart_enqueue_js_script( 'add-to-cart-all-types' );   
                    break;
            }

            //Start 
            if ($no_mobile == '1' && wp_is_mobile()){
                return; //break the function
            }

            //Grid-Layout confs
            if($grid_layout == 1 || $grid_layout == 'yes'){

                $dont_swipe = 'no';
                $dont_swipe_on_mob = 'no';

                $perpage_wide = $grid_slides_wide;
                $perpage_desk = $grid_slides_desk;
                $perpage_tab = $grid_slides_tab;
                $perpage_mob = $grid_slides_mob;
                $perpage_mob2 = $grid_slides_mob2;
            }
            /***/

            //Render the main container
            $rand_id = 'mdz' . wp_date('smdyih') . rand(123, 123123) . 'con';
            $main_con_classes = explode(',', $class);
            switch($align) {
                case 'center':
                    $setalign = 'margin-left: auto;margin-right: auto;';
                    break;
                case 'left':
                    $setalign = 'margin-right: auto;';
                    break;
                case 'right':
                    $setalign = 'margin-left: auto;';
                    break;
            }
 
            ob_start(); //Start the php output buffering
            ?>
            <div style="<?php echo $setalign; ?><?php if($no_bg == 'yes'){echo "background-color: #bcb0ff00 !important;";} ?>min-height: <?php echo esc_attr($height); ?>px;width: <?php echo esc_attr($width); ?>%; <?php if($showonlyto_members == '1' && !is_user_logged_in()){echo esc_attr('background: url("'.$bg_404_url.'"); background-size: cover;'); } ?>" class="woosbymdz-carousel-con <?php foreach($main_con_classes as $main_con_classe) {echo esc_attr($main_con_classe.' '); } ?> <?php if($loading == '1') { echo esc_attr('woosbymdz-loading '); } ?> <?php if($no_mobile == '1') {echo esc_attr('no-mobile');} ?> <?php if($reload_ajax_comp == 'yes'){echo esc_attr('woosbymdz-reload_ajax_comp'); } ?>" dont-swipe-onmobile="<?php echo esc_attr($dont_swipe_on_mob); ?>" active-nav="<?php echo esc_attr($swiper_navs_active); ?>" sliding-eff="<?php echo esc_attr($swiper_slide_effect); ?>" cache-header="<?php echo esc_attr($cache_header); ?>" reseter="<?php echo esc_attr($reseter); ?>" infinital="<?php echo esc_attr($adv_infinital); ?>" inf-mode="<?php echo esc_attr($adv_infinital_mode); ?>" grid-layout="<?php echo esc_attr($grid_layout); ?>" is-masonry="<?php echo esc_attr($grid_masonry); ?>" masonry-c-gap="<?php echo esc_attr($grid_masonry_gap); ?>" pros-card-temp="<?php echo esc_attr($products_template); ?>" overflow-vis="<?php echo esc_attr($overflow_vis); ?>">

            <!--Reset Button-->
            <?php if($reseter == 'yes'): ?>
            <div onclick="woosbymdz_reset_products(this)" class="woosbymdz-reseter-button woosbymdz-hidden" has-setted="no" pos="<?php echo esc_attr($reseter_pos); ?>">
                <div>
                    <span>
                        <?php if($reseter_icon_type == 'icon'): ?>
                        <?php
                        $reseter_icon_classes = explode(',', $reseter_icon_classes);
                        ?>
                        <i class="<?php foreach($reseter_icon_classes as $icon){echo esc_attr($icon);} ?>"></i>
                        <?php endif; ?>
                        <?php if($reseter_icon_type == 'img'): ?>
                        <img class="wooslider-reseter-img" alt="icon" src="<?php echo esc_url($reseter_img_src); ?>">
                        <?php endif; ?>
                    </span>
                    <div style="display: none !important;" class="htmlcon"></div>
                    <div class="reseter-tooltip" pos="<?php echo esc_attr($reseter_txt_pos); ?>">
                        <?php echo esc_html($reseter_txt); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!--Endof Reset Button-->
           

            <?php //Header
            if ($header == '1'){ 

                if ($showonlyto_members == '1' && $showheaderonlyto_members == '1' && !is_user_logged_in()){
                    //dont show header to not_logged in users
                }else{
    
                $auth = '1';
                if ($showonlyto_members == '1' && !is_user_logged_in()){
                    $auth = '0';
                }

                ?>

                <div style="display: none;" class="woosbymdz-header woosbymdz-hidden <?php if($header_hoverchanger == '1'){echo 'woosbymdz-header-fadebg'; } ?>" el-align="<?php echo esc_attr($header_elems_align); ?>">

                <!-- header buttons -->
                <div class="woosbymdz-header-wrapper">

                <?php
                //get header rep values
                $header_titles = explode(',', $header_titles);
                $header_cats = explode(',', $header_cats);
                $header_nums = explode(',', $header_nums);
                $header_srcs = explode(',', $header_srcs);
                $header_stocks = explode(',', $header_stocks);
                $header_types = explode(',', $header_types);
                $header_icons_color = explode(',', $header_icons_color);
                $header_imgs_url = explode(',', $header_imgs_url);
                $header_imgs_width = explode(',', $header_imgs_width);
                $header_txticon_pos = explode(',', $header_txticon_pos);
                $header_titles_color = explode(',', $header_titles_color);
                $header_seeallcat = explode(',', $header_seeallcat);
                $header_seeallcat_title = explode(',', $header_seeallcat_title);
                $header_seeallcat_titlescolor = explode(',', $header_seeallcat_titlescolor);
                $header_seeallcat_bgtype = explode(',', $header_seeallcat_bgtype);
                $header_seeallcat_bgcolor = explode(',', $header_seeallcat_bgcolor);
                $header_seeallcat_bgimg = explode(',', $header_seeallcat_bgimg);
                $header_seeallcat_links = explode(',', $header_seeallcat_links);
                $header_seeallcat_linkext = explode(',', $header_seeallcat_linkext);
                $header_seeallcat_linknofollow = explode(',', $header_seeallcat_linknofollow);
                $header_change_headerbg = explode(',', $header_change_headerbg);
                $header_is_change_headerbg = explode(',', $header_is_change_headerbg);
                $header_elems_maketitle = explode(',', $header_elems_maketitle);
                $header_elems_maketitle_theme = explode(',', $header_elems_maketitle_theme);
                $header_icons_class = explode(',', $header_icons_class);
                

                $tmp_inxer = 0;
                foreach($header_icons_class as $header_icons_clas){

                    if(str_contains($header_icons_clas, '|')){
                        $header_icons_class[$tmp_inxer] = str_replace('|', ' ', $header_icons_clas);
                    }

                    $tmp_inxer++;

                    //array_push($icons_class_arr, $header_icons_clas);
                }
                $icons_class_arr = $header_icons_class;


                //start rendering header elements
                $header_indexer = 0;
                foreach ( $header_titles as $index => $item ) {
    
                    switch($header_txticon_pos[$index]){//prepare header text & icon alignment
                        case '0':
                            $header_txticon_align = 'woosbymdz-texticon-wrapeer1';
                            break;
                        case '1':
                            $header_txticon_align = 'woosbymdz-texticon-wrapeer2';
                            break;
                        case '2':
                            $header_txticon_align = 'woosbymdz-texticon-wrapeer3';
                            break;
                        case '3':
                            $header_txticon_align = 'woosbymdz-texticon-wrapeer4';
                            break;
                    }

                    switch($header_stocks[$index]){
                        case '0':
                            $header_stocks[$index] = 'all';
                            break;
                        case '1':
                            $header_stocks[$index] = 'only_instocks';
                            break;
                        case '2':
                            $header_stocks[$index] = 'only_not_instocks';
                            break;
                    }

                    /*if ($header_change_headerbg[$index] != 'no'){
                        $isheader_bg_changer = 'yes';

                    }else {
                        $isheader_bg_changer = '';
                        $header_change_headerbg[$index] = '';
                    }*/

                    

                    if ($header_is_change_headerbg[$index] == 'yes'){
                        $isheader_bg_changer = 'yes';

                    }else {
                        $isheader_bg_changer = '';
                        $header_change_headerbg[$index] = '';
                    }

                    
                    ?>

                    <div cat="<?php echo esc_attr($header_cats[$index])?>" number="<?php echo esc_attr($header_nums[$index]) ?>" stock="<?php echo esc_attr($header_stocks[$index]) ?>" type="<?php echo esc_attr($header_srcs[$index]) ?>" seeallcat="<?php echo esc_attr($header_seeallcat[$index]) ?>" 

                    <?php if($header_seeallcat_bgtype[$index] == 'color'){
                        echo esc_attr('seeallcat_bgcolor='.$header_seeallcat_bgcolor[$index]);
                    }else if($header_seeallcat_bgtype[$index] == 'wallpaper') { echo esc_attr('seeallcat_bgwalp='.$header_seeallcat_bgimg[$index]);}?> seeallcat_title="<?php echo esc_attr($header_seeallcat_title[$index])?>"
                    seeallcat_titlecolor="<?php echo esc_attr($header_seeallcat_titlescolor[$index])?>" seeallcat_link="<?php echo esc_attr($header_seeallcat_links[$index])?>" seeallcat_link_ext="<?php echo esc_attr($header_seeallcat_linkext[$index])?>" seeallcat_link_follow="<?php echo esc_attr($header_seeallcat_linknofollow[$index])?>" auth="<?php echo esc_attr($auth); ?>" bgchanger="<?php echo esc_attr($isheader_bg_changer); ?>" bgchanger_color="<?php echo esc_attr($header_change_headerbg[$index]); ?>" has-cached="no" cache-elem="woosbymdz-hcacher-<?php echo esc_attr($header_indexer); ?>" infinital="<?php echo esc_attr($adv_infinital); ?>" inf-mode="<?php echo esc_attr($adv_infinital_mode); ?>" inf-num="<?php echo esc_attr($adv_infinital_num); ?>" inf-txt="<?php echo esc_attr($adv_infinital_txt); ?>" class="woosbymdz-header-divs" <?php if($header_elems_maketitle[$index] == 'yes'){ echo 'no-btn'.' title-theme='.$header_elems_maketitle_theme[$index]; } ?>>

                    <!--Header Cache Elements-->
                    <?php if($cache_header == 'yes'): ?>
                    <div style="display: none !important;" id="woosbymdz-hcacher-<?php echo esc_attr($header_indexer); ?>">
                    </div>
                    <?php endif; ?>
                    <!--Endof Header Cache Elements-->
                    
                    <div class="<?php echo esc_attr($header_txticon_align); ?>">
                    
                    <?php if($header_types[$index] == '1') { ?>

                    <i style="color: <?php echo esc_attr($header_icons_color[$index]) ?> ;" class="<?php echo esc_attr($icons_class_arr[$index]); ?>"></i> <?php
                    }else if($header_types[$index] == '2'){
                        ?><img style="width:<?php echo esc_attr($header_imgs_width[$index]) ?>px; height: auto;" src="<?php echo esc_attr($header_imgs_url[$index]) ?>"><?php
                    }?>
                    <span style="color:<?php echo esc_attr($header_titles_color[$index]); ?> ;"><?php echo esc_html($header_titles[$index]); ?></span>
    
                    </div>
                    </div> 
                    <?php
                    $header_indexer++;
                }
                ?>
                </div>
                </div>
                <?php } //show header to not_loggen in users or not? ?> 
                <?php } else { ?> <div class="woosbymdz-noheader"></div> <?php } //End of Header ?>

                <div id="<?php echo esc_attr($rand_id); ?>" style="display: none;" class="woosbymdz-swiper woosbymdz-hidden <?php if($slider_arrows_show == 'dynamic'){echo esc_attr('dynamic-arrows ');} if($slider_noplay_whileusing == 'yes'){echo esc_attr('pauseonhover ');} if($slider_centered_highlight == 'yes') {echo esc_attr('center-highlight ');} ?> <?php if($slider_centered_rotate == 'yes') {echo esc_attr('centered-rotate');} ?>" backtofirstslide="<?php echo esc_attr($header_ajax_fslide); ?>" type="<?php echo esc_attr($slider_type); ?>" navs="<?php echo esc_attr($navigation); ?>" arrows="<?php echo esc_attr($slider_arrows_show); ?>" autoplay="<?php echo esc_attr($slider_autoplay); ?>" autoplay-pause="<?php echo esc_attr($slider_noplay_whileusing); ?>" autoplay-time="<?php echo esc_attr($slider_playspeed) ?>" autoheight="<?php echo esc_attr($slider_autoheight); ?>" mousewheel="<?php echo esc_attr($slider_mousewheel); ?>" grab="<?php echo esc_attr($slider_grabcursor); ?>" wide-pp ="<?php echo esc_attr($perpage_wide); ?>" wide-s ="<?php echo esc_attr($space_wide); ?>" desk-pp="<?php echo esc_attr($perpage_desk); ?>" desk-s="<?php echo esc_attr($space_desk); ?>" tab-pp="<?php echo esc_attr($perpage_tab); ?>" tab-s="<?php echo esc_attr($space_tab); ?>" mob-pp="<?php echo esc_attr($perpage_mob); ?>" mob-s="<?php echo esc_attr($space_mob); ?>" mob2-pp="<?php echo esc_attr($perpage_mob2); ?>" mob2-s="<?php echo esc_attr($space_mob2); ?>" is-automover="<?php echo esc_attr($slider_automover); ?>" keyboard="<?php echo esc_attr($slider_keyboard); ?>" dont-swipe="<?php echo esc_attr($dont_swipe); ?>" dont-swipe-onmobile="<?php echo esc_attr($dont_swipe_on_mob); ?>" forced-loop="<?php echo esc_attr($forced_loop); ?>" cflow-rotate="<?php echo esc_attr($cflow_rotate); ?>" is-grid-swiper="<?php echo esc_attr($slider_grid); ?>" grid-rows="<?php echo esc_attr($grid_rows); ?>" grid-rows-mob="<?php echo esc_attr($grid_rows_mob) ?>" grid-sp-up="<?php echo esc_attr($grid_slides_up_sp); ?>" grid-sp-down="<?php echo esc_attr($grid_slides_down_sp); ?>">

                <div class="swiper-wrapper" no-products-title="<?php echo esc_attr($no_pros_title); ?>" no-products-cats="<?php echo esc_attr($no_pros_cat); ?>" no-products-price="<?php echo esc_attr($no_pros_price) ?>" no-products-labels="<?php echo esc_attr($no_pros_label); ?>" no-products-addbtn="<?php echo esc_attr($no_pros_addbtn); ?>" products-same-height="<?php echo esc_attr($auto_same_h); ?>" products-center-title="<?php echo esc_attr($pros_centered_title) ?>" products-title-selector="<?php echo esc_attr($theme_prod_title); ?>">
                <!-- here goes the slides -->
                <?php

                /*Prepare values to pass to function*/

                switch($stock){
                  case '0':
                    $stock = 'all';
                    break;
                  case '1':
                    $stock = 'only_instocks';
                    break;
                  case '2':
                    $stcok = 'only_not_instocks';
                    break;
                }
        
                switch($slider_arrows_show){
                    case '0':
                        $slider_arrows_show = 'none';
                        break;
                    case '1':
                        $slider_arrows_show = 'always';
                        break;
                    case '2':
                        $slider_arrows_show = 'dynamic';
                        break;
                    case '3':
                        $slider_arrows_show = 'desktop';
                        break;
                    case '4':
                        $slider_arrows_show = 'mobile';
                        break;
                    case '5':
                        $slider_arrows_show = 'mobile_tablet';
                        break;
                }

                switch($right_arrow_bg){
                    case '0':
                        $right_arrow_bg = 'none';
                        break;
                    case '1':
                        $right_arrow_bg = 'sharped';
                        break;
                    case '2':
                        $right_arrow_bg = 'ease_sharped';
                        break;
                    case '3':
                        $right_arrow_bg = 'circle';
                        break;
                    case '4':
                        $right_arrow_bg = 'leave1';
                        break;
                    case '5':
                        $right_arrow_bg = 'leave2';
                        break;
                }

                switch($left_arrow_bg){
                    case '0':
                        $left_arrow_bg = 'none';
                        break;
                    case '1':
                        $left_arrow_bg = 'sharped';
                        break;
                    case '2':
                        $left_arrow_bg = 'ease_sharped';
                        break;
                    case '3':
                        $left_arrow_bg = 'circle';
                        break;
                    case '4':
                        $left_arrow_bg = 'leave1';
                        break;
                    case '5':
                        $left_arrow_bg = 'leave2';
                        break;
                }

                switch ($arrows_onhover){
                    case '0':
                        $arrows_onhover = 'none';
                        break;
                    case '1':
                        $arrows_onhover = 'out_mover';
                        break;
                    case '2':
                        $arrows_onhover = 'in_mover';
                        break;
                    case '3':
                        $arrows_onhover = 'bigger';
                        break;
                    case '4':
                        $arrows_onhover = 'shrink';
                        break;
                    case '5':
                        $arrows_onhover = 'highlight';
                        break;
                }

                $products_seeallcatbgimg_arr = $products_seeallcaturl_array = array();
                $products_bg_img_arr['url'] = $seeallcats_bg_image;
                $products_seeallcaturl_array['url'] = $seeallcats_link;
                $products_seeallcaturl_array['is_external'] = $seeallcats_link_ext;
                $products_seeallcaturl_array['nofollow'] = $seeallcats_link_nofollow;

                switch($seeallcats_bgtype){
                    case 'wallpaper':
                        $seeallcats_bg_color = '';
                        break;
                }

                //Set not found btn url
                $notfounf_btnurl_array['url'] = $notfound_btn_url;
                $notfounf_btnurl_array['is_external'] = $notfound_btn_urlext;
                $notfounf_btnurl_array['nofollow'] = $notfound_btn_urlnofollow;

                $all_settings = array(  
                    'products_fetch' => $products_fetch,
                    'products_query_operator' => $products_query_operator,
                    'products_query_operator2' => $products_query_operator2,
                    'cat' => $cats, 
                    'products_tags' => $products_tags,
                    'products_use_attrs' => $products_use_attrs, 
                    'yoast_main_cat' => $yoast_main_cat, 
                    'products_number' => $number,
                    'src' => $src,
                    'only_instock' => $stock,
                    'products_extra' => $show_these_pros,
                    'products_exclude' => $notshow_these_pros,
                    'products_allcats_show' => $see_allcats,
                    'products_allcats_title' => sanitize_text_field($seeallcats_title),
                    'products_allcats_titlecolor' => $seeallcats_title_color,
                    'products_allcats_backtype' => $seeallcats_bgtype,
                    'products_allcats_backcolor' => $seeallcats_bg_color,
                    'products_allcats_backimg' => $products_bg_img_arr,
                    'products_allcats_url' => $products_seeallcaturl_array,
                    'products_allcats_yoast_link' => $seeallcats_link_yoast,
                    'swiper_type' => $slider_type,
                    'swiper_navs' => $navigation,
                    'swiper_arrows' => $slider_arrows_show,
                    'arrows_style_leftbg' => $left_arrow_bg,
                    'arrows_style_rightbg' => $right_arrow_bg,
                    'arrows_style_hover' => $arrows_onhover,
                    'pags_style_fraction_direction' => $fraction_dir,
                    'pags_style_margin' => $pags_style_margin,
                    'notfound_show' => $notfound_show,
                    'notfound_txt' => $notfound_txt,
                    'notfound_btn_txt' => $notfound_btn_txt,
                    'notfound_btn_url' => $notfounf_btnurl_array,
                    'notfound_space_top' => $notfound_space_top,  
                    'notfound_btn_classes' => $notfound_btn_classes, 
                    /*'rand_id' => $rand_id,*/
                    //extra
                    'tablet_breakpoint' => $tablet_breakpoint,
                    'mobile_breakpoint' => $mobile_breakpoint,
                    //adv
                    'adv_infinital' => $adv_infinital,
                    'adv_infinital_txt' => $adv_infinital_txt,
                    'adv_infinital_num' => $adv_infinital_num,
                    'adv_infinital_mode' => $adv_infinital_mode,
                    'products_template' => $products_template,
                );

                //get and show products  
                if ($showonlyto_members == '1'){
                    if (is_user_logged_in()){
        
                        include_once WOOSBYMDZ_PATH . 'widgets'.DIRECTORY_SEPARATOR.'class-getandshowprods.php' ;
                        $class_fetch = new woosbymdz_getandshow();
                        $class_fetch->getandshow_mdz($all_settings);
        
                    }else {
                        ?> </div></div><!-- End of Swiper wrapper -->
        
                        <div class="woosbymdz-404wrapper">
                            <h5><?php echo esc_html($title_404); ?></h5>
                            <div><p><?php echo esc_html($desc_404) ?></p></div>
                            <?php 
                            if ($login_btn == 'yes' || $register_btn=='yes'){
        
                                ?><div class="woosbymdz-404btns-wrapper">
        
                                    <?php if($login_btn=='yes'){
        
                                        $login_btn_classes = explode(',', $login_btn_classes);
        
                                        switch($login_btn_linkext){
                                            case 'on':
                                                $loginbtn_url_ext = '_blank';
                                                break;
                                            default:
                                                $loginbtn_url_ext = '_self';
                                                break;
                                        }
                                        switch($login_btn_linknofolow){
                                            case 'on':
                                                $loginbtn_url_follow = 'nofollow';
                                                break;
                                            default:
                                                $loginbtn_url_follow = 'follow';
                                                break;
                                        }
        
                                        ?>
        
                                        <a href="<?php echo esc_url($login_btn_link) ?>" target="<?php echo esc_attr($loginbtn_url_ext) ?>" rel="<?php echo esc_attr($loginbtn_url_follow) ?>"><button class="<?php foreach ($login_btn_classes as $login_btn_classe) echo esc_attr($login_btn_classe.' '); ?>"><?php echo esc_html($login_btn_title); ?></button></a>
        
                                        <?php
                                    } 
        
                                    if($register_btn=='yes'){
        
                                        $register_btn_classes = explode(',', $register_btn_classes);
        
                                        switch($register_btn_linkext){
                                            case 'on':
                                              $registerbtn_url_ext = '_blank';
                                              break;
                                            default:
                                              $registerbtn_url_ext = '_self';
                                              break;
                                        }
                                        switch($register_btn_linknofolow){
                                            case 'on':
                                              $registerbtn_url_follow = 'nofollow';
                                              break;
                                            default:
                                              $registerbtn_url_follow = 'follow';
                                              break;
                                         }
        
                                        ?>
                                        <a href="<?php echo esc_url($register_btn_link) ?>" target="<?php echo esc_attr($registerbtn_url_ext) ?>" rel="<?php echo esc_attr($registerbtn_url_follow) ?>"><button class="<?php foreach($register_btn_classes as $register_btn_classe) echo esc_attr($register_btn_classe.' '); ?>"><?php echo esc_html($register_btn_title) ?></button></a> 
                                        <?php
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
                }
            
                $content = ob_get_contents();
	            ob_end_clean();
	        
 	            return $content;
        }

        public function shortcode2_func($atts, $content='')
        {

            //Set Options
            extract( shortcode_atts( array(

                //Main con
                'width' => '100',//Percent
                'horizon_pos' => 'center', // center | left | right
                'class' => '',//rep

                //Slides
                //'slides_number' => 'auto',//DEPRECATED (ver 6.0)
                'slides_show_title' => 'yes', //Rep
                'slides_title' => 'دسته بندی 1', //Rep
                'slides_title_color' => '#000',
                'slides_img_type' => 'img', //Rep => img or icon
                'slides_img_url' => '', //Rep
                'slides_icon_classes' => 'fas|fa', //2 layered Rep!
                'slides_icon_color' => '#000', //Rep
                'slides_img_width' => '100', //Rep in Pixels
                'slides_img_alt' => '', //Rep
                'slides_block_type' => 'imagetop', //Rep => imagetop | imagedown | imageright | imageleft | imagehover | titlehover | imagerotate | metro
                'slides_link_url' => '#', //Rep
                'slides_link_ext' => 'no', //Rep
                'slides_link_nofollow' => 'no', //Rep
                'slides_is_exthtml' => 'no', //Rep
                'slides_exthtml' => '', //Rep  Here goes HTML Code
                'slides_has_extclass' => 'no', //Rep
                'slides_ext_classes' => 'fas|fa', //2 layered Rep!
                'slides_lazyload' => 'no',
                
                //Swiper
                'show_dots' => 'no',
                'show_arrows' => 'yes',
                'grid' => 'yes',
                'grid_rows' => 2,
                'auto_height' => 'no',
                'full_width_slides' => 'no', 
                'loop' => 'no',
                'autoplay' => 'no',
                'autoplay_speed' => 1775,
                'slider_move_eff' => 'default',// ease | linear | static | fast | cool | hammer | steps | light
                'rotate_eff' => 'default',// fade | css | coverflow | cards | cube | rotate | rewind | overflow | creative1 TO creative5 
                'centered_slider' => 'no',
                'no_shadow' => 'no',
                'slider_active_eff' => 'none', // grow | shrink | growrotat | shrinkrotate | rotate | 3d | ghost | blur | xray | fantasy
                'slider_active_dot' => 'none', // grow | shrink | sharp | modern | goup | godown | alone
                'wide_pp' => 3,
                'wide_s' => 30,
                'desk_pp' => 3,
                'desk_s' => 30,
                'tab_pp' => 3,
                'tab_s' => 25,
                'mob_pp' => 2,
                'mob_s' => 20,
                'mob2_pp' => 1,
                'mob2_s' => 10,

                //Styles
                'rounded_images' => 'no',
                'circle_images' => 'no',
                'auto_images' => 'no',
                'dots_margin' => 0,

                //Sh-generator plugin
                'madeby-sh-generator' => '0.0.00',
            
            ), $atts ) );

            //add css & js files
            $is_global_assets = get_option('woosbymdz_globalimport');
            if($is_global_assets != '1'){ 

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

                    wp_enqueue_style('woosbymdz-swiper', WOOSBYMDZ_URL . 'front/css/swiper-min.css', array(), WOOSBYMDZ_SW_VER);

                    wp_enqueue_script('woosbymdz-swiperjs', WOOSBYMDZ_URL . 'front/js/swiper-min.js', array(), WOOSBYMDZ_SW_VER);

                }
            
                $final_version = WOOSBYMDZ_VER.'.'.$dynamic_ver;
                wp_enqueue_style('woosbymdz-front', WOOSBYMDZ_URL . 'front/css/frontcss.css', array(), $final_version);

                $is_old_theme = get_option('woosbymdz_oldthemesdebuger');
                if($is_old_theme == '1'){
                    wp_enqueue_script('woosbymdz-front', WOOSBYMDZ_URL . 'front/js/frontjs.js', array('woosbymdz-swiperjs'), $final_version);
                }else wp_enqueue_script('woosbymdz-front', WOOSBYMDZ_URL . 'front/js/frontjs.js', array('jquery', 'woosbymdz-swiperjs'), $final_version);

                wp_localize_script(
                    'woosbymdz-front',
                    'woosbymdz_frnt_ajx_obj',
                    [
                        'wwkxssjk'  => wp_create_nonce( 'woosbymdz-uio2899nb-w144wkxs' ),
                    ],
                );//For Ajax-call Security
                
            }   

            //set general options
            switch($rounded_images){
                case 'yes':
                    $images_borderradius_class = 'woosbymdz-allimages-rounded';
                    break;
                default:
                    $images_borderradius_class = '';
                    break;
            }
            switch($circle_images){
                case 'yes':
                    $images_circle_class = 'woosbymdz-allimages-circled';
                    break;
                default:
                    $images_circle_class = '';
                    break;
            }
            switch($horizon_pos){
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
            switch($show_arrows){
                case 'yes':
                    $no_arrows = 'no';
                    break;
                default:
                    $no_arrows = 'yes';
                    break;
            }

            if($show_dots == 'yes'){
                $nav_active_style = $settings['nav_style_active_eff'];
            }else $nav_active_style = 'none';

            switch($auto_images){
                case 'yes':
                    $maincon_autowimages = 'woosbymdz-autowidth-images';
                    break;
                default:
                    $maincon_autowimages = '';
                    break;
            }
            switch($slider_active_eff){
                case 'grow':
                    $active_slide_eff = 'grow';
                    break;
                case 'shrink':
                    $active_slide_eff = 'shrink';
                    break;
                case 'growrotat':
                    $active_slide_eff = 'grow-rotate';
                    break;
                case 'shrinkrotate':
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
                case 'xray':
                    $active_slide_eff = 'x-ray';
                    break;
                case 'fantasy':
                    $active_slide_eff = 'fantasy';
                    break;
                default:
                    $active_slide_eff = 'none';
                    break;
            }

            /*switch($slider_move_eff){
                case 'ease':
                    $slider_move_effect = 'easein';
                    break;
                case 'linear':
                    $slider_move_effect = 'linar';
                    break;
                case 'static':
                    $slider_move_effect = 'stepend';
                    break;
                case 'fast':
                    $slider_move_effect = 'stepstart';
                    break;
                case 'cool':
                    $slider_move_effect = 'funky';
                    break;
                case 'hammer':
                    $slider_move_effect = 'hammer';
                    break;
                case 'steps':
                    $slider_move_effect = 'steps';
                    break;
                case 'light':
                    $slider_move_effect = 'light';
                    break;
                default:
                    $slider_move_effect = 'default';
                    break;
            }*/

            switch($slider_active_dot){

                case 'grow':
                    $slider_active_dot_eff = 'bigger';
                    break;
                case 'shrink':
                    $slider_active_dot_eff = 'shrink';
                    break;
                case 'sharp':
                    $slider_active_dot_eff = 'sharped';
                    break;
                case 'modern':
                    $slider_active_dot_eff = 'modern';
                    break;
                case 'goup':
                    $slider_active_dot_eff = 'upper';
                    break;
                case 'godown':
                    $slider_active_dot_eff = 'downer';
                    break;
                case 'alone':
                    $slider_active_dot_eff = 'alone';
                    break;
                default:
                    $slider_active_dot_eff = 'none';
                    break;
            }

            if(strpos($class, ',') !== false){
                $classes_str = str_replace(',', ' ', $class);
            }else $classes_str = $class;
            
            //HTML
            ob_start();
            ?>

            <div style="width: <?php echo esc_attr($width."%;"); ?><?php echo esc_attr($maincon_side_margins); ?>" class="woosbymdz-catslider-con <?php echo esc_attr($classes_str); ?> <?php echo esc_attr($maincon_autowimages); ?>" move-effect="<?php echo esc_attr($slider_move_eff); ?>" no-arrows="<?php echo esc_attr($no_arrows); ?>" nav-active="<?php echo esc_attr($slider_active_dot_eff); ?>" active-slide-eff="<?php echo esc_attr($active_slide_eff); ?>" no3dsshdow="<?php echo esc_attr($no_shadow); ?>">

            <div class="swiper woosbymdz-catslider-swiper" loop="<?php echo esc_attr($loop); ?>" navs="<?php echo esc_attr($show_dots); ?>" arrows="<?php echo esc_attr($show_arrows); ?>" grids="<?php echo esc_attr($grid); ?>" <?php if($grid == 'yes') { echo esc_attr('grid-rows= '.$grid_rows.''); }?> autoplay="<?php echo esc_attr($autoplay); ?>" <?php if($autoplay == 'yes') { echo esc_attr('play-speed= '.$autoplay_speed.''); }?> wide-pp ="<?php echo esc_attr($wide_pp); ?>" wide-s ="<?php echo esc_attr($wide_s); ?>" desk-pp="<?php echo esc_attr($desk_pp); ?>" desk-s="<?php echo esc_attr($desk_s); ?>" tab-pp="<?php echo esc_attr($tab_pp); ?>" tab-s="<?php echo esc_attr($tab_s); ?>" mob-pp="<?php echo esc_attr($mob_pp); ?>" mob-s="<?php echo esc_attr($mob_s); ?>" mob2-pp="<?php echo esc_attr($mob2_pp); ?>" mob2-s="<?php echo esc_attr($mob2_s);?>" rotate-effect="<?php echo esc_attr($rotate_eff); ?>" is-centered-slider="<?php echo esc_attr($centered_slider); ?>">

            <div class="swiper-wrapper <?php echo esc_attr($images_borderradius_class); ?> <?php echo esc_attr($images_circle_class); ?>" auto-h-slides="<?php echo esc_attr($auto_height); ?>" full-wid-slides="<?php echo esc_attr($full_width_slides); ?>">

            <?php
            //set repeater options
            switch($slides_lazyload){
                case 'yes':
                    $lazy_images_attr = 'loading= "lazy"';
                    break;
                default:
                    $lazy_images_attr = '';
                    break;
            }
            //Convert Repeater strs to arrays
            $slides_show_title = explode(',', $slides_show_title);
            /*if($slides_show_title[1] == '*'){

                for($indexer = 0; $indexer < $total_slides; $indexer++){
                    $slides_show_title[$indexer] = $slides_show_title[0];                    
                }

            }*/ //Deprecated from the version: 6.0 Due-to existance of Short-Code Generator plugin!

            $slides_title = explode(',', $slides_title);
            $total_slides = count($slides_title);

            $slides_title_color = explode(',', $slides_title_color);
            if($slides_title_color[1] == '*'){

                for($indexer = 0; $indexer < $total_slides; $indexer++){
                    $slides_title_color[$indexer] = $slides_title_color[0];
                }

            }

            $slides_img_type = explode(',', $slides_img_type);
            $slides_img_width = explode(',', $slides_img_width);
            $slides_img_alt = explode(',', $slides_img_alt);
            $slides_block_type = explode(',', $slides_block_type);
            $slides_link_url = explode(',', $slides_link_url);
            $slides_link_ext = explode(',', $slides_link_ext);
            $slides_link_nofollow = explode(',', $slides_link_nofollow);
            $slides_is_exthtml = explode(',', $slides_is_exthtml);
            $slides_exthtml = explode(',', $slides_exthtml);
            $slides_has_extclass = explode(',', $slides_has_extclass);
            $slides_img_url = explode(',', $slides_img_url);
            $slides_icon_color = explode(',', $slides_icon_color);

            //2 Layered Reps
            $slides_icon_classes = explode(',', $slides_icon_classes);
            $tmp_inxer = 0;
            foreach($slides_icon_classes as $slides_icon_classe){

                if(str_contains($slides_icon_classe, '|')){
                    $slides_icon_classes[$tmp_inxer] = str_replace('|', ' ', $slides_icon_classe);
                }

                $tmp_inxer++;

                //array_push($icons_class_arr, $header_icons_clas);
            }

            $block_extracss_classes = explode(',', $slides_ext_classes);
            $tmp_inxer = 0;
            foreach($block_extracss_classes as $block_extracss_classe){

                if(str_contains($block_extracss_classe, '|')){
                    $block_extracss_classes[$tmp_inxer] = str_replace('|', ' ', $block_extracss_classe);
                }

                $tmp_inxer++;

                //array_push($icons_class_arr, $header_icons_clas);
            }

            /*$slides_ext_classes = explode(',', $slides_ext_classes);
                $tmp_arr2 = array();
                foreach ($slides_ext_classes as $slides_ext_classe){

                   $tmp = explode('|', $slides_ext_classe);
                   foreach ($tmp as $tm){array_push($tmp_arr2, $tm);}

            }*/
            //Start the Loop on slides
            for ($i = 0; $i < $total_slides; $i++) {

            if(!empty($slides_link_url[$i])){

                $url = $slides_link_url[$i];

            }else $url = '#';

            switch($slides_link_ext[$i]){
                case 'yes':
                    $is_ext = '_blank';
                    break;
                default:
                    $is_ext = '_self';
                    break;
            }

            switch($slides_link_nofollow[$i]){
                case 'yes':
                    $is_nofollow = 'rel= "nofollow"';
                    break;
                default:
                    $is_nofollow = '';
                    break;
            }

            switch($slides_block_type[$i]){

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

            //Extra Css Clases
            $has_extracss_classes = false;
            if ($slides_has_extclass[$i] != ''){//Extra css classes
                $has_extracss_classes = true;
            }
        
            //HTML
            ?>
            <div class="swiper-slide <?php if($has_extracss_classes) { echo esc_attr($block_extracss_classes[$i]); } ?>">

            <a href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($is_ext); ?>" <?php echo $is_nofollow; ?>>
            <div class="woosbymdz-catslider" template="<?php echo esc_attr($slides_block_type[$i]); ?>">
        
            <?php if($slides_img_type[$i] == 'img') {

            ?>
            <div class="woosbymdz-catslider-innerimg-con">
                <img class="woosbymdz-imgcanautowidth <?php echo esc_attr($image_class); ?>" style="height: auto;width: <?php echo esc_attr($slides_img_width[$i])?>px" src="<?php echo esc_url($slides_img_url[$i]); ?>" alt="<?php echo esc_attr($slides_img_alt[$i]); ?>" <?php echo $lazy_images_attr; ?>>
            </div>
            <?php

            }else if ($slides_img_type[$i] == 'icon'){

                ?>
                <div class="woosbymdz-catslider-innerimg-con">
                    <i style="color: <?php echo esc_attr($slides_icon_color[$i]); ?>;" class="woosbymdz-elem-icon <?php echo esc_attr($slides_icon_classes[$i]); ?> <?php echo esc_attr($image_class); ?>"></i>
                </div>
                <?php

            }
            ?>

            <?php if($slides_show_title[$i] == 'yes') { ?>
            <h6 style="color: <?php echo esc_attr($slides_title_color[$i]); ?>" class="<?php echo esc_attr($h6_class); ?>"><?php echo esc_html($slides_title[$i]); ?></h6>
            <?php }//end of title if ?>
            </div>
            </a>

            <?php 
            if($slides_is_exthtml[$i] == 'yes'){

                ?>
                <div class="woosbymdz-catslider-extrahtml-con">
                    <?php 
                    if($slides_exthtml[$i] != '#'){

                        if(strpos($slides_exthtml[$i], '%*%') !== false){//Convert some Sepecial chars from the Short-Code Generator plugin!
                            $slides_exthtml[$i] = str_replace('%*%', '=', $slides_exthtml[$i]);
                        }
                        //ob_start();  

                        if($this->is_likely_html_encoded($slides_exthtml[$i])){
                            echo htmlspecialchars_decode($slides_exthtml[$i]);
                        }else echo $slides_exthtml[$i];

                        //ob_flush(); //ob_clean();
                    }
                    //echo $slides_exthtml[$i]; 
                    ?>
                </div>
                <?php

            }
            ?>

            </div>
            <?php }//End of slides Loop ?>
            </div><!--End of swiper inner-->

            <div style="transform: translateY(<?php echo esc_attr($dots_margin); ?>px)" class="swiper-pagination"></div>

            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>

            </div>
            </div><!--End of main con--> 
            <?php
            $contents = ob_get_contents();
            ob_get_clean();

            return $contents;

        }

        //Miscs
        private function is_likely_html_encoded($text)
        {
            return preg_match('/&(?:lt|gt|amp|quot|#[0-9]+);/i', $text) > 0;
        }
       
    }  

}

//init
woosbymdz_mainconfigs::get_instance();
//close the PHP tag to reduce the blank spaces ?>