<?php

/**
 * 产品展示模块应用函数
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/product
 */

/**
 * 生成产品小程序码函数
 *
 * @since 1.0.0
 * @param integer $post_id
 */
function wwpo_wpmall_product_create_qrcode($post_id)
{
    // 判断小程序类，是否已经加载小程序模块
    if (!class_exists('wwpo_wxapps')) {
        return;
    }

    /**
     * 声明产小程序类
     *
     * @var object
     */
    $wxapps = new WWPO_Wxapps();

    // 获取小程序码内容
    $create_limit_qrcode     = $wxapps->rest_create_limit_qrcode([
        'scene'         => $post_id,
        'page'          => 'pages/product/item/item',
        'stream'        => true,
        'guid'          => sprintf('wxapps/qrcode/%s.png', $post_id),
        'check_path'    => false
    ]);

    if (empty($create_limit_qrcode)) {
        return;
    }
}

/**
 * 产品排序等级
 *
 * @since 1.0.0
 * @param integer $order
 */
function wwpo_wpmall_product_menu_order($order = null)
{
    $menu_order = [
        0   => '选择推荐度',
        50  => '一星推荐',
        75  => '二星推荐',
        100  => '三星推荐',
        150  => '四星推荐',
        200  => '五星推荐',
        300  => '六星推荐',
        350  => '七星推荐',
        400  => '八星推荐',
        450  => '九星推荐',
        500  => '十星推荐',
    ];

    if (isset($order)) {
        return $menu_order[$order] ?? '';
    }

    return $menu_order;
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_wpmall_product_edit_form_button($post)
{
    if ('prdouct' != $post->post_type) {
        return;
    }

    printf('<button type="button" class="button button-small" data-action="wpajax" data-post="%d" value="copyproduct">复制产品</button>', $post->ID);
}
add_action('post_submitbox_minor_actions', 'wwpo_wpmall_product_edit_form_button');

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_ajax_wpmall_copy_product()
{
    global $wpdb;

    $post_id = $_POST['post'] ?? 0;

    if (empty($post_id)) {
        echo new WWPO_Error('not_null_content', '产品编号');
        exit;
    }

    $post = get_post($post_id);
    $product = wwpo_get_row(WWPO_SQL_PRODUCT, 'post_id', $post_id);

    $post_category      = wp_get_post_terms($post_id, 'product_category');
    $post_tags          = wp_get_post_terms($post_id, 'product_tags');
    $post_brand         = wp_get_post_terms($post_id, 'product_brand');

    $insert_id = wwpo_insert_post($wpdb->posts, [
        'post_author'       => $post->post_author,
        'post_title'        => $post->post_title,
        'post_excerpt'      => $post->post_excerpt,
        'post_content'      => $post->post_content,
        'post_type'         => 'product',
        'comment_status'    => 'closed',
        'ping_status'       => 'closed'
    ]);

    if (empty($insert_id)) {
        echo new WWPO_Error('invalid_added');
        exit;
    }

    wwpo_insert_post(WWPO_SQL_PRODUCT, [
        'post_id'       => $insert_id,
        'barcode'       => $product->barcode,
        'promo'         => $product->promo,
        'price_sale'    => $product->price_sale,
        'price_vip'     => $product->price_vip,
        'price_buy'     => $product->price_buy,
        'product_modal' => $product->product_modal
    ]);

    if ($post_category) {
        wp_set_post_terms($insert_id, array_column($post_category, 'term_id'), 'product_category');
    }

    if ($post_tags) {
        wp_set_post_terms($insert_id, array_column($post_tags, 'slug'), 'product_tags');
    }

    if ($post_brand) {
        wp_set_post_terms($insert_id, array_column($post_brand, 'slug'), 'product_brand');
    }

    $page_url = admin_url('post.php');
    $page_url = add_query_arg(['post' => $insert_id, 'action' => 'edit'], $page_url);

    // 设定日志
    wwpo_logs('admin:ajax:copyproduct:' . $post_id . 'to' . $insert_id);

    echo WWPO_Error::toast('success', '复制成功', ['url' => $page_url]);
}
add_action('wwpo_ajax_admin_copyproduct', 'wwpo_ajax_wpmall_copy_product');

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_wpmall_product_get_sync($data)
{
    global $wpdb;

    $data['product'] = wwpo_wpmall_get_terms('product');
    $data['product']['recommend'] = wwpo_wpmall_get_posts([
        'post_type' => 'product',
        'slug'      => 'recommend'
    ]);

    $product_content = $wpdb->get_results(sprintf("SELECT * FROM %s", WWPO_SQL_PR_CONTENT));
    if ($product_content) {
        foreach ($product_content as $item) {
            $data['product']['content'][$item->post_name] = WWPO_Wxapps::nodes($item->post_content);
        }
    }

    return $data;
}
add_filter('wwpo_wxapps_sync', 'wwpo_wpmall_product_get_sync');
