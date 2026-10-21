<?php


if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Logs {
    

    private static function get_log_file() {
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/eshterakyar-logs';
        
        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
            file_put_contents($log_dir . '/.htaccess', 'deny from all');
        }
        
        $log_file = $log_dir . '/eshterakyar-' . date('Y-m-d') . '.log';
        return $log_file;
    }
    

    public static function log_email($user_id, $subscription_id, $recipient, $subject, $message, $status = 'success', $error_message = null) {
        $log_file = self::get_log_file();
        
        $user = $user_id ? get_userdata($user_id) : null;
        $user_info = $user ? $user->display_name . ' (' . $user->user_email . ')' : 'Unknown User';
        
        $log_entry = array(
            'type' => 'EMAIL',
            'status' => strtoupper($status),
            'date' => current_time('Y-m-d H:i:s'),
            'user_id' => $user_id,
            'user_info' => $user_info,
            'subscription_id' => $subscription_id,
            'recipient' => $recipient,
            'subject' => $subject,
            'message_preview' => wp_trim_words(strip_tags($message), 30),
            'error' => $error_message
        );
        
        $log_line = self::format_log_entry($log_entry);
        self::write_to_file($log_file, $log_line);
    }
    

    public static function log_sms($user_id, $subscription_id, $recipient, $message, $status = 'success', $error_message = null) {
        $log_file = self::get_log_file();
        
        $user = $user_id ? get_userdata($user_id) : null;
        $user_info = $user ? $user->display_name . ' (' . $user->user_email . ')' : 'Unknown User';
        
        $log_entry = array(
            'type' => 'SMS',
            'status' => strtoupper($status),
            'date' => current_time('Y-m-d H:i:s'),
            'user_id' => $user_id,
            'user_info' => $user_info,
            'subscription_id' => $subscription_id,
            'recipient' => $recipient,
            'message_preview' => wp_trim_words($message, 50),
            'error' => $error_message
        );
        
        $log_line = self::format_log_entry($log_entry);
        self::write_to_file($log_file, $log_line);
    }
    

    private static function format_log_entry($entry) {
        $line = sprintf(
            "[%s] [%s] [%s] User: %s (ID: %d) | Subscription ID: %d | Recipient: %s",
            $entry['date'],
            $entry['type'],
            $entry['status'],
            $entry['user_info'],
            $entry['user_id'] ? $entry['user_id'] : 0,
            $entry['subscription_id'] ? $entry['subscription_id'] : 0,
            $entry['recipient']
        );
        
        if (isset($entry['subject'])) {
            $line .= " | Subject: " . $entry['subject'];
        }
        
        if (isset($entry['message_preview'])) {
            $line .= " | Message: " . $entry['message_preview'];
        }
        
        if (!empty($entry['error'])) {
            $line .= " | ERROR: " . $entry['error'];
        }
        
        $line .= "\n";
        
        return $line;
    }
    

    private static function write_to_file($file, $content) {
        @file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
    }
    

    public static function log_debug($message, $context = array()) {

        $debug_config_file = ESHTERAKYAR_PLUGIN_DIR . 'includes/debug-config.php';
        $debug_logs_enabled = false;
        
        if (file_exists($debug_config_file)) {

            if (defined('ESHTERAKYAR_DEBUG_LOGS_ENABLED')) {
                $debug_logs_enabled = ESHTERAKYAR_DEBUG_LOGS_ENABLED;
            }
        }
        
        if (!$debug_logs_enabled) {
            if (!defined('WP_DEBUG') || !WP_DEBUG) {
                return;
            }
        }
        
        $log_file = self::get_log_file();
        
        $log_entry = array(
            'type' => 'DEBUG',
            'status' => 'INFO',
            'date' => current_time('Y-m-d H:i:s'),
            'message' => $message,
            'context' => $context
        );
        
        $line = sprintf(
            "[%s] [%s] [%s] %s",
            $log_entry['date'],
            $log_entry['type'],
            $log_entry['status'],
            $log_entry['message']
        );
        
        if (!empty($context)) {
            $line .= " | Context: " . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        
        $line .= "\n";
        
        self::write_to_file($log_file, $line);
    }
    

    public static function log_download($user_id, $subscription_id, $product_id, $file_url, $status = 'success', $error_message = null, $context = array()) {
        $log_file = self::get_log_file();
        
        $user = $user_id ? get_userdata($user_id) : null;
        $user_info = $user ? $user->display_name . ' (' . $user->user_email . ')' : 'Unknown User';
        
        $product = $product_id ? wc_get_product($product_id) : null;
        $product_name = $product ? $product->get_name() : 'Unknown Product';
        
        $log_entry = array(
            'type' => 'DOWNLOAD',
            'status' => strtoupper($status),
            'date' => current_time('Y-m-d H:i:s'),
            'user_id' => $user_id,
            'user_info' => $user_info,
            'subscription_id' => $subscription_id,
            'product_id' => $product_id,
            'product_name' => $product_name,
            'file_url' => $file_url,
            'error' => $error_message
        );
        
        $line = sprintf(
            "[%s] [%s] [%s] User: %s (ID: %d) | Subscription ID: %d | Product: %s (ID: %d) | File URL: %s",
            $log_entry['date'],
            $log_entry['type'],
            $log_entry['status'],
            $log_entry['user_info'],
            $log_entry['user_id'] ? $log_entry['user_id'] : 0,
            $log_entry['subscription_id'] ? $log_entry['subscription_id'] : 0,
            $log_entry['product_name'],
            $log_entry['product_id'] ? $log_entry['product_id'] : 0,
            $log_entry['file_url']
        );
        
        if (!empty($error_message)) {
            $line .= " | ERROR: " . $error_message;
        }
        
        if (!empty($context)) {
            $line .= " | Context: " . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        
        $line .= "\n";
        
        self::write_to_file($log_file, $line);
    }
    

    public static function cleanup_old_logs($days = 30) {
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/eshterakyar-logs';
        
        if (!is_dir($log_dir)) {
            return;
        }
        
        $files = glob($log_dir . '/eshterakyar-*.log');
        $cutoff_time = time() - ($days * 24 * 60 * 60);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff_time) {
                @unlink($file);
            }
        }
    }
}
