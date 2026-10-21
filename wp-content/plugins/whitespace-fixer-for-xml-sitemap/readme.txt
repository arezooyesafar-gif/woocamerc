=== Whitespace Fixer for XML Sitemap ===  
Contributors: TurboAddons, wpanik, sharifok, siraji2017
Tags: xml, whitespace, error, rss feed error, sitemap
Requires at least: 5.2 
Tested up to: 7.1
Requires PHP: 7.2
Stable tag: 1.1.1
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html 

Fixes XML declaration errors in sitemaps and RSS by removing leading whitespace. Just activate, no setup required.

== Description ==

**Whitespace Fixer for XML Sitemap** is a lightweight plugin that automatically removes unwanted spaces or blank lines before the XML declaration (`<?xml version="1.0" ... ?>`) in your WordPress output.

https://youtu.be/CvXXCGxMTqk?feature=shared

This small whitespace, often caused by themes or plugins, breaks XML feeds and causes browser or validator errors like:

XML declaration allowed only at the start of the document

With this plugin activated, the output buffer is cleaned before anything is sent to the browser—ensuring your sitemaps and RSS feeds are valid and readable by search engines like Google.

✅ Works with:
- Rank Math
- Yoast SEO
- Other Seo Tools
- All themes and plugins

* [Live Demo](https://wp-turbo.com/whitespace-fixer-for-xml-sitemap/)

**No settings page. No configuration. Just activate and forget.**

== Features ==

* Automatically removes leading whitespace from XML output
* Fixes "XML declaration not at start" errors
* Works with Rank Math, Yoast, and other sitemap plugins
* Compatible with any WordPress theme or hosting
* Runs silently in the background

== Screenshots ==

1. Browser error message without the plugin (before fix)

2. Browser error message without the plugin (before fix)

3. XML sitemap loads cleanly after plugin activation (no errors)

== Installation ==

1. Upload the plugin to the `/wp-content/plugins/` directory, or install it via the WordPress Plugin Installer.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. That’s it! Your sitemaps and feeds will now be clean and error-free.

== Frequently Asked Questions ==

= Does this plugin modify any content or database? =
No. It only buffers and cleans output whitespace before it reaches the browser. It doesn’t store or alter data.

= Will this conflict with my SEO plugin? =
Not at all. It is compatible with Rank Math, Yoast SEO, All in One SEO, SEOPress, and others.

= What if I still see the error after activation? =
Ensure no theme or plugin is sending output (like `echo`, `var_dump()`, or spaces) before the plugin loads. Also, clear any caching and test again.

= Does it have any settings page? =
No. It's designed to be automatic. Activate and you’re done.

== Changelog ==

= 1.1.1 =
* Fixed leading whitespace removal to target only the start of the document instead of every line.
* Improved content-type detection so only XML, HTML, and feed responses are processed.
* Fixed the plugin text domain to match the plugin slug for better translation support.
* Limited output buffering to sitemap and feed requests for improved performance.
* Enabled SSL verification for analytics requests and removed leftover debug logging.
* Cached the support popup check to reduce admin page load overhead.

= 1.1.0 =
* Added a new status page under Tools → XML Whitespace Fixer.
* Display plugin, WordPress, and PHP version information.
* Display XML sitemap URL for quick access.

= 1.0.5 =
* Added more security and optimized the plugin structure.

= 1.0.4 =
* Improved performance and update security.

= 1.0.3 =
* Tested up to WordPress 6.9 to ensure full compatibility.
* Updated internal hooks for better stability with the latest WordPress core changes.

= 1.0.2 =
* Improved performance by reducing unnecessary output buffering.
* Refactored core cleanup logic for cleaner and more reliable XML output.

= 1.0.1 =
* Improved output sanitization to enhance overall security and prevent unwanted data leakage.
* Added stricter header validation before applying whitespace cleanup for safer XML response handling.
* Optimized output buffer handling to ensure better compatibility with caching and SEO plugins.
* Improved performance and reduced risk of accidental whitespace removal in non-XML outputs.

= 1.0.0 =
* Initial release
* Automatically removes whitespace before XML declarations

## License
Whitespace Fixer for XML Sitemap is licensed under the [GNU General Public License v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).