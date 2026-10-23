# تنظیمات پیشنهادی WP Rocket برای tarh99.com (مرحله ۴)

> قبل از هر تغییر: WP Rocket → Tools → **Export settings** (فایل JSON را نگه دارید تا در صورت مشکل با Import برگردید).
> بعد از هر بخش: Clear cache → سایت را در پنجره ناشناس (مهمان) و روی موبایل بررسی کنید → سپس بخش بعد.
> وضعیت فعلی تنظیمات شما را کوئری `45_rocket_settings` فایل `db-audit.sql` نشان می‌دهد؛ با ارسال خروجی آن، این فهرست را دقیق‌تر می‌کنم.

## ۱. Cache
| گزینه | مقدار | دلیل |
|---|---|---|
| Enable caching for mobile devices | ✅ روشن | — |
| Separate cache files for mobile devices | (در نسخه نصب‌شده گزینه‌ای در تنظیمات ندارد) | WP Rocket این گزینه را مخفی و همیشه روشن نگه می‌دارد؛ تغییرش لازم نیست |
| Enable caching for logged-in WordPress users | ❌ خاموش | هدر، سبد، نام کاربر و پنل کاربری uPanel شخصی‌اند |
| Cache Lifespan | **10 ساعت** (پیش‌فرض)؛ **۷۲ ساعت فقط بعد از روشن کردن ماژول `cache_stability`** | صفحات کش‌شده نانس‌های ووکامرس، المنتور پرو، Wooslider و اشتراک‌یار دارند که برای مهمان بعد از ۱۲–۲۴ ساعت منقضی می‌شوند؛ عمر بیشتر بدون آن ماژول = خطای اسلایدرها، فرم ورود و افزودن به سبد روی صفحات قدیمی. ماژول `cache_stability` اعتبار nonce فقط برای مهمان‌ها را ۷ روز می‌کند (nonce مهمان برای همه بازدیدکننده‌ها یکسان است و ارزش امنیتی ندارد)، پس عمر کش تا ۸۴ ساعت امن است. با عمر ۱۰ ساعته، راکت هر ۱۰ ساعت همه صفحات را دوباره می‌سازد (هر صفحه ۴ تا ۵ ثانیه روی این هاست) و محصولاتی که هنوز ساخته نشده‌اند برای اولین بازدیدکننده کند باز می‌شوند. |

## ۲. File Optimization
| گزینه | مقدار | توضیح |
|---|---|---|
| Minify CSS files | ✅ | — |
| Optimize CSS delivery (بهینه‌سازی تحویل CSS) | ❌ **خاموش** | Remove Unused CSS سرویس ابری است و نسخه نصب‌شده آن را به `wpsaas.gpltimes.com` می‌فرستد. روی سایت هیچ صفحه‌ای `completed` نشد (خطای 400 و «We could not authenticate»)، صف آن بیش از ۸۰۰۰ ردیف شد و صفحات در انتظار از پیش‌بارگذاری قفل می‌شدند. Load CSS asynchronously هم به سرویس رسمی Critical CSS و لایسنس اصلی وابسته است و بدون آن صفحه لحظه‌ای بدون استایل دیده می‌شود. جایگزین: ماژول `css_usage` افزونه tarh99-performance (`docs/css-usage.md`). وضعیت صف: `SELECT status, COUNT(*) FROM wp_wpr_rucss_used_css GROUP BY status;` |
| CSS Safelist (برای RUCSS) | مقادیر زیر | کلاس‌هایی که با جاوااسکریپت اضافه می‌شوند و نباید حذف شوند |
| Minify JavaScript files | ✅ | — |
| Combine JavaScript files | ❌ | با HTTP/2 مفید نیست و ریسک خرابی دارد |
| Load JavaScript deferred | ✅ | قالب اسکریپت‌های خودش را از قبل defer کرده است. jQuery را در «Excluded JavaScript Files» نگذارید مگر خطا دیدید؛ اگر اسکریپت inline خطای `jQuery is not defined` داد، گزینه **Safe Mode for jQuery** را روشن کنید. |
| **Delay JavaScript execution** | ✅ فقط با استثناهای زیر | TBT سایت سبز است؛ هدف Delay فقط اسکریپت‌های جانبی است. بدون این استثناها اسلایدرهای Swiper بالای صفحه تا اولین حرکت کاربر مخفی می‌مانند (قالب Swiper راه‌اندازی‌نشده را `display:none` می‌کند)، جستجوی زنده و منوها تا اولین لمس کار نمی‌کنند و اسلایدرهای Wooslider خالی می‌مانند. |

