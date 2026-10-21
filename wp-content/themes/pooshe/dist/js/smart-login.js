/**
 * Smart login
 */

(function ($) {
    $(document).ready(function () {
        let login = $(".smart-login");
        if (login.length <= 0) {
            return;
        }

        if (login.find("#ns-mobile").val().length > 0) {
            login.find(".step-one-submit").removeClass("disabled");
        }
    });
    $(document).on("keyup change", ".smart-login #ns-mobile", function () {
        if ($(this).val().length > 0) {
            $(this).parents(".smart-login").find(".step-one-submit").removeClass("disabled");
        } else {
            $(this).parents(".smart-login").find(".step-one-submit").addClass("disabled");
        }
    });
    $(document).on("keyup change", ".smart-login #ns-password", function () {
        if ($(this).val().length > 0) {
            $(this).parents(".smart-login").find(".password-login-submit").removeClass("disabled");
        } else {
            $(this).parents(".smart-login").find(".password-login-submit").addClass("disabled");
        }
    });
    $(document).on("keyup change", ".smart-login #auth-code", function (e) {
        if (e.key === undefined) {
            return;
        }
        if ($(this).val().length === 5) {
            $(this).parents(".smart-login").find(".auth-login-submit").removeClass("disabled");
            $(this).parents(".smart-login").trigger("smart_login_step2");
        } else {
            $(this).parents(".smart-login").find(".auth-login-submit").addClass("disabled");
        }
    });
    $(document).on("smart_login_step1", ".smart-login", function (e) {
        if ($(this).hasClass("loading")) {
            return;
        }
        $(this).addClass('loading');
        $(this).find('.login-result').text('');
        let mobile = $(this).find("input#ns-mobile");
        mobile.attr("readonly", true);
        $.post(pooshe_obj.ajax_url, {
            'action': 'pooshe_smart_login',
            'mobile': mobile.val(),
            '_nonce': $(this).find("[name='_wpnonce']").val(),
        }, (response) => {
            $(this).removeClass('loading');

            if (!response.success) {
                $(this).find('.login-result').text(response.data.message);
                setTimeout(() => {
                    $(this).find('.login-result').text('');
                }, 5000);
                mobile.attr("readonly", false);
            } else {
                if (response.data.login.type === "mobile") {
                    $(this).trigger("smart_login_change_step", {
                        step: 2, frag: {
                            '.user-mobile': response.data.login.user
                        }
                    });
                    $(this).trigger("smart_login_step2_load", {timer: response.data.login.timer});
                }
                if (response.data.login.type === "email") {
                    $(this).trigger("smart_login_change_step", {
                        step: 3
                    });
                    $(this).trigger("smart_login_step3_load");
                }
            }
        });
    });
    $(document).on("smart_login_step2_load", ".smart-login", function (e, data) {
        let smsTimer = $(this).find('.sms-timer');
        const d = new Date();
        d.setSeconds(d.getSeconds() + data.timer);
        smsTimer.countdown(d, function (event) {
            $(this).html(event.strftime('%M:%S'));

            if (event.elapsed) {
                $(this).parents('.smart-login').find(".resend-code").show();
            } else {
                $(this).parents('.smart-login').find(".resend-code").hide();
            }
        });

        $(this).find("#auth-code").val("");
        $(this).find("#auth-code").focus();
    });
    $(document).on("smart_login_step3_load", ".smart-login", function (e, data) {
        $(this).find("#ns-password").focus();
    });
    $(document).on("smart_login_step2", ".smart-login", function (e, data) {
        if ($(this).hasClass("loading")) {
            return;
        }
        let code = $(this).find("input#auth-code");
        if (code.val().length !== 5) {
            code.focus();
            return;
        }

        $(this).addClass('loading');
        $(this).find('.login-result').text('');
        let mobile = $(this).find("input#ns-mobile");
        let redirect = $(this).find("input[name='ns-redirect']").val();
        code.attr("readonly", true);
        $.post(pooshe_obj.ajax_url, {
            'action': 'pooshe_smart_login_auth',
            'mobile': mobile.val(),
            'code': code.val(),
            'redirect': redirect,
            '_nonce': $(this).find("[name='_wpnonce']").val(),
        }, (response) => {
            $(this).find('.login-result').text(response.data.message);
            setTimeout(() => {
                $(this).find('.login-result').text('');
            }, 5000);
            if (!response.success) {
                $(this).removeClass('loading');
                code.attr("readonly", false);
            } else if (response.success && response.data.code === 200) {
                window.location.replace(response.data.redirect);

                setTimeout(() => {
                    window.location.href = response.data.redirect;
                }, 10000);
            }
        });
    });
    $(document).on("smart_login_step3", ".smart-login", function (e, data) {
        if ($(this).hasClass("loading")) {
            return;
        }
        let pass = $(this).find("input#ns-password");
        if (pass.val().length <= 0) {
            pass.focus();
            return;
        }

        $(this).addClass('loading');
        $(this).find('.login-result').text('');
        let email = $(this).find("input#ns-mobile");
        let redirect = $(this).find("input[name='ns-redirect']").val();
        pass.attr("readonly", true);
        $.post(pooshe_obj.ajax_url, {
            'action': 'pooshe_smart_login_email_pass',
            'email': email.val(),
            'password': pass.val(),
            'redirect': redirect,
            '_nonce': $(this).find("[name='_wpnonce']").val(),
        }, (response) => {
            $(this).find('.login-result').html(response.data.message);
            setTimeout(() => {
                $(this).find('.login-result').text('');
            }, 5000);
            if (!response.success) {
                $(this).removeClass('loading');
                pass.attr("readonly", false);
            } else if (response.success && response.data.code === 200) {
                window.location.replace(response.data.redirect);

                setTimeout(() => {
                    window.location.href = response.data.redirect;
                }, 10000);
            }
        });
    });
    $(document).on("click", ".smart-login .resend-code", function (e, data) {
        $(this).parents(".smart-login").trigger("smart_login_step1");
    });
    $(document).on("click", ".smart-login .back-btn", function (e, data) {
        $(this).parents(".smart-login").trigger("smart_login_change_step", {
            step: 1
        });
        let inputs = $(this).parents('.smart-login').find('#ns-mobile, #ns-password, #auth-code');
        inputs.val("");
        inputs.attr("readonly", false);
    });
    $(document).on("smart_login_change_step", ".smart-login", function (e, data) {
        $(this).find("[name='step']").val(data.step);
        $(this).find('.login-steps').hide();
        $(this).find('.step-' + data.step).slideDown("fast");

        if (parseInt(data.step) > 1) {
            $(this).find(".back-btn").slideDown("fast");
        } else {
            $(this).find(".back-btn").slideUp("fast");
        }

        if (typeof data.frag === "object") {
            Object.keys(data.frag).forEach((item) => {
                $(this).find('.step-' + data.step).find(item).html(data.frag[item]);
            });
        }
    });

    $(document).on("submit", ".smart-login .login-form", function (e) {
        e.preventDefault();

        let step = $(this).find("[name='step']").val();
        if ('1' === step) {
            $(this).parents(".smart-login").trigger("smart_login_step1");
        }
        if ('2' === step) {
            $(this).parents(".smart-login").trigger("smart_login_step2");
        }
        if ('3' === step) {
            $(this).parents(".smart-login").trigger("smart_login_step3");
        }
    });
})(jQuery);

