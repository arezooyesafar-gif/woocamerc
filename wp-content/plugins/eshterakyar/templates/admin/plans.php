<?php

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="eshterakyar-plans-page">
        <?php if (!isset($_GET['action']) && !isset($_GET['plan_id'])): ?>
            <div class="eshterakyar-plans-header">
                <div class="plans-header-content">
                    <div>
                        <h2 class="plans-title"><?php esc_html_e('پلن‌های اشتراک', 'eshterakyar'); ?></h2>
                        <p class="plans-subtitle"><?php esc_html_e('مدیریت و تنظیم پلن‌های اشتراک برای محصولات - می‌توانید با drag & drop ترتیب پلن‌ها را تغییر دهید', 'eshterakyar'); ?></p>
                    </div>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-plans&action=add')); ?>" class="eshterakyar-add-plan-btn">
                        <span class="dashicons dashicons-plus-alt2"></span>
                        <?php esc_html_e('افزودن پلن جدید', 'eshterakyar'); ?>
                    </a>
                </div>
            </div>
            
            <div class="eshterakyar-plans-grid" id="eshterakyar-sortable-plans">
                <?php if (empty($plans) || !is_array($plans) || count($plans) === 0): ?>
                    <div class="eshterakyar-empty-state">
                        <div class="empty-state-icon">📦</div>
                        <h3><?php esc_html_e('هنوز پلنی ایجاد نشده است', 'eshterakyar'); ?></h3>
                        <p><?php esc_html_e('برای شروع، اولین پلن اشتراک خود را ایجاد کنید', 'eshterakyar'); ?></p>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-plans&action=add')); ?>" class="button button-primary button-large">
                            <?php esc_html_e('افزودن اولین پلن', 'eshterakyar'); ?>
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($plans as $plan): ?>
                        <div class="eshterakyar-plan-card <?php echo esc_attr($plan->status); ?>" data-plan-id="<?php echo esc_attr($plan->id); ?>">
                            <div class="plan-card-header">
                                <div class="plan-card-title-section">
                                    <h3 class="plan-card-name"><?php echo esc_html($plan->name); ?></h3>
                                    <span class="plan-card-status status-badge status-<?php echo esc_attr($plan->status); ?>">
                                        <?php echo esc_html($plan->status == 'active' ? __('فعال', 'eshterakyar') : __('غیرفعال', 'eshterakyar')); ?>
                                    </span>
                                </div>
                                <div class="plan-card-quick-actions" style="display:flex; align-items:center; gap:10px;">
                                    <?php
                                    $is_recommended = isset($plan->is_recommended) && (int) $plan->is_recommended === 1;
                                    $star = $is_recommended ? 'dashicons-star-filled' : 'dashicons-star-empty';
                                    $star_title = $is_recommended ? __('پلن پیشنهادی (کلیک برای برداشتن)', 'eshterakyar') : __('پیشنهادی کردن این پلن', 'eshterakyar');
                                    ?>
                                    <button type="button"
                                            class="button button-small eshterakyar-recommend-plan-toggle"
                                            data-plan-id="<?php echo esc_attr($plan->id); ?>"
                                            data-recommended="<?php echo esc_attr($is_recommended ? 1 : 0); ?>"
                                            title="<?php echo esc_attr($star_title); ?>"
                                            style="display:inline-flex; align-items:center; gap:6px;">
                                        <span class="dashicons <?php echo esc_attr($star); ?>"></span>
                                        <span><?php esc_html_e('پیشنهادی', 'eshterakyar'); ?></span>
                                    </button>
                                </div>
                                <div class="plan-card-price">
                                    <?php 
                                    $price_toman = intval($plan->price);
                                    $has_sale_price = isset($plan->sale_price) && $plan->sale_price > 0 && $plan->sale_price < $price_toman;
                                    
                                    if ($has_sale_price):
                                        $sale_price_toman = intval($plan->sale_price);
                                        $discount_percentage = round((($price_toman - $sale_price_toman) / $price_toman) * 100);
                                    ?>
                                        <div class="plan-price-wrapper-admin" style="display: flex; flex-direction: column; gap: 4px;">
                                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                <span style="font-size: 24px; font-weight: 700; color: #ef4444;">
                                                    <?php echo esc_html(number_format_i18n($sale_price_toman, 0)); ?> <?php esc_html_e('تومان', 'eshterakyar'); ?>
                                                </span>
                                                <span style="background: #ef4444; color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">
                                                    <?php echo esc_html($discount_percentage); ?>% <?php esc_html_e('تخفیف', 'eshterakyar'); ?>
                                                </span>
                                            </div>
                                            <span style="font-size: 16px; color: #9ca3af; text-decoration: line-through;">
                                                <?php echo esc_html(number_format_i18n($price_toman, 0)); ?> <?php esc_html_e('تومان', 'eshterakyar'); ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span style="font-size: 24px; font-weight: 700; color: #1a1a1a;">
                                            <?php echo esc_html(number_format_i18n($price_toman, 0)); ?> <?php esc_html_e('تومان', 'eshterakyar'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="plan-card-body">
                                <div class="plan-card-features">
                                    <div class="plan-feature-item">
                                        <span class="feature-icon">📅</span>
                                        <div class="feature-content">
                                            <span class="feature-label"><?php esc_html_e('مدت زمان', 'eshterakyar'); ?></span>
                                            <span class="feature-value"><?php echo esc_html(number_format_i18n($plan->duration_days)); ?> <?php esc_html_e('روز', 'eshterakyar'); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="plan-feature-item">
                                        <span class="feature-icon">⬇️</span>
                                        <div class="feature-content">
                                            <span class="feature-label"><?php esc_html_e('دانلود روزانه', 'eshterakyar'); ?></span>
                                            <span class="feature-value">
                                                <?php 
                                                $daily_limit = isset($plan->daily_limit) ? intval($plan->daily_limit) : 0;
                                                echo esc_html($daily_limit > 0 ? number_format_i18n($daily_limit) : __('نامحدود', 'eshterakyar')); 
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="plan-feature-item">
                                        <span class="feature-icon">📊</span>
                                        <div class="feature-content">
                                            <span class="feature-label"><?php esc_html_e('دانلود کلی', 'eshterakyar'); ?></span>
                                            <span class="feature-value">
                                                <?php 
                                                $total_limit = isset($plan->total_limit) ? intval($plan->total_limit) : 0;
                                                echo esc_html($total_limit > 0 ? number_format_i18n($total_limit) : __('نامحدود', 'eshterakyar')); 
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php 
                                    $download_access_days = isset($plan->download_access_days) ? intval($plan->download_access_days) : 0;
                                    $max_downloads_per_product = isset($plan->max_downloads_per_product) ? intval($plan->max_downloads_per_product) : 0;
                                    if ($download_access_days > 0 || $max_downloads_per_product > 0): 
                                    ?>
                                    <div class="plan-feature-item">
                                        <span class="feature-icon">🔗</span>
                                        <div class="feature-content">
                                            <span class="feature-label"><?php esc_html_e('دسترسی به فایل', 'eshterakyar'); ?></span>
                                            <span class="feature-value">
                                                <?php 
                                                if ($download_access_days > 0 && $max_downloads_per_product > 0) {
                                                    echo esc_html(number_format_i18n($download_access_days) . ' ' . __('روز', 'eshterakyar') . '، ' . number_format_i18n($max_downloads_per_product) . ' ' . __('بار هر محصول', 'eshterakyar'));
                                                } elseif ($download_access_days > 0) {
                                                    echo esc_html(number_format_i18n($download_access_days) . ' ' . __('روز از اولین دانلود', 'eshterakyar'));
                                                } else {
                                                    echo esc_html(number_format_i18n($max_downloads_per_product) . ' ' . __('بار هر محصول', 'eshterakyar'));
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($plan->description)): ?>
                                    <div class="plan-card-description">
                                        <p><?php echo esc_html(wp_trim_words(wp_strip_all_tags($plan->description), 20)); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="plan-card-footer">
                                <div class="plan-card-actions">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-plans&plan_id=' . $plan->id)); ?>" class="plan-action-btn plan-edit-btn">
                                        <span class="dashicons dashicons-edit"></span>
                                        <?php esc_html_e('ویرایش', 'eshterakyar'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=eshterakyar-plans&action=duplicate&plan_id=' . $plan->id), 'duplicate_plan_' . $plan->id)); ?>" class="plan-action-btn plan-duplicate-btn" title="<?php esc_attr_e('کپی این پلن با تمام تنظیمات', 'eshterakyar'); ?>">
                                        <span class="dashicons dashicons-admin-page"></span>
                                        <?php esc_html_e('کپی', 'eshterakyar'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=eshterakyar-plans&action=delete&plan_id=' . $plan->id), 'delete_plan_' . $plan->id)); ?>" class="plan-action-btn plan-delete-btn plan-delete-btn-icon" title="<?php esc_attr_e('حذف پلن', 'eshterakyar'); ?>" onclick="return confirm('<?php echo esc_js(__('آیا مطمئن هستید؟', 'eshterakyar')); ?>');">
                                        <span class="dashicons dashicons-trash"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php 
        $show_form = (isset($_GET['action']) && $_GET['action'] == 'add') || isset($_GET['plan_id']);
        
        if ($show_form): 
            if (isset($_GET['plan_id']) && !$plan): ?>
                <div class="eshterakyar-notice notice notice-error">
                    <p><?php esc_html_e('پلن مورد نظر یافت نشد.', 'eshterakyar'); ?></p>
                    <p><a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-plans')); ?>"><?php esc_html_e('بازگشت به لیست پلن‌ها', 'eshterakyar'); ?></a></p>
                </div>
            <?php else: ?>
            <div class="eshterakyar-plan-form-wrapper">
                <div class="plan-form-header">
                    <div>
                        <h2 class="plan-form-title"><?php echo esc_html($plan ? __('ویرایش پلن', 'eshterakyar') : __('افزودن پلن جدید', 'eshterakyar')); ?></h2>
                        <p class="plan-form-subtitle"><?php echo esc_html($plan ? __('ویرایش اطلاعات پلن اشتراک', 'eshterakyar') : __('ایجاد پلن اشتراک جدید برای محصولات', 'eshterakyar')); ?></p>
                    </div>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-plans')); ?>" class="plan-form-back-btn">
                        <span class="dashicons dashicons-arrow-right-alt"></span>
                        <?php esc_html_e('بازگشت به لیست', 'eshterakyar'); ?>
                    </a>
                </div>
                
                <div class="eshterakyar-plan-form">
                
                <form method="post" action="" id="eshterakyar-plan-form">
                    <?php wp_nonce_field('eshterakyar_plan_nonce'); ?>
                    <input type="hidden" name="action" value="save_plan">
                    <?php if ($plan): ?>
                        <input type="hidden" name="plan_id" value="<?php echo esc_attr($plan->id); ?>">
                    <?php endif; ?>
                    
                    <table class="form-table">
                        <tr>
                            <th><label for="name"><?php esc_html_e('نام پلن', 'eshterakyar'); ?></label></th>
                            <td><input type="text" name="name" id="name" value="<?php echo $plan ? esc_attr($plan->name) : ''; ?>" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="description"><?php esc_html_e('توضیحات', 'eshterakyar'); ?></label></th>
                            <td><?php wp_editor($plan ? $plan->description : '', 'description', array('textarea_name' => 'description', 'textarea_rows' => 5)); ?></td>
                        </tr>
                        <tr>
                            <th><label for="price"><?php esc_html_e('قیمت (تومان)', 'eshterakyar'); ?></label></th>
                            <td>
                                <input type="number" name="price" id="price" value="<?php echo $plan ? esc_attr(intval($plan->price)) : ''; ?>" step="1" min="0" class="regular-text" required>
                                <p class="description"><?php esc_html_e('قیمت به تومان و بدون اعشار. این قیمت فقط برای نمایش در صفحه پلن‌ها استفاده می‌شود. قیمت واقعی که کاربر پرداخت می‌کند، همان قیمت محصول ووکامرس است که در بخش "محصول مرتبط" انتخاب می‌کنید. توصیه می‌شود این دو قیمت یکسان باشند.', 'eshterakyar'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="sale_price"><?php esc_html_e('قیمت تخفیف خورده (تومان)', 'eshterakyar'); ?></label></th>
                            <td>
                                <input type="number" name="sale_price" id="sale_price" value="<?php echo $plan && isset($plan->sale_price) && $plan->sale_price > 0 ? esc_attr(intval($plan->sale_price)) : ''; ?>" step="1" min="0" class="regular-text" placeholder="<?php esc_attr_e('اختیاری - برای نمایش قیمت تخفیف خورده', 'eshterakyar'); ?>">
                                <p class="description"><?php esc_html_e('قیمت تخفیف خورده به تومان (اختیاری). اگر این فیلد را پر کنید، در صفحه پلن‌ها قیمت اصلی خط‌خورده و قیمت تخفیف خورده نمایش داده می‌شود.', 'eshterakyar'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="duration_days"><?php esc_html_e('مدت زمان (روز)', 'eshterakyar'); ?></label></th>
                            <td><input type="number" name="duration_days" id="duration_days" value="<?php echo $plan ? esc_attr($plan->duration_days) : '30'; ?>" min="1" class="small-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="daily_limit"><?php esc_html_e('محدودیت دانلود روزانه', 'eshterakyar'); ?></label></th>
                            <td>
                                <input type="number" name="daily_limit" id="daily_limit" value="<?php echo $plan ? esc_attr($plan->daily_limit) : '0'; ?>" min="0" class="small-text">
                                <p class="description"><?php esc_html_e('0 به معنای نامحدود است', 'eshterakyar'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="total_limit"><?php esc_html_e('محدودیت دانلود کلی', 'eshterakyar'); ?></label></th>
                            <td>
                                <input type="number" name="total_limit" id="total_limit" value="<?php echo $plan ? esc_attr($plan->total_limit) : '0'; ?>" min="0" class="small-text">
                                <p class="description"><?php esc_html_e('0 به معنای نامحدود است', 'eshterakyar'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="download_access_days"><?php esc_html_e('مدت دسترسی به فایل‌ها (روز)', 'eshterakyar'); ?></label></th>
                            <td>
                                <input type="number" name="download_access_days" id="download_access_days" value="<?php echo $plan && isset($plan->download_access_days) ? esc_attr(intval($plan->download_access_days)) : '0'; ?>" min="0" max="3650" class="small-text">
                                <p class="description"><?php esc_html_e('از زمان اولین دانلود هر محصول توسط کاربر، چند روز می‌تواند لینک دانلود آن محصول را بگیرد. پس از اتمام این مدت، در صورت داشتن سهمیه روزانه می‌تواند با کسر اعتبار دسترسی مجدد (پنجره جدید) بگیرد — مثل ووکامرس. پیش‌فرض: ۰ = نامحدود.', 'eshterakyar'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="max_downloads_per_product"><?php esc_html_e('حداکثر تعداد دانلود هر محصول', 'eshterakyar'); ?></label></th>
                            <td>
                                <input type="number" name="max_downloads_per_product" id="max_downloads_per_product" value="<?php echo $plan && isset($plan->max_downloads_per_product) ? esc_attr(intval($plan->max_downloads_per_product)) : '0'; ?>" min="0" max="100" class="small-text">
                                <p class="description"><?php esc_html_e('هر محصول را کاربر حداکثر چند بار می‌تواند دانلود (یا دانلود مجدد) کند. پس از اتمام این تعداد، در صورت داشتن سهمیه روزانه می‌تواند با کسر اعتبار دسترسی مجدد (پنجره جدید) بگیرد — مثل ووکامرس. پیش‌فرض: ۰ = نامحدود.', 'eshterakyar'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="lifetime_access"><?php esc_html_e('دسترسی دائمی به فایل‌های دانلود شده', 'eshterakyar'); ?></label></th>
                            <td>
                                <input type="checkbox" name="lifetime_access" id="lifetime_access" value="1" <?php checked(1, ($plan && !empty($plan->lifetime_access)) ? 1 : 0); ?>>
                                <label for="lifetime_access"><?php esc_html_e('دسترسی دائمی برای فایل‌هایی که با این پلن دانلود می‌شوند فعال باشد', 'eshterakyar'); ?></label>
                                <p class="description"><?php esc_html_e('در صورت فعال بودن، کاربر برای فایل‌هایی که در طول این پلن دانلود کرده است، حتی بعد از اتمام اشتراک هم دسترسی خواهد داشت و می‌تواند دوباره دانلود کند.', 'eshterakyar'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="product_id"><?php esc_html_e('محصول مرتبط', 'eshterakyar'); ?></label></th>
                            <td>
                                <p class="description" style="margin-bottom: 10px; color: #d63638; font-weight: 600;"><?php esc_html_e('⚠️ مهم: قیمت محصول ووکامرس که انتخاب می‌کنید، همان قیمتی است که کاربر در صفحه پرداخت می‌بیند و پرداخت می‌کند. قیمت پلن فقط برای نمایش در صفحه پلن‌ها استفاده می‌شود. حتماً قیمت محصول را با قیمت پلن یکسان تنظیم کنید.', 'eshterakyar'); ?></p>
                                <div class="product-select-wrapper" style="position: relative;">
                                    <input type="text" id="product_search" class="regular-text" placeholder="<?php esc_attr_e('جست‌وجوی محصول...', 'eshterakyar'); ?>" autocomplete="off" style="margin-bottom: 10px;">
                                    <select name="product_id" id="product_id" class="regular-text" style="display: none;">
                                    <option value=""><?php esc_html_e('-- انتخاب کنید --', 'eshterakyar'); ?></option>
                                    <?php
                                    $products = wc_get_products(array('limit' => -1));
                                    foreach ($products as $product_item):
                                        $selected = ($plan && $plan->product_id == $product_item->get_id()) ? 'selected' : '';
                                    ?>
                                            <option value="<?php echo esc_attr($product_item->get_id()); ?>" <?php echo esc_attr($selected); ?> data-name="<?php echo esc_attr($product_item->get_name()); ?>">
                                            <?php echo esc_html($product_item->get_name()); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                    <div id="product_search_results" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 4px; max-height: 300px; overflow-y: auto; z-index: 1000; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></div>
                                    <div id="selected_product_display" style="margin-top: 10px; padding: 10px; background: #f0f0f1; border-radius: 4px; display: <?php echo ($plan && $plan->product_id) ? 'block' : 'none'; ?>;">
                                        <strong><?php esc_html_e('محصول انتخاب شده:', 'eshterakyar'); ?></strong>
                                        <span id="selected_product_name">
                                            <?php 
                                            if ($plan && $plan->product_id) {
                                                $selected_product = wc_get_product($plan->product_id);
                                                if ($selected_product) {
                                                    echo esc_html($selected_product->get_name());
                                                }
                                            }
                                            ?>
                                        </span>
                                        <button type="button" id="clear_product_selection" class="button button-small" style="margin-right: 10px;"><?php esc_html_e('پاک کردن', 'eshterakyar'); ?></button>
                                    </div>
                                </div>
                                <p class="description">
                                    <?php esc_html_e('برای خرید این پلن، یک محصول ووکامرس تعریف و انتخاب کنید. با خرید این محصول، اشتراک برای کاربر فعال می‌شود.', 'eshterakyar'); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="status"><?php esc_html_e('وضعیت', 'eshterakyar'); ?></label></th>
                            <td>
                                <select name="status" id="status">
                                    <option value="active" <?php echo (!$plan || ($plan && $plan->status == 'active')) ? 'selected' : ''; ?>><?php esc_html_e('فعال', 'eshterakyar'); ?></option>
                                    <option value="inactive" <?php echo ($plan && $plan->status == 'inactive') ? 'selected' : ''; ?>><?php esc_html_e('غیرفعال', 'eshterakyar'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="merge_with_existing"><?php esc_html_e('جمع کردن با اشتراک قبلی', 'eshterakyar'); ?></label></th>
                            <td>
                                <input type="checkbox" name="merge_with_existing" id="merge_with_existing" value="1" <?php echo ($plan && isset($plan->merge_with_existing) && $plan->merge_with_existing) ? 'checked' : ''; ?>>
                                <label for="merge_with_existing"><?php esc_html_e('در صورت خرید این پلن، مجوزها و روزهای باقی‌مانده با اشتراک قبلی جمع شود', 'eshterakyar'); ?></label>
                                <p class="description"><?php esc_html_e('این گزینه فقط برای پلن‌های هم‌گروه کار می‌کند', 'eshterakyar'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="subscription_group_id"><?php esc_html_e('گروه اشتراک (ایدی اشتراک‌ها)', 'eshterakyar'); ?></label></th>
                            <td>
                                <input type="text" name="subscription_group_id" id="subscription_group_id" value="<?php echo ($plan && isset($plan->subscription_group_id) && $plan->subscription_group_id !== null && $plan->subscription_group_id !== '') ? esc_attr($plan->subscription_group_id) : ''; ?>" class="regular-text" placeholder="<?php esc_attr_e('مثال: g1 یا 1', 'eshterakyar'); ?>">
                                <p class="description"><?php esc_html_e('پلن‌هایی که این شناسه را دارند با هم هم‌گروه هستند. در صورت خرید پلن هم‌گروه، روز و محدودیت دانلود به اشتراک فعلی اضافه می‌شود؛ در صورت خرید پلن غیر هم‌گروه، اشتراک جدید رزرو می‌شود و بعد از اتمام اشتراک فعلی فعال می‌شود. خالی = بدون گروه.', 'eshterakyar'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="badge_text"><?php esc_html_e('متن نشان', 'eshterakyar'); ?></label></th>
                            <td>
                                <input type="text" name="badge_text" id="badge_text" value="<?php echo ($plan && isset($plan->badge_text) && $plan->badge_text !== null) ? esc_attr($plan->badge_text) : ''; ?>" class="regular-text" placeholder="<?php esc_attr_e('مثلاً: پرفروش، به صرفه، پر تخفیف', 'eshterakyar'); ?>">
                                <p class="description"><?php esc_html_e('متن نشان که در بالای کارت پلن نمایش داده می‌شود (اختیاری)', 'eshterakyar'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="is_recommended"><?php esc_html_e('پلن پیشنهادی', 'eshterakyar'); ?></label></th>
                            <td>
                                <?php $is_recommended_form = $plan && isset($plan->is_recommended) && (int) $plan->is_recommended === 1; ?>
                                <label>
                                    <input type="checkbox" name="is_recommended" id="is_recommended" value="1" <?php checked($is_recommended_form); ?>>
                                    <?php esc_html_e('این پلن به‌عنوان پلن پیشنهادی نمایش داده شود (فقط یکی می‌تواند پیشنهادی باشد)', 'eshterakyar'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="badge_color"><?php esc_html_e('رنگ نشان', 'eshterakyar'); ?></label></th>
                            <td>
                                <select name="badge_color" id="badge_color">
                                    <?php 
                                    $current_badge_color = ($plan && isset($plan->badge_color) && $plan->badge_color !== null) ? $plan->badge_color : 'orange';
                                    $badge_colors = array(
                                        'orange' => __('نارنجی', 'eshterakyar'),
                                        'red' => __('قرمز', 'eshterakyar'),
                                        'green' => __('سبز', 'eshterakyar'),
                                        'blue' => __('آبی', 'eshterakyar'),
                                        'purple' => __('بنفش', 'eshterakyar'),
                                        'pink' => __('صورتی', 'eshterakyar')
                                    );
                                    foreach ($badge_colors as $color_value => $color_label):
                                    ?>
                                        <option value="<?php echo esc_attr($color_value); ?>" <?php selected($current_badge_color, $color_value); ?>><?php echo esc_html($color_label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php esc_html_e('رنگ نشان (فقط در صورت وجود متن نشان)', 'eshterakyar'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="custom_features"><?php esc_html_e('ویژگی‌های سفارشی', 'eshterakyar'); ?></label></th>
                            <td>
                                <textarea name="custom_features" id="custom_features" rows="8" class="large-text" placeholder="<?php esc_attr_e('هر ویژگی را در یک خط بنویسید:\nدسترسی به بیش از 70 هزار طرح لایه باز اختصاصی ایرانی\nدسترسی به بیش از 3 هزار تایپوگرافی اختصاصی ایرانی\nدسترسی به بیش از 500 هزار تصویر اختصاصی ایرانی', 'eshterakyar'); ?>"><?php echo $plan && isset($plan->custom_features) ? esc_textarea($plan->custom_features) : ''; ?></textarea>
                                <p class="description"><?php esc_html_e('ویژگی‌های سفارشی که در صفحه خرید اشتراک نمایش داده می‌شوند. هر ویژگی را در یک خط جداگانه بنویسید.', 'eshterakyar'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e('دسترسی به دسته‌بندی‌ها', 'eshterakyar'); ?></label></th>
                            <td>
                                <?php
                                $categories = get_terms(array(
                                    'taxonomy' => 'product_cat',
                                    'hide_empty' => false,
                                    'orderby' => 'name',
                                    'order' => 'ASC',
                                ));
                                if (is_wp_error($categories) || !is_array($categories)) {
                                    $categories = array();
                                }
                                $categories_by_parent = array();
                                foreach ($categories as $cat_item) {
                                    $parent_id = isset($cat_item->parent) ? (int) $cat_item->parent : 0;
                                    if (!isset($categories_by_parent[$parent_id])) {
                                        $categories_by_parent[$parent_id] = array();
                                    }
                                    $categories_by_parent[$parent_id][] = $cat_item;
                                }
                                $render_category_tree = static function(array $nodes_by_parent, int $parent_id, string $input_name, array $selected_ids, array $ancestor_ids = array()) use (&$render_category_tree) {
                                    if (empty($nodes_by_parent[$parent_id])) {
                                        return;
                                    }
                                    foreach ($nodes_by_parent[$parent_id] as $cat_node) {
                                        $term_id = (int) $cat_node->term_id;
                                        $has_children = !empty($nodes_by_parent[$term_id]);
                                        $depth = count($ancestor_ids);
                                        $ancestor_marker = '';
                                        if (!empty($ancestor_ids)) {
                                            $ancestor_marker = '|' . implode('|', array_map('intval', $ancestor_ids)) . '|';
                                        }
                                        $next_ancestors = $ancestor_ids;
                                        $next_ancestors[] = $term_id;
                                        ?>
                                        <label
                                            class="access-item access-item-tree <?php echo $has_children ? 'has-children' : 'no-children'; ?>"
                                            data-term-id="<?php echo esc_attr($term_id); ?>"
                                            data-parent-id="<?php echo esc_attr($parent_id); ?>"
                                            data-depth="<?php echo esc_attr($depth); ?>"
                                            data-ancestor-ids="<?php echo esc_attr($ancestor_marker); ?>">
                                            <span class="access-item-tree-main" style="padding-right: <?php echo esc_attr($depth * 18); ?>px;">
                                                <?php if ($has_children): ?>
                                                    <span class="access-tree-dot" aria-hidden="true"></span>
                                                <?php else: ?>
                                                    <span class="access-tree-leaf" aria-hidden="true"></span>
                                                <?php endif; ?>
                                                <input type="checkbox" name="<?php echo esc_attr($input_name); ?>[]" value="<?php echo esc_attr($term_id); ?>" data-term-id="<?php echo esc_attr($term_id); ?>" <?php checked(in_array($term_id, $selected_ids, true)); ?>>
                                                <span class="access-item-label"><?php echo esc_html($cat_node->name); ?></span>
                                                <span class="access-item-count">(<?php echo esc_html($cat_node->count); ?>)</span>
                                            </span>
                                        </label>
                                        <?php
                                        $render_category_tree($nodes_by_parent, $term_id, $input_name, $selected_ids, $next_ancestors);
                                    }
                                };
                                ?>
                                <div class="eshterakyar-access-control">
                                    <div class="access-control-grid">
                                        <div class="access-control-box allowed-box">
                                            <div class="access-control-header">
                                                <span class="access-icon">✅</span>
                                                <h4><?php esc_html_e('دسته‌بندی‌های مجاز', 'eshterakyar'); ?></h4>
                                                <span class="access-badge allowed-badge"><?php esc_html_e('اختیاری', 'eshterakyar'); ?></span>
                                            </div>
                                            <p class="access-description"><?php esc_html_e('اگر انتخاب شود، فقط محصولات این دسته‌بندی‌ها قابل دانلود هستند', 'eshterakyar'); ?></p>
                                            <?php
                                            $include_children_allowed = true;
                                            if ($plan && isset($plan->include_children_allowed_categories)) {
                                                $include_children_allowed = (int) $plan->include_children_allowed_categories === 1;
                                            }
                                            ?>
                                            <div style="margin: 8px 0 12px;">
                                                <label>
                                                    <input type="checkbox" name="include_children_allowed_categories" value="1" <?php checked($include_children_allowed); ?>>
                                                    <?php esc_html_e('اعمال روی زیر دسته‌بندی‌ها', 'eshterakyar'); ?>
                                                </label>
                                            </div>
                                            <div class="access-search-box">
                                                <input type="text" class="access-search" placeholder="<?php esc_attr_e('جست‌وجو...', 'eshterakyar'); ?>" data-target="allowed_categories">
                                            </div>
                                            <div class="access-items-container access-items-tree category-tree-container" data-input-name="allowed_categories[]">
                                                <?php
                                                $selected_cats = array();
                                                if ($plan && isset($plan->allowed_categories) && $plan->allowed_categories !== null && $plan->allowed_categories !== '') {
                                                    $selected_cats = array_map('intval', explode(',', $plan->allowed_categories));
                                                }
                                                $render_category_tree($categories_by_parent, 0, 'allowed_categories', $selected_cats);
                                                ?>
                                            </div>
                                            <div class="access-actions">
                                                <button type="button" class="access-select-all" data-target="allowed_categories"><?php esc_html_e('انتخاب همه', 'eshterakyar'); ?></button>
                                                <button type="button" class="access-deselect-all" data-target="allowed_categories"><?php esc_html_e('لغو همه', 'eshterakyar'); ?></button>
                                            </div>
                                        </div>
                                        
                                        <div class="access-control-box excluded-box">
                                            <div class="access-control-header">
                                                <span class="access-icon">🚫</span>
                                                <h4><?php esc_html_e('دسته‌بندی‌های مستثنی', 'eshterakyar'); ?></h4>
                                            </div>
                                            <p class="access-description"><?php esc_html_e('محصولات این دسته‌بندی‌ها قابل دانلود نیستند', 'eshterakyar'); ?></p>
                                            <?php
                                            $include_children_excluded = true;
                                            if ($plan && isset($plan->include_children_exclude_categories)) {
                                                $include_children_excluded = (int) $plan->include_children_exclude_categories === 1;
                                            }
                                            ?>
                                            <div style="margin: 8px 0 12px;">
                                                <label>
                                                    <input type="checkbox" name="include_children_exclude_categories" value="1" <?php checked($include_children_excluded); ?>>
                                                    <?php esc_html_e('اعمال روی زیر دسته‌بندی‌ها', 'eshterakyar'); ?>
                                                </label>
                                            </div>
                                            <div class="access-search-box">
                                                <input type="text" class="access-search" placeholder="<?php esc_attr_e('جست‌وجو...', 'eshterakyar'); ?>" data-target="exclude_categories">
                                            </div>
                                            <div class="access-items-container access-items-tree category-tree-container" data-input-name="exclude_categories[]">
                                                <?php
                                                $excluded_cats = array();
                                                if ($plan && isset($plan->exclude_categories) && $plan->exclude_categories !== null && $plan->exclude_categories !== '') {
                                                    $excluded_cats = array_map('intval', explode(',', $plan->exclude_categories));
                                                }
                                                $render_category_tree($categories_by_parent, 0, 'exclude_categories', $excluded_cats);
                                                ?>
                                            </div>
                                            <div class="access-actions">
                                                <button type="button" class="access-select-all" data-target="exclude_categories"><?php esc_html_e('انتخاب همه', 'eshterakyar'); ?></button>
                                                <button type="button" class="access-deselect-all" data-target="exclude_categories"><?php esc_html_e('لغو همه', 'eshterakyar'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e('دسترسی به تگ‌ها', 'eshterakyar'); ?></label></th>
                            <td>
                                <div class="eshterakyar-access-control">
                                    <div class="access-control-grid">
                                        <div class="access-control-box allowed-box">
                                            <div class="access-control-header">
                                                <span class="access-icon">✅</span>
                                                <h4><?php esc_html_e('تگ‌های مجاز', 'eshterakyar'); ?></h4>
                                                <span class="access-badge allowed-badge"><?php esc_html_e('اختیاری', 'eshterakyar'); ?></span>
                                            </div>
                                            <p class="access-description"><?php esc_html_e('اگر انتخاب شود، فقط محصولات با این تگ‌ها قابل دانلود هستند', 'eshterakyar'); ?></p>
                                            <div class="access-search-box">
                                                <input type="text" class="access-search" placeholder="<?php esc_attr_e('جست‌وجو...', 'eshterakyar'); ?>" data-target="allowed_tags">
                                            </div>
                                            <div class="access-items-container">
                                                <?php
                                                $tags = get_terms(array('taxonomy' => 'product_tag', 'hide_empty' => false));
                                                $selected_tags = array();
                                                if ($plan && isset($plan->allowed_tags) && $plan->allowed_tags !== null && $plan->allowed_tags !== '') {
                                                    $selected_tags = array_map('intval', explode(',', $plan->allowed_tags));
                                                }
                                                foreach ($tags as $tag):
                                                ?>
                                                    <label class="access-item">
                                                        <input type="checkbox" name="allowed_tags[]" value="<?php echo esc_attr($tag->term_id); ?>" <?php checked(in_array($tag->term_id, $selected_tags)); ?>>
                                                        <span class="access-item-label"><?php echo esc_html($tag->name); ?></span>
                                                        <span class="access-item-count">(<?php echo esc_html($tag->count); ?>)</span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="access-actions">
                                                <button type="button" class="access-select-all" data-target="allowed_tags"><?php esc_html_e('انتخاب همه', 'eshterakyar'); ?></button>
                                                <button type="button" class="access-deselect-all" data-target="allowed_tags"><?php esc_html_e('لغو همه', 'eshterakyar'); ?></button>
                                            </div>
                                        </div>
                                        
                                        <div class="access-control-box excluded-box">
                                            <div class="access-control-header">
                                                <span class="access-icon">🚫</span>
                                                <h4><?php esc_html_e('تگ‌های مستثنی', 'eshterakyar'); ?></h4>
                                            </div>
                                            <p class="access-description"><?php esc_html_e('محصولات با این تگ‌ها قابل دانلود نیستند', 'eshterakyar'); ?></p>
                                            <div class="access-search-box">
                                                <input type="text" class="access-search" placeholder="<?php esc_attr_e('جست‌وجو...', 'eshterakyar'); ?>" data-target="exclude_tags">
                                            </div>
                                            <div class="access-items-container">
                                                <?php
                                                $excluded_tags = array();
                                                if ($plan && isset($plan->exclude_tags) && $plan->exclude_tags !== null && $plan->exclude_tags !== '') {
                                                    $excluded_tags = array_map('intval', explode(',', $plan->exclude_tags));
                                                }
                                                foreach ($tags as $tag):
                                                ?>
                                                    <label class="access-item">
                                                        <input type="checkbox" name="exclude_tags[]" value="<?php echo esc_attr($tag->term_id); ?>" <?php checked(in_array($tag->term_id, $excluded_tags)); ?>>
                                                        <span class="access-item-label"><?php echo esc_html($tag->name); ?></span>
                                                        <span class="access-item-count">(<?php echo esc_html($tag->count); ?>)</span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="access-actions">
                                                <button type="button" class="access-select-all" data-target="exclude_tags"><?php esc_html_e('انتخاب همه', 'eshterakyar'); ?></button>
                                                <button type="button" class="access-deselect-all" data-target="exclude_tags"><?php esc_html_e('لغو همه', 'eshterakyar'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php include ESHTERAKYAR_PLUGIN_DIR . 'templates/admin/partials/plan-post-access-fields.php'; ?>
                    </table>
                    
                    <div class="plan-form-actions">
                        <button type="submit" class="button button-primary button-large plan-save-btn">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <?php esc_html_e('ذخیره پلن', 'eshterakyar'); ?>
                        </button>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=eshterakyar-plans')); ?>" class="button button-large">
                            <?php esc_html_e('انصراف', 'eshterakyar'); ?>
                        </a>
                    </div>
                </form>
                </div>
            </div>
            <?php endif; ?>
        <?php endif;?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#eshterakyar-plan-form').on('submit', function() {
        var $f = $(this);
        function collectCsv(selector) {
            var ids = [];
            $f.find(selector + ':checked').each(function() {
                ids.push($(this).val());
            });
            return ids.join(',');
        }
        var ac = collectCsv('input[name="allowed_categories[]"]');
        var ec = collectCsv('input[name="exclude_categories[]"]');
        var at = collectCsv('input[name="allowed_tags[]"]');
        var et = collectCsv('input[name="exclude_tags[]"]');
        var apc = collectCsv('input[name="allowed_post_categories[]"]');
        var epc = collectCsv('input[name="exclude_post_categories[]"]');
        var apt = collectCsv('input[name="allowed_post_tags[]"]');
        var ept = collectCsv('input[name="exclude_post_tags[]"]');
        $f.find('input[name="allowed_categories[]"], input[name="exclude_categories[]"], input[name="allowed_tags[]"], input[name="exclude_tags[]"], input[name="allowed_post_categories[]"], input[name="exclude_post_categories[]"], input[name="allowed_post_tags[]"], input[name="exclude_post_tags[]"]').prop('disabled', true);
        $f.find('input[name="eshterakyar_taxonomies_via_csv"]').remove();
        $f.find('input[name="allowed_categories_csv"], input[name="exclude_categories_csv"], input[name="allowed_tags_csv"], input[name="exclude_tags_csv"], input[name="allowed_post_categories_csv"], input[name="exclude_post_categories_csv"], input[name="allowed_post_tags_csv"], input[name="exclude_post_tags_csv"]').remove();
        $f.append($('<input>', { type: 'hidden', name: 'eshterakyar_taxonomies_via_csv', value: '1' }));
        $f.append($('<input>', { type: 'hidden', name: 'allowed_categories_csv', value: ac }));
        $f.append($('<input>', { type: 'hidden', name: 'exclude_categories_csv', value: ec }));
        $f.append($('<input>', { type: 'hidden', name: 'allowed_tags_csv', value: at }));
        $f.append($('<input>', { type: 'hidden', name: 'exclude_tags_csv', value: et }));
        $f.append($('<input>', { type: 'hidden', name: 'allowed_post_categories_csv', value: apc }));
        $f.append($('<input>', { type: 'hidden', name: 'exclude_post_categories_csv', value: epc }));
        $f.append($('<input>', { type: 'hidden', name: 'allowed_post_tags_csv', value: apt }));
        $f.append($('<input>', { type: 'hidden', name: 'exclude_post_tags_csv', value: ept }));
    });

    var $productSelect = $('#product_id');
    var $productSearch = $('#product_search');
    var $productResults = $('#product_search_results');
    var $selectedProductDisplay = $('#selected_product_display');
    var $selectedProductName = $('#selected_product_name');
    if ($productSelect.val()) {
        var selectedOption = $productSelect.find('option:selected');
        if (selectedOption.length) {
            $productSearch.val(selectedOption.data('name') || selectedOption.text());
            $selectedProductName.text(selectedOption.text());
            $selectedProductDisplay.show();
        }
    }
    
    $productSearch.on('input', function() {
        var searchTerm = $(this).val().toLowerCase().trim();
        
        if (searchTerm.length === 0) {
            $productResults.hide().empty();
            return;
        }
        
        var matches = [];
        $productSelect.find('option').each(function() {
            var $option = $(this);
            var productName = $option.text().toLowerCase();
            if (productName.indexOf(searchTerm) !== -1 && $option.val() !== '') {
                matches.push({
                    id: $option.val(),
                    name: $option.text()
                });
            }
        });
        
        if (matches.length > 0) {
            var html = '<div style="padding: 8px; border-bottom: 1px solid #eee; cursor: pointer; transition: background 0.2s;" class="product-result-item">';
            matches.forEach(function(product) {
                html += '<div style="padding: 10px; border-bottom: 1px solid #f0f0f1;" data-product-id="' + product.id + '" data-product-name="' + product.name + '">';
                html += '<strong>' + product.name + '</strong>';
                html += '</div>';
            });
            html += '</div>';
            $productResults.html(html).show();
            
            $productResults.find('[data-product-id]').on('click', function() {
                var productId = $(this).data('product-id');
                var productName = $(this).data('product-name');
                
                $productSelect.val(productId);
                $productSearch.val(productName);
                $selectedProductName.text(productName);
                $selectedProductDisplay.show();
                $productResults.hide();
            });
        } else {
            $productResults.html('<div style="padding: 15px; text-align: center; color: #666;"><?php esc_html_e('محصولی یافت نشد', 'eshterakyar'); ?></div>').show();
        }
    });
    
    $('#clear_product_selection').on('click', function() {
        $productSelect.val('');
        $productSearch.val('');
        $selectedProductDisplay.hide();
        $productResults.hide();
    });
    
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.product-select-wrapper').length) {
            $productResults.hide();
        }
    });
    
    $(document).on('input keyup', '.access-search', function() {
        var searchTerm = $(this).val().toLowerCase().trim();
        var container = $(this).closest('.access-control-box').find('.access-items-container');
        var visibleCount = 0;
        
        container.find('.no-results-message').remove();
        
        container.find('.access-item').each(function() {
            var $item = $(this);
            var label = $item.find('.access-item-label').text().toLowerCase();
            
            if (searchTerm.length === 0 || label.indexOf(searchTerm) !== -1) {
                $item.removeClass('hidden').show();
                visibleCount++;
            } else {
                $item.addClass('hidden').hide();
            }
        });
        
        if (visibleCount === 0 && searchTerm.length > 0) {
            container.append('<div class="no-results-message" style="padding: 15px; text-align: center; color: #666; font-style: italic; margin-top: 10px;"><?php echo esc_js(__('نتیجه‌ای یافت نشد', 'eshterakyar')); ?></div>');
        }
    });
    
    $(document).on('click', '.access-select-all', function() {
        var container = $(this).closest('.access-control-box').find('.access-items-container');
        container.find('input[type="checkbox"]:not(:checked)').each(function() {
            var $item = $(this).closest('.access-item');
            if (!$item.hasClass('hidden') && $item.is(':visible')) {
                $(this).prop('checked', true).trigger('change');
            }
        });
    });
    
    $(document).on('click', '.access-deselect-all', function() {
        var container = $(this).closest('.access-control-box').find('.access-items-container');
        container.find('input[type="checkbox"]:checked').prop('checked', false).trigger('change');
    });
    
    function updateSelectedCount() {
        $('.access-control-box').each(function() {
            var selected = $(this).find('input[type="checkbox"]:checked').length;
            var header = $(this).find('.access-control-header h4');
            var originalText = header.data('original-text') || header.text().replace(/\s*\(\d+\)\s*$/, '');
            header.data('original-text', originalText);
            
            if (selected > 0) {
                header.text(originalText + ' (' + selected + ')');
            } else {
                header.text(originalText);
            }
        });
    }
    
    $(document).on('change', '.access-item input[type="checkbox"]', updateSelectedCount);
    updateSelectedCount();

    function isChildSelectionEnabled($box) {
        var $toggle = $box.find('input[name="include_children_allowed_categories"], input[name="include_children_exclude_categories"]');
        return $toggle.length && $toggle.is(':checked');
    }

    function cascadeCategorySelection($container, termId, checkedState) {
        $container.find('.access-item[data-ancestor-ids*="|' + termId + '|"] input[type="checkbox"]').prop('checked', checkedState);
    }

    $(document).on('change', '.category-tree-container .access-item input[type="checkbox"]', function() {
        var $checkbox = $(this);
        var $box = $checkbox.closest('.access-control-box');
        if (!isChildSelectionEnabled($box)) {
            updateSelectedCount();
            return;
        }
        var termId = String($checkbox.data('term-id') || '');
        if (termId !== '') {
            cascadeCategorySelection($checkbox.closest('.category-tree-container'), termId, $checkbox.is(':checked'));
        }
        updateSelectedCount();
    });

    $(document).on('change', 'input[name="include_children_allowed_categories"], input[name="include_children_exclude_categories"]', function() {
        var $toggle = $(this);
        if (!$toggle.is(':checked')) {
            return;
        }
        var $container = $toggle.closest('.access-control-box').find('.category-tree-container');
        $container.find('.access-item input[type="checkbox"]:checked').each(function() {
            var termId = String($(this).data('term-id') || '');
            if (termId !== '') {
                cascadeCategorySelection($container, termId, true);
            }
        });
        updateSelectedCount();
    });
    
    $(document).on('mouseenter', '#product_search_results [data-product-id]', function() {
        $(this).css('background', '#f0f0f1');
    }).on('mouseleave', '#product_search_results [data-product-id]', function() {
        $(this).css('background', '');
    });

    $(document).on('click', '.eshterakyar-recommend-plan-toggle', function() {
        var $btn = $(this);
        var planId = $btn.data('plan-id');
        var current = parseInt($btn.data('recommended') || 0, 10);
        var next = current ? 0 : 1;
        $btn.prop('disabled', true);
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'eshterakyar_toggle_recommended_plan',
                plan_id: planId,
                recommended: next,
                nonce: '<?php echo wp_create_nonce('eshterakyar_toggle_recommended_plan'); ?>'
            },
            success: function(resp) {
                if (!resp || !resp.success) {
                    $btn.prop('disabled', false);
                    return;
                }
                // If set to recommended, clear all others in UI
                if (next === 1) {
                    $('.eshterakyar-recommend-plan-toggle').each(function() {
                        $(this).data('recommended', 0);
                        $(this).find('.dashicons').removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
                    });
                }
                $btn.data('recommended', next);
                $btn.find('.dashicons').toggleClass('dashicons-star-filled', next === 1).toggleClass('dashicons-star-empty', next === 0);
                $btn.prop('disabled', false);
            },
            error: function() {
                $btn.prop('disabled', false);
            }
        });
    });
    
    
    if ($('#eshterakyar-sortable-plans').length && typeof $.fn.sortable !== 'undefined') {
        $('#eshterakyar-sortable-plans').sortable({
            items: '.eshterakyar-plan-card',
            cursor: 'move',
            opacity: 0.8,
            placeholder: 'eshterakyar-plan-card-placeholder',
            tolerance: 'pointer',
            axis: false,
            handle: '.plan-card-header, .plan-card-name',
            start: function(e, ui) {
                ui.placeholder.height(ui.item.height());
                ui.placeholder.css('background', '#f0f0f1');
                ui.placeholder.css('border', '2px dashed #2271b1');
            },
            update: function(e, ui) {
                var planIds = [];
                $('#eshterakyar-sortable-plans .eshterakyar-plan-card').each(function(index) {
                    planIds.push($(this).data('plan-id'));
                });
                
                
                var savingMsg = $('<div class="notice notice-info is-dismissible" style="margin: 10px 0;"><p>در حال ذخیره ترتیب پلن‌ها...</p></div>');
                $('.eshterakyar-plans-header').after(savingMsg);
                
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'eshterakyar_save_plans_order',
                        plan_ids: planIds,
                        nonce: '<?php echo wp_create_nonce('eshterakyar_save_plans_order'); ?>'
                    },
                    success: function(response) {
                        savingMsg.remove();
                        if (response.success) {
                            var successMsg = $('<div class="notice notice-success is-dismissible" style="margin: 10px 0;"><p>ترتیب پلن‌ها با موفقیت ذخیره شد.</p></div>');
                            $('.eshterakyar-plans-header').after(successMsg);
                            setTimeout(function() {
                                successMsg.fadeOut(function() {
                                    $(this).remove();
                                });
                            }, 3000);
                        } else {
                            var errorMsg = $('<div class="notice notice-error is-dismissible" style="margin: 10px 0;"><p>خطا در ذخیره ترتیب پلن‌ها. لطفاً دوباره تلاش کنید.</p></div>');
                            $('.eshterakyar-plans-header').after(errorMsg);
                        }
                    },
                    error: function() {
                        savingMsg.remove();
                        var errorMsg = $('<div class="notice notice-error is-dismissible" style="margin: 10px 0;"><p>خطا در ارتباط با سرور. لطفاً دوباره تلاش کنید.</p></div>');
                        $('.eshterakyar-plans-header').after(errorMsg);
                    }
                });
            }
        });
        
       
        $('#eshterakyar-sortable-plans .eshterakyar-plan-card').css('cursor', 'move');
    }
});
</script>

