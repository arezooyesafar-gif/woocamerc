<?php


if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="eshterakyar-product-download-status">
    <?php if (!$can_download['can']): ?>
        <?php
        $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
        ?>
        <div class="eshterakyar-download-error">
            <p><?php echo esc_html($can_download['message']); ?></p>
            
            <?php if ($subscription_page_url): ?>
                <div class="eshterakyar-renew-subscription-action">
                    <a href="<?php echo esc_url($subscription_page_url); ?>" class="button eshterakyar-renew-subscription-button">
                        <?php echo esc_html(eshterakyar_get_label('renew_subscription', __('تمدید اشتراک', 'eshterakyar'))); ?>
                    </a>
                    <p class="eshterakyar-renew-note"><?php echo esc_html(eshterakyar_get_label('renew_note', __('با تمدید اشتراک، محدودیت‌های دانلود شما بازنشانی می‌شود.', 'eshterakyar'))); ?></p>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php if ($is_downloaded): ?>
            <div class="eshterakyar-downloaded-badge">
                <span class="dashicons dashicons-yes-alt"></span>
                <?php echo esc_html(eshterakyar_get_label('already_downloaded', __('این محصول قبلاً دانلود شده است', 'eshterakyar'))); ?>
            </div>
        <?php endif; ?>
        
        <?php
        $user_id = get_current_user_id();
        $active_subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        $subscription_id = $active_subscription ? $active_subscription->id : null;
        $download_url = Eshterakyar_Download::get_tokenized_download_url($user_id, $product_id, $subscription_id);
        if (!$download_url) {
            $download_url = '#';
        }
        $multipart_list = Eshterakyar_Download::render_multipart_download_list_html($product_id, !empty($can_download['redownload_uses_quota']));
        ?>

        <?php
        $options = get_option('eshterakyar_options', array());
        $frontend_style = isset($options['frontend_style']) ? $options['frontend_style'] : 'style1';
        $is_modal_style = ($frontend_style === 'style2');
        $redownload_uses_quota = !empty($can_download['redownload_uses_quota']);
        $download_button_label = $redownload_uses_quota ? eshterakyar_get_label('redownload_with_quota', __('دانلود مجدد با کسر اعتبار', 'eshterakyar')) : ($is_downloaded ? eshterakyar_get_label('download_again', __('دانلود مجدد', 'eshterakyar')) : eshterakyar_get_label('download_with_subscription_short', __('دانلود با اشتراک', 'eshterakyar')));
        ?>

        <?php if ($redownload_uses_quota): ?>
            <p class="eshterakyar-redownload-quota-note"><?php echo esc_html(eshterakyar_get_label('redownload_with_quota_note', __('با کلیک، یک بار از سهمیه روزانه/کلی شما کسر می‌شود.', 'eshterakyar'))); ?></p>
        <?php endif; ?>

        <?php if ($is_modal_style): ?>
            <button type="button"
                    class="button eshterakyar-download-toggle-button"
                    data-eshterakyar-toggle="subscription-modal">
                <?php echo esc_html($is_downloaded ? eshterakyar_get_label('view_download_details', __('نمایش جزئیات دانلود', 'eshterakyar')) : eshterakyar_get_label('download_with_subscription_short', __('دانلود با اشتراک', 'eshterakyar'))); ?>
            </button>

            <div class="eshterakyar-download-modal" style="display:none;">
                <div class="eshterakyar-download-modal-backdrop"></div>
                <div class="eshterakyar-download-modal-content">
                    <button type="button" class="eshterakyar-download-modal-close" aria-label="<?php esc_attr_e('بستن', 'eshterakyar'); ?>">×</button>
                    <h4 class="eshterakyar-download-modal-title"><?php echo esc_html(eshterakyar_get_label('download_with_subscription_short', __('دانلود با اشتراک', 'eshterakyar'))); ?></h4>

                    <div class="eshterakyar-download-modal-body">
                        <?php if ($multipart_list !== '') : ?>
                            <?php echo $multipart_list; ?>
                        <?php else : ?>
                        <a href="<?php echo esc_url($download_url); ?>" class="button button-primary eshterakyar-download-button<?php echo $redownload_uses_quota ? ' eshterakyar-redownload-with-quota' : ''; ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html($redownload_uses_quota ? eshterakyar_get_label('redownload_with_quota', __('دانلود مجدد با کسر اعتبار', 'eshterakyar')) : ($is_downloaded ? eshterakyar_get_label('download_again', __('دانلود مجدد', 'eshterakyar')) : eshterakyar_get_label('start_download', __('شروع دانلود', 'eshterakyar')))); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php if ($multipart_list !== '') : ?>
                <?php echo $multipart_list; ?>
            <?php else : ?>
            <a href="<?php echo esc_url($download_url); ?>" class="button eshterakyar-download-button<?php echo $redownload_uses_quota ? ' eshterakyar-redownload-with-quota' : ''; ?>" target="_blank" rel="noopener noreferrer">
                <?php echo esc_html($download_button_label); ?>
            </a>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

