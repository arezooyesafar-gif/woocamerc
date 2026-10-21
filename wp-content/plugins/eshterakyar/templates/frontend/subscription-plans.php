<?php

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="eshterakyar-subscription-plans-page">
    <?php if (isset($show_header) && $show_header): ?>
        <div class="eshterakyar-plans-header">
            <h1 class="eshterakyar-plans-title"><?php esc_html_e('پلن‌ها و قیمت‌گذاری', 'eshterakyar'); ?></h1>
            <?php if ($active_subscription): ?>
                <div class="eshterakyar-current-subscription">
                    <p class="eshterakyar-current-subscription-message">
                        <?php
                        $days_left = Eshterakyar_Subscription::get_days_left($active_subscription->id);
                        if ($days_left > 0) {
                            printf(
                                esc_html__('اشتراک فعلی شما: %1$s | %2$d روز باقی مانده', 'eshterakyar'),
                                esc_html($active_subscription->plan_name),
                                absint($days_left)
                            );
                        } else {
                            esc_html_e('اشتراک شما به اتمام رسیده است', 'eshterakyar');
                        }
                        ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php 
    $terms_position = isset($terms_position) ? $terms_position : 'top';
    if (isset($show_terms) && $show_terms && !empty($terms_content) && $terms_position === 'top'): ?>
        <div class="eshterakyar-terms-section">
            <h2 class="eshterakyar-terms-title"><?php esc_html_e('قوانین و مقررات', 'eshterakyar'); ?></h2>
            <div class="eshterakyar-terms-content">
                <?php echo wp_kses_post(wpautop($terms_content)); ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (empty($plans)): ?>
        <div class="eshterakyar-no-plans">
            <p><?php esc_html_e('در حال حاضر پلن اشتراکی موجود نیست.', 'eshterakyar'); ?></p>
        </div>
    <?php else: ?>
        <div class="eshterakyar-plans-grid">
            <?php foreach ($plans as $index => $plan): ?>
                <?php
                $is_popular = false;
                $is_recommended = isset($plan->is_recommended) && (int) $plan->is_recommended === 1;
                $product = null;
                $product_exists = false;
                $product_purchasable = false;
                if ($plan->product_id) {
                    $product = wc_get_product($plan->product_id);
                    if ($product && $product->exists()) {
                        $product_exists = true;
                        $product_purchasable = $product->is_purchasable();
                    }
                }
                
                $price_toman = intval($plan->price);
                $price_formatted = number_format_i18n($price_toman, 0) . ' ' . __('تومان', 'eshterakyar');
                
                $has_sale_price = false;
                $sale_price_toman = 0;
                $sale_price_formatted = '';
                $discount_percentage = 0;
                if (isset($plan->sale_price) && $plan->sale_price > 0 && $plan->sale_price < $price_toman) {
                    $has_sale_price = true;
                    $sale_price_toman = intval($plan->sale_price);
                    $sale_price_formatted = number_format_i18n($sale_price_toman, 0) . ' ' . __('تومان', 'eshterakyar');
                    $discount_percentage = round((($price_toman - $sale_price_toman) / $price_toman) * 100);
                }
                
                $duration_text = '';
                if ($plan->duration_days >= 365) {
                    $years = floor($plan->duration_days / 365);
                    $duration_text = sprintf(
                        esc_html__('%d سال', 'eshterakyar'),
                        $years
                    );
                } elseif ($plan->duration_days >= 30) {
                    $months = floor($plan->duration_days / 30);
                    $duration_text = sprintf(
                        esc_html__('%d ماه', 'eshterakyar'),
                        $months
                    );
                } else {
                    $duration_text = sprintf(
                        esc_html__('%d روز', 'eshterakyar'),
                        $plan->duration_days
                    );
                }
                
                $features = array();
                
                if ($plan->duration_days > 0) {
                    if ($plan->duration_days >= 365) {
                        $years = floor($plan->duration_days / 365);
                        $features[] = sprintf(
                            esc_html__('%d سال', 'eshterakyar'),
                            $years
                        );
                    } elseif ($plan->duration_days >= 30) {
                        $months = floor($plan->duration_days / 30);
                        $features[] = sprintf(
                            esc_html__('%d ماه', 'eshterakyar'),
                            $months
                        );
                    } else {
                        $features[] = sprintf(
                            esc_html__('%d روز', 'eshterakyar'),
                            $plan->duration_days
                        );
                    }
                }
                
                $daily_limit = isset($plan->daily_limit) ? intval($plan->daily_limit) : 0;
                if ($daily_limit > 0) {
                    $features[] = sprintf(
                        /* translators: %s: Daily download limit (formatted) */
                        esc_html__('%s دانلود روزانه', 'eshterakyar'),
                        number_format_i18n($daily_limit)
                    );
                } else {
                    $features[] = esc_html__('دانلود نامحدود روزانه', 'eshterakyar');
                }
                
                $total_limit = isset($plan->total_limit) ? intval($plan->total_limit) : 0;
                if ($total_limit > 0) {
                    $features[] = sprintf(
                        /* translators: %s: Total download limit (formatted) */
                        esc_html__('%s دانلود کل', 'eshterakyar'),
                        number_format_i18n($total_limit)
                    );
                } else {
                    $features[] = esc_html__('دانلود نامحدود', 'eshterakyar');
                }
                
                if (!empty($plan->custom_features)) {
                    $custom_features_lines = explode("\n", $plan->custom_features);
                    foreach ($custom_features_lines as $custom_feature) {
                        $custom_feature = trim($custom_feature);
                        if (!empty($custom_feature)) {
                            $features[] = $custom_feature;
                        }
                    }
                }
                
                $has_this_plan = false;
                if ($active_subscription && $active_subscription->plan_id == $plan->id) {
                    $has_this_plan = true;
                }
                
                $has_custom_badge = !empty($plan->badge_text);
                $badge_color = isset($plan->badge_color) ? $plan->badge_color : 'orange';
                ?>
                
                <div class="eshterakyar-plan-card <?php echo $is_popular ? 'eshterakyar-plan-popular' : ''; ?> <?php echo $has_this_plan ? 'eshterakyar-plan-current' : ''; ?> <?php echo $is_recommended ? 'eshterakyar-plan-recommended' : ''; ?>">
                    <?php if ($is_recommended): ?>
                        <div class="eshterakyar-plan-badge eshterakyar-plan-badge-recommended" aria-hidden="false">
                            <span class="eshterakyar-plan-badge-star" aria-hidden="true">★</span>
                            <span><?php esc_html_e('پیشنهادی', 'eshterakyar'); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($has_custom_badge): ?>
                        <div class="eshterakyar-plan-badge eshterakyar-plan-badge-custom eshterakyar-plan-badge-<?php echo esc_attr($badge_color); ?>">
                            <?php echo esc_html($plan->badge_text); ?>
                        </div>
                    <?php elseif ($is_popular): ?>
                        <div class="eshterakyar-plan-badge"><?php esc_html_e('محبوب', 'eshterakyar'); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($has_this_plan): ?>
                        <div class="eshterakyar-plan-current-badge"><?php esc_html_e('اشتراک فعلی', 'eshterakyar'); ?></div>
                    <?php endif; ?>
                    
                    <div class="eshterakyar-plan-header">
                        <h3 class="eshterakyar-plan-name"><?php echo esc_html($plan->name); ?></h3>
                        <div class="eshterakyar-plan-price">
                            <?php if ($has_sale_price): ?>
                                <div class="eshterakyar-plan-price-wrapper">
                                    <div class="eshterakyar-plan-price-original">
                                        <span class="eshterakyar-plan-price-amount-original"><?php echo esc_html($price_formatted); ?></span>
                                    </div>
                                    <div class="eshterakyar-plan-price-sale">
                                        <span class="eshterakyar-plan-price-amount-sale"><?php echo esc_html($sale_price_formatted); ?></span>
                                        <?php if ($discount_percentage > 0): ?>
                                            <span class="eshterakyar-plan-discount-badge"><?php echo esc_html($discount_percentage); ?>% <?php esc_html_e('تخفیف', 'eshterakyar'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="eshterakyar-plan-price-amount"><?php echo esc_html($price_formatted); ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="eshterakyar-plan-duration">
                            <?php
                            printf(esc_html__('طول دوره: %s', 'eshterakyar'), esc_html($duration_text));
                            ?>
                        </p>
                    </div>
                    
                    <div class="eshterakyar-plan-description">
                        <?php if (!empty($plan->description)): ?>
                            <p><?php echo esc_html($plan->description); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <ul class="eshterakyar-plan-features">
                        <?php foreach ($features as $feature): ?>
                            <li class="eshterakyar-plan-feature">
                                <span class="eshterakyar-plan-feature-icon">✓</span>
                                <span class="eshterakyar-plan-feature-text"><?php echo esc_html($feature); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="eshterakyar-plan-footer">
                        <?php if ($has_this_plan): ?>
                            <button class="eshterakyar-plan-button eshterakyar-plan-button-current" disabled>
                                <?php esc_html_e('اشتراک فعلی شما', 'eshterakyar'); ?>
                            </button>
                        <?php elseif ($product_exists && $product_purchasable): ?>
                            <?php
                            $add_to_cart_url = add_query_arg(
                                array(
                                    'add-to-cart' => $product->get_id(),
                                ),
                                home_url()
                            );
                            ?>
                            <a href="<?php echo esc_url($add_to_cart_url); ?>" class="eshterakyar-plan-button eshterakyar-plan-button-primary" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                                <?php esc_html_e('خرید', 'eshterakyar'); ?>
                            </a>
                        <?php else: ?>
                            <button class="eshterakyar-plan-button eshterakyar-plan-button-disabled" disabled>
                                <?php if (!$plan->product_id): ?>
                                    <?php esc_html_e('محصول مرتبط تنظیم نشده است', 'eshterakyar'); ?>
                                <?php elseif (!$product_exists): ?>
                                    <?php esc_html_e('محصول یافت نشد', 'eshterakyar'); ?>
                                <?php else: ?>
                                    <?php esc_html_e('در دسترس نیست', 'eshterakyar'); ?>
                                <?php endif; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php 
    if (isset($show_terms) && $show_terms && !empty($terms_content) && $terms_position === 'bottom'): ?>
        <div class="eshterakyar-terms-section">
            <h2 class="eshterakyar-terms-title"><?php esc_html_e('قوانین و مقررات', 'eshterakyar'); ?></h2>
            <div class="eshterakyar-terms-content">
                <?php echo wp_kses_post(wpautop($terms_content)); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

