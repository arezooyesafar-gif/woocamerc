(function($) {
    'use strict';
    
    $(document).ready(function() {
        $('.eshterakyar-plan-button-primary').on('click', function(e) {
            var $button = $(this);
            var href = $button.attr('href');
            
            if (href && href.indexOf('checkout') !== -1) {
                $button.prop('disabled', true);
                $button.text(eshterakyar_plans.i18n.loading || 'در حال انتقال...');
                return true;
            }
            return true;
        });
        
        $('.eshterakyar-plan-card').on('mouseenter', function() {
            $(this).addClass('eshterakyar-plan-hover');
        }).on('mouseleave', function() {
            $(this).removeClass('eshterakyar-plan-hover');
        });
        
        $('meta[name="generator"]').remove();
        
        $('body *').each(function() {
            var $this = $(this);
            var text = $this.text();
            if (text && (text.indexOf('سپاسگزاریم از اینکه سایت خود را با وردپرس') !== -1 || 
                         text.indexOf('Thank you for creating with WordPress') !== -1 ||
                         (text.indexOf('نگارش') !== -1 && text.indexOf('وردپرس') !== -1))) {
                if ($this.children().length === 0) {
                    $this.remove();
                } else {
                    var html = $this.html();
                    html = html.replace(/سپاسگزاریم از اینکه سایت خود را با وردپرس[^<]*/g, '');
                    html = html.replace(/Thank you for creating with WordPress[^<]*/g, '');
                    html = html.replace(/نگارش\s+\d+[^<]*/g, '');
                    $this.html(html);
                }
            }
        });
    });
    
    $(window).on('load', function() {
        $('meta[name="generator"]').remove();
        
        $('body *').each(function() {
            var $this = $(this);
            var text = $this.text();
            if (text && (text.indexOf('سپاسگزاریم') !== -1 || text.indexOf('نگارش') !== -1) && 
                (text.indexOf('وردپرس') !== -1 || text.indexOf('WordPress') !== -1)) {
                if ($this.children().length === 0 && text.length < 150) {
                    $this.remove();
                }
            }
        });
    });
    
})(jQuery);

