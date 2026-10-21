<?php

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="eshterakyar-downloads-content">
    <div class="eshterakyar-date-range-filter">
        <form method="get" action="" class="eshterakyar-date-range-form">
            <input type="hidden" name="page" value="eshterakyar-downloads">
            <?php
            if (isset($_GET['s'])) echo '<input type="hidden" name="s" value="' . esc_attr($_GET['s']) . '">';
            if (isset($_GET['user_id'])) echo '<input type="hidden" name="user_id" value="' . esc_attr($_GET['user_id']) . '">';
            if (isset($_GET['product_id'])) echo '<input type="hidden" name="product_id" value="' . esc_attr($_GET['product_id']) . '">';
            if (isset($_GET['subscription_id'])) echo '<input type="hidden" name="subscription_id" value="' . esc_attr($_GET['subscription_id']) . '">';
            ?>
            <div class="date-range-wrapper">
                <label for="date_range" class="date-range-label">
                    <span class="date-range-icon">📅</span>
                    <span><?php esc_html_e('بازه زمانی:', 'eshterakyar'); ?></span>
                </label>
                <select name="date_range" id="date_range" class="eshterakyar-date-range-select" onchange="this.form.submit()">
                <option value="all" <?php selected($date_range, 'all'); ?>><?php esc_html_e('همه زمان‌ها', 'eshterakyar'); ?></option>
                <option value="today" <?php selected($date_range, 'today'); ?>><?php esc_html_e('امروز', 'eshterakyar'); ?></option>
                <option value="week" <?php selected($date_range, 'week'); ?>><?php esc_html_e('هفته گذشته', 'eshterakyar'); ?></option>
                <option value="month" <?php selected($date_range, 'month'); ?>><?php esc_html_e('ماه گذشته', 'eshterakyar'); ?></option>
                <option value="year" <?php selected($date_range, 'year'); ?>><?php esc_html_e('سال گذشته', 'eshterakyar'); ?></option>
            </select>
            </div>
        </form>
    </div>
    <div class="eshterakyar-stats-box">
        <div class="stat-item stat-item-primary">
            <h3><?php esc_html_e('کل دانلودها', 'eshterakyar'); ?></h3>
            <p><?php echo esc_html(number_format_i18n($stats['total_downloads'])); ?></p>
        </div>
        <div class="stat-item stat-item-success">
            <h3><?php esc_html_e('محصولات منحصر به فرد', 'eshterakyar'); ?></h3>
            <p><?php echo esc_html(number_format_i18n($stats['unique_products'])); ?></p>
        </div>
        <div class="stat-item stat-item-warning">
            <h3><?php esc_html_e('کاربران فعال', 'eshterakyar'); ?></h3>
            <p><?php echo esc_html(number_format_i18n($stats['unique_users'])); ?></p>
        </div>
        <div class="stat-item stat-item-error">
            <h3><?php esc_html_e('دانلودهای امروز', 'eshterakyar'); ?></h3>
            <p><?php echo esc_html(number_format_i18n($stats['today_downloads'])); ?></p>
        </div>
    </div>
    <div class="eshterakyar-insights-section" style="margin-top: 30px;">
        <div class="eshterakyar-insights-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div class="eshterakyar-insight-box" style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #2271b1; border-bottom: 2px solid #2271b1; padding-bottom: 10px;">
                    <span class="dashicons dashicons-chart-line" style="vertical-align: middle;"></span>
                    <?php esc_html_e('پردانلودترین محصولات', 'eshterakyar'); ?>
                </h3>
                <?php if (!empty($insights['top_products'])): ?>
                    <ol style="list-style: decimal; padding-right: 20px; margin: 15px 0;">
                        <?php foreach ($insights['top_products'] as $index => $product): ?>
                            <li style="margin: 10px 0; padding: 8px; background: #f9f9f9; border-radius: 4px;">
                                <a href="<?php echo esc_url(get_edit_post_link($product->product_id)); ?>" style="text-decoration: none; font-weight: 600;">
                                    <?php echo esc_html($product->product_name ? $product->product_name : __('محصول #', 'eshterakyar') . $product->product_id); ?>
                                </a>
                                <span style="float: left; color: #666; font-size: 14px;">
                                    <?php echo esc_html(number_format_i18n($product->download_count)); ?> <?php esc_html_e('دانلود', 'eshterakyar'); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php else: ?>
                    <p style="color: #666; text-align: center; padding: 20px;"><?php esc_html_e('داده‌ای یافت نشد.', 'eshterakyar'); ?></p>
                <?php endif; ?>
            </div>
            <div class="eshterakyar-insight-box" style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #00a32a; border-bottom: 2px solid #00a32a; padding-bottom: 10px;">
                    <span class="dashicons dashicons-groups" style="vertical-align: middle;"></span>
                    <?php esc_html_e('پردانلودترین کاربران', 'eshterakyar'); ?>
                </h3>
                <?php if (!empty($insights['top_users'])): ?>
                    <ol style="list-style: decimal; padding-right: 20px; margin: 15px 0;">
                        <?php foreach ($insights['top_users'] as $index => $user): ?>
                            <li style="margin: 10px 0; padding: 8px; background: #f9f9f9; border-radius: 4px;">
                                <a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $user->user_id)); ?>" style="text-decoration: none; font-weight: 600;">
                                    <?php echo esc_html($user->user_name); ?>
                                </a>
                                <br>
                                <small style="color: #666;"><?php echo esc_html($user->user_email); ?></small>
                                <span style="float: left; color: #666; font-size: 14px;">
                                    <?php echo esc_html(number_format_i18n($user->download_count)); ?> <?php esc_html_e('دانلود', 'eshterakyar'); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php else: ?>
                    <p style="color: #666; text-align: center; padding: 20px;"><?php esc_html_e('داده‌ای یافت نشد.', 'eshterakyar'); ?></p>
                <?php endif; ?>
            </div>
            <div class="eshterakyar-insight-box" style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #d63638; border-bottom: 2px solid #d63638; padding-bottom: 10px;">
                    <span class="dashicons dashicons-star-filled" style="vertical-align: middle;"></span>
                    <?php esc_html_e('پلن‌های محبوب', 'eshterakyar'); ?>
                </h3>
                <?php if (!empty($insights['popular_plans'])): ?>
                    <ol style="list-style: decimal; padding-right: 20px; margin: 15px 0;">
                        <?php foreach ($insights['popular_plans'] as $index => $plan): ?>
                            <li style="margin: 10px 0; padding: 8px; background: #f9f9f9; border-radius: 4px;">
                                <strong><?php echo esc_html($plan->plan_name ? $plan->plan_name : __('پلن #', 'eshterakyar') . $plan->plan_id); ?></strong>
                                <span style="float: left; color: #666; font-size: 14px;">
                                    <?php echo esc_html(number_format_i18n($plan->download_count)); ?> <?php esc_html_e('دانلود', 'eshterakyar'); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php else: ?>
                    <p style="color: #666; text-align: center; padding: 20px;"><?php esc_html_e('داده‌ای یافت نشد.', 'eshterakyar'); ?></p>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
    <div class="eshterakyar-filters-wrapper">
        <div class="filters-header">
            <h3 class="filters-title">
                <span class="dashicons dashicons-filter"></span>
                <?php esc_html_e('فیلترها و جست‌وجو', 'eshterakyar'); ?>
            </h3>
        </div>
        <div class="eshterakyar-filters">
            <form method="get" action="" class="eshterakyar-advanced-filters">
                <input type="hidden" name="page" value="eshterakyar-downloads">
                
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label"><?php esc_html_e('جست‌وجو', 'eshterakyar'); ?></label>
                        <input type="text" name="s" placeholder="<?php esc_attr_e('نام کاربر، ایمیل یا محصول', 'eshterakyar'); ?>" value="<?php echo isset($_GET['s']) ? esc_attr($_GET['s']) : ''; ?>" class="filter-input">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label"><?php esc_html_e('کاربر', 'eshterakyar'); ?></label>
                        <input type="number" name="user_id" placeholder="<?php esc_attr_e('شناسه کاربر', 'eshterakyar'); ?>" value="<?php echo isset($_GET['user_id']) ? esc_attr($_GET['user_id']) : ''; ?>" class="filter-input">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label"><?php esc_html_e('محصول', 'eshterakyar'); ?></label>
                        <input type="number" name="product_id" placeholder="<?php esc_attr_e('شناسه محصول', 'eshterakyar'); ?>" value="<?php echo isset($_GET['product_id']) ? esc_attr($_GET['product_id']) : ''; ?>" class="filter-input">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label"><?php esc_html_e('اشتراک', 'eshterakyar'); ?></label>
                        <input type="number" name="subscription_id" placeholder="<?php esc_attr_e('شناسه اشتراک', 'eshterakyar'); ?>" value="<?php echo isset($_GET['subscription_id']) ? esc_attr($_GET['subscription_id']) : ''; ?>" class="filter-input">
                    </div>
                </div>
                
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label"><?php esc_html_e('از تاریخ', 'eshterakyar'); ?></label>
                        <?php 
                        $date_from_display = '';
                        if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                            if (function_exists('parsidate')) {
                                $date_from_display = parsidate('Y/m/d', $_GET['date_from'], 'per');
                            } elseif (function_exists('persian_date')) {
                                $timestamp = strtotime($_GET['date_from']);
                                $date_from_display = persian_date('Y/m/d', $timestamp);
                            } else {
                                $date_from_display = sanitize_text_field($_GET['date_from']);
                            }
                        }
                        ?>
                        <input type="text" name="date_from" id="downloads_date_from" data-jdp value="<?php echo esc_attr($date_from_display); ?>" class="filter-input filter-date-input persian-date-input" placeholder="<?php esc_attr_e('انتخاب تاریخ شمسی', 'eshterakyar'); ?>" readonly>
                        <input type="hidden" name="date_from_gregorian" id="downloads_date_from_gregorian" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : ''; ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label"><?php esc_html_e('تا تاریخ', 'eshterakyar'); ?></label>
                        <?php 
                        $date_to_display = '';
                        if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                            if (function_exists('parsidate')) {
                                $date_to_display = parsidate('Y/m/d', $_GET['date_to'], 'per');
                            } elseif (function_exists('persian_date')) {
                                $timestamp = strtotime($_GET['date_to']);
                                $date_to_display = persian_date('Y/m/d', $timestamp);
                            } else {
                                $date_to_display = sanitize_text_field($_GET['date_to']);
                            }
                        }
                        ?>
                        <input type="text" name="date_to" id="downloads_date_to" data-jdp value="<?php echo esc_attr($date_to_display); ?>" class="filter-input filter-date-input persian-date-input" placeholder="<?php esc_attr_e('انتخاب تاریخ شمسی', 'eshterakyar'); ?>" readonly>
                        <input type="hidden" name="date_to_gregorian" id="downloads_date_to_gregorian" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : ''; ?>">
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="button button-primary filter-submit-btn">
                        <span class="dashicons dashicons-search"></span>
                        <?php esc_html_e('اعمال فیلتر', 'eshterakyar'); ?>
                    </button>
                    <?php if (isset($_GET['s']) || isset($_GET['user_id']) || isset($_GET['product_id']) || isset($_GET['subscription_id']) || isset($_GET['date_from']) || isset($_GET['date_to'])): ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-downloads')); ?>" class="button filter-clear-btn">
                            <span class="dashicons dashicons-dismiss"></span>
                            <?php esc_html_e('پاک کردن فیلتر', 'eshterakyar'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    <div class="eshterakyar-reset-credits" style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-top: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; color: #2271b1; border-bottom: 2px solid #2271b1; padding-bottom: 10px;">
            <span class="dashicons dashicons-update" style="vertical-align: middle;"></span>
            <?php esc_html_e('ریست اعتبار دانلود', 'eshterakyar'); ?>
        </h3>
        
        <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <h4 style="margin-top: 0; color: #856404;"><?php esc_html_e('این ابزار چه کاری انجام می‌دهد؟', 'eshterakyar'); ?></h4>
            <p style="color: #856404; margin: 10px 0;">
                <?php esc_html_e('با استفاده از این ابزار می‌توانید اعتبار دانلود یک اشتراک را به حالت اولیه برگردانید. این کار موارد زیر را انجام می‌دهد:', 'eshterakyar'); ?>
            </p>
            <ul style="color: #856404; margin: 10px 0; padding-right: 20px;">
                <li><?php esc_html_e('تعداد دانلودهای استفاده شده (downloads_used) را به 0 برمی‌گرداند', 'eshterakyar'); ?></li>
                <li><?php esc_html_e('تعداد دانلودهای روزانه (daily_downloads_count) را به 0 برمی‌گرداند', 'eshterakyar'); ?></li>
                <li><?php esc_html_e('تاریخ آخرین دانلود (last_download_date) را پاک می‌کند', 'eshterakyar'); ?></li>
                <li><?php esc_html_e('تمام رکوردهای دانلود این اشتراک از جدول subscription_downloads را حذف می‌کند', 'eshterakyar'); ?></li>
            </ul>
            <p style="color: #856404; margin: 10px 0;">
                <strong><?php esc_html_e('نکته مهم:', 'eshterakyar'); ?></strong> <?php esc_html_e('این ابزار شمارنده‌های دانلود را reset می‌کند و تمام سوابق دانلود این اشتراک را حذف می‌کند. بعد از ریست، محدودیت‌های روزانه و کلی بازنشانی می‌شوند و کاربر می‌تواند دوباره دانلود کند. این عمل قابل بازگشت نیست. برای پیدا کردن شناسه اشتراک، به صفحه "اشتراک‌ها" بروید و شناسه اشتراک را از جدول کپی کنید.', 'eshterakyar'); ?>
            </p>
        </div>
        
        <form method="post" action="">
            <?php wp_nonce_field('eshterakyar_reset_credits', 'eshterakyar_reset_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="reset_subscription_id"><?php esc_html_e('شناسه اشتراک', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="number" name="subscription_id" id="reset_subscription_id" placeholder="<?php esc_attr_e('مثال: 1', 'eshterakyar'); ?>" required class="regular-text" min="1">
                        <p class="description">
                            <?php esc_html_e('شناسه اشتراک را وارد کنید. می‌توانید این شناسه را از صفحه "اشتراک‌ها" پیدا کنید.', 'eshterakyar'); ?>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-subscriptions')); ?>" target="_blank">
                                <?php esc_html_e('مشاهده لیست اشتراک‌ها', 'eshterakyar'); ?>
                            </a>
                        </p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="reset_credits" class="button button-secondary" value="<?php esc_attr_e('ریست اعتبار', 'eshterakyar'); ?>" onclick="return confirm('<?php echo esc_js(__('آیا مطمئن هستید که می‌خواهید اعتبار این اشتراک را ریست کنید؟ این عمل قابل بازگشت نیست.', 'eshterakyar')); ?>');">
            </p>
        </form>
    </div>
    
    <div class="tablenav top">
        <div class="tablenav-pages">
            <?php echo wp_kses_post($pagination); ?>
        </div>
    </div>
    
    <table class="wp-list-table widefat fixed striped table-view-list eshterakyar-downloads-table">
        <thead>
            <tr>
                <th class="manage-column"><?php esc_html_e('کاربر', 'eshterakyar'); ?></th>
                <th class="manage-column"><?php esc_html_e('محصول', 'eshterakyar'); ?></th>
                <th class="manage-column"><?php esc_html_e('اشتراک', 'eshterakyar'); ?></th>
                <th class="manage-column"><?php esc_html_e('تاریخ دانلود', 'eshterakyar'); ?></th>
                <th class="manage-column"><?php esc_html_e('تعداد دانلود', 'eshterakyar'); ?></th>
                <th class="manage-column"><?php esc_html_e('IP Address', 'eshterakyar'); ?></th>
                <th class="manage-column"><?php esc_html_e('عملیات', 'eshterakyar'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($downloads)): ?>
                <tr>
                    <td colspan="7"><?php esc_html_e('دانلودی یافت نشد.', 'eshterakyar'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($downloads as $download): ?>
                    <tr>
                        <td>
                            <strong>
                                <a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $download->user_id)); ?>">
                                    <?php echo esc_html($download->user_name); ?>
                                </a>
                            </strong>
                            <br>
                            <small><?php echo esc_html($download->user_email); ?></small>
                            <br>
                            <span class="user-id">ID: <?php echo esc_html($download->user_id); ?></span>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(get_edit_post_link($download->product_id)); ?>">
                                <?php echo esc_html($download->product_name); ?>
                            </a>
                            <br>
                            <small>ID: <?php echo esc_html($download->product_id); ?></small>
                        </td>
                        <td>
                            <?php if ($download->subscription_status): ?>
                                <span class="status-badge status-<?php echo esc_attr($download->subscription_status); ?>">
                                    <?php
                                    $status_labels = array(
                                        'active' => __('فعال', 'eshterakyar'),
                                        'expired' => __('منقضی', 'eshterakyar'),
                                        'cancelled' => __('لغو شده', 'eshterakyar')
                                    );
                                    echo esc_html(isset($status_labels[$download->subscription_status]) ? $status_labels[$download->subscription_status] : $download->subscription_status);
                                    ?>
                                </span>
                            <?php else: ?>
                                <span class="dashicons dashicons-minus"></span>
                            <?php endif; ?>
                            <br>
                            <small>ID: <?php echo esc_html($download->subscription_id); ?></small>
                        </td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($download->download_date))); ?></td>
                        <td><?php echo esc_html(number_format_i18n($download->download_count)); ?></td>
                        <td>
                            <code><?php echo esc_html($download->ip_address); ?></code>
                            <?php if (isset($download->user_agent) && !empty($download->user_agent)): ?>
                                <br>
                                <small title="<?php echo esc_attr($download->user_agent); ?>" style="cursor: help;">
                                    <?php echo esc_html(substr($download->user_agent, 0, 50)) . '...'; ?>
                                </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post" action="" class="inline-form">
                                <?php wp_nonce_field('eshterakyar_download_action', 'eshterakyar_download_nonce'); ?>
                                <input type="hidden" name="download_id" value="<?php echo esc_attr($download->id); ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="button button-small button-link-delete" onclick="return confirm('<?php echo esc_js(__('آیا مطمئن هستید؟', 'eshterakyar')); ?>');">
                                    <?php esc_html_e('حذف', 'eshterakyar'); ?>
                                </button>
                            </form>
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
</div>

