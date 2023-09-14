<?php

/**
 * 微信小程序设置表单
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */

if (!current_user_can('edit_posts')) {
    wp_die(__('您没有权限访问此页面。', 'wwpo'));
}

$option_data  = get_option(WWPO_Wxapps::KEY_OPTION);

echo WWPO_Form::table([
    'submit'    => 'updatewechat',
    'hidden'    => ['post_id' => $_GET['tab']],
    'formdata'  => [
        'updated[appid]'  => [
            'title' => '开发者ID（AppID）',
            'field' =>  ['type' => 'text', 'value' => $option_data['appid'] ?? '']
        ],
        'updated[appsecret]'  => [
            'title' => '开发者密码（AppSecret）',
            'field' => ['type' => 'text', 'value' => $option_data['appsecret'] ?? '']
        ],
        'accesstoken'    => [
            'title' => '接口调用凭据（Access Token）',
            'desc'  => '过期时间：' . (isset($option_data['tokenexpires']) ? date('Y-m-d H:i:s', $option_data['tokenexpires'] + 7200) : '没有记录'),
            'field' => ['type' => 'textarea', 'value' => ($option_data['accesstoken'] ?? ''), 'disabled' =>  true]
        ]
    ]
]);
