<?php

defined('ABSPATH') || exit;

function pooshe_tiered_is_enabled()
{
    return function_exists('pooshe_opt') && defined('STYLE_OPTS') && pooshe_opt(STYLE_OPTS, 'tiered_discount', false);
}

function pooshe_parse_tiered_rules($raw)
{
    $rules = [];
    $lines = explode("\n", $raw);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        $parts = explode(':', $line);
        if (count($parts) === 2) {
            $qty = intval(trim($parts[0]));
            $pct = floatval(trim($parts[1]));
            if ($qty > 0 && $pct > 0) {
                if (!isset($rules[$qty]) || $pct > $rules[$qty]) {
                    $rules[$qty] = $pct;
                }
            }
        }
    }
    ksort($rules);
    return $rules;
}

add_action('woocommerce_cart_calculate_fees', 'pooshe_apply_tiered_discount');
function pooshe_apply_tiered_discount($cart)
{
    if (is_admin() && !defined('DOING_AJAX')) return;
    if (!pooshe_tiered_is_enabled()) return;

    $rules_raw = pooshe_opt(STYLE_OPTS, 'tiered_discount_rules', "2:5\n3:10\n5:15");
    $rules = pooshe_parse_tiered_rules($rules_raw);
    if (empty($rules)) return;

    $total_qty = $cart->get_cart_contents_count();
    if ($total_qty < min(array_keys($rules))) return;

    $discount_pct = 0;
    $matched_qty = 0;
    foreach ($rules as $qty => $pct) {
        if ($total_qty >= $qty) {
            $discount_pct = $pct;
            $matched_qty = $qty;
        }
    }

    if ($discount_pct <= 0) return;

    $subtotal = $cart->get_subtotal() - $cart->get_discount_total();
    if ($subtotal <= 0) return;

    $discount_amount = $subtotal * ($discount_pct / 100);

    $cart->add_fee(
        sprintf(__('تخفیف پله‌ای %s‌%% (خرید %d محصول)', 'pooshe'), number_format($discount_pct), $matched_qty),
        -$discount_amount,
        false
    );
}

add_action('woocommerce_before_cart_table', 'pooshe_show_tiered_discount_notice');
function pooshe_show_tiered_discount_notice()
{
    if (!pooshe_tiered_is_enabled()) return;

    $raw = pooshe_opt(STYLE_OPTS, 'tiered_discount_rules', "2:5\n3:10\n5:15");
    $rules = pooshe_parse_tiered_rules($raw);
    if (empty($rules)) return;

    $qty = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>
    <div class="pooshe-tiered-notice">
        <div class="pooshe-tiered-header">
            <i class="fas fa-percent"></i>
            <span><?php _e('تخفیف پله‌ای', 'pooshe'); ?></span>
        </div>
        <div class="pooshe-tiered-body">
            <?php foreach ($rules as $need_qty => $pct) : ?>
                <div class="pooshe-tiered-tier <?php echo $qty >= $need_qty ? 'reached' : ''; ?>">
                    <span class="tier-icon"><?php echo $qty >= $need_qty ? '✅' : '🔒'; ?></span>
                    <span class="tier-label"><?php printf(__('خرید %d محصول', 'pooshe'), $need_qty); ?></span>
                    <span class="tier-badge"><?php printf(__('%s‌%% تخفیف', 'pooshe'), number_format($pct)); ?></span>
                </div>
            <?php endforeach; ?>
            <p class="pooshe-tiered-cart-qty">
                <?php printf(__('تعداد محصولات سبد خرید: %d عدد', 'pooshe'), $qty); ?>
            </p>
        </div>
    </div>
<?php
}

add_action('wp_enqueue_scripts', 'pooshe_tiered_styles');
function pooshe_tiered_styles()
{
    if (!pooshe_tiered_is_enabled()) return;

    $css = '
    .pooshe-tiered-notice { background:#fefce8; border:1px solid #fde68a; border-radius:12px; margin-bottom:20px; overflow:hidden; direction:rtl }
    .pooshe-tiered-header { background:#f59e0b; color:#fff; padding:10px 16px; font-size:14px; font-weight:600; display:flex; align-items:center; gap:8px }
    .pooshe-tiered-header i { font-size:16px }
    .pooshe-tiered-body { padding:14px 16px }
    .pooshe-tiered-tier { display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid #fde68a; font-size:13px }
    .pooshe-tiered-tier:last-child { border-bottom:none }
    .pooshe-tiered-tier .tier-icon { font-size:16px; width:24px; text-align:center }
    .pooshe-tiered-tier .tier-label { flex:1; color:#92400e }
    .pooshe-tiered-tier .tier-badge { background:#f59e0b; color:#fff; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600 }
    .pooshe-tiered-tier.reached .tier-label { color:#065f46; font-weight:600 }
    .pooshe-tiered-tier.reached .tier-badge { background:#10b981 }
    .pooshe-tiered-cart-qty { margin:10px 0 0; font-size:12px; color:#92400e; text-align:left }
    .body.dark-mode .pooshe-tiered-notice { background:#1e293b; border-color:#f59e0b }
    .body.dark-mode .pooshe-tiered-tier .tier-label { color:#fbbf24 }
    .body.dark-mode .pooshe-tiered-tier.reached .tier-label { color:#6ee7b7 }
    .body.dark-mode .pooshe-tiered-cart-qty { color:#fbbf24 }
    ';
    wp_add_inline_style('pooshe-main', $css);
}
