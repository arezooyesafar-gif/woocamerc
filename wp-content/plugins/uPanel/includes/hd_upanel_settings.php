<div id="hd-upanel" class="container">
    <div class="row">
        <div class="col-12 col-md-12">
            <?php
            add_action('admin_enqueue_scripts', 'my_admin_scripts');
            wp_enqueue_media();
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $wpdb->prefix . 'hd_upanel_sidemenu';
            $table_sms = $wpdb->prefix . 'hd_upanel_sms_providers';
            if (isset($_POST['submit'])) {
          
               
                hd_upanel_set_default_provider($_POST['sms']);
                foreach ($_POST['sms-id'] as $key => $value) {
                    $wpdb->update($table_sms, array('username' => $_POST['username'][$key], 'password' => $_POST['password'][$key],
                        'token' => $_POST['token'][$key], 'number' => $_POST['number'][$key], 'reset_password_pattern' => $_POST['reset_password_pattern'][$key]
                    ), array("id" => $value));
                }
                if (get_option('upanel_sms_content') !== false) {
                   
                    update_option('upanel_sms_content', $_POST['sms_content']);
                } else {
                    add_option('upanel_sms_content', $_POST['sms_content']);
                }
                
                if (get_option('upanel_login_content') !== false && isset($_POST['upanel_login_content'])) {
                    update_option('upanel_login_content', stripslashes($_POST['upanel_login_content']));
                } else if(isset($_POST['upanel_login_content'])){
                    add_option('upanel_login_content', stripslashes($_POST['upanel_login_content']));
                }

                if (get_option('upanel_sms_reset_password') !== false) {
                    update_option('upanel_sms_reset_password', $_POST['sms_content_reset_password']);
                } else {
                    add_option('upanel_sms_reset_password', $_POST['sms_content_reset_password']);
                }


                $_POST['sms_content'] = '';
                $_POST['sms_content_reset_password'] = '';
                $_POST['upanel_login_content'] = '';

                if (!isset($_POST['facebook_active'])) {
                    $_POST['facebook_active'] = 'off';
                }
                if (!isset($_POST['twitter_active'])) {
                    $_POST['twitter_active'] = 'off';
                }
                if (!isset($_POST['instagram_active'])) {
                    $_POST['instagram_active'] = 'off';
                }
                if (!isset($_POST['telegram_active'])) {
                    $_POST['telegram_active'] = 'off';
                }
                if (!isset($_POST['whatsapp_active'])) {
                    $_POST['whatsapp_active'] = 'off';
                }
                if (!isset($_POST['login_page'])) {
                    $_POST['login_page'] = 'off';
                }
                if (!isset($_POST['avatar'])) {
                    $_POST['avatar'] = 'off';
                }
                if (!isset($_POST['home_link'])) {
                    $_POST['home_link'] = 'off';
                }
                if (!isset($_POST['account'])) {
                    $_POST['account'] = 'off';
                }
                if (!isset($_POST['dark_mode'])) {
                    $_POST['dark_mode'] = 'off';
                }
                if (!isset($_POST['login_by_mobile'])) {
                    $_POST['login_by_mobile'] = 'off';
                }
                if (!isset($_POST['login_by_email'])) {
                    $_POST['login_by_email'] = 'off';
                }
                if (!isset($_POST['login_by_username'])) {
                    $_POST['login_by_username'] = 'off';
                }
                if (!isset($_POST['register_by_mobile'])) {
                    $_POST['register_by_mobile'] = 'off';
                }
                if (!isset($_POST['register_by_email'])) {
                    $_POST['register_by_email'] = 'off';
                }
                if (!isset($_POST['register_by_username'])) {
                    $_POST['register_by_username'] = 'off';
                }
                if (!isset($_POST['login_sms'])) {
                    $_POST['login_sms'] = 'off';
                }
                if (!isset($_POST['register_sms'])) {
                    $_POST['register_sms'] = 'off';
                }
                if (!isset($_POST['register_firstname'])) {
                    $_POST['register_firstname'] = 'off';
                }
                if (!isset($_POST['register_lastname'])) {
                    $_POST['register_lastname'] = 'off';
                }
                if (!isset($_POST['register_username'])) {
                    $_POST['register_username'] = 'off';
                }
                if (!isset($_POST['register_email'])) {
                    $_POST['register_email'] = 'off';
                }
                if (!isset($_POST['register_mobile'])) {
                    $_POST['register_mobile'] = 'off';
                }
                if (!isset($_POST['logout_popup_image'])) {
                    $_POST['logout_popup_image'] = 'off';
                }
                if (!isset($_POST['basket_image_dashboard'])) {
                    $_POST['basket_image_dashboard'] = 'off';
                }
                if (!isset($_POST['no_index'])) {
                    $_POST['no_index'] = 'off';
                }

     

                $_POST['departments'] = array_filter($_POST['departments'], 'strlen');
                $_POST['departments'] = urlencode(implode(",", $_POST['departments']));
                $all_settings = json_encode($_POST);

                if (
                    $wpdb->update($table_name, array('all_settings' => $all_settings),
                        array(
                            "setting_id" => 1
                        )) === FALSE
                ) {
                    $_POST['status'] = 'error';
                    ?>
            <div class="col-md-12">
                <div class="submited">خطا در ویرایش تنظیمات</div>
            </div>
            <?php
                } else {
                    $_POST['status'] = 'success';
                    ?>
            <div class="col-md-12">
                <div class="success-submit">تنظیمات با موفقیت ذخیره شد</div>
            </div>
            <?php
                }
            }
            $result = $wpdb->get_results("SELECT * FROM $table_name LIMIT 1")[0];
            $settings = json_decode($result->all_settings);
            $settings->sms_content = trim(get_option('upanel_sms_content'));
            $settings->upanel_login_content = trim(get_option('upanel_login_content'));
            $settings->sms_content_reset_password = trim(get_option('upanel_sms_reset_password'));
            $sms = $wpdb->get_results("SELECT * FROM $table_sms ORDER BY ordering");
            $social = ['facebook', 'twitter', 'instagram', 'telegram', 'whatsapp'];
            ?>
        </div>
    </div>

    <form method="post">
        <div class="row">
            <div class="col-12 col-md-12">
                <div class="row">
                    <div class="col-12 col-md-9">
                        <ul class="tabs">
                            <li><button type="button" class="active" id="settings">تنظیمات کلی</button></li>
                            <li><button type="button" id="sms"> پیامک</button></li>
                            <li><button type="button" id="login"> ورود و ثبت نام</button></li>
                            <li><button type="button" id="ticket"> پشتیبانی</button></li>
                            <li><button type="button" id="mail"> ایمیل</button></li>
                        </ul>
                      
                        <?php include 'setting/setting.php'; ?>
                        <?php include 'setting/sms.php'; ?>
                        <?php include 'setting/login.php'; ?>
                        <?php include 'setting/ticket.php'; ?>
                        <?php include 'setting/mail.php'; ?>
                       
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="sticky">
                            <input style="margin: 50px 0;padding: 7px 17px;font-size: 16px;" type="submit" name="submit"
                                id="submit" class="button button-primary" value="ذخیره تغییرات">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>