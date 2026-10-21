<?php


if (!defined('ABSPATH')) {
    exit;
}

abstract class Eshterakyar_SMS_Base {
    
    /**
     * نام سامانه پیامکی
     */
    abstract public function get_name();
    
    /**
     * ارسال پیامک
     * 
     * @param string $phone شماره موبایل
     * @param string $message متن پیامک
     * @param array $args آرگومان‌های اضافی (مثل template_code)
     * @return array|false نتیجه ارسال
     */
    abstract public function send($phone, $message, $args = array());
    
    /**
     * اعتبارسنجی تنظیمات
     * 
     * @param array $settings تنظیمات
     * @return bool|string true در صورت صحیح بودن، یا پیام خطا
     */
    abstract public function validate_settings($settings);
    
    /**
     * فرمت کردن شماره موبایل
     * 
     * @param string $phone شماره موبایل
     * @return string شماره فرمت شده
     */
    protected function format_phone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) == '0') {
            $phone = substr($phone, 1);
        }
        
        if (substr($phone, 0, 2) != '98') {
            $phone = '98' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * لاگ کردن خطا
     * 
     * @param string $message پیام خطا
     */
    protected function log_error($message) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Eshterakyar SMS Error (' . $this->get_name() . '): ' . $message);
        }
    }
}

