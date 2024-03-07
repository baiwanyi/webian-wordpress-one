<?php

/**
 * Undocumented function
 *
 * @param [type] $request
 * @return void
 */
function wwpo_ajax_order_search_item($request)
{
    global $wpdb;

    $results = $wpdb->get_results(sprintf("SELECT * FROM %s WHERE barcode LIKE '%%{$request['search']}%%' LIMIT 10", WWPO_SQL_PRODUCT), ARRAY_A);

    if (empty($results)) {
        echo wwpo_json_send(['status' => 'error', 'message' => sprintf('没有找到「%s」相关产品', $request['search'])]);
        return;
    }

    $data = [];

    foreach ($results as $item) {

        $price_sale = $item['price_sale'] ?? '';
        if ($price_sale) {
            $price_sale = $price_sale / 100;
        }

        $price_buy = $item['price_buy'] ?? '';
        if ($price_buy) {
            $price_buy = $price_buy / 100;
        }

        $data[] = [
            'product_id'    => $item['product_id'],
            'barcode'       => $item['barcode'],
            'title'         => get_the_title($item['post_id']),
            'price_sale'    => $price_sale,
            'price_buy'     => $price_buy,
            'thumb'         => wp_get_attachment_image_url($item['thumb_id'])
        ];
    }

    echo wwpo_json_send(['status' => 'success', 'data' => $data]);
}
add_action('wwpo_ajax_admin_wwpoordersearchitem', 'wwpo_ajax_order_search_item');

/**
 * Undocumented function
 *
 * @param [type] $request
 * @return void
 */
