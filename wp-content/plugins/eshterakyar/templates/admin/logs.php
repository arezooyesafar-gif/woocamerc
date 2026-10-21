<?php

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap eshterakyar-admin">
    <div class="eshterakyar-logs-container">
        <div class="logs-header">
            <h2><?php esc_html_e('لاگ‌های افزونه اشتراک یار', 'eshterakyar'); ?></h2>
            
            <div class="logs-status-info">
                <div class="status-item">
                    <span class="status-label"><?php esc_html_e('وضعیت کلاس لاگ:', 'eshterakyar'); ?></span>
                    <span class="status-value <?php echo $logs_class_exists ? 'status-success' : 'status-error'; ?>">
                        <?php echo $logs_class_exists ? __('فعال', 'eshterakyar') : __('غیرفعال', 'eshterakyar'); ?>
                    </span>
                </div>
                <div class="status-item">
                    <span class="status-label"><?php esc_html_e('وضعیت WP_DEBUG:', 'eshterakyar'); ?></span>
                    <span class="status-value <?php echo $wp_debug_enabled ? 'status-success' : 'status-warning'; ?>">
                        <?php echo $wp_debug_enabled ? __('فعال', 'eshterakyar') : __('غیرفعال', 'eshterakyar'); ?>
                    </span>
                </div>
                <div class="status-item">
                    <span class="status-label"><?php esc_html_e('لاگ‌های Debug (مخفی):', 'eshterakyar'); ?></span>
                    <span class="status-value <?php echo $debug_logs_enabled ? 'status-success' : 'status-warning'; ?>">
                        <?php echo $debug_logs_enabled ? __('فعال', 'eshterakyar') : __('غیرفعال', 'eshterakyar'); ?>
                    </span>
                </div>
            </div>
            
            <div class="debug-config-info" style="margin-top: 15px; padding: 15px; background: #e7f3ff; border-right: 4px solid #2196F3; border-radius: 4px;">
                <p style="margin: 0 0 10px 0;">
                    <strong><?php esc_html_e('💡 راهنمای فعال‌سازی لاگ‌های Debug:', 'eshterakyar'); ?></strong>
                </p>
                <p style="margin: 0; font-size: 13px; color: #555;">
                    <?php esc_html_e('برای فعال کردن لاگ‌های Debug، فایل', 'eshterakyar'); ?>
                    <code style="background: #fff; padding: 2px 6px; border-radius: 3px;">includes/debug-config.php</code>
                    <?php esc_html_e('را باز کنید و مقدار', 'eshterakyar'); ?>
                    <code style="background: #fff; padding: 2px 6px; border-radius: 3px;">ESHTERAKYAR_DEBUG_LOGS_ENABLED</code>
                    <?php esc_html_e('را به', 'eshterakyar'); ?>
                    <code style="background: #fff; padding: 2px 6px; border-radius: 3px;">true</code>
                    <?php esc_html_e('تغییر دهید.', 'eshterakyar'); ?>
                </p>
            </div>
            
            <?php if (!$wp_debug_enabled): ?>
                <div class="notice notice-warning inline">
                    <p>
                        <strong><?php esc_html_e('توجه:', 'eshterakyar'); ?></strong>
                        <?php esc_html_e('برای فعال‌سازی لاگ‌های Debug، باید WP_DEBUG را در فایل wp-config.php فعال کنید:', 'eshterakyar'); ?>
                        <code>define('WP_DEBUG', true);</code>
                    </p>
                </div>
            <?php endif; ?>
            
            <?php if (!$logs_class_exists): ?>
                <div class="notice notice-error inline">
                    <p>
                        <strong><?php esc_html_e('خطا:', 'eshterakyar'); ?></strong>
                        <?php esc_html_e('کلاس Eshterakyar_Logs یافت نشد. لطفاً افزونه را دوباره نصب کنید.', 'eshterakyar'); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="logs-controls">
            <form method="get" action="" class="logs-date-selector">
                <input type="hidden" name="page" value="eshterakyar-logs">
                <label for="log_date"><?php esc_html_e('انتخاب تاریخ لاگ:', 'eshterakyar'); ?></label>
                <select name="log_date" id="log_date" onchange="this.form.submit()">
                    <?php if (empty($log_files)): ?>
                        <option value=""><?php esc_html_e('هیچ فایل لاگی یافت نشد', 'eshterakyar'); ?></option>
                    <?php else: ?>
                        <?php foreach ($log_files as $date): ?>
                            <option value="<?php echo esc_attr($date); ?>" <?php selected($selected_date, $date); ?>>
                                <?php echo esc_html(date_i18n('l، d F Y', strtotime($date))); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </form>
            
            <?php if ($file_exists): ?>
                <div class="log-file-info">
                    <span class="info-label"><?php esc_html_e('مسیر فایل:', 'eshterakyar'); ?></span>
                    <code><?php echo esc_html($log_file); ?></code>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="logs-content-wrapper">
            <?php if ($file_exists): ?>
                <div class="logs-content">
                    <div class="logs-toolbar">
                        <button type="button" class="button" onclick="copyLogs()">
                            <?php esc_html_e('کپی لاگ‌ها', 'eshterakyar'); ?>
                        </button>
                        <button type="button" class="button" onclick="downloadLogs()">
                            <?php esc_html_e('دانلود لاگ‌ها', 'eshterakyar'); ?>
                        </button>
                        <button type="button" class="button" onclick="clearLogs()">
                            <?php esc_html_e('پاک کردن لاگ‌ها', 'eshterakyar'); ?>
                        </button>
                    </div>
                    <div class="logs-viewer" id="logs-viewer">
                        <pre><?php echo esc_html($log_content); ?></pre>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-logs-message">
                    <p><?php esc_html_e('هیچ فایل لاگی برای تاریخ انتخاب شده یافت نشد.', 'eshterakyar'); ?></p>
                    <p><?php esc_html_e('لاگ‌ها به صورت خودکار در مسیر زیر ذخیره می‌شوند:', 'eshterakyar'); ?></p>
                    <code><?php echo esc_html($log_dir); ?></code>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="logs-help-section">
            <h3><?php esc_html_e('راهنمای استفاده از لاگ‌ها', 'eshterakyar'); ?></h3>
            <ul>
                <li><?php esc_html_e('لاگ‌ها به صورت روزانه در فایل‌های جداگانه ذخیره می‌شوند.', 'eshterakyar'); ?></li>
                <li><?php esc_html_e('برای فعال‌سازی لاگ‌های Debug، WP_DEBUG را در wp-config.php فعال کنید.', 'eshterakyar'); ?></li>
                <li><?php esc_html_e('لاگ‌های قدیمی‌تر از 30 روز به صورت خودکار پاک می‌شوند.', 'eshterakyar'); ?></li>
                <li><?php esc_html_e('لاگ‌ها شامل اطلاعات مربوط به خرید اشتراک، دانلودها، ایمیل‌ها و SMS ها می‌شوند.', 'eshterakyar'); ?></li>
            </ul>
        </div>
    </div>
