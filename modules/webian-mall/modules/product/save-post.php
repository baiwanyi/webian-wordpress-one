<?php

/**
 * 产品数据保存操作
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/product
 */

/**
 * 产品提交数据库附加操作函数
 *
 * @since 1.0.0
 * @param integer $post_id
 * @param WP_Post $post
 * @param boolean $update
 */
function wwpo_wpmall_product_post_updated($post_id, $post, $update)
{
    /** 判断页面包含 action 参数，保证操作只在 edit 页面下执行，禁止在列表页「快速编辑」执行 */
    if (empty($_POST['action'])) {
        return $post_id;
    }

    /** 判断产品编辑权限 */
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    // 产品更新内容数组
    $updated = [
        'ID'            => $post_id,
        'post_excerpt'  => $_POST['excerpt'],
        'menu_order'    => $_POST['wpmall-order']
    ];

    // 设定参数
    $product_media = $_POST['wpmall-media'] ?? [];
    $product_modal = $_POST['wpmall-modal'] ?? [];
    $product_price = $_POST['wpmall-price'] ?? [];

    // 分类目录参数
    $product_terms['tags']      = $_POST['wpmall-tags'] ?? [];
    $product_terms['category']  = $_POST['wpmall-category'] ?? [];
    $product_terms['brand']     = $_POST['wpmall-brand'] ?? [];

    // 转换金额格式
    $price_sale = $_POST['wpmall-sale'] ?? 0;
    $price_sale = (int) $price_sale;

    $price_vip = $_POST['wpmall-vip'] ?? 0;
    $price_vip = (int) $price_vip;

    $price_buy = $_POST['wpmall-buy'] ?? 0;
    $price_buy = (int) $price_buy;

    // 查询产品数据表 ID，用于判断新建或更新
    $product_id         = $_POST['product_id'] ?? 0;
    $product_updated    = [
        'barcode'       => $_POST['wpmall-barcode'],
        'promo'         => $_POST['wpmall-promo'],
        'price_sale'    => $price_sale * 100,
        'price_vip'     => $price_vip * 100,
        'price_buy'     => $price_buy * 100,
        'product_modal' => stripslashes($product_modal)
    ];

    // 更新产品价格库存
    if ($product_media) {
        $product_updated['thumb_id'] = wwpo_wpmall_product_update_image($post_id, $product_media);
    }

    // 更新产品数据
    if ($product_id) {
        wwpo_update_post(WWPO_SQL_PRODUCT, $product_updated, ['product_id' => $product_id]);
    }

    // 更新产品价格库存
    if ($product_price) {
        $product_updated['product_sku'] = wwpo_wpmall_product_update_sku($product_id, $product_price);
    }

    // 更新产品分类目录
    wwpo_mall_product_update_terms($post_id, $product_terms);

    //
    $product_updated['product_id'] = $product_id;

    // 缓存产品数据
    wwpo_wpmall_product_update_redis($post_id, $product_updated, $product_media);

    //
    wwpo_wpmall_product_create_qrcode($post_id);

    /** 新产品修改别名 */
    if (!$update || empty($_POST['post_name'])) {
        $updated['post_name'] = wwpo_unique($post_id, 10);
    }

    /** 更新产品内容 */
    if (!wp_is_post_revision($post_id)) {
        remove_action('save_post_product', 'wwpo_wpmall_product_post_updated');
        wp_update_post($updated);
        add_action('save_post_product', 'wwpo_wpmall_product_post_updated');
    }

    wwpo_logs('product:updated:' . $post_id);
}
add_action('save_post_product', 'wwpo_wpmall_product_post_updated', 10, 3);

/**
 * 更新产品价格和库存
 *
 * @since 1.0.0
 * @param integer $product_id
 * @param string $updated
 */
