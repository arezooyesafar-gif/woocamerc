<?php

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="eshterakyar-shortcode-downloads">
    <h2 class="eshterakyar-shortcode-heading"><?php esc_html_e('دانلودهای اخیر', 'eshterakyar'); ?></h2>
    <?php if (empty($downloads)): ?>
        <p><?php esc_html_e('دانلودی یافت نشد.', 'eshterakyar'); ?></p>
    <?php else: ?>
        <ul class="eshterakyar-downloads-shortcode-list">
            <?php foreach ($downloads as $download):
                $product_id = (int) $download->product_id;
                $thumb = get_the_post_thumbnail($product_id, 'thumbnail', array('class' => 'eshterakyar-download-shortcode-thumb-img'));
                if (!$thumb) {
                    $thumb_src = function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src() : '';
                    if ($thumb_src) {
                        $thumb = '<img src="' . esc_url($thumb_src) . '" class="eshterakyar-download-shortcode-thumb-img" alt="' . esc_attr($download->product_name) . '" />';
                    }
                }
                $active_subscription = isset($active_subscription) ? $active_subscription : null;
                $can_download_row = Eshterakyar_Subscription::can_download(get_current_user_id(), $product_id, true);
                $redownload_uses_quota = !empty($can_download_row['redownload_uses_quota']);
                ?>
                <li class="eshterakyar-download-shortcode-item">
                    <?php if ($thumb): ?>
                        <div class="eshterakyar-download-shortcode-thumb">
                            <a href="<?php echo esc_url(get_permalink($product_id)); ?>" title="<?php echo esc_attr($download->product_name); ?>">
                                <?php echo $thumb; ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="eshterakyar-download-shortcode-info">
                        <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="eshterakyar-download-shortcode-title">
                            <?php echo esc_html($download->product_name); ?>
                        </a>
                    </div>
                    <div class="eshterakyar-download-shortcode-actions">
                        <span class="download-date"><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($download->download_date))); ?></span>
                        <?php if (empty($can_download_row['can'])): ?>
                            <span class="eshterakyar-download-disabled-note"><?php echo esc_html($can_download_row['message']); ?></span>
                        <?php else:
                            $subscription_id = $active_subscription ? $active_subscription->id : (isset($download->subscription_id) ? (int) $download->subscription_id : null);
                            $download_url = Eshterakyar_Download::get_tokenized_download_url(get_current_user_id(), $product_id, $subscription_id);
                            if (!$download_url) {
                                $download_url = '#';
                            }
                            $multipart_shortcode = Eshterakyar_Download::render_multipart_download_list_html($product_id, $redownload_uses_quota);
                            if ($multipart_shortcode !== ''): ?>
                                <?php echo $multipart_shortcode; ?>
                            <?php else: ?>
                                <a href="<?php echo esc_url($download_url); ?>" class="button eshterakyar-redownload-button<?php echo $redownload_uses_quota ? ' eshterakyar-redownload-with-quota' : ''; ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($redownload_uses_quota ? eshterakyar_get_label('redownload_with_quota', __('دانلود مجدد با کسر اعتبار', 'eshterakyar')) : eshterakyar_get_label('download_again', __('دانلود مجدد', 'eshterakyar'))); ?></a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

