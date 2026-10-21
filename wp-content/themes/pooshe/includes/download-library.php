<?php

defined('ABSPATH') || exit;

function pooshe_dl_lib_is_enabled()
{
    return function_exists('pooshe_opt') && defined('STYLE_OPTS') && pooshe_opt(STYLE_OPTS, 'download_library', false);
}

function pooshe_is_woo_downloads_hidden()
{
    return function_exists('pooshe_opt') && defined('STYLE_OPTS') && pooshe_opt(STYLE_OPTS, 'hide_woo_downloads', false);
}

add_filter('woocommerce_account_menu_items', 'pooshe_hide_woo_downloads_menu', 5);
function pooshe_hide_woo_downloads_menu($items)
{
    if (pooshe_is_woo_downloads_hidden() && isset($items['downloads'])) {
        unset($items['downloads']);
    }
    return $items;
}

add_action('template_redirect', 'pooshe_hide_woo_downloads_page');
function pooshe_hide_woo_downloads_page()
{
    if (!pooshe_is_woo_downloads_hidden()) return;
    global $wp_query;
    if (isset($wp_query->query_vars['downloads'])) {
        wp_redirect(wc_get_account_endpoint_url('dashboard'));
        exit;
    }
}

add_action('init', 'pooshe_dl_lib_add_endpoint');
function pooshe_dl_lib_add_endpoint()
{
    if (!pooshe_dl_lib_is_enabled()) return;
    add_rewrite_endpoint('download-library', EP_ROOT | EP_PAGES);
}

add_filter('query_vars', 'pooshe_dl_lib_query_vars', 0);
function pooshe_dl_lib_query_vars($vars)
{
    if (!pooshe_dl_lib_is_enabled()) return $vars;
    $vars[] = 'download-library';
    return $vars;
}

add_filter('woocommerce_account_menu_items', 'pooshe_dl_lib_menu_item', 25);
function pooshe_dl_lib_menu_item($items)
{
    if (!pooshe_dl_lib_is_enabled()) return $items;

    if (isset($items['downloads'])) {
        $new_items = [];
        foreach ($items as $key => $label) {
            $new_items[$key] = $label;
            if ($key === 'downloads') {
                $new_items['download-library'] = 'کتابخانه دانلود';
            }
        }
        return $new_items;
    }

    $new_items = [];
    foreach ($items as $key => $label) {
        if ($key === 'edit-account') {
            $new_items['download-library'] = 'کتابخانه دانلود';
        }
        $new_items[$key] = $label;
    }
    return $new_items;
}

