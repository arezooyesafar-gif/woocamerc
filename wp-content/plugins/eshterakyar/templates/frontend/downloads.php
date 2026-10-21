<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="eshterakyar-user-downloads">
    <h2><?php _e('دانلودهای اشتراک من', 'eshterakyar'); ?></h2>
    
    <?php
    $options = get_option('eshterakyar_options', array());
    $retention_days = isset($options['download_history_retention']) ? intval($options['download_history_retention']) : 365;
    $retention_date = date_i18n(get_option('date_format'), strtotime("+{$retention_days} days"));
    ?>
    
    <?php if ($retention_days > 0): ?>
        <div class="eshterakyar-retention-notice">
            <p class="description">
                <?php
                /* translators: %s: Retention date */
                printf(__('سوابق دانلود شما تا %s نگهداری می‌شوند.', 'eshterakyar'), $retention_date);
                ?>
            </p>
        </div>
    <?php endif; ?>
    
    <?php
    $subscription_downloads = isset($subscription_downloads) ? $subscription_downloads : array();
    $other_downloads = isset($other_downloads) ? $other_downloads : array();
    ?>
    
    <?php if (empty($subscription_downloads) && empty($other_downloads)): ?>
        <p><?php _e('شما هنوز هیچ محصولی دانلود نکرده‌اید.', 'eshterakyar'); ?></p>
    <?php else: ?>
        
        <?php if (!empty($subscription_downloads)): ?>
            <div class="eshterakyar-downloads-section eshterakyar-subscription-downloads">
                <div class="eshterakyar-downloads-section-header">
                    <h3><?php _e('دانلودهای اشتراک فعلی', 'eshterakyar'); ?></h3>
                    <?php if ($active_subscription): ?>
                        <p class="eshterakyar-section-description">
                            <?php
                            /* translators: %s: Subscription plan name */
                            printf(__('دانلودهای مربوط به اشتراک "%s" شما', 'eshterakyar'), esc_html($active_subscription->plan_name)); ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="eshterakyar-downloads-list">
                    <table class="eshterakyar-downloads-table">
                        <thead>
                            <tr>
                                <th><?php _e('محصول', 'eshterakyar'); ?></th>
                                <th><?php _e('تاریخ دانلود', 'eshterakyar'); ?></th>
                                <th><?php _e('تعداد دانلود', 'eshterakyar'); ?></th>
                                <th><?php _e('عملیات', 'eshterakyar'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subscription_downloads as $download): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo get_permalink($download->product_id); ?>">
                                            <?php echo esc_html($download->product_name); ?>
                                        </a>
                                    </td>
                                    <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($download->download_date)); ?></td>
                                    <td><?php echo number_format_i18n($download->download_count); ?></td>
                                    <td>
                                        <?php
                                        $user_id = get_current_user_id();
                                        $product_id = (int) $download->product_id;
                                        $subscription_id = $active_subscription ? $active_subscription->id : null;
                                        $can_download_row = Eshterakyar_Subscription::can_download($user_id, $product_id, true);
                                        $download_url = $can_download_row['can'] ? Eshterakyar_Download::get_tokenized_download_url($user_id, $product_id, $subscription_id) : null;
                                        if (!$download_url) {
                                            $download_url = '#';
                                        }
                                        $redownload_uses_quota = !empty($can_download_row['redownload_uses_quota']);
                                        $is_lifetime_access = !empty($can_download_row['lifetime_access']) && $can_download_row['lifetime_access'];
                                        ?>
                                        <?php if (!$can_download_row['can']): ?>
                                            <span class="eshterakyar-download-disabled-note"><?php echo esc_html($can_download_row['message']); ?></span>
                                            <?php
                                            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
                                            if ($subscription_page_url):
                                            ?>
                                                <a href="<?php echo esc_url($subscription_page_url); ?>" class="button eshterakyar-renew-subscription-button"><?php echo esc_html(eshterakyar_get_label('renew_subscription', __('تمدید اشتراک', 'eshterakyar'))); ?></a>
                                                <p class="eshterakyar-renew-note description"><?php echo esc_html(eshterakyar_get_label('renew_note', __('با تمدید اشتراک، محدودیت‌های دانلود شما بازنشانی می‌شود.', 'eshterakyar'))); ?></p>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($is_lifetime_access): ?>
                                                <p class="eshterakyar-lifetime-access-note description" style="margin: 0 0 20px; color: #22c55e;"><?php echo esc_html(eshterakyar_get_label('lifetime_access_note', __('دسترسی مادام‌العمر: شما می‌توانید این فایل را در هر زمان، حتی بدون اشتراک فعال، دانلود کنید.', 'eshterakyar'))); ?></p>
                                            <?php elseif ($redownload_uses_quota): ?>
                                                <p class="eshterakyar-redownload-quota-note description" style="margin: 0 0 20px;"><?php echo esc_html(eshterakyar_get_label('redownload_with_quota_note', __('با کلیک، یک بار از سهمیه روزانه/کلی شما کسر می‌شود.', 'eshterakyar'))); ?></p>
                                            <?php endif; ?>
                                            <?php
                                            $multipart_acct_sub = Eshterakyar_Download::render_multipart_download_list_html($product_id, $redownload_uses_quota);
                                            if ($multipart_acct_sub !== '') {
                                                echo $multipart_acct_sub;
                                            } else {
                                                ?>
                                            <a href="<?php echo esc_url($download_url); ?>" class="button eshterakyar-redownload-button<?php echo $redownload_uses_quota ? ' eshterakyar-redownload-with-quota' : ''; ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($redownload_uses_quota ? eshterakyar_get_label('redownload_with_quota', __('دانلود مجدد با کسر اعتبار', 'eshterakyar')) : eshterakyar_get_label('download_again', __('دانلود مجدد', 'eshterakyar'))); ?></a>
                                                <?php
                                            }
                                            ?>
                                        <?php endif; ?>
                                        <a href="<?php echo esc_url(get_permalink($download->product_id)); ?>" class="button"><?php _e('مشاهده محصول', 'eshterakyar'); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($other_downloads)): ?>
            <div class="eshterakyar-downloads-section eshterakyar-other-downloads">
                <div class="eshterakyar-downloads-section-header">
                    <h3><?php _e('سایر دانلودها', 'eshterakyar'); ?></h3>
                    <p class="eshterakyar-section-description">
                        <?php _e('دانلودهای مربوط به اشتراک‌های قبلی یا محصولات رایگان', 'eshterakyar'); ?>
                    </p>
                </div>
                <div class="eshterakyar-downloads-list">
                    <table class="eshterakyar-downloads-table">
                        <thead>
                            <tr>
                                <th><?php _e('محصول', 'eshterakyar'); ?></th>
                                <th><?php _e('تاریخ دانلود', 'eshterakyar'); ?></th>
                                <th><?php _e('تعداد دانلود', 'eshterakyar'); ?></th>
                                <th><?php _e('عملیات', 'eshterakyar'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($other_downloads as $download): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo get_permalink($download->product_id); ?>">
                                            <?php echo esc_html($download->product_name); ?>
                                        </a>
                                    </td>
                                    <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($download->download_date)); ?></td>
                                    <td><?php echo number_format_i18n($download->download_count); ?></td>
                                    <td>
                                        <?php
                                        $user_id = get_current_user_id();
                                        $product_id = (int) $download->product_id;
                                        $subscription_id = $active_subscription ? $active_subscription->id : (isset($download->subscription_id) ? (int) $download->subscription_id : null);
                                        $can_download_row = Eshterakyar_Subscription::can_download($user_id, $product_id, true);
                                        $download_url = $can_download_row['can'] ? Eshterakyar_Download::get_tokenized_download_url($user_id, $product_id, $subscription_id) : null;
                                        if (!$download_url) {
                                            $download_url = '#';
                                        }
                                        $redownload_uses_quota = !empty($can_download_row['redownload_uses_quota']);
                                        $is_lifetime_access = !empty($can_download_row['lifetime_access']) && $can_download_row['lifetime_access'];
                                        ?>
                                        <?php if (!$can_download_row['can']): ?>
                                            <span class="eshterakyar-download-disabled-note"><?php echo esc_html($can_download_row['message']); ?></span>
                                            <?php
                                            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
                                            if ($subscription_page_url):
                                            ?>
                                                <a href="<?php echo esc_url($subscription_page_url); ?>" class="button eshterakyar-renew-subscription-button"><?php echo esc_html(eshterakyar_get_label('renew_subscription', __('تمدید اشتراک', 'eshterakyar'))); ?></a>
                                                <p class="eshterakyar-renew-note description"><?php echo esc_html(eshterakyar_get_label('renew_note', __('با تمدید اشتراک، محدودیت‌های دانلود شما بازنشانی می‌شود.', 'eshterakyar'))); ?></p>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($is_lifetime_access): ?>
                                                <p class="eshterakyar-lifetime-access-note description" style="margin: 0 0 20px; color: #22c55e;"><?php echo esc_html(eshterakyar_get_label('lifetime_access_note', __('دسترسی مادام‌العمر: شما می‌توانید این فایل را در هر زمان، حتی بدون اشتراک فعال، دانلود کنید.', 'eshterakyar'))); ?></p>
                                            <?php elseif ($redownload_uses_quota): ?>
                                                <p class="eshterakyar-redownload-quota-note description" style="margin: 0 0 20px;"><?php echo esc_html(eshterakyar_get_label('redownload_with_quota_note', __('با کلیک، یک بار از سهمیه روزانه/کلی شما کسر می‌شود.', 'eshterakyar'))); ?></p>
                                            <?php endif; ?>
                                            <?php
                                            $multipart_acct_other = Eshterakyar_Download::render_multipart_download_list_html($product_id, $redownload_uses_quota);
                                            if ($multipart_acct_other !== '') {
                                                echo $multipart_acct_other;
                                            } else {
                                                ?>
                                            <a href="<?php echo esc_url($download_url); ?>" class="button eshterakyar-redownload-button<?php echo $redownload_uses_quota ? ' eshterakyar-redownload-with-quota' : ''; ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($redownload_uses_quota ? eshterakyar_get_label('redownload_with_quota', __('دانلود مجدد با کسر اعتبار', 'eshterakyar')) : eshterakyar_get_label('download_again', __('دانلود مجدد', 'eshterakyar'))); ?></a>
                                                <?php
                                            }
                                            ?>
                                        <?php endif; ?>
                                        <a href="<?php echo esc_url(get_permalink($download->product_id)); ?>" class="button"><?php _e('مشاهده محصول', 'eshterakyar'); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
    <?php endif; ?>
    
    <?php
    $featurebase_enabled = defined('ESHTERAKYAR_FEATUREBASE_ENABLED') ? ESHTERAKYAR_FEATUREBASE_ENABLED : false;
    $featurebase_url = defined('ESHTERAKYAR_FEATUREBASE_URL') ? ESHTERAKYAR_FEATUREBASE_URL : '';
    $featurebase_widget_enabled = defined('ESHTERAKYAR_FEATUREBASE_WIDGET_ENABLED') ? ESHTERAKYAR_FEATUREBASE_WIDGET_ENABLED : false;
    $featurebase_widget_id = defined('ESHTERAKYAR_FEATUREBASE_WIDGET_ID') ? ESHTERAKYAR_FEATUREBASE_WIDGET_ID : '';
    
    if ($featurebase_enabled && $featurebase_widget_enabled && !empty($featurebase_widget_id)):
    ?>
        <div class="eshterakyar-featurebase-widget-frontend" style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-radius: 5px; border: 1px solid #ddd;">
            <h3><?php _e('گزارش باگ و پیشنهاد ویژگی', 'eshterakyar'); ?></h3>
            <p><?php _e('اگر مشکلی دارید یا پیشنهادی دارید، لطفاً از طریق فرم زیر با ما در میان بگذارید:', 'eshterakyar'); ?></p>
            
            <?php if (!empty($featurebase_url)): ?>
                <p>
                    <a href="<?php echo esc_url($featurebase_url); ?>" target="_blank" class="button">
                        <?php _e('مشاهده پورتال بازخورد', 'eshterakyar'); ?>
                    </a>
                    <a href="<?php echo esc_url($featurebase_url . '/roadmap'); ?>" target="_blank" class="button button-secondary">
                        <?php _e('مشاهده نقشه راه', 'eshterakyar'); ?>
                    </a>
                </p>
            <?php endif; ?>
            
            <div id="featurebase-widget-frontend" style="margin-top: 20px;">
                <iframe 
                    src="https://feedback.featurebaseapp.com/widget/<?php echo esc_attr($featurebase_widget_id); ?>" 
                    width="100%" 
                    height="500" 
                    frameborder="0" 
                    style="border-radius: 5px; border: 1px solid #ddd;">
                </iframe>
            </div>
        </div>
    <?php endif; ?>
</div>

