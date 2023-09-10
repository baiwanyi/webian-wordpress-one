<?php

/**
 * 设置页面消息函数
 *
 * @since 1.0.0
 * @param array $message
 */
function wwpo_admin_message_wxapps($message)
{
    $message['wxapps'] = [
        'not_found_key'             => ['error'     => __('没要找到保存的字段。', 'wwpo')],
        'not_found_data'            => ['error'     => __('没要找到保存的数据内容。', 'wwpo')],
        'not_found_id'              => ['error'     => __('没要找到保存的编号。', 'wwpo')],
        'settting_success_updated'  => ['updated'   => __('小程序设置内容已更新', 'wwpo')],
        'banner_success_updated'    => ['updated'   => __('通栏广告内容更新成功', 'wwpo')],
    ];

    return $message;
}
add_filter('wwpo_admin_message', 'wwpo_admin_message_wxapps');
