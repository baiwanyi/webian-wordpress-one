<?php

/**
 * 界面设置函数页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage pages
 */

/**
 * 主题设置页面
 *
 * @since 1.0.0
 */
// function wwpo_admin_display_settings()
// {

// }
// add_action('wwpo_admin_display_webian-wordpress-one', 'wwpo_admin_display_settings');


/**
 * 设置页面消息函数
 *
 * @since 1.0.0
 * @param array $message
 */
function wwpo_admin_message_settings($message)
{
    $message['webianwordpressone'] = [
        'no_option_key'     => ['error' => '没有找到相关保存参数'],
        'updated_success'   => ['updated' => '设置保存成功']
    ];

    return $message;
}
add_filter('wwpo_admin_message', 'wwpo_admin_message_settings');

/**
 * 设置保存操作函数
 *
 * @since 1.0.0
 */
function wwpo_admin_post_update_settings()
{
    /** 判断保存 KEY */
    if (empty($_POST['option_key'])) {
        new WWPO_Error('message', 'no_option_key');
        exit;
    }

    // 设置保存选项 KEY
    $option_key = sprintf('wwpo-settings-%s', $_POST['option_key']);

    // 更新到数据库
    update_option($option_key, $_POST['option_data']);

    // 设定日志
    wwpo_logs('admin:post:updatesettings:' . $_POST['option_key']);

    // 返回信息
    new WWPO_Error('message', 'updated_success', ['tab' => $_POST['option_key']]);
}
add_action('wwpo_post_admin_updatesettings', 'wwpo_admin_post_update_settings');
