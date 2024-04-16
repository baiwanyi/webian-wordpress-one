<?php

/*
 * Modules Name: 微博模块
 * Description: 微博模块功能。
 * Version: 1.0.0
 * Author: Baiwanyi
 * Updated: 2022-10-20
 */
add_action('show_user_profile', 'wwpo_weibo_user_profile');
add_filter('wwpo_admin_page_settings', ['WWPO_Weibo', 'settings']);

/**
 * 微博开放平台操作类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Weibo
 */
class WWPO_Weibo
{
    /** 接口常量 */
    const KEY_UID = 'user_weibo_uid';

    const KEY_ACCESSTOKEN = 'user_weibo_accesstoken';

    /**
     * 设置参数
     *
     * @since 1.0.0
     * @var array
     */
    public $option = [];

    /**
     * 用户设置
     *
     * @since 1.0.0
     * @var array
     */
    public $accesstoken = [];

    /**
     * 用户 ID
     *
     * @since 1.0.0
     * @var integer
     */
    public $user_id = 0;

    /**
     * 构造函数
     *
     * @since 1.0.0
     */
    public function __construct()
    {

        $this->user_id = get_current_user_id();
        $this->option = get_option('wwpo-settings-weibo');
        $this->accesstoken = get_user_meta($this->user_id, self::KEY_ACCESSTOKEN, true);
    }

    /**
     * OAuth2 授权第一步 authorize 接口，请求用户授权 code
     *
     * @since 1.0.0
     * @param string $redirect_uri  授权回调地址，传的值需与在开放平台网站填写的回调地址一致
     * @param string $state         用于保持请求和回调的状态。在回调时,会在Query Parameter中回传该参数
     * @param string $display       授权页面类型 可选范围:
     *  - default		默认授权页面
     *  - mobile		支持 html5 的手机
     *  - popup			弹窗授权页
     *  - wap1.2		wap1.2 页面
     *  - wap2.0		wap2.0 页面
     *  - js			js-sdk 专用 授权页面是弹窗，返回结果为 js-sdk 回掉函数
     *  - apponweibo	站内应用专用，站内应用不传 display 参数,并且 response_type 为 token 时，默认使用改 display 授权后不会返回 access_token，只是输出 js 刷新站内应用父框架
     * @link https://open.weibo.com/wiki/Oauth2/authorize
     * @return array
     * {
     *  @var string code    用于第二步调用 access_token 接口，换取授权的 access token
     *  @var string state   如果请求时传递了该参数，则会在回调时回传该参数
     * }
     */
    public function authorizeurl($redirect_uri, $state = NULL, $display = NULL)
    {
        $params = [];
        $params['client_id']        = $this->option['appid'] ?? 0;
        $params['redirect_uri']     = $redirect_uri;
        $params['response_type']    = 'code';

        if (isset($state)) {
            $params['state'] = $state;
        }

        if (isset($display)) {
            $params['display'] = $display;
        }

        return 'https://api.weibo.com/oauth2/authorize?' . http_build_query($params);
    }

    /**
     * OAuth2 授权第二步 access_token 接口，用 code 换取授权 access_token，该步需在服务端完成
     *
     * @since 1.0.0
     * @param string $redirect_uri  授权回调地址，传的值需与在开放平台网站填写的回调地址一致
     * @return array
     * {
     *  @var string access_token    用户授权的唯一票据，用于调用微博的开放接口，同时也是第三方应用验证微博用户登录的唯一票据
     *  @var string expires_in      access_token 的生命周期，单位是秒数
     *  @var string uid             授权用户的 UID
     * }
     */
    public function access_token($redirect_uri = null)
    {
        $response = [];

        if (empty($_GET['code'])) {
            return;
        }

        if (empty($redirect_uri)) {
            $redirect_uri = home_url();
        }

        // 过期时间设定默认值
        $expirestime = $this->accesstoken['expirestime'] ?? 0;

        /** 判断过期时间小于当前时间时，获取新的 access_token 信息 */
        if (NOW > $expirestime) {
            $options['body'] = [
                'client_id'     => $this->option['appid'],
                'client_secret' => $this->option['appsecret'],
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => $redirect_uri,
                'code'          => $_GET['code'],
            ];

            $response = wwpo_curl('https://api.weibo.com/oauth2/access_token', $options);

            if (isset($response['error_code'])) {
                return;
            }
        }

        return $response;
    }

    /**
     * 授权回收接口，帮助开发者主动取消用户的授权。
     *
     * @since 1.0.0
     * @param string $access_token 用户授权应用的 access_token
     * @link https://open.weibo.com/wiki/Oauth2/revokeoauth2
     */
    public function revokeoauth2()
    {
        if (empty($this->accesstoken['accesstoken'])) {
            return;
        }

        $response = wwpo_curl('https://api.weibo.com/oauth2/revokeoauth2?access_token=' . $this->accesstoken['accesstoken']);

        if (isset($response['result'])) {
            delete_user_meta($this->user_id, self::KEY_ACCESSTOKEN);
        }
    }

