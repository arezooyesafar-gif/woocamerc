<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$options = get_option('eshterakyar_options', array());
$expiring_soon = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}subscriptions 
     WHERE status = 'active' 
     AND end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL %d DAY)",
    isset($options['days_before_expiry']) ? intval($options['days_before_expiry']) : 7
));
$downloads_last_30_days = $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}subscription_downloads 
     WHERE download_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
);
$subscriptions_last_30_days = $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}subscriptions 
     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
);
if (!isset($chart_data) || !is_array($chart_data)) {
    $chart_data = array();
}

$active_customers = $wpdb->get_var(
    "SELECT COUNT(DISTINCT user_id) 
     FROM {$wpdb->prefix}subscriptions 
     WHERE status = 'active' AND end_date > NOW()"
);
?>

<div class="eshterakyar-dashboard-wrapper">
    <div class="eshterakyar-dashboard-main">
        <div class="dashboard-content">
            <div class="dashboard-stats-grid">
                <div class="stat-card stat-primary <?php echo intval($stats['total_subscriptions']) == 0 ? 'stat-empty' : ''; ?>">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label"><?php esc_html_e('کل اشتراک‌ها', 'eshterakyar'); ?></div>
                        <div class="stat-value"><?php echo esc_html(number_format_i18n($stats['total_subscriptions'])); ?></div>
                        <?php if (intval($stats['total_subscriptions']) > 0): ?>
                        <div class="stat-change positive">
                            <span>+<?php echo esc_html(number_format_i18n($subscriptions_last_30_days)); ?></span>
                            <?php esc_html_e('در 30 روز گذشته', 'eshterakyar'); ?>
                        </div>
                        <?php else: ?>
                            <div class="stat-change">
                                <span><?php esc_html_e('هنوز اشتراکی ایجاد نشده', 'eshterakyar'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="stat-card stat-success <?php echo intval($stats['active_subscriptions']) == 0 ? 'stat-empty' : ''; ?>">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                            <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label"><?php esc_html_e('اشتراک‌های فعال', 'eshterakyar'); ?></div>
                        <div class="stat-value"><?php echo esc_html(number_format_i18n($stats['active_subscriptions'])); ?></div>
                        <?php if (intval($stats['active_subscriptions']) > 0): ?>
                        <div class="stat-change">
                            <span><?php echo esc_html(number_format_i18n($active_customers)); ?> <?php esc_html_e('مشتری فعال', 'eshterakyar'); ?></span>
                        </div>
                        <?php else: ?>
                            <div class="stat-change">
                                <span><?php esc_html_e('اشتراک فعالی وجود ندارد', 'eshterakyar'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="stat-card stat-warning <?php echo intval($expiring_soon) == 0 ? 'stat-empty' : ''; ?>">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label"><?php esc_html_e('در حال انقضا', 'eshterakyar'); ?></div>
                        <div class="stat-value"><?php echo esc_html(number_format_i18n($expiring_soon)); ?></div>
                        <?php if (intval($expiring_soon) > 0): ?>
                        <div class="stat-change">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-subscriptions&status=active&expiring_soon=1')); ?>" class="stat-link">
                                <?php esc_html_e('مشاهده لیست', 'eshterakyar'); ?>
                            </a>
                        </div>
                        <?php else: ?>
                            <div class="stat-change">
                                <span><?php esc_html_e('اشتراکی در حال انقضا نیست', 'eshterakyar'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="stat-card stat-info <?php echo intval($stats['total_downloads']) == 0 ? 'stat-empty' : ''; ?>">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label"><?php esc_html_e('کل دانلودها', 'eshterakyar'); ?></div>
                        <div class="stat-value"><?php echo esc_html(number_format_i18n($stats['total_downloads'])); ?></div>
                        <?php if (intval($stats['total_downloads']) > 0): ?>
                        <div class="stat-change positive">
                            <span>+<?php echo esc_html(number_format_i18n($downloads_last_30_days)); ?></span>
                            <?php esc_html_e('در 30 روز گذشته', 'eshterakyar'); ?>
                        </div>
                        <?php else: ?>
                            <div class="stat-change">
                                <span><?php esc_html_e('هنوز دانلودی انجام نشده', 'eshterakyar'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="stat-card stat-purple <?php echo intval($stats['total_plans']) == 0 ? 'stat-empty' : ''; ?>">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label"><?php esc_html_e('پلن‌های فعال', 'eshterakyar'); ?></div>
                        <div class="stat-value"><?php echo esc_html(number_format_i18n($stats['total_plans'])); ?></div>
                        <?php if (intval($stats['total_plans']) > 0): ?>
                        <div class="stat-change">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-plans')); ?>" class="stat-link">
                                <?php esc_html_e('مدیریت پلن‌ها', 'eshterakyar'); ?>
                            </a>
                        </div>
                        <?php else: ?>
                        <div class="stat-change">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-plans&action=add')); ?>" class="stat-link">
                                    <?php esc_html_e('افزودن اولین پلن', 'eshterakyar'); ?>
                                </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="dashboard-charts-section">
                <div class="chart-card <?php echo intval($stats['total_subscriptions']) == 0 ? 'chart-empty' : ''; ?>">
                    <div class="chart-header">
                        <h3 class="chart-title"><?php esc_html_e('نمودار اشتراک‌های ایجاد شده', 'eshterakyar'); ?></h3>
                        <span class="chart-subtitle"><?php esc_html_e('7 روز گذشته', 'eshterakyar'); ?></span>
                    </div>
                    <div class="chart-body">
                        <?php if (intval($stats['total_subscriptions']) == 0): ?>
                            <div class="chart-empty-state">
                                <div class="chart-empty-icon">📊</div>
                                <h4><?php esc_html_e('هنوز اشتراکی ایجاد نشده', 'eshterakyar'); ?></h4>
                                <p><?php esc_html_e('پس از ایجاد اولین اشتراک، نمودار اینجا نمایش داده می‌شود', 'eshterakyar'); ?></p>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-plans&action=add')); ?>" class="button button-primary">
                                    <?php esc_html_e('افزودن پلن جدید', 'eshterakyar'); ?>
                                </a>
                            </div>
                        <?php else: ?>
                        <canvas id="subscriptionsChart"></canvas>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="chart-card <?php echo intval($stats['total_subscriptions']) == 0 ? 'chart-empty' : ''; ?>">
                    <div class="chart-header">
                        <h3 class="chart-title"><?php esc_html_e('وضعیت اشتراک‌ها', 'eshterakyar'); ?></h3>
                        <span class="chart-subtitle"><?php esc_html_e('توزیع کلی', 'eshterakyar'); ?></span>
                    </div>
                    <div class="chart-body">
                        <?php if (intval($stats['total_subscriptions']) == 0): ?>
                            <div class="chart-empty-state">
                                <div class="chart-empty-icon">📈</div>
                                <h4><?php esc_html_e('هنوز اشتراکی وجود ندارد', 'eshterakyar'); ?></h4>
                                <p><?php esc_html_e('پس از ایجاد اشتراک، نمودار وضعیت اینجا نمایش داده می‌شود', 'eshterakyar'); ?></p>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-subscriptions&action=add')); ?>" class="button button-primary">
                                    <?php esc_html_e('ایجاد اشتراک', 'eshterakyar'); ?>
                                </a>
                            </div>
                        <?php else: ?>
                        <canvas id="statusChart"></canvas>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="dashboard-actions-section">
                <div class="actions-header">
                    <h3 class="section-title"><?php esc_html_e('دسترسی سریع', 'eshterakyar'); ?></h3>
                </div>
                <div class="actions-grid">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-plans&action=add')); ?>" class="action-card action-primary">
                        <div class="action-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                        </div>
                        <div class="action-content">
                            <div class="action-title"><?php esc_html_e('افزودن پلن جدید', 'eshterakyar'); ?></div>
                            <div class="action-desc"><?php esc_html_e('ایجاد پلن اشتراک جدید', 'eshterakyar'); ?></div>
                        </div>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-customers')); ?>" class="action-card">
                        <div class="action-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                            </svg>
                        </div>
                        <div class="action-content">
                            <div class="action-title"><?php esc_html_e('مشتریان', 'eshterakyar'); ?></div>
                            <div class="action-desc"><?php esc_html_e('مشاهده لیست مشتریان', 'eshterakyar'); ?></div>
                        </div>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-subscriptions')); ?>" class="action-card">
                        <div class="action-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/>
                            </svg>
                        </div>
                        <div class="action-content">
                            <div class="action-title"><?php esc_html_e('اشتراک‌ها', 'eshterakyar'); ?></div>
                            <div class="action-desc"><?php esc_html_e('مدیریت اشتراک‌ها', 'eshterakyar'); ?></div>
                        </div>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-downloads')); ?>" class="action-card">
                        <div class="action-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                            </svg>
                        </div>
                        <div class="action-content">
                            <div class="action-title"><?php esc_html_e('دانلودها', 'eshterakyar'); ?></div>
                            <div class="action-desc"><?php esc_html_e('مشاهده تاریخچه دانلود', 'eshterakyar'); ?></div>
                        </div>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-settings')); ?>" class="action-card">
                        <div class="action-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94L14.4 2.81c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.12.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.07.62-.07.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.12-.57 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/>
                            </svg>
                        </div>
                        <div class="action-content">
                            <div class="action-title"><?php esc_html_e('تنظیمات', 'eshterakyar'); ?></div>
                            <div class="action-desc"><?php esc_html_e('تنظیمات افزونه', 'eshterakyar'); ?></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
