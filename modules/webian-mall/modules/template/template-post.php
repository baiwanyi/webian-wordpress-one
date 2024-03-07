<?php

/**
 * 自定义模板查询语句函数
 *
 * @since 1.0.0
 * @param array     $clause     查询语句
 * @param WP_Query  wp_query
 */
function wwpo_wpmall_template_query_posts($clauses, $wp_query)
{
    global $wpdb;

    // 获取文章类型
    $post_type = $wp_query->query['post_type'] ?? '';

    /** 判断不为模板格式，返回默认查询 */
    if ('template' != $post_type) {
        return $clauses;
    }

    /**
     * 判断是否是搜收藏列表
     *
     * 查询用户收藏列表，并按收藏时间排序
     */
    if (isset($wp_query->query['favor'])) {
        $clauses['join'] .= sprintf(' RIGHT JOIN %s fav ON %s.ID = fav.post_id', WWPO_SQL_TMPL_FAVOR, $wpdb->posts);
        $clauses['where'] = " AND fav.user_id = {$wp_query->query['favor']}";
        $clauses['orderby'] = 'fav.time_post';
    }

    // 返回查询语句
    return $clauses;
}
add_filter('posts_clauses', 'wwpo_wpmall_template_query_posts', 10, 2);

/**
 * Undocumented function
 *
 * @param [type] $post
 * @param array $favors
 * @param string $size
 */
function wwpo_template_format_posts($post, $favors = [], $size = 'thumbnail')
{
    $postmeta = get_post_meta($post->ID);
    $thumb_id = $postmeta['_thumbnail_id'][0] ?? 0;

    $data = [
        'post_id'   => (int) $post->ID,
        'title'     => $post->post_title,
        'barcode'   => $post->post_name,
        'favor'     => 0
    ];

    if (in_array($post->ID, $favors)) {
        $data['favor'] = 1;
    }

    if ($thumb_id) {
        $data['thumb'] = wp_get_attachment_image_url($thumb_id, $size);
    }

    $post_topic = wp_get_post_terms($post->ID, 'template_tags');
    if ($post_topic) {
        $data['tags'] = array_column($post_topic, 'name', 'term_id');
    }

    return $data;
}
