<?php


if (!defined('ABSPATH')) {
    exit;
}

$current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 'eshterakyar';

$show_logs_in_nav = false;
if (defined('ESHTERAKYAR_DEBUG_LOGS_ENABLED')) {
    $show_logs_in_nav = ESHTERAKYAR_DEBUG_LOGS_ENABLED ? true : false;
} else {
    $show_logs_in_nav = (defined('WP_DEBUG') && WP_DEBUG);
}

$page_titles = array(
    'eshterakyar' => __('داشبورد', 'eshterakyar'),
    'eshterakyar-plans' => __('پلن‌های اشتراک', 'eshterakyar'),
    'eshterakyar-customers' => __('مشتریان', 'eshterakyar'),
    'eshterakyar-subscriptions' => __('اشتراک‌ها', 'eshterakyar'),
    'eshterakyar-downloads' => __('دانلودها', 'eshterakyar'),
    'eshterakyar-settings' => __('تنظیمات', 'eshterakyar'),
    'eshterakyar-feedback' => __('بازخورد', 'eshterakyar'),
);
if ($show_logs_in_nav) {
    $page_titles['eshterakyar-logs'] = __('لاگ‌ها', 'eshterakyar');
}

$page_title = isset($page_titles[$current_page]) ? $page_titles[$current_page] : __('اشتراک یار', 'eshterakyar');

$nav_items = array(
    'eshterakyar' => array(
        'title' => __('داشبورد', 'eshterakyar'),
        'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>',
        'url' => admin_url('admin.php?page=eshterakyar')
    ),
    'eshterakyar-customers' => array(
        'title' => __('مشتریان', 'eshterakyar'),
        'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>',
        'url' => admin_url('admin.php?page=eshterakyar-customers')
    ),
    'eshterakyar-subscriptions' => array(
        'title' => __('اشتراک‌ها', 'eshterakyar'),
        'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg>',
        'url' => admin_url('admin.php?page=eshterakyar-subscriptions')
    ),
    'eshterakyar-plans' => array(
        'title' => __('پلن‌ها', 'eshterakyar'),
        'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>',
        'url' => admin_url('admin.php?page=eshterakyar-plans')
    ),
    'eshterakyar-downloads' => array(
        'title' => __('دانلودها', 'eshterakyar'),
        'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>',
        'url' => admin_url('admin.php?page=eshterakyar-downloads')
    ),
    'eshterakyar-settings' => array(
        'title' => __('تنظیمات', 'eshterakyar'),
        'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94L14.4 2.81c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.12.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.07.62-.07.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.12-.57 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>',
        'url' => admin_url('admin.php?page=eshterakyar-settings')
    ),
);
if ($show_logs_in_nav) {
    $nav_items['eshterakyar-logs'] = array(
        'title' => __('لاگ‌ها', 'eshterakyar'),
        'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>',
        'url' => admin_url('admin.php?page=eshterakyar-logs')
    );
}
?>

<div class="eshterakyar-page-wrapper">
    <div class="eshterakyar-page-header">
        <div class="page-header-content">
            <div class="header-logo-section">
                <div class="header-logo">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                        <circle cx="20" cy="20" r="18" stroke="white" stroke-width="2"/>
                        <path d="M20 10 L20 30 M10 20 L30 20" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="20" cy="20" r="6" fill="white"/>
                    </svg>
                </div>
                <div class="header-brand">
                    <h1 class="brand-title"><?php echo esc_html($page_title); ?></h1>
                    <p class="brand-tagline"><?php esc_html_e('قدرتمندترین ابزار برای مدیریت اشتراک‌های دانلود در وردپرس', 'eshterakyar'); ?></p>
                </div>
            </div>
            <div class="header-version">
                <?php
                /* translators: %s: Plugin version number */
                printf(esc_html__('نسخه: %s', 'eshterakyar'), esc_html(ESHTERAKYAR_VERSION));
                ?>
            </div>
        </div>
    </div>

    <div class="eshterakyar-nav-bar">
        <nav class="nav-bar-items">
            <?php foreach ($nav_items as $page_key => $nav_item): ?>
                <a href="<?php echo esc_url($nav_item['url']); ?>" 
                   class="nav-bar-item <?php echo $current_page == $page_key ? 'active' : ''; ?>"
                   title="<?php echo esc_attr($nav_item['title']); ?>">
                    <span class="nav-text"><?php echo esc_html($nav_item['title']); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
    <div class="eshterakyar-page-main">
        <div class="page-content">
            <?php
            if (isset($_GET['created']) || isset($_GET['updated']) || isset($_GET['deleted']) || isset($_GET['duplicated']) || isset($_GET['cancelled']) || isset($_GET['activated']) || isset($_GET['deactivated']) || isset($_GET['extended']) || isset($_GET['bulk_updated']) || isset($_GET['credits_reset']) || isset($_GET['settings-updated']) || isset($_GET['subscription_created'])) {
                $message_type = 'success';
                $message = '';
                
                if (isset($_GET['subscription_created'])) {
                    $message = __('اشتراک با موفقیت ایجاد شد.', 'eshterakyar');
                } elseif (isset($_GET['duplicated'])) {
                    $message = __('پلن با موفقیت کپی شد. می‌توانید آن را ویرایش کنید.', 'eshterakyar');
                } elseif (isset($_GET['created'])) {
                    $message = __('پلن با موفقیت ایجاد شد.', 'eshterakyar');
                } elseif (isset($_GET['updated'])) {
                    $message = __('تغییرات با موفقیت ذخیره شد.', 'eshterakyar');
                } elseif (isset($_GET['deleted'])) {
                    $message = __('با موفقیت حذف شد.', 'eshterakyar');
                } elseif (isset($_GET['cancelled'])) {
                    $message = __('اشتراک با موفقیت لغو شد.', 'eshterakyar');
                } elseif (isset($_GET['activated'])) {
                    $message = __('اشتراک با موفقیت فعال شد.', 'eshterakyar');
                } elseif (isset($_GET['deactivated'])) {
                    $message = __('اشتراک با موفقیت غیرفعال شد.', 'eshterakyar');
                } elseif (isset($_GET['extended'])) {
                    $message = __('اشتراک با موفقیت تمدید شد.', 'eshterakyar');
                } elseif (isset($_GET['bulk_updated'])) {
                    $message = __('عملیات دسته‌ای با موفقیت انجام شد.', 'eshterakyar');
                } elseif (isset($_GET['credits_reset'])) {
                    $message = __('اعتبار اشتراک با موفقیت ریست شد.', 'eshterakyar');
                } elseif (isset($_GET['settings-updated'])) {
                    $message = __('تنظیمات با موفقیت ذخیره شد.', 'eshterakyar');
                }
                
                if ($message):
            ?>
                <div class="eshterakyar-notice eshterakyar-notice-success">
                    <div class="notice-content">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                        </svg>
                        <span><?php echo esc_html($message); ?></span>
                    </div>
                </div>
            <?php
                endif;
            }
            
            if (isset($_GET['error'])): 
                $error_message = __('خطا در انجام عملیات.', 'eshterakyar');
                if (isset($_GET['error_msg']) && current_user_can('manage_options')) {
                    $error_message = __('خطا در انجام عملیات.', 'eshterakyar') . ' ' . esc_html(urldecode($_GET['error_msg']));
                }
            ?>
                <div class="eshterakyar-notice eshterakyar-notice-error">
                    <div class="notice-content">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                        <span><?php echo esc_html($error_message); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <div class="page-content-inner">
