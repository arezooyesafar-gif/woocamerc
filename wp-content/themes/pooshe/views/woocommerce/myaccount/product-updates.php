<?php
defined('ABSPATH') || exit;
?>

<div class="pooshe-product-updates">
    <h2 class="section-heading">به‌روزرسانی محصولات</h2>

    <?php if (empty($products)) : ?>
        <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info">
            <p>همه محصولات خریداری شده شما به‌روز هستند. در صورت انتشار نسخه جدید، از طریق ایمیل به شما اطلاع داده خواهد شد.</p>
        </div>
    <?php else : ?>
        <p class="woocommerce-info">
            محصولات زیر به‌روزرسانی شده‌اند. می‌توانید آخرین نسخه را دانلود کنید.
        </p>

        <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive">
            <thead>
                <tr>
                    <th>محصول</th>
                    <th>تاریخ به‌روزرسانی</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) :
                    $notified_at = get_post_meta($product->get_id(), '_pooshe_new_version_notified_at', true);
                    ?>
                    <tr>
                        <td data-title="محصول">
                            <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                <?php echo esc_html($product->get_name()); ?>
                            </a>
                        </td>
                        <td data-title="تاریخ به‌روزرسانی">
                            <?php echo $notified_at ? pooshe_jdate('Y/m/d', strtotime($notified_at)) : '—'; ?>
                        </td>
                        <td data-title="عملیات">
                            <a href="<?php echo esc_url($product->get_permalink()); ?>"
                               class="woocommerce-button button">
                                مشاهده و دانلود
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
    .pooshe-updates-badge {
        display: inline-block;
        background: #ff9e22;
        color: #fff;
        font-size: 11px;
        padding: 1px 7px;
        border-radius: 10px;
        margin-right: 5px;
        vertical-align: middle;
        line-height: 18px;
    }
    .pooshe-product-updates .section-heading {
        font-size: 20px;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #ff9e22;
    }
</style>
