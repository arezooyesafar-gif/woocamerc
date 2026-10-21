<?php
/**
 * تب تنظیمات محتوای ویژه (محدودسازی مقالات)
 *
 * @package Eshterakyar
 */

if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('eshterakyar_options', array());
$content_enabled = !empty($options['content_restriction_enabled']);
$content_mode = isset($options['content_restriction_mode']) ? $options['content_restriction_mode'] : 'inline';
$post_types_raw = isset($options['content_restriction_post_types']) ? $options['content_restriction_post_types'] : 'post';
$auto_restrict = !empty($options['content_auto_restrict_by_plan']);
$cumulative_content = !empty($options['cumulative_plan_content_access']);
$show_login_hint = !isset($options['content_show_login_hint']) || !empty($options['content_show_login_hint']);
?>
<div id="content" class="eshterakyar-tab-content">
    <h2><?php esc_html_e('محتوای ویژه (مقالات و وبلاگ)', 'eshterakyar'); ?></h2>
    <p class="description"><?php esc_html_e('با این بخش می‌توانید بخشی از مقاله را رایگان نشان دهید و ادامه مطلب را فقط برای دارندگان اشتراک فعال کنید. کافی است شورتکد جداکننده را بعد از پیش‌نمایش بگذارید و در صورت نیاز دسترسی را در پلن و متاباکس نوشته تنظیم کنید.', 'eshterakyar'); ?></p>

    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="content_restriction_enabled"><?php esc_html_e('فعال‌سازی محدودسازی محتوا', 'eshterakyar'); ?></label>
            </th>
            <td>
                <input type="checkbox" name="eshterakyar_options[content_restriction_enabled]" id="content_restriction_enabled" value="1" <?php checked(1, $content_enabled); ?>>
                <label for="content_restriction_enabled"><?php esc_html_e('محدودسازی ادامه مطلب برای نوشته‌ها فعال باشد', 'eshterakyar'); ?></label>
                <p class="description"><?php esc_html_e('اگر فقط در بعضی مقالات شورتکد جداکننده گذاشته‌اید، همان مقالات بدون این تیک هم محدود می‌شوند.', 'eshterakyar'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('حالت پیش‌فرض برای بازدیدکننده', 'eshterakyar'); ?></th>
            <td>
                <fieldset>
                    <label style="display:block;margin-bottom:8px;">
                        <input type="radio" name="eshterakyar_options[content_restriction_mode]" value="inline" <?php checked($content_mode, 'inline'); ?>>
                        <?php esc_html_e('نمایش پیش‌نمایش + پیام و دکمه خرید اشتراک (در همان صفحه مقاله)', 'eshterakyar'); ?>
                    </label>
                    <label style="display:block;">
                        <input type="radio" name="eshterakyar_options[content_restriction_mode]" value="redirect" <?php checked($content_mode, 'redirect'); ?>>
                        <?php esc_html_e('هدایت مستقیم به صفحه خرید پلن‌های اشتراک', 'eshterakyar'); ?>
                    </label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="content_restriction_post_types"><?php esc_html_e('انواع محتوا', 'eshterakyar'); ?></label>
            </th>
            <td>
                <input type="text" name="eshterakyar_options[content_restriction_post_types]" id="content_restriction_post_types" class="regular-text" value="<?php echo esc_attr(is_array($post_types_raw) ? implode(',', $post_types_raw) : $post_types_raw); ?>">
                <p class="description"><?php esc_html_e('پیش‌فرض: نوشته (post). برای چند نوع، شناسه‌ها را با ویرگول جدا کنید؛ مثال: post,page', 'eshterakyar'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="content_auto_restrict_by_plan"><?php esc_html_e('محدودسازی خودکار بر اساس پلن', 'eshterakyar'); ?></label>
            </th>
            <td>
                <input type="checkbox" name="eshterakyar_options[content_auto_restrict_by_plan]" id="content_auto_restrict_by_plan" value="1" <?php checked(1, $auto_restrict); ?>>
                <label for="content_auto_restrict_by_plan"><?php esc_html_e('مقالاتی که در دسته یا برچسب مجاز پلن‌ها هستند، حتی بدون شورتکد جداکننده هم محدود شوند', 'eshterakyar'); ?></label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="cumulative_plan_content_access"><?php esc_html_e('دسترسی پلن‌های بالاتر به محتوا', 'eshterakyar'); ?></label>
            </th>
            <td>
                <input type="checkbox" name="eshterakyar_options[cumulative_plan_content_access]" id="cumulative_plan_content_access" value="1" <?php checked(1, $cumulative_content); ?>>
                <label for="cumulative_plan_content_access"><?php esc_html_e('اگر در متاباکس مقاله فقط پلن بالاتر انتخاب شود، دارندگان پلن‌های پایین‌تر هم بتوانند ادامه مطلب را ببینند', 'eshterakyar'); ?></label>
                <p class="description"><?php esc_html_e('ترتیب پلن‌ها از همان چیدمان صفحه «پلن‌ها» (کشیدن و رها کردن) خوانده می‌شود.', 'eshterakyar'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="content_show_login_hint"><?php esc_html_e('پیام جدا برای مهمان', 'eshterakyar'); ?></label>
            </th>
            <td>
                <input type="checkbox" name="eshterakyar_options[content_show_login_hint]" id="content_show_login_hint" value="1" <?php checked(1, $show_login_hint); ?>>
                <label for="content_show_login_hint"><?php esc_html_e('برای بازدیدکنندهٔ لاگین‌نشده، متن مخصوص در بنر «ادامه مطلب» نمایش داده شود', 'eshterakyar'); ?></label>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('شورتکد جداکننده محتوا', 'eshterakyar'); ?></th>
            <td>
                <?php
                if (function_exists('eshterakyar_render_code_block')) {
                    echo eshterakyar_render_code_block('[eshterakyar_paywall]', array(
                        'lang' => 'shortcode',
                        'title' => __('کپی و قرار دادن در مقاله', 'eshterakyar'),
                    ));
                } else {
                    echo '<code>[eshterakyar_paywall]</code>';
                }
                ?>
                <p class="description"><?php esc_html_e('این شورتکد را در ویرایشگر مقاله، دقیقاً بعد از پاراگراف‌های رایگان قرار دهید. هر چیزی بعد از آن فقط برای کاربران دارای اشتراک مجاز نمایش داده می‌شود.', 'eshterakyar'); ?></p>
                <p class="description" style="margin-top:8px;color:#b45309;"><strong><?php esc_html_e('راهنمای تست:', 'eshterakyar'); ?></strong> <?php esc_html_e('مدیر سایت معمولاً کل مقاله را می‌بیند. برای دیدن حالت واقعی کاربر: صفحه را در حالت ناشناس باز کنید، یا به انتهای آدرس مقاله این را اضافه کنید:', 'eshterakyar'); ?> <code dir="ltr">?eshterakyar_force_paywall=1</code></p>
            </td>
        </tr>
    </table>
</div>
