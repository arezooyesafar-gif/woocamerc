<?php
/**
 * فیلدهای دسترسی پلن به محتوای وبلاگ (دسته و تگ نوشته)
 *
 * @var object|null $plan
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_categories = get_terms(array(
    'taxonomy' => 'category',
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC',
));
if (is_wp_error($post_categories) || !is_array($post_categories)) {
    $post_categories = array();
}
$post_categories_by_parent = array();
foreach ($post_categories as $cat_item) {
    $parent_id = isset($cat_item->parent) ? (int) $cat_item->parent : 0;
    if (!isset($post_categories_by_parent[$parent_id])) {
        $post_categories_by_parent[$parent_id] = array();
    }
    $post_categories_by_parent[$parent_id][] = $cat_item;
}
$render_post_category_tree = static function(array $nodes_by_parent, int $parent_id, string $input_name, array $selected_ids, array $ancestor_ids = array()) use (&$render_post_category_tree) {
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
                <?php if ($has_children) : ?>
                    <span class="access-tree-dot" aria-hidden="true"></span>
                <?php else : ?>
                    <span class="access-tree-leaf" aria-hidden="true"></span>
                <?php endif; ?>
                <input type="checkbox" name="<?php echo esc_attr($input_name); ?>[]" value="<?php echo esc_attr($term_id); ?>" data-term-id="<?php echo esc_attr($term_id); ?>" <?php checked(in_array($term_id, $selected_ids, true)); ?>>
                <span class="access-item-label"><?php echo esc_html($cat_node->name); ?></span>
                <span class="access-item-count">(<?php echo esc_html($cat_node->count); ?>)</span>
            </span>
        </label>
        <?php
        $render_post_category_tree($nodes_by_parent, $term_id, $input_name, $selected_ids, $next_ancestors);
    }
};
?>
<tr>
    <th><label><?php esc_html_e('دسترسی به محتوای وبلاگ (دسته‌بندی نوشته)', 'eshterakyar'); ?></label></th>
    <td>
        <p class="description" style="margin-bottom: 12px;"><?php esc_html_e('جدا از دسته‌بندی محصولات ووکامرس. خالی = همه دسته‌های نوشته برای این پلن؛ در صورت انتخاب، فقط نوشته‌های همان دسته‌ها با اشتراک این پلن قابل مشاهده‌اند.', 'eshterakyar'); ?></p>
        <div class="eshterakyar-access-control">
            <div class="access-control-grid">
                <div class="access-control-box allowed-box">
                    <div class="access-control-header">
                        <span class="access-icon">✅</span>
                        <h4><?php esc_html_e('دسته‌های نوشته مجاز', 'eshterakyar'); ?></h4>
                        <span class="access-badge allowed-badge"><?php esc_html_e('اختیاری', 'eshterakyar'); ?></span>
                    </div>
                    <?php
                    $include_children_allowed_post = true;
                    if ($plan && isset($plan->include_children_allowed_post_categories)) {
                        $include_children_allowed_post = (int) $plan->include_children_allowed_post_categories === 1;
                    }
                    ?>
                    <div style="margin: 8px 0 12px;">
                        <label>
                            <input type="checkbox" name="include_children_allowed_post_categories" value="1" <?php checked($include_children_allowed_post); ?>>
                            <?php esc_html_e('اعمال روی زیر دسته‌بندی‌ها', 'eshterakyar'); ?>
                        </label>
                    </div>
                    <div class="access-search-box">
                        <input type="text" class="access-search" placeholder="<?php esc_attr_e('جست‌وجو...', 'eshterakyar'); ?>" data-target="allowed_post_categories">
                    </div>
                    <div class="access-items-container access-items-tree category-tree-container" data-input-name="allowed_post_categories[]">
                        <?php
                        $selected_post_cats = array();
                        if ($plan && !empty($plan->allowed_post_categories)) {
                            $selected_post_cats = array_map('intval', explode(',', $plan->allowed_post_categories));
                        }
                        $render_post_category_tree($post_categories_by_parent, 0, 'allowed_post_categories', $selected_post_cats);
                        ?>
                    </div>
                    <div class="access-actions">
                        <button type="button" class="access-select-all" data-target="allowed_post_categories"><?php esc_html_e('انتخاب همه', 'eshterakyar'); ?></button>
                        <button type="button" class="access-deselect-all" data-target="allowed_post_categories"><?php esc_html_e('لغو همه', 'eshterakyar'); ?></button>
                    </div>
                </div>
                <div class="access-control-box excluded-box">
                    <div class="access-control-header">
                        <span class="access-icon">🚫</span>
                        <h4><?php esc_html_e('دسته‌های نوشته مستثنی', 'eshterakyar'); ?></h4>
                    </div>
                    <?php
                    $include_children_excluded_post = true;
                    if ($plan && isset($plan->include_children_exclude_post_categories)) {
                        $include_children_excluded_post = (int) $plan->include_children_exclude_post_categories === 1;
                    }
                    ?>
                    <div style="margin: 8px 0 12px;">
                        <label>
                            <input type="checkbox" name="include_children_exclude_post_categories" value="1" <?php checked($include_children_excluded_post); ?>>
                            <?php esc_html_e('اعمال روی زیر دسته‌بندی‌ها', 'eshterakyar'); ?>
                        </label>
                    </div>
                    <div class="access-search-box">
                        <input type="text" class="access-search" placeholder="<?php esc_attr_e('جست‌وجو...', 'eshterakyar'); ?>" data-target="exclude_post_categories">
                    </div>
                    <div class="access-items-container access-items-tree category-tree-container" data-input-name="exclude_post_categories[]">
                        <?php
                        $excluded_post_cats = array();
                        if ($plan && !empty($plan->exclude_post_categories)) {
                            $excluded_post_cats = array_map('intval', explode(',', $plan->exclude_post_categories));
                        }
                        $render_post_category_tree($post_categories_by_parent, 0, 'exclude_post_categories', $excluded_post_cats);
                        ?>
                    </div>
                    <div class="access-actions">
                        <button type="button" class="access-select-all" data-target="exclude_post_categories"><?php esc_html_e('انتخاب همه', 'eshterakyar'); ?></button>
                        <button type="button" class="access-deselect-all" data-target="exclude_post_categories"><?php esc_html_e('لغو همه', 'eshterakyar'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
<tr>
    <th><label><?php esc_html_e('دسترسی به محتوای وبلاگ (تگ نوشته)', 'eshterakyar'); ?></label></th>
    <td>
        <?php
        $post_tags = get_terms(array('taxonomy' => 'post_tag', 'hide_empty' => false));
        if (is_wp_error($post_tags)) {
            $post_tags = array();
        }
        ?>
        <div class="eshterakyar-access-control">
            <div class="access-control-grid">
                <div class="access-control-box allowed-box">
                    <div class="access-control-header">
                        <span class="access-icon">✅</span>
                        <h4><?php esc_html_e('تگ‌های نوشته مجاز', 'eshterakyar'); ?></h4>
                    </div>
                    <div class="access-search-box">
                        <input type="text" class="access-search" placeholder="<?php esc_attr_e('جست‌وجو...', 'eshterakyar'); ?>" data-target="allowed_post_tags">
                    </div>
                    <div class="access-items-container">
                        <?php
                        $selected_post_tags = array();
                        if ($plan && !empty($plan->allowed_post_tags)) {
                            $selected_post_tags = array_map('intval', explode(',', $plan->allowed_post_tags));
                        }
                        foreach ($post_tags as $tag) :
                            ?>
                            <label class="access-item">
                                <input type="checkbox" name="allowed_post_tags[]" value="<?php echo esc_attr($tag->term_id); ?>" <?php checked(in_array((int) $tag->term_id, $selected_post_tags, true)); ?>>
                                <span class="access-item-label"><?php echo esc_html($tag->name); ?></span>
                                <span class="access-item-count">(<?php echo esc_html($tag->count); ?>)</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="access-actions">
                        <button type="button" class="access-select-all" data-target="allowed_post_tags"><?php esc_html_e('انتخاب همه', 'eshterakyar'); ?></button>
                        <button type="button" class="access-deselect-all" data-target="allowed_post_tags"><?php esc_html_e('لغو همه', 'eshterakyar'); ?></button>
                    </div>
                </div>
                <div class="access-control-box excluded-box">
                    <div class="access-control-header">
                        <span class="access-icon">🚫</span>
                        <h4><?php esc_html_e('تگ‌های نوشته مستثنی', 'eshterakyar'); ?></h4>
                    </div>
                    <div class="access-search-box">
                        <input type="text" class="access-search" placeholder="<?php esc_attr_e('جست‌وجو...', 'eshterakyar'); ?>" data-target="exclude_post_tags">
                    </div>
                    <div class="access-items-container">
                        <?php
                        $excluded_post_tags = array();
                        if ($plan && !empty($plan->exclude_post_tags)) {
                            $excluded_post_tags = array_map('intval', explode(',', $plan->exclude_post_tags));
                        }
                        foreach ($post_tags as $tag) :
                            ?>
                            <label class="access-item">
                                <input type="checkbox" name="exclude_post_tags[]" value="<?php echo esc_attr($tag->term_id); ?>" <?php checked(in_array((int) $tag->term_id, $excluded_post_tags, true)); ?>>
                                <span class="access-item-label"><?php echo esc_html($tag->name); ?></span>
                                <span class="access-item-count">(<?php echo esc_html($tag->count); ?>)</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="access-actions">
                        <button type="button" class="access-select-all" data-target="exclude_post_tags"><?php esc_html_e('انتخاب همه', 'eshterakyar'); ?></button>
                        <button type="button" class="access-deselect-all" data-target="exclude_post_tags"><?php esc_html_e('لغو همه', 'eshterakyar'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
