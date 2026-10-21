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
/*Get Themes Configs*/
include_once WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'themes-conf.php';

/*Set Dynamic CSS and JS path*/
$dynamic_css_file = WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'frontcss.css';//Set dynamic css file
$dynamic_js_file = WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'frontjs.js';//Set dynamic js file

/*Save Settings*/
if (isset($_POST['submit']))
{

    if (isset($_POST['woosbymdz-hddn-orgtheme-name']))
    {
        $org_theme_name = sanitize_text_field($_POST['woosbymdz-hddn-orgtheme-name']); 
        update_option('woosbymdz_orgtheme_name', $org_theme_name, false);
    }

    if (isset($_POST['woosbymdz-loading-time']))
    {
        $sliders_loadtime = sanitize_text_field($_POST['woosbymdz-loading-time']); 
        $sliders_loadtime = intval($sliders_loadtime); //must be a number
        update_option('woosbymdz_loadingtime', $sliders_loadtime, false);
    }
    
    if (isset($_POST['woosbymdz-loading-effect']))
    {
        $sliders_loadeffect = sanitize_text_field($_POST['woosbymdz-loading-effect']);
        update_option('woosbymdz_loadingeffect', $sliders_loadeffect, false);
    }

    if (isset($_POST['woosbymdz-ppage-customload-img']))
    {
        $sliders_loadeffect_img = sanitize_text_field($_POST['woosbymdz-ppage-customload-img']);
        update_option('woosbymdz_loadingeffect_img', $sliders_loadeffect_img, false);
    }

    if (isset($_POST['woosbymdz-ppage-customload-imgw']))
    {
        $sliders_loadeffect_imgw = sanitize_text_field($_POST['woosbymdz-ppage-customload-imgw']);
        update_option('woosbymdz_loadingeffect_imgw', $sliders_loadeffect_imgw, false);
    }

    if (isset($_POST['woosbymdz-ppage-customload-imgh']))
    {
        $sliders_loadeffect_imgh = sanitize_text_field($_POST['woosbymdz-ppage-customload-imgh']);
        update_option('woosbymdz_loadingeffect_imgh', $sliders_loadeffect_imgh, false);
    }

    if (isset($_POST['woosbymdz-ppage-customload-imgb']))
    {
        $sliders_loadeffect_imgb = sanitize_text_field($_POST['woosbymdz-ppage-customload-imgb']);
        update_option('woosbymdz_loadingeffect_imgb', $sliders_loadeffect_imgb, false);
    }


    if (isset($_POST['woosbymdz-mob2-breakpoint']))
    {
        $mob2_breakpoint = sanitize_text_field($_POST['woosbymdz-mob2-breakpoint']); 
        $mob2_breakpoint = intval($mob2_breakpoint); //must be a number
        update_option('woosbymdz_mob2breakpoint', $mob2_breakpoint, false);
    }

    if (isset($_POST['woosbymdz-wide-breakpoint']))
    {
        $wide_breakpoint = sanitize_text_field($_POST['woosbymdz-wide-breakpoint']); 
        $wide_breakpoint = intval($wide_breakpoint); //must be a number
        update_option('woosbymdz_widebreakpoint', $wide_breakpoint, false);
    }
    

    if (isset($_POST['woosbymdz-customcss-active']))
    {
        $is_extracss = $this->sanitize_checkbox($_POST['woosbymdz-customcss-active']) === true ? '1' : '0';
        update_option('woosbymdz_iscustomcss', $is_extracss, false);
    }else update_option('woosbymdz_iscustomcss', '0', false); 

    if (isset($_POST['woosbymdz-csscode']))
    {
        $css_extracodes = sanitize_textarea_field($_POST['woosbymdz-csscode']);
        $css_extracodes = wp_unslash($css_extracodes);
        update_option('woosbymdz_customcss', $css_extracodes, false);
    }

    if (isset($_POST['woosbymdz-customjs-active']))
    {
        $is_extrajs = $this->sanitize_checkbox($_POST['woosbymdz-customjs-active']) === true ? '1' : '0';
        update_option('woosbymdz_iscustomjs', $is_extrajs, false);
    }else update_option('woosbymdz_iscustomjs', '0', false); 

    if (isset($_POST['woosbymdz-jscode']))
    {
        $js_extracodes = $_POST['woosbymdz-jscode'];
        $js_extracodes = wp_unslash($js_extracodes);
        update_option('woosbymdz_customjs', $js_extracodes, false);
    }
    
    if (isset($_POST['woosbymdz-products-nobg']))
    {
        $products_nobg = $this->sanitize_checkbox($_POST['woosbymdz-products-nobg']) === true ? '1' : '0';
        update_option('woosbymdz_productsnobg', $products_nobg, false);
    }else update_option('woosbymdz_productsnobg', '0', false); 

    if (isset($_POST['woosbymdz-oldthemedebuger']))
    {
        $old_themes_debuger = $this->sanitize_checkbox($_POST['woosbymdz-oldthemedebuger']) === true ? '1' : '0';
        update_option('woosbymdz_oldthemesdebuger', $old_themes_debuger, false);
    }else update_option('woosbymdz_oldthemesdebuger', '0', false);
    
    if (isset($_POST['woosbymdz-globalimport']))
    {
        $global_import = $this->sanitize_checkbox($_POST['woosbymdz-globalimport']) === true ? '1' : '0';
        update_option('woosbymdz_globalimport', $global_import, false);
    }else update_option('woosbymdz_globalimport', '0', false);

    if (isset($_POST['woosbymdz-importjq']))
    {
        $import_jq = $this->sanitize_checkbox($_POST['woosbymdz-importjq']) === true ? '1' : '0';
        update_option('woosbymdz_importjq', $import_jq, false);
    }else update_option('woosbymdz_importjq', '0', false); 

    if (isset($_POST['woosbymdz-donimport-swiper']))
    {
        $dont_import_swiper = $this->sanitize_checkbox($_POST['woosbymdz-donimport-swiper']) === true ? '1' : '0';
        update_option('woosbymdz_donimport_swiper', $dont_import_swiper, false);
    }else update_option('woosbymdz_donimport_swiper', '0', false); 

    if (isset($_POST['woosbymdz-productstracker']))
    {
        $last_seen_cookie = $this->sanitize_checkbox($_POST['woosbymdz-productstracker']) === true ? '1' : '0';
        update_option('woosbymdz_last_seen_cookie', $last_seen_cookie, false);
    }else update_option('woosbymdz_last_seen_cookie', '0', false); 

    if (isset($_POST['woosbymdz-productstracker-js']))
    {
        $last_seen_cookie_js = $this->sanitize_checkbox($_POST['woosbymdz-productstracker-js']) === true ? '1' : '0';
        update_option('woosbymdz_productstracker_js', $last_seen_cookie_js, false);
    }else update_option('woosbymdz_productstracker_js', '0', false); 

    if (isset($_POST['woosbymdz-nobrowsercache']))
    {
        $is_browsernocache = $this->sanitize_checkbox($_POST['woosbymdz-nobrowsercache']) === true ? '1' : '0';
        update_option('woosbymdz_isbrowsernocache', $is_browsernocache, false);
    }else update_option('woosbymdz_isbrowsernocache', '0', false); 
     
    //Short codes
    if (isset($_POST['woosbymdz-shortcode1']))
    {
        $shortcode1 = $this->sanitize_checkbox($_POST['woosbymdz-shortcode1']) === true ? '1' : '0';
        update_option('woosbymdz_shortcode1', $shortcode1, false);
    }else update_option('woosbymdz_shortcode1', '0', false); 

    if (isset($_POST['woosbymdz-shortcode2']))
    {
        $shortcode2 = $this->sanitize_checkbox($_POST['woosbymdz-shortcode2']) === true ? '1' : '0';
        update_option('woosbymdz_shortcode2', $shortcode2, false);
    }else update_option('woosbymdz_shortcode2', '0', false); 

    //Yoast Main-Cat
    if (isset($_POST['woosbymdz-yoast-propermalink']))
    {
        $yoast_maincat_permalink = sanitize_text_field($_POST['woosbymdz-yoast-propermalink']); 
        update_option('woosbymdz_yoast_maincat_permalink', $yoast_maincat_permalink, false);
    }

    if (isset($_POST['woosbymdz-addtoproductpage']))
    {
        $add_yoast_slider_to_productspage= $this->sanitize_checkbox($_POST['woosbymdz-addtoproductpage']) === true ? '1' : '0';
        update_option('woosbymdz_yoast_maincat_addslider', $add_yoast_slider_to_productspage, false);
    }else update_option('woosbymdz_yoast_maincat_addslider', '0', false); 

    if (isset($_POST['woosbymdz-iscustom-shortcode']))
    {
        $add_yoast_slider_is_customized_shortcode= $this->sanitize_checkbox($_POST['woosbymdz-iscustom-shortcode']) === true ? '1' : '0';
        update_option('woosbymdz_yoast_maincat_iscustomizedshortcode', $add_yoast_slider_is_customized_shortcode, false);
    }else update_option('woosbymdz_yoast_maincat_iscustomizedshortcode', '0', false); 

    if (isset($_POST['woosbymdz-custom-shortcode']))
    {
        $customized_shortcode = sanitize_textarea_field($_POST['woosbymdz-custom-shortcode']);
        $customized_shortcode = wp_unslash($customized_shortcode);
        update_option('woosbymdz_yoast_maincat_customizedshortcode', $customized_shortcode, false);
    }

    if (isset($_POST['woosbymdz-ppage-slidertitle']))
    {
        $yoast_slider_title = $_POST['woosbymdz-ppage-slidertitle']; 
        $yoast_slider_title = wp_unslash($yoast_slider_title);
        update_option('woosbymdz_ppage_slidertitle', $yoast_slider_title, false);
    }

    //Extra Widgets
    if (isset($_POST['woosbymdz-extrawidget1']))
    {
        $extra_widget1 = $this->sanitize_checkbox($_POST['woosbymdz-extrawidget1']) === true ? '1' : '0';
        update_option('woosbymdz_extrawidget1', $extra_widget1, false);
    }else update_option('woosbymdz_extrawidget1', '0', false); 

    //Disable main Widget
    if (isset($_POST['woosbymdz-nomainwidget']))
    {
        $nomain_widget = $this->sanitize_checkbox($_POST['woosbymdz-nomainwidget']) === true ? '1' : '0';
        update_option('woosbymdz_nomainwidget', $nomain_widget, false);
    }else update_option('woosbymdz_nomainwidget', '0', false); 

    //Add Widgets-Category
    if (isset($_POST['woosbymdz-addnewwidgets-category']))
    {
        $widgets_category = $this->sanitize_checkbox($_POST['woosbymdz-addnewwidgets-category']) === true ? '1' : '0';
        update_option('woosbymdz_addnewwidgets_category', $widgets_category, false);
    }else update_option('woosbymdz_addnewwidgets_category', '0', false); 

    //Ajax reload page compatibility
    if (isset($_POST['woosbymdz-ajaxreload-comp']))
    {
        $reload_ajax_comp = $this->sanitize_checkbox($_POST['woosbymdz-ajaxreload-comp']) === true ? '1' : '0';
        update_option('woosbymdz_reloadajaxcomp', $reload_ajax_comp, false);
    }else update_option('woosbymdz_reloadajaxcomp', '0', false); 

    if (isset($_POST['woosbymdz-ajaxjs-active']))
    {
        $is_ajaxjs = $this->sanitize_checkbox($_POST['woosbymdz-ajaxjs-active']) === true ? '1' : '0';
        update_option('woosbymdz_isajaxjs', $is_ajaxjs, false);
    }else update_option('woosbymdz_isajaxjs', '0', false); 

    if (isset($_POST['woosbymdz-ajaxjscode']))
    {
        $js_ajax_codes = $_POST['woosbymdz-ajaxjscode'];
        $js_ajax_codes = wp_unslash($js_ajax_codes);
        update_option('woosbymdz_ajaxjs', $js_ajax_codes, false);
    }
    //Charged System
    if (isset($_POST['woosbymdz-charged-pros']))
    {
        $use_charged = $this->sanitize_checkbox($_POST['woosbymdz-charged-pros']) === true ? '1' : '0';
        update_option('woosbymdz_charged_pros', $use_charged, false);

        require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR."admin/charged-pros-meta.php";

    }else update_option('woosbymdz_charged_pros', '0', false); 

    if(isset($_POST['woosbymdz-charged-chek-reset']))
    {
        delete_option('woosbymdz_charged_pros_everrunned');
    }

    if (isset($_POST['woosbymdz-charged-cancdisc']))
    {
        $charged_cancel_disc = $this->sanitize_checkbox($_POST['woosbymdz-charged-cancdisc']) === true ? '1' : '0';
        update_option('woosbymdz_charged_cancdisc', $charged_cancel_disc, false);
    }else update_option('woosbymdz_charged_cancdisc', '0', false); 


    if (isset($_POST['woosbymdz-charged-isaddcat']))
    {
        $charged_is_addcat = $this->sanitize_checkbox($_POST['woosbymdz-charged-isaddcat']) === true ? '1' : '0';
        update_option('woosbymdz_charged_isaddcat', $charged_is_addcat, false);
    }else update_option('woosbymdz_charged_isaddcat', '0', false); 

    if (isset($_POST['woosbymdz-charged-addcat']))
    {
        $charged_addcat = sanitize_text_field($_POST['woosbymdz-charged-addcat']); 
        $charged_addcat = intval($charged_addcat); //must be a number
        update_option('woosbymdz_charged_addcat', $charged_addcat, false);
    }

    if (isset($_POST['woosbymdz-charged-donshowbtn']))
    {
        $charged_don_btn = $this->sanitize_checkbox($_POST['woosbymdz-charged-donshowbtn']) === true ? '1' : '0';
        update_option('woosbymdz_charged_donshowbtn', $charged_don_btn, false);
    }else update_option('woosbymdz_charged_donshowbtn', '0', false);

    if (isset($_POST['woosbymdz-charged-btnpos']))
    {
        $charged_btn_pos = sanitize_text_field($_POST['woosbymdz-charged-btnpos']);
        update_option('woosbymdz_charged_btnpos', $charged_btn_pos, false);
    }
    /***/

    if (isset($_POST['woosbymdz-sliders-noeffect']))
    {
        $swiper_noeff = $this->sanitize_checkbox($_POST['woosbymdz-sliders-noeffect']) === true ? '1' : '0';
        update_option('woosbymdz_swipernoeff', $swiper_noeff, false);
    }else update_option('woosbymdz_swipernoeff', '0', false); 

    if (isset($_POST['woosbymdz-allcss-impo']))
    {
        $allcss_impo = $this->sanitize_checkbox($_POST['woosbymdz-allcss-impo']) === true ? '1' : '0';
        update_option('woosbymdz_elem_allcss_impo', $allcss_impo, false);
    }else update_option('woosbymdz_elem_allcss_impo', '0', false); 

    if (isset($_POST['woosbymdz-procss-impo']))
    {
        $procss_impo = $this->sanitize_checkbox($_POST['woosbymdz-procss-impo']) === true ? '1' : '0';
        update_option('woosbymdz_elem_procss_impo', $procss_impo, false);
    }else update_option('woosbymdz_elem_procss_impo', '0', false); 

    if (isset($_POST['woosbymdz-res-titles']))
    {
        $slider_res_title = $this->sanitize_checkbox($_POST['woosbymdz-res-titles']) === true ? '1' : '0';
        update_option('woosbymdz_elem_slider_restitle', $slider_res_title, false);
    }else update_option('woosbymdz_elem_slider_restitle', '0', false); 

    if (isset($_POST['woosbymdz-res-titles-max-lines']))
    {
        $pros_restitle_maxlines = sanitize_text_field($_POST['woosbymdz-res-titles-max-lines']); 
        $pros_restitle_maxlines = intval($pros_restitle_maxlines); //must be a number
        update_option('woosbymdz_restitle_maxlines', $pros_restitle_maxlines, false);
    }

    if (isset($_POST['woosbymdz-noajaxrefchecker']))
    {
        $noajax_ref_checker = $this->sanitize_checkbox($_POST['woosbymdz-noajaxrefchecker']) === true ? '1' : '0';
        update_option('woosbymdz_noajaxrefchecker', $noajax_ref_checker, false);
    }else update_option('woosbymdz_noajaxrefchecker', '0', false);

    if (isset($_POST['woosbymdz-use-custtemp-procards']))
    {
        $use_custom_procards = $this->sanitize_checkbox($_POST['woosbymdz-use-custtemp-procards']) === true ? '1' : '0';
        update_option('woosbymdz_use_custtemp_procards', $use_custom_procards, false);
    }else update_option('woosbymdz_use_custtemp_procards', '0', false);

    

    /*Widget Loop-Builder*/
    if (isset($_POST['woosbymdz-extrawidget2']))
    {
        $extra_widget2 = $this->sanitize_checkbox($_POST['woosbymdz-extrawidget2']) === true ? '1' : '0';
        update_option('woosbymdz_extrawidget2', $extra_widget2, false);

        /*$has_php_file_edited = get_option('woosbymdz_elmpro_hasfile_edited');
        if(!$has_php_file_edited || $has_php_file_edited != 1){*/
            $this->edit_phpfile_elem_pro();
        /*}*/

    }else update_option('woosbymdz_extrawidget2', '0', false); 
    /***/

    if (isset($_POST['woosbymdz-elem-dis-dynatags']))
    {
        $dis_dyna_tags = $this->sanitize_checkbox($_POST['woosbymdz-elem-dis-dynatags']) === true ? '1' : '0';
        update_option('woosbymdz_elem_dis_dynatags', $dis_dyna_tags, false);
    }else update_option('woosbymdz_elem_dis_dynatags', '0', false); 

    update_option('woosbymdz_plugin_version', WOOSBYMDZ_VER );//Define DB Version

    /*Save CSS & JS Contents inside the DB*/
    $main_css = file_get_contents($dynamic_css_file);
    $main_js = file_get_contents($dynamic_js_file);
    update_option('woosbymdz_css_file', $main_css, false);
    update_option('woosbymdz_js_file', $main_js, false);

    //Save elementor-pro backup-module.php.txt file inside the DB
    $modules_backup_txt = WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'file-changes'.DIRECTORY_SEPARATOR.'module-php-backup.txt';
    if(is_file($modules_backup_txt)){
        $tmp_conts = file_get_contents($modules_backup_txt);
        if(!empty($modules_backup_txt)){
            update_option('woosbymdz_elempro_modules_backup_file', $tmp_conts, false);
        }
    }
    /***/

    //Set active theme inside db
    update_option('woosbymdz_active_theme', $current_theme, false);

    //No-cache advanced configs
    if(get_option('woosbymdz_isbrowsernocache') == '1')
    {
    
        $woosbymdz_tmp_ver = get_option('woosbymdz_tmpversion');
        if(!$woosbymdz_tmp_ver){
            update_option('woosbymdz_tmpversion', '0.0.0.1');
        }else {
            $new_ver = $this->version_increaser($woosbymdz_tmp_ver);
            update_option('woosbymdz_tmpversion', $new_ver);
        }
    }
    /**/
 
}
/**/

