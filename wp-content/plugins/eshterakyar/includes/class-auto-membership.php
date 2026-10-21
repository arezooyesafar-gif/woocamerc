<?php

if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Auto_Membership {

    private static $instance = null;

    /** @var array<int, bool> */
    private static $processing_users = array();

    const BATCH_SIZE = 50;
    const DAILY_SYNC_LIMIT = 50;
    const COUNT_CACHE_TTL = 600;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('user_register', array($this, 'on_user_register'), 20);
        add_action('wp', array($this, 'maybe_schedule_daily_sync'));
        add_action('eshterakyar_daily_auto_membership_sync', array($this, 'run_daily_sync'));
        add_action('admin_notices', array($this, 'maybe_show_admin_notice'));
    }

    /**
     * @return array<string, mixed>
     */
    public static function get_options(): array {
        $options = get_option('eshterakyar_options', array());
        return is_array($options) ? $options : array();
    }

    /**
     * @return bool
     */
    public static function is_enabled(): bool {
        $options = self::get_options();
        if (empty($options['auto_membership_enabled'])) {
            return false;
        }
        return (bool) self::get_configured_plan();
    }

    /**
     * @return object|null
     */
    public static function get_configured_plan() {
        $options = self::get_options();
        $plan_id = isset($options['auto_membership_plan_id']) ? absint($options['auto_membership_plan_id']) : 0;
        if ($plan_id <= 0) {
            return null;
        }
        $plan = Eshterakyar_Plan::get_by_id($plan_id);
        if (!$plan || !isset($plan->status) || $plan->status !== 'active') {
            return null;
        }
        return $plan;
    }

    /**
     * @return array<int, string>
     */
    public static function get_excluded_roles(): array {
        $options = self::get_options();
        $raw = isset($options['auto_membership_exclude_roles'])
            ? $options['auto_membership_exclude_roles']
            : 'administrator,shop_manager';
        $roles = array_map('trim', explode(',', (string) $raw));
        $roles = array_filter($roles, static function ($role) {
            return $role !== '';
        });
        return array_values(array_unique($roles));
    }

    /**
     * @param int    $user_id
     * @param string $context registration|batch|cron|count
     * @return bool
     */
    public static function is_user_eligible(int $user_id, string $context = 'batch'): bool {
        $user_id = absint($user_id);
        if ($user_id <= 0) {
            return false;
        }

        if (!self::is_enabled()) {
            return false;
        }

        $options = self::get_options();

        if ($context === 'registration' && empty($options['auto_membership_new_users'])) {
            return false;
        }

        if (in_array($context, array('batch', 'cron', 'count'), true) && empty($options['auto_membership_existing_users'])) {
            return false;
        }

        $user = get_user_by('ID', $user_id);
        if (!$user) {
            return false;
        }

        $excluded_roles = self::get_excluded_roles();
        if (!empty($excluded_roles) && array_intersect($excluded_roles, (array) $user->roles)) {
            return false;
        }

        if (Eshterakyar_Subscription::get_active_subscription($user_id)) {
            return false;
        }

        if (!empty($options['auto_membership_skip_reserved']) && self::user_has_reserved_subscription($user_id)) {
            return false;
        }

        return (bool) apply_filters('eshterakyar_auto_membership_eligible', true, $user_id, $context);
    }

    /**
     * @param int $user_id
     * @return bool
     */
    private static function user_has_reserved_subscription(int $user_id): bool {
        $reserved = Eshterakyar_Subscription::get_user_subscriptions($user_id, 'reserved');
        return !empty($reserved);
    }

    /**
     * @param int    $user_id
     * @param string $context
     * @return int|false
     */
    public static function maybe_assign(int $user_id, string $context = 'batch') {
        $user_id = absint($user_id);
        if ($user_id <= 0) {
            return false;
        }

        if (isset(self::$processing_users[$user_id])) {
            return false;
        }

        if (!self::is_user_eligible($user_id, $context)) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('عضویت خودکار: کاربر واجد شرایط نیست', array(
                    'user_id' => $user_id,
                    'context' => $context,
                    'feature' => 'auto_membership',
                ));
            }
            return false;
        }

        $plan = self::get_configured_plan();
        if (!$plan) {
            return false;
        }

        self::$processing_users[$user_id] = true;

        do_action('eshterakyar_before_auto_membership_assign', $user_id, (int) $plan->id, $context);

        $subscription_id = Eshterakyar_Subscription::create($user_id, (int) $plan->id, null);

        if (!$subscription_id) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('عضویت خودکار: خطا در ایجاد اشتراک', array(
                    'user_id' => $user_id,
                    'plan_id' => (int) $plan->id,
                    'context' => $context,
                    'feature' => 'auto_membership',
                ));
            }
            unset(self::$processing_users[$user_id]);
            return false;
        }

        self::maybe_send_welcome_notification($user_id, (int) $subscription_id);

        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('عضویت خودکار: اشتراک ایجاد شد', array(
                'user_id' => $user_id,
                'subscription_id' => (int) $subscription_id,
                'plan_id' => (int) $plan->id,
                'context' => $context,
                'feature' => 'auto_membership',
            ));
        }

        do_action('eshterakyar_auto_membership_assigned', (int) $subscription_id, $user_id, (int) $plan->id, $context);

        delete_transient('eshterakyar_auto_membership_eligible_count');

        unset(self::$processing_users[$user_id]);

        return (int) $subscription_id;
    }

    /**
     * @param int $user_id
     * @param int $subscription_id
     */
    private static function maybe_send_welcome_notification(int $user_id, int $subscription_id): void {
        $options = self::get_options();
        if (empty($options['auto_membership_send_welcome']) || empty($options['enable_notifications'])) {
            return;
        }
        if (class_exists('Eshterakyar_Notifications')) {
            Eshterakyar_Notifications::send_purchase_notification($user_id, $subscription_id);
        }
    }

    /**
     * @param int $user_id
     */
    public function on_user_register($user_id): void {
        self::maybe_assign((int) $user_id, 'registration');
    }

    /**
     * @param bool $force_refresh
     * @return int
     */
    public static function count_eligible_users(bool $force_refresh = false): int {
        if (!$force_refresh) {
            $cached = get_transient('eshterakyar_auto_membership_eligible_count');
            if ($cached !== false) {
                return (int) $cached;
            }
        }

        $count = 0;
        $offset = 0;
        $batch_size = 100;

        while (true) {
            $users = get_users(array(
                'fields'  => 'ID',
                'number'  => $batch_size,
                'offset'  => $offset,
                'orderby' => 'ID',
                'order'   => 'ASC',
            ));

            if (empty($users)) {
                break;
            }

            foreach ($users as $user_id) {
                if (self::is_user_eligible((int) $user_id, 'count')) {
                    $count++;
                }
            }

            $offset += $batch_size;
            if (count($users) < $batch_size) {
                break;
            }
        }

        set_transient('eshterakyar_auto_membership_eligible_count', $count, self::COUNT_CACHE_TTL);

        return $count;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array<string, int|bool>
     */
    public static function process_batch(int $offset = 0, int $limit = self::BATCH_SIZE): array {
        $offset = max(0, $offset);
        $limit = max(1, min(200, $limit));

        $stats = array(
            'processed' => 0,
            'assigned'  => 0,
            'skipped'   => 0,
            'failed'    => 0,
            'next_offset' => $offset,
            'done'      => false,
        );

        if (!self::is_enabled()) {
            return $stats;
        }

        $options = self::get_options();
        if (empty($options['auto_membership_existing_users'])) {
            return $stats;
        }

        $users = get_users(array(
            'fields'  => 'ID',
            'number'  => $limit,
            'offset'  => $offset,
            'orderby' => 'ID',
            'order'   => 'ASC',
        ));

        if (empty($users)) {
            $stats['done'] = true;
            update_option('eshterakyar_auto_membership_batch_completed_at', current_time('mysql'));
            delete_transient('eshterakyar_auto_membership_eligible_count');
            return $stats;
        }

        foreach ($users as $user_id) {
            $user_id = (int) $user_id;
            $stats['processed']++;

            if (!self::is_user_eligible($user_id, 'batch')) {
                $stats['skipped']++;
                continue;
            }

            $result = self::maybe_assign($user_id, 'batch');
            if ($result) {
                $stats['assigned']++;
            } else {
                $stats['failed']++;
            }
        }

        $stats['next_offset'] = $offset + count($users);
        if (count($users) < $limit) {
            $stats['done'] = true;
            update_option('eshterakyar_auto_membership_batch_completed_at', current_time('mysql'));
            delete_transient('eshterakyar_auto_membership_eligible_count');
        }

        return $stats;
    }

    public function maybe_schedule_daily_sync(): void {
        $options = self::get_options();
        $should_run = self::is_enabled() && !empty($options['auto_membership_daily_sync']);

        if ($should_run) {
            if (!wp_next_scheduled('eshterakyar_daily_auto_membership_sync')) {
                wp_schedule_event(time(), 'daily', 'eshterakyar_daily_auto_membership_sync');
            }
            return;
        }

        $timestamp = wp_next_scheduled('eshterakyar_daily_auto_membership_sync');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'eshterakyar_daily_auto_membership_sync');
        }
    }

    public function run_daily_sync(): void {
        $options = self::get_options();
        if (!self::is_enabled() || empty($options['auto_membership_existing_users']) || empty($options['auto_membership_daily_sync'])) {
            return;
        }

        $offset = 0;
        $assigned = 0;

        while ($assigned < self::DAILY_SYNC_LIMIT) {
            $users = get_users(array(
                'fields'  => 'ID',
                'number'  => 50,
                'offset'  => $offset,
                'orderby' => 'ID',
                'order'   => 'ASC',
            ));

            if (empty($users)) {
                break;
            }

            foreach ($users as $user_id) {
                if ($assigned >= self::DAILY_SYNC_LIMIT) {
                    break 2;
                }

                if (!self::is_user_eligible((int) $user_id, 'cron')) {
                    continue;
                }

                $result = self::maybe_assign((int) $user_id, 'cron');
                if ($result) {
                    $assigned++;
                }
            }

            $offset += count($users);
            if (count($users) < 50) {
                break;
            }
        }

        if ($assigned > 0) {
            delete_transient('eshterakyar_auto_membership_eligible_count');
        }
    }

    public function maybe_show_admin_notice(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen || strpos($screen->id, 'eshterakyar') === false) {
            return;
        }

        $options = self::get_options();
        if (empty($options['auto_membership_enabled'])) {
            return;
        }

        $plan_id = isset($options['auto_membership_plan_id']) ? absint($options['auto_membership_plan_id']) : 0;
        if ($plan_id <= 0) {
            echo '<div class="notice notice-warning eshterakyar-notice"><p>';
            esc_html_e('عضویت خودکار فعال است اما پلن اشتراکی انتخاب نشده است. لطفاً در تنظیمات → عضویت خودکار یک پلن فعال انتخاب کنید.', 'eshterakyar');
            echo '</p></div>';
            return;
        }

        if (!self::get_configured_plan()) {
            echo '<div class="notice notice-warning eshterakyar-notice"><p>';
            esc_html_e('پلن انتخاب‌شده برای عضویت خودکار غیرفعال یا حذف شده است. لطفاً پلن دیگری انتخاب کنید.', 'eshterakyar');
            echo '</p></div>';
        }
    }
}
