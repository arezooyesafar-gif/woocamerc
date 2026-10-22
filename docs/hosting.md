# تنظیمات پنل هاست (لیمو هاست — cPanel) (مرحله ۴)

## ⛔ ۰. قبل از هر تغییری: ionCube Loader
قالب پوشه و WP Rocket، المنتور پرو، یواست پرمیوم، uPanel، Wooslider و اشتراک‌یار (نسخه‌های راست‌چین) فایل‌های رمزگذاری‌شده ionCube دارند. در تست:
- **ionCube Loader 15.5.1** (آخرین نسخه) همه این فایل‌ها را با خطای `was created with an unauthorised copy of the ionCube PHP Encoder` رد کرد ⇐ **صفحه سفید در کل سایت**.
- نسخه‌های 13.3.1، 14.0.0، 14.4.1، 15.0.0 و **15.5.0** آن‌ها را اجرا کردند.

اقدام:
1. از پشتیبانی لیمو هاست بپرسید نسخه فعلی ionCube Loader چیست و **درخواست کنید لودر به 15.5.1 یا بالاتر به‌روز نشود** تا راست‌چین فایل‌ها را دوباره رمزگذاری کند.
2. موضوع را به پشتیبانی راست‌چین (rtl-theme.com) هم گزارش کنید.
3. هر تغییر نسخه PHP (که معمولاً لودر ionCube همان نسخه را هم نصب می‌کند) اول روی یک ساب‌دامین/استیجینگ تست شود.
4. افزونه **RTL-CareUnit را هرگز غیرفعال یا حذف نکنید و از بارگذاری آن در صفحات سایت هم جلوگیری نکنید** (تست ۱۴۰۵/۰۷/۳۰ روی سایت زنده: بدون آن المنتور پرو و هدر قالب کار نمی‌کنند). طبق ماژول profiler این افزونه در هر ساخت صفحه ۱.۳ تا ۲.۳ ثانیه زمان می‌گیرد؛ رفعش فقط از طرف پشتیبانی راست‌چین ممکن است: بدون آن، فایل‌های لایسنس در هر بازدید تلاش می‌کنند `rtlCareUnit.zip` را با timeout ۳۰۰ ثانیه دانلود کنند (ماژول `admin_http` جلوی منتظر ماندن بازدیدکننده را می‌گیرد، ولی پیشخوان همچنان به صفحه لایسنس ریدایرکت می‌شود).
   - **نتیجه ابزار «عیب‌یابی کندی RTL-CareUnit» روی هاست (۱۴۰۵/۰۷/۳۰، PHP 8.3.35، ionCube 15.5، OPcache 99.6%):** اتصال به `www.rtl-theme.com` و `files.rtl-theme.com` حدود ۴۰ میلی‌ثانیه (شبکه و DNS هاست مشکلی ندارد؛ `api.rtl-theme.com` اصلاً resolve نمی‌شود). یک رمزگشایی لایسنس (PBKDF2-SHA256 با ۱۰۰ هزار تکرار، همان کتابخانه defuse داخل CareUnit) روی پردازنده هاست **۲۳۶ میلی‌ثانیه** طول می‌کشد. ۹ محصول دارای لایسنس راست‌چین روی سایت است (قالب پوشه، WP Rocket، Elementor Pro، اشتراک‌یار، Yoast Premium، Yoast WooCommerce SEO، uPanel، Wooslider، Advanced Database Cleaner Premium)؛ ۹ × ۲۳۶ ≈ ۲.۱ ثانیه که با عدد profiler (۱.۳ تا ۲.۳ ثانیه) می‌خواند. یعنی کندی از طراحی سیستم لایسنس (رمزگشایی سنگین در هر درخواست بدون کش) ضربدر سرعت پردازنده هاست است و از داخل سایت قابل رفع نیست؛ راه‌حل: کش کردن نتیجه بررسی لایسنس توسط راست‌چین، یا هاست با پردازنده تک‌هسته‌ای سریع‌تر.

