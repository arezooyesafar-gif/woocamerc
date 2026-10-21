<?php
add_action('wp_ajax_register_update_user_info', 'register_update_user_info');
add_action('wp_ajax_nopriv_register_update_user_info', 'register_update_user_info');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'phpmailer/autoload.php';

function register_update_user_info()
{


    if (isset($_REQUEST['data']['image'])) {

        $info = getimagesizefromstring(base64_decode($_REQUEST['data']['image']));
        $mim = (explode('/', $info['mime'], 2)[1]);
        $imageName = time() . "." . $mim;
        $image = str_replace('data:image/png;base64,', '', $_REQUEST['data']['image']);
        $image = str_replace(' ', '+', $image);
        $location = WP_PLUGIN_DIR . '/uPanel/admin/users/' . $imageName;
        $t = file_put_contents($location, base64_decode($image));

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'hd_upanel_notifications_read';
        if (
            $wpdb->update(
                $table_name,
                array(
                    'image' => str_replace('includes/', 'admin/users/', plugin_dir_url(__FILE__)) . $imageName,
                ),
                array(
                    "user_id" => $_REQUEST['user_id']
                )
            ) === FALSE
        ) {
            echo "false";
        } else {

        }



    }

    $data = ["status" => ""];
    // $table_name = $wpdb->prefix . 'users';
    // if (
    //     $wpdb->update($table_name, array(
    //         'email' => str_replace('includes/', 'admin/users/', plugin_dir_url(__FILE__)) . $imageName,
    //     ),
    //         array(
    //             "user_id" => $_REQUEST['user_id']
    //         )) === FALSE
    // )

    $current_user = wp_get_current_user();
    $user_data = wp_update_user(array('ID' => $current_user->ID, 'first_name' => $_REQUEST['data']["first_name"], 'last_name' => $_REQUEST['data']["last_name"], 'user_email' => $_REQUEST['data']["email"], 'nickname' => $_REQUEST['data']["nickname"]));

    if (is_wp_error($user_data)) {
        // There was an error; possibly this user doesn't exist.
        $data["status"] = 'Error.';
    } else {
        $data["status"] = 'User profile updated.';
    }
    return wp_send_json($data);


}

add_action('wp_ajax_hd_upanel_charge', 'hd_upanel_charge');
add_action('wp_ajax_nopriv_hd_upanel_charge', 'hd_upanel_charge');

function hd_upanel_charge()
{

    $amount = (int) $_REQUEST['data'];



    add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id) use ($amount) {
        $product = wc_get_product($product_id);
        if ($product) {
            $recharge_amount = apply_filters('woo_wallet_rechargeable_amount', round(sanitize_text_field(wp_unslash($amount)), 2));
            $cart_item_data['recharge_amount'] = $recharge_amount;
        }
        return $cart_item_data;
    }, 10, 2);

    $product = get_wallet_rechargeable_product();

    if ($product) {
        if (!is_wallet_rechargeable_cart()) {
            woo_wallet_persistent_cart_update();
        }
        wc()->cart->empty_cart();
        wc()->cart->add_to_cart($product->get_id());

        $redirect_url = apply_filters('', true) ? wc_get_checkout_url() : wc_get_cart_url();
        echo $redirect_url;
        die();
    }

}




add_action('wp_ajax_hd_upanel_add_address', 'hd_upanel_add_address');
add_action('wp_ajax_nopriv_hd_upanel_add_address', 'hd_upanel_add_address');
function hd_upanel_add_address()
{
    global $wpdb;
    $data = $_REQUEST['data'];
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'hd_upanel_addresses';
    if (isset($_REQUEST['data']['id']) && $_REQUEST['data']['id'] != '') {

        if (
            $wpdb->update(
                $table_name,
                $data,
                array(
                    "id" => $_REQUEST['data']['id']
                )
            ) === FALSE
        ) {
            echo "error";
        } else {
            echo "success";
        }

    } else {


        if ($wpdb->insert($table_name, $data) === FALSE) {
            print_r($wpdb);
        } else {
            echo "success";
        }
        die();

    }

}




