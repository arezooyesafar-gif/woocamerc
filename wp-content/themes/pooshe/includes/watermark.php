<?php

defined('ABSPATH') || exit;

function pooshe_watermark_is_enabled()
{
    return function_exists('pooshe_opt') && defined('STYLE_OPTS') && pooshe_opt(STYLE_OPTS, 'watermark_enabled', false);
}

function pooshe_watermark_font_path()
{
    $fonts = [
        get_template_directory() . '/dist/font/iransans/fonts/ttf/IRANSansWeb(FaNum).ttf',
        get_template_directory() . '/dist/font/IRANYekan/fonts/ttf/IRANYekanWebRegular.ttf',
    ];
    foreach ($fonts as $font) {
        if (file_exists($font)) return $font;
    }
    return '';
}

function pooshe_watermark_text($order_id, $customer_email)
{
    $template = pooshe_opt(STYLE_OPTS, 'watermark_text', 'خریدار: {name} - سفارش: {order}');
    $order = wc_get_order($order_id);
    $name = $order ? trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()) : '';
    $date = $order ? $order->get_date_created()->date_i18n('Y/m/d') : '';

    $replacements = [
        '{name}' => $name ?: $customer_email,
        '{email}' => $customer_email,
        '{order}' => $order_id,
        '{date}' => $date,
    ];

    return str_replace(array_keys($replacements), array_values($replacements), $template);
}

function pooshe_watermark_apply($file_path, $order_id, $customer_email)
{
    if (!file_exists($file_path) || !is_file($file_path)) return $file_path;

    if (!extension_loaded('gd') || !function_exists('getimagesize')) return $file_path;

    $info = @getimagesize($file_path);
    if (!$info) return $file_path;

    $mime = $info['mime'];
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mime, $allowed)) return $file_path;

    $font = pooshe_watermark_font_path();
    if (empty($font)) return $file_path;

    switch ($mime) {
        case 'image/jpeg':
            $src = @imagecreatefromjpeg($file_path);
            break;
        case 'image/png':
            $src = @imagecreatefrompng($file_path);
            break;
        case 'image/gif':
            $src = @imagecreatefromgif($file_path);
            break;
        case 'image/webp':
            if (!function_exists('imagecreatefromwebp')) return $file_path;
            $src = @imagecreatefromwebp($file_path);
            break;
        default:
            return $file_path;
    }

    if (!$src) return $file_path;

    $w = imagesx($src);
    $h = imagesy($src);

    $text = pooshe_watermark_text($order_id, $customer_email);

    $font_size = max(10, min(40, round($w / 22)));
    $opacity = max(1, min(100, intval(pooshe_opt(STYLE_OPTS, 'watermark_opacity', 15))));
    $alpha = 127 - round(127 * $opacity / 100);

    $overlay = imagecreatetruecolor($w, $h);
    imagesavealpha($overlay, true);
    $transparent = imagecolorallocatealpha($overlay, 0, 0, 0, 127);
    imagefill($overlay, 0, 0, $transparent);

    $text_color = imagecolorallocatealpha($overlay, 255, 255, 255, $alpha);

    $angle = -30;

    $bbox = imagettfbbox($font_size, 0, $font, $text);
    $text_w = abs($bbox[2] - $bbox[0]);
    $text_h = abs($bbox[7] - $bbox[1]);

    $step_x = round($text_w * 2.2);
    $step_y = round($text_h * 3.5);
    if ($step_x < 1) $step_x = $text_w + 20;
    if ($step_y < 1) $step_y = $text_h + 20;

    for ($x = -$text_w; $x < $w + $text_w; $x += $step_x) {
        for ($y = -$text_h; $y < $h + $text_h; $y += $step_y) {
            imagettftext($overlay, $font_size, $angle, $x, $y, $text_color, $font, $text);
        }
    }

    imagecopy($src, $overlay, 0, 0, 0, 0, $w, $h);
    imagedestroy($overlay);

    $temp = get_temp_dir() . 'pooshe_wm_' . uniqid('', true) . '_' . basename($file_path);

    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($src, $temp, 92);
            break;
        case 'image/png':
            imagepng($src, $temp, 9);
            break;
        case 'image/gif':
            imagegif($src, $temp);
            break;
        case 'image/webp':
            if (function_exists('imagewebp')) {
                imagewebp($src, $temp, 85);
            } else {
                imagedestroy($src);
                return $file_path;
            }
            break;
    }

    imagedestroy($src);

    return $temp;
}

$GLOBALS['pooshe_watermark_temp_files'] = [];

add_action('woocommerce_download_product', 'pooshe_watermark_capture_context', 5, 6);
function pooshe_watermark_capture_context($customer_email, $order_key, $download_id, $product_id, $order_id, $download)
{
    if (!pooshe_watermark_is_enabled()) return;
    $GLOBALS['pooshe_watermark_context'] = compact('customer_email', 'order_id', 'product_id', 'download_id');
}

add_filter('woocommerce_file_download_path', 'pooshe_watermark_file_path', 10, 3);
function pooshe_watermark_file_path($file_path, $download_id = 0, $customer_email = '')
{
    if (!pooshe_watermark_is_enabled()) return $file_path;
    if (filter_var($file_path, FILTER_VALIDATE_URL)) return $file_path;

    $ctx = isset($GLOBALS['pooshe_watermark_context']) ? $GLOBALS['pooshe_watermark_context'] : [];
    $order_id = !empty($ctx['order_id']) ? $ctx['order_id'] : 0;
    if (empty($customer_email) && !empty($ctx['customer_email'])) {
        $customer_email = $ctx['customer_email'];
    }

    $watermarked = pooshe_watermark_apply($file_path, $order_id, $customer_email);

    if ($watermarked !== $file_path) {
        $GLOBALS['pooshe_watermark_temp_files'][] = $watermarked;
    }

    return $watermarked;
}

add_action('shutdown', 'pooshe_watermark_cleanup');
function pooshe_watermark_cleanup()
{
    if (!empty($GLOBALS['pooshe_watermark_temp_files'])) {
        foreach ($GLOBALS['pooshe_watermark_temp_files'] as $file) {
            if (file_exists($file)) @unlink($file);
        }
    }
}
