SELECT 'STOP: قبل از اجرا از کل دیتابیس بکاپ کامل بگیرید و ترجیحا در ساعت کم‌ترافیک اجرا کنید' AS warning;

SELECT 'step_01_expired_transients' AS step;

DELETE a, b
FROM wp_options a
INNER JOIN wp_options b ON b.option_name = CONCAT('_transient_timeout_', SUBSTRING(a.option_name, 12))
WHERE a.option_name LIKE '\_transient\_%'
  AND a.option_name NOT LIKE '\_transient\_timeout\_%'
  AND CAST(b.option_value AS UNSIGNED) < UNIX_TIMESTAMP();

DELETE a, b
FROM wp_options a
INNER JOIN wp_options b ON b.option_name = CONCAT('_site_transient_timeout_', SUBSTRING(a.option_name, 17))
WHERE a.option_name LIKE '\_site\_transient\_%'
  AND a.option_name NOT LIKE '\_site\_transient\_timeout\_%'
  AND CAST(b.option_value AS UNSIGNED) < UNIX_TIMESTAMP();

SELECT 'step_02_expired_wc_sessions' AS step;

DELETE FROM wp_woocommerce_sessions
WHERE session_expiry < UNIX_TIMESTAMP();

SELECT 'step_03_action_scheduler_complete_older_14d' AS step;

DELETE FROM wp_actionscheduler_logs
WHERE action_id IN (
  SELECT action_id FROM (
    SELECT action_id FROM wp_actionscheduler_actions
    WHERE status IN ('complete', 'canceled')
      AND scheduled_date_gmt < UTC_TIMESTAMP() - INTERVAL 14 DAY
    LIMIT 50000
  ) AS batch_ids
);

DELETE FROM wp_actionscheduler_actions
WHERE status IN ('complete', 'canceled')
  AND scheduled_date_gmt < UTC_TIMESTAMP() - INTERVAL 14 DAY
LIMIT 50000;

SELECT 'step_04_action_scheduler_failed_older_60d' AS step;

DELETE FROM wp_actionscheduler_logs
WHERE action_id IN (
  SELECT action_id FROM (
    SELECT action_id FROM wp_actionscheduler_actions
    WHERE status = 'failed'
      AND scheduled_date_gmt < UTC_TIMESTAMP() - INTERVAL 60 DAY
    LIMIT 50000
  ) AS batch_ids
);

DELETE FROM wp_actionscheduler_actions
WHERE status = 'failed'
  AND scheduled_date_gmt < UTC_TIMESTAMP() - INTERVAL 60 DAY
LIMIT 50000;

SELECT 'step_05_action_scheduler_orphan_logs' AS step;

DELETE FROM wp_actionscheduler_logs
WHERE log_id IN (
  SELECT log_id FROM (
    SELECT l.log_id FROM wp_actionscheduler_logs l
    LEFT JOIN wp_actionscheduler_actions a ON a.action_id = l.action_id
    WHERE a.action_id IS NULL
    LIMIT 100000
  ) AS batch_ids
);

SELECT 'step_06_revisions_older_60d' AS step;

DELETE FROM wp_posts
WHERE post_type = 'revision'
  AND post_date < NOW() - INTERVAL 60 DAY;

SELECT 'step_07_auto_drafts_older_7d' AS step;

DELETE FROM wp_posts
WHERE post_status = 'auto-draft'
  AND post_date < NOW() - INTERVAL 7 DAY;

SELECT 'step_08_oembed_cache' AS step;

DELETE FROM wp_posts
WHERE post_type = 'oembed_cache';

SELECT 'step_09_orphan_postmeta' AS step;

DELETE FROM wp_postmeta
WHERE meta_id IN (
  SELECT meta_id FROM (
    SELECT pm.meta_id FROM wp_postmeta pm
    LEFT JOIN wp_posts p ON p.ID = pm.post_id
    WHERE p.ID IS NULL
    LIMIT 100000
  ) AS batch_ids
);

SELECT 'step_10_orphan_term_relationships' AS step;

DELETE tr
FROM wp_term_relationships tr
INNER JOIN wp_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
LEFT JOIN wp_posts p ON p.ID = tr.object_id
WHERE p.ID IS NULL
  AND tt.taxonomy NOT IN ('link_category');

SELECT 'step_11_orphan_commentmeta' AS step;

DELETE cm
FROM wp_commentmeta cm
LEFT JOIN wp_comments c ON c.comment_ID = cm.comment_id
WHERE c.comment_ID IS NULL;

SELECT 'step_12_stale_lookup_rows' AS step;

DELETE l
FROM wp_wc_product_meta_lookup l
LEFT JOIN wp_posts p ON p.ID = l.product_id
WHERE p.ID IS NULL;

SELECT 'step_13_optimize' AS step;

OPTIMIZE TABLE wp_options, wp_postmeta, wp_posts, wp_woocommerce_sessions, wp_actionscheduler_actions, wp_actionscheduler_logs, wp_term_relationships, wp_commentmeta;

SELECT 'done' AS step;