add_action('wp_ajax_update_notification_readed', 'update_notification_readed');
add_action('wp_ajax_nopriv_update_notification_readed', 'update_notification_readed');
function update_notification_readed()
{

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'hd_upanel_notifications_read';

    if (
        $wpdb->update(
            $table_name,
            array(
                'notification_id' => $_REQUEST['notification_id'],
            ),
            array(
                "user_id" => $_REQUEST['user_id']
            )
        ) === FALSE
    ) {
        echo "error";
    } else {
        echo "success";
    }

}


add_action('wp_ajax_get_notification_content', 'get_notification_content');
add_action('wp_ajax_nopriv_get_notification_content', 'get_notification_content');
function get_notification_content()
{

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'hd_upanel_notifications';
    $id = $_REQUEST['notification_id'];
    $result = $wpdb->get_results("SELECT description FROM $table_name WHERE id=$id");

    echo do_shortcode(stripslashes($result[0]->description));

}


add_action('wp_ajax_nopriv_update_billing_address', 'update_billing_address');
add_action('wp_ajax_update_billing_address', 'update_billing_address');

function update_billing_address() {
    // بررسی ورود کاربر
    if ( ! is_user_logged_in() ) {
        wp_send_json_error(['message' => 'Not logged in']);
    }

    // دریافت داده‌ها
    $user_id = intval($_POST['user_id'] ?? 0);
    $billing_data = isset($_POST['data']) ? (array) $_POST['data'] : [];

    if ( ! $user_id || empty($billing_data) ) {
        wp_send_json_error(['message' => 'Invalid data']);
    }

    // ذخیره امن
    foreach ($billing_data as $key => $value) {
        update_user_meta($user_id, sanitize_key($key), sanitize_text_field($value));
    }

    wp_send_json_success(['message' => 'Address updated']);
}

add_action('wp_ajax_hd-upanel-save-ticket', 'hd_upanel_save_ticket');
add_action('wp_ajax_nopriv_hd-upanel-save-ticket', 'hd_upanel_save_ticket');
function hd_upanel_save_ticket()
{
    global $wpdb;
    $data = $_REQUEST['data'];
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'hd_upanel_tickets';
    if ($wpdb->insert($table_name, $data) === FALSE) {
        print_r($wpdb);




    } else {
        if ($wpdb->update($table_name, array('checked' => 0), array("id" => $data['parent'])) === FALSE) {
            print_r($wpdb);
        } else {
            echo "success";
        }
        echo "success";
    }
    die();
}