**CSS Safelist (فقط اگر روزی Remove Unused CSS با لایسنس معتبر روشن شد):**
```
.swiper(.*)
.modal(.*)
.show
.fade
.collapse(.*)
.select2(.*)
.dark-mode(.*)
.header-search(.*)
.searching
.typing
.loaded
.not-loaded
.csb(.*)
.ps-(.*)
.cart-item-clone
.added_to_cart
.woocommerce-message
.woocommerce-error
.woocommerce-info
.pooshe-(.*)
#poosheQuickViewModal(.*)
.woosbymdz(.*)
.e-con(.*)
.elementor-(.*)
.is-active
.active
.open
.sticky(.*)
```

**Delay JavaScript — Excluded JavaScript Files:**
```
/jquery-?[0-9.](.*)(.min|.slim|.slim.min)?.js
/jquery-migrate(.min)?.js
/wp-content/themes/pooshe/dist/js/swiper.min.js
/wp-content/themes/pooshe/dist/js/app.js
WP_OBJ
/wp-content/themes/pooshe/dist/js/dark-mode.js
POOSHE_DARK_MODE
/wp-content/plugins/elementor/assets/js/
/wp-content/plugins/elementor-pro/assets/js/
elementorFrontendConfig
ElementorProFrontendConfig
/wp-content/plugins/wooslider-bymdez/front/js/
woosbymdz
ajaxurl
/wp-content/plugins/woocommerce/assets/js/frontend/add-to-cart(.*).js
/wp-content/plugins/woocommerce/assets/js/frontend/single-product(.*).js
/wp-content/plugins/woocommerce/assets/js/flexslider/
/wp-content/plugins/woocommerce/assets/js/zoom/
/wp-content/plugins/woocommerce/assets/js/photoswipe/
wc_single_product_params
wc_add_to_cart_params
```
افزونه قبلی TPS و ماژول `pooshe_compat` افزونه جدید یک CSS جایگزین برای محصولات مرتبط دارند، ولی با این استثناها اصلاً نیازی به آن نیست.
بعد از روشن کردن Delay JS این‌ها را حتماً تست کنید: اسلایدر صفحه اصلی، جستجوی زنده هدر، منوی موبایل، حالت تاریک، افزودن به سبد، گالری محصول، نمایش سریع، اسلایدرهای Wooslider، ورود پیامکی.

## ۳. Media
| گزینه | مقدار | توضیح |
|---|---|---|
| LazyLoad → Enable for images | ✅ | ماژول `lcp` تصویر اصلی محصول و چند تصویر اول آرشیو را eager می‌کند و راکت `loading="eager"` را خودکار مستثنی می‌کند |
| Enable for CSS background images | ✅ | — |
| Enable for iframes and videos | ✅ | — |
| Replace YouTube iframe with preview image | ✅ | — |
| Excluded images or iframes | `Tarh99.com-logo-original` و `skip-lazy` و `data-tarh99-lcp` | لوگو و تصاویر LCP دستی (ماژول `lcp` این‌ها را با فیلتر هم اضافه می‌کند؛ نوشتنشان اینجا هم بی‌ضرر است) |
| Add missing image dimensions | ✅ | جلوگیری از CLS |
| Fonts → Preload fonts | **خالی بگذارید** | ماژول `fonts` افزونه جدید فونت فعال قالب (Regular و Bold) را preload می‌کند. اگر ترجیح می‌دهید راکت این کار را بکند، همان دو مسیر را اینجا وارد کنید؛ افزونه تکراری‌ها را تشخیص می‌دهد و دوباره چاپ نمی‌کند. |
| Self-host Google Fonts | ✅ | اگر المنتور Google Fonts لود کند، از سرور خودتان سرو می‌شود (دسترسی از ایران مطمئن‌تر است) |

