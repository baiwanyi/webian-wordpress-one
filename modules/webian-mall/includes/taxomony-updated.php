<?php

/**
 * 分类目录更新操作
 *
 * @since 1.0.0
 * @package Webian WP Mall
 */

/**
 * 分类目录保存操作函数
 *
 * @since 1.0.0
 * @param integer $term_id
 * @param integer $tt_id
 * @param string $taxonomy
 * @param boolean $update
 */
function wwpo_wpmall_taxonomy_category_saved_term($term_id, $tt_id, $taxonomy, $update)
{
    /**
     * 新建分类目录写入菜单排序默认值
     *
     * @since 1.0.0
     */
    if (!$update) {
        update_term_meta($term_id, '_wwpo_category_menu_order', 0);
        return;
    }

    global $wpdb;

    // 获取一级分类目录 ID 列表
    $parent_ids = get_terms([
        'taxonomy'      => $taxonomy,
        'hide_empty'    => false,
        'parent'        => 0,
        'fields'        => 'ids',
        'update_term_meta_cache' => false
    ]);

    // 统计更新一级分类目录下子目录数量
    if ($parent_ids) {
        foreach ($parent_ids as $parent_id) {
            $parent_term_total = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE taxonomy = '{$taxonomy}' AND parent = $parent_id");
            update_term_meta($parent_id, '_wwpo_total_' . $taxonomy, $parent_term_total);
        }
    }

    // 更新图标
    if (!empty($_POST['iconfont'])) {
        update_term_meta($term_id, 'iconfont', $_POST['iconfont']);
    }

    if (!empty($_POST['thumb_id'])) {
        update_term_meta($term_id, 'thumb_id', $_POST['thumb_id']);
    }

    if (isset($_POST['parent_id'])) {
        update_term_meta($term_id, 'parent_id', $_POST['parent_id']);
    }
}
add_action('saved_term', 'wwpo_wpmall_taxonomy_category_saved_term', 10, 4);

/**
 * 分类目录删除操作函数
 *
 * @since 1.0.0
 * @param integer $term_id
 * @param integer $tt_id
 * @param WP_Term $term
 */
function wwpo_wpmall_taxonomy_category_delete_term($term, $tt_id, $taxonomy)
{
    // 判断为子目录时，删除目录会把父级分类目录下子目录数量 - 1
    if ($term->parent) {
        global $wpdb;
        $wpdb->query("UPDATE $wpdb->termmeta SET meta_value = meta_value - 1 WHERE term_id = {$term->parent} AND meta_key = '_wwpo_total_{$taxonomy}'");
    }
}
add_action('delete_term', 'wwpo_wpmall_taxonomy_category_delete_term', 10, 3);

/**
 * 自定义分类目录别名函数（新建分类）
 * 设定分类别名为 20 位纯数字
 *
 * @since 1.0.0
 * @param array $data
 * @param string $taxonomy
 * @param array $args
 */
function wwpo_wpmall_taxonomy_insert_term_data($data, $taxonomy, $args)
{
    $option_data = get_option(OPTION_CONTENTS_KEY);

    if (empty($option_data)) {
        return $data;
    }

    $option_data = wp_list_filter($option_data, ['type' => 'taxonomy']);
    $option_data = array_column($option_data, 'slug');

    if (!in_array($taxonomy, $option_data)) {
        return $data;
    }

    // 判断分类别名为空，或别名不为数字类型时
    if (empty($args['slug']) || !is_numeric($args['slug'])) {
        $data['slug'] = wwpo_unique(NOW, 20);
    }

    return $data;
}
add_filter('wp_insert_term_data', 'wwpo_wpmall_taxonomy_insert_term_data', 10, 3);

/**
 * 自定义分类目录别名函数（更新分类）
 * 设定分类别名为 20 位纯数字
 *
 * @since 1.0.0
 * @param array $data
 * @param integer $term_id
 * @param string $taxonomy
 * @param array $args
 */
function wwpo_wpmall_taxonomy_update_term_data($data, $term_id, $taxonomy, $args)
{
    $option_data = get_option(OPTION_CONTENTS_KEY);

    if (empty($option_data)) {
        return $data;
    }

    $option_data = wp_list_filter($option_data, ['type' => 'taxonomy']);
    $option_data = array_column($option_data, 'slug');

    if (!in_array($taxonomy, $option_data)) {
        return $data;
    }

    // 判断分类别名为空，或别名不为数字类型时
    if (empty($args['slug']) || !is_numeric($args['slug'])) {
        $data['slug'] = wwpo_unique(NOW, 20);
    }

    return $data;
}
add_filter('wp_update_term_data', 'wwpo_wpmall_taxonomy_update_term_data', 10, 4);

/**
 * 分类目录排序操作函数
 *
 * @since 1.0.0
 */
function wwpo_ajax_wpmall_taxonomy_category_order()
{
    $term_ids = $_POST['term_ids'] ?? [];

    if (empty($term_ids)) {
        echo wwpo_json_send(['icon' => 'error', 'title' => '排序为空']);
        exit;
    }

    foreach ($term_ids as $i => $term_id) {
        update_term_meta($term_id, '_wwpo_category_menu_order', $i + 1);
    }

    echo wwpo_json_send(['icon' => 'success', 'title' => '排序完成']);
    exit;
}
add_action('wp_ajax_category_order_updated', 'wwpo_ajax_wpmall_taxonomy_category_order');