add_action('wp_ajax_register_update_user_password', 'register_update_user_password');
add_action('wp_ajax_nopriv_register_update_user_password', 'register_update_user_password');
function register_update_user_password()
{

    $user_id = $_REQUEST['user_id'];
    $user = get_user_by('id', $user_id);

    $password = $_REQUEST['password'];
    $new_password = $_REQUEST['new_password'];


    if (empty($user_id)) {
        $json = array('code' => '0', 'msg' => 'Please enter user id');
        echo json_encode($json);
        exit;
    }
    if (empty($password)) {
        $json = array('code' => '0', 'msg' => 'Please enter old password');
        echo json_encode($json);
        exit;
    }
    if (empty($new_password)) {
        $json = array('code' => '0', 'msg' => 'Please enter new password');
        echo json_encode($json);
        exit;
    }
    $hash = $user->data->user_pass;
    $code = 500;
    $status = false;



    if (wp_check_password($password, $hash)) {
        wp_set_password($new_password, $user_id);
        $json = array('code' => '200', 'msg' => 'Password updated successfully');
        echo json_encode($json);
    } else {
        $json = array('code' => '500', 'msg' => 'Current password does not match.');
        $msg = 'Current password does not match.';
        echo json_encode($json);
    }



}
add_action('wp_ajax_hd_upanel_check_mobile', 'hd_upanel_check_mobile');
add_action('wp_ajax_nopriv_hd_upanel_check_mobile', 'hd_upanel_check_mobile');
function hd_upanel_check_mobile()
{
    // add_user_meta( 1, 'hd_upanel_mobile', 20);

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'hd_upanel_sidemenu';
    $result = $wpdb->get_results("SELECT * FROM $table_name LIMIT 1")[0];
    $all_settings = json_decode($result->all_settings);
    $users_table = $wpdb->prefix . 'users';

    $mobile = $_REQUEST['mobile'];
    $type = $_REQUEST['type'];
    $mobile_digits = substr($mobile, 1);

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $usermeta_table = $wpdb->prefix . 'usermeta';

    $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='hd_upanel_mobile'");
    $result_digits = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='digits_phone_no' ");

    $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE user_email='$mobile'");
    $result_username = $wpdb->get_results("SELECT * FROM $users_table WHERE user_login='$mobile'");

    if (!isset($all_settings->login_by_mobile)) {
        $all_settings->login_by_mobile = 'on';
        $all_settings->login_by_email = 'on';
        $all_settings->login_by_username = 'on';
        $all_settings->register_by_mobile = 'on';
        $all_settings->register_by_email = 'on';
        $all_settings->register_by_username = 'on';
    }

    if (((count($result) > 0 || count($result_digits) > 0) && $all_settings->login_by_mobile == 'on') || (count($result_email) > 0 && $all_settings->login_by_email == 'on') || (count($result_username) > 0 && $all_settings->login_by_username == 'on')) {
        echo 'true';
    } else if ($type == 'mobile' && (count($result) > 0 || count($result_digits) > 0) && $all_settings->login_by_mobile != 'on') {
        echo 'error';
    } else if ($type == 'email' && (count($result_email) > 0) && $all_settings->login_by_email != 'on') {
        echo 'error';
    } else if ($type == 'username' && (count($result_username) > 0) && $all_settings->login_by_username != 'on') {
        echo 'error';
    } else if ($type == 'mobile' && (count($result) == 0 && count($result_digits) == 0) && $all_settings->register_by_mobile != 'on') {
        echo 'error';
    } else if ($type == 'email' && (count($result_email) == 0) && $all_settings->register_by_email != 'on') {
        echo 'error';
    } else if ((count($result) == 0 || count($result_digits) == 0) && count($result_email) == 0 && count($result_username) == 0 && $type != 'mobile' && $type != 'email') {
        echo 'error';
    } else {
        echo 'false';
    }
    die();
    //   if(metadata_exists( 'user', $user_id, 'hd_upanel_mobile' ) ) {

    //  }
}

add_action('wp_ajax_hd_upanel_validate_username', 'hd_upanel_validate_username');
add_action('wp_ajax_nopriv_hd_upanel_validate_username', 'hd_upanel_validate_username');
function hd_upanel_validate_username()
{
    $email = $_REQUEST['email'];
    $username = $_REQUEST['username'];
    $mobile = $_REQUEST['mobile'];

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $users_table = $wpdb->prefix . 'users';
    $usermeta_table = $wpdb->prefix . 'usermeta';
    $result_mobile = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='hd_upanel_mobile'");
    $result_digits = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='digits_phone_no' ");
    $result_user = $wpdb->get_results("SELECT * FROM $users_table WHERE user_login='$username'");
    $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE user_email='$email'");
    $array = array('username' => '', 'email' => '', 'mobile' => '');
    if (count($result_user) > 0) {
        $array['username'] = 'نام کاربری وارد شده تکراری است';
    }
    if (count($result_email) > 0) {
        $array['email'] = 'ایمیل وارد شده تکراری است';
    }
    if (count($result_mobile) > 0 || count($result_digits) > 0) {
        $array['mobile'] = 'تلفن همراه وارد شده تکراری است';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $array['email'] = 'ایمیل وارد شده معتبر نیست';
    }

    print_r(json_encode($array));
    die();
}

