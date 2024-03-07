<?php

/**
 * 产品分类标签自定义
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/product
 */

/**
 * 自定义表格栏目名称设定函数
 *
 * @since 1.0.0
 * @param array $column 表格栏目名称内容数组
 */
function wwpo_wpmall_product_taxonomy_tags_columns($column)
{
    $column = [
        'cb'        => __('选择', 'wpmall'),
        'name'      => _x('Name', 'term name'),
        'slug'      => __('Slug'),
        'category'  => __('Categories'),
        'posts'     => _x('Count', 'Number/count of items')
    ];

    return $column;
}
add_filter('manage_edit-product_brand_columns', 'wwpo_wpmall_product_taxonomy_tags_columns');
add_filter('manage_edit-product_tags_columns', 'wwpo_wpmall_product_taxonomy_tags_columns');

/**
 * 自定义分类标签显示内容函数
 *
 * @since 1.0.0
 * @param string    $content
 * @param string    $column_name
 * @param integer   $term_id
 */
function wwpo_wpmall_product_taxonomy_tags_custom_column($content, $column, $term_id)
{
    $termmeta   = get_term_meta($term_id);
    $parent_id  = $termmeta['parent_id'][0] ?? 0;

    // 显示父级分类目录
    if ('category' == $column) {

        if (empty($parent_id)) {
            return '—';
        }

        $page_url = admin_url('edit-tags.php');
        $page_url = add_query_arg([
            'taxonomy'  => $_GET['taxonomy'],
            'post_type' => 'product',
            'category'  => $parent_id
        ], $page_url);

        // 显示分类目录名称和列表链接
        return sprintf('<a href="%s">%s</a>',  $page_url, get_term_field('name', $parent_id, 'product_category'));
    }
}
add_filter('manage_product_brand_custom_column', 'wwpo_wpmall_product_taxonomy_tags_custom_column', 10, 3);
add_filter('manage_product_tags_custom_column', 'wwpo_wpmall_product_taxonomy_tags_custom_column', 10, 3);

/**
 * 分类标签编辑表单，添加分类目录选择函数
 *
 * @since 1.0.0
 * @param WP_Term $term
 */
function wwpo_wpmall_product_taxonomy_tags_edit_form_fields($term)
{
    $termmeta   = get_term_meta($term->term_id);
    $parent_id  = $termmeta['parent_id'][0] ?? 0;

    echo '<tr class="form-field"><th scope="row">所属分类</th><td>';
    echo wwpo_wpmall_product_taxonomy_tags_parent_fields($parent_id);
    echo ' </td></tr>';
}
add_action('product_brand_edit_form_fields', 'wwpo_wpmall_product_taxonomy_tags_edit_form_fields');
add_action('product_tags_edit_form_fields', 'wwpo_wpmall_product_taxonomy_tags_edit_form_fields');

/**
 * 分类标签添加表单，添加分类目录选择函数
 *
 * @since 1.0.0
 * @param string $taxonomy
 */
function wwpo_wpmall_product_taxonomy_tags_add_form_fields($taxonomy)
{
    if (!in_array($taxonomy, ['product_brand', 'product_tags'])) {
        return;
    }

    echo '<div class="form-field term-parent-wrap"><label for="parent">所属分类</label>';
    echo wwpo_wpmall_product_taxonomy_tags_parent_fields();
    echo '</div>';
}
add_action('add_tag_form_fields', 'wwpo_wpmall_product_taxonomy_tags_add_form_fields');

/**
 * 分类标签更新操作函数
 *
 * @since 1.0.0
 * @param integer $term_id
 * @param integer $tt_id
 * @param string $taxonomy
 */
function wwpo_wpmall_product_taxonomy_terms_updated($term_id, $tt_id, $taxonomy)
{
    if (!in_array($taxonomy, ['product_brand', 'product_tags'])) {
        return;
    }

    global $wpdb;

    // 获取一级分类目录 ID 列表
    $parent_ids = get_terms([
        'taxonomy'      => 'product_category',
        'hide_empty'    => false,
        'parent'        => 0,
        'fields'        => 'ids',
        'update_term_meta_cache' => false
    ]);

    // 获取当前分类标签类型 ID 列表
    $taxonomy_ids = get_terms([
        'taxonomy'      => $taxonomy,
        'hide_empty'    => false,
        'fields'        => 'ids',
        'update_term_meta_cache' => false
    ]);

    // 当前分类标签转换成 SQL 查询格式
    $taxonomy_ids = implode("','", $taxonomy_ids);

    // 判断一级分类目录 ID 不为空
    if ($parent_ids) {

        // 遍历一级分类目录，统计当前分类目录 ID 下分类标签的数量
        foreach ($parent_ids as $parent_id) {
            $parent_term_total = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->termmeta WHERE meta_key = 'parent_id' AND meta_value = $parent_id AND term_id IN ('{$taxonomy_ids}')");
            update_term_meta($parent_id, '_wwpo_total_' . $taxonomy, $parent_term_total);
        }
    }

    // 获取一级分类目录 ID
    $parent_term_id = $_POST['parent_id'] ?? 0;

    if (empty($parent_term_id)) {
        return;
    }

    // 更新当前分类标签 parent_id 元数据
    update_term_meta($term_id, 'parent_id', $parent_term_id);
}
add_action('saved_term', 'wwpo_wpmall_product_taxonomy_terms_updated', 10, 3);

/**
 * 分类标签删除操作函数
 *
 * @since 1.0.0
 * @param integer $term_id
 * @param string $taxonomy
 */
function wwpo_wpmall_product_taxonomy_pre_delete_term($term_id, $taxonomy)
{
    if (!in_array($taxonomy, ['product_brand', 'product_tags'])) {
        return;
    }

    // 判断当前分类标签是否设定了分类目录 ID
    $parent_term_id = get_term_meta($term_id, 'parent_id', true);
    if (empty($parent_term_id)) {
        return;
    }

    global $wpdb;

    // 统计当前分类标签的分类目录 ID 下包含标签数量
    $parent_term_total = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->termmeta WHERE meta_key = 'parent_id' AND meta_value = $parent_term_id");

    // 更新标签统计数量 - 1
    update_term_meta($parent_term_id, '_wwpo_total_' . $taxonomy, $parent_term_total - 1);
}
add_action('pre_delete_term', 'wwpo_wpmall_product_taxonomy_pre_delete_term', 10, 2);

/**
 * 分类标签选择所属分类目录下拉菜单函数
 *
 * @since 1.0.0
 * @param integer $current_id
 */
function wwpo_wpmall_product_taxonomy_tags_parent_fields($current_id = 0)
{
    // 获取父级分类目录列表
    $parent_terms = get_terms([
        'taxonomy'      => 'product_category',
        'parent'        => 0,
        'hide_empty'    => false,
        'dropdown'      => true,
        'fields'        => 'id=>name',
        'update_term_meta_cache' => false
    ]);

    // 显示下拉菜单
    return WWPO_Form::field('parent_id', [
        'field' => [
            'type'          => 'select',
            'option'        => $parent_terms,
            'selected'      => $current_id,
            'show_option_all' => '选择所属分类目录'
        ]
    ]);
}
