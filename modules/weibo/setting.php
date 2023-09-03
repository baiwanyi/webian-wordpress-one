<?php

/**
 * Undocumented function
 *
 * @param [type] $settings
 * @return void
 */
function wwpo_admin_settings_weibo($settings)
{
    // 获取设置保存值
    $option_data = wwpo_get_option('wwpo-settings-weibo');

    // 设置表单内容数组
    $settings['weibo'] = [
        'title'     => __('微博开放平台', 'wwpo'),
        'formdata'  => [
            'option_data[appid]'  => [
                'title' => '开发者ID（AppID）',
                'field' =>  ['type' => 'text', 'value' => $option_data['appid'] ?? '']
            ],
            'option_data[appsecret]'  => [
                'title' => '开发者密码（AppSecret）',
                'field' => ['type' => 'text', 'value' => $option_data['appsecret'] ?? '']
            ]
        ]
    ];

    // 返回设置内容
    return $settings;
}
add_filter('wwpo_admin_page_settings', 'wwpo_admin_settings_weibo');
