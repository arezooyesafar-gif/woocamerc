<?php

defined('ABSPATH') || exit;

$user_id = get_current_user_id();
$remaining = pooshe_dl_sub_get_remaining($user_id);
$total = intval(get_user_meta($user_id, 'pooshe_dl_total', true));
$history = get_user_meta($user_id, 'pooshe_dl_history', true);
if (!is_array($history)) $history = [];
?>

<div class="pooshe-dl-sub-page">
    <h2><?php _e('اشتراک دانلود', 'pooshe'); ?></h2>

    <div class="pooshe-dl-sub-status" style="margin-bottom:25px;">
        <h3><?php _e('وضعیت اشتراک شما', 'pooshe'); ?></h3>
        <div class="dl-sub-info">
            <?php if ($remaining === -1) : ?>
                <span class="dl-sub-count unlimited"><?php _e('اشتراک شما نامحدود است 🎉', 'pooshe'); ?></span>
            <?php else : ?>
                <span class="dl-sub-count"><?php _e('دانلود باقی‌مانده:', 'pooshe'); ?> <strong><?php echo number_format($remaining); ?></strong></span>
                <?php if ($total > 0) : ?>
                    <span class="dl-sub-total"><?php _e('مجموع خرید:', 'pooshe'); ?> <?php echo number_format($total); ?> <?php _e('دانلود', 'pooshe'); ?></span>
                <?php endif; ?>
            <?php endif; ?>
            <a href="<?php echo esc_url(wc_get_account_endpoint_url('downloads')); ?>" class="btn btn-primary btn-sm"><?php _e('دانلودهای خریداری شده', 'pooshe'); ?></a>
        </div>
    </div>

    <?php if ($remaining === 0) : ?>
        <div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
            <p><?php _e('شما اشتراک دانلود فعالی ندارید. برای خرید اشتراک، یکی از بسته‌های زیر را انتخاب کنید:', 'pooshe'); ?></p>
        </div>
    <?php endif; ?>

    <h3 style="margin-top:30px;"><?php _e('محصولات قابل دانلود با اشتراک', 'pooshe'); ?></h3>

    <?php
    $eligible_products = get_posts([
        'post_type' => 'product',
        'posts_per_page' => -1,
        'meta_query' => [
            ['key' => '_pooshe_dl_eligible', 'value' => 'yes'],
        ],
        'fields' => 'ids',
    ]);

    $downloaded_ids = [];
    foreach ($history as $h) {
        $downloaded_ids[] = $h['product_id'];
    }

    if (!empty($eligible_products)) : ?>
        <table class="pooshe-dl-sub-table shop_table">
            <thead>
                <tr>
                    <th><?php _e('محصول', 'pooshe'); ?></th>
                    <th><?php _e('قیمت', 'pooshe'); ?></th>
                    <th><?php _e('عملیات', 'pooshe'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eligible_products as $pid) :
                    $product = wc_get_product($pid);
                    if (!$product) continue;
                    if (wc_customer_bought_product('', $user_id, $pid)) continue;
                ?>
                    <tr>
                        <td>
                            <a href="<?php echo get_permalink($pid); ?>" target="_blank">
                                <?php echo $product->get_image('thumbnail', ['style' => 'width:40px;height:40px;border-radius:6px;margin-left:10px;vertical-align:middle;']); ?>
                                <?php echo $product->get_name(); ?>
                            </a>
                        </td>
                        <td><?php echo $product->get_price_html(); ?></td>
                        <td>
                            <?php if ($remaining > 0 || $remaining === -1) :
                                $dl_url = wp_nonce_url(
                                    add_query_arg('pooshe_sub_dl', $pid, home_url()),
                                    'pooshe_sub_dl_' . $pid
                                );
                            ?>
                                <a href="<?php echo esc_url($dl_url); ?>" class="btn-dl-sub"><?php _e('دانلود', 'pooshe'); ?></a>
                            <?php else : ?>
                                <span style="color:#999;"><?php _e('بدون اشتراک', 'pooshe'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php _e('هیچ محصولی برای دانلود با اشتراک وجود ندارد.', 'pooshe'); ?></p>
    <?php endif; ?>

    <h3 style="margin-top:30px;"><?php _e('تاریخچه دانلودهای اشتراک', 'pooshe'); ?></h3>
    <?php if (!empty($history)) : ?>
        <table class="pooshe-dl-sub-table shop_table">
            <thead>
                <tr>
                    <th><?php _e('محصول', 'pooshe'); ?></th>
                    <th><?php _e('تاریخ', 'pooshe'); ?></th>
                    <th><?php _e('آیپی', 'pooshe'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $history = array_reverse($history);
                foreach ($history as $h) :
                    $product = wc_get_product($h['product_id']);
                ?>
                    <tr>
                        <td><?php echo $product ? $product->get_name() : '#' . $h['product_id']; ?></td>
                        <td><?php echo date_i18n('Y/m/d - H:i', $h['time']); ?></td>
                        <td><?php echo esc_html($h['ip']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php _e('هنوز هیچ دانلودی با اشتراک انجام نداده‌اید.', 'pooshe'); ?></p>
    <?php endif; ?>
</div>
