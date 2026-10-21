<?php
if (!function_exists('add_action'))
{
    echo "<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>";
    exit();
}
else if (!defined('ABSPATH'))
{
    echo "<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>";
    exit();
}
//Yoast Main-cat screen-shots url
$yoast_mc_sc_1 = WOOSBYMDZ_URL.'admin/img/extra/yoast-help1-en.webp';
$yoast_mc_sc_2 = WOOSBYMDZ_URL.'admin/img/extra/yoast-help2-en.webp';
$yoast_mc_sc_3 = WOOSBYMDZ_URL.'admin/img/extra/yoast-help3-fa.webp';
$meta_box_img = WOOSBYMDZ_URL.'admin/img/extra/cap-2.webp';
$version_direction = "right";

$example_img_1 = WOOSBYMDZ_URL.'admin/img/wooslider-widgets.webp';
$example_img_2 = WOOSBYMDZ_URL.'admin/img/wooslider-widgets2.webp';
$example_img_3 = WOOSBYMDZ_URL.'admin/img/loop-builder-widget.webp';
$example_img_4 = WOOSBYMDZ_URL.'admin/img/woocats-slider.webp';

$sh_gen_plg_dl_link = 'https://wooslider.ir/download-sh-generator-plg/';
if(is_rtl())
{

  $yoast_mc_sc_1 = WOOSBYMDZ_URL.'admin/img/extra/yoast-help1-fa.webp';
  $yoast_mc_sc_2 = WOOSBYMDZ_URL.'admin/img/extra/yoast-help2-fa.webp';
  $yoast_mc_sc_3 = WOOSBYMDZ_URL.'admin/img/extra/yoast-help3-fa.webp';
  $meta_box_img = WOOSBYMDZ_URL.'admin/img/extra/cap-1.webp';
  $version_direction = "left";

  $example_img_1 = WOOSBYMDZ_URL.'admin/img/fa-exm-4.webp';
  $example_img_2 = WOOSBYMDZ_URL.'admin/img/fa-exm-2.webp';
  $example_img_3 = WOOSBYMDZ_URL.'admin/img/fa-exm-1.webp';
  $example_img_4 = WOOSBYMDZ_URL.'admin/img/fa-exm-3.webp';

}

$buy_elempro_aff_link = 'https://www.rtl-theme.com/elementor-pro/?aff=delbarbash';
$thank_you_text = esc_html__('Thank you for using this Plugin', 'wooslider-bymdez');
$tank_you_con_class = '';
$rtl_logo = WOOSBYMDZ_URL . 'admin/img/extra/rtl-logo.svg';
$plg_link2 = "https://www.rtl-theme.com/woo-slider-wordpress-plugin/";
if(WOOSBYMDZ_IS_WPHOOLU == 1){

    $buy_elempro_aff_link = 'https://wphoolu.com/p/elementor-pro/';
    $thank_you_text = esc_html__('Thank you for using this Plugin from the Wphoolu.com', 'wooslider-bymdez');
    $tank_you_con_class = 'wph-thankyou-con';

    $plg_link2 = 'https://wphoolu.com/p/wooslider/';
    $rtl_logo = WOOSBYMDZ_URL . 'admin/img/extra/wph-logo.svg';
}

/**/

//Set user color css-vars
$admin_color_themes = array
(
    'modern' => '#3858e9',
    'fresh' => '#2271b1',
    'light' => '#04a4cc',
    'blue' => '#e1a948',
    'coffee' => '#c7a589',
    'ectoplasm' => '#a3b745',
    'midnight' => '#e14d43',
    'ocean' => '#9ebaa0',
    'sunrise' => '#dd823b',

    //admin color schemes themes
    'modern-evergreen' => '#0F4232',
    'lawn' => '#A7B145',
    'seashore' => '#456A7F',
    'primary' => '#D8B230',
    'vineyard' => '#CC9E14',
    'vinyard' => '#CC9E14',
    'kirk' => '#BD3854',
    'flat' => '#1ABC9C',
    'cruise' => '#79B591',
    'contrast-blue' => '#264D79',
    'aubergine' => '#C99416',
    'adderley' => '#1730E5',
    '80s-kid' => '#D13674',
);

if($is_nightmode == '1'){
    $admin_color_themes['contrast-blue'] = '#a5cde8';
    $admin_color_themes['modern-evergreen'] = '#4bcda2';
}
?>
<style>
:root {
  --woosbymdz-user-color: <?php if(isset($admin_color_themes[$user_color])){ echo wp_kses($admin_color_themes[$user_color], array()); }else{echo wp_kses('#192229', array());} ?>;

  --woosbymdz-help-txt: "<?php echo __('help', 'wooslider-bymdez') ?>";
  --woosbymdz-txt1: "<?php echo __('Settings related to slider products', ' wooslider-bymdez') ?>";
  --woosbymdz-txt2: "<?php echo __('Settings related to Products Main-Cat Source Mode', ' wooslider-bymdez') ?>";
  --woosbymdz-txt3: "<?php echo __('After changing products inside slider', ' wooslider-bymdez') ?>";
  --woosbymdz-txt4: "<?php echo __('Settings related to the charged mode controller button on the product page', ' wooslider-bymdez') ?>";



  --woosbymdz-css-bg: url("<?php echo esc_url( WOOSBYMDZ_URL. 'admin/img/extra/bg-footer-noise.jpg'); ?>");
  
}
</style>
<?php
/***/

