<?php

/**
 * 微信支付设置表单页面函数
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat/wxpay
 */
function wwpo_wxpay_display_setting($option_data)
{
    if (!current_user_can('edit_posts')) {
        wp_die(__('您没有权限访问此页面。', 'wwpo'));
    }

    $option_data = $option_data['wxpay'] ?? [];

    $wechat_form  = [
        'submit'    => 'updatewechat',
        'hidden'    => ['post_key' => 'setting'],
        'formdata'  => [
            'updated[mchid]'  => [
                'title' => '直连商户号',
                'field' =>  ['type' => 'text', 'value' => $option_data['mchid'] ?? '']
            ],
            'updated[apikeyid]'  => [
                'title' => 'API 证书序列号',
                'field' => ['type' => 'text', 'value' => $option_data['apikeyid'] ?? ''],
                'desc'  => '微信支付后台「账户设置 - API安全」处申请'
            ],
            'updated[apikeysecret]'  => [
                'title' => 'API 证书序内容',
                'field' => ['type' => 'textarea', 'value' => $option_data['apikeysecret'] ?? '']
            ],
            'updated[apiv2secret]'   => [
                'title' => 'APIv2密钥',
                'field' => ['type' => 'text', 'value' => $option_data['apiv2secret'] ?? '']
            ],
            'updated[apiv3secret]' => [
                'title' => 'APIv3密钥',
                'field' => ['type' => 'text', 'value' => $option_data['apiv3secret'] ?? '']
            ]
        ]
    ];

    echo WWPO_Form::table($wechat_form);
}
