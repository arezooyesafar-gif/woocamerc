<?php
if (!function_exists('add_action'))
{
    echo '<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>';
    exit();
}
else if (!defined('ABSPATH'))
{
    echo '<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>';
    exit();
}
/**/

/*submit supported themes*/
$theme_conf = array(
    'astra' => array(
        '.woosbymdz-carousel-con .swiper-slide li {padding: 0px !important;display: flex !important;flex-direction: column;justify-content: center;text-align: center;}
        .woosbymdz-carousel-con .swiper-slide li .astra-shop-summary-wrap {display: flex !important;flex-direction: column !important;justify-content: center !important;align-items: center !important;}
        .woosbymdz-carousel-con .swiper-slide li span.price {display: block;}
        .woosbymdz-carousel-con .swiper-slide span.onsale {width: fit-content;height: fit-content;}
        ', 
        'astra-logo.svg', '200px',
        'h2.woocommerce-loop-product__title',
        '.ast-woo-product-category',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        'a.button',
    ),
    'woodmart' => array(
        '.woosbymdz-carousel-con .swiper-slide div.product-grid-item {
        flex: 0 0 100%!important;
        max-width: 100%!important;
        padding-left: 0px!important;
        padding-right: 0px!important;
        }
        .woosbymdz-carousel-con .swiper-slide .hover-content-inner.wd-more-desc-inner.woodmart-more-desc-inner {display: none !important;}
        .woosbymdz-carousel-con .swiper-slide span.price del bdi {text-decoration: line-through;}
        .wd-hover-with-fade.hover-ready:hover .fade-in-block, .wd-hover-with-fade.hover-ready.state-hover .fade-in-block {
            visibility: visible !important;
            opacity: 1 !important;
            transition: opacity .3s ease, visibility .0s ease;
            background-color: white;
        }
        .woosbymdz-carousel-con .swiper-wrapper[products-same-height="yes"] .swiper-slide .product-wrapper {
            height: 100%;
        }
        ',
        'woodmart-logo.svg', '300px',
        '.wd-entities-title',
        '.wd-product-cats',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.product-labels',
        '.product-labels span',
        '.wd-add-btn a',
    ),
    'xstore' => array(
        '.woosbymdz-carousel-con .swiper-slide > div {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }
        .woosbymdz-carousel-con .swiper-horizontal>.swiper-pagination-bullets.swiper-pagination-bullets-dynamic, .woosbymdz-carousel-con .swiper-pagination-horizontal.swiper-pagination-bullets.swiper-pagination-bullets-dynamic {
            left: 0 !important;
            transform: unset !important;
            margin: auto !important;
        }',
        'xstore-logo.svg', '262px',
        'h2.product-title',
        '.products-page-cats',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.onsale',
        '.onsale',
        'a.button',
    ),
    'zephyr' => array(
        '.woosbymdz-carousel-con .swiper-slide li {
            display: block;
            text-align: center;
        }',
        'zephyr-logo.svg', '262px',
        'h2.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.onsale',
        '.onsale',
        'a.button',
    ),
    'impreza' => array(
        '.woosbymdz-carousel-con .swiper-slide li {
            display: block;
            text-align: center;
        }',
        'impreza2-logo.svg', '262px',
        'h2.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.onsale',
        '.onsale',
        'a.button',
    ),
    'flatsome' => array(
        '.woosbymdz-carousel-con .swiper-slide .box-text.box-text-products {
            text-align: center;
        }
        .woosbymdz-carousel-con .swiper-slide .add-to-cart-button a {
            margin-right: auto;
            margin-left: auto;
        }
        .woosbymdz-carousel-con .swiper-wrapper[no-products-addbtn="yes"] .cart-icon strong {
            display: none;
        }
        ',
        'flatsome-logo.svg', '262px',
        '.product-title',
        '.category',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.badge-container',
        '.badge-container span',
        '.add-to-cart-button a',
    ),
    'divi' => array(
        '.woosbymdz-carousel-con .swiper-slide li {display: block !important;text-align: center;}', 
        'divi-logo.svg', '200px',
        'h2.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.onsale',
        '.onsale',
        '.et_overlay:before',
    ),
    'xtra' => array(
        '.woosbymdz-carousel-con .swiper-slide li {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .woosbymdz-carousel-con .swiper-slide li > a {
            width: 100%;
        }
        .woosbymdz-carousel-con .swiper-slide .xtra-product-thumbnail {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }'
        , 
        'xtra-logo2.png', '200px',
        'h2.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        'a.add_to_cart_button',
        /*'a.wp-element-button',*/  
    ),
    'cando' => array(
        '.woosbymdz-carousel-con .swiper-slide >li {display: block;text-align: center;}
        .woosbymdz-carousel-con .swiper-slide >li ul.single-p-box-colors {display: flex; justify-content: space-around;}
        .woosbymdz-carousel-con .swiper-slide >li ul.single-p-box-colors li {display: grid;}'
        , 
        'candoo-logo.png', '200px',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',  
    ),
    'emperor' => array('
        .woosbymdz-carousel-con .swiper-slide > div {width: 100%;}
        .woosbymdz-carousel-con .swiper-slide {overflow: inherit;}
        .woosbymdz-carousel-con .swiper-slide h3.course-title { margin-top: 15px;}'
        , 
        'emperatoor-theme-logo.png', '200px',
        '.course-title',
        '.course-description',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.sale-perc-badge',
        '.sale-perc-badge',
        'a.add_to_cart_button',  
    ),
    'negarshop' => array('', 
        'negarshop-theme-logo.png', '200px',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',  
    ),
    'nikikala' => array('
        .woosbymdz-carousel-con .swiper-slide >li {display: block;}', 
        'nikikalla-logo.svg', '255px',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',  
    ),
    'medizin' => array('
        .woosbymdz-carousel-con .swiper-slide .product-hover-image {display: none;}
        .woosbymdz-carousel-con .swiper-slide .grid-item.product {text-align: center;}
        ', 
        'medizin-logo.png', '330px', 
        'h2.woocommerce-loop-product__title',
        '.loop-product__category',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.product-badges',
        '.product-badges span',
        'a.button', 
    ),
    'hello elementor' => array('
        .woosbymdz-carousel-con .swiper-slide > li, .woosbymdz-carousel-con .swiper-slide > li > a{    
            display: flex;
            text-align: center;
            flex-direction: column;
            align-items: center;
        }
        .woosbymdz-carousel-con .swiper-slide h2.woocommerce-loop-product__title {font-size: 22px;}', 
        'hello-elementor-logo.svg', '330px',
        'h2.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.onsale',
        '.onsale',
        'a.button', 
    ),
    'meza' => array('
    .woosbymdz-carousel-con .swiper-wrapper {transition-duration: 300ms !important;}', 
        'meza-logo.svg', '200px',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',
        '.woosbymdz-null',  
    ),
    'metro' => array('
    .woosbymdz-carousel-con .swiper-slide > div {
        width: 100%;
        max-width: 100%;
        flex-basis: 100%;
        text-align: center;
        padding-left: 0px;
        padding-right: 0px;
    }
    .woosbymdz-carousel-con .swiper-slide .rtin-price.price {
        margin-left: auto;
        margin-right: auto;
    }
    ', 
    'metro-logo.png', '200px',
    '.rtin-title',
    '.woosbymdz-null',
    'span.price',
    '.rtin-price',
    '.rtin-price del',
    '.onsale',
    '.onsale',
    'a.button',  
    ),
    'mweb-digiland-pro' => array('
    .woosbymdz-carousel-con .swiper-slide > li {width: 100% !important;}
    .woosbymdz-carousel-con .swiper-slide .add-to-links {float: none !important;}
    .woosbymdz-carousel-con .swiper-slide div.actions {text-align: center;}', 
    'digiland-logo.png', '200px',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',  
    ),
    'ahura' => array('
    .woosbymdz-carousel-con .swiper-slide > li {text-align: center;}
    .woosbymdz-carousel-con .swiper-slide span.price {justify-content: center;}', 
    'ahura-logo.png', '200px',
    'h2.woocommerce-loop-product__title',
    '.woosbymdz-null',
    'span.price bdi',
    'span.price .amount',
    'span.price del .amount',
    'span.onsale',
    'span.onsale',
    '.woosbymdz-null',  
    ),
    'mweb-digiacademy' => array('
    .woosbymdz-carousel-con .swiper-slide > li {width: 100%;max-width: 100%;}
    .woosbymdz-carousel-con .swiper-slide > li > div {width: 1000px;max-width: 100%;}
    .woosbymdz-carousel-con .swiper-slide .product_item_meta {justify-content: space-between;}
    ', 
    'digi-academi-logo.png', '286px',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',  
    ),
    'avada' => array('
    .woosbymdz-carousel-con .swiper-slide > li {
        display: flex;
        justify-content: center;
    }
    .woosbymdz-carousel-con .swiper-slide .fusion-product-wrapper {text-align: center;}
    .woosbymdz-carousel-con .swiper-slide .fusion-product-content {
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .woosbymdz-carousel-con .swiper-slide .fusion-price-rating {
        display: block !important;
        margin-left: auto;
        margin-right: auto;
    }
    .woosbymdz-carousel-con .swiper-slide .product-buttons, .woosbymdz-carousel-con .swiper-slide .product-buttons a {width: 100%;max-width: 100%;text-align: center;}
    ', 
    'avada-logo.png', '286px', 
    '.product-title',
    '.woosbymdz-null',
    'span.price',
    'span.price .amount',
    'span.price del .amount',
    '.onsale',
    '.onsale',
    'a.button',
    ),
    'cinema' => array('
    .woosbymdz-carousel-con .swiper-slide > li {
        text-align: center;
        display: flex;
        flex-direction: column;
    }
    .woosbymdz-carousel-con .swiper-slide a.add_to_cart_button {
        background-color: #ffffff;
        color: #727272;
        font-weight: 600;
        margin-top: 10px;
        padding: 7px 0px;
    }
    ', 
    'cinema-logo.png', '141px',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null', 
    ),
    'charsoogh' => array('
    .woosbymdz-carousel-con .swiper-slide > li {
        display: contents;
    }
    .woosbymdz-carousel-con .swiper-slide div.meta {text-align: center;}
    ', 
    'charsoogh_logo.png', '286px', 
    '.meta a.title h2',
    '.meta figure',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.woosbymdz-null',
    '.wobd-badges-wrapper .wobd-badges',
    '.wobd-badges-wrapper .wobd-badges .wobd-text',
    '.location',
    ),
    'glb' => array(
        '.woosbymdz-carousel-con .swiper-slide > li {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .woosbymdz-carousel-con .swiper-slide h2.woocommerce-loop-product__title {
            font-size: 21px;
            text-align: center;
        }
        .woosbymdz-carousel-con .swiper-slide .star-rating {text-align: center;}
        .woosbymdz-carousel-con .swiper-slide span.price {
            display: block;
            text-align: center;
            margin-bottom: 15px;
        }
        .woosbymdz-carousel-con .swiper-slide span.onsale {
            position: absolute;
        }
        ',
        'glb-logo.png', '197px',
        '.woocommerce-loop-product__title',
        '',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        'a.add_to_cart_button',
    ),
    'manufacturer' => array(
        '.woosbymdz-carousel-con .swiper-slide > li {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .woosbymdz-carousel-con .swiper-slide h2.woocommerce-loop-product__title {
            font-size: 21px;
            text-align: center;
        }
        .woosbymdz-carousel-con .swiper-slide .star-rating {text-align: center;}
        .woosbymdz-carousel-con .swiper-slide span.price {
            display: block;
            text-align: center;
            margin-bottom: 15px;
        }
        .woosbymdz-carousel-con .swiper-slide span.onsale {
            position: absolute;
        }
        ',
        'manufacturer-logo.jpg', '197px',
        '.woocommerce-loop-product__title',
        '',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        'a.add_to_cart_button',
    ),
    'پارس کالا' => array(
        '.woosbymdz-carousel-con .swiper-slide > li {
            padding-right: 6px;
            padding-left: 6px;
        }
        .woosbymdz-carousel-con .swiper-slide span.onsale.prs {
            font-size: 17px !important;
            font-weight: 100 !important;
        }
        ',
        'pars-kala-icon.webp', '197px',
        '.woocommerce-loop-product__title',
        '.woosbymdz-null',
        '.index-prices-pro',
        'span.woocommerce-Price-amount.amount.price_sale',
        'span.woocommerce-Price-amount.amount.sale_price',
        '.head-archie-pro.not',
        'span.onsale.prs',
        'p.call_pro',
    ),
    'luman' => array('
        .woosbymdz-carousel-con .swiper-slide .xts-product-thumb {min-height: 160px;}
        .woosbymdz-carousel-con .swiper-slide .xts-more-desc-inner blockquote {
            padding: 0px;
            font-size: 13px;
            font-style: normal;
            margin: 10px 0px;
        }
        ',
        'lumen-logo.svg', '197px',
        '.woocommerce-loop-product__title',
        '.xts-more-desc-inner',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.xts-product-labels',
        '.xts-product-labels span',
        '.xts-product-add-btn a',
    ),
    'irankala' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            display: flex;
            flex-direction: column;
            text-align: center;
        }
        .woosbymdz-carousel-con .swiper-slide .woocommerce.variations-form {display: none !important;}
        .woosbymdz-carousel-con .swiper-slide span.price {display: flex;justify-content: center;}
        ',
        'irankala-logo.png', '197px',
        'h2.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale img',
        'a.add_to_cart_button',
    ),
    'lucille' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .woosbymdz-carousel-con .swiper-slide h2.woocommerce-loop-product__title {font-size: 1em;}
        ',
        'lucille-logo.png', '129px',
        'h2.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.woosbymdz-null',
        '.woosbymdz-null',
        'a.add_to_cart_button',
    ),
    'enfold' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            display: block;
            text-align: center;
        }
        .woosbymdz-carousel-con .swiper-slide span.onsale {
           position: absolute;
           top: -100% !important;
        }
        ',
        'enfold-logo.png', '194px',
        'h2.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        '.woosbymdz-null',
    ),
    'gemplay' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        ',
        'gemplay-logo.png', '114px',
        'h2.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        'a.add_to_cart_button',
    ),
    'studiare' => array('
        .woosbymdz-carousel-con .swiper-slide {overflow: initial;}
        .woosbymdz-carousel-con .swiper-slide .course-content-main {
            padding: 20px 20px 20px 25px;
            overflow: hidden;
        }
        .woosbymdz-carousel-con .swiper-slide .course-rating-teacher {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .woosbymdz-carousel-con .swiper-slide .course-content-bottom {
            border-top: 1px solid #e5e5e5;
            padding: 10px 20px;
            font-size: .9375rem;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
        }
        ',
        'studiare-logo.png', '114px',
        'h4.course-title a',
        '.course-rating-teacher',
        '.course-price',
        '.course-price .price-sale',
        '.course-price span.sc_reg_onsale_price',
        '.sale-perc',
        '.sale-perc span.sc_offer_txt',
        '.course-loop-element__course-type--onsite',
    ),
    'dinakala' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            display: flex;
            width: auto !important;
            max-width: 1200px;
            padding: 0px !important;
            margin: 0px !important;
            flex: 0 0 100%;
        }
        ',
        'dinakala-logo.png', '188px',
        '.product-title a',
        '.woosbymdz-null',
        '.product-price',
        '.product-price .amount bdi',
        '.product-price del .amount bdi',
        '.product-dis',
        '.product-dis',
        'a.btn-buy',
    ),
    'ویترین' => array('
        .woosbymdz-carousel-con .swiper-slide .vitrin-simple-product-slider-item {text-align: center;}
        .woosbymdz-carousel-con .swiper-slide .vitrin-simple-product-item-detail {justify-content: center;}
        ',
        'vitrin-logo.png', '188px',
        '.vitrin-simple-product-item-name a',
        '.woosbymdz-null',
        '.vitrin-simple-product-price',
        '.vitrin-simple-product-price span ins',
        '.vitrin-simple-product-price span del',
        '.vitrin-simple-product-onsale-badge',
        '.vitrin-simple-product-onsale-badge',
        '.vitrin-simple-product-item-hover-stuff',
    ),
    'pooshe' => array('
        ',
        'pooshe-logo.png', '188px',
        'h2.woocommerce-loop-product__title',
        '.posted_in',
        'div.prices',
        'div.prices amount bdi',
        'div.prices del bdi',
        '.add-product-favo',
        '.add-product-favo i',
        'a.add_to_cart_button',
    ),
    'digi marsh' => array('
        .woosbymdz-carousel-con .swiper-slide > li:not(.yith-wcwl-add-to-wishlist) {
            display: flex;
            width: 100%;
            max-width: 100%;
            position: relative;
            text-align: center;
            flex: 0 0 100% !important;
            justify-content: center;
            flex-direction: column;
        }
        .woosbymdz-carousel-con .swiper-slide h2.woocommerce-loop-product__title {
            font-size: 17px;
            margin-top: 12px;
        }
        ',
        'digimarsh-logo.png', '188px',
        'h2.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        '.woosbymdz-null',
    ),
    'avina' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        .woosbymdz-carousel-con .swiper-slide h2.woocommerce-loop-product__title {
            margin-top: 16px;
            margin-bottom: 16px;
        }
        .woosbymdz-carousel-con .swiper-slide a.add_to_cart_button {margin-top: 16px;}
        ',
        'avina-logo.png', '228px',
        'h2.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.badge',
        'span.badge',
        'a.add_to_cart_button',
    ),
    'hara' => array('
        .woosbymdz-carousel-con .swiper-slide h3.name {
            text-align: center;
        }
        .woosbymdz-carousel-con .swiper-slide span.price {
            justify-content: center;
        }
        .woosbymdz-carousel-con .swiper-slide div.rating {
            width: 100%;
            text-align: center;
        }
        ',
        'hara-logo.png', '169px',
        'h3.name',
        'div.rating',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        'div.add-cart',
    ),
    'timcheh' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            display: contents;
        }
        .woosbymdz-carousel-con .swiper-slide article {
            width: 100%;
        }
        ',
        'timche-logo.png', '200px',
        'h4.title',
        'span.sub-title',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.badge',
        'span.badge',
        'a.product-add-to-cart',
    ),
    'bookie wp' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            display: block;
        }
        .woosbymdz-carousel-con .swiper-slide h2.woocommerce-loop-product__title {
            text-align: center;
            font-size: 20px;
        }
        .woosbymdz-carousel-con .swiper-slide .product-price-box {
            text-align: center;
        }
        .woosbymdz-carousel-con .swiper-slide .woo-button-wrapper {
            margin-top: 15px;
        }
        .woosbymdz-carousel-con .swiper-slide .woo-button-wrapper .woo-button-border {
            display: flex;
            justify-content: space-around;
        }
        .woosbymdz-carousel-con .swiper-slide .woo-button-border > a {
            background-color: white;
            border-radius: 10px;
            padding: 7px 10px;
            text-align: center;
        }
        ',
        'bookie-logo.png', '225px',
        'h2.woocommerce-loop-product__title',
        '.person-name',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        'a.add_to_cart_button',
    ),
    'قالب فارسی یادگیری شیرینی پزی' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            display: block;
            padding: 10px 0px;
        }
        .woosbymdz-carousel-con .swiper-slide .astra-shop-summary-wrap {
            text-align: center;
        }
        .woosbymdz-carousel-con .swiper-slide h2.woocommerce-loop-product__title {
            font-size: 20px;
        }
        .woosbymdz-carousel-con .swiper-slide span.price {
            display: flex;
            justify-content: center;
            margin: 10px 0px;
        }
        .woosbymdz-carousel-con .swiper-slide a.add_to_cart_button {
            margin-bottom: 11px;
            display: flex;
            justify-content: center;
            margin-left: 20px;
            margin-right: 20px;
        }
        ',
        'backe-nicole-logo.png', '300px',
        'h2.woocommerce-loop-product__title',
        '.ast-woo-product-category',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        'a.add_to_cart_button',
    ),
    'technocy' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            display: block;
        }
        .woosbymdz-carousel-con .swiper-slide .posted-in, .woosbymdz-carousel-con .swiper-slide .product-caption-bottom {
            text-align: center;
        }
        .woosbymdz-carousel-con .swiper-slide h3.woocommerce-loop-product__title {
            text-align: center;
            font-size: 18px;
        }
        .woosbymdz-carousel-con .swiper-slide .technocy-count-review {
            margin: 16px 0px;
        }
        .woosbymdz-carousel-con .swiper-slide .price {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }
        ',
        'technocy-logo.png', '300px',
        'h3.woocommerce-loop-product__title',
        '.posted-in',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        'a.add_to_cart_button',
    ),
    'immiex' => array('
        .woosbymdz-carousel-con .swiper-slide > .single-product-details {text-align: center;}
        ',
        'immiex-logo.png', '285px',
        'woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        'a.add_to_cart_button',
        'product-item',//Adds extra css class/classes to swiper-slide element
    ),
    'betheme' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            text-align: center;
            list-style: none;
        }
        .woosbymdz-carousel-con .edd_download_image:after, .woosbymdz-carousel-con .image_frame .image_wrapper .mask {
            box-shadow: none;
        }
        ',
        'be-logo.png', '300px',
        '.mfn-woo-product-title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        '.image_links > a',
        '',//Adds extra css class/classes to swiper-slide element
    ),
    'techex' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            text-align: center;
            list-style: none;
        }
        .woosbymdz-carousel-con h2.woocommerce-loop-product__title {
            font-size: 21px;
            margin-top: 20px;
        }
        .woosbymdz-carousel-con span.price {
            display: flex;
            justify-content: space-between;
        }
        .woosbymdz-carousel-con span.price ins {
            padding: 2px 6px;
            border-radius: 6px;
        }
        .woosbymdz-carousel-con span.onsale {
            background-color: #ffffffd6;
            position: absolute;
            padding: 10px;
            border-bottom-left-radius: 10px;
            backdrop-filter: blur(3px)saturate(166%);
            -webkit-backdrop-filter: blur(3px)saturate(166%);
        }
        ',
        'techex-logo.png', '175px',
        'h2.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        'a.add_to_cart_button',
        '',//Adds extra css class/classes to swiper-slide element
    ),
    'meta theme' => array('
        .woosbymdz-carousel-con .swiper-slide .xts-product-content {
            text-align: center;
            padding-bottom: 28px;
            padding-left: 10px;
            padding-right: 10px;
        }
        .woosbymdz-carousel-con .swiper-slide .xts-product .price {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
        }
        ',
        'meta-theme-logo.png', '279px',
        'h2.woocommerce-loop-product__title',
        '.xts-more-desc-inner',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.xts-product-labels',
        '.xts-product-label',
        'a.add_to_cart_button',
        '',//Adds extra css class/classes to swiper-slide element
    ),
    'partdo' => array('
        .woosbymdz-carousel-con .swiper-slide .product-content {
            display: flex;
            flex-direction: column;
        }
        ',
        'partdo-logo.png', '279px',
        '.product-title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.woosbymdz-null',
        '.woosbymdz-null',
        "a[role='button']",
        '',//Adds extra css class/classes to swiper-slide element
    ),
    'baghal' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            list-style: none;
            width: 100%;
        }
        ',
        'baghal-logo.png', '211px',
        'h5.card-title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.badge',
        'span.badge',
        'a.product-add-to-cart',
        '',//Adds extra css class/classes to swiper-slide element
    ),
    'bakala theme' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            list-style: none;
            width: 100%;
            display: block;
        }
        .woosbymdz-carousel-con .swiper-slide .info-product {
            position: relative !important;
        }
        .woosbymdz-carousel-con .swiper-slide .products__item-fatitle {
            margin-top: 12px;
            margin-bottom: 8px;
        }
        .woosbymdz-carousel-con .swiper-slide .products__item-img-color-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .woosbymdz-carousel-con .swiper-slide .products__item-image-wrapper {
            height: fit-content !important;
        }
        .woosbymdz-carousel-con .swiper-slide .products__item-image-wrapper img {
            margin-top: 0px !important;
            transition: all .2s cubic-bezier(0.98, 1.06, 0.25, 1);
        }
        .woosbymdz-carousel-con .swiper-slide .products__item-info {
            display: grid;
            justify-content: center;
            align-items: center;
        }
        .woosbymdz-carousel-con .swiper-slide .products__item-gift-price {
            width: 100%;
        }
        .woosbymdz-carousel-con .swiper-slide .products__item-price {
            display: block;
            height: fit-content;
        }
        .woosbymdz-carousel-con .swiper-slide .products__item-gift-price .modified-info {
            margin: 0px;
            display: flex;
            justify-content: center;
        }
        .woosbymdz-carousel-con .swiper-slide .products__item-gift-price .updated-price.down {
            margin: 0px;
            position: relative;
        }
        .woosbymdz-carousel-con .swiper-slide .products__item-gift-price .updated-price.down:before {
            right: -16px;
            top: 2px;
        }
        .woosbymdz-carousel-con .swiper-slide .box-footer {
            display: grid;
            justify-content: center;
            margin: 0px;
            width: 100%;
            grid-row-end: 10;
        }
        .woosbymdz-carousel-con .swiper-slide .box-footer .loop-add-to-cart {
            width: 100%;
            display: flex;
            margin-top: 12px;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .woosbymdz-carousel-con .swiper-slide .custom-stars {
            position: absolute;
            top: 8px;
            background-color: white;
            width: fit-content;
            height: fit-content;
            padding: 1px;
            
        }
        .woosbymdz-carousel-con .swiper-slide .custom-stars .customStar {
            margin-left: 22px;
        }
        .woosbymdz-carousel-con .swiper-slide .custom-stars .customStar:before {right: 23px;}
        .woosbymdz-carousel-con .swiper-slide .bakala-minus, .woosbymdz-carousel-con .swiper-slide .bakala-plus {
            align-items: end;
        }
        .woosbymdz-carousel-con .swiper-slide .box-footer .main-featured-loop {
            grid-row-start: 1;
            width: 100%;
        }
        .woosbymdz-carousel-con .swiper-slide ul.main-features {
            max-height: 72px;
            overflow-y: auto;
        }
        .woosbymdz-carousel-con .swiper-slide .loop-add-to-cart .quantity {
            background: white;
        }
        .woosbymdz-carousel-con .swiper-slide .loop-add-to-cart .quantity input[type="number"] {
            margin-top: 5px;
            margin-bottom: 5px;
            padding-top: 1.5px;
            padding-bottom: 1.5px;
            text-align: center;
        }
        .woosbymdz-carousel-con .swiper-slide img.second-image {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
        }
        .woosbymdz-carousel-con .swiper-slide .variable-cart-product, .woosbymdz-carousel-con .swiper-slide button.bakala-select-options {
            display: none !important;
        }
        .woosbymdz-carousel-con .swiper-slide .secimg-shown {
            opacity: 1 !important;
            transform: scale(1.09);
        }
        .woosbymdz-carousel-con .swiper-pagination {
            display: flex;
            justify-content: center;
        }
        .woosbymdz-carousel-con .swiper-button-next:after, .woosbymdz-carousel-con .swiper-button-prev:after, .woosbymdz-catslider-con .swiper-button-next:after, .woosbymdz-catslider-con .swiper-button-prev:after {
            opacity: 0 !important;
            pointer-events: none !important;
        }
        .woosbymdz-carousel-con .swiper-button-next, .woosbymdz-carousel-con .swiper-button-prev {
            top: 50% !important;
        }
        ',
        'bakala-logo.png', '122px',
        '.products__item-fatitle a',
        '.main-featured-loop',
        '.products__item-price',
        '.white_catfinal-price .amount',
        '.modified-info .amount',
        '.loop-saving-percentage',
        '.loop-saving-percentage',
        "a.ajax_add_to_cart",
        '',//Adds extra css class/classes to swiper-slide element
    ),
    'قالب کاوه' => array('
        .woosbymdz-carousel-con .swiper-slide > div {
            width: 100%;
        }
        ',
        'kave-logo.png', '124px',
        '.detail h2',
        '.product-image .woosc-btn',
        '.price',
        '.amount',
        '.offer',
        '.discount',
        '.discount',
        'a.btn',
        '',//Adds extra css class/classes to swiper-slide element
    ),
    'printspace' => array('
        .woosbymdz-carousel-con .swiper-slide .product-actions .sp-wqv-view-button.button {
            display: none !important;
        }
        .woosbymdz-carousel-con .swiper-slide .product-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        ',
        'printspace-logo.png', '217px',
        '.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.product-label',
        '.product-label > span',
        'a.after_add_to_cart',
        '',//Adds extra css class/classes to swiper-slide element
    ),
    'wolmart' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            list-style: none;
        }
        .woosbymdz-carousel-con .wolmart-hover-slider-wrapper .slider-button.slider-button-prev {
            right: unset;
            left: 0;
        }
        .woosbymdz-carousel-con .swiper-slide .product-details {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .woosbymdz-carousel-con .woocommerce-loop-product__title {
            text-align: center;
        }
        .woosbymdz-carousel-con .product-action-vertical {
            left: 28px;
        }
        ',
        'woolmart-logo.png', '248px',
        '.woocommerce-loop-product__title',
        '.product-cat',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        '.product-label-group',
        '.product-label-group .product-label',
        '.product-details>:last-child',
        '',//Adds extra css class/classes to swiper-slide element
    ),
    'homey' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            list-style: none;
        }
        .woosbymdz-carousel-con .swiper-slide h2.woocommerce-loop-product__title {
            margin-top: 20px;
        }
        ',
        'homey-logo.png', '245px',
        '.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        'a.button[data-product_id]',
        '',//Adds extra css class/classes to swiper-slide element
    ),
    'induxter' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            list-style: none;
        }
        ',
        'induxter-logo.png', '259px',
        '.td-product-info-wrapper h3 a',
        '.star-rating',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        '.add_to_cart_button',
        '',//Adds extra css class/classes to swiper-slide element
    ),
    'gimnas' => array('
        .woosbymdz-carousel-con .swiper-slide > li {
            display: flex;
            list-style: none;
            flex-direction: column;
        }
        .woosbymdz-carousel-con .swiper-slide .product-info ul {
            margin-left: auto;
            margin-right: auto;
            display: flex;
            justify-content: center;
        }
        .woosbymdz-carousel-con .swiper-slide .product-info ul li {list-style: none;}
        .woosbymdz-carousel-con .swiper-slide > li .woocommerce-loop-product__title {
            text-align: center;
        }
        ',
        'gimnas-logo.png', '230px',
        '.woocommerce-loop-product__title',
        '.woosbymdz-null',
        'span.price',
        'span.price .amount',
        'span.price del .amount',
        'span.onsale',
        'span.onsale',
        '.add_to_cart_button',
        '',//Adds extra css class/classes to swiper-slide element
    ),
    'ishop' => array('
        .woosbymdz-carousel-con .swiper-slide > div {
            width: 100%;
        }
        ',
        'ishop-logo.png', '254px',
        '.product-card--title a',
        '.product-card--detail',
        '.product-card--actions-price',
        '.product-card--actions-price .amount',
        '.product-main-content__buy-content-price-no-discount-price',
        '.product-main-content__buy-content-price-discounted-percentage',
        '.product-main-content__buy-content-price-discounted-percentage span',
        '.product-card--actions-addtocart-wrapper button',
        '',//Adds extra css class/classes to swiper-slide element
    ),
);





