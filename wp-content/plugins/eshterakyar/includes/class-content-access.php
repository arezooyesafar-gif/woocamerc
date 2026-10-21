<?php

if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Content_Access {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('eshterakyar_paywall', array($this, 'paywall_shortcode'));

        add_filter('the_content', array($this, 'filter_the_content'), 12);
        add_filter('the_content', array($this, 'filter_the_content_finalize'), 999);
        add_filter('the_excerpt', array($this, 'filter_the_excerpt'), 12);
        add_filter('get_the_excerpt', array($this, 'filter_get_the_excerpt'), 12, 2);

        add_action('template_redirect', array($this, 'maybe_redirect_to_plans'), 5);

        add_action('add_meta_boxes', array($this, 'add_post_meta_box'));
        add_action('save_post', array($this, 'save_post_meta_box'));

        add_filter('elementor/frontend/the_content', array($this, 'filter_the_content'), 12);
    }

    /**
     * @return bool
     */
    private function should_skip_content_filter(): bool {
        if (is_admin() && !wp_doing_ajax()) {
            return true;
        }
        if (wp_doing_ajax() || is_feed() || (defined('REST_REQUEST') && REST_REQUEST)) {
            return true;
        }
        if (is_preview()) {
            return false;
        }
        return false;
    }

    /**
     * @param int|null $post_id
     * @return bool
     */
    private function is_restricted_context($post_id = null): bool {
        if (!Eshterakyar_Subscription::is_content_restriction_enabled()) {
            return false;
        }
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        if (!$post_id) {
            return false;
        }
        return Eshterakyar_Subscription::should_enforce_post_paywall((int) $post_id);
    }

    /**
     * @param int $post_id
     * @return string inline|redirect|inherit
     */
    public static function get_display_mode_for_post(int $post_id): string {
        $mode = get_post_meta($post_id, '_eshterakyar_content_mode', true);
        if (in_array($mode, array('inline', 'redirect'), true)) {
            return $mode;
        }
        $options = get_option('eshterakyar_options', array());
        $default = isset($options['content_restriction_mode']) ? $options['content_restriction_mode'] : 'inline';
        return in_array($default, array('inline', 'redirect'), true) ? $default : 'inline';
    }

    /**
     * Split content at paywall shortcode.
     *
     * @param string $content
     * @return array{preview: string, has_marker: bool}
     */
    public static function split_content_at_paywall(string $content): array {
        $split_patterns = array(
            '/\[eshterakyar_paywall(?:\s[^\]]*)?\]/i',
            '/<!--eshterakyar-paywall-split-->/',
            '/<div[^>]*class="[^"]*eshterakyar-paywall-marker[^"]*"[^>]*>.*?<\/div>/is',
        );

        foreach ($split_patterns as $pattern) {
            $parts = preg_split($pattern, $content, 2);
            if ($parts !== false && count($parts) >= 2) {
                return array(
                    'preview' => $parts[0],
                    'has_marker' => true,
                );
            }
        }

        return array(
            'preview' => $content,
            'has_marker' => false,
        );
    }

    /**
     * @return string
     */
    public static function render_paywall_box(int $post_id = 0): string {
        $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
        $is_logged_in = is_user_logged_in();
        $options = get_option('eshterakyar_options', array());
        $show_login_hint = !isset($options['content_show_login_hint']) || !empty($options['content_show_login_hint']);

        ob_start();
        include ESHTERAKYAR_PLUGIN_DIR . 'templates/frontend/content-paywall.php';
        return (string) ob_get_clean();
    }

    public function paywall_shortcode() {
        $post_id = get_the_ID();
        $show_editor_marker = is_admin();
        if (!$show_editor_marker && $post_id && is_preview() && current_user_can('edit_post', $post_id)) {
            $show_editor_marker = true;
        }

        if ($show_editor_marker) {
            return '<div class="eshterakyar-paywall-marker" aria-hidden="true"><span>' .
                esc_html__('— مرز محتوای ویژه (اشتراک‌یار) —', 'eshterakyar') .
                '</span></div>';
        }
        return '<!--eshterakyar-paywall-split-->';
    }

    /**
     * Final pass: strip any locked HTML that leaked after other filters.
     *
     * @param string $content
     * @return string
     */
    public function filter_the_content_finalize($content) {
        if ($this->should_skip_content_filter()) {
            return $content;
        }

        $post_id = get_the_ID();
        if (!$post_id || !is_singular(Eshterakyar_Subscription::get_content_restriction_post_types())) {
            return $content;
        }

        if (!Eshterakyar_Subscription::should_enforce_post_paywall($post_id)) {
            return $content;
        }

        if (Eshterakyar_Subscription::user_can_read_post_content(get_current_user_id(), $post_id)) {
            return $content;
        }

        if (strpos($content, 'eshterakyar-content-paywall') !== false) {
            return $content;
        }

        return $this->filter_the_content($content);
    }

    /**
     * @param string $content
     * @return string
     */
    public function filter_the_content($content) {
        if (strpos($content, 'eshterakyar-content-paywall') !== false) {
            return $content;
        }
        static $filtering = false;
        if ($filtering) {
            return $content;
        }

        if ($this->should_skip_content_filter()) {
            return $content;
        }

        $post_id = get_the_ID();
        if (!$post_id || !is_singular(Eshterakyar_Subscription::get_content_restriction_post_types())) {
            return $content;
        }

        if (!Eshterakyar_Subscription::should_enforce_post_paywall($post_id)) {
            return $content;
        }

        $user_id = get_current_user_id();
        if (Eshterakyar_Subscription::user_can_read_post_content($user_id, $post_id)) {
            $content = preg_replace('/\[eshterakyar_paywall(?:\s[^\]]*)?\]/i', '', $content);
            $content = str_replace('<!--eshterakyar-paywall-split-->', '', $content);
            return preg_replace('/<div[^>]*class="[^"]*eshterakyar-paywall-marker[^"]*"[^>]*>.*?<\/div>/is', '', $content);
        }

        if (self::get_display_mode_for_post($post_id) === 'redirect') {
            return $content;
        }

        $raw = (string) get_post_field('post_content', $post_id);
        $split = self::split_content_at_paywall($raw);
        if (!$split['has_marker']) {
            $split = self::split_content_at_paywall($content);
        }

        $preview = '';
        if ($split['has_marker'] && trim($split['preview']) !== '') {
            $filtering = true;
            $preview = apply_filters('the_content', $split['preview']);
            $filtering = false;
        } elseif (!$split['has_marker']) {
            $preview = '';
        }

        $preview = str_replace('<!--eshterakyar-paywall-split-->', '', $preview);
        $preview = preg_replace('/<div[^>]*class="[^"]*eshterakyar-paywall-marker[^"]*"[^>]*>.*?<\/div>/is', '', $preview);

        return $preview . self::render_paywall_box($post_id);
    }

    /**
     * @param string $excerpt
     * @return string
     */
    public function filter_the_excerpt($excerpt) {
        if ($this->should_skip_content_filter()) {
            return $excerpt;
        }

        $post_id = get_the_ID();
        if (!$post_id || !Eshterakyar_Subscription::should_enforce_post_paywall($post_id)) {
            return $excerpt;
        }

        $user_id = get_current_user_id();
        if (Eshterakyar_Subscription::user_can_read_post_content($user_id, $post_id)) {
            return $excerpt;
        }

        $post = get_post($post_id);
        if (!$post) {
            return $excerpt;
        }

        $split = self::split_content_at_paywall((string) $post->post_content);
        if ($split['has_marker'] && trim(wp_strip_all_tags($split['preview'])) !== '') {
            return wp_trim_words(wp_strip_all_tags($split['preview']), 55, '…');
        }

        if (!empty($excerpt)) {
            return $excerpt;
        }

        return wp_trim_words(wp_strip_all_tags($post->post_content), 40, '…');
    }

    /**
     * @param string  $excerpt
     * @param WP_Post $post
     * @return string
     */
    public function filter_get_the_excerpt($excerpt, $post) {
        if (!$post instanceof WP_Post) {
            return $excerpt;
        }
        if ($this->should_skip_content_filter()) {
            return $excerpt;
        }
        if (!Eshterakyar_Subscription::should_enforce_post_paywall($post->ID)) {
            return $excerpt;
        }
        if (Eshterakyar_Subscription::user_can_read_post_content(get_current_user_id(), $post->ID)) {
            return $excerpt;
        }
        $split = self::split_content_at_paywall((string) $post->post_content);
        if ($split['has_marker'] && trim(wp_strip_all_tags($split['preview'])) !== '') {
            return wp_trim_words(wp_strip_all_tags($split['preview']), 55, '…');
        }
        return $excerpt;
    }

    public function maybe_redirect_to_plans() {
        if (is_admin() || !is_singular(Eshterakyar_Subscription::get_content_restriction_post_types())) {
            return;
        }
        if (!Eshterakyar_Subscription::is_content_restriction_enabled()) {
            return;
        }

        $post_id = get_queried_object_id();
        if (!$post_id || !Eshterakyar_Subscription::should_enforce_post_paywall($post_id)) {
            return;
        }

        if (self::get_display_mode_for_post($post_id) !== 'redirect') {
            return;
        }

        if (Eshterakyar_Subscription::user_can_read_post_content(get_current_user_id(), $post_id)) {
            return;
        }

        $url = Eshterakyar_Subscription_Page::get_page_url();
        if (!$url) {
            return;
        }

        $url = add_query_arg(
            array(
                'eshterakyar_redirect' => 1,
                'redirect_to' => rawurlencode(get_permalink($post_id)),
            ),
            $url
        );

        wp_safe_redirect($url);
        exit;
    }

    public function add_post_meta_box() {
        $post_types = Eshterakyar_Subscription::get_content_restriction_post_types();
        foreach ($post_types as $post_type) {
            add_meta_box(
                'eshterakyar_post_content_meta',
                __('محتوای ویژه (اشتراک‌یار)', 'eshterakyar'),
                array($this, 'post_meta_box_callback'),
                $post_type,
                'side',
                'default'
            );
        }
    }

    public function post_meta_box_callback($post) {
        wp_nonce_field('eshterakyar_post_content_meta', 'eshterakyar_post_content_meta_nonce');

        $is_restricted = get_post_meta($post->ID, '_eshterakyar_content_restricted', true);
        $content_all_plans = get_post_meta($post->ID, '_eshterakyar_content_all_plans', true);
        $content_plans = get_post_meta($post->ID, '_eshterakyar_content_plans', true);
        if (!is_array($content_plans)) {
            $content_plans = array();
        }
        $override_plan_rules = get_post_meta($post->ID, '_eshterakyar_content_override_plan_rules', true);
        $content_mode = get_post_meta($post->ID, '_eshterakyar_content_mode', true);
        if ($content_mode === '') {
            $content_mode = 'inherit';
        }

        $plans = Eshterakyar_Plan::get_all('all');
        $plan_type = $content_all_plans ? 'all' : 'specific';

        echo '<div class="eshterakyar-post-content-meta">';
        echo '<p><label><input type="checkbox" name="_eshterakyar_content_restricted" value="1" ' . checked(1, $is_restricted, false) . ' /> ';
        echo esc_html__('محدودسازی این نوشته', 'eshterakyar') . '</label></p>';
        echo '<p class="description">' . esc_html__('شورتکد جداکننده [eshterakyar_paywall] را در متن مقاله، بلافاصله بعد از بخش رایگان قرار دهید.', 'eshterakyar') . '</p>';

        echo '<div id="eshterakyar-post-content-options" style="' . ($is_restricted ? '' : 'display:none;') . ' margin-top:12px;">';

        echo '<p><strong>' . esc_html__('حالت نمایش:', 'eshterakyar') . '</strong></p>';
        echo '<p><label><input type="radio" name="_eshterakyar_content_mode" value="inherit" ' . checked('inherit', $content_mode, false) . ' /> ';
        echo esc_html__('از تنظیمات افزونه', 'eshterakyar') . '</label></p>';
        echo '<p><label><input type="radio" name="_eshterakyar_content_mode" value="inline" ' . checked('inline', $content_mode, false) . ' /> ';
        echo esc_html__('پیش‌نمایش + پیام خرید اشتراک', 'eshterakyar') . '</label></p>';
        echo '<p><label><input type="radio" name="_eshterakyar_content_mode" value="redirect" ' . checked('redirect', $content_mode, false) . ' /> ';
        echo esc_html__('هدایت به صفحه خرید پلن‌ها', 'eshterakyar') . '</label></p>';

        echo '<p style="margin-top:12px;"><label><input type="checkbox" name="_eshterakyar_content_override_plan_rules" value="1" ' . checked(1, $override_plan_rules, false) . ' /> ';
        echo esc_html__('فقط پلن‌های انتخاب‌شده (بدون فیلتر دسته/تگ پلن)', 'eshterakyar') . '</label></p>';

        echo '<p><label><input type="radio" name="_eshterakyar_content_plan_type" value="all" ' . checked('all', $plan_type, false) . ' /> ';
        echo esc_html__('همه پلن‌های فعال', 'eshterakyar') . '</label></p>';
        echo '<p><label><input type="radio" name="_eshterakyar_content_plan_type" value="specific" ' . checked('specific', $plan_type, false) . ' /> ';
        echo esc_html__('پلن‌های خاص', 'eshterakyar') . '</label></p>';

        echo '<div id="eshterakyar-post-specific-plans" style="' . ($content_all_plans ? 'display:none;' : '') . ' max-height:160px;overflow:auto;border:1px solid #ddd;padding:8px;background:#fff;">';
        foreach ($plans as $plan) {
            if ($plan->status === 'deleted') {
                continue;
            }
            $checked = in_array((int) $plan->id, array_map('intval', $content_plans), true) ? 'checked' : '';
            echo '<label style="display:block;margin-bottom:6px;"><input type="checkbox" name="_eshterakyar_content_plans[]" value="' . esc_attr($plan->id) . '" ' . $checked . ' /> ';
            echo esc_html($plan->name) . '</label>';
        }
        echo '</div></div></div>';

        ?>
        <script>
        jQuery(function($) {
            $('input[name="_eshterakyar_content_restricted"]').on('change', function() {
                $('#eshterakyar-post-content-options').toggle(this.checked);
            });
            $('input[name="_eshterakyar_content_plan_type"]').on('change', function() {
                $('#eshterakyar-post-specific-plans').toggle($(this).val() === 'specific');
            });
        });
        </script>
        <?php
    }

    public function save_post_meta_box($post_id) {
        if (!isset($_POST['eshterakyar_post_content_meta_nonce']) ||
            !wp_verify_nonce($_POST['eshterakyar_post_content_meta_nonce'], 'eshterakyar_post_content_meta')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $restricted = isset($_POST['_eshterakyar_content_restricted']) ? 1 : 0;
        update_post_meta($post_id, '_eshterakyar_content_restricted', $restricted);

        $all_plans = (isset($_POST['_eshterakyar_content_plan_type']) && $_POST['_eshterakyar_content_plan_type'] === 'all') ? 1 : 0;
        update_post_meta($post_id, '_eshterakyar_content_all_plans', $all_plans);

        $plans = array();
        if (!$all_plans && isset($_POST['_eshterakyar_content_plans']) && is_array($_POST['_eshterakyar_content_plans'])) {
            $plans = array_map('intval', $_POST['_eshterakyar_content_plans']);
        }
        update_post_meta($post_id, '_eshterakyar_content_plans', $plans);

        update_post_meta($post_id, '_eshterakyar_content_override_plan_rules', isset($_POST['_eshterakyar_content_override_plan_rules']) ? 1 : 0);

        $mode = isset($_POST['_eshterakyar_content_mode']) ? sanitize_text_field($_POST['_eshterakyar_content_mode']) : 'inherit';
        if (!in_array($mode, array('inherit', 'inline', 'redirect'), true)) {
            $mode = 'inherit';
        }
        update_post_meta($post_id, '_eshterakyar_content_mode', $mode);
    }
}
