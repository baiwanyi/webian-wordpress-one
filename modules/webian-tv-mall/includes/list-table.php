<?php

/**
 * 作品列表页面自定义
 *
 * @since 1.0.0
 * @package Yanjiushe
 */

/**
 * 自定义表格栏目名称设定函数
 *
 * @since 1.0.0
 * @param array $column 表格栏目名称内容数组
 */
function wwpo_playlet_columns($column)
{
    $column = [
        'cb'            => __('选择', 'yjscc'),
        'thumb'         => __('封面', 'yjscc'),
        'title'         => __('Title'),
        'small-search'  => __('剧集数', 'yjscc'),
        'small-click'   => __('点击数', 'yjscc'),
        'small-favor'   => __('收藏量', 'yjscc'),
        'date'          => __('Date')
    ];

    return $column;
}
add_filter('manage_edit-playlet_columns', 'wwpo_playlet_columns');

/**
 * 自定义表格列输出函数
 *
 * @since 1.0.0
 * @param string    $column     表格列名称
 * @param integer   $post_id    作品编号
 */
function wwpo_playlet_custom_column($column)
{
    global $post, $wpdb;

    $post_meta = get_post_meta($post->ID);

    switch ($column) {
            // 作品封面
        case 'thumb':

            // 获取作品特色图片地址
            $post_thumbnail_id  = $post_meta['_thumbnail_id'][0] ?? 0;

            // 判断未设置特色图片则获取图片 ID
            if (empty($post_thumbnail_id) && 'image' == $post->post_mime_type) {
                $post_thumbnail_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_parent = $post->ID AND post_type = 'attachment' ORDER BY menu_order DESC");
            }

            if (empty($post_thumbnail_id)) {
                return;
            }

            printf(
                '<div class="ratio ratio-1x1"><img src="%s" class="thumb rounded"></div><span class="dashicons dashicons-format-%s text-white position-absolute top-0 end-0 m-3"></span>',
                wp_get_attachment_image_url($post_thumbnail_id),
                $post->post_mime_type
            );
            break;

            // 作品搜索重
        case 'small-search':
            echo $post->menu_order;
            break;

            // 作品点击数
        case 'small-click':
            // echo $post_meta[YJS_POST_CLICK_KEY][0] ?? 0;
            break;

            // 作品收藏数
        case 'small-favor':
            // echo $post_meta[YJS_POST_FAVOR_KEY][0] ?? 0;
            break;

        default:
            break;
    }
}
add_action('manage_playlet_posts_custom_column', 'wwpo_playlet_custom_column');

/**
 * 自定义表格排序函数
 *
 * @since 1.0.0
 * @param array $columns 排序列表别名数组，索引为列表别名，值为排序数据库字段
 */
function wwpo_playlet_sortable_columns($columns)
{
    $columns['small-search']    = 'search';
    $columns['small-click']     = 'click';
    $columns['small-favor']     = 'favor';
    return $columns;
}
add_filter('manage_edit-playlet_sortable_columns', 'wwpo_playlet_sortable_columns');