function wwpo_wpmall_product_update_sku($product_id, $updated)
{
    global $wpdb;

    if (empty($product_id)) {
        return;
    }

    // 获取 SKU 数据内容
    $old_sku_data = wwpo_get_post(WWPO_SQL_PR_SKU, 'product_id', $product_id);

    if (empty($updated)) {

        if (empty($old_sku_data)) {
            return;
        }

        wwpo_delete_post(WWPO_SQL_PR_SKU, ['product_id' => $product_id]);
        return;
    }

    // 整理 JSON 数据，删除反斜杠并转换为数组
    $updated = stripslashes($updated);
    $updated = wwpo_json_decode($updated);

    // 获取需要更新的 SKU 名称列表
    $updated_sku_name = array_keys($updated);

    // 获取当前数据的 SKU 名称列表，用于删除不存在的 SKU 名称
    $old_sku_name = array_column($old_sku_data, 'sku_name');

    // 获取当前数据的 ID 和 SKU 名称列表，用于判断存在的 SKU 名称进行更新操作
    $old_sku_list = array_column($old_sku_data, 'sku_id', 'sku_name');

    // 获取需要删除的 SKU 名称列表
    $delete_sku_name = array_diff($old_sku_name, $updated_sku_name);

    // 删除不存在更新列表的 SKU 名称
    $wpdb->query(sprintf("DELETE FROM %s WHERE sku_name IN ('%s')", WWPO_SQL_PR_SKU, implode("','", $delete_sku_name)));

    $updated_sku_data = [];

    /**
     * 遍历需要更新的 SKU 列表
     *
     * @property string $sku_name
     * @property array $sku_data
     */
    foreach ($updated as $sku_name => $sku_data) {

        // 获取当前 SKU 名称对应的 ID，不存在设定为 0
        $sku_id = $old_sku_list[$sku_name] ?? 0;

        // 设定保存数据内容数组
        $updated_data = [
            'product_id'    => $product_id,
            'sku_name'      => $sku_name,
            'sku_code'      => $sku_data['code'],
            'price_buy'     => $sku_data['buy'] * 100,
            'price_vip'     => $sku_data['vip'] * 100,
            'price_sale'    => $sku_data['sale'] * 100,
            'num_stock'     => $sku_data['stock']
        ];

        // 判断 SKU ID，进行更新操作
        if ($sku_id) {
            $wpdb->update(WWPO_SQL_PR_SKU, $updated_data, ['sku_id' => $sku_id]);
        } else {
            $wpdb->insert(WWPO_SQL_PR_SKU, $updated_data);
        }

        $updated_sku_data[] = $updated_data;
    }

    return $updated_sku_data;
}

/**
 * 更新产品图片函数
 *
 * @since 1.0.0
 * @param integer $post_id
 * @param array $media_ids
 */
function wwpo_wpmall_product_update_image($post_id, $media_ids)
{
    global $wpdb;

    // 重置作品媒体关联编号
    $wpdb->update($wpdb->posts, ['post_parent' => 0], ['post_parent' => $post_id]);

    /** 判断媒体文件 ID 为空 */
    if (empty($media_ids)) {
        return;
    }

    // 获取媒体文件总数
    $total_media = count($media_ids);

    /** 遍历媒体文件 ID，使用 menu_order 进行媒体排序 */
    foreach ($media_ids as $index => $media_id) {
        $menu_order = $total_media - $index;
        $wpdb->update($wpdb->posts, ['post_parent' => $post_id, 'menu_order' => $menu_order], ['ID' => $media_id]);
    }

    return current($media_ids);
}

/**
 * 更新产品缓存函数
 *
 * @since 1.0.0
 * @param integer $post_id
 * @param integer $product_id
 * @param array $updated
 * @param array $media_ids
 */
