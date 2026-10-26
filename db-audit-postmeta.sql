SELECT 'p1_elementor_page_settings_by_type' AS q, p.post_type, COUNT(*) AS rows_count, SUM(LENGTH(m.meta_value) > 50000) AS big_rows, ROUND(SUM(LENGTH(m.meta_value)) / 1024 / 1024, 2) AS size_mb, MAX(LENGTH(m.meta_value)) AS max_bytes
FROM wp_postmeta m
LEFT JOIN wp_posts p ON p.ID = m.post_id
WHERE m.meta_key = '_elementor_page_settings'
GROUP BY p.post_type
ORDER BY size_mb DESC;

SELECT 'p2_big_page_settings_vs_builder' AS q, COUNT(*) AS big_rows, SUM(e.meta_value = 'builder') AS built_with_elementor, SUM(e.meta_value IS NULL) AS no_edit_mode
FROM wp_postmeta m
LEFT JOIN wp_postmeta e ON e.post_id = m.post_id AND e.meta_key = '_elementor_edit_mode'
WHERE m.meta_key = '_elementor_page_settings' AND LENGTH(m.meta_value) > 50000;

SELECT 'p3_postmeta_top_keys' AS q, meta_key, COUNT(*) AS rows_count, ROUND(SUM(LENGTH(meta_value)) / 1024 / 1024, 2) AS size_mb
FROM wp_postmeta
GROUP BY meta_key
ORDER BY SUM(LENGTH(meta_value)) DESC
LIMIT 20;

SELECT 'p4_orphan_postmeta_by_key' AS q, m.meta_key, COUNT(*) AS rows_count, ROUND(SUM(LENGTH(m.meta_value)) / 1024, 1) AS size_kb
FROM wp_postmeta m
LEFT JOIN wp_posts p ON p.ID = m.post_id
WHERE p.ID IS NULL
GROUP BY m.meta_key
ORDER BY rows_count DESC
LIMIT 20;

SELECT 'p5_big_tables' AS q, TABLE_NAME, TABLE_ROWS, ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 1) AS total_mb, ROUND(DATA_FREE / 1024 / 1024, 1) AS free_mb
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
LIMIT 12;