    public function update_user_meta($redirect_uri = null)
    {
        $data = $this->access_token($redirect_uri);

        if (empty($data)) {
            return;
        }

        update_user_meta($this->user_id, self::KEY_UID, $data['uid']);
        update_user_meta($this->user_id, self::KEY_ACCESSTOKEN, [
            'accesstoken' => $data['access_token'],
            'expirestime' => NOW + $data['expires_in']
        ]);
    }

    /**
     * Undocumented function
     *
     * @param integer $post_id
     * @return void
     */
    public function post_weibo($data)
    {
        if (empty($this->accesstoken['accesstoken'])) {
            return;
        }

        $boundary   = uniqid('------------------');
        $params     = [
            'access_token'  => $this->accesstoken['accesstoken'],
            'status'        => $data['content'],
            'pic'           => $data['image'],
            'rip'           => wwpo_get_ip()
        ];

        $response = wwpo_curl('https://api.weibo.com/2/statuses/share.json', [
            'headers' => [
                'Content-Type' => 'multipart/form-data;boundary=' . $boundary
            ],
            'body' => $this->build_http_multipart($params, $boundary)
        ]);

        return $response;
    }

    /**
     * Undocumented function
     *
     * @param [type] $params
     * @param [type] $boundary
     */
    private function build_http_multipart($params, $boundary = null)
    {
        if (empty($params)) {
            return;
        }

        if (empty($boundary)) {
            $boundary = uniqid('------------------');
        }

        if (!$params) return '';

        uksort($params, 'strcmp');

        $start = '--' . $boundary;
        $end = $start . '--';
        $payload = '';

        foreach ($params as $parameter => $value) {

            if (in_array($parameter, ['pic', 'image'])) {
                $payload .= $start . "\r\n";
                $payload .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . pathinfo($value, PATHINFO_BASENAME) . '"' . "\r\n";
                $payload .= "Content-Type: image/jpg\r\n\r\n";
                $payload .= wwpo_curl(home_url($value), [
                    'json_decode'   => false
                ]);
                $payload .= "\r\n";
            } else {
                $payload .= $start . "\r\n";
                $payload .= 'Content-Disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
                $payload .= $value . "\r\n";
            }
        }

        $payload .= $end;

        return $payload;
    }

    /**
     * 会员信息编辑内容函数
     *
     * @since 1.0.0
     * @param object $user  用户信息
     */
    function wwpo_weibo_user_profile($user)
    {
        if (!current_user_can(WWPO_ROLE)) {
            return;
        }

        $weibo_api      = new WWPO_Weibo();
        $page_action    = $_GET['action'] ?? '';
        $redirect_uri   = admin_url('profile.php');

        /** 注销 Oauth2 动作 */
        if ('revokeoauth' == $page_action) {
            $weibo_api->revokeoauth2();
        }

        if (isset($_GET['code'])) {
            $weibo_api->access_token($redirect_uri);
        }

        // 获取用户 access_token 信息
        $weibo_uid          = get_user_meta($user->ID, WWPO_Weibo::KEY_UID, true);
        $weibo_accesstoken  = get_user_meta($user->ID, WWPO_Weibo::KEY_ACCESSTOKEN, true);

        /** 判断 UID 为空，显示绑定按钮 */
        if (empty($weibo_uid)) {
            $bindweibo = sprintf('<a href="%s" class="button">绑定微博</a>', $weibo_api->authorizeurl($redirect_uri));
        }
        // 已绑定显示 UID 和取消按钮
        else {
            $bindweibo = sprintf('<div class="d-flex align-items-center gap-3"><span class="lead">UID：<strong>%s</strong></span><a href="?action=revokeoauth" class="button">取消绑定</a></div>', $weibo_uid);
        }

        echo WWPO_Form::table([
            'title' => '微博绑定',
            'formdata'  => [
                'bindweibo' => [
                    'title' => '绑定状态',
                    'content' => $bindweibo
                ],
                'accesstoken' => [
                    'title' => '接口调用凭据',
                    'desc'  => '过期时间：' . (isset($weibo_accesstoken['expirestime']) ? date('Y-m-d H:i:s', $weibo_accesstoken['expirestime']) : '没有记录'),
                    'field' => ['type' => 'text', 'value' => $weibo_accesstoken['accesstoken'] ?? '', 'disabled' => true]
                ]
            ]
        ], false);
    }

    /**
     * Undocumented function
     *
     * @param [type] $settings
     * @return void
     */
    static function settings($settings)
    {
        // 获取设置保存值
        $option_data = get_option('wwpo-settings-weibo');

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
}
