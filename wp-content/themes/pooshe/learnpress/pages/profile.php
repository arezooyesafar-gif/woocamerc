<?php
/**
 * Template for displaying main user profile page.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.1
 */

use LearnPress\Helpers\Template;

defined( 'ABSPATH' ) || exit();

if ( ! isset( $profile ) ) {
	return;
}
$active_tab = "login";
//$profile->main_class();
?>
<?php if (is_user_logged_in()){ ?>
<?php if ( $profile->is_public() || $profile->get_user()->is_guest() ) : ?>
<div id="learn-press-profile" <?php $profile->main_class(); ?>>
    <?php do_action( 'learn-press/before-user-profile', $profile ); ?>
    <div class="lp-content-area">
        <?php
        if ( ! is_user_logged_in() ) {
            learn_press_print_messages( true );
        }

        /**
         * @since 3.0.0
         */
        do_action( 'learn-press/user-profile', $profile );
        ?>
    </div>
</div>
<?php else : ?>
        <div class="lp-content-area">
            <?php
            if ( ! is_user_logged_in() ) {
                learn_press_print_messages( true );
            }

            if ( $profile->get_user() instanceof LP_User && $profile->get_user()->can_create_course() ) {
                $data = [
                    'instructor_id' => $profile->get_user()->get_id(),
                ];
                do_action( 'learn-press/single-instructor/layout', $data );
            }
            ?>
        </div>
<?php endif; ?>
<?php }else{ ?>
    <div class="pooshe-userlogin " id="customer_login learn-press-profile">
        <ul class="nav nav-pills mb-3" id="myaccount-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link<?php echo $active_tab == "login"?" active":"" ?>" id="login-tab" data-bs-toggle="pill" href="#pills-login" role="tab" aria-controls="pills-login" aria-selected="true">
                    <i class="far fa-sign-in"></i>
                    <span class="title"><?php _e('ورود','pooshe'); ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?php echo $active_tab == "register"?" active":"" ?>" id="register-tab" data-bs-toggle="pill" href="#pills-register" role="tab" aria-controls="pills-register" aria-selected="false">
                    <i class="far fa-user-plus"></i>
                    <span class="title"><?php _e('ثبت نام','pooshe'); ?></span>
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myaccount-tabContent">
            <div class="tab-pane fade<?php echo $active_tab == "login"?" show active":"" ?>" id="pills-login" role="tabpanel" aria-labelledby="login-tab">
                <div class="pooshe-login-tab">
                    <div class="pooshe-card">

                        <h2><?php _e('ورود به حساب کاربری','pooshe'); ?></h2>
                        <?php do_action( 'learn-press/before-user-profile', $profile ); ?>
                        <div class="learn-press-form-login learn-press-form learn-press-form p-0 border-0">
                            <?php do_action( 'learn-press/before-form-login' ); ?>

                            <form name="learn-press-login" method="post" action="">

                                <?php do_action( 'learn-press/before-form-login-fields' ); ?>

                                <ul class="form-fields">
                                    <li class="form-field">
                                        <label for="username"><?php esc_html_e( 'Username or email', 'learnpress' ); ?>&nbsp;<span class="required">*</span></label>
                                        <input type="text" name="username" id="username" placeholder="<?php esc_attr_e( 'Email or username', 'learnpress' ); ?>" autocomplete="username" />
                                    </li>
                                    <li class="form-field">
                                        <label for="password"><?php esc_html_e( 'Password', 'learnpress' ); ?>&nbsp;<span class="required">*</span></label>
                                        <input type="password" name="password" id="password" placeholder="<?php esc_attr_e( 'Password', 'learnpress' ); ?>" autocomplete="current-password" />
                                    </li>
                                </ul>

                                <?php do_action( 'learn-press/after-form-login-fields' ); ?>
                                <p>
                                    <label>
                                        <input type="checkbox" name="rememberme"/>
                                        <?php esc_html_e( 'Remember me', 'learnpress' ); ?>
                                    </label>
                                </p>

                                <?php do_action( 'login_form' ); ?>

                                <p>
                                    <input type="hidden" name="learn-press-login-nonce" value="<?php echo wp_create_nonce( 'learn-press-login' ); ?>">
                                    <button type="submit"><?php esc_html_e( 'Login', 'learnpress' ); ?></button>
                                </p>
                                <p>
                                    <a href="<?php echo wp_lostpassword_url(); ?>"><?php esc_html_e( 'Lost your password?', 'learnpress' ); ?></a>
                                </p>
                            </form>

                            <?php do_action( 'learn-press/after-form-login' ); ?>

                        </div>


                    </div>
                </div>
            </div>
            <div class="tab-pane fade<?php echo $active_tab == "register"?" show active":"" ?>" id="pills-register" role="tabpanel" aria-labelledby="pills-register">
                <div class="pooshe-register-tab">
                    <div class="pooshe-card">
                        <h2><?php esc_html_e( 'ثبت نام در', 'pooshe' ); ?> <?php bloginfo('name'); ?></h2>

                        <div class="learn-press-form-register learn-press-form p-0 border-0">
                            <?php do_action( 'learn-press/before-form-register' ); ?>

                            <form name="learn-press-register" method="post" action="">

                                <ul class="form-fields">

                                    <?php do_action( 'learn-press/before-form-register-fields' ); ?>

                                    <li class="form-field">
                                        <label for="reg_email"><?php esc_html_e( 'Email address', 'learnpress' ); ?>&nbsp;<span class="required">*</span></label>
                                        <input id ="reg_email" name="reg_email" type="text" placeholder="<?php esc_attr_e( 'Email', 'learnpress' ); ?>" autocomplete="email" value="<?php echo esc_attr( LP_Helper::sanitize_params_submitted( $_POST['reg_email'] ?? '' ) ); ?>">
                                    </li>
                                    <li class="form-field">
                                        <label for="reg_username"><?php esc_html_e( 'Username', 'learnpress' ); ?>&nbsp;<span class="required">*</span></label>
                                        <input id ="reg_username" name="reg_username" type="text" placeholder="<?php esc_attr_e( 'Username', 'learnpress' ); ?>" autocomplete="username" value="<?php echo esc_attr( LP_Helper::sanitize_params_submitted( $_POST['reg_username'] ?? '' ) ); ?>">
                                    </li>
                                    <li class="form-field">
                                        <label for="reg_password"><?php esc_html_e( 'Password', 'learnpress' ); ?>&nbsp;<span class="required">*</span></label>
                                        <input id ="reg_password" name="reg_password" type="password" placeholder="<?php esc_attr_e( 'Password', 'learnpress' ); ?>" autocomplete="new-password">
                                    </li>
                                    <li class="form-field">
                                        <label for="reg_password2"><?php esc_html_e( 'Confirm Password', 'learnpress' ); ?>&nbsp;<span class="required">*</span></label>
                                        <input id ="reg_password2" name="reg_password2" type="password" placeholder="<?php esc_attr_e( 'Password', 'learnpress' ); ?>" autocomplete="off">
                                    </li>

                                    <?php do_action( 'learn-press/after-form-register-fields' ); ?>
                                </ul>

                                <?php do_action( 'register_form' ); ?>

                                <p>
                                    <?php wp_nonce_field( 'learn-press-register', 'learn-press-register-nonce' ); ?>
                                    <button type="submit"><?php esc_html_e( 'Register', 'learnpress' ); ?></button>
                                </p>

                            </form>

                            <?php do_action( 'learn-press/after-form-register' ); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

