<?php
/**
 * Shortcodes reference tab — card layout with copyable code blocks.
 *
 * @package Eshterakyar
 */

if (!defined('ABSPATH')) {
    exit;
}

$eshterakyar_shortcode_cards = array(
    array(
        'tag' => 'user_subscriptions',
        'desc' => __('نمایش لیست تمام اشتراک‌های کاربر (فعال، منقضی، لغو شده)', 'eshterakyar'),
        'params' => array(
            'status' => 'all | active | expired | cancelled',
            'show_expired' => 'yes | no',
        ),
        'examples' => array(
            '[user_subscriptions]',
            '[user_subscriptions status="active" show_expired="yes"]',
        ),
    ),
    array(
        'tag' => 'subscription_downloads',
        'desc' => __('نمایش لیست محصولات دانلود شده توسط کاربر در تمام دوره‌های اشتراک', 'eshterakyar'),
        'params' => array(
            'limit' => __('تعداد نمایش (پیش‌فرض: 20)', 'eshterakyar'),
        ),
        'examples' => array(
            '[subscription_downloads]',
            '[subscription_downloads limit="10"]',
        ),
    ),
    array(
        'tag' => 'subscription_days_left',
        'desc' => __('نمایش تعداد روزهای باقی‌مانده تا انقضای اشتراک فعال', 'eshterakyar'),
        'params' => array(
            'format' => 'days | full',
        ),
        'examples' => array(
            '[subscription_days_left]',
            '[subscription_days_left format="full"]',
        ),
    ),
    array(
        'tag' => 'subscription_expired',
        'desc' => __('نمایش پیام هشدار در صورت عدم وجود اشتراک فعال', 'eshterakyar'),
        'params' => array(
            'message' => __('پیام سفارشی', 'eshterakyar'),
        ),
        'examples' => array(
            '[subscription_expired]',
            '[subscription_expired message="اشتراک شما منقضی شده است"]',
        ),
    ),
    array(
        'tag' => 'product_download_status',
        'desc' => __('وضعیت دانلود محصول و دکمه دانلود — مناسب صفحه محصول', 'eshterakyar'),
        'params' => array(
            'product_id' => __('شناسه محصول (اختیاری در صفحه محصول)', 'eshterakyar'),
        ),
        'examples' => array(
            '[product_download_status]',
            '[product_download_status product_id="123"]',
        ),
    ),
    array(
        'tag' => 'eshterakyar_product_cta',
        'desc' => __('دکمه حرفه‌ای صفحه محصول: بدون اشتراک (قیمت+سبد / خرید اشتراک / هر دو) و با اشتراک فعال «دانلود رایگان با اشتراک» + لینک دانلود + نوار سهمیه روزانه', 'eshterakyar'),
        'params' => array(
            'product_id' => __('شناسه محصول (اختیاری در صفحه محصول)', 'eshterakyar'),
            'no_sub_mode' => 'cart | plans | both — ' . __('الف=سبد، ب=پلن‌ها، ج=هر دو (پیش‌فرض: both)', 'eshterakyar'),
            'show_price' => 'yes | no — ' . __('نمایش قیمت ووکامرس در حالت cart/both', 'eshterakyar'),
            'show_quantity' => 'yes | no — ' . __('نمایش فیلد تعداد (محصول ساده)', 'eshterakyar'),
            'show_note' => 'yes | no — ' . __('پیام راهنما زیر دکمه‌ها در حالت بدون اشتراک', 'eshterakyar'),
            'show_daily_bar' => 'yes | no — ' . __('نوار دانلودهای باقی‌مانده امروز', 'eshterakyar'),
            'show_total_bar' => 'yes | no — ' . __('نوار دانلودهای باقی‌مانده کل', 'eshterakyar'),
            'show_download_url' => 'yes | no — ' . __('نمایش لینک دانلود زیر دکمه', 'eshterakyar'),
            'show_plan_name' => 'yes | no',
            'download_text / plans_text / cart_text / note_text' => __('متن سفارشی دکمه‌ها و پیام', 'eshterakyar'),
            'plans_url' => __('لینک سفارشی صفحه پلن‌ها (اختیاری)', 'eshterakyar'),
            'align' => 'stretch | left | center | right',
            'class' => __('کلاس CSS اضافی', 'eshterakyar'),
        ),
        'examples' => array(
            '[eshterakyar_product_cta]',
            '[eshterakyar_product_cta no_sub_mode="cart"]',
            '[eshterakyar_product_cta no_sub_mode="plans"]',
            '[eshterakyar_product_cta no_sub_mode="both" show_daily_bar="yes" show_download_url="yes"]',
            '[eshterakyar_product_cta product_id="123" no_sub_mode="cart" show_price="yes" show_total_bar="yes"]',
            '[eshterakyar_product_cta no_sub_mode="both" download_text="دانلود رایگان" plans_text="خرید اشتراک"]',
        ),
        'notes' => __('فقط شورتکد است و دکمه خودکار صفحه محصول را عوض نمی‌کند. دکمه پیش‌فرض ووکامرس را در قالب/المنتور مخفی کنید و این شورتکد را جایگزین بگذارید. با can_download، پلن‌ها، سهمیه روزانه/کلی، lifetime و چندپارته سازگار است.', 'eshterakyar'),
    ),
    array(
        'tag' => 'subscription_info',
        'desc' => __('اطلاعات کامل اشتراک فعال: پلن، روز باقی‌مانده، سهمیه دانلود و دکمه دانلود در صفحه محصول', 'eshterakyar'),
        'params' => array(
            'show_plan' => 'yes | no',
            'show_days' => 'yes | no',
            'show_downloads' => 'yes | no',
            'show_download_button' => 'yes | no',
        ),
        'examples' => array(
            '[subscription_info]',
            '[subscription_info show_plan="yes" show_days="yes" show_downloads="yes" show_download_button="no"]',
        ),
        'notes' => __('برای بالای مقاله یا سایدبار، show_download_button="no" توصیه می‌شود.', 'eshterakyar'),
    ),
    array(
        'tag' => 'subscription_badge',
        'desc' => __('دکمه فشرده وضعیت اشتراک با tooltip — ایده‌آل برای هدر سایت و بالای مقالات', 'eshterakyar'),
        'params' => array(
            'variant' => 'default | pill | outline | minimal | dark',
            'align' => 'inline | left | center | right',
            'tooltip_position' => 'top | bottom | left | right',
            'show_plan / show_days / show_downloads / show_daily / show_expiry' => 'yes | no',
            'show_tooltip_title' => 'yes | no',
            'active_text / inactive_text / login_text / tooltip_title' => __('متن سفارشی', 'eshterakyar'),
            'plans_url / account_url' => __('لینک سفارشی (اختیاری)', 'eshterakyar'),
        ),
        'examples' => array(
            '[subscription_badge]',
            '[subscription_badge variant="pill" show_daily="yes"]',
            '[subscription_badge variant="outline" align="center" tooltip_position="bottom"]',
            '[subscription_badge variant="dark" show_expiry="yes" active_text="عضو ویژه"]',
        ),
        'extra' => '<?php echo do_shortcode(\'[subscription_badge variant="pill"]\'); ?>',
        'extra_lang' => 'php',
        'extra_title' => __('استفاده در header.php', 'eshterakyar'),
    ),
    array(
        'tag' => 'eshterakyar_paywall',
        'desc' => __('جداکننده «بخش رایگان» و «ادامه مطلب ویژه» — متن بعد از این شورتکد فقط برای دارندگان اشتراک فعال نمایش داده می‌شود', 'eshterakyar'),
        'params' => array(),
        'examples' => array(
            '[eshterakyar_paywall]',
        ),
        'notes' => __('در ویرایش مقاله، شورتکد را دقیقاً بعد از پاراگراف‌های رایگان بگذارید. در سایدبار همان مقاله، متاباکس «محتوای ویژه» را هم فعال کنید.', 'eshterakyar'),
    ),
    array(
        'tag' => 'eshterakyar_subscription_plans',
        'desc' => __('صفحه پلن‌ها و قیمت‌گذاری با طراحی کارتی', 'eshterakyar'),
        'params' => array(
            'show_header' => 'true | false | yes | no | 1 | 0',
            'show_terms' => 'true | false | yes | no | 1 | 0',
            'terms_content' => __('متن قوانین (اختیاری)', 'eshterakyar'),
            'terms_position' => 'top | bottom',
        ),
        'examples' => array(
            '[eshterakyar_subscription_plans]',
            '[eshterakyar_subscription_plans show_header="true" show_terms="true"]',
            '[eshterakyar_subscription_plans show_terms="yes" terms_content="متن قوانین..." terms_position="bottom"]',
        ),
    ),
);
?>