function wwpo_wpmall_product_update_redis($post_id, $updated = [], $media_ids = [])
{
    if (empty($updated)) {
        $updated    = wwpo_get_row(WWPO_SQL_PRODUCT, 'post_id', $post_id, null, ARRAY_A);
        $product_id = $updated['product_id'];
    }

    if (empty($media_ids)) {
        $media_ids  = get_children([
            'post_parent'   => $post_id,
            'post_type'     => 'attachment',
            'orderby'       => 'menu_order',
            'fields'        => 'ids'
        ]);
    }

    /**
     *
     */
    $post_category      = wp_get_post_terms($post_id, 'product_category', ['fields' => 'ids']);
    $post_brand         = wp_get_post_terms($post_id, 'product_brand', ['fields' => 'ids']);

    global $wpdb;

    // 获取当前产品缓存
    $product = WWPO_Redis::get(WWPO_RD_PRODUCT_KEY . $post_id, WP_REDIS_DATABASE);

    // 设定 Redis 保存内容数组
    $data = [
        'thumb_id'      => $updated['thumb_id'],
        'product_id'    => $product_id,
        'post_id'       => $post_id,
        'modal'         => wwpo_json_decode($updated['product_modal']),
        'price'         => 0,
        'barcode'       => $updated['barcode'],
        'promo'         => $updated['promo'],
        'terms'         => $post_category[0] . $post_brand[0],
        'qrcode'        => wwpo_oss_cdnurl(sprintf('wxapps/qrcode/%s.png', $post_id), 'qrcode')
    ];

    // 设定销售价格
    $price_sale = $updated['price_sale'] ?? 0;
    if ($price_sale) {
        $price_sale = $price_sale / 100;
        $data['price'] = number_format($price_sale, 2);
    }

    // 获取封面地址列表数组
    if ($media_ids) {
        foreach ($media_ids as $media_id) {
            $data['thumb'][] = wp_get_attachment_image_url($media_id, 'display');
        }
    }

    if (empty($product['product_sku'])) {
        $product['product_sku'] = $wpdb->get_results(sprintf("SELECT * FROM %s WHERE product_id = $product_id", WWPO_SQL_PR_SKU));
    }

    if ($product['product_sku']) {
        foreach ($product['product_sku'] as $sku) {

            $price_sale = $sku->price_sale ?? 0;
            if ($price_sale) {
                $price_sale = $price_sale / 100;
            }

            $price_buy = $sku->price_buy ?? 0;
            if ($price_buy) {
                $price_buy = $price_buy / 100;
            }

            $data['sku'][$sku->sku_name] = [
                'code'  => $sku->sku_code,
                'sale'  => $price_sale,
                'buy'   => $price_buy
            ];
        }
    }

    // 缓存数据
    WWPO_Redis::set(WWPO_RD_PRODUCT_KEY . $post_id, $data, WP_REDIS_DATABASE);

    // 返回数据
    return $data;
}

/**
 * 更新产品分类目录函数
 *
 * @since 1.0.0
 * @param integer $post_id
 * @param string $updated
 */
function wwpo_mall_product_update_terms($post_id, $updated)
{
    wp_set_post_terms($post_id, $updated['category'], 'product_category');
    wp_set_post_terms($post_id, $updated['tags'], 'product_tags');
    wp_set_post_terms($post_id, $updated['brand'], 'product_brand');
}

/**
 * Undocumented function
 *
 * @param [type] $post_id
 * @param [type] $post
 * @return void
 */
function wwpo_wpmall_product_post_delete($post_id, $post)
{
    if ('product' != $post->post_type) {
        return;
    }

    $product_id = wwpo_get_post(WWPO_SQL_PRODUCT, 'post_id', $post_id, 'product_id');

    if ($product_id) {
        wwpo_delete_post(WWPO_SQL_PRODUCT, ['post_id' => $post_id]);
        wwpo_delete_post(WWPO_SQL_PR_SKU, ['product_id' => $product_id]);
    }

    wwpo_oss_delete_object('wxapps/qrcode/' . $post_id . '.png');
}
add_action('delete_post', 'wwpo_wpmall_product_post_delete', 10, 2);
