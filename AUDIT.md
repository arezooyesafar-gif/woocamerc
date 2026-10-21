# گزارش بررسی کامل سرعت — tarh99.com (مرحله ۱)

> تاریخ بررسی: ۲۰۲۶-۱۰-۲۱ — فقط تحلیل کد موجود در مخزن (بدون دسترسی به دیتابیس، پنل هاست و تنظیمات ذخیره‌شده WP Rocket/المنتور).
> مواردی که به دیتابیس یا تنظیمات پیشخوان وابسته‌اند با «نیاز به تأیید» علامت خورده‌اند و کوئری/روش بررسی‌شان در `db-audit.sql` و `docs/` آمده است.

---

## ۰. خلاصه مدیریتی

| مشکل گزارش‌شده | علت(های) اصلی پیدا‌شده در کد |
|---|---|
| FCP ‏2.9s / LCP ‏6.5s موبایل | فایل CSS قالب `dist/style/main.css` به حجم **۵۷۳KB (۸۶KB gzip)** رندربلاک است (شامل Bootstrap، Font Awesome 5 Pro کامل، استایل‌های LearnPress/Dokan/RCP که روی سایت نصب نیستند)، به‌علاوه CSS المنتور/المنتور پرو، ووکامرس و فونت. قالب روی **همه تصاویر** اجباری `loading="lazy"` می‌گذارد (حتی تصویر LCP). سه فونت (۱۰۰KB) در همه صفحات preload می‌شوند و با تصویر LCP رقابت می‌کنند. |
| کندی جستجو | جستجوی زنده قالب از `admin-ajax.php` بدون کش، با `LIKE '%…%'` روی عنوان، خلاصه و **متن کامل** ۳۰۰۰ محصول اجرا می‌شود و در صورت نبود نتیجه، دو کوئری دیگر (شناسه و SKU روی `postmeta`) هم اجرا می‌کند. صفحه نتایج جستجو هم توسط WP Rocket کش نمی‌شود. |
| کندی سبد/تسویه | این صفحات ذاتاً کش نمی‌شوند؛ هر بار کل وردپرس + ۱۷ افزونه + المنتور اجرا می‌شود. افزونه «اشتراک‌یار» در **هر درخواست غیرکش** حدود ۱۱ کوئری `SHOW TABLES / DESC / SHOW INDEX` اجرا می‌کند. هیچ Object Cache پایداری وجود ندارد. |
| کند شدن ناگهانی بعد از چند محصول | (۱) اسلایدرهای Wooslider محصولات را بعد از لود صفحه با `admin-ajax` می‌گیرند → هر بازدید = چند درخواست PHP غیرکش؛ روی هاست اشتراکی (CloudLinux/LVE) این درخواست‌ها سهمیه CPU/Entry Process را پر می‌کنند و درخواست‌های بعدی صف می‌شوند. (۲) محصولات زیاد (۳۰۰۰) و عمر کش WP Rocket باعث می‌شود بسیاری از صفحات محصول در لحظه بازدید کش نشده باشند. (۳) اگر کوکی‌های ووکامرس در «Never Cache Cookies» راکت ثبت شده باشند، بعد از افزودن به سبد کل سایت از کش خارج می‌شود (نیاز به تأیید در تنظیمات راکت). |
| حالت لاگین کند | کاربر لاگین‌شده هیچ‌وقت کش صفحه ندارد؛ بنابراین همه هزینه‌های سمت سرور بالا (اشتراک‌یار، المنتور، کوئری‌ها، نبود Object Cache) مستقیماً دیده می‌شود. Heartbeat هم برای کاربر لاگین در فرانت فعال است. |
| پیشخوان کند | درخواست‌های بیرونی (لایسنس/آپدیت) چند افزونه از سرور ایران به سرورهای خارجی (المنتور، Brainstorm/Schema Pro، یواست، wordpress.org، راست‌چین)، WooCommerce Admin (Analytics، Inbox، Marketing، پیشنهادها)، ویجت‌های داشبورد (WooCommerce Status با کوئری سنگین، اخبار وردپرس، المنتور، یواست)، Heartbeat هر ۱۵–۶۰ ثانیه و کوئری‌های اشتراک‌یار در هر صفحه. |
| خطاهای CWV در سرچ کنسول | همان LCP بالا (lazy روی تصویر LCP + CSS سنگین) و TTFB بالای صفحات غیرکش. |

**مهم‌ترین کشف‌های غیرمنتظره**
0. ⛔ **ریسک از کار افتادن کامل سایت (بخش ۱۶):** فایل‌های رمزگذاری‌شده محصولات راست‌چین (قالب پوشه، WP Rocket، المنتور پرو، یواست پرمیوم، uPanel، Wooslider، اشتراک‌یار) با **ionCube Loader نسخه 15.5.1** (آخرین نسخه) خطای «created with an unauthorised copy of the ionCube PHP Encoder» می‌دهند و اجرا نمی‌شوند؛ تا نسخه 15.5.0 کار می‌کنند. همچنین اگر افزونه RTL-CareUnit غیرفعال شود، همه این محصولات در **هر بازدید** تلاش می‌کنند یک فایل zip را با زمان انتظار ۳۰۰ ثانیه دانلود کنند.
1. افزونه بهینه‌سازی قبلی `tarh99-performance-suite` (TPS نسخه 1.4.0) روی سایت نصب است و بخشی از کارها (اولویت تصویر محصول، حذف lazy اجباری قالب، ایموجی، dashicons) را انجام می‌دهد. افزونه جدید طوری طراحی شده که با آن تداخل نکند و در نهایت جایگزینش شود.
2. `functions.php` قالب پوشه قبلاً دستی ویرایش شده (preload فونت، حذف ایموجی، بافر خروجی برای همه مهمان‌ها، تکمیل خودکار سفارش، تغییر breadcrumb). این تغییرات با **آپدیت قالب پاک می‌شوند**؛ بعضی‌شان (مثل `woocommerceir_autocomplete_processing_orders`) عملکرد فروشگاه را تغییر می‌دهند و باید جایی پایدار نگهداری شوند.
3. شمارنده بازدید قالب (`includes/post-views-counter.php`) به‌خاطر یک باگ در قالب **اصلاً اجرا نمی‌شود** (`add_action('plugins_loaded', ...)` بعد از اجرای `plugins_loaded` ثبت می‌شود). بنابراین بار دیتابیسی ندارد — نیازی به بهینه‌سازی نیست.
4. پوشه‌های اضافی داخل `wp-content/plugins` (`Addons`، `Help`، `Plugin`، `Yoast`، `__MACOSX`، `2- Documentation`) شامل **۲۶۴ فایل PHP** از یک قالب/پکیج دیگر هستند که مستقیماً از وب قابل دسترسی‌اند (ریسک امنیتی).
5. افزونه Wordfence نصب نیست ولی پوشه `wp-content/wflogs` مانده؛ احتمال وجود `auto_prepend_file` و جداول `wp_wf*` باقی‌مانده.

