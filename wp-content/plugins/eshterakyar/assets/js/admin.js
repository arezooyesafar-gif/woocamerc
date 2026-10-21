

jQuery(document).ready(function($) {

    if (typeof $.fn.pointer === 'undefined') {
        $.fn.pointer = function() {
            return this;
        };
    }
    
    function removeWordPressGenerator() {
        $('meta[name="generator"]').remove();
        $('#wpfooter').hide();
        $('.wp-generator').remove();
        var $footer = $('#wpfooter');
        if ($footer.length && $footer.find('*').length < 50) {
            $footer.find('a, span').each(function() {
                var $el = $(this), t = $el.text();
                if (t && (t.indexOf('وردپرس') !== -1 || t.indexOf('WordPress') !== -1) && $el.children().length === 0) {
                    $el.remove();
                }
            });
        }
    }
    
    removeWordPressGenerator();
    $(window).on('load', removeWordPressGenerator);
    
    function removeDefaultWordPressNotices() {
        if (!$('body.eshterakyar-fullscreen').length) {
            return;
        }
        
        $('.notice:not(.eshterakyar-notice)').each(function() {
            var $notice = $(this);
            var noticeText = $notice.text().trim();
            
            if (!$notice.hasClass('eshterakyar-notice')) {
                var isPluginNotice = (
                    noticeText.indexOf('پلن با موفقیت') !== -1 || 
                    noticeText.indexOf('تنظیمات با موفقیت') !== -1 ||
                    noticeText.indexOf('خطا در ذخیره پلن') !== -1 ||
                    noticeText.indexOf('پلن با موفقیت حذف شد') !== -1 ||
                    noticeText.indexOf('پلن با موفقیت به‌روزرسانی شد') !== -1
                );
                
                if (!isPluginNotice) {
                    $notice.remove();
                } else {
                    $notice.addClass('eshterakyar-notice');
                }
            }
        });
        
        $('.update-nag, .error:not(.eshterakyar-notice-error), .updated:not(.eshterakyar-notice-success)').not('.eshterakyar-notice').remove();
        
        var seenTexts = {};
        $('.notice').each(function() {
            var $notice = $(this);
            var noticeText = $notice.text().trim();
            if (noticeText && seenTexts[noticeText]) {
                if (!$notice.hasClass('eshterakyar-notice')) {
                $notice.remove();
                } else {
                    $('.notice').not($notice).filter(function() {
                        return $(this).text().trim() === noticeText && !$(this).hasClass('eshterakyar-notice');
                    }).remove();
                }
            } else if (noticeText) {
                seenTexts[noticeText] = true;
            }
        });
        
        $('.notice:not(.eshterakyar-notice)').each(function() {
            var $notice = $(this);
            var noticeText = $notice.text().trim();
            if ($('.eshterakyar-notice').filter(function() {
                return $(this).text().trim() === noticeText;
            }).length > 0) {
                $notice.remove();
            }
        });
    }
    
    removeDefaultWordPressNotices();
    $(window).on('load', removeDefaultWordPressNotices);

    if ($('body.eshterakyar-fullscreen').length) {
        var observer = new MutationObserver(function() {
            removeDefaultWordPressNotices();
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }
    
    $('.eshterakyar-cancel-subscription').on('click', function(e) {
        e.preventDefault();
        
        var subscriptionId = $(this).data('subscription-id');
        var button = $(this);
        
        if (!confirm('آیا مطمئن هستید که می‌خواهید این اشتراک را لغو کنید؟')) {
            return false;
        }
        
        $.ajax({
            url: eshterakyar_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'eshterakyar_cancel_subscription',
                subscription_id: subscriptionId,
                nonce: eshterakyar_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
    
    if (typeof jalaliDatepicker !== 'undefined') {
        
        var datepickerInitialized = false;
        
        var isUpdatingGregorian = false;
        
        function convertPersianToGregorian(persianDate) {
            if (!persianDate || persianDate.trim() === '') {
                return '';
            }
            
            if (typeof jalaali === 'undefined') {
                return '';
            }
            
            var parts = persianDate.trim().split(' ');
            var datePart = parts[0];
            var timePart = parts[1] || '00:00:00';
            
            var dateParts = datePart.split('/');
            if (dateParts.length !== 3) {
                return '';
            }
            
            var jy = parseInt(dateParts[0], 10);
            var jm = parseInt(dateParts[1], 10);
            var jd = parseInt(dateParts[2], 10);
            
            if (isNaN(jy) || isNaN(jm) || isNaN(jd) || jy < 1 || jm < 1 || jm > 12 || jd < 1 || jd > 31) {
                return '';
            }
            
            try {
                var gregorian = jalaali.jalaaliToGregorian(jy, jm, jd);
                
                var gregorianDate = gregorian.gy + '-' + 
                    String(gregorian.gm).padStart(2, '0') + '-' + 
                    String(gregorian.gd).padStart(2, '0');
                
                if (timePart && timePart.trim() !== '') {
                    var timeParts = timePart.split(':');
                    if (timeParts.length >= 2) {
                        var hour = parseInt(timeParts[0], 10) || 0;
                        var minute = parseInt(timeParts[1], 10) || 0;
                        var second = timeParts.length >= 3 ? (parseInt(timeParts[2], 10) || 0) : 0;
                        gregorianDate += ' ' + String(hour).padStart(2, '0') + ':' + 
                                        String(minute).padStart(2, '0') + ':' + 
                                        String(second).padStart(2, '0');
                    } else {
                        gregorianDate += ' 00:00:00';
                    }
                } else {
                    gregorianDate += ' 00:00:00';
                }
                
                return gregorianDate;
            } catch (e) {
                if (typeof console !== 'undefined' && console.error) {
                    console.error('خطا در تبدیل تاریخ شمسی به میلادی:', e);
                }
                return '';
            }
        }
        
        function updateGregorianDate($input) {
            if (isUpdatingGregorian) {
                return;
            }
            
            var persianDate = $input.val();
            if (!persianDate || persianDate.trim() === '') {
                var $hidden = $input.siblings('input[type="hidden"]');
                if ($hidden.length) {
                    $hidden.val('');
                }
                return;
            }
            
            isUpdatingGregorian = true;
            
            var $hidden = null;
            var inputId = $input.attr('id');
            
            if (inputId) {
                if (inputId === 'date_from') {
                    $hidden = $('#date_from_gregorian');
                } else if (inputId === 'date_to') {
                    $hidden = $('#date_to_gregorian');
                } else if (inputId === 'downloads_date_from') {
                    $hidden = $('#downloads_date_from_gregorian');
                } else if (inputId === 'downloads_date_to') {
                    $hidden = $('#downloads_date_to_gregorian');
                } else if (inputId === 'start_date') {
                    $hidden = $('#start_date_gregorian');
                } else if (inputId === 'end_date') {
                    $hidden = $('#end_date_gregorian');
                } else if (inputId === 'manual_start_date') {
                    $hidden = $('#manual_start_date_gregorian');
                } else if (inputId === 'manual_end_date') {
                    $hidden = $('#manual_end_date_gregorian');
                } else if (inputId === 'modal_manual_start_date') {
                    $hidden = $('#modal_manual_start_date_gregorian');
                } else if (inputId === 'modal_manual_end_date') {
                    $hidden = $('#modal_manual_end_date_gregorian');
                }
            }
            
            if (!$hidden || $hidden.length === 0) {
                $hidden = $input.siblings('input[type="hidden"]');
            }
            
            if ($hidden && $hidden.length) {
                var gregorianDate = convertPersianToGregorian(persianDate);
                if (gregorianDate) {
                    $hidden.val(gregorianDate);
                }
            }
            
            setTimeout(function() {
                isUpdatingGregorian = false;
            }, 200);
        }
        
        function initDatePickers() {
            if (datepickerInitialized) {
                return;
            }
            
            if (typeof jalaliDatepicker === 'undefined') {
                setTimeout(initDatePickers, 100);
                return;
            }
            
            if (typeof jalaliDatepicker.startWatch !== 'function') {
                setTimeout(initDatePickers, 100);
                return;
            }
            
            var datepickerOptions = {
                date: true,
                time: true, 
                hasSecond: false,
                autoShow: true,
                autoHide: true,
                hideAfterChange: true,
                persianDigits: false,
                separatorChars: {
                    date: '/',
                    between: ' ',
                    time: ':'
                }
            };
            
            try {
                if (!datepickerInitialized) {
                    jalaliDatepicker.startWatch(datepickerOptions);
                    datepickerInitialized = true;
                    
                    $(document).off('change.persianDateInput').on('change.persianDateInput', '.persian-date-input', function() {
                        var $input = $(this);
                        clearTimeout($input.data('datepicker-timeout'));
                        $input.data('datepicker-timeout', setTimeout(function() {
                            updateGregorianDate($input);
                        }, 500));
                    });
                    
                    setTimeout(function() {
                        $('.persian-date-input').each(function() {
                            var $input = $(this);
                            var $hidden = $input.siblings('input[type="hidden"]');
                            if ($hidden.length && (!$hidden.val() || $hidden.val().trim() === '')) {
                                if ($input.val() && $input.val().trim() !== '') {
                                    updateGregorianDate($input);
                                }
                            }
                        });
                    }, 1000);
                }
            } catch (e) {
                if (typeof console !== 'undefined' && console.error) {
                    console.error('خطا در راه‌اندازی datepicker:', e);
                }
            }
        }
        
        setTimeout(initDatePickers, 300);
        
        $('form.eshterakyar-advanced-filters').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var hasDateInputs = false;
            
            $form.find('.persian-date-input').each(function() {
                var $input = $(this);
                var $hidden = $input.siblings('input[type="hidden"]');
                
                if ($hidden.length) {
                    hasDateInputs = true;
                    var persianDate = $input.val();
                    
                    if (persianDate && persianDate.trim() !== '') {
                        var gregorianDate = convertPersianToGregorian(persianDate);
                        if (gregorianDate) {
                            var dateOnly = gregorianDate.split(' ')[0];
                            $hidden.val(dateOnly);
                        } else {
                            $hidden.val('');
                        }
                    } else {
                        $hidden.val('');
                    }
                }
            });
            
            $form[0].submit();
            
            return false;
        });
    }

    function initAutoMembershipSettings() {
        var $tab = $('#auto-membership');
        if (!$tab.length || typeof eshterakyar_admin === 'undefined') {
            return;
        }

        var $master = $('#auto_membership_enabled');
        var $fields = $tab.find('.eshterakyar-auto-membership-fields input, .eshterakyar-auto-membership-fields select, .eshterakyar-auto-membership-fields fieldset');
        var $batchWrap = $('#eshterakyar-auto-membership-batch-wrap');
        var $runBtn = $('#eshterakyar-run-auto-membership-batch');
        var $spinner = $('#eshterakyar-auto-membership-spinner');
        var $progress = $('#eshterakyar-auto-membership-progress');
        var $progressFill = $('#eshterakyar-auto-membership-progress-fill');
        var $progressText = $('#eshterakyar-auto-membership-progress-text');
        var $result = $('#eshterakyar-auto-membership-batch-result');
        var $refreshCount = $('#eshterakyar-refresh-eligible-count');
        var $eligibleCount = $('#eshterakyar-auto-membership-eligible-count');
        var $lastBatch = $('#eshterakyar-auto-membership-last-batch');
        var running = false;

        function toggleFields() {
            var enabled = $master.is(':checked');
            $fields.prop('disabled', !enabled);
            var existingEnabled = enabled && $('#auto_membership_existing_users').is(':checked');
            $batchWrap.toggle(enabled);
            $runBtn.prop('disabled', !existingEnabled || running);
        }

        $master.on('change', toggleFields);
        $('#auto_membership_existing_users').on('change', toggleFields);
        toggleFields();

        $refreshCount.on('click', function(e) {
            e.preventDefault();
            $refreshCount.prop('disabled', true);
            $.post(eshterakyar_admin.ajax_url, {
                action: 'eshterakyar_auto_membership_count',
                nonce: eshterakyar_admin.nonce,
                force_refresh: 1
            }).done(function(response) {
                if (response.success && response.data) {
                    $eligibleCount.text(response.data.count);
                }
            }).always(function() {
                $refreshCount.prop('disabled', false);
            });
        });

        function runBatch(offset, totals) {
            totals = totals || { processed: 0, assigned: 0, skipped: 0, failed: 0 };

            $.post(eshterakyar_admin.ajax_url, {
                action: 'eshterakyar_auto_membership_batch',
                nonce: eshterakyar_admin.nonce,
                offset: offset
            }).done(function(response) {
                if (!response.success) {
                    var msg = (response.data && response.data.message) ? response.data.message : 'خطا در اجرای دسته‌ای';
                    $result.removeAttr('hidden').addClass('notice notice-error').html('<p>' + msg + '</p>');
                    running = false;
                    $spinner.removeClass('is-active');
                    $runBtn.prop('disabled', false);
                    return;
                }

                var stats = response.data.stats || {};
                totals.processed += stats.processed || 0;
                totals.assigned += stats.assigned || 0;
                totals.skipped += stats.skipped || 0;
                totals.failed += stats.failed || 0;

                var percent = stats.done ? 100 : Math.min(95, Math.round((stats.next_offset || 0) / Math.max(1, (stats.next_offset || 0) + 50) * 100));
                if (stats.done) {
                    percent = 100;
                }
                $progressFill.css('width', percent + '%');
                $progressText.text(
                    'پردازش‌شده: ' + totals.processed +
                    ' | عضو شده: ' + totals.assigned +
                    ' | رد شده: ' + totals.skipped +
                    ' | خطا: ' + totals.failed
                );

                if (response.data.last_batch) {
                    $lastBatch.text(response.data.last_batch);
                }

                if (!stats.done) {
                    runBatch(stats.next_offset || 0, totals);
                    return;
                }

                $result.removeAttr('hidden').addClass('notice notice-success').html(
                    '<p>اجرای دسته‌ای تکمیل شد. عضو شده: ' + totals.assigned +
                    ' | رد شده: ' + totals.skipped +
                    ' | خطا: ' + totals.failed + '</p>'
                );
                running = false;
                $spinner.removeClass('is-active');
                $runBtn.prop('disabled', false);
                toggleFields();
                $refreshCount.trigger('click');
            }).fail(function() {
                $result.removeAttr('hidden').addClass('notice notice-error').html('<p>خطا در ارتباط با سرور.</p>');
                running = false;
                $spinner.removeClass('is-active');
                $runBtn.prop('disabled', false);
            });
        }

        $runBtn.on('click', function(e) {
            e.preventDefault();
            if (running) {
                return;
            }
            if (!confirm('آیا از اجرای عضویت خودکار برای کاربران واجد شرایط اطمینان دارید؟ ابتدا تنظیمات را ذخیره کرده باشید.')) {
                return;
            }

            running = true;
            $runBtn.prop('disabled', true);
            $spinner.addClass('is-active');
            $progress.removeAttr('hidden');
            $result.attr('hidden', true).removeClass('notice notice-success notice-error').empty();
            $progressFill.css('width', '0%');
            $progressText.text('در حال اجرا...');
            runBatch(0, { processed: 0, assigned: 0, skipped: 0, failed: 0 });
        });
    }

    initAutoMembershipSettings();
    
    
});

