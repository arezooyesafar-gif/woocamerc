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

if (!class_exists('woosbymdz_adminconf'))
{

    class woosbymdz_adminconf 
    {
        //PROPS
        private static $instance;

        //min ver of php
        const MINIMUM_PHP_VERSION = '7.3';


        //constructor
        public function __Construct() 
        {

            //activate & deactivate hooks
            register_activation_hook(WOOSBYMDZ_PLG, array($this, 'onactivate'));
            register_deactivation_hook(WOOSBYMDZ_PLG, array($this, 'ondeactivate'));

            $this->checkfirstredirect(); //redirect on first time activation
            $this->deactivationConfirmation(); //Confirm to empty or not the data

            //add menue
            add_action('admin_menu', array($this, 'addmenue'));

            //Check plugin verison
            $version = get_option('woosbymdz_plugin_version'); 

            if(version_compare($version, WOOSBYMDZ_VER, '<') && $version != '') {//Just runs after an update

                update_option('woosbymdz_plugin_version', WOOSBYMDZ_VER);

                $dynamic_css_file = WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'frontcss.css';
                $dynamic_js_file = WOOSBYMDZ_PATH . 'front'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'frontjs.js';

                $modules_backup_txt = WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'file-changes'.DIRECTORY_SEPARATOR.'module-php-backup.txt';



                $css_code_fromdb = get_option('woosbymdz_css_file');
                $js_code_fromdb = get_option('woosbymdz_js_file');


                $this->writetofile($dynamic_css_file, $css_code_fromdb, '', 'begin');
                $this->writetofile($dynamic_js_file, $js_code_fromdb, '', 'begin');

                //re-fill the `module-php-backup.txt` file when plugin updates to a new-version
                if(is_file($modules_backup_txt)){
                    $tmp_php_codes = get_option('woosbymdz_elempro_modules_backup_file');
                    if(!empty($tmp_php_codes)){
                        file_put_contents($modules_backup_txt, $tmp_php_codes);
                    }

                }
                /***/

                //tmp-ver reversion
                $woosbymdz_tmp_ver = get_option('woosbymdz_tmpversion');
                if($woosbymdz_tmp_ver){
                    update_option('woosbymdz_tmpversion', '0.0.0.1');
                }

            }

            //Charged BTN Adder
            $use_charged = get_option('woosbymdz_charged_pros');
            $don_btn = get_option('woosbymdz_charged_donshowbtn');
            if($use_charged == '1'){
                add_action( 'admin_head', function() {
                    remove_meta_box( 'postcustom', 'product', 'normal' );
                });
            }
            if($use_charged == '1' && $don_btn != '1'){

                $btn_pos = get_option("woosbymdz_charged_btnpos");
                switch($btn_pos){
                    case 'top':
                        add_action( 'edit_form_top', array($this, 'add_charged_btn') );
                        break;
                    case 'mid':
                        add_action( 'edit_form_after_editor', array($this, 'add_charged_btn') );
                        break;
                    case 'bottom':
                        add_action( 'edit_form_advanced', array($this, 'add_charged_btn') );
                        break;
                }
                add_action('admin_enqueue_scripts', array($this, 'add_admin_charged_cssjs'));
            }

            //Add elementor-editor icons
            if($this->check_plg('elementor.php'))
            {
                add_action( 'elementor/editor/before_enqueue_styles', function()
                {
                    wp_enqueue_style( 'woosbymdz-elem-editor', WOOSBYMDZ_URL . 'admin/css/elementor.css', array( 'elementor-editor' ), WOOSBYMDZ_VER );
                });
            }
            /***/

            //Elementor-Pro update (Wooslider-Loop-builder)
            $use_loop_builder = get_option('woosbymdz_extrawidget2');
            if($use_loop_builder == 1){

                $use_elementor = $this->check_plg('elementor.php');

                if($use_elementor){

                    
                    add_action( 'upgrader_process_complete', [$this, 'after_update_elem_pro'], 10, 2 );

                    //wp_die('تست شدش!');
                }
            }

            $elempro_has_updated = get_option('woosbymdz_elmpro_has_updated');
            if($elempro_has_updated == 1){


                //$current_screen = get_current_screen();
                //update the php file contents

                $tmp_res = $this->edit_phpfile_elem_pro();

                global $pagenow;
                if ( $pagenow && $pagenow != 'update.php' ) { 

                    if($tmp_res){
                        update_option('woosbymdz_elmpro_has_updated', 0, false);
                        $this->set_autoload_off('woosbymdz_elmpro_has_updated');
    
                        //wp_die('OK-2');
                    }
                }
                

                
                
                //wp_die('OK-1');
                /***/
                
            }
            /***/

        }

        //METHODS
        public static function get_instance() 
        {
            if ( ! isset( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function onactivate()
        {

            /*check PHP Ver*/
            if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')){
                
                $msg = sprintf('حداقل ورژن PHP سایت شما می بایست %1$s باشد', self::MINIMUM_PHP_VERSION);
                wp_die($msg);

            }

            //set default options on first-time activation
            $is_first_activate = get_option('woosbymdz_redirect_onActivate'); 
            if($is_first_activate === false){

                update_option('woosbymdz_plugin_version', WOOSBYMDZ_VER);

                add_option('woosbymdz_loadingtime', 2000);
                add_option('woosbymdz_loadingeffect', 'effect-1');
                add_option('woosbymdz_mob2breakpoint', 300);
                add_option('woosbymdz_widebreakpoint', 2550);
                
                
            }

            add_option('woosbymdz_empty_onDeactivate', 0);
            add_option('woosbymdz_redirect_onActivate', 1);
            

        }

        public function ondeactivate() 
        {

            update_option('woosbymdz_redirect_onActivate', 1, false);

        }
        
        //checks to do redirect on activation or not
        public function checkfirstredirect()
        {
            
            global $pagenow;
            $do_redirect = get_option('woosbymdz_redirect_onActivate');
            if ($pagenow == 'plugins.php' && $do_redirect == '1'){
                add_action('admin_footer', array($this, 'doredirect'));
            }
        }

        //do redirect
        public function doredirect() 
        {
            ?>
            <script type="text/javascript">
                window.location.href = '<?php echo esc_url(admin_url()); ?>?page=woosbymdz-settings';
            </script>
            <?php
            update_option('woosbymdz_redirect_onActivate', 0, false);
        }

        //empty on deactivate checker
        public function deactivationConfirmation()
        {

            global $pagenow;
            if ($pagenow == 'plugins.php'){
                add_action('admin_footer', array($this, 'emptyonDeactivate'));
            }
        }

        //empty on deactivate
        public function emptyonDeactivate()
        {

        ?>
            <script type="text/javascript">
            var btn = document.getElementById("deactivate-wooproducts-touch-slider");
            if (btn != null){
            btn.addEventListener('click', function(){
            let conf = confirm('<?php echo esc_html__('Do you want to KEEP all of the data of this plugin\ninside your database?\nPlease remind that, all of the plugins options get stored inside\nWP_OPTIONS table inside your database and having too much extra data on this table\nwill cause your web-site to load slowly\nif this is a TEMPORARY de-activation\nclick on the OK button otherwise click Cancel', 'wooslider-bymdez') ?>');
            if (conf == false){
                var data = {
                  action: "woosbymdz_emptyOptions_AjaxConf",
                  tmp: 1,
                };
                jQuery.post(ajaxurl, data, function (resp){
                    console.log(resp);
                });
            }else {
                console.log('User Decided to keep Options!');
            } 
            });
            }  
           </script>

        <?php

        }

        //menue adder
        public function addmenue()
        {

            $woosbymdz_icon = include_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'menu-cion.php';

            add_menu_page( 
                'Woo Slider',
                esc_html__('Woo Modern Slider', 'wooslider-bymdez'), 
                'install_plugins', 
                'woosbymdz-settings', 
                array($this, 'menufunc'), 
                "$woosbymdz_icon", 
                '99', 
            );

            $is_shrtcode_generator = $this->check_shortcode();

            if($is_shrtcode_generator){

                add_submenu_page(
                    'woosbymdz-settings',
                    esc_html__('Short-Code', 'wooslider-bymdez'),
                    esc_html__('Short-Code', 'wooslider-bymdez'),
                    'install_plugins',
                    'woosbymdz-shortcode',
                    array($this, 'page_shortcode'),
                );

                add_submenu_page(
                    'woosbymdz-settings',
                    esc_html__('Saved Short-Codes', 'wooslider-bymdez'),
                    esc_html__('Saved Short-Codes', 'wooslider-bymdez'),
                    'install_plugins',
                    'woosbymdz-shortcode-saved',
                    array($this, 'page_shortcode2'),
                );
            }

            add_submenu_page(

				'woosbymdz-settings',
				esc_html__('Free plugins', 'wooslider-bymdez'),
				esc_html__('Free plugins', 'wooslider-bymdez'),
				'install_plugins',
				'plugin-install.php?s=mdesign&tab=search&type=author',
				''
			);

            add_submenu_page(

				'woosbymdz-settings',
				esc_html__('Other plugins', 'wooslider-bymdez'),
				esc_html__('Other plugins', 'wooslider-bymdez'),
				'install_plugins',
				'https://www.rtl-theme.com/author/delbarbash/products/',
				''
			);

        }

        //Menu function
        public function menufunc()
        {

            include_once WOOSBYMDZ_PATH . 'admin'.DIRECTORY_SEPARATOR.'settingspage.php';
            /*add this page CSS*/
            $user_id = get_current_user_id();
            $user_color = get_user_option( 'admin_color', $user_id );
            $is_nightmod = get_option('woosbymdz_nightmode');

            include_once WOOSBYMDZ_PATH . 'admin'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'loadings-css.php';

            //include_once WOOSBYMDZ_PATH . 'admin'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'admin-css.php';

            wp_enqueue_style('woosbymdz-admin-main', WOOSBYMDZ_URL.'admin/css/admin-css.css', [], WOOSBYMDZ_VER);
            /**/
            include_once WOOSBYMDZ_PATH . 'admin'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'settings-pagehtml.php';
        }

        //Shortcode Page
        public function page_shortcode()
        {

            //Add wp-dashicons
            wp_enqueue_style('dashicons');
            
            //include_once WOOSBYMDZSHORTCODE_PATH . 'admin'.DIRECTORY_SEPARATOR.'settingspage.php';
            /*add this page CSS*/
            $user_id = get_current_user_id();
            $user_color = get_user_option( 'admin_color', $user_id );

            include_once WOOSBYMDZSHORTCODE_PATH . 'admin'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'admin-css.php';
            /**/
            include_once WOOSBYMDZSHORTCODE_PATH . 'admin'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'settings-pagehtml.php';

        }
        public function page_shortcode2()
        {
            include_once WOOSBYMDZSHORTCODE_PATH.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'settingspage.php';
        }
        
        //write to file
        public function writetofile($path, $content, $static_content, $where) 
        {

            if(empty($path)) {
                return;
            }
            
            // write file
            if ($static_content == '') {
                $static = '';
            }else $static = file_get_contents($static_content);
            
            if ($where == 'end'){
                $content = $static . $content;
            }else if ($where == 'begin') { $content = $content . $static;}
            
            $handle = fopen($path, 'w+');
            $is_writed = fwrite($handle, $content);
            fclose($handle);
        
            // update last modified & access time
            touch($path);
        }

        //custom sanitize
        public function sanitize_checkbox( $input )
        {         
            //returns true if checkbox is checked
            return ( isset( $input ) ? true : false );
        }

        //Set Options Autoload off (no)
        public function set_autoload_off($option) 
        {

            $tmp_option_val = get_option($option);
            delete_option($option);
  
            add_option($option, $tmp_option_val, '', 'no');
  
        }

        //Check Short-Code Generator Plugin
        private function check_shortcode()
        {

            $all_plugins = apply_filters('active_plugins', get_option('active_plugins'));
            if (stripos(implode($all_plugins), 'wooslider-bymdez-shortcode.php')) {

                return true;//Plugin found

            }else return false;
        }

        //Charged-Btn
        public function add_charged_btn()
        {

            global $post;

            
            $type = $post->post_type;

            if($type == 'product'){

            $pid = $post->ID;

            $is_charged = get_post_meta($pid, 'woosbymdz_is_charged', true);
 
            $checked = '';
            if($is_charged == '1'){
                $checked = 'checked';
            }

            ?>
            <div class="woosbymdz-charged-btn-wrapper">
                <span style="font-size: 16px;"><?php echo esc_html__('is Charged?', 'wooslider-bymdez') ?></span>
                <label class="switch">
                    <input type="checkbox" onclick="woosbymdz_admin_pro_check_charge(this)" <?php echo esc_attr($checked); ?> pid="<?php echo esc_attr($pid); ?>">
                    <span class="slider round"></span>
                </label>
            </div>
            <?php

            }
        }

        public function add_admin_charged_cssjs()
        {

            global $post;

            $type = $post->post_type;

            if($type == 'product'){

                wp_enqueue_style( 'woosbymdz-chargedcss', WOOSBYMDZ_URL.'admin/css/charged.css', [], WOOSBYMDZ_VER );
                wp_enqueue_script( 'woosbymdz-chargedjs', WOOSBYMDZ_URL.'admin/js/charged.js', [], WOOSBYMDZ_VER, true );

                wp_localize_script(
                    'woosbymdz-chargedjs',
                    'woosbymdz_chargeds_ajx_obj',
                    [
                        'sedamej12'  => wp_create_nonce( 'woosbymdz-sikansoo9m-wook' ),
                    ],
                );//For Ajax-call Security
            }
        }

        //Version Increaser
        public function version_increaser($ver)
        {

            $ver_arr = explode('.', $ver);
            $indexer = count($ver_arr)-1;

            $outputter = [];

            for($i = $indexer; $i >= 0; $i--){
                
                $tmp = intval($ver_arr[$i]);
                $tmp2 = intval($ver_arr[$i-1]);

                if($tmp <= 999){

                    $tmp++;
                    $ver_arr[$i] = $tmp;
                    break;

                }else if($i != 0 && $tmp > 999){
                    $tmp = 0;
                    $tmp2++;
                    $ver_arr[$i] = $tmp;
                    $ver_arr[$i-1] = $tmp2;
                }
            }

            $outputter = implode('.', $ver_arr);
            return $outputter;
        }

        //Elementor-Pro
        public function after_update_elem_pro($upgrader_object, $options)
        {
            
            $tmp_test = $upgrader_object->skin->result->error_data;

            $is_this = 0;
            
            foreach($tmp_test as $tmp){

                
                //"C:/xampp/htdocs/wp/wp-content/plugins/wooslider-bymdez/; `Before Explode`
                //"C:/xampp/htdocs/wp/wp-content/         /wooslider-bymdez/; `After Explode`
                if(strpos($tmp, 'plugins') !== false){

                    $new_temp = explode('plugins', $tmp);
                    $new_temp[1] = str_replace('/', '', $new_temp[1]);
                    $new_temp[1] = str_replace('\\', '', $new_temp[1]);
                    $new_temp[1] = str_replace(DIRECTORY_SEPARATOR, '', $new_temp[1]);

                    if(strtolower($new_temp[1]) == 'elementor-pro'){

                        $is_this = 1;
                        
                    }
                }


                if($is_this == 1){


                    update_option('woosbymdz_elmpro_hasfile_edited', 0, false);
                    update_option('woosbymdz_elmpro_has_updated', 1, false);

                    //wp_die('تست شدشد!!!!!');

                    
                }

                
            }

        }

        private function edit_phpfile_elem_pro()
        {

            $this->logger('');//make logger-file empty!
            $this->logger('Start elementor-pro file change');

            $file_path = ABSPATH.DIRECTORY_SEPARATOR.'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'elementor-pro'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'loop-builder'.DIRECTORY_SEPARATOR.'module.php';

            if(!is_file($file_path)){
                $this->logger('Elementor-Pro `module.php` file not-found! try another path (Elementor-Pro)!');

                $file_path = ABSPATH.DIRECTORY_SEPARATOR.'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'Elementor-Pro'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'loop-builder'.DIRECTORY_SEPARATOR.'module.php';

                if(!is_file($file_path)){
                    $this->logger('Elementor-Pro `module.php` file not-found! try another way (use self elementor-pro path constant)!');

                    $file_path = ELEMENTOR_PRO_PATH.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'loop-builder'.DIRECTORY_SEPARATOR.'module.php';
                }



            }else $this->logger('Elementor-Pro `module.php` file found (elementor-pro)');

            

            $contents_file = WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'file-changes'.DIRECTORY_SEPARATOR.'module-php.txt';
            $backup_file =WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'file-changes'.DIRECTORY_SEPARATOR.'module-php-backup.txt';

            if(is_file($file_path) && is_file($backup_file)){
                $orginal_contents = file_get_contents($file_path);

                $this->writetofile($backup_file, $orginal_contents, '', 'begin'); 
            }

            if(is_file($file_path) && is_file($contents_file)){

                $new_contents = file_get_contents($contents_file);

                if(!empty($new_contents)){

                    $this->writetofile($file_path, $new_contents, '', 'begin'); 

                    update_option('woosbymdz_elmpro_hasfile_edited', 1, false);

                    return true;
                }
                
            }

            return false;
        }
        /***/

        //Misc
        private function check_plg($php_filename)
        {
            $all_plugins = apply_filters('active_plugins', get_option('active_plugins'));
            if (stripos(implode($all_plugins), $php_filename)) {
                return true;
            } else return false;
        }
        private function logger($txt='')
        {

            $log_file = WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'log.txt';

            if(is_file($log_file)){

                if(empty($txt)){
                    file_put_contents($log_file, '');
                    return;

                }

                $old_conts = file_get_contents($log_file);
                $new_conts = $old_conts."\n".$txt."\n";

                file_put_contents($log_file, $new_conts);

            }
            
            

        }
        /***/

    }

}
//init
woosbymdz_adminconf::get_instance();
//close the PHP tag to reduce the blank spaces ?>