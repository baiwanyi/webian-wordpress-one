<?php

/**
 * Undocumented function
 *
 * @param [type] $action
 * @return void
 */
function wwpo_wpmall_product_bulk_actions($action)
{
    $action['redis']    = 'Redis 缓存';
    $action['qrcode']   = '生成小程序码';
    return $action;
}
add_filter('bulk_actions-edit-product', 'wwpo_wpmall_product_bulk_actions');

/**
 * Undocumented function
 *
 * @param [type] $sendback
 * @param [type] $doaction
 * @param [type] $post_ids
 * @return void
 */
function wwpo_wpmall_product_handle_bulk_actions($sendback, $doaction, $post_ids)
{
    if ('redis' == $doaction) {
        foreach ($post_ids as $post_id) {
            wwpo_wpmall_product_update_redis($post_id);
            return add_query_arg('updated', 3, $sendback);
        }
    }

    if ('qrcode' == $doaction) {
        foreach ($post_ids as $post_id) {
            wwpo_wpmall_product_create_qrcode($post_id);
            return add_query_arg('updated', 4, $sendback);
        }
    }
}
add_filter('handle_bulk_actions-edit-product', 'wwpo_wpmall_product_handle_bulk_actions', 10, 3);

/**
 * 自定义后台批量操作返回消息函数
 *
 * @since 1.0.0
 * @param array $messages
 */
function wwpo_wpmall_product_updated_messages($bulk_messages)
{
    $updated = $_REQUEST['updated'] ?? 0;

    switch ($updated) {
        case 3:
            $bulk_messages['product']['updated'] = __('Redis 缓存生成完毕', 'wpmall');
            break;
        case 4:
            $bulk_messages['product']['updated'] = __('小程序码生成完毕', 'wpmall');
            break;

        default:
            break;
    }

    return $bulk_messages;
}
add_filter('bulk_post_updated_messages', 'wwpo_wpmall_product_updated_messages');
