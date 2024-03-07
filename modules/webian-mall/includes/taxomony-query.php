<?php

/**
 * 分类目录查询操作
 *
 * @since 1.0.0
 * @package Webian WP Mall
 */

/**
 * 自定义分类目录查询函数
 *
 * @since 1.0.0
 * @param array $wp_term_query
 */
function wwpo_wpmall_taxonomy_category_parse_query($wp_term_query)
{
    // 设定请求参数
    $post_type  = $_REQUEST['post_type'] ?? '';
    $taxonomy   = $_REQUEST['taxonomy'] ?? '';
    $orderby    = $_REQUEST['orderby'] ?? '';
    $parent     = $_REQUEST['parent'] ?? 0;

    // 判断动作操作请求参数，则不为列表显示
    if (isset($_REQUEST['action'])) {
        return $wp_term_query;
    }

    // 判断分类目录
    if (!in_array($taxonomy, ['product_category', 'template_category'])) {
        return $wp_term_query;
    }

    // 判断显示当前父级分类 ID 下的目录
    // 作用于分类目录列表显示
    if (in_array($post_type, ['product', 'template']) && in_array($taxonomy, ['product_category', 'template_category'])) {
        $wp_term_query['parent'] = $parent;
    }

    // 判断显示子分类目录
    // 作用于产品发布编辑页面，分类品牌和分类标签显示
    if (isset($wp_term_query['secondary'])) {
        $wp_term_query['parent'] = $wp_term_query['secondary'];
    }

    // 判断下拉菜单
    // 作用于分类标签添加和分类编辑表单
    if (isset($wp_term_query['dropdown'])) {
        $wp_term_query['parent'] = 0;
    }

    // 判断排序类型，按照「排序」元数据字段排序
    if ('meta_value_num' == $orderby) {
        $wp_term_query['meta_key'] = '_wwpo_category_menu_order';
    }

    return $wp_term_query;
}
add_filter('get_terms_args', 'wwpo_wpmall_taxonomy_category_parse_query');
