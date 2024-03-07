<?php

/**
 * 模板列表页面
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/template
 */

/**
 * 自定义表格栏目名称设定函数
 *
 * @since 1.0.0
 * @param array $column 表格栏目名称内容数组
 */
function wwpo_wpmall_template_columns($column)
{
    $column = [
        'cb'            => __('选择', 'wpmall'),
        'thumb'         => __('封面', 'wpmall'),
        'title'         => __('Title'),
        'taxonomy-template_category' => __('Categories'),
        'medium-barcode' => __('编号', 'wpmall'),
        'small-favor'   => __('收藏数', 'wpmall'),
        'small-order'   => __('订单', 'wpmall'),
        'date'          => __('Date')
    ];

    return $column;
}
add_filter('manage_edit-template_columns', 'wwpo_wpmall_template_columns');

/**
 * 自定义表格列输出函数
 *
 * @since 1.0.0
 * @param string    $column     表格列名称
 * @param integer   $post_id    作品编号
 */
function wwpo_wpmall_template_custom_column($column)
{
    global $post, $wpdb;

    $post_meta = get_post_meta($post->ID);

    switch ($column) {
            // 产品封面
        case 'thumb':

            $post_thumbnail_id = $post_meta['_thumbnail_id'][0] ?? 0;

            if (empty($post_thumbnail_id)) {
                return;
            }

            printf(
                '<div class="ratio ratio-1x1"><img src="%s" class="thumb rounded"></div>',
                wp_get_attachment_image_url($post_thumbnail_id)
            );

            break;

            // 收藏数量
        case 'small-favor':
            echo $post_meta[WWPO_TMPL_META_FAVOR][0] ?? 0;
            break;

            // 订单数量
        case 'small-order':
            echo $post_meta[WWPO_TMPL_META_ORDER][0] ?? 0;
            break;

            // 货号
        case 'medium-barcode':
            echo $post->post_name;
            break;

        default:
            break;
    }
}
add_action('manage_template_posts_custom_column', 'wwpo_wpmall_template_custom_column');

/**
 * 禁止按月份筛选函数
 *
 * @since 1.0.0
 * @param boolean   $disabled   是否禁止，默认：false
 * @param string    $post_type
 */
function wwpo_wpmall_template_disable_months_dropdown($disabled, $post_type)
{
    if ('template' == $post_type) {
        return true;
    }

    return $disabled;
}
add_filter('disable_months_dropdown', 'wwpo_wpmall_template_disable_months_dropdown', 10, 2);
