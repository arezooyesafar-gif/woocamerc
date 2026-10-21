<?php
/**
 * کلاس ارسال پیامک از طریق سامانه ملی پیامک
 * پشتیبانی از دو متد:
 * 1. SendByBaseNumber3 - برای ارسال با متن پیش‌فرض (bodyId)
 * 2. SendSimpleSMS2 - برای ارسال پیامک ساده
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-sms-base.php';

class Eshterakyar_SMS_Melli extends Eshterakyar_SMS_Base {
    
    /**
     * نام سامانه
     */
    public function get_name() {
        return __('ملی پیامک', 'eshterakyar');
    }
    
    /**
     * ارسال پیامک
     * 
     * @param string $phone شماره موبایل
     * @param string $message متن پیامک یا bodyId
     * @param array $args آرگومان‌های اضافی
     *   - username: نام کاربری
     *   - password: رمز عبور
     *   - sender: شماره فرستنده (برای SendSimpleSMS2)
     *   - body_id: کد متن وب‌سرویس خدماتی (در صورت استفاده از الگو)
     *   - template_vars: متغیرهای الگو (array)
     *   - method: 'SendByBaseNumber3' یا 'SendSimpleSMS2' (اختیاری - خودکار تشخیص داده می‌شود)
     * @return array|false نتیجه ارسال
     */
    public function send($phone, $message, $args = array()) {
        $username = isset($args['username']) ? $args['username'] : '';
        $password = isset($args['password']) ? $args['password'] : '';
        $sender = isset($args['sender']) ? $args['sender'] : '';
        $body_id = isset($args['body_id']) ? $args['body_id'] : '';
        $template_vars = isset($args['template_vars']) ? $args['template_vars'] : array();
        $method = isset($args['method']) ? $args['method'] : '';
        
        if (empty($username) || empty($password)) {
            $this->log_error('Username or password is empty');
            return array('success' => false, 'message' => __('نام کاربری یا رمز عبور خالی است', 'eshterakyar'));
        }
        
        $phone = $this->format_phone($phone);
        
        $wsdl_url = 'http://api.payamak-panel.com/post/Send.asmx?wsdl';
        
        try {
            ini_set("soap.wsdl_cache_enabled", "0");
            
            $client = new SoapClient($wsdl_url, array(
                "encoding" => "UTF-8",
                "trace" => true,
                "exceptions" => true
            ));
            
            $has_body_id_in_message = preg_match('/^@\d+@/', $message);
            
            if (empty($method)) {
                if (!empty($body_id) || $has_body_id_in_message) {
                    $method = 'SendByBaseNumber3';
                } else {
                    $method = 'SendSimpleSMS2';
                }
            }
            
            if ($method === 'SendByBaseNumber3') {
                return $this->send_by_base_number3($client, $phone, $message, $username, $password, $body_id, $template_vars, $has_body_id_in_message);
            } else {
                return $this->send_simple_sms2($client, $phone, $message, $username, $password, $sender);
            }
            
        } catch (SoapFault $e) {
            $this->log_error('SOAP Error: ' . $e->getMessage());
            return array('success' => false, 'message' => __('خطا در اتصال به سامانه: ', 'eshterakyar') . $e->getMessage());
        } catch (Exception $e) {
            $this->log_error('Exception: ' . $e->getMessage());
            return array('success' => false, 'message' => __('خطای غیرمنتظره: ', 'eshterakyar') . $e->getMessage());
        }
    }
    

    private function send_by_base_number3($client, $phone, $message, $username, $password, $body_id, $template_vars, $has_body_id_in_message) {
        if ($has_body_id_in_message) {
            preg_match('/^@(\d+)@/', $message, $matches);
            if (isset($matches[1])) {
                $body_id = $matches[1];
                $vars_string = preg_replace('/^@\d+@/', '', $message);
                if (!empty($vars_string)) {
                    $template_vars = explode(';', $vars_string);
                    $template_vars = array_map('trim', $template_vars);
                    $template_vars = array_filter($template_vars);
                }
            }
        }
        
        if (empty($body_id)) {
            $this->log_error('BodyId is required for SendByBaseNumber3');
            return array('success' => false, 'message' => __('برای استفاده از SendByBaseNumber3، bodyId الزامی است', 'eshterakyar'));
        }
        
        $text = '@' . $body_id . '@';
        if (!empty($template_vars) && is_array($template_vars) && count($template_vars) > 0) {
            $text .= implode(';', $template_vars);
        }
        
        $data = array(
            "username" => $username,
            "password" => $password,
            "text" => $text,
            "to" => $phone
        );
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('استفاده از متد SendByBaseNumber3', array(
                'phone' => $phone,
                'body_id' => $body_id,
                'text' => $text,
                'template_vars_count' => count($template_vars)
            ));
        }
        
        $result = $client->SendByBaseNumber3($data);
        $return_value = $result->SendByBaseNumber3Result;
        
        return $this->parse_result($return_value);
    }
    
    private function send_simple_sms2($client, $phone, $message, $username, $password, $sender) {
        $data = array(
            "username" => $username,
            "password" => $password,
            "to" => $phone,
            "text" => $message
        );
        
        if (!empty($sender)) {
            $data['from'] = $sender;
        }
        
        $data['isflash'] = false;
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('استفاده از متد SendSimpleSMS2', array(
                'phone' => $phone,
                'message_length' => strlen($message),
                'has_sender' => !empty($sender) ? 'yes' : 'no',
                'sender' => $sender
            ));
        }
        
        $result = $client->SendSimpleSMS2($data);
        $return_value = $result->SendSimpleSMS2Result;
        
        return $this->parse_result_simple($return_value);
    }
    
    /**
     * تجزیه و تحلیل نتیجه ارسال (SendByBaseNumber3)
     * 
     * @param string $result نتیجه بازگشتی از سامانه
     * @return array نتیجه پردازش شده
     */
    private function parse_result($result) {
        $messages = array(
            '110' => __('الزام استفاده از ApiKey به جای رمز عبور', 'eshterakyar'),
            '109' => __('الزام تنظیم IP مجاز برای استفاده از API', 'eshterakyar'),
            '108' => __('مسدود شدن IP به دلیل تلاش ناموفق استفاده از API', 'eshterakyar'),
            '-10' => __('در میان متغیرهای ارسالی، لینک وجود دارد', 'eshterakyar'),
            '-8' => __('متن ارسالی، طبق راهنمای مستندات، باید با @ شروع شود', 'eshterakyar'),
            '-7' => __('خطای داخلی رخ داده است. با پشتیبانی تماس بگیرید', 'eshterakyar'),
            '-6' => __('متن ارسالی با توجه به متغیرهای مشخص شده در متن پیش‌فرض همخوانی ندارد', 'eshterakyar'),
            '-5' => __('کد متن ارسالی صحیح نمی‌باشد و یا توسط مدیر سامانه تأیید نشده است', 'eshterakyar'),
            '-4' => __('خط ارسالی در سیستم تعریف نشده است. با پشتیبانی تماس بگیرید', 'eshterakyar'),
            '-9' => __('خط ارسالی در سیستم تعریف نشده است. با پشتیبانی تماس بگیرید', 'eshterakyar'),
            '-3' => __('محدودیت تعداد شماره. محدودیت هر بار ارسال یک شماره موبایل می‌باشد', 'eshterakyar'),
            '-2' => __('طبق راهنمای مستندات، در متن ارسالی، مقدار bodyId درج نشده است', 'eshterakyar'),
            '-1' => __('دسترسی برای استفاده از این وب‌سرویس غیرفعال است. با پشتیبانی تماس بگیرید', 'eshterakyar'),
            '0' => __('نام کاربری یا رمزعبور صحیح نمی‌باشد', 'eshterakyar'),
            '2' => __('اعتبار کافی نمی‌باشد', 'eshterakyar'),
            '6' => __('سامانه در حال بروزرسانی می‌باشد', 'eshterakyar'),
            '7' => __('متن حاوی کلمه فیلتر شده می‌باشد. با واحد اداری تماس بگیرید', 'eshterakyar'),
            '10' => __('کاربر موردنظر فعال نمی‌باشد', 'eshterakyar'),
            '11' => __('ارسال نشده', 'eshterakyar'),
            '12' => __('مدارک کاربر کامل نمی‌باشد', 'eshterakyar'),
            '18' => __('شماره گیرنده نامعتبر است', 'eshterakyar'),
            '19' => __('از محدودیت ساعتی فراتر رفته اید', 'eshterakyar'),
        );
        if (is_numeric($result) && strlen($result) > 15) {
            return array(
                'success' => true,
                'message' => __('پیامک با موفقیت ارسال شد', 'eshterakyar'),
                'rec_id' => $result
            );
        }
        
        if (isset($messages[$result])) {
            return array(
                'success' => false,
                'message' => $messages[$result],
                'error_code' => $result
            );
        }
        
        if (strpos($result, 'recId') !== false) {
            preg_match('/recId\s*:\s*(\d+)/', $result, $matches);
            if (isset($matches[1])) {
                return array(
                    'success' => true,
                    'message' => __('پیامک با موفقیت ارسال شد', 'eshterakyar'),
                    'rec_id' => $matches[1]
                );
            }
        }
        
        return array(
            'success' => false,
            'message' => __('خطای نامشخص: ', 'eshterakyar') . $result,
            'error_code' => $result
        );
    }
    
    /**
     * تجزیه و تحلیل نتیجه ارسال (SendSimpleSMS2)
     * 
     * @param string $result نتیجه بازگشتی از سامانه
     * @return array نتیجه پردازش شده
     */
    private function parse_result_simple($result) {
        $messages = array(
            '110' => __('الزام استفاده از ApiKey به جای رمز عبور', 'eshterakyar'),
            '109' => __('الزام تنظیم IP مجاز برای استفاده از API', 'eshterakyar'),
            '108' => __('مسدود شدن IP به دلیل تلاش ناموفق استفاده از API', 'eshterakyar'),
            '0' => __('نام کاربری یا رمز عبور اشتباه می باشد', 'eshterakyar'),
            '2' => __('اعتبار کافی نمی باشد', 'eshterakyar'),
            '3' => __('محدودیت در ارسال روزانه', 'eshterakyar'),
            '4' => __('محدودیت در حجم ارسال', 'eshterakyar'),
            '5' => __('شماره فرستنده معتبر نمی باشد', 'eshterakyar'),
            '6' => __('سامانه در حال بروزرسانی می باشد', 'eshterakyar'),
            '7' => __('متن حاوی کلمه فیلتر شده می باشد', 'eshterakyar'),
            '9' => __('ارسال از خطوط عمومی از طریق وب سرویس امکان پذیر نمی باشد', 'eshterakyar'),
            '10' => __('کاربر مورد نظر فعال نمی باشد', 'eshterakyar'),
            '11' => __('ارسال نشده', 'eshterakyar'),
            '12' => __('مدارک کاربر کامل نمی باشد', 'eshterakyar'),
            '14' => __('متن حاوی لینک می باشد', 'eshterakyar'),
            '15' => __('ارسال به بیش از 1 شماره همراه بدون درج "لغو11" ممکن نیست', 'eshterakyar'),
            '16' => __('شماره گیرنده ای یافت نشد', 'eshterakyar'),
            '17' => __('متن پیامک خالی می باشد', 'eshterakyar'),
            '18' => __('شماره گیرنده نامعتبر است', 'eshterakyar'),
            '35' => __('شماره در لیست سیاه مخابرات می‌باشد', 'eshterakyar'),
        );
        
        if (is_numeric($result) && $result > 0 && strlen($result) >= 1) {
            if ($result > 1000 || (is_numeric($result) && !isset($messages[$result]))) {
                return array(
                    'success' => true,
                    'message' => __('پیامک با موفقیت ارسال شد', 'eshterakyar'),
                    'rec_id' => $result
                );
            }
        }
        
        if (isset($messages[$result])) {
            return array(
                'success' => false,
                'message' => $messages[$result],
                'error_code' => $result
            );
        }
        
        return array(
            'success' => false,
            'message' => __('خطای نامشخص: ', 'eshterakyar') . $result,
            'error_code' => $result
        );
    }
    
    /**
     * اعتبارسنجی تنظیمات
     * 
     * @param array $settings تنظیمات
     * @return bool|string true در صورت صحیح بودن، یا پیام خطا
     */
    public function validate_settings($settings) {
        if (empty($settings['sms_username'])) {
            return __('نام کاربری الزامی است', 'eshterakyar');
        }
        
        if (empty($settings['sms_password'])) {
            return __('رمز عبور الزامی است', 'eshterakyar');
        }
        
        return true;
    }
}