function wwpo_ajax_order_selected_item($request)
{
    if (empty($request['product']) || empty($request['order'])) {
        echo wwpo_json_send(['status' => 'error', 'message' => '找不到相关产品']);
        return;
    }

    // 获取产品库信息
    $product = wwpo_get_row(WWPO_SQL_PRODUCT, 'product_id', $request['product']);

    if (empty($product)) {
        echo wwpo_json_send(['status' => 'error', 'message' => '找不到相关产品']);
        return;
    }

    // 设定订单产品更新内容数组
    $item_updated = [
        'order_id'      => $request['order'],
        'product_id'    => $request['product'],
        'barcode'       => $product->barcode,
        'item_title'    => get_the_title($product->post_id),
        'thumb_url'     => get_post_meta($product->thumb_id, '_wp_attached_file', true)
    ];

    // 写入订单产品数据库
    $item_id = wwpo_insert_post(WWPO_SQL_ORDER_ITEM, $item_updated);

    if (empty($item_id)) {
        echo wwpo_json_send(['status' => 'error', 'message' => '写入产品失败']);
        return;
    }

    // 设定带域名的缩略图地址和写入的订单 ID
    // 用于列表展示
    $item_updated['thumb_url']  = wwpo_oss_cdnurl($item_updated['thumb_url'], 'thumbnail');
    $item_updated['item_id']    = $item_id;

    /**
     * 判断产品规格未设定，则建立默认规格列表
     */
    if (empty($product->product_modal)) {

        // 获取产品分类目录
        $post_category = wp_get_post_terms($product->post_id, 'product_category');

        // 未设定分类目录，规格类型设定为：other
        if (empty($post_category)) {
            $item_modal_type = 'other';
        }
        // 获取分类目录的规格类型
        // 只获取父级分类的规格类型，父级目录为空则使用分类目录 ID
        else {

            $current_id = $post_category[0]->parent ?? 0;

            if (empty($current_id)) {
                $current_id = $post_category[0]->term_id ?? 0;
            }

            $item_modal_type = get_term_meta($current_id, 'modal_type', true);
        }

        // other 规格类型设定一条规格记录
        if ('other' == $item_modal_type) {
            $item_updated['modal'][] = ['item_id' => $item_id];
        }
        // 设定规格内容数组为衣服尺码
        else {
            $item_updated['modal'] = [
                ['item_id' => $item_id, 'item_modal' => '6#'],
                ['item_id' => $item_id, 'item_modal' => '8#'],
                ['item_id' => $item_id, 'item_modal' => '10#'],
                ['item_id' => $item_id, 'item_modal' => '12#'],
                ['item_id' => $item_id, 'item_modal' => '14#'],
                ['item_id' => $item_id, 'item_modal' => '16#'],
                ['item_id' => $item_id, 'item_modal' => '18#'],
                ['item_id' => $item_id, 'item_modal' => '20#'],
                ['item_id' => $item_id, 'item_modal' => '22#']
            ];
        }
    }
    // 使用产品规格进行订单数量和列表设定
    else {

        // 对产品规格进行 JSON 数组化
        $product_modal = wwpo_json_decode($product->product_modal);

        // 获取产品所有 SKU 数据
        $product_skus = wwpo_get_post(WWPO_SQL_PR_SKU, 'product_id', $request['product']);

        // 遍历 SKU 数据内容数组，设定 SKU 数组
        foreach ($product_skus as $sku) {
            $sku_data[$sku->sku_name] = [
                'price_buy'     => $sku->price_buy * 100,
                'price_sale'    => $sku->price_sale * 100
            ];
        }

        // 单一规格类型设定
        if (1 == count($product_modal)) {

            // 对规格项目列表进行排序
            $current_product_modal = current($product_modal)['list'];
            $current_product_modal = wp_list_sort($current_product_modal, 'order');
            $current_modal_id = current($product_modal)['id'];

            $i = 0;

            // 遍历规格项目列表内容数组
            foreach ($current_product_modal as $modal) {

                // 设定当前项目 SKU
                $sku_name = $current_modal_id . ':' . $modal['id'];

                // 设定订单产品的规格
                $item_updated['modal'][$i] = $sku_data[$sku_name];
                $item_updated['modal'][$i]['item_id'] = $item_id;
                $item_updated['modal'][$i]['item_modal'] = $modal['name'];

                $i++;
            }
        }
        // 多个规格类型进行循环读取
        else {

            // 对规格进行排序
            $product_modal = wp_list_sort($product_modal, 'order');

            // 主规格和次要规格项目进行排序
            $product_modal_primary      = wp_list_sort($product_modal[0]['list'], 'order');
            $product_modal_secondary    = wp_list_sort($product_modal[1]['list'], 'order');

            $i = 0;
            foreach ($product_modal_primary as $primary_list) {
                foreach ($product_modal_secondary as $secondary_list) {

                    // 设定当前项目 SKU
                    $sku_name = $product_modal[0]['id'] . ':' . $primary_list['id'] . ':' . $product_modal[1]['id'] . ':' . $secondary_list['id'];

                    // 设定订单产品的规格
                    $item_updated['modal'][$i] = $sku_data[$sku_name];
                    $item_updated['modal'][$i]['item_id'] = $item_id;
                    $item_updated['modal'][$i]['item_modal'] = $primary_list['name'] . '-' . $secondary_list['name'];

                    $i++;
                }
            }
        }
    }

    foreach ($item_updated['modal'] as $i => $itemmeta_updated) {
        $oimeta_id = wwpo_insert_post(WWPO_SQL_ORDER_ITEMMETA, $itemmeta_updated);

        if ($oimeta_id) {
            $item_updated['modal'][$i]['oimeta_id'] = $oimeta_id;
        }
    }

    echo wwpo_json_send(['status' => 'success', 'data' => [$item_updated]]);
}
add_action('wwpo_ajax_admin_wwpoorderselecteditem', 'wwpo_ajax_order_selected_item');

/**
 * Undocumented function
 *
 * @param [type] $request
 * @return void
 */
