<?php

if (!defined('ABSPATH')) {
    exit;
}

$plans = Eshterakyar_Plan::get_all('all');

?>
<div class="wrap eshterakyar-admin">
    <h1>
        <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-subscriptions')); ?>" class="page-title-action">
            <span class="dashicons dashicons-arrow-right-alt"></span>
            <?php esc_html_e('بازگشت به لیست اشتراک‌ها', 'eshterakyar'); ?>
        </a>
        <?php esc_html_e('ایجاد اشتراک دستی', 'eshterakyar'); ?>
    </h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('eshterakyar_create_manual_subscription', 'eshterakyar_manual_subscription_nonce'); ?>
        <input type="hidden" name="create_manual_subscription" value="1">
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="manual_user_id"><?php esc_html_e('کاربر', 'eshterakyar'); ?> <span style="color: red;">*</span></label>
                </th>
                <td>
                    <div class="eshterakyar-user-search-dropdown">
                        <div class="eshterakyar-dropdown-header" id="user_dropdown_header">
                            <input type="text" id="user_search" class="eshterakyar-dropdown-search" placeholder="<?php esc_attr_e('جستجوی کاربر (نام، ایمیل)...', 'eshterakyar'); ?>" autocomplete="off">
                            <span class="dashicons dashicons-arrow-down-alt2 eshterakyar-dropdown-arrow"></span>
                        </div>
                        <div class="eshterakyar-dropdown-list" id="user_dropdown_list" style="display: none;">
                            <div class="eshterakyar-dropdown-items" id="user_dropdown_items">
                            </div>
                        </div>
                        <select name="manual_user_id" id="manual_user_id" class="regular-text" required style="display: none;">
                            <option value=""><?php esc_html_e('-- انتخاب کاربر --', 'eshterakyar'); ?></option>
                        </select>
                    </div>
                    <p class="description"><?php esc_html_e('کاربری که می‌خواهید برای او اشتراک ایجاد کنید', 'eshterakyar'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="manual_plan_id"><?php esc_html_e('پلن', 'eshterakyar'); ?> <span style="color: red;">*</span></label>
                </th>
                <td>
                    <select name="manual_plan_id" id="manual_plan_id" class="regular-text" required style="width: 100%; max-width: 400px;">
                        <option value=""><?php esc_html_e('-- انتخاب پلن --', 'eshterakyar'); ?></option>
                        <?php foreach ($plans as $plan_item): ?>
                            <option value="<?php echo esc_attr($plan_item->id); ?>">
                                <?php echo esc_html($plan_item->name . ' (' . number_format_i18n($plan_item->price) . ' تومان)'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php esc_html_e('پلن اشتراکی که می‌خواهید برای کاربر فعال کنید', 'eshterakyar'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="manual_start_date"><?php esc_html_e('تاریخ شروع', 'eshterakyar'); ?></label>
                </th>
                <td>
                    <?php 
                    $start_date_display = '';
                    if (function_exists('parsidate')) {
                        $start_date_display = parsidate('Y/m/d H:i', current_time('mysql'), 'per');
                    } elseif (function_exists('persian_date')) {
                        $timestamp = current_time('timestamp');
                        $start_date_display = persian_date('Y/m/d', $timestamp);
                        $start_time = date('H:i', $timestamp);
                        $start_date_display .= ' ' . $start_time;
                    } else {
                        $start_date_display = date_i18n('Y/m/d H:i', current_time('timestamp'));
                    }
                    if (!function_exists('eshterakyar_convert_persian_digits')) {
                        function eshterakyar_convert_persian_digits($str) {
                            if (empty($str)) return $str;
                            $persian_digits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
                            $english_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
                            return str_replace($persian_digits, $english_digits, $str);
                        }
                    }
                    $start_date_display = eshterakyar_convert_persian_digits($start_date_display);
                    ?>
                    <input type="text" name="manual_start_date_persian" id="manual_start_date" data-jdp data-jdp-time="true" value="<?php echo esc_attr($start_date_display); ?>" class="regular-text persian-date-input persian-datetime-input" placeholder="<?php esc_attr_e('انتخاب تاریخ و زمان شمسی', 'eshterakyar'); ?>" readonly>
                    <input type="hidden" name="manual_start_date" id="manual_start_date_gregorian" value="<?php echo esc_attr(current_time('mysql')); ?>">
                    <p class="description"><?php esc_html_e('تاریخ و زمان شروع اشتراک (پیش‌فرض: الان)', 'eshterakyar'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="manual_end_date"><?php esc_html_e('تاریخ پایان', 'eshterakyar'); ?></label>
                </th>
                <td>
                    <input type="text" name="manual_end_date_persian" id="manual_end_date" data-jdp data-jdp-time="true" value="" class="regular-text persian-date-input persian-datetime-input" placeholder="<?php esc_attr_e('انتخاب تاریخ و زمان شمسی', 'eshterakyar'); ?>" readonly>
                    <input type="hidden" name="manual_end_date" id="manual_end_date_gregorian" value="">
                    <p class="description"><?php esc_html_e('تاریخ و زمان پایان اشتراک (اگر خالی بگذارید، بر اساس مدت زمان پلن محاسبه می‌شود)', 'eshterakyar'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="manual_status"><?php esc_html_e('وضعیت', 'eshterakyar'); ?></label>
                </th>
                <td>
                    <select name="manual_status" id="manual_status" class="regular-text" style="width: 100%; max-width: 400px;">
                        <option value="active" selected><?php esc_html_e('فعال', 'eshterakyar'); ?></option>
                        <option value="inactive"><?php esc_html_e('غیرفعال', 'eshterakyar'); ?></option>
                    </select>
                    <p class="description"><?php esc_html_e('وضعیت اولیه اشتراک', 'eshterakyar'); ?></p>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-subscriptions')); ?>" class="button"><?php esc_html_e('انصراف', 'eshterakyar'); ?></a>
            <button type="submit" class="button button-primary button-large">
                <span class="dashicons dashicons-yes-alt"></span>
                <?php esc_html_e('ایجاد اشتراک', 'eshterakyar'); ?>
            </button>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    var $userDropdown = $('.eshterakyar-user-search-dropdown');
    var $userSearch = $('#user_search');
    var $userDropdownList = $('#user_dropdown_list');
    var $userDropdownItems = $('#user_dropdown_items');
    var $userDropdownHeader = $('#user_dropdown_header');
    var $userSelect = $('#manual_user_id');
    var userCurrentPage = 1;
    var userHasMore = false;
    var userLoading = false;
    var userLastTerm = '';
    var userSearchDelay;

    function renderUserItems(items, append) {
        if (!append) {
            $userDropdownItems.empty();
            $userSelect.find('option').not(':first').remove();
        }

        $.each(items, function(index, item) {
            var $item = $('<div class="eshterakyar-dropdown-item"></div>');
            $item.attr('data-value', item.id);
            $item.attr('data-search', (item.text || '').toLowerCase());
            $item.text(item.text || '');
            $userDropdownItems.append($item);

            if ($userSelect.find('option[value="' + item.id + '"]').length === 0) {
                var $option = $('<option></option>')
                    .val(item.id)
                    .text(item.text || '');
                $userSelect.append($option);
            }
        });
    }

    function loadUsers(term, page, append) {
        if (userLoading) {
            return;
        }

        userLoading = true;
        $userDropdownItems.addClass('loading');

        $.ajax({
            url: (typeof eshterakyar_admin !== 'undefined') ? eshterakyar_admin.ajax_url : ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'eshterakyar_search_users',
                nonce: (typeof eshterakyar_admin !== 'undefined') ? eshterakyar_admin.nonce : '',
                term: term,
                page: page
            }
        }).done(function(response) {
            if (response && response.success && response.data && $.isArray(response.data.items)) {
                renderUserItems(response.data.items, append);
                userHasMore = !!response.data.has_more;
                userCurrentPage = response.data.page || page;

                if (!response.data.items.length) {
                    if ($userDropdownItems.find('.no-results-message').length === 0) {
                        $userDropdownItems.append('<div class="no-results-message">نتیجه‌ای یافت نشد</div>');
                    }
                } else {
                    $userDropdownItems.find('.no-results-message').remove();
                }
            } else {
                if (!append) {
                    $userDropdownItems.empty().append('<div class="no-results-message">خطا در بارگذاری کاربران</div>');
                }
            }
        }).fail(function() {
            if (!append) {
                $userDropdownItems.empty().append('<div class="no-results-message">خطا در برقراری ارتباط با سرور</div>');
            }
        }).always(function() {
            userLoading = false;
            $userDropdownItems.removeClass('loading');
        });
    }
    
    $userDropdownHeader.on('click', function(e) {
        e.stopPropagation();
        if ($userDropdownList.is(':visible')) {
            $userDropdownList.slideUp(200);
            $userDropdownHeader.find('.eshterakyar-dropdown-arrow').removeClass('open');
        } else {
            $userDropdownList.slideDown(200);
            $userDropdownHeader.find('.eshterakyar-dropdown-arrow').addClass('open');
            $userSearch.focus();

            if (!$userDropdownItems.children().length) {
                userCurrentPage = 1;
                userLastTerm = '';
                loadUsers('', userCurrentPage, false);
            }
        }
    });
    
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.eshterakyar-user-search-dropdown').length) {
            $userDropdownList.slideUp(200);
            $userDropdownHeader.find('.eshterakyar-dropdown-arrow').removeClass('open');
        }
    });
    
    $userSearch.on('input keyup', function(e) {
        e.stopPropagation();
        var searchTerm = $(this).val().toLowerCase().trim();

        clearTimeout(userSearchDelay);
        userSearchDelay = setTimeout(function() {
            if (searchTerm === userLastTerm && $userDropdownItems.children().length) {
                return;
            }

            userLastTerm = searchTerm;
            userCurrentPage = 1;
            loadUsers(searchTerm, userCurrentPage, false);
        }, 250);
    });
    
    $userDropdownItems.on('click', '.eshterakyar-dropdown-item', function(e) {
        e.stopPropagation();
        var $item = $(this);
        var value = $item.data('value');
        var text = $item.text();
        
        $userSelect.val(value);
        $userSearch.val(text);
        $userDropdownList.slideUp(200);
        $userDropdownHeader.find('.eshterakyar-dropdown-arrow').removeClass('open');
        $userDropdownItems.find('.eshterakyar-dropdown-item').removeClass('selected');
        $item.addClass('selected');
    });

    $userDropdownList.on('scroll', function() {
        var $this = $(this);
        var scrollTop = $this.scrollTop();
        var scrollHeight = $this.prop('scrollHeight');
        var height = $this.height();

        if (userHasMore && !userLoading && scrollTop + height >= scrollHeight - 50) {
            loadUsers(userLastTerm, userCurrentPage + 1, true);
        }
    });
    
    function checkDatePickers() {
        if (typeof jalaliDatepicker === 'undefined') {
            setTimeout(checkDatePickers, 100);
            return;
        }
        $('#manual_start_date, #manual_end_date').each(function() {
            if (!$(this).attr('data-jdp')) {
                $(this).attr('data-jdp', 'true');
            }
            if (!$(this).attr('data-jdp-time')) {
                $(this).attr('data-jdp-time', 'true');
            }
        });
    }
    
    checkDatePickers();
});
</script>

