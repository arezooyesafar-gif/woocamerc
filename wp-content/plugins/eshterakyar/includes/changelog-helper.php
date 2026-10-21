<?php


if (!defined('ABSPATH')) {
    exit;
}

/**
 * دریافت نسخه فعلی افزونه
 * 
 * @return string نسخه افزونه
 */
function eshterakyar_get_version() {
    return defined('ESHTERAKYAR_VERSION') ? ESHTERAKYAR_VERSION : '1.5.0';
}

/**
 * دریافت changelog به صورت HTML
 * 
 * @return string HTML changelog
 */
function eshterakyar_get_changelog_html() {
    $version = eshterakyar_get_version();
    $changelog_file = ESHTERAKYAR_PLUGIN_DIR . 'CHANGELOG.md';
    
    if (!file_exists($changelog_file)) {
        return '<p>' . esc_html__('فایل changelog یافت نشد.', 'eshterakyar') . '</p>';
    }
    
    $content = file_get_contents($changelog_file);
    
    $content = esc_html($content);
    $content = nl2br($content);
    
    $content = str_replace('نسخه فعلی:', 'نسخه فعلی: ' . esc_html($version), $content);
    
    return '<div class="eshterakyar-changelog">' . $content . '</div>';
}