function wwpo_ajax_order_delete_item($request)
{
    if (empty($request['item'])) {
        echo wwpo_json_send(['status' => 'error']);
        return;
    }

    wwpo_delete_post(WWPO_SQL_ORDER_ITEM, ['item_id' => $request['item']]);
    wwpo_delete_post(WWPO_SQL_ORDER_ITEMMETA, ['item_id' => $request['item']]);

    echo wwpo_json_send(['status' => 'success']);
}
add_action('wwpo_ajax_admin_wwpoorderdeleteitem', 'wwpo_ajax_order_delete_item');

/**
 * Undocumented function
 *
 * @param [type] $request
 * @return void
 */
function wwpo_ajax_order_delete_modal($request)
{
    if (empty($request['modal'])) {
        return;
    }

    $current_modal = wwpo_get_row(WWPO_SQL_ORDER_ITEMMETA, 'oimeta_id', $request['modal']);
    $current_item = wwpo_get_row(WWPO_SQL_ORDER_ITEM, 'item_id', $current_modal->item_id);

    $current_modal_total = $current_modal->price_sale * $current_modal->amount;
    $current_item_total = $current_item->price_total - $current_modal_total;
    $current_item_amount = $current_item->amount - $current_modal->amount;

    $item_updated = ['price_total' => $current_item_total, 'amount' => $current_item_amount];

    wwpo_update_post(WWPO_SQL_ORDER_ITEM, $item_updated, ['item_id' => $current_modal->item_id]);

    wwpo_delete_post(WWPO_SQL_ORDER_ITEMMETA, ['oimeta_id' => $request['modal']]);

    $item_updated['price_total'] = number_format($item_updated['price_total'] / 100, 2);

    echo wwpo_json_send(['status' => 'success', 'data' => $item_updated]);
}
add_action('wwpo_ajax_admin_wwpoorderdeletemodal', 'wwpo_ajax_order_delete_modal');

/**
 * Undocumented function
 *
 * @param [type] $request
 * @return void
 */
function wwpo_ajax_order_create_modal($request)
{
    if (empty($request['modal']) || empty($request['item'])) {
        return;
    }

    $updated = [
        'item_id'       => $request['item'],
        'item_modal'    => $request['modal']
    ];

    $oimeta_id = wwpo_insert_post(WWPO_SQL_ORDER_ITEMMETA, $updated);

    if (empty($oimeta_id)) {
        return;
    }

    $updated['oimeta_id'] = $oimeta_id;

    echo wwpo_json_send(['status' => 'success', 'data' => $updated]);
}
add_action('wwpo_ajax_admin_wwpoordercreatemodal', 'wwpo_ajax_order_create_modal');

/**
 * Undocumented function
 *
 * @param [type] $request
 * @return void
 */
function wwpo_ajax_order_updated_modal($request)
{
    if (empty($request['modal']) || empty($request['item'])) {
        return;
    }

    switch ($request['type']) {
        case 'name':
            $updated['item_modal'] = $request['value'];
            break;
        case 'amount':
            $updated['amount'] = $request['value'];
            break;
        case 'buy':
            $updated['price_buy'] = $request['value'] * 100;
            break;
        case 'sale':
            $updated['price_sale'] = $request['value'] * 100;
            break;
        default:
            break;
    }

    if (empty($updated)) {
        return;
    }

    wwpo_update_post(WWPO_SQL_ORDER_ITEMMETA, $updated, ['oimeta_id' => $request['modal']]);

    $data = [
        'amount'    => 0,
        'total'     => 0,
        'modal'     => 0
    ];

    $item_modals = wwpo_get_post(WWPO_SQL_ORDER_ITEMMETA, 'item_id', $request['item']);

    if ($item_modals) {

        $current_item_modal = wp_list_filter($item_modals, ['oimeta_id' => $request['modal']]);
        $data['modal'] = (current($current_item_modal)->price_sale / 100) * current($current_item_modal)->amount;

        foreach ($item_modals as $modal) {
            $amount = (int) $modal->amount;
            $data['amount'] += $amount;
            $data['total'] += ($modal->price_sale / 100) * $amount;
        }

        wwpo_update_post(WWPO_SQL_ORDER_ITEM, [
            'amount'        => $data['amount'],
            'price_total'   => $data['total'] * 100
        ], ['item_id' => $request['item']]);
    }

    $data['modal'] = number_format($data['modal'], 2);
    $data['total'] = number_format($data['total'], 2);

    echo wwpo_json_send(['status' => 'success', 'data' => $data]);
}
add_action('wwpo_ajax_admin_wwpoordermodalupdate', 'wwpo_ajax_order_updated_modal');

