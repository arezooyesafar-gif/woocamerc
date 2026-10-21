

jQuery(document).ready(function($) {
    $('.eshterakyar-download-button').on('click', function(e) {
        if (typeof eshterakyar !== 'undefined' && eshterakyar.i18n && eshterakyar.i18n.confirm_download) {
            if (!confirm(eshterakyar.i18n.confirm_download)) {
                e.preventDefault();
                return false;
            }
        }
    });

    $(document).on('click', '.eshterakyar-style-2 .eshterakyar-download-toggle-button', function(e) {
        e.preventDefault();
        var $wrapper = $(this).closest('.eshterakyar-style-wrapper');
        if (!$wrapper.length) {
            return;
        }

        var $modal = $wrapper.find('.eshterakyar-download-modal').first();
        if ($modal.length) {
            $modal.fadeIn(150);
            $('body').addClass('eshterakyar-modal-open');
        }
    });

    $(document).on('click', '.eshterakyar-download-modal-close, .eshterakyar-download-modal-backdrop', function(e) {
        e.preventDefault();
        var $modal = $(this).closest('.eshterakyar-download-modal');
        $modal.fadeOut(150, function() {
            $('body').removeClass('eshterakyar-modal-open');
        });
    });

    $(document).on('click', '.eshterakyar-style-2 .eshterakyar-subscription-toggle-button', function(e) {
        e.preventDefault();
        var $button = $(this);
        var $wrapper = $button.closest('.eshterakyar-style-wrapper');
        if (!$wrapper.length) {
            return;
        }

        var $box = $wrapper.find('.eshterakyar-subscription-collapsible').first();
        if ($box.length) {
            $box.stop(true, true).slideToggle(300, function() {
                if ($box.is(':visible')) {
                    $button.addClass('active');
                } else {
                    $button.removeClass('active');
                }
            });
        }
    });

    $(document).on('click', '.eshterakyar-subscription-badge.eshterakyar-badge-has-tooltip', function(e) {
        if (window.matchMedia('(hover: hover)').matches) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();
        var $badge = $(this);
        var isOpen = $badge.hasClass('is-tooltip-open');
        $('.eshterakyar-subscription-badge.is-tooltip-open').removeClass('is-tooltip-open').attr('aria-expanded', 'false');
        if (!isOpen) {
            $badge.addClass('is-tooltip-open').attr('aria-expanded', 'true');
        }
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.eshterakyar-subscription-badge.eshterakyar-badge-has-tooltip').length) {
            $('.eshterakyar-subscription-badge.is-tooltip-open').removeClass('is-tooltip-open').attr('aria-expanded', 'false');
        }
    });
    
    
});

