<?php
/**
 * کلاس ارسال پیامک از طریق سامانه کاوه نگار (Kavenegar)
 * استفاده از REST API: https://api.kavenegar.com/v1/{API-KEY}/sms/send.json
 * پارامترها: receptor, message, sender (اختیاری)
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-sms-base.php';

class Eshterakyar_SMS_Kavenegar extends Eshterakyar_SMS_Base {

    const API_BASE = 'https://api.kavenegar.com/v1';

    /**
     * نام سامانه
     */
    public function get_name() {
        return __('کاوه نگار', 'eshterakyar');
    }

    /**
     * ارسال پیامک
     *
     * @param string $phone شماره موبایل
     * @param string $message متن پیامک (در صورت وجود template_vars در args از آن به‌عنوان متن نهایی استفاده می‌شود)
     * @param array $args api_key, sender (اختیاری), template_vars (آرایه مقادیر جایگزین شده)
     * @return array|false نتیجه ارسال
     */
    public function send($phone, $message, $args = array()) {
        $api_key = isset($args['api_key']) ? trim($args['api_key']) : '';
        $sender  = isset($args['sender']) ? trim($args['sender']) : '';

        if (empty($api_key)) {
            $this->log_error('API Key is empty');
            return array('success' => false, 'message' => __('API Key کاوه نگار الزامی است', 'eshterakyar'));
        }

        $phone = $this->format_phone($phone);
        $receptor = $this->format_receptor($phone);

        if (!empty($args['template_vars']) && is_array($args['template_vars'])) {
            $message = implode(' ', array_map('trim', $args['template_vars']));
        } else {
            $message = preg_replace('/^@\d+@/', '', $message);
        }

        $message = trim($message);
        if (empty($message)) {
            $this->log_error('Message is empty');
            return array('success' => false, 'message' => __('متن پیامک خالی است', 'eshterakyar'));
        }

        $url = self::API_BASE . '/' . $api_key . '/sms/send.json';
        $body = array(
            'receptor' => $receptor,
            'message'  => $message,
        );
        if (!empty($sender)) {
            $body['sender'] = $sender;
        }

        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('ارسال پیامک کاوه نگار', array(
                'receptor' => $receptor,
                'message_length' => strlen($message),
                'has_sender' => !empty($sender) ? 'yes' : 'no',
            ));
        }

        $response = wp_remote_post($url, array(
            'timeout' => 15,
            'body'   => $body,
            'headers' => array(),
        ));

        if (is_wp_error($response)) {
            $this->log_error('HTTP Error: ' . $response->get_error_message());
            return array(
                'success' => false,
                'message' => __('خطا در اتصال به سرویس: ', 'eshterakyar') . $response->get_error_message(),
            );
        }

        $code = wp_remote_retrieve_response_code($response);
        $body_raw = wp_remote_retrieve_body($response);
        $data = json_decode($body_raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $this->log_error('Invalid JSON response: ' . substr($body_raw, 0, 200));
            return array(
                'success' => false,
                'message' => __('پاسخ سرویس معتبر نیست', 'eshterakyar'),
            );
        }

        $return = isset($data['return']) ? $data['return'] : array();
        $status = isset($return['status']) ? intval($return['status']) : 0;
        $message_text = isset($return['message']) ? $return['message'] : '';

        if ($status === 200) {
            $entries = isset($data['entries']) ? $data['entries'] : array();
            $message_id = null;
            if (isset($entries[0]['messageid'])) {
                $message_id = $entries[0]['messageid'];
            } elseif (!empty($entries) && is_array($entries) && isset($entries['messageid'])) {
                $message_id = $entries['messageid'];
            }
            return array(
                'success'  => true,
                'message'  => __('پیامک با موفقیت ارسال شد', 'eshterakyar'),
                'rec_id'   => $message_id,
            );
        }

        $error_message = $this->get_error_message($status, $message_text);
        return array(
            'success'    => false,
            'message'    => $error_message,
            'error_code' => $status,
        );
    }

    /**
     * فرمت شماره گیرنده برای API کاوه نگار (مثلاً 09121234567)
     */
    private function format_receptor($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 2) === '98') {
            return '0' . substr($phone, 2);
        }
        if (substr($phone, 0, 1) !== '0') {
            return '0' . $phone;
        }
        return $phone;
    }

    /**
     * متن خطا بر اساس کد برگشتی API
     */
    private function get_error_message($status, $default = '') {
        $messages = array(
            400 => __('پارامترها ناقص هستند', 'eshterakyar'),
            401 => __('حساب کاربری غیرفعال است', 'eshterakyar'),
            402 => __('عملیات ناموفق بود', 'eshterakyar'),
            403 => __('API Key معتبر نیست', 'eshterakyar'),
            404 => __('متد یافت نشد', 'eshterakyar'),
            405 => __('متد GET یا POST اشتباه است', 'eshterakyar'),
            409 => __('سرور در دسترس نیست؛ بعداً تلاش کنید', 'eshterakyar'),
            411 => __('شماره گیرنده معتبر نیست', 'eshterakyar'),
            412 => __('شماره فرستنده معتبر نیست', 'eshterakyar'),
            413 => __('متن پیام خالی یا طول آن بیش از حد مجاز است', 'eshterakyar'),
            414 => __('حجم درخواست بیش از حد مجاز است', 'eshterakyar'),
            417 => __('تاریخ ارسال اشتباه یا گذشته است', 'eshterakyar'),
            418 => __('اعتبار حساب کافی نیست', 'eshterakyar'),
            607 => __('نام تگ انتخابی اشتباه است', 'eshterakyar'),
        );
        if (isset($messages[$status])) {
            return $messages[$status];
        }
        return !empty($default) ? $default : __('خطای ارسال پیامک: ', 'eshterakyar') . $status;
    }

    /**
     * اعتبارسنجی تنظیمات
     *
     * @param array $settings تنظیمات (کلیدهای مربوط به کاوه نگار)
     * @return bool|string true در صورت صحیح بودن، یا پیام خطا
     */
    public function validate_settings($settings) {
        $api_key = isset($settings['sms_kavenegar_api_key']) ? trim($settings['sms_kavenegar_api_key']) : '';
        if (empty($api_key)) {
            return __('API Key کاوه نگار الزامی است', 'eshterakyar');
        }
        return true;
    }
}