</div>

<style>
.eshterakyar-logs-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.logs-header {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #ddd;
}

.logs-header h2 {
    margin: 0 0 15px 0;
    font-size: 24px;
    color: #333;
}

.logs-status-info {
    display: flex;
    gap: 30px;
    margin-top: 15px;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-label {
    font-weight: 600;
    color: #666;
}

.status-value {
    padding: 4px 12px;
    border-radius: 4px;
    font-weight: 600;
}

.status-success {
    background: #d4edda;
    color: #155724;
}

.status-warning {
    background: #fff3cd;
    color: #856404;
}

.status-error {
    background: #f8d7da;
    color: #721c24;
}

.logs-controls {
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.logs-date-selector {
    display: flex;
    align-items: center;
    gap: 10px;
}

.logs-date-selector select {
    padding: 6px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.log-file-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.log-file-info code {
    background: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    color: #666;
}

.logs-content-wrapper {
    margin-top: 20px;
}

.logs-toolbar {
    margin-bottom: 10px;
    display: flex;
    gap: 10px;
}

.logs-viewer {
    background: #1e1e1e;
    border-radius: 4px;
    padding: 15px;
    max-height: 600px;
    overflow-y: auto;
}

.logs-viewer pre {
    margin: 0;
    padding: 0;
    color: #d4d4d4;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    line-height: 1.6;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.no-logs-message {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

.no-logs-message code {
    display: block;
    margin-top: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
    font-size: 12px;
}

.logs-help-section {
    margin-top: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 4px;
}

.logs-help-section h3 {
    margin: 0 0 15px 0;
    font-size: 18px;
    color: #333;
}

.logs-help-section ul {
    margin: 0;
    padding-right: 20px;
    color: #666;
}

.logs-help-section li {
    margin-bottom: 8px;
}

.debug-config-info {
    margin-top: 15px;
}
</style>

<script>
function copyLogs() {
    const logsContent = document.querySelector('#logs-viewer pre').textContent;
    navigator.clipboard.writeText(logsContent).then(function() {
        alert('<?php echo esc_js(__('لاگ‌ها با موفقیت کپی شدند.', 'eshterakyar')); ?>');
    }, function() {
        alert('<?php echo esc_js(__('خطا در کپی کردن لاگ‌ها.', 'eshterakyar')); ?>');
    });
}

function downloadLogs() {
    const logsContent = document.querySelector('#logs-viewer pre').textContent;
    const blob = new Blob([logsContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'eshterakyar-<?php echo esc_js($selected_date); ?>.log';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function clearLogs() {
    if (confirm('<?php echo esc_js(__('آیا مطمئن هستید که می‌خواهید این فایل لاگ را پاک کنید؟', 'eshterakyar')); ?>')) {
        window.location.href = '<?php echo esc_url(admin_url('admin.php?page=eshterakyar-logs&action=clear&log_date=' . $selected_date . '&_wpnonce=' . wp_create_nonce('clear_log_' . $selected_date))); ?>';
    }
}
</script>

<?php
if (isset($_GET['action']) && $_GET['action'] === 'clear' && isset($_GET['log_date'])) {
    $log_date = sanitize_text_field($_GET['log_date']);
    $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';
    
    if (wp_verify_nonce($nonce, 'clear_log_' . $log_date) && current_user_can('manage_options')) {
        $log_file = $log_dir . '/eshterakyar-' . $log_date . '.log';
        if (file_exists($log_file) && is_writable($log_file)) {
            @unlink($log_file);
            echo '<div class="notice notice-success"><p>' . esc_html__('فایل لاگ با موفقیت پاک شد.', 'eshterakyar') . '</p></div>';
            echo '<script>setTimeout(function(){ window.location.href = "' . esc_url(admin_url('admin.php?page=eshterakyar-logs')) . '"; }, 1000);</script>';
        }
    }
}
?>
