<?php
/**
 * قالب صفحه تنظیمات حرفه‌ای
 */

if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('eshterakyar_options', array());
?>

<div class="eshterakyar-settings-content">
    <div id="eshterakyar-copy-toast" class="eshterakyar-copy-toast" role="status" aria-live="polite" hidden><?php esc_html_e('کپی شد!', 'eshterakyar'); ?></div>
    <div class="eshterakyar-settings-tabs">
        <nav class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active"><?php esc_html_e('تنظیمات عمومی', 'eshterakyar'); ?></a>
            <a href="#auto-membership" class="nav-tab"><?php esc_html_e('عضویت خودکار', 'eshterakyar'); ?></a>
            <a href="#email" class="nav-tab"><?php esc_html_e('ایمیل', 'eshterakyar'); ?></a>
            <a href="#sms" class="nav-tab"><?php esc_html_e('پیامک', 'eshterakyar'); ?></a>
            <a href="#content" class="nav-tab"><?php esc_html_e('محتوای ویژه', 'eshterakyar'); ?></a>
            <a href="#performance" class="nav-tab"><?php esc_html_e('بهینه‌سازی', 'eshterakyar'); ?></a>
            <a href="#security" class="nav-tab"><?php esc_html_e('امنیت', 'eshterakyar'); ?></a>
            <a href="#shortcodes" class="nav-tab"><?php esc_html_e('شورتکدها', 'eshterakyar'); ?></a>
            <a href="#labels" class="nav-tab"><?php esc_html_e('متن‌های قابل ویرایش', 'eshterakyar'); ?></a>
            <a href="#terms" class="nav-tab"><?php esc_html_e('قوانین و مقررات', 'eshterakyar'); ?></a>
            <a href="#help" class="nav-tab"><?php esc_html_e('راهنما', 'eshterakyar'); ?></a>
        </nav>
    </div>
    
    <form method="post" action="options.php" id="eshterakyar-settings-form">
        <?php
        settings_fields('eshterakyar_settings');
        ?>
        
        <!-- تب تنظیمات عمومی -->
        <div id="general" class="eshterakyar-tab-content active">
            <h2><?php esc_html_e('تنظیمات عمومی', 'eshterakyar'); ?></h2>
            <p class="description"><?php esc_html_e('تنظیمات اصلی افزونه را در این بخش مدیریت کنید.', 'eshterakyar'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="subscription_plans_page_id"><?php esc_html_e('صفحه پلن‌ها و خرید اشتراک', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $plans_page_id = isset($options['subscription_plans_page_id']) ? absint($options['subscription_plans_page_id']) : 0;
                        if ($plans_page_id <= 0) {
                            $plans_page_id = absint(get_option('eshterakyar_subscription_page_id', 0));
                        }
                        $plans_custom_url = isset($options['subscription_plans_custom_url']) ? (string) $options['subscription_plans_custom_url'] : '';
                        wp_dropdown_pages(array(
                            'name' => 'eshterakyar_options[subscription_plans_page_id]',
                            'id' => 'subscription_plans_page_id',
                            'selected' => $plans_page_id,
                            'show_option_none' => __('— پیش‌فرض افزونه (subscription-plans) —', 'eshterakyar'),
                            'option_none_value' => '0',
                        ));
                        ?>
                        <p class="description">
                            <?php esc_html_e('صفحه‌ای که همه دکمه‌های «خرید اشتراک و دانلود رایگان»، «تمدید اشتراک» و لینک‌های مشابه به آن می‌روند. اگر با المنتور صفحه پلن‌ها را طراحی کرده‌اید، همان صفحه را اینجا انتخاب کنید.', 'eshterakyar'); ?>
                        </p>
                        <?php
                        $resolved_plans_url = class_exists('Eshterakyar_Subscription_Page')
                            ? Eshterakyar_Subscription_Page::get_page_url()
                            : '';
                        if ($resolved_plans_url) :
                            ?>
                            <p class="description" style="margin-top:8px;">
                                <strong><?php esc_html_e('آدرس فعلی:', 'eshterakyar'); ?></strong>
                                <a href="<?php echo esc_url($resolved_plans_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($resolved_plans_url); ?></a>
                            </p>
                        <?php endif; ?>
                        <p style="margin-top:12px;">
                            <label for="subscription_plans_custom_url"><strong><?php esc_html_e('آدرس سفارشی (اختیاری)', 'eshterakyar'); ?></strong></label><br>
                            <input type="url" name="eshterakyar_options[subscription_plans_custom_url]" id="subscription_plans_custom_url" value="<?php echo esc_attr($plans_custom_url); ?>" class="regular-text ltr" placeholder="https://example.com/my-plans" style="direction:ltr; text-align:left;">
                        </p>
                        <p class="description"><?php esc_html_e('اگر پر شود، اولویت با این آدرس است (حتی بالاتر از صفحه انتخاب‌شده). برای اسلاگ/صفحه خارجی یا مسیر غیر استاندارد استفاده کنید. خالی بگذارید تا از صفحه بالا استفاده شود.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="enable_notifications"><?php esc_html_e('فعال‌سازی اطلاع‌رسانی', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $enable_notifications = isset($options['enable_notifications']) ? $options['enable_notifications'] : 0;
                        ?>
                        <input type="checkbox" name="eshterakyar_options[enable_notifications]" id="enable_notifications" value="1" <?php checked(1, $enable_notifications); ?>>
                        <label for="enable_notifications"><?php esc_html_e('ارسال ایمیل و پیامک برای رویدادهای اشتراک', 'eshterakyar'); ?></label>
                        <p class="description"><?php esc_html_e('با فعال کردن این گزینه، ایمیل و پیامک برای خرید، هشدار انقضا و انقضای اشتراک ارسال می‌شود.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="days_before_expiry"><?php esc_html_e('روز قبل از انقضا برای هشدار', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="number" name="eshterakyar_options[days_before_expiry]" id="days_before_expiry" value="<?php echo isset($options['days_before_expiry']) ? esc_attr($options['days_before_expiry']) : 7; ?>" min="0" max="365" class="small-text">
                        <p class="description"><?php esc_html_e('تعداد روز قبل از انقضای اشتراک که هشدار ارسال شود. (0 = غیرفعال)', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="enable_redownload"><?php esc_html_e('دانلود مجدد بدون کاهش اعتبار', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $enable_redownload = isset($options['enable_redownload']) ? $options['enable_redownload'] : 1;
                        ?>
                        <input type="checkbox" name="eshterakyar_options[enable_redownload]" id="enable_redownload" value="1" <?php checked(1, $enable_redownload); ?>>
                        <label for="enable_redownload"><?php esc_html_e('اجازه دانلود مجدد بدون کاهش اعتبار', 'eshterakyar'); ?></label>
                        <p class="description"><?php esc_html_e('با فعال کردن این گزینه، کاربران می‌توانند فایل‌های قبلاً دانلود شده را مجدد دانلود کنند بدون اینکه از اعتبار دانلودشان کم شود.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="show_downloaded_badge"><?php esc_html_e('نمایش کارت دانلود شده', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $show_badge = isset($options['show_downloaded_badge']) ? $options['show_downloaded_badge'] : 1;
                        ?>
                        <input type="checkbox" name="eshterakyar_options[show_downloaded_badge]" id="show_downloaded_badge" value="1" <?php checked(1, $show_badge); ?>>
                        <label for="show_downloaded_badge"><?php esc_html_e('نمایش کارت "دانلود شده" روی تصویر محصول', 'eshterakyar'); ?></label>
                        <p class="description"><?php esc_html_e('این کارت با المنتور و ووکامرس کاملاً سازگار است.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="merge_subscription_credits"><?php esc_html_e('جمع کردن مجوزها در ارتقاء', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $merge_credits = isset($options['merge_subscription_credits']) ? $options['merge_subscription_credits'] : 0;
                        ?>
                        <input type="checkbox" name="eshterakyar_options[merge_subscription_credits]" id="merge_subscription_credits" value="1" <?php checked(1, $merge_credits); ?>>
                        <label for="merge_subscription_credits"><?php esc_html_e('در صورت خرید اشتراک جدید هم‌گروه، مجوزها و روزهای باقی‌مانده جمع شوند', 'eshterakyar'); ?></label>
                        <p class="description"><?php esc_html_e('اگر این گزینه فعال باشد، در هنگام خرید اشتراک جدید هم‌گروه، مجوزهای دانلود و روزهای باقی‌مانده با اشتراک قبلی جمع می‌شوند. برای استفاده از این قابلیت، باید در تنظیمات هر پلن، گزینه "جمع کردن با اشتراک قبلی" را نیز فعال کنید.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="cumulative_plan_download_access"><?php esc_html_e('دسترسی جمع‌شونده پلن‌ها به دانلود محصول', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $cumulative_download = isset($options['cumulative_plan_download_access']) ? $options['cumulative_plan_download_access'] : 0;
                        ?>
                        <input type="checkbox" name="eshterakyar_options[cumulative_plan_download_access]" id="cumulative_plan_download_access" value="1" <?php checked(1, $cumulative_download); ?>>
                        <label for="cumulative_plan_download_access"><?php esc_html_e('انتخاب بالاترین پلن در محصول، دسترسی دانلود را برای پلن‌های پایین‌تر هم فعال کند', 'eshterakyar'); ?></label>
                        <p class="description"><?php esc_html_e('با فعال کردن این گزینه، اگر در صفحه محصول فقط پلن بالاتر (مثلاً طلایی) را انتخاب کنید، کاربران با پلن‌های پایین‌تر (مثلاً برنزی و نقره‌ای) هم می‌توانند دانلود کنند. سلسله‌مراتب پلن‌ها از ترتیب نمایش در «اشتراک یار > پلن‌ها» (drag & drop) تعیین می‌شود؛ پلن پایین‌تر = عدد sort_order کمتر.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="auto_add_shortcodes"><?php esc_html_e('اجرای اتوماتیک شورتکدها', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $auto_shortcodes = isset($options['auto_add_shortcodes']) ? $options['auto_add_shortcodes'] : 1;
                        ?>
                        <input type="checkbox" name="eshterakyar_options[auto_add_shortcodes]" id="auto_add_shortcodes" value="1" <?php checked(1, $auto_shortcodes); ?>>
                        <label for="auto_add_shortcodes"><?php esc_html_e('افزودن خودکار شورتکدها به صفحات (در صورت عدم سازگاری با قالب، این گزینه را غیرفعال کنید)', 'eshterakyar'); ?></label>
                        <p class="description"><?php esc_html_e('اگر قالب شما با شورتکدهای خودکار سازگار نیست، این گزینه را غیرفعال کنید و خودتان شورتکدها را در بخش مورد نظر قرار دهید.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="frontend_style"><?php esc_html_e('استایل نمایش باکس‌ها و شورتکدها', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $frontend_style = isset($options['frontend_style']) ? $options['frontend_style'] : 'style1';
                        ?>
                        <fieldset id="frontend_style">
                            <label style="display:inline-block; margin-left:15px;">
                                <input type="radio" name="eshterakyar_options[frontend_style]" value="style1" <?php checked($frontend_style, 'style1'); ?>>
                                <?php esc_html_e('استایل ۱ (پیش‌فرض فعلی)', 'eshterakyar'); ?>
                            </label>
                            <label style="display:inline-block; margin-left:15px;">
                                <input type="radio" name="eshterakyar_options[frontend_style]" value="style2" <?php checked($frontend_style, 'style2'); ?>>
                                <?php esc_html_e('استایل ۲ (طراحی جدید)', 'eshterakyar'); ?>
                            </label>
                        </fieldset>
                        <p class="description"><?php esc_html_e('این گزینه روی ظاهر باکس دانلود، اطلاعات اشتراک و شورتکدهای مرتبط در صفحات محصول و حساب کاربری اعمال می‌شود.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="show_download_box_for_non_subscription_products"><?php esc_html_e('نمایش باکس دانلود برای محصولات غیر اشتراکی', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $show_download_box_for_non_subscription = isset($options['show_download_box_for_non_subscription_products']) ? $options['show_download_box_for_non_subscription_products'] : 1;
                        ?>
                        <input type="checkbox" name="eshterakyar_options[show_download_box_for_non_subscription_products]" id="show_download_box_for_non_subscription_products" value="1" <?php checked(1, $show_download_box_for_non_subscription); ?>>
                        <label for="show_download_box_for_non_subscription_products"><?php esc_html_e('نمایش باکس دانلود و اطلاعات اشتراک برای محصولاتی که با اشتراک قابل دانلود نیستند', 'eshterakyar'); ?></label>
                        <p class="description"><?php esc_html_e('اگر این گزینه غیرفعال باشد، باکس دانلود و اطلاعات اشتراک فقط برای محصولاتی نمایش داده می‌شود که با اشتراک قابل دانلود هستند. اگر فعال باشد، برای همه محصولات نمایش داده می‌شود (حتی اگر قابل دانلود با اشتراک نباشند).', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="product_centric_subscription_access"><?php esc_html_e('تشخیص محصول‌محور دسترسی اشتراک', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $product_centric_subscription_access = isset($options['product_centric_subscription_access']) ? $options['product_centric_subscription_access'] : 0;
                        ?>
                        <input type="checkbox" name="eshterakyar_options[product_centric_subscription_access]" id="product_centric_subscription_access" value="1" <?php checked(1, $product_centric_subscription_access); ?>>
                        <label for="product_centric_subscription_access"><?php esc_html_e('محصول فقط وقتی اشتراکی تشخیص داده شود که «دانلود با اشتراک» فعال باشد یا در دسته/تگ مجاز یک پلن قرار بگیرد', 'eshterakyar'); ?></label>
                        <p class="description"><?php esc_html_e('در حالت فعال: پلن بدون دسته/تگ مجاز به همه محصولات دسترسی نمی‌دهد. در حالت غیرفعال (پیش‌فرض): پلن بدون محدودیت دسترسی به همه محصولات دارد.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="download_history_retention"><?php esc_html_e('مدت نگهداری سوابق دانلود (روز)', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="number" name="eshterakyar_options[download_history_retention]" id="download_history_retention" value="<?php echo isset($options['download_history_retention']) ? esc_attr($options['download_history_retention']) : '365'; ?>" min="30" max="3650" class="small-text">
                        <p class="description"><?php esc_html_e('سوابق دانلود تا این تعداد روز در دیتابیس نگهداری می‌شوند. بعد از این مدت، سوابق قدیمی به صورت خودکار پاک می‌شوند. (پیش‌فرض: 365 روز = 1 سال)', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="delete_data_on_uninstall"><?php esc_html_e('پاک کردن دیتابیس هنگام حذف افزونه', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $delete_data = get_option('eshterakyar_delete_data_on_uninstall', false);
                        ?>
                        <input type="checkbox" name="eshterakyar_delete_data_on_uninstall" id="delete_data_on_uninstall" value="1" <?php checked($delete_data); ?>>
                        <label for="delete_data_on_uninstall"><?php esc_html_e('در صورت حذف افزونه، تمامی جداول و داده‌های مرتبط با افزونه پاک شوند', 'eshterakyar'); ?></label>
                        <p class="description warning-text"><?php esc_html_e('⚠️ هشدار: در صورت فعال بودن این گزینه، در هنگام حذف افزونه تمامی داده‌ها (اشتراک‌ها، پلن‌ها، دانلودها و ...) به صورت دائمی حذف خواهند شد. این عمل غیرقابل بازگشت است!', 'eshterakyar'); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <?php include ESHTERAKYAR_PLUGIN_DIR . 'templates/admin/partials/settings-auto-membership-tab.php'; ?>
        
        <!-- تب ایمیل -->
        <div id="email" class="eshterakyar-tab-content">
            <h2><?php esc_html_e('تنظیمات ایمیل', 'eshterakyar'); ?></h2>
            <p class="description"><?php esc_html_e('تنظیمات مربوط به ایمیل‌های اطلاع‌رسانی', 'eshterakyar'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="email_subject_purchase"><?php esc_html_e('موضوع ایمیل خرید', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="eshterakyar_options[email_subject_purchase]" id="email_subject_purchase" value="<?php echo isset($options['email_subject_purchase']) ? esc_attr($options['email_subject_purchase']) : esc_attr(__('خرید اشتراک شما با موفقیت انجام شد', 'eshterakyar')); ?>" class="regular-text">
                        <p class="description"><?php esc_html_e('موضوع ایمیل که بعد از خرید اشتراک ارسال می‌شود.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="email_subject_expiry_warning"><?php esc_html_e('موضوع ایمیل هشدار انقضا', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="eshterakyar_options[email_subject_expiry_warning]" id="email_subject_expiry_warning" value="<?php echo isset($options['email_subject_expiry_warning']) ? esc_attr($options['email_subject_expiry_warning']) : esc_attr(__('اشتراک شما به زودی منقضی می‌شود', 'eshterakyar')); ?>" class="regular-text">
                        <p class="description"><?php esc_html_e('موضوع ایمیل هشدار قبل از انقضای اشتراک.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="email_subject_expired"><?php esc_html_e('موضوع ایمیل انقضا', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="eshterakyar_options[email_subject_expired]" id="email_subject_expired" value="<?php echo isset($options['email_subject_expired']) ? esc_attr($options['email_subject_expired']) : esc_attr(__('اشتراک شما منقضی شد', 'eshterakyar')); ?>" class="regular-text">
                        <p class="description"><?php esc_html_e('موضوع ایمیل بعد از انقضای اشتراک.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- تب پیامک -->
        <div id="sms" class="eshterakyar-tab-content">
            <h2><?php esc_html_e('تنظیمات پیامک', 'eshterakyar'); ?></h2>
            <p class="description"><?php esc_html_e('تنظیمات مربوط به ارسال پیامک از طریق سامانه‌های پیامکی', 'eshterakyar'); ?></p>

            <!-- راهنمای حرفه‌ای فرمت و متغیرهای پیامک (برای همه سامانه‌ها) -->
            <div class="eshterakyar-sms-help-box" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #0ea5e9; border-radius: 12px; padding: 24px; margin-bottom: 28px; box-shadow: 0 2px 8px rgba(14,165,233,0.08);">
                <h3 style="margin-top: 0; color: #0369a1; font-size: 1.1em; border-bottom: 2px solid #0ea5e9; padding-bottom: 10px;">
                    <span style="margin-left: 6px;">📱</span> <?php esc_html_e('فرمت متن پیامک و متغیرهای قابل استفاده (برای همه سامانه‌ها)', 'eshterakyar'); ?>
                </h3>
                <p style="margin: 0 0 14px 0; font-size: 12px; color: #64748b;">
                    <?php esc_html_e('این راهنما برای ملی‌پیامک، کاوه‌نگار و SMS.ir یکسان است. در متن پیامک‌های خرید، هشدار انقضا و انقضای اشتراک از متغیرهای زیر استفاده کنید.', 'eshterakyar'); ?>
                </p>
                <div style="margin: 16px 0;">
                    <p style="margin: 0 0 12px 0; color: #0c4a6e; line-height: 1.7;">
                        <strong><?php esc_html_e('۱) پیامک ساده (متن آزاد):', 'eshterakyar'); ?></strong><br>
                        <?php esc_html_e('در کاوه‌نگار، SMS.ir (Bulk) و ملی‌پیامک (بدون bodyId) متن را با متغیرهای داخل آکولاد بنویسید. مثال:', 'eshterakyar'); ?>
                    </p>
                    <p style="background: #fff; padding: 12px 16px; border-radius: 8px; border: 1px solid #bae6fd; margin: 8px 0; font-family: monospace;color: #000;">
                        سلام {userName}، اشتراک شما با موفقیت فعال شد. پلن: {planName} — انقضا: {endDate}
                    </p>
                </div>
                <div style="margin: 16px 0;">
                    <p style="margin: 0 0 12px 0; color: #0c4a6e; line-height: 1.7;">
                        <strong><?php esc_html_e('۲) پیامک با قالب (فقط ملی‌پیامک — bodyId):', 'eshterakyar'); ?></strong><br>
                        <?php esc_html_e('فرمت:', 'eshterakyar'); ?> <code style="background: #fff; padding: 4px 10px; border-radius: 6px;">@bodyId@{var1};{var2};{var3}</code>
                        <?php esc_html_e('— اگر bodyId را در فیلد جداگانه وارد کنید، فقط بخش متغیرها را با نقطه‌ویرگول بنویسید.', 'eshterakyar'); ?>
                    </p>
                    <p style="background: #fff; padding: 12px 16px; border-radius: 8px; border: 1px solid #bae6fd; margin: 8px 0; font-family: monospace; color: #000;">
                        @12345@{userName};{planName};{endDate}
                    </p>
                    <p class="description" style="margin: 6px 0 0 0; font-size: 12px;">
                        <?php esc_html_e('۱۲۳۴۵ = کد bodyId در پنل ملی‌پیامک. متغیرها با آکولاد یا بدون آن پشتیبانی می‌شوند.', 'eshterakyar'); ?>
                    </p>
                </div>
                <div style="margin: 16px 0;">
                    <p style="margin: 0 0 12px 0; color: #0c4a6e; line-height: 1.7;">
                        <strong><?php esc_html_e('۳) SMS.ir با قالب (Verify):', 'eshterakyar'); ?></strong><br>
                        <?php esc_html_e('برای ارسال با پترن، در فیلد «نام پارامترهای قالب» ترتیب پارامترها را با کاما وارد کنید (مثلاً userName,planName,endDate). متن پیامک در بخش‌های زیر همان متغیرهای آکولادی یا متن ساده است.', 'eshterakyar'); ?>
                    </p>
                </div>
                <p style="margin: 14px 0 8px 0; font-weight: 600; color: #0369a1;"><?php esc_html_e('متغیرهای قابل استفاده (کلیک برای کپی):', 'eshterakyar'); ?></p>
                <div class="eshterakyar-sms-var-chips" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px;">
                    <?php
                    $sms_vars = array(
                        '{userName}' => __('نام کاربر', 'eshterakyar'),
                        '{planName}' => __('نام پلن', 'eshterakyar'),
                        '{startDate}' => __('تاریخ شروع', 'eshterakyar'),
                        '{endDate}' => __('تاریخ پایان', 'eshterakyar'),
                        '{daysLeft}' => __('روزهای باقی‌مانده', 'eshterakyar'),
                        '{totalDownloads}' => __('مجموع دانلودهای مجاز', 'eshterakyar'),
                        '{dailyLimit}' => __('محدودیت دانلود روزانه', 'eshterakyar'),
                        '{siteName}' => __('نام سایت', 'eshterakyar'),
                    );
                    foreach ($sms_vars as $var => $label) :
                    ?>
                        <button type="button" class="button eshterakyar-var-chip" data-copy="<?php echo esc_attr($var); ?>" title="<?php echo esc_attr(sprintf(__('کپی: %s', 'eshterakyar'), $var)); ?>" style="font-family: monospace; font-size: 12px; padding: 6px 12px; border-radius: 20px; cursor: pointer; background: #fff; border: 1px solid #0ea5e9; color: #0369a1;">
                            <code><?php echo esc_html($var); ?></code>
                        </button>
                    <?php endforeach; ?>
                </div>
                <p style="margin: 0; font-size: 12px; color: #64748b;">
                    <span id="eshterakyar-var-copy-feedback" style="display: none; color: #059669; font-weight: 600;"><?php esc_html_e('کپی شد!', 'eshterakyar'); ?></span>
                </p>
            </div>
            <style>
            .eshterakyar-var-chip:hover { background: #e0f2fe !important; border-color: #0284c7 !important; color: #0369a1 !important; }
            .eshterakyar-sms-preset:hover { background: #f0fdf4 !important; border-color: #22c55e !important; }
            </style>
            
            <h3><?php esc_html_e('انتخاب سامانه پیامکی', 'eshterakyar'); ?></h3>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="sms_provider"><?php esc_html_e('سامانه پیامکی', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <select name="eshterakyar_options[sms_provider]" id="sms_provider" class="regular-text">
                            <option value="melli" <?php selected(isset($options['sms_provider']) ? $options['sms_provider'] : 'melli', 'melli'); ?>><?php esc_html_e('ملی پیامک', 'eshterakyar'); ?></option>
                            <option value="kavenegar" <?php selected(isset($options['sms_provider']) ? $options['sms_provider'] : '', 'kavenegar'); ?>><?php esc_html_e('کاوه نگار', 'eshterakyar'); ?></option>
                            <option value="smsir" <?php selected(isset($options['sms_provider']) ? $options['sms_provider'] : '', 'smsir'); ?>><?php esc_html_e('SMS.ir', 'eshterakyar'); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('سامانه پیامکی مورد استفاده برای ارسال پیامک را انتخاب کنید', 'eshterakyar'); ?></p>
                    </td>
                </tr>
            </table>

            <div id="sms-settings-kavenegar" class="eshterakyar-sms-provider-settings" data-provider="kavenegar" style="<?php echo (isset($options['sms_provider']) && $options['sms_provider'] === 'kavenegar') ? '' : 'display:none;'; ?>">
            <h3><?php esc_html_e('تنظیمات کاوه نگار', 'eshterakyar'); ?></h3>
            <p class="description" style="background: #f0fdf4; border-right: 4px solid #22c55e; padding: 12px; margin-bottom: 20px;">
                <strong><?php esc_html_e('راهنمای تنظیمات:', 'eshterakyar'); ?></strong><br>
                <?php esc_html_e('کاوه نگار از REST API استفاده می‌کند. API Key را از پنل کاربری کاوه نگار (kavenegar.com) دریافت کنید.', 'eshterakyar'); ?><br>
                <?php esc_html_e('متن پیامک در بخش‌های «پیامک بعد از خرید»، «هشدار انقضا» و «انقضای اشتراک» با همان فرمت و متغیرهای جعبه راهنمای بالا است — از متغیرهای داخل آکولاد مثل ', 'eshterakyar'); ?><code>{userName}</code>، <code>{planName}</code>، <code>{endDate}</code> <?php esc_html_e('استفاده کنید (کلیک برای کپی).', 'eshterakyar'); ?>
            </p>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="sms_kavenegar_api_key"><?php esc_html_e('API Key', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="eshterakyar_options[sms_kavenegar_api_key]" id="sms_kavenegar_api_key" value="<?php echo isset($options['sms_kavenegar_api_key']) ? esc_attr($options['sms_kavenegar_api_key']) : ''; ?>" class="regular-text" autocomplete="off">
                        <p class="description"><?php esc_html_e('API Key دریافتی از پنل کاوه نگار (بخش وب‌سرویس)', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="sms_kavenegar_sender"><?php esc_html_e('شماره فرستنده', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="eshterakyar_options[sms_kavenegar_sender]" id="sms_kavenegar_sender" value="<?php echo isset($options['sms_kavenegar_sender']) ? esc_attr($options['sms_kavenegar_sender']) : ''; ?>" class="regular-text" placeholder="<?php esc_attr_e('مثال: 10004346', 'eshterakyar'); ?>">
                        <p class="description"><?php esc_html_e('شماره خط ارسال (اختیاری؛ در صورت خالی بودن از خط پیش‌فرض حساب استفاده می‌شود)', 'eshterakyar'); ?></p>
                    </td>
                </tr>
            </table>
            </div>

            <div id="sms-settings-smsir" class="eshterakyar-sms-provider-settings" data-provider="smsir" style="<?php echo (isset($options['sms_provider']) && $options['sms_provider'] === 'smsir') ? '' : 'display:none;'; ?>">
            <h3><?php esc_html_e('تنظیمات SMS.ir', 'eshterakyar'); ?></h3>
            <p class="description" style="background: #fef3c7; border-right: 4px solid #f59e0b; padding: 12px; margin-bottom: 20px;">
                <strong><?php esc_html_e('دو متد ارسال:', 'eshterakyar'); ?></strong><br>
                <?php esc_html_e('• Bulk (خط اختصاصی): برای پیامک‌های تکی و گروهی با متن آزاد. شماره خط و API Key الزامی است. متن پیامک با همان متغیرهای راهنمای بالا (', 'eshterakyar'); ?><code>{userName}</code>, <code>{planName}</code>, <code>{endDate}</code> <?php esc_html_e('و ...) نوشته می‌شود.', 'eshterakyar'); ?><br>
                <?php esc_html_e('• Verify (خط خدماتی): برای ارسال با قالب (پترن). در صورت پر کردن «شناسه قالب» و «نام پارامترها»، برای پیامک‌هایی که متغیر دارند از این متد استفاده می‌شود. نام پارامترها را به ترتیب با کاما وارد کنید (مثلاً userName,planName,endDate).', 'eshterakyar'); ?>
            </p>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="sms_smsir_api_key"><?php esc_html_e('API Key', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="eshterakyar_options[sms_smsir_api_key]" id="sms_smsir_api_key" value="<?php echo isset($options['sms_smsir_api_key']) ? esc_attr($options['sms_smsir_api_key']) : ''; ?>" class="regular-text" autocomplete="off">
                        <p class="description"><?php esc_html_e('کلید API از پنل برنامه‌نویسان SMS.ir (هدر X-API-KEY)', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="sms_smsir_line_number"><?php esc_html_e('شماره خط اختصاصی (Bulk)', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="eshterakyar_options[sms_smsir_line_number]" id="sms_smsir_line_number" value="<?php echo isset($options['sms_smsir_line_number']) ? esc_attr($options['sms_smsir_line_number']) : ''; ?>" class="regular-text" placeholder="<?php esc_attr_e('مثال: 300000000000', 'eshterakyar'); ?>">
                        <p class="description"><?php esc_html_e('شماره خط اختصاصی برای متد Bulk (ارسال با متن آزاد). الزامی برای ارسال عادی.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="sms_smsir_verify_template_id"><?php esc_html_e('شناسه قالب (Verify، اختیاری)', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="number" name="eshterakyar_options[sms_smsir_verify_template_id]" id="sms_smsir_verify_template_id" value="<?php echo isset($options['sms_smsir_verify_template_id']) ? esc_attr($options['sms_smsir_verify_template_id']) : ''; ?>" class="small-text" min="0" placeholder="<?php esc_attr_e('خالی = فقط Bulk', 'eshterakyar'); ?>">
                        <p class="description"><?php esc_html_e('اگر قالب (پترن) در SMS.ir تعریف کرده‌اید، شناسه آن را وارد کنید تا برای پیامک‌های دارای متغیر از متد Verify استفاده شود.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="sms_smsir_verify_parameter_names"><?php esc_html_e('نام پارامترهای قالب (Verify)', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="eshterakyar_options[sms_smsir_verify_parameter_names]" id="sms_smsir_verify_parameter_names" value="<?php echo isset($options['sms_smsir_verify_parameter_names']) ? esc_attr($options['sms_smsir_verify_parameter_names']) : ''; ?>" class="regular-text" placeholder="<?php esc_attr_e('مثال: userName,planName,endDate', 'eshterakyar'); ?>">
                        <p class="description"><?php esc_html_e('نام پارامترهای قالب به ترتیب، با کاما جدا شده (مطابق تعریف در SMS.ir). مثال: userName,planName,endDate', 'eshterakyar'); ?></p>
                    </td>
                </tr>
            </table>
            </div>
            
            <div id="sms-settings-melli" class="eshterakyar-sms-provider-settings" data-provider="melli" style="<?php echo (!isset($options['sms_provider']) || $options['sms_provider'] === 'melli') ? '' : 'display:none;'; ?>">
            <h3><?php esc_html_e('تنظیمات ملی پیامک', 'eshterakyar'); ?></h3>
            <p class="description" style="background: #f0f9ff; border-right: 4px solid #3b82f6; padding: 12px; margin-bottom: 20px;">
                <strong><?php esc_html_e('راهنمای تنظیمات:', 'eshterakyar'); ?></strong><br>
                <?php esc_html_e('سامانه ملی پیامک به صورت خودکار بهترین متد را انتخاب می‌کند. فرمت و متغیرهای جعبه راهنمای بالا برای این سامانه هم یکسان است:', 'eshterakyar'); ?><br>
                <?php esc_html_e('• اگر bodyId را وارد کنید، از SendByBaseNumber3 (قالب پیش‌فرض) استفاده می‌شود. فرمت متغیرها: ', 'eshterakyar'); ?><code>@bodyId@{userName};{planName};{endDate}</code> <?php esc_html_e('(از دکمه‌های متغیر در راهنمای بالا کلیک برای کپی).', 'eshterakyar'); ?><br>
                <?php esc_html_e('• اگر bodyId وارد نکنید، از SendSimpleSMS2 (پیامک ساده) استفاده می‌شود — متن را با همان متغیرهای آکولادی بنویسید.', 'eshterakyar'); ?>
            </p>
            <input type="hidden" name="eshterakyar_options[sms_method]" value="auto">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="sms_username"><?php esc_html_e('نام کاربری', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="eshterakyar_options[sms_username]" id="sms_username" value="<?php echo isset($options['sms_username']) ? esc_attr($options['sms_username']) : ''; ?>" class="regular-text">
                        <p class="description"><?php esc_html_e('نام کاربری شما در سامانه ملی پیامک (payamak-panel.com)', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="sms_password"><?php esc_html_e('رمز عبور', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="password" name="eshterakyar_options[sms_password]" id="sms_password" value="<?php echo isset($options['sms_password']) ? esc_attr($options['sms_password']) : ''; ?>" class="regular-text">
                        <p class="description"><?php esc_html_e('رمز عبور شما در سامانه ملی پیامک', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="sms_sender"><?php esc_html_e('شماره ارسال کننده', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="eshterakyar_options[sms_sender]" id="sms_sender" value="<?php echo isset($options['sms_sender']) ? esc_attr($options['sms_sender']) : ''; ?>" class="regular-text">
                        <p class="description"><?php esc_html_e('شماره ارسال کننده پیامک (برای SendSimpleSMS2 الزامی است، برای SendByBaseNumber3 اختیاری)', 'eshterakyar'); ?></p>
                    </td>
                </tr>
            </table>
            </div>
            
            <h3><?php esc_html_e('پیامک بعد از خرید اشتراک', 'eshterakyar'); ?></h3>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="sms_enable_purchase"><?php esc_html_e('فعال‌سازی پیامک خرید', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" name="eshterakyar_options[sms_enable_purchase]" id="sms_enable_purchase" value="1" <?php checked(isset($options['sms_enable_purchase']) && $options['sms_enable_purchase']); ?>>
                        <label for="sms_enable_purchase"><?php esc_html_e('ارسال پیامک بعد از خرید اشتراک', 'eshterakyar'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="sms_body_id_purchase"><?php esc_html_e('کد متن وب‌سرویس (bodyId)', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="eshterakyar_options[sms_body_id_purchase]" id="sms_body_id_purchase" value="<?php echo isset($options['sms_body_id_purchase']) ? esc_attr($options['sms_body_id_purchase']) : ''; ?>" class="regular-text" placeholder="<?php esc_attr_e('مثال: 12345', 'eshterakyar'); ?>">
                        <p class="description">
                            <?php esc_html_e('کد متن (bodyId) که در پنل کاربری ملی پیامک برای این نوع پیامک تعریف کرده‌اید. اگر خالی بگذارید، از متن ساده استفاده می‌شود.', 'eshterakyar'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="sms_message_purchase"><?php esc_html_e('متن پیامک', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <textarea name="eshterakyar_options[sms_message_purchase]" id="sms_message_purchase" rows="5" class="large-text" placeholder="<?php esc_attr_e('سلام {userName}، اشتراک شما فعال شد. پلن: {planName} — انقضا: {endDate}', 'eshterakyar'); ?>"><?php echo isset($options['sms_message_purchase']) ? esc_textarea($options['sms_message_purchase']) : ''; ?></textarea>
                        <p class="description" style="margin-top: 10px;">
                            <?php esc_html_e('از متغیرهای داخل آکولاد در جعبه راهنمای بالا استفاده کنید. برای bodyId فرمت:', 'eshterakyar'); ?> <code>@bodyId@{userName};{planName};{endDate}</code>
                        </p>
                        <p style="margin: 12px 0 6px 0; font-weight: 600;color: #000;"><?php esc_html_e('پیام‌های آماده (کلیک برای قرار دادن در متن):', 'eshterakyar'); ?></p>
                        <div class="eshterakyar-sms-preset-buttons" style="display: flex; flex-wrap: wrap; gap: 8px;">
                            <button type="button" class="button button-small eshterakyar-sms-preset" data-target="sms_message_purchase" data-value="<?php echo esc_attr('سلام {userName}، اشتراک شما با موفقیت فعال شد. پلن: {planName} — تاریخ انقضا: {endDate}. {siteName}'); ?>"><?php esc_html_e('خرید موفق (ساده)', 'eshterakyar'); ?></button>
                            <button type="button" class="button button-small eshterakyar-sms-preset" data-target="sms_message_purchase" data-value="<?php echo esc_attr('تبریک {userName}! اشتراک «{planName}» با موفقیت فعال شد. تا {endDate} از خدمات استفاده کنید. {siteName}'); ?>"><?php esc_html_e('خرید موفق (جذاب)', 'eshterakyar'); ?></button>
                            <button type="button" class="button button-small eshterakyar-sms-preset" data-target="sms_message_purchase" data-value="<?php echo esc_attr('@bodyId@{userName};{planName};{endDate}'); ?>"><?php esc_html_e('قالب bodyId (۳ متغیر)', 'eshterakyar'); ?></button>
                        </div>
                    </td>
                </tr>
            </table>
            
            <h3><?php esc_html_e('پیامک هشدار قبل از اتمام اشتراک', 'eshterakyar'); ?></h3>
            <div id="expiry-warnings-container">
                <?php
                $expiry_warnings = isset($options['sms_expiry_warnings']) && is_array($options['sms_expiry_warnings']) 
                    ? $options['sms_expiry_warnings'] 
                    : array(
                        array(
                            'days' => 3,
                            'enabled' => 1,
                            'template_code' => '',
                            'message' => __('سلام {user_name}\nاشتراک شما {days_left} روز دیگر منقضی می‌شود.\nتاریخ انقضا: {end_date}\nلطفاً برای تمدید اقدام کنید.', 'eshterakyar')
                        )
                    );
                
                foreach ($expiry_warnings as $index => $warning):
                ?>
                    <div class="expiry-warning-item" data-index="<?php echo esc_attr($index); ?>">
                        <h4><?php
                        /* translators: %d: Number of days before expiry */
                        printf(esc_html__('هشدار %d روز قبل از انقضا', 'eshterakyar'), esc_html($warning['days']));
                        ?></h4>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label><?php esc_html_e('فعال', 'eshterakyar'); ?></label>
                                </th>
                                <td>
                                    <input type="checkbox" name="eshterakyar_options[sms_expiry_warnings][<?php echo esc_attr($index); ?>][enabled]" value="1" <?php checked(isset($warning['enabled']) && $warning['enabled']); ?>>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label><?php esc_html_e('تعداد روز قبل از انقضا', 'eshterakyar'); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="eshterakyar_options[sms_expiry_warnings][<?php echo esc_attr($index); ?>][days]" value="<?php echo esc_attr($warning['days']); ?>" min="1" max="365" class="small-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label><?php esc_html_e('کد متن وب‌سرویس (bodyId)', 'eshterakyar'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="eshterakyar_options[sms_expiry_warnings][<?php echo esc_attr($index); ?>][body_id]" value="<?php echo isset($warning['body_id']) ? esc_attr($warning['body_id']) : ''; ?>" class="regular-text" placeholder="<?php esc_attr_e('مثال: 12345', 'eshterakyar'); ?>">
                                    <p class="description">
                                        <?php esc_html_e('کد متن (bodyId) که در پنل کاربری ملی پیامک برای این نوع پیامک تعریف کرده‌اید. اگر خالی بگذارید، از متن ساده استفاده می‌شود.', 'eshterakyar'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label><?php esc_html_e('متن پیامک', 'eshterakyar'); ?></label>
                                </th>
                                <td>
                                    <textarea name="eshterakyar_options[sms_expiry_warnings][<?php echo esc_attr($index); ?>][message]" rows="4" class="large-text eshterakyar-expiry-message" data-index="<?php echo esc_attr($index); ?>" placeholder="<?php esc_attr_e('سلام {userName}، {daysLeft} روز تا انقضای اشتراک «{planName}» باقی مانده. تاریخ انقضا: {endDate}', 'eshterakyar'); ?>"><?php echo isset($warning['message']) ? esc_textarea($warning['message']) : ''; ?></textarea>
                                    <p class="description" style="margin-top: 10px;">
                                        <?php esc_html_e('از متغیرهای راهنمای بالا استفاده کنید. برای bodyId:', 'eshterakyar'); ?> <code>@bodyId@{userName};{daysLeft};{endDate}</code>
                                    </p>
                                    <p style="margin: 10px 0 6px 0; font-weight: 600;color: #000;"><?php esc_html_e('پیام آماده:', 'eshterakyar'); ?></p>
                                    <button type="button" class="button button-small eshterakyar-sms-preset-expiry" data-index="<?php echo esc_attr($index); ?>" data-value="<?php echo esc_attr('سلام {userName}، {daysLeft} روز تا انقضای اشتراک «{planName}» باقی مانده. لطفاً برای تمدید اقدام کنید. {siteName}'); ?>"><?php esc_html_e('هشدار انقضا (جذاب)', 'eshterakyar'); ?></button>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"></th>
                                <td>
                                    <button type="button" class="button button-small remove-warning" data-index="<?php echo esc_attr($index); ?>"><?php esc_html_e('حذف این هشدار', 'eshterakyar'); ?></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
            <p>
                <button type="button" id="add-expiry-warning" class="button"><?php esc_html_e('+ افزودن هشدار جدید', 'eshterakyar'); ?></button>
            </p>
            
            <h3><?php esc_html_e('پیامک انقضای اشتراک', 'eshterakyar'); ?></h3>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="sms_enable_expired"><?php esc_html_e('فعال‌سازی پیامک انقضا', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" name="eshterakyar_options[sms_enable_expired]" id="sms_enable_expired" value="1" <?php checked(isset($options['sms_enable_expired']) && $options['sms_enable_expired']); ?>>
                        <label for="sms_enable_expired"><?php esc_html_e('ارسال پیامک بعد از انقضای اشتراک', 'eshterakyar'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="sms_body_id_expired"><?php esc_html_e('کد متن وب‌سرویس (bodyId)', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="eshterakyar_options[sms_body_id_expired]" id="sms_body_id_expired" value="<?php echo isset($options['sms_body_id_expired']) ? esc_attr($options['sms_body_id_expired']) : ''; ?>" class="regular-text" placeholder="<?php esc_attr_e('مثال: 12345', 'eshterakyar'); ?>">
                        <p class="description">
                            <?php esc_html_e('کد متن (bodyId) که در پنل کاربری ملی پیامک برای این نوع پیامک تعریف کرده‌اید. اگر خالی بگذارید، از متن ساده استفاده می‌شود.', 'eshterakyar'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="sms_message_expired"><?php esc_html_e('متن پیامک', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <textarea name="eshterakyar_options[sms_message_expired]" id="sms_message_expired" rows="4" class="large-text" placeholder="<?php esc_attr_e('سلام {userName}، اشتراک «{planName}» منقضی شد. برای تمدید به سایت مراجعه کنید.', 'eshterakyar'); ?>"><?php echo isset($options['sms_message_expired']) ? esc_textarea($options['sms_message_expired']) : ''; ?></textarea>
                        <p class="description" style="margin-top: 10px;">
                            <?php esc_html_e('از متغیرهای راهنمای بالا استفاده کنید. برای bodyId:', 'eshterakyar'); ?> <code>@bodyId@{userName};{planName}</code>
                        </p>
                        <p style="margin: 12px 0 6px 0; font-weight: 600;color: #000;"><?php esc_html_e('پیام‌های آماده:', 'eshterakyar'); ?></p>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            <button type="button" class="button button-small eshterakyar-sms-preset" data-target="sms_message_expired" data-value="<?php echo esc_attr('سلام {userName}، اشتراک «{planName}» منقضی شد. برای تمدید به {siteName} مراجعه کنید.'); ?>"><?php esc_html_e('انقضا (ساده)', 'eshterakyar'); ?></button>
                            <button type="button" class="button button-small eshterakyar-sms-preset" data-target="sms_message_expired" data-value="<?php echo esc_attr('اشتراک «{planName}» شما به پایان رسید. برای ادامه دسترسی، همین حالا تمدید کنید. {siteName}'); ?>"><?php esc_html_e('انقضا (جذاب)', 'eshterakyar'); ?></button>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- تب بهینه‌سازی -->
        <div id="performance" class="eshterakyar-tab-content">
            <h2><?php esc_html_e('بهینه‌سازی عملکرد', 'eshterakyar'); ?></h2>
            <p class="description"><?php esc_html_e('تنظیمات مربوط به بهینه‌سازی سرعت و عملکرد افزونه', 'eshterakyar'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="enable_cache"><?php esc_html_e('فعال‌سازی کش', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $enable_cache = isset($options['enable_cache']) ? $options['enable_cache'] : 1;
                        ?>
                        <input type="checkbox" name="eshterakyar_options[enable_cache]" id="enable_cache" value="1" <?php checked(1, $enable_cache); ?>>
                        <label for="enable_cache"><?php esc_html_e('استفاده از کش برای بهبود سرعت', 'eshterakyar'); ?></label>
                        <p class="description"><?php esc_html_e('با فعال کردن این گزینه، اطلاعات اشتراک‌ها و دانلودها در کش ذخیره می‌شوند تا سرعت بارگذاری صفحات افزایش یابد.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="cache_duration"><?php esc_html_e('مدت زمان کش (ثانیه)', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <input type="number" name="eshterakyar_options[cache_duration]" id="cache_duration" value="<?php echo isset($options['cache_duration']) ? esc_attr($options['cache_duration']) : 3600; ?>" min="60" max="86400" class="small-text">
                        <p class="description"><?php esc_html_e('مدت زمان نگهداری اطلاعات در کش (پیش‌فرض: 3600 ثانیه = 1 ساعت)', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="optimize_queries"><?php esc_html_e('بهینه‌سازی کوئری‌ها', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $optimize_queries = isset($options['optimize_queries']) ? $options['optimize_queries'] : 1;
                        ?>
                        <input type="checkbox" name="eshterakyar_options[optimize_queries]" id="optimize_queries" value="1" <?php checked(1, $optimize_queries); ?> disabled checked>
                        <label for="optimize_queries"><?php esc_html_e('استفاده از کوئری‌های بهینه (همیشه فعال)', 'eshterakyar'); ?></label>
                        <p class="description"><?php esc_html_e('افزونه به صورت خودکار از کوئری‌های بهینه با استفاده از INDEX و COUNT(DISTINCT) استفاده می‌کند.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label><?php esc_html_e('بهینه‌سازی‌های انجام شده', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <ul class="optimization-list">
                            <li><?php esc_html_e('استفاده از INDEX در جداول دیتابیس برای سرعت بیشتر', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('استفاده از COUNT(DISTINCT) برای محاسبه دقیق دانلودها', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('کش کردن اطلاعات اشتراک‌های فعال', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('بهینه‌سازی بارگذاری فایل‌های CSS و JS', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('استفاده از lazy loading برای تصاویر', 'eshterakyar'); ?></li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php include ESHTERAKYAR_PLUGIN_DIR . 'templates/admin/partials/settings-content-tab.php'; ?>

        <!-- تب شورتکدها -->
        <?php include ESHTERAKYAR_PLUGIN_DIR . 'templates/admin/partials/shortcodes-tab.php'; ?>
        
        <!-- تب متن‌های قابل ویرایش -->
        <div id="labels" class="eshterakyar-tab-content">
            <h2><?php esc_html_e('متن‌های قابل ویرایش', 'eshterakyar'); ?></h2>
            <p class="description"><?php esc_html_e('متن‌های مهمی که در شورتکدها و صفحات (مثل دکمه خرید اشتراک، مشاهده وضعیت و ...) نمایش داده می‌شوند را می‌توانید اینجا تغییر دهید. در صورت خالی گذاشتن هر فیلد، متن پیش‌فرض افزونه استفاده می‌شود.', 'eshterakyar'); ?></p>
            
            <table class="form-table widefat eshterakyar-labels-table">
                <thead>
                    <tr>
                        <th class="label-desc-col"><?php esc_html_e('توضیح', 'eshterakyar'); ?></th>
                        <th class="label-default-col"><?php esc_html_e('متن پیش‌فرض', 'eshterakyar'); ?></th>
                        <th class="label-custom-col"><?php esc_html_e('متن دلخواه شما', 'eshterakyar'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $label_definitions = array(
                        'content_paywall_title' => array(
                            'desc' => __('عنوان بنر «ادامه مطلب» در مقاله', 'eshterakyar'),
                            'default' => __('ادامه مطلب ویژه', 'eshterakyar'),
                        ),
                        'content_paywall_message' => array(
                            'desc' => __('متن بنر برای کاربر لاگین‌شده بدون اشتراک فعال', 'eshterakyar'),
                            'default' => __('برای مطالعه ادامه این مقاله به اشتراک فعال نیاز دارید.', 'eshterakyar'),
                        ),
                        'content_paywall_guest' => array(
                            'desc' => __('متن بنر برای بازدیدکنندهٔ لاگین‌نشده (مهمان)', 'eshterakyar'),
                            'default' => __('برای دیدن ادامه مطلب وارد حساب شوید یا اشتراک تهیه کنید.', 'eshterakyar'),
                        ),
                        'content_buy_subscription' => array(
                            'desc' => __('متن دکمه خرید اشتراک در بنر مقاله', 'eshterakyar'),
                            'default' => __('خرید اشتراک', 'eshterakyar'),
                        ),
                        'buy_subscription' => array(
                            'desc' => __('دکمه / لینک خرید اشتراک', 'eshterakyar'),
                            'default' => __('خرید اشتراک', 'eshterakyar'),
                        ),
                        'buy_subscription_free_download' => array(
                            'desc' => __('دکمه خرید اشتراک و دانلود رایگان (صفحه محصول)', 'eshterakyar'),
                            'default' => __('خرید اشتراک و دانلود رایگان', 'eshterakyar'),
                        ),
                        'renew_subscription' => array(
                            'desc' => __('دکمه تمدید اشتراک', 'eshterakyar'),
                            'default' => __('تمدید اشتراک', 'eshterakyar'),
                        ),
                        'subscription_info' => array(
                            'desc' => __('عنوان بخش اطلاعات اشتراک', 'eshterakyar'),
                            'default' => __('اطلاعات اشتراک', 'eshterakyar'),
                        ),
                        'subscription_badge_active' => array(
                            'desc' => __('متن دکمه [subscription_badge] وقتی کاربر اشتراک فعال دارد', 'eshterakyar'),
                            'default' => __('شما اشتراک دارید!', 'eshterakyar'),
                        ),
                        'subscription_badge_inactive' => array(
                            'desc' => __('متن دکمه [subscription_badge] وقتی کاربر لاگین است ولی اشتراک ندارد', 'eshterakyar'),
                            'default' => __('خرید اشتراک', 'eshterakyar'),
                        ),
                        'subscription_badge_login' => array(
                            'desc' => __('متن دکمه [subscription_badge] برای مهمان (لاگین نشده)', 'eshterakyar'),
                            'default' => __('ورود / خرید اشتراک', 'eshterakyar'),
                        ),
                        'subscription_badge_account_link' => array(
                            'desc' => __('لینک داخل tooltip شورتکد [subscription_badge]', 'eshterakyar'),
                            'default' => __('مشاهده حساب کاربری', 'eshterakyar'),
                        ),
                        'show_subscription_download_info' => array(
                            'desc' => __('دکمه نمایش اطلاعات اشتراک و دانلود (استایل تاشو)', 'eshterakyar'),
                            'default' => __('نمایش اطلاعات اشتراک و دانلود', 'eshterakyar'),
                        ),
                        'download_with_subscription' => array(
                            'desc' => __('دکمه دانلود با اشتراک / دانلود رایگان با اشتراک', 'eshterakyar'),
                            'default' => __('دانلود رایگان با اشتراک', 'eshterakyar'),
                        ),
                        'download_again' => array(
                            'desc' => __('دکمه دانلود مجدد', 'eshterakyar'),
                            'default' => __('دانلود مجدد', 'eshterakyar'),
                        ),
                        'already_downloaded' => array(
                            'desc' => __('پیام «این محصول قبلاً دانلود شده است»', 'eshterakyar'),
                            'default' => __('این محصول قبلاً دانلود شده است', 'eshterakyar'),
                        ),
                        'start_download' => array(
                            'desc' => __('دکمه شروع دانلود (در مودال)', 'eshterakyar'),
                            'default' => __('شروع دانلود', 'eshterakyar'),
                        ),
                        'view_download_details' => array(
                            'desc' => __('دکمه نمایش جزئیات دانلود (بعد از دانلود)', 'eshterakyar'),
                            'default' => __('نمایش جزئیات دانلود', 'eshterakyar'),
                        ),
                        'download_with_subscription_short' => array(
                            'desc' => __('برچسب کوتاه: دانلود با اشتراک (جایی که «دانلود رایگان با اشتراک» طولانی است)', 'eshterakyar'),
                            'default' => __('دانلود با اشتراک', 'eshterakyar'),
                        ),
                        'merge_success_message' => array(
                            'desc' => __('پیام موفقیت بعد از ارتقا با جمع (جمع روز و محدودیت با اشتراک قبلی)', 'eshterakyar'),
                            'default' => __('اشتراک شما با موفقیت ارتقا یافت. روزها و محدودیت دانلود به اشتراک فعلی اضافه شد.', 'eshterakyar'),
                        ),
                        'reserved_subscription_message' => array(
                            'desc' => __('پیام بعد از خرید پلن غیر هم‌گروه (اشتراک رزرو شده)', 'eshterakyar'),
                            'default' => __('اشتراک شما با موفقیت ثبت شد و پس از اتمام اشتراک فعلی به‌صورت خودکار فعال می‌شود.', 'eshterakyar'),
                        ),
                        'reserved_subscription_status_label' => array(
                            'desc' => __('برچسب وضعیت اشتراک رزرو شده در لیست‌ها', 'eshterakyar'),
                            'default' => __('رزرو شده', 'eshterakyar'),
                        ),
                        'renew_note' => array(
                            'desc' => __('توضیح کنار دکمه تمدید اشتراک', 'eshterakyar'),
                            'default' => __('با تمدید اشتراک، محدودیت‌های دانلود شما بازنشانی می‌شود.', 'eshterakyar'),
                        ),
                        'redownload_with_quota' => array(
                            'desc' => __('دکمه دانلود مجدد با کسر اعتبار (وقتی تعداد مجاز هر محصول تمام شده ولی سهمیه روزانه/کلی باقی است)', 'eshterakyar'),
                            'default' => __('دانلود مجدد با کسر اعتبار', 'eshterakyar'),
                        ),
                        'redownload_with_quota_note' => array(
                            'desc' => __('توضیح کنار دکمه «دانلود مجدد با کسر اعتبار»', 'eshterakyar'),
                            'default' => __('با کلیک، یک بار از سهمیه روزانه/کلی شما کسر می‌شود.', 'eshterakyar'),
                        ),
                        'product_out_of_stock_subscription_disabled' => array(
                            'desc' => __('پیام نمایش‌داده‌شده وقتی محصول ناموجود است و ویجت اشتراک غیرفعال می‌شود', 'eshterakyar'),
                            'default' => __('این محصول ناموجود است و امکان اشتراک برای آن فعال نیست.', 'eshterakyar'),
                        ),
                        'error_plan_not_found' => array(
                            'desc' => __('خطا: پلن یافت نشد', 'eshterakyar'),
                            'default' => __('پلن انتخاب‌شده یافت نشد.', 'eshterakyar'),
                        ),
                        'error_subscription_not_found' => array(
                            'desc' => __('خطا: اشتراک یافت نشد', 'eshterakyar'),
                            'default' => __('اشتراک یافت نشد.', 'eshterakyar'),
                        ),
                        'menu_subscriptions' => array(
                            'desc' => __('عنوان منوی «اشتراک‌ها» در پنل کاربری (حساب من ووکامرس)', 'eshterakyar'),
                            'default' => __('اشتراک‌ها', 'eshterakyar'),
                        ),
                        'menu_subscription_downloads' => array(
                            'desc' => __('عنوان منوی «دانلودهای اشتراک» در پنل کاربری (حساب من ووکامرس)', 'eshterakyar'),
                            'default' => __('دانلودهای اشتراک', 'eshterakyar'),
                        ),
                    );
                    foreach ($label_definitions as $key => $def) :
                        $opt_key = 'label_' . $key;
                        $value = isset($options[$opt_key]) ? $options[$opt_key] : '';
                    ?>
                    <tr>
                        <td class="label-desc-col"><?php echo esc_html($def['desc']); ?></td>
                        <td class="label-default-col"><code><?php echo esc_html($def['default']); ?></code></td>
                        <td class="label-custom-col">
                            <input type="text" name="eshterakyar_options[<?php echo esc_attr($opt_key); ?>]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="<?php echo esc_attr__('خالی = استفاده از متن پیش‌فرض', 'eshterakyar'); ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- تب امنیت -->
        <div id="security" class="eshterakyar-tab-content">
            <h2><?php esc_html_e('تنظیمات امنیتی', 'eshterakyar'); ?></h2>
            <p class="description"><?php esc_html_e('تنظیمات مربوط به امنیت و جلوگیری از دسترسی غیرمجاز', 'eshterakyar'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="enable_ip_validation"><?php esc_html_e('بررسی IP Address', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $enable_ip = isset($options['enable_ip_validation']) ? $options['enable_ip_validation'] : 0;
                        ?>
                        <input type="checkbox" name="eshterakyar_options[enable_ip_validation]" id="enable_ip_validation" value="1" <?php checked(1, $enable_ip); ?>>
                        <label for="enable_ip_validation"><?php esc_html_e('بررسی تغییر IP Address در هنگام دانلود', 'eshterakyar'); ?></label>
                        <p class="description"><?php esc_html_e('در صورت فعال بودن، اگر IP کاربر تغییر کند، دسترسی به دانلود محدود می‌شود. (توصیه می‌شود برای امنیت بیشتر فعال باشد)', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="rate_limit_enabled"><?php esc_html_e('محدودیت نرخ درخواست', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $rate_limit = isset($options['rate_limit_enabled']) ? (int) $options['rate_limit_enabled'] : 1;
                        $rate_limit_max = isset($options['rate_limit_max']) ? absint($options['rate_limit_max']) : 10;
                        if ($rate_limit_max < 1) {
                            $rate_limit_max = 10;
                        }
                        ?>
                        <input type="checkbox" name="eshterakyar_options[rate_limit_enabled]" id="rate_limit_enabled" value="1" <?php checked(1, $rate_limit); ?>>
                        <label for="rate_limit_enabled"><?php esc_html_e('محدود کردن تعداد درخواست‌های دانلود در بازه زمانی کوتاه', 'eshterakyar'); ?></label>
                        <p class="description" style="margin-top:10px;">
                            <label for="rate_limit_max"><strong><?php esc_html_e('حداکثر درخواست در هر دقیقه:', 'eshterakyar'); ?></strong></label>
                            <input type="number" name="eshterakyar_options[rate_limit_max]" id="rate_limit_max" value="<?php echo esc_attr((string) $rate_limit_max); ?>" min="1" max="500" class="small-text" style="margin-right:8px;">
                        </p>
                        <p class="description"><?php esc_html_e('در صورت غیرفعال بودن، محدودیت نرخ اعمال نمی‌شود. پیش‌فرض پیشنهادی: ۱۰ درخواست در دقیقه برای هر کاربر/IP.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="download_token_max_uses"><?php esc_html_e('توکن لینک دانلود', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $token_max_uses = isset($options['download_token_max_uses']) ? absint($options['download_token_max_uses']) : 5;
                        if ($token_max_uses < 1) {
                            $token_max_uses = 5;
                        }
                        $token_expiry_hours = isset($options['download_token_expiry_hours']) ? absint($options['download_token_expiry_hours']) : 24;
                        if ($token_expiry_hours < 1) {
                            $token_expiry_hours = 24;
                        }
                        ?>
                        <p>
                            <label for="download_token_max_uses"><strong><?php esc_html_e('حداکثر دفعات استفاده از هر لینک توکن:', 'eshterakyar'); ?></strong></label>
                            <input type="number" name="eshterakyar_options[download_token_max_uses]" id="download_token_max_uses" value="<?php echo esc_attr((string) $token_max_uses); ?>" min="1" max="100" class="small-text" style="margin-right:8px;">
                        </p>
                        <p class="description"><?php esc_html_e('اگر روی همان لینک چند بار کلیک شود (یا مرورگر/دانلودمنیجر دوباره درخواست بزند)، با مقدار ۱ خطای «توکن نامعتبر یا منقضی» می‌آید. مقدار ۵ تا ۱۰ معمولاً مناسب است.', 'eshterakyar'); ?></p>
                        <p style="margin-top:12px;">
                            <label for="download_token_expiry_hours"><strong><?php esc_html_e('اعتبار توکن (ساعت):', 'eshterakyar'); ?></strong></label>
                            <input type="number" name="eshterakyar_options[download_token_expiry_hours]" id="download_token_expiry_hours" value="<?php echo esc_attr((string) $token_expiry_hours); ?>" min="1" max="720" class="small-text" style="margin-right:8px;">
                        </p>
                        <p class="description"><?php esc_html_e('پس از این مدت لینک توکن منقضی می‌شود و باید دوباره از صفحه محصول لینک جدید گرفته شود. پیش‌فرض: ۲۴ ساعت.', 'eshterakyar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label><?php esc_html_e('اقدامات امنیتی انجام شده', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <ul class="security-features-list">
                            <li><?php esc_html_e('استفاده از Nonce برای جلوگیری از CSRF', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('بررسی دسترسی کاربر قبل از دانلود', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('بررسی اعتبار URL دانلود', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('Rate Limiting برای جلوگیری از سوء استفاده', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('ثبت IP Address و User Agent برای هر دانلود', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('بررسی دسته‌بندی و تگ محصول', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('بررسی فعال بودن اشتراک', 'eshterakyar'); ?></li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- تب قوانین و مقررات -->
        <div id="terms" class="eshterakyar-tab-content">
            <h2><?php esc_html_e('قوانین و مقررات اشتراک', 'eshterakyar'); ?></h2>
            <p class="description"><?php esc_html_e('متن قوانین و مقررات که در صفحه خرید اشتراک نمایش داده می‌شود.', 'eshterakyar'); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="subscription_terms"><?php esc_html_e('متن قوانین و مقررات', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $terms_content = isset($options['subscription_terms']) ? $options['subscription_terms'] : '';
                        wp_editor(
                            $terms_content,
                            'subscription_terms',
                            array(
                                'textarea_name' => 'eshterakyar_options[subscription_terms]',
                                'textarea_rows' => 15,
                                'media_buttons' => true,
                                'teeny' => false,
                                'tinymce' => true,
                                'quicktags' => true,
                            )
                        );
                        ?>
                        <p class="description">
                            <?php esc_html_e('این متن در بخش "قوانین و مقررات" صفحه خرید اشتراک نمایش داده می‌شود. می‌توانید از ویرایشگر وردپرس برای فرمت‌بندی استفاده کنید.', 'eshterakyar'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="subscription_terms_position"><?php esc_html_e('موقعیت نمایش قوانین و مقررات', 'eshterakyar'); ?></label>
                    </th>
                    <td>
                        <?php
                        $terms_position = isset($options['subscription_terms_position']) ? $options['subscription_terms_position'] : 'top';
                        ?>
                        <fieldset>
                            <label>
                                <input type="radio" name="eshterakyar_options[subscription_terms_position]" value="top" <?php checked($terms_position, 'top'); ?>>
                                <?php esc_html_e('بالا (قبل از پلن‌ها)', 'eshterakyar'); ?>
                            </label>
                            <br>
                            <label>
                                <input type="radio" name="eshterakyar_options[subscription_terms_position]" value="bottom" <?php checked($terms_position, 'bottom'); ?>>
                                <?php esc_html_e('پایین (بعد از پلن‌ها)', 'eshterakyar'); ?>
                            </label>
                        </fieldset>
                        <p class="description">
                            <?php esc_html_e('انتخاب کنید که متن قوانین و مقررات در بالا (قبل از پلن‌ها) یا پایین (بعد از پلن‌ها) نمایش داده شود.', 'eshterakyar'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- تب راهنما -->
        <div id="help" class="eshterakyar-tab-content">
            <h2><?php esc_html_e('راهنمای کامل افزونه', 'eshterakyar'); ?></h2>
            
            <div class="eshterakyar-help-sections">
                <h3><?php esc_html_e('۱. نصب و راه‌اندازی', 'eshterakyar'); ?></h3>
                <ol class="help-steps-list">
                    <li><?php esc_html_e('افزونه را در پوشه wp-content/plugins نصب کنید', 'eshterakyar'); ?></li>
                    <li><?php esc_html_e('افزونه را از پنل مدیریت وردپرس فعال کنید', 'eshterakyar'); ?></li>
                    <li><?php esc_html_e('اطمینان حاصل کنید که ووکامرس نصب و فعال است', 'eshterakyar'); ?></li>
                    <li><?php esc_html_e('به بخش "اشتراک یار > پلن‌ها" بروید و پلن‌های اشتراک را ایجاد کنید', 'eshterakyar'); ?></li>
                </ol>
                
                <h3><?php esc_html_e('۲. ایجاد پلن اشتراک', 'eshterakyar'); ?></h3>
                <ol class="help-steps-list">
                    <li><?php esc_html_e('به بخش "اشتراک یار > پلن‌ها" بروید', 'eshterakyar'); ?></li>
                    <li><?php esc_html_e('روی "افزودن پلن جدید" کلیک کنید', 'eshterakyar'); ?></li>
                    <li><?php esc_html_e('اطلاعات پلن را وارد کنید:', 'eshterakyar'); ?>
                        <ul class="help-sub-list">
                            <li><?php esc_html_e('نام پلن', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('قیمت (به تومان و بدون اعشار)', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('مدت زمان (روز)', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('محدودیت دانلود روزانه (0 = نامحدود)', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('محدودیت دانلود کلی (0 = نامحدود)', 'eshterakyar'); ?></li>
                            <li><strong><?php esc_html_e('محصول مرتبط (ضروری)', 'eshterakyar'); ?></strong> - <?php esc_html_e('یک محصول WooCommerce انتخاب کنید که با خرید آن، اشتراک برای کاربر فعال می‌شود', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('متن نشان (Badge) - اختیاری', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('رنگ نشان - اختیاری', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('ویژگی‌های سفارشی - اختیاری', 'eshterakyar'); ?></li>
                        </ul>
                    </li>
                </ol>
                
                <h3><?php esc_html_e('۳. صفحه محصول و دکمه‌های دانلود', 'eshterakyar'); ?></h3>
                <div class="help-notice-box" style="background: #e8f5e9; border-right: 4px solid #4caf50; padding: 15px; margin: 15px 0; border-radius: 4px;">
                    <h4 style="margin-top: 0; color: #2e7d32;"><?php esc_html_e('نمایش دکمه‌ها در صفحه محصول', 'eshterakyar'); ?></h4>
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('افزونه به صورت خودکار در صفحه محصول WooCommerce دکمه‌های مربوط به اشتراک و دانلود را نمایش می‌دهد:', 'eshterakyar'); ?></p>
                    
                    <h5 style="color: #2e7d32; margin-top: 20px;"><?php esc_html_e('اگر کاربر اشتراک ندارد:', 'eshterakyar'); ?></h5>
                    <ul style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('دکمه "خرید اشتراک و دانلود رایگان" - کاربر را به صفحه خرید اشتراک هدایت می‌کند', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('پیام توضیحی: "برای دانلود رایگان این محصول نیاز به اشتراک فعال دارید."', 'eshterakyar'); ?></li>
                    </ul>
                    
                    <h5 style="color: #2e7d32; margin-top: 20px;"><?php esc_html_e('اگر کاربر اشتراک فعال دارد:', 'eshterakyar'); ?></h5>
                    <ul style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('عنوان: "دانلود رایگان با اشتراک"', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('نام اشتراک کاربر', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('روزهای باقی‌مانده تا انقضای اشتراک', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('تعداد دانلود باقی‌مانده (یا "نامحدود" در صورت عدم محدودیت)', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('تعداد دانلود امروز (در صورت وجود محدودیت روزانه)', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('دکمه "دانلود رایگان با اشتراک" یا "دانلود مجدد" (اگر قبلاً دانلود شده باشد)', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('بج "دانلود شده" در صورت دانلود قبلی محصول', 'eshterakyar'); ?></li>
                    </ul>
                    
                    <h5 style="color: #2e7d32; margin-top: 20px;"><?php esc_html_e('موقعیت نمایش:', 'eshterakyar'); ?></h5>
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('دکمه‌ها و اطلاعات اشتراک به صورت خودکار در زیر دکمه "افزودن به سبد خرید" در صفحه محصول نمایش داده می‌شوند.', 'eshterakyar'); ?></p>
                    
                    <h5 style="color: #2e7d32; margin-top: 20px;"><?php esc_html_e('سفارشی‌سازی:', 'eshterakyar'); ?></h5>
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('اگر می‌خواهید موقعیت یا استایل دکمه‌ها را تغییر دهید، می‌توانید از CSS سفارشی استفاده کنید:', 'eshterakyar'); ?></p>
                    <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left; margin-top: 10px;"><code>/* تغییر رنگ دکمه خرید اشتراک */
.eshterakyar-buy-subscription-button {
    background: #your-color !important;
}

/* تغییر رنگ دکمه دانلود */
.eshterakyar-download-button {
    background: #your-color !important;
}

/* تغییر استایل بخش اشتراک */
.eshterakyar-subscription-status {
    background: #your-color !important;
}</code></pre>
                    
                    <h5 style="color: #2e7d32; margin-top: 20px;"><?php esc_html_e('استفاده از شورتکد:', 'eshterakyar'); ?></h5>
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('اگر می‌خواهید دکمه‌ها را در موقعیت دیگری نمایش دهید، می‌توانید از شورتکد زیر استفاده کنید:', 'eshterakyar'); ?></p>
                    <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left; margin-top: 10px;"><code>[product_download_status]</code></pre>
                    <p style="color: #333; margin: 10px 0; font-size: 13px;"><?php esc_html_e('این شورتکد همان عملکرد دکمه‌های خودکار را دارد و می‌توانید آن را در هر جای صفحه محصول (یا صفحات دیگر) قرار دهید.', 'eshterakyar'); ?></p>
                </div>

                <h3><?php esc_html_e('۳.۱. دسترسی جمع‌شونده پلن‌ها به دانلود محصول', 'eshterakyar'); ?></h3>
                <div class="help-notice-box" style="background: #fff8e1; border-right: 4px solid #ff9800; padding: 15px; margin: 15px 0; border-radius: 4px;">
                    <h4 style="margin-top: 0; color: #e65100;"><?php esc_html_e('دسترسی سلسله‌مراتبی (برنزی، نقره‌ای، طلایی)', 'eshterakyar'); ?></h4>
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('در صفحه ویرایش هر محصول WooCommerce، متاباکس «تنظیمات اشتراک یار» به شما اجازه می‌دهد مشخص کنید این محصول با کدام پلن‌های اشتراک قابل دانلود است. به‌صورت پیش‌فرض، فقط پلن‌هایی که مستقیماً تیک خورده‌اند دسترسی دارند.', 'eshterakyar'); ?></p>
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('با فعال کردن گزینه «دسترسی جمع‌شونده پلن‌ها به دانلود محصول» در تنظیمات عمومی، می‌توانید منطق سلسله‌مراتبی فعال کنید: انتخاب بالاترین پلن، به‌صورت خودکار پلن‌های پایین‌تر را هم شامل می‌شود.', 'eshterakyar'); ?></p>

                    <h5 style="color: #e65100; margin-top: 20px;"><?php esc_html_e('تنظیم سلسله‌مراتب پلن‌ها', 'eshterakyar'); ?></h5>
                    <ol style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('به «اشتراک یار > پلن‌ها» بروید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('با drag & drop ترتیب پلن‌ها را تنظیم کنید: پلن پایین‌تر (مثلاً برنزی) بالاتر در لیست، پلن بالاتر (مثلاً طلایی) پایین‌تر در لیست', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('این ترتیب، سطح (tier) هر پلن را برای دسترسی جمع‌شونده تعیین می‌کند', 'eshterakyar'); ?></li>
                    </ol>

                    <h5 style="color: #e65100; margin-top: 20px;"><?php esc_html_e('فعال‌سازی', 'eshterakyar'); ?></h5>
                    <ol style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('به «اشتراک یار > تنظیمات > تنظیمات عمومی» بروید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('گزینه «دسترسی جمع‌شونده پلن‌ها به دانلود محصول» را فعال کنید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('در صفحه محصول، «این محصول با اشتراک قابل دانلود است» را فعال و «پلن‌های خاص» را انتخاب کنید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('فقط بالاترین پلن مورد نظر را تیک بزنید — پلن‌های پایین‌تر خودکار شامل می‌شوند', 'eshterakyar'); ?></li>
                    </ol>

                    <h5 style="color: #e65100; margin-top: 20px;"><?php esc_html_e('مثال‌های عملی', 'eshterakyar'); ?></h5>
                    <table style="width: 100%; border-collapse: collapse; margin: 10px 0; background: #fff;">
                        <thead>
                            <tr style="background: #ffe0b2;">
                                <th style="border: 1px solid #ffcc80; padding: 8px; text-align: right;"><?php esc_html_e('پلن‌های تیک‌خورده روی محصول', 'eshterakyar'); ?></th>
                                <th style="border: 1px solid #ffcc80; padding: 8px; text-align: right;"><?php esc_html_e('چه کسانی می‌توانند دانلود کنند', 'eshterakyar'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="border: 1px solid #ffcc80; padding: 8px;"><?php esc_html_e('فقط طلایی', 'eshterakyar'); ?></td>
                                <td style="border: 1px solid #ffcc80; padding: 8px;"><?php esc_html_e('برنزی + نقره‌ای + طلایی', 'eshterakyar'); ?></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ffcc80; padding: 8px;"><?php esc_html_e('فقط نقره‌ای', 'eshterakyar'); ?></td>
                                <td style="border: 1px solid #ffcc80; padding: 8px;"><?php esc_html_e('برنزی + نقره‌ای', 'eshterakyar'); ?></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ffcc80; padding: 8px;"><?php esc_html_e('فقط برنزی', 'eshterakyar'); ?></td>
                                <td style="border: 1px solid #ffcc80; padding: 8px;"><?php esc_html_e('فقط برنزی', 'eshterakyar'); ?></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ffcc80; padding: 8px;"><?php esc_html_e('برنزی + طلایی', 'eshterakyar'); ?></td>
                                <td style="border: 1px solid #ffcc80; padding: 8px;"><?php esc_html_e('همه پلن‌ها (حداکثر = طلایی)', 'eshterakyar'); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="background: #e3f2fd; border: 1px solid #2196f3; padding: 12px; border-radius: 4px; margin-top: 15px;">
                        <p style="margin: 0; color: #1565c0;"><strong><?php esc_html_e('نکات مهم:', 'eshterakyar'); ?></strong></p>
                        <ul style="margin: 8px 0 0 0; padding-right: 20px; color: #1565c0;">
                            <li><?php esc_html_e('این قابلیت فقط برای محصولاتی که «پلن‌های خاص» انتخاب شده اعمال می‌شود (نه «همه پلن‌ها»)', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('اگر تنظیم غیرفعال باشد، فقط پلن‌های مستقیماً تیک‌خورده دسترسی دارند (رفتار قبلی)', 'eshterakyar'); ?></li>
                            <li><?php esc_html_e('دسترسی مبتنی بر دسته‌بندی/تگ (وقتی «دانلود با اشتراک» روی محصول فعال نیست) تحت تأثیر این تنظیم قرار نمی‌گیرد', 'eshterakyar'); ?></li>
                        </ul>
                    </div>
                </div>

                <h3><?php esc_html_e('۳.۲. محدودسازی ادامه مطلب (محتوای ویژه مقالات)', 'eshterakyar'); ?></h3>
                <div class="help-notice-box" style="background: #f3e5f5; border-right: 4px solid #9c27b0; padding: 15px; margin: 15px 0; border-radius: 4px;">
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('با تب «محتوای ویژه» می‌توانید مثل سایت‌های اشتراک‌محور عمل کنید: بخشی از مقاله برای همه رایگان است و ادامه فقط برای کسانی که اشتراک فعال دارند.', 'eshterakyar'); ?></p>
                    <h5 style="color: #6a1b9a; margin-top: 15px;"><?php esc_html_e('اولویت تصمیم‌گیری (از کلی به جزئی)', 'eshterakyar'); ?></h5>
                    <ol style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('تنظیمات افزونه: روشن/خاموش بودن قابلیت و نحوه نمایش (بنر در مقاله یا هدایت به صفحه پلن‌ها)', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('فرم هر پلن: دسته و برچسب مقاله (جدا از دسته محصولات ووکامرس)', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('ویرایش هر مقاله: متاباکس «محتوای ویژه» — محدود بودن، پلن‌های مجاز، نحوه نمایش', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('متن مقاله: شورتکد جداکننده [eshterakyar_paywall] بلافاصله بعد از بخش رایگان', 'eshterakyar'); ?></li>
                    </ol>
                    <h5 style="color: #6a1b9a; margin-top: 15px;"><?php esc_html_e('شروع سریع', 'eshterakyar'); ?></h5>
                    <ol style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('مسیر: اشتراک یار ← تنظیمات ← محتوای ویژه — گزینه محدودسازی را فعال کنید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('در صورت نیاز، در فرم پلن دسته و برچسب مقاله مجاز را مشخص کنید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('در ویرایش مقاله، متاباکس محتوای ویژه را تیک بزنید و شورتکد جداکننده را بعد از پیش‌نمایش بگذارید', 'eshterakyar'); ?></li>
                    </ol>
                    <p style="margin: 10px 0; color: #555;"><strong><?php esc_html_e('نکته:', 'eshterakyar'); ?></strong> <?php esc_html_e('در لیست مقالات و فید سایت فقط پیش‌نمایش دیده می‌شود. اگر مقاله با المنتور ساخته شده، یک‌بار در حالت مهمان یا ناشناس تست کنید.', 'eshterakyar'); ?></p>
                </div>

                <h3><?php esc_html_e('۳.۳. عضویت خودکار در اشتراک', 'eshterakyar'); ?></h3>
                <div class="help-notice-box" style="background: #e8f5e9; border-right: 4px solid #4caf50; padding: 15px; margin: 15px 0; border-radius: 4px;">
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('با این قابلیت، کاربرانی که اشتراک فعال ندارند (از جمله تازه‌ثبت‌نام‌شده‌ها یا کاربران با اشتراک منقضی‌شده) می‌توانند به‌صورت خودکار عضو پلن انتخاب‌شده — مثلاً پلن رایگان — شوند.', 'eshterakyar'); ?></p>
                    <h5 style="color: #2e7d32; margin-top: 15px;"><?php esc_html_e('راه‌اندازی گام‌به‌گام', 'eshterakyar'); ?></h5>
                    <ol style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('در «اشتراک یار → پلن‌ها» یک پلن بسازید (برای عضویت رایگان: قیمت ۰)', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('به «اشتراک یار → تنظیمات → عضویت خودکار» بروید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('قابلیت را فعال کنید و پلن هدف را انتخاب کنید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('برای کاربران جدید: گزینه «کاربران جدید» را فعال کنید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('برای کاربران قدیمی: «کاربران موجود» را فعال کنید، تنظیمات را ذخیره کنید، سپس «اجرا برای کاربران واجد شرایط» را بزنید', 'eshterakyar'); ?></li>
                    </ol>
                    <h5 style="color: #2e7d32; margin-top: 15px;"><?php esc_html_e('جدول تنظیمات', 'eshterakyar'); ?></h5>
                    <table class="widefat" style="margin-top: 10px; background: #fff;">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('تنظیم', 'eshterakyar'); ?></th>
                                <th><?php esc_html_e('توضیح', 'eshterakyar'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td><?php esc_html_e('فعال‌سازی', 'eshterakyar'); ?></td><td><?php esc_html_e('روشن/خاموش کردن کل قابلیت', 'eshterakyar'); ?></td></tr>
                            <tr><td><?php esc_html_e('پلن اشتراک', 'eshterakyar'); ?></td><td><?php esc_html_e('پلنی که به‌صورت خودکار اختصاص می‌یابد', 'eshterakyar'); ?></td></tr>
                            <tr><td><?php esc_html_e('کاربران جدید', 'eshterakyar'); ?></td><td><?php esc_html_e('عضویت بلافاصله پس از ثبت‌نام', 'eshterakyar'); ?></td></tr>
                            <tr><td><?php esc_html_e('کاربران موجود', 'eshterakyar'); ?></td><td><?php esc_html_e('امکان اجرای دسته‌ای برای کاربران بدون اشتراک فعال', 'eshterakyar'); ?></td></tr>
                            <tr><td><?php esc_html_e('اشتراک رزرو', 'eshterakyar'); ?></td><td><?php esc_html_e('رد کاربرانی که اشتراک در صف دارند', 'eshterakyar'); ?></td></tr>
                            <tr><td><?php esc_html_e('نقش‌های مستثنی', 'eshterakyar'); ?></td><td><?php esc_html_e('مثلاً administrator,shop_manager', 'eshterakyar'); ?></td></tr>
                            <tr><td><?php esc_html_e('اعلان خوش‌آمد', 'eshterakyar'); ?></td><td><?php esc_html_e('ارسال ایمیل/پیامک خرید (پیش‌فرض: خاموش)', 'eshterakyar'); ?></td></tr>
                            <tr><td><?php esc_html_e('همگام‌سازی روزانه', 'eshterakyar'); ?></td><td><?php esc_html_e('بررسی روزانه تا ۵۰ کاربر جاافتاده', 'eshterakyar'); ?></td></tr>
                        </tbody>
                    </table>
                    <h5 style="color: #2e7d32; margin-top: 15px;"><?php esc_html_e('عیب‌یابی', 'eshterakyar'); ?></h5>
                    <ul style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('کاربر اشتراک فعال دارد → عضویت خودکار اجرا نمی‌شود', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('نقش کاربر در لیست مستثنی است → رد می‌شود', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('پلن غیرفعال یا حذف شده → هشدار در پنل ادمین نمایش داده می‌شود', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('کاربر قدیمی عضو نشد → «کاربران موجود» را فعال کنید و دکمه اجرای دسته‌ای را بزنید', 'eshterakyar'); ?></li>
                    </ul>
                    <?php if (function_exists('eshterakyar_render_code_block')) : ?>
                        <h5 style="color: #2e7d32; margin-top: 15px;"><?php esc_html_e('هوک توسعه‌دهنده', 'eshterakyar'); ?></h5>
                        <?php
                        eshterakyar_render_code_block(
                            "add_action('eshterakyar_auto_membership_assigned', function(\$subscription_id, \$user_id, \$plan_id, \$context) {\n    // \$context: registration, batch, cron\n}, 10, 4);",
                            array(
                                'title' => 'eshterakyar_auto_membership_assigned',
                                'lang'  => 'php',
                            )
                        );
                        ?>
                    <?php endif; ?>
                </div>
                
                <h3><?php esc_html_e('۴. راهنمای محصول مرتبط', 'eshterakyar'); ?></h3>
                <div class="help-notice-box" style="background: #e3f2fd; border-right: 4px solid #2196f3; padding: 15px; margin: 15px 0; border-radius: 4px;">
                    <h4 style="margin-top: 0; color: #1976d2;"><?php esc_html_e('چرا محصول مرتبط مهم است؟', 'eshterakyar'); ?></h4>
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('برای فعال شدن اشتراک برای کاربر، باید یک محصول WooCommerce به پلن مرتبط شود. فرآیند کار به این صورت است:', 'eshterakyar'); ?></p>
                    <ol style="margin: 10px 0; padding-right: 20px;">
                        <li><strong><?php esc_html_e('ایجاد محصول در WooCommerce:', 'eshterakyar'); ?></strong>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><?php esc_html_e('به بخش "محصولات > افزودن محصول" بروید', 'eshterakyar'); ?></li>
                                <li><?php esc_html_e('یک محصول جدید ایجاد کنید (مثلاً "اشتراک طلایی 1 ماهه")', 'eshterakyar'); ?></li>
                                <li><?php esc_html_e('قیمت محصول را تنظیم کنید', 'eshterakyar'); ?></li>
                                <li><?php esc_html_e('محصول را منتشر کنید', 'eshterakyar'); ?></li>
                            </ul>
                        </li>
                        <li><strong><?php esc_html_e('مرتبط کردن محصول با پلن:', 'eshterakyar'); ?></strong>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><?php esc_html_e('در فرم ایجاد/ویرایش پلن، در بخش "محصول مرتبط"', 'eshterakyar'); ?></li>
                                <li><?php esc_html_e('محصولی که ایجاد کردید را انتخاب کنید', 'eshterakyar'); ?></li>
                                <li><?php esc_html_e('با خرید این محصول، اشتراک به صورت خودکار برای کاربر فعال می‌شود', 'eshterakyar'); ?></li>
                            </ul>
                        </li>
                        <li><strong><?php esc_html_e('فرآیند فعال‌سازی:', 'eshterakyar'); ?></strong>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><?php esc_html_e('کاربر محصول را به سبد خرید اضافه می‌کند', 'eshterakyar'); ?></li>
                                <li><?php esc_html_e('سفارش را تکمیل می‌کند', 'eshterakyar'); ?></li>
                                <li><?php esc_html_e('بعد از تکمیل سفارش (وضعیت "تکمیل شده")', 'eshterakyar'); ?></li>
                                <li><strong><?php esc_html_e('اشتراک به صورت خودکار برای کاربر فعال می‌شود', 'eshterakyar'); ?></strong></li>
                                <li><?php esc_html_e('کاربر می‌تواند از امکانات اشتراک استفاده کند', 'eshterakyar'); ?></li>
                            </ul>
                        </li>
                    </ol>
                    <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('نکات مهم:', 'eshterakyar'); ?></strong></p>
                    <ul style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('هر پلن می‌تواند یک محصول مرتبط داشته باشد', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('اگر محصول مرتبط تنظیم نشود، کاربر نمی‌تواند اشتراک را خریداری کند', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('محصول مرتبط باید قابل خرید باشد (is_purchasable = true)', 'eshterakyar'); ?></li>
                        <li><strong><?php esc_html_e('قیمت پلن و محصول مرتبط:', 'eshterakyar'); ?></strong> <?php esc_html_e('قیمت پلن در افزونه فقط برای نمایش و مقایسه استفاده می‌شود. قیمت واقعی که کاربر پرداخت می‌کند، همان قیمت محصول WooCommerce است. توصیه می‌شود قیمت پلن و محصول مرتبط یکسان باشند تا کاربر دچار سردرگمی نشود. کاربر در صفحه پرداخت، قیمت محصول را می‌بیند و پرداخت می‌کند، نه قیمت پلن.', 'eshterakyar'); ?></li>
                    </ul>
                    
                    <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 4px; margin-top: 15px;">
                        <p style="margin: 0; color: #856404;"><strong><?php esc_html_e('⚠️ نکته مهم درباره قیمت:', 'eshterakyar'); ?></strong></p>
                        <p style="margin: 10px 0 0 0; color: #856404;"><?php esc_html_e('قیمتی که در پلن اشتراک تنظیم می‌کنید، فقط برای نمایش در صفحه پلن‌ها استفاده می‌شود. قیمت واقعی که کاربر در صفحه پرداخت می‌بیند و پرداخت می‌کند، همان قیمت محصول WooCommerce است که در بخش "محصول مرتبط" انتخاب کرده‌اید. بنابراین حتماً قیمت محصول WooCommerce را با قیمت پلن یکسان تنظیم کنید تا کاربر دچار سردرگمی نشود.', 'eshterakyar'); ?></p>
                    </div>
                </div>
                
                <h3><?php esc_html_e('۵. طراحی صفحات با المنتور', 'eshterakyar'); ?></h3>
                <div class="help-notice-box" style="background: #f3e5f5; border-right: 4px solid #9c27b0; padding: 15px; margin: 15px 0; border-radius: 4px;">
                    <h4 style="margin-top: 0; color: #7b1fa2;"><?php esc_html_e('طراحی صفحه خرید اشتراک با المنتور', 'eshterakyar'); ?></h4>
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('برای طراحی صفحه خرید اشتراک با المنتور، دو روش دارید:', 'eshterakyar'); ?></p>
                    
                    <h5 style="color: #7b1fa2; margin-top: 20px;"><?php esc_html_e('روش 1: استفاده از شورتکد (ساده و سریع)', 'eshterakyar'); ?></h5>
                    <ol style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('به بخش "صفحات" بروید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('صفحه "پلن‌ها و قیمت‌گذاری" را پیدا کنید (یا یک صفحه جدید ایجاد کنید)', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('روی "ویرایش با المنتور" کلیک کنید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('از ویجت "Shortcode" المنتور استفاده کنید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('شورتکد زیر را در ویجت قرار دهید:', 'eshterakyar'); ?>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left; margin-top: 10px;"><code>[eshterakyar_subscription_plans]</code></pre>
                        </li>
                        <li><?php esc_html_e('این شورتکد به صورت خودکار تمام پلن‌های فعال را با طراحی پیش‌فرض نمایش می‌دهد', 'eshterakyar'); ?></li>
                    </ol>
                    
                    <div style="background: #e3f2fd; border: 1px solid #2196f3; padding: 15px; border-radius: 4px; margin-top: 15px;">
                        <p style="margin: 0 0 10px 0; color: #1565c0;"><strong><?php esc_html_e('پارامترهای شورتکد:', 'eshterakyar'); ?></strong></p>
                        <ul style="margin: 5px 0; padding-right: 20px; color: #1565c0;">
                            <li><code>show_header="true"</code> - <?php esc_html_e('نمایش هدر صفحه (عنوان و اطلاعات اشتراک فعلی)', 'eshterakyar'); ?></li>
                            <li><code>show_terms="true"</code> - <?php esc_html_e('نمایش قوانین و مقررات', 'eshterakyar'); ?></li>
                            <li><code>terms_content="متن قوانین..."</code> - <?php esc_html_e('متن سفارشی قوانین و مقررات (اختیاری - اگر خالی باشد از تنظیمات استفاده می‌شود)', 'eshterakyar'); ?></li>
                            <li><code>terms_position="top"</code> - <?php esc_html_e('موقعیت نمایش قوانین: "top" برای بالا یا "bottom" برای پایین', 'eshterakyar'); ?></li>
                        </ul>
                        <p style="margin: 10px 0 0 0; color: #1565c0;"><strong><?php esc_html_e('مثال‌های استفاده:', 'eshterakyar'); ?></strong></p>
                        <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left; margin-top: 10px; font-size: 12px;"><code>[eshterakyar_subscription_plans show_header="true" show_terms="true"]

[eshterakyar_subscription_plans show_terms="yes" terms_content="متن قوانین شما..." terms_position="bottom"]</code></pre>
                        <p style="margin: 10px 0 0 0; font-size: 13px; color: #1565c0;"><?php esc_html_e('💡 نکته: اگر پارامتر terms_content را در شورتکد قرار ندهید، از متن قوانین و مقررات که در بخش "قوانین و مقررات" تنظیمات وارد کرده‌اید استفاده می‌شود.', 'eshterakyar'); ?></p>
                    </div>
                    
                    <h5 style="color: #7b1fa2; margin-top: 20px;"><?php esc_html_e('روش 2: طراحی کاملاً سفارشی', 'eshterakyar'); ?></h5>
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('اگر می‌خواهید صفحه را کاملاً سفارشی طراحی کنید:', 'eshterakyar'); ?></p>
                    <ol style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('صفحه جدیدی در المنتور ایجاد کنید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('از ویجت‌های المنتور (مثلاً Heading، Text، Image) برای طراحی کارت‌های پلن استفاده کنید', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('برای دکمه خرید اشتراک، از ویجت "Button" المنتور استفاده کنید', 'eshterakyar'); ?></li>
                        <li><strong><?php esc_html_e('مهم: لینک دکمه خرید:', 'eshterakyar'); ?></strong>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><?php esc_html_e('لینک دکمه باید به صفحه محصول مرتبط با پلن اشاره کند', 'eshterakyar'); ?></li>
                                <li><?php esc_html_e('برای افزودن مستقیم به سبد خرید، از لینک زیر استفاده کنید:', 'eshterakyar'); ?>
                                    <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left; margin-top: 10px;"><code>?add-to-cart=PRODUCT_ID</code></pre>
                                    <p style="margin-top: 5px; font-size: 13px; color: #666;"><?php esc_html_e('به جای PRODUCT_ID، شناسه محصول مرتبط با پلن را وارد کنید. این شناسه را می‌توانید از بخش "محصولات" در ووکامرس پیدا کنید.', 'eshterakyar'); ?></p>
                                </li>
                                <li><?php esc_html_e('مثال: اگر شناسه محصول شما 123 است، لینک دکمه باید این باشد:', 'eshterakyar'); ?>
                                    <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left; margin-top: 10px;"><code>?add-to-cart=123</code></pre>
                                </li>
                                <li><?php esc_html_e('یا می‌توانید لینک کامل صفحه محصول را بگذارید:', 'eshterakyar'); ?>
                                    <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left; margin-top: 10px;"><code>https://yoursite.com/product/product-name/</code></pre>
                                </li>
                            </ul>
                        </li>
                        <li><?php esc_html_e('بعد از خرید محصول توسط کاربر، اشتراک به صورت خودکار برای او فعال می‌شود', 'eshterakyar'); ?></li>
                    </ol>
                    
                    <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 10px; border-radius: 4px; margin-top: 15px;">
                        <p style="margin: 0;"><strong><?php esc_html_e('نکته مهم:', 'eshterakyar'); ?></strong> <?php esc_html_e('برای پیدا کردن شناسه محصول مرتبط با هر پلن، به بخش "اشتراک یار > پلن‌ها" بروید و پلن مورد نظر را ویرایش کنید. در بخش "محصول مرتبط"، شناسه محصول نمایش داده می‌شود.', 'eshterakyar'); ?></p>
                    </div>
                    
                    <h4 style="margin-top: 20px; color: #7b1fa2;"><?php esc_html_e('طراحی صفحه حساب کاربری با المنتور', 'eshterakyar'); ?></h4>
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('می‌توانید تب‌های "اشتراک‌ها" و "دانلودها" را با المنتور طراحی کنید:', 'eshterakyar'); ?></p>
                    <ul style="margin: 10px 0; padding-right: 20px;">
                        <li><strong><?php esc_html_e('شورتکدهای موجود:', 'eshterakyar'); ?></strong>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>[user_subscriptions]</code> - <?php esc_html_e('نمایش لیست اشتراک‌های کاربر', 'eshterakyar'); ?></li>
                                <li><code>[subscription_downloads]</code> - <?php esc_html_e('نمایش لیست دانلودها', 'eshterakyar'); ?></li>
                                <li><code>[subscription_days_left]</code> - <?php esc_html_e('نمایش روزهای باقی‌مانده', 'eshterakyar'); ?></li>
                                <li><code>[subscription_info]</code> - <?php esc_html_e('نمایش اطلاعات کامل اشتراک', 'eshterakyar'); ?></li>
                                <li><code>[eshterakyar_product_cta]</code> - <?php esc_html_e('دکمه خرید/دانلود رایگان محصول (صفحه محصول / المنتور)', 'eshterakyar'); ?></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                
                <h3><?php esc_html_e('۶. صفحه دانلودهای کاربر', 'eshterakyar'); ?></h3>
                <div class="help-notice-box" style="background: #e8f5e9; border-right: 4px solid #4caf50; padding: 15px; margin: 15px 0; border-radius: 4px;">
                    <h4 style="margin-top: 0; color: #2e7d32;"><?php esc_html_e('سازماندهی دانلودها', 'eshterakyar'); ?></h4>
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('افزونه به صورت خودکار دانلودهای کاربر را در صفحه "دانلودها" در حساب کاربری به دو بخش تقسیم می‌کند:', 'eshterakyar'); ?></p>
                    
                    <h5 style="color: #2e7d32; margin-top: 20px;"><?php esc_html_e('۱. دانلودهای اشتراک فعلی:', 'eshterakyar'); ?></h5>
                    <ul style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('این بخش شامل تمام دانلودهای مربوط به اشتراک فعال فعلی کاربر است', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('با border سبز رنگ نمایش داده می‌شود', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('نام اشتراک فعلی در بالای بخش نمایش داده می‌شود', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('کاربر می‌تواند این دانلودها را مجدد دانلود کند', 'eshterakyar'); ?></li>
                    </ul>
                    
                    <h5 style="color: #2e7d32; margin-top: 20px;"><?php esc_html_e('۲. سایر دانلودها:', 'eshterakyar'); ?></h5>
                    <ul style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('این بخش شامل دانلودهای مربوط به اشتراک‌های قدیمی یا محصولات رایگان است', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('با border خاکستری نمایش داده می‌شود', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('کاربر همچنان می‌تواند این دانلودها را مجدد دانلود کند', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('این بخش برای حفظ تاریخچه دانلودهای کاربر است', 'eshterakyar'); ?></li>
                    </ul>
                    
                    <h5 style="color: #2e7d32; margin-top: 20px;"><?php esc_html_e('ویژگی‌های صفحه دانلودها:', 'eshterakyar'); ?></h5>
                    <ul style="margin: 10px 0; padding-right: 20px;">
                        <li><?php esc_html_e('نمایش نام محصول', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('تاریخ و زمان دانلود', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('تعداد دفعات دانلود', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('دکمه "دانلود مجدد" برای هر محصول', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('لینک به صفحه محصول', 'eshterakyar'); ?></li>
                        <li><?php esc_html_e('ذخیره سوابق دانلود تا تاریخ مشخص (قابل تنظیم در تنظیمات)', 'eshterakyar'); ?></li>
                    </ul>
                    
                    <h5 style="color: #2e7d32; margin-top: 20px;"><?php esc_html_e('دسترسی به صفحه دانلودها:', 'eshterakyar'); ?></h5>
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('کاربران می‌توانند از طریق حساب کاربری WooCommerce به تب "دانلودها" دسترسی داشته باشند. این تب به صورت خودکار به منوی حساب کاربری اضافه می‌شود.', 'eshterakyar'); ?></p>
                </div>
                
                <h3><?php esc_html_e('۷. گروه اشتراک‌ها و اشتراک‌های رزرو شده', 'eshterakyar'); ?></h3>
                <ul class="help-features-list">
                    <li>
                        <strong><?php esc_html_e('گروه اشتراک (شناسه گروه):', 'eshterakyar'); ?></strong>
                        <?php esc_html_e('در فرم ایجاد/ویرایش پلن، فیلدی به نام «گروه اشتراک (ایدی اشتراک‌ها)» دارید. هر پلنی که شناسه گروه یکسان داشته باشد، هم‌گروه محسوب می‌شود.', 'eshterakyar'); ?>
                    </li>
                    <li>
                        <strong><?php esc_html_e('رفتار خرید پلن هم‌گروه:', 'eshterakyar'); ?></strong>
                        <?php esc_html_e('اگر کاربر هم‌زمان یک اشتراک فعال از یک گروه داشته باشد و دوباره پلنی از همان گروه (و با گزینه «جمع کردن با اشتراک قبلی» فعال) بخرد، روزهای باقیمانده و محدودیت‌های دانلود با پلن جدید جمع می‌شوند و یک اشتراک فعال واحد خواهد داشت.', 'eshterakyar'); ?>
                    </li>
                    <li>
                        <strong><?php esc_html_e('رفتار خرید پلن غیر هم‌گروه (اشتراک رزرو شده):', 'eshterakyar'); ?></strong>
                        <?php esc_html_e('اگر کاربر اشتراک فعال دارد و پلن جدید از گروه دیگری باشد (یا گزینه «جمع کردن با اشتراک قبلی» غیرفعال باشد)، اشتراک جدید به صورت «رزرو شده» ثبت می‌شود؛ یعنی تا زمانی که اشتراک فعلی منقضی نشود فعال نمی‌شود.', 'eshterakyar'); ?>
                    </li>
                    <li>
                        <strong><?php esc_html_e('فعال‌سازی خودکار اشتراک رزرو شده:', 'eshterakyar'); ?></strong>
                        <?php esc_html_e('به محض این‌که اشتراک فعلی کاربر منقضی شود، افزونه به صورت خودکار اشتراک(های) رزرو شده‌ای را که برای بعد از آن تنظیم شده‌اند فعال می‌کند (تاریخ شروع از همان لحظه و تاریخ پایان بر اساس مدت پلن تنظیم می‌شود).', 'eshterakyar'); ?>
                    </li>
                    <li>
                        <strong><?php esc_html_e('نمایش وضعیت «رزرو شده» در ادمین و حساب کاربری:', 'eshterakyar'); ?></strong>
                        <?php esc_html_e('در لیست اشتراک‌ها در بخش ادمین، وضعیت رزرو شده با برچسب قابل تنظیم (reserved_subscription_status_label) نمایش داده می‌شود و ستونی با عنوان «فعال پس از» نشان می‌دهد این اشتراک بعد از کدام اشتراک فعال خواهد شد. در حساب کاربری کاربر نیز اشتراک‌های رزرو شده با پیام «پس از اتمام اشتراک فعلی به‌صورت خودکار فعال می‌شود» نمایش داده می‌شوند.', 'eshterakyar'); ?>
                    </li>
                    <li>
                        <strong><?php esc_html_e('پیام‌های قابل ویرایش برای ارتقا و رزرو:', 'eshterakyar'); ?></strong>
                        <?php esc_html_e('در تب «متن‌های قابل ویرایش»، می‌توانید پیام موفقیت ارتقا (merge_success_message) و پیام اشتراک رزرو شده (reserved_subscription_message) را ویرایش کنید تا در ایمیل و پیامک خرید، متن دلخواه شما نمایش داده شود.', 'eshterakyar'); ?>
                    </li>
                </ul>
                
                <h3><?php esc_html_e('۸. ویژگی‌های اصلی دیگر', 'eshterakyar'); ?></h3>
                <ul class="help-features-list">
                    <li><strong><?php esc_html_e('دانلود مجدد بدون کاهش اعتبار:', 'eshterakyar'); ?></strong> <?php esc_html_e('کاربران می‌توانند فایل‌های قبلاً دانلود شده را مجدد دانلود کنند بدون اینکه از اعتبارشان کم شود.', 'eshterakyar'); ?></li>
                    <li><strong><?php esc_html_e('دسترسی به فایل‌های دوره‌های قبلی:', 'eshterakyar'); ?></strong> <?php esc_html_e('کاربران به تمام فایل‌هایی که در هر دوره اشتراک دانلود کرده‌اند دسترسی دارند.', 'eshterakyar'); ?></li>
                    <li><strong><?php esc_html_e('کارت دانلود شده:', 'eshterakyar'); ?></strong> <?php esc_html_e('کارت "دانلود شده" روی تصویر محصول نمایش داده می‌شود که با المنتور و ووکامرس سازگار است.', 'eshterakyar'); ?></li>
                    <li><strong><?php esc_html_e('محدودیت هوشمند:', 'eshterakyar'); ?></strong> <?php esc_html_e('محدودیت‌ها فقط برای دانلودهای جدید اعمال می‌شوند، دانلود مجدد از اعتبار کم نمی‌کند.', 'eshterakyar'); ?></li>
                    <li><strong><?php esc_html_e('محدودیت روزانه بر اساس ساعت 00:00:', 'eshterakyar'); ?></strong> <?php esc_html_e('محدودیت دانلود روزانه هر شب در ساعت 00:00 (نیمه شب) صفر می‌شود، نه بر اساس 24 ساعت از زمان خرید.', 'eshterakyar'); ?></li>
                    <li><strong><?php esc_html_e('دکمه تمدید اشتراک:', 'eshterakyar'); ?></strong> <?php esc_html_e('وقتی محدودیت دانلود روزانه یا کلی به پایان می‌رسد، دکمه "تمدید اشتراک" نمایش داده می‌شود که کاربر را به صفحه خرید اشتراک هدایت می‌کند.', 'eshterakyar'); ?></li>
                </ul>
                
                <h3><?php esc_html_e('۹. بهینه‌سازی و عملکرد', 'eshterakyar'); ?></h3>
                <ul class="help-features-list">
                    <li><?php esc_html_e('دیتابیس بهینه با استفاده از INDEX برای سرعت بیشتر', 'eshterakyar'); ?></li>
                    <li><?php esc_html_e('استفاده از COUNT(DISTINCT) برای محاسبه دقیق دانلودها', 'eshterakyar'); ?></li>
                    <li><?php esc_html_e('کش کردن اطلاعات برای کاهش بار دیتابیس', 'eshterakyar'); ?></li>
                    <li><?php esc_html_e('بهینه‌سازی فایل‌های CSS و JS', 'eshterakyar'); ?></li>
                </ul>
                
                <h3><?php esc_html_e('۱۰. راهنمای توسعه‌دهندگان', 'eshterakyar'); ?></h3>
                <div class="help-notice-box" style="background: #fff3cd; border-right: 4px solid #ffc107; padding: 15px; margin: 15px 0; border-radius: 4px;">
                    <h4 style="margin-top: 0; color: #856404;"><?php esc_html_e('فانکشن‌های PHP برای استفاده در قالب', 'eshterakyar'); ?></h4>
                    <p style="color: #333; margin: 10px 0;"><?php esc_html_e('افزونه شامل فانکشن‌های مختلفی است که می‌توانید در قالب یا افزونه‌های خود استفاده کنید:', 'eshterakyar'); ?></p>
                    
                    <h5 style="color: #856404; margin-top: 20px;"><?php esc_html_e('کلاس Eshterakyar_Subscription', 'eshterakyar'); ?></h5>
                    <ul style="margin: 10px 0; padding-right: 20px;">
                        <li style="margin-bottom: 25px;">
                            <strong><code>Eshterakyar_Subscription::get_active_subscription($user_id)</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این فانکشن اشتراک فعال کاربر را برمی‌گرداند. اشتراک فعال یعنی اشتراکی که وضعیت آن "active" است، فعال (is_active = 1) است و تاریخ انقضای آن هنوز نرسیده است. این فانکشن از کش استفاده می‌کند برای بهبود عملکرد.', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$user_id</code> (int) - <?php esc_html_e('شناسه کاربر (ID کاربر در وردپرس)', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مقدار بازگشتی:', 'eshterakyar'); ?></strong> <?php esc_html_e('یک object از اشتراک فعال یا null در صورت عدم وجود اشتراک فعال. object شامل فیلدهای زیر است:', 'eshterakyar'); ?></p>
                            <ul style="margin: 5px 0; padding-right: 20px; font-size: 13px;">
                                <li><code>id</code> - <?php esc_html_e('شناسه اشتراک', 'eshterakyar'); ?></li>
                                <li><code>user_id</code> - <?php esc_html_e('شناسه کاربر', 'eshterakyar'); ?></li>
                                <li><code>plan_id</code> - <?php esc_html_e('شناسه پلن', 'eshterakyar'); ?></li>
                                <li><code>plan_name</code> - <?php esc_html_e('نام پلن', 'eshterakyar'); ?></li>
                                <li><code>start_date</code> - <?php esc_html_e('تاریخ شروع', 'eshterakyar'); ?></li>
                                <li><code>end_date</code> - <?php esc_html_e('تاریخ انقضا', 'eshterakyar'); ?></li>
                                <li><code>status</code> - <?php esc_html_e('وضعیت (active, expired, cancelled)', 'eshterakyar'); ?></li>
                                <li><code>total_downloads</code> - <?php esc_html_e('محدودیت دانلود کلی', 'eshterakyar'); ?></li>
                                <li><code>downloads_used</code> - <?php esc_html_e('تعداد دانلودهای استفاده شده', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>$user_id = get_current_user_id();
$subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
if ($subscription) {
    echo 'اشتراک فعال: ' . esc_html($subscription->plan_name);
    echo 'تاریخ انقضا: ' . date_i18n('Y/m/d', strtotime($subscription->end_date));
} else {
    echo 'شما اشتراک فعالی ندارید';
}</code></pre>
                        </li>
                        
                        <li style="margin-bottom: 25px;">
                            <strong><code>Eshterakyar_Subscription::get_user_subscriptions($user_id, $status = null)</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این فانکشن تمام اشتراک‌های کاربر را برمی‌گرداند. می‌توانید با پارامتر $status، اشتراک‌ها را فیلتر کنید (active, expired, cancelled). اگر $status را null بگذارید، تمام اشتراک‌ها را برمی‌گرداند.', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$user_id</code> (int) - <?php esc_html_e('شناسه کاربر', 'eshterakyar'); ?></li>
                                <li><code>$status</code> (string|null) - <?php esc_html_e('وضعیت اشتراک (active, expired, cancelled) یا null برای همه', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مقدار بازگشتی:', 'eshterakyar'); ?></strong> <?php esc_html_e('یک آرایه از object های اشتراک، مرتب شده بر اساس تاریخ ایجاد (جدیدترین اول)', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>// دریافت تمام اشتراک‌های فعال
$subscriptions = Eshterakyar_Subscription::get_user_subscriptions($user_id, 'active');
foreach ($subscriptions as $sub) {
    echo $sub->plan_name . ' - ' . date_i18n('Y/m/d', strtotime($sub->end_date)) . '<br>';
}

// دریافت تمام اشتراک‌ها (فعال و منقضی شده)
$all_subscriptions = Eshterakyar_Subscription::get_user_subscriptions($user_id);
echo 'تعداد کل اشتراک‌ها: ' . count($all_subscriptions);</code></pre>
                        </li>
                        
                        <li style="margin-bottom: 25px;">
                            <strong><code>Eshterakyar_Subscription::get_days_left($subscription_id)</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این فانکشن تعداد روزهای باقی‌مانده تا انقضای اشتراک را محاسبه و برمی‌گرداند. اگر اشتراک منقضی شده باشد، عدد 0 را برمی‌گرداند.', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$subscription_id</code> (int) - <?php esc_html_e('شناسه اشتراک', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مقدار بازگشتی:', 'eshterakyar'); ?></strong> <?php esc_html_e('تعداد روزهای باقی‌مانده (int) یا 0 در صورت منقضی شدن', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>$subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
if ($subscription) {
    $days_left = Eshterakyar_Subscription::get_days_left($subscription->id);
    if ($days_left > 0) {
        echo 'روزهای باقی‌مانده: ' . $days_left;
    } else {
        echo 'اشتراک شما منقضی شده است';
    }
}</code></pre>
                        </li>
                        
                        <li style="margin-bottom: 25px;">
                            <strong><code>Eshterakyar_Subscription::can_download($user_id, $product_id, $is_redownload = false)</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این فانکشن بررسی می‌کند که آیا کاربر می‌تواند محصول را دانلود کند یا نه. این بررسی شامل موارد زیر است: وجود اشتراک فعال، فعال بودن اشتراک، دسترسی به محصول بر اساس دسته‌بندی و تگ پلن، محدودیت دانلود روزانه و کلی. اگر $is_redownload = true باشد، محدودیت‌ها بررسی نمی‌شوند (فقط اگر قبلاً دانلود شده باشد).', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$user_id</code> (int) - <?php esc_html_e('شناسه کاربر', 'eshterakyar'); ?></li>
                                <li><code>$product_id</code> (int) - <?php esc_html_e('شناسه محصول WooCommerce', 'eshterakyar'); ?></li>
                                <li><code>$is_redownload</code> (bool) - <?php esc_html_e('آیا این دانلود مجدد است؟ (پیش‌فرض: false)', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مقدار بازگشتی:', 'eshterakyar'); ?></strong> <?php esc_html_e('یک آرایه با کلیدهای زیر:', 'eshterakyar'); ?></p>
                            <ul style="margin: 5px 0; padding-right: 20px; font-size: 13px;">
                                <li><code>can</code> (bool) - <?php esc_html_e('آیا می‌تواند دانلود کند؟', 'eshterakyar'); ?></li>
                                <li><code>message</code> (string) - <?php esc_html_e('پیام خطا در صورت عدم امکان دانلود', 'eshterakyar'); ?></li>
                                <li><code>subscription</code> (object) - <?php esc_html_e('object اشتراک در صورت امکان دانلود', 'eshterakyar'); ?></li>
                                <li><code>is_redownload</code> (bool) - <?php esc_html_e('آیا این دانلود مجدد است؟', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>$result = Eshterakyar_Subscription::can_download($user_id, $product_id);
if ($result['can']) {
    echo 'کاربر می‌تواند دانلود کند';
    if ($result['is_redownload']) {
        echo ' (این دانلود مجدد است)';
    }
} else {
    echo 'خطا: ' . $result['message'];
}</code></pre>
                        </li>
                        
                        <li style="margin-bottom: 25px;">
                            <strong><code>Eshterakyar_Subscription::create($user_id, $plan_id, $order_id = null)</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این فانکشن یک اشتراک جدید برای کاربر ایجاد می‌کند. اگر گزینه "جمع کردن مجوزها" در پلن فعال باشد و کاربر اشتراک فعال هم‌گروه داشته باشد، مجوزها و روزهای باقی‌مانده جمع می‌شوند. بعد از ایجاد، وضعیت اشتراک‌های قبلی بررسی و به‌روزرسانی می‌شود.', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$user_id</code> (int) - <?php esc_html_e('شناسه کاربر', 'eshterakyar'); ?></li>
                                <li><code>$plan_id</code> (int) - <?php esc_html_e('شناسه پلن اشتراک', 'eshterakyar'); ?></li>
                                <li><code>$order_id</code> (int|null) - <?php esc_html_e('شناسه سفارش WooCommerce (اختیاری)', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مقدار بازگشتی:', 'eshterakyar'); ?></strong> <?php esc_html_e('شناسه اشتراک ایجاد شده (int) یا false در صورت خطا', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>// ایجاد اشتراک بعد از خرید محصول
$subscription_id = Eshterakyar_Subscription::create($user_id, $plan_id, $order_id);
if ($subscription_id) {
    echo 'اشتراک با موفقیت ایجاد شد. ID: ' . $subscription_id;
    // می‌توانید hook را اجرا کنید
    do_action('eshterakyar_subscription_created', $subscription_id, $user_id, $plan_id);
} else {
    echo 'خطا در ایجاد اشتراک';
}</code></pre>
                        </li>
                    </ul>
                    
                    <h5 style="color: #856404; margin-top: 20px;"><?php esc_html_e('کلاس Eshterakyar_Plan', 'eshterakyar'); ?></h5>
                    <ul style="margin: 10px 0; padding-right: 20px;">
                        <li style="margin-bottom: 25px;">
                            <strong><code>Eshterakyar_Plan::get_all($status = 'active')</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این فانکشن تمام پلن‌های اشتراک را برمی‌گرداند. می‌توانید با پارامتر $status، پلن‌ها را فیلتر کنید. مقادیر مجاز: "active" (فعال)، "inactive" (غیرفعال)، "deleted" (حذف شده) یا "all" (همه به جز حذف شده). پلن‌ها بر اساس قیمت (صعودی) مرتب می‌شوند.', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$status</code> (string) - <?php esc_html_e('وضعیت پلن (active, inactive, deleted, all) - پیش‌فرض: active', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مقدار بازگشتی:', 'eshterakyar'); ?></strong> <?php esc_html_e('یک آرایه از object های پلن، مرتب شده بر اساس قیمت', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>// دریافت تمام پلن‌های فعال
$plans = Eshterakyar_Plan::get_all('active');
foreach ($plans as $plan) {
    echo $plan->name . ' - ' . number_format_i18n($plan->price) . ' تومان<br>';
    echo 'مدت زمان: ' . $plan->duration_days . ' روز<br>';
    if ($plan->badge_text) {
        echo 'نشان: ' . esc_html($plan->badge_text) . '<br>';
    }
}

// دریافت همه پلن‌ها (فعال و غیرفعال)
$all_plans = Eshterakyar_Plan::get_all('all');</code></pre>
                        </li>
                        
                        <li style="margin-bottom: 25px;">
                            <strong><code>Eshterakyar_Plan::get_by_id($plan_id)</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این فانکشن یک پلن را بر اساس شناسه آن برمی‌گرداند. از این فانکشن برای دریافت اطلاعات کامل یک پلن استفاده کنید.', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$plan_id</code> (int) - <?php esc_html_e('شناسه پلن', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مقدار بازگشتی:', 'eshterakyar'); ?></strong> <?php esc_html_e('یک object از پلن یا null در صورت عدم وجود', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>$plan = Eshterakyar_Plan::get_by_id(1);
if ($plan) {
    echo 'نام پلن: ' . esc_html($plan->name) . '<br>';
    echo 'قیمت: ' . number_format_i18n($plan->price) . ' تومان<br>';
    echo 'مدت زمان: ' . $plan->duration_days . ' روز<br>';
    echo 'محدودیت روزانه: ' . ($plan->daily_limit > 0 ? $plan->daily_limit : 'نامحدود') . '<br>';
    echo 'محدودیت کلی: ' . ($plan->total_limit > 0 ? $plan->total_limit : 'نامحدود') . '<br>';
    if ($plan->product_id) {
        echo 'محصول مرتبط: <a href="' . esc_url(get_permalink($plan->product_id)) . '">مشاهده محصول</a>';
    }
}</code></pre>
                        </li>
                        
                        <li style="margin-bottom: 25px;">
                            <strong><code>Eshterakyar_Plan::get_by_product($product_id)</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این فانکشن پلن مرتبط با یک محصول WooCommerce را برمی‌گرداند. از این فانکشن برای پیدا کردن پلن مرتبط با یک محصول استفاده کنید. فقط پلن‌های فعال را برمی‌گرداند.', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$product_id</code> (int) - <?php esc_html_e('شناسه محصول WooCommerce', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مقدار بازگشتی:', 'eshterakyar'); ?></strong> <?php esc_html_e('یک object از پلن مرتبط یا null در صورت عدم وجود', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>// در صفحه محصول WooCommerce
if (is_product()) {
    $product_id = get_the_ID();
    $plan = Eshterakyar_Plan::get_by_product($product_id);
    if ($plan) {
        echo 'این محصول مربوط به پلن: ' . esc_html($plan->name) . ' است';
        echo '<br>قیمت پلن: ' . number_format_i18n($plan->price) . ' تومان';
    }
}</code></pre>
                        </li>
                    </ul>
                    
                    <h5 style="color: #856404; margin-top: 20px;"><?php esc_html_e('کلاس Eshterakyar_Download', 'eshterakyar'); ?></h5>
                    <ul style="margin: 10px 0; padding-right: 20px;">
                        <li style="margin-bottom: 25px;">
                            <strong><code>Eshterakyar_Download::is_downloaded($user_id, $subscription_id, $product_id)</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این فانکشن بررسی می‌کند که آیا کاربر محصول را در یک اشتراک خاص دانلود کرده است یا نه. این بررسی برای دانلود مجدد بدون کاهش اعتبار استفاده می‌شود.', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$user_id</code> (int) - <?php esc_html_e('شناسه کاربر', 'eshterakyar'); ?></li>
                                <li><code>$subscription_id</code> (int) - <?php esc_html_e('شناسه اشتراک', 'eshterakyar'); ?></li>
                                <li><code>$product_id</code> (int) - <?php esc_html_e('شناسه محصول', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مقدار بازگشتی:', 'eshterakyar'); ?></strong> <?php esc_html_e('true اگر محصول قبلاً دانلود شده باشد، false در غیر این صورت', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>$subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
if ($subscription) {
    $is_downloaded = Eshterakyar_Download::is_downloaded($user_id, $subscription->id, $product_id);
    if ($is_downloaded) {
        echo '<span class="downloaded-badge">این محصول قبلاً دانلود شده است</span>';
        echo '<a href="#" class="redownload-btn">دانلود مجدد</a>';
    } else {
        echo '<a href="#" class="download-btn">دانلود</a>';
    }
}</code></pre>
                        </li>
                        
                        <li style="margin-bottom: 25px;">
                            <strong><code>Eshterakyar_Download::get_user_downloads($user_id, $subscription_id = null)</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این فانکشن لیست تمام دانلودهای کاربر را برمی‌گرداند. اگر $subscription_id مشخص شود، فقط دانلودهای آن اشتراک را برمی‌گرداند. اگر null باشد، تمام دانلودهای کاربر در تمام اشتراک‌ها را برمی‌گرداند. نتایج بر اساس تاریخ دانلود (جدیدترین اول) مرتب می‌شوند.', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$user_id</code> (int) - <?php esc_html_e('شناسه کاربر', 'eshterakyar'); ?></li>
                                <li><code>$subscription_id</code> (int|null) - <?php esc_html_e('شناسه اشتراک (اختیاری) - اگر null باشد، تمام دانلودها را برمی‌گرداند', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مقدار بازگشتی:', 'eshterakyar'); ?></strong> <?php esc_html_e('یک آرایه از object های دانلود، مرتب شده بر اساس تاریخ دانلود', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>// دریافت تمام دانلودهای کاربر
$downloads = Eshterakyar_Download::get_user_downloads($user_id);
echo '<h3>تاریخچه دانلودها (' . count($downloads) . ' مورد)</h3>';
echo '<ul>';
foreach ($downloads as $download) {
    $product = wc_get_product($download->product_id);
    echo '<li>';
    echo esc_html($product->get_name()) . ' - ';
    echo date_i18n('Y/m/d H:i', strtotime($download->download_date));
    echo ' (تعداد دانلود: ' . $download->download_count . ')';
    echo '</li>';
}
echo '</ul>';

// دریافت دانلودهای یک اشتراک خاص
$subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
if ($subscription) {
    $subscription_downloads = Eshterakyar_Download::get_user_downloads($user_id, $subscription->id);
    echo 'دانلودهای این اشتراک: ' . count($subscription_downloads);
}</code></pre>
                        </li>
                        
                        <li style="margin-bottom: 25px;">
                            <strong><code>Eshterakyar_Download::is_downloaded_any_subscription($user_id, $product_id)</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این فانکشن بررسی می‌کند که آیا کاربر محصول را در هر کدام از اشتراک‌هایش (فعال یا منقضی شده) دانلود کرده است یا نه. این برای نمایش کارت "دانلود شده" روی تصویر محصول استفاده می‌شود.', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$user_id</code> (int) - <?php esc_html_e('شناسه کاربر', 'eshterakyar'); ?></li>
                                <li><code>$product_id</code> (int) - <?php esc_html_e('شناسه محصول', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مقدار بازگشتی:', 'eshterakyar'); ?></strong> <?php esc_html_e('true اگر محصول در هر اشتراکی دانلود شده باشد، false در غیر این صورت', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>// در صفحه محصول
if (is_user_logged_in() && is_product()) {
    $user_id = get_current_user_id();
    $product_id = get_the_ID();
    if (Eshterakyar_Download::is_downloaded_any_subscription($user_id, $product_id)) {
        echo '<div class="downloaded-badge">دانلود شده</div>';
    }
}</code></pre>
                        </li>
                    </ul>
                    
                    <h5 style="color: #856404; margin-top: 20px;"><?php esc_html_e('کلاس Eshterakyar_Subscription_Page', 'eshterakyar'); ?></h5>
                    <ul style="margin: 10px 0; padding-right: 20px;">
                        <li style="margin-bottom: 25px;">
                            <strong><code>Eshterakyar_Subscription_Page::get_page_url()</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این فانکشن لینک صفحه خرید اشتراک را برمی‌گرداند. افزونه به صورت خودکار یک صفحه با slug "subscription-plans" ایجاد می‌کند که شامل شورتکد [eshterakyar_subscription_plans] است. از این فانکشن برای لینک دادن به صفحه خرید اشتراک استفاده کنید.', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong> <?php esc_html_e('هیچ پارامتری ندارد', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مقدار بازگشتی:', 'eshterakyar'); ?></strong> <?php esc_html_e('لینک صفحه خرید اشتراک (string) یا رشته خالی در صورت عدم وجود صفحه', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>// در منوی سایت یا sidebar
$page_url = Eshterakyar_Subscription_Page::get_page_url();
if ($page_url) {
    echo '<a href="' . esc_url($page_url) . '" class="buy-subscription-btn">خرید اشتراک</a>';
}

// در قالب
if (!is_user_logged_in()) {
    $page_url = Eshterakyar_Subscription_Page::get_page_url();
    echo '<div class="subscription-cta">';
    echo '<h2>برای دسترسی به محتوا، اشتراک خریداری کنید</h2>';
    echo '<a href="' . esc_url($page_url) . '" class="button">مشاهده پلن‌ها</a>';
    echo '</div>';
}</code></pre>
                        </li>
                    </ul>
                    
                    <h5 style="color: #856404; margin-top: 20px;"><?php esc_html_e('Hook ها و Filter ها', 'eshterakyar'); ?></h5>
                    <p><?php esc_html_e('افزونه از Hook ها و Filter های WordPress استفاده می‌کند که می‌توانید در قالب یا افزونه‌های خود استفاده کنید:', 'eshterakyar'); ?></p>
                    <ul style="margin: 10px 0; padding-right: 20px;">
                        <li style="margin-bottom: 25px;">
                            <strong><code>eshterakyar_subscription_created</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این hook بعد از ایجاد موفقیت‌آمیز یک اشتراک جدید اجرا می‌شود. از این hook برای انجام عملیات اضافی بعد از ایجاد اشتراک استفاده کنید (مثلاً ارسال ایمیل، ثبت لاگ، اتصال به سیستم‌های خارجی).', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$subscription_id</code> (int) - <?php esc_html_e('شناسه اشتراک ایجاد شده', 'eshterakyar'); ?></li>
                                <li><code>$user_id</code> (int) - <?php esc_html_e('شناسه کاربر', 'eshterakyar'); ?></li>
                                <li><code>$plan_id</code> (int) - <?php esc_html_e('شناسه پلن', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>// در functions.php قالب یا یک افزونه سفارشی
add_action('eshterakyar_subscription_created', function($subscription_id, $user_id, $plan_id) {
    // دریافت اطلاعات
    $user = get_userdata($user_id);
    $plan = Eshterakyar_Plan::get_by_id($plan_id);
    $subscription = Eshterakyar_Subscription::get_by_id($subscription_id);
    
    // ارسال ایمیل سفارشی
    $to = $user->user_email;
    $subject = 'اشتراک شما با موفقیت فعال شد';
    $message = "سلام {$user->display_name}\n\n";
    $message .= "اشتراک شما برای پلن {$plan->name} با موفقیت فعال شد.\n";
    $message .= "تاریخ انقضا: " . date_i18n('Y/m/d', strtotime($subscription->end_date));
    wp_mail($to, $subject, $message);
    
    // ثبت لاگ
    error_log("Subscription created: ID={$subscription_id}, User={$user_id}, Plan={$plan_id}");
}, 10, 3);</code></pre>
                        </li>
                        
                        <li style="margin-bottom: 25px;">
                            <strong><code>eshterakyar_subscription_expired</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این hook بعد از انقضای یک اشتراک اجرا می‌شود. از این hook برای انجام عملیات بعد از انقضا استفاده کنید (مثلاً ارسال ایمیل، غیرفعال کردن دسترسی، اتصال به سیستم‌های خارجی).', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$subscription_id</code> (int) - <?php esc_html_e('شناسه اشتراک منقضی شده', 'eshterakyar'); ?></li>
                                <li><code>$user_id</code> (int) - <?php esc_html_e('شناسه کاربر', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>add_action('eshterakyar_subscription_expired', function($subscription_id, $user_id) {
    $user = get_userdata($user_id);
    $subscription = Eshterakyar_Subscription::get_by_id($subscription_id);
    
    // ارسال ایمیل
    $to = $user->user_email;
    $subject = 'اشتراک شما منقضی شد';
    $message = "سلام {$user->display_name}\n\n";
    $message .= "اشتراک شما منقضی شده است. برای ادامه استفاده، لطفاً اشتراک جدید خریداری کنید.";
    wp_mail($to, $subject, $message);
    
    // غیرفعال کردن دسترسی به محتوای خاص (مثال)
    // update_user_meta($user_id, 'premium_access', false);
}, 10, 2);</code></pre>
                        </li>
                        
                        <li style="margin-bottom: 25px;">
                            <strong><code>eshterakyar_download_recorded</code></strong>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('توضیحات:', 'eshterakyar'); ?></strong> <?php esc_html_e('این hook بعد از ثبت موفقیت‌آمیز یک دانلود اجرا می‌شود. از این hook برای انجام عملیات بعد از دانلود استفاده کنید (مثلاً ثبت آمار، ارسال اطلاع‌رسانی، اتصال به سیستم‌های خارجی).', 'eshterakyar'); ?></p>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('پارامترها:', 'eshterakyar'); ?></strong></p>
                            <ul style="margin: 5px 0; padding-right: 20px;">
                                <li><code>$download_id</code> (int) - <?php esc_html_e('شناسه دانلود ثبت شده', 'eshterakyar'); ?></li>
                                <li><code>$user_id</code> (int) - <?php esc_html_e('شناسه کاربر', 'eshterakyar'); ?></li>
                                <li><code>$product_id</code> (int) - <?php esc_html_e('شناسه محصول دانلود شده', 'eshterakyar'); ?></li>
                            </ul>
                            <p style="color: #333; margin: 10px 0;"><strong><?php esc_html_e('مثال استفاده:', 'eshterakyar'); ?></strong></p>
                            <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>add_action('eshterakyar_download_recorded', function($download_id, $user_id, $product_id) {
    $user = get_userdata($user_id);
    $product = wc_get_product($product_id);
    $download = Eshterakyar_Download::get_download($user_id, $subscription_id, $product_id);
    
    // ثبت آمار در Google Analytics (مثال)
    // wp_enqueue_script('ga-track', '...');
    // wp_localize_script('ga-track', 'gaData', array(
    //     'event' => 'download',
    //     'product' => $product->get_name()
    // ));
    
    // ارسال اطلاع‌رسانی به مدیر
    $admin_email = get_option('admin_email');
    $subject = 'دانلود جدید ثبت شد';
    $message = "کاربر {$user->display_name} محصول {$product->get_name()} را دانلود کرد.";
    wp_mail($admin_email, $subject, $message);
}, 10, 3);</code></pre>
                        </li>
                    </ul>
                    
                    <h5 style="color: #856404; margin-top: 20px;"><?php esc_html_e('مثال: نمایش اطلاعات اشتراک در قالب', 'eshterakyar'); ?></h5>
                    <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>&lt;?php
// در فایل قالب خود (مثلاً header.php یا sidebar.php)
if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
    
    if ($subscription) {
        $days_left = Eshterakyar_Subscription::get_days_left($subscription->id);
        $plan = Eshterakyar_Plan::get_by_id($subscription->plan_id);
        ?&gt;
        &lt;div class="user-subscription-info"&gt;
            &lt;h3&gt;اشتراک فعال&lt;/h3&gt;
            &lt;p&gt;پلن: &lt;?php echo esc_html($plan->name); ?&gt;&lt;/p&gt;
            &lt;p&gt;روزهای باقی‌مانده: &lt;?php echo $days_left; ?&gt;&lt;/p&gt;
            &lt;p&gt;تاریخ انقضا: &lt;?php echo date_i18n('Y/m/d', strtotime($subscription->end_date)); ?&gt;&lt;/p&gt;
        &lt;/div&gt;
        &lt;?php
    } else {
        ?&gt;
        &lt;div class="no-subscription"&gt;
            &lt;a href="&lt;?php echo esc_url(Eshterakyar_Subscription_Page::get_page_url()); ?&gt;"&gt;
                خرید اشتراک
            &lt;/a&gt;
        &lt;/div&gt;
        &lt;?php
    }
}
?&gt;</code></pre>
                    
                    <h5 style="color: #856404; margin-top: 20px;"><?php esc_html_e('مثال: نمایش دکمه دانلود سفارشی', 'eshterakyar'); ?></h5>
                    <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>&lt;?php
// در قالب صفحه محصول
if (is_user_logged_in() && is_product()) {
    $user_id = get_current_user_id();
    $product_id = get_the_ID();
    
    if (Eshterakyar_Subscription::can_download($user_id, $product_id)) {
        $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        $is_downloaded = Eshterakyar_Download::is_downloaded($user_id, $subscription->id, $product_id);
        ?&gt;
        &lt;a href="#" class="custom-download-btn" data-product-id="&lt;?php echo $product_id; ?&gt;"&gt;
            &lt;?php echo $is_downloaded ? 'دانلود مجدد' : 'دانلود'; ?&gt;
        &lt;/a&gt;
        &lt;?php
    }
}
?&gt;</code></pre>
                    
                    <h5 style="color: #856404; margin-top: 20px;"><?php esc_html_e('مثال: نمایش لیست پلن‌ها در قالب', 'eshterakyar'); ?></h5>
                    <pre style="background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left;"><code>&lt;?php
// نمایش لیست پلن‌ها در قالب
$plans = Eshterakyar_Plan::get_all('active');
if (!empty($plans)) {
    ?&gt;
    &lt;div class="subscription-plans-grid"&gt;
        &lt;?php foreach ($plans as $plan): ?&gt;
            &lt;div class="plan-card"&gt;
                &lt;h3&gt;&lt;?php echo esc_html($plan->name); ?&gt;&lt;/h3&gt;
                &lt;p class="price"&gt;&lt;?php echo number_format_i18n($plan->price); ?&gt; تومان&lt;/p&gt;
                &lt;p class="duration"&gt;&lt;?php echo $plan->duration_days; ?&gt; روز&lt;/p&gt;
                &lt;?php if ($plan->product_id): ?&gt;
                    &lt;a href="&lt;?php echo esc_url(get_permalink($plan->product_id)); ?&gt;" class="buy-btn"&gt;
                        خرید اشتراک
                    &lt;/a&gt;
                &lt;?php endif; ?&gt;
            &lt;/div&gt;
        &lt;?php endforeach; ?&gt;
    &lt;/div&gt;
    &lt;?php
}
?&gt;</code></pre>
                </div>
                
                <h3><?php esc_html_e('۱۰. پشتیبانی', 'eshterakyar'); ?></h3>
                <p style="color: #333; margin: 10px 0;"><?php esc_html_e('برای پشتیبانی و گزارش مشکل، لطفاً با توسعه‌دهنده تماس بگیرید.', 'eshterakyar'); ?></p>
            </div>
        </div>
        
        <p class="submit">
            <?php submit_button(esc_html__('ذخیره تنظیمات', 'eshterakyar'), 'primary', 'submit', false); ?>
        </p>
    </form>
</div>

<script>
(function($) {
    'use strict';
    
    if (typeof $ === 'undefined' || typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded!');
        return;
    }
    
    function initTabs() {
        var $tabs = $('.nav-tab');
        var $contents = $('.eshterakyar-tab-content');
        
        if ($tabs.length === 0 || $contents.length === 0) {
            console.error('Tabs or content not found!');
            return;
        }
        
        $contents.not('.active').css('display', 'none');
        $contents.filter('.active').css('display', 'block');
        
        $tabs.off('click.tabs');
        
        $tabs.on('click.tabs', function(e) {
        e.preventDefault();
            e.stopPropagation();
            
            var $this = $(this);
            var target = $this.attr('href');
        
            if (!target || target === '#') {
                console.error('Invalid target:', target);
                return false;
            }
            
            var $targetContent = $(target);
            
            if ($targetContent.length === 0) {
                console.error('Target content not found:', target);
                return false;
            }
            
            $tabs.removeClass('nav-tab-active');
            $this.addClass('nav-tab-active');
        
            $contents.removeClass('active').css('display', 'none');
            
            $targetContent.addClass('active').css('display', 'block');
            
            return false;
    });
    }
    
    var warningIndex = <?php echo count($expiry_warnings); ?>;
    
    function initExpiryWarnings() {
        $('#add-expiry-warning').off('click.warnings').on('click.warnings', function() {
            var html = '<div class="expiry-warning-item" data-index="' + warningIndex + '">' +
            '<h4><?php esc_html_e('هشدار جدید', 'eshterakyar'); ?></h4>' +
            '<table class="form-table">' +
            '<tr><th scope="row"><label><?php esc_html_e('فعال', 'eshterakyar'); ?></label></th>' +
            '<td><input type="checkbox" name="eshterakyar_options[sms_expiry_warnings][' + warningIndex + '][enabled]" value="1" checked></td></tr>' +
            '<tr><th scope="row"><label><?php esc_html_e('تعداد روز قبل از انقضا', 'eshterakyar'); ?></label></th>' +
            '<td><input type="number" name="eshterakyar_options[sms_expiry_warnings][' + warningIndex + '][days]" value="7" min="1" max="365" class="small-text"></td></tr>' +
            '<tr><th scope="row"><label><?php esc_html_e('کد الگو', 'eshterakyar'); ?></label></th>' +
            '<td><input type="text" name="eshterakyar_options[sms_expiry_warnings][' + warningIndex + '][template_code]" value="" class="regular-text">' +
            '<p class="description"><?php esc_html_e('کد الگوی پیامک (اختیاری)', 'eshterakyar'); ?></p></td></tr>' +
            '<tr><th scope="row"><label><?php esc_html_e('متن پیامک', 'eshterakyar'); ?></label></th>' +
            '<td><textarea name="eshterakyar_options[sms_expiry_warnings][' + warningIndex + '][message]" rows="4" class="large-text"><?php esc_html_e('سلام {user_name}\nاشتراک شما {days_left} روز دیگر منقضی می‌شود.\nتاریخ انقضا: {end_date}', 'eshterakyar'); ?></textarea>' +
            '<p class="description"><?php esc_html_e('متغیرها: {user_name}, {plan_name}, {end_date}, {days_left}, {site_name}', 'eshterakyar'); ?></p></td></tr>' +
            '<tr><th scope="row"></th>' +
            '<td><button type="button" class="button button-small remove-warning" data-index="' + warningIndex + '"><?php esc_html_e('حذف این هشدار', 'eshterakyar'); ?></button></td></tr>' +
            '</table></div>';
        
        $('#expiry-warnings-container').append(html);
        warningIndex++;
    });
    
        $(document).off('click.remove-warning').on('click.remove-warning', '.remove-warning', function() {
        if (confirm('<?php esc_html_e('آیا مطمئن هستید که می‌خواهید این هشدار را حذف کنید؟', 'eshterakyar'); ?>')) {
            $(this).closest('.expiry-warning-item').remove();
        }
    });
    }
    if (typeof $.fn.pointer === 'undefined') {
        $.fn.pointer = function() {
            return this;
        };
    }
    
    $('#eshterakyar-settings-form').on('submit', function(e) {
        $('.eshterakyar-tab-content:not(.active)').find('[required]').each(function() {
            $(this).removeAttr('required');
        });
    });
    
    function toggleSmsProviderSettings() {
        var provider = $('#sms_provider').val() || 'melli';
        $('.eshterakyar-sms-provider-settings').each(function() {
            var $el = $(this);
            $el.toggle($el.data('provider') === provider);
        });
    }
    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            return navigator.clipboard.writeText(text);
        }
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = 0;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        return Promise.resolve();
    }
    function showCopyToast() {
        var toast = document.getElementById('eshterakyar-copy-toast');
        if (!toast) {
            return;
        }
        toast.hidden = false;
        toast.classList.add('is-visible');
        clearTimeout(showCopyToast._timer);
        showCopyToast._timer = setTimeout(function() {
            toast.classList.remove('is-visible');
            toast.hidden = true;
        }, 1800);
    }
    function detectCodeLang(code) {
        var trimmed = (code || '').trim();
        if (/^\[/.test(trimmed)) {
            return 'shortcode';
        }
        if (/^<\?php|^&lt;\?php/.test(trimmed) || /Eshterakyar_/.test(trimmed) || /\$user_id/.test(trimmed) || /add_action\(/.test(trimmed) || /get_current_user_id/.test(trimmed)) {
            return 'php';
        }
        if (/^\/\*|^\.eshterakyar/.test(trimmed)) {
            return 'css';
        }
        if (/^https?:\/\//.test(trimmed) || /^\?add-to-cart=/.test(trimmed)) {
            return 'url';
        }
        return 'code';
    }
    var eshterakyarCodeLangLabels = {
        shortcode: '<?php echo esc_js(__('شورتکد', 'eshterakyar')); ?>',
        php: 'PHP',
        css: 'CSS',
        url: 'URL',
        code: '<?php echo esc_js(__('کد', 'eshterakyar')); ?>'
    };
    function buildCodeBlockHtml(code, lang, compact) {
        var blockClass = 'eshterakyar-code-block' + (compact ? ' is-compact' : '') + ' is-legacy-enhanced';
        var langLabel = eshterakyarCodeLangLabels[lang] || lang.toUpperCase();
        return '<div class="' + blockClass + '" dir="ltr">' +
            '<div class="eshterakyar-code-block-header">' +
            '<span class="eshterakyar-code-block-lang">' + langLabel + '</span>' +
            '<button type="button" class="eshterakyar-code-copy-btn" aria-label="<?php echo esc_js(__('کپی کد', 'eshterakyar')); ?>">' +
            '<span class="dashicons dashicons-admin-page" aria-hidden="true"></span>' +
            '<span class="eshterakyar-code-copy-label"><?php echo esc_js(__('کپی', 'eshterakyar')); ?></span>' +
            '<span class="eshterakyar-code-copy-done"><?php echo esc_js(__('کپی شد!', 'eshterakyar')); ?></span>' +
            '</button></div>' +
            '<pre class="eshterakyar-code-block-pre"><code>' + $('<div/>').text(code).html() + '</code></pre>' +
            '</div>';
    }
    function initLegacyCodeBlocks() {
        $('#help pre:not(.eshterakyar-code-block-pre)').each(function() {
            var $pre = $(this);
            if ($pre.closest('.eshterakyar-code-block').length) {
                return;
            }
            var $code = $pre.children('code');
            var text = ($code.length ? $code.text() : $pre.text()).replace(/\r\n/g, '\n');
            var lang = detectCodeLang(text);
            var compact = text.split('\n').length <= 2 && text.length < 140;
            $pre.replaceWith(buildCodeBlockHtml(text, lang, compact));
        });
    }
    $(document).ready(function() {
        initTabs();
        initExpiryWarnings();
        initLegacyCodeBlocks();
        toggleSmsProviderSettings();
        $('#sms_provider').on('change', toggleSmsProviderSettings);
        $(document).on('click', '.eshterakyar-code-copy-btn', function() {
            var $btn = $(this);
            var $block = $btn.closest('.eshterakyar-code-block');
            var text = $block.find('.eshterakyar-code-block-pre code').text();
            if (!text) {
                return;
            }
            copyToClipboard(text).then(function() {
                $btn.addClass('is-copied');
                showCopyToast();
                setTimeout(function() {
                    $btn.removeClass('is-copied');
                }, 2000);
            });
        });
        $('.eshterakyar-var-chip').on('click', function() {
            var text = $(this).data('copy');
            if (text) {
                copyToClipboard(text).then(function() {
                    showCopyToast();
                    var $fb = $('#eshterakyar-var-copy-feedback');
                    $fb.show();
                    setTimeout(function() { $fb.fadeOut(); }, 1500);
                });
            }
        });
        $(document).on('click', '.eshterakyar-sms-preset', function() {
            var targetId = $(this).data('target');
            var value = $(this).data('value');
            if (targetId && value !== undefined) {
                $('#' + targetId).val(value);
            }
        });
        $(document).on('click', '.eshterakyar-sms-preset-expiry', function() {
            var index = $(this).data('index');
            var value = $(this).data('value');
            if (index !== undefined && value !== undefined) {
                $('.eshterakyar-expiry-message[data-index="' + index + '"]').val(value);
            }
        });
    });
    $(window).on('load', function() {
        initTabs();
    });
})(jQuery);
</script>
