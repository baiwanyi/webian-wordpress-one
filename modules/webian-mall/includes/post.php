<?php

/**
 * 插件核心发布函数
 *
 * @since 1.0.0
 * @package Webian WP Mall
 */

/**
 * 自定义媒体库显示查询函数
 * 设定文章上传界面只显示该文章关联的媒体，或未被任何文章关联（post_id 为 0）的媒体文件
 *
 * @since 1.0.0
 * @param WP_Query $wp_query
 */
function wwpo_wpmall_restrict_media_library($wp_query)
{
    global $pagenow;

    /** 判断 AJAX 动作页面和查询媒体动作 */
    if ('admin-ajax.php' != $pagenow || 'query-attachments' != $_REQUEST['action']) {
        return;
    }

    // 设定显示媒体关联的 post_id，以及未关联的媒体（post_id 为 0）
    $post_id = $_REQUEST['post_id'] ?? 0;

    // if (empty($post_id)) {
    //     return;
    // }

    // 设定查询的文章 ID
    $post_ids[] = 0;
    $post_ids[] = $post_id;
    $post_ids   = array_unique($post_ids);

    // 设定查询方法
    $wp_query->set('orderby', 'parent');
    $wp_query->set('post_parent__in', $post_ids);
}
add_action('pre_get_posts', 'wwpo_wpmall_restrict_media_library');

/**
 * 获取自定义分类法内容数组函数
 * 分类目录别名设定为 {post_type}_category，分类标签为 {post_type}_tags
 * 分类目录最大设置二级分类
 * 一级分类目录如无子分类则显示该分类内容
 *
 * @since 1.0.0
 * @param string $post_type
 */
function wwpo_wpmall_get_terms($post_type)
{
    // 初始化内容
    $data = [];

    // 获取所有分类目录内容，按照「排序」字段进行排序
    $all_category = get_terms([
        'taxonomy'      => $post_type . '_category',
        'orderby'       => 'meta_value_num',
        'meta_key'      => '_wwpo_category_menu_order',
        'hide_empty'    => false,
        'update_term_meta_cache' => false
    ]);

    if (empty($all_category)) {
        return;
    }

    /**
     * 设定一级分类目录内容数组
     */
    $parent_index = 0;
    $parent_category = wp_list_filter($all_category, ['parent' => 0]);

    // 遍历一级分类目录内容数组
    foreach ($parent_category as $category) {

        // 获取当前分类元数据信息
        $parent_meta    = get_term_meta($category->term_id);
        $iconfont       = $parent_meta['iconfont'][0] ?? '';
        $parent_thumb   = $parent_meta['thumb_id'][0] ?? 0;

        // 设定分类内容内容数组
        $data['category'][$parent_index] = [
            'id'    => $category->term_id,
            'title' => $category->name
        ];

        // 设定一级分类图标
        if ($iconfont) {
            $data['category'][$parent_index]['icon'] = $iconfont;
        }

        // 获取当前一级目录下的标签内容
        $category_tags = get_terms([
            'taxonomy'      => $post_type . '_tags',
            'meta_key'      => 'parent_id',
            'meta_value'    => $category->term_id,
            'hide_empty'    => false,
            'update_term_meta_cache' => false
        ]);

        // 设定标签内容数组
        if ($category_tags) {
            $data['category'][$parent_index]['tags'] = array_column($category_tags, 'name', 'term_id');
        }

        /**
         * 设定二级分类目录内容数组
         */
        $submenu_category = wp_list_filter($all_category, ['parent' => $category->term_id]);

        /**
         * 判断没有二级分类目录，显示当前分类内容列表
         */
        if (empty($submenu_category)) {
            $data['category'][$parent_index]['list'] = wwpo_wpmall_get_posts([
                'post_type' => $post_type,
                'slug'      => 'recommend',
                'category'  => $category->term_id
            ]);
        }
        // 显示二级分类目录
        else {

            $submenu_index = 1;

            $data['category'][$parent_index]['terms'][0] = [
                'id'    => $category->term_id,
                'title' => '全部'
            ];

            // 遍历二级分类目录内容数组
            foreach ($submenu_category as $submenu) {

                // 获取当前分类元数据信息
                $submenu_meta   = get_term_meta($submenu->term_id);
                $submenu_thumb  = $submenu_meta['thumb_id'][0] ?? 0;

                // 设定二级分类列表内容
                $data['category'][$parent_index]['terms'][$submenu_index] = [
                    'id'    => $submenu->term_id,
                    'title' => $submenu->name
                ];

                // 判断有缩略图
                if ($submenu_thumb) {
                    $data['category'][$parent_index]['terms'][$submenu_index]['thumb'] = wp_get_attachment_url($submenu_thumb);
                }

                $submenu_index++;
            }
        }

        // 设定一级分类缩略图
        if ($parent_thumb) {
            $parent_thumb_url = wp_get_attachment_url($parent_thumb);
            $data['category'][$parent_index]['thumb'] = $parent_thumb_url;
            $data['category'][$parent_index]['terms'][0]['thumb'] = $parent_thumb_url;
        }

        $parent_index++;
    }

    // 获取当前一级目录下的标签内容
    $parent_tags = get_terms([
        'taxonomy'      => $post_type . '_tags',
        'meta_key'      => 'parent_id',
        'meta_value'    => 0,
        'hide_empty'    => false,
        'update_term_meta_cache' => false
    ]);

    // 设定标签内容数组
    if (empty($parent_tags) || isset($parent_tags->errors)) {
        return $data;
    }

    $data['tags'] = array_column($parent_tags, 'name', 'term_id');

    return $data;
}

