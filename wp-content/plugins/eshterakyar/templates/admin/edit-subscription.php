<?php

if (!defined('ABSPATH')) {
    exit;
}

$plans = Eshterakyar_Plan::get_all();
$admin = new Eshterakyar_Admin();

$user = get_userdata($subscription->user_id);
$user_name = $user ? $user->display_name : __('کاربر حذف شده', 'eshterakyar');
$user_email = $user ? $user->user_email : __('-', 'eshterakyar');

$start_date_display = '';
$end_date_display = '';

function convert_persian_digits_to_english($str) {
    if (empty($str)) {
        return $str;
    }
    $persian_digits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $english_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    return str_replace($persian_digits, $english_digits, $str);
}

if (function_exists('parsidate')) {
    if (!empty($subscription->start_date) && $subscription->start_date !== '0000-00-00 00:00:00') {
        $start_date_display = parsidate('Y/m/d H:i', $subscription->start_date, 'per');
        $start_date_display = convert_persian_digits_to_english($start_date_display);
    }
    if (!empty($subscription->end_date) && $subscription->end_date !== '0000-00-00 00:00:00') {
        $end_date_display = parsidate('Y/m/d H:i', $subscription->end_date, 'per');
        $end_date_display = convert_persian_digits_to_english($end_date_display);
    }
} elseif (function_exists('persian_date')) {
    if (!empty($subscription->start_date) && $subscription->start_date !== '0000-00-00 00:00:00') {
        $timestamp = strtotime($subscription->start_date);
        $start_date_display = persian_date('Y/m/d', $timestamp);
        $start_time = date('H:i', $timestamp);
        $start_date_display .= ' ' . $start_time;
        $start_date_display = convert_persian_digits_to_english($start_date_display);
    }
    if (!empty($subscription->end_date) && $subscription->end_date !== '0000-00-00 00:00:00') {
        $timestamp = strtotime($subscription->end_date);
        $end_date_display = persian_date('Y/m/d', $timestamp);
        $end_time = date('H:i', $timestamp);
        $end_date_display .= ' ' . $end_time;
        $end_date_display = convert_persian_digits_to_english($end_date_display);
    }
} else {
    if (!empty($subscription->start_date) && $subscription->start_date !== '0000-00-00 00:00:00') {
        $timestamp = strtotime($subscription->start_date);
        if ($timestamp) {
            $start_date_display = date_i18n('Y/m/d H:i', $timestamp);
        }
    }
    if (!empty($subscription->end_date) && $subscription->end_date !== '0000-00-00 00:00:00') {
        $timestamp = strtotime($subscription->end_date);
        if ($timestamp) {
            $end_date_display = date_i18n('Y/m/d H:i', $timestamp);
        }
    }
}

?>