add_action('woocommerce_account_download-library_endpoint', 'pooshe_dl_lib_content');
function pooshe_dl_lib_content()
{
    if (!pooshe_dl_lib_is_enabled()) return;

    $downloads = WC()->customer->get_downloadable_products();

    if (empty($downloads)) :
?>
        <div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
            <p><?php _e('هنوز هیچ محصولی خریداری نکرده‌اید.', 'pooshe'); ?></p>
            <a class="woocommerce-Button button" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">
                <?php _e('رفتن به فروشگاه', 'pooshe'); ?>
            </a>
        </div>
<?php
        return;
    endif;

    $grouped = [];
    foreach ($downloads as $dl) {
        $pid = $dl['product_id'];
        if (!isset($grouped[$pid])) {
            $grouped[$pid] = [
                'product_id'  => $pid,
                'product_name' => $dl['product_name'],
                'product_url'  => $dl['product_url'],
                'downloads'    => [],
            ];
        }
        $grouped[$pid]['downloads'][] = $dl;
    }
?>

    <div class="pooshe-dl-library">
        <div class="pooshe-dl-library-header">
            <h2><?php _e('کتابخانه دانلود', 'pooshe'); ?></h2>
            <span class="pooshe-dl-library-count"><?php printf(__('%d محصول', 'pooshe'), count($grouped)); ?></span>
        </div>

        <?php foreach ($grouped as $pid => $group) :
            $product       = wc_get_product($pid);
            $thumbnail     = $product ? $product->get_image('thumbnail') : wc_placeholder_img('thumbnail');
            $version       = get_post_meta($pid, '_pooshe_file_version', true);
            $file_size     = 0;
            $total_dl      = 0;
            $last_updated  = $product ? $product->get_date_modified() : null;

            if (!empty($group['downloads'][0]['file']['file'])) {
                $file_size = pooshe_dl_lib_get_file_size($group['downloads'][0]['file']['file']);
            }

            global $wpdb;
            $total_dl = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT SUM(download_count) FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
                WHERE product_id = %d AND user_id = %d",
                $pid,
                get_current_user_id()
            ));

            $product_url = $product ? get_permalink($pid) : $group['product_url'];
            $product_name = $product ? $product->get_name() : $group['product_name'];
        ?>
            <div class="pooshe-dl-library-item">
                <div class="pooshe-dl-item-thumb">
                    <?php if ($product) : ?>
                        <a href="<?php echo esc_url($product_url); ?>"><?php echo $thumbnail; ?></a>
                    <?php else : ?>
                        <?php echo $thumbnail; ?>
                    <?php endif; ?>
                </div>

                <div class="pooshe-dl-item-info">
                    <h3 class="pooshe-dl-item-title">
                        <a href="<?php echo esc_url($product_url); ?>"><?php echo esc_html($product_name); ?></a>
                    </h3>
                    <div class="pooshe-dl-item-meta">
                        <?php if ($version) : ?>
                            <span class="pooshe-dl-meta version">
                                <?php _e('نسخه:', 'pooshe'); ?> <strong><?php echo esc_html($version); ?></strong>
                            </span>
                        <?php endif; ?>
                        <?php if ($file_size > 0) : ?>
                            <span class="pooshe-dl-meta size">
                                <?php _e('حجم:', 'pooshe'); ?> <strong><?php echo size_format($file_size); ?></strong>
                            </span>
                        <?php endif; ?>
                        <?php if ($total_dl) : ?>
                            <span class="pooshe-dl-meta count">
                                <?php _e('دانلود:', 'pooshe'); ?> <strong><?php echo number_format($total_dl); ?></strong>
                            </span>
                        <?php endif; ?>
                        <?php if ($last_updated) : ?>
                            <span class="pooshe-dl-meta updated">
                                <?php _e('آخرین بروزرسانی:', 'pooshe'); ?>
                                <strong><?php echo date_i18n('Y/m/d', strtotime($last_updated->date('Y-m-d'))); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pooshe-dl-item-actions">
                    <?php if ($product && pooshe_product_already_bought($product)) : ?>
                        <button type="button" class="pooshe-dl-btn quick-dl" data-product-id="<?php echo $pid; ?>"
                            data-bs-toggle="modal" data-bs-target="#productDownloadModal">
                            <i class="fas fa-download"></i> <?php _e('دانلود سریع', 'pooshe'); ?>
                        </button>
                    <?php endif; ?>
                    <?php foreach ($group['downloads'] as $dl) : ?>
                        <a href="<?php echo esc_url($dl['download_url']); ?>" class="pooshe-dl-btn" title="<?php echo esc_attr($dl['download_name']); ?>">
                            <i class="fas fa-download"></i> <?php echo esc_html($dl['download_name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php
}

function pooshe_dl_lib_get_file_size($file_path)
{
    if (preg_match('/^https?:\/\//', $file_path)) {
        $response = @wp_remote_head($file_path, ['timeout' => 5]);
        if (!is_wp_error($response) && isset($response['headers']['content-length'])) {
            return (int) $response['headers']['content-length'];
        }
        return 0;
    }

    if (file_exists($file_path)) {
        return filesize($file_path);
    }

    $uploads  = wp_get_upload_dir();
    $absolute = $uploads['basedir'] . '/' . ltrim(str_replace($uploads['basedir'], '', $file_path), '/');
    if (file_exists($absolute)) {
        return filesize($absolute);
    }

    return 0;
}

add_action('add_meta_boxes', 'pooshe_dl_lib_add_meta_box');
function pooshe_dl_lib_add_meta_box()
{
    if (!pooshe_dl_lib_is_enabled()) return;
    add_meta_box(
        'pooshe_dl_lib_version',
        'کتابخانه دانلود',
        'pooshe_dl_lib_meta_box',
        'product',
        'side',
        'default'
    );
}

function pooshe_dl_lib_meta_box($post)
{
    wp_nonce_field('pooshe_dl_lib_version', 'pooshe_dl_lib_version_nonce');
    $version = get_post_meta($post->ID, '_pooshe_file_version', true);
?>
    <p>
        <label>نسخه فایل:</label>
        <input type="text" name="_pooshe_file_version" value="<?php echo esc_attr($version); ?>" class="widefat" placeholder="مثلاً 2.1.0">
    </p>
    <p><small>این نسخه در کتابخانه دانلود کاربران نمایش داده می‌شود.</small></p>
<?php
}

add_action('save_post_product', 'pooshe_dl_lib_save_meta');
function pooshe_dl_lib_save_meta($post_id)
{
    if (isset($_POST['pooshe_dl_lib_version_nonce']) && wp_verify_nonce($_POST['pooshe_dl_lib_version_nonce'], 'pooshe_dl_lib_version')) {
        update_post_meta($post_id, '_pooshe_file_version', sanitize_text_field($_POST['_pooshe_file_version'] ?? ''));
    }
}

add_action('wp_enqueue_scripts', 'pooshe_dl_lib_styles');
function pooshe_dl_lib_styles()
{
    if (!pooshe_dl_lib_is_enabled()) return;

    $css = '
    .pooshe-dl-library-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px }
    .pooshe-dl-library-header h2 { margin:0 }
    .pooshe-dl-library-count { color:#666; font-size:14px }
    .pooshe-dl-library-item { display:flex; align-items:center; gap:18px; padding:18px 20px; background:#fff; border:1px solid #e9ecef; border-radius:14px; margin-bottom:14px; transition:box-shadow .2s,border-color .2s }
    .pooshe-dl-library-item:hover { box-shadow:0 3px 16px rgba(0,0,0,.07); border-color:#dee2e6 }
    .pooshe-dl-item-thumb { flex-shrink:0 }
    .pooshe-dl-item-thumb img { width:64px; height:64px; border-radius:10px; object-fit:cover; display:block }
    .pooshe-dl-item-info { flex:1; min-width:0 }
    .pooshe-dl-item-title { margin:0 0 8px; font-size:15px; font-weight:600 }
    .pooshe-dl-item-title a { color:#1a1a1a; text-decoration:none }
    .pooshe-dl-item-title a:hover { color:var(--pooshe-color-main,#ff9e22) }
    .pooshe-dl-item-meta { display:flex; flex-wrap:wrap; gap:14px; font-size:12px; color:#666 }
    .pooshe-dl-meta strong { color:#1a1a1a; font-weight:600 }
    .pooshe-dl-item-actions { flex-shrink:0; display:flex; flex-direction:column; gap:6px }
    .pooshe-dl-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 20px; background:#fff; color:var(--pooshe-color-main,#ff9e22); border:1.5px solid var(--pooshe-color-main,#ff9e22); border-radius:8px; text-decoration:none; font-size:13px; cursor:pointer; transition:all .2s; white-space:nowrap }
    .pooshe-dl-btn:hover { background:var(--pooshe-color-main,#ff9e22); color:#fff }
    .pooshe-dl-btn i { font-size:14px }
    .woocommerce-MyAccount-content a:not(.button):not(.dokan-btn):not(.btn):hover { color:#f9f9f9 }
    .body.dark-mode .pooshe-dl-library-item { background:#1e293b; border-color:#2d3a50 }
    .body.dark-mode .pooshe-dl-library-item:hover { border-color:#3b4a65 }
    .body.dark-mode .pooshe-dl-item-title a { color:#e2e8f0 }
    .body.dark-mode .pooshe-dl-meta strong { color:#e2e8f0 }
    .body.dark-mode .pooshe-dl-library-count { color:#94a3b8 }
    .body.dark-mode .pooshe-dl-meta { color:#94a3b8 }
    @media(max-width:768px){ .pooshe-dl-library-item { flex-direction:column; text-align:center; padding:16px } .pooshe-dl-item-actions { width:100% } .pooshe-dl-btn { white-space:normal; justify-content:center } .pooshe-dl-item-meta { justify-content:center } }
    ';
    wp_add_inline_style('pooshe-main', $css);
}
