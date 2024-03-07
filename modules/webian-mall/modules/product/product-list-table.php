<?php

/**
 * 产品列表页面自定义
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
function wwpo_wpmall_product_columns($column)
{
    $column = [
        'cb'            => __('选择', 'wpmall'),
        'thumb'         => __('封面', 'wpmall'),
        'title'         => __('Title'),
        'medium-barcode'    => __('货号', 'wpmall'),
        'taxonomy-product_category' => __('Categories'),
        'taxonomy-product_brand'    => __('品牌', 'wpmall'),
        // 'small-sort'    => __('排序', 'wpmall'),
        'small-buy'     => __('进货价', 'wpmall'),
        'small-price'   => __('售价', 'wpmall'),
        // 'small-sales'   => __('销量', 'wpmall'),
        // 'small-order'   => __('订单', 'wpmall'),
        'date'          => __('Date')
    ];

    return $column;
}
add_filter('manage_edit-product_columns', 'wwpo_wpmall_product_columns');

/**
 * 自定义表格列输出函数
 *
 * @since 1.0.0
 * @param string    $column     表格列名称
 * @param integer   $post_id    作品编号
 */
function wwpo_wpmall_product_custom_column($column)
{
    global $post, $wpdb;

    $post_meta = get_post_meta($post->ID);

    switch ($column) {
            // 产品封面
        case 'thumb':

            $post_thumbnail_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_parent = $post->ID AND post_type = 'attachment' ORDER BY menu_order DESC");

            if (empty($post_thumbnail_id)) {
                return;
            }

            printf(
                '<div class="ratio ratio-1x1"><img src="%s" class="thumb rounded"></div>',
                wp_get_attachment_image_url($post_thumbnail_id)
            );

            break;

            // 推荐排序
        case 'small-sort':

            if (empty($post->menu_order)) {
                echo '—';
                return;
            }

            echo wwpo_wpmall_product_menu_order($post->menu_order);
            break;

            // 进货价
        case 'small-buy':
            $price_buy = $post->price_buy ?? 0.00;
            if ($price_buy) {
                $price_buy = $price_buy / 100;
            }
            echo $price_buy;
            break;

            // 销售价
        case 'small-price':
            $price_market = $post->price_market ?? 0.00;
            if ($price_market) {
                $price_market = $price_market / 100;
            }
            echo $price_market;
            break;

            // 销售数量
        case 'small-sales':
            echo $post_meta[WWPO_PR_META_SALES][0] ?? 0.00;
            break;

            // 订单数量
        case 'small-order':
            echo $post_meta[WWPO_PR_META_ORDER][0] ?? 0;
            break;

            // 货号
        case 'medium-barcode':
            echo $post->barcode;
            break;

        default:
            break;
    }
}
add_action('manage_product_posts_custom_column', 'wwpo_wpmall_product_custom_column');

/**
 * 自定义表格排序函数
 *
 * @since 1.0.0
 * @param array $columns 排序列表别名数组，索引为列表别名，值为排序数据库字段
 */
function wwpo_wpmall_product_sortable_columns($columns)
{
    $columns['small-sort']  = 'menu_order';
    $columns['small-buy']   = 'buy';
    $columns['small-price'] = 'price';
    $columns['small-sales'] = 'sales';
    $columns['small-order'] = 'order';
    return $columns;
}
add_filter('manage_edit-product_sortable_columns', 'wwpo_wpmall_product_sortable_columns');

/**
 * 自定义列表项目标题行动作函数
 *
 * @since 1.0.0
 * @param array     $actions
 * @param WP_Post   $post
 */
function wwpo_wpmall_product_row_actions($actions, $post)
{
    // 设定操作动作链接
    $page_url = admin_url('post.php?post=' . $post->ID . '&action=qrcode');

    // 设置操作内容数组
    $actions['qrcode']      = sprintf('<a href="%s">%s</a>', $page_url, __('生成小程序码', 'wpmall'));
    $actions['weappview']   = sprintf('<a href="#weapp" data-action="weappview" data-post="%d">%s</a>', $post->ID, __('小程序查看', 'wpmall'));

    // 返回内容数组
    return $actions;
}
add_filter('post_row_actions', 'wwpo_wpmall_product_row_actions', 10, 2);

