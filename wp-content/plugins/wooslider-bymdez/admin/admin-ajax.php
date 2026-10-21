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


/*Ajax for Empty data & Options Conf*/
if (!function_exists('woosbymdz_emptyOptions_AjaxConf'))
{
    function woosbymdz_emptyOptions_AjaxConf()
    {

        if(!current_user_can('install_plugins')){
            wp_send_json_error( 'WooSlider: Ajax-Error!' );
            wp_die();
        }//For the Security

        $tmp = sanitize_text_field($_POST['tmp']);
        $res = array();
        if ($tmp == '1'){
            
            $res[0] = delete_option('woosbymdz_empty_onDeactivate');
            $res[1] = delete_option('woosbymdz_redirect_onActivate');
            $res[2] = delete_option('woosbymdz_loadingtime');
            $res[3] = delete_option('woosbymdz_loadingeffect');
            $res[4] = delete_option('woosbymdz_mob2breakpoint');
            $res[5] = delete_option('woosbymdz_widebreakpoint');
            $res[6] = delete_option('woosbymdz_iscustomcss');
            $res[7] = delete_option('woosbymdz_customcss');
            $res[8] = delete_option('woosbymdz_nightmode');
            $res[9] = delete_option('woosbymdz_productsnobg');
            $res[10] = delete_option('woosbymdz_oldthemesdebuger');
            $res[11] = delete_option('woosbymdz_importjq');
            $res[12] = delete_option('woosbymdz_last_seen_cookie');
            $res[13] = delete_option('woosbymdz_shortcode1');
            $res[14] = delete_option('woosbymdz_isdsc');
            $res[15] = delete_option('woosbymdz_dsc_delay1');
            $res[16] = delete_option('woosbymdz_dsc_delay2');
            $res[17] = delete_option('woosbymdz_isbrowsernocache');
            $res[18] = delete_option('woosbymdz_extrawidget1');
            $res[19] = delete_option('woosbymdz_nomainwidget');
            $res[20] = delete_option('woosbymdz_reloadajaxcomp');
            $res[22] = delete_option('woosbymdz_shortcode2'); 
            $res[23] = delete_option('woosbymdz_iscustomjs');
            $res[24] = delete_option('woosbymdz_customjs');
            $res[25] = delete_option('woosbymdz_plugin_version'); 
            $res[26] = delete_option('woosbymdz_css_file');
            $res[27] = delete_option('woosbymdz_js_file'); 
            $res[28] = delete_option('woosbymdz_yoast_maincat_permalink');
            $res[29] = delete_option('woosbymdz_yoast_maincat_addslider');
            $res[30] = delete_option('woosbymdz_yoast_maincat_iscustomizedshortcode');
            $res[31] = delete_option('woosbymdz_yoast_maincat_customizedshortcode');
            $res[32] = delete_option('woosbymdz_donimport_swiper');
            $res[33] = delete_option('woosbymdz_productstracker_js');
            $res[34] = delete_option('woosbymdz_ppage_slidertitle');
            $res[35] = delete_option('woosbymdz_addnewwidgets_category');
            $res[36] = delete_option('woosbymdz_loadingeffect_img'); 
            $res[37] = delete_option('woosbymdz_loadingeffect_imgw'); 
            $res[38] = delete_option('woosbymdz_loadingeffect_imgh'); 
            $res[40] = delete_option('woosbymdz_loadingeffect_imgb');
            $res[41] = delete_option('woosbymdz_is_modertheme');  
            $res[42] = delete_option('woosbymdz_globalimport');
            $res[43] = delete_option('woosbymdz_active_theme');
            $res[44] = delete_option('woosbymdz_isajaxjs');
            $res[45] = delete_option('woosbymdz_ajaxjs'); 
            $res[46] = delete_option('woosbymdz_charged_pros');
            $res[47] = delete_option('woosbymdz_charged_pros_everrunned');
            $res[48] = delete_option('woosbymdz_charged_cancdisc');
            $res[49] = delete_option('woosbymdz_charged_isaddcat');
            $res[50] = delete_option('woosbymdz_charged_addcat');
            $res[51] = delete_option('woosbymdz_charged_donshowbtn');
            $res[52] = delete_option('woosbymdz_charged_btnpos');   
            $res[53] = delete_option('woosbymdz_tmpversion');  
            $res[54] = delete_option('woosbymdz_swipernoeff');     
            $res[55] = delete_option('woosbymdz_elem_allcss_impo');
            $res[56] = delete_option('woosbymdz_elem_procss_impo');
            $res[57] = delete_option('woosbymdz_elem_slider_restitle');  
            $res[58] = delete_option('woosbymdz_restitle_maxlines'); 
            $res[59] = delete_option('woosbymdz_orgtheme_name'); 
            $res[60] = delete_option('woosbymdz_extrawidget2'); 
            $res[61] = delete_option('woosbymdz_elmpro_hasfile_edited'); 
            $res[62] = delete_option('woosbymdz_elmpro_has_updated'); 
            $res[63] = delete_option('woosbymdz_noajaxrefchecker'); 
            $res[64] = delete_option('woosbymdz_use_custtemp_procards'); 
            $res[65] = delete_option('woosbymdz_elempro_modules_backup_file'); 
            $res[66] = delete_option('woosbymdz_elem_dis_dynatags'); 
        }
        $count = count($res);
        $counter = 0;

        foreach($res as $re){
            if($re === true){ $counter++; }
        }

        if ($counter == $count){
            echo 'Woo Slider by MDZ Plugin Datas and Options Deleted!';
        }

        //remove products metas
        delete_post_meta_by_key('woosbymdz_pro_quan');
        delete_post_meta_by_key('woosbymdz_is_charged');

        wp_die();
    }
}
add_action('wp_ajax_woosbymdz_emptyOptions_AjaxConf', 'woosbymdz_emptyOptions_AjaxConf' );
/**/