/**
 * My account mobile verification.
 */
(function ($) {
    $(document).on("click", ".pooshe-mobile-verify-form .send-code", function (e) {
        e.preventDefault();
        $(this).parents(".pooshe-mobile-verify-form").trigger("send_code");
    });
    $(document).on("click", ".pooshe-mobile-verify-form.step-2 .verify-code", function (e) {
        e.preventDefault();
        $(this).parents(".pooshe-mobile-verify-form").trigger("verify_code");
    });
    $(document).on("send_code", ".pooshe-mobile-verify-form", function () {
        if ($(this).hasClass("loading")) {
            return;
        }
        $(this).addClass('loading');
        $(this).find('.login-result').text('');
        let mobile = $(this).find("input#ns-mobile");
        mobile.attr("readonly", true);
        $.post(pooshe_obj.ajax_url, {
            'action': 'pooshe_smart_login_myaccount_verify_mobile',
            'mobile': mobile.val(),
            '_nonce': $(this).find("[name='ns-nonce']").val(),
        }, (response) => {
            $(this).removeClass('loading');

            if (!response.success) {
                $(this).find('.login-result').text(response.data.message);
                setTimeout(() => {
                    $(this).find('.login-result').text('');
                }, 5000);
                mobile.attr("readonly", false);
            } else {
                $(".pooshe-mobile-verify-form").hide();
                $(".pooshe-mobile-verify-form.step-2").slideDown("fast");
                $(".pooshe-mobile-verify-form.step-2 .user-mobile").text(response.data.data.mobile);
                $(".pooshe-mobile-verify-form.step-2 input#ns-mobile-number").val(response.data.data.mobile);
            }
        });
    });
    $(document).on("verify_code", ".pooshe-mobile-verify-form", function () {
        if ($(this).hasClass("loading")) {
            return;
        }
        let code = $(this).find("input#ns-mobile-verify");
        if (code.val().length === 0) {
            return;
        }

        $(this).addClass('loading');
        $(this).find('.login-result').text('');
        let mobile = $(this).find("input#ns-mobile-number");
        code.attr("readonly", true);
        $.post(pooshe_obj.ajax_url, {
            'action': 'pooshe_smart_login_myaccount_verify_mobile',
            'mobile': mobile.val(),
            'code': code.val(),
            '_nonce': $(this).find("[name='ns-nonce']").val(),
        }, (response) => {
            $(this).removeClass('loading');

            if (!response.success) {
                $(this).find('.login-result').text(response.data.message);
                setTimeout(() => {
                    $(this).find('.login-result').text('');
                }, 5000);
                code.attr("readonly", false);
            } else {
                $(".pooshe-mobile-verify-form").remove();
                $(".pooshe-mobile-verify-form.step-2").addClass("loading");
                window.location.reload();
            }
        });
    });
})(jQuery);