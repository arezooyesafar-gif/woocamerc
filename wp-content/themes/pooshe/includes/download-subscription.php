<?php

defined('ABSPATH') || exit;

function pooshe_dl_sub_is_enabled()
{
    return function_exists('pooshe_opt') && defined('STYLE_OPTS') && pooshe_opt(STYLE_OPTS, 'download_subscription', false);
}

add_action('add_meta_boxes', 'pooshe_dl_sub_add_meta_boxes');
function pooshe_dl_sub_add_meta_boxes()
{
    if (!pooshe_dl_sub_is_enabled()) return;

    add_meta_box(
        'pooshe_dl_sub_package',
        'بسته اشتراک دانلود',
        'pooshe_dl_sub_package_meta_box',
        'product',
        'side',
        'default'
    );
    add_meta_box(
        'pooshe_dl_sub_eligible',
        'دانلود با اشتراک',
        'pooshe_dl_sub_eligible_meta_box',
        'product',
        'side',
        'default'
    );
}

function pooshe_dl_sub_package_meta_box($post)
{
    wp_nonce_field('pooshe_dl_sub_package', 'pooshe_dl_sub_package_nonce');
    $is_package = get_post_meta($post->ID, '_pooshe_dl_package', true);
    $dl_count = get_post_meta($post->ID, '_pooshe_dl_count', true);
?>
    <p>
        <label>
            <input type="checkbox" name="_pooshe_dl_package" value="yes" <?php checked($is_package, 'yes'); ?>>
            این محصول یک بسته اشتراک دانلود است
        </label>
    </p>
    <p>
        <label>تعداد دانلود مجاز:</label>
        <input type="number" name="_pooshe_dl_count" value="<?php echo esc_attr($dl_count ?: ''); ?>" class="widefat" placeholder="خالی = نامحدود">
        <small>خالی یا صفر = نامحدود</small>
    </p>
<?php
}

function pooshe_dl_sub_eligible_meta_box($post)
{
    wp_nonce_field('pooshe_dl_sub_eligible', 'pooshe_dl_sub_eligible_nonce');
    $eligible = get_post_meta($post->ID, '_pooshe_dl_eligible', true);
    if ($eligible === '') {
        $eligible = 'yes';
    }
?>
    <p>
        <label>
            <input type="checkbox" name="_pooshe_dl_eligible" value="yes" <?php checked($eligible, 'yes'); ?>>
            قابل دانلود با اشتراک
        </label>
    </p>
    <p><small>اگر فعال باشد، کاربران دارای اشتراک می‌توانند این محصول را دانلود کنند.</small></p>
<?php
}

add_action('save_post_product', 'pooshe_dl_sub_save_meta');
function pooshe_dl_sub_save_meta($post_id)
{
    if (isset($_POST['pooshe_dl_sub_package_nonce']) && wp_verify_nonce($_POST['pooshe_dl_sub_package_nonce'], 'pooshe_dl_sub_package')) {
        update_post_meta($post_id, '_pooshe_dl_package', isset($_POST['_pooshe_dl_package']) ? 'yes' : 'no');
        $count = isset($_POST['_pooshe_dl_count']) ? intval($_POST['_pooshe_dl_count']) : -1;
        update_post_meta($post_id, '_pooshe_dl_count', $count > 0 ? $count : -1);
    }
    if (isset($_POST['pooshe_dl_sub_eligible_nonce']) && wp_verify_nonce($_POST['pooshe_dl_sub_eligible_nonce'], 'pooshe_dl_sub_eligible')) {
        update_post_meta($post_id, '_pooshe_dl_eligible', isset($_POST['_pooshe_dl_eligible']) ? 'yes' : 'no');
    }
}

add_action('woocommerce_order_status_completed', 'pooshe_dl_sub_grant_credits');
function pooshe_dl_sub_grant_credits($order_id)
{
    if (!pooshe_dl_sub_is_enabled()) return;
    $order = wc_get_order($order_id);
    if (!$order) return;

    $user_id = $order->get_user_id();
    if (!$user_id) return;

    $total_granted = 0;

    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        if (get_post_meta($product_id, '_pooshe_dl_package', true) !== 'yes') continue;

        $dl_count = get_post_meta($product_id, '_pooshe_dl_count', true);
        $qty = $item->get_quantity();

        if ($dl_count < 0) {
            $total_granted = -1;
            break;
        }
        $total_granted += intval($dl_count) * $qty;
    }

    if ($total_granted === 0) return;

    if ($total_granted === -1) {
        update_user_meta($user_id, 'pooshe_dl_remaining', -1);
        update_user_meta($user_id, 'pooshe_dl_total', -1);
        $order->add_order_note('اشتراک دانلود نامحدود به کاربر اعطا شد.');
    } else {
        $current = intval(get_user_meta($user_id, 'pooshe_dl_remaining', true));
        if ($current === -1) return;

        update_user_meta($user_id, 'pooshe_dl_remaining', $current + $total_granted);
        $total = intval(get_user_meta($user_id, 'pooshe_dl_total', true));
        update_user_meta($user_id, 'pooshe_dl_total', $total + $total_granted);
        $order->add_order_note(sprintf('اعتبار دانلود اشتراک به کاربر اعطا شد: %d دانلود', $total_granted));
    }
}

