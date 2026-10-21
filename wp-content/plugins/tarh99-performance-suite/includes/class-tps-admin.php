<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class TPS_Admin {
    private $plugin;

    public function __construct( $plugin ) {
        $this->plugin = $plugin;
        add_action( 'admin_menu', array( $this, 'menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
        add_action( 'admin_post_tps_save', array( $this, 'save' ) );
        add_action( 'admin_post_tps_toggle', array( $this, 'toggle' ) );
        add_action( 'admin_post_tps_export', array( $this, 'export_diagnostics' ) );
        add_action( 'wp_ajax_tps_regen_images', array( $this, 'ajax_regen_images' ) );
        add_action( 'admin_post_tps_regen_reset', array( $this, 'reset_regen' ) );
    }

    public function menu() {
        add_menu_page( 'Tarh99 Performance Suite', 'Tarh99 Speed', 'manage_options', 'tarh99-speed', array( $this, 'page' ), 'dashicons-performance', 80 );
    }

    public function assets( $hook ) {
        if ( 'toplevel_page_tarh99-speed' !== $hook ) {
            return;
        }
        wp_enqueue_style( 'tps-admin', TPS_URL . 'assets/admin.css', array(), TPS_VERSION );
        wp_enqueue_script( 'tps-admin', TPS_URL . 'assets/admin.js', array(), TPS_VERSION, true );
        wp_localize_script( 'tps-admin', 'tpsAdmin', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'tps_regen_images' ),
        ) );
    }

    private function checked( $field ) {
        checked( (bool) $this->plugin->get( $field ), true );
    }

    private function switch_field( $field, $label, $description, $recommended = '' ) {
        echo '<label class="tps-switch-row"><span class="tps-switch-copy"><strong>' . esc_html( $label ) . '</strong><small>' . esc_html( $description ) . '</small>';
        if ( $recommended ) {
            echo '<em>' . esc_html( $recommended ) . '</em>';
        }
        echo '</span><span class="tps-switch"><input type="checkbox" name="' . esc_attr( $field ) . '" value="1" ';
        $this->checked( $field );
        echo '><span class="tps-track"></span></span></label>';
    }

    public function page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'overview';
        if ( ! in_array( $tab, array( 'overview', 'modules', 'pooshe', 'rules', 'images', 'scanner' ), true ) ) {
            $tab = 'overview';
        }
        $root = admin_url( 'admin.php?page=tarh99-speed' );
        $scan = get_transient( 'tps_scan_' . get_current_user_id() );
        $rocket = TPS_Plugin::rocket_active();
        echo '<div class="wrap tps-admin" dir="rtl"><header class="tps-top"><div><span class="tps-eyebrow">TARH99 • PERFORMANCE SUITE <b>v' . esc_html( TPS_VERSION ) . '</b></span><h1>مرکز بهینه‌سازی سایت</h1><p>بهینه‌سازی کنترل‌شده برای WooCommerce و Elementor در کنار WP Rocket</p></div><div class="tps-status"><span class="tps-dot ' . ( $this->plugin->is_enabled() ? 'on' : '' ) . '"></span>' . ( $this->plugin->is_enabled() ? 'بهینه‌سازی فعال' : 'حالت ایمن — متوقف' ) . '</div></header>';
        if ( isset( $_GET['saved'] ) ) {
            echo '<div class="notice notice-success inline"><p>تنظیمات ذخیره شد. در صورت نیاز کش WP Rocket را پاک کنید و صفحه را دوباره بررسی کنید.</p></div>';
        }
        echo '<nav class="tps-tabs">';
        $tabs = array( 'overview' => 'داشبورد', 'modules' => 'ماژول‌ها', 'pooshe' => 'پوسته Pooshe', 'rules' => 'مدیریت منابع', 'images' => 'ساخت تصویر کم‌حجم', 'scanner' => 'اسکن و گزارش' );
        foreach ( $tabs as $key => $name ) {
            echo '<a class="' . ( $tab === $key ? 'active' : '' ) . '" href="' . esc_url( add_query_arg( 'tab', $key, $root ) ) . '">' . esc_html( $name ) . '</a>';
        }
        echo '</nav>';

        if ( 'overview' === $tab ) {
            $this->overview( $rocket, $scan, $root );
        } elseif ( 'modules' === $tab ) {
            $this->settings_form( 'modules' );
        } elseif ( 'pooshe' === $tab ) {
            $this->settings_form( 'pooshe' );
        } elseif ( 'rules' === $tab ) {
            $this->settings_form( 'rules' );
        } elseif ( 'images' === $tab ) {
            $this->images_page();
        } else {
            $this->scanner( $scan );
        }
        echo '<footer class="tps-footer">Tarh99 Performance Suite v' . esc_html( TPS_VERSION ) . ' — هیچ تغییری مستقیماً در فایل‌های قالب یا دیتابیس محصولات داده نمی‌شود.</footer></div>';
    }

    private function overview( $rocket, $scan, $root ) {
        $count = 0;
        foreach ( array( 'wc_image_sizes', 'product_lcp', 'product_logo_priority', 'register_card_size', 'remove_dashicons', 'remove_emoji', 'fa_dedupe', 'manual_lcp', 'manual_asset_rules', 'pooshe_lazy_fix', 'pooshe_fonts_fix', 'pooshe_block_css', 'pooshe_related_fallback' ) as $key ) {
            $count += (int) (bool) $this->plugin->get( $key );
        }
        echo '<div class="tps-grid tps-grid-three"><div class="tps-tile"><span>وضعیت WP Rocket</span><strong>' . ( $rocket ? 'شناسایی شد' : 'شناسایی نشد' ) . '</strong><small>' . ( $rocket ? 'قابلیت‌های هم‌پوشان تکرار نمی‌شوند' : 'وجود افزونه فعال را بررسی کنید' ) . '</small></div><div class="tps-tile"><span>ماژول‌های انتخاب‌شده</span><strong>' . esc_html( (string) $count ) . ' / 13</strong><small>همه ماژول‌های پرریسک اختیاری هستند</small></div><div class="tps-tile"><span>آخرین اسکن منابع</span><strong>' . ( $scan ? esc_html( $scan['date'] ) : 'ثبت نشده' ) . '</strong><small>اسکن از نوار مدیریت سایت قابل اجراست</small></div></div>';
        echo '<div class="tps-grid tps-grid-two"><section class="tps-card"><h2>برنامه اصلاح مرحله‌ای</h2><ol class="tps-steps"><li>بهینه‌سازی اندازه تصاویر کارت‌های محصولات</li><li>اولویت‌دهی تصویر اصلی محصول</li><li>کاهش منابع عمومی کم‌فایده در صفحات مهمان</li><li>اسکن فایل‌های CSS/JS قالب و افزونه‌ها</li><li>تعریف قانون حذف شرطی فقط برای منابع تأییدشده</li><li>تست مجدد LCP و تأیید Core Web Vitals در سرچ کنسول</li></ol><a class="button button-primary" href="' . esc_url( add_query_arg( 'tab', 'modules', $root ) ) . '">مدیریت ماژول‌ها</a></section>';
        echo '<section class="tps-card"><h2>بررسی هماهنگی با WP Rocket</h2>';
        if ( $rocket ) {
            $flags = TPS_Plugin::rocket_status();
            $names = array( 'remove_unused_css' => 'Remove Unused CSS', 'delay_js' => 'Delay JavaScript Execution', 'defer_all_js' => 'Load JavaScript Deferred', 'lazyload' => 'LazyLoad تصاویر', 'manual_preload' => 'Preload کش' );
            foreach ( $names as $key => $label ) {
                echo '<div class="tps-status-row"><span>' . esc_html( $label ) . '</span><b class="' . ( ! empty( $flags[ $key ] ) ? 'tps-green' : 'tps-muted' ) . '">' . ( ! empty( $flags[ $key ] ) ? 'فعال' : 'غیرفعال / نامشخص' ) . '</b></div>';
            }
        } else {
            echo '<p>WP Rocket در این بارگذاری شناسایی نشد. اگر فعال است، صفحه را مجدد بارگذاری کنید.</p>';
        }
        echo '<p class="tps-hint">طبق خروجی تنظیمات ارسالی ۱۶ اکتبر، Remove Unused CSS و Defer/Delay JS غیرفعال بودند؛ Minify CSS و JS فعال بودند. Preload کش با manual_preload بررسی می‌شود. گزینه‌های CSS و Delay JS را مرحله‌ای در خود WP Rocket فعال کنید.</p></section></div>';
        echo '<section class="tps-card"><div class="tps-between"><div><h2>خاموشی فوری همه بهینه‌سازی‌های Tarh99</h2><p class="tps-hint">در صورت بروز مشکل ظاهری یا عملکردی، با یک کلیک همه بهینه‌سازی‌ها متوقف می‌شوند. WP Rocket تغییری نمی‌کند.</p></div>';
        $this->toggle_form();
        echo '</div></section>';
        $logs = get_option( TPS_Plugin::LOG_OPTION, array() );
        if ( is_array( $logs ) && $logs ) {
            echo '<section class="tps-card"><h2>آخرین رویدادها</h2>';
            foreach ( array_slice( $logs, 0, 6 ) as $line ) {
                echo '<div class="tps-status-row"><span>' . esc_html( $line['text'] ) . '</span><small>' . esc_html( $line['date'] ) . '</small></div>';
            }
            echo '</section>';
        }
    }

    private function toggle_form() {
        echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
        wp_nonce_field( 'tps_toggle' );
        echo '<input type="hidden" name="action" value="tps_toggle"><button class="button ' . ( $this->plugin->is_enabled() ? 'tps-danger' : 'button-primary' ) . '" type="submit">' . ( $this->plugin->is_enabled() ? 'فعال‌سازی حالت ایمن' : 'فعال‌سازی دوباره بهینه‌سازی' ) . '</button></form>';
    }

    private function settings_form( $active_tab ) {
        echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
        wp_nonce_field( 'tps_save' );
        echo '<input type="hidden" name="action" value="tps_save"><input type="hidden" name="return_tab" value="' . esc_attr( $active_tab ) . '">';
        // The form posts all fields, preserving fields owned by other tabs.
        $tab_fields = array(
            'modules' => array( 'wc_image_sizes', 'wc_sizes', 'product_lcp', 'product_logo_priority', 'register_card_size', 'remove_dashicons', 'remove_emoji', 'fa_dedupe', 'manual_lcp', 'lcp_src_fragment', 'lcp_scope' ),
            'pooshe'  => array( 'pooshe_lazy_fix', 'pooshe_fonts_fix', 'pooshe_block_css', 'pooshe_related_fallback' ),
            'rules'   => array( 'manual_asset_rules', 'asset_rules' ),
        );
        $all_fields = array_unique( array_merge( ...array_values( $tab_fields ) ) );
        $other = array_diff( $all_fields, $tab_fields[ $active_tab ] );
        foreach ( $other as $key ) {
            echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( (string) $this->plugin->get( $key ) ) . '">';
        }
        if ( 'modules' === $active_tab ) {
            echo '<section class="tps-card"><h2>بهینه‌سازی تصاویر فروشگاه</h2>';
            $this->switch_field( 'wc_image_sizes', 'اصلاح ابعاد نمایشی تصاویر WooCommerce', 'مقدار sizes تصاویر کارت محصول را بهینه می‌کند؛ تصویر جدید نمی‌سازد.', 'پیش‌فرض: فعال' );
            echo '<div class="tps-field"><label for="wc_sizes">الگوی اندازه تصاویر کارت محصولات (sizes)</label><input id="wc_sizes" class="large-text" type="text" name="wc_sizes" dir="ltr" value="' . esc_attr( $this->plugin->get( 'wc_sizes' ) ) . '"><small>بر اساس شبکه دو ستونه در موبایل تنظیم اولیه شده؛ بعد از دریافت قالب قابل دقیق‌تر شدن است.</small></div>';
            $this->switch_field( 'register_card_size', 'ثبت تصویر میانی ۴۲۰ پیکسلی', 'سایز tps_card_420 را برای آپلودهای جدید ثبت می‌کند. تصاویر قدیمی فقط با اجرای دستی ابزار تصویر ساخته می‌شوند.', 'پیش‌فرض: فعال' );
            $this->switch_field( 'product_logo_priority', 'اولویت لوگو فقط در حالت بدون LCP محصول', 'برای سایت فعلی توصیه نمی‌شود. اگر تصویر اصلی محصول اولویت دارد، این گزینه عمداً نادیده گرفته می‌شود تا دو تصویر با اولویت بالا با هم رقابت نکنند.', 'توصیه: غیرفعال' );
            $this->switch_field( 'product_lcp', 'اولویت تصویر اصلی صفحه محصول', 'تصویر اصلی صفحه محصول را حتی در خروجی گالری ووکامرس eager/high می‌کند؛ لوگوی هدر در این حالت اولویت بالا نمی‌گیرد.', 'توصیه: فعال' );
            echo '</section><section class="tps-card"><h2>منابع وردپرس و آیکون‌ها</h2>';
            $this->switch_field( 'remove_dashicons', 'حذف شرطی Dashicons برای مهمان', 'فقط اگر فایل وابستگی ثبت‌شده دیگری نباشد؛ اگر آیکون‌ها ناپدید شدند خاموش کنید.', 'پیش‌فرض: فعال' );
            $this->switch_field( 'remove_emoji', 'کاهش اسکریپت‌های Emoji وردپرس', 'تشخیص Emoji وردپرس در بخش عمومی را خاموش می‌کند؛ ایموجی‌های معمول سیستم‌عامل همچنان قابل نمایش هستند.', 'پیش‌فرض: فعال' );
            $this->switch_field( 'fa_dedupe', 'حذف نسخه‌های کاملاً تکراری Font Awesome', 'فقط دو استایل با URL دقیقاً یکسان را بررسی می‌کند؛ نسخه‌ها و وزن‌های متفاوت دست‌نخورده می‌مانند.', 'پیش‌فرض: فعال' );
            echo '</section><section class="tps-card"><h2>تنظیم دقیق تصویر LCP</h2>';
            $this->switch_field( 'manual_lcp', 'اصلاح دستی تصویر هدف', 'فقط وقتی آدرس/بخشی از نام تصویر زیر را مشخص کرده باشید، ویژگی‌های eager و high اعمال می‌شود.', 'پیش‌فرض: غیرفعال' );
            echo '<div class="tps-field"><label for="lcp_src_fragment">بخشی از آدرس یا نام فایل تصویر LCP</label><input id="lcp_src_fragment" class="large-text" name="lcp_src_fragment" type="text" dir="ltr" value="' . esc_attr( $this->plugin->get( 'lcp_src_fragment' ) ) . '" placeholder="hero-banner.webp"><small>باید مختص همان تصویر باشد. نام کوتاه و عمومی مثل jpg وارد نکنید؛ WP Rocket ممکن است خودش این تصویر را بهینه کرده باشد.</small></div>';
            echo '<div class="tps-field"><label for="lcp_scope">محدوده اصلاح تصویر</label><select id="lcp_scope" name="lcp_scope">';
            foreach ( array( 'home' => 'صفحه اصلی', 'shop' => 'صفحه فروشگاه', 'product' => 'صفحات تک‌محصول', 'page' => 'صفحات عادی', 'all' => 'همه صفحات عمومی' ) as $key => $label ) {
                echo '<option value="' . esc_attr( $key ) . '" ' . selected( $this->plugin->get( 'lcp_scope' ), $key, false ) . '>' . esc_html( $label ) . '</option>';
            }
            echo '</select></div><p class="tps-hint">این اصلاح فقط ویژگی img را تغییر می‌دهد؛ اگر بنر با CSS background-image ساخته شده، نیاز به بررسی قالب دارد.</p></section>';
        } elseif ( 'pooshe' === $active_tab ) {
            echo '<section class="tps-card"><h2>اصلاحات اختصاصی قالب Pooshe</h2>';
            if ( 'pooshe' !== get_template() ) {
                echo '<div class="tps-note">قالب اصلی فعال Pooshe نیست؛ این گزینه‌ها اثری ندارند.</div>';
            } else {
                echo '<div class="tps-note">کد قالب ارسالی بررسی شد: includes/lazy-loading.php به صورت سراسری تصاویر را lazy می‌کند، functions.php سه فونت را preload می‌کند و main.css/app.js سراسری هستند. گزینه‌های زیر صرفاً Hookها را اصلاح می‌کنند و به فایل قالب دست نمی‌زنند.</div>';
            }
            $this->switch_field( 'pooshe_lazy_fix', 'رفع LazyLoad اجباری قالب Pooshe', 'سه فیلتر تحمیلی قالب را غیرفعال می‌کند تا WP Rocket و وردپرس بتوانند درباره تصاویر بالای صفحه تصمیم بگیرند. LazyLoad اصلی همچنان فعال می‌ماند.', 'پیش‌فرض: فعال' );
            $this->switch_field( 'pooshe_fonts_fix', 'حذف Preload اجباری سه وزن فونت', 'پیش‌بارگذاری سه وزن YekanBakh در همه صفحات را متوقف می‌کند و مدیریت فونت حیاتی را به WP Rocket می‌سپارد؛ ابتدا روی موبایل تست شود.', 'پیش‌فرض: غیرفعال' );
            $this->switch_field( 'pooshe_block_css', 'حذف CSS بلوک‌های وردپرس از صفحه محصول مهمان', 'فقط در صفحات تک‌محصول بدون بلوک در محتوای محصول و بدون وابستگی شناخته‌شده؛ ممکن است هدر/فوتر از بلوک استفاده کند. فقط بعد از بررسی ظاهر فعال شود.', 'پیش‌فرض: غیرفعال' );
            $this->switch_field( 'pooshe_related_fallback', 'نمایش ایمن محصولات مرتبط هنگام Delay JavaScript', 'در صفحه محصول، تا قبل از راه‌اندازی Swiper، محصولات مرتبط را به شکل گرید واکنش‌گرا نمایش می‌دهد. پس از اجرای اسکریپت، اسلایدر اصلی قالب فعال می‌شود. فقط CSS؛ بدون حذف هیچ فایل JS.', 'پیشنهاد: فعال' );
            echo '<p class="tps-hint">حذف خودکار فایل‌های pooshe-app / pooshe-swiper / pooshe-main انجام نمی‌شود؛ چون app.js به Swiper و عملکرد عمومی قالب وابسته است. برای CSS از Remove Unused CSS در WP Rocket استفاده کنید.</p></section>';
        } else {
            echo '<section class="tps-card"><h2>حذف شرطی CSS و JavaScript</h2><p class="tps-hint">قبل از تعریف قانون، صفحه مربوط را با ابزار «اسکن منابع» بررسی کنید. هیچ قانون حذف خودکار برای Elementor یا WooCommerce از پیش وارد نشده است.</p>';
            $this->switch_field( 'manual_asset_rules', 'اجرای قوانین حذف شرطی منابع', 'قوانین پایین فقط روی صفحات عمومی عادی اجرا می‌شوند؛ صفحات سبد، پرداخت و حساب کاربری محافظت شده‌اند.', 'پیش‌فرض: غیرفعال' );
            echo '<div class="tps-field"><label for="asset_rules">قوانین؛ هر خط یک قانون</label><textarea id="asset_rules" name="asset_rules" rows="10" dir="ltr" class="large-text code" placeholder="home|css|some-unused-handle\nshop|js|some-unused-script">' . esc_textarea( $this->plugin->get( 'asset_rules' ) ) . '</textarea><small>قالب: scope|css یا js|handle. محدوده‌ها: all، home، shop، product، category، page، post، archive. نام handle را از اسکن کپی کنید.</small></div>';
            echo '<div class="tps-note">فایل‌های حیاتی jQuery، Elementor frontend، WooCommerce، پرداخت، احراز هویت و منابع موردنیاز سایر فایل‌های صف، عمداً قابل حذف نیستند.</div></section>';
        }
        echo '<div class="tps-savebar"><button class="button button-primary button-hero" type="submit">ذخیره تنظیمات</button><span>بعد از ذخیره، کش WP Rocket را پاک کنید و صفحه را تست کنید.</span></div></form>';
    }


    private function images_page() {
        $cursor = max( 0, (int) get_option( TPS_Plugin::REGEN_CURSOR, 0 ) );
        echo '<section class="tps-card"><h2>ساخت اختیاری سایز ۴۲۰ پیکسل برای تصاویر قدیمی</h2>';
        echo '<p>گزارش Lighthouse نشان می‌دهد برخی کارت‌های محصول فایل ۶۷۷ پیکسلی را بارگذاری می‌کنند. برای کاهش حجم دانلود، افزونه اندازه‌ای با حداکثر عرض یا ارتفاع ۴۲۰ پیکسل و بدون برش ثبت می‌کند تا مرورگر در صورت وجود آن بتواند از srcset انتخاب کند.</p>';
        echo '<div class="tps-note">حتماً ابتدا بکاپ تصاویر بگیرید. این عملیات به‌صورت خودکار هنگام نصب اجرا نمی‌شود. پردازش فقط برای تصاویر شاخص محصولات منتشرشده است؛ مرورگر باید باز بماند و در هر درخواست حداکثر ۳ محصول بررسی می‌شود. ممکن است حجم دیسک کمی افزایش پیدا کند.</div>';
        echo '<p>وضعیت آخرین اجرا: <strong id="tps-regen-cursor">' . esc_html( (string) $cursor ) . '</strong> محصول بررسی شده</p>';
        echo '<p><button id="tps-regen-start" type="button" class="button button-primary" ' . ( $this->plugin->is_enabled() && $this->plugin->get( 'register_card_size' ) ? '' : 'disabled' ) . '>شروع پردازش مرحله‌ای</button> <button id="tps-regen-stop" type="button" class="button" disabled>توقف</button></p>';
        echo '<p id="tps-regen-message" role="status" aria-live="polite"></p>';
        echo '<p class="tps-hint">پردازش ممکن است زمان‌بر باشد. بعد از اتمام، کش WP Rocket را پاک کنید و گزارش PageSpeed جدید بگیرید.</p>';
        echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
        wp_nonce_field( 'tps_regen_reset' );
        echo '<input type="hidden" name="action" value="tps_regen_reset"><button type="submit" class="button">شروع مجدد شمارنده از صفر</button></form></section>';
    }

    public function ajax_regen_images() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Access denied.' ), 403 );
        }
        check_ajax_referer( 'tps_regen_images', 'nonce' );
        if ( ! $this->plugin->is_enabled() || ! $this->plugin->get( 'register_card_size' ) ) {
            wp_send_json_error( array( 'message' => 'ماژول تصویر غیرفعال است.' ), 400 );
        }
        if ( ! post_type_exists( 'product' ) ) {
            wp_send_json_error( array( 'message' => 'WooCommerce product post type unavailable.' ), 400 );
        }
        wp_send_json_success( TPS_Plugin::regenerate_card_batch() );
    }

    public function reset_regen() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Access denied', '', array( 'response' => 403 ) );
        }
        check_admin_referer( 'tps_regen_reset' );
        update_option( TPS_Plugin::REGEN_CURSOR, 0, false );
        wp_safe_redirect( admin_url( 'admin.php?page=tarh99-speed&tab=images' ) );
        exit;
    }

    private function scanner( $scan ) {
        echo '<section class="tps-card"><h2>ثبت منابع هر صفحه</h2><p>۱. در سایت، صفحه اصلی یا صفحه محصول را در حالی که وارد پیشخوان هستید باز کنید.<br>۲. از نوار بالای وردپرس گزینه «Tarh99: اسکن منابع این صفحه» را بزنید.<br>۳. به این تب برگردید؛ فهرست فایل‌های ثبت‌شده را خواهید دید و می‌توانید JSON گزارش را دانلود کنید.</p><p class="tps-hint">این ابزار فقط فهرست منابعی را که WordPress enqueue کرده ثبت می‌کند؛ نتایج Core Web Vitals یا منابع درج‌شده مستقیم در HTML را اندازه‌گیری نمی‌کند.</p>';
        echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
        wp_nonce_field( 'tps_export' );
        echo '<input type="hidden" name="action" value="tps_export"><button type="submit" class="button button-primary">دانلود گزارش فنی JSON برای بررسی</button></form></section>';
        if ( ! $scan || ! is_array( $scan ) ) {
            echo '<div class="tps-empty">هنوز اسکن معتبری ثبت نشده است.</div>';
            return;
        }
        echo '<section class="tps-card"><h2>آخرین اسکن: ' . esc_html( $scan['date'] ) . '</h2><p class="tps-hint" dir="ltr">' . esc_html( $scan['page'] ) . '</p>';
        foreach ( array( 'styles' => 'فایل‌های CSS', 'scripts' => 'فایل‌های JavaScript' ) as $type => $name ) {
            echo '<h3>' . esc_html( $name ) . ' (' . count( $scan[ $type ] ) . ')</h3><div class="tps-table-wrap"><table class="widefat striped"><thead><tr><th>Handle</th><th>Source URL</th><th>Dependencies</th><th>وضعیت</th></tr></thead><tbody>';
            foreach ( $scan[ $type ] as $line ) {
                echo '<tr><td><code>' . esc_html( $line['handle'] ) . '</code></td><td class="tps-url" dir="ltr">' . esc_html( $line['src'] ) . '</td><td class="tps-url" dir="ltr">' . esc_html( $line['deps'] ) . '</td><td>' . ( $line['printed'] ? 'چاپ‌شده' : 'صف' ) . '</td></tr>';
            }
            echo '</tbody></table></div>';
        }
        if ( ! empty( $scan['removed'] ) ) {
            echo '<p><strong>منابع حذف‌شده توسط افزونه در این اسکن:</strong> ' . esc_html( implode( ', ', $scan['removed'] ) ) . '</p>';
        }
        echo '</section>';
    }

    public function save() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Access denied', '', array( 'response' => 403 ) );
        }
        check_admin_referer( 'tps_save' );
        $posted = wp_unslash( $_POST );
        $defaults = TPS_Plugin::defaults();
        $new = $this->plugin->settings();
        foreach ( array( 'wc_image_sizes', 'product_lcp', 'product_logo_priority', 'register_card_size', 'remove_dashicons', 'remove_emoji', 'fa_dedupe', 'manual_lcp', 'manual_asset_rules', 'pooshe_lazy_fix', 'pooshe_fonts_fix', 'pooshe_block_css', 'pooshe_related_fallback' ) as $key ) {
            $new[ $key ] = ! empty( $posted[ $key ] ) ? 1 : 0;
        }
        $size = isset( $posted['wc_sizes'] ) && is_string( $posted['wc_sizes'] ) ? trim( $posted['wc_sizes'] ) : '';
        $new['wc_sizes'] = $size && strlen( $size ) <= 250 && preg_match( '/^[a-zA-Z0-9\s(),.%\-]+$/', $size ) ? $size : $defaults['wc_sizes'];
        $src = isset( $posted['lcp_src_fragment'] ) && is_string( $posted['lcp_src_fragment'] ) ? sanitize_text_field( $posted['lcp_src_fragment'] ) : '';
        $new['lcp_src_fragment'] = substr( $src, 0, 200 );
        $scope = isset( $posted['lcp_scope'] ) && is_string( $posted['lcp_scope'] ) ? sanitize_key( $posted['lcp_scope'] ) : 'home';
        $new['lcp_scope'] = in_array( $scope, array( 'home', 'shop', 'product', 'page', 'all' ), true ) ? $scope : 'home';
        $rules = isset( $posted['asset_rules'] ) && is_string( $posted['asset_rules'] ) ? $posted['asset_rules'] : '';
        $lines = array();
        foreach ( TPS_Plugin::parsed_rules( substr( $rules, 0, 15000 ) ) as $rule ) {
            $lines[] = implode( '|', $rule );
        }
        $new['asset_rules'] = implode( "\n", $lines );
        update_option( TPS_Plugin::OPTION, $new, false );
        TPS_Plugin::log( 'تنظیمات بهینه‌سازی توسط مدیر ذخیره شد.' );
        if ( TPS_Plugin::rocket_active() && function_exists( 'rocket_clean_domain' ) ) {
            rocket_clean_domain();
        }
        $return = isset( $posted['return_tab'] ) && in_array( $posted['return_tab'], array( 'rules', 'pooshe' ), true ) ? $posted['return_tab'] : 'modules';
        wp_safe_redirect( admin_url( 'admin.php?page=tarh99-speed&tab=' . $return . '&saved=1' ) );
        exit;
    }

    public function toggle() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Access denied', '', array( 'response' => 403 ) );
        }
        check_admin_referer( 'tps_toggle' );
        $settings = $this->plugin->settings();
        $settings['enabled'] = empty( $settings['enabled'] ) ? 1 : 0;
        update_option( TPS_Plugin::OPTION, $settings, false );
        TPS_Plugin::log( $settings['enabled'] ? 'بهینه‌سازی Tarh99 دوباره فعال شد.' : 'حالت ایمن فعال شد؛ بهینه‌سازی Tarh99 غیرفعال است.' );
        if ( TPS_Plugin::rocket_active() && function_exists( 'rocket_clean_domain' ) ) {
            rocket_clean_domain();
        }
        wp_safe_redirect( admin_url( 'admin.php?page=tarh99-speed&saved=1' ) );
        exit;
    }

    public function export_diagnostics() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Access denied', '', array( 'response' => 403 ) );
        }
        check_admin_referer( 'tps_export' );
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();
        $active = (array) get_option( 'active_plugins', array() );
        $plugins = array();
        foreach ( $active as $path ) {
            if ( isset( $all_plugins[ $path ] ) ) {
                $plugins[] = array( 'name' => $all_plugins[ $path ]['Name'], 'version' => $all_plugins[ $path ]['Version'] );
            }
        }
        global $wp_version;
        $theme = wp_get_theme();
        $settings = $this->plugin->settings();
        // URLs/handles and public plugin metadata only: no credentials, customers, cookies or content.
        $data = array(
            'generated_at' => current_time( 'mysql' ),
            'plugin_version' => TPS_VERSION,
            'wordpress_version' => $wp_version,
            'php_version' => PHP_VERSION,
            'theme_name' => $theme->get( 'Name' ),
            'theme_version' => $theme->get( 'Version' ),
            'active_plugins' => $plugins,
            'wp_rocket_detected' => TPS_Plugin::rocket_active(),
            'wp_rocket_flags' => TPS_Plugin::rocket_status(),
            'module_flags' => array_intersect_key( $settings, array_flip( array( 'enabled', 'wc_image_sizes', 'register_card_size', 'product_logo_priority', 'product_lcp', 'remove_dashicons', 'remove_emoji', 'fa_dedupe', 'manual_lcp', 'manual_asset_rules', 'lcp_scope', 'pooshe_lazy_fix', 'pooshe_fonts_fix', 'pooshe_block_css', 'pooshe_related_fallback' ) ) ),
            'last_page_scan' => get_transient( 'tps_scan_' . get_current_user_id() ),
        );
        nocache_headers();
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="tarh99-performance-diagnostic.json"' );
        echo wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
        exit;
    }
}
