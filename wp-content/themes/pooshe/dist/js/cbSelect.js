(function ($) {
    "use strict";

    $('.cb-select').each(function () {
        $(this).addClass('proHidden');
        var html_output = '<div class="cb-select-container">';
        html_output += '<div class="select-inner"><span class="title">'+$(this).children('option:selected').text();
        html_output += '</span><span class="arrow"><i class="far fa-angle-down"></i></span></div>';
        html_output += '<ul class="select-list">';
        $(this).find('option').each(function () {
            var itemClass = "";
            if($(this).is(':selected')){itemClass = "active";}
            html_output += '<li class="'+itemClass+'" data-value="'+$(this).val()+'">'+$(this).text()+'</li>';
        });
        html_output += '</ul></div>';
        $(this).after(html_output);

    }).on('change',function () {
        $(this).next('.cb-select-container').find('.select-inner .title').text($(this).children('option:selected').text());
    })
    $("html").click(function(a) {
        if(a.target.parentNode.className !== "select-inner") {
            $(".cb-select-container").removeClass('show');
        }
    });

    $(document).on("click",".cb-select-container .select-inner",function () {
        $(this).parents('.cb-select-container').toggleClass('show');
    });
    $('.cb-select').on('focus',function () {
        var self = $(this);
        setTimeout(function () {
            self.next('.cb-select-container').addClass('show');
        },100);
    });
    $(document).on("click",".cb-select-container .select-list li",function () {
        var select_tag = $(this).parents('.cb-select-container').prev('.cb-select');
        var selected_opt = $(this).attr("data-value");
        select_tag.val(selected_opt);
        select_tag.change();
        select_tag.trigger('change');
        select_tag.parents('form').change();
        $(this).parents(".cb-select-container").removeClass('show');
        $(this).addClass('active');
        $(this).parents('.select-list').find('li').not(this).removeClass('active');
    });

})(jQuery);
