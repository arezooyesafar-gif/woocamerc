<?php

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="eshterakyar-customers-content">
    <div class="eshterakyar-filters">
        <form method="get" action="" class="eshterakyar-search-form">
            <input type="hidden" name="page" value="eshterakyar-customers">
            <input type="text" name="search" placeholder="<?php esc_attr_e('جست‌وجو بر اساس نام، ایمیل یا شماره موبایل', 'eshterakyar'); ?>" value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>" class="regular-text">
            <select name="subscription_status">
                <option value=""><?php esc_html_e('همه مشتریان', 'eshterakyar'); ?></option>
                <option value="has_active" <?php selected(isset($_GET['subscription_status']) && $_GET['subscription_status'] == 'has_active'); ?>><?php esc_html_e('دارای اشتراک فعال', 'eshterakyar'); ?></option>
                <option value="has_expired" <?php selected(isset($_GET['subscription_status']) && $_GET['subscription_status'] == 'has_expired'); ?>><?php esc_html_e('فقط اشتراک‌های منقضی', 'eshterakyar'); ?></option>
            </select>
            <input type="submit" class="button" value="<?php esc_attr_e('جست‌وجو', 'eshterakyar'); ?>">
            <?php if (isset($_GET['search']) || isset($_GET['subscription_status'])): ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-customers')); ?>" class="button"><?php esc_html_e('پاک کردن فیلتر', 'eshterakyar'); ?></a>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="eshterakyar-customers-stats-grid">
        <div class="stat-card-customer stat-item-primary">
            <div class="stat-icon-customer">👥</div>
            <div class="stat-content-customer">
                <div class="stat-label-customer"><?php esc_html_e('کل مشتریان', 'eshterakyar'); ?></div>
                <div class="stat-value-customer"><?php echo esc_html(number_format_i18n($total_customers)); ?></div>
            </div>
        </div>
        <div class="stat-card-customer stat-item-success">
            <div class="stat-icon-customer">✅</div>
            <div class="stat-content-customer">
                <div class="stat-label-customer"><?php esc_html_e('مشتریان با اشتراک فعال', 'eshterakyar'); ?></div>
                <div class="stat-value-customer"><?php echo esc_html(number_format_i18n($customers_with_active)); ?></div>
            </div>
        </div>
    </div>
    
    <form method="post" action="" id="eshterakyar-customers-form">
        <?php wp_nonce_field('eshterakyar_bulk_action', 'eshterakyar_bulk_nonce'); ?>
        
        <div class="tablenav top eshterakyar-bulk-actions-wrapper">
            <div class="eshterakyar-bulk-actions">
                <div class="bulk-actions-label">
                    <span class="bulk-actions-icon">⚙️</span>
                    <span><?php esc_html_e('عملیات دسته‌ای', 'eshterakyar'); ?></span>
                </div>
                <select name="bulk_action" id="bulk-action-selector" class="eshterakyar-bulk-select">
                    <option value=""><?php esc_html_e('انتخاب عملیات...', 'eshterakyar'); ?></option>
                    <option value="export"><?php esc_html_e('خروجی Excel', 'eshterakyar'); ?></option>
                </select>
                <button type="submit" class="button button-primary eshterakyar-bulk-submit">
                    <span class="submit-icon">✓</span>
                    <span><?php esc_html_e('اعمال', 'eshterakyar'); ?></span>
                </button>
            </div>
            <div class="tablenav-pages">
                <?php echo wp_kses_post($pagination); ?>
            </div>
        </div>
        
        <table class="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <input type="checkbox" id="cb-select-all">
                    </td>
                    <th class="manage-column"><?php esc_html_e('نام کاربر', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('ایمیل', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('شماره موبایل', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('اشتراک‌های فعال', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('اشتراک‌های منقضی', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('کل دانلودها', 'eshterakyar'); ?></th>
                    <th class="manage-column"><?php esc_html_e('عملیات', 'eshterakyar'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="8"><?php esc_html_e('مشتریی یافت نشد.', 'eshterakyar'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <th scope="row" class="check-column">
                                <input type="checkbox" name="customer_ids[]" value="<?php echo esc_attr($customer->user_id); ?>">
                            </th>
                            <td>
                                <strong>
                                    <a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $customer->user_id)); ?>">
                                        <?php echo esc_html($customer->display_name); ?>
                                    </a>
                                </strong>
                                <br>
                                <span class="user-id">ID: <?php echo esc_html($customer->user_id); ?></span>
                            </td>
                            <td><?php echo esc_html($customer->user_email); ?></td>
                            <td><?php echo esc_html($customer->billing_phone ? $customer->billing_phone : '-'); ?></td>
                            <td>
                                <span class="count-badge active"><?php echo esc_html(number_format_i18n($customer->active_count)); ?></span>
                            </td>
                            <td>
                                <span class="count-badge expired"><?php echo esc_html(number_format_i18n($customer->expired_count)); ?></span>
                            </td>
                            <td><?php echo esc_html(number_format_i18n($customer->total_downloads)); ?></td>
                            <td>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-subscriptions&user_id=' . $customer->user_id)); ?>" class="button button-small">
                                    <?php esc_html_e('مشاهده اشتراک‌ها', 'eshterakyar'); ?>
                                </a>
                                <a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $customer->user_id)); ?>" class="button button-small">
                                    <?php esc_html_e('ویرایش کاربر', 'eshterakyar'); ?>
                                </a>
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
        $('input[name="customer_ids[]"]').prop('checked', this.checked);
    });
});
</script>

