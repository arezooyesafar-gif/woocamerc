(function ($) {
    "use strict";
    function CB_Icon_tag(){
        $('.cb-icon-select').not('.rendered').each(function () {
            $(this).hide();
            var data = [];
            var current  = "";
            $(this).children('li').each(function (index) {
                var title = $(this).html();
                var value = $(this).attr("data-value");
                var tmp = $(this).attr("data-current");
                if(tmp !== undefined){
                    current = value;
                }
                data[index] = {
                    "id": value,
                    "text": title
                };
            });
            var s2Tag = $(this).next('select.cb-icon-tag');
            s2Tag.select2({
                data: data,
                escapeMarkup: function(markup) {
                    return markup;
                }
            });
            s2Tag.val(current);
            s2Tag.trigger('change.select2');
            $(this).addClass('rendered');
        });
    }
    $(document).ready(function () {
        CB_Icon_tag();
    });
})(jQuery);
function CB_Icon_tag(){
    $('.cb-icon-select').not('.rendered').each(function () {
        $(this).hide();
        var data = [];
        var current  = "";
        $(this).children('li').each(function (index) {
            var title = $(this).html();
            var value = $(this).attr("data-value");
            var tmp = $(this).attr("data-current");
            if(tmp !== undefined){
                current = value;
            }
            data[index] = {
                "id": value,
                "text": title
            };
        });
        var s2Tag = $(this).next('select.cb-icon-tag');
        s2Tag.select2({
            data: data,
            escapeMarkup: function(markup) {
                return markup;
            }
        });
        s2Tag.val(current);
        s2Tag.trigger('change.select2');
        $(this).addClass('rendered');
    });
}
