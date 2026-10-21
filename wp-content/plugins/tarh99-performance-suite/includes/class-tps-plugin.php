<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class TPS_Plugin {
    const OPTION = 'tps_settings';
    const LOG_OPTION = 'tps_activity_log';
    const REGEN_CURSOR = 'tps_regen_cursor';
    private static $instance;
    private $options = array();
    private $primary_product_seen = false;
    private $removed_assets = array();
    private $scan_active = false;

    public static function defaults() {
        return array(
            'enabled'               => 1,
            'wc_image_sizes'        => 1,
            'wc_sizes'              => '(max-width: 480px) 48vw, (max-width: 768px) 33vw, (max-width: 1200px) 24vw, 300px',
            'product_lcp'           => 1,
            'product_logo_priority' => 0,
            'register_card_size'    => 1,
            'remove_dashicons'      => 1,
            'remove_emoji'          => 1,
            'fa_dedupe'             => 1,
            // Pooshe-specific integration; avoids editing licensed theme files.
            'pooshe_lazy_fix'       => 1,
            'pooshe_fonts_fix'      => 0,
            'pooshe_block_css'      => 0,
            'pooshe_related_fallback' => 1,
            'manual_lcp'            => 0,
            'lcp_src_fragment'      => '',
            'lcp_scope'             => 'home',
            'manual_asset_rules'    => 0,
            'asset_rules'           => '',
        );
    }

    public static function activate() {
        if ( ! get_option( self::OPTION ) ) {
            add_option( self::OPTION, self::defaults(), '', false );
        }
    }

    public static function boot() {
        if ( ! self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function instance() {
        return self::boot();
    }

    private function __construct() {
        $stored = get_option( self::OPTION, array() );
        $this->options = wp_parse_args( is_array( $stored ) ? $stored : array(), self::defaults() );
        $this->migrate_settings();
        if ( is_admin() ) {
            new TPS_Admin( $this );
        }
        // Theme hooks are registered while theme functions.php loads, after plugins_loaded.
        // after_setup_theme (late priority) is the first reliable point to unhook them.
        add_action( 'after_setup_theme', array( $this, 'integrate_pooshe' ), 999 );
        add_action( 'admin_bar_menu', array( $this, 'add_scan_menu' ), 110 );
        if ( $this->is_enabled() && $this->get( 'register_card_size' ) ) {
            add_action( 'after_setup_theme', array( $this, 'register_card_size' ), 30 );
        }
        if ( $this->is_enabled() && $this->get( 'product_logo_priority' ) && ! $this->get( 'product_lcp' ) ) {
            // Safeguard the specific header logo identified as the LCP candidate in
            // the 2026-10-16 Lighthouse product-page report. Other pages untouched.
            add_filter( 'rocket_lazyload_excluded_src', array( $this, 'exclude_product_logo_from_rocket_lazyload' ) );
        }
        // The Pooshe theme sets display:none on every uninitialized Swiper.
        // When WP Rocket delays the initializer, related products vanish. Show
        // a scoped, responsive CSS fallback until Swiper is ready.
        if ( $this->is_enabled() && $this->get( 'pooshe_related_fallback' ) ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'related_products_fallback_css' ), 200 );
        }
        add_action( 'template_redirect', array( $this, 'maybe_scan_or_buffer' ), 0 );
        if ( $this->is_enabled() && $this->get( 'product_lcp' ) ) {
            // WooCommerce builds the gallery HTML itself, sometimes bypassing core
            // attachment filters. The first image is handled explicitly, once.
            add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'correct_product_gallery_lcp' ), 99, 2 );
        }

        if ( ! $this->is_enabled() ) {
            return;
        }

        if ( $this->get( 'remove_emoji' ) ) {
            add_action( 'init', array( $this, 'disable_frontend_emoji' ), 30 );
        }
        if ( $this->get( 'wc_image_sizes' ) || $this->get( 'product_lcp' ) || $this->get( 'product_logo_priority' ) ) {
            add_filter( 'wp_get_attachment_image_attributes', array( $this, 'image_attributes' ), 30, 3 );
        }
        if ( $this->get( 'remove_dashicons' ) || $this->get( 'manual_asset_rules' ) || $this->get( 'fa_dedupe' ) ) {
            add_action( 'wp_print_styles', array( $this, 'optimize_styles' ), 999 );
        }
        if ( $this->get( 'manual_asset_rules' ) ) {
            add_action( 'wp_print_scripts', array( $this, 'optimize_scripts' ), 999 );
            add_action( 'wp_print_footer_scripts', array( $this, 'optimize_scripts' ), 1 );
        }
    }

    public function is_enabled() {
        return ! empty( $this->options['enabled'] );
    }

    public function get( $key ) {
        return isset( $this->options[ $key ] ) ? $this->options[ $key ] : null;
    }

    public function settings() {
        return $this->options;
    }

    /**
     * Version 1.2 could give both the small header logo and hero image a high
     * priority, competing for mobile bandwidth. Unset the obsolete logo priority
     * once, without changing any user-defined asset rules.
     */
    private function migrate_settings() {
        $schema = (string) get_option( 'tps_schema_version', '1.2.0' );
        if ( version_compare( $schema, '1.3.0', '>=' ) ) {
            return;
        }
        $changed = false;
        if ( ! empty( $this->options['product_lcp'] ) && ! empty( $this->options['product_logo_priority'] ) ) {
            $this->options['product_logo_priority'] = 0;
            $changed = true;
        }
        if ( $changed ) {
            update_option( self::OPTION, $this->options, false );
        }
        update_option( 'tps_schema_version', '1.3.0', false );
    }

    public static function log( $message ) {
        $rows = get_option( self::LOG_OPTION, array() );
        if ( ! is_array( $rows ) ) {
            $rows = array();
        }
        array_unshift( $rows, array( 'date' => current_time( 'mysql' ), 'text' => sanitize_text_field( $message ) ) );
        update_option( self::LOG_OPTION, array_slice( $rows, 0, 30 ), false );
    }

    public static function rocket_active() {
        return defined( 'WP_ROCKET_VERSION' ) || function_exists( 'rocket_clean_domain' );
    }

    public static function rocket_status() {
        if ( ! self::rocket_active() ) {
            return array();
        }
        $raw = get_option( 'wp_rocket_settings', array() );
        $raw = is_array( $raw ) ? $raw : array();
        $keys = array(
            'remove_unused_css', 'async_css', 'delay_js', 'defer_all_js',
            'lazyload', 'manual_preload', 'preload_links', 'auto_preload_fonts',
            'image_dimensions', 'minify_css', 'minify_js',
        );
        $out = array();
        foreach ( $keys as $key ) {
            $out[ $key ] = ! empty( $raw[ $key ] );
        }
        // Keep legacy key in exports, but do not equate it with actual
        // activation of cache preloading in recent WP Rocket versions.
        $out['preload'] = ! empty( $raw['manual_preload'] );
        return $out;
    }

    /**
     * Remove only the Pooshe theme's unconditional lazyload filters. WordPress core
     * and WP Rocket still decide which offscreen images are lazyloaded. In particular
     * this restores their LCP/above-the-fold heuristics instead of forcing every
     * attachment, post thumbnail and content image to load lazily.
     */
    public function integrate_pooshe() {
        if ( ! $this->is_enabled() || 'pooshe' !== get_template() ) {
            return;
        }
        if ( $this->get( 'pooshe_lazy_fix' ) ) {
            remove_filter( 'wp_get_attachment_image_attributes', 'pooshe_lazy_loading_attributes', 10 );
            remove_filter( 'the_post_thumbnail', 'pooshe_lazy_loading_post_thumbnail', 10 );
            remove_filter( 'the_content', 'pooshe_lazy_loading_content_images', 100 );
            // The accompanying 'loaded' style is cosmetic; leave it intact.
        }
        if ( $this->get( 'pooshe_fonts_fix' ) && self::rocket_active() ) {
            // Theme indiscriminately preloads 3 font weights on every page.
            // Let WP Rocket optimize actual critical font(s); no replacement preloads.
            remove_action( 'wp_head', 'pooshe_preload_yekanbakh_fonts', 1 );
        }
    }

    /**
     * Keep server-rendered related products visible even when WP Rocket delays
     * the Pooshe slider initializer. Once Swiper adds its initialized class,
     * these selectors no longer match and its native carousel takes over.
     * This is a CSS-only safety net; it does not force heavy JS to load early.
     */
    public function related_products_fallback_css() {
        if ( is_admin() || 'pooshe' !== get_template() || ! function_exists( 'is_product' ) || ! is_product() ) {
            return;
        }
        $css = '
/* TPS 1.4: Pooshe related products, safe before Swiper init */
'
            . '.related.products.pooshe-carousel .swiper-container:not(.swiper-container-initialized){display:block!important;visibility:visible!important;overflow:hidden}
'
            . '.related.products.pooshe-carousel .swiper-container:not(.swiper-container-initialized)>.swiper-wrapper{display:grid!important;grid-template-columns:minmax(0,1fr);gap:16px;transform:none!important;height:auto!important}
'
            . '.related.products.pooshe-carousel .swiper-container:not(.swiper-container-initialized)>.swiper-wrapper>.swiper-slide{width:auto!important;min-width:0;max-width:100%;height:auto;margin:0!important;transform:none!important}
'
            . '@media(min-width:768px){.related.products.pooshe-carousel .swiper-container:not(.swiper-container-initialized)>.swiper-wrapper{grid-template-columns:repeat(2,minmax(0,1fr))}}
'
            . '@media(min-width:992px){.related.products.pooshe-carousel .swiper-container:not(.swiper-container-initialized)>.swiper-wrapper{grid-template-columns:repeat(4,minmax(0,1fr))}}
';
        // The theme registers and enqueues pooshe-main at priority 10.
        // Enqueue at priority 200 so our override follows theme CSS.
        if ( wp_style_is( 'pooshe-main', 'enqueued' ) ) {
            wp_add_inline_style( 'pooshe-main', $css );
        }
    }

    public function disable_frontend_emoji() {
        if ( is_admin() ) {
            return;
        }
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
        add_filter( 'emoji_svg_url', '__return_false' );
    }

    public function register_card_size() {
        // Non-cropped 420px max dimension: preserves the original image aspect ratio.
        // Older media is NOT regenerated automatically; opt-in batches are in admin.
        add_image_size( 'tps_card_420', 420, 420, false );
    }

    public function exclude_product_logo_from_rocket_lazyload( $excluded ) {
        if ( ! function_exists( 'is_product' ) || ! is_product() || ! is_array( $excluded ) ) {
            return $excluded;
        }
        $excluded[] = 'Tarh99.com-logo-original.webp';
        return array_unique( $excluded );
    }

    private function manual_product_lcp_active() {
        return $this->get( 'manual_lcp' ) && 'product' === $this->get( 'lcp_scope' ) && '' !== trim( (string) $this->get( 'lcp_src_fragment' ) );
    }

    public function image_attributes( $attr, $attachment, $size ) {
        if ( is_admin() || ! is_array( $attr ) ) {
            return $attr;
        }
        $classes = isset( $attr['class'] ) ? (string) $attr['class'] : '';
        $thumb   = false !== strpos( $classes, 'attachment-woocommerce_thumbnail' ) || false !== strpos( $classes, 'size-woocommerce_thumbnail' );

        if ( $this->get( 'product_logo_priority' ) && ! $this->get( 'product_lcp' ) && ! $this->manual_product_lcp_active() && function_exists( 'is_product' ) && is_product() ) {
            $is_logo = ( isset( $attachment->ID ) && 14276 === (int) $attachment->ID )
                || false !== strpos( $classes, 'wp-image-14276' )
                || ( isset( $attr['src'] ) && false !== stripos( (string) $attr['src'], 'Tarh99.com-logo-original.webp' ) );
            if ( $is_logo ) {
                // Only the confirmed product-template logo, never arbitrary images.
                $attr['loading']       = 'eager';
                $attr['fetchpriority'] = 'high';
                $attr['decoding']      = 'async';
            }
        }

        if ( $this->get( 'wc_image_sizes' ) && $thumb ) {
            // Preserve WordPress-generated srcset; only advise the browser about actual card size.
            $attr['sizes'] = $this->get( 'wc_sizes' );
        }

        if ( $this->get( 'product_lcp' ) && ! $this->manual_product_lcp_active() && ! $this->primary_product_seen && function_exists( 'is_product' ) && is_product() && false !== strpos( ' ' . $classes . ' ', ' wp-post-image ' ) && ! $thumb ) {
            $this->primary_product_seen = true;
            $attr['loading'] = 'eager';
            $attr['fetchpriority'] = 'high';
        }
        return $attr;
    }

    /**
     * Improve the actual first WooCommerce gallery image. Some templates
     * generate image markup without wp_get_attachment_image_attributes; this
     * runs only for the first hero image and does not touch gallery thumbs.
     */
    public function correct_product_gallery_lcp( $html, $attachment_id = 0 ) {
        if ( ! $this->is_enabled() || ! $this->get( 'product_lcp' ) || $this->manual_product_lcp_active()
            || ! function_exists( 'is_product' ) || ! is_product() || is_admin()
            || ! class_exists( 'WP_HTML_Tag_Processor' ) || ! is_string( $html ) ) {
            return $html;
        }
        // WooCommerce invokes this hook on gallery thumbnails as well.
        // Only the main product image has the wp-post-image class.
        if ( false === strpos( $html, 'wp-post-image' ) ) {
            return $html;
        }
        $processor = new WP_HTML_Tag_Processor( $html );
        while ( $processor->next_tag( array( 'tag_name' => 'IMG' ) ) ) {
            $classes = ' ' . (string) $processor->get_attribute( 'class' ) . ' ';
            if ( false === strpos( $classes, ' wp-post-image ' ) ) {
                continue;
            }
            $processor->set_attribute( 'loading', 'eager' );
            $processor->set_attribute( 'fetchpriority', 'high' );
            return $processor->get_updated_html();
        }
        return $html;
    }

    private function frontend_safe_page() {
        if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || is_feed() ) {
            return false;
        }
        if ( function_exists( 'is_checkout' ) && is_checkout() ) {
            return false;
        }
        if ( function_exists( 'is_cart' ) && is_cart() ) {
            return false;
        }
        if ( function_exists( 'is_account_page' ) && is_account_page() ) {
            return false;
        }
        return true;
    }

    private function needs_handle( $registry, $candidate ) {
        if ( ! $registry || empty( $registry->registered ) ) {
            return true;
        }
        $queue = array_unique( array_merge( (array) $registry->queue, (array) $registry->to_do ) );
        foreach ( $queue as $handle ) {
            if ( $handle === $candidate ) {
                continue;
            }
            if ( $this->depends_on( $registry, $handle, $candidate, array() ) ) {
                return true;
            }
        }
        return false;
    }

    private function depends_on( $registry, $handle, $target, $seen ) {
        if ( isset( $seen[ $handle ] ) || ! isset( $registry->registered[ $handle ] ) ) {
            return false;
        }
        $seen[ $handle ] = true;
        foreach ( (array) $registry->registered[ $handle ]->deps as $dep ) {
            if ( $dep === $target || $this->depends_on( $registry, $dep, $target, $seen ) ) {
                return true;
            }
        }
        return false;
    }

    private function safe_remove( $type, $handle ) {
        $registry = ( 'css' === $type ) ? wp_styles() : wp_scripts();
        if ( ! $registry || ! isset( $registry->registered[ $handle ] ) ) {
            return false;
        }
        if ( in_array( $handle, (array) $registry->done, true ) || $this->needs_handle( $registry, $handle ) ) {
            return false;
        }
        if ( 'css' === $type ) {
            wp_dequeue_style( $handle );
        } else {
            wp_dequeue_script( $handle );
        }
        $this->removed_assets[] = $type . ':' . $handle;
        return true;
    }

    private function is_critical_handle( $handle ) {
        // Never dequeue foundational or checkout/payment/authentication resources automatically.
        return (bool) preg_match( '/^(jquery|wp-polyfill|wp-hooks|wp-i18n|wp-api-fetch|elementor-frontend|elementor-pro-frontend)$|^(wc-|woocommerce)|rocket|checkout|stripe|gateway|recaptcha|captcha|payment|cart-fragments|login/i', $handle );
    }

    private function matches_scope( $scope ) {
        switch ( $scope ) {
            case 'home': return is_front_page();
            case 'shop': return function_exists( 'is_shop' ) && is_shop();
            case 'product': return function_exists( 'is_product' ) && is_product();
            case 'category': return function_exists( 'is_product_category' ) && is_product_category();
            case 'post': return is_single() && ! ( function_exists( 'is_product' ) && is_product() );
            case 'page': return is_page() && ! is_front_page();
            case 'archive': return is_archive();
            case 'all': return true;
            default: return false;
        }
    }

    public static function parsed_rules( $text ) {
        $out = array();
        foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
            $line = trim( $line );
            if ( '' === $line || 0 === strpos( $line, '#' ) ) {
                continue;
            }
            $parts = array_map( 'trim', explode( '|', $line ) );
            if ( 3 !== count( $parts ) ) {
                continue;
            }
            list( $scope, $kind, $handle ) = $parts;
            if ( ! in_array( $scope, array( 'all', 'home', 'shop', 'product', 'category', 'post', 'page', 'archive' ), true ) || ! in_array( $kind, array( 'css', 'js' ), true ) || ! preg_match( '/^[a-zA-Z0-9_\-.]{1,100}$/', $handle ) ) {
                continue;
            }
            $out[] = array( $scope, $kind, $handle );
            if ( count( $out ) >= 100 ) {
                break;
            }
        }
        return $out;
    }

    private function run_rules( $type ) {
        if ( ! $this->get( 'manual_asset_rules' ) || ! $this->frontend_safe_page() ) {
            return;
        }
        foreach ( self::parsed_rules( $this->get( 'asset_rules' ) ) as $rule ) {
            list( $scope, $kind, $handle ) = $rule;
            if ( $kind !== $type || ! $this->matches_scope( $scope ) || $this->is_critical_handle( $handle ) ) {
                continue;
            }
            $this->safe_remove( $type, $handle );
        }
    }

    public function optimize_styles() {
        if ( ! $this->frontend_safe_page() ) {
            return;
        }
        $styles = wp_styles();
        if ( ! $styles ) {
            return;
        }

        if ( $this->get( 'pooshe_block_css' ) && 'pooshe' === get_template() && function_exists( 'is_product' ) && is_product() && ! is_user_logged_in() ) {
            // Optional: only on guest single-product pages, never on other content,
            // in case product content or header templates contain Gutenberg blocks.
            $post = get_post();
            if ( $post && ! has_blocks( $post ) ) {
                $this->safe_remove( 'css', 'wp-block-library' );
            }
        }
        if ( $this->get( 'remove_dashicons' ) && ! is_user_logged_in() && in_array( 'dashicons', (array) $styles->queue, true ) ) {
            $this->safe_remove( 'css', 'dashicons' );
        }

        if ( $this->get( 'fa_dedupe' ) ) {
            $seen = array();
            foreach ( (array) $styles->queue as $handle ) {
                if ( ! isset( $styles->registered[ $handle ] ) ) {
                    continue;
                }
                $src = (string) $styles->registered[ $handle ]->src;
                if ( ! preg_match( '/font[\-_]?awesome/i', $handle . ' ' . $src ) || '' === $src ) {
                    continue;
                }
                // Only remove literally identical registered resources. Distinct FA weights or versions remain intact.
                $key = strtolower( $src );
                if ( isset( $seen[ $key ] ) ) {
                    $this->safe_remove( 'css', $handle );
                } else {
                    $seen[ $key ] = $handle;
                }
            }
        }
        $this->run_rules( 'css' );
    }

    public function optimize_scripts() {
        $this->run_rules( 'js' );
    }

    private function is_get_request() {
        return isset( $_SERVER['REQUEST_METHOD'] ) && 'GET' === strtoupper( (string) $_SERVER['REQUEST_METHOD'] );
    }

    public function maybe_scan_or_buffer() {
        if ( is_admin() || ! $this->is_get_request() || is_feed() || wp_doing_ajax() || wp_doing_cron() ) {
            return;
        }
        if ( current_user_can( 'manage_options' ) && isset( $_GET['tps_scan'], $_GET['_wpnonce'] ) && '1' === (string) $_GET['tps_scan'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'tps_scan' ) ) {
            $this->scan_active = true;
            add_action( 'shutdown', array( $this, 'save_scan' ), PHP_INT_MAX );
        }

        if ( ! $this->is_enabled() || ! $this->get( 'manual_lcp' ) || '' === trim( $this->get( 'lcp_src_fragment' ) ) || ! $this->matches_scope( $this->get( 'lcp_scope' ) ) ) {
            return;
        }
        if ( ! class_exists( 'WP_HTML_Tag_Processor' ) || ( function_exists( 'is_checkout' ) && is_checkout() ) || ( function_exists( 'is_cart' ) && is_cart() ) ) {
            return;
        }
        ob_start( array( $this, 'filter_lcp_html' ) );
    }

    public function filter_lcp_html( $html ) {
        if ( false === stripos( $html, '<img' ) || false === stripos( $html, $this->get( 'lcp_src_fragment' ) ) ) {
            return $html;
        }
        $processor = new WP_HTML_Tag_Processor( $html );
        $match = (string) $this->get( 'lcp_src_fragment' );
        while ( $processor->next_tag( array( 'tag_name' => 'IMG' ) ) ) {
            $src = (string) $processor->get_attribute( 'src' );
            $lazy_src = (string) $processor->get_attribute( 'data-src' );
            $lazy_src_rocket = (string) $processor->get_attribute( 'data-lazy-src' );
            if ( false === stripos( $src, $match ) && false === stripos( $lazy_src, $match ) && false === stripos( $lazy_src_rocket, $match ) ) {
                continue;
            }
            // A target image is explicit; do not alter its URL, srcset or script-based lazyload internals.
            $processor->set_attribute( 'loading', 'eager' );
            $processor->set_attribute( 'fetchpriority', 'high' );
            return $processor->get_updated_html();
        }
        return $html;
    }


    /**
     * Regenerate ONLY the missing tps_card_420 size for at most three published
     * products per request. The admin UI invokes this via nonce-protected AJAX.
     */
    public static function regenerate_card_batch() {
        if ( ! function_exists( 'wp_update_image_subsizes' ) ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }
        $offset = max( 0, (int) get_option( self::REGEN_CURSOR, 0 ) );
        $product_ids = get_posts( array(
            'post_type'              => 'product',
            'post_status'            => 'publish',
            'fields'                 => 'ids',
            'posts_per_page'         => 3,
            'offset'                 => $offset,
            'orderby'                => 'ID',
            'order'                  => 'ASC',
            'no_found_rows'          => true,
            'suppress_filters'       => true,
            'ignore_sticky_posts'    => true,
        ) );
        $created = 0;
        $skipped = 0;
        $errors = 0;
        foreach ( $product_ids as $product_id ) {
            $attachment_id = (int) get_post_thumbnail_id( $product_id );
            if ( ! $attachment_id || ! wp_attachment_is_image( $attachment_id ) || ! is_file( get_attached_file( $attachment_id ) ) ) {
                ++$skipped;
                continue;
            }
            $missing = wp_get_missing_image_subsizes( $attachment_id );
            if ( ! isset( $missing['tps_card_420'] ) ) {
                ++$skipped;
                continue;
            }
            // Avoid regenerating every image size registered by the theme/plugins.
            $only_target = static function( $sizes, $metadata, $id ) use ( $attachment_id ) {
                return (int) $id === $attachment_id
                    ? array_intersect_key( $sizes, array( 'tps_card_420' => true ) )
                    : $sizes;
            };
            add_filter( 'wp_get_missing_image_subsizes', $only_target, 999, 3 );
            try {
                $result = wp_update_image_subsizes( $attachment_id );
                if ( is_wp_error( $result ) ) {
                    ++$errors;
                } else {
                    $updated = wp_get_attachment_metadata( $attachment_id );
                    if ( isset( $updated['sizes']['tps_card_420'] ) ) {
                        ++$created;
                    } else {
                        ++$errors;
                    }
                }
            } catch ( Throwable $e ) {
                ++$errors;
            } finally {
                remove_filter( 'wp_get_missing_image_subsizes', $only_target, 999 );
            }
        }
        $offset += count( $product_ids );
        update_option( self::REGEN_CURSOR, $offset, false );
        return array( 'processed' => count( $product_ids ), 'offset' => $offset,
            'created' => $created, 'skipped' => $skipped, 'errors' => $errors,
            'finished' => count( $product_ids ) < 3 );
    }

    public function add_scan_menu( $bar ) {
        if ( is_admin() || ! current_user_can( 'manage_options' ) || ! $this->is_get_request() ) {
            return;
        }
        global $wp;
        $path = isset( $wp->request ) && $wp->request ? $wp->request : '/';
        $url = add_query_arg( array( 'tps_scan' => '1', '_wpnonce' => wp_create_nonce( 'tps_scan' ) ), home_url( '/' . ltrim( $path, '/' ) ) );
        $bar->add_node( array( 'id' => 'tps-scan', 'title' => 'Tarh99: اسکن منابع این صفحه', 'href' => $url ) );
    }

    public function save_scan() {
        if ( ! $this->scan_active || ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $out = array( 'date' => current_time( 'mysql' ), 'page' => esc_url_raw( home_url( add_query_arg( array() ) ) ), 'styles' => array(), 'scripts' => array(), 'removed' => array_values( array_unique( $this->removed_assets ) ) );
        foreach ( array( 'styles' => wp_styles(), 'scripts' => wp_scripts() ) as $type => $registry ) {
            if ( ! $registry ) {
                continue;
            }
            $handles = array_unique( array_merge( (array) $registry->queue, (array) $registry->done, (array) $registry->to_do ) );
            foreach ( array_slice( $handles, 0, 250 ) as $handle ) {
                if ( ! isset( $registry->registered[ $handle ] ) ) {
                    continue;
                }
                $dep = $registry->registered[ $handle ];
                $out[ $type ][] = array(
                    'handle' => sanitize_text_field( $handle ),
                    'src'    => esc_url_raw( (string) $dep->src ),
                    'deps'   => implode( ', ', array_slice( array_map( 'sanitize_text_field', (array) $dep->deps ), 0, 15 ) ),
                    'printed' => in_array( $handle, (array) $registry->done, true ),
                );
            }
        }
        set_transient( 'tps_scan_' . get_current_user_id(), $out, 4 * HOUR_IN_SECONDS );
    }
}
