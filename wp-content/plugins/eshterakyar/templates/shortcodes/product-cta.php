<?php
/**
 * Product CTA shortcode template — buy (cart/plans) or free download with subscription.
 *
 * Expected vars from product_cta_shortcode():
 * $atts, $product_id, $state, $no_sub_mode (via $atts), $wrap_classes,
 * $plans_url, $plans_text, $download_text, $note_text, $download_url,
 * $multipart_html, $quota, $can_download, $lifetime_access, $active_subscription,
 * $is_downloaded, $is_downloaded_any, $redownload_uses_quota
 *
 * @package Eshterakyar
 */

if (!defined('ABSPATH')) {
    exit;
}

$no_sub_mode = isset($atts['no_sub_mode']) ? $atts['no_sub_mode'] : 'both';
$show_cart = in_array($no_sub_mode, array('cart', 'both'), true);
$show_plans = in_array($no_sub_mode, array('plans', 'both'), true);
$show_note = $this->shortcode_flag_enabled($atts['show_note']);
$show_daily_bar = $this->shortcode_flag_enabled($atts['show_daily_bar']);
$show_total_bar = $this->shortcode_flag_enabled($atts['show_total_bar']);
$show_download_url = $this->shortcode_flag_enabled($atts['show_download_url']);
$show_plan_name = $this->shortcode_flag_enabled($atts['show_plan_name']);
?>

