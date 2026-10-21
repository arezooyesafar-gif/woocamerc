<?php

defined('ABSPATH') || exit;

function pooshe_affiliate_is_enabled()
{
    return function_exists('pooshe_opt') && defined('STYLE_OPTS') && pooshe_opt(STYLE_OPTS, 'affiliate_enabled', false);
}

function pooshe_affiliate_get_code($user_id = 0)
{
    if (!$user_id) $user_id = get_current_user_id();
    if (!$user_id) return '';

    $code = get_user_meta($user_id, 'pooshe_affiliate_code', true);
    if (empty($code)) {
        $code = 'POO' . $user_id . strtoupper(substr(md5($user_id . wp_generate_password(8, false) . time()), 0, 6));
        update_user_meta($user_id, 'pooshe_affiliate_code', $code);
    }
    return $code;
}

function pooshe_affiliate_get_link($user_id = 0)
{
    $code = pooshe_affiliate_get_code($user_id);
    if (empty($code)) return '';
    return add_query_arg('ref', $code, wc_get_page_permalink('shop'));
}

function pooshe_affiliate_get_commission_rate()
{
    $type = pooshe_opt(STYLE_OPTS, 'affiliate_commission_type', 'percentage');
    $value = floatval(pooshe_opt(STYLE_OPTS, 'affiliate_commission_value', 10));
    return compact('type', 'value');
}