/**
 * Undocumented function
 *
 * @param [type] $request
 * @return void
 */
function wwpo_ajax_order_get_items($request)
{
    if (empty($request['order'])) {
        echo wwpo_json_send(['status' => 'error', 'message' => '找不到相关内容。']);
        return;
    }

    $items = wwpo_get_post(WWPO_SQL_ORDER_ITEM, 'order_id', $request['order']);

    if (empty($items)) {
        echo wwpo_json_send(['status' => 'error', 'message' => '找不到相关内容。']);
        return;
    }

    foreach ($items as $i => $item) {

        $price_total = $item->price_total;
        if ($price_total) {
            $price_total = $price_total / 100;
        }

        $items[$i]->thumb_url = wwpo_oss_cdnurl($item->thumb_url, 'thumbnail');
        $items[$i]->price_total = number_format($price_total, 2);

        $current_modal = wwpo_get_post(WWPO_SQL_ORDER_ITEMMETA, 'item_id', $item->item_id);

        if (empty($current_modal)) {
            continue;
        }

        $items[$i]->modal = $current_modal;

        foreach ($current_modal as $modal_index => $modal_val) {
            $modal_total = 0;
            $amount = $modal_val->amount ?? 0;
            $amount = (int) $amount;

            $price_buy = (int) $modal_val->price_buy ?? 0;
            if ($price_buy) {
                $price_buy = $price_buy / 100;
            }

            $price_sale = (int) $modal_val->price_sale ?? 0;
            if ($price_sale) {
                $price_sale = $price_sale / 100;
            }

            if ($amount) {
                $modal_total = $price_sale * $amount;
            }

            $items[$i]->modal[$modal_index]->price_buy = $price_buy;
            $items[$i]->modal[$modal_index]->price_sale = $price_sale;

            $items[$i]->modal[$modal_index]->total = number_format($modal_total, 2);
        }
    }

    echo wwpo_json_send(['status' => 'success', 'data' => $items]);
}
add_action('wwpo_ajax_admin_wwpoordergetitems', 'wwpo_ajax_order_get_items');

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_ajax_wpmall_check_customer_phone()
{
    $customer_phone = $_POST['user_customer']['phone'];

    if (empty($customer_phone)) {
        echo WWPO_Error::toast('error', '查询号码为空');
        return;
    }

    $customer_data = wwpo_get_row(WWPO_SQL_CUSTOMER, 'user_phone', $customer_phone);

    if (empty($customer_data)) {
        echo WWPO_Error::toast('error', '未找到内容');
        return;
    }

    $user_location = maybe_unserialize($customer_data->user_location);

    echo WWPO_Error::value([
        'user_customer[display]'    => $customer_data->user_display,
        'user_customer[contact]'    => $customer_data->user_contact,
        'user_customer[address]'    => $user_location['address'],
    ]);
}
add_action('wwpo_ajax_admin_checkcustomerphone', 'wwpo_ajax_wpmall_check_customer_phone');

function wwpo_ajax_wpmall_get_user_agent_rate()
{
    $user_commission_rate = get_user_meta($_POST['user_agent'], '_wwpo_wpmall_user_rate', true);
    if (empty($user_commission_rate)) {
        $user_commission_rate = 10;
    }

    echo WWPO_Error::value([
        'user_commission_rate' => $user_commission_rate,
    ]);
}
add_action('wwpo_ajax_admin_getuseragentrate', 'wwpo_ajax_wpmall_get_user_agent_rate');