---

## ۱. فهرست افزونه‌ها

| # | پوشه | نام | نسخه | کاربرد | نکات سرعت/ریسک |
|---|---|---|---|---|---|
| 1 | `woocommerce` | WooCommerce | 11.2.1 | هسته فروشگاه | CSS عمومی + `woocommerce.js` + `sourcebuster`/Order Attribution در همه صفحات؛ WooCommerce Admin در پیشخوان |
| 2 | `elementor` | Elementor | 4.3.4 | صفحه‌ساز | هدر/فوتر و صفحات با المنتور → CSS/JS در همه صفحات |
| 3 | `elementor-pro` | Elementor Pro | 4.2.3 | صفحه‌ساز (نسخه راست‌چین) | `frontend-rtl.min.css` ‏۴۵۹KB؛ کد دور زدن لایسنس (بخش ۱۴) |
| 4 | `wp-rocket` | WP Rocket (ترجمه Syntax/راست‌چین) | 3.23.2.2 | کش و بهینه‌سازی | بلوک rewrite کش در `.htaccess` وجود ندارد → کش از طریق PHP (`advanced-cache.php`) سرو می‌شود |
| 5 | `wordpress-seo` | Yoast SEO | 28.6 | سئو | Indexables؛ ویجت داشبورد |
| 6 | `wordpress-seo-premium` | Yoast SEO Premium | 28.4 | سئو | نسخه پایین‌تر از free (28.4 در برابر 28.6) — ناسازگاری احتمالی |
| 7 | `wpseo-woocommerce` | Yoast WooCommerce SEO | 16.8 | سئو فروشگاه | `WC tested up to 10.6` |
| 8 | `wp-schema-pro - 10` | Schema Pro | 2.7.4 | اسکیما | نام پوشه غیراستاندارد (آپدیت خودکار را مختل می‌کند)؛ bsf-core در `admin_init` چک آپدیت ریموت دارد؛ با Yoast اسکیمای تکراری تولید می‌شود |
| 9 | `all-in-one-wp-security-and-firewall` | AIOS | 5.4.10 | امنیت | فایروال 5G در htaccess، کران ۱۵ دقیقه‌ای و ساعتی، اسکن فایل، لاگ رویدادها |
| 10 | `advanced-database-cleaner-premium` | Advanced DB Cleaner Pro | 4.1.1 | پاکسازی دیتابیس | کران‌های اتوماسیون و آنالیتیکس؛ فقط وقتی لازم است فعال باشد |
| 11 | `wp-mail-smtp` | WP Mail SMTP | 4.10.2 | ارسال ایمیل | فقط پیشخوان؛ گزارش هفتگی |
| 12 | `zarinpal-woocommerce-payment-gateway` | درگاه زرین‌پال | 5.1.1 | پرداخت | اسکریپت فقط در cart/checkout؛ یک درخواست ریموت برای تشخیص کارمزد (فقط یک‌بار) |
| 13 | `eshterakyar` | اشتراک‌یار | 1.5.0 | اشتراک و دانلود | **۱۱ کوئری اسکیما در هر درخواست** (بخش ۳)؛ CSS/JS در صفحه محصول و حساب کاربری |
| 14 | `uPanel` | پنل کاربری uPanel | 4.1.2 | پنل کاربری | آسیب‌پذیری SQL Injection در پیشخوان (بخش ۱۴)؛ `get_transient` در هر `init` |
| 15 | `wooslider-bymdez` | Wooslider | 6.1.12 | اسلایدر محصولات | بارگذاری محصولات با **admin-ajax بعد از لود صفحه**؛ Swiper دوم (۱۳۶KB)؛ اسکریپت inline در `<head>` همه صفحات |
| 16 | `RTL-CareUnit` | مدیریت هوشمند راست‌چین | 2.5.2 | لایسنس/آپدیت محصولات راست‌چین | ionCube؛ در هر درخواست اجرا و `set_time_limit(120)` می‌کند؛ رفتار داخلی قابل بررسی نیست |
| 17 | `whitespace-fixer-for-xml-sitemap` | Whitespace Fixer | 1.1.1 | اصلاح سایت‌مپ | SDK تله‌متری (ارسال ایمیل ادمین به wp-turbo.com هنگام فعال/غیرفعال‌سازی) |
| 18 | `tarh99-performance-suite` | Tarh99 Performance Suite | 1.4.0 | بهینه‌سازی قبلی | جایگزین با افزونه mu جدید |

