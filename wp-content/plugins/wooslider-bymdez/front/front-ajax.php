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

/*Fetch Products*/  
if (!function_exists('woosbymdz_fetchpro'))
{

    function woosbymdz_fetchpro() 
    {
        
        $is_no_ajax_ref_checker = get_option('woosbymdz_noajaxrefchecker');

        if($is_no_ajax_ref_checker != 1){

            if ( ! check_ajax_referer( 'woosbymdz-uio2899nb-w144wkxs', 'wwkxssjk', false ) ) 
            {
                wp_send_json_error( 'WooSlider: Ajax-Error!' );
                wp_die();
            }//For the Security
        }
        


        $is_loop = sanitize_text_field($_POST['is_loop']);
        if($is_loop == 1){
            $template_id = sanitize_text_field($_POST['temp_id']);

            $pluginElementor = \Elementor\Plugin::instance();
        }

        $cat = sanitize_text_field($_POST['cat']);
        $number = sanitize_text_field($_POST['number']); $number = intval($number);
        $stock = sanitize_text_field($_POST['stock']);
        $seeallcat = sanitize_text_field($_POST['seeallcat']);
        $type = sanitize_text_field($_POST['type']);
        $auth = sanitize_text_field($_POST['auth']);
        $seeallcat_bgcolor = sanitize_text_field($_POST['seeallcat_bgcolor']);
        $seeallcat_bgwalp = sanitize_text_field($_POST['seeallcat_bgwalp']);
        $seeallcat_link = sanitize_url($_POST['seeallcat_link']);
        $seeallcat_link_ext = sanitize_text_field($_POST['seeallcat_link_ext']);
        $seeallcat_link_follow = sanitize_text_field($_POST['seeallcat_link_follow']);
        $seeallcat_title = sanitize_text_field($_POST['seeallcat_title']);
        $seeallcat_titlecolor = sanitize_text_field($_POST['seeallcat_titlecolor']);     
        $use_inf = sanitize_text_field($_POST['use_inf']);
        $inf_mode = sanitize_text_field($_POST['inf_mode']);
        $inf_num = sanitize_text_field($_POST['inf_num']);
        $inf_txt = sanitize_text_field($_POST['inf_txt']);
        $taken_products = 0;
        

        $product_ids = $meta_key = $order = $orderby = '';
        $meta_query = array();
        switch ($type) {
            case 'featured':
                $product_ids = wc_get_featured_product_ids();
                break;
            case 'sale':
                $product_ids = wc_get_product_ids_on_sale();
                break;
            case 'bestsellings':
                $meta_key = 'total_sales';
                $order = 'DESC';
                $orderby = 'meta_value_num';
                break;
            case 'lowsellings':
                $meta_key = 'total_sales';
                $order = 'ASC';
                $orderby = 'meta_value_num';
                break;
            case 'lastedited':
                $orderby = 'modified';
                break;
            case 'olds':
                $order = 'ASC';
                break;
            case 'rand':
                $orderby = 'rand';
                break;
            default:
                $product_ids = '';
                break;
        }

        switch ($stock){
            case 'only_instocks':
                $meta_query = array(
                    array(
                        'key' => '_stock_status',
                        'value' => 'instock',
                        'compare' => '=',
                    ),
                );
                break;
            case 'only_not_instocks':
                $meta_query = array(
                    array(
                        'key' => '_stock_status',
                        'value' => 'outofstock',
                        'compare' => '=',
                    ),
                );
                break;
        }

        if (!empty($cat)){//Cats
            $tax_query = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $cat,
                    'operator' => 'IN'
                ),
            );
        }else $tax_query = '';

        if($seeallcat == 'yes'){

            switch($seeallcat_link){
                case '':
                    $seeallcat_link = '#';
                    break;
                default:
                    $seeallcat_link = $seeallcat_link;
                    break;
            }
            switch($seeallcat_link_ext){
                case 'on':
                    $seeallcat_link_ext = '_blank';
                    break;
                default:
                    $seeallcat_link_ext = '_self';
                    break;
            }
            switch($seeallcat_link_follow){
                case 'on':
                    $seeallcat_link_follow = 'nofollow';
                    break;
                default:
                    $seeallcat_link_follow = 'follow';
                    break;
            }
            switch($seeallcat_bgcolor){
                case '':
                    $seeallcat_bgcolor = '#00000000';
                    break;
                default:
                    $seeallcat_bgwalp = '#';
                    break;
            }
            switch($seeallcat_titlecolor){
                case '':
                    $seeallcat_titlecolor = '#000';
                    break;
            }
        }

        if($type == 'chargeds'){

            $tmp_arr = array(
                'key' => 'woosbymdz_is_charged',
                'value' => '1',
                'compare' => '=',
            );
            array_push($meta_query, $tmp_arr);
    
        }

        ob_start(); //start the output buffering

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $number,
            'post__in'=> $product_ids,
            'meta_key' => $meta_key,
            'order' => $order,
			'orderby'  => $orderby,
            'tax_query' => $tax_query,
            'meta_query' => $meta_query,
        );

        /*Get Themes Configs*/
        include_once WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'this-theme-confs.php';

        if(function_exists('woosbymdz_ret_themeSwiperClasses')){
            $theme_swiper_classes = woosbymdz_ret_themeSwiperClasses();
        }else $theme_swiper_classes = '';
        
                
        //start the loop on products
        if ($auth == '1'){//show only to logged in users

        //Procards-template
        $pro_cards_temp = 'default_temp';
        if(isset($_POST['pro_cards_temp'])){
            $pro_cards_temp = sanitize_text_field($_POST['pro_cards_temp']); 
        }
        /***/

        
        $loop = new WP_Query( $args );

        while ( $loop->have_posts()) {

            $loop->the_post();
            global $product;
            $taken_products++;
      
            ?> 
            <div class="swiper-slide <?php echo $theme_swiper_classes;?> <?php if(($seeallcat == 'yes' && $taken_products === $number) || ($use_inf == 'yes' && $taken_products === $number && ($inf_mode == 'both' || $inf_mode == 'slide'))){echo 'woosbymdz-seeallcat'; } ?>">

            <?php 
            if (($seeallcat == 'yes' && $taken_products === $number) || ($use_inf == 'yes' && $taken_products === $number && ($inf_mode == 'both' || $inf_mode == 'slide'))){ 

                if($use_inf != 'yes' || ($use_inf == 'yes' && $inf_mode == 'nextarrow')){

                    echo "<a href='".$seeallcat_link."' target='".$seeallcat_link_ext."' rel='".$seeallcat_link_follow."'><div class='seealcat-inner' style='background-color:".$seeallcat_bgcolor."; background-image: url(".$seeallcat_bgwalp.");'><p style='color:".$seeallcat_titlecolor."'>".$seeallcat_title."</p></div></a>"; 

                }else if($taken_products === $number && $use_inf == 'yes' && ($inf_mode == 'both' || $inf_mode == 'slide')){

                    echo "<a class='slide-infiniter' href='#' onclick='woosbymdz_infital_next(event, this)' statoost='ready' inload-txt='".$inf_txt."' pro-src='".$type."' fetch='cat' cats='".$cat."' stock='".$stock."' exc='' num='".$number."' numload='".$inf_num."' method='".$inf_mode."'>
                    <div class='seealcat-inner' style='background-color:".$seeallcat_bgcolor."; background-image: url(".$seeallcat_bgwalp.");'><p style='color:".$seeallcat_titlecolor."'>".$seeallcat_title."</p></div>
                    </a>";

                }
                

            }
            ?> 
            <?php 
            if($is_loop != 1){

                switch($pro_cards_temp){
                    case 'default_temp':
                        wc_get_template_part( 'content', 'product' ); 
                        break;
                    default:
                        $pid = get_the_ID();

                        require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'class-getandshowprods.php';

                        $class_getanshow = new woosbymdz_getandshow();

                        $class_getanshow->render_pro_card_temp($pro_cards_temp, $pid);
                        break;
                }
                
            }else {
                echo $pluginElementor->frontend->get_builder_content_for_display( $template_id );
            }
            
            ?>
            </div>
            <?php           
            
        }
        wp_reset_postdata();
            

        }else {
            ?> <div class="woosbymdz-ajax-mssg">برای مشاهده این محصولات ابتدا وارد شوید</div> <?php
        }
        
        //render the output
        $content = ob_get_contents();
	    ob_end_clean();
	        
 	    echo $content;
       
        wp_die();
    }

}
add_action( 'wp_ajax_woosbymdz_fetchpro', 'woosbymdz_fetchpro' );
add_action( 'wp_ajax_nopriv_woosbymdz_fetchpro', 'woosbymdz_fetchpro' );
/**/

