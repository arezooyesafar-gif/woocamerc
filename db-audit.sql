SELECT '00_prefix_check' AS q, TABLE_NAME AS options_table
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE '%\_options';

SELECT '01_server' AS q, VERSION() AS mysql_version, @@innodb_buffer_pool_size / 1024 / 1024 AS buffer_pool_mb, @@max_allowed_packet / 1024 / 1024 AS max_packet_mb, @@character_set_database AS charset, @@collation_database AS collation_db;

SELECT '02_autoload_total' AS q, COUNT(*) AS rows_count, ROUND(SUM(LENGTH(option_value)) / 1024, 1) AS size_kb
FROM wp_options
WHERE autoload IN ('yes', 'on', 'auto-on', 'auto');

SELECT '03_autoload_by_flag' AS q, autoload, COUNT(*) AS rows_count, ROUND(SUM(LENGTH(option_value)) / 1024, 1) AS size_kb
FROM wp_options
GROUP BY autoload
ORDER BY size_kb DESC;

SELECT '04_autoload_top30' AS q, option_name, autoload, ROUND(LENGTH(option_value) / 1024, 1) AS size_kb
FROM wp_options
WHERE autoload IN ('yes', 'on', 'auto-on', 'auto')
ORDER BY LENGTH(option_value) DESC
LIMIT 30;

SELECT '05_autoload_by_prefix' AS q, SUBSTRING_INDEX(option_name, '_', 1) AS prefix, COUNT(*) AS rows_count, ROUND(SUM(LENGTH(option_value)) / 1024, 1) AS size_kb
FROM wp_options
WHERE autoload IN ('yes', 'on', 'auto-on', 'auto')
GROUP BY prefix
ORDER BY size_kb DESC
LIMIT 25;

SELECT '06_options_total' AS q, COUNT(*) AS rows_count, ROUND(SUM(LENGTH(option_value)) / 1024 / 1024, 2) AS size_mb
FROM wp_options;

SELECT '07_transients_total' AS q,
       COUNT(*) AS rows_count,
       SUM(option_name LIKE '\_transient\_timeout\_%' OR option_name LIKE '\_site\_transient\_timeout\_%') AS timeout_rows,
       SUM(autoload IN ('yes', 'on', 'auto-on', 'auto')) AS autoloaded_rows,
       ROUND(SUM(LENGTH(option_value)) / 1024, 1) AS size_kb
FROM wp_options
WHERE option_name LIKE '\_transient\_%' OR option_name LIKE '\_site\_transient\_%';

SELECT '08_transients_expired' AS q, COUNT(*) AS expired_count
FROM wp_options
WHERE (option_name LIKE '\_transient\_timeout\_%' OR option_name LIKE '\_site\_transient\_timeout\_%')
  AND CAST(option_value AS UNSIGNED) < UNIX_TIMESTAMP();

SELECT '09_transients_by_prefix' AS q,
       SUBSTRING_INDEX(REPLACE(REPLACE(option_name, '_site_transient_', ''), '_transient_', ''), '_', 2) AS transient_prefix,
       COUNT(*) AS rows_count,
       ROUND(SUM(LENGTH(option_value)) / 1024, 1) AS size_kb
FROM wp_options
WHERE (option_name LIKE '\_transient\_%' OR option_name LIKE '\_site\_transient\_%')
  AND option_name NOT LIKE '\_transient\_timeout\_%'
  AND option_name NOT LIKE '\_site\_transient\_timeout\_%'
GROUP BY transient_prefix
ORDER BY rows_count DESC
LIMIT 25;

SELECT '10_cron_option' AS q, ROUND(LENGTH(option_value) / 1024, 1) AS size_kb
FROM wp_options
WHERE option_name = 'cron';

SELECT '11_wc_sessions' AS q,
       COUNT(*) AS rows_count,
       SUM(session_expiry < UNIX_TIMESTAMP()) AS expired_rows,
       ROUND(SUM(LENGTH(session_value)) / 1024 / 1024, 2) AS data_mb,
       FROM_UNIXTIME(MIN(session_expiry)) AS oldest_expiry,
       FROM_UNIXTIME(MAX(session_expiry)) AS newest_expiry
FROM wp_woocommerce_sessions;

SELECT '12_wc_sessions_table' AS q, TABLE_NAME, TABLE_ROWS, ROUND(DATA_LENGTH / 1024 / 1024, 2) AS data_mb, ROUND(INDEX_LENGTH / 1024 / 1024, 2) AS index_mb, ROUND(DATA_FREE / 1024 / 1024, 2) AS free_mb
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE '%woocommerce\_sessions';

