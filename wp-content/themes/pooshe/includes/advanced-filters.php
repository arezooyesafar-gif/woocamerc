<?php
defined('ABSPATH') || exit;

add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_setting(SHOP_OPTS . '[advanced_filters]', array(
        'default' => true,
        'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('advanced-filters-toggle', array(
        'settings' => SHOP_OPTS . '[advanced_filters]',
        'label' => 'فیلترهای پیشرفته',
        'section' => 'pooshe_section_shop',
        'description' => 'نمایش فیلترهای پیشرفته در بالای صفحه فروشگاه',
        'type' => 'checkbox',
    ));
});

function pooshe_advanced_filters_bar() {
    if (!pooshe_opt(SHOP_OPTS, 'advanced_filters', true)) {
        return;
    }
    if (!is_shop() && !is_product_category() && !is_product_tag() && !is_product_taxonomy()) {
        return;
    }

    $current_url = home_url(add_query_arg(array(), $_SERVER['REQUEST_URI']));
    $current_cat_slug = get_query_var('product_cat', '');

    $cats = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => 0,
    ));

    $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : '';
    $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : '';
    $current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'menu_order';
    $current_rcp = isset($_GET['rcp_access']) ? sanitize_text_field($_GET['rcp_access']) : '';
    ?>
    <div class="pooshe-advanced-filters">
        <div class="filters-header">
            <span class="filters-title"><i class="far fa-filter"></i> <?php esc_html_e('فیلترهای پیشرفته', 'pooshe'); ?></span>
            <button class="btn btn-sm btn-link filters-toggle d-xl-none" type="button">
                <i class="far fa-sliders-h"></i>
            </button>
        </div>

        <form class="filters-body" method="get" action="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
            <div class="filter-row">

                <div class="filter-field filter-category">
                    <select name="product_cat">
                        <option value=""><?php esc_html_e('همه دسته‌ها', 'pooshe'); ?></option>
                        <?php foreach ($cats as $cat) :
                            $selected = $current_cat_slug === $cat->slug ? 'selected' : '';
                        ?>
                            <option value="<?php echo esc_attr($cat->slug); ?>" <?php echo $selected; ?>>
                                <?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)
                            </option>
                            <?php
                            $sub_cats = get_terms(array(
                                'taxonomy' => 'product_cat',
                                'hide_empty' => true,
                                'parent' => $cat->term_id,
                            ));
                            foreach ($sub_cats as $sub) :
                                $sub_selected = $current_cat_slug === $sub->slug ? 'selected' : '';
                            ?>
                                <option value="<?php echo esc_attr($sub->slug); ?>" <?php echo $sub_selected; ?>>
                                    &mdash; <?php echo esc_html($sub->name); ?> (<?php echo $sub->count; ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-field filter-price">
                    <input type="number" name="min_price" placeholder="<?php esc_attr_e('حداقل قیمت', 'pooshe'); ?>" value="<?php echo esc_attr($min_price); ?>" min="0" step="1000">
                    <span class="price-sep">&ndash;</span>
                    <input type="number" name="max_price" placeholder="<?php esc_attr_e('حداکثر قیمت', 'pooshe'); ?>" value="<?php echo esc_attr($max_price); ?>" min="0" step="1000">
                </div>

                <div class="filter-field filter-orderby">
                    <select name="orderby">
                        <option value="menu_order" <?php selected($current_orderby, 'menu_order'); ?>><?php esc_html_e('مرتب‌سازی: پیش‌فرض', 'pooshe'); ?></option>
                        <option value="popularity" <?php selected($current_orderby, 'popularity'); ?>><?php esc_html_e('مرتب‌سازی: محبوب‌ترین', 'pooshe'); ?></option>
                        <option value="rating" <?php selected($current_orderby, 'rating'); ?>><?php esc_html_e('مرتب‌سازی: بالاترین امتیاز', 'pooshe'); ?></option>
                        <option value="date" <?php selected($current_orderby, 'date'); ?>><?php esc_html_e('مرتب‌سازی: جدیدترین', 'pooshe'); ?></option>
                        <option value="price" <?php selected($current_orderby, 'price'); ?>><?php esc_html_e('مرتب‌سازی: ارزان‌ترین', 'pooshe'); ?></option>
                        <option value="price-desc" <?php selected($current_orderby, 'price-desc'); ?>><?php esc_html_e('مرتب‌سازی: گران‌ترین', 'pooshe'); ?></option>
                    </select>
                </div>

                <?php if (function_exists('rcp_get_subscription_levels')) : ?>
                <div class="filter-field filter-rcp">
                    <select name="rcp_access">
                        <option value="" <?php selected($current_rcp, ''); ?>><?php esc_html_e('همه محصولات', 'pooshe'); ?></option>
                        <option value="restricted" <?php selected($current_rcp, 'restricted'); ?>><?php esc_html_e('فقط اشتراکی', 'pooshe'); ?></option>
                        <option value="unrestricted" <?php selected($current_rcp, 'unrestricted'); ?>><?php esc_html_e('فقط آزاد', 'pooshe'); ?></option>
                    </select>
                </div>
                <?php endif; ?>

                <div class="filter-field filter-submit">
                    <button type="submit" class="btn btn-primary btn-sm"><?php esc_html_e('اعمال', 'pooshe'); ?></button>
                </div>

            </div>

            <?php
            $active_tags = array();

            if ($current_cat_slug) {
                $term = get_term_by('slug', $current_cat_slug, 'product_cat');
                if ($term) {
                    $remove_url = remove_query_arg('product_cat', $current_url);
                    $active_tags[] = '<a href="' . esc_url($remove_url) . '" class="filter-tag">' . esc_html($term->name) . ' <i class="far fa-times"></i></a>';
                }
            }

            if ($min_price !== '' || $max_price !== '') {
                $label = '';
                if ($min_price !== '' && $max_price !== '') {
                    $label = number_format($min_price) . ' - ' . number_format($max_price);
                } elseif ($min_price !== '') {
                    $label = 'از ' . number_format($min_price);
                } else {
                    $label = 'تا ' . number_format($max_price);
                }
                $remove_url = remove_query_arg(array('min_price', 'max_price'), $current_url);
                $active_tags[] = '<a href="' . esc_url($remove_url) . '" class="filter-tag">قیمت: ' . esc_html($label) . ' <i class="far fa-times"></i></a>';
            }

            if ($current_orderby !== 'menu_order') {
                $orderby_labels = array(
                    'popularity' => 'محبوب‌ترین',
                    'rating'     => 'بالاترین امتیاز',
                    'date'       => 'جدیدترین',
                    'price'      => 'ارزان‌ترین',
                    'price-desc' => 'گران‌ترین',
                );
                if (isset($orderby_labels[$current_orderby])) {
                    $remove_url = remove_query_arg('orderby', $current_url);
                    $active_tags[] = '<a href="' . esc_url($remove_url) . '" class="filter-tag">مرتب‌سازی: ' . esc_html($orderby_labels[$current_orderby]) . ' <i class="far fa-times"></i></a>';
                }
            }

            if ($current_rcp !== '') {
                $rcp_labels = array(
                    'restricted'   => 'فقط اشتراکی',
                    'unrestricted' => 'فقط آزاد',
                );
                if (isset($rcp_labels[$current_rcp])) {
                    $remove_url = remove_query_arg('rcp_access', $current_url);
                    $active_tags[] = '<a href="' . esc_url($remove_url) . '" class="filter-tag">' . esc_html($rcp_labels[$current_rcp]) . ' <i class="far fa-times"></i></a>';
                }
            }

            if (!empty($active_tags)) : ?>
                <div class="filter-active">
                    <?php echo implode(' ', $active_tags); ?>
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="filter-tag tag-clear"><?php esc_html_e('پاک کردن همه', 'pooshe'); ?> <i class="far fa-times"></i></a>
                </div>
            <?php endif; ?>
        </form>
    </div>
    <?php
}
add_action('woocommerce_before_shop_loop', 'pooshe_advanced_filters_bar', 5);