سایر موارد:
- `mu-plugins/elementor-safe-mode.php`: فقط یک `get_option` در هر درخواست؛ بی‌خطر.
- `wp-content/plugin/RTL-CareUnit` (پوشه اشتباه `plugin` بدون s، نسخه قدیمی 1.6.0): توسط وردپرس لود نمی‌شود؛ باقی‌مانده است.
- پوشه‌های `Addons`، `Help`، `Plugin`، `Yoast`، `__MACOSX`، `2- Documentation`، `Help.pdf` داخل `plugins`: فایل‌های فشرده‌ای که اشتباهی آنجا باز شده‌اند (۲۶۴ فایل PHP قالب Rooten و افزونه‌های bdthemes).
- Wordfence: افزونه نیست، فقط `wflogs`. باید در `.user.ini`/`php.ini` دنبال `auto_prepend_file = wordfence-waf.php` گشت (اگر فایلش حذف شده باشد ولی دستور مانده باشد، خطا/تأخیر ایجاد می‌کند) و جداول `wp_wf*` را بررسی کرد (`db-audit.sql` بخش ۱۰).
- قالب‌ها: `pooshe` (نسخه 6) و `twentytwentyfive` (نسخه 1.0، به‌عنوان قالب پشتیبان می‌تواند بماند).
- ۲۵ فایل خالی `temp-write-test-*` در `wp-content` (باقی‌مانده تست نوشتن افزونه‌ها) — بی‌ضرر، قابل حذف.
- PHP فعلی سایت طبق `.htaccess`: **ea-php81** (پشتیبانی امنیتی PHP 8.1 تمام شده).

---

## ۲. CSS و JS بدون شرط در همه صفحات

### قالب پوشه (`functions.php` → `pooshe_scripts`)
| handle | فایل | حجم (raw / gzip) | نکته |
|---|---|---|---|
| `pooshe-style` | `style.css` | 0.6KB | — |
| `pooshe-print-style` | `print.css` | — | media=print، غیربلاک |
| `pooshe-main` | `dist/style/main.css` | **573KB / 86KB** | رندربلاک؛ شامل Bootstrap، FA5 Pro (بدون `font-display`)، ~۲۲KB LearnPress، ~۶KB Dokan، ~۷KB RCP، ~۲۶KB حالت تاریک |
| `pooshe-yekanbakh` (یا فونت انتخابی) | `dist/font/*/css/fontiran.css` | 1.8KB | `font-display: swap` دارد ✅ |
| `pooshe-sms` | `dist/js/smart-login.js` | 11KB | defer ✅ |
| `pooshe-swiper` | `dist/js/swiper.min.js` | 134KB / 34KB | defer ✅ |
| `pooshe-app` | `dist/js/app.js` | 264KB / 80KB | defer ✅؛ شامل Bootstrap JS، select2 init، جستجو، سبد |
| `pooshe-dark-mode` | `dist/js/dark-mode.js` | 3.8KB | defer ✅ |
| inline | `pooshe_lazy_loading_css`، `pooshe_quick_view_css` | — | در `<head>` همه صفحات |
| inline | مودال Quick View در `wp_footer` | — | همه صفحات |
| preload | ۳ فونت YekanBakh (Regular/SemiBold/Bold) | ۱۰۰KB | **تغییر دستی در functions.php**؛ با تصویر LCP رقابت می‌کند |

قالب خودش این‌ها را شرطی کرده (`includes/dequeue-assets.php`): `selectWoo/select2` فقط checkout، `wc-add-to-cart` فقط صفحات محصول/فروشگاه، `wc-cart-fragments` فقط cart/checkout، `wc-checkout`… فقط checkout. ✅

### ووکامرس
- `woocommerce-layout`، `woocommerce-smallscreen`، `woocommerce-general` (RTL) در **همه** صفحات (رفتار پیش‌فرض ووکامرس) — چون مینی‌سبد هدر در همه صفحات است، حذفش از صفحات غیرفروشگاهی ریسک ظاهری دارد.
- `woocommerce` (JS) + `js-cookie` در همه صفحات.
- `sourcebuster-js` + `wc-order-attribution` در همه صفحات (اگر قابلیت Order Attribution روشن باشد — نیاز به تأیید).

### المنتور / المنتور پرو
- چون هدر و فوتر با ویجت‌های پوشه در المنتور ساخته شده، `elementor-frontend` (CSS/JS)، `elementor-pro`، آیکن‌ها، Swiper المنتور و CSS پست‌ها در همه صفحات لود می‌شوند.
- المنتور به‌صورت پیش‌فرض Google Fonts (Roboto/Roboto Slab کیت پیش‌فرض) را لود می‌کند مگر در تنظیمات غیرفعال شده باشد (نیاز به تأیید در سورس صفحه).
- Font Awesome المنتور + Font Awesome 5 Pro داخل `main.css` قالب = آیکن‌فونت تکراری.

### افزونه‌ها
- **Wooslider** (`front/class-frontconfs.php`):
  - `defajaxurl()` روی `wp_head` در همه صفحات یک `<script>` inline چاپ می‌کند.
  - اگر گزینه «Global Import» روشن باشد: `woosbymdz-swiper` (نسخه دوم Swiper، ۱۳۶KB)، `woosbymdz-front` (CSS/JS) در همه صفحات.
  - `woosbymdz-cookies-js` بیرون از هوک `wp_enqueue_scripts` (در سازنده کلاس) enqueue می‌شود.
- **اشتراک‌یار**: شرط دارد (حساب کاربری، endpointها، صفحه محصول، شورت‌کد) ولی روی **همه صفحات محصول** سه فایل CSS (`frontend.css` ۳۹KB + `subscription-plans` + `content-paywall`) و JS لود می‌کند.
- **زرین‌پال**: فقط cart/checkout ✅.
- **uPanel**: enqueue فرانت کامنت شده ✅.
- **Yoast / Schema Pro**: فقط JSON-LD inline.
- **TPS**: CSS inline محصولات مرتبط در صفحه محصول.

### هسته وردپرس
- `wp-block-library` (CSS گوتنبرگ)، `classic-theme-styles`، `global-styles` inline در همه صفحات (قالب `wp-block-styles` را فعال کرده).
- ایموجی: توسط قالب (دستی) و TPS غیرفعال شده.
- `dashicons`: فقط برای کاربران لاگین (نوار مدیریت).

---

## ۳. هوک‌های سنگین (`init`، `wp_loaded`، `wp`، `template_redirect`)