/*Night Mode*/
if ($is_nightmode == '1')
{
$is_nightmode = 'on';
?>
<style>
body {color: #b5b5b5;}
div#wpcontent {background-color: #1e1e1e;}
</style>
<?php
}
/**/

/*Modern Graphics*/
if($is_modern_theme == '0')
{
    $page_current_theme = __('Classic', 'wooslider-bymdez');
}

if($is_modern_theme === false || $is_modern_theme == '1' || $is_modern_theme == "" || $is_modern_theme == " ")
{

    include_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."css".DIRECTORY_SEPARATOR."modern-styles.php";
    $page_current_theme = __('Modern', 'wooslider-bymdez');
    update_option("woosbymdz_is_modertheme", "1", false);

}
/**/

/*Links*/
$plg_link = "https://wooslider.ir/";

$yt_link = "https://www.aparat.com/M.Design";
$learn_link = "https://wooslider.ir/learn/";
$ajax_reload_link = "https://wooslider.ir/reloading-ajax-comp/";
$sh1_link = "https://wooslider.ir/usingshortcode/";
$sh2_link = "https://wooslider.ir/usingshortcode2/";
$yoast_maincat_link = "https://wooslider.ir/yoast-main-cat-mode/";
/***/

//Elementor-Pro configs
$has_elem_pro = $this->check_plg('elementor-pro.php');
$has_elementor = $this->check_plg('elementor.php');
$elem_pro_ver = 'none';

$loop_widget_status = 1;
$loop_opt_check = get_option('woosbymdz_extrawidget2');
if(!$loop_opt_check || $loop_opt_check != 1){
    $loop_widget_status = 0;
}

$has_php_file_edited = get_option('woosbymdz_elmpro_hasfile_edited');
$loop_widget_status_txt = esc_html__('Not Active', 'wooslider-bymdez');
if($loop_widget_status == 1 && $has_php_file_edited == 1){
    $loop_widget_status_txt = esc_html__('Active', 'wooslider-bymdez');
}


if($has_elem_pro){
    $elem_pro_ver = ELEMENTOR_PRO_VERSION;
}
/***/

//Announces Config
$has_anns = 0;
$all_anns = [];



if(!$has_elementor){


    $all_anns[] = [
        esc_html__('It seems that you are not using the Elementor plugin, in this case, you can use the Wooslider widgets in any page-builder by using a short code. Just visit the `short code [ ]` section!', 'wooslider-bymdez'),
        '',
        'no-elementor-ann'
    ];

    
}else {
    
    if(!$has_elem_pro){

        $all_anns[] = [
            esc_html__('It seems that you are not using the Elementor Pro plugin, in this case you cannot use the Loop-Builder widget of the Wooslider plugin without Elementor Pro.', 'wooslider-bymdez'),
            esc_html__('Buy Elementor-Pro', 'wooslider-bymdez').'|'.$buy_elempro_aff_link.'|'.'#008d3d',
            'no-elementorpro--ann'
        ];
    }
}




if(count($all_anns) > 0){
    $has_anns = 1; 
}
/***/

?>

<div class="woosbymdz-con <?php if ($is_nightmode == 'on'){echo esc_attr('nightmodeon');} ?>" is-rtl="<?php echo esc_attr(is_rtl()); ?>">

<div style="display: flex;flex-direction: row;align-items: center;">
<h2 style="margin: 55px 22px;"><?php echo __('Welcome', 'wooslider-bymdez') ?></h2>
<a href="<?php echo esc_url($plg_link); ?>" target="_blank"><img src="<?php echo esc_url(WOOSBYMDZ_URL . 'admin/img/extra/woosbymdz-logo.svg'); ?>" style="height: fit-content;width: 90px;margin-right: 35px;" class="woosbymdzlogo"></a>
</div>

<div style="justify-content: space-between;" class="woosbymdz-page-header flex items-center">

<div class="flex-col items-center">
    <div class="div-inline" style="justify-content: center;"><!--Night Btn--> 
    <div class="nightmode-btn" style="margin-top: 15px;"><span><img class="nightmode-btn-img1" src="<?php echo esc_url(WOOSBYMDZ_URL . 'admin/img/extra/daytinight.svg'); ?>" width="99px"></span><span class="nightbtn-arrow"><img src="<?php echo esc_url(WOOSBYMDZ_URL . 'admin/img/extra/arrow-btn.svg'); ?>" width="23px" class="nightmode-btn-img2"></span></div></div><!--Endof Night Btn-->

    <div class='woosbymdz-theme-changer flex items-center justify-center cursor-pointer trans-base' title='<?php echo __('change theme', 'wooslider-bymdez'); ?>'><!--Theme Changer-->
        <span class='mx-1'><?php echo __('Theme: ', 'wooslider-bymdez'); ?></span>
        <span class='mx-1'><?php echo $page_current_theme; ?></span>
    </div><!--Endof Theme Changer-->
</div>

<div>
<a style="color: #506b7800;" href="<?php echo esc_url($plg_link2); ?>" target="_blank">
    <img src="<?php echo esc_url($rtl_logo); ?>" style="width: 34px;margin-top: 10px;margin-left: 5px;" class="social-logo">
</a>

<a style="color: #506b7800;" href="<?php echo esc_url($yt_link); ?>" target="_blank">
    <img src="<?php echo esc_url(WOOSBYMDZ_URL . 'admin/img/extra/aparat-logo-svg.svg'); ?>" style="width: 34px;margin-top: 10px;margin-left: 5px;" class="social-logo">
</a>
</div>

</div>

<span style="float: <?php echo $version_direction; ?>;margin-top: 9px;"><?php echo __('Version: ', 'wooslider-bymdez').WOOSBYMDZ_VER; ?></span>

<div style="margin-top: 55px;" id="settings-mother">

    
    <div id="settings-side" woosbymdz-stick>

        <form action="" method="POST">
        <div class="settings-side-inner" woosbymdz-sticky="45">

            <div class="block-setting-connector button btn-sec-active" open-filed="1">
                <?php echo esc_html__('Theme', 'wooslider-bymdez'); ?>
            </div>

            <?php if($nomain_widget != 'checked')://Add this if no main widget is deactive ! ?>
            <div class="block-setting-connector button" open-filed="2">
                <?php echo esc_html__('Product-Widget General Settings', 'wooslider-bymdez'); ?>
            </div>
            <?php endif; ?>

            <div class="block-setting-connector button" open-filed="3">
                <?php echo esc_html__('Extra Breakpoints', 'wooslider-bymdez'); ?>
            </div>

            <div class="block-setting-connector button" open-filed="4">
                <?php echo esc_html__('Extra CSS', 'wooslider-bymdez'); ?>
            </div>

            <div class="block-setting-connector button" open-filed="5">
                <?php echo esc_html__('Extra JS', 'wooslider-bymdez'); ?>
            </div>

            <div class="block-setting-connector button" open-filed="6">
                <?php echo esc_html__('Advanced', 'wooslider-bymdez'); ?>
            </div>

            <div class="block-setting-connector button" open-filed="7">
                <?php echo esc_html__('[   ] Short-Codes', 'wooslider-bymdez'); ?>
            </div>

            <?php if($has_elementor): ?>
            <div class="block-setting-connector button" open-filed="8">
                <?php echo esc_html__('Extra Widgets', 'wooslider-bymdez'); ?>
            </div>
            <?php endif; ?>

            <?php if($nomain_widget != 'checked')://Add this if no main widget is deactive ! ?>
            <div class="block-setting-connector button" open-filed="9">
                <?php echo esc_html__('Charged products', 'wooslider-bymdez'); ?>
            </div>
            <?php endif; ?>

            <?php if($has_elementor): ?>
            <div class="block-setting-connector button" open-filed="10">
                <?php echo esc_html__('Elementor', 'wooslider-bymdez'); ?>
            </div>
            <?php endif; ?>

            <div class="block-setting-connector saver-side-btn">
                <?php submit_button(__('Save Settings', 'wooslider-bymdez')); ?>
            </div>

        </div>

    </div>

    <div id="settings-main">

            <div id="new-features-sec">
                <h5>
                    <?php echo sprintf(esc_html__('New features added in version: %s', 'wooslider-bymdez'), WOOSBYMDZ_VER) ?>
                </h5>

                <ul style="list-style: square;font-size: 11px;">

                    <li>
                        <?php echo esc_html__('Adding exclusive dynamic tags of WooSlider to Elementor Pro (Visit `Elementor` Section)', 'wooslider-bymdez'); ?>
                    </li>

                    <li>
                        <?php echo esc_html__('Added new `Loop Builder Slider` Elementor-Widget (requires Elementor-pro plugin) (Visit `Extra Widgets` Section)', 'wooslider-bymdez'); ?>
                    </li>

                    <li>
                        <?php echo esc_html__('Added new `Short-Code Generator System` (requires WooProducts-ShortCode-Generator plugin) (Visit `[   ] Short-Codes` Section)', 'wooslider-bymdez'); ?>
                    </li>

                    <li>
                        <?php echo esc_html__('Added new `Charged-Products` System (Visit `Charged products` section)', 'wooslider-bymdez'); ?>
                    </li>
                </ul>

                

            </div>

            <?php if($has_anns == 1): ?>
            <fieldset closed="no" id="plugin-announcements"> 

                <legend><?php echo __('Notifications', 'wooslider-bymdez') ?></legend>

                <div class="acordion-btn"><span class="dashicons dashicons-plus" title="<?php echo __('Click to open', 'wooslider-bymdez') ?>"></span></div>

                <div class="acordion">

                    <div class="inner-announcements">
                        <?php
                        foreach($all_anns as $ann){
                            $ann_txt = $ann[0];
                            $ann_link = $ann[1];
                            $ann_id = $ann[2];

                            $link_txt = '';
                            $link_url = '#plugin-announcements';
                            $link_btn_bgcolor = '';
                            if(strpos($ann_link, '|') !== false){
                                $ann_link = explode('|', $ann_link);

                                $link_url = $ann_link[1];
                                $link_txt = $ann_link[0];
                                $link_btn_bgcolor = $ann_link[2];

                            }

                            ?>
                            <div class="single-woosbymdz-ann" id="<?php echo esc_attr($ann_id); ?>">

                                <span><?php echo esc_html($ann_txt); ?></span>

                                <?php if(!empty($link_txt)): ?>
                                <a href="<?php echo esc_url($link_url); ?>" target="_blank" class="woosbymdz-btn" <?php if(!empty($link_btn_bgcolor)){ echo 'style="background-color: '.$link_btn_bgcolor.';"';} ?>>
                                    <?php echo esc_html($link_txt); ?>
                                </a>
                                <?php endif; ?>

                            </div>
                            <?php
                        } 
                        ?>

                    </div>
                </div>


            </fieldset>
            <?php endif; ?>
        

            <fieldset closed="no" numb="1">

                <div style="display: none;">
                    <input type="text" placeholder="orginal theme name" value="<?php echo esc_attr($org_theme_name); ?>" name="woosbymdz-hddn-orgtheme-name" id="woosbymdz-hddn-orgtheme-name">
                </div>

                <legend><?php echo __('Theme', 'wooslider-bymdez') ?></legend>

                <div class="acordion-btn"><span class="dashicons dashicons-plus" title="<?php echo __('Click to open', 'wooslider-bymdez') ?>"></span></div>
                <div class="acordion">

                <p>
                <div style="align-items: center;" class="custom-select div-inline <?php if ($is_theme_supported) {echo esc_attr('actived_checkboxes'); }else echo esc_attr('disabled_checkboxes'); ?>">
                <label><?php echo __('Your active theme', 'wooslider-bymdez') ?></label>
                <div class="theme_icon"><img src="<?php echo esc_url('https://wooslider.ir/theme-icons/'.$theme_icon); ?>" height="auto" style="width: <?php echo esc_attr($theme_icon_dims); ?>" ></div>
                <sub><?php if ($is_theme_supported) {echo esc_html__('We Support your Theme!', 'wooslider-bymdez'); }else echo esc_html__('Oops! Your theme is not supported', 'wooslider-bymdez') ?></sub>
                </div>

                <p class='helptip'><span></span><?php echo __('Contact plugin developer to add your current theme ASAP', 'wooslider-bymdez') ?></p>
                </p> 
        
                <hr>

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-nomainwidget"><?php echo __('Disable Main widget', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-nomainwidget" name="woosbymdz-nomainwidget" <?php echo esc_attr($nomain_widget); ?>>
                <sub><?php echo __('Do Not activate this if you want to use the main products slider elementor widget', 'wooslider-bymdez') ?></sub>
                </div>

                <p class='helptip'><span></span><?php echo __('This option will be usefull for non-woocomerce web sites who want to use other widgets of this plugin', 'wooslider-bymdez') ?></p>
                </p>

                <hr>

                <?php if($has_elementor): ?>

                    <p class='helptip'><span></span><?php echo __('In which part are Elementor widgets located? ', 'wooslider-bymdez') ?></p>
                    </p>

                    <p class='helptip'><span></span><?php echo __('Elementor widgets of this plugin are located in the WooCommerce section and you can separate it from the WooCommerce section using the option below.', 'wooslider-bymdez') ?></p>
                    </p>

                    <p>
                        <a href="<?php echo esc_url($example_img_1); ?>" target="_blank"><span class="woosbymdz-btn"><?php echo esc_html__('Help image', 'wooslider-bymdez') ?></span></a>
                    </p>

                    <hr>

                <?php endif; ?>

                <?php if($has_elementor): ?>
                    <p>
                    <div style="align-items: center;" class="custom-select div-inline">
                    <label for="woosbymdz-addnewwidgets-category"><?php echo __('Add new widgets-category', 'wooslider-bymdez') ?></label>
                    <input type="checkbox" id="woosbymdz-addnewwidgets-category" name="woosbymdz-addnewwidgets-category" <?php echo esc_attr($widgets_category); ?>>
                    <sub><?php echo __('Use this option if you have issues with finding this plugin Elementor widgets inside the own-woocomerece widgets-category', 'wooslider-bymdez') ?></sub>
                    </div>

                    <p>
                        <a href="<?php echo esc_url($example_img_2); ?>" target="_blank"><span class="woosbymdz-btn"><?php echo esc_html__('Help image', 'wooslider-bymdez') ?></span></a>
                    </p>
                    

                <?php endif; ?>

                <?php if($nomain_widget != 'checked')://Add this if no main widget is deactive ! ?>

                    <hr>

                    <p>
                        <div style="align-items: center;" class="custom-select div-inline">
                            <label for="woosbymdz-use-custtemp-procards"><?php echo __('Custom templates for Product-Cards', 'wooslider-bymdez') ?></label>
                            <input type="checkbox" id="woosbymdz-use-custtemp-procards" name="woosbymdz-use-custtemp-procards" <?php echo esc_attr($use_custom_procards); ?>>
                            <sub><?php echo __('If you dont use this option or dont know what it does, dont activate it', 'wooslider-bymdez') ?></sub>
                        </div>
                    </p>

                    <p class='helptip'><span></span><?php echo __('Activating this option adds additional codes to the main CSS file of the plugin, which is used to display product-cards in different templates.', 'wooslider-bymdez') ?></p>
                <?php endif; ?>

                <div style="margin-top: 55px;" class="tuts-btn-con text-center"><a href="<?php echo esc_url($learn_link); ?>" target="_blank"><span class="woosbymdz-btn"><?php echo __("Plugin Tutorials", 'wooslider-bymdez'); ?></span></a></div>

                </div>
            </fieldset>

            <?php if($nomain_widget != 'checked')://Add this if no main widget is deactive ! ?>
            <fieldset closed="yes" numb="2">
                <legend style="min-width: 200px;"><?php echo __('Product-Widget General Settings', 'wooslider-bymdez') ?></legend>

                <div class="acordion-btn"><span class="dashicons dashicons-plus" title="<?php echo __('Click to open', 'wooslider-bymdez') ?>"></span></div>
                <div class="acordion">

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-loading-time"><?php echo __('Sliders first time load duration', 'wooslider-bymdez') ?></label>
                <input type="number" name="woosbymdz-loading-time" id="woosbymdz-loading-time" value="<?php echo get_option('woosbymdz_loadingtime') ?>">
                <sub><?php echo __('1000 mili-seconds is equal to 1 second', 'wooslider-bymdez') ?></sub>
                </div>
                </p>
                
                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-loading-effect"><?php echo __('Loading Effects', 'wooslider-bymdez') ?></label>
                <select id="woosbymdz-loading-effect" name="woosbymdz-loading-effect" style="flex: 100%; margin: -7px 10px">
                <option value="effect-1"><?php echo __('Default', 'wooslider-bymdez') ?></option>
                <option value="effect-2"><?php echo __('Retro-Violet', 'wooslider-bymdez') ?></option>
                <option value="effect-3"><?php echo __('Retro-Green', 'wooslider-bymdez') ?></option>
                <option value="effect-4"><?php echo __('Fairy', 'wooslider-bymdez') ?></option>
                <option value="effect-5"><?php echo __('Heavy', 'wooslider-bymdez') ?></option>
                <option value="effect-6"><?php echo __('Instagram', 'wooslider-bymdez') ?></option>
                <option value="effect-7"><?php echo __('Fast', 'wooslider-bymdez') ?></option>
                <option value="effect-8"><?php echo __('Wooslider', 'wooslider-bymdez') ?></option>
                <option value="effect-9"><?php echo __('Rotating', 'wooslider-bymdez') ?></option>
                <option value="effect-10"><?php echo __('Pulser', 'wooslider-bymdez') ?></option>
                <option value="effect-11"><?php echo __('Pink', 'wooslider-bymdez') ?></option>
                <option value="effect-12"><?php echo __('Cubes', 'wooslider-bymdez') ?></option>
                <option value="effect-14"><?php echo __('Speed-Bump', 'wooslider-bymdez') ?></option>
                <option value="effect-15"><?php echo __('Music', 'wooslider-bymdez') ?></option>
                <option value="effect-13"><?php echo __('Your-loading', 'wooslider-bymdez') ?></option>
                </select>
                </div>
                </p> 

                <p>
                <div style="" class="slidersloadeffectpreview-wrapper expanded">
                    <div id="slidersloadeffectpreview-div" class="slidersloadeffectpreview" expanded="1"></div>
                </div>
                </p> 

                <div id="woosbymdz-your-loading-fields" class="mdz-hidden trans-base"><!--Hidden-Con-->

                    <p class="justflex" style="align-items: center;">
                        <label for="woosbymdz-ppage-customload-img"><?php echo __('Custom Image Url: ', 'wooslider-bymdez') ?></label>
                        <input type="text" id="woosbymdz-ppage-customload-img" style="width: 500px;" name="woosbymdz-ppage-customload-img" value="<?php echo esc_attr($sliders_loadeffect_img); ?>">
                    </p>

                    <p class="justflex" style="align-items: center;">
                        <label for="woosbymdz-ppage-customload-imgw"><?php echo __('Image Width: ', 'wooslider-bymdez') ?></label>
                        <input type="number" id="woosbymdz-ppage-customload-imgw" style="width: 155px;" name="woosbymdz-ppage-customload-imgw" value="<?php echo esc_attr($sliders_loadeffect_imgw); ?>" placeholder="<?php echo __('default: 200px', 'wooslider-bymdez'); ?>">
                    </p>

                    <p class="justflex" style="align-items: center;">
                        <label for="woosbymdz-ppage-customload-imgh"><?php echo __('Image Height: ', 'wooslider-bymdez') ?></label>
                        <input type="number" id="woosbymdz-ppage-customload-imgh" style="width: 155px;" name="woosbymdz-ppage-customload-imgh" value="<?php echo esc_attr($sliders_loadeffect_imgh); ?>" placeholder="<?php echo __('default: 150px', 'wooslider-bymdez'); ?>">
                    </p>

                    <p class="justflex" style="align-items: center;">
                        <label for="woosbymdz-ppage-customload-imgb"><?php echo __('Background Color(hex-code): ', 'wooslider-bymdez') ?></label>
                        <input type="text" id="woosbymdz-ppage-customload-imgb" style="width: 235px;" name="woosbymdz-ppage-customload-imgb" value="<?php echo esc_attr($sliders_loadeffect_imgb); ?>" placeholder="<?php echo __('default: #f1f2f3', 'wooslider-bymdez'); ?>">
                    </p>

                    <p class='helptip'><span></span><?php echo __('Enter an image URL here to use that as a loadign logo/image or animation, has no live preview', 'wooslider-bymdez') ?></p> 

                    <p class='helptip'><span></span><?php echo __('Suggestion: try to use an SVG animation file or download one from the https://loading.io/', 'wooslider-bymdez') ?></p> 

                    <div style="margin-top: 15px;margin-bottom: 30px;"><a href="http://wooslider.ir/theme-icons/Wedges-3s-200px.svg" target="_blank"><span class="woosbymdz-btn"><?php echo __("Download Example Image", 'wooslider-bymdez'); ?></span></a></div>

                </div><!--Endof Hidden-Con-->

                <hr>
                <div style="margin-bottom: 45px" id="midsection-products" class="div-center1 midsection-con"><h4><?php echo __('--Products--', 'wooslider-bymdez'); ?></h4></div>

                <p class="justflex">
                <input type="checkbox" id="woosbymdz-products-nobg" name="woosbymdz-products-nobg" <?php echo esc_attr($products_nobg); ?>>
                <label for="woosbymdz-products-nobg"><?php echo __('Fade products background', 'wooslider-bymdez') ?><sub><?php echo __('Activate this to have all products within all sliders to have transparent background', 'wooslider-bymdez') ?></sub></label>
                </p> 
                
                <p class='helptip'><span></span><?php echo __('Options inside this section is globally effected', 'wooslider-bymdez') ?></p> 

                <hr>

                <?php if($has_elementor): ?>
                    <p class="justflex">
                        <input type="checkbox" id="woosbymdz-sliders-noeffect" name="woosbymdz-sliders-noeffect" <?php echo esc_attr($swiper_noeff); ?>>
                        <label for="woosbymdz-sliders-noeffect"><?php echo __('Disable Swiper-Slider in elementor-editor', 'wooslider-bymdez') ?><sub><?php echo __('If you use a lot of sliders on your Elementor page and you feel excessive lag and slowness in the Elementor editor, enable this option.', 'wooslider-bymdez') ?></sub></label>
                    </p> 
                    
                    <p class='helptip'><span></span><?php echo __('Actually, this option disables the slider object immediately after it is created', 'wooslider-bymdez') ?></p> 

                    <hr>
                

                    <p class="justflex">
                        <input type="checkbox" id="woosbymdz-allcss-impo" name="woosbymdz-allcss-impo" <?php echo esc_attr($allcss_impo); ?>>
                        <label for="woosbymdz-allcss-impo"><?php echo __('Use Important for all elementor css styles', 'wooslider-bymdez') ?><sub><?php echo __('This option applies all the CSS codes in the style tab of the products-widget with the !important property of the CSS language', 'wooslider-bymdez') ?></sub></label>
                    </p> 
                    
                    <p class='helptip'><span></span><?php echo __('If some of your styles are not applied in the Elementor editor, use this option', 'wooslider-bymdez') ?></p> 

                    <p class="justflex">
                        <input type="checkbox" id="woosbymdz-procss-impo" name="woosbymdz-procss-impo" <?php echo esc_attr($procss_impo); ?>>
                        <label for="woosbymdz-procss-impo"><?php echo __('Use Important for products-section css styles', 'wooslider-bymdez') ?><sub><?php echo __('This option applies all the CSS codes in the style tab of the products-widget with the !important property of the CSS language', 'wooslider-bymdez') ?></sub></label>
                    </p> 
                    
                    <p class='helptip'><span></span><?php echo __('If you dont want all parts of the widget style tab to use the !important attribute and you only need this attribute for the products part, use this option and disable the top option', 'wooslider-bymdez') ?></p> 

                    <p class='helptip'><span></span><?php echo __('Note: In order to optimize the page design, the less !important is used in CSS, the better the result will be.', 'wooslider-bymdez') ?></p> 

                    <hr>
                <?php endif; ?>

                <p class="justflex">
                    <input type="checkbox" id="woosbymdz-res-titles" name="woosbymdz-res-titles" <?php echo esc_attr($slider_res_title); ?>>
                    <label for="woosbymdz-res-titles"><?php echo __('Restrict product titles', 'wooslider-bymdez') ?><sub><?php echo __('If your product titles are long and longer, use this option to make them uniform in height.', 'wooslider-bymdez') ?></sub></label>
                </p> 

                <p>
                    <div style="align-items: center;" class="custom-select div-inline">
                    <label for="woosbymdz-res-titles-max-lines"><?php echo __('Maximum number of lines: ', 'wooslider-bymdez') ?></label>
                    <input type="number" name="woosbymdz-res-titles-max-lines" id="woosbymdz-res-titles-max-lines" value="<?php echo esc_attr($pros_restitle_maxlines); ?>">
                    <sub><?php echo __('Default: 2 (2 lines)', 'wooslider-bymdez') ?></sub>
                    </div>
                </p>
                
                <p class='helptip'><span></span><?php echo __('Note: Using this option is very useful in making the product block the same height inside the slider', 'wooslider-bymdez') ?></p> 
                

                </div>
            </fieldset>
            <?php endif; ?>

            <fieldset closed="yes" numb="3">
                <legend><?php echo __('Extra Breakpoints', 'wooslider-bymdez') ?></legend>

                <div class="acordion-btn"><span class="dashicons dashicons-plus" title="<?php echo __('Click to open', 'wooslider-bymdez') ?>"></span></div>
                <div class="acordion">

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-mob2-breakpoint"><?php echo __('Very slim mobiles breakpoint', 'wooslider-bymdez') ?></label>
                <input type="number" name="woosbymdz-mob2-breakpoint" id="woosbymdz-mob2-breakpoint" value="<?php echo get_option('woosbymdz_mob2breakpoint') ?>">
                <sub><?php echo __('Like Galaxy-Fold device (below 300px)', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-wide-breakpoint"><?php echo __('Wide monitors breakpoint', 'wooslider-bymdez') ?></label>
                <input type="number" name="woosbymdz-wide-breakpoint" id="woosbymdz-wide-breakpoint" value="<?php echo get_option('woosbymdz_widebreakpoint') ?>">
                </div>
                </p>

                <p class='helptip'><span></span><?php echo __('Other breakpoints like desktop,tablet and etc no need to re-config, this plugin will use elementor self breakpoints', 'wooslider-bymdez') ?></p>

                </div>
            </fieldset>

            <fieldset closed="yes" numb="4">
                <legend><?php echo __('Extra CSS', 'wooslider-bymdez') ?></legend>

                <div class="acordion-btn"><span class="dashicons dashicons-plus" title="<?php echo __('Click to open', 'wooslider-bymdez') ?>"></span></div>
                <div class="acordion">

                <p class="justflex">
                <input type="checkbox" id="woosbymdz-customcss-active" name="woosbymdz-customcss-active" <?php echo esc_attr($is_extracss); ?>>
                <label for="woosbymdz-customcss-active"><?php echo __('Activate', 'wooslider-bymdez') ?><sub><?php echo __('Activate this to use your css codes Globally', 'wooslider-bymdez') ?></sub></label>
                </p>  
                <div class="css-code-con"><textarea name="woosbymdz-csscode" id="woosbymdz-csscode" placeholder=".woosbymdz-carousel-con .swiper-slide {  /*...your css code here...*/  }"><?php echo get_option('woosbymdz_customcss'); ?></textarea></div>

                <div class="div-inline" style="justify-content: center;"> 
                <div class="nightmode-btn" style="margin-top: 15px;"><span><img class="nightmode-btn-img1" src="<?php echo esc_url(WOOSBYMDZ_URL . 'admin/img/extra/daytinight.svg'); ?>" width="99px"></span><span class="nightbtn-arrow"><img src="<?php echo esc_url(WOOSBYMDZ_URL . 'admin/img/extra/arrow-btn.svg'); ?>" width="23px" class="nightmode-btn-img2"></span></div></div>

                <p class='helptip'><span></span><?php echo __('To see all of your changes use CTRL+F5 to ensure your browser local cache is empty', 'wooslider-bymdez') ?></p>
                <p class='helptip'><span></span><?php echo __('If you use any cache plugins, please purge all the cached files via your cache plugin setting page to see your changes', 'wooslider-bymdez') ?></p>
                <p class='helptip'><span></span><?php echo __('To change the apearance of the products inside the Sliders, USE these CSS selectors below', 'wooslider-bymdez') ?></p>

                <div class="help-csstxt-con <?php if(!is_rtl()){echo 'help-csstxt-con-ltr';} ?>">
                <p style="text-align: right;direction: ltr;">.woosbymdz-carousel-con .swiper-slide</p>
                <p style="text-align: right;direction: ltr;">.woosbymdz-carousel-con .swiper-slide div</p>
                <p style="text-align: right;direction: ltr;">.woosbymdz-carousel-con .swiper-slide li</p>
                </div>
        
                </div>
            </fieldset>

            <fieldset closed="yes" numb="5">
                <legend><?php echo __('Extra JS', 'wooslider-bymdez') ?></legend>

                <div class="acordion-btn"><span class="dashicons dashicons-plus" title="<?php echo __('Click to open', 'wooslider-bymdez') ?>"></span></div>
                <div class="acordion">

                <p class="justflex">
                <input type="checkbox" id="woosbymdz-customjs-active" name="woosbymdz-customjs-active" <?php echo esc_attr($is_extrajs); ?>>
                <label for="woosbymdz-customjs-active"><?php echo __('Activate', 'wooslider-bymdez') ?><sub><?php echo __('Activate this to use your custom JS codes inside plugin js-file', 'wooslider-bymdez') ?></sub></label>
                </p>  
                <div class="css-code-con"><textarea name="woosbymdz-jscode" id="woosbymdz-jscode" placeholder="alert('My Custom JS code!');"><?php echo get_option('woosbymdz_customjs'); ?></textarea></div>

                <div class="div-inline" style="justify-content: center;"> 
                <div class="nightmode-btn" style="margin-top: 15px;"><span><img class="nightmode-btn-img1" src="<?php echo esc_url(WOOSBYMDZ_URL . 'admin/img/extra/daytinight.svg'); ?>" width="99px"></span><span class="nightbtn-arrow"><img src="<?php echo esc_url(WOOSBYMDZ_URL . 'admin/img/extra/arrow-btn.svg'); ?>" width="23px" class="nightmode-btn-img2"></span></div></div>

                <p class='helptip'><span></span><?php echo __('To see all of your changes use CTRL+F5 to ensure your browser local cache is empty', 'wooslider-bymdez') ?></p>
                <p class='helptip'><span></span><?php echo __('If you use any cache plugins, please purge all the cached files via your cache plugin setting page to see your changes', 'wooslider-bymdez') ?></p>

                <?php if($nomain_widget != 'checked') {//Add this if no main widget is deactive ! ?>
                <hr>

                <div style="margin-bottom: 45px" id="midsection-jsafterajax" class="div-center1 midsection-con"><h4><?php echo __('--JS Code after Ajax--', 'wooslider-bymdez'); ?></h4></div>

                <p class="justflex">
                <input type="checkbox" id="woosbymdz-ajaxjs-active" name="woosbymdz-ajaxjs-active" <?php echo esc_attr($is_ajaxjs); ?>>
                <label for="woosbymdz-ajaxjs-active"><?php echo __('Activate', 'wooslider-bymdez') ?><sub><?php echo __('Activate this to execute your custom JS codes after changing products inside the slider', 'wooslider-bymdez') ?></sub></label>
                </p>  
                <div class="css-code-con"><textarea name="woosbymdz-ajaxjscode" id="woosbymdz-ajaxjscode" placeholder="alert('Products Changed!');"><?php echo get_option('woosbymdz_ajaxjs'); ?></textarea></div>

                <div class="div-inline" style="justify-content: center;"> 
                <div class="nightmode-btn" style="margin-top: 15px;"><span><img class="nightmode-btn-img1" src="<?php echo esc_url(WOOSBYMDZ_URL . 'admin/img/extra/daytinight.svg'); ?>" width="99px"></span><span class="nightbtn-arrow"><img src="<?php echo esc_url(WOOSBYMDZ_URL . 'admin/img/extra/arrow-btn.svg'); ?>" width="23px" class="nightmode-btn-img2"></span></div></div>

                <p class='helptip'><span></span><?php echo __('When user change the products inside the Main-Widget slider by slider header buttons (after Ajax call) above JS codes will trigger', 'wooslider-bymdez') ?></p>

                <p class='helptip'><span></span><?php echo __('To see all of your changes use CTRL+F5 to ensure your browser local cache is empty', 'wooslider-bymdez') ?></p>
                <p class='helptip'><span></span><?php echo __('If you use any cache plugins, please purge all the cached files via your cache plugin setting page to see your changes', 'wooslider-bymdez') ?></p>
                <?php } ?>

                </div>

            </fieldset>

            <fieldset closed="yes" numb="6">
                <legend><?php echo __('Advanced', 'wooslider-bymdez') ?></legend>

                <div class="acordion-btn"><span class="dashicons dashicons-plus" title="<?php echo __('Click to open', 'wooslider-bymdez') ?>"></span></div>
                <div class="acordion">

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-globalimport"><?php echo __('Assets Global Import', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-globalimport" name="woosbymdz-globalimport" <?php echo esc_attr($global_import); ?>>
                <sub><?php echo __('Adds all the CSS and JS files of the plugin to all the front pages of your site', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p class='helptip'><span></span><?php echo __('Note: If you have used the widget of this plugin in a global element such as site header or footer, activate this option', 'wooslider-bymdez') ?></p> 

                <hr>

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-importjq"><?php echo __('Import jQuery?', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-importjq" name="woosbymdz-importjq" <?php echo esc_attr($import_jq); ?>>
                <sub><?php echo __('Usefull when your theme have not imported the jQuery Library into the front-side preview of your site', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <hr>

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-donimport-swiper"><?php echo __('Dont add Swiper-js Library', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-donimport-swiper" name="woosbymdz-donimport-swiper" <?php echo esc_attr($dont_import_swiper); ?>>
                <sub><?php echo __('Usefull when your theme or other plugins have imported Swiper js CSS & JS files globally to your site', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <hr>

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-oldthemedebuger"><?php echo __('Old themes dubuger', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-oldthemedebuger" name="woosbymdz-oldthemedebuger" <?php echo esc_attr($old_themes_debuger); ?>>
                <sub><?php echo __('Do Not activate this if you have no problem while using the plugin', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p class='helptip'><span></span><?php echo __('Useful when your current theme have imported the jQuery library at the end of "BODY" tag', 'wooslider-bymdez') ?></p> 

                <p class='helptip'><span></span><?php echo __('Beware: this item will disable this widget live-preview on the elementor editor', 'wooslider-bymdez') ?></p> 

                <?php if($nomain_widget != 'checked') {//Add this if no main widget is deactive ! ?>
                <hr>

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-productstracker"><?php echo __('set recently viewd products', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-productstracker" name="woosbymdz-productstracker" <?php echo esc_attr($last_seen_cookie); ?>>
                <sub><?php echo __('when activated sets a session type cookie to users browser to track last recently seen products', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p class='helptip'><span></span><?php echo __('Session type cookies will perish upon closing the browser', 'wooslider-bymdez') ?></p> 

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-productstracker-js"><?php echo __('Wp-Cache Compatibility for Cookies', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-productstracker-js" name="woosbymdz-productstracker-js" <?php echo esc_attr($last_seen_cookie_js); ?>>
                <sub><?php echo __('If you use any wordpress cache plugins like Wp-Rocket, Activate this to set the user last seen products via JS instead of PHP', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p class='helptip'><span></span><?php echo __('when activated sets recently viewed cookies by javascript instead PHP therefore helps to avoid any conflicts with wordpress cache plugins like Wp-Rocket', 'wooslider-bymdez') ?></p> 

                <p class='helptip'><span></span><?php echo __('To use this Advanced feature, you must also activate `set recently viewd products` (the above option)', 'wooslider-bymdez') ?></p> 

                <p class='helptip'><span></span><?php echo __('Enabling this option will add a single JS file to the front-end of your web-site with `woosbymdz-cookies-js` ID', 'wooslider-bymdez') ?></p> 

                <?php }//Endof if ?>

                <hr>

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-nobrowsercache"><?php echo __('Prevent css & js files from user browser caching', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-nobrowsercache" name="woosbymdz-nobrowsercache" <?php echo esc_attr($is_browsernocache); ?>>
                <sub><?php echo __('when activated, prevent the front css and js files from being cached in all users browser', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p class='helptip'><span></span><?php echo __('This option will decrease the load time of your web-site', 'wooslider-bymdez') ?></p>

                <?php if($nomain_widget != 'checked') {//Add this if no main widget is deactive ! ?>

                <hr>
                    
                <p>
                    <div style="align-items: center;" class="custom-select div-inline">
                        <label for="woosbymdz-noajaxrefchecker"><?php echo __('Disable Ajax-Refferer Checker', 'wooslider-bymdez') ?></label>
                        <input type="checkbox" id="woosbymdz-noajaxrefchecker" name="woosbymdz-noajaxrefchecker" <?php echo esc_attr($noajax_ref_checker); ?>>
                        <sub><?php echo __('Not recommended, but you can enable this option if the plugins front-end Ajax requests interfere with some cache and optimization plugins', 'wooslider-bymdez') ?></sub>
                    </div>
                </p>

                <hr>

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-ajaxreload-comp"><?php echo __('Re-Construct sliders with ajax reload compatibility', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-ajaxreload-comp" name="woosbymdz-ajaxreload-comp" <?php echo esc_attr($reload_ajax_comp); ?>>
                <sub><?php echo __('Activate only if you use wooslider shortcode inside ajax shop page like Woodmart ajax-shop page', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p style="color: #d75050;" class='helptip'><span></span><?php echo __('Warning: This option is under Developing, use with caution...', 'wooslider-bymdez') ?></p>

                <p class='helptip'><span></span><?php echo __('Do Not Enable this option if you have no errors and bugs while using this plugin. This option will re-construct sliders when an ajax request reloads the entire page or reload a section that contains the woo-slider and slider will not show the products any more', 'wooslider-bymdez') ?></p>

                <p><a href="<?php echo esc_url($ajax_reload_link); ?>" target="_blank"><?php echo __('More Help!', 'wooslider-bymdez'); ?></a></p>
                <?php }//Endof if ?>

                <?php if($nomain_widget != 'checked') {//Add this if no main widget is deactive ! ?>

                <hr>

                <p>
                <div style="margin-bottom: 45px" id="midsection-maincat-yoast" class="div-center1 midsection-con"><h4><?php echo __('--Related Products--', 'wooslider-bymdez'); ?></h4></div>

                <p>
                <div style="align-items: center;margin-top: 65px;" class="custom-select div-inline">
                <label for="woosbymdz-addtoproductpage"><?php echo __('Add Slider to Products-Page (Globally)', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-addtoproductpage" name="woosbymdz-addtoproductpage" <?php echo esc_attr($add_yoast_slider_to_productspage); ?>>
                <sub><?php echo __('Adds an Normal Slider at the end of products page with the Yoast main-category config', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p class='helptip'><span></span><?php echo __('Note: To use this option you must activate main-slider shortcode at the [ ]Shortcodes section in this page', 'wooslider-bymdez') ?></p>

                <p class='helptip'><span></span><?php echo __('Note: If you dont want to show products from yoast-main category or you dont have the yoast-seo plugin, you should use your custom short-code', 'wooslider-bymdez') ?></p>

                <p class='helptip'><span></span><?php echo __('Note: You can customize slider with the customized shortcode button at the below', 'wooslider-bymdez') ?></p>

                <hr>

                <p class="justflex" style="align-items: center;">
                <label for="woosbymdz-ppage-slidertitle"><?php echo __('Slider Title: ', 'wooslider-bymdez') ?></label>
                <input type="text" id="woosbymdz-ppage-slidertitle" style="width: 500px;" name="woosbymdz-ppage-slidertitle" value="<?php echo esc_attr($yoast_slider_title); ?>">
                </p>

                <p class='helptip'><span></span><?php echo __('HTML tags are accepted', 'wooslider-bymdez') ?></p>
                <p class='helptip'><span></span><?php echo __('Note: To change field direction, use Ctrl+Shift', 'wooslider-bymdez') ?></p>

                <p class="justflex">
                <input type="checkbox" id="woosbymdz-iscustom-shortcode" name="woosbymdz-iscustom-shortcode" <?php echo esc_attr($add_yoast_slider_is_customized_shortcode); ?>>
                <label for="woosbymdz-iscustom-shortcode"><?php echo __('Use Customized Shortcode: ', 'wooslider-bymdez') ?><sub><?php echo __('Activate this to use your own customized shortcode', 'wooslider-bymdez') ?></sub></label>
                </p> 

                <div style="min-height: 110px;" class="css-code-con"><textarea style="min-height: 110px;" name="woosbymdz-custom-shortcode" id="woosbymdz-custom-shortcode" placeholder="Write Down Shortcode Here Like: [woo_modern_slider cat=25 src=rand number=25] or [woo_modern_slider yoast_main_cat=yes number=10]"><?php echo get_option('woosbymdz_yoast_maincat_customizedshortcode'); ?></textarea></div>

                <div><a href="<?php echo esc_url($sh1_link); ?>" target="_blank"><?php echo __('How to use Shortcode ?', 'wooslider-bymdez') ?></a></div>

                <div class="div-inline" style="justify-content: center;"> 
                <div class="nightmode-btn" style="margin-top: 15px;"><span><img class="nightmode-btn-img1" src="<?php echo esc_url(WOOSBYMDZ_URL . 'admin/img/extra/daytinight.svg'); ?>" width="99px"></span><span class="nightbtn-arrow"><img src="<?php echo esc_url(WOOSBYMDZ_URL . 'admin/img/extra/arrow-btn.svg'); ?>" width="23px" class="nightmode-btn-img2"></span></div></div>


                <div style="align-items: center;margin-top: 65px;" class="custom-select div-inline">
                <label for="woosbymdz-yoast-propermalink"><?php echo __('Product permalinks:', 'wooslider-bymdez') ?></label>
                <input type="text" name="woosbymdz-yoast-propermalink" id="woosbymdz-yoast-propermalink" value="<?php echo get_option('woosbymdz_yoast_maincat_permalink') ?>">
                <a href="<?php echo esc_url(WOOSBYMDZ_URL.'admin/img/extra/yoas-help.webp'); ?>" target="_blank"><span class="woosbymdz-btn"><?php echo __('screen-shot', 'wooslider-bymdez'); ?></span></a>
                </div>

                <p class='helptip'><span></span><?php echo __('If you use Custom product permalink, please set this option', 'wooslider-bymdez') ?></p>

                <p class='helptip'><span></span><?php echo __('The products permalink will be used to detect front product pages and its default value is equal to `product`', 'wooslider-bymdez') ?></p>

                <p class='helptip'><span></span><?php echo __('Note: with this option You are able to use Product Main-Cat product source type in the Elementor editor panel or via Shortcode and it will use the Yoast plugin Primary-Category feature to fetch and show related products in the product pages, please see below screen-shots', 'wooslider-bymdez') ?></p>

                <div class="screenshots-3-con">
                    <a href="<?php echo esc_url($yoast_mc_sc_1); ?>" target="_blank"><span class="woosbymdz-btn"><?php echo __('screen-shot 1', 'wooslider-bymdez'); ?></span></a>
                    <a href="<?php echo esc_url($yoast_mc_sc_2); ?>" target="_blank"><span class="woosbymdz-btn"><?php echo __('screen-shot 2', 'wooslider-bymdez'); ?></span></a>
                    <a href="<?php echo esc_url($yoast_mc_sc_3); ?>" target="_blank"><span class="woosbymdz-btn"><?php echo __('screen-shot 3', 'wooslider-bymdez'); ?></span></a>
                </div>

                <p class='helptip'><span></span><?php echo __('Note: To use this Advanced feature you must have Yoast-Seo Plugin (Yoast-Premium is not needed)', 'wooslider-bymdez') ?></p>
                </p>

                <p><a href="<?php echo esc_url($yoast_maincat_link); ?>" target="_blank"><?php echo __('More Help!', 'wooslider-bymdez'); ?></a></p>
                <?php }//End of if ?>

                </div>
            </fieldset>

            <fieldset closed="yes" numb="7">
                <legend><?php echo __('[   ] Short-Codes', 'wooslider-bymdez') ?></legend>

                <div class="acordion-btn"><span class="dashicons dashicons-plus" title="<?php echo __('Click to open', 'wooslider-bymdez') ?>"></span></div>
                <div class="acordion">

                <p>
                    <a href="<?php echo esc_url($sh_gen_plg_dl_link); ?>" target="_blank"><span class="woosbymdz-btn">
                        <?php echo esc_html__('Download Short-Code Generator Plugin', 'wooslider-bymdez'); ?>
                    </span></a>
                </p>
                <p class='helptip'><span></span><?php echo __('What is short-code generator? With the help of the auxiliary short-code generator plugin, you can easily and quickly generate the short-code you need and use WooSlider plugin in any page builder!', 'wooslider-bymdez') ?></p> 

                <?php if($nomain_widget != 'checked') {//Add this if no main widget is deactive ! ?>
                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-shortcode1"><?php echo __('Modern slider short code', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-shortcode1" name="woosbymdz-shortcode1" <?php echo esc_attr($shortcode1); ?>>
                <sub><?php echo __('Do Not activate this if you do not use the short-code of this widget', 'wooslider-bymdez') ?></sub>
                </div>
                </p>
                

                <p class='helptip'><span></span><?php echo __('Useful if you dont use Elementor page builder', 'wooslider-bymdez') ?></p> 
                <p class='helptip'><span></span><?php echo __('Base short-code: [woo_modern_slider]', 'wooslider-bymdez') ?></p>
                <p><a href="<?php echo esc_url($sh1_link); ?>" target="_blank"><?php echo __('How to use main short-code? (documents)', 'wooslider-bymdez'); ?></a></p>

                <div class="copier-con"><span class="cursor-pointer" onclick="woosbymdz_copycode('[woo_modern_slider]')"><?php echo __('Copy', 'wooslider-bymdez'); ?></span></div>

                <hr>
                <?php }//Endof is ?>

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-shortcode2"><?php echo __('Cats Slider Shortcode', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-shortcode2" name="woosbymdz-shortcode2" <?php echo esc_attr($shortcode2); ?>>
                <sub><?php echo __('Do Not activate this if you do not use the short-code of the category sliders widget', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p class='helptip'><span></span><?php echo __('Useful if you dont use Elementor page builder', 'wooslider-bymdez') ?></p> 
                <p class='helptip'><span></span><?php echo __('Base short-code: [woo_cats_slider]', 'wooslider-bymdez') ?></p>
                <p><a href="<?php echo esc_url($sh2_link); ?>" target="_blank"><?php echo __('How to use cat-slider short-code? (documents)', 'wooslider-bymdez'); ?></a></p>

                <div class="copier-con"><span class="cursor-pointer" onclick="woosbymdz_copycode('[woo_cats_slider]')"><?php echo __('Copy', 'wooslider-bymdez'); ?></span></div>

                </div>
            </fieldset>

            <?php if($has_elementor): ?>
            <fieldset closed="yes" numb="8">
                <legend><?php echo __('Extra Widgets', 'wooslider-bymdez') ?></legend>

                <div class="acordion-btn"><span class="dashicons dashicons-plus" title="<?php echo __('Click to open', 'wooslider-bymdez') ?>"></span></div>
                <div class="acordion">

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-extrawidget1"><?php echo __('Category slider', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-extrawidget1" name="woosbymdz-extrawidget1" <?php echo esc_attr($extra_widget1); ?>>
                <sub><?php echo __('Do Not activate this if you do not use the category widget of this plugin', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p>
                    <a href="<?php echo esc_url($example_img_4); ?>" target="_blank"><span class="woosbymdz-btn"><?php echo esc_html__('Help image', 'wooslider-bymdez') ?></span></a>
                </p>

                <p class='helptip'><span></span><?php echo __('Tip: With the help of this extremely useful widget, you can design all kinds of banner, brand, logo and category sliders in a normal and graded way!', 'wooslider-bymdez') ?></p>
                </p>

                <hr>

                
                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-extrawidget2"><?php echo __('Loop Builder Slider (new)', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-extrawidget2" name="woosbymdz-extrawidget2" <?php echo esc_attr($extra_widget2); ?>>
                <sub><?php echo __('Do Not activate this if you do not use the category widget of this plugin, this widget does-not support short-code and only works with Elementor', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p>
                    <a href="<?php echo esc_url($example_img_3); ?>" target="_blank"><span class="woosbymdz-btn"><?php echo esc_html__('Help image', 'wooslider-bymdez') ?></span></a>
                </p>

                <div>
                    <label class="disabled-btn">
                        <span><?php echo __('Loop Widget Status:', 'wooslider-bymdez') ?></span>
                        <input class="disabled-btn" type="text" value="<?php echo $loop_widget_status_txt; ?>">
                    </label> 
                </div>

                <p class='helptip'><span></span><?php echo __('Note: The activation of this option requires the Elementor Pro plugin', 'wooslider-bymdez') ?></p>
                </p>

                <p>
                    <a href="<?php echo esc_url($buy_elempro_aff_link); ?>" target="_blank"><span class="woosbymdz-btn" style="background-color: #008d3d;"><?php echo esc_html__('Buy Elementor-Pro', 'wooslider-bymdez') ?></span></a>
                </p>

                <p class='helptip'><span></span><?php echo __('Important note: To activate and use this option, the Wooslider plugin automatically has to edit one of the internal PHP files of the Elementor Pro plugin in order to add its own Loop Builder widget.', 'wooslider-bymdez') ?></p>
                </p>

                <p class='helptip'><span></span><?php echo sprintf(esc_html__('This option has been tested with Elementor Pro version %s, and if your Elementor Pro plugin version is older or newer, please use this option with more caution (we recommend taking a full backup through the host)', 'wooslider-bymdez'), WOOSBYMDZ_ELEMPRO_TESTED_VER); ?></p>
                </p>

                <p class='helptip'><span></span><?php echo sprintf(esc_html__('Your Elementor-Pro Version: %s', 'wooslider-bymdez'), $elem_pro_ver); ?></p>
                </p>

                <div id="rollback-elempro-file" class="woosbymdz-btn" style="background-color: #915252;">
                    <?php echo esc_html__('Restore the file', 'wooslider-bymdez') ?>
                </div>

                <p class='helptip'><span></span><?php echo __('If you want the content of the edited file to return to its initial and default state, use the button above', 'wooslider-bymdez') ?></p>
                </p>


                    
                

            </div>
            </fieldset>
            <?php endif; ?>

            <?php if($nomain_widget != 'checked')://Add this if no main widget is deactive ! ?>
            <fieldset closed="yes" numb="9">
                <legend><?php echo __('Charged products', 'wooslider-bymdez') ?></legend>

                <div class="acordion-btn"><span class="dashicons dashicons-plus" title="<?php echo __('Click to open', 'wooslider-bymdez') ?>"></span></div>
                <div class="acordion">

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-charged-pros"><?php echo __('Charged Products System', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-charged-pros" name="woosbymdz-charged-pros" <?php echo esc_attr($use_charged); ?>>
                <sub><?php echo __('Activate to use this new & advanced system, PLEASE read the below Help-Tips before activating this', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p class='helptip'><span></span><?php echo __('Note: by activating this option, the system of charged products will be activated for all the products in your store, and whenever the inventory of your out-of-stock products is added, this system will automatically charge the product, and you can show these types of products inside the sliders of this plugin.', 'wooslider-bymdez') ?></p>
                </p>

                <p class='helptip'><span></span><?php echo __('Note: This system works professionally on simple and variable products and is even compatible with accounting programs in the form of API.', 'wooslider-bymdez') ?></p>
                </p>
                <p class='helptip'><span></span><?php echo __('Note: The meta information added on the products will be completely removed from all the products on your site if you remove this plugin, so dont worry about anything!', 'wooslider-bymdez') ?></p>
                </p>

                <p class='helptip'><span></span><?php echo __('Note: In order to avoid any possible bugs with the activation of the charged products system, the product metabox (product admin pages) will be completely disabled', 'wooslider-bymdez') ?></p>
                </p>

                <div style="margin-bottom: 55px;">
                    <a href="<?php echo esc_url($meta_box_img); ?>" target="_blank"><span class="woosbymdz-btn"><?php echo __('What is products meta-box?', 'wooslider-bymdez'); ?></span></a>
                </div>

                <div>
                    <label class="disabled-btn">
                        <span><?php echo __('Products Meta Status:', 'wooslider-bymdez') ?></span>
                        <input class="disabled-btn" type="text" value="<?php echo $hs_uset_stats; ?>">
                    </label> 

                    <p style="margin: 16px 0px;"></p>

                    <label>
                        <span><?php echo __('Reset to Default', 'wooslider-bymdez') ?></span>
                        <input type="checkbox" id="woosbymdz-charged-chek-reset" name="woosbymdz-charged-chek-reset">
                    </label> 
                    
                    <p class='helptip'><span></span><?php echo __('To perform the meta-setting operation, just activate the charged products option and press the save settings button', 'wooslider-bymdez') ?></p>
                </div>

                <hr>

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-charged-cancdisc"><?php echo __('Canceling the product discount after charging', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-charged-cancdisc" name="woosbymdz-charged-cancdisc" <?php echo esc_attr($charged_cancel_disc); ?>>
                <sub><?php echo __('Whenever a product is charged, if it is discounted, it will be removed from the discount', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-charged-isaddcat"><?php echo __('Add product-category after charging', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-charged-isaddcat" name="woosbymdz-charged-isaddcat" <?php echo esc_attr($charged_is_addcat); ?>>
                <sub><?php echo __('You can specify a product category that will be added to the products after they are charged', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <div style="align-items: center;margin-top: 65px;" class="custom-select div-inline">
                <label for="woosbymdz-charged-addcat"><?php echo __('Product-Category ID:', 'wooslider-bymdez') ?></label>
                <input type="number" name="woosbymdz-charged-addcat" id="woosbymdz-charged-addcat" value="<?php echo get_option('woosbymdz_charged_addcat') ?>" placeholder="<?php echo __('Enter a single number', 'wooslider-bymdez'); ?>">

                <a href="<?php echo esc_url(WOOSBYMDZ_URL.'admin/img/extra/woop-id.webp'); ?>" target="_blank"><span class="woosbymdz-btn"><?php echo __('Where is ID?', 'wooslider-bymdez'); ?></span></a>
                </div>

                <p class='helptip'><span></span><?php echo __('Note: In fact, WooCommerce category ID is a simple number', 'wooslider-bymdez') ?></p>

                <hr>

                <div style="margin-bottom: 45px" id="midsection-chargeds" class="div-center1 midsection-con"><h4><?php echo __('--Chrage Controller Button--', 'wooslider-bymdez'); ?></h4></div>

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-charged-donshowbtn"><?php echo __('Do not Show the Button', 'wooslider-bymdez') ?></label>
                <input type="checkbox" id="woosbymdz-charged-donshowbtn" name="woosbymdz-charged-donshowbtn" <?php echo esc_attr($charged_don_btn); ?>>
                <sub><?php echo __('By means of this button, you can manually change the charged state of the products on the products page, so do not activate this option!', 'wooslider-bymdez') ?></sub>
                </div>
                </p>

                <p>
                <div style="align-items: center;" class="custom-select div-inline">
                <label for="woosbymdz-charged-btnpos"><?php echo __('Position of the button on the product page', 'wooslider-bymdez') ?></label>
                <select id="woosbymdz-charged-btnpos" name="woosbymdz-charged-btnpos" style="flex: 100%; margin: -7px 10px" active-opt="<?php echo esc_attr($charged_btn_pos); ?>">
                <option value="top"><?php echo __('Top-Page (recommended)', 'wooslider-bymdez') ?></option>
                <option value="mid"><?php echo __('Middle', 'wooslider-bymdez') ?></option>
                <option value="bottom"><?php echo __('Bottom', 'wooslider-bymdez') ?></option>
                </select>
                </div>
                </p> 

                <p class='helptip'><span></span><?php echo __('Note: The functionallity of the controller button of the charged state of the products is in the form of an Ajax, and after changing it, you do not need to save the product.', 'wooslider-bymdez') ?></p>



            </div>
            </fieldset>
            <?php endif; ?>


            <?php if($has_elementor): ?>
            <fieldset closed="yes" numb="10">
                <legend><?php echo __('Elementor', 'wooslider-bymdez') ?></legend>

                <div class="acordion-btn"><span class="dashicons dashicons-plus" title="<?php echo __('Click to open', 'wooslider-bymdez') ?>"></span></div>
                <div class="acordion">

                <p>
                    <div style="align-items: center;" class="custom-select div-inline">
                    <label for="woosbymdz-elem-dis-dynatags"><?php echo __('Disable Wooslider DynamicTags', 'wooslider-bymdez') ?></label>
                    <input type="checkbox" id="woosbymdz-elem-dis-dynatags" name="woosbymdz-elem-dis-dynatags" <?php echo esc_attr($dis_dyna_tags); ?>>
                    <sub><?php echo __('WooSlider Dynamic tags added to Elementor Pro will be disabled on your site.', 'wooslider-bymdez') ?></sub>
                    </div>
                </p>

                <a href="<?php echo esc_url(WOOSBYMDZ_URL.'admin/img/dyna-tags.webp'); ?>" target="_blank"><span class="woosbymdz-btn"><?php echo __('What is Dynamic Tags? (helper image)', 'wooslider-bymdez'); ?></span></a>

                <p class='helptip'><span></span><?php echo __('Note: You can use dynamic tags to more easily and better design your own product cards for WooCommerce sliders.', 'wooslider-bymdez') ?></p>


            </div>
            </fieldset>
            <?php endif; ?>

        <div class="sticky-submit"><?php submit_button(__('Save Settings', 'wooslider-bymdez')); ?></div>

        <div class="fixed-submit"><?php submit_button(__('Save Settings', 'wooslider-bymdez')); ?></div>
        </form>   
    </div>
</div>

<div class="thankyou-con <?php echo esc_attr($tank_you_con_class); ?>"><small><?php echo esc_html($thank_you_text); ?></small></div>

</div>

<script type="text/javascript">
var admin_ajax_url = "<?php echo admin_url()."admin-ajax.php"; ?>";
var sliders_load_eff = "<?php echo $sliders_loadeffect; ?>";
var conf_trans = "<?php echo __('Done!\nif you have unsaved settings on this page, click the cancel button and then click the save settings button\notherwise the page will reload', 'wooslider-bymdez'); ?>";
var conf_trans2 = "<?php echo __('Theme changed\nif you have unsaved settings on this page, click the cancel button and then click the save settings button\notherwise the page will reload', 'wooslider-bymdez'); ?>";
var copy_trans1 = "<?php echo __('Copied Successfully!', 'wooslider-bymdez'); ?>";
var copy_trans2 = "<?php echo __('Something went wrong!\nplease try agian', 'wooslider-bymdez'); ?>";

var conf_trans3 = "<?php echo __('Ajax request Sent, but not worked for some security reasons!', 'wooslider-bymdez'); ?>";
var conf_trans4 = "<?php echo __('PHP-file restored to its orginal contents successfully', 'wooslider-bymdez'); ?>";
var conf_trans5 = "<?php echo __('Error: PHP-file not been restored to its orginal contents!!', 'wooslider-bymdez'); ?>";

<?php 
//echo file_get_contents(WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."admin.js"); 
wp_enqueue_script('woosbymdz-settings', WOOSBYMDZ_URL.'admin/js/admin.js', array('jquery'), WOOSBYMDZ_VER, true);

wp_localize_script(
    'woosbymdz-settings',
    'woosbymdz_settings_ajx_obj',
    [
        'shonafahmi'  => wp_create_nonce( 'woosbymdz-8883-xmdzrt9y' ),
    ],
);//For Ajax-call Security

?>
</script>