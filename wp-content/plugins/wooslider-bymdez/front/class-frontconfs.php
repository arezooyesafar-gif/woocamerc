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

if (!class_exists('woosbymdz_frontconf')) 
{

    class woosbymdz_frontconf 
    {

        //PROPS
        private static $instance;

        //constructor
        public function __Construct() 
        {

            add_action('wp_head', array($this, 'defajaxurl')); //define ajaxurl on the front

            //add jquery
            $import_jq = get_option('woosbymdz_importjq');
            if($import_jq == '1'){
                add_action('wp_enqueue_scripts', array($this, 'add_jQ'));
            }      
            
            //add Slider (Yoast) to products page
            $is_add_slider_to_productspage = get_option('woosbymdz_yoast_maincat_addslider');
            if($is_add_slider_to_productspage == '1'){

                add_action('woocommerce_after_single_product', array($this, 'addslider_products'));

            }

            //Set JS Cookies
            $last_seen_cookie_js = get_option('woosbymdz_productstracker_js');
            $last_seen_cookie = get_option('woosbymdz_last_seen_cookie');

            if($last_seen_cookie_js == '1' && $last_seen_cookie == '1'){

                wp_enqueue_script('woosbymdz-cookies-js', WOOSBYMDZ_URL.'front/js/cookies/cookie.js', array(), WOOSBYMDZ_VER, true);

            }

            //Global Import Assets
            $is_global_assets = get_option('woosbymdz_globalimport');
            if($is_global_assets == '1'){
                add_action('wp_enqueue_scripts', array($this, 'add_assets_globally'));

            }     
            
        }

        //METHODS
        public static function get_instance() 
        {
            if ( ! isset( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function defajaxurl()
        {

            echo '<script type="text/javascript"> var ajaxurl = "' . admin_url('admin-ajax.php') . '"; </script><!--Added by woosbymdz plugin-->';
        }

        //add jquery
        function add_jQ() { wp_enqueue_script('jquery'); }

        //add slider to products page
        public function addslider_products() 
        {

            $shop_url = get_permalink( wc_get_page_id( 'shop' ) );
            $default_shortcode = '[woo_modern_slider header=0 stock=1 yoast_main_cat=yes src=rand slider_arrows_show=dynamic navigation=none notfound_txt="محصولی پیشنهادی وجود ندارد" notfound_btn_url='.$shop_url.']';

            $is_custom_shortcode = get_option('woosbymdz_yoast_maincat_iscustomizedshortcode');
            $yoast_slider_title = get_option('woosbymdz_ppage_slidertitle');

            if($is_custom_shortcode == '1'){

                $default_shortcode = get_option('woosbymdz_yoast_maincat_customizedshortcode'); 

            }
            if($yoast_slider_title == ''){
                $yoast_slider_title = esc_html__('Related Products', 'wooslider-bymdez');
            }
	        ?>
            <div id="woosbymdz-customrelated-pros-con">

            <h6 class="woosbymdz-relatedpros-title"><?php echo ($yoast_slider_title); ?></h6>

	        <?php
	        echo do_shortcode($default_shortcode, false);
	        ?>
            
            </div>
            <?php

        }

        //Global Import Assets
        public function add_assets_globally()
        {

            //add css & js files
            $is_browsernocache = get_option('woosbymdz_isbrowsernocache');
            $dont_import_swiper = get_option('woosbymdz_donimport_swiper');
            $plugin_current_ver = intval(WOOSBYMDZ_SW_VER);
            $random_ver = $plugin_current_ver*99;

            switch ($is_browsernocache) {
                case '1':
                    $dynamic_ver = get_option('woosbymdz_tmpversion');
                    break;
                default:
                    $dynamic_ver = "";
                    break;
            }

            if($dont_import_swiper != '1'){//if Dont add Swiper.js is not activated

                wp_enqueue_style('woosbymdz-swiper', WOOSBYMDZ_URL . 'front/css/swiper-min.css', array(), WOOSBYMDZ_SW_VER);

                wp_enqueue_script('woosbymdz-swiper', WOOSBYMDZ_URL . 'front/js/swiper-min.js', array(), WOOSBYMDZ_SW_VER, true);

            }
            
            $final_version = WOOSBYMDZ_VER.'.'.$dynamic_ver; 
            wp_enqueue_style('woosbymdz-front', WOOSBYMDZ_URL . 'front/css/frontcss.css', array(), $final_version);
            wp_enqueue_script('woosbymdz-front', WOOSBYMDZ_URL . 'front/js/frontjs.js', array(), $final_version, true);
        }

    } 
}

//init
woosbymdz_frontconf::get_instance();
//close the PHP tag to reduce the blank spaces ?>