| شدت | محل | توضیح |
|---|---|---|
| **زیاد** | `eshterakyar/eshterakyar.php:94` → `Eshterakyar_Database::update_tables()` روی `init` | در **هر** درخواست غیرکش (فرانت، پیشخوان، admin-ajax، Heartbeat، کران) اجرا می‌شود: `SHOW TABLES LIKE` ×۵، `DESC` ×۴، `SHOW INDEX` ×۱ (`class-database.php:186-380`). روی MySQL اشتراکی این کوئری‌های متادیتا کندتر از SELECT معمولی‌اند. |
| زیاد (نامعلوم) | `RTL-CareUnit/RTL-CareUnit.php` | بوت کد رمزگذاری‌شده در هر درخواست، `set_time_limit(120)`، فیلتر `filesystem_method`. رفتار داخلی (چک لایسنس، درخواست ریموت) قابل مشاهده نیست؛ در پیشخوان با ماژول «لاگ درخواست‌های کند» قابل اندازه‌گیری است. |
| متوسط | `themes/pooshe/functions.php:385` | `ob_start` با `preg_replace` یونیکد روی **کل HTML همه صفحات** برای مهمان‌ها (فقط برای حذف یک پیام). هزینه CPU کم ولی جریان خروجی را تا انتها نگه می‌دارد. |
| متوسط | `wp-schema-pro - 10/wpsp-blocks/classes/class-bsf-sp-helper.php:154` (`wp` اولویت ۹۹) و ثبت اسکیماها روی `init` | پردازش اسکیما در هر صفحه. |
| کم | `themes/pooshe/functions.php:15-56` (`pooshe_setup`) | دو `update_option` (`yith_system_info`، `dokan_colors`) در هر درخواست؛ چون مقدار ثابت است نوشتن واقعی انجام نمی‌شود ولی بی‌فایده است. |
| کم | `uPanel/includes/class-hd-upanel-endpoint.php:16` | `get_transient('vpt_flush')` در هر `init` (۱–۲ کوئری وقتی Object Cache نیست). |
| کم | `wooslider-bymdez/front/class-mainconfs.php:36` (`template_redirect`) | تنظیم کوکی `woocommerce_recently_viewed` در صفحه محصول (روی صفحات کش‌شده اجرا نمی‌شود). |
| کم | `themes/pooshe/includes/affiliate.php:37` | فقط با پارامتر `?ref=` (کوئری `get_users` روی usermeta). |
| کم | `zarinpal…/class-wc-gateway-zarinpal.php:90` | `auto_detect_fee_payer()` در سازنده؛ یک‌بار درخواست ریموت تا وقتی گزینه ذخیره شود. |
| — | `themes/pooshe/includes/post-views-counter.php:296` | به‌خاطر باگ ترتیب هوک اجرا نمی‌شود (بار ندارد). |

کران‌ها و اکشن‌های زمان‌بندی‌شده: بخش ۹.

---

## ۴. درخواست‌های admin-ajax و REST در فرانت‌اند

| اکشن | منبع | چه زمانی | هزینه |
|---|---|---|---|
| `pooshe_ajax_search` | `themes/pooshe/includes/ajax.php:2` | تایپ در جستجوی هدر (debounce ۱ ثانیه) | **زیاد** — بخش ۷ |
| `woosbymdz_fetchpro` / `woosbymdz_fetchpro_infinite` | `wooslider-bymdez/front/front-ajax.php:285,535` | **بعد از لود صفحه** برای هر اسلایدر (و اسکرول بی‌نهایت) | **زیاد** — هر اسلایدر یک درخواست کامل وردپرس + رندر قالب المنتور؛ نانس داخل HTML کش‌شده است و بعد از ۱۲–۲۴ ساعت منقضی می‌شود (مگر گزینه `noajaxrefchecker`) |
| `pooshe_ajax_carousel` | `ajax.php:225` | تب/صفحه‌بندی کاروسل‌های فروشگاه المنتور قالب | متوسط |
| `pooshe_quick_view` | `quick-view.php:106` | کلیک «نمایش سریع» | متوسط |
| `pooshe_ajax_add_to_cart` | `ajax.php:178` | افزودن به سبد ایجکسی قالب | لازم (ایجاد سشن) |
| `pooshe_advanced_filter` | `advanced-filters.php:171` | فیلتر پیشرفته فروشگاه | متوسط |
| `pooshe_ajax_login`، `pooshe_smart_login*`، `pooshe_like_ajax`، `pooshe_ajax_comment_vote`، `pooshe_ajax_submit_comment` | قالب | تعامل کاربر | کم |
| `eshterakyar_*` | اشتراک‌یار | حساب کاربری/دانلود | کم |
| `?wc-ajax=update_order_review`، `checkout`، `zarinpal_update_payment_method`، `get_zarinpal_fee` | ووکامرس/زرین‌پال | checkout | لازم |
| `heartbeat` | هسته | هر ۶۰ ثانیه برای کاربر لاگین در فرانت (نوار مدیریت) | متوسط برای لاگین‌ها |
| REST | `pooshe-pvc/v1` | ثبت نمی‌شود (باگ قالب) | — |

---

## ۵. وضعیت `wc-cart-fragments` و AJAX سبد

- ووکامرس ۱۱ دیگر `wc-cart-fragments` را به‌صورت سراسری enqueue نمی‌کند و قالب هم آن را خارج از cart/checkout حذف می‌کند (`includes/dequeue-assets.php:23`). پس درخواست `?wc-ajax=get_refreshed_fragments` روی صفحات عادی **اجرا نمی‌شود** ✅.
- WP Rocket پاسخ «سبد خالی» fragments را کش می‌کند (`WooCommerceSubscriber::serve_cache_empty_cart`).
- پیامد: مینی‌سبد هدر (`element-header_basket.php:130` → `woocommerce_mini_cart()`) در صفحات کش‌شده همان نسخه کش (خالی) را نشان می‌دهد؛ فقط بعد از افزودن ایجکسی، پاسخ `pooshe_ajax_add_to_cart` آن را به‌روز می‌کند. اگر شمارنده سبد بعد از رفتن به صفحه دیگر درست است، یعنی در WP Rocket کوکی‌های ووکامرس در «Never cache cookies» ثبت شده‌اند ⇐ **بعد از افزودن به سبد، کل سایت برای آن کاربر بدون کش و ۳–۴ ثانیه‌ای می‌شود** (نیاز به تأیید؛ راهکار در `docs/wp-rocket-settings.md`).
- سشن ووکامرس: در نسخه ۱۱ برای مهمان بدون سبد ساخته نمی‌شود (`class-wc-session-handler.php:553`، `destroy_session_if_empty`) ✅. منبع اصلی سشن‌های اضافه، ربات‌هایی است که لینک‌های `?add-to-cart=` را باز می‌کنند (robots.txt آن را Disallow کرده ولی ربات‌های بد رعایت نمی‌کنند).

