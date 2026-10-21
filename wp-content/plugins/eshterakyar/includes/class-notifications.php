<?php


if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Notifications {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp', array($this, 'schedule_daily_check'));
        add_action('eshterakyar_daily_expiry_check', array($this, 'check_expiring_subscriptions'));
    }
    

    public function schedule_daily_check() {
        if (!wp_next_scheduled('eshterakyar_daily_expiry_check')) {
            wp_schedule_event(time(), 'daily', 'eshterakyar_daily_expiry_check');
        }
    }
    

    public function check_expiring_subscriptions() {
        global $wpdb;
        $table = $wpdb->prefix . 'subscriptions';
        
        $options = get_option('eshterakyar_options', array());
        
        $expiry_warnings = isset($options['sms_expiry_warnings']) && is_array($options['sms_expiry_warnings']) 
            ? $options['sms_expiry_warnings'] 
            : array();
        
        foreach ($expiry_warnings as $warning) {
            if (!isset($warning['enabled']) || !$warning['enabled']) {
                continue;
            }
            
            $days = isset($warning['days']) ? intval($warning['days']) : 0;
            if ($days <= 0) {
                continue;
            }
            
            $date_threshold = date('Y-m-d H:i:s', strtotime("+{$days} days"));
            
            $table_name = $wpdb->prefix . 'subscriptions';
            $subscriptions = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM `{$table_name}` 
                 WHERE status = 'active' 
                 AND is_active = 1
                 AND end_date <= %s 
                 AND end_date > NOW()",
                $date_threshold
            ));
            
            foreach ($subscriptions as $subscription) {
                $days_left = Eshterakyar_Subscription::get_days_left($subscription->id);
                
                if ($days_left == $days) {
                    $notification_type = 'expiry_warning_' . $days;
                    if (!$this->is_notification_sent($subscription->id, $notification_type)) {
                        $this->send_expiry_warning($subscription->user_id, $subscription->id, $warning);
                    }
                }
            }
        }
        
        $table_name = $wpdb->prefix . 'subscriptions';
        $expired_subscriptions = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM `{$table_name}` 
             WHERE status = 'active' 
             AND end_date <= NOW()"
            )
        );
        
        foreach ($expired_subscriptions as $subscription) {
            Eshterakyar_Subscription::check_and_update_status($subscription->user_id);
            
            if (!$this->is_notification_sent($subscription->id, 'expired')) {
                $this->send_expired_notification($subscription->user_id, $subscription->id);
            }
        }
    }
    

    public static function send_purchase_notification($user_id, $subscription_id) {
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('شروع ارسال اطلاع‌رسانی خرید', array(
                'user_id' => $user_id,
                'subscription_id' => $subscription_id
            ));
        }
        
        $options = get_option('eshterakyar_options', array());
        
        if (!isset($options['enable_notifications']) || !$options['enable_notifications']) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('اطلاع‌رسانی‌ها غیرفعال است - توقف', array(
                    'user_id' => $user_id,
                    'subscription_id' => $subscription_id,
                    'enable_notifications' => isset($options['enable_notifications']) ? $options['enable_notifications'] : 'not_set'
                ));
            }
            return;
        }
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('اطلاع‌رسانی‌ها فعال است - ادامه', array(
                'user_id' => $user_id,
                'subscription_id' => $subscription_id
            ));
        }
        
        $subscription = Eshterakyar_Subscription::get_by_id($subscription_id);
        if (!$subscription) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('اشتراک یافت نشد - توقف', array(
                    'user_id' => $user_id,
                    'subscription_id' => $subscription_id
                ));
            }
            return;
        }
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('اشتراک دریافت شد', array(
                'subscription_id' => $subscription_id,
                'plan_id' => $subscription->plan_id,
                'plan_name' => isset($subscription->plan_name) ? $subscription->plan_name : 'N/A'
            ));
        }
        
        $user = get_userdata($user_id);
        if (!$user) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('کاربر یافت نشد - توقف', array(
                    'user_id' => $user_id,
                    'subscription_id' => $subscription_id
                ));
            }
            return;
        }
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('کاربر دریافت شد', array(
                'user_id' => $user_id,
                'user_email' => $user->user_email,
                'display_name' => $user->display_name
            ));
        }
        
        $instance = self::get_instance();
        
        $subject = isset($options['email_subject_purchase']) ? $options['email_subject_purchase'] : __('خرید اشتراک شما با موفقیت انجام شد', 'eshterakyar');
        $message = $instance->get_purchase_email_template($subscription, $user);
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('آماده‌سازی ایمیل', array(
                'user_id' => $user_id,
                'user_email' => $user->user_email,
                'subject' => $subject,
                'message_length' => strlen($message)
            ));
        }
        
        if (empty($user->user_email) || !is_email($user->user_email)) {
            $error_msg = 'Invalid email address for user ID: ' . $user_id;
            $instance->log_error($error_msg);
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('ایمیل نامعتبر - توقف ارسال ایمیل', array(
                    'user_id' => $user_id,
                    'user_email' => $user->user_email
                ));
            }
        } else {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('در حال ارسال ایمیل', array(
                    'user_id' => $user_id,
                    'user_email' => $user->user_email,
                    'subject' => $subject
                ));
            }
            
            $email_sent = wp_mail($user->user_email, $subject, $message, array('Content-Type: text/html; charset=UTF-8'));
            
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('نتیجه ارسال ایمیل', array(
                    'user_id' => $user_id,
                    'user_email' => $user->user_email,
                    'email_sent' => $email_sent ? 'yes' : 'no'
                ));
            }
            
            if (class_exists('Eshterakyar_Logs')) {
                if ($email_sent) {
                    Eshterakyar_Logs::log_email($user_id, $subscription_id, $user->user_email, $subject, $message, 'success');
                } else {
                    $error_msg = 'Failed to send email. Check WordPress mail configuration.';
                    Eshterakyar_Logs::log_email($user_id, $subscription_id, $user->user_email, $subject, $message, 'failed', $error_msg);
                    $instance->log_error('Failed to send purchase notification email to: ' . $user->user_email);
                }
            }
        }
        
        $sms_enabled = isset($options['sms_enable_purchase']) && $options['sms_enable_purchase'];
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('بررسی تنظیمات پیامک', array(
                'user_id' => $user_id,
                'sms_enable_purchase' => $sms_enabled ? 'yes' : 'no',
                'sms_message_purchase' => isset($options['sms_message_purchase']) && !empty($options['sms_message_purchase']) ? 'set' : 'not_set'
            ));
        }
        
        if ($sms_enabled) {
            $sms_message = isset($options['sms_message_purchase']) ? $options['sms_message_purchase'] : '';
            
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('پیامک فعال است', array(
                    'user_id' => $user_id,
                    'sms_message_length' => strlen($sms_message),
                    'sms_message_empty' => empty($sms_message) ? 'yes' : 'no'
                ));
            }
            
            if (!empty($sms_message)) {
                $body_id = isset($options['sms_body_id_purchase']) ? trim($options['sms_body_id_purchase']) : '';
                
                if (class_exists('Eshterakyar_Logs')) {
                    Eshterakyar_Logs::log_debug('آماده‌سازی پیامک', array(
                        'user_id' => $user_id,
                        'body_id' => $body_id,
                        'sms_message_before' => $sms_message
                    ));
                }
                
                $has_body_id_in_message = preg_match('/^@\d+@/', $sms_message);
                
                if ($has_body_id_in_message) {
                    preg_match('/^@(\d+)@/', $sms_message, $matches);
                    if (isset($matches[1])) {
                        $body_id = $matches[1];
                        
                        if (class_exists('Eshterakyar_Logs')) {
                            Eshterakyar_Logs::log_debug('bodyId از message استخراج شد', array(
                                'user_id' => $user_id,
                                'body_id' => $body_id
                            ));
                        }
                    }
                } elseif (!empty($body_id)) {
                    $sms_message = '@' . $body_id . '@' . $sms_message;
                    
                    if (class_exists('Eshterakyar_Logs')) {
                        Eshterakyar_Logs::log_debug('bodyId به پیامک اضافه شد', array(
                            'user_id' => $user_id,
                            'body_id' => $body_id,
                            'sms_message_after' => $sms_message
                        ));
                    }
                } else {
                    $sms_message = $instance->replace_sms_variables($sms_message, $subscription, $user);
                    
                    if (class_exists('Eshterakyar_Logs')) {
                        Eshterakyar_Logs::log_debug('متغیرهای پیامک جایگزین شدند (بدون bodyId - ارسال به صورت متن ساده)', array(
                            'user_id' => $user_id,
                            'sms_message_after' => $sms_message,
                            'method' => 'SendSimpleSMS'
                        ));
                    }
                }
                
                if (class_exists('Eshterakyar_Logs')) {
                    Eshterakyar_Logs::log_debug('فراخوانی ارسال پیامک', array(
                        'user_id' => $user_id,
                        'sms_message' => $sms_message,
                        'body_id' => $body_id
                    ));
                }
                
                $instance->send_sms($user_id, $sms_message, $body_id);
            } else {
                if (class_exists('Eshterakyar_Logs')) {
                    Eshterakyar_Logs::log_debug('پیامک خالی است - ارسال نمی‌شود', array(
                        'user_id' => $user_id
                    ));
                }
            }
        } else {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('پیامک غیرفعال است - ارسال نمی‌شود', array(
                    'user_id' => $user_id
                ));
            }
        }
        
        self::record_notification($user_id, $subscription_id, 'email', $message);
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('پایان ارسال اطلاع‌رسانی خرید', array(
                'user_id' => $user_id,
                'subscription_id' => $subscription_id
            ));
        }
    }
    private function send_expiry_warning($user_id, $subscription_id, $warning_config = null) {
        $options = get_option('eshterakyar_options', array());
        
        if (!isset($options['enable_notifications']) || !$options['enable_notifications']) {
            return;
        }
        
        $subscription = Eshterakyar_Subscription::get_by_id($subscription_id);
        if (!$subscription) {
            return;
        }
        
        $user = get_userdata($user_id);
        if (!$user) {
            return;
        }
        
        $days_left = Eshterakyar_Subscription::get_days_left($subscription_id);
        $instance = self::get_instance();
        
        $subject = isset($options['email_subject_expiry_warning']) ? $options['email_subject_expiry_warning'] : __('اشتراک شما به زودی منقضی می‌شود', 'eshterakyar');
        $message = $instance->get_expiry_warning_email_template($subscription, $user, $days_left);
        
        if (empty($user->user_email) || !is_email($user->user_email)) {
            $instance->log_error('Invalid email address for user ID: ' . $user_id);
            return;
        }
        
        $email_sent = wp_mail($user->user_email, $subject, $message, array('Content-Type: text/html; charset=UTF-8'));
        
        if (class_exists('Eshterakyar_Logs')) {
            if ($email_sent) {
                Eshterakyar_Logs::log_email($user_id, $subscription_id, $user->user_email, $subject, $message, 'success');
            } else {
                $error_msg = 'Failed to send email. Check WordPress mail configuration.';
                Eshterakyar_Logs::log_email($user_id, $subscription_id, $user->user_email, $subject, $message, 'failed', $error_msg);
                $instance->log_error('Failed to send expiry warning email to: ' . $user->user_email);
            }
        }
        
        if ($warning_config && isset($warning_config['message']) && !empty($warning_config['message'])) {
            $sms_message = $warning_config['message'];
            $body_id = isset($warning_config['body_id']) ? trim($warning_config['body_id']) : '';

            $has_body_id_in_message = preg_match('/^@\d+@/', $sms_message);

            if ($has_body_id_in_message) {
            } elseif (!empty($body_id)) {
                $sms_message = '@' . $body_id . '@' . $sms_message;
            } else {
                $sms_message = $instance->replace_sms_variables($sms_message, $subscription, $user);
            }

            $instance->send_sms($user_id, $sms_message, $body_id);

            $notification_type = 'expiry_warning_' . (isset($warning_config['days']) ? $warning_config['days'] : '');
            self::record_notification($user_id, $subscription_id, $notification_type, $sms_message);
        }
        
        self::record_notification($user_id, $subscription_id, 'email', $message);
    }
    
    private function send_expired_notification($user_id, $subscription_id) {
        $options = get_option('eshterakyar_options', array());
        
        if (!isset($options['enable_notifications']) || !$options['enable_notifications']) {
            return;
        }
        
        $subscription = Eshterakyar_Subscription::get_by_id($subscription_id);
        if (!$subscription) {
            return;
        }
        
        $user = get_userdata($user_id);
        if (!$user) {
            return;
        }
        
        $instance = self::get_instance();
        
        $subject = isset($options['email_subject_expired']) ? $options['email_subject_expired'] : __('اشتراک شما منقضی شد', 'eshterakyar');
        $message = $instance->get_expired_email_template($subscription, $user);
        
        if (empty($user->user_email) || !is_email($user->user_email)) {
            $instance->log_error('Invalid email address for user ID: ' . $user_id);
            return;
        }
        
        $email_sent = wp_mail($user->user_email, $subject, $message, array('Content-Type: text/html; charset=UTF-8'));
        
        if (class_exists('Eshterakyar_Logs')) {
            if ($email_sent) {
                Eshterakyar_Logs::log_email($user_id, $subscription_id, $user->user_email, $subject, $message, 'success');
            } else {
                $error_msg = 'Failed to send email. Check WordPress mail configuration.';
                Eshterakyar_Logs::log_email($user_id, $subscription_id, $user->user_email, $subject, $message, 'failed', $error_msg);
                $instance->log_error('Failed to send expired notification email to: ' . $user->user_email);
            }
        }
        
        if (isset($options['sms_enable_expired']) && $options['sms_enable_expired']) {
            $sms_message = isset($options['sms_message_expired']) ? $options['sms_message_expired'] : '';
            if (!empty($sms_message)) {
                $body_id = isset($options['sms_body_id_expired']) ? trim($options['sms_body_id_expired']) : '';

                $has_body_id_in_message = preg_match('/^@\d+@/', $sms_message);

                if ($has_body_id_in_message) {
                } elseif (!empty($body_id)) {
                    $sms_message = '@' . $body_id . '@' . $sms_message;
                } else {
                    $sms_message = $instance->replace_sms_variables($sms_message, $subscription, $user);
                }

                $instance->send_sms($user_id, $sms_message, $body_id);
            }
        }
        
        self::record_notification($user_id, $subscription_id, 'email', $message);
    }
    
    private function get_purchase_email_template($subscription, $user) {
        $options = get_option('eshterakyar_options', array());
        $is_reserved = isset($subscription->status) && $subscription->status === 'reserved';
        $days_left = $is_reserved ? 0 : Eshterakyar_Subscription::get_days_left($subscription->id);
        $is_merged = !$is_reserved && (isset($subscription->merged_old_days_left) && (int) $subscription->merged_old_days_left > 0 || isset($subscription->merged_old_daily_limit) && (int) $subscription->merged_old_daily_limit > 0);
        if ($is_reserved) {
            $purchase_message = eshterakyar_get_label('reserved_subscription_message', __('اشتراک شما با موفقیت ثبت شد و پس از اتمام اشتراک فعلی به‌صورت خودکار فعال می‌شود.', 'eshterakyar'));
        } elseif ($is_merged) {
            $purchase_message = eshterakyar_get_label('merge_success_message', __('اشتراک شما با موفقیت ارتقا یافت. روزها و محدودیت دانلود به اشتراک فعلی اضافه شد.', 'eshterakyar'));
        } else {
            $purchase_message = __('اشتراک شما با موفقیت فعال شد.', 'eshterakyar');
        }
        $start_date_display = $is_reserved ? __('پس از اتمام اشتراک فعلی', 'eshterakyar') : date_i18n(get_option('date_format'), strtotime($subscription->start_date));
        $end_date_display = $is_reserved ? __('پس از اتمام اشتراک فعلی', 'eshterakyar') : date_i18n(get_option('date_format'), strtotime($subscription->end_date));
        $days_left_display = $is_reserved ? '-' : $days_left;
        
        if (isset($options['email_template_purchase']) && !empty(trim($options['email_template_purchase']))) {
            $template = $options['email_template_purchase'];
        } else {
            $template = '<html><body style="font-family: Tahoma, Arial; direction: rtl; padding: 20px; background-color: #f5f5f5;">';
            $template .= '<div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
            $template .= '<h2 style="color: #2c3e50; margin-top: 0;">' . __('خرید اشتراک با موفقیت انجام شد', 'eshterakyar') . '</h2>';
            /* translators: %s: User's display name */
            $template .= '<p style="color: #34495e; font-size: 16px;">' . sprintf(__('سلام %s', 'eshterakyar'), esc_html($user->display_name)) . '</p>';
            $template .= '<p style="color: #34495e; font-size: 16px;">{purchase_message}</p>';
            $template .= '<div style="background-color: #ecf0f1; padding: 20px; border-radius: 5px; margin: 20px 0;">';
            $template .= '<p style="margin-top: 0; font-weight: bold; color: #2c3e50; font-size: 18px;">' . __('جزئیات اشتراک:', 'eshterakyar') . '</p>';
            $template .= '<ul style="list-style: none; padding: 0; margin: 0;">';
            $template .= '<li style="padding: 10px 0; border-bottom: 1px solid #bdc3c7;"><strong>' . __('پلن:', 'eshterakyar') . '</strong> ' . esc_html($subscription->plan_name) . '</li>';
            $template .= '<li style="padding: 10px 0; border-bottom: 1px solid #bdc3c7;"><strong>' . __('تاریخ شروع:', 'eshterakyar') . '</strong> {start_date}</li>';
            $template .= '<li style="padding: 10px 0; border-bottom: 1px solid #bdc3c7;"><strong>' . __('تاریخ پایان:', 'eshterakyar') . '</strong> {end_date}</li>';
            $template .= '<li style="padding: 10px 0; border-bottom: 1px solid #bdc3c7;"><strong>' . __('روزهای باقی‌مانده:', 'eshterakyar') . '</strong> {days_left} ' . __('روز', 'eshterakyar') . '</li>';

            if (isset($subscription->total_downloads) && $subscription->total_downloads > 0) {
                $template .= '<li style="padding: 10px 0; border-bottom: 1px solid #bdc3c7;"><strong>' . __('محدودیت دانلود کل:', 'eshterakyar') . '</strong> ' . number_format_i18n($subscription->total_downloads) . '</li>';
            } else {
                $template .= '<li style="padding: 10px 0; border-bottom: 1px solid #bdc3c7;"><strong>' . __('محدودیت دانلود کل:', 'eshterakyar') . '</strong> ' . __('نامحدود', 'eshterakyar') . '</li>';
            }
            
            if (isset($subscription->daily_limit) && $subscription->daily_limit > 0) {
                $template .= '<li style="padding: 10px 0;"><strong>' . __('محدودیت دانلود روزانه:', 'eshterakyar') . '</strong> ' . number_format_i18n($subscription->daily_limit) . '</li>';
            } else {
                $template .= '<li style="padding: 10px 0;"><strong>' . __('محدودیت دانلود روزانه:', 'eshterakyar') . '</strong> ' . __('نامحدود', 'eshterakyar') . '</li>';
            }
            
        $template .= '</ul>';
            $template .= '</div>';
            $template .= '<p style="color: #34495e; font-size: 16px; margin-top: 20px;">' . __('با تشکر', 'eshterakyar') . '</p>';
            $template .= '<p style="color: #7f8c8d; font-size: 14px; margin-top: 20px;">' . get_bloginfo('name') . '</p>';
            $template .= '</div>';
        $template .= '</body></html>';
        }
        
        $replacements = array(
            '{user_name}' => $user->display_name,
            '{plan_name}' => isset($subscription->plan_name) ? $subscription->plan_name : '',
            '{purchase_message}' => $purchase_message,
            '{start_date}' => $start_date_display,
            '{end_date}' => $end_date_display,
            '{days_left}' => $days_left_display,
            '{total_downloads}' => isset($subscription->total_downloads) && $subscription->total_downloads > 0 ? number_format_i18n($subscription->total_downloads) : __('نامحدود', 'eshterakyar'),
            '{daily_limit}' => isset($subscription->daily_limit) && $subscription->daily_limit > 0 ? number_format_i18n($subscription->daily_limit) : __('نامحدود', 'eshterakyar'),
            '{site_name}' => get_bloginfo('name')
        );
        
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
    
    private function get_expiry_warning_email_template($subscription, $user, $days_left) {
        $options = get_option('eshterakyar_options', array());
        
        if (isset($options['email_template_expiry_warning']) && !empty(trim($options['email_template_expiry_warning']))) {
            $template = $options['email_template_expiry_warning'];
        } else {
            $template = '<html><body style="font-family: Tahoma, Arial; direction: rtl; padding: 20px; background-color: #f5f5f5;">';
            $template .= '<div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
            $template .= '<h2 style="color: #e67e22; margin-top: 0;">' . __('هشدار انقضای اشتراک', 'eshterakyar') . '</h2>';
            /* translators: %s: User's display name */
            $template .= '<p style="color: #34495e; font-size: 16px;">' . sprintf(__('سلام %s', 'eshterakyar'), esc_html($user->display_name)) . '</p>';
            /* translators: %d: Number of days until subscription expires */
            $template .= '<p style="color: #34495e; font-size: 16px;">' . sprintf(__('اشتراک شما %d روز دیگر منقضی می‌شود.', 'eshterakyar'), $days_left) . '</p>';
            $template .= '<div style="background-color: #fff3cd; padding: 20px; border-radius: 5px; margin: 20px 0; border-right: 4px solid #ffc107;">';
            $template .= '<p style="margin: 0; color: #856404;"><strong>' . __('جزئیات اشتراک:', 'eshterakyar') . '</strong></p>';
            $template .= '<ul style="list-style: none; padding: 0; margin: 10px 0 0 0;">';
            $template .= '<li style="padding: 5px 0;"><strong>' . __('پلن:', 'eshterakyar') . '</strong> ' . esc_html($subscription->plan_name) . '</li>';
            $template .= '<li style="padding: 5px 0;"><strong>' . __('تاریخ انقضا:', 'eshterakyar') . '</strong> ' . date_i18n(get_option('date_format'), strtotime($subscription->end_date)) . '</li>';
            $template .= '</ul>';
            $template .= '</div>';
            $template .= '<p style="text-align: center; margin: 30px 0;">';
            $template .= '<a href="' . esc_url(wc_get_page_permalink('myaccount')) . '" style="background-color: #3498db; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">' . __('برای تمدید اینجا کلیک کنید', 'eshterakyar') . '</a>';
            $template .= '</p>';
            $template .= '<p style="color: #7f8c8d; font-size: 14px; margin-top: 20px;">' . get_bloginfo('name') . '</p>';
            $template .= '</div>';
        $template .= '</body></html>';
        }
        
        $replacements = array(
            '{user_name}' => $user->display_name,
            '{plan_name}' => isset($subscription->plan_name) ? $subscription->plan_name : '',
            '{start_date}' => date_i18n(get_option('date_format'), strtotime($subscription->start_date)),
            '{end_date}' => date_i18n(get_option('date_format'), strtotime($subscription->end_date)),
            '{days_left}' => $days_left,
            '{total_downloads}' => isset($subscription->total_downloads) && $subscription->total_downloads > 0 ? number_format_i18n($subscription->total_downloads) : __('نامحدود', 'eshterakyar'),
            '{daily_limit}' => isset($subscription->daily_limit) && $subscription->daily_limit > 0 ? number_format_i18n($subscription->daily_limit) : __('نامحدود', 'eshterakyar'),
            '{site_name}' => get_bloginfo('name')
        );
        
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
    

    private function get_expired_email_template($subscription, $user) {
        $options = get_option('eshterakyar_options', array());
        
        if (isset($options['email_template_expired']) && !empty(trim($options['email_template_expired']))) {
            $template = $options['email_template_expired'];
        } else {
            $template = '<html><body style="font-family: Tahoma, Arial; direction: rtl; padding: 20px; background-color: #f5f5f5;">';
            $template .= '<div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
            $template .= '<h2 style="color: #e74c3c; margin-top: 0;">' . __('اشتراک شما منقضی شد', 'eshterakyar') . '</h2>';
            /* translators: %s: User's display name */
            $template .= '<p style="color: #34495e; font-size: 16px;">' . sprintf(__('سلام %s', 'eshterakyar'), esc_html($user->display_name)) . '</p>';
            $template .= '<p style="color: #34495e; font-size: 16px;">' . __('متأسفانه اشتراک شما منقضی شده است.', 'eshterakyar') . '</p>';
            $template .= '<div style="background-color: #fee; padding: 20px; border-radius: 5px; margin: 20px 0; border-right: 4px solid #e74c3c;">';
            $template .= '<p style="margin: 0; color: #c0392b;"><strong>' . __('جزئیات اشتراک منقضی شده:', 'eshterakyar') . '</strong></p>';
            $template .= '<ul style="list-style: none; padding: 0; margin: 10px 0 0 0;">';
            $template .= '<li style="padding: 5px 0;"><strong>' . __('پلن:', 'eshterakyar') . '</strong> ' . esc_html($subscription->plan_name) . '</li>';
            $template .= '<li style="padding: 5px 0;"><strong>' . __('تاریخ انقضا:', 'eshterakyar') . '</strong> ' . date_i18n(get_option('date_format'), strtotime($subscription->end_date)) . '</li>';
            $template .= '</ul>';
            $template .= '</div>';
            $template .= '<p style="text-align: center; margin: 30px 0;">';
            $template .= '<a href="' . esc_url(wc_get_page_permalink('shop')) . '" style="background-color: #e74c3c; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">' . __('برای خرید اشتراک جدید اینجا کلیک کنید', 'eshterakyar') . '</a>';
            $template .= '</p>';
            $template .= '<p style="color: #7f8c8d; font-size: 14px; margin-top: 20px;">' . get_bloginfo('name') . '</p>';
            $template .= '</div>';
        $template .= '</body></html>';
        }
        
        $replacements = array(
            '{user_name}' => $user->display_name,
            '{plan_name}' => isset($subscription->plan_name) ? $subscription->plan_name : '',
            '{start_date}' => date_i18n(get_option('date_format'), strtotime($subscription->start_date)),
            '{end_date}' => date_i18n(get_option('date_format'), strtotime($subscription->end_date)),
            '{days_left}' => 0,
            '{total_downloads}' => isset($subscription->total_downloads) && $subscription->total_downloads > 0 ? number_format_i18n($subscription->total_downloads) : __('نامحدود', 'eshterakyar'),
            '{daily_limit}' => isset($subscription->daily_limit) && $subscription->daily_limit > 0 ? number_format_i18n($subscription->daily_limit) : __('نامحدود', 'eshterakyar'),
            '{site_name}' => get_bloginfo('name')
        );
        
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
    
    /**
     * دریافت instance سامانه پیامکی
     * 
     * @return Eshterakyar_SMS_Base|false
     */
    private function get_sms_provider() {
        $options = get_option('eshterakyar_options', array());
        $provider = isset($options['sms_provider']) ? $options['sms_provider'] : 'melli';
        
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-sms-base.php';
        
        switch ($provider) {
            case 'melli':
                require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-sms-melli.php';
                return new Eshterakyar_SMS_Melli();
            case 'kavenegar':
                require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-sms-kavenegar.php';
                return new Eshterakyar_SMS_Kavenegar();
            case 'smsir':
                require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-sms-smsir.php';
                return new Eshterakyar_SMS_Smsir();
            default:
                return false;
        }
    }
    
    /**
     * ارسال پیامک
     * 
     * @param int $user_id شناسه کاربر
     * @param string $message متن پیامک (می‌تواند به فرمت @bodyId@var1;var2;var3 باشد)
     * @param string $body_id_override bodyId جداگانه برای این نوع پیامک (اختیاری)
     */
    private function send_sms($user_id, $message, $body_id_override = '') {
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('شروع ارسال پیامک', array(
                'user_id' => $user_id,
                'message_length' => strlen($message),
                'body_id_override' => $body_id_override
            ));
        }
        
        $options = get_option('eshterakyar_options', array());
        
        $user = get_userdata($user_id);
        if (!$user) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('کاربر یافت نشد در send_sms - توقف', array('user_id' => $user_id));
            }
            return false;
        }
        
        $phone = get_user_meta($user_id, 'billing_phone', true);
        if (empty($phone)) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('شماره تلفن یافت نشد - توقف', array(
                    'user_id' => $user_id,
                    'user_email' => $user->user_email
                ));
            }
            return false;
        }
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('اطلاعات کاربر دریافت شد', array(
                'user_id' => $user_id,
                'phone' => $phone,
                'user_email' => $user->user_email
            ));
        }
        
        $sms_provider = $this->get_sms_provider();
        if (!$sms_provider) {
            $error_msg = 'SMS provider not found';
            $this->log_error($error_msg);
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('سامانه پیامکی یافت نشد - توقف', array(
                    'user_id' => $user_id,
                    'sms_provider' => isset($options['sms_provider']) ? $options['sms_provider'] : 'not_set'
                ));
            }
            return false;
        }
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('سامانه پیامکی دریافت شد', array(
                'user_id' => $user_id,
                'sms_provider' => isset($options['sms_provider']) ? $options['sms_provider'] : 'unknown'
            ));
        }
        
        $provider = isset($options['sms_provider']) ? $options['sms_provider'] : 'melli';
        $args = array();

        if ($provider === 'kavenegar') {
            $args['api_key'] = isset($options['sms_kavenegar_api_key']) ? $options['sms_kavenegar_api_key'] : '';
            $args['sender']  = isset($options['sms_kavenegar_sender']) ? $options['sms_kavenegar_sender'] : '';
        } elseif ($provider === 'smsir') {
            $args['api_key']      = isset($options['sms_smsir_api_key']) ? $options['sms_smsir_api_key'] : '';
            $args['line_number']  = isset($options['sms_smsir_line_number']) ? $options['sms_smsir_line_number'] : '';
            $args['template_id']  = isset($options['sms_smsir_verify_template_id']) ? $options['sms_smsir_verify_template_id'] : 0;
            $args['parameter_names'] = isset($options['sms_smsir_verify_parameter_names']) ? $options['sms_smsir_verify_parameter_names'] : '';
        } else {
            $args['username'] = isset($options['sms_username']) ? $options['sms_username'] : '';
            $args['password'] = isset($options['sms_password']) ? $options['sms_password'] : '';
            $args['sender']   = isset($options['sms_sender']) ? $options['sms_sender'] : '';
            if (isset($options['sms_method']) && !empty($options['sms_method']) && $options['sms_method'] !== 'auto') {
                $args['method'] = $options['sms_method'];
            }
        }
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('آماده‌سازی آرگومان‌های پیامک', array(
                'user_id' => $user_id,
                'has_username' => !empty($args['username']) ? 'yes' : 'no',
                'has_password' => !empty($args['password']) ? 'yes' : 'no'
            ));
        }
        
        $body_id = '';
        $template_vars = array();
        
        if (preg_match('/^@(\d+)@(.+)$/', $message, $matches)) {
            $body_id = $matches[1];
            $vars_string = $matches[2];
            $template_vars = explode(';', $vars_string);
            $template_vars = array_map(function ($v) {
                $v = trim($v);
                if (preg_match('/^\{([^}]+)\}$/', $v, $m)) {
                    return $m[1];
                }
                return $v;
            }, $template_vars);
            $template_vars = array_filter($template_vars);
            
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('bodyId از message استخراج شد', array(
                    'user_id' => $user_id,
                    'body_id' => $body_id,
                    'template_vars_count' => count($template_vars),
                    'template_vars' => $template_vars
                ));
            }
        } elseif (!empty($body_id_override)) {
            $body_id = $body_id_override;
            
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('استفاده از bodyId جداگانه', array(
                    'user_id' => $user_id,
                    'body_id' => $body_id
                ));
            }
        }
        
        if (!empty($body_id)) {
            $args['body_id'] = $body_id;
            
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('bodyId تنظیم شد', array(
                    'user_id' => $user_id,
                    'body_id' => $body_id,
                    'has_template_vars' => !empty($template_vars) ? 'yes' : 'no'
                ));
            }
            
            if (!empty($template_vars)) {
                $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
                $days_left = $subscription ? Eshterakyar_Subscription::get_days_left($subscription->id) : 0;
                
                if (class_exists('Eshterakyar_Logs')) {
                    Eshterakyar_Logs::log_debug('تبدیل متغیرهای پیامک', array(
                        'user_id' => $user_id,
                        'subscription_id' => $subscription ? $subscription->id : null,
                        'template_vars_count' => count($template_vars)
                    ));
                }
                
                $var_values = array();
                foreach ($template_vars as $var) {
                    $var = trim($var);
                    switch ($var) {
                        case 'userName':
                            $var_values[] = $user->display_name;
                            break;
                        case 'planName':
                            $var_values[] = $subscription && isset($subscription->plan_name) ? $subscription->plan_name : '';
                            break;
                        case 'startDate':
                            $var_values[] = $subscription ? date_i18n(get_option('date_format'), strtotime($subscription->start_date)) : '';
                            break;
                        case 'endDate':
                            $var_values[] = $subscription ? date_i18n(get_option('date_format'), strtotime($subscription->end_date)) : '';
                            break;
                        case 'daysLeft':
                            $var_values[] = $days_left;
                            break;
                        case 'totalDownloads':
                            $var_values[] = $subscription && $subscription->total_downloads > 0 ? number_format_i18n($subscription->total_downloads) : __('نامحدود', 'eshterakyar');
                            break;
                        case 'dailyLimit':
                            $var_values[] = $subscription && $subscription->daily_limit > 0 ? number_format_i18n($subscription->daily_limit) : __('نامحدود', 'eshterakyar');
                            break;
                        case 'siteName':
                            $var_values[] = get_bloginfo('name');
                            break;
                        default:
                            $var_values[] = $var;
                    }
                }
                $args['template_vars'] = $var_values;
                
                if (class_exists('Eshterakyar_Logs')) {
                    Eshterakyar_Logs::log_debug('متغیرهای پیامک تبدیل شدند', array(
                        'user_id' => $user_id,
                        'var_values_count' => count($var_values)
                    ));
                }
            } else {
                $args['template_vars'] = array();
            }
        } else {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('بدون bodyId - ارسال مستقیم پیامک', array(
                    'user_id' => $user_id
                ));
            }
        }

        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('در حال ارسال پیامک به سامانه', array(
                'user_id' => $user_id,
                'phone' => $phone,
                'has_body_id' => !empty($body_id) ? 'yes' : 'no',
                'body_id' => $body_id ? $body_id : 'N/A',
                'message_length' => strlen($message)
            ));
        }
        
        $result = $sms_provider->send($phone, $message, $args);
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('نتیجه ارسال پیامک از سامانه', array(
                'user_id' => $user_id,
                'phone' => $phone,
                'result_success' => isset($result['success']) && $result['success'] ? 'yes' : 'no',
                'result_message' => isset($result['message']) ? $result['message'] : 'N/A',
                'result_full' => $result
            ));
        }
        
        $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        $subscription_id = $subscription ? $subscription->id : null;
        
        if (class_exists('Eshterakyar_Logs')) {
            if (isset($result['success']) && $result['success']) {
                Eshterakyar_Logs::log_sms($user_id, $subscription_id, $phone, $message, 'success');
                if ($subscription) {
                    self::record_notification($user_id, $subscription->id, 'sms', $message);
                }
            } else {
                $error_message = isset($result['message']) ? $result['message'] : __('خطای نامشخص در ارسال پیامک', 'eshterakyar');
                Eshterakyar_Logs::log_sms($user_id, $subscription_id, $phone, $message, 'failed', $error_message);
                $this->log_error('SMS Send Failed: ' . $error_message . ' To: ' . $phone . ' Message: ' . $message);
            }
        } else {
            if (isset($result['success']) && $result['success']) {
        if ($subscription) {
            self::record_notification($user_id, $subscription->id, 'sms', $message);
        }
            } else {
                $error_message = isset($result['message']) ? $result['message'] : __('خطای نامشخص در ارسال پیامک', 'eshterakyar');
                $this->log_error('SMS Send Failed: ' . $error_message . ' To: ' . $phone . ' Message: ' . $message);
            }
        }
        
        return isset($result['success']) ? $result['success'] : false;
    }
    

    private function log_error($message) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Eshterakyar SMS Error: ' . $message);
        }
    }
    

    private function extract_template_vars($message, $subscription, $user) {
        $days_left = Eshterakyar_Subscription::get_days_left($subscription->id);
        
        $vars_order = array('{user_name}', '{plan_name}', '{start_date}', '{end_date}', '{days_left}', '{total_downloads}', '{daily_limit}', '{site_name}');
            
        $values = array(
            '{user_name}' => $user->display_name,
            '{plan_name}' => isset($subscription->plan_name) ? $subscription->plan_name : '',
            '{start_date}' => date_i18n(get_option('date_format'), strtotime($subscription->start_date)),
            '{end_date}' => date_i18n(get_option('date_format'), strtotime($subscription->end_date)),
            '{days_left}' => $days_left,
            '{total_downloads}' => $subscription->total_downloads > 0 ? number_format_i18n($subscription->total_downloads) : __('نامحدود', 'eshterakyar'),
            '{daily_limit}' => $subscription->daily_limit > 0 ? number_format_i18n($subscription->daily_limit) : __('نامحدود', 'eshterakyar'),
            '{site_name}' => get_bloginfo('name')
        );
        
        $used_vars = array();
        foreach ($vars_order as $var) {
            if (strpos($message, $var) !== false) {
                $used_vars[] = $values[$var];
        }
        }
        
        return implode(';', $used_vars);
    }
    
    private function replace_sms_variables($message, $subscription, $user) {
        $is_reserved = isset($subscription->status) && $subscription->status === 'reserved';
        $days_left = $is_reserved ? 0 : Eshterakyar_Subscription::get_days_left($subscription->id);
        $is_merged = !$is_reserved && (isset($subscription->merged_old_days_left) && (int) $subscription->merged_old_days_left > 0 || isset($subscription->merged_old_daily_limit) && (int) $subscription->merged_old_daily_limit > 0);
        if ($is_reserved) {
            $purchase_message = eshterakyar_get_label('reserved_subscription_message', __('اشتراک شما با موفقیت ثبت شد و پس از اتمام اشتراک فعلی به‌صورت خودکار فعال می‌شود.', 'eshterakyar'));
        } elseif ($is_merged) {
            $purchase_message = eshterakyar_get_label('merge_success_message', __('اشتراک شما با موفقیت ارتقا یافت. روزها و محدودیت دانلود به اشتراک فعلی اضافه شد.', 'eshterakyar'));
        } else {
            $purchase_message = __('اشتراک شما با موفقیت فعال شد.', 'eshterakyar');
        }
        $start_date_str = $is_reserved ? __('پس از اتمام اشتراک فعلی', 'eshterakyar') : date_i18n(get_option('date_format'), strtotime($subscription->start_date));
        $end_date_str = $is_reserved ? __('پس از اتمام اشتراک فعلی', 'eshterakyar') : date_i18n(get_option('date_format'), strtotime($subscription->end_date));
        $replacements = array(
            '{user_name}' => $user->display_name,
            '{plan_name}' => isset($subscription->plan_name) ? $subscription->plan_name : '',
            '{purchase_message}' => $purchase_message,
            '{start_date}' => $start_date_str,
            '{end_date}' => $end_date_str,
            '{days_left}' => $days_left,
            '{total_downloads}' => isset($subscription->total_downloads) && $subscription->total_downloads > 0 ? number_format_i18n($subscription->total_downloads) : __('نامحدود', 'eshterakyar'),
            '{daily_limit}' => isset($subscription->daily_limit) && $subscription->daily_limit > 0 ? number_format_i18n($subscription->daily_limit) : __('نامحدود', 'eshterakyar'),
            '{site_name}' => get_bloginfo('name'),
            '{userName}' => $user->display_name,
            '{planName}' => isset($subscription->plan_name) ? $subscription->plan_name : '',
            '{startDate}' => $start_date_str,
            '{endDate}' => $end_date_str,
            '{daysLeft}' => $days_left,
            '{totalDownloads}' => isset($subscription->total_downloads) && $subscription->total_downloads > 0 ? number_format_i18n($subscription->total_downloads) : __('نامحدود', 'eshterakyar'),
            '{dailyLimit}' => isset($subscription->daily_limit) && $subscription->daily_limit > 0 ? number_format_i18n($subscription->daily_limit) : __('نامحدود', 'eshterakyar'),
            '{siteName}' => get_bloginfo('name')
        );
        $message = str_replace(array_keys($replacements), array_values($replacements), $message);
        $message = str_replace('\\n', "\n", $message);
        return $message;
    }
    

    private static function record_notification($user_id, $subscription_id, $type, $message) {
        global $wpdb;
        $table = $wpdb->prefix . 'subscription_notifications';
        
        $wpdb->insert($table, array(
            'user_id' => $user_id,
            'subscription_id' => $subscription_id,
            'type' => $type,
            'message' => $message,
            'sent_date' => current_time('mysql')
        ));
    }
    

    private function is_notification_sent($subscription_id, $type) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscription_notifications';
        
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM `{$table_name}` 
             WHERE subscription_id = %d 
             AND type LIKE %s",
            intval($subscription_id),
            '%' . $wpdb->esc_like($type) . '%'
        ));
        
        return $count > 0;
    }
}