/*Night Mode*/
$is_nightmode = get_option('woosbymdz_nightmode');
/**/

/*Modern Theme*/
$is_modern_theme = get_option('woosbymdz_is_modertheme');
/**/

echo '<script> console.log("Theme: '.$current_theme.'"); </script>'; //Test theme name

$is_theme_supported = true;
/**/

/*Create css based on active theme*/
$nomain_widget = get_option('woosbymdz_nomainwidget');//check the activity of main widget


if($nomain_widget == '0') 
{//Css Static file root
    $static_css_file = WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'front-static.css';
}else $static_css_file = '';


if($nomain_widget == '0')
{//ADD Theme CSS Basics

$css_code = $theme_conf[$current_theme][0];//add the theme Css codes
$css_code .= '
.woosbymdz-carousel-con .swiper-wrapper[no-products-title="yes"] '.$theme_conf[$current_theme][3].'{
    display: none;
    opacity: 0;
    padding: 0px;
    margin: 0px;
    width: 0px;
    height: 0px;
}
.woosbymdz-carousel-con .swiper-wrapper[no-products-cats="yes"] '.$theme_conf[$current_theme][4].'{
    display: none;
}
.woosbymdz-carousel-con .swiper-wrapper[no-products-price="yes"] '.$theme_conf[$current_theme][5].'{
    display: none;
}
.woosbymdz-carousel-con .swiper-wrapper[no-products-labels="yes"] '.$theme_conf[$current_theme][8].'{
    display: none;
}
.woosbymdz-carousel-con .swiper-wrapper[no-products-addbtn="yes"] '.$theme_conf[$current_theme][10].'{
    display: none;
}
.woosbymdz-carousel-con .swiper-wrapper[products-label-abs="yes"] '.$theme_conf[$current_theme][8].'{
    position: absolute;
}

';

}else $css_code = '';