SELECT '13_as_by_status' AS q, status, COUNT(*) AS rows_count, MIN(scheduled_date_gmt) AS oldest, MAX(scheduled_date_gmt) AS newest
FROM wp_actionscheduler_actions
GROUP BY status
ORDER BY rows_count DESC;

SELECT '14_as_pending_past_due' AS q, COUNT(*) AS past_due_count, MIN(scheduled_date_gmt) AS oldest_due
FROM wp_actionscheduler_actions
WHERE status = 'pending' AND scheduled_date_gmt < UTC_TIMESTAMP() - INTERVAL 10 MINUTE;

SELECT '15_as_top_hooks' AS q, hook, status, COUNT(*) AS rows_count
FROM wp_actionscheduler_actions
GROUP BY hook, status
ORDER BY rows_count DESC
LIMIT 30;

SELECT '16_as_failed_recent' AS q, hook, COUNT(*) AS failed_last_7_days
FROM wp_actionscheduler_actions
WHERE status = 'failed' AND last_attempt_gmt > UTC_TIMESTAMP() - INTERVAL 7 DAY
GROUP BY hook
ORDER BY failed_last_7_days DESC
LIMIT 15;

SELECT '17_as_logs' AS q, COUNT(*) AS log_rows
FROM wp_actionscheduler_logs;

SELECT '18_as_orphan_logs' AS q, COUNT(*) AS orphan_log_rows
FROM wp_actionscheduler_logs l
LEFT JOIN wp_actionscheduler_actions a ON a.action_id = l.action_id
WHERE a.action_id IS NULL;

SELECT '19_as_tables' AS q, TABLE_NAME, TABLE_ROWS, ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS total_mb
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE '%actionscheduler%';

SELECT '20_largest_tables' AS q, TABLE_NAME, ENGINE, TABLE_ROWS,
       ROUND(DATA_LENGTH / 1024 / 1024, 2) AS data_mb,
       ROUND(INDEX_LENGTH / 1024 / 1024, 2) AS index_mb,
       ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS total_mb,
       ROUND(DATA_FREE / 1024 / 1024, 2) AS free_mb,
       TABLE_COLLATION
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
LIMIT 40;

