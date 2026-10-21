<?php
defined('ABSPATH') || exit;

$text_align = is_rtl() ? 'right' : 'left';
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .pooshe-email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .pooshe-email-header {
            background: #ff9e22;
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        .pooshe-email-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
        }
        .pooshe-email-body {
            padding: 30px;
            text-align: <?php echo esc_attr($text_align); ?>;
        }
        .pooshe-email-body p {
            font-size: 15px;
            line-height: 1.8;
            color: #444;
            margin: 0 0 15px;
        }
        .pooshe-email-product {
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .pooshe-email-product h3 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #222;
        }
        .pooshe-email-btn {
            display: inline-block;
            background: #ff9e22;
            color: #fff !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            margin-top: 15px;
        }
        .pooshe-email-btn:hover {
            background: #ff8d00;
        }
        .pooshe-email-footer {
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="pooshe-email-wrapper">
        <div class="pooshe-email-header">
            <h1><?php echo esc_html($email_heading); ?></h1>
        </div>
        <div class="pooshe-email-body">
            <p>سلام،</p>
            <p>
                محصول <strong><?php echo esc_html($product_name); ?></strong> که قبلاً آن را خریداری کرده‌اید،
                به‌روزرسانی شده است. نسخه جدید این محصول هم‌اکنون برای دانلود در دسترس می‌باشد.
            </p>

            <div class="pooshe-email-product">
                <h3><?php echo esc_html($product_name); ?></h3>
                <a href="<?php echo esc_url($product_link); ?>" class="pooshe-email-btn">
                    مشاهده و دانلود نسخه جدید
                </a>
            </div>

            <p style="font-size: 13px; color: #888;">
                اگر دکمه بالا کار نمی‌کند، لینک زیر را در مرورگر خود کپی کنید:<br>
                <a href="<?php echo esc_url($product_link); ?>" style="color: #ff9e22;">
                    <?php echo esc_url($product_link); ?>
                </a>
            </p>
        </div>
        <div class="pooshe-email-footer">
            <?php echo get_bloginfo('name'); ?> &mdash; کلیه حقوق محفوظ است.
        </div>
    </div>
</body>
</html>
