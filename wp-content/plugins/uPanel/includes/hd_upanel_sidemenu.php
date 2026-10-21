<div id="hd-upanel" class="container">
  <div class="row">
    <div class="col-12 col-md-12">


      <h4 style="margin-bottom:30px;margin-top:20px">تنظیمات منو کناری</h4>

      <?php
      add_action('admin_enqueue_scripts', 'my_admin_scripts');
      wp_enqueue_media();
      global $wpdb;
      $charset_collate = $wpdb->get_charset_collate();
      $table_name = $wpdb->prefix . 'hd_upanel_sidemenu';
      if (isset($_POST['submit'])) {


        if (!isset($_POST['dashboard_active'])) {
          $_POST['dashboard_active'] = 'off';
        }
        if (!isset($_POST['dashboard_active'])) {
          $_POST['dashboard_active'] = 'off';
        }
        if (!isset($_POST['welcome_card_active'])) {
          $_POST['welcome_card_active'] = 'off';
        }
        if (!isset($_POST['calendar_card_active'])) {
          $_POST['calendar_card_active'] = 'off';
        }
        if (!isset($_POST['jalali_card_active'])) {
          $_POST['jalali_card_active'] = 'off';
        }
        if (!isset($_POST['georgian_card_active'])) {
          $_POST['georgian_card_active'] = 'off';
        }
        if (!isset($_POST['comments_card_active'])) {
          $_POST['comments_card_active'] = 'off';
        }
        if (!isset($_POST['settings_card_active'])) {
          $_POST['settings_card_active'] = 'off';
        }
        if (!isset($_POST['orders_card_active'])) {
          $_POST['orders_card_active'] = 'off';
        }
        if (!isset($_POST['register_card_active'])) {
          $_POST['register_card_active'] = 'off';
        }
        if (!isset($_POST['address_card_active'])) {
          $_POST['address_card_active'] = 'off';
        }
        if (!isset($_POST['log_out_card_active'])) {
          $_POST['log_out_card_active'] = 'off';
        }
        if (!isset($_POST['orders_active'])) {
          $_POST['orders_active'] = 'off';
        }
        if (!isset($_POST['address_active'])) {
          $_POST['address_active'] = 'off';
        }
        if (!isset($_POST['notifications_active'])) {
          $_POST['notifications_active'] = 'off';
        }
        if (!isset($_POST['downloads_active'])) {
          $_POST['downloads_active'] = 'off';
        }
        if (!isset($_POST['settings_active'])) {
          $_POST['settings_active'] = 'off';
        }
        if (!isset($_POST['logout_active'])) {
          $_POST['logout_active'] = 'off';
        }
        if (!isset($_POST['divider_active'])) {
          $_POST['divider_active'] = 'off';
        }
        if (!isset($_POST['link_1_active'])) {
          $_POST['link_1_active'] = 'off';
        }
        if (!isset($_POST['link_1_newtab'])) {
          $_POST['link_1_newtab'] = 'off';
        }
        if (!isset($_POST['link_2_active'])) {
          $_POST['link_2_active'] = 'off';
        }
        if (!isset($_POST['link_2_newtab'])) {
          $_POST['link_2_newtab'] = 'off';
        }
        if (!isset($_POST['link_3_active'])) {
          $_POST['link_3_active'] = 'off';
        }
        if (!isset($_POST['link_3_newtab'])) {
          $_POST['link_3_newtab'] = 'off';
        }
        if (!isset($_POST['link_4_active'])) {
          $_POST['link_4_active'] = 'off';
        }
        if (!isset($_POST['link_4_newtab'])) {
          $_POST['link_4_newtab'] = 'off';
        }
        if (!isset($_POST['link_5_active'])) {
          $_POST['link_5_active'] = 'off';
        }
        if (!isset($_POST['link_5_newtab'])) {
          $_POST['link_5_newtab'] = 'off';
        }
        if (!isset($_POST['html_1_active'])) {
          $_POST['html_1_active'] = 'off';
        }
        if (!isset($_POST['html_1_active'])) {
          $_POST['html_1_active'] = 'off';
        }
        if (!isset($_POST['html_2_active'])) {
          $_POST['html_2_active'] = 'off';
        }
        if (!isset($_POST['html_3_active'])) {
          $_POST['html_3_active'] = 'off';
        }
        if (!isset($_POST['html_4_active'])) {
          $_POST['html_4_active'] = 'off';
        }
        if (!isset($_POST['html_5_active'])) {
          $_POST['html_5_active'] = 'off';
        }
        if (!isset($_POST['tickets_active'])) {
          $_POST['tickets_active'] = 'off';
        }
        

        update_option('upanel_html_1', stripslashes($_POST['upanel_html_1']));
        update_option('upanel_html_2', stripslashes($_POST['upanel_html_2']));
        update_option('upanel_html_3', stripslashes($_POST['upanel_html_3']));
        update_option('upanel_html_4', stripslashes($_POST['upanel_html_4']));
        update_option('upanel_html_5', stripslashes($_POST['upanel_html_5']));

        // $_POST['items'] = stripslashes(json_encode($_POST['items']));
        $all_settings = $_POST;
        unset($all_settings['items']);
        unset($all_settings['upanel_html_1']);
        unset($all_settings['upanel_html_2']);
        unset($all_settings['upanel_html_3']);
        unset($all_settings['upanel_html_4']);
        unset($all_settings['upanel_html_5']);
        $all_settings = json_encode($all_settings);

        if (
          $wpdb->update(
            $table_name,
            array('items' => $_POST['items'], 'settings' => $all_settings),
            array(
              "setting_id" => 1
            )
          ) === FALSE
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


      $items = json_decode(stripslashes($result->items));

      $settings = json_decode($result->settings);

      ?>

    </div>
  </div>

  <form id="orders" method="post">
  <div class="row">

    <div class="col-12 col-md-4">

      <ul id="hd-upanel-sortable" class="sidemenu">

      
      
      <?php

      foreach ($items as $key => $value) {
        switch ($value) {
          case 'dashboard':
            echo '<li item="dashboard"><span class="dashicons dashicons-move"></span>داشبورد <span class="dashicons dashicons-admin-generic"></span>' . '<span class="is_active ' . $settings->dashboard_active . '">' . (($settings->dashboard_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . ' </li>';
            break;
          case 'orders':
            echo '<li item="orders"><span class="dashicons dashicons-move"></span>سفارش ها <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->orders_active . '">' . (($settings->orders_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'addresses':
            echo '<li item="addresses"><span class="dashicons dashicons-move"></span>آدرس ها <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->address_active . '">' . (($settings->address_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'notifications':
            echo '<li item="notifications"><span class="dashicons dashicons-move"></span>اعلان ها <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->notifications_active . '">' . (($settings->notifications_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'downloads':
            echo '<li item="downloads"><span class="dashicons dashicons-move"></span>دانلود ها <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->downloads_active . '">' . (($settings->downloads_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'settings':
            echo '<li item="settings"><span class="dashicons dashicons-move"></span>تنظیمات <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->settings_active . '">' . (($settings->settings_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'logout':
            echo '<li item="logout"><span class="dashicons dashicons-move"></span>خروج از حساب <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->logout_active . '">' . (($settings->logout_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'divider':
            echo '<li item="divider"><span class="dashicons dashicons-move"></span>جدا کننده <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->divider_active . '">' . (($settings->divider_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'link_1':
            echo '<li item="link_1"><span class="dashicons dashicons-move"></span>لینک یک <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->link_1_active . '">' . (($settings->link_1_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'link_2':
            echo '<li item="link_2"><span class="dashicons dashicons-move"></span>لینک دو  <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->link_2_active . '">' . (($settings->link_2_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;

          case 'link_3':
            echo '<li item="link_3"><span class="dashicons dashicons-move"></span>لینک سه <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->link_3_active . '">' . (($settings->link_3_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'link_4':
            echo '<li item="link_4"><span class="dashicons dashicons-move"></span>لینک چهار <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->link_4_active . '">' . (($settings->link_4_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'link_5':
            echo '<li item="link_5"><span class="dashicons dashicons-move"></span>لینک پنج <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->link_5_active . '">' . (($settings->link_5_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'html_1':
            echo '<li item="html_1"><span class="dashicons dashicons-move"></span>HTML سفارشی یک <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->html_1_active . '">' . (($settings->html_1_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'html_2':
            echo '<li item="html_2"><span class="dashicons dashicons-move"></span>HTML سفارشی دو  <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->html_2_active . '">' . (($settings->html_2_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'html_3':
            echo '<li item="html_3"><span class="dashicons dashicons-move"></span>HTML سفارشی سه  <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->html_3_active . '">' . (($settings->html_3_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'html_4':
            echo '<li item="html_4"><span class="dashicons dashicons-move"></span>HTML سفارشی چهار  <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->html_4_active . '">' . (($settings->html_4_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'html_5':
            echo '<li item="html_5"><span class="dashicons dashicons-move"></span>HTML سفارشی پنج  <span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->html_5_active . '">' . (($settings->html_5_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'tickets':
            echo '<li item="tickets"><span class="dashicons dashicons-move"></span> پشتیبانی<span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->tickets_active . '">' . (($settings->tickets_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;
          case 'membership':
            echo '<li item="membership"><span class="dashicons dashicons-move"></span> اشتراک<span class="dashicons dashicons-admin-generic"></span> ' . '<span class="is_active ' . $settings->membership_active . '">' . (($settings->membership_active == 'on') ? 'فعال' : 'غیر فعال') . '</span>' . '</li>';
            break;

          // link_2
// link_3
// link_4
// link_5
// html_1
// html_2
// html_3
// html_4
// html_5
      
          default:
            # code...
            break;
        }
      }
      ?>
        
        
        
        
        
        
        
        

      </ul>

    </div>
    <div class="col-12 col-md-8">


        <input name="items" type="hidden">
        <input name="settings" type="hidden">
        <input name="all_settings" type="hidden">

        <div class="mdl-card mdl-shadow--2dp" target="dashboard" id="dashboard-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو
              <span> داشبورد </span>
            </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="dashboard_label" type="text" value="<?php echo $settings->dashboard_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="dashboard_active">
              <input type="checkbox" name="dashboard_active" id="dashboard_active" class="mdl-switch__input" <?php if ($settings->dashboard_active == 'on') {
                echo 'checked="on"';
              } ?>>
              <span class="mdl-switch__label">فعال بودن</span>
            </label>

            <p class="divider"></p>
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title" style="margin-bottom: 20px;"> تنظیمات کارت های
              <span> داشبورد </span>
            </h4>


            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="welcome_card_active">
              <input type="checkbox" name="welcome_card_active" <?php if ($settings->welcome_card_active == 'on') {
                echo 'checked="on"';
              } ?> id="welcome_card_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن خوش آمدگویی</span>
            </label>
            <p class="hd-upanel-label">متن خوش گویی</p>
            <input name="welcome_label" type="text" value="<?php echo $settings->welcome_label; ?>" class="hd-upanel-input">
            متغیرها: name(نام کاربر)
            <p class="divider"></p>

            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="calendar_card_active">
              <input type="checkbox" name="calendar_card_active" <?php if ($settings->calendar_card_active == 'on') {
                echo 'checked="on"';
              } ?> id="calendar_card_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن تقویم</span>
            </label>

                                    <input class="button upload_image_button" type="button" value=" تصویر زمینه باکس تقویم" />
                                    <input style="display:none" id="calendar_img"
                                        value="<?php echo $settings->calendar_img; ?>" type="text" name="calendar_img">
                                    <?php if ($settings->calendar_img == "") { ?>
                                      <img style="display:none" id="uploaded-img" src="#">
                                      <?php

                                    } else {
                                      ?>
                                      <img id="uploaded-img" src="<?php echo $settings->calendar_img; ?>">
                                      <?php
                                    } ?>

            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="jalali_card_active">
              <input type="checkbox" name="jalali_card_active" <?php if ($settings->jalali_card_active == 'on') {
                echo 'checked="on"';
              } ?> id="jalali_card_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن تقویم شمسی</span>
            </label>
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="georgian_card_active">
              <input type="checkbox" name="georgian_card_active" <?php if ($settings->georgian_card_active == 'on') {
                echo 'checked="on"';
              } ?> id="georgian_card_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن تقویم میلادی</span>
            </label>

            <p class="divider"></p>

            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="comments_card_active">
              <input type="checkbox" name="comments_card_active" <?php if ($settings->comments_card_active == 'on') {
                echo 'checked="on"';
              } ?> id="comments_card_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن نظرات</span>
            </label>
            <p class="hd-upanel-label"> رنگ زمینه باکس نظرات</p>
            <input type="color"  name="comments_background" value="<?php echo $settings->comments_background; ?>">
            <p class="divider"></p>

            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="settings_card_active">
              <input type="checkbox" name="settings_card_active" <?php if ($settings->settings_card_active == 'on') {
                echo 'checked="on"';
              } ?> id="settings_card_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن دکمه تنظیمات</span>
            </label>

            <p class="divider"></p>

            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="orders_card_active">
              <input type="checkbox" name="orders_card_active" <?php if ($settings->orders_card_active == 'on') {
                echo 'checked="on"';
              } ?> id="orders_card_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن اطلاعات سفارش ها</span>
            </label>

            <p class="divider"></p>

            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="register_card_active">
              <input type="checkbox" name="register_card_active" <?php if ($settings->register_card_active == 'on') {
                echo 'checked="on"';
              } ?> id="register_card_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن تاریخ عضویت</span>
            </label>

            <p class="hd-upanel-label"> رنگ زمینه باکس تاریخ عضویت</p>
            <input type="color"  name="register_background" value="<?php echo $settings->register_background; ?>">

            <p class="hd-upanel-label"> نحوه نمایش تاریخ عضویت</p>
            <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="from_date">
              <input type="radio" id="from_date" class="mdl-radio__button" name="register_card_show" value="1"
                <?php if ($settings->register_card_show == '1') {
                  echo 'checked';
                } ?>>
              <span class="mdl-radio__label">نمایش تاریخ عضویت</span>
            </label>
            <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="duration">
              <input type="radio" id="duration" class="mdl-radio__button" name="register_card_show" value="2"  <?php if ($settings->register_card_show == '2') {
                echo 'checked';
              } ?>>
              <span class="mdl-radio__label">نمایش مدت زمان عضویت</span>
            </label>

            <p class="divider"></p>

            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="address_card_active">
              <input type="checkbox" name="address_card_active" <?php if ($settings->address_card_active == 'on') {
                echo 'checked="on"';
              } ?> id="address_card_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن دکمه آدرس های من</span>
            </label>

            <p class="divider"></p>

            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="log_out_card_active">
              <input type="checkbox" name="log_out_card_active" <?php if ($settings->log_out_card_active == 'on') {
                echo 'checked="on"';
              } ?> id="log_out_card_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن دکمه خروج از حساب</span>
            </label>

          </div>
        </div>

        <div class="mdl-card mdl-shadow--2dp" target="orders" id="orders-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو
              <span> سفارش ها </span>
            </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="orders_label" type="text" value="<?php echo $settings->orders_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="orders_active">
              <input type="checkbox" name="orders_active" <?php if ($settings->orders_active == 'on') {
                echo 'checked="on"';
              } ?> id="orders_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
          </div>
        </div>

        <div class="mdl-card mdl-shadow--2dp" target="address" id="addresses-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو
              <span> آدرس ها </span>
            </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="address_label" type="text" value="<?php echo $settings->address_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="address_active">
              <input type="checkbox" name="address_active" <?php if ($settings->address_active == 'on') {
                echo 'checked="on"';
              } ?> id="address_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
          </div>
        </div>

        <div class="mdl-card mdl-shadow--2dp" target="notifications" id="notifications-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو
              <span> اعلان ها </span>
            </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="notifications_label" type="text" value="<?php echo $settings->notifications_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="notifications_active">
              <input type="checkbox" name="notifications_active" <?php if ($settings->notifications_active == 'on') {
                echo 'checked="on"';
              } ?> id="notifications_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
          </div>
        </div>

        <div class="mdl-card mdl-shadow--2dp" target="downloads" id="downloads-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو
              <span> دانلود ها </span>
            </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="downloads_label" type="text" value="<?php echo $settings->downloads_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="downloads_active">
              <input type="checkbox" name="downloads_active" <?php if ($settings->downloads_active == 'on') {
                echo 'checked="on"';
              } ?> id="downloads_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
          </div>
        </div>

        <div class="mdl-card mdl-shadow--2dp" target="settings" id="settings-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو
              <span> تنظیمات </span>
            </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="settings_label" type="text" value="<?php echo $settings->settings_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="settings_active">
              <input type="checkbox" name="settings_active" <?php if ($settings->settings_active == 'on') {
                echo 'checked="on"';
              } ?> id="settings_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
          </div>
        </div>

        <div class="mdl-card mdl-shadow--2dp" target="logout" id="logout-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو
              <span> خروج از حساب </span>
            </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="logout_label" type="text" value="<?php echo $settings->logout_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="logout_active">
              <input type="checkbox" name="logout_active" <?php if ($settings->logout_active == 'on') {
                echo 'checked="on"';
              } ?> id="logout_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
          </div>
        </div>

        <div class="mdl-card mdl-shadow--2dp" target="divider" id="divider-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو
              <span> جدا کننده </span>
            </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label"> ارتفاع جدا کننده (px)</p>
            <input name="divider_label" type="number" value="<?php echo $settings->divider_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="divider_active">
              <input type="checkbox" name="divider_active" <?php if ($settings->divider_active == 'on') {
                echo 'checked="on"';
              } ?> id="divider_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
          </div>
        </div>

        



        <div class="mdl-card mdl-shadow--2dp" target="link_1" id="link_1-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو <span> لینک یک</span> </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="link_1_label" type="text" value="<?php echo $settings->link_1_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="link_1_active">
              <input type="checkbox" name="link_1_active" <?php if ($settings->link_1_active == 'on') {
                echo 'checked="on"';
              } ?> id="link_1_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
            <p class="hd-upanel-label"> لینک</p>
            <input name="link_1_link" type="text" value="<?php echo $settings->link_1_link; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="link_1_newtab">
              <input type="checkbox" name="link_1_newtab" <?php if ($settings->logout_active == 'on') {
                echo 'checked="on"';
              } ?> id="link_1_newtab" class="mdl-switch__input">
              <span class="mdl-switch__label"> تب جدید</span>
            </label>
          </div>
        </div>

        <div class="mdl-card mdl-shadow--2dp" target="link_2" id="link_2-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو <span> لینک دو</span> </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="link_2_label" type="text" value="<?php echo $settings->link_2_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="link_2_active">
              <input type="checkbox" name="link_2_active" <?php if ($settings->link_2_active == 'on') {
                echo 'checked="on"';
              } ?> id="link_2_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
            <p class="hd-upanel-label"> لینک</p>
            <input name="link_2_link" type="text" value="<?php echo $settings->link_2_link; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="link_2_newtab">
              <input type="checkbox" name="link_2_newtab" <?php if ($settings->logout_active == 'on') {
                echo 'checked="on"';
              } ?> id="link_2_newtab" class="mdl-switch__input">
              <span class="mdl-switch__label"> تب جدید</span>
            </label>
          </div>
        </div>

        <div class="mdl-card mdl-shadow--2dp" target="link_3" id="link_3-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو <span> لینک سه</span> </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="link_3_label" type="text" value="<?php echo $settings->link_3_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="link_3_active">
              <input type="checkbox" name="link_3_active" <?php if ($settings->link_3_active == 'on') {
                echo 'checked="on"';
              } ?> id="link_3_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
            <p class="hd-upanel-label"> لینک</p>
            <input name="link_3_link" type="text" value="<?php echo $settings->link_3_link; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="link_3_newtab">
              <input type="checkbox" name="link_3_newtab" <?php if ($settings->logout_active == 'on') {
                echo 'checked="on"';
              } ?> id="link_3_newtab" class="mdl-switch__input">
              <span class="mdl-switch__label"> تب جدید</span>
            </label>
          </div>
        </div>

        <div class="mdl-card mdl-shadow--2dp" target="link_4" id="link_4-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو <span> لینک چهار</span> </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="link_4_label" type="text" value="<?php echo $settings->link_4_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="link_4_active">
              <input type="checkbox" name="link_4_active" <?php if ($settings->link_4_active == 'on') {
                echo 'checked="on"';
              } ?> id="link_4_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
            <p class="hd-upanel-label"> لینک</p>
            <input name="link_4_link" type="text" value="<?php echo $settings->link_4_link; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="link_4_newtab">
              <input type="checkbox" name="link_4_newtab" <?php if ($settings->logout_active == 'on') {
                echo 'checked="on"';
              } ?> id="link_4_newtab" class="mdl-switch__input">
              <span class="mdl-switch__label"> تب جدید</span>
            </label>
          </div>
        </div>

        <div class="mdl-card mdl-shadow--2dp" target="link_5" id="link_5-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو <span> لینک پنج</span> </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="link_5_label" type="text" value="<?php echo $settings->link_5_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="link_5_active">
              <input type="checkbox" name="link_5_active" <?php if ($settings->link_5_active == 'on') {
                echo 'checked="on"';
              } ?> id="link_5_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
            <p class="hd-upanel-label"> لینک</p>
            <input name="link_5_link" type="text" value="<?php echo $settings->link_5_link; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="link_5_newtab">
              <input type="checkbox" name="link_5_newtab" <?php if ($settings->logout_active == 'on') {
                echo 'checked="on"';
              } ?> id="link_5_newtab" class="mdl-switch__input">
              <span class="mdl-switch__label"> تب جدید</span>
            </label>
          </div>
        </div>


        <div class="mdl-card mdl-shadow--2dp" target="html_1" id="html_1-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو <span> Html یک</span> </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="html_1_label" type="text" value="<?php echo $settings->html_1_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="html_1_active">
              <input type="checkbox" name="html_1_active" <?php if ($settings->html_1_active == 'on') {
                echo 'checked="on"';
              } ?> id="html_1_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
            <p class="hd-upanel-label"> لینک</p>
            <input name="html_1_link" type="text" value="<?php echo $settings->html_1_link; ?>" class="hd-upanel-input">
            <p class="hd-upanel-label"> محتوای صفحه</p>
            <?php
            $content = '';
            if (get_option('upanel_html_1')) {
              $content = get_option('upanel_html_1');
            } else {
              add_option('upanel_html_1', '', '', 'yes');
            }
            wp_editor($content, 'upanel_html_1'); ?>
          </div>
        </div>
     
 


        <div class="mdl-card mdl-shadow--2dp" target="html_2" id="html_2-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو <span> Html دو</span> </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="html_2_label" type="text" value="<?php echo $settings->html_2_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="html_2_active">
              <input type="checkbox" name="html_2_active" <?php if ($settings->html_2_active == 'on') {
                echo 'checked="on"';
              } ?> id="html_2_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
            <p class="hd-upanel-label"> لینک</p>
            <input name="html_2_link" type="text" value="<?php echo $settings->html_2_link; ?>" class="hd-upanel-input">
            <p class="hd-upanel-label"> محتوای صفحه</p>
            <?php
            $content = '';
            if (get_option('upanel_html_2')) {
              $content = get_option('upanel_html_2');
            } else {
              add_option('upanel_html_2', '', '', 'yes');
            }
            wp_editor($content, 'upanel_html_2'); ?>
          </div>
        </div>
        <div class="mdl-card mdl-shadow--2dp" target="html_3" id="html_3-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو <span> Html سه</span> </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="html_3_label" type="text" value="<?php echo $settings->html_3_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="html_3_active">
              <input type="checkbox" name="html_3_active" <?php if ($settings->html_3_active == 'on') {
                echo 'checked="on"';
              } ?> id="html_3_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
            <p class="hd-upanel-label"> لینک</p>
            <input name="html_3_link" type="text" value="<?php echo $settings->html_3_link; ?>" class="hd-upanel-input">
            <p class="hd-upanel-label"> محتوای صفحه</p>
            <?php
            $content = '';
            if (get_option('upanel_html_3')) {
              $content = get_option('upanel_html_3');
            } else {
              add_option('upanel_html_3', '', '', 'yes');
            }
            wp_editor($content, 'upanel_html_3'); ?>
          </div>
        </div>

        <div class="mdl-card mdl-shadow--2dp" target="html_4" id="html_4-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو <span> Html چهار</span> </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="html_4_label" type="text" value="<?php echo $settings->html_4_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="html_4_active">
              <input type="checkbox" name="html_4_active" <?php if ($settings->html_4_active == 'on') {
                echo 'checked="on"';
              } ?> id="html_4_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
            <p class="hd-upanel-label"> لینک</p>
            <input name="html_4_link" type="text" value="<?php echo $settings->html_4_link; ?>" class="hd-upanel-input">
            <p class="hd-upanel-label"> محتوای صفحه</p>
            <?php
            $content = '';
            if (get_option('upanel_html_4')) {
              $content = get_option('upanel_html_4');
            } else {
              add_option('upanel_html_4', '', '', 'yes');
            }
            wp_editor($content, 'upanel_html_4'); ?>

          </div>
        </div>
        <div class="mdl-card mdl-shadow--2dp" target="html_5" id="html_5-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو <span> Html پنج</span> </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="html_5_label" type="text" value="<?php echo $settings->html_5_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="html_5_active">
              <input type="checkbox" name="html_5_active" <?php if ($settings->html_5_active == 'on') {
                echo 'checked="on"';
              } ?> id="html_5_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
            <p class="hd-upanel-label"> لینک</p>
            <input name="html_5_link" type="text" value="<?php echo $settings->html_5_link; ?>" class="hd-upanel-input">
            <p class="hd-upanel-label"> محتوای صفحه</p>
            <?php
            $content = '';
            if (get_option('upanel_html_5')) {
              $content = get_option('upanel_html_5');
            } else {
              add_option('upanel_html_5', '', '', 'yes');
            }
            wp_editor($content, 'upanel_html_5'); ?>
          </div>
        </div>


        <div class="mdl-card mdl-shadow--2dp" target="tickets" id="tickets-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو <span> پشتیبانی </span> </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="tickets_label" type="text" value="<?php echo $settings->tickets_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="tickets_active">
              <input type="checkbox" name="tickets_active" <?php if ($settings->tickets_active == 'on') {
                echo 'checked="on"';
              } ?> id="tickets_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
       
          </div>
        </div>


        <div class="mdl-card mdl-shadow--2dp" target="membership" id="membership-card" style="width: 100%;">
          <div class="mdl-card__title">
            <h4 class="mdl-card__title-text hd-upanel-sidemenu-title"> تنظیمات منو <span> اشتراک </span> </h4>
          </div>
          <div class="mdl-card__supporting-text">
            <p class="hd-upanel-label">متن منو</p>
            <input name="membership_label" type="text" value="<?php echo $settings->membership_label; ?>" class="hd-upanel-input">
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="membership_active">
              <input type="checkbox" name="membership_active" <?php if ($settings->membership_active == 'on') {
                echo 'checked="on"';
              } ?> id="membership_active" class="mdl-switch__input">
              <span class="mdl-switch__label">فعال بودن</span>
            </label>
       
          </div>
        </div>



    </div>
    <div class="col-12 col-md-12">

      <input style="margin: 20px 0;padding: 7px 17px;font-size: 16px;float: left;" type="submit" name="submit" id="submit" class="button button-primary" value="ذخیره تغییرات">

    </div>


  </div>
  </form>
</div>