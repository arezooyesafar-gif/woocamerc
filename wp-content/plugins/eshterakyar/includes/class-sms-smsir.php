<?php
/**
 * کلاس ارسال پیامک از طریق سامانه SMS.ir
 * دو متد:
 * 1. Bulk - ارسال از خط اختصاصی: https://api.sms.ir/v1/send/bulk
 * 2. Verify - ارسال از خط خدماتی (قالب/پترن): https://api.sms.ir/v1/send/verify
 * هدر احراز هویت: X-API-KEY
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-sms-base.php';

class Eshterakyar_SMS_Smsir extends Eshterakyar_SMS_Base {

    const API_BULB   = 'https://api.sms.ir/v1/send/bulk';
    const API_VERIFY = 'https://api.sms.ir/v1/send/verify';

    /**
     * نام سامانه
     */
    public function get_name() {
        return __('SMS.ir', 'eshterakyar');
    }

    /**
     * ارسال پیامک
     *
     * @param string $phone شماره موبایل
     * @param string $message متن پیامک
     * @param array $args api_key, line_number (برای Bulk), template_id + template_parameters (برای Verify)
     *   - template_parameters: آرایه [ name => value ] یا آرایه مقادیر (با parameter_names برای نام‌گذاری)
     *   - parameter_names: رشته جدا شده با کاما برای نام پارامترها در Verify (مثلاً userName,planName,endDate)
     * @return array نتیجه ارسال
     */
    public function send($phone, $message, $args = array()) {
        $api_key = isset($args['api_key']) ? trim($args['api_key']) : '';
        if (empty($api_key)) {
            $this->log_error('API Key is empty');
            return array('success' => false, 'message' => __('API Key مربوط به SMS.ir الزامی است', 'eshterakyar'));
        }

        $phone = $this->format_phone($phone);
        $mobile = $this->format_mobile($phone);

        $template_id = isset($args['template_id']) ? $args['template_id'] : 0;
        $template_parameters = isset($args['template_parameters']) ? $args['template_parameters'] : null;
        if ($template_parameters === null && !empty($args['template_vars']) && is_array($args['template_vars'])) {
            $template_parameters = $args['template_vars'];
        }
        $parameter_names = isset($args['parameter_names']) ? $args['parameter_names'] : '';

        if ($template_id && is_array($template_parameters) && count($template_parameters) > 0) {
            return $this->send_verify($api_key, $mobile, (int) $template_id, $template_parameters, $parameter_names);
        }

        return $this->send_bulk($api_key, $args, $mobile, $message);
    }

    /**
     * متد Bulk - ارسال از خط اختصاصی
     */
    private function send_bulk($api_key, $args, $mobile, $message_text) {
        $line_number = isset($args['line_number']) ? trim($args['line_number']) : '';
        if (empty($line_number)) {
            $this->log_error('Line number is required for Bulk');
            return array('success' => false, 'message' => __('شماره خط اختصاصی برای ارسال Bulk الزامی است', 'eshterakyar'));
        }

        if (!empty($args['template_vars']) && is_array($args['template_vars'])) {
            $message_text = implode(' ', array_map('trim', $args['template_vars']));
        } else {
            $message_text = preg_replace('/^@\d+@/', '', $message_text);
        }
        $message_text = trim($message_text);
        if (empty($message_text)) {
            $this->log_error('Message is empty');
            return array('success' => false, 'message' => __('متن پیامک خالی است', 'eshterakyar'));
        }

        $line_number_numeric = preg_replace('/[^0-9]/', '', $line_number);
        if (empty($line_number_numeric)) {
            $line_number_numeric = $line_number;
        }

        $body = array(
            'lineNumber'   => (int) $line_number_numeric,
            'messageText'  => $message_text,
            'mobiles'      => array($mobile),
            'sendDateTime' => null,
        );

        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('ارسال پیامک SMS.ir (Bulk)', array(
                'mobile' => $mobile,
                'lineNumber' => $line_number_numeric,
                'message_length' => strlen($message_text),
            ));
        }

        $response = wp_remote_post(self::API_BULB, array(
            'timeout' => 15,
            'body'    => wp_json_encode($body),
            'headers' => array(
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'X-API-KEY'    => $api_key,
            ),
        ));

        return $this->parse_response($response, 'Bulk');
    }

    /**
     * متد Verify - ارسال از خط خدماتی با قالب (پترن)
     */
    private function send_verify($api_key, $mobile, $template_id, $template_parameters, $parameter_names = '') {
        $names = array();
        if (is_string($parameter_names) && $parameter_names !== '') {
            $names = array_map('trim', explode(',', $parameter_names));
        }

        $parameters = array();
        $idx = 0;
        foreach ($template_parameters as $key => $value) {
            $name = is_string($key) ? $key : (isset($names[$idx]) ? $names[$idx] : 'PARAMETER' . ($idx + 1));
            $parameters[] = array('name' => $name, 'value' => (string) $value);
            $idx++;
        }

        $body = array(
            'mobile'     => $mobile,
            'templateId' => $template_id,
            'parameters' => $parameters,
        );

        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('ارسال پیامک SMS.ir (Verify)', array(
                'mobile' => $mobile,
                'templateId' => $template_id,
                'parameters_count' => count($parameters),
            ));
        }

        $response = wp_remote_post(self::API_VERIFY, array(
            'timeout' => 15,
            'body'    => wp_json_encode($body),
            'headers' => array(
                'Content-Type' => 'application/json',
                'Accept'       => 'text/plain',
                'x-api-key'    => $api_key,
            ),
        ));

        return $this->parse_response($response, 'Verify');
    }

    /**
     * فرمت شماره برای API (مثلاً 09121234567)
     */
    private function format_mobile($phone) {
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
     * تفسیر پاسخ یکپارچه SMS.ir (status: 1 موفق، message، data)
     */
    private function parse_response($response, $method_label = '') {
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
            $this->log_error('Invalid JSON: ' . substr($body_raw, 0, 200));
            return array(
                'success' => false,
                'message' => __('پاسخ سرویس معتبر نیست', 'eshterakyar'),
            );
        }

        $status = isset($data['status']) ? (int) $data['status'] : 0;
        $message = isset($data['message']) ? $data['message'] : '';

        if ($status === 1) {
            $ids = isset($data['data']) && is_array($data['data']) ? $data['data'] : array();
            $rec_id = !empty($ids) ? $ids[0] : null;
            return array(
                'success' => true,
                'message'  => __('پیامک با موفقیت ارسال شد', 'eshterakyar'),
                'rec_id'   => $rec_id,
            );
        }

        $error_message = $this->get_error_message($code, $message);
        return array(
            'success'    => false,
            'message'    => $error_message,
            'error_code' => $code,
        );
    }

    /**
     * متن خطا بر اساس HTTP status و پیام سرویس
     */
    private function get_error_message($http_code, $server_message = '') {
        $messages = array(
            400 => __('خطای منطقی در درخواست', 'eshterakyar'),
            401 => __('خطا در احراز هویت (API Key)', 'eshterakyar'),
            429 => __('تعداد درخواست غیرمجاز؛ بعداً تلاش کنید', 'eshterakyar'),
            500 => __('خطای سرور؛ بعداً تلاش کنید', 'eshterakyar'),
        );
        if (isset($messages[$http_code])) {
            $msg = $messages[$http_code];
            if (!empty($server_message)) {
                $msg .= ' — ' . $server_message;
            }
            return $msg;
        }
        return !empty($server_message) ? $server_message : __('خطای ارسال پیامک', 'eshterakyar');
    }

    /**
     * اعتبارسنجی تنظیمات
     */
    public function validate_settings($settings) {
        $api_key = isset($settings['sms_smsir_api_key']) ? trim($settings['sms_smsir_api_key']) : '';
        if (empty($api_key)) {
            return __('API Key مربوط به SMS.ir الزامی است', 'eshterakyar');
        }
        $provider = isset($settings['sms_provider']) ? $settings['sms_provider'] : '';
        if ($provider === 'smsir') {
            $line = isset($settings['sms_smsir_line_number']) ? trim($settings['sms_smsir_line_number']) : '';
            if (empty($line)) {
                return __('شماره خط اختصاصی برای ارسال Bulk الزامی است', 'eshterakyar');
            }
        }
        return true;
    }
}
