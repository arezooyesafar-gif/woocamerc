<?php


if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="eshterakyar-shortcode-subscriptions">
    <h2 class="eshterakyar-shortcode-heading"><?php esc_html_e('اشتراک‌های من', 'eshterakyar'); ?></h2>
    <?php if (empty($subscriptions)): ?>
        <?php
        $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
        ?>
        <p><?php esc_html_e('اشتراکی یافت نشد.', 'eshterakyar'); ?></p>
        <?php if ($subscription_page_url): ?>
            <a href="<?php echo esc_url($subscription_page_url); ?>" class="button eshterakyar-buy-subscription-button">
                <?php echo esc_html(eshterakyar_get_label('buy_subscription', __('خرید اشتراک', 'eshterakyar'))); ?>
            </a>
        <?php endif; ?>
    <?php else: ?>
        <ul class="eshterakyar-subscriptions-shortcode-list">
            <?php foreach ($subscriptions as $subscription): ?>
                <?php $is_reserved = isset($subscription->status) && $subscription->status === 'reserved'; ?>
                <li class="eshterakyar-subscription-shortcode-item status-<?php echo esc_attr($subscription->status); ?>">
                    <strong><?php echo esc_html($subscription->plan_name); ?></strong>
                    <span class="subscription-status"><?php echo $is_reserved ? esc_html(eshterakyar_get_label('reserved_subscription_status_label', __('رزرو شده', 'eshterakyar'))) : esc_html($subscription->status); ?></span>
                    <?php if ($is_reserved): ?>
                        <p class="eshterakyar-reserved-message"><?php echo esc_html(eshterakyar_get_label('reserved_subscription_message', __('اشتراک شما با موفقیت ثبت شد و پس از اتمام اشتراک فعلی به‌صورت خودکار فعال می‌شود.', 'eshterakyar'))); ?></p>
                    <?php elseif ($subscription->status == 'active'): ?>
                        <span class="days-left"><?php echo esc_html(Eshterakyar_Subscription::get_days_left($subscription->id)); ?> <?php esc_html_e('روز باقی مانده', 'eshterakyar'); ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