/**/
$theme_conf['proshoproyal child'] = $theme_conf['avada'];
$theme_conf['astra child'] = $theme_conf['astra'];
$theme_conf['قالب وودمارت'] = $theme_conf['woodmart'];
$theme_conf['ایران وودمارت'] = $theme_conf['woodmart'];
$theme_conf['woodmart child'] = $theme_conf['woodmart'];
$theme_conf['woodmart-child'] = $theme_conf['woodmart'];
$theme_conf['woodmart-see5'] = $theme_conf['woodmart'];
$theme_conf['flatsome child'] = $theme_conf['flatsome'];
$theme_conf['ایران فلتسام'] = $theme_conf['flatsome'];
$theme_conf['دیوی'] = $theme_conf['divi'];
$theme_conf['فارسی‌ساز هوشمند دیوی'] = $theme_conf['divi'];
$theme_conf['اکسترا'] = $theme_conf['xtra'];
$theme_conf['xtra child'] = $theme_conf['xtra'];
$theme_conf['جادا'] = $theme_conf['xtra'];//Special Customer
$theme_conf['hello elementor child'] = $theme_conf['hello elementor'];
$theme_conf['مزا'] = $theme_conf['meza'];
$theme_conf['مِـزا'] = $theme_conf['meza'];
$theme_conf['مترو'] = $theme_conf['metro'];
$theme_conf['glb child'] = $theme_conf['glb'];
$theme_conf['alireza mahdiloo2020'] = $theme_conf['lucille'];
$theme_conf['xstore child'] = $theme_conf['xstore'];
$theme_conf['avina child'] = $theme_conf['avina'];
$theme_conf['hara child'] = $theme_conf['hara'];
$theme_conf['technocy child'] = $theme_conf['technocy'];
$theme_conf['betheme child'] = $theme_conf['betheme'];
$theme_conf['betheme | بی تم'] = $theme_conf['betheme'];
$theme_conf['studiare child theme'] = $theme_conf['studiare'];
$theme_conf['پوشـــه'] = $theme_conf['pooshe'];
$theme_conf['impreza child'] = $theme_conf['impreza'];
$theme_conf['partdo child'] = $theme_conf['partdo'];
$theme_conf['قالب فروشگاهی بقال'] = $theme_conf['baghal'];
$theme_conf['gimnas چایلد'] = $theme_conf['gimnas'];
$theme_conf['nikikala child'] = $theme_conf['nikikala'];
$theme_conf['luman child'] = $theme_conf['luman'];




