<?php

/**
 * 微信支付设置表单
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */

 if (!current_user_can('edit_posts')) {
    wp_die(__('您没有权限访问此页面。', 'wwpo'));
}
// class Account
// {
//     public $user_id;

//     public function __construct()
//     {
//         $this->user_id = get_current_user_id();
//     }

//     /**
//      * Undocumented function
//      *
//      * @param [type] $tabs
//      * @return void
//      */
//     public function display($tabs)
//     {
//         call_user_func([$this, $tabs]);
//     }

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_display_wechat()
{
    echo wwpo_wp_tabs([
        'index'     => '概览',
        'account'   => '帐号设定',
        'wxpay'     => '微信支付'
    ]);
?>

<?php
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_display_wechat_account()
{
    if (!current_user_can('edit_posts')) {
        wp_die(__('您没有权限访问此页面。', 'wwpo'));
    }

    echo wwpo_wp_tabs([
        'index'     => '概览',
        'account'   => '帐号设定',
        'wxpay'     => '微信支付'
    ]);

    $wechat_data  = get_option(\Wechat\MP\Api::KEY_WECHAT);

    $wechat_form  = [
        'button'    => \Wechat\Ajax::WECHAT,
        'hidden'    => ['post_key' => \Wechat\MP\Api::KEY_WECHAT],
        'formdata'  => [
            'wechat[appid]'  => [
                'title' => '开发者ID（AppID）',
                'field' =>  ['type' => 'text', 'value' => $wechat_data['appid'] ?? '']
            ],
            'wechat[appsecret]'  => [
                'title' => '开发者密码（AppSecret）',
                'field' => ['type' => 'text', 'value' => $wechat_data['appsecret'] ?? '']
            ],
            'wechat[token]'  => [
                'title' => '令牌（Token）',
                'field' => ['type' => 'text', 'value' => $wechat_data['token'] ?? '']
            ],
            'wechat[encodingaeskey]'  => [
                'title' => '消息加解密密钥（EncodingAESKey）',
                'field' => ['type' => 'text', 'value' => $wechat_data['encodingaeskey'] ?? '']
            ],
            'wechat_url'    => [
                'title' => '微信服务器连接地址',
                'field' => ['type' => 'text', 'value' => home_url('account/wechat'), 'disabled' => true],
                'after' => '<button class="btn btn-outline-secondary" type="button" data-action="copy" data-target="#wechat_url">复制</button>'
            ],
            'accesstoken'    => [
                'title' => '接口调用凭据（Access Token）',
                'desc'  => '过期时间：' . (isset($wechat_data['tokenexpires']) ? date('Y-m-d H:i:s', $wechat_data['tokenexpires'] + 7200) : '没有记录'),
                'field' => ['type' => 'textarea', 'value' => ($wechat_data['accesstoken'] ?? ''), 'disabled' => WP_DEBUG ? false : true]
            ],
            'wechat[customer]'   => [
                'title' => '呼叫客服关键字',
                'desc'  => '设置呼叫在线客服的关键字',
                'field' => ['type' => 'text', 'value' => $wechat_data['customer'] ?? '']
            ],
            'wechat[ads]' => [
                'title' => '广告头图',
                'desc'  => '被关注时消息封面显示广告内容。<a href="https://codex.wordpress.org/Keyboard_Shortcuts" target="_blank">广告内容设置',
                'field' => ['type' => 'select', 'selected' => $wechat_data['ads'] ?? 0, 'option' => ['关闭', '开启']]
            ],
            'wechat[register]' => [
                'title' => '注册链接',
                'desc'  => sprintf('被关注时消息显示注册链接，请先在<a href="%s">[设置-常规]</a>里开启成员资格注册功能。', admin_url('options-general.php')),
                'field' => ['type' => 'select', 'selected' => $wechat_data['register'] ?? 0, 'option' => ['关闭', '开启']]
            ]
        ]
    ];

    if (WP_DEBUG) {
        $wechat_form['formdata']['accesstoken']['desc'] .= sprintf('（<a href="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%1$s&secret=%2$s" target="_blank">获取 Access Token</a>）', $wechat_data['appid'] ?? '', $wechat_data['appsecret'] ?? '');
    }

    echo WWPO_Form::table($wechat_form);
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_display_wechat_wxpay()
{
    if (!current_user_can('edit_posts')) {
        wp_die(__('您没有权限访问此页面。', 'wwpo'));
    }

    echo wwpo_wp_tabs([
        'index'     => '概览',
        'account'   => '帐号设定',
        'wxpay'     => '微信支付'
    ]);

    $wechat_data  = get_option(\Wechat\MP\Api::KEY_WXPAY);

    $wechat_form  = [
        'button'    => \Wechat\Ajax::WXPAY,
        'hidden'    => ['post_key' => \Wechat\MP\Api::KEY_WXPAY],
        'formdata'  => [
            'wechat[mchid]'  => [
                'title' => '商户号',
                'field' =>  ['type' => 'text', 'value' => $wechat_data['mchid'] ?? '']
            ],
            'wechat[apikeyid]'  => [
                'title' => 'API 证书编号',
                'field' => ['type' => 'text', 'value' => $wechat_data['apikeyid'] ?? '']
            ],
            'wechat[apikeysecret]'  => [
                'title' => 'API 证书密钥',
                'field' => ['type' => 'textarea', 'value' => $wechat_data['apikeysecret'] ?? '']
            ],
            'wechat[apisecret]'    => [
                'title' => 'API 密钥',
                'field' => ['type' => 'text', 'value' => $wechat_data['apisecret'] ?? ''],
            ],
            'wechat[apiv3secret]'   => [
                'title' => 'APIv3 密钥',
                'field' => ['type' => 'text', 'value' => $wechat_data['apiv3secret'] ?? '']
            ]
        ]
    ];

    echo WWPO_Form::table($wechat_form);
}