add_action('wp_ajax_hd_upanel_check_user_has_email_mobile', 'hd_upanel_check_user_has_email_mobile');
add_action('wp_ajax_nopriv_hd_upanel_check_user_has_email_mobile', 'hd_upanel_check_user_has_email_mobile');

function hd_upanel_check_user_has_email_mobile()
{

    $mobile = $_REQUEST['mobile'];
    $type = $_REQUEST['type'];
    $mobile_digits = substr($mobile, 1);

    global $wpdb;
    $users_table = $wpdb->prefix . 'users';
    $charset_collate = $wpdb->get_charset_collate();
    $usermeta_table = $wpdb->prefix . 'usermeta';

    $email_found = '';
    $mobile_found = '';

    if ($type == 'mobile') {
        $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='hd_upanel_mobile'");
        $result_digits = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='digits_phone_no' ");
        if (count($result) > 0) {
            $user_id = $result[0]->user_id;
            $mobile_found = $mobile;
            $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE ID='$user_id'");
            if (count($result_email) > 0) {
                $email_found = $result_email[0]->user_email;
            }
        }
        if (count($result_digits) > 0) {
            $user_id = $result[0]->user_id;
            $mobile_found = $mobile;
            $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE ID='$user_id'");
            if (count($result_email) > 0) {
                $email_found = $result_email[0]->user_email;
            }
        }


    }
    if ($type == 'email') {
        $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE user_email='$mobile'");
        if (count($result_email) > 0) {
            $email_found = $mobile;
            $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE user_email='$mobile'");
            if (count($result_email) > 0) {
                $user_id = $result_email[0]->ID;
                $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE user_id='$user_id' AND meta_key='hd_upanel_mobile'");
                $result_digits = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE user_id='$user_id' AND meta_key='digits_phone_no' ");
                if (count($result) > 0) {
                    $mobile_found = $result[0]->meta_value;
                }
                if (count($result_digits) > 0) {
                    $mobile_found = $result_digits[0]->meta_value;
                }
            }


        }
    }
    if ($type == 'username') {
        $result_username = $wpdb->get_results("SELECT * FROM $users_table WHERE user_login='$mobile'");
        if (count($result_username) > 0) {
            $email_found = $result_username[0]->user_email;
            $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE user_login='$mobile'");
            if (count($result_email) > 0) {
                $user_id = $result_email[0]->ID;
                $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE user_id='$user_id' AND meta_key='hd_upanel_mobile'");
                $result_digits = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE user_id='$user_id' AND meta_key='digits_phone_no' ");
                if (count($result) > 0) {
                    $mobile_found = $result[0]->meta_value;
                }
                if (count($result_digits) > 0) {
                    $mobile_found = $result_digits[0]->meta_value;
                }
            }


        }

    }




    echo json_encode(["email" => $email_found, "mobile" => $mobile_found]);
    die();
}
add_action('wp_ajax_hd_upanel_send_sms', 'hd_upanel_send_sms');
add_action('wp_ajax_nopriv_hd_upanel_send_sms', 'hd_upanel_send_sms');