<div class="wrap eshterakyar-admin">
    <h1><?php esc_html_e('ویرایش اشتراک', 'eshterakyar'); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('update_subscription_' . $subscription->id); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label><?php esc_html_e('کاربر', 'eshterakyar'); ?></label>
                </th>
                <td>
                    <strong><?php echo esc_html($user_name); ?></strong>
                    <br>
                    <small><?php echo esc_html($user_email); ?></small>
                    <?php if ($user): ?>
                        <br>
                        <a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $subscription->user_id)); ?>" class="button button-small">
                            <?php esc_html_e('ویرایش کاربر', 'eshterakyar'); ?>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="plan_id"><?php esc_html_e('پلن', 'eshterakyar'); ?></label>
                </th>
                <td>
                    <select name="plan_id" id="plan_id" class="regular-text">
                        <?php foreach ($plans as $plan): ?>
                            <option value="<?php echo esc_attr($plan->id); ?>" <?php selected($subscription->plan_id, $plan->id); ?>>
                                <?php echo esc_html($plan->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="start_date"><?php esc_html_e('تاریخ شروع', 'eshterakyar'); ?></label>
                </th>
                <td>
                    <input type="text" name="start_date_persian" id="start_date" data-jdp data-jdp-time="true" value="<?php echo esc_attr($start_date_display); ?>" class="regular-text persian-date-input persian-datetime-input" placeholder="<?php esc_attr_e('انتخاب تاریخ و زمان شمسی', 'eshterakyar'); ?>" readonly>
                    <input type="hidden" name="start_date" id="start_date_gregorian" value="<?php echo esc_attr($subscription->start_date ? $subscription->start_date : ''); ?>">
                    <p class="description"><?php esc_html_e('تاریخ و زمان شروع اشتراک به شمسی', 'eshterakyar'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="end_date"><?php esc_html_e('تاریخ پایان', 'eshterakyar'); ?></label>
                </th>
                <td>
                    <input type="text" name="end_date_persian" id="end_date" data-jdp data-jdp-time="true" value="<?php echo esc_attr($end_date_display); ?>" class="regular-text persian-date-input persian-datetime-input" placeholder="<?php esc_attr_e('انتخاب تاریخ و زمان شمسی', 'eshterakyar'); ?>" readonly>
                    <input type="hidden" name="end_date" id="end_date_gregorian" value="<?php echo esc_attr($subscription->end_date ? $subscription->end_date : ''); ?>">
                    <p class="description"><?php esc_html_e('تاریخ و زمان پایان اشتراک به شمسی', 'eshterakyar'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="status"><?php esc_html_e('وضعیت', 'eshterakyar'); ?></label>
                </th>
                <td>
                    <select name="status" id="status">
                        <option value="active" <?php selected($subscription->status, 'active'); ?>><?php esc_html_e('فعال', 'eshterakyar'); ?></option>
                        <option value="expired" <?php selected($subscription->status, 'expired'); ?>><?php esc_html_e('منقضی شده', 'eshterakyar'); ?></option>
                        <option value="cancelled" <?php selected($subscription->status, 'cancelled'); ?>><?php esc_html_e('لغو شده', 'eshterakyar'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="is_active"><?php esc_html_e('فعال/غیرفعال', 'eshterakyar'); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="is_active" id="is_active" value="1" <?php checked(isset($subscription->is_active) && $subscription->is_active); ?>>
                    <label for="is_active"><?php esc_html_e('اشتراک فعال است', 'eshterakyar'); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="total_downloads"><?php esc_html_e('مجموع دانلودهای مجاز', 'eshterakyar'); ?></label>
                </th>
                <td>
                    <input type="number" name="total_downloads" id="total_downloads" value="<?php echo esc_attr($subscription->total_downloads); ?>" min="0" class="small-text">
                    <p class="description"><?php esc_html_e('0 به معنای نامحدود است', 'eshterakyar'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="daily_limit"><?php esc_html_e('محدودیت دانلود روزانه', 'eshterakyar'); ?></label>
                </th>
                <td>
                    <input type="number" name="daily_limit" id="daily_limit" value="<?php echo esc_attr($subscription->daily_limit); ?>" min="0" class="small-text">
                    <p class="description"><?php esc_html_e('0 به معنای نامحدود است', 'eshterakyar'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="downloads_used"><?php esc_html_e('تعداد دانلودهای استفاده شده', 'eshterakyar'); ?></label>
                </th>
                <td>
                    <input type="number" name="downloads_used" id="downloads_used" value="<?php echo esc_attr($subscription->downloads_used); ?>" min="0" class="small-text">
                    <p class="description"><?php esc_html_e('می‌توانید این مقدار را تغییر دهید', 'eshterakyar'); ?></p>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="update_subscription" class="button button-primary" value="<?php esc_attr_e('ذخیره تغییرات', 'eshterakyar'); ?>">
            <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-subscriptions')); ?>" class="button"><?php esc_html_e('انصراف', 'eshterakyar'); ?></a>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    function checkDatePickers() {
        if (typeof jalaliDatepicker === 'undefined') {
            setTimeout(checkDatePickers, 100);
            return;
        }
        $('#start_date, #end_date').each(function() {
            var $input = $(this);
            if (!$input.attr('data-jdp')) {
                $input.attr('data-jdp', 'true');
            }
            if (!$input.attr('data-jdp-time')) {
                $input.attr('data-jdp-time', 'true');
            }
            
            var currentValue = $input.val();
            if (currentValue && currentValue.trim() !== '') {
                if (currentValue.match(/\d{4}\/\d{2}\/\d{2}/)) {
                    setTimeout(function() {
                        var val = $input.val();
                        if (val && val.trim() !== '' && val.match(/\d{4}\/\d{2}\/\d{2}/)) {
                            $input.val(val);
                            $input.trigger('input').trigger('change');
                        }
                    }, 1000);
                }
            }
        });
    }
    checkDatePickers();
});
</script>
