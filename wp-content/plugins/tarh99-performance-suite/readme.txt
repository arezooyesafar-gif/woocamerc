=== Tarh99 Performance Suite ===
Contributors: tarh99
Tags: speed, optimization, woocommerce, lcp, wp-rocket, assets
Requires at least: 6.2
Requires PHP: 7.4
Stable tag: 1.4.0
License: GPLv2 or later

== Version 1.2.0 ==
Reviewed provided Pooshe theme ZIP. Adds a safe, reversible Pooshe-specific module:
- Removes unconditional theme-added LazyLoad on attachment, thumbnail and content images, allowing WordPress/WP Rocket heuristics to take over. Enabled by default ONLY when Pooshe is active.
- Optional disable of three forced YekanBakh preloads across all pages; leave WP Rocket in charge of critical fonts. Disabled by default.
- Optional dequeue Gutenberg block library CSS on guest WooCommerce product pages without Gutenberg post blocks. Disabled by default; verify template/header visual integrity.
- Safeguard: no two images prioritized if a manual product LCP override is configured.
- Keeps v1.1 settings and safe rollback; does not modify theme files.

== Version 1.1.0 ==
Based on mobile Lighthouse report 2026-10-16, tested page:
https://tarh99.com/shop/بنر-لایه-باز-سوپر-مارکت-رایگان/
Lighthouse: performance 77, LCP 5.0s, FCP 2.2s, TBT 20ms, CLS ~0.

Changes from v1.0.0:
- Product-page logo image ID 14276 prioritized, no lazy loading and high fetch priority, plus WP Rocket lazyload exclusion (other pages unchanged).
- Registered optional proportional 420px WooCommerce product card image size for future uploads.
- Opt-in, cancellable batch generator for existing product featured images; at most 3 products per AJAX request, only the newly registered size is generated.
- User settings retained across update.

== Installation / Upgrade ==
Plugins > Add New > Upload Plugin > choose v1.1.0 ZIP; approve replacing existing plugin.
Then visit Tarh99 Speed > Modules and review. WP Rocket stays installed.
Backup before optional old-image regeneration; the feature writes new thumbnail files to uploads and media metadata.

== Important ==
The plugin does NOT rewrite main.css, app.js, turn on Remove Unused CSS, guarantee a PageSpeed score or guarantee real-user Core Web Vitals passing. The current LCP logo can be added late by third-party Elementor lazyloading; check Elements/View Source after clearing WP Rocket and browser cache. Elementor performance experiments may conflict with WP Rocket's critical image optimization.
Optimizing image sub-sizes requires disk space; generated images can reduce network transfer but increase disk usage. The stop button halts after the currently executing request.
Never automatically dequeue WooCommerce/Elementor/payment resources without verifying their dependencies and testing checkout.

== 1.3.0 ==
- Fix WooCommerce product main image LCP via gallery HTML filter.
- Avoid competing high-priority product logo.
- Fix misleading WP Rocket preload diagnostics based on manual_preload.
- All risky asset removals remain opt-in.

== Version 1.4.0 ==
- Pooshe related product carousel: theme main.css hides uninitialized Swiper containers using display:none. WP Rocket Delay JS can postpone the initialization.
- Adds an on-by-default, optional CSS fallback limited to WooCommerce related products on single product pages; server-rendered products remain visible as a responsive grid until Swiper initializes.
- Does not disable Delay JS or remove scripts, does not modify theme files.
- If Swiper functionality is still broken, troubleshoot jQuery, WP_OBJ inline localization and script order in WP Rocket Delay exclusions.