SELECT '21_db_total' AS q, COUNT(*) AS tables_count, ROUND(SUM(DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS total_mb, SUM(ENGINE = 'MyISAM') AS myisam_tables
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE();

SELECT '22_posts_by_type_status' AS q, post_type, post_status, COUNT(*) AS rows_count
FROM wp_posts
GROUP BY post_type, post_status
ORDER BY rows_count DESC
LIMIT 40;

SELECT '23_revisions' AS q, p.post_type AS parent_type, COUNT(*) AS revisions,
       SUM(r.post_date < NOW() - INTERVAL 60 DAY) AS older_than_60_days
FROM wp_posts r
LEFT JOIN wp_posts p ON p.ID = r.post_parent
WHERE r.post_type = 'revision'
GROUP BY p.post_type
ORDER BY revisions DESC;

SELECT '24_revisions_top_parents' AS q, r.post_parent, p.post_type, COUNT(*) AS revisions
FROM wp_posts r
LEFT JOIN wp_posts p ON p.ID = r.post_parent
WHERE r.post_type = 'revision'
GROUP BY r.post_parent, p.post_type
ORDER BY revisions DESC
LIMIT 15;

SELECT '25_drafts_trash' AS q,
       SUM(post_status = 'auto-draft') AS auto_drafts,
       SUM(post_status = 'auto-draft' AND post_date < NOW() - INTERVAL 7 DAY) AS auto_drafts_older_7d,
       SUM(post_status = 'trash') AS trashed,
       SUM(post_type = 'oembed_cache') AS oembed_cache,
       SUM(post_type = 'customize_changeset') AS changesets
FROM wp_posts;

SELECT '26_orphan_postmeta' AS q, COUNT(*) AS orphan_rows, ROUND(SUM(LENGTH(pm.meta_value)) / 1024 / 1024, 2) AS size_mb
FROM wp_postmeta pm
LEFT JOIN wp_posts p ON p.ID = pm.post_id
WHERE p.ID IS NULL;

SELECT '27_postmeta_total' AS q, COUNT(*) AS rows_count, ROUND(SUM(LENGTH(meta_value)) / 1024 / 1024, 2) AS value_mb
FROM wp_postmeta;

SELECT '28_postmeta_top_keys' AS q, meta_key, COUNT(*) AS rows_count, ROUND(SUM(LENGTH(meta_value)) / 1024 / 1024, 2) AS size_mb
FROM wp_postmeta
GROUP BY meta_key
ORDER BY size_mb DESC
LIMIT 30;

SELECT '29_orphan_term_relationships' AS q, COUNT(*) AS orphan_rows
FROM wp_term_relationships tr
INNER JOIN wp_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
LEFT JOIN wp_posts p ON p.ID = tr.object_id
WHERE p.ID IS NULL AND tt.taxonomy NOT IN ('link_category');

SELECT '30_orphan_commentmeta' AS q, COUNT(*) AS orphan_rows
FROM wp_commentmeta cm
LEFT JOIN wp_comments c ON c.comment_ID = cm.comment_id
WHERE c.comment_ID IS NULL;

SELECT '31_comments_by_type' AS q, comment_type, comment_approved, COUNT(*) AS rows_count
FROM wp_comments
GROUP BY comment_type, comment_approved
ORDER BY rows_count DESC
LIMIT 20;

SELECT '32_usermeta_top_keys' AS q, meta_key, COUNT(*) AS rows_count, ROUND(SUM(LENGTH(meta_value)) / 1024 / 1024, 2) AS size_mb
FROM wp_usermeta
GROUP BY meta_key
ORDER BY size_mb DESC
LIMIT 20;

SELECT '33_users' AS q, COUNT(*) AS users_count
FROM wp_users;

SELECT '34_lookup_rows' AS q,
       (SELECT COUNT(*) FROM wp_wc_product_meta_lookup) AS lookup_rows,
       (SELECT COUNT(*) FROM wp_posts WHERE post_type IN ('product', 'product_variation') AND post_status NOT IN ('trash', 'auto-draft')) AS product_rows;

SELECT '35_lookup_missing_products' AS q, COUNT(*) AS products_without_lookup_row
FROM wp_posts p
LEFT JOIN wp_wc_product_meta_lookup l ON l.product_id = p.ID
WHERE p.post_type IN ('product', 'product_variation')
  AND p.post_status NOT IN ('trash', 'auto-draft')
  AND l.product_id IS NULL;

SELECT '36_lookup_stale_rows' AS q, COUNT(*) AS lookup_rows_without_product
FROM wp_wc_product_meta_lookup l
LEFT JOIN wp_posts p ON p.ID = l.product_id
WHERE p.ID IS NULL;

SELECT '37_lookup_stock_sku' AS q,
       SUM(stock_status = 'instock') AS instock,
       SUM(stock_status = 'outofstock') AS outofstock,
       SUM(sku IS NULL OR sku = '') AS without_sku,
       SUM(onsale = 1) AS onsale
FROM wp_wc_product_meta_lookup;

SELECT '38_indexes_postmeta_options_posts' AS q, TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX, COLUMN_NAME, SUB_PART, NON_UNIQUE, CARDINALITY
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND (TABLE_NAME LIKE '%\_postmeta' OR TABLE_NAME LIKE '%\_options' OR TABLE_NAME LIKE '%\_posts' OR TABLE_NAME LIKE '%wc\_product\_meta\_lookup')
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

SELECT '39_hpos_tables' AS q, TABLE_NAME, TABLE_ROWS, ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS total_mb
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE '%wc\_order%';

SELECT '40_wordfence_leftover_tables' AS q, TABLE_NAME, TABLE_ROWS, ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS total_mb
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND (TABLE_NAME LIKE '%\_wf%' OR TABLE_NAME LIKE '%wordfence%');

SELECT '41_leftover_options' AS q, option_name, autoload, ROUND(LENGTH(option_value) / 1024, 1) AS size_kb
FROM wp_options
WHERE option_name LIKE 'wordfence%' OR option_name LIKE 'wf\_%' OR option_name LIKE 'smush%' OR option_name LIKE 'wp-smush%' OR option_name LIKE 'wp\_smush%' OR option_name LIKE 'dokan%' OR option_name LIKE 'yith%' OR option_name LIKE 'learnpress%' OR option_name LIKE 'litespeed%' OR option_name LIKE 'w3tc%'
ORDER BY LENGTH(option_value) DESC
LIMIT 40;

SELECT '42_plugin_tables' AS q, TABLE_NAME, TABLE_ROWS, ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS total_mb
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND (TABLE_NAME LIKE '%aiowps%' OR TABLE_NAME LIKE '%wpr\_%' OR TABLE_NAME LIKE '%yoast%' OR TABLE_NAME LIKE '%e\_events%' OR TABLE_NAME LIKE '%e\_submissions%' OR TABLE_NAME LIKE '%subscription%' OR TABLE_NAME LIKE '%upanel%' OR TABLE_NAME LIKE '%post\_views%' OR TABLE_NAME LIKE '%wpmailsmtp%' OR TABLE_NAME LIKE '%adbc%')
ORDER BY total_mb DESC;

SELECT '43_settings' AS q, option_name, LEFT(option_value, 200) AS value_preview
FROM wp_options
WHERE option_name IN (
  'woocommerce_custom_orders_table_enabled',
  'woocommerce_custom_orders_table_data_sync_enabled',
  'woocommerce_feature_order_attribution_enabled',
  'woocommerce_analytics_enabled',
  'woocommerce_allow_tracking',
  'woocommerce_show_marketplace_suggestions',
  'woocommerce_enable_ajax_add_to_cart',
  'woocommerce_task_list_hidden',
  'woocommerce_feature_product_block_editor_enabled',
  'woocommerce_attribute_lookup_enabled',
  'woocommerce_hpos_fts_index_enabled',
  'elementor_css_print_method',
  'elementor_google_font',
  'elementor_font_display',
  'elementor_load_fa4_shim',
  'elementor_optimized_image_loading',
  'elementor_optimized_gutenberg_loading',
  'elementor_lazy_load_background_images',
  'elementor_element_cache_ttl',
  'elementor_experiment-e_font_icon_svg',
  'elementor_experiment-e_optimized_markup',
  'elementor_experiment-additional_custom_breakpoints',
  'elementor_experiment-e_local_google_fonts',
  'woosbymdz_globalimport',
  'woosbymdz_noajaxrefchecker',
  'woosbymdz_importjq',
  'woosbymdz_donimport_swiper',
  'woosbymdz_yoast_maincat_addslider',
  'woosbymdz_isbrowsernocache',
  'template',
  'stylesheet',
  'permalink_structure',
  'db_version',
  'posts_per_page',
  'woocommerce_db_version',
  'woocommerce_version'
)
ORDER BY option_name;

SELECT '44_active_plugins' AS q, option_value
FROM wp_options
WHERE option_name = 'active_plugins';

SELECT '45_rocket_settings' AS q, k.setting_key,
       SUBSTRING(s.v, LOCATE(CONCAT('"', k.setting_key, '"'), s.v), 400) AS serialized_fragment
FROM (SELECT option_value AS v FROM wp_options WHERE option_name = 'wp_rocket_settings') s
JOIN (
  SELECT 'cache_mobile' AS setting_key UNION ALL
  SELECT 'do_caching_mobile_files' UNION ALL
  SELECT 'cache_logged_user' UNION ALL
  SELECT 'purge_cron_interval' UNION ALL
  SELECT 'purge_cron_unit' UNION ALL
  SELECT 'manual_preload' UNION ALL
  SELECT 'preload_links' UNION ALL
  SELECT 'preload_fonts' UNION ALL
  SELECT 'cache_reject_uri' UNION ALL
  SELECT 'cache_reject_cookies' UNION ALL
  SELECT 'cache_reject_ua' UNION ALL
  SELECT 'cache_query_strings' UNION ALL
  SELECT 'minify_css' UNION ALL
  SELECT 'remove_unused_css' UNION ALL
  SELECT 'remove_unused_css_safelist' UNION ALL
  SELECT 'async_css' UNION ALL
  SELECT 'critical_css' UNION ALL
  SELECT 'minify_js' UNION ALL
  SELECT 'minify_concatenate_js' UNION ALL
  SELECT 'defer_all_js' UNION ALL
  SELECT 'exclude_defer_js' UNION ALL
  SELECT 'delay_js' UNION ALL
  SELECT 'delay_js_exclusions' UNION ALL
  SELECT 'delay_js_exclusions_selected' UNION ALL
  SELECT 'lazyload' UNION ALL
  SELECT 'lazyload_css_bg_img' UNION ALL
  SELECT 'lazyload_iframes' UNION ALL
  SELECT 'lazyload_youtube' UNION ALL
  SELECT 'exclude_lazyload' UNION ALL
  SELECT 'image_dimensions' UNION ALL
  SELECT 'auto_preload_fonts' UNION ALL
  SELECT 'host_fonts_locally' UNION ALL
  SELECT 'minify_google_fonts' UNION ALL
  SELECT 'control_heartbeat' UNION ALL
  SELECT 'heartbeat_site_behavior' UNION ALL
  SELECT 'heartbeat_admin_behavior' UNION ALL
  SELECT 'heartbeat_editor_behavior' UNION ALL
  SELECT 'database_auto_cleanup' UNION ALL
  SELECT 'cdn'
) k
WHERE LOCATE(CONCAT('"', k.setting_key, '"'), s.v) > 0;