if ($theme_conf[$current_theme][1] != '')
{
    $theme_icon = $theme_conf[$current_theme][1];
}else {
    $theme_icon = 'nothing-found.svg';
    $is_theme_supported = false;
}

if ($theme_conf[$current_theme][2] != '')
{
    $theme_icon_dims = $theme_conf[$current_theme][2];
}else $theme_icon_dims = '55px';
/**/

/*set sliders loading effect*/
$sliders_loadeffect = get_option('woosbymdz_loadingeffect');
switch ($sliders_loadeffect)
{

    case 'effect-1':
        $css_code_loading = '
        .woosbymdz-loading:before {content: ""; position: absolute;width: 17%; height: 172%; top: -60px; left: 0; background: rgb(230,230,230);background: linear-gradient(90deg, rgba(230,230,230,0) 0%, rgba(246,246,246,0.6306723372942927) 50%, rgba(255,255,255,0) 100%);animation: woosbymdz-loader1-anima 1s cubic-bezier(0.25, 0.1, 0.63,-0.08) infinite;transform: rotate(200deg); }
        @keyframes woosbymdz-loader1-anima {
          from {left: -100%;}
          to {left: 100%;}
        }
        .woosbymdz-loading:after {content: ""; position: absolute;width: 100%; height: 100%;background-color: rgba(255, 255, 255, 0.603);top: 0;left: 0;z-index: 99;}
        ';
        break;
    case 'effect-2':
        $css_code_loading ='
        .woosbymdz-loading:before {
            content: ""; 
            position: absolute;
            width: 50%; 
            height: 100%;
            background-color: #2b4096;
            top: 0;left: 0;
            z-index: 99;
            animation: woosbymdz-loader2-anima1 2s ease infinite;
        }
        @keyframes woosbymdz-loader2-anima1 {
            0% {left: 100%;opacity: 25%;}
            50% {left: -20%;background: #b021ed;opacity: 100%;}
            100% {left: 100%;background: #2b4096;opacity: 55%;}
        }
        .woosbymdz-loading:after {
            content: ""; 
            position: absolute;
            width: 50%; 
            height: 100%;
            background-color: #ed214f;
            top: 0;
            right: 0;
            z-index: 100;
            animation: woosbymdz-loader2-anima2 2s ease infinite;
        }
        @keyframes woosbymdz-loader2-anima2 {
            0% {right: 100%;opacity: 25%;}
            50% {right: -20%;background: #b021ed;opacity: 100%;}
            100% {right: 100%;background: #ed214f;opacity: 55%;}
        }
        ';
        break;
    case 'effect-3':
        $css_code_loading = '
        .woosbymdz-loading:before {
            content: ""; 
            position: absolute;
            width: 50%; 
            height: 100%;
            background-color: #375ef4;
            top: 0;left: 0;
            z-index: 99;
            animation: woosbymdz-loader3-anima1 2s ease infinite;
        }
        @keyframes woosbymdz-loader3-anima1 {
            0% {left: 100%;opacity: 25%;}
            50% {left: -20%;background: #63ed21;opacity: 100%;}
            100% {left: 100%;background: #375ef4;opacity: 55%;}
        }
        .woosbymdz-loading:after {
            content: ""; 
            position: absolute;
            width: 50%; 
            height: 100%;
            background-color: #edd321;
            top: 0;
            right: 0;
            z-index: 100;
            animation: woosbymdz-loader3-anima2 2s ease infinite;
        }
        @keyframes woosbymdz-loader3-anima2 {
            0% {right: 100%;opacity: 25%;}
            50% {right: -20%;background: #63ed21;opacity: 100%;}
            100% {right: 100%;background: #edd321;opacity: 55%;}
        }
        ';
        break;
    case 'effect-4':
        $css_code_loading = '
        .woosbymdz-loading:before {
            content: "";
            position: absolute;
            width: 170%;
            height: 100%;
            top: 0;
            left: 0;
            background: rgb(55,94,244);
            background: linear-gradient(
            90deg, rgba(55,94,244,0) 0%, rgba(51,48,255,1) 25%, rgba(54,239,225,1) 50%, rgba(141,20,255,1) 75%, rgba(163,33,237,0) 100%);
            animation: woosbymdz-loader4-anima 3s cubic-bezier(0.28, 0.98, 0.96, 0.82) infinite;
            z-index: 100;
        }
        @keyframes woosbymdz-loader4-anima {
            0% {left: -200%;}
            50% {left: 200%;}
            100% {left: -200%;}
        }
        .woosbymdz-loading:after {content: ""; position: absolute;width: 100%; height: 100%;background-color: rgba(255, 255, 255, 0.603);top: 0;left: 0;z-index: 99;}
        ';
        break;
    case 'effect-5':
        $css_code_loading = '
        .woosbymdz-loading:before {
            content: "";
            position: absolute;
            width: 26%;
            height: 172%;
            top: -60px;
            left: 0;
            background: rgb(230,230,230);
            background: linear-gradient(
            90deg, rgba(230,230,230,0) 0%, rgba(246,246,246,0.6306723372942927) 50%, rgba(255,255,255,0) 100%);
            animation: woosbymdz-loader5-anima 1.5s cubic-bezier(0.25, 0.1, 0.63,-0.08) infinite;
            z-index: 100;
        }
        @keyframes woosbymdz-loader5-anima {
            from {left: -100%;}
            to {left: 100%;}
        }
        .woosbymdz-loading:after {content: ""; position: absolute;width: 100%; height: 100%;background-color: rgb(66 66 66 / 60%);top: 0;left: 0;z-index: 99;}
        ';
        break;
    case 'effect-6':
        $css_code_loading = '
        .woosbymdz-loading:before {
            content: "";
            position: absolute;
            width: 170%;
            height: 100%;
            top: 0;
            left: 0;
            background: rgb(55,94,244);
            background: linear-gradient(90deg, rgba(55,94,244,0) 0%, rgba(138,35,135,1) 25%, rgba(233,64,87,1) 50%, rgba(242,113,33,1) 75%, rgba(163,33,237,0) 100%); 
            animation: woosbymdz-loader6-anima 3s cubic-bezier(0.28, 0.98, 0.96, 0.82) infinite;
            z-index: 100;
        }
        @keyframes woosbymdz-loader6-anima {
            0% {left: -200%;}
            50% {left: 200%;}
            100% {left: -200%;}
        }
        .woosbymdz-loading:after {content: ""; position: absolute;width: 100%; height: 100%;background-color: rgb(0 20 255 / 28%);top: 0;left: 0;z-index: 99;}
        ';
        break;
    case 'effect-7':
        $css_code_loading = '
        .woosbymdz-loading:before {
            content: "";
            position: absolute;
            width: 26%;
            height: 172%;
            top: -60px;
            left: 0;
            background: rgb(230,230,230);
            background: linear-gradient(
            90deg, rgba(230,230,230,0) 0%, rgba(246,246,246,0.6306723372942927) 50%, rgba(255,255,255,0) 100%);
            animation: woosbymdz-loader7-anima 0.88s cubic-bezier(0.25, 0.1, 0.63,-0.08) infinite;
            z-index: 100;
        }
        @keyframes woosbymdz-loader7-anima {
            from {left: -100%;}
            to {left: 100%;}
        }
        .woosbymdz-loading:after {content: ""; position: absolute;width: 100%; height: 100%;background-color: rgb(230, 230, 230);top: 0;left: 0;z-index: 99;}
        ';
        break;
    case 'effect-8':
        $css_code_loading = '
        .woosbymdz-loading:before {
            content: "";
            position: absolute;
            width: 200px;
            height: 150px;
            top: 50%;
            left: 50%;
            background-image: url('.WOOSBYMDZ_URL."front/img/pre-loaders/Infinity-1s-200px.svg".');
            margin-left: -100px;
            margin-top: -75px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position-x: center;
            z-index: 100;  
        }
        .woosbymdz-loading:after {content: ""; position: absolute;width: 100%; height: 100%;background-color: rgb(201, 201, 201);top: 0;left: 0;z-index: 99;}
        ';
        break;
    case 'effect-9':
        $css_code_loading = '
        .woosbymdz-loading:before {
            content: "";
            position: absolute;
            width: 200px;
            height: 150px;
            top: 50%;
            left: 50%;
            background-image: url('.WOOSBYMDZ_URL."front/img/pre-loaders/Eclipse-0.3s-204px.svg".');
            margin-left: -100px;
            margin-top: -75px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position-x: center;
            z-index: 100;  
        }
        .woosbymdz-loading:after {content: ""; position: absolute;width: 100%; height: 100%;    background-color: rgb(241 217 74 / 49%);top: 0;left: 0;z-index: 99;}
        ';
        break;
    case 'effect-10':
        $css_code_loading = '
        .woosbymdz-loading:before {
            content: "";
            position: absolute;
            width: 200px;
            height: 150px;
            top: 50%;
            left: 50%;
            background-image: url('.WOOSBYMDZ_URL."front/img/pre-loaders/Ripple-0.9s-251px.svg".');
            margin-left: -100px;
            margin-top: -75px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position-x: center;
            z-index: 100;  
        }
        .woosbymdz-loading:after {content: ""; position: absolute;width: 100%; height: 100%;background-color: rgb(0 0 0 / 84%);top: 0;left: 0;z-index: 99;}';
        break;
    case 'effect-11':
        $css_code_loading = '
        .woosbymdz-loading:before {
            content: "";
            position: absolute;
            width: 200px;
            height: 150px;
            top: 50%;
            left: 50%;
            background-image: url('.WOOSBYMDZ_URL."front/img/pre-loaders/Ellipsis-1.6s-211px.svg".');
            margin-left: -100px;
            margin-top: -75px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position-x: center;
            z-index: 100;  
        }
        .woosbymdz-loading:after {content: ""; position: absolute;width: 100%; height: 100%;background-color: rgb(255 212 212 / 80%);top: 0;left: 0;z-index: 99;}
        ';
        break;
    case 'effect-12':
        $css_code_loading = '
        .woosbymdz-loading:before {
            content: "";
            position: absolute;
            width: 200px;
            height: 150px;
            top: 50%;
            left: 50%;
            background-image: url('.WOOSBYMDZ_URL."front/img/pre-loaders/Chunk-4s-200px.svg".');
            margin-left: -100px;
            margin-top: -75px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position-x: center;
            z-index: 100;  
        }
        .woosbymdz-loading:after {content: ""; position: absolute;width: 100%; height: 100%;background-color: rgb(255 255 255 / 76%);top: 0;left: 0;z-index: 99;}
        ';
        break;
    case 'effect-13':
        $custom_loader_url = get_option("woosbymdz_loadingeffect_img");
        $custom_loader_width = get_option("woosbymdz_loadingeffect_imgw");
        $custom_loader_height = get_option("woosbymdz_loadingeffect_imgh");
        $custom_loader_bg = get_option("woosbymdz_loadingeffect_imgb");
        

        if($custom_loader_url == "" || $custom_loader_url == " "){
            $custom_loader_url = WOOSBYMDZ_URL."front/img/pre-loaders/Infinity-1s-200px.svg";
        }

        $custom_loader_top = (intval($custom_loader_height)/2);
        if($custom_loader_width == "" || $custom_loader_width == " "){
            $custom_loader_width = "200";
            $custom_loader_top = "100";
        }

        $custom_loader_left = (intval($custom_loader_width)/2);
        if($custom_loader_height == "" || $custom_loader_height == " "){
            $custom_loader_height = "150";
            $custom_loader_left = "75";
        }

        if($custom_loader_bg == "" || $custom_loader_bg == " "){
            $custom_loader_bg = "#f1f2f3";
        }
        $css_code_loading = '
        .woosbymdz-loading:before {
            content: "";
            position: absolute;
            width: '.$custom_loader_width.'px;
            height: '.$custom_loader_height.'px;
            top: 50%;
            left: 50%;
            background-image: url('.$custom_loader_url.');
            margin-left: -'.$custom_loader_left.'px;
            margin-top: -'.$custom_loader_top.'px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position-x: center;
            z-index: 100;  
        }
        .woosbymdz-loading:after {content: ""; position: absolute;width: 100%; height: 100%;background-color: '.$custom_loader_bg.';top: 0;left: 0;z-index: 99;}
        ';
        break;
    case 'effect-14':
        $css_code_loading = '
        .woosbymdz-loading:before {
            content: "";
            position: absolute;
            width: 200px;
            height: 150px;
            top: 50%;
            left: 50%;
            margin-left: -100px;
            margin-top: -75px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position-x: center;
            z-index: 100; 
            background-image: url('.WOOSBYMDZ_URL."front/img/pre-loaders/gas.svg".');
        }
        .woosbymdz-loading:after {
           content: ""; 
           position: absolute;
           width: 100%; 
           height: 100%;            
           background-color: #cbff86c4;
           top: 0;
           left: 0;
           z-index: 99;
           backdrop-filter: blur(3.5px)saturate(175%);
           -webkit-backdrop-filter: blur(3.5px)saturate(175%);
        }
        ';
        break;
    case 'effect-15':
        $css_code_loading = '
        .woosbymdz-loading:before {
            content: "";
            position: absolute;
            width: 250px;
            height: 250px;
            top: 50%;
            left: 50%;
            margin-left: -150px;
            margin-top: -150px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position-x: center;
            z-index: 100;  
            background-image: url('.WOOSBYMDZ_URL."front/img/pre-loaders/musics.svg".');
        }
        .woosbymdz-loading:after {
            content: ""; 
            position: absolute;
            width: 100%; 
            height: 100%;            
            background-color: #00dfff5c;
            top: 0;
            left: 0;
            z-index: 99;
            backdrop-filter: blur(10px)saturate(50%);
            -webkit-backdrop-filter: blur(10px)saturate(50%);
        }
        ';
        break;
    default:
        $css_code_loading = '';
        break;
}

if($nomain_widget == '1')
{//no loading eff css when main widget is deactive
    $css_code_loading = '';
}
/**/

/*EDIT CSS FILE*/
if (get_option('woosbymdz_iscustomcss') == '1')
{ //custom css
    $extra_css = get_option('woosbymdz_customcss');
}else $extra_css = '';

if (get_option('woosbymdz_productsnobg') == '1' && $nomain_widget == '0')
{ //products-nobg
    $extra_css .= '
.woosbymdz-carousel-con .swiper-slide > div:first-child {background: none !important;}
.woosbymdz-carousel-con .swiper-slide > li:first-child {background: none !important;}';
}

if(get_option('woosbymdz_elem_slider_restitle') == '1' && $nomain_widget == '0')
{
    $max_lines = get_option('woosbymdz_restitle_maxlines');

    if(!$max_lines || trim($max_lines) == ""){
        $max_lines = 2;
    }

    //Some themes like (Flatsome) Needs extra configs!
    $curr_theme_name = get_option('woosbymdz_active_theme');
    switch($curr_theme_name){
        default:
            $extra_css .= '
.woosbymdz-carousel-con .swiper-slide '.$theme_conf[$current_theme][3].', .woosbymdz-carousel-con .swiper-slide .pro-main-title {
    display: -webkit-box !important;
    -moz-box-orient: vertical !important;
    overflow: hidden !important;
    -webkit-line-clamp: '.$max_lines.' !important;
    -webkit-box-orient: vertical !important;
}
';
            break;

        case 'flatsome':
        case 'flatsome child':
        case 'ایران فلتسام':
        case 'فلت سام':
        case 'فلتسام':
            $extra_css .= '
.woosbymdz-carousel-con .swiper-slide '.$theme_conf[$current_theme][3].', .woosbymdz-carousel-con .swiper-slide '.$theme_conf[$current_theme][3].' > a, .woosbymdz-carousel-con .swiper-slide .pro-main-title {
    display: -webkit-box !important;
    -moz-box-orient: vertical !important;
    overflow: hidden !important;
    -webkit-line-clamp: '.$max_lines.' !important;
    -webkit-box-orient: vertical !important;
}
';
            break;
    }

    $extra_css .= '
.wooslider-pro-main-title {
    display: -webkit-box !important;
    -moz-box-orient: vertical !important;
    overflow: hidden !important;
    -webkit-line-clamp: '.$max_lines.' !important;
    -webkit-box-orient: vertical !important;
}';//to use the limit-line css class Globally on other sliders designed without Wooslider!

    /***/
    
}

//Custom-product-cards templates-css
$custom_cards_css_codes = '';
$use_cust_procards = get_option('woosbymdz_use_custtemp_procards');
if($use_cust_procards == 1){
    $custom_cards_css_codes = file_get_contents(WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'custom-cards'.DIRECTORY_SEPARATOR.'custom-pro-cards.css');
}
/***/


$css_code = $css_code.$css_code_loading.$extra_css.$custom_cards_css_codes;
$this->writetofile($dynamic_css_file, $css_code, $static_css_file, 'end');
/**/

/*get elem responsive breakpoints*/
$js_code = '';
if (defined('ELEMENTOR_VERSION'))
{

$breakpoints = \Elementor\Plugin::$instance->breakpoints->get_breakpoints();
$mobile_breakpoint = ($breakpoints['mobile']->get_value())+1;
$tablet_breakpoint = ($breakpoints['tablet']->get_value())+1;
$wide_breakpoint = (intval(get_option('woosbymdz_widebreakpoint'))+1);
$mob2_breakpoint = (intval(get_option('woosbymdz_mob2breakpoint'))+1);
$defcss_resp_roots = '
@media only screen and (max-width: '.$tablet_breakpoint.'px) {.woosbymdz-deskarrows, .woosbymdz-carousel-con[arrows="desktop"] .elementor-swiper-button {display: none !important;}}
@media only screen and (min-width: '.$mobile_breakpoint.'px) {.woosbymdz-mobarrows, .woosbymdz-carousel-con[arrows="mobile"] .elementor-swiper-button {display: none !important;}}
@media only screen and (min-width: '.$tablet_breakpoint.'px) {.woosbymdz-mobtabarrows, .woosbymdz-carousel-con[arrows="mobile_tablet"] .elementor-swiper-button {display: none !important;}}
';



if($nomain_widget == '0')
{
    $this->writetofile($dynamic_css_file, $defcss_resp_roots, $dynamic_css_file, 'end');
}
/*add break points to css file*/

}else{//if no elementor detected

$mobile_breakpoint = 768; /*static_values*/
$tablet_breakpoint = 1025; /*static_values*/
$wide_breakpoint = (intval(get_option('woosbymdz_widebreakpoint'))+1);
$mob2_breakpoint = (intval(get_option('woosbymdz_mob2breakpoint'))+1);

}

/*Extra Widgets CSS adder*/
if(get_option('woosbymdz_extrawidget1') == '1' || get_option('woosbymdz_shortcode2') == '1')
{//Extra widget-1 css codes

    $widget_css = WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'cats-slider'.DIRECTORY_SEPARATOR.'cats-slider-min.css';
    
    $css_code = file_get_contents($widget_css);
    $this->writetofile($dynamic_css_file, $css_code, $dynamic_css_file, 'end');
    
}
/**/

//Elementor Dynamic-tags CSS Codes
if(get_option('woosbymdz_elem_dis_dynatags') != 1){
    $tmp_css_file = WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'dyna-tags'.DIRECTORY_SEPARATOR.'dyna-tags.css';

    $css_code = file_get_contents($tmp_css_file);
    $this->writetofile($dynamic_css_file, $css_code, $dynamic_css_file, 'end');
}
/***/

if($nomain_widget == '0') 
{//add mobile enhanced breakpoint to dynamic CSS file

    $tmp_css_code = "
    @media only screen and (max-width: ".$mobile_breakpoint."px){
        .woosbymdz-carousel-con[dont-swipe-onmobile='yes'] .swiper-button-prev, .woosbymdz-carousel-con[dont-swipe-onmobile='yes'] .swiper-button-next, .woosbymdz-carousel-con[dont-swipe-onmobile='yes'] .swiper-pagination {display: none !important;}
        .woosbymdz-carousel-con .woosbymdz-swiper[dont-swipe-onmobile='yes'] .swiper-wrapper {
            overflow-x: auto !important;
            overflow-y: hidden !important;
        }
    }
    ";

    $this->writetofile($dynamic_css_file, $tmp_css_code, $dynamic_css_file, 'end');

}

/*Extra Widgets JS adder*/
if(get_option('woosbymdz_extrawidget1') == '1' || get_option('woosbymdz_shortcode2') == '1')
{//Extra widget-1 js codes

$widget_js_min_content1 = file_get_contents(WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'cats-slider'.DIRECTORY_SEPARATOR.'cats-slider.js');
    
}else $widget_js_min_content1 = '';
/**/

/*Swiper main js file*/
$is_old_theme = get_option('woosbymdz_oldthemesdebuger');
$is_user_ajaxjs = get_option('woosbymdz_isajaxjs');

if($is_user_ajaxjs == '1')
{
    $user_cust_ajax_js = get_option('woosbymdz_ajaxjs');
    
}else $user_cust_ajax_js = '';


switch($is_old_theme)
{
    case '1':
    $js_start_func = 
'
function woosbymdz_mainjs_run() {
  window.addEventListener("load", woosbymdz_innerjs_run);
} 
';
    break;
    case '0':
    $js_start_func = 
'
function woosbymdz_mainjs_run() {
    jQuery(document).ready(woosbymdz_innerjs_run);
} 
';
    break;
}

/*Main js code*/
if ($nomain_widget == '0')
{//if main widget is active add the core js codes

$js_swiper_before_constructor = '
function woosbymdz_do_user_cutom_js_codes(){'.$user_cust_ajax_js.'}
';
$js_swiper_constructor = file_get_contents(WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'front-swiper-constructor.js');



$main_widget_js_codes = file_get_contents(WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'front-static.js');

}else $main_widget_js_codes = '';

//Extra JS adder
if (get_option('woosbymdz_iscustomjs') == '1')
{ //custom css
    $extra_js = get_option('woosbymdz_customjs');
}else $extra_js = '';

//set devices-js
$devices_js = file_get_contents(WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'devices.js');
/***/

//Main-JS file Structure
$js_code = '
var woosbymdz_loading_time = '.intval(get_option('woosbymdz_loadingtime')).';
var woosbymdz_siteroot_url = "'.home_url().'";
var woosbymdz_plugin_root_url = "'.WOOSBYMDZ_URL.'";
var woosbymdz_active_theme  = "'.$current_theme.'";
var woosbymdz_wide_breakpoint = "'.$wide_breakpoint.'";
var woosbymdz_tablet_breakpoint = "'.$tablet_breakpoint.'";
var woosbymdz_mobile_breakpoint = "'.$mobile_breakpoint.'";
var woosbymdz_mobile_breakpoint2 = "'.$mob2_breakpoint.'";
var woosbymdz_now_device = "default";
'.$devices_js.'
'.$extra_js.'
'.$js_swiper_before_constructor.'
'.$js_swiper_constructor.'
'.$js_start_func.'
function woosbymdz_innerjs_run(){
'.$main_widget_js_codes.'
'.$widget_js_min_content1.'
}woosbymdz_mainjs_run();';

$this->writetofile($dynamic_js_file, $js_code, '', 'begin'); 
/**/

if(get_option('woosbymdz_reloadajaxcomp') == '1' && $nomain_widget == '0' )
{//Reloading ajax Comp JS Adder

$js_code = '
jQuery(document).ajaxComplete(function(event, request, settings){

    var ajax_data = settings.data;
    var ajax_type = settings.type;

    var all_reloadajaxcomp_woosliders = document.querySelectorAll(".woosbymdz-carousel-con.woosbymdz-reload_ajax_comp");


    if(ajax_type == "get" || ajax_type == "GET"){


        for(var mdz = 0; mdz < all_reloadajaxcomp_woosliders.length; mdz++){

            var tmp_slider = all_reloadajaxcomp_woosliders[mdz];
            var tmp_swiper_sw = tmp_slider.querySelector(".woosbymdz-swiper");
            all_header_items = document.getElementsByClassName(
                "woosbymdz-header-divs"
            );

            woosbymdz_construct_swiper(tmp_swiper_sw, tmp_slider);
            woosbymdz_assign_allajax_btns(all_header_items);
        }

    }
});
';
$this->writetofile($dynamic_js_file, $js_code, $dynamic_js_file, 'end');
}

/*get the current options of this page for HTML*/
$sliders_loadeffect = get_option('woosbymdz_loadingeffect');
$is_extracss = get_option('woosbymdz_iscustomcss') == '1' ? 'checked' : '';
$is_extrajs = get_option('woosbymdz_iscustomjs') == '1' ? 'checked' : '';
$products_nobg = get_option('woosbymdz_productsnobg') == '1' ? 'checked' : '';
$old_themes_debuger = get_option('woosbymdz_oldthemesdebuger') == '1' ? 'checked' : '';
$import_jq = get_option('woosbymdz_importjq') == '1' ? 'checked' : '';
$dont_import_swiper = get_option('woosbymdz_donimport_swiper') == '1' ? 'checked' : '';
$last_seen_cookie = get_option('woosbymdz_last_seen_cookie') == '1' ? 'checked' : '';
$last_seen_cookie_js = get_option('woosbymdz_productstracker_js') == '1' ? 'checked' : '';
$is_browsernocache = get_option('woosbymdz_isbrowsernocache') == '1' ? 'checked' : '';
$nomain_widget = get_option('woosbymdz_nomainwidget') == '1' ? 'checked' : '';
$widgets_category = get_option('woosbymdz_addnewwidgets_category') == '1' ? 'checked' : '';
$reload_ajax_comp = get_option('woosbymdz_reloadajaxcomp') == '1' ? 'checked' : '';
$shortcode2 = get_option('woosbymdz_shortcode2') == '1' ? 'checked' : '';
$add_yoast_slider_to_productspage = get_option('woosbymdz_yoast_maincat_addslider') == '1' ? 'checked' : '';
$add_yoast_slider_is_customized_shortcode = get_option('woosbymdz_yoast_maincat_iscustomizedshortcode') == '1' ? 'checked' : '';
$yoast_slider_title = get_option('woosbymdz_ppage_slidertitle');
$sliders_loadeffect_img = get_option('woosbymdz_loadingeffect_img');
$sliders_loadeffect_imgw = get_option('woosbymdz_loadingeffect_imgw');
$sliders_loadeffect_imgh = get_option('woosbymdz_loadingeffect_imgh');
$sliders_loadeffect_imgb = get_option('woosbymdz_loadingeffect_imgb');
$global_import = get_option('woosbymdz_globalimport') == '1' ? 'checked' : '';
$is_ajaxjs = get_option('woosbymdz_isajaxjs') == '1' ? 'checked' : '';
$js_ajax_codes = get_option('woosbymdz_ajaxjs');
$use_charged = get_option('woosbymdz_charged_pros') == '1' ? 'checked' : '';
$charged_cancel_disc = get_option('woosbymdz_charged_cancdisc') == '1' ? 'checked' : '';
$charged_is_addcat = get_option('woosbymdz_charged_isaddcat') == '1' ? 'checked' : '';
$charged_don_btn = get_option('woosbymdz_charged_donshowbtn') == '1' ? 'checked' : '';
$charged_btn_pos = get_option('woosbymdz_charged_btnpos');
$swiper_noeff = get_option('woosbymdz_swipernoeff') == '1' ? 'checked' : '';
$allcss_impo = get_option('woosbymdz_elem_allcss_impo') == '1' ? 'checked' : '';
$procss_impo = get_option('woosbymdz_elem_procss_impo') == '1' ? 'checked' : '';
$slider_res_title = get_option('woosbymdz_elem_slider_restitle') == '1' ? 'checked' : '';
$pros_restitle_maxlines = get_option('woosbymdz_restitle_maxlines');
$org_theme_name = get_option('woosbymdz_orgtheme_name');
$noajax_ref_checker = get_option('woosbymdz_noajaxrefchecker') == '1' ? 'checked' : '';
$use_custom_procards = get_option('woosbymdz_use_custtemp_procards') == '1' ? 'checked' : '';
$dis_dyna_tags = get_option('woosbymdz_elem_dis_dynatags') == '1' ? 'checked' : '';
/**/

//Charged-Pros
$has_used_ever = get_option('woosbymdz_charged_pros_everrunned');
if(!$has_used_ever){
    $hs_uset_stats = esc_html__('Not done yet', 'wooslider-bymdez');
}else $hs_uset_stats = esc_html__('Done!', 'wooslider-bymdez');

//short - codes
$shortcode1 = get_option('woosbymdz_shortcode1') == '1' ? 'checked' : '';
//Extra - widgets
$extra_widget1 = get_option('woosbymdz_extrawidget1') == '1' ? 'checked' : '';
$extra_widget2 = get_option('woosbymdz_extrawidget2') == '1' ? 'checked' : '';
/**/

/*SET This PAGE's OPTIONS AUTOLOAD FALSE*/
$this->set_autoload_off('woosbymdz_loadingtime');

$this->set_autoload_off('woosbymdz_loadingeffect');

$this->set_autoload_off('woosbymdz_mob2breakpoint');

$this->set_autoload_off('woosbymdz_widebreakpoint');

$this->set_autoload_off('woosbymdz_iscustomcss');

$this->set_autoload_off('woosbymdz_customcss');

$this->set_autoload_off('woosbymdz_iscustomjs');

$this->set_autoload_off('woosbymdz_customjs');

$this->set_autoload_off('woosbymdz_productsnobg');

$this->set_autoload_off('woosbymdz_oldthemesdebuger');

$this->set_autoload_off('woosbymdz_isdsc');

$this->set_autoload_off('woosbymdz_dsc_delay1');

$this->set_autoload_off('woosbymdz_dsc_delay2');

$this->set_autoload_off('woosbymdz_reloadajaxcomp');

$this->set_autoload_off('woosbymdz_css_file');

$this->set_autoload_off('woosbymdz_js_file');

$this->set_autoload_off('woosbymdz_loadingeffect_img');

$this->set_autoload_off('woosbymdz_loadingeffect_imgw');

$this->set_autoload_off('woosbymdz_loadingeffect_imgh');

$this->set_autoload_off('woosbymdz_loadingeffect_imgb');

$this->set_autoload_off('woosbymdz_nightmode');

$this->set_autoload_off('woosbymdz_is_modertheme');

$this->set_autoload_off('woosbymdz_isajaxjs');

$this->set_autoload_off('woosbymdz_ajaxjs');

$this->set_autoload_off('woosbymdz_orgtheme_name');

$this->set_autoload_off('woosbymdz_elmpro_hasfile_edited');

$this->set_autoload_off('woosbymdz_use_custtemp_procards');
//close the PHP tag to reduce the blank spaces ?>