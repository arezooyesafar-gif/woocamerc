<?php

if (!defined('ABSPATH')) {
    exit;
}

$featurebase_enabled = defined('ESHTERAKYAR_FEATUREBASE_ENABLED') ? ESHTERAKYAR_FEATUREBASE_ENABLED : false;
$featurebase_url = defined('ESHTERAKYAR_FEATUREBASE_URL') ? ESHTERAKYAR_FEATUREBASE_URL : '';
$featurebase_widget_enabled = defined('ESHTERAKYAR_FEATUREBASE_WIDGET_ENABLED') ? ESHTERAKYAR_FEATUREBASE_WIDGET_ENABLED : false;
$featurebase_widget_id = defined('ESHTERAKYAR_FEATUREBASE_WIDGET_ID') ? ESHTERAKYAR_FEATUREBASE_WIDGET_ID : '';
?>

<div class="wrap eshterakyar-admin">
    <h1><?php esc_html_e('بازخورد و پیشنهادات', 'eshterakyar'); ?></h1>
    
    <div class="eshterakyar-feedback-container">
        <div class="eshterakyar-feedback-info">
            <h2><?php esc_html_e('گزارش باگ و پیشنهاد ویژگی', 'eshterakyar'); ?></h2>
            <p><?php esc_html_e('از طریق Featurebase می‌توانید:', 'eshterakyar'); ?></p>
            <ul>
                <li><?php esc_html_e('🐛 گزارش باگ و مشکلات', 'eshterakyar'); ?></li>
                <li><?php esc_html_e('💡 پیشنهاد ویژگی‌های جدید', 'eshterakyar'); ?></li>
                <li><?php esc_html_e('👍 رأی دادن به پیشنهادات دیگران', 'eshterakyar'); ?></li>
                <li><?php esc_html_e('📊 مشاهده نقشه راه افزونه', 'eshterakyar'); ?></li>
                <li><?php esc_html_e('📝 مشاهده تغییرات نسخه‌ها (Changelog)', 'eshterakyar'); ?></li>
            </ul>
        </div>
        
        <?php if ($featurebase_enabled && !empty($featurebase_url)): ?>
            <div class="eshterakyar-feedback-embed">
                <h3><?php esc_html_e('پورتال بازخورد Featurebase', 'eshterakyar'); ?></h3>
                <p><?php esc_html_e('می‌توانید مستقیماً از این صفحه به پورتال بازخورد دسترسی داشته باشید:', 'eshterakyar'); ?></p>
                
                <div class="feedback-actions">
                    <a href="<?php echo esc_url($featurebase_url); ?>" target="_blank" class="button button-primary button-large">
                        <?php esc_html_e('باز کردن پورتال بازخورد', 'eshterakyar'); ?>
                    </a>
                    <a href="<?php echo esc_url($featurebase_url . '/roadmap'); ?>" target="_blank" class="button button-secondary button-large">
                        <?php esc_html_e('مشاهده نقشه راه', 'eshterakyar'); ?>
                    </a>
                    <a href="<?php echo esc_url($featurebase_url . '/changelog'); ?>" target="_blank" class="button button-secondary button-large">
                        <?php esc_html_e('مشاهده تغییرات', 'eshterakyar'); ?>
                    </a>
                </div>
                
                <?php if ($featurebase_widget_enabled && !empty($featurebase_widget_id)): ?>
                    <div class="eshterakyar-feedback-widget">
                        <h3><?php esc_html_e('ویجت بازخورد', 'eshterakyar'); ?></h3>
                        <p><?php esc_html_e('ویجت بازخورد Featurebase در زیر نمایش داده می‌شود:', 'eshterakyar'); ?></p>
                        
                        <div id="featurebase-widget-container">
                            <iframe 
                                src="https://feedback.featurebaseapp.com/widget/<?php echo esc_attr($featurebase_widget_id); ?>" 
                                width="100%" 
                                height="600" 
                                frameborder="0">
                            </iframe>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="eshterakyar-feedback-boards">
                <h3><?php esc_html_e('تابلوهای بازخورد', 'eshterakyar'); ?></h3>
                <p><?php esc_html_e('دسترسی سریع به تابلوهای مختلف:', 'eshterakyar'); ?></p>
                
                <div>
                    <div class="board-card">
                        <div class="board-icon">🐛</div>
                        <h4><?php esc_html_e('گزارش باگ', 'eshterakyar'); ?></h4>
                        <p><?php esc_html_e('گزارش مشکلات و خطاها', 'eshterakyar'); ?></p>
                        <a href="<?php echo esc_url($featurebase_url . '/board/bug-reports'); ?>" target="_blank" class="button button-small">
                            <?php esc_html_e('مشاهده', 'eshterakyar'); ?>
                        </a>
                    </div>
                    
                    <div class="board-card">
                        <div class="board-icon">💡</div>
                        <h4><?php esc_html_e('درخواست ویژگی', 'eshterakyar'); ?></h4>
                        <p><?php esc_html_e('پیشنهاد قابلیت‌های جدید', 'eshterakyar'); ?></p>
                        <a href="<?php echo esc_url($featurebase_url . '/board/feature-requests'); ?>" target="_blank" class="button button-small">
                            <?php esc_html_e('مشاهده', 'eshterakyar'); ?>
                        </a>
                    </div>
                    
                    <div class="board-card">
                        <div class="board-icon">🚀</div>
                        <h4><?php esc_html_e('ادغام‌ها', 'eshterakyar'); ?></h4>
                        <p><?php esc_html_e('پیشنهاد ادغام با افزونه‌های دیگر', 'eshterakyar'); ?></p>
                        <a href="<?php echo esc_url($featurebase_url . '/board/integrations'); ?>" target="_blank" class="button button-small">
                            <?php esc_html_e('مشاهده', 'eshterakyar'); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="notice notice-warning">
                <p>
                    <?php esc_html_e('Featurebase فعال نشده است. لطفاً تنظیمات Featurebase را در فایل eshterakyar.php تنظیم کنید.', 'eshterakyar'); ?>
                </p>
                <p>
                    <strong><?php esc_html_e('برای فعال‌سازی:', 'eshterakyar'); ?></strong><br>
                    <?php esc_html_e('1. فایل eshterakyar.php را باز کنید', 'eshterakyar'); ?><br>
                    <?php esc_html_e('2. Constants مربوط به Featurebase را پیدا کنید (خطوط 34-37)', 'eshterakyar'); ?><br>
                    <?php esc_html_e('3. مقادیر ESHTERAKYAR_FEATUREBASE_URL و ESHTERAKYAR_FEATUREBASE_WIDGET_ID را تنظیم کنید', 'eshterakyar'); ?><br>
                    <?php esc_html_e('4. ESHTERAKYAR_FEATUREBASE_ENABLED را true و ESHTERAKYAR_FEATUREBASE_WIDGET_ENABLED را true تنظیم کنید', 'eshterakyar'); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