/**
 * 获取自定义文章内容数组函数
 *
 * @since 1.0.0
 * @param array $request
 * {
 *  请求参数
 *  @var string     orderby     列表显示排序，默认：recommend
 *  @var string     size        缩略图显示尺寸，默认：thumbnail
 *  @var string     post_type   文章类型，默认：post
 *  @var integer    limit       显示数量，默认：posts_per_page
 *  @var integer    paged       页码，默认：1
 *  @var string     search      搜索关键字
 *  @var string     tag         查询标签
 *  @var string     category    查询分类
 * }
 */
function wwpo_wpmall_get_posts($request)
{
    // 初始化列表数组
    $data = [];

    // 设定查询方法默认值
    $order_key = $request['orderby'] ?? 'recommend';

    // 设定图片缩略图默认值
    $size = $request['size'] ?? 'thumbnail';

    // 设定文章类型默认值
    $post_type = $request['post_type'] ?? 'post';

    // 设定生成列表默认值
    $post_query = [
        'post_type'                 => $post_type,
        'numberposts'               => $request['limit'] ?? get_option('posts_per_page'),
        'paged'                     => $request['paged'] ?? 1,
        'suppress_filters'          => false,
        'cache_results'             => false,
        'update_post_term_cache'    => false,
        'update_post_meta_cache'    => false
    ];

    // 查询方法设定
    switch ($order_key) {

            // 推荐
        case 'recommend':
            $post_query['orderby'] = 'menu_order date';
            break;

            // 最新
        case 'latest':
            $post_query['orderby'] = 'date';
            break;

            // 按年
        case 'year':
            $post_query['date_query'][]['before'] = '1 year ago';
            break;

            // 按月
        case 'month':
            $post_query['date_query'][]['before'] = '1 month ago';
            break;

        default:
            break;
    }

    // 判断搜索关键字
    if (isset($request['search'])) {
        $post_query['s'] = $request['search'];
    }

    // 判断标签
    if (isset($request['tag'])) {
        $post_query['tax_query'][] = [
            'taxonomy'  => $post_type . '_tags',
            'terms'     => $request['tag']
        ];
    }

    // 判断分类目录
    if (isset($request['category'])) {
        $post_query['tax_query'][] = [
            'taxonomy'  => $post_type . '_category',
            'terms'     => $request['category']
        ];
    }

    // 获取内容列表
    $posts = get_posts($post_query);

    /** 判断内容列表为空 */
    if (empty($posts)) {
        return;
    }

    /**
     * 遍历产品内容数组
     * 每个内容都按标准格式化显示
     */
    foreach ($posts as $post) {
        $data[] = call_user_func(sprintf('wwpo_%s_format_posts', $post_type), $post, $size);
    }

    // 返回列表内容
    return $data;
}