function hd_upanel_send_email_code()
{
    $mobile = $_REQUEST['mobile'];
    global $wpdb;
    $table_name = $wpdb->prefix . 'hd_upanel_sidemenu';
    $result = $wpdb->get_results("SELECT * FROM $table_name LIMIT 1")[0];
    $all_settings = json_decode($result->all_settings);

    $users_table = $wpdb->prefix . 'users';
    $charset_collate = $wpdb->get_charset_collate();
    $usermeta_table = $wpdb->prefix . 'usermeta';
    $user_id = -1;
    $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE user_email='$mobile'");
    if (count($result_email) > 0) {
        $email_found = $mobile;
        $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE user_email='$mobile'");
        if (count($result_email) > 0) {
            $user_id = $result_email[0]->ID;
        }
    }


    $code = rand(10000, 99999);
    $wpdb->insert($wpdb->prefix . 'hd_upanel_email_forgot', array(
        'user_id' => $user_id,
        'code' => $code,
    ));

    $msg = "کاربر گرامی  کد تایید شما جهت فراموشی رمز عبور:" . $code . " .می‌باشد  " . "\r\n" . " . اگر شما درخواست فراموشی رمز عبور ارسال نکرده‌اید این ایمیل را نادیده بگیرید" . "\r\n";
    $mail = new PHPMailer(true);
    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';     //Send using SMTP
        $mail->Host = $all_settings->email_host;                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = $all_settings->email_username;                     //SMTP username
        $mail->Password = $all_settings->email_password;                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $all_settings->email_smtp;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients 
        $mail->setFrom($all_settings->email_email, $all_settings->email_name);
        $mail->addAddress($mobile, 's');
        // $mail->addAddress('ellen@example.com'); 
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');

        //Content
        // $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = "فراموشی رمز عبور";
        $mail->Body = $msg;
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        echo 'success';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    die();
}
add_action('wp_ajax_send_email_code', 'hd_upanel_send_email_code');
add_action('wp_ajax_nopriv_send_email_code', 'hd_upanel_send_email_code');


function hd_upanel_reset_password()
{

    $mobile = $_REQUEST['mobile'];
    $sms = $_REQUEST['sms'];
    $type = $_REQUEST['type'];
    $password = $_REQUEST['password'];
    global $wpdb;
    $users_table = $wpdb->prefix . 'users';
    $charset_collate = $wpdb->get_charset_collate();
    $usermeta_table = $wpdb->prefix . 'usermeta';
    $user_id = -1;

    if ($type == 'mobile') {
        $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='hd_upanel_mobile'");
        $result_digits = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='digits_phone_no' ");
        if (count($result) > 0) {
            $user_id = $result[0]->user_id;
        }
        if (count($result_digits) > 0) {
            $user_id = $result[0]->user_id;
        }
    }
    if ($type == 'email') {
        $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE user_email='$mobile'");
        if (count($result_email) > 0) {
            $email_found = $mobile;
            $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE user_email='$mobile'");
            if (count($result_email) > 0) {
                $user_id = $result_email[0]->ID;
            }
        }
    }
    if ($type == 'username') {
        $result_username = $wpdb->get_results("SELECT * FROM $users_table WHERE user_login='$mobile'");
        if (count($result_username) > 0) {
            $email_found = $result_username[0]->user_email;
            $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE user_login='$mobile'");
            if (count($result_email) > 0) {
                $user_id = $result_email[0]->ID;
            }
        }
    }

    $mail_forgot = $wpdb->prefix . 'hd_upanel_email_forgot';
    $result_email = $wpdb->get_results("SELECT * FROM $mail_forgot WHERE user_id='$user_id' AND code='$sms' ORDER BY id desc LIMIT 1");



    $check = hd_upanel_check_login_sms($mobile, $sms);
    if ($check == 'true' || count($result_email) > 0) {
        wp_set_password($password, $user_id);
        echo "true";
    } else {
        echo "false";
    }
    die();
}

add_action('wp_ajax_hd_upanel_reset_password', 'hd_upanel_reset_password');
add_action('wp_ajax_nopriv_hd_upanel_reset_password', 'hd_upanel_reset_password');


