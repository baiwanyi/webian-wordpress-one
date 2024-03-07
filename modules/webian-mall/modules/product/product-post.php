<?php

/**
 * 产品内容展示
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/product
 */

/**
 * 自定义产品查询语句函数
 *
 * @since 1.0.0
 * @param array     $clause     查询语句
 * @param WP_Query  wp_query
 */
function wwpo_wpmall_product_query_posts($clauses, $wp_query)
{
    global $wpdb;

    // 获取文章类型
    $post_type      = $wp_query->query['post_type'] ?? '';
    $post_search    = $wp_query->query['s'] ?? null;

    /** 判断不为产品格式，返回默认查询 */
    if ('product' != $post_type) {
        return $clauses;
    }

    // 设定联结查询语句
    $clauses['join'] .= sprintf(' LEFT JOIN %s pr ON %s.ID = pr.post_id', WWPO_SQL_PRODUCT, $wpdb->posts);

    // 设定产品数据库表查询字段
    $clauses['fields'] .= ', pr.*';

    /**
     * 判断搜索关键为数字和字母组合
     *
     * 查询产品编码
     */
    if ($post_search) {

        if (ctype_alnum($post_search)) {
            $clauses['where'] = " AND pr.barcode LIKE '%{$post_search}%'";
        }
    }

    // 返回查询语句
    return $clauses;
}
add_filter('posts_clauses', 'wwpo_wpmall_product_query_posts', 10, 2);

/**
 * 将获取的内容进行格式化显示函数
 *
 * @since 1.0.0
 * @param object    $post
 * @param boolean   $favor 是否被收藏，默认：false
 */
function wwpo_product_format_posts($post, $size = 'thumbnail')
{
    global $wpdb;
    $post_thumbnail_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_parent = $post->ID AND post_type = 'attachment' ORDER BY menu_order DESC");

    $price_sale = $post->price_sale ?? 0;
    if ($price_sale) {
        $price_sale = $price_sale / 100;
        $price_sale = number_format($price_sale, 2);
    }

    $data = [
        'post_id'       => (int) $post->ID,
        'product_id'    => (int) $post->product_id,
        'barcode'       => $post->barcode,
        'promo'         => $post->promo,
        'title'         => $post->post_title,
        'price'         => $price_sale
    ];

    if ($post_thumbnail_id) {
        $data['thumb'] = wp_get_attachment_image_url($post_thumbnail_id, $size);
    }

    return $data;
}