<div class="<?php echo esc_attr(implode(' ', $wrap_classes)); ?>" data-product-id="<?php echo esc_attr((string) $product_id); ?>">

    <?php if ($state === 'download') : ?>

        <div class="eshterakyar-cta-download">
            <?php if ($lifetime_access) : ?>
                <p class="eshterakyar-lifetime-access-note description">
                    <?php echo esc_html(eshterakyar_get_label('lifetime_access_note', __('دسترسی مادام‌العمر: شما می‌توانید این فایل را در هر زمان، حتی بدون اشتراک فعال، دانلود کنید.', 'eshterakyar'))); ?>
                </p>
            <?php endif; ?>

            <?php if ($show_plan_name && $active_subscription && !empty($active_subscription->plan_name)) : ?>
                <p class="eshterakyar-cta-plan-name">
                    <span class="eshterakyar-cta-plan-label"><?php echo esc_html__('اشتراک:', 'eshterakyar'); ?></span>
                    <span class="eshterakyar-cta-plan-value"><?php echo esc_html($active_subscription->plan_name); ?></span>
                </p>
            <?php endif; ?>

            <?php if ($is_downloaded || $is_downloaded_any) : ?>
                <div class="eshterakyar-downloaded-badge">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php echo esc_html(eshterakyar_get_label('already_downloaded', __('این محصول قبلاً دانلود شده است', 'eshterakyar'))); ?>
                </div>
            <?php endif; ?>

            <?php if ($redownload_uses_quota) : ?>
                <p class="eshterakyar-redownload-quota-note">
                    <?php echo esc_html(eshterakyar_get_label('redownload_with_quota_note', __('با کلیک، یک بار از سهمیه روزانه/کلی شما کسر می‌شود.', 'eshterakyar'))); ?>
                </p>
            <?php endif; ?>

            <div class="eshterakyar-cta-download-actions">
                <?php if ($multipart_html !== '') : ?>
                    <div class="eshterakyar-download-button-wrapper eshterakyar-download-multipart-wrap">
                        <?php echo $multipart_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by plugin helper ?>
                    </div>
                <?php elseif ($download_url !== '') : ?>
                    <div class="eshterakyar-download-button-wrapper">
                        <a href="<?php echo esc_url($download_url); ?>"
                           class="button eshterakyar-download-button eshterakyar-cta-download-button<?php echo $redownload_uses_quota ? ' eshterakyar-redownload-with-quota' : ''; ?>"
                           data-product-id="<?php echo esc_attr((string) $product_id); ?>"
                           target="_blank"
                           rel="noopener noreferrer">
                            <?php echo esc_html($download_text); ?>
                        </a>
                    </div>

                    <?php if ($show_download_url) : ?>
                        <p class="eshterakyar-cta-download-link-wrap">
                            <span class="eshterakyar-cta-download-link-label"><?php echo esc_html__('لینک دانلود:', 'eshterakyar'); ?></span>
                            <a href="<?php echo esc_url($download_url); ?>"
                               class="eshterakyar-cta-download-url"
                               target="_blank"
                               rel="noopener noreferrer">
                                <?php echo esc_html__('باز کردن لینک دانلود', 'eshterakyar'); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="eshterakyar-download-error">
                        <p class="eshterakyar-alert"><?php echo esc_html__('لینک دانلود در دسترس نیست.', 'eshterakyar'); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($quota && ($show_daily_bar || $show_total_bar)) : ?>
                <div class="eshterakyar-cta-quota-bars">
                    <?php if ($show_daily_bar) : ?>
                        <div class="eshterakyar-cta-quota-bar eshterakyar-cta-daily-bar" role="status">
                            <div class="eshterakyar-cta-quota-bar-header">
                                <span class="eshterakyar-cta-quota-bar-title">
                                    <?php echo esc_html__('دانلودهای باقی‌مانده امروز', 'eshterakyar'); ?>
                                </span>
                                <span class="eshterakyar-cta-quota-bar-value">
                                    <?php if (!empty($quota['daily_unlimited'])) : ?>
                                        <?php echo esc_html__('نامحدود', 'eshterakyar'); ?>
                                    <?php else : ?>
                                        <?php echo esc_html(absint($quota['remaining_daily']) . ' / ' . absint($quota['daily_limit'])); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php if (empty($quota['daily_unlimited'])) : ?>
                                <?php
                                $daily_pct = $quota['daily_limit'] > 0
                                    ? min(100, round(($quota['remaining_daily'] / $quota['daily_limit']) * 100))
                                    : 0;
                                $daily_state = $quota['remaining_daily'] <= 0 ? 'exhausted' : ($daily_pct <= 30 ? 'low' : 'ok');
                                ?>
                                <div class="eshterakyar-cta-quota-bar-track" aria-hidden="true">
                                    <div class="eshterakyar-cta-quota-bar-fill eshterakyar-cta-quota-<?php echo esc_attr($daily_state); ?>"
                                         style="width: <?php echo esc_attr((string) $daily_pct); ?>%;"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($show_total_bar) : ?>
                        <div class="eshterakyar-cta-quota-bar eshterakyar-cta-total-bar" role="status">
                            <div class="eshterakyar-cta-quota-bar-header">
                                <span class="eshterakyar-cta-quota-bar-title">
                                    <?php echo esc_html__('دانلودهای باقی‌مانده کل', 'eshterakyar'); ?>
                                </span>
                                <span class="eshterakyar-cta-quota-bar-value">
                                    <?php if (!empty($quota['total_unlimited'])) : ?>
                                        <?php echo esc_html__('نامحدود', 'eshterakyar'); ?>
                                    <?php else : ?>
                                        <?php echo esc_html(absint($quota['remaining_total']) . ' / ' . absint($quota['total_limit'])); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php if (empty($quota['total_unlimited'])) : ?>
                                <?php
                                $total_pct = $quota['total_limit'] > 0
                                    ? min(100, round(($quota['remaining_total'] / $quota['total_limit']) * 100))
                                    : 0;
                                $total_state = $quota['remaining_total'] <= 0 ? 'exhausted' : ($total_pct <= 30 ? 'low' : 'ok');
                                ?>
                                <div class="eshterakyar-cta-quota-bar-track" aria-hidden="true">
                                    <div class="eshterakyar-cta-quota-bar-fill eshterakyar-cta-quota-<?php echo esc_attr($total_state); ?>"
                                         style="width: <?php echo esc_attr((string) $total_pct); ?>%;"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    <?php elseif ($state === 'blocked') : ?>

        <div class="eshterakyar-cta-blocked eshterakyar-download-error">
            <p class="eshterakyar-alert">
                <?php echo esc_html(!empty($can_download['message']) ? $can_download['message'] : __('امکان دانلود این محصول وجود ندارد.', 'eshterakyar')); ?>
            </p>
            <?php if ($plans_url) : ?>
                <div class="eshterakyar-renew-subscription-action">
                    <a href="<?php echo esc_url($plans_url); ?>" class="button eshterakyar-renew-subscription-button">
                        <?php echo esc_html(eshterakyar_get_label('renew_subscription', __('تمدید اشتراک', 'eshterakyar'))); ?>
                    </a>
                    <p class="eshterakyar-renew-note">
                        <?php echo esc_html(eshterakyar_get_label('renew_note', __('با تمدید اشتراک، محدودیت‌های دانلود شما بازنشانی می‌شود.', 'eshterakyar'))); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if ($show_cart) : ?>
                <div class="eshterakyar-cta-blocked-cart">
                    <?php echo $this->render_product_cta_cart_block($product_id, $atts); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            <?php endif; ?>
        </div>

    <?php else : ?>

        <div class="eshterakyar-cta-inactive eshterakyar-cta-mode-<?php echo esc_attr($no_sub_mode); ?>">
            <?php if ($show_cart) : ?>
                <?php echo $this->render_product_cta_cart_block($product_id, $atts); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php endif; ?>

            <?php if ($show_plans && $plans_url) : ?>
                <div class="eshterakyar-cta-plans-action">
                    <a href="<?php echo esc_url($plans_url); ?>" class="button eshterakyar-buy-subscription-button eshterakyar-cta-plans-button">
                        <?php echo esc_html($plans_text); ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($show_note && $note_text !== '') : ?>
                <p class="eshterakyar-subscription-note eshterakyar-cta-note"><?php echo esc_html($note_text); ?></p>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</div>