/*NightMode Ajax*/
if (!function_exists('woosbymdz_nightMode_ajax'))
{
    function woosbymdz_nightMode_ajax()
    {
   
        if ( ! check_ajax_referer( 'woosbymdz-8883-xmdzrt9y', 'shonafahmi', false ) ) 
        {
            wp_send_json_error( 'WooSlider: Ajax-Error!' );
            wp_die();
        }//For the Security

        if(!current_user_can('install_plugins')){
            wp_send_json_error( 'WooSlider: Ajax-Error!' );
            wp_die();
        }//For the Security

        $mode = sanitize_text_field($_POST['mode']);

        switch($mode){

            case 'dark':
                $is_night = get_option('woosbymdz_nightmode');

                if ($is_night == '0' || $is_night == '' || $is_night == 'null' || $is_night == NULL){
                    update_option('woosbymdz_nightmode', '1', false);
                    echo '1';
                }else if ($is_night == '1'){
                    update_option('woosbymdz_nightmode', '0', false);
                    echo '0';
                }
                wp_die();
                break;

            case 'theme':
                $is_modern = get_option('woosbymdz_is_modertheme');

                if($is_modern === false){

                    update_option('woosbymdz_is_modertheme', '0', false);
                    echo '0';

                }else if ($is_modern == '0'){

                    update_option('woosbymdz_is_modertheme', '1', false);
                    echo '1';

                }else if ($is_modern == '1'){

                    update_option('woosbymdz_is_modertheme', '0', false);
                    echo '0';

                }
                wp_die();
                break;
            default:
                break;
        }
    }
}
add_action('wp_ajax_woosbymdz_nightMode_ajax', 'woosbymdz_nightMode_ajax' );
/**/

/*Charged-Btn*/
if (!function_exists('woosbymdz_check_pro_charge'))
{
    function woosbymdz_check_pro_charge()
    { 
        if ( ! check_ajax_referer( 'woosbymdz-sikansoo9m-wook', 'sedamej12', false ) ) 
        {
            wp_send_json_error( 'WooSlider: Ajax-Error!' );
            wp_die();
        }//For the Security

        if(!current_user_can('edit_products')){
            wp_send_json_error( 'WooSlider: Ajax-Error!' );
            wp_die();
        }//For the Security

        $is_charged = sanitize_text_field($_POST['is_sharjed']);
        $pid = sanitize_text_field($_POST['pid']);

        switch($is_charged){
            case '1':
                update_post_meta($pid, 'woosbymdz_is_charged', 0);
                echo 0;
                break;
            case '0':
                update_post_meta($pid, 'woosbymdz_is_charged', 1);
                echo 1;
                break;
        }
        
        wp_die();
    }
}
add_action('wp_ajax_woosbymdz_check_pro_charge', 'woosbymdz_check_pro_charge' );
/**/

/*Elementor-Pro restore-file*/
if (!function_exists('woosbymdz_elmpro_restore_file'))
{
    function woosbymdz_elmpro_restore_file()
    { 
        if ( ! check_ajax_referer( 'woosbymdz-8883-xmdzrt9y', 'shonafahmi', false ) ) 
        {
            wp_send_json_error( 'WooSlider: Ajax-Error!' );
            wp_die();
        }//For the Security

        if(!current_user_can('install_plugins')){
            wp_send_json_error( 'WooSlider: Ajax-Error!' );
            wp_die();
        }//For the Security

        $php_file = ABSPATH.DIRECTORY_SEPARATOR.'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'elementor-pro'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'loop-builder'.DIRECTORY_SEPARATOR.'module.php';

        $backup_file =WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'file-changes'.DIRECTORY_SEPARATOR.'module-php-backup.txt';

        if(is_file($php_file) && is_file($backup_file)){

            $contents = file_get_contents($backup_file);

            if(!empty($contents)){

                file_put_contents($php_file, $contents);

                update_option('woosbymdz_elmpro_hasfile_edited', 0, false);

                file_put_contents($backup_file, '');

                echo 'done';
                wp_die();
            }
        }
        
        echo 'error';
        wp_die();
    }
}
add_action('wp_ajax_woosbymdz_elmpro_restore_file', 'woosbymdz_elmpro_restore_file' );
/**/

//close the PHP tag to reduce the blank spaces ?>