function pooshe_dl_sub_get_remaining($user_id = null)
{
    if (!$user_id) $user_id = get_current_user_id();
    if (!$user_id) return 0;

    $val = get_user_meta($user_id, 'pooshe_dl_remaining', true);
    return ($val === '' || $val === false) ? 0 : intval($val);
}

add_action('init', 'pooshe_dl_sub_add_endpoint');
function pooshe_dl_sub_add_endpoint()
{
    if (!pooshe_dl_sub_is_enabled()) return;
    add_rewrite_endpoint('download-subscription', EP_ROOT | EP_PAGES);
}

add_filter('query_vars', 'pooshe_dl_sub_query_vars', 0);
function pooshe_dl_sub_query_vars($vars)
{
    $vars[] = 'download-subscription';
    return $vars;
}

add_filter('woocommerce_account_menu_items', 'pooshe_dl_sub_menu_item', 20);
function pooshe_dl_sub_menu_item($items)
{
    $new_items = [];
    foreach ($items as $key => $label) {
        $new_items[$key] = $label;
        if ($key === 'downloads') {
            $new_items['download-subscription'] = 'اشتراک دانلود';
        }
    }
    return $new_items;
}

add_action('woocommerce_account_download-subscription_endpoint', 'pooshe_dl_sub_endpoint_content');
function pooshe_dl_sub_endpoint_content()
{
    if (!pooshe_dl_sub_is_enabled()) return;

    $user_id = get_current_user_id();
    $remaining = pooshe_dl_sub_get_remaining($user_id);

    $template = get_template_directory() . '/views/woocommerce/myaccount/download-subscription.php';
    if (file_exists($template)) {
        include $template;
    }
}

add_action('template_redirect', 'pooshe_dl_sub_handle_download');
function pooshe_dl_sub_handle_download()
{
    if (!pooshe_dl_sub_is_enabled()) return;

    if (empty($_GET['pooshe_sub_dl']) || empty($_GET['_wpnonce'])) return;

    $product_id = absint($_GET['pooshe_sub_dl']);
    if (!wp_verify_nonce($_GET['_wpnonce'], 'pooshe_sub_dl_' . $product_id)) {
        wp_die('خطای امنیتی. لطفا صفحه را بازخوانی کنید.');
    }

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_die('لطفا ابتدا وارد حساب کاربری خود شوید.');
    }

    if (wc_customer_bought_product('', $user_id, $product_id)) {
        wp_redirect(wc_get_account_endpoint_url('downloads'));
        exit;
    }

    $eligible = get_post_meta($product_id, '_pooshe_dl_eligible', true);
    if ($eligible === 'no') {
        wc_add_notice('این محصول قابل دانلود با اشتراک نیست.', 'error');
        wp_redirect(wc_get_account_endpoint_url('download-subscription'));
        exit;
    }

    $remaining = pooshe_dl_sub_get_remaining($user_id);
    if ($remaining === 0) {
        wc_add_notice('سقف دانلود اشتراک شما به پایان رسیده است. لطفا یک بسته جدید خریداری کنید.', 'error');
        wp_redirect(wc_get_account_endpoint_url('download-subscription'));
        exit;
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        wp_die('محصول مورد نظر یافت نشد.');
    }

    $downloads = $product->get_downloads();
    if (empty($downloads)) {
        wp_die('فایلی برای دانلود در این محصول وجود ندارد.');
    }

    if ($remaining !== -1) {
        update_user_meta($user_id, 'pooshe_dl_remaining', $remaining - 1);
    }

    $file = reset($downloads);
    $file_path = $file->get_file();

    pooshe_dl_sub_record_download($user_id, $product_id, $file->get_id());

    if (preg_match('/^https?:\/\//', $file_path)) {
        wp_redirect($file_path);
        exit;
    }

    if (file_exists($file_path)) {
        pooshe_dl_sub_stream_file($file_path, $file->get_name());
        exit;
    }

    $uploads = wp_get_upload_dir();
    $absolute = $uploads['basedir'] . '/' . ltrim(str_replace($uploads['basedir'], '', $file_path), '/');
    if (file_exists($absolute)) {
        pooshe_dl_sub_stream_file($absolute, $file->get_name());
        exit;
    }

    if (function_exists('wc_download_file')) {
        wc_download_file($file_path, $file->get_name());
        exit;
    }

    wp_die('فایل مورد نظر یافت نشد.');
}

