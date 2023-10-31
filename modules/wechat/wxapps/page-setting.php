<?php

/**
 * 微信小程序设置表单页面函数
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat/wxapps
 */
function wwpo_wxapps_display_setting($option_data)
{
    if (!current_user_can('edit_posts')) {
        wp_die(__('您没有权限访问此页面。', 'wwpo'));
    }

    $wp_roles       = new WP_Roles();
    $role_data      = [];
    $option_data    = $option_data['wxapps'] ?? [];
    $invite_roles   = $option_data['inviteroles'] ?? [];

    foreach ($wp_roles->roles as $role_slug => $role_val) {
        $role_data[$role_slug] = translate_user_role($role_val['name']);
    }

    $wxapps_setting_form = [
        'submit'    => 'updatewechat',
        'hidden'    => ['post_key' => 'setting'],
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
            ],
            'inviteroles'  => [
                'title' => '应用邀请用户角色'
            ],
            'updated[updatedrole]'  => [
                'title' => '注册邀请用户角色',
                'field' => [
                    'type'      => 'select',
                    'option'    => $role_data,
                    'selected'  => $option_data['updatedrole'] ?? ''
                ]
            ]
        ]
    ];

    foreach ($role_data as $role_key => $role_name) {
        $wxapps_setting_form['formdata']['inviteroles']['fields'][$role_key] = [
            'title' => $role_name,
            'field' => ['type' => 'checkbox', 'name' => 'updated[inviteroles][]', 'value' => $role_key]
        ];

        if ($invite_roles) {
            if (in_array($role_key, $invite_roles)) {
                $wxapps_setting_form['formdata']['inviteroles']['fields'][$role_key]['field']['checked'] = 1;
            }
        }
    }

    echo WWPO_Form::table($wxapps_setting_form);
}