## ۱. نسخه PHP
- فعلی: **8.1** (پشتیبانی امنیتی تمام شده).
- پیشنهاد: **8.3** (یا 8.2) — ۱۰ تا ۱۵٪ سریع‌تر. ووکامرس ۱۱، المنتور ۴ و وردپرس فعلی پشتیبانی می‌کنند.
- شرط: بند ۰ (نسخه ionCube برای PHP جدید ≤ 15.5.0) و تست روی استیجینگ: صفحه اصلی، محصول، سبد، پرداخت زرین‌پال (حالت sandbox)، ورود پیامکی، دانلود اشتراک، پنل uPanel.
- مسیر: cPanel → **Select PHP Version** یا **MultiPHP Manager**.

## ۲. OPcache
cPanel → Select PHP Version → Options (یا MultiPHP INI Editor):
```
opcache.enable = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 20000
opcache.validate_timestamps = 1
opcache.revalidate_freq = 60
opcache.save_comments = 1
```
بعد از تنظیم، در پیشخوان → سرعت طرح۹۹ → مانیتورینگ، وضعیت OPcache و hit rate (باید بالای ۹۵٪ باشد) دیده می‌شود.

## ۳. Object Cache (Redis یا Memcached) — بیشترین اثر برای لاگین، سبد، پرداخت و پیشخوان
1. از پشتیبانی بپرسید پلن ووکامرس Redis یا Memcached (یا LSMCD روی LiteSpeed) دارد یا نه، و آدرس/پورت/سوکت آن را بگیرید.
2. افزونه **Redis Object Cache** (از Till Krüss) را نصب کنید.
3. این خطوط را **خودتان** در `wp-config.php` بالای خط `/* That's all, stop editing! */` اضافه کنید (مقادیر را از هاست بگیرید):
   ```php
   define( 'WP_REDIS_HOST', '127.0.0.1' );
   define( 'WP_REDIS_PORT', 6379 );
   define( 'WP_REDIS_DATABASE', 0 );
   define( 'WP_REDIS_PREFIX', 'tarh99_' );
   define( 'WP_CACHE_KEY_SALT', 'tarh99_' );
   ```
   اگر هاست سوکت داده: `define( 'WP_REDIS_SCHEME', 'unix' ); define( 'WP_REDIS_PATH', '/home/USER/redis.sock' );`
4. تنظیمات → Redis → Enable Object Cache. در تب مانیتورینگ افزونه ما «Object Cache: فعال» دیده می‌شود.
5. افزونه tarh99-performance خودکار کش ایجکس را به‌جای فایل در Redis نگه می‌دارد.

### اگر Redis/Memcached در دسترس نیست
افزونه tarh99-performance بدون Object Cache هم کامل کار می‌کند (کش ایجکس را در فایل نگه می‌دارد). جایگزین‌ها به ترتیب:
1. **Memcached یا LSMCD:** از پشتیبانی بپرسید؛ بعضی پلن‌هایی که Redis ندارند این‌ها را دارند. اگر وب‌سرور LiteSpeed است، LSMCD معمولاً در دسترس است.
2. **Docket Cache** (افزونه رایگان مخزن وردپرس): Object Cache پایدار روی فایل که از OPcache استفاده می‌کند و به سرویس جداگانه نیاز ندارد. پیش‌نیاز آن OPcache فعال است (بند ۲).
   - نصب در ساعت کم‌ترافیک ← Docket Cache → Overview → **Enable Object Cache**.
   - گزینه‌های «Advanced Post Caching» و «Object Cache Precaching» را روشن و «Optimize WP Query / Admin» را خاموش بگذارید تا رفتار سایت تغییر نکند.
   - تست: سبد، پرداخت، حالت لاگین و پیشخوان. اگر خطایی بود، Disable Object Cache بزنید؛ با حذف فایل `wp-content/object-cache.php` هم به حالت قبل برمی‌گردد.
   - روی هاست با دیسک کند یا سهمیه inode کم، اثرش کمتر است؛ بعد از یک روز در cPanel → Resource Usage بررسی کنید.
