jQuery( window ).on( 'elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction( 'frontend/element_ready/pooshe_shop_small-product-grid.default', function($scope, $){
            $scope.find('.pooshe-elementor-shop-small-products').each(function () {

                var targetTag = $(this).find('.swiper-container');
                var opts = targetTag.attr('data-options') !== undefined?JSON.parse(targetTag.attr('data-options')):[];
                var slider = new Swiper(targetTag,opts);
            });
            $(window).resize();
    });
} );
