const { PurgeCSS } = require('purgecss');

async function run() {
    const path = require('path');
    const fs = require('fs');
    const cssPath = path.resolve('./dist/style/main.css');
    console.log('CSS path:', cssPath);
    console.log('CSS file exists:', fs.existsSync(cssPath));

    // Read CSS content directly
    const cssContent = fs.readFileSync(cssPath, 'utf-8');
    console.log('CSS length:', cssContent.length);

    // Find sample content files
    const files = [
        './views/**/*.php',
        './includes/**/*.php',
        './dokan/**/*.php',
        './learnpress/**/*.php',
        './rcp/**/*.php',
        '/*.php',
        './dist/init.js',
        './dist/js/*.js',
    ];

    const purge = new PurgeCSS();
    const results = await purge.purge({
        content: files,
        css: [{ raw: cssContent, name: cssPath }],
        safelist: {
            standard: [
                /swiper/,
                /select2/,
                /modal/,
                /fade/,
                /show/,
                /tooltip/,
                /popover/,
                /fa-/,
                /elementor/,
                /dokan/,
                /menu-item/,
                /sub-menu/,
                /current-menu/,
                /onsale/,
                /related/,
                /upsells/,
                /wc-tab/,
                /product-type-/,
                /added_to_cart/,
                /comment-/,
                /respond/,
                /logged-in/,
                /entry-title/,
                /wp-block/,
                /align/,
                /has-/,
                /is-/,
                /screen-reader-text/,
                /woocommerce-/,
                /product/,
                /cardFooter/,
                /product-share/,
                /add-product-favo/,
                /order-tabs/,
                /card-list-style/,
                /ps-native-scroll/,
                /copied/,
                /liked/,
                /login_req/,
                /sending/,
                /typing/,
                /searching/,
                /loaded/,
                /not-loaded/,
                /to-right/,
                /to-left/,
                /to-bottom/,
                /initialized/,
                /dark-mode/,
                /pooshe/,
                /v2-/,
                /button-/,
                /add_to_cart/,
                /single_add_to_cart/,
                /reset_variations/,
                /quantity/,
                /input-text/,
                /variations/,
                /flex-/,
                /star-rating/,
                /price/,
                /amount/,
                /variation/,
                /cart-item-clone/,
                /loading/,
                /active/,
                /added/,
                /product-card/,
                /card-black-style/,
                /product-hover/,
                /col-/,
                /container/,
                /row$/,
                /btn/,
                /button$/,
                /alert/,
                /badge/,
                /form-control/,
                /input-group/,
                /list-group/,
                /header-/,
                /footer-/,
                /search-/,
                /cart-/,
                /site-header/,
                /swiper-container/,
                /swiper-wrapper/,
                /swiper-slide/,
                /swiper-button-/,
                /swiper-pagination/,
                /woocommerce-product-gallery/,
                /product-custom-atts/,
                /stats-/,
                /product-v2/,
                /page/,
                /post/,
                /style-grid/,
                /entry/,
                /widget/,
                /sidebar/,
                /content/,
                /wp-/,
                /wp-admin-bar/,
                /customize/,
                /mpm/,
            ],
            greedy: [
                /modal/,
                /fade/,
                /show/,
                /active/,
                /tooltip/,
                /popover/,
                /loading/
            ]
        },
    });

    console.log('Results count:', results.length);
    for (const result of results) {
        console.log(`Writing ${result.file} (${result.css.length} bytes)`);
        fs.writeFileSync(result.file, result.css);
    }
}

run().catch(err => { console.error(err); process.exit(1); });
