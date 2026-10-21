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

/*Fetch Products Class*/

if (!class_exists('woosbymdz_getandshow'))
{

class woosbymdz_getandshow 
{

    //Fetch Products
    public function getandshow_mdz($all_settings) 
    {

        $is_woo_exists = $this->check_woo();

        if($is_woo_exists) {//Just runs if find Woo Plugin
 
        $taken_products = 0;

        $all_settings['products_number'] = intval($all_settings['products_number']); 

        if ($all_settings['products_number'] != ''){
            $number_args = $all_settings['products_number'];
        }else $number_args = -1;
              
        $products_extra = explode(',', $all_settings['products_extra']); //get extra products
        $products_exclude = explode(',', $all_settings['products_exclude']); //get excluded products
        $product_ids = $meta_key = $order = $orderby = '';
        $tax_query = $meta_query = array();

        $last_seen_cookie_js = get_option('woosbymdz_productstracker_js');

        //Define Products Source
        switch ($all_settings['src']) {
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
            case 'recently_viewed':
                if($last_seen_cookie_js != '1'){

                    $product_ids = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();

                }else if($last_seen_cookie_js == '1'){

                    $product_ids = ! empty( $_COOKIE['woosbymdz_recently_viewed_js'] ) ? explode( '|', $_COOKIE['woosbymdz_recently_viewed_js'] ) : array();

                }
                break;
            case 'upsells':
                $product_ids = $this->get_related_pros(1, "", []);
                break;
            case 'crossells':
                $product_ids = $this->get_related_pros(2, "", []);
                break;
            case 'related':
                $product_ids = $this->get_related_pros(3, $number_args, []);
                break;
            default:
                $product_ids = '';
                break;
        }


        //Set Query Operator (Custom_tax)
        if ($all_settings['products_fetch'] == 'custom_tax') {
            $tax_query = array('relation' => $all_settings['products_query_operator2']);
        }

        //Fetch From Products-Attrs
        if($all_settings['products_use_attrs'] == 'yes' && $all_settings['yoast_main_cat'] != 'yes'){

            if($all_settings['products_fetch'] != 'custom_tax') {

                $tax_query = array('relation' => $all_settings['products_query_operator']);

            }
            
            foreach($all_settings as $name => $all_setting){

                if(str_contains($name, 'products_attr_')){

                    if(!empty($all_setting)){

                        $name = explode('products_attr_', $name);
                        $tmp_arr = array(
                          'taxonomy' => $name[1],
                          'field' => 'term_id',
                          'terms' => $all_setting,
                          'operator' => 'IN'
                        );
                    
                        array_push($tax_query, $tmp_arr);

                    }   

                }

            }
        }

        //Fetch From Categories
        if (!empty($all_settings['cat']) && $all_settings['products_fetch'] == 'cat' && $all_settings['yoast_main_cat'] != 'yes'){

            if(!is_array($all_settings['cat'])){

                $all_settings['cat'] = explode(',', $all_settings['cat']);
            }
            

            $tmp_arr = array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $all_settings['cat'],
                'operator' => 'IN'
            ); 

            array_push($tax_query, $tmp_arr);

            

        }

        //Fetch From Tags
        if (!empty($all_settings['products_tags']) && $all_settings['products_fetch'] == 'tag' && $all_settings['yoast_main_cat'] != 'yes'){

            $tmp_arr = array(

                'taxonomy' => 'product_tag',
                'field' => 'term_id',
                'terms' => $all_settings['products_tags'],
                'operator' => 'IN'
            ); 

            array_push($tax_query, $tmp_arr);

        }  
        
        //Fetch From Custom-Tax
        if ($all_settings['products_fetch'] == 'custom_tax' && $all_settings['yoast_main_cat'] != 'yes'){

            foreach($all_settings as $name => $all_setting){

                if(str_contains($name, 'products_customtax_')){

                    if(!empty($all_setting)){

                        $name = explode('products_customtax_', $name);
                        $tmp_arr = array(
                          'taxonomy' => $name[1],
                          'field' => 'term_id',
                          'terms' => $all_setting,
                          'operator' => 'IN'
                        );
                    
                        array_push($tax_query, $tmp_arr);
                    }   

                }

            }

        }  

        //Fetch From Yoast-Main-Cat
        if($all_settings['yoast_main_cat'] == 'yes'){

            $get_yoast_cat = $this->yoast_get_pro_maincat();

            if($get_yoast_cat != ''){//if yoast detected & is product page

                $get_yoast_cat = explode('|', $get_yoast_cat);
                $get_yoast_cat[0] = intval($get_yoast_cat[0]);
                $get_yoast_cat[1] = intval($get_yoast_cat[1]);

                $tax_query = array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $get_yoast_cat[0],
                        'operator' => 'IN'
                    ),
                );

                array_push($products_exclude, $get_yoast_cat[1]);

            }

        }

        //Stock Options
        switch ($all_settings['only_instock']){
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

        if($all_settings['src'] == 'chargeds'){
            $tmp_arr = array(
                'key' => 'woosbymdz_is_charged',
                'value' => '1',
                'compare' => '=',
            );
            array_push($meta_query, $tmp_arr);
        }

        /*ARGS*/
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $number_args,
            'post__in'=> $product_ids,
            'meta_key' => $meta_key,
            'order' => $order,
			'orderby'  => $orderby,
            'tax_query' => $tax_query,
            'meta_query' => $meta_query,
            'post__not_in' => $products_exclude,
        );

        //Seeallcat confs
        if ($all_settings['products_allcats_show'] == 'yes') {
            
            switch($all_settings['products_allcats_url']['url']){
                case '':
                    $seeallcat_link = '#';
                    break;
                default:
                    $seeallcat_link = $all_settings['products_allcats_url']['url'];
                    break;
            }
            switch($all_settings['products_allcats_url']['is_external']){
                case 'on':
                    $seeallcat_link_ext = '_blank';
                    break;
                default:
                    $seeallcat_link_ext = '_self';
                    break;
            }
            switch($all_settings['products_allcats_url']['nofollow']){
                case 'on':
                    $seeallcat_link_follow = 'nofollow';
                    break;
                default:
                    $seeallcat_link_follow = 'follow';
                    break;
            }
            switch($all_settings['products_allcats_backcolor']){
                case '':
                    $seeallcat_bgcolor = '#00000000';
                    $seeallcat_bgwalp = $all_settings['products_allcats_backimg']['url'];
                    break;
                default:
                    $seeallcat_bgwalp = '#';
                    $seeallcat_bgcolor = $all_settings['products_allcats_backcolor'];
                    break;
            }
            switch($all_settings['products_allcats_titlecolor']){
                case '':
                    $all_settings['products_allcats_titlecolor'] = '#fff';
                    break;
            }


            if($all_settings['products_allcats_yoast_link'] == 'yes'){
            

                $get_yoast_cat = $this->yoast_get_pro_maincat();

                if(!empty($get_yoast_cat)){

                    if(strpos($get_yoast_cat, '|') !== false){
                        $get_yoast_cat = explode('|', $get_yoast_cat);

                        if(isset($get_yoast_cat[0])){
                            $get_yoast_cat = $get_yoast_cat[0];
                        }
                        
                    }
                    

                    $term = get_term($get_yoast_cat);
                    $term_link = get_term_link($term);

                    $check_url = filter_var($term_link, FILTER_VALIDATE_URL);
                    if ($check_url !== false) {
                        $seeallcat_link = $term_link;
                    }
                    //var_dump($term_link);

                }
                //var_dump($get_yoast_cat);
            }
        }

        

        //Not found confs
        if($all_settings['notfound_show'] == 'yes'){

            switch($all_settings['notfound_btn_url']['url']){
                case '':
                    $notfound_btn_link = '#';
                    break;
                default:
                    $notfound_btn_link = $all_settings['notfound_btn_url']['url'];
                    break;
            }
            switch($all_settings['notfound_btn_url']['is_external']){
                case 'on':
                    $notfound_btn_link_ext = '_blank';
                    break;
                default:
                    $notfound_btn_link_ext = '_self';
                    break;
            }
            switch($all_settings['notfound_btn_url']['nofollow']){
                case 'on':
                    $notfound_btn_link_follow = 'nofollow';
                    break;
                default:
                    $notfound_btn_link_follow = 'follow';
                    break;
            }


        }

        /*Get Themes Configs*/
        include_once WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'this-theme-confs.php';

        if(function_exists('woosbymdz_ret_themeSwiperClasses')){
            $theme_swiper_classes = woosbymdz_ret_themeSwiperClasses();
        }else $theme_swiper_classes = '';

        //prevent the empty see-all-cat title (set default txt)
        if(empty($all_settings['products_allcats_title'])){
            $all_settings['products_allcats_title'] = esc_html__('+ Show More', 'wooslider-bymdez');
        }
        /***/

        //Set product-cards template
        $pro_cards_template = 'default_temp';
        if(isset($all_settings['products_template']) && !empty($all_settings['products_template'])){
            $pro_cards_template = $all_settings['products_template'];
        }
        /***/

        ob_start(); //start the output buffering
        
        //start the loop on products
        $loop = new WP_Query( $args );
    
        while ( $loop->have_posts()) {
    
            $loop->the_post();
            global $product;
            $taken_products++;
            ?> 
            <div class="swiper-slide <?php echo $theme_swiper_classes;?> <?php if(($all_settings['products_allcats_show'] == 'yes' && $taken_products === $number_args && $products_extra[0]=='') || ($taken_products === $number_args && $products_extra[0]=='' && $all_settings['adv_infinital'] == 'yes' && ($all_settings['adv_infinital_mode'] == 'both' || $all_settings['adv_infinital_mode'] == 'slide'))){echo 'woosbymdz-seeallcat'; } ?>">

            <?php 
            if (($all_settings['products_allcats_show'] == 'yes' && $taken_products === $number_args && $products_extra[0]=='') || ($all_settings['adv_infinital'] == 'yes' && ($all_settings['adv_infinital_mode'] == 'both' || $all_settings['adv_infinital_mode'] == 'slide'))){ 

                if($all_settings['adv_infinital'] != 'yes' || ($all_settings['adv_infinital'] == 'yes' && $all_settings['adv_infinital_mode'] == 'nextarrow')){

                    echo "<a href='".$seeallcat_link."' target='".$seeallcat_link_ext."' rel='".$seeallcat_link_follow."'><div class='seealcat-inner' style='background-color:".$seeallcat_bgcolor."; background-image: url(".$seeallcat_bgwalp.");'><p style='color:".$all_settings['products_allcats_titlecolor']."'>".$all_settings['products_allcats_title']."</p></div></a>";

                }else if($taken_products === $number_args && $products_extra[0]=='' && $all_settings['adv_infinital'] == 'yes' && ($all_settings['adv_infinital_mode'] == 'both'|| $all_settings['adv_infinital_mode'] == 'slide')){

                    if($all_settings['cat'] != "" && $all_settings['cat'] != " "){
                        $str_cats = implode(",", $all_settings['cat']);
                    }
                    echo "<a class='slide-infiniter' href='#' onclick='woosbymdz_infital_next(event, this)' statoost='ready' inload-txt='".$all_settings["adv_infinital_txt"]."' pro-src='".$all_settings['src']."' fetch='".$all_settings['products_fetch']."' cats='".$str_cats."' stock='".$all_settings['only_instock']."' exc='".$all_settings['products_exclude']."' num='".$number_args."' numload='".$all_settings['adv_infinital_num']."' method='".$all_settings['adv_infinital_mode']."'><div class='seealcat-inner' style='background-color:".$seeallcat_bgcolor."; background-image: url(".$seeallcat_bgwalp.");'>
                    <p style='color:".$all_settings['products_allcats_titlecolor']."'>".$all_settings['products_allcats_title']."</p>
                    </div></a>";

                }

            }
            ?> 

            <?php 
            switch($pro_cards_template){
                case 'default_temp':
                    wc_get_template_part( 'content', 'product' );
                    break;
                default:
                    $pid = get_the_ID();
                    $this->render_pro_card_temp($pro_cards_template, $pid, $all_settings['products_template_opt2'], [
                        'translate_woo_curr' => $all_settings['products_template_opt1'],
                        'vert_align_temp1' => $all_settings['products_template_opt3'],
                        'vert_align_temp2' => $all_settings['products_template_opt4'],
                        'rem_proname_from_childs_temp3' => $all_settings['products_template_opt5'],
                    ]);
                    break;

            }
            ?>
            </div> 
            <?php      
                    
        }

        wp_reset_postdata();

        if($products_extra[0] == '' && $taken_products == 0 && $all_settings['notfound_show'] == 'yes') {//Show Not-Found products message
 
            ?>
            <div class="woosbymdz-notfound-con" style="top: <?php echo esc_attr($all_settings['notfound_space_top']); ?>px;">
 
                <div>
                <h6><?php echo esc_html($all_settings['notfound_txt']); ?></h6>

                <button class="<?php echo esc_attr($all_settings['notfound_btn_classes']); ?>"><a href="<?php echo esc_url($notfound_btn_link); ?>" target="<?php echo esc_attr($notfound_btn_link_ext) ?>" rel="<?php echo esc_attr($notfound_btn_link_follow) ?>"><?php echo esc_html($all_settings['notfound_btn_txt']); ?></a></button>

                </div>

            </div>
            <?php
        }

        /*Extra products*/
        if ($products_extra[0] != ''){
       
            $args = array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'post__in'=> $products_extra,
            );

            $loop = new WP_Query( $args );
    
            while ( $loop->have_posts()) {
    
            $loop->the_post();
            global $product;

            ?><div class="swiper-slide <?php echo $theme_swiper_classes;?>">
            <?php 
            switch($pro_cards_template){
                case 'default_temp':
                    wc_get_template_part( 'content', 'product' );
                    break;
                default:
                    $pid = get_the_ID();
                    $this->render_pro_card_temp($pro_cards_template, $pid, $all_settings['products_template_opt2'], [
                        'translate_woo_curr' => $all_settings['products_template_opt1'],
                        'vert_align_temp1' => $all_settings['products_template_opt3'],
                        'vert_align_temp2' => $all_settings['products_template_opt4'],
                        'rem_proname_from_childs_temp3' => $all_settings['products_template_opt5'],
                    ]);
                    break;
            }
            ?>
            </div> 
            <?php       
             
            }
            wp_reset_postdata();

            if($all_settings['products_allcats_show'] == 'yes' || $all_settings['adv_infinital'] == 'yes'){

                $taken_products = 0;
                $tmp = $products_extra[0];
                $products_extra = array();

                array_push($products_extra, $tmp);
                $products_extra_number = count($products_extra);

                $args = array(
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'post__in'=> $products_extra,
                );
    
                $loop = new WP_Query( $args );
        
                while ( $loop->have_posts()) {
        
                $loop->the_post();
                global $product;
                $taken_products++;
                ?>
                <div class="swiper-slide <?php echo $theme_swiper_classes;?> <?php echo esc_attr('woosbymdz-seeallcat'); ?>">

                <?php
                if($all_settings['adv_infinital'] != 'yes' || ($all_settings['adv_infinital'] == 'yes' &&$all_settings['adv_infinital_mode'] == 'nextarrow')){

                    echo "<a href='".$seeallcat_link."' target='".$seeallcat_link_ext."' rel='".$seeallcat_link_follow."'><div class='seealcat-inner' style='background-color:".$seeallcat_bgcolor."; background-image: url(".$seeallcat_bgwalp.");'><p style='color:".$all_settings['products_allcats_titlecolor']."'>".$all_settings['products_allcats_title']."</p></div></a>"; 

                }else if($all_settings['adv_infinital'] == 'yes' && ($all_settings['adv_infinital_mode'] == 'both'|| $all_settings['adv_infinital_mode'] == 'slide')){

                    if($all_settings['cat'] != '' && $all_settings['cat'] != ' '){
                        $str_cats = implode(",", $all_settings['cat']);
                    }
                    echo "<a class='slide-infiniter' href='#' onclick='woosbymdz_infital_next(event, this)' statoost='ready' inload-txt='".$all_settings["adv_infinital_txt"]."' pro-src='".$all_settings['src']."' fetch='".$all_settings['products_fetch']."' cats='".$str_cats."' stock='".$all_settings['only_instock']."' exc='".$all_settings['products_exclude']."' num='".$number_args."' numload='".$all_settings['adv_infinital_num']."' method='".$all_settings['adv_infinital_mode']."'><div class='seealcat-inner' style='background-color:".$seeallcat_bgcolor."; background-image: url(".$seeallcat_bgwalp.");'>
                    <p style='color:".$all_settings['products_allcats_titlecolor']."'>".$all_settings['products_allcats_title']."</p>
                    </div></a>";

                }
                ?>

                <?php 
                switch($pro_cards_template){
                    case 'default_temp':
                        wc_get_template_part( 'content', 'product' );
                        break;
                    default:
                        $pid = get_the_ID();
                        $this->render_pro_card_temp($pro_cards_template, $pid, $all_settings['products_template_opt2'], [
                            'translate_woo_curr' => $all_settings['products_template_opt1'],
                            'vert_align_temp1' => $all_settings['products_template_opt3'],
                            'vert_align_temp2' => $all_settings['products_template_opt4'],
                            'rem_proname_from_childs_temp3' => $all_settings['products_template_opt5'],
                        ]);
                        break;
                }
                ?>
                </div>
                <?php       
                 
                }
                wp_reset_postdata();

            }

        }
        
        
        ?> 

        </div><!-- end of swiper inner -->

        <?php
        /*swiper confs*/
        $arrow_next_class = $arrow_prev_class = '';
        switch ($all_settings['swiper_arrows']){//set arrows

            case 'none':
                $arrow_next_class = 'woosbymdz-none';
                $arrow_prev_class = 'woosbymdz-none';
                break;
            case 'dynamic':
                $arrow_next_class = 'woosbymdz-arrownext-hide woosbymdz-arrow-transit';
                $arrow_prev_class = 'woosbymdz-arrowprev-hide woosbymdz-arrow-transit';
                break;
            case 'desktop':
                $arrow_next_class = 'woosbymdz-deskarrows';
                $arrow_prev_class = 'woosbymdz-deskarrows';
                break;
            case 'mobile':
                $arrow_next_class = 'woosbymdz-mobarrows';
                $arrow_prev_class = 'woosbymdz-mobarrows';
                break;
            case 'mobile_tablet':
                $arrow_next_class = 'woosbymdz-mobtabarrows';
                $arrow_prev_class = 'woosbymdz-mobtabarrows';
                break;
            default:
                break;
        }

        switch($all_settings['pags_style_fraction_direction']){
            case 'yes':
                if(is_rtl()){
                    $swiper_fraction_dir = 'woosbymdz-ltr';
                }else $swiper_fraction_dir = 'woosbymdz-rtl';
                break;
            default:
                $swiper_fraction_dir = '';
                break;
        }
        switch($all_settings['swiper_navs']){//set pags margin(translateY)
            case 'bullets':
                $pags_style_margin = 'transform: translateX(-50%) translateY('.$all_settings['pags_style_margin'].'px)';
                break;
            default:
                $pags_style_margin = 'transform: translateY('.$all_settings['pags_style_margin'].'px)';
                break;
        }
        switch($all_settings['swiper_type']){
            case 'coverflow':
                break;
            default:
                $pags_style_margin = '';
                break;
        }
        ?>
        
        <div style="<?php echo esc_attr($pags_style_margin) ?>" class="swiper-pagination <?php echo esc_attr($swiper_fraction_dir) ?>"></div>

        <?php //arrows bg type
        switch($all_settings['arrows_style_leftbg']){
            case 'ease_sharped':
                $arrows_style_leftbg = 'woosbymdz-easesharped-bg';
                break;
            case 'circle':
                $arrows_style_leftbg = 'woosbymdz-circle-bg';
                break;
            case 'leave1':
                $arrows_style_leftbg = 'woosbymdz-leaves1-bg';
                break;
            case 'leave2':
                $arrows_style_leftbg = 'woosbymdz-leaves2-bg';
                break;
            default: 
                $arrows_style_leftbg = '';
                break;
        }
        switch($all_settings['arrows_style_rightbg']){
            case 'ease_sharped':
                $arrows_style_rightbg = 'woosbymdz-easesharped-bg';
                break;
            case 'circle':
                $arrows_style_rightbg = 'woosbymdz-circle-bg';
                break;
            case 'leave1':
                $arrows_style_rightbg = 'woosbymdz-leaves1-bg';
                break;
            case 'leave2':
                $arrows_style_rightbg = 'woosbymdz-leaves2-bg';
                break;
            default: 
                $arrows_style_rightbg = '';
                break;
        }
        switch($all_settings['arrows_style_hover']){
            case 'out_mover':
                $arrows_style_hover_left = 'woosbymdz-outmover-left';
                $arrows_style_hover_right = 'woosbymdz-outmover-right';
                break;
            case 'in_mover':
                $arrows_style_hover_left = 'woosbymdz-inmover-left';
                $arrows_style_hover_right = 'woosbymdz-inmover-right';
                break;
            case 'bigger':
                $arrows_style_hover_left = 'woosbymdz-bigger';
                $arrows_style_hover_right = 'woosbymdz-bigger';
                break;
            case 'shrink':
                $arrows_style_hover_left = 'woosbymdz-shrink';
                $arrows_style_hover_right = 'woosbymdz-shrink';
                break;
            case 'highlight':
                $arrows_style_hover_left = 'woosbymdz-highlight';
                $arrows_style_hover_right = 'woosbymdz-highlight';
                break;
            default:
                $arrows_style_hover_left = '';
                $arrows_style_hover_right = '';
                break;
        }

        if($all_settings['cat'] != '' && $all_settings['cat'] != ' '){
            $str_cats = implode(",", $all_settings['cat']);
        }
        ?>
        <div class="swiper-button-prev <?php echo esc_attr($arrow_prev_class); ?> <?php echo esc_attr($arrows_style_rightbg); ?> <?php echo esc_attr($arrows_style_hover_right); ?>"></div>
        <div class="swiper-button-next <?php echo esc_attr($arrow_next_class); ?> <?php echo esc_attr($arrows_style_leftbg); ?> <?php echo esc_attr($arrows_style_hover_left); ?>" <?php if($all_settings['adv_infinital'] == "yes" && ($all_settings['adv_infinital_mode'] == 'nextarrow' || $all_settings['adv_infinital_mode'] == 'both')){echo 'onclick="woosbymdz_infital_next(event, this)" statoost="ready" inload-txt="'.$all_settings["adv_infinital_txt"].'" pro-src="'.$all_settings['src'].'" fetch="'.$all_settings['products_fetch'].'" cats="'.$str_cats.'" stock="'.$all_settings['only_instock'].'" exc="'.$all_settings['products_exclude'].'" num="'.$number_args.'" numload="'.$all_settings['adv_infinital_num'].'" method="'.$all_settings['adv_infinital_mode'].'"';} ?>></div>

        </div><!--End of swiper outer-->
        </div><!--End of Main-Con-->


        <?php //Final out putting

        $content = ob_get_contents();
        ob_end_clean();

        echo $content;
        
        }else {

            ?>
            </div><!--End of swiper outer-->
            </div><!--End of Main-Con-->
            <?php

        }//End of if Woo Check

	}/*End of func*/

    //Check Woo Plugin
    private function check_woo() 
    {
        $all_plugins = apply_filters('active_plugins', get_option('active_plugins'));

        if (stripos(implode($all_plugins), 'woocommerce.php')) {
            return true;
        } else return false;

    }
    //Check Yoast-Seo Plugin
    private function check_yoast() 
    {
        $all_plugins = apply_filters('active_plugins', get_option('active_plugins'));

        if (stripos(implode($all_plugins), 'wp-seo.php')) {
            return true;
        } else return false;

    }

    //Get yoast main cat
    private function yoast_get_pro_maincat()
    {

        //check is yoast active ?
        $is_yoast = $this->check_yoast();
        if($is_yoast){

            //Check is Product-Page ?
            $products_permalink = get_option('woosbymdz_yoast_maincat_permalink');
            if($products_permalink == '' || $products_permalink === NULL || $products_permalink == 'null'){
                $products_permalink = 'product';
            }

            $home_url = home_url().'/';
            $current_url = home_url($_SERVER['REQUEST_URI']);
	        $current_url = explode($home_url, $current_url);
	        $current_url2 = explode('/', $current_url[1]);

            //Check product-page with Default woo-functions
            $is_product_page = 'no';
            if(function_exists('is_product')){
                if(is_product()){
                    $is_product_page = 'yes';
                }
            }
            /***/

            if($current_url2[0] == $products_permalink || $is_product_page=='yes'){//is pro page (True)

                global $product;

	            $id = $product->id;
	            $yoast_main_cat = yoast_get_primary_term_id( 'product_cat', $id );

                return $yoast_main_cat.'|'.$id;


            }else return '';


        }else return '';

    }

    //Get related Products
    private function get_related_pros($methode ,$num, $exc_ids)
    {

        //Check is Product-Page ?
        $products_permalink = get_option('woosbymdz_yoast_maincat_permalink');
        if($products_permalink == '' || $products_permalink === NULL || $products_permalink == 'null'){
            $products_permalink = 'product';
        }

        $home_url = home_url().'/';
        $current_url = home_url($_SERVER['REQUEST_URI']);
	    $current_url = explode($home_url, $current_url);
	    $current_url2 = explode('/', $current_url[1]);

        
        if($current_url2[0] == $products_permalink){//is pro page (True)

            global $product;
	        $id = $product->id;

            $pro_obj = new WC_Product($id);
            
            switch($methode){
                case '1':
                    $related_pros = $pro_obj->get_upsell_ids();
                    break;
                case '2':
                    $related_pros = $pro_obj->get_cross_sell_ids();
                    break;
                case '3':
                    $related_pros = wc_get_related_products( $id, $num, $exc_ids );
                    break;
            }
            return $related_pros;

        }else return '';

    }

    //Custom Card Pros
    public function render_pro_card_temp($template=1, $pid='none', $blank_links='no', $ext_opts=[])
    {


        if($pid != 'none'){

            $product = wc_get_product($pid);

            $p_type = $product->get_type();
            $p_name = $product->get_name();

            $p_price = $product->get_price();
            $formatted_price = $this->format_price($p_price);

            $p_reg_price = $product->get_regular_price();
            $formatted_reg_price = $this->format_price($p_reg_price);

            $p_sale_price = $product->get_sale_price();
            $formatted_sale_price = $this->format_price($p_sale_price);

            $p_total_sales = $product->get_total_sales();
            $p_total_sales = intval($p_total_sales);
            $p_stock_status = $product->get_stock_status();

            $woo_currency_sign = get_option( 'woocommerce_currency' );
            if($ext_opts['translate_woo_curr'] == 1 || $ext_opts['translate_woo_curr'] == 'yes' || $ext_opts['translate_woo_curr'] == 'on'){

                //$woo_currency_sign = __($woo_currency_sign, 'woocommerce');
                //$woo_currency_sign = get_woocommerce_currency();

                $woo_currency_sign = get_woocommerce_currency_symbol();
                /*if(defined('WC_PLUGIN_FILE')){
                    $locale_info         = include WC()->plugin_path() . '/i18n/locale-info.php';
                    $locale = get_locale();
                    if(strpos($locale, '_') !== false){
                        $locale_arr = explode('_', $locale);
                        if(isset($locale_arr[1])){
                            $locale = $locale_arr[1];
                        }
                    }

                    $locale_info = $locale_info[$locale];
                    $locale_curr_name = $locale_info['singular'];
                    $woo_currency_sign = __($locale_curr_name, 'woocommerce');
                }*/
                

                //var_dump($woo_currency_sign);
            }

            $cards_vertical_align = 'default';
            switch($template){
                case 'temp1':
                case 'temp3':
                    $cards_vertical_align = $ext_opts['vert_align_temp1'];
                    break;
                case 'temp2':
                    $cards_vertical_align = $ext_opts['vert_align_temp2'];
                    break;
            }

            $is_on_sale = 0;
            if(!empty($p_sale_price) && $p_sale_price > 0){$is_on_sale=1;}

            $product_url = get_permalink( $pid );
            $links_target = '_self';
            if($blank_links == 'yes'){
                $links_target = '_blank';
            }

            $p_img = $product->get_image();


            switch($template){
                case 'temp1':
                case 'temp2':

                    $p_sh_descs = $product->get_short_description();

                    if($p_type == 'variable'){
                        $p_child_vars = $product->get_children(); // get variations
                        $p_childs_count = count($p_child_vars);
                    }

                    
                    //HTMLs
                    ?>
                    <div class="woosbymdz-custom-pro-card" template="<?php echo esc_attr($template); ?>" product-type="<?php echo esc_attr($p_type); ?>" in-stock="<?php echo esc_attr($p_stock_status); ?>" is-sale="<?php echo esc_attr($is_on_sale); ?>" pro-price="<?php echo esc_attr($p_price); ?>">

                        <a href="<?php echo esc_url($product_url); ?>" target="<?php echo $links_target; ?>">

                            <div class="woosbymdz-custom-pro-inner" vart-align="<?php echo esc_attr($cards_vertical_align); ?>">

                                <div class="pro-main-image-con">
                                    <?php echo $p_img; ?>
                                </div>

                                <div class="woosbymdz-contents-div woosbymdz-contents-div-1">
                                    <div class="pro-main-title-con">
                                        <h4 class="pro-main-title"><?php echo esc_attr($p_name) ?></h4>
                                    </div>

                                    <?php if(!empty($p_price)):?>
                                    <div class="pro-main-price-con">
                                        <?php $this->render_inner_price($is_on_sale, $formatted_price, $formatted_sale_price, $p_reg_price, $formatted_reg_price, $woo_currency_sign); ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if($p_total_sales > 0):?>
                                    <div class="pro-main-totalsales-con">
                                        <span><?php echo esc_html__('Number of sales:', 'wooslider-bymdez'); ?></span>
                                        <span><?php echo esc_html($p_total_sales); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="woosbymdz-contents-div woosbymdz-contents-div-2">
                                    <?php if(!empty($p_sh_descs)): ?>
                                    <div class="pro-main-shdescs-con">
                                        <div class="inner-sh-descs">
                                            <?php echo $p_sh_descs; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <div class="pro-main-btns-con">
                                        <button><?php echo esc_html__('View Product', 'wooslider-bymdez'); ?></button>
                                    </div>
                                </div>


                                <?php if($p_type == 'variable'): ?>
                                    <div class="pro-main-varscount-con">
                                        <span><?php echo (sprintf( esc_html__( 'In %d different types', 'wooslider-bymdez' ), $p_childs_count )); ?></span>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </a>
                    </div>
                    <?php
                    
                    break;

                case 'temp3':

                    if($p_type == 'variable'){
                        $p_child_vars = $product->get_children(); // get variations (ids)
                        $p_childs_count = count($p_child_vars);
                    }

                    $child_pro_urls = [];
                    $childs_url_type = '_self';
                    if($blank_links == 'yes'){
                        $childs_url_type = '_blank';
                    }

                    $is_rem_orgpro_name = $ext_opts['rem_proname_from_childs_temp3'];
                    //var_dump($is_rem_orgpro_name);
                    foreach($p_child_vars as $child_id){
                        //$child_pro = wc_get_product($child_id);
                        $child_url = get_permalink( $child_id );
                        $child_name = get_post_field('post_name', $child_id);
                        $child_name = urldecode($child_name);

                        if($is_rem_orgpro_name == 'yes'){

                            $child_name = str_replace('-', ' ', $child_name);

                            if(strpos($child_name, $p_name) !== false){
                                $child_name = str_replace($p_name, '', $child_name);
                            }
                        }
                        

                        $child_pro_urls[] = [$child_name, $child_url];

                    }

                    $terms = get_the_terms( $pid, 'product_cat' );
                    $terms_link_type = '_self';
                    if($blank_links == 'yes'){
                        $terms_link_type = '_blank';
                    }

                    $offer_perc = 0;
                    if($is_on_sale == 1){

                        $sale_price_numb = intval($p_sale_price);
                        $p_reg_price_numb = intval($p_reg_price);
                        $offer_perc = (($sale_price_numb*100)/$p_reg_price_numb)-100;
                        $offer_perc = intval($offer_perc);
                    }
                    //var_dump($terms);

                    $img_src = get_the_post_thumbnail_url($pid ,'post-thumbnail');

                    //var_dump($child_pro_urls);

                    ?>
                    <div class="woosbymdz-custom-pro-card" template="<?php echo esc_attr($template); ?>" product-type="<?php echo esc_attr($p_type); ?>" in-stock="<?php echo esc_attr($p_stock_status); ?>" is-sale="<?php echo esc_attr($is_on_sale); ?>" pro-price="<?php echo esc_attr($p_price); ?>">

                        <a href="<?php echo esc_url($product_url); ?>" target="<?php echo $links_target; ?>">

                            <div class="woosbymdz-custom-pro-inner" vart-align="<?php echo esc_attr($cards_vertical_align); ?>">

                                <div class="card-3-img-wrapper" style="background-image: url(<?php echo esc_url($img_src); ?>)"></div>


                                <div class="card-3-cats-wrapper">
                                    <small><?php echo esc_html__('Categories:', 'wooslider-bymdez') ?></small>
                                    <div>
                                    <?php
                                    foreach($terms as $term){
                                        $term_link = get_term_link($term);
                                        ?>
                                        <a href="<?php echo esc_url($term_link); ?>" target="<?php echo esc_attr($terms_link_type); ?>">
                                        <div class="card3-single-cat-itm" cat-id="<?php echo esc_attr($term->term_id); ?>">
                                            <span><?php echo esc_html($term->name); ?></span>
                                        </div>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                    </div>
                                </div>

                                <div class="card-3-price-title-con">
                                    <h4 class="pro-main-title"><?php echo esc_html($p_name); ?></h4>

                                    <div class="card-3-inner-price-con">
                                        <?php $this->render_inner_price($is_on_sale, $formatted_price, $formatted_sale_price, $p_reg_price, $formatted_reg_price, $woo_currency_sign); ?>
                                    </div>

                                </div>

                                <?php if($p_type == 'variable'): ?>
                                <div class="card-3-childs-btns-wrapper">
                                    <small><?php echo esc_html__('Variations:', 'wooslider-bymdez') ?></small>
                                    <div>
                                        <?php foreach($child_pro_urls as $child_data){ ?>
                                            <a href="<?php echo esc_url($child_data[1]) ?>" target="<?php echo esc_attr($childs_url_type); ?>">
                                                <div>
                                                    <span><?php echo esc_html($child_data[0]) ?></span>
                                                </div>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="card-3-btn-wrapper">
                                    <?php if($p_type == 'variable'): ?>
                                        <button><?php echo esc_html__('View Main Product', 'wooslider-bymdez') ?></button>
                                    <?php endif; ?>

                                    <?php if($p_type == 'simple'): ?>
                                        <button><?php echo esc_html__('View Product', 'wooslider-bymdez') ?></button>
                                    <?php endif; ?>
                                </div>

                                <?php if($is_on_sale == 1): ?>
                                    <div class="card-3-sale-badge-perc">
                                        <span><?php echo esc_html($offer_perc); ?></span>
                                        <span>%</span>
                                    </div>
                                <?php endif; ?>

                                

                            </div>

                            
                        </a>

                    </div>
                    <?php


                    break;
            }
        }
    }

    private function render_inner_price($is_on_sale, $formatted_price, $formatted_sale_price, $p_reg_price, $formatted_reg_price, $woo_currency_sign)
    {

        if($is_on_sale == 0): ?>
            <bdi class="woosbymdz-bdi">
                <span class="spn-price spn-active-price"><?php echo esc_html($formatted_price); ?></span>
                <span class="spn-currency"><?php echo $woo_currency_sign; ?></span>
            </bdi>
        <?php endif; ?>

        <?php if($is_on_sale == 1): ?>
            <ins>
                <bdi class="woosbymdz-bdi">
                    <span class="spn-price spn-sale-price spn-active-price"><?php echo esc_html($formatted_sale_price); ?></span>
                    <span class="spn-currency"><?php echo $woo_currency_sign; ?></span>
                </bdi>
            </ins>

            <?php if(!empty($p_reg_price)): ?>
            <del>
                <bdi class="woosbymdz-bdi">
                    <span class="spn-price spn-normal-price"><?php echo esc_html($formatted_reg_price); ?></span>
                    <span class="spn-currency"><?php echo $woo_currency_sign; ?></span>
                </bdi>
            </del>
            <?php endif; ?>
        
        <?php endif;
    }
    /***/

    //Miscs
    private function format_price($price)
    {
        $final_price = strval($price);
        $final_price = str_replace('.', ',', $final_price);
            
        $final_price = str_split($final_price);
        $len = count($final_price);

        $dots_number = abs(floor(($len-1) / 3));
        $dots_number = intval($dots_number);
        $out_arr = [];

        if ($dots_number > 0) {

            $indexer = 0;
            for ($i = $len-1; $i >= 0; $i--) {

                if($len%3 != 0){

                    if($indexer%3 == 0 && $dots_number > 0 && $i != $len-1){
                        array_push($out_arr, ".");
                        $dots_number--;
                    }

                    array_push($out_arr, $final_price[$i]);

                }else {

                    if($indexer%3 == 0 && $dots_number > 0 && $indexer != 0){
                        array_push($out_arr, ".");
                        $dots_number--;
                    }

                    array_push($out_arr, $final_price[$i]);

                }  

                $indexer++;
            }

        $out_arr = array_reverse($out_arr);

        } else {
            $out_arr = $final_price;
        }

        $final_price = implode("", $out_arr);
        return $final_price;
    }

}/*End of class*/

}//close the php tag to reduce the blank spaces ?>