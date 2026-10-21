<?php
function pooshe_smart_login_content()
{
    global $wp;
    $ref = $_GET['ref'] ?? $wp->request;
    ?>
    <div class="smart-login-page">
        <div class="smart-login">
            <i class="sign-up-vector"></i>
            <h2 class="login-title">ورود / عضویت</h2>
            <button class="btn btn-transparent back-btn mb-4" style="display: none" type="button"><i
                        class="fas fa-angle-right"></i> بازگشت به عقب
            </button>
            <div class="smart-login-box">
                <form class="login-form">
                    <div class="form-loading">
                        <span class="spinner"></span>
                    </div>
                    <?php
                    wp_nonce_field('pooshe_login');
                    ?>
                    <input type="hidden" name="ns-redirect"
                           value="<?php echo esc_attr(apply_filters('pooshe_login_redirect', site_url($ref))); ?>">
                    <input type="hidden" name="step" value="1">
                    <div class="login-steps step-1" style="display: block">
                        <p class="field-row mb-5">
                            <label for="mobile">لطفا شماره موبایل یا ایمیل خود را وارد نمایید.</label>
                            <input type="text" id="ns-mobile" placeholder="09330000000"
                                   style="direction: ltr" required autocomplete="off">
                        </p>
                        <div class="login-result"></div>
                        <p class="field-row d-flex justify-content-between align-items-center flex-column">
                            <span>برای ورود و عضویت فقط شماره موبایل کافی است.</span>
                            <button class="btn btn-primary btn-large step-one-submit disabled" type="submit">
                                <i class="fas fa-angle-right"></i>
                                ادامه

                            </button>
                        </p>
                    </div>
                    <div class="login-steps step-2">
                        <p class="field-row mb-3">
                            <label for="auth-code">کد یکبار مصرف را وارد نمایید.</label>
                            <input type="text" placeholder="- - - - -" id="auth-code" name="code" maxlength="5"
                                   minlength="5">
                        </p>
                        <div class="desc mb-5">
						<span style="<?php echo pooshe_is_demo() ? 'opacity: 0.2; margin-bottom: 10px;' : ''; ?>">
						کد ورود به شماره <b class="user-mobile">00000000000</b> ارسال شد.
						<span class="sms-timer">00:00</span>
							</span>

                            <?php
                            if (pooshe_is_demo()) {
                                echo '<p style="color: red">برای تست میتوانید از کد 12345 استفاده کنید.</p>';
                            }
                            ?>
                        </div>
                        <div class="login-result"></div>
                        <p class="field-row d-flex justify-content-between flex-column">
                            <span><button type="button"
                                          class="btn btn-large resend-code">دریافت نکردید؟ ارسال مجدد</button></span>
                            <button class="btn btn-primary btn-large disabled auth-login-submit" type="submit">ورود به
                                حساب
                            </button>
                        </p>
                    </div>
                    <div class="login-steps step-3">
                        <p class="field-row mb-5">
                            <label for="ns-password">رمز عبور خود را وارد نمایید.</label>
                            <input type="password" id="ns-password" name="password" class="input-text">
                        </p>
                        <div class="login-result"></div>
                        <p class="field-row d-flex justify-content-between align-items-center flex-column">
						<span><a href="<?php echo esc_url(wp_lostpassword_url()); ?>"
                                 class="forgot-pass-btn">
								رمز عبور خود را فراموش کرده اید؟
							</a></span>
                            <button class="btn btn-primary password-login-submit btn-large disabled" type="submit">ورود
                                به
                                حساب
                            </button>
                        </p>
                    </div>
                </form>
            </div>
            <?php if (is_account_page()) : ?>
                <hr class="mt-5">
                <a href="<?php echo site_url(); ?>" class="btn btn-transparent btn-large w-100"><i
                            class="fas fa-angle-right"></i> بازگشت
                    به
                    سایت</a>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function pooshe_is_mobile(string $mobile)
{
    $mobile = str_replace(array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'), array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9), $mobile);
    preg_match('/^(\+98|98|0098|0)?9\d{9}$/', $mobile, $matches);
    if (empty($matches)) {
        return false;
    }
    if (!empty($matches[0])) {
        return str_replace('+98', '0', $matches[0]);
    }
    return false;
}

function pooshe_smart_login_ajax()
{
    $nonce = wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_nonce'])), 'pooshe_login');
    if ($nonce === false) {
        wp_send_json_error(
            array(
                'message' => 'درخواست نا معتبر است.',
                'code' => 403,
            )
        );
    }

    $email_mobile = sanitize_text_field(wp_unslash($_POST['mobile'] ?? ''));

    if (empty($email_mobile)) {
        wp_send_json_error(
            array(
                'message' => 'لطفا شماره موبایل خود را وارد کنید.',
                'code' => 400,
            )
        );
    }

    // Detect login type.
    $login_type = is_email($email_mobile) ? 'email' : false;
    if (false === $login_type && false !== pooshe_is_mobile($email_mobile)) {
        $login_type = 'mobile';
        $email_mobile = pooshe_is_mobile($email_mobile);
    }

    // Find the user.
    $auth = new Pooshe_Smart_Auth();
    $user = $auth->get_user($email_mobile, $login_type);

    $is_login = !empty($user);

    if ('yes' !== get_option('woocommerce_enable_myaccount_registration')) {
        wp_send_json_error(
            array(
                'message' => 'امکان ثبت نام جدید، غیرفعال شده است.',
                'code' => 400,
            )
        );
    }

    // Create user when is mobile and not exists
    if (!$is_login && $login_type === 'mobile') {
        $user = $auth->create_user_by_mobile($email_mobile);
    }

    if ((!$is_login && $login_type === 'email') || !$login_type) {
        wp_send_json_error(
            array(
                'message' => 'حساب کاربری یافت نشد، لطفا با شماره موبایل خود وارد شوید.',
                'code' => 400,
            )
        );
    }

    if ('mobile' === $login_type && !empty($user)) {
        Pooshe_Otf::create_code($user->ID, 'auth');
    }

    wp_send_json_success(
        array(
            'message' => 'کاربر پیدا شد.',
            'login' => array(
                'type' => $login_type,
                'user' => $email_mobile,
                'timer' => Pooshe_Otf::$time,
            ),
            'code' => 200,
        )
    );
}

function pooshe_smart_login_auth_ajax()
{
    $nonce = wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_nonce'])), 'pooshe_login');
    if ($nonce === false) {
        wp_send_json_error(
            array(
                'message' => 'درخواست نا معتبر است.',
                'code' => 403,
            )
        );
    }

    $mobile = sanitize_text_field(wp_unslash($_POST['mobile'] ?? ''));
    $code = sanitize_text_field(wp_unslash($_POST['code'] ?? ''));

    if (empty($mobile) || empty($code)) {
        wp_send_json_error(
            array(
                'message' => 'شماره موبایل یا کد ارسالی نا معتبر است.',
                'code' => 400,
            )
        );
    }

    $auth = new Pooshe_Smart_Auth();

    $login = $auth->login_by_mobile($mobile, $code);

    if ($login === 404) {
        wp_send_json_error(
            array(
                'message' => 'کاربر یافت نشد!',
                'code' => 404,
            )
        );
    }
    if ($login === 0) {
        wp_send_json_error(
            array(
                'message' => 'کد تایید نا معتبر می باشد!',
                'code' => 403,
            )
        );
    }
    if ($login === -1) {
        wp_send_json_error(
            array(
                'message' => 'کد تایید مربوط به عملیات دیگری می باشد!',
                'code' => 403,
            )
        );
    }
    if ($login === -2) {
        wp_send_json_error(
            array(
                'message' => 'کد تایید منقضی شده است!',
                'code' => 403,
            )
        );
    }

    $redirect = esc_url($_POST['redirect'] ?? '');
    $redirect = str_replace(site_url(), '', $redirect);
    wp_send_json_success(
        array(
            'message' => 'وارد شدید! در حال انتقال...',
            'redirect' => empty($redirect) ? get_permalink(get_option('woocommerce_myaccount_page_id')) : site_url($redirect),
            'code' => 200,
        )
    );
}

function pooshe_smart_login_email_pass_ajax()
{
    $nonce = wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_nonce'])), 'pooshe_login');
    if ($nonce === false) {
        wp_send_json_error(
            array(
                'message' => 'درخواست نا معتبر است.',
                'code' => 403,
            )
        );
    }

    $email = sanitize_text_field(wp_unslash($_POST['email'] ?? ''));
    $pass = sanitize_text_field(wp_unslash($_POST['password'] ?? ''));

    if (empty($email) || empty($pass)) {
        wp_send_json_error(
            array(
                'message' => 'رمز عبور نمیتواند خالی باشد.',
                'code' => 400,
            )
        );
    }

    $auth = new Pooshe_Smart_Auth();
    $login = $auth->login_by_email($email, $pass);
    if ($login === true) {
        $redirect = esc_url($_POST['redirect'] ?? '');
        $redirect = str_replace(site_url(), '', $redirect);
        wp_send_json_success(
            array(
                'message' => 'وارد شدید! در حال انتقال...',
                'redirect' => empty($redirect) ? get_permalink(get_option('woocommerce_myaccount_page_id')) : site_url($redirect),
                'code' => 200,
            )
        );
    }

    wp_send_json_error(
        array(
            'message' => is_string($login) ? $login : 'ایمیل یا رمز عبور اشتباه است.',
            'code' => 400,
        )
    );
}

function pooshe_myaccount_mobile_verify_form()
{
    if (empty(pooshe_opt('pooshe_theme_settings_sms', 'sms_gateway'))) {
        return;
    }
    $user = wp_get_current_user();
    $mobile = Pooshe_Smart_Auth::get_user_mobile($user->ID);
    $needActivate = !Pooshe_Smart_Auth::is_active($user->ID);
    $mobileStatus = "<span style='color: green;'>تایید شده</span>";

    if ($needActivate) {
        $mobileStatus = "<span style='color: red;'>در انتظار تایید</span>";
    }
    ?>
    <div class="pooshe-mobile-verify-form step-1 <?php echo $needActivate ? 'need-action' : ''; ?>">
        <?php wp_nonce_field('pooshe_verify', 'ns-nonce'); ?>
        <h3 class="section-heading mb-4">
            <?php
            if ($needActivate) {
                echo 'ثبت شماره موبایل';
            } else {
                echo 'تغییر شماره موبایل';
            }
            ?>
        </h3>
        <p class="woocommerce-form-row woocommerce-form-row--ns-mobile form-row form-row-first">
            <label for="ns-mobile">شماره موبایل <b> (وضعیت: <?php echo $mobileStatus; ?>)</b></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text form-control"
                   name="ns-mobile" id="ns-mobile" autocomplete="mobile" value="<?php echo esc_attr($mobile); ?>"/>
        </p>
        <button type="button" class="send-code btn btn-primary btn-large">ارسال کد تایید</button>
        <div class="login-result"></div>
        <div class="form-loading">
            <span class="spinner"></span>
        </div>
    </div>
    <div class="pooshe-mobile-verify-form step-2" style="display: none">
        <?php wp_nonce_field('pooshe_verify', 'ns-nonce'); ?>
        <input type="hidden" id="ns-mobile-number" value=""/>
        <p class="woocommerce-form-row woocommerce-form-row--ns-mobile-verify form-row form-row-first">
            <span class="d-block w-100 mb-3">کد تایید به شماره <b class="user-mobile">000000000000</b> ارسال شد.</span>
            <label for="ns-mobile-verify">کد تایید &nbsp;<span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text form-control"
                   name="ns-mobile-verify" id="ns-mobile-verify" autocomplete="verify"
                   value=""/>
        </p>
        <button type="button" class="btn btn-primary btn-large verify-code">تایید کد</button>
        <div class="login-result"></div>
        <div class="form-loading">
            <span class="spinner"></span>
        </div>
    </div>
    <?php
}

function pooshe_smart_login_myaccount_verify_mobile_ajax()
{
    $nonce = wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_nonce'])), 'pooshe_verify');
    if ($nonce === false) {
        wp_send_json_error(
            array(
                'message' => 'درخواست نا معتبر است.',
                'code' => 403,
            )
        );
    }

    $mobile = pooshe_is_mobile(sanitize_text_field(wp_unslash($_POST['mobile'] ?? '')));
    $code = sanitize_text_field(wp_unslash($_POST['code'] ?? ''));
    $ref = sanitize_text_field(wp_unslash($_POST['ref'] ?? ''));

    if (empty($mobile)) {
        wp_send_json_error(
            array(
                'message' => 'لطفا شماره موبایل خود را بصورت صحیح وارد نمایید.',
                'code' => 400,
            )
        );
    }
    $exists = Pooshe_Smart_Auth::user_exists($mobile);
    $user = wp_get_current_user();

    if (Pooshe_Smart_Auth::get_user_mobile($user->ID) === $mobile) {
        wp_send_json_error(
            array(
                'message' => 'حساب کاربری شما فعال می باشد.',
                'code' => 400,
            )
        );
    }

    if (false !== $exists && $user->ID !== $exists) {
        wp_send_json_error(
            array(
                'message' => 'این شماره موبایل در سیستم ثبت شده است.',
                'code' => 202,
            )
        );
    }

    if (empty($user) || is_wp_error($user)) {
        wp_send_json_error(
            array(
                'message' => 'لطفا ابتدا وارد حساب کاربری خود شوید.',
                'code' => 403,
            )
        );
    }

    if (empty($code)) {
        Pooshe_Otf::create_code($user->ID, 'verify', $mobile);

        wp_send_json_success(
            array(
                'message' => 'کد تایید ارسال شد.',
                'data' => array(
                    'mobile' => $mobile,
                ),
                'code' => 1,
            )
        );
    }

    $auth = new Pooshe_Smart_Auth();

    $verify = $auth->verify_mobile($mobile, $code);

    if ($verify === 404) {
        wp_send_json_error(
            array(
                'message' => 'لطفا وارد حساب کاربری خود شوید.',
                'code' => 404,
            )
        );
    }
    if ($verify === 202) {
        wp_send_json_error(
            array(
                'message' => 'شماره موبایل برای کاربر دیگری ثبت شده است.',
                'code' => 404,
            )
        );
    }
    if ($verify === 0) {
        wp_send_json_error(
            array(
                'message' => 'کد تایید نا معتبر می باشد!',
                'code' => 403,
            )
        );
    }
    if ($verify === -1) {
        wp_send_json_error(
            array(
                'message' => 'کد تایید مربوط به عملیات دیگری می باشد!',
                'code' => 403,
            )
        );
    }
    if ($verify === -2) {
        wp_send_json_error(
            array(
                'message' => 'کد تایید منقضی شده است!',
                'code' => 403,
            )
        );
    }

    wp_send_json_success(
        array(
            'message' => 'شماره موبایل با موفقیت تایید شد.',
            'code' => 2,
        )
    );
}

function pooshe_smart_login_myaccount_required_fields($account_fields)
{
    if (!empty(pooshe_opt('pooshe_theme_settings_sms', 'sms_gateway'))) {
        unset($account_fields['account_email']);
    }
    return $account_fields;
}

function pooshe_smart_login_redirect()
{
    if (empty(pooshe_opt('pooshe_theme_settings_sms', 'sms_gateway')) || !is_user_logged_in()) {
        return;
    }
    $user = wp_get_current_user();
    if (Pooshe_Smart_Auth::is_active($user->ID)) {
        return;
    }

    if (is_account_page() && !is_edit_account_page()) {
        wp_redirect(wc_customer_edit_account_url());
        die;
    }
}

/**
 * Check is user changed password.
 *
 * @param int $user_id The user id.
 * @return bool
 */
function pooshe_is_user_first_password(int $user_id): bool
{
    return 'yes' === get_user_meta($user_id, 'pooshe_smart_login_first_password', true);
}


function pooshe_is_demo(): bool
{
    return strpos(site_url(), 'moontheme.ir') !== false;
}

add_action('wp_ajax_pooshe_smart_login', 'pooshe_smart_login_ajax');
add_action('wp_ajax_nopriv_pooshe_smart_login', 'pooshe_smart_login_ajax');
add_action('wp_ajax_pooshe_smart_login_auth', 'pooshe_smart_login_auth_ajax');
add_action('wp_ajax_nopriv_pooshe_smart_login_auth', 'pooshe_smart_login_auth_ajax');
add_action('wp_ajax_pooshe_smart_login_email_pass', 'pooshe_smart_login_email_pass_ajax');
add_action('wp_ajax_nopriv_pooshe_smart_login_email_pass', 'pooshe_smart_login_email_pass_ajax');
add_action('woocommerce_before_edit_account_form', 'pooshe_myaccount_mobile_verify_form');
add_action('wp_ajax_pooshe_smart_login_myaccount_verify_mobile', 'pooshe_smart_login_myaccount_verify_mobile_ajax');
add_action('wp_ajax_nopriv_pooshe_smart_login_myaccount_verify_mobile', 'pooshe_smart_login_myaccount_verify_mobile_ajax');
add_filter('woocommerce_save_account_details_required_fields', 'pooshe_smart_login_myaccount_required_fields');
add_action('template_redirect', 'pooshe_smart_login_redirect');