add_action('wp_ajax_pooshe_advanced_filter', 'pooshe_advanced_filter_ajax');
add_action('wp_ajax_nopriv_pooshe_advanced_filter', 'pooshe_advanced_filter_ajax');
function pooshe_advanced_filter_ajax() {
    $ppp = apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $ppp,
        'paged'          => isset($_POST['paged']) ? max(1, intval($_POST['paged'])) : 1,
        'meta_query'     => array(),
        'tax_query'      => array(),
    );

    if (!empty($_POST['product_cat'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_POST['product_cat']),
        );
    }

    if (isset($_POST['min_price']) && $_POST['min_price'] !== '') {
        $args['meta_query'][] = array(
            'key'     => '_price',
            'value'   => floatval($_POST['min_price']),
            'type'    => 'NUMERIC',
            'compare' => '>=',
        );
    }
    if (isset($_POST['max_price']) && $_POST['max_price'] !== '') {
        $args['meta_query'][] = array(
            'key'     => '_price',
            'value'   => floatval($_POST['max_price']),
            'type'    => 'NUMERIC',
            'compare' => '<=',
        );
    }

    $rcp_access = isset($_POST['rcp_access']) ? sanitize_text_field($_POST['rcp_access']) : '';
    if ($rcp_access === 'restricted') {
        $args['meta_query'][] = array(
            'key'     => '_rcp_woo_subscription_levels_to_purchase',
            'compare' => 'EXISTS',
        );
    } elseif ($rcp_access === 'unrestricted') {
        $args['meta_query'][] = array(
            'key'     => '_rcp_woo_subscription_levels_to_purchase',
            'compare' => 'NOT EXISTS',
        );
    }

    $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'menu_order';
    switch ($orderby) {
        case 'popularity':
            $args['meta_key'] = 'total_sales';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;
        case 'rating':
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;
        case 'date':
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
        case 'price':
            $args['meta_key'] = '_price';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'ASC';
            break;
        case 'price-desc':
            $args['meta_key'] = '_price';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;
        default:
            $args['orderby'] = 'menu_order title';
            $args['order']   = 'ASC';
    }

    $query = new WP_Query($args);

    ob_start();
    wc_set_loop_prop('total', $query->found_posts);
    wc_set_loop_prop('total_pages', $query->max_num_pages);
    wc_set_loop_prop('current_page', $args['paged']);
    wc_set_loop_prop('per_page', $ppp);

    if ($query->have_posts()) {
        woocommerce_product_loop_start();
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product');
        }
        woocommerce_product_loop_end();
    } else {
        echo '<div class="pooshe-no-products">';
        echo '<p>' . esc_html__('هیچ محصولی یافت نشد.', 'pooshe') . '</p>';
        echo '</div>';
    }
    $products_html = ob_get_clean();

    ob_start();
    woocommerce_pagination();
    $pagination_html = ob_get_clean();

    ob_start();
    woocommerce_result_count();
    $count_html = ob_get_clean();

    wp_reset_postdata();

    wp_send_json_success(array(
        'products'   => $products_html,
        'pagination' => $pagination_html,
        'count'      => $count_html,
    ));
}

