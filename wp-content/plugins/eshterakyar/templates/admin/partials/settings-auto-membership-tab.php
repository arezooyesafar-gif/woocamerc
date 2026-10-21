<?php
/**
 * تب تنظیمات عضویت خودکار در اشتراک
 *
 * @package Eshterakyar
 */

if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('eshterakyar_options', array());
$enabled = !empty($options['auto_membership_enabled']);
$plan_id = isset($options['auto_membership_plan_id']) ? absint($options['auto_membership_plan_id']) : 0;
$new_users = !isset($options['auto_membership_new_users']) || !empty($options['auto_membership_new_users']);
$existing_users = !empty($options['auto_membership_existing_users']);
$skip_reserved = !isset($options['auto_membership_skip_reserved']) || !empty($options['auto_membership_skip_reserved']);
$exclude_roles = isset($options['auto_membership_exclude_roles'])
    ? $options['auto_membership_exclude_roles']
    : 'administrator,shop_manager';
$send_welcome = !empty($options['auto_membership_send_welcome']);
$daily_sync = !empty($options['auto_membership_daily_sync']);

$active_plans = class_exists('Eshterakyar_Plan') ? Eshterakyar_Plan::get_all('active') : array();
$configured_plan = class_exists('Eshterakyar_Auto_Membership') ? Eshterakyar_Auto_Membership::get_configured_plan() : null;
$eligible_count = ($enabled && $existing_users && class_exists('Eshterakyar_Auto_Membership'))
    ? Eshterakyar_Auto_Membership::count_eligible_users()
    : 0;
