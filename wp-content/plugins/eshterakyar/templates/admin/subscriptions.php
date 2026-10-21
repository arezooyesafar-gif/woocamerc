<?php

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="eshterakyar-subscriptions-content">
    
    <?php
    if (isset($_GET['action']) && $_GET['action'] == 'create'):
        if (isset($_POST['create_manual_subscription']) && check_admin_referer('eshterakyar_create_manual_subscription', 'eshterakyar_manual_subscription_nonce')) {
            $this->create_manual_subscription();
        }
        include ESHTERAKYAR_PLUGIN_DIR . 'templates/admin/create-subscription.php';
        return;
    endif;
    
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['subscription_id'])):
        $subscription_id = intval($_GET['subscription_id']);
        $subscription = Eshterakyar_Subscription::get_by_id($subscription_id);
        
        if (!$subscription) {
            echo '<div class="notice notice-error"><p>' . esc_html(__('اشتراک یافت نشد.', 'eshterakyar')) . '</p></div>';
        } else {
            if (isset($_POST['update_subscription']) && check_admin_referer('update_subscription_' . $subscription_id)) {
                $this->update_subscription($subscription_id);
            }
            include ESHTERAKYAR_PLUGIN_DIR . 'templates/admin/edit-subscription.php';
            return;
        }
    endif;
    ?>
    
    <div class="eshterakyar-filters-wrapper">
        <div class="filters-header">
            <h3 class="filters-title">
                <span class="dashicons dashicons-filter"></span>
                <?php esc_html_e('فیلترها و جست‌وجو', 'eshterakyar'); ?>
            </h3>
        </div>
        <div class="eshterakyar-filters">
            <form method="get" action="" class="eshterakyar-advanced-filters">
                <input type="hidden" name="page" value="eshterakyar-subscriptions">
                
                <div class="filter-main-grid">
                    <div class="filter-left-column">
                        <div class="filter-group">
                            <label class="filter-label"><?php esc_html_e('از تاریخ', 'eshterakyar'); ?></label>
                            <?php 
                            if (!function_exists('eshterakyar_convert_persian_digits')) {
                                function eshterakyar_convert_persian_digits($str) {
                                    if (empty($str)) return $str;
                                    $persian_digits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
                                    $english_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
                                    return str_replace($persian_digits, $english_digits, $str);
                                }
                            }
                            
                            $date_from_display = '';
                            if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                                if (preg_match('/^\d{4}\/\d{2}\/\d{2}/', $_GET['date_from'])) {
                                    $date_from_display = sanitize_text_field($_GET['date_from']);
                                } else {
                                if (function_exists('parsidate')) {
                                    $date_from_display = parsidate('Y/m/d', $_GET['date_from'], 'per');
                                } elseif (function_exists('persian_date')) {
                                    $timestamp = strtotime($_GET['date_from']);
                                    $date_from_display = persian_date('Y/m/d', $timestamp);
                                } else {
                                    $date_from_display = sanitize_text_field($_GET['date_from']);
                                }
                            }
                            $date_from_display = eshterakyar_convert_persian_digits($date_from_display);
                            }
                            $date_from_gregorian = isset($_GET['date_from_gregorian']) ? esc_attr($_GET['date_from_gregorian']) : '';
                            ?>
                            <input type="text" name="date_from" id="date_from" data-jdp value="<?php echo esc_attr($date_from_display); ?>" class="filter-input filter-date-input persian-date-input" placeholder="<?php esc_attr_e('انتخاب تاریخ شمسی', 'eshterakyar'); ?>" readonly>
                            <input type="hidden" name="date_from_gregorian" id="date_from_gregorian" value="<?php echo esc_attr($date_from_gregorian); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label"><?php esc_html_e('تا تاریخ', 'eshterakyar'); ?></label>
                            <?php 
                            $date_to_display = '';
                            if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                                if (preg_match('/^\d{4}\/\d{2}\/\d{2}/', $_GET['date_to'])) {
                                    $date_to_display = sanitize_text_field($_GET['date_to']);
                                } else {
                                if (function_exists('parsidate')) {
                                    $date_to_display = parsidate('Y/m/d', $_GET['date_to'], 'per');
                                } elseif (function_exists('persian_date')) {
                                    $timestamp = strtotime($_GET['date_to']);
                                    $date_to_display = persian_date('Y/m/d', $timestamp);
                                } else {
                                    $date_to_display = sanitize_text_field($_GET['date_to']);
                                }
                            }
                            $date_to_display = eshterakyar_convert_persian_digits($date_to_display);
                            }
                            $date_to_gregorian = isset($_GET['date_to_gregorian']) ? esc_attr($_GET['date_to_gregorian']) : '';
                            ?>
                            <input type="text" name="date_to" id="date_to" data-jdp value="<?php echo esc_attr($date_to_display); ?>" class="filter-input filter-date-input persian-date-input" placeholder="<?php esc_attr_e('انتخاب تاریخ شمسی', 'eshterakyar'); ?>" readonly>
                            <input type="hidden" name="date_to_gregorian" id="date_to_gregorian" value="<?php echo esc_attr($date_to_gregorian); ?>">
                        </div>
                        
                        <div class="filter-group filter-checkbox-group">
                            <label class="filter-checkbox-label">
                                <input type="checkbox" name="expiring_soon" value="1" <?php checked(isset($_GET['expiring_soon'])); ?> class="filter-checkbox">
                                <span class="filter-checkbox-text"><?php esc_html_e('فقط اشتراک‌های در حال انقضا', 'eshterakyar'); ?></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="filter-right-column">
                        <div class="filter-group filter-search-group">
                            <label class="filter-label"><?php esc_html_e('جست‌وجو', 'eshterakyar'); ?></label>
                            <input type="text" name="s" placeholder="<?php esc_attr_e('نام کاربر، ایمیل یا پلن', 'eshterakyar'); ?>" value="<?php echo isset($_GET['s']) ? esc_attr($_GET['s']) : ''; ?>" class="filter-input filter-search-input">
                        </div>
                        
                        <div class="filter-dropdowns-row">
                            <div class="filter-group">
                                <label class="filter-label"><?php esc_html_e('وضعیت', 'eshterakyar'); ?></label>
                                <select name="status" class="filter-input filter-select">
                                    <option value=""><?php esc_html_e('همه وضعیت‌ها', 'eshterakyar'); ?></option>
                                    <option value="active" <?php selected($status, 'active'); ?>><?php esc_html_e('فعال', 'eshterakyar'); ?></option>
                                    <option value="reserved" <?php selected($status, 'reserved'); ?>><?php echo esc_html(eshterakyar_get_label('reserved_subscription_status_label', __('رزرو شده', 'eshterakyar'))); ?></option>
                                    <option value="expired" <?php selected($status, 'expired'); ?>><?php esc_html_e('منقضی شده', 'eshterakyar'); ?></option>
                                    <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php esc_html_e('لغو شده', 'eshterakyar'); ?></option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label class="filter-label"><?php esc_html_e('پلن', 'eshterakyar'); ?></label>
                                <select name="plan_id" class="filter-input filter-select">
                                    <option value=""><?php esc_html_e('همه پلن‌ها', 'eshterakyar'); ?></option>
                                    <?php foreach ($plans as $plan): ?>
                                        <option value="<?php echo esc_attr($plan->id); ?>" <?php selected(isset($_GET['plan_id']) && $_GET['plan_id'] == $plan->id); ?>>
                                            <?php echo esc_html($plan->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="button button-primary button-large filter-submit-btn">
                        <span class="dashicons dashicons-search"></span>
                        <?php esc_html_e('اعمال فیلتر', 'eshterakyar'); ?>
                    </button>
                    <?php if (isset($_GET['s']) || isset($_GET['status']) || isset($_GET['plan_id']) || isset($_GET['date_from']) || isset($_GET['date_to']) || isset($_GET['expiring_soon'])): ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-subscriptions')); ?>" class="button button-large filter-clear-btn">
                            <span class="dashicons dashicons-dismiss"></span>
                            <?php esc_html_e('پاک کردن فیلتر', 'eshterakyar'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    
    <form method="post" action="" id="eshterakyar-subscriptions-form">
        <?php wp_nonce_field('eshterakyar_bulk_action', 'eshterakyar_bulk_nonce'); ?>
        
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-subscriptions&action=create')); ?>" class="button button-primary">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php esc_html_e('ایجاد اشتراک دستی', 'eshterakyar'); ?>
                </a>
                <select name="bulk_action" id="bulk-action-selector">
                    <option value=""><?php esc_html_e('عملیات دسته‌ای', 'eshterakyar'); ?></option>
                    <option value="cancel"><?php esc_html_e('لغو اشتراک‌ها', 'eshterakyar'); ?></option>
                    <option value="extend"><?php esc_html_e('تمدید اشتراک‌ها', 'eshterakyar'); ?></option>
                </select>
                <input type="submit" class="button action" value="<?php esc_attr_e('اعمال', 'eshterakyar'); ?>">
            </div>
            <div class="tablenav-pages">
                <?php echo wp_kses_post($pagination); ?>
            </div>
        </div>
        
        <table class="wp-list-table widefat fixed striped table-view-list eshterakyar-subscriptions-table">
            <thead>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <input type="checkbox" id="cb-select-all">
                    </td>
                    <th class="manage-column"><?php esc_html_e('کاربر', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('پلن', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('تاریخ شروع', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('تاریخ پایان', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('روزهای باقی‌مانده', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('وضعیت', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('فعال پس از', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('فعال/غیرفعال', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('دانلودها', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('محدودیت روزانه', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('عملیات', 'eshterakyar'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subscriptions)): ?>
                    <tr>
                        <td colspan="13" class="empty-table-cell">
                            <p class="empty-table-message"><?php esc_html_e('اشتراکی یافت نشد.', 'eshterakyar'); ?></p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subscriptions as $subscription): ?>
                        <?php
                        $days_left = ($subscription->status === 'reserved') ? 0 : Eshterakyar_Subscription::get_days_left($subscription->id);
                        $is_expiring_soon = ($subscription->status == 'active' && $days_left > 0 && $days_left <= 7);
                        $is_expired = ($subscription->status == 'expired');
                        $is_reserved = ($subscription->status === 'reserved');
                        ?>
                        <tr class="<?php echo $is_expiring_soon ? 'expiring-soon' : ($is_expired ? 'expired-subscription' : ($is_reserved ? 'reserved-subscription' : '')); ?>">
                            <th scope="row" class="check-column">
                                <input type="checkbox" name="subscription_ids[]" value="<?php echo esc_attr($subscription->id); ?>">
                            </th>
                            <td>
                                <div class="user-info-display">
                                    <strong>
                                        <a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $subscription->user_id)); ?>">
                                            <?php echo esc_html($subscription->user_name ? $subscription->user_name : esc_html(__('کاربر حذف شده', 'eshterakyar'))); ?>
                                        </a>
                                    </strong>
                                    <small><?php echo esc_html($subscription->user_email); ?></small>
                                    <span class="user-id">ID: <?php echo esc_html($subscription->user_id); ?></span>
                                    <?php if (isset($subscription->order_id) && $subscription->order_id): ?>
                                        <a href="<?php echo esc_url(admin_url('post.php?post=' . $subscription->order_id . '&action=edit')); ?>" class="order-link">
                                            <?php esc_html_e('سفارش:', 'eshterakyar'); ?> #<?php echo esc_html($subscription->order_id); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="plan-info-display">
                                    <strong><?php echo esc_html($subscription->plan_name ? $subscription->plan_name : esc_html(__('پلن حذف شده', 'eshterakyar'))); ?></strong>
                                    <?php if (isset($subscription->plan_duration)): ?>
                                        <small>
                                            <?php
                                            /* translators: %d: Plan duration in days */
                                            printf(esc_html__('مدت: %d روز', 'eshterakyar'), esc_html($subscription->plan_duration));
                                            ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php if ($is_reserved && (empty($subscription->start_date) || $subscription->start_date === '0000-00-00 00:00:00')): ?><span class="reserved-date">-</span><?php else: ?><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($subscription->start_date))); ?><?php endif; ?></td>
                            <td>
                                <?php if ($is_reserved && (empty($subscription->end_date) || $subscription->end_date === '0000-00-00 00:00:00')): ?>
                                    <span class="reserved-date">-</span>
                                <?php else: ?>
                                    <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($subscription->end_date))); ?>
                                    <?php if ($is_expiring_soon): ?>
                                        <span class="expiring-badge"><?php esc_html_e('در حال انقضا', 'eshterakyar'); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($subscription->status == 'active'): ?>
                                    <span class="days-left <?php echo esc_attr($days_left <= 7 ? 'warning' : ''); ?>">
                                        <?php echo esc_html(number_format_i18n($days_left)); ?> <?php esc_html_e('روز', 'eshterakyar'); ?>
                                    </span>
                                <?php elseif ($is_reserved): ?>
                                    <span class="reserved-days">-</span>
                                <?php else: ?>
                                    <span class="expired">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo esc_attr($subscription->status); ?>">
                                    <?php
                                    $status_labels = array(
                                        'active' => __('فعال', 'eshterakyar'),
                                        'reserved' => eshterakyar_get_label('reserved_subscription_status_label', __('رزرو شده', 'eshterakyar')),
                                        'expired' => __('منقضی شده', 'eshterakyar'),
                                        'cancelled' => __('لغو شده', 'eshterakyar')
                                    );
                                    echo esc_html(isset($status_labels[$subscription->status]) ? $status_labels[$subscription->status] : $subscription->status);
                                    ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($is_reserved && !empty($subscription->activates_after_subscription_id)): ?>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-subscriptions&action=edit&subscription_id=' . intval($subscription->activates_after_subscription_id))); ?>">#<?php echo esc_html($subscription->activates_after_subscription_id); ?></a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($subscription->status == 'active'): ?>
                                    <?php if (isset($subscription->is_active) && $subscription->is_active): ?>
                                        <span class="status-badge status-active"><?php esc_html_e('فعال', 'eshterakyar'); ?></span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactive"><?php esc_html_e('غیرفعال', 'eshterakyar'); ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="status-badge status-<?php echo esc_attr($subscription->status); ?>">
                                        <?php
                                        $status_labels_secondary = array(
                                            'reserved' => eshterakyar_get_label('reserved_subscription_status_label', __('رزرو شده', 'eshterakyar')),
                                            'expired' => __('منقضی شده', 'eshterakyar'),
                                            'cancelled' => __('لغو شده', 'eshterakyar')
                                        );
                                        echo esc_html(isset($status_labels_secondary[$subscription->status]) ? $status_labels_secondary[$subscription->status] : $subscription->status);
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="download-stats-display">
                                    <span class="download-count">
                                        <?php echo esc_html(number_format_i18n($subscription->downloads_used)); ?> / 
                                        <?php echo isset($subscription->total_downloads) && $subscription->total_downloads > 0 ? esc_html(number_format_i18n($subscription->total_downloads)) : esc_html(__('∞', 'eshterakyar')); ?>
                                    </span>
                                    <?php if (isset($subscription->total_downloads) && $subscription->total_downloads > 0): ?>
                                        <?php 
                                        $percentage = ($subscription->downloads_used / $subscription->total_downloads) * 100;
                                        $progress_class = $percentage >= 90 ? 'error' : ($percentage >= 70 ? 'warning' : 'success');
                                        ?>
                                        <div class="progress-bar-container">
                                            <div class="progress-bar <?php echo esc_attr($progress_class); ?>" style="width: <?php echo esc_attr($percentage); ?>%;"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if (isset($subscription->daily_limit) && $subscription->daily_limit > 0): ?>
                                    <div class="download-stats-display">
                                        <span class="download-count">
                                            <?php echo esc_html(number_format_i18n(isset($subscription->daily_downloads_count) ? $subscription->daily_downloads_count : 0)); ?> / 
                                            <?php echo esc_html(number_format_i18n($subscription->daily_limit)); ?>
                                        </span>
                                        <?php 
                                        $today = current_time('Y-m-d');
                                        $is_today = isset($subscription->last_download_date) && $subscription->last_download_date == $today;
                                        if ($is_today):
                                            $daily_percentage = (($subscription->daily_downloads_count ?? 0) / $subscription->daily_limit) * 100;
                                            $daily_progress_class = $daily_percentage >= 90 ? 'error' : ($daily_percentage >= 70 ? 'warning' : 'success');
                                        ?>
                                            <div class="progress-bar-container">
                                                <div class="progress-bar <?php echo esc_attr($daily_progress_class); ?>" style="width: <?php echo esc_attr($daily_percentage); ?>%;"></div>
                                            </div>
                                        <?php else: ?>
                                            <span class="no-download-today"><?php esc_html_e('امروز دانلود نشده', 'eshterakyar'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="no-limit-text"><?php esc_html_e('بدون محدودیت', 'eshterakyar'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <?php if ($subscription->status == 'active'): ?>
                                        <?php if ($subscription->is_active): ?>
                                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=eshterakyar-subscriptions&action=deactivate&subscription_id=' . $subscription->id), 'toggle_subscription_' . $subscription->id)); ?>" class="button button-small">
                                                <?php esc_html_e('غیرفعال', 'eshterakyar'); ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=eshterakyar-subscriptions&action=activate&subscription_id=' . $subscription->id), 'toggle_subscription_' . $subscription->id)); ?>" class="button button-small">
                                                <?php esc_html_e('فعال', 'eshterakyar'); ?>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=eshterakyar-subscriptions&action=extend&subscription_id=' . $subscription->id), 'extend_subscription_' . $subscription->id)); ?>" class="button button-small">
                                            <?php esc_html_e('تمدید', 'eshterakyar'); ?>
                                        </a>
                                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=eshterakyar-subscriptions&action=cancel&subscription_id=' . $subscription->id), 'cancel_subscription_' . $subscription->id)); ?>" class="button button-small" onclick="return confirm('<?php echo esc_js(__('آیا مطمئن هستید؟', 'eshterakyar')); ?>');">
                                            <?php esc_html_e('لغو', 'eshterakyar'); ?>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-subscriptions&action=edit&subscription_id=' . $subscription->id)); ?>" class="button button-small">
                                        <?php esc_html_e('ویرایش', 'eshterakyar'); ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php echo wp_kses_post($pagination); ?>
            </div>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('#cb-select-all').on('click', function() {
        $('input[name="subscription_ids[]"]').prop('checked', this.checked);
    });
});
</script>
