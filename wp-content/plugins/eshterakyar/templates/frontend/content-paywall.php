<?php
/**
 * بنر «ادامه مطلب ویژه» در مقاله
 *
 * @var string $subscription_page_url
 * @var bool   $is_logged_in
 * @var bool   $show_login_hint
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="eshterakyar-content-paywall">
    <div class="eshterakyar-content-paywall-inner">
        <h3 class="eshterakyar-content-paywall-title">
            <?php echo esc_html(eshterakyar_get_label('content_paywall_title', __('ادامه مطلب ویژه', 'eshterakyar'))); ?>
        </h3>
        <p class="eshterakyar-content-paywall-message">
            <?php
            if (!$is_logged_in && $show_login_hint) {
                echo esc_html(eshterakyar_get_label('content_paywall_guest', __('برای دیدن ادامه مطلب وارد حساب شوید یا اشتراک تهیه کنید.', 'eshterakyar')));
            } else {
                echo esc_html(eshterakyar_get_label('content_paywall_message', __('برای مطالعه ادامه این مقاله به اشتراک فعال نیاز دارید.', 'eshterakyar')));
            }
            ?>
        </p>
        <?php if ($subscription_page_url) : ?>
            <div class="eshterakyar-content-paywall-actions">
                <a href="<?php echo esc_url($subscription_page_url); ?>" class="button eshterakyar-buy-subscription-button">
                    <?php echo esc_html(eshterakyar_get_label('content_buy_subscription', __('خرید اشتراک', 'eshterakyar'))); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
