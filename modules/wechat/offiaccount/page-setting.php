<?php

/**
 * 微信帐号设置表单
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
function wwpo_wechat_display_setting($option_data)
{
    if (!current_user_can('edit_posts')) {
        wp_die(__('您没有权限访问此页面。', 'wwpo'));
    }

    $option_data = $option_data['wechat'] ?? [];

    $wechat_form  = [
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
            'updated[token]'  => [
                'title' => '令牌（Token）',
                'field' => ['type' => 'text', 'value' => $option_data['token'] ?? ''],
                'desc'  => sprintf('微信服务器连接地址：<code class="user-select-all">%s</code>', home_url('wp-json/wwpo/wechat/connect'))
            ],
            'updated[encodingaeskey]'  => [
                'title' => '消息加解密密钥（EncodingAESKey）',
                'field' => ['type' => 'text', 'value' => $option_data['encodingaeskey'] ?? '']
            ],
            'accesstoken'    => [
                'title' => '接口调用凭据（Access Token）',
                'desc'  => '过期时间：' . (isset($option_data['tokenexpires']) ? date('Y-m-d H:i:s', $option_data['tokenexpires'] + 7200) : '没有记录'),
                'field' => ['type' => 'textarea', 'value' => ($option_data['accesstoken'] ?? ''), 'disabled' => WP_DEBUG ? false : true]
            ],
            'updated[customer]'   => [
                'title' => '呼叫客服关键字',
                'desc'  => '设置呼叫在线客服的关键字',
                'field' => ['type' => 'text', 'value' => $option_data['customer'] ?? '']
            ],
            'updated[ads]' => [
                'title' => '广告头图',
                'desc'  => '被关注时消息封面显示广告内容。<a href="https://codex.wordpress.org/Keyboard_Shortcuts" target="_blank">广告内容设置',
                'field' => ['type' => 'select', 'selected' => $option_data['ads'] ?? 0, 'option' => ['关闭', '开启']]
            ],
            'updated[register]' => [
                'title' => '注册链接',
                'desc'  => sprintf('被关注时消息显示注册链接，请先在<a href="%s">[设置-常规]</a>里开启成员资格注册功能。', admin_url('options-general.php')),
                'field' => ['type' => 'select', 'selected' => $option_data['register'] ?? 0, 'option' => ['关闭', '开启']]
            ]
        ]
    ];

    if (WP_DEBUG) {
        $wechat_form['formdata']['accesstoken']['desc'] .= sprintf('（<a href="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%1$s&secret=%2$s" target="_blank">获取 Access Token</a>）', $option_data['appid'] ?? '', $option_data['appsecret'] ?? '');
    }

    echo WWPO_Form::table($wechat_form);
}