function hd_upanel_send_sms()
{
    $mobile = $_REQUEST['mobile'];
    $sms_type = $_REQUEST['sms_type'];

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $users_table = $wpdb->prefix . 'users';
    $type = $_REQUEST['type'];
    $usermeta_table = $wpdb->prefix . 'usermeta';



    $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='hd_upanel_mobile'");
    $result_digits = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='digits_phone_no' ");
    $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE user_email='$mobile'");
    $result_username = $wpdb->get_results("SELECT * FROM $users_table WHERE user_login='$mobile'");


    $userid = '';
    if ($type == 'mobile' && count($result) > 0) {
        $userid = $result[0]->user_id;
    } else if ($type == 'mobile' && count($result_digits) > 0) {
        $userid = $result_digits[0]->user_id;
    } else if ($type == 'email') {
        $userid = $result_email[0]->ID;
        $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE user_id='$userid' AND meta_key='hd_upanel_mobile'");
        if (count($result) > 0)
            $mobile = $result[0]->meta_value;
        else
            $mobile = '';
    }

    if ($type == 'username') {
        $userid = $result_username[0]->ID;
        $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE user_id='$userid' AND meta_key='hd_upanel_mobile'");
        if (count($result) > 0)
            $mobile = $result[0]->meta_value;
        else
            $mobile = '';
    }

    if (strlen($mobile) > 0) {



        if ($sms_type == 'login')
            echo json_encode(hd_upanel_get_current_provider()->sendSMS($mobile, get_option('upanel_sms_content'), rand(10000, 99999), 'login'));
        else
            echo json_encode(hd_upanel_get_current_provider()->sendSMS($mobile, get_option('upanel_sms_reset_password'), rand(10000, 99999), 'password'));



    } else {
        echo json_encode(["status" => "notfound", "data" => null, "message" => null]);
    }



    die();
}



add_action('wp_ajax_hd_upanel_login_password', 'hd_upanel_login_password');
add_action('wp_ajax_nopriv_hd_upanel_login_password', 'hd_upanel_login_password');

function hd_upanel_login_password()
{
    $mobile = $_REQUEST['mobile'];
    $password = $_REQUEST['password'];
    $type = $_REQUEST['type'];
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $users_table = $wpdb->prefix . 'users';
    $usermeta_table = $wpdb->prefix . 'usermeta';
    $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='hd_upanel_mobile'");
    $result_digits = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='digits_phone_no' ");
    if ($type == 'mobile') {
        $result_username = $wpdb->get_results("SELECT * FROM $users_table WHERE user_login='$mobile' ");
        if (count($result_username) > 0) {
            $user = wp_authenticate(get_user_by('id', $result_username[0]->ID)->user_login, $password);
            if (!is_wp_error($user)) {
                echo "true";
                wp_clear_auth_cookie();
                wp_set_current_user($result_username[0]->ID);
                wp_set_auth_cookie($result_username[0]->ID);

                die();
            }
        } else if (count($result) > 0) {

            $user = wp_authenticate(get_user_by('id', $result[0]->user_id)->user_login, $password);
            if (!is_wp_error($user)) {
                echo "true";
                wp_clear_auth_cookie();
                wp_set_current_user($result[0]->user_id);
                wp_set_auth_cookie($result[0]->user_id);
            } else {
                echo "false";
            }
        } else if (count($result_digits) > 0) {
            $user = wp_authenticate(get_user_by('id', $result[0]->user_id)->user_login, $password);
            if (!is_wp_error($user)) {
                echo "true";
                wp_clear_auth_cookie();
                wp_set_current_user($result[0]->user_id);
                wp_set_auth_cookie($result[0]->user_id);
            } else {
                echo "false";
            }
        }
    } else if ($type == 'username') {
        $result = $wpdb->get_results("SELECT * FROM $users_table WHERE user_login='$mobile' ");
        $user = wp_authenticate(get_user_by('id', $result[0]->ID)->user_login, $password);
        if (!is_wp_error($user)) {
            echo "true";
            wp_clear_auth_cookie();
            wp_set_current_user($result[0]->ID);
            wp_set_auth_cookie($result[0]->ID);
        } else {
            echo "false";
        }
    } else if ($type == 'email') {
        $result = $wpdb->get_results("SELECT * FROM $users_table WHERE user_email='$mobile' ");
        $user = wp_authenticate(get_user_by('id', $result[0]->ID)->user_login, $password);
        if (!is_wp_error($user)) {
            echo "true";
            wp_clear_auth_cookie();
            wp_set_current_user($result[0]->ID);
            wp_set_auth_cookie($result[0]->ID);
        } else {
            echo "false";
        }

    }
    die();
}