---

## ۶. کوئری‌های سنگین / بدون محدودیت

| محل | مشکل |
|---|---|
| `themes/pooshe/includes/ajax.php:14-62` | جستجو: `LIKE` روی `post_title`/`post_excerpt`/`post_content` + `SQL_CALC_FOUND_ROWS` (بی‌استفاده)، fallback SKU با `meta_query` روی `postmeta` (به‌جای جدول `wc_product_meta_lookup`)، `pooshe_product_logo()` دوبار برای هر نتیجه |
| `wooslider-bymdez/front/front-ajax.php` | `WP_Query` محصولات + رندر قالب المنتور در هر درخواست ایجکس، بدون کش؛ گزینه `src=rand` (ORDER BY RAND) در اسلایدر محصولات مرتبط |
| `themes/pooshe/views/woocommerce/myaccount/download-subscription.php:41` | `posts_per_page => -1` (فقط حساب کاربری) |
| `themes/pooshe/includes/support-tickets.php:160` | `posts_per_page => -1` (تیکت‌های کاربر) |
| `uPanel/templates/theme-*/panel.php` | `numberposts => -1` با `meta_key => _customer_user` (همه سفارش‌های کاربر با جستجوی postmeta) |
| `wp-schema-pro - 10/classes/class-bsf-aiosrs-pro-schema.php:2936`، `lib/target-rule/…:1444` | `-1` در پیشخوان |
| `eshterakyar/includes/class-admin.php:929,1337,2069` | جستجوی `LIKE` روی `usermeta` در پیشخوان |
| `eshterakyar/includes/class-database.php` | کوئری‌های متادیتا در هر درخواست (بخش ۳) |

---

## ۷. جستجوی سایت

- **جستجوی زنده هدر** (ویجت المنتور `pooshe_header_search` → `app.js` → `pooshe_ajax_search`):
  1. هر کلید + ۱ ثانیه مکث = یک POST به `admin-ajax.php` (بدون کش، کل وردپرس + المنتور + ووکامرس بوت می‌شود).
  2. کوئری اول: جستجوی پیش‌فرض وردپرس روی ۳ ستون؛ `post_content` محصولات طولانی است و `LIKE '%…%'` روی آن full table scan روی `wp_posts` (که revisionها و پیش‌نویس‌ها را هم دارد) است.
  3. اگر نتیجه‌ای نبود: کوئری با `p=` (اگر عددی بود) و سپس کوئری SKU روی `postmeta`.
  4. برای هر نتیجه: ساخت آبجکت محصول، `get_price_html`، دو بار `pooshe_product_logo()` و thumbnail.
  5. فیلتر دسته‌بندی هدر از select2 استفاده می‌کند.
- **صفحه نتایج** (`/?s=…&post_type=product`): همان جستجوی پیش‌فرض وردپرس؛ پارامتر `pcat` فرم هدر در صفحه نتایج نادیده گرفته می‌شود (باگ قالب). WP Rocket صفحات `?s=` را کش نمی‌کند.
- **دلیل کندی**: هزینه بوت admin-ajax (۰.۵–۱.۵ ثانیه روی هاست اشتراکی) + `LIKE` روی متن کامل + نبود کش.

---

## ۸. داده autoload در `wp_options` (نیاز به تأیید با `db-audit.sql`)

سازندگان احتمالی گزینه‌های autoload حجیم:
- **AIOS**: `aio_wp_security_configs` (بزرگ)، به‌علاوه جداول لاگ خودش.
- **Elementor/Elementor Pro**: `elementor_*`، `_elementor_*`، اطلاعات کیت، `elementor_pro_license_*`.
- **Yoast (free + premium + woo)**: `wpseo`، `wpseo_titles`، `wpseo_social`، `wpseo_premium`، …
- **Schema Pro / bsf-core**: `brainstrom_products`، `brainstrom_bundled_products`، `wp-schema-pro-*`.
- **Wooslider**: حدود ۶۱ گزینه کوچک `woosbymdz_*`.
- **WP Rocket**: `wp_rocket_settings`.
- **RTL-CareUnit**: داده لایسنس/کش محصولات (رمزگذاری‌شده، نامعلوم).
- **ووکامرس**: ده‌ها گزینه، به‌علاوه `woocommerce_admin_*`.
- **هسته**: `rewrite_rules` (endpointهای زیاد قالب/اشتراک‌یار/uPanel)، `cron` (بزرگ اگر کران‌ها انباشته شده‌اند).
- **باقی‌مانده‌ها**: `wordfence*`/`wf*`، `dokan_colors` و `yith_system_info` (ساخته قالب)، افزونه‌های حذف‌شده قبلی.

---

## ۹. Action Scheduler، Cron و ترنزینت‌ها

- **Action Scheduler** (جداول `actionscheduler_*`): ووکامرس (WooCommerce Admin/Analytics: `wc-admin_import_orders`، `woocommerce_cleanup_draft_orders`، lookup tableها، Order Attribution، Remote Inbox…). پیش‌فرض نگهداری اکشن‌های complete ‏۳۰ روز و پاکسازی فقط ۲۰ ردیف در هر اجرا ⇐ انباشت در فروشگاه‌های پرسفارش.
- **WP-Cron** (ثبت‌شده در کد):
  - AIOS: هر ۱۵ دقیقه، ساعتی، روزانه، هفتگی، اسکن فایل.
  - ADC: `adbc_cron_automation`، `adbc_cron_analytics`.
  - اشتراک‌یار: سه کران روزانه.
  - المنتور: `elementor/tracker/send_event` (تله‌متری).
  - یواست، WP Rocket (پیش‌بارگذاری کش، RUCSS)، WP Mail SMTP، ووکامرس (sessions، scheduled sales، …).
  - اگر `DISABLE_WP_CRON` تعریف نشده باشد، کران‌ها روی درخواست بازدیدکننده اجرا می‌شوند (نیاز به تأیید در wp-config؛ پیشنهاد کران واقعی در `docs/hosting.md`).
