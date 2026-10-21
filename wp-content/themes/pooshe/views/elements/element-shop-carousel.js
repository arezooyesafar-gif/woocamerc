(function ($) {
    "use strict";
    function car_init($scope) {
        $scope.find('.pooshe-elementor-shop-carousel').each(function () {
            var targetTag = $(this).find('.swiper-container');
            var opts = targetTag.attr('data-options') !== undefined ? JSON.parse(targetTag.attr('data-options')) : [];
            var slider = new Swiper(targetTag, opts);
        });
        $(window).resize();
    }
    jQuery( window ).on( 'elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', function( $scope ) {
            car_init( $scope );
        });
    });
})(jQuery);