## ۴. Preload
| گزینه | مقدار | توضیح |
|---|---|---|
| Activate Preloading | ✅ | با ۳۰۰۰ محصول، بدون پیش‌بارگذاری بیشتر صفحات محصول برای اولین بازدیدکننده بعد از هر پاکسازی کش ۳–۴ ثانیه‌ای هستند (یکی از دلایل «کند شدن بعد از چند محصول») |
| Exclude URLs | `/cart/(.*)`، `/checkout/(.*)`، `/my-account/(.*)` | راکت خودش هم این صفحات را مستثنی می‌کند |
| Enable link preloading | ✅ | لینک‌ها هنگام hover/لمس از قبل بارگذاری می‌شوند |
| Prefetch DNS requests | فقط اگر سرویس بیرونی دارید (مثلاً `//www.google-analytics.com`) | — |

## ۵. Advanced Rules
| گزینه | مقدار | توضیح |
|---|---|---|
| Never Cache URL(s) | صفحات حساب کاربری uPanel، صفحه دانلود/اشتراک (`/eshterakyar(.*)`)، صفحه پرداخت زرین‌پال (`/wc-api/(.*)`) | سبد/تسویه/حساب را راکت خودش مستثنی می‌کند |
| **Never Cache Cookies** | بررسی کنید `woocommerce_items_in_cart`، `woocommerce_cart_hash` یا `wp_woocommerce_session_` اینجا **نباشد** | اگر باشد، هر کسی که یک محصول به سبد اضافه کند تا ۴۸ ساعت کل سایت را بدون کش (۳–۴ ثانیه) می‌بیند. **مهم:** قالب پوشه `wc-cart-fragments` را حذف کرده، پس با حذف این کوکی‌ها مینی‌سبد هدر روی صفحات کش‌شده خالی نشان داده می‌شود تا کاربر دوباره چیزی اضافه کند. تصمیم بین «سرعت» و «شمارنده دقیق سبد روی همه صفحات» با شماست؛ اگر شمارنده دقیق مهم است، در ماژول `cart_fragments` گزینه «بارگذاری cart-fragments در همه صفحات» را روشن کنید (صفحه از کش سرو می‌شود و فقط یک درخواست ایجکس سبک برای به‌روز کردن مینی‌سبد ارسال می‌شود؛ پاسخ آن برای سبد خالی هم توسط راکت کش می‌شود). |
| Never Cache User Agent(s) | خالی | — |
| Always Purge URL(s) | صفحه اصلی `/` | بعد از ویرایش هر محصول |
| Cache Query String(s) | خالی | — |

## ۶. Database
همه گزینه‌های پاکسازی خودکار راکت را **خاموش** کنید؛ ماژول `cleanup` افزونه جدید همین کارها را دسته‌ای و با سقف زمان انجام می‌دهد (اجرای همزمان دو پاکسازی روی دیتابیس بزرگ باعث قفل جداول می‌شود). افزونه Advanced Database Cleaner را هم فقط برای پاکسازی‌های دستی نگه دارید و اتوماسیون‌هایش را خاموش کنید.

## ۷. Heartbeat
**Control Heartbeat** را خاموش کنید؛ ماژول `heartbeat` افزونه جدید این کار را انجام می‌دهد (فرانت خاموش، پیشخوان ۱۲۰ ثانیه، ویرایشگر ۶۰ ثانیه). اگر ترجیح می‌دهید راکت انجام دهد، ماژول `heartbeat` را خاموش کنید؛ هر دو با هم لازم نیست.

## ۸. CDN / Add-ons
- اگر Cloudflare یا CDN ایرانی (ابرآروان) دارید، افزودنی مخصوص آن را فعال کنید و در CDN کش HTML را خاموش بگذارید (راکت کش HTML را مدیریت می‌کند).
- Varnish: خاموش (هاست اشتراکی).

## ۹. تست نهایی
1. Clear cache → صبر تا preload چند صفحه انجام شود.
2. PageSpeed موبایل: صفحه اصلی، یک دسته، یک محصول.
3. در بخش «Largest Contentful Paint element» گزارش، نام فایل تصویر LCP صفحه اصلی را در ماژول `lcp` → «تصاویر LCP دلخواه» وارد کنید (مثال: `front | banner-mobile.webp`).