/**
 * 生成小程序二维码操作函数
 *
 * @since 1.0.0
 * @param integer $post_id
 */
function wwpo_wpmall_product_action_create_qrcode($post_id)
{
    // 生成小程序码
    $qrcode = wwpo_wpmall_product_create_qrcode($post_id);

    /** 判断小程序码生成失败，返回消息 */
    if (empty($qrcode)) {
        wp_redirect(admin_url('edit.php?post_type=product&updated=4'));
        exit;
    }

    // 小程序码图片地址写入产品缓存
    $product = WWPO_Redis::get(WPMALL_RD_PRODUCT_KEY . $post_id, WP_REDIS_DATABASE);
    $product['qrcode'] = wwpo_oss_cdnurl($qrcode, 'qrcode');

    WWPO_Redis::set(WPMALL_RD_PRODUCT_KEY . $post_id, $product, WP_REDIS_DATABASE);

    // 返回成功消息
    wp_redirect(admin_url('edit.php?post_type=product&updated=3&paged=' . $_GET['paged'] ?? 1));
    exit;
}
add_action('post_action_qrcode', 'wwpo_wpmall_product_action_create_qrcode');

/**
 * 自定义筛选操作函数
 *
 * @since 1.0.0
 * @param string $post_type
 * @param string $which
 */
function wwpo_wpmall_product_restrict_manage($post_type, $which)
{
    if ('product' != $post_type) {
        return;
    }

    if ('top' != $which) {
        return;
    }

    // 获取产品父级分类目录
    $parent_data = get_terms([
        'taxonomy'      => 'product_category',
        'hide_empty'    => false,
        'parent'        => 0,
        'fields'        => 'id=>name',
        'update_term_meta_cache' => false
    ]);

    // 设定下拉菜单显示内容数组
    $dropdown_options['field'] = [
        'type'              => 'select',
        'show_option_all'   => get_taxonomy('product_brand')->labels->all_items,
        'selected'          => $_GET['brand'] ?? 0,
    ];

    // 遍历父级分类目录内容数组，获取所属的目录品牌列表
    foreach ($parent_data as $parent_id => $parent_name) {

        // 目录标题
        $dropdown_options['field']['group'][$parent_id]['title'] = $parent_name;

        // 获取品牌内容列表
        $brand_data = get_terms([
            'taxonomy'      => 'product_brand',
            'meta_key'      => 'parent_id',
            'meta_value'    => $parent_id,
            'fields'        => 'id=>name',
            'update_term_meta_cache' => false
        ]);

        // 设定品牌显示内容数组
        foreach ($brand_data as $brand_id => $brand_name) {
            $dropdown_options['field']['group'][$parent_id]['option'][$brand_id] = $brand_name;
        }
    }

    // 显示下拉菜单内容
    printf('<label class="screen-reader-text" for="brand">%s</label>', get_taxonomy('product_brand')->labels->filter_by_item);
    echo WWPO_Form::field('brand', $dropdown_options);
    echo WWPO_Form::field('menu_order', [
        'field' => [
            'type'      => 'select',
            'option'    => wwpo_wpmall_product_menu_order(),
            'selected'  => $_GET['menu_order'] ?? 0
        ]
    ]);
}
add_action('restrict_manage_posts', 'wwpo_wpmall_product_restrict_manage', 10, 2);

/**
 * 禁止按月份筛选函数
 *
 * @since 1.0.0
 * @param boolean   $disabled   是否禁止，默认：false
 * @param string    $post_type
 */
function wwpo_wpmall_product_disable_months_dropdown($disabled, $post_type)
{
    if ('product' == $post_type) {
        return true;
    }

    return $disabled;
}
add_filter('disable_months_dropdown', 'wwpo_wpmall_product_disable_months_dropdown', 10, 2);
