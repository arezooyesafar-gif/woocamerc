<div class="mdl-card mdl-shadow--2dp tab" id="login-tab" style="width: 100%;display:none">
    <div class="mdl-card__supporting-text">

        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="login_page">
            <input type="checkbox" <?php if (isset($settings->login_page) && $settings->login_page == 'on') {
                echo 'checked="on"';
            }
            if (!isset($settings->login_page)) {
                echo 'checked="on"';
            } ?> name="login_page" id="login_page" class="mdl-switch__input">
            <span class="mdl-switch__label"> وضعیت فعال بودن صفحه ورود و ثبت نام</span>
        </label>

        <p class="divider"></p>

        <div class="row">
            <div class="col-6">
                <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="login_sms">
                    <input type="checkbox" <?php if (isset($settings->login_sms) && $settings->login_sms == 'on') {
                        echo 'checked="on"';
                    }
                    if (!isset($settings->login_sms)) {
                        echo 'checked="on"';
                    } ?> name="login_sms" id="login_sms" class="mdl-switch__input">
                    <span class="mdl-switch__label"> ارسال پیامک در ورود به حساب</span>
                </label>
            </div>
            <div class="col-6">
                <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="register_sms">
                    <input type="checkbox" <?php if (isset($settings->register_sms) && $settings->register_sms == 'on') {
                        echo 'checked="on"';
                    }
                    if (!isset($settings->register_sms)) {
                        echo 'checked="on"';
                    } ?> name="register_sms" id="register_sms" class="mdl-switch__input">
                    <span class="mdl-switch__label"> ارسال پیامک در ثبت نام</span>
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <p class="hd-upanel-label"> ورود با</p>
                <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="login_by_mobile">
                    <input type="checkbox" id="login_by_mobile" name="login_by_mobile" class="mdl-checkbox__input" <?php if (isset($settings->login_by_mobile) && $settings->login_by_mobile == 'on') {
                        echo 'checked="on"';
                    }
                    if (!isset($settings->login_by_mobile)) {
                        echo 'checked="on"';
                    } ?>>
                    <span class="mdl-checkbox__label">شماره تلفن همراه</span>
                </label>
                <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="login_by_email">
                    <input type="checkbox" id="login_by_email" name="login_by_email" class="mdl-checkbox__input" <?php if (isset($settings->login_by_email) && $settings->login_by_email == 'on') {
                        echo 'checked="on"';
                    }
                    if (!isset($settings->login_by_email)) {
                        echo 'checked="on"';
                    } ?>>
                    <span class="mdl-checkbox__label">ایمیل</span>
                </label>
                <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="login_by_username">
                    <input type="checkbox" id="login_by_username" name="login_by_username" class="mdl-checkbox__input" <?php if (isset($settings->login_by_username) && $settings->login_by_username == 'on') {
                        echo 'checked="on"';
                    }
                    if (!isset($settings->login_by_username)) {
                        echo 'checked="on"';
                    } ?>>
                    <span class="mdl-checkbox__label">نام کاربری</span>
                </label>
            </div>
            <div class="col-6">
                <p class="hd-upanel-label"> ثبت نام با</p>
                <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="register_by_mobile">
                    <input type="checkbox" id="register_by_mobile" name="register_by_mobile" class="mdl-checkbox__input" <?php if (isset($settings->register_by_mobile) && $settings->register_by_mobile == 'on') {
                        echo 'checked="on"';
                    }
                    if (!isset($settings->register_by_mobile)) {
                        echo 'checked="on"';
                    } ?>>
                    <span class="mdl-checkbox__label">شماره تلفن همراه</span>
                </label>
                <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="register_by_email">
                    <input type="checkbox" id="register_by_email" name="register_by_email" class="mdl-checkbox__input" <?php if (isset($settings->register_by_email) && $settings->register_by_email == 'on') {
                        echo 'checked="on"';
                    }
                    if (!isset($settings->register_by_email)) {
                        echo 'checked="on"';
                    } ?>>
                    <span class="mdl-checkbox__label">ایمیل</span>
                </label>

            </div>
        </div>

        <p class="divider"></p>
        <p class="hd-upanel-label">فیلدهای ثبت نام</p>

        <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="register_firstname">
            <input type="checkbox" id="register_firstname" name="register_firstname" class="mdl-checkbox__input" <?php if (isset($settings->register_firstname) && $settings->register_firstname == 'on') {
                echo 'checked="on"';
            }
            if (!isset($settings->register_firstname)) {
                echo 'checked="on"';
            } ?>>
            <span class="mdl-checkbox__label">نام</span>
        </label>
        <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="register_lastname">
            <input type="checkbox" id="register_lastname" name="register_lastname" class="mdl-checkbox__input" <?php if (isset($settings->register_lastname) && $settings->register_lastname == 'on') {
                echo 'checked="on"';
            }
            if (!isset($settings->register_lastname)) {
                echo 'checked="on"';
            } ?>>
            <span class="mdl-checkbox__label">نام خانوادگی</span>
        </label>
        <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="register_username">
            <input type="checkbox" id="register_username" name="register_username" class="mdl-checkbox__input" <?php if (isset($settings->register_username) && $settings->register_username == 'on') {
                echo 'checked="on"';
            }
            if (!isset($settings->register_username)) {
                echo 'checked="on"';
            } ?>>
            <span class="mdl-checkbox__label">نام کاربری</span>
        </label>
        <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="register_email">
            <input type="checkbox" id="register_email" name="register_email" class="mdl-checkbox__input" <?php if (isset($settings->register_email) && $settings->register_email == 'on') {
                echo 'checked="on"';
            }
            if (!isset($settings->register_email)) {
                echo 'checked="on"';
            } ?>>
            <span class="mdl-checkbox__label">ایمیل</span>
        </label>
        <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="register_mobile">
            <input type="checkbox" id="register_mobile" name="register_mobile" class="mdl-checkbox__input" <?php if (isset($settings->register_mobile) && $settings->register_mobile == 'on') {
                echo 'checked="on"';
            }
            if (!isset($settings->register_mobile)) {
                echo 'checked="on"';
            } ?>>
            <span class="mdl-checkbox__label">تلفن همراه</span>
        </label>

    </div>
</div>