function pooshe_dl_sub_stream_file($file_path, $file_name = '')
{
    if (!file_exists($file_path)) {
        wp_die('فایل یافت نشد.');
    }

    $file_name = $file_name ?: basename($file_path);

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header('Content-Length: ' . filesize($file_path));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    if (ob_get_level()) {
        ob_end_clean();
    }

    readfile($file_path);
    exit;
}

function pooshe_dl_sub_record_download($user_id, $product_id, $file_id)
{
    $history = get_user_meta($user_id, 'pooshe_dl_history', true);
    if (!is_array($history)) $history = [];

    $history[] = [
        'product_id' => $product_id,
        'file_id' => $file_id,
        'time' => current_time('timestamp'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ];

    if (count($history) > 100) {
        $history = array_slice($history, -100);
    }

    update_user_meta($user_id, 'pooshe_dl_history', $history);
}

add_action('woocommerce_account_dashboard', 'pooshe_dl_sub_dashboard', 5);
function pooshe_dl_sub_dashboard()
{
    if (!pooshe_dl_sub_is_enabled()) return;
    $remaining = pooshe_dl_sub_get_remaining();
    if ($remaining === 0) return;
?>
    <div class="pooshe-dl-sub-status">
        <h3><?php _e('وضعیت اشتراک دانلود', 'pooshe'); ?></h3>
        <div class="dl-sub-info">
            <?php if ($remaining === -1) : ?>
                <span class="dl-sub-count unlimited"><?php _e('اشتراک شما نامحدود است', 'pooshe'); ?></span>
            <?php else : ?>
                <span class="dl-sub-count"><?php _e('تعداد دانلود باقی‌مانده:', 'pooshe'); ?> <strong><?php echo number_format($remaining); ?></strong></span>
            <?php endif; ?>
            <a href="<?php echo esc_url(wc_get_account_endpoint_url('download-subscription')); ?>" class="btn btn-primary btn-sm"><?php _e('مشاهده جزئیات', 'pooshe'); ?></a>
        </div>
    </div>
<?php
}

add_action('wp_enqueue_scripts', 'pooshe_dl_sub_styles');
function pooshe_dl_sub_styles()
{
    if (!pooshe_dl_sub_is_enabled()) return;

    $css = '
    .pooshe-dl-sub-status { background: var(--pooshe-color-main, #ff9e22); color: #fff; padding: 20px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .pooshe-dl-sub-status h3 { margin: 0; font-size: 16px; color: #fff; }
    .pooshe-dl-sub-status .dl-sub-info { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
    .pooshe-dl-sub-status .dl-sub-count { font-size: 15px; }
    .pooshe-dl-sub-status .dl-sub-count.unlimited { font-weight: 700; }
    .pooshe-dl-sub-status .dl-sub-count strong { font-size: 20px; }
    .pooshe-dl-sub-status .btn { background: rgba(255,255,255,0.2); color: #fff; border: 2px solid rgba(255,255,255,0.5); padding: 6px 18px; border-radius: 8px; text-decoration: none; font-size: 13px; transition: all 0.2s; }
    .pooshe-dl-sub-status .btn:hover { background: #fff; color: var(--pooshe-color-main, #ff9e22); }
    .pooshe-dl-sub-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .pooshe-dl-sub-table th, .pooshe-dl-sub-table td { padding: 12px 15px; text-align: right; border-bottom: 1px solid #e9ecef; }
    .pooshe-dl-sub-table th { background: #f8f9fa; font-weight: 600; font-size: 13px; }
    .pooshe-dl-sub-table td { font-size: 14px; }
    .pooshe-dl-sub-table .btn-dl-sub { display: inline-block; padding: 6px 16px; background: #28a745; color: #fff; border-radius: 6px; text-decoration: none; font-size: 13px; transition: background 0.2s; }
    .pooshe-dl-sub-table .btn-dl-sub:hover { background: #218838; }
    .body.dark-mode .pooshe-dl-sub-table th { background: #1e293b; color: #e2e8f0; }
    .body.dark-mode .pooshe-dl-sub-table td { border-color: #2d3a50; color: #e2e8f0; }
    ';
    wp_add_inline_style('pooshe-main', $css);
}