3. **بدون Object Cache:** پاکسازی منظم autoload، ترنزینت‌ها و سشن‌ها (ماژول `cleanup` و فایل `db-cleanup.sql`) مهم‌تر می‌شود، چون هر درخواست غیرکش این داده‌ها را مستقیم از دیتابیس می‌خواند.

## ۴. حافظه و زمان اجرا
```
memory_limit = 512M
max_execution_time = 120
max_input_vars = 5000
post_max_size = 128M
upload_max_filesize = 128M
```
و در `wp-config.php` (خودتان):
```php
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );
define( 'WP_POST_REVISIONS', 5 );
define( 'EMPTY_TRASH_DAYS', 14 );
```

## ۵. منابع CloudLinux (LVE) — علت احتمالی «کند شدن ناگهانی بعد از چند صفحه»
cPanel → **Resource Usage**:
- اگر در ساعات اوج «Faults» برای **Entry Processes (EP)** یا **CPU** یا **Physical Memory** ثبت شده، یعنی درخواست‌ها صف می‌شوند (همان پرش از ۱ به ۴ ثانیه).
- از هاست بخواهید: EP حداقل ۴۰، CPU حداقل ۲۰۰٪ (۲ هسته)، RAM حداقل ۲–۴ گیگ، PHP workers/NPROC بالاتر — یا ارتقا به پلن بالاتر ووکامرس.
- ماژول `ajax_cache` تعداد درخواست‌های PHP هر بازدید را کم می‌کند (اسلایدرهای Wooslider، جستجو)، که مستقیماً فشار روی EP را پایین می‌آورد.

## ۶. کران واقعی به‌جای WP-Cron
1. در `wp-config.php` (خودتان): `define( 'DISABLE_WP_CRON', true );`
2. cPanel → **Cron Jobs** → هر ۵ دقیقه:
   ```
   */5 * * * * /usr/local/bin/php /home/USER/public_html/wp-cron.php >/dev/null 2>&1
   ```
   (مسیر PHP و پوشه را از پشتیبانی/File Manager بگیرید؛ یا: `wget -q -O - https://tarh99.com/wp-cron.php?doing_wp_cron >/dev/null 2>&1`)
3. نتیجه: هیچ بازدیدکننده‌ای منتظر اجرای کران‌ها (AIOS هر ۱۵ دقیقه، Action Scheduler ووکامرس، اشتراک‌یار، یواست…) نمی‌ماند.

## ۷. باقی‌مانده Wordfence
1. cPanel → File Manager → ریشه سایت: فایل `.user.ini` (و `php.ini` اگر هست) را باز کنید؛ اگر خطی شبیه `auto_prepend_file = '/home/.../wordfence-waf.php'` دارد و افزونه Wordfence نصب نیست، آن خط را حذف کنید (یا از هاست بخواهید).
2. پوشه `wp-content/wflogs` را حذف کنید.
3. جداول `wp_wf*` را بعد از دیدن نتیجه کوئری `40_wordfence_leftover_tables` حذف می‌کنیم.

## ۸. سایر موارد
- **MySQL/MariaDB**: نسخه MariaDB 10.6+ یا MySQL 8؛ موتور همه جداول InnoDB (کوئری `21_db_total`).
- **HTTP/2 یا HTTP/3** و **Brotli**: از هاست بپرسید؛ اگر LiteSpeed است، HTTP/3 و Brotli معمولاً فعال‌اند (بلوک `.htaccess` پیشنهادی Brotli را در Apache فعال می‌کند).
- **SSL/TLS 1.3** فعال باشد.
- **دسترسی شبکه سرور به خارج**: تب «ابزارها و لاگ‌ها» افزونه درخواست‌های بیرونی کند را با نام دامنه نشان می‌دهد؛ اگر دامنه‌ای همیشه کند یا خطادار بود، در ماژول `admin_http` آن را در «دامنه‌های مسدود در پیشخوان» بگذارید یا سقف زمان انتظار ۵ ثانیه تعیین کنید.
- **بکاپ خودکار روزانه** هاست (JetBackup) را فعال و یک‌بار بازگردانی آزمایشی را تمرین کنید.