- **ترنزینت‌ها**: بدون Object Cache همه در `wp_options` ذخیره می‌شوند؛ وردپرس فقط روزی یک‌بار ترنزینت‌های منقضی را پاک می‌کند. نمونه‌ها: `wc_related_*`، `wc_product_children_*`، `rocket_*`، `elementor_*`، `pooshe_pvc_*` (عملاً ساخته نمی‌شود)، `eshterakyar_*`، `vpt_flush`.

---

## ۱۰. فونت‌ها

- فونت‌ها از قالب (`dist/font/{YekanBakh|IRANYekan|iransans|Kalameh}/css/fontiran.css`) با فرمت woff2/woff/ttf/eot؛ همه `font-display: swap` دارند ✅.
- YekanBakh فقط ۳ وزن (400/600/700) تعریف کرده است.
- preload: سه وزن YekanBakh در همه صفحات با `functions.php` دستی (`pooshe_preload_yekanbakh_fonts`, اولویت ۱) → ۱۰۰KB با اولویت بالا قبل از تصویر LCP. TPS گزینه حذف این preload را دارد (پیش‌فرض خاموش).
- Font Awesome 5 Pro (Light/Regular/Solid/Brands — ‏۸۳ تا ۱۰۷KB هر woff2) داخل `main.css` **بدون** `font-display` (رفتار block). آیکن‌فونت المنتور هم جداگانه.
- هیچ Google Font از قالب لود نمی‌شود؛ المنتور ممکن است لود کند (نیاز به تأیید).

---

## ۱۱. تصاویر و المان LCP موبایل

- `themes/pooshe/includes/lazy-loading.php`: روی **همه** تصاویر (`wp_get_attachment_image_attributes`، `the_post_thumbnail`، `the_content`) اجباری `loading="lazy"` می‌گذارد ⇐ تصویر LCP هم lazy می‌شود و هیوریستیک وردپرس/WP Rocket برای تصاویر بالای صفحه از کار می‌افتد. TPS این فیلترها را برمی‌دارد ✅.
- المان LCP احتمالی:
  - **صفحه محصول**: تصویر اصلی گالری (TPS در گزارش Lighthouse مورخ ۲۰۲۶-۱۰-۱۶ تأیید کرده) — قبل از آن لوگوی هدر `Tarh99.com-logo-original.webp` (ID 14276).
  - **صفحه اصلی**: احتمالاً بنر/اسلایدر المنتور یا اسلایدر Wooslider (اسلایدر Wooslider با ایجکس بعد از لود ساخته می‌شود ⇐ اگر بالای صفحه باشد LCP بسیار دیر می‌شود).
  - **دسته‌بندی‌ها**: بسته به ارتفاع هدر/فیلترها، اولین تصویر کارت محصول یا عنوان دسته.
  - برای قطعی شدن: بخش «Largest Contentful Paint element» در PageSpeed هر صفحه (روش در `docs/images.md`).
- سایز تصاویر کارت‌ها: TPS اندازه `tps_card_420` و `sizes` واکنش‌گرا اضافه کرده.
- پوشه `smush-webp` نشان می‌دهد Smush قبلاً نصب بوده؛ اکنون افزونه تبدیل WebP فعالی وجود ندارد.

---

## ۱۲. تداخل‌های احتمالی با WP Rocket

1. **Delay JS + اسلایدرهای Swiper قالب**: قالب Swiperهای مقداردهی‌نشده را `display:none` می‌کند؛ با Delay JS محصولات مرتبط ناپدید می‌شوند (TPS 1.4 با CSS جایگزین رفع کرده).
2. **Delay/Defer JS + Wooslider**: اسکریپت inline `ajaxurl` در head و نانس داخل HTML کش‌شده؛ با Delay JS اسلایدرها تا اولین تعامل کاربر خالی می‌مانند.
3. **LazyLoad راکت + lazy اجباری قالب + lazy المنتور**: سه لایه lazy؛ تصویر LCP باید از هر سه مستثنی شود.
4. **Remove Unused CSS / Async CSS**: با `main.css` بزرگ مفیدترین گزینه است ولی کلاس‌های جاوااسکریپتی (`swiper-*`، `modal`، `show`، `dark-mode`، `select2`) باید safelist شوند.
5. **کش سبد خالی fragments** + حذف fragments توسط قالب (بخش ۵).
6. **عدم وجود بلوک rewrite کش در `.htaccess`**: راکت کش را از طریق PHP سرو می‌کند (کندتر از سرو مستقیم Apache). یا راکت روی این هاست rewrite را غیرفعال کرده یا فیلتر `rocket_htaccess_mod_rewrite` خاموش است (نیاز به تأیید).
7. **کوکی‌های Never Cache** (بخش ۵).
8. **عمر کش** پیش‌فرض ۱۰ ساعت با ۳۰۰۰ محصول ⇐ بخش زیادی از صفحات محصول اغلب کش ندارند.
9. **تکرار وظایف**: emoji/dashicons در قالب + TPS + راکت؛ preload فونت در قالب + احتمالاً راکت.
10. TPS و افزونه جدید هر دو از `rocket_lazyload_excluded_src` استفاده می‌کنند (سازگار).

---

## ۱۳. سنگینی پیشخوان