/*Recognize the Theme*/
$current_theme = wp_get_theme();
$current_theme = $current_theme['Name'];
$current_theme = strtolower($current_theme);


//Org theme-name 
$org_theme_name = get_option('woosbymdz_orgtheme_name');
if($org_theme_name && $org_theme_name != '' && $org_theme_name != null && $org_theme_name != NULL){
    $current_theme = $org_theme_name;
}


$path = WOOSBYMDZ_PATH . "front" .DIRECTORY_SEPARATOR."this-theme-confs.php";

if(isset($theme_conf[$current_theme][11])){
    $tmp_swiper_classes = $theme_conf[$current_theme][11];
}else $tmp_swiper_classes = "";

$content = '
<?php
if (!function_exists("add_action"))
{
    echo "<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>";
    exit();
}
else if (!defined("ABSPATH"))
{
    echo "<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>";
    exit();
}
/**/
$theme_prod_title = "'.$theme_conf[$current_theme][3].'"; 
$theme_prod_cats = "'.$theme_conf[$current_theme][4].'"; 
$theme_prod_price = "'.$theme_conf[$current_theme][5].'";
$theme_prod_price_normal = "'.$theme_conf[$current_theme][6].'"; 
$theme_prod_price_del = "'.$theme_conf[$current_theme][7].'"; 
$theme_prod_label_con = "'.$theme_conf[$current_theme][8].'"; 
$theme_prod_label_txt = "'.$theme_conf[$current_theme][9].'"; 
$theme_prod_add_btn = "'.$theme_conf[$current_theme][10].'";
if(!function_exists("woosbymdz_ret_themeSwiperClasses")){
function woosbymdz_ret_themeSwiperClasses(){
    return "'.$tmp_swiper_classes.'";
}
}
//close the PHP tag to reduce the blank spaces ?>';

$this->writetofile($path, $content, '', 'end');
//close the PHP tag to reduce the blank spaces ?>