$last_batch = get_option('eshterakyar_auto_membership_batch_completed_at', '');
?>
<div id="auto-membership" class="eshterakyar-tab-content">
    <h2><?php esc_html_e('عضویت خودکار در اشتراک', 'eshterakyar'); ?></h2>
    <p class="description"><?php esc_html_e('کاربران بدون اشتراک فعال را به‌صورت خودکار عضو پلن انتخاب‌شده کنید — برای کاربران تازه‌ثبت‌نام‌شده و (در صورت نیاز) کاربران قدیمی سایت.', 'eshterakyar'); ?></p>

    <div class="help-notice-box eshterakyar-auto-membership-help" style="background: #e8f5e9; border-right: 4px solid #4caf50; padding: 15px; margin: 15px 0; border-radius: 4px;">
        <p><strong><?php esc_html_e('پیش‌نیاز:', 'eshterakyar'); ?></strong> <?php esc_html_e('ابتدا در «اشتراک یار → پلن‌ها» یک پلن (مثلاً رایگان با قیمت ۰) بسازید و آن را اینجا انتخاب کنید.', 'eshterakyar'); ?></p>
        <p><?php esc_html_e('کاربرانی که هم‌اکنون اشتراک فعال دارند تغییری نمی‌کنند. کاربران منقضی‌شده یا بدون اشتراک، واجد شرایط عضویت خودکار هستند.', 'eshterakyar'); ?></p>
    </div>

    <div class="eshterakyar-auto-membership-status-card" id="eshterakyar-auto-membership-status">
        <h3><?php esc_html_e('وضعیت فعلی', 'eshterakyar'); ?></h3>
        <ul class="eshterakyar-status-list">
            <li>
                <strong><?php esc_html_e('پلن انتخاب‌شده:', 'eshterakyar'); ?></strong>
                <span id="eshterakyar-auto-membership-plan-label">
                    <?php
                    if ($configured_plan) {
                        echo esc_html($configured_plan->name);
                    } elseif ($plan_id > 0) {
                        esc_html_e('پلن نامعتبر یا غیرفعال', 'eshterakyar');
                    } else {
                        esc_html_e('انتخاب نشده', 'eshterakyar');
                    }
                    ?>
                </span>
            </li>
            <li>
                <strong><?php esc_html_e('کاربران واجد شرایط (بدون اشتراک فعال):', 'eshterakyar'); ?></strong>
                <span id="eshterakyar-auto-membership-eligible-count"><?php echo esc_html((string) $eligible_count); ?></span>
                <button type="button" class="button button-small" id="eshterakyar-refresh-eligible-count"><?php esc_html_e('به‌روزرسانی شمارش', 'eshterakyar'); ?></button>
            </li>
            <li>
                <strong><?php esc_html_e('آخرین اجرای دسته‌ای:', 'eshterakyar'); ?></strong>
                <span id="eshterakyar-auto-membership-last-batch">
                    <?php echo $last_batch ? esc_html($last_batch) : esc_html__('هنوز اجرا نشده', 'eshterakyar'); ?>
                </span>
            </li>
        </ul>
    </div>

    <table class="form-table eshterakyar-auto-membership-fields">
        <tr>
            <th scope="row">
                <label for="auto_membership_enabled"><?php esc_html_e('فعال‌سازی عضویت خودکار', 'eshterakyar'); ?></label>
            </th>
            <td>
                <input type="checkbox" name="eshterakyar_options[auto_membership_enabled]" id="auto_membership_enabled" value="1" <?php checked(1, $enabled); ?>>
                <label for="auto_membership_enabled"><?php esc_html_e('عضویت خودکار در اشتراک فعال باشد', 'eshterakyar'); ?></label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="auto_membership_plan_id"><?php esc_html_e('پلن اشتراک', 'eshterakyar'); ?></label>
            </th>
            <td>
                <select name="eshterakyar_options[auto_membership_plan_id]" id="auto_membership_plan_id" class="regular-text">
                    <option value="0"><?php esc_html_e('— انتخاب پلن —', 'eshterakyar'); ?></option>
                    <?php foreach ($active_plans as $plan) : ?>
                        <option value="<?php echo esc_attr($plan->id); ?>" <?php selected($plan_id, (int) $plan->id); ?>>
                            <?php
                            echo esc_html($plan->name);
                            if (isset($plan->price) && (float) $plan->price <= 0) {
                                echo ' (' . esc_html__('رایگان', 'eshterakyar') . ')';
                            }
                            ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php esc_html_e('پلنی که به‌صورت خودکار به کاربران واجد شرایط اختصاص می‌یابد.', 'eshterakyar'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('مخاطبان', 'eshterakyar'); ?></th>
            <td>
                <fieldset>
                    <label style="display:block;margin-bottom:8px;">
                        <input type="checkbox" name="eshterakyar_options[auto_membership_new_users]" id="auto_membership_new_users" value="1" <?php checked(1, $new_users); ?>>
                        <?php esc_html_e('کاربران جدید — بلافاصله پس از ثبت‌نام', 'eshterakyar'); ?>
                    </label>
                    <label style="display:block;">
                        <input type="checkbox" name="eshterakyar_options[auto_membership_existing_users]" id="auto_membership_existing_users" value="1" <?php checked(1, $existing_users); ?>>
                        <?php esc_html_e('کاربران موجود — کسانی که الان اشتراک فعال ندارند', 'eshterakyar'); ?>
                    </label>
                </fieldset>
                <p class="description"><?php esc_html_e('برای کاربران موجود، پس از ذخیره تنظیمات از دکمه «اجرا برای کاربران واجد شرایط» استفاده کنید.', 'eshterakyar'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="auto_membership_skip_reserved"><?php esc_html_e('اشتراک رزرو', 'eshterakyar'); ?></label>
            </th>
            <td>
                <input type="checkbox" name="eshterakyar_options[auto_membership_skip_reserved]" id="auto_membership_skip_reserved" value="1" <?php checked(1, $skip_reserved); ?>>
                <label for="auto_membership_skip_reserved"><?php esc_html_e('کاربرانی که اشتراک رزرو (در صف) دارند، از عضویت خودکار مستثنی شوند', 'eshterakyar'); ?></label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="auto_membership_exclude_roles"><?php esc_html_e('نقش‌های مستثنی', 'eshterakyar'); ?></label>
            </th>
            <td>
                <input type="text" name="eshterakyar_options[auto_membership_exclude_roles]" id="auto_membership_exclude_roles" class="regular-text" value="<?php echo esc_attr($exclude_roles); ?>">
                <p class="description"><?php esc_html_e('شناسه نقش‌ها با ویرگول جدا شوند. پیش‌فرض: administrator,shop_manager', 'eshterakyar'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="auto_membership_send_welcome"><?php esc_html_e('اعلان خوش‌آمد', 'eshterakyar'); ?></label>
            </th>
            <td>
                <input type="checkbox" name="eshterakyar_options[auto_membership_send_welcome]" id="auto_membership_send_welcome" value="1" <?php checked(1, $send_welcome); ?>>
                <label for="auto_membership_send_welcome"><?php esc_html_e('ارسال ایمیل/پیامک خرید اشتراک پس از عضویت خودکار (نیاز به فعال بودن «اطلاع‌رسانی» در تنظیمات عمومی)', 'eshterakyar'); ?></label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="auto_membership_daily_sync"><?php esc_html_e('همگام‌سازی روزانه', 'eshterakyar'); ?></label>
            </th>
            <td>
                <input type="checkbox" name="eshterakyar_options[auto_membership_daily_sync]" id="auto_membership_daily_sync" value="1" <?php checked(1, $daily_sync); ?>>
                <label for="auto_membership_daily_sync"><?php esc_html_e('هر روز تا ۵۰ کاربر واجد شرایط جاافتاده را بررسی و عضو کن (برای سایت‌های پرکاربر)', 'eshterakyar'); ?></label>
            </td>
        </tr>
    </table>

    <div class="eshterakyar-auto-membership-batch-wrap" id="eshterakyar-auto-membership-batch-wrap">
        <h3><?php esc_html_e('اجرای دسته‌ای برای کاربران موجود', 'eshterakyar'); ?></h3>
        <p class="description"><?php esc_html_e('این عملیات کاربران را به دسته‌های ۵۰ نفره پردازش می‌کند. قبل از اجرا، تنظیمات را ذخیره کنید.', 'eshterakyar'); ?></p>

        <div class="eshterakyar-batch-progress" id="eshterakyar-auto-membership-progress" hidden>
            <div class="eshterakyar-batch-progress-bar">
                <div class="eshterakyar-batch-progress-fill" id="eshterakyar-auto-membership-progress-fill" style="width:0%;"></div>
            </div>
            <p class="eshterakyar-batch-progress-text" id="eshterakyar-auto-membership-progress-text"></p>
        </div>

        <p>
            <button type="button" class="button button-primary" id="eshterakyar-run-auto-membership-batch">
                <?php esc_html_e('اجرا برای کاربران واجد شرایط', 'eshterakyar'); ?>
            </button>
            <span class="spinner" id="eshterakyar-auto-membership-spinner" style="float:none;"></span>
        </p>

        <div id="eshterakyar-auto-membership-batch-result" class="eshterakyar-batch-result" hidden></div>
    </div>

    <div class="help-notice-box" style="background: #fff8e1; border-right: 4px solid #ff9800; padding: 15px; margin: 15px 0; border-radius: 4px;">
        <p><strong><?php esc_html_e('سناریوهای رایج:', 'eshterakyar'); ?></strong></p>
        <ul style="margin:0 1.5em 0 0;">
            <li><?php esc_html_e('فقط کاربران جدید: «کاربران جدید» را فعال کنید.', 'eshterakyar'); ?></li>
            <li><?php esc_html_e('کاربران قدیمی + جدید: هر دو گزینه مخاطبان را فعال کنید و دکمه اجرای دسته‌ای را بزنید.', 'eshterakyar'); ?></li>
            <li><?php esc_html_e('سایت پرکاربر: همگام‌سازی روزانه را فعال کنید تا کاربران جاافتاده به‌تدریج عضو شوند.', 'eshterakyar'); ?></li>
        </ul>
    </div>
</div>