- **WooCommerce Admin**: Home/Activity panel، Inbox و Remote Inbox Notifications، Marketing، پیشنهادهای Marketplace و پرداخت، Analytics و import اکشن‌ها، tracker.
- **ویجت‌های داشبورد**: `woocommerce_dashboard_status` (کوئری فروش ماه و موجودی)، `woocommerce_dashboard_recent_reviews`، `dashboard_primary` (اخبار وردپرس — درخواست بیرونی)، `e-dashboard-overview` (المنتور — فید ریموت)، `wpseo-dashboard-overview` (یواست)، `dashboard_quick_press`.
- **درخواست‌های بیرونی در `admin_init`**: bsf-core (`bsf_update_all_product_version`)، المنتور، یواست، wordpress.org، RTL-CareUnit، WP Mail SMTP. از ایران با تأخیر/قطعی ⇐ هر بار timeout چند ثانیه‌ای.
- **Heartbeat**: هر ۱۵–۶۰ ثانیه در همه صفحات پیشخوان (هر بار یک بوت کامل با کوئری‌های اشتراک‌یار).
- **لیست محصولات/سفارش‌ها**: ستون‌های یواست، `SQL_CALC_FOUND_ROWS`، dropdown ماه‌ها، تعداد ردیف در صفحه (Screen Options) — اگر روی عدد بزرگ تنظیم شده باشد بسیار کند می‌شود.
- **اشتراک‌یار**: کوئری‌های اسکیما در هر صفحه پیشخوان.
- **ADC Pro**: اسکن‌های دیتابیس در صفحات خودش.

---

## ۱۴. مشکلات امنیتی و کد مشکوک

| شدت | مورد |
|---|---|
| **بحرانی** | لودر ionCube نسخه 15.5.1 فایل‌های رمزگذاری‌شده راست‌چین را رد می‌کند (تست‌شده؛ نسخه‌های 13.3.1، 14.0.0، 14.4.1، 15.0.0 و 15.5.0 اجرا می‌کنند). اگر هاست لودر را به‌روز کند یا با تغییر نسخه PHP لودر جدید نصب شود، قالب و ۶ افزونه از کار می‌افتند (صفحه سفید). بخش ۱۶. |
| **بحرانی** | وابستگی کامل به RTL-CareUnit: با غیرفعال شدن آن، فایل‌های `RTL_License_*.php` قالب و افزونه‌ها در هر درخواست `https://files.rtl-theme.com/plugins/rtlCareUnit.zip` را با timeout ۳۰۰ ثانیه درخواست می‌کنند و صفحات محصولات/پیشخوان به صفحه لایسنس ریدایرکت می‌شوند. |
| **بالا** | `uPanel/includes/hd_upanel_tickets.php:43-48`: `$_GET['s']` بدون escape مستقیم در SQL (`id LIKE '%$search%'`) ⇐ **SQL Injection** (فقط برای کاربران پیشخوان که به این صفحه دسترسی دارند؛ قابل سوءاستفاده از طریق CSRF/لینک). به سازنده افزونه گزارش شود. |
| **بالا** | ۲۶۴ فایل PHP در `wp-content/plugins/Addons/…` (قالب Rooten، bdthemes، meta-box) که مستقیماً از وب قابل اجرا هستند. باید حذف شوند. |
| متوسط | `elementor-pro/includes/custom-options-maker.php`: فیلتر `pre_http_request` پاسخ لایسنس را جعل می‌کند و قالب‌ها را از `rtlelem.site` با `sslverify => false` دانلود می‌کند (وابستگی به سرور شخص ثالث بدون اعتبارسنجی SSL). |
| متوسط | `RTL-CareUnit` و بخش‌هایی از قالب ionCube هستند و قابل بازبینی نیستند (به منبع رسمی راست‌چین اعتماد شده). |
| متوسط | Wordfence حذف شده ولی `wflogs` (و احتمالاً `auto_prepend_file` و جداول `wf*`) مانده است. |
| کم | `whitespace-fixer-for-xml-sitemap/wppulse`: ارسال دامنه و ایمیل ادمین به wp-turbo.com هنگام فعال/غیرفعال‌سازی. |
| کم | `themes/pooshe/includes/ajax.php`: `$_POST['s']` و `$_POST['stuck']` بدون sanitize (از طریق WP_Query امن می‌شود). |
| کم | Yoast Premium 28.4 با Yoast free 28.6 — نسخه‌ها هماهنگ نیستند. |
| کم | PHP 8.1 (پایان پشتیبانی امنیتی). |
| اطلاع | `functions.php` قالب: `woocommerce_payment_complete_order_status` همه سفارش‌های پرداخت‌شده را `completed` می‌کند (عمدی، ولی با آپدیت قالب حذف می‌شود). |

اسکن الگوهای رایج بدافزار (`eval(base64_decode`، `gzinflate`، `assert($_`، `preg_replace /e`، رشته‌های hex طولانی) در فایل‌های PHP غیررمزگذاری‌شده نتیجه‌ای نداشت.

---

## ۱۶. نتایج تست روی نسخه محلی سایت (بعد از نوشتن افزونه)

برای اطمینان، یک کپی محلی با وردپرس 7.1.3، ووکامرس 11.2.1، قالب پوشه و همه افزونه‌های همین مخزن (به‌جز AIOS) روی MariaDB 10.11 و PHP 8.3 راه‌اندازی و تست شد:

| مورد | نتیجه |
|---|---|
| کوئری‌های `SHOW TABLES/DESC/SHOW INDEX` اشتراک‌یار در هر درخواست | ۹ → ۰ (کل کوئری‌های صفحه اصلی ۱۸۷ → ۱۷۶) |
| جستجوی زنده (admin-ajax) بار دوم برای مهمان | ~۱.۴ ثانیه → ~۰.۲ ثانیه (پاسخ کش‌شده قبل از بارگذاری افزونه‌ها) |
| یکسان بودن خروجی جستجو با حالت بدون افزونه | ✅ بایت‌به‌بایت یکسان (با تنظیمات پیش‌فرض) |
| باطل شدن کش با تغییر قیمت محصول | ✅ |
| درخواست‌های دارای نانس نامعتبر یا ترتیب تصادفی | ✅ کش نمی‌شوند |
| ربات با `?add-to-cart=` | ✅ سشن/کوکی ساخته نمی‌شود (مرورگر عادی: ساخته می‌شود) |
| پاکسازی روزانه | ✅ فقط داده منقضی حذف شد (ترنزینت معتبر، سشن معتبر و ۱۰ revision آخر ماندند) |
| `db-audit.sql` و `db-cleanup.sql` | ✅ بدون خطا روی MariaDB 10.11 |
| صفحات فرانت، پیشخوان، تنظیمات افزونه، ذخیره تنظیمات | ✅ بدون Fatal/Warning از افزونه |
| بای‌پس `?tarh99_perf=off` (فقط مدیر)، `TARH99_PERF_SAFE_MODE`، ثابت‌های ماژول | ✅ |
| ionCube Loader 15.5.1 | ❌ همه فایل‌های رمزگذاری‌شده راست‌چین رد شدند |
| ionCube Loader 15.5.0 / 15.0.0 / 14.x / 13.x | ✅ اجرا شدند |
| غیرفعال کردن RTL-CareUnit | ❌ هر درخواست تلاش به دانلود zip با timeout ۳۰۰ ثانیه (ماژول `admin_http` با «محافظ فرانت‌اند» جلوی قفل شدن صفحات بازدیدکننده را می‌گیرد) |