function pooshe_advanced_filters_scripts() {
    if (!pooshe_opt(SHOP_OPTS, 'advanced_filters', true)) {
        return;
    }
    if (!is_shop() && !is_product_category() && !is_product_tag() && !is_product_taxonomy()) {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggles = document.querySelectorAll('.filters-toggle');
        toggles.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var body = this.closest('.pooshe-advanced-filters').querySelector('.filters-body');
                if (body) {
                    body.classList.toggle('show');
                }
            });
        });

        var filterForm = document.querySelector('.pooshe-advanced-filters .filters-body');
        if (!filterForm) return;

        var shopContainer = document.querySelector('.pooshe-advanced-filters');
        var productsEl, paginationEl, countEl;

        function findShopElements() {
            productsEl = document.querySelector('.products');
            if (!productsEl) {
                productsEl = document.querySelector('ul.products');
            }
            paginationEl = document.querySelector('.woocommerce-pagination');
            countEl = document.querySelector('.woocommerce-result-count');
        }

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            findShopElements();
            if (!productsEl) return;

            shopContainer.classList.add('af-loading');

            var formData = new FormData(filterForm);
            formData.append('action', 'pooshe_advanced_filter');

            fetch(WP_OBJ.ajax_url, {
                method: 'POST',
                body: formData,
            })
            .then(function(r) { return r.json(); })
            .then(function(response) {
                if (!response.success) return;
                var data = response.data;

                var temp = document.createElement('div');
                temp.innerHTML = data.products;
                var newProducts = temp.querySelector('.products');
                if (!newProducts) {
                    newProducts = temp.querySelector('ul.products');
                }
                if (newProducts && productsEl) {
                    productsEl.innerHTML = newProducts.innerHTML;
                } else if (productsEl) {
                    productsEl.innerHTML = data.products;
                }

                if (paginationEl) {
                    paginationEl.outerHTML = data.pagination;
                }
                if (countEl) {
                    countEl.outerHTML = data.count;
                }

                history.replaceState(null, '', filterForm.action + '?' + new URLSearchParams(new FormData(filterForm)).toString());

                shopContainer.classList.remove('af-loading');
                findShopElements();
            })
            .catch(function() {
                shopContainer.classList.remove('af-loading');
            });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'pooshe_advanced_filters_scripts');
