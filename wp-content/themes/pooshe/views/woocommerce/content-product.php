<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Check if the product is a valid WooCommerce product and ensure its visibility before proceeding.
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}
if (!isset($style)) {
    $style = 'default';
}

$cardStyle = 'product-card--';
if (isset($style)) {
    $cardStyle .= $style;
} else {
    $cardStyle .= 'default';
}

if (isset($style) && $style === 'modern') {
    remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
    add_action('woocommerce_after_shop_loop_item', 'pooshe_card_category', 5);
}
if (!($product instanceof WC_Product)) {
    return;
}
if (isset($style) && $style == 'default' || $style == 'modern') {
    ?>
    <li <?php wc_product_class('product-card ' . $cardStyle, $product); ?>>
        <?php
        /**
         * Hook: woocommerce_before_shop_loop_item.
         *
         * @hooked woocommerce_template_loop_product_link_open - 10
         */
        do_action('woocommerce_before_shop_loop_item');

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
        version_badge();
	do_action( 'woocommerce_before_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</li>

<?php }
if (isset($style) && $style === 'style-3') { ?>
    <li <?php wc_product_class('product-card ' . $cardStyle, $product); ?>>
        <figure class="thumbnail">
            <a href="<?= $product->get_permalink() ?>">
                <?= $product->get_image() ?>
            </a>
            <?php pooshe_product_badges(); ?>
        </figure>
        <div class="card-content">
            <a class="product-card-title" href="<?= $product->get_permalink() ?>">
                <h5 class="title" aria-expanded="false" tabindex="0"> <?= $product->get_title() ?> </h5>
            </a>
            <div class="delivery-time">
                <?php
                $delivery_time = get_post_meta($product->get_id(), 'pooshe_product_delivery_time', true);
                if ((int) $delivery_time > 0) {
                    ?>
                    <span class="product-delivery-time">
                  زمان تحویل از 1 الی
                      <b><?php echo $delivery_time; ?></b>
                    روزکاری
                </span>
                <?php }
                if ((int) $delivery_time === 0) { ?>
                    <span class="product-delivery-time">
                    آماده تحویل
                </span>
                <?php } elseif (empty($delivery_time)) { ?>
                    <span class="product-delivery-time">
                    زمان تحویل در دسترس نیست
                </span>
                <?php } ?>
            </div>
            <span class="price-section">
                <?= $product->get_price_html() ?>
            </span>
            <?php
            if (pooshe_opt(SHOP_OPTS, 'card-add', true)) {
                echo '<div class="addtocart">';
                do_action('pooshe_card_addtocart');
                echo '</div>';
            }
            ?>
        </div>
    </li>
<?php }
if (isset($style) && $style === 'style-4') { ?>
    <li <?php wc_product_class('product-card ' . $cardStyle, $product); ?>>
        <figure class="thumbnail">
            <a href="<?= $product->get_permalink() ?>">
                <?= $product->get_image() ?>
            </a>
            <?php pooshe_product_badges(); ?>
        </figure>
        <div class="card-content">
            <a class="product-card-title" href="<?= $product->get_permalink() ?>">
                <h5 class="title" aria-expanded="false" tabindex="0"> <?= $product->get_title() ?> </h5>
            </a>

            <p class="short-description">
                <?php
                $product_slogan = get_post_meta($product->get_id(), 'pooshe_product_slogan', true);
                echo $product_slogan;
                ?>
            </p>

            <span class="price-section">
                <?= $product->get_price_html() ?>
            </span>
            <div class="inner">
                <?php
                echo '<div class="cardFooter-right">';
                if ( pooshe_opt(SHOP_OPTS, 'card-preview', true) ) {
                    echo pooshe_product_preview();
                }
                if ( pooshe_opt(SHOP_OPTS, 'card-bookmark', true) ) {
                    echo pooshe_product_favourite();
                }
                echo '</div>';
                if (pooshe_opt(SHOP_OPTS, 'card-add', true)) {
                    echo '<div class="addtocart">';
                    do_action('pooshe_card_addtocart');
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </li>
<?php }
if (isset($style) && $style === 'style-5' || $style === 'style-6') { ?>
    <li <?php wc_product_class('product-card ' . $cardStyle, $product); ?>>
        <figure class="thumbnail">
            <a href="<?= $product->get_permalink() ?>">
                <?= $product->get_image() ?>
            </a>
            <?php pooshe_product_badges(); ?>
        </figure>
        <div class="card-content">
            <div class="card-cat">
                <?php
                // دریافت دسته‌بندی‌های محصول
                $terms = get_the_terms($product->get_id(), 'product_cat');
                if ($terms && !is_wp_error($terms)) {
                    // مرتب‌سازی دسته‌بندی‌ها بر اساس ترتیب
                    usort($terms, function($a, $b) {
                        return $a->term_order - $b->term_order;
                    });
                    // چاپ نام دسته‌بندی اصلی (اولین دسته‌بندی) با لینک
                    echo '<a href="' . esc_url(get_term_link($terms[0])) . '" class="category">' . esc_html($terms[0]->name) . '</a>';
                }
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
                if ($regular_price && $sale_price) {
                    // محاسبه درصد تخفیف
                    $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                    // چاپ درصد تخفیف
                    echo '<span class="discount">' . esc_html($discount_percentage) . '%</span>';
                }
                ?>
            </div>
            <div class="card-title-price">
                <a class="product-card-title" href="<?= $product->get_permalink() ?>">
                    <h5 class="title" aria-expanded="false" tabindex="0"> <?= $product->get_title() ?> </h5>
                </a>
                <span class="price-section">
                <?= $product->get_price_html() ?>
            </span>
            </div>
            <div class="card-button">
                <?php
                if ( pooshe_opt(SHOP_OPTS, 'card-preview', true) ) {
                    echo pooshe_product_preview();
                }
                if (pooshe_opt(SHOP_OPTS, 'card-add', true)) {
                    echo '<div class="addtocart">';
                    do_action('pooshe_card_addtocart');
                    echo '</div>';
                }
                ?>
            </div>

        </div>
    </li>
<?php } ?>