add_action('wp_ajax_hd_upanel_register_without_sms', 'hd_upanel_register_without_sms');
add_action('wp_ajax_nopriv_hd_upanel_register_without_sms', 'hd_upanel_register_without_sms');
function hd_upanel_register_without_sms()
{
    $mobile = $_REQUEST['mobile'];
    $is_login = $_REQUEST['is_login'];
    $type = $_REQUEST['type'];
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $users_table = $wpdb->prefix . 'users';
    // $data = json_decode($_REQUEST['data']);
    $usermeta_table = $wpdb->prefix . 'usermeta';
    $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='hd_upanel_mobile'");
    $result_digits = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='digits_phone_no' ");
    $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE user_email='$mobile'");
    $result_username = $wpdb->get_results("SELECT * FROM $users_table WHERE user_login='$mobile'");

    $userid = '';
    if ($type == 'mobile' && count($result) > 0) {
        $userid = $result[0]->user_id;
    } else if ($type == 'mobile' && count($result_digits) > 0) {
        $userid = $result_digits[0]->user_id;
    } else if ($type == 'email') {
        $userid = $result_email[0]->ID;
        $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE user_id='$userid' AND meta_key='hd_upanel_mobile'");
        if (count($result) > 0)
            $mobile = $result[0]->meta_value;
    }
    if ($type == 'username') {
        $userid = $result_username[0]->ID;
        $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE user_id='$userid' AND meta_key='hd_upanel_mobile'");
        if (count($result) > 0)
            $mobile = $result[0]->meta_value;
    }
    $result = 'true';

    if ($is_login == 'false') {
        if ($_REQUEST['data']['username'] == '' && $_REQUEST['data']['mobile'] != '') {
            $_REQUEST['data']['username'] = 'u-' . $_REQUEST['data']['mobile'];
        }
        if ($_REQUEST['data']['username'] == '' && $_REQUEST['data']['email'] != '') {
            $_REQUEST['data']['username'] = 'u-' . $_REQUEST['data']['email'];
        }
        $userdata = array(
            'user_login' => $_REQUEST['data']['username'],
            'user_email' => $_REQUEST['data']['email'],
            'first_name' => $_REQUEST['data']['first_name'],
            'last_name' => $_REQUEST['data']['last_name'],
            'user_pass' => $_REQUEST['data']['password']
        );

        $user_id = wp_insert_user($userdata);
        if (is_wp_error($user_id)) {
            echo $user_id->get_error_message();
        }

        if (is_wp_error($user_id)) {
            $result = 'false';
        } else {

            if ($type == 'mobile') {
                add_user_meta($user_id, 'hd_upanel_mobile', $_REQUEST['data']['mobile']);
            }
            wp_clear_auth_cookie();
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
        }
    } else {
        wp_clear_auth_cookie();
        wp_set_current_user($userid);
        wp_set_auth_cookie($userid);
    }
    echo $result;
    die();
}

