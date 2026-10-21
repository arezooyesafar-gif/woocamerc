import jQuery from 'jquery';
import { Modal, Tooltip, Tab } from 'bootstrap';
import countdown from 'jquery-countdown';
import 'select2/dist/js/select2';
import './js/cbSelect';



(function ($) {
    "use strict";
    $(document).on("click", ".add_to_cart_button.product_type_simple", function () {
        if ($('.header-cart').length > 0) {
            var cart = $('.header-cart');
            var imgtodrag = $(this).parents('.product').find("img").eq(0);
            if (imgtodrag) {
                var imgclone = imgtodrag.clone()
                    .offset({
                        top: imgtodrag.offset().top,
                        left: imgtodrag.offset().left
                    })
                    .addClass('cart-item-clone')
                    .appendTo($('body'))
                    .animate({
                        'top': cart.offset().top + 10,
                        'left': cart.offset().left + 10,
                        'width': 75,
                        'height': 75
                    }, 1000);


                imgclone.animate({
                    'width': 0,
                    'height': 0
                }, function () {
                    $(this).detach()
                });
            }
        }
    });


    $(document).on("click", ".add-product-favo, .dislike_product", function () {
        var thisItem = $(this);
        if (!thisItem.hasClass('login_req')) {
            thisItem.addClass('loading');
            jQuery.post(WP_OBJ.ajax_url, {
                'action': 'pooshe_like_ajax',
                'id': thisItem.attr('data-id'),
            }, function (response) {
                if (response.status) {
                    if (response.status_code == 1) {
                        thisItem.addClass("liked");
                    } else if (response.status_code == 2) {
                        thisItem.removeClass("liked");
                    }
                    thisItem.removeClass('loading');
                    if (thisItem.hasClass('dislike_product')) {
                        location.reload();
                    }
                }
            });
        } else {
            $('#header-popup-login').modal('show');
        }
        return false;
    });

    $('.woocommerce-ordering select.orderby').each(function () {
        var select = $(this);
        $(this).hide();
        var html = '<ul class="order-tabs">';
        $(this).find('option').each(function () {
            var isActive = select.val() == $(this).attr('value') ? 'active' : '';
            html += '<li data-value="' + $(this).attr('value') + '" class="' + isActive + '">' + $(this).text().replace("مرتب سازی بر اساس", "").replace("مرتب سازی", "").replace("مرتب‌سازی بر اساس", "").replace("مرتب‌سازی", "") + '</li>';
        });
        html += '</ul>';
        $('.woocommerce-products-header .woocommerce-products-header__title').after(html);
    });

    $(document).on("click", ".woocommerce-products-header .order-tabs li", function () {
        $('.woocommerce-ordering select.orderby').val($(this).attr('data-value'));
        $('.woocommerce-ordering').submit();
    });
    var wcTabs;
    var wcTabsContent;
    if ($('.wc-tabs').length > 0) {
        wcTabs = new Swiper('.wc-tabs', {
            spaceBetween: 20,
            slidesPerView: 'auto',
            freeMode: true,
            watchSlidesVisibility: true,
            hashNavigation: true,
            slideToClickedSlide: true,
            preventClicks: false,
            preventClicksPropagation: false,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });

        wcTabsContent = new Swiper('.wc-tab-contents-inner', {
            spaceBetween: 10,
            autoHeight: true,
            thumbs: {
                swiper: wcTabs
            },
            hashNavigation: true,
            slideToClickedSlide: true,
            simulateTouch: false,

        });

        wcTabsContent.on('slideChangeTransitionEnd', function () {
            //productSticky.updateSticky();
            setInterval(function () {
                wcTabsContent.updateAutoHeight(100);
                //productSticky.updateSticky();
            }, 500);
        });
        $(window).resize(function () {
            wcTabsContent.updateAutoHeight(100);
            //productSticky.updateSticky();
        });
    }


    var productVideo;
    var productSticky;
    $(document).ready(function () {
        if ($('.product-video-slider').length > 0) {
            productVideo = new Swiper('.product-video-slider', {
                spaceBetween: 20,
                slidesPerView: 1,
                navigation: {
                    nextEl: '.cb-swiper-button-next',
                    prevEl: '.cb-swiper-button-prev',
                },
                autoHeight: true,
                simulateTouch: false,
            });
        }
    });

    $('.woocommerce-product-gallery--with-images').show();

    $(window).scroll(function () {
        if ($('.add-to-cart-product').length <= 0) {
            return;
        }
        if ($(this).scrollTop() > $('.add-to-cart-product').offset().top + $('.add-to-cart-product').height()) {
            $('.product-responsive-bottom').addClass('show');
        } else {
            $('.product-responsive-bottom').removeClass('show');
        }
    });
    $('.cb-chips').each(function () {
        $(this).append('<div class="cb-chips">');
        $(this).append('<div class="add-box"><input type="text" name="' + $(this).attr('data-name') + '[]" placeholder="' + $(this).attr('data-phold') + '" class="form-control text-field"><button class="btn add-item" type="button"><i class="far fa-plus"></i></button></div>');
        $(this).append('<ul class="chip-items">');
        $(this).append('</ul>');
        $(this).append('</div>');
    });

    $(document).on("keypress", ".cb-chips input.text-field", function (e) {
        if (e.which === 13) {
            $(this).parents('.add-box').find('.add-item').click();
            return false;
        }
    });

    $(document).on("click", ".cb-chips .add-box .add-item", function () {
        var parent = $(this).parents('.cb-chips');
        var txt_inp = parent.find('.text-field').val();
        if (txt_inp !== "") {
            var div = document.createElement("div");
            div.innerHTML = txt_inp;
            parent.find('.chip-items').prepend('<li><span>' + div.innerText + '</span><button class="remove-item" type="button"><i class="far fa-times"></i></button><input type="hidden" name="' + parent.attr('data-name') + '[]" value="' + txt_inp + '"></li>');
            parent.find('.text-field').val("");
        }
    });

    $(document).on("click", ".cb-chips .chip-items .remove-item", function () {
        $(this).parents('li').remove();
    });

    $('.comment-form-cookies-consent').addClass('ns-checkbox');


    $('.card-style button').click(function () {
        if ($(this).hasClass('list-style')) {
            localStorage.setItem("shop_cards", "list");
            $('body.archive.woocommerce-page').addClass('card-list-style');
        } else {
            localStorage.setItem("shop_cards", "grid");
            $('body.archive.woocommerce-page').removeClass('card-list-style');
        }
    });
    if (localStorage.getItem("shop_cards") === "list") {
        $('.card-style button.list-style').click();
    } else if (localStorage.getItem("shop_cards") === "grid") {
        $('.card-style button.grid-style').click();
    }
    $('.smart-hover').each(function () {
        var pageOffset = $(window).width();
        $(this).addClass('initialized');
        if ($(this).offset().left - 20 < 0) {
            $(this).addClass('to-right');
        } else {
            $(this).addClass('to-left');
        }
        if ($(this).offset().top + 20 < 0) {
            $(this).addClass('to-bottom');
        }
    });

    function pooshe_header_search(obj) {
        var parents = obj.parents('.header-search');
        if (parents.find('.search-field').val() === "") {
            return;
        }
        var bodyFormData = {action: 'pooshe_ajax_search'};
        $.each(parents.find('form.search_form')[0], function (key, val) {
            if (val.name !== undefined && val.name !== "") {
                bodyFormData[val.name] = val.value;
            }
        });
        parents.removeClass('typing');
        parents.addClass('searching');
        $.ajax({
            method: "POST",
            url: WP_OBJ.ajax_url,
            data: bodyFormData
        }).done(function (msg) {
            if (msg.status) {
                if (msg.data.length > 0) {
                    parents.removeClass('not-loaded');
                    parents.addClass('loaded');
                } else {
                    parents.removeClass('loaded');
                    parents.addClass("not-loaded");
                }
                var output = '';
                $.each(msg.data, function (key, item) {
                    output += '<li>' +
                        '<a href="' + item.link + '">' +
                        '<figure>';
                    if (item.img !== false) {
                        output += item.img;
                    }
                    output += '</figure>' +
                        '<span class="inner">' +
                        '<span class="title">' + item.title + '</span>' +
                        '<span class="pooshe-price">' + item.price + '</span>' +
                        '</span>' +
                        '</a>' +
                        '</li>';
                });
                parents.find('.search-results ul.csb').html(output);
                parents.find('.search-results .csb-outer').addClass('ps-native-scroll');
            } else {
                parents.removeClass('loaded');
                parents.addClass("not-loaded");
                parents.find('.search-results ul.csb').html('');
            }
            parents.removeClass('searching');
            parents.find('.search-field').select();
        });
    }

    $('.header-search-select2').select2();

    $(document).on('change', '.header-search.is-ajax form.search_form select', function () {
        pooshe_header_search($(this));
    });
    var searchInputTimer;
    $(document).on('keyup', '.header-search.is-ajax form.search_form .search-field', function () {
        clearTimeout(searchInputTimer);
        if ($(this).parents('.header-search').find('.search-field').val() === "") {
            $(this).parents('.header-search').removeClass('typing');
            return;
        }
        $(this).parents('.header-search').addClass('typing');
        var self = $(this);
        searchInputTimer = setTimeout(function () {
            pooshe_header_search(self);
        }, 1000);

    });
    $(document).on('submit', '#header-popup-login form.login-form', function () {
        var bodyFormData = {action: 'pooshe_ajax_login'};
        $.each($(this)[0], function (key, val) {
            if (val.name !== undefined && val.name !== "") {
                bodyFormData[val.name] = val.value;
            }
        });
        $("#header-popup-login").addClass('sending');
        $.ajax({
            method: "POST",
            url: WP_OBJ.ajax_url,
            data: bodyFormData
        }).done(function (msg) {
            if (msg.status) {
                $("#header-popup-login .form-col .loading span").html('');
                $("#header-popup-login").addClass('success');
                location.reload();
            } else {
                $("#header-popup-login").addClass('warning');
                setTimeout(function () {
                    $("#header-popup-login").removeClass('warning');
                    $("#header-popup-login").removeClass('sending');
                    $("#header-popup-login .form-col .loading span").html('');
                }, 3000);
            }
            $("#header-popup-login .form-col .loading span").html(msg.data);


        });
        return false;
    });
    $('ul.woocommerce-mini-cart').addClass('ps-native-scroll');


    $(document).on('mouseenter', '#cat-drop-stack li', function () {
        $(this).addClass('show');
        $(this).children('ul').stop().slideDown('fast');
        return false;
    });
    $(document).on('mouseleave', '#cat-drop-stack li', function () {
        $(this).removeClass('show');
        $(this).children('ul').stop().slideUp('fast');
        return false;
    });

    $('.pooshe-timer').each(function () {
        var finalDate = $(this).attr('endtime');
        $(this).countdown(finalDate)
            .on('update.countdown', function (event) {
                var format = '\n' +
                    '        <li class="hour">\n' +
                    '            <span class="number">%-I</span>\n' +
                    '            <span class="format">ساعت</span>\n' +
                    '        </li>\n' +
                    '        <li class="min">\n' +
                    '            <span class="number">%M</span>\n' +
                    '            <span class="format">دقیقه</span>\n' +
                    '        </li>\n' +
                    '        <li class="sec">\n' +
                    '            <span class="number">%S</span>\n' +
                    '            <span class="format">ثانیه</span>\n' +
                    '        </li>';
                $(this).html(event.strftime(format));
            })
            .on('finish.countdown', function () {
                location.reload();
            });

    });

    $(document).on('click', '.to-top', function () {
        $("html, body").animate({scrollTop: 0}, 600);
        return false;
    });

    $(document).on('click', '.product-responsive-bottom .add-cart-btn', function () {
        $("html, body").animate({scrollTop: $('.add-to-cart-product').offset().top - 300}, 300);
        return false;
    });

    $(document).on("click", ".responsive-menu .toggle-menu", function () {
        $(this).next(".menu-popup").addClass('show');
    });

    $(document).on("click", ".responsive-menu .overlay", function () {
        $(this).prev(".menu-popup").removeClass('show');
    });

    $(".responsive-menu nav li.menu-item-has-children,footer#footer .footer-copyright nav.footer-menu li.menu-item-has-children").each(function () {
        $(this).children('a').after('<span class="toggle"><i class="fal fa-angle-down"></i></span>');
    });

    $(document).on("click", ".responsive-menu nav li.menu-item-has-children span.toggle", function () {
        $(this).toggleClass('active');
        $(this).next('ul').slideToggle('fast');
    });

    $(document).on("click", ".woocommerce-MyAccount-navigation li.responsive-toggle", function () {
        $(this).toggleClass('show');
    });

    $('.pooshe-carousel').each(function () {
        var targetTag = $(this).find('.swiper-container');
        var opts = targetTag.attr('data-options') !== undefined ? JSON.parse(targetTag.attr('data-options')) : [];
        if (targetTag.length <= 0) {
            return false;
        }
        var slider = new Swiper(targetTag[0], opts);
    });


    $('.elementor-widget-pooshe_shop_carousel').each(function () {
        let $scope = $(this);

        function car_init($scope) {
            $scope.find('.pooshe-elementor-shop-carousel').each(function () {
                var targetTag = $(this).find('.swiper-container');
                var opts = targetTag.attr('data-options') !== undefined ? JSON.parse(targetTag.attr('data-options')) : [];
                var slider = new Swiper(targetTag, opts);
            });
            $(window).resize();
        }

        car_init($scope);
        $scope.find('.carouselWidgetTabs li').on('click', function () {
            var pr = $(this).parents('.carouselWidgetTabs').find('.close').click();
        });
        $scope.find('.widget-title li').on('click', function () {
            var self = $(this);
            var options = self.attr('data-options');
            if (!self.hasClass('active') && !self.hasClass('loading')) {
                self.parents('.widget-container').addClass('content-loading');
                $.ajax({
                    method: "POST",
                    url: WP_OBJ.ajax_url,
                    data: {
                        action: "pooshe_ajax_carousel",
                        options: options
                    }
                }).done(function (response) {
                    if (response.status) {
                        if (self.parents('.widget-container').hasClass('swiper-container')) {
                            self.parents('.widget-container').find('.swiper-wrapper').html(response.data.content);
                            car_init($scope);
                        } else {
                            self.parents('.widget-container').find('.row').html(response.data.content);
                        }
                        self.parents('.widget-container').removeClass('content-loading');
                        self.parents('.widget-container').find('.widget-title li').removeClass('active');
                        self.parents('.widget-container').find('.carousel-archive-link').attr('href', response.data.archive);
                        self.addClass('active');
                    }
                });
            }
        });
    });

    $(document).on('submit', 'form.cart', function (e) {
        if (WP_OBJ.ajax_add_to_cart !== '1') {
            return;
        }
        var $form = $(this);
        if ($form.closest('.product').hasClass('product-type-grouped') || $form.closest('.product').hasClass('product-type-external')) {
            return;
        }
        e.preventDefault();
        var $button = $form.find('.single_add_to_cart_button');
        if ($button.hasClass('disabled')) {
            return;
        }
        var formData = $form.serializeArray();
        var data = { action: 'pooshe_ajax_add_to_cart' };
        $.each(formData, function (i, field) {
            if (field.name === 'add-to-cart') {
                data.product_id = field.value;
            } else if (field.name === 'product_id') {
                data.product_id = field.value;
            } else if (field.name === 'variation_id') {
                data.variation_id = field.value;
            } else if (field.name.indexOf('attribute_') === 0) {
                if (!data.variation) {
                    data.variation = {};
                }
                data.variation[field.name] = field.value;
            } else if (field.name === 'quantity') {
                data.quantity = field.value;
            }
        });
        if (!data.product_id) {
            data.product_id = $button.val();
        }
        $button.addClass('loading');
        $.ajax({
            method: 'POST',
            url: WP_OBJ.ajax_url,
            data: data
        }).done(function (response) {
            if (response.success) {
                $(document.body).trigger('wc_fragment_refresh');
                $.each(response.data.fragments, function (key, value) {
                    $(key).replaceWith(value);
                });
                var cart = $('.header-cart');
                if (cart.length > 0) {
                    var img = $form.closest('.product').find('.woocommerce-product-gallery__image img, .product-logo img').eq(0);
                    if (img.length) {
                        var imgclone = img.clone()
                            .offset({ top: img.offset().top, left: img.offset().left })
                            .addClass('cart-item-clone')
                            .appendTo($('body'))
                            .animate({
                                top: cart.offset().top + 10,
                                left: cart.offset().left + 10,
                                width: 75,
                                height: 75
                            }, 1000);
                        imgclone.animate({ width: 0, height: 0 }, function () {
                            $(this).detach();
                        });
                    }
                }
                if (response.data.message) {
                    var notice = $('<div class="pooshe-notice success"><span>' + response.data.message + '</span></div>');
                    $form.after(notice);
                    setTimeout(function () { notice.fadeOut(function () { $(this).remove(); }); }, 3000);
                }
            } else {
                var errMsg = response.data.message || 'خطا در افزودن به سبد خرید';
                var notice = $('<div class="pooshe-notice error"><span>' + errMsg + '</span></div>');
                $form.after(notice);
                setTimeout(function () { notice.fadeOut(function () { $(this).remove(); }); }, 3000);
            }
        }).always(function () {
            $button.removeClass('loading');
        });
    });

    $(document).on('click', '.pooshe-copy-link', function () {
        var $btn = $(this);
        var link = $btn.data('link');
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(link).then(function () {
                $btn.addClass('copied').html('<i class="fal fa-check"></i> ' + 'کپی شد');
                setTimeout(function () {
                    $btn.removeClass('copied').html('<i class="fal fa-copy"></i> ' + 'کپی لینک');
                }, 2000);
            });
        } else {
            var $temp = $('<input>');
            $('body').append($temp);
            $temp.val(link).select();
            document.execCommand('copy');
            $temp.remove();
            $btn.addClass('copied').html('<i class="fal fa-check"></i> ' + 'کپی شد');
            setTimeout(function () {
                $btn.removeClass('copied').html('<i class="fal fa-copy"></i> ' + 'کپی لینک');
            }, 2000);
        }
    });

    $(document).on('click', '.v2-accordion-trigger', function () {
        var $trigger = $(this);
        var $item = $trigger.closest('.v2-accordion-item');
        var $body = $item.find('.v2-accordion-body');

        $trigger.toggleClass('active');
        $body.slideToggle(200);
    });

    $(document).on('click', '.btn-comment-like, .btn-comment-dislike', function () {
        var $btn = $(this);
        var commentId = $btn.data('comment-id');
        var voteType = $btn.data('action');
        var nonce = $btn.data('nonce');
        var $likeBtn = $btn.closest('.comment-likes').find('.btn-comment-like');
        var $dislikeBtn = $btn.closest('.comment-likes').find('.btn-comment-dislike');
        var $likeCount = $likeBtn.find('.like-count');
        var $dislikeCount = $dislikeBtn.find('.dislike-count');

        if ($btn.hasClass('loading')) {
            return;
        }

        $btn.addClass('loading');

        $.ajax({
            method: 'POST',
            url: WP_OBJ.ajax_url,
            data: {
                action: 'pooshe_ajax_comment_vote',
                comment_id: commentId,
                vote_type: voteType,
                nonce: nonce
            }
        }).done(function (response) {
            if (response.success) {
                $likeCount.text(response.data.like_count);
                $dislikeCount.text(response.data.dislike_count);
                $likeBtn.removeClass('voted loading');
                $dislikeBtn.removeClass('voted loading');
                if (response.data.user_vote === 'like') {
                    $likeBtn.addClass('voted');
                } else if (response.data.user_vote === 'dislike') {
                    $dislikeBtn.addClass('voted');
                }
            } else {
                var msg = response.data && response.data.message ? response.data.message : 'خطا';
                var notice = $('<div class="pooshe-notice error"><span>' + msg + '</span></div>');
                $btn.after(notice);
                setTimeout(function () { notice.fadeOut(function () { $(this).remove(); }); }, 3000);
                $btn.removeClass('loading');
            }
        }).fail(function () {
            $btn.removeClass('loading');
        });
    });

    $(document).on('submit', '#commentform', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $submitBtn = $form.find('.form-submit .submit, .btn-primary');
        var formData = $form.serializeArray();
        var commentData = {};

        $.each(formData, function (i, field) {
            if (field.name.indexOf('[]') !== -1) {
                var key = field.name.replace('[]', '');
                if (!commentData[key]) {
                    commentData[key] = [];
                }
                commentData[key].push(field.value);
            } else {
                commentData[field.name] = field.value;
            }
        });

        $submitBtn.addClass('loading').prop('disabled', true);

        $.ajax({
            method: 'POST',
            url: WP_OBJ.ajax_url,
            data: {
                action: 'pooshe_ajax_submit_comment',
                comment_data: commentData
            }
        }).done(function (response) {
            if (response.success) {
                var $list = $form.closest('.post-comments, #reviews').find('.comment-list, .commentlist');
                $list.prepend(response.data.comment_html);
                $form[0].reset();
                var notice = $('<div class="pooshe-notice success"><span>' + response.data.message + '</span></div>');
                $form.before(notice);
                setTimeout(function () { notice.fadeOut(function () { $(this).remove(); }); }, 5000);
            } else {
                var msg = response.data && response.data.message ? response.data.message : 'خطا در ارسال دیدگاه';
                var notice = $('<div class="pooshe-notice error"><span>' + msg + '</span></div>');
                $form.before(notice);
                setTimeout(function () { notice.fadeOut(function () { $(this).remove(); }); }, 3000);
            }
        }).always(function () {
            $submitBtn.removeClass('loading').prop('disabled', false);
        });
    });

    $(document).on('change', '#comment-sort', function () {
        var url = new URL(window.location.href);
        url.searchParams.set('comment_sort', $(this).val());
        url.searchParams.delete('_pjax');
        window.location.href = url.toString();
    });

})(jQuery);