<div id="shortcodes" class="eshterakyar-tab-content">
    <div class="eshterakyar-shortcodes-tab-header">
        <div>
            <h2><?php esc_html_e('راهنمای شورتکدها', 'eshterakyar'); ?></h2>
            <p class="description"><?php esc_html_e('هر شورتکد در کارت جداگانه با دکمه کپی سریع. روی «کپی» بزنید و در صفحه، مقاله، ویجت یا المنتور قرار دهید.', 'eshterakyar'); ?></p>
        </div>
        <div class="eshterakyar-shortcodes-tab-hint">
            <span class="dashicons dashicons-admin-page" aria-hidden="true"></span>
            <?php esc_html_e('کلیک روی «کپی» = آماده چسباندن', 'eshterakyar'); ?>
        </div>
    </div>

    <div class="eshterakyar-shortcode-cards">
        <?php foreach ($eshterakyar_shortcode_cards as $card) : ?>
            <article class="eshterakyar-shortcode-card" id="eshterakyar-sc-<?php echo esc_attr($card['tag']); ?>">
                <header class="eshterakyar-shortcode-card-header">
                    <?php
                    echo eshterakyar_render_code_block('[' . $card['tag'] . ']', array(
                        'lang' => 'shortcode',
                        'compact' => true,
                        'title' => '[' . $card['tag'] . ']',
                        'class' => 'eshterakyar-shortcode-primary-code',
                    ));
                    ?>
                </header>

                <p class="eshterakyar-shortcode-card-desc"><?php echo esc_html($card['desc']); ?></p>

                <?php if (!empty($card['params'])) : ?>
                    <div class="eshterakyar-shortcode-card-section">
                        <h4><?php esc_html_e('پارامترها', 'eshterakyar'); ?></h4>
                        <ul class="eshterakyar-shortcode-params-list">
                            <?php foreach ($card['params'] as $param_key => $param_val) : ?>
                                <li>
                                    <code class="eshterakyar-param-key"><?php echo esc_html($param_key); ?></code>
                                    <span class="eshterakyar-param-sep">:</span>
                                    <span class="eshterakyar-param-val"><?php echo esc_html($param_val); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($card['examples'])) : ?>
                    <?php
                    $basic_tag = '[' . $card['tag'] . ']';
                    $card_examples = array_values(array_filter($card['examples'], function ($example) use ($basic_tag) {
                        return trim($example) !== $basic_tag;
                    }));
                    ?>
                    <?php if (!empty($card_examples)) : ?>
                    <div class="eshterakyar-shortcode-card-section">
                        <h4><?php esc_html_e('مثال‌های آماده', 'eshterakyar'); ?></h4>
                        <div class="eshterakyar-shortcode-examples-stack">
                            <?php foreach ($card_examples as $example_index => $example_code) : ?>
                                <?php
                                echo eshterakyar_render_code_block($example_code, array(
                                    'lang' => 'shortcode',
                                    'compact' => true,
                                    'title' => sprintf(__('مثال %d', 'eshterakyar'), $example_index + 1),
                                ));
                                ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (!empty($card['notes'])) : ?>
                    <p class="eshterakyar-shortcode-card-note"><?php echo esc_html($card['notes']); ?></p>
                <?php endif; ?>

                <?php if (!empty($card['extra'])) : ?>
                    <div class="eshterakyar-shortcode-card-section">
                        <h4><?php echo esc_html($card['extra_title'] ?? __('نمونه کد', 'eshterakyar')); ?></h4>
                        <?php
                        echo eshterakyar_render_code_block($card['extra'], array(
                            'lang' => $card['extra_lang'] ?? 'code',
                        ));
                        ?>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="eshterakyar-shortcodes-footer-notes">
        <h3 class="shortcodes-notes-title"><?php esc_html_e('نکات مهم', 'eshterakyar'); ?></h3>
        <ul class="shortcodes-notes">
            <li><?php esc_html_e('شورتکدها را در صفحات، پست‌ها، ویجت‌ها و المنتور (ویجت «کد کوتاه») استفاده کنید.', 'eshterakyar'); ?></li>
            <li><?php esc_html_e('برای مهمان و کاربر بدون اشتراک، دکمه یا پیام خرید اشتراک نمایش داده می‌شود.', 'eshterakyar'); ?></li>
            <li><?php esc_html_e('متن‌های دکمه‌ها را از تب «متن‌های قابل ویرایش» یا پارامترهای شورتکد سفارشی کنید.', 'eshterakyar'); ?></li>
            <li><?php esc_html_e('در تب «راهنما» نمونه‌های PHP و CSS توسعه‌دهندگان هم با همین دکمه کپی در دسترس است.', 'eshterakyar'); ?></li>
        </ul>
    </div>
</div>