add_action('init', 'pooshe_affiliate_track_click');
function pooshe_affiliate_track_click()
{
    if (!pooshe_affiliate_is_enabled()) return;
    if (is_admin()) return;

    if (isset($_GET['ref']) && !empty($_GET['ref'])) {
        $code = sanitize_text_field($_GET['ref']);

        $days = max(1, intval(pooshe_opt(STYLE_OPTS, 'affiliate_cookie_days', 30)));
        setcookie('pooshe_affiliate_ref', $code, time() + ($days * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN, false, true);

        $users = get_users([
            'meta_key' => 'pooshe_affiliate_code',
            'meta_value' => $code,
            'fields' => 'ID',
            'number' => 1,
        ]);
        if (!empty($users)) {
            $clicks = intval(get_user_meta($users[0], 'pooshe_affiliate_clicks', true));
            update_user_meta($users[0], 'pooshe_affiliate_clicks', $clicks + 1);
        }
    }
}

add_action('init', 'pooshe_affiliate_add_endpoint');
function pooshe_affiliate_add_endpoint()
{
    if (!pooshe_affiliate_is_enabled()) return;
    add_rewrite_endpoint('affiliate-dashboard', EP_ROOT | EP_PAGES);
}

add_filter('query_vars', 'pooshe_affiliate_query_vars', 0);
function pooshe_affiliate_query_vars($vars)
{
    if (!pooshe_affiliate_is_enabled()) return $vars;
    $vars[] = 'affiliate-dashboard';
    return $vars;
}

add_filter('woocommerce_account_menu_items', 'pooshe_affiliate_menu_item', 30);
function pooshe_affiliate_menu_item($items)
{
    if (!pooshe_affiliate_is_enabled()) return $items;

    $label = pooshe_opt(STYLE_OPTS, 'affiliate_menu_label', '');
    if (empty($label)) $label = 'همکاری در فروش';

    $new_items = [];
    $inserted = false;
    foreach ($items as $key => $val) {
        if ($key === 'edit-account' && !$inserted) {
            $new_items['affiliate-dashboard'] = $label;
            $inserted = true;
        }
        $new_items[$key] = $val;
    }
    if (!$inserted) {
        $new_items['affiliate-dashboard'] = $label;
    }
    return $new_items;
}

add_action('woocommerce_account_affiliate-dashboard_endpoint', 'pooshe_affiliate_content');
function pooshe_affiliate_content()
{
    $user_id = get_current_user_id();
    if (!$user_id) return;

    $code = pooshe_affiliate_get_code($user_id);
    $link = pooshe_affiliate_get_link($user_id);
    $rate = pooshe_affiliate_get_commission_rate();
    $earnings = get_user_meta($user_id, 'pooshe_affiliate_earnings', true) ?: [];
    $paid = floatval(get_user_meta($user_id, 'pooshe_affiliate_paid', true));
    $clicks = intval(get_user_meta($user_id, 'pooshe_affiliate_clicks', true));
    $conversions = intval(get_user_meta($user_id, 'pooshe_affiliate_conversions', true));

    $total_pending = 0;
    $total_approved = 0;
    foreach ($earnings as $e) {
        if (($e['status'] ?? 'pending') === 'pending') {
            $total_pending += floatval($e['amount']);
        } else {
            $total_approved += floatval($e['amount']);
        }
    }

    $currency = get_woocommerce_currency_symbol();
?>
    <div class="pooshe-aff-wrap">
        <h2><?php _e('همکاری در فروش', 'pooshe'); ?></h2>

        <div class="pooshe-aff-stats">
            <div class="pooshe-aff-stat">
                <span class="pooshe-aff-stat-val"><?php echo number_format($clicks); ?></span>
                <span class="pooshe-aff-stat-lbl"><?php _e('کلیک', 'pooshe'); ?></span>
            </div>
            <div class="pooshe-aff-stat">
                <span class="pooshe-aff-stat-val"><?php echo number_format($conversions); ?></span>
                <span class="pooshe-aff-stat-lbl"><?php _e('فروش', 'pooshe'); ?></span>
            </div>
            <div class="pooshe-aff-stat">
                <span class="pooshe-aff-stat-val"><?php echo $currency . number_format($total_pending); ?></span>
                <span class="pooshe-aff-stat-lbl"><?php _e('در انتظار', 'pooshe'); ?></span>
            </div>
            <div class="pooshe-aff-stat">
                <span class="pooshe-aff-stat-val"><?php echo $currency . number_format($total_approved + $paid); ?></span>
                <span class="pooshe-aff-stat-lbl"><?php _e('تأیید شده', 'pooshe'); ?></span>
            </div>
        </div>

        <div class="pooshe-aff-link-box">
            <label><?php _e('لینک اختصاصی شما:', 'pooshe'); ?></label>
            <div class="pooshe-aff-link-row">
                <input type="text" class="pooshe-aff-link-input" value="<?php echo esc_url($link); ?>" readonly
                    onclick="this.select()">
                <button class="pooshe-aff-copy-btn"
                    onclick="
                    navigator.clipboard.writeText('<?php echo esc_js($link); ?>');
                    this.textContent='کپی شد ✓';
                    setTimeout(()=>this.textContent='کپی 📋',1200);
                    ">کپی 📋</button>
            </div>
            <p class="pooshe-aff-rate-info">
                <?php
                if ($rate['type'] === 'percentage') {
                    printf(__('نرخ کمیسیون: %s‌%% از مبلغ هر سفارش', 'pooshe'), number_format($rate['value']));
                } else {
                    printf(__('کمیسیون ثابت: %s%s به ازای هر سفارش', 'pooshe'), $currency, number_format($rate['value']));
                }
                ?>
            </p>
        </div>

        <?php if (!empty($earnings)) : ?>
            <h3><?php _e('تاریخچه کمیسیون‌ها', 'pooshe'); ?></h3>
            <table class="pooshe-aff-table">
                <thead>
                    <tr>
                        <th><?php _e('سفارش', 'pooshe'); ?></th>
                        <th><?php _e('مبلغ سفارش', 'pooshe'); ?></th>
                        <th><?php _e('کمیسیون', 'pooshe'); ?></th>
                        <th><?php _e('وضعیت', 'pooshe'); ?></th>
                        <th><?php _e('تاریخ', 'pooshe'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($earnings) as $e) : ?>
                        <tr>
                            <td>#<?php echo intval($e['order_id']); ?></td>
                            <td><?php echo $currency . number_format(floatval($e['order_total'])); ?></td>
                            <td><strong><?php echo $currency . number_format(floatval($e['amount'])); ?></strong></td>
                            <td>
                                <span class="pooshe-aff-status pooshe-aff-status-<?php echo esc_attr($e['status'] ?? 'pending'); ?>">
                                    <?php echo ($e['status'] ?? 'pending') === 'pending' ? 'در انتظار' : 'پرداخت شده'; ?>
                                </span>
                            </td>
                            <td><?php echo date_i18n('Y/m/d', strtotime($e['date'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php
}

add_action('woocommerce_order_status_completed', 'pooshe_affiliate_process_commission', 20, 1);
function pooshe_affiliate_process_commission($order_id)
{
    if (!pooshe_affiliate_is_enabled()) return;

    $order = wc_get_order($order_id);
    if (!$order) return;

    $affiliate_code = get_post_meta($order_id, '_pooshe_affiliate_code', true);
    if (empty($affiliate_code)) {
        $cookie = $_COOKIE['pooshe_affiliate_ref'] ?? '';
        if (empty($cookie)) return;
        $affiliate_code = sanitize_text_field($cookie);
    }

    $users = get_users([
        'meta_key' => 'pooshe_affiliate_code',
        'meta_value' => $affiliate_code,
        'fields' => 'ID',
        'number' => 1,
    ]);
    if (empty($users)) return;

    $affiliate_id = $users[0];

    if (intval($affiliate_id) === intval($order->get_user_id())) return;

    if (get_post_meta($order_id, '_pooshe_affiliate_processed', true) === 'yes') return;

    $rate = pooshe_affiliate_get_commission_rate();
    $order_total = $order->get_total();

    if ($rate['type'] === 'percentage') {
        $commission = $order_total * ($rate['value'] / 100);
    } else {
        $commission = $rate['value'];
    }

    $min_order = floatval(pooshe_opt(STYLE_OPTS, 'affiliate_min_order', 0));
    if ($min_order > 0 && $order_total < $min_order) return;

    update_post_meta($order_id, '_pooshe_affiliate_code', $affiliate_code);
    update_post_meta($order_id, '_pooshe_affiliate_user_id', $affiliate_id);
    update_post_meta($order_id, '_pooshe_affiliate_commission', $commission);
    update_post_meta($order_id, '_pooshe_affiliate_processed', 'yes');

    $earnings = get_user_meta($affiliate_id, 'pooshe_affiliate_earnings', true) ?: [];
    $earnings[] = [
        'order_id' => $order_id,
        'order_total' => $order_total,
        'amount' => $commission,
        'status' => 'pending',
        'date' => current_time('mysql'),
    ];
    update_user_meta($affiliate_id, 'pooshe_affiliate_earnings', $earnings);

    $conversions = intval(get_user_meta($affiliate_id, 'pooshe_affiliate_conversions', true));
    update_user_meta($affiliate_id, 'pooshe_affiliate_conversions', $conversions + 1);
}

add_action('woocommerce_checkout_update_order_meta', 'pooshe_affiliate_save_order_ref', 10, 1);
function pooshe_affiliate_save_order_ref($order_id)
{
    if (!pooshe_affiliate_is_enabled()) return;
    if (empty($_COOKIE['pooshe_affiliate_ref'])) return;

    $code = sanitize_text_field($_COOKIE['pooshe_affiliate_ref']);
    update_post_meta($order_id, '_pooshe_affiliate_code', $code);
}

add_action('show_user_profile', 'pooshe_affiliate_admin_user_profile');
add_action('edit_user_profile', 'pooshe_affiliate_admin_user_profile');
function pooshe_affiliate_admin_user_profile($user)
{
    if (!pooshe_affiliate_is_enabled()) return;
    if (!current_user_can('manage_woocommerce')) return;

    $code = pooshe_affiliate_get_code($user->ID);
    $link = pooshe_affiliate_get_link($user->ID);
    $earnings = get_user_meta($user->ID, 'pooshe_affiliate_earnings', true) ?: [];
    $clicks = intval(get_user_meta($user->ID, 'pooshe_affiliate_clicks', true));
    $conversions = intval(get_user_meta($user->ID, 'pooshe_affiliate_conversions', true));
    $paid = floatval(get_user_meta($user->ID, 'pooshe_affiliate_paid', true));
    $currency = get_woocommerce_currency_symbol();

    $total_pending = 0;
    foreach ($earnings as $e) {
        if (($e['status'] ?? 'pending') === 'pending') {
            $total_pending += floatval($e['amount']);
        }
    }
?>
    <h3>سیستم همکاری در فروش</h3>
    <table class="form-table">
        <tr>
            <th>کد معرف</th>
            <td><code><?php echo esc_html($code); ?></code></td>
        </tr>
        <tr>
            <th>لینک معرف</th>
            <td><input type="text" value="<?php echo esc_url($link); ?>" readonly style="width:100%;direction:ltr"></td>
        </tr>
        <tr>
            <th>کلیک‌ها / فروش</th>
            <td><?php echo number_format($clicks); ?> کلیک — <?php echo number_format($conversions); ?> فروش</td>
        </tr>
        <tr>
            <th>در انتظار پرداخت</th>
            <td><strong><?php echo $currency . number_format($total_pending); ?></strong></td>
        </tr>
        <tr>
            <th>پرداخت شده</th>
            <td><?php echo $currency . number_format($paid); ?></td>
        </tr>
    </table>
<?php
}

add_action('add_meta_boxes', 'pooshe_affiliate_order_meta_box');
function pooshe_affiliate_order_meta_box()
{
    if (!pooshe_affiliate_is_enabled()) return;
    add_meta_box(
        'pooshe_affiliate_order_meta',
        'همکاری در فروش',
        'pooshe_affiliate_order_meta_box_cb',
        'shop_order',
        'side',
        'default'
    );
}

function pooshe_affiliate_order_meta_box_cb($post)
{
    $code = get_post_meta($post->ID, '_pooshe_affiliate_code', true);
    if (empty($code)) {
        echo '<p>هیچ رفرالی برای این سفارش ثبت نشده.</p>';
        return;
    }

    $affiliate_id = get_post_meta($post->ID, '_pooshe_affiliate_user_id', true);
    $commission = get_post_meta($post->ID, '_pooshe_affiliate_commission', true);
    $currency = get_woocommerce_currency_symbol();

    $user = $affiliate_id ? get_user_by('ID', $affiliate_id) : null;

    echo '<p><strong>کد معرف:</strong> ' . esc_html($code) . '</p>';
    if ($user) {
        echo '<p><strong>معرف:</strong> <a href="' . get_edit_user_link($affiliate_id) . '">' . esc_html($user->display_name) . '</a></p>';
    }
    if ($commission) {
        echo '<p><strong>کمیسیون:</strong> ' . $currency . number_format(floatval($commission)) . '</p>';
    }
}

add_action('wp_enqueue_scripts', 'pooshe_affiliate_styles');
function pooshe_affiliate_styles()
{
    if (!pooshe_affiliate_is_enabled()) return;

    $css = '
    .pooshe-aff-wrap { max-width:100% }
    .pooshe-aff-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(120px,1fr)); gap:14px; margin:20px 0 }
    .pooshe-aff-stat { background:#fff; border:1px solid #e9ecef; border-radius:12px; padding:18px; text-align:center }
    .pooshe-aff-stat-val { display:block; font-size:22px; font-weight:700; color:var(--pooshe-color-main,#ff9e22) }
    .pooshe-aff-stat-lbl { display:block; font-size:12px; color:#666; margin-top:4px }
    .pooshe-aff-link-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px; margin:20px 0 }
    .pooshe-aff-link-box label { display:block; font-weight:600; margin-bottom:8px; font-size:14px }
    .pooshe-aff-link-row { display:flex; gap:8px }
    .pooshe-aff-link-input { flex:1; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; direction:ltr; background:#fff }
    .pooshe-aff-copy-btn { background:var(--pooshe-color-main,#ff9e22); color:#fff; border:none; padding:9px 16px; border-radius:8px; cursor:pointer; font-size:13px; white-space:nowrap }
    .pooshe-aff-copy-btn:hover { opacity:.85 }
    .pooshe-aff-rate-info { font-size:13px; color:#666; margin:10px 0 0 }
    .pooshe-aff-table { width:100%; border-collapse:collapse; font-size:14px; margin-top:10px }
    .pooshe-aff-table th,.pooshe-aff-table td { padding:10px 12px; text-align:right; border-bottom:1px solid #e9ecef }
    .pooshe-aff-table th { background:#f8f9fa; font-weight:600 }
    .pooshe-aff-status { display:inline-block; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:600 }
    .pooshe-aff-status-pending { background:#fef3c7; color:#92400e }
    .pooshe-aff-status-paid { background:#d1fae5; color:#065f46 }
    .body.dark-mode .pooshe-aff-stat { background:#1e293b; border-color:#2d3a50 }
    .body.dark-mode .pooshe-aff-stat-lbl { color:#94a3b8 }
    .body.dark-mode .pooshe-aff-link-box { background:#1e293b; border-color:#2d3a50 }
    .body.dark-mode .pooshe-aff-link-input { background:#0f172a; border-color:#334155; color:#e2e8f0 }
    .body.dark-mode .pooshe-aff-rate-info { color:#94a3b8 }
    .body.dark-mode .pooshe-aff-table th { background:#1e293b; color:#e2e8f0 }
    .body.dark-mode .pooshe-aff-table td,.body.dark-mode .pooshe-aff-table th { border-color:#2d3a50 }
    ';
    wp_add_inline_style('pooshe-main', $css);
}
