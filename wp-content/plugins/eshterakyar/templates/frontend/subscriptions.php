<?php

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="eshterakyar-user-subscriptions">
    <h2><?php _e('اشتراک‌های من', 'eshterakyar'); ?></h2>
    
    <?php if (empty($subscriptions)): ?>
        <p><?php _e('شما هیچ اشتراکی ندارید.', 'eshterakyar'); ?></p>
    <?php else: ?>
        <div class="eshterakyar-subscriptions-list">
            <?php foreach ($subscriptions as $subscription): ?>
                <?php $is_reserved = isset($subscription->status) && $subscription->status === 'reserved'; ?>
                <div class="eshterakyar-subscription-item status-<?php echo esc_attr($subscription->status); ?>">
                    <h3><?php echo esc_html($subscription->plan_name); ?></h3>
                    <div class="subscription-details">
                        <?php if (!$is_reserved): ?>
                        <p><strong><?php _e('تاریخ شروع:', 'eshterakyar'); ?></strong> <?php echo date_i18n(get_option('date_format'), strtotime($subscription->start_date)); ?></p>
                        <p><strong><?php _e('تاریخ پایان:', 'eshterakyar'); ?></strong> <?php echo date_i18n(get_option('date_format'), strtotime($subscription->end_date)); ?></p>
                        <?php endif; ?>
                        <p><strong><?php _e('وضعیت:', 'eshterakyar'); ?></strong> 
                            <span class="status-badge status-<?php echo esc_attr($subscription->status); ?>">
                                <?php
                                $status_labels = array(
                                    'active' => __('فعال', 'eshterakyar'),
                                    'reserved' => eshterakyar_get_label('reserved_subscription_status_label', __('رزرو شده', 'eshterakyar')),
                                    'expired' => __('منقضی شده', 'eshterakyar'),
                                    'cancelled' => __('لغو شده', 'eshterakyar')
                                );
                                echo isset($status_labels[$subscription->status]) ? esc_html($status_labels[$subscription->status]) : esc_html($subscription->status);
                                ?>
                            </span>
                        </p>
                        <?php if ($is_reserved): ?>
                            <p class="eshterakyar-reserved-message"><?php echo esc_html(eshterakyar_get_label('reserved_subscription_message', __('اشتراک شما با موفقیت ثبت شد و پس از اتمام اشتراک فعلی به‌صورت خودکار فعال می‌شود.', 'eshterakyar'))); ?></p>
                        <?php elseif ($subscription->status == 'active'): ?>
                            <?php $days_left = Eshterakyar_Subscription::get_days_left($subscription->id); ?>
                            <p><strong><?php _e('روزهای باقی‌مانده:', 'eshterakyar'); ?></strong> <?php echo number_format_i18n($days_left); ?> <?php _e('روز', 'eshterakyar'); ?></p>
                            <p><strong><?php _e('دانلودها:', 'eshterakyar'); ?></strong> 
                                <?php echo number_format_i18n($subscription->downloads_used); ?> / 
                                <?php echo $subscription->total_downloads > 0 ? number_format_i18n($subscription->total_downloads) : esc_html(__('∞', 'eshterakyar')); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

