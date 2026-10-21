<?php
defined('ABSPATH') || exit;

if (class_exists('Post_Views_Counter')) {
    return;
}

define('POOSHE_PVC_TABLE', 'post_views');

class Pooshe_Post_Views_Counter {

    private static $instance = null;

    private $table;

    private $cache_group = 'pooshe-pvc';

    private function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . POOSHE_PVC_TABLE;
        $this->init_hooks();
    }

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function init_hooks() {
        add_action('wp', [$this, 'maybe_count_visit']);
        add_action('deleted_post', [$this, 'delete_post_views']);
        add_action('rest_api_init', [$this, 'register_rest_route']);
        add_shortcode('post-views', [$this, 'shortcode']);
        add_filter('posts_clauses', [$this, 'orderby_post_views'], 10, 2);
        add_filter('manage_posts_columns', [$this, 'admin_column_header']);
        add_action('manage_posts_custom_column', [$this, 'admin_column_content'], 10, 2);
    }

    private function get_countable_post_types() {
        return apply_filters('pooshe_pvc_post_types', ['post', 'page', 'product']);
    }

    public function maybe_count_visit() {
        if (is_admin() && !wp_doing_ajax()) {
            return;
        }
        if (is_preview() || is_feed() || is_trackback() || is_customize_preview()) {
            return;
        }
        $post_types = $this->get_countable_post_types();
        if (!$post_types || !is_singular($post_types)) {
            return;
        }
        $post_id = (int) get_the_ID();
        if ($post_id < 1) {
            return;
        }
        if ($this->is_crawler()) {
            return;
        }
        if (!$this->should_count($post_id)) {
            return;
        }
        $this->count_visit($post_id);
    }

    private function is_crawler() {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if ($ua === '') {
            return true;
        }
        $crawlers = apply_filters('pooshe_pvc_crawlers_list', [
            'Googlebot', 'Bingbot', 'Slurp', 'DuckDuckBot', 'Baiduspider',
            'YandexBot', 'Sogou', 'Exabot', 'facebookexternalhit', 'Facebot',
            'Twitterbot', 'Pinterest', 'WhatsApp', 'Applebot', 'SemrushBot',
            'AhrefsBot', 'MJ12bot', 'DotBot', 'rogerbot', 'SiteChecker',
            'Screaming Frog', 'Wget', 'curl', 'python-requests', 'Go-http-client',
        ]);
        foreach ($crawlers as $crawler) {
            if (stripos($ua, $crawler) !== false) {
                return true;
            }
        }
        return false;
    }

    private function should_count($post_id) {
        $cache_key = 'pooshe_pvc_' . $post_id . '_' . $this->get_user_ip();
        if (get_transient($cache_key) !== false) {
            return false;
        }
        $interval = apply_filters('pooshe_pvc_time_between_counts', HOUR_IN_SECONDS);
        set_transient($cache_key, 1, $interval);
        return true;
    }

    private function get_user_ip() {
        $headers = [
            'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP', 'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_CLIENT_IP', 'REMOTE_ADDR',
        ];
        foreach ($headers as $header) {
            $ip = $_SERVER[$header] ?? '';
            if ($ip === '') {
                continue;
            }
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
        return '127.0.0.1';
    }

    private function count_visit($post_id) {
        global $wpdb;
        $increment = max(1, (int) apply_filters('pooshe_pvc_increment_amount', 1, $post_id));
        $date = explode('-', gmdate('W-d-m-Y-o', time()));
        $visits = [
            0 => $date[3] . $date[2] . $date[1],
            1 => $date[4] . $date[0],
            2 => $date[3] . $date[2],
            3 => $date[3],
            4 => 'total',
        ];
        $sql = "INSERT INTO {$this->table} (`id`, `type`, `period`, `count`)
                VALUES (%d, %d, %s, %d)
                ON DUPLICATE KEY UPDATE count = count + %d";
        foreach ($visits as $type => $period) {
            $result = $wpdb->query($wpdb->prepare($sql, $post_id, $type, $period, $increment, $increment));
            if ($result === false) {
                error_log('Pooshe PVC: DB insert failed for ID ' . $post_id . ', type ' . $type . ': ' . $wpdb->last_error);
            }
        }
        wp_cache_delete('views_' . $post_id, $this->cache_group);
    }

    public function delete_post_views($post_id) {
        global $wpdb;
        $wpdb->delete($this->table, ['id' => (int) $post_id], ['%d']);
        wp_cache_delete('views_' . $post_id, $this->cache_group);
    }

    public function register_rest_route() {
        register_rest_route('pooshe-pvc/v1', '/count/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_count'],
            'permission_callback' => '__return_true',
            'args' => [
                'id' => [
                    'required' => true,
                    'validate_callback' => function ($p) {
                        return is_numeric($p);
                    },
                ],
            ],
        ]);
    }

    public function rest_count($request) {
        $post_id = (int) $request->get_param('id');
        if ($post_id < 1) {
            return new WP_Error('invalid_id', 'Invalid post ID', ['status' => 400]);
        }
        if ($this->is_crawler()) {
            return ['counted' => false, 'reason' => 'crawler'];
        }
        $this->count_visit($post_id);
        return ['counted' => true, 'views' => self::get_post_views($post_id)];
    }

    public static function get_post_views($post_id = 0) {
        global $wpdb;
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        $table = $wpdb->prefix . POOSHE_PVC_TABLE;
        if (is_array($post_id)) {
            $ids = array_map('intval', $post_id);
            $placeholders = implode(',', array_fill(0, count($ids), '%d'));
            $cache_key = 'views_' . implode(',', $ids);
            $views = wp_cache_get($cache_key, 'pooshe-pvc');
            if ($views === false) {
                $views = (int) $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT SUM(count) FROM {$table} WHERE id IN ({$placeholders}) AND type = 4",
                        $ids
                    )
                );
                wp_cache_add($cache_key, $views, 'pooshe-pvc', 300);
            }
        } else {
            $post_id = (int) $post_id;
            $cache_key = 'views_' . $post_id;
            $views = wp_cache_get($cache_key, 'pooshe-pvc');
            if ($views === false) {
                $views = (int) $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT SUM(count) FROM {$table} WHERE id = %d AND type = 4",
                        $post_id
                    )
                );
                wp_cache_add($cache_key, $views, 'pooshe-pvc', 300);
            }
        }
        return $views;
    }

    public static function get_most_viewed_posts($args = []) {
        global $wpdb;
        $args = wp_parse_args($args, [
            'posts_per_page' => 5,
            'post_type' => 'post',
            'days' => 0,
        ]);
        $table = $wpdb->prefix . POOSHE_PVC_TABLE;
        $where = 'pv.type = 4';
        if ($args['days'] > 0) {
            $min_period = absint(gmdate('Ymd', time() - $args['days'] * DAY_IN_SECONDS));
            $where = "(pv.type = 4) OR (pv.type = 0 AND CAST(pv.period AS SIGNED) >= {$min_period})";
        }
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT p.ID, SUM(pv.count) AS views
                FROM {$wpdb->posts} p
                INNER JOIN {$table} pv ON p.ID = pv.id
                WHERE p.post_type = %s AND p.post_status = 'publish' AND ({$where})
                GROUP BY p.ID
                ORDER BY views DESC
                LIMIT %d",
                $args['post_type'],
                $args['posts_per_page']
            )
        );
        if (!$results) {
            return [];
        }
        $views_map = wp_list_pluck($results, 'views', 'ID');
        $posts = get_posts([
            'post__in' => array_keys($views_map),
            'post_type' => $args['post_type'],
            'posts_per_page' => count($views_map),
            'orderby' => 'post__in',
            'suppress_filters' => true,
        ]);
        foreach ($posts as $post) {
            $post->post_views = (int) ($views_map[$post->ID] ?? 0);
        }
        return $posts;
    }

    public function shortcode($atts) {
        $atts = shortcode_atts([
            'id' => 0,
            'period' => 'total',
            'text' => 'بازدید: %s',
        ], $atts);
        $post_id = max(0, (int) $atts['id']) ?: get_the_ID();
        $views = self::get_post_views($post_id);
        return sprintf(esc_html($atts['text']), number_format_i18n($views));
    }

    public function orderby_post_views($clauses, $query) {
        if ($query->get('orderby') !== 'post_views') {
            return $clauses;
        }
        $clauses['join'] .= " LEFT JOIN {$this->table} pvc ON {$GLOBALS['wpdb']->posts}.ID = pvc.id AND pvc.type = 4";
        $clauses['orderby'] = 'COALESCE(pvc.count, 0) ' . ($query->get('order') === 'ASC' ? 'ASC' : 'DESC');
        $clauses['groupby'] = "{$GLOBALS['wpdb']->posts}.ID";
        return $clauses;
    }

    public function admin_column_header($columns) {
        $columns['post_views'] = 'بازدید';
        return $columns;
    }

    public function admin_column_content($column, $post_id) {
        if ($column !== 'post_views') {
            return;
        }
        $views = self::get_post_views($post_id);
        $link = admin_url('edit.php?post_type=' . get_post_type($post_id) . '&orderby=post_views&order=DESC');
        echo '<a href="' . esc_url($link) . '" title="مرتب‌سازی بر اساس بازدید">' . number_format_i18n($views) . '</a>';
    }
}

function pooshe_pvc() {
    return Pooshe_Post_Views_Counter::instance();
}
add_action('plugins_loaded', 'pooshe_pvc');

function pooshe_get_post_views($post_id = 0) {
    return Pooshe_Post_Views_Counter::get_post_views($post_id);
}

function pooshe_most_viewed_posts($args = [], $display = true) {
    $posts = Pooshe_Post_Views_Counter::get_most_viewed_posts($args);
    if (!$display) {
        return $posts;
    }
    if (!$posts) {
        echo '<p class="pooshe-pvc-none">مطلبی یافت نشد.</p>';
        return;
    }
    echo '<ul class="pooshe-most-viewed">';
    foreach ($posts as $post) {
        printf(
            '<li><a href="%s">%s</a> <span class="views">(%s)</span></li>',
            esc_url(get_permalink($post->ID)),
            esc_html($post->post_title),
            sprintf(__('%s بازدید', 'pooshe'), number_format_i18n($post->post_views))
        );
    }
    echo '</ul>';
}