add_action('wp_ajax_hd_upanel_verify_sms', 'hd_upanel_verify_sms');
add_action('wp_ajax_nopriv_hd_upanel_verify_sms', 'hd_upanel_verify_sms');
function hd_upanel_verify_sms()
{
    $mobile = $_REQUEST['mobile'];
    $sms = $_REQUEST['sms'];
    $is_login = $_REQUEST['is_login'];
    $type = $_REQUEST['type'];
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $users_table = $wpdb->prefix . 'users';
    // $data = json_decode($_REQUEST['data']);
    $usermeta_table = $wpdb->prefix . 'usermeta';
    $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='hd_upanel_mobile'");
    $result_digits = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE meta_value='$mobile' AND meta_key='digits_phone_no' ");
    $result_email = $wpdb->get_results("SELECT * FROM $users_table WHERE user_email='$mobile'");
    $result_username = $wpdb->get_results("SELECT * FROM $users_table WHERE user_login='$mobile'");

    $userid = '';
    if ($type == 'mobile' && count($result) > 0) {
        $userid = $result[0]->user_id;
    } else if ($type == 'mobile' && count($result_digits) > 0) {
        $userid = $result_digits[0]->user_id;
    } else if ($type == 'email') {
        $userid = $result_email[0]->ID;
        $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE user_id='$userid' AND meta_key='hd_upanel_mobile'");
        if (count($result) > 0)
            $mobile = $result[0]->meta_value;
    }
    if ($type == 'username') {
        $userid = $result_username[0]->ID;
        $result = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE user_id='$userid' AND meta_key='hd_upanel_mobile'");
        if (count($result) > 0)
            $mobile = $result[0]->meta_value;
    }

    $check = hd_upanel_check_login_sms($mobile, $sms);

    if ($check == 'true') {
        if ($is_login == 'false') {
            if ($_REQUEST['data']['username'] == '' && $_REQUEST['data']['mobile'] != '') {
                $_REQUEST['data']['username'] = 'u-' . $_REQUEST['data']['mobile'];
            }
            if ($_REQUEST['data']['username'] == '' && $_REQUEST['data']['email'] != '') {
                $_REQUEST['data']['username'] = 'u-' . $_REQUEST['data']['email'];
            }
            $userdata = array(
                'user_login' => $_REQUEST['data']['username'],
                'user_email' => $_REQUEST['data']['email'],
                'first_name' => $_REQUEST['data']['first_name'],
                'last_name' => $_REQUEST['data']['last_name'],
                'user_pass' => $_REQUEST['data']['password']
            );


            // global $wpdb;

            // $username = sanitize_user($_REQUEST['data']['username']);
            // $password = $_REQUEST['data']['password'];
            // $email = sanitize_email($_REQUEST['data']['email']);
            // $first_name = sanitize_text_field($_REQUEST['data']['first_name']);
            // $last_name = sanitize_text_field($_REQUEST['data']['last_name']);
            // $mobile = sanitize_text_field($_REQUEST['data']['mobile']);

            // $hashed_password = wp_hash_password($password);


            // if (username_exists($username) || email_exists($email)) {
            //     wp_send_json_error(['message' => 'Username or email already exists.']);
            // }

            // $wpdb->insert($wpdb->users, [
            //     'user_login' => $username,
            //     'user_pass' => $hashed_password,
            //     'user_email' => $email,
            //     'user_nicename' => sanitize_title($username),
            //     'display_name' => $first_name . ' ' . $last_name,
            //     'user_registered' => current_time('mysql'),
            //     'user_status' => 0
            // ]);

            // $user_id = $wpdb->insert_id;

            // if ($user_id) {
            //     add_user_meta($user_id, 'first_name', $first_name);
            //     add_user_meta($user_id, 'last_name', $last_name);
            //     if ($type == 'mobile') {
            //         add_user_meta($user_id, 'hd_upanel_mobile', $mobile);
            //     }

            //     $wp_capabilities = [
            //         'subscriber' => true
            //     ];
            //     add_user_meta($user_id, $wpdb->prefix . 'capabilities', $wp_capabilities);
            //     add_user_meta($user_id, $wpdb->prefix . 'user_level', 0);


            // } else {
            //     wp_send_json_error(['message' => 'Failed to insert user.']);
            // }


            $user_id = wp_insert_user($userdata);
            if ($type == 'mobile') {
                add_user_meta($user_id, 'hd_upanel_mobile', $_REQUEST['data']['mobile']);
            }
            wp_clear_auth_cookie();
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
        } else {
            wp_clear_auth_cookie();
            wp_set_current_user($userid);
            wp_set_auth_cookie($userid);
        }
    } else {
    }
    echo trim($check);
    die();
}