/*Infinite Slider Fetcher*/
if (!function_exists('woosbymdz_fetchpro_infinite'))
{

function woosbymdz_fetchpro_infinite() 
{
    $is_no_ajax_ref_checker = get_option('woosbymdz_noajaxrefchecker');

    if($is_no_ajax_ref_checker != 1){

        if ( ! check_ajax_referer( 'woosbymdz-uio2899nb-w144wkxs', 'wwkxssjk', false ) ) 
        {
            wp_send_json_error( 'WooSlider: Ajax-Error!' );
            wp_die();
        }//For the Security
    }

    $is_loop = sanitize_text_field($_POST['is_loop']); 

    $fetch = sanitize_text_field($_POST['fetch']);
    if($fetch != 'cat' && $is_loop != 1){
        echo "<div class='swiper-slide'>
        <span>Error: Only Can Fetch Products From Product-Categories</span>
        </div>
        ";
        wp_die();
    }

    $src = sanitize_text_field($_POST['src']);
    $cats = sanitize_text_field($_POST['cats']);
    $stock = sanitize_text_field($_POST['stock']);
    $exc = sanitize_text_field($_POST['exc']);
    $mode = sanitize_text_field($_POST['mode']);

    $num = sanitize_text_field($_POST['num']); 
    $num = intval($num);
    
    $numload = sanitize_text_field($_POST['numload']); 
    $numload = intval($numload);

    //Procards-template
    $pro_cards_temp = 'default_temp';
    if(isset($_POST['pro_cards_temp'])){
        $pro_cards_temp = sanitize_text_field($_POST['pro_cards_temp']); 
    }
    /***/
    

    $taken_products = 0;
    $products_exclude = explode(',', $exc); //get excluded products
    $product_ids = $meta_key = $order = $orderby = '';
    $tax_query = $meta_query = array();

    if($is_loop == 1 || $is_loop == 'yes'){

        $post_type = sanitize_text_field($_POST['post_type']);
        $query_order_by = sanitize_text_field($_POST['q_order_by']);
        $query_order = sanitize_text_field($_POST['q_order']);
        $template_id = sanitize_text_field($_POST['temp_id']);

    }


    //Define Products Source
    switch ($src) {
        case 'featured':
            $product_ids = wc_get_featured_product_ids();
            break;
        case 'sale':
            $product_ids = wc_get_product_ids_on_sale();
            break;
        case 'bestsellings':
            $meta_key = 'total_sales';
            $order = 'DESC';
            $orderby = 'meta_value_num';
            break;
        case 'lowsellings':
            $meta_key = 'total_sales';
            $order = 'ASC';
            $orderby = 'meta_value_num';
            break;
        case 'lastedited':
            $orderby = 'modified';
            break;
        case 'olds':
            $order = 'ASC';
            break;
        case 'rand':
            $orderby = 'rand';
            break;
        case 'upsells':
        case 'crossells':
        case 'related':
        case 'recently_viewed':
        default:
            $product_ids = '';
            break;
    }

    if(trim($cats) != ''){

        $cats = explode(',', $cats);
        $tmp_arr = array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $cats,
            'operator' => 'IN'
        ); 

    }
    
    if(isset($tmp_arr)){array_push($tax_query, $tmp_arr);}
    

    //Stock Options
    switch ($stock){
        case 'only_instocks':
            $meta_query = array(
                array(
                    'key' => '_stock_status',
                    'value' => 'instock',
                    'compare' => '=',
                ),
            );
            break;
        case 'only_not_instocks':
            $meta_query = array(
                array(
                    'key' => '_stock_status',
                    'value' => 'outofstock',
                    'compare' => '=',
                ),
            );
            break;
    }

    if($src == 'chargeds'){

        $tmp_arr = array(
            'key' => 'woosbymdz_is_charged',
            'value' => '1',
            'compare' => '=',
        );
        array_push($meta_query, $tmp_arr);

    }

    $numload = $numload + $num;


    /*ARGS*/
    if($is_loop != 1){

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $numload,
            'post__in'=> $product_ids,
            'meta_key' => $meta_key,
            'order' => $order,
            'orderby'  => $orderby,
            'tax_query' => $tax_query,
            'meta_query' => $meta_query,
            'post__not_in' => $products_exclude,
        );

    }else {

        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => $numload,
            //'meta_key' => $meta_key,
            'order' => $query_order,
            'orderby'  => $query_order_by,
            //'tax_query' => $tax_query,
            //'meta_query' => $meta_query,
        );

        $pluginElementor = \Elementor\Plugin::instance();
    }
    

    /*Get Themes Configs*/
    include_once WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'this-theme-confs.php';

    if(function_exists('woosbymdz_ret_themeSwiperClasses')){
        $theme_swiper_classes = woosbymdz_ret_themeSwiperClasses();
    }else $theme_swiper_classes = '';

    //HTML
    ob_start();

    //start the loop on products
    $loop = new WP_Query( $args );
    
    while ( $loop->have_posts()) {

        $loop->the_post();
        global $product;
        if(($taken_products+1) <= $num){
            $taken_products++;
            continue;
        }
        ?> 
        <div class="swiper-slide <?php echo $theme_swiper_classes;?>"> 

        <?php 
        if($is_loop != 1){
            switch($pro_cards_temp){
                    case 'default_temp':
                        wc_get_template_part( 'content', 'product' ); 
                        break;
                    default:
                        $pid = get_the_ID();

                        require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'class-getandshowprods.php';

                        $class_getanshow = new woosbymdz_getandshow();

                        $class_getanshow->render_pro_card_temp($pro_cards_temp, $pid);
                        break;
                }
        }else {
            
            echo $pluginElementor->frontend->get_builder_content_for_display( $template_id );
        }
        
        ?>

        </div> 
        <?php      
                
    }

    wp_reset_postdata();

    $conts = ob_get_contents();
    ob_get_clean();

    echo $conts;
       
    wp_die();
}

}
add_action( 'wp_ajax_woosbymdz_fetchpro_infinite', 'woosbymdz_fetchpro_infinite' );
add_action( 'wp_ajax_nopriv_woosbymdz_fetchpro_infinite', 'woosbymdz_fetchpro_infinite' );
/**/
//close the PHP tag to reduce the blank spaces ?>