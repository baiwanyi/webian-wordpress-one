<?php

/**
 * 分类目录查询操作
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/product
 */

/**
 * 自定义分类标签查询函数
 *
 * @since 1.0.0
 * @param array $wp_term_query
 */
function wwpo_wpmall_product_tags_parse_query($wp_term_query)
{
    // 设定请求参数
    $taxonomy   = $_REQUEST['taxonomy'] ?? '';
    $category   = $_REQUEST['category'] ?? 0;

    // 判断分类标签和分类品牌类型
    if (!in_array($taxonomy, ['product_brand', 'product_tags'])) {
        return $wp_term_query;
    }

    // 判断有分类目录请求参数并且为下拉菜单时，显示同一 parent_id 元数据字段的分类内容
    if ($category && empty($wp_term_query['dropdown'])) {
        $wp_term_query['meta_key']  = 'parent_id';
        $wp_term_query['meta_value'] = $category;
    }

    return $wp_term_query;
}
add_filter('get_terms_args', 'wwpo_wpmall_product_tags_parse_query');

/**
 * 自定义分类目录下拉菜单查询参数函数
 *
 * @since 1.0.0
 * @param array $dropdown_args
 * @param string $taxonomy
 */
function wwpo_wpmall_product_category_parent_dropdown($dropdown_args, $taxonomy)
{
    if ('product_category' != $taxonomy) {
        return $dropdown_args;
    }

    // 添加 dropdown 查询字段，用于标记查询列表按照下拉菜单查询方式显示
    $dropdown_args['dropdown'] = 'parent';

    return $dropdown_args;
}
add_filter('taxonomy_parent_dropdown_args', 'wwpo_wpmall_product_category_parent_dropdown', 10, 2);