یافته جانبی: قابلیت «Optimized Image Loading» المنتور (پیش‌فرض روشن) خودش روی اولین تصویر بزرگ `fetchpriority="high"` می‌گذارد و اگر افزونه دیگری (مثل TPS فعلی) هم همین را بگذارد ویژگی تکراری ساخته می‌شود؛ ماژول `lcp` این حالت را تشخیص می‌دهد.

---

## ۱۵. فهرست اولویت‌بندی‌شده

| اولویت | اقدام | تأثیر | ریسک | سختی | پیاده‌سازی |
|---|---|---|---|---|---|
| 0 | **جلوگیری از به‌روزرسانی ionCube Loader به 15.5.1+ و هرگز غیرفعال نکردن RTL-CareUnit** (هماهنگی با پشتیبانی هاست و راست‌چین) | **بحرانی** (پایداری) | — | کم | `docs/hosting.md` |
| 1 | حذف lazy از تصویر LCP + `fetchpriority=high` (محصول، اولین تصاویر آرشیو، تصویر دلخواه صفحه اصلی) | **زیاد** (LCP) | کم | کم | ماژول `lcp` |
| 2 | کاهش preload فونت از ۳ به ۲ وزن و جلوگیری از تکرار با راکت | متوسط (LCP/FCP) | کم | کم | ماژول `fonts` |
| 3 | فعال‌سازی Remove Unused CSS (یا Load CSS Async) در WP Rocket با safelist | **زیاد** (FCP/LCP) | متوسط | متوسط | تنظیمات راکت (`docs/wp-rocket-settings.md`) |
| 4 | کش پاسخ‌های ایجکس عمومی (اسلایدرهای Wooslider، کاروسل، Quick View، جستجو) | **زیاد** (TTFB، بار سرور، «کندی بعد از چند صفحه») | کم | متوسط | ماژول `ajax_cache` |
| 5 | بهینه‌سازی جستجو (ستون‌های محدود، SKU از lookup table) | **زیاد** برای جستجو | متوسط (نتایج کمی تغییر می‌کند اگر متن کامل حذف شود) | متوسط | ماژول `search` |
| 6 | محدودکردن کوئری‌های اسکیمای اشتراک‌یار به هر ۱۲ ساعت | **زیاد** برای لاگین/سبد/پیشخوان | کم | کم | ماژول `queries` |
| 7 | Redis Object Cache (اگر هاست ارائه کند) | **زیاد** برای لاگین/سبد/پیشخوان | کم | کم | هاست + `docs/hosting.md` + ماژول `monitor` |
| 8 | PHP 8.3 + OPcache + افزایش PHP workers / بررسی LVE | **زیاد** | متوسط (سازگاری ionCube) | کم | پنل هاست |
| 9 | تنظیمات کش راکت: عمر کش، پیش‌بارگذاری، کوکی‌ها | **زیاد** برای «کندی بعد از چند محصول» | کم | کم | `docs/wp-rocket-settings.md` |
| 10 | Heartbeat (فرانت خاموش، پیشخوان ۱۲۰ ثانیه) | متوسط (لاگین/پیشخوان) | کم | کم | ماژول `heartbeat` |
| 11 | سبک‌سازی WooCommerce Admin و ویجت‌های داشبورد | متوسط (پیشخوان) | کم | کم | ماژول `wc_admin` |
| 12 | لاگ و محدودیت زمان درخواست‌های بیرونی پیشخوان | متوسط (پیشخوان) | کم | کم | ماژول `admin_http` |
| 13 | پاکسازی زمان‌بندی‌شده (ترنزینت، سشن، Action Scheduler، revision) + محدودیت revision | متوسط (پایداری بلندمدت) | کم | کم | ماژول‌های `cleanup` و `revisions` |
| 14 | مانیتورینگ autoload/ترنزینت/سشن/AS | متوسط (پایداری) | ندارد | کم | ماژول `monitor` |
| 15 | جلوگیری از ساخت سشن توسط ربات‌ها | کم | کم | کم | ماژول `wc_session` |
| 16 | حذف موارد اضافی head (embed، generator، …) | کم | کم | کم | ماژول `head_cleanup` |
| 17 | قوانین لود شرطی CSS/JS بر اساس اسکن | متوسط | متوسط | متوسط | ماژول `asset_rules` (قوانین بعد از اسکن واقعی) |
| 18 | defer اسکریپت‌های غیرضروری وقتی راکت این کار را نمی‌کند | کم | کم | کم | ماژول `defer_js` |
| 19 | انتقال تغییرات دستی functions.php به افزونه | پایداری | کم | کم | ماژول `pooshe_compat` + راهنما |
| 20 | WebP/AVIF و ابعاد درست تصاویر | متوسط | کم | متوسط | `docs/images.md` |
| 21 | حذف پوشه‌های اضافی plugins، باقی‌مانده Wordfence، RTL-CareUnit قدیمی | امنیت/نظم | کم | کم | دستی (`docs/deploy.md`) |
| 22 | گزارش SQL Injection به سازنده uPanel | امنیت | — | — | دستی |
