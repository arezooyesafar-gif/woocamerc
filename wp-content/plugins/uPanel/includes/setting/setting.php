<div class="mdl-card mdl-shadow--2dp tab" id="settings-tab" style="width: 100%;">
    <div class="mdl-card__supporting-text">

        <p hidden class="hd-upanel-label">آدرس url پنل کاربری </p>
        <input hidden name="url" type="text" value="<?php if (isset($settings->url)) {
            echo $settings->url;
        } else {
            echo 'panel';
        } ?>" class="hd-upanel-input">

        <div class="row">
            <div class="col-12">
                <p class="hd-upanel-label"> قالب نمایش</p>
            </div>
            <div class="col-3">
                <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="template-0">
                    <input type="radio" id="template-0" class="mdl-radio__button" name="template" value="0" <?php if (isset($settings->template) && $settings->template == '0') {
                        echo 'checked';
                    }
                    if (!isset($settings->template)) {
                        echo 'checked';
                    } ?>>
                    <span class="mdl-radio__label">قالب پیشفرض</span>
                </label>
            </div>
            <div class="col-3">
                <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="template-1">
                    <input type="radio" id="template-1" class="mdl-radio__button" name="template" value="1" <?php if (isset($settings->template) && $settings->template == '1') {
                        echo 'checked';
                    } ?>>
                    <span class="mdl-radio__label">قالب دیجی پنل</span>
                </label>
            </div>
        </div>



        <div class="row " style="margin-top:30px;width:100%">

            <div class="col-12 col-md-3">
                <label class="pure-material-textfield-outlined" style="width:100%">
                    <input type="text" name="panel_url" value="<?php if (isset($settings->panel_url)) {
                        echo $settings->panel_url;
                    } else {
                        echo 'panel';
                    } ?>" placeholder=" ">
                    <span>url صفحه </span>
                </label>
            </div>

            <div class="col-12 col-md-3">
                <label class="pure-material-textfield-outlined" style="width:100%">
                    <input type="text" name="panel_title" value="<?php if (isset($settings->panel_title)) {
                        echo $settings->panel_title;
                    } else {
                        echo 'پنل کاربری';
                    } ?>" placeholder=" ">
                    <span>عنوان صفحه </span>
                </label>
            </div>

        </div>

          <div class="row " style="margin-top:10px;width:100%">
            
            <div class="col-12 col-md-6">
                <label class="pure-material-textfield-outlined" style="width:100%">
                    <input type="text" dir="ltr" name="redirect_url" placeholder="/sample-url" value="<?php if (isset($settings->redirect_url)) {
                        echo $settings->redirect_url;
                    } ?>" placeholder=" ">
                    <span>آدرس تغییر مسیر بعد از خروج از حساب  </span>
                </label>
            </div>

        </div>


        <div class="row">
            <div class="col-12" style="margin-top:20px">
                <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="no_index">
                    <input type="checkbox" <?php if (isset($settings->no_index) && $settings->no_index == 'on') {
                        echo 'checked="on"';
                    }
                    if (!isset($settings->no_index)) {
                        echo 'checked="on"';
                    } ?> name="no_index"
                        id="no_index" class="mdl-switch__input">
                    <span class="mdl-switch__label">از موتورهای جستجو درخواست کن تا محتوای صفحه داشبورد را بررسی نکنند
                        (noindex)</span>
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                <p class="hd-upanel-label"> رنگ پیشفرض </p>
                <input type="color" name="color" value="<?php echo $settings->color; ?>">
            </div>
            <div class="col-3">
                <p class="hd-upanel-label"> رنگ پیشفرض حال شب </p>
                <input type="color" name="color_dark" value="<?php echo $settings->color_dark; ?>">
            </div>
        </div>




        <p class="divider"></p>
        <div class="row">
            <div class="col-12">
                <p class="hd-upanel-label"> لوگو اصلی </p>
                <input class="button image" type="button" value="انتخاب تصویر" />
                <input style="display:none" id="main_logo" value="<?php echo $settings->main_logo; ?>" type="text"
                    name="main_logo">
                <?php if ($settings->main_logo == "") { ?>
                    <img style="display:none;width:200px" id="main_logo_img" src="#">
                <?php } else { ?>
                    <img style="width:200px" id="main_logo_img" src="<?php echo $settings->main_logo; ?>">
                <?php } ?>
            </div>
            <div class="col-12">
                <p class="hd-upanel-label"> لوگو در حالت شب </p>
                <input class="button image" type="button" value="انتخاب تصویر" />
                <input style="display:none" id="main_logo_dark" value="<?php echo $settings->main_logo_dark; ?>"
                    type="text" name="main_logo_dark">
                <?php if ($settings->main_logo_dark == "") { ?>
                    <img style="display:none;width:200px" id="main_logo_dark_img" src="#">
                <?php } else { ?>
                    <img style="width:200px" id="main_logo_dark_img" src="<?php echo $settings->main_logo_dark; ?>">
                <?php } ?>

            </div>

            <div <?php if (isset($settings->template) && $settings->template != '0') {
                echo 'style="display:none"';
            } ?>
                class="col-12">
                <p class="hd-upanel-label"> تصویر زمینه صفحه ثبت نام </p>
                <input class="button image" type="button" value="انتخاب تصویر" />
                <input style="display:none" value="<?php echo $settings->login_background; ?>" type="text"
                    name="login_background">
                <?php if ($settings->login_background == "") { ?>
                    <img style="display:none;width:200px" src="#">
                <?php } else { ?>
                    <img style="width:200px" src="<?php echo $settings->login_background; ?>">
                <?php } ?>

            </div>
            <div <?php if (isset($settings->template) && $settings->template != '0') {
                echo 'style="display:none"';
            } ?>
                class="col-12">
                <p class="hd-upanel-label">محتوای متنی صفحه ورود و ثبت نام</p>
                <?php wp_editor($settings->upanel_login_content, 'upanel_login_content'); ?>

            </div>
            <div <?php if (isset($settings->template) && $settings->template != '0') {
                echo 'style="display:none"';
            } ?>
                class="col-12">
                <p class="hd-upanel-label"> رنگ محتوای متنی صفحه ورود و ثبت نام </p>
                <input type="color" name="upanel_login_content_color"
                    value="<?php echo $settings->upanel_login_content_color; ?>">
            </div>

        </div>
        <p class="divider"></p>

        <p class="hd-upanel-label">متن کپی رایت </p>
        <input name="copyright" type="text" value="<?php echo $settings->copyright; ?>" class="hd-upanel-input">

        <p class="divider"></p>


        <p class="hd-upanel-label">حالت شب</p>
        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="dark_mode">
            <input type="checkbox" <?php if ($settings->dark_mode == 'on') {
                echo 'checked="on"';
            } ?> name="dark_mode"
                id="dark_mode" class="mdl-switch__input">
            <span class="mdl-switch__label"> قابلیت تنظیم حالت شب توسط کاربر</span>
        </label>

        <p class="divider"></p>
        <p class="hd-upanel-label"> شبکه های اجتماعی</p>
        <?php foreach ($social as $key => $value) {
            $label = $value . '_active';
            ?>
            <div class="row">
                <div class="col-2">
                    <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="<?php echo $value; ?>_active">
                        <input type="checkbox" name="<?php echo $value; ?>_active" <?php if ($settings->$label == 'on') {
                               echo 'checked="on"';
                           } ?> id="<?php echo $value; ?>_active" class="mdl-switch__input">
                        <span class="mdl-switch__label"> <?php echo $value; ?></span>
                    </label>
                </div>
                <div class="col-9">
                    <p style="margin:0;display:inline-block" class="hd-upanel-label"> آدرس </p>
                    <input name="<?php echo $value; ?>" type="text" value="<?php if (isset($settings->$value)) {
                           echo $settings->$value;
                       } else {
                           echo '';
                       } ?>" class="hd-upanel-input">
                </div>
            </div>
        <?php } ?>



        <?php
        global $wp_roles;

        $all_roles = $wp_roles->roles;
        $editable_roles = apply_filters('editable_roles', $all_roles);

        ?>
        <p class="divider"></p>
        <p class="hd-upanel-label">مدیریت دسترسی نقش های کاربری به Upanel</p>
        <?php foreach ($editable_roles as $key => $value) {
            $role = 'role-' . strtolower(str_replace(" ", "_", $value['name']));
            ?>
            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="<?php echo $role; ?>">
                <input <?php if ($value['name'] == 'Administrator') {
                    echo 'disabled';
                } ?> type="checkbox"
                    name="<?php echo $role; ?>" <?php if ((isset($settings->$role) && $settings->$role == 'on') || ($value['name'] == 'Administrator')) {
                           echo 'checked="on"';
                       } ?> id="<?php echo $role; ?>"
                    class="mdl-switch__input">
                <span class="mdl-switch__label"><?php echo $value['name']; ?></span> </span>
            </label>
        <?php } ?>




        <p class="divider"></p>
        <p class="hd-upanel-label"> تنظیمات نوار بالای صفحه</p>
        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="avatar">
            <input type="checkbox" name="avatar" <?php if ($settings->avatar == 'on') {
                echo 'checked="on"';
            } ?>
                id="avatar" class="mdl-switch__input">
            <span class="mdl-switch__label"> آواتار کاربر</span>
        </label>
        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="home_link">
            <input type="checkbox" name="home_link" <?php if ($settings->home_link == 'on') {
                echo 'checked="on"';
            } ?>
                id="home_link" class="mdl-switch__input">
            <span class="mdl-switch__label"> لینک به صفحه اصلی</span>
        </label>
        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="account">
            <input type="checkbox" name="account" <?php if ($settings->account == 'on') {
                echo 'checked="on"';
            } ?>
                id="account" class="mdl-switch__input">
            <span class="mdl-switch__label"> نوع حساب کاربری</span>
        </label>
        <p class="divider"></p>
        <p class="hd-upanel-label"> سایر تنظیمات </p>
        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="logout_popup_image">
            <input type="checkbox" name="logout_popup_image" <?php
            if (!isset($settings->logout_popup_image))
                $settings->logout_popup_image = 'on';
            if ($settings->logout_popup_image == 'on') {
                echo 'checked="on"';
            } ?>
                id="logout_popup_image" class="mdl-switch__input">
            <span class="mdl-switch__label"> تصویر پاپ آپ خروج</span>
        </label>
        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="basket_image_dashboard">
            <input type="checkbox" name="basket_image_dashboard" <?php
            if (!isset($settings->basket_image_dashboard))
                $settings->basket_image_dashboard = 'on';
            if ($settings->basket_image_dashboard == 'on') {
                echo 'checked="on"';
            } ?> id="basket_image_dashboard" class="mdl-switch__input">
            <span class="mdl-switch__label">سبد اطلاعات سفارش‌ها</span>
        </label>
    </div>
</div>