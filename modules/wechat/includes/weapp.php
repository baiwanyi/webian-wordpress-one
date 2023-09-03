<?php

/**
 * 微信小程序 API 接口类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Weapp
{
    /** 接口常量 */
    const KEY_OPTION = 'wwpo_wechat_app';
    const KEY_USERMETA = 'wwpo_wxapp_usermeta';
    const KEY_OPENID = 'wwpo_wxapp_openid';
    const DOMAIN = 'https://api.weixin.qq.com';

    /**
     * 命名空间
     *
     * @since 1.0.0
     * @var string
     */
    public $namespace;

    /**
     * 构造函数
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->namespace = 'wwpo';
    }

    /**
     * 注册接口
     *
     * @since 1.0.0
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, 'weapp/userlogin', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'rest_user_login'],
            'permission_callback'   => [$this, 'permissions_check'],
        ]);

        register_rest_route($this->namespace, 'weapp/qrcode/unlimited', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'rest_create_limit_qrcode'],
            'permission_callback'   => [$this, 'permissions_check'],
        ]);

        register_rest_route($this->namespace, 'weapp/page', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'rest_create_page'],
            'permission_callback'   => [$this, 'permissions_check'],
        ]);
    }

    /**
     * 获取 access token 函数
     *
     * @since 1.0.0
     * ACCESS_TOKEN 的存储至少要保留 512 个字符空间。
     * ACCESS_TOKEN 的有效期目前为 2 个小时，需定时刷新，重复获取将导致上次获取的 ACCESS_TOKEN失效。
     */
    public function access_token()
    {
        $option = get_option(self::KEY_OPTION);

        if (empty($option['appid']) || empty($option['appsecret'])) {
            return;
        }

        /** 获取系统存储 ACCESS_TOKEN 信息 */
        $access_token = $option['accesstoken'] ?? 0;

        /** 判断当系统的 ACCESS_TOKEN 信息为空或者有效时间过期（当前时间比有效时间多 7200 秒），则从服务器获取最新 ACCESS_TOKEN 信息 */
        if (empty($access_token) || (NOW - $option['tokenexpires']) > 7200) {

            /** 获取微信服务器 ACCESS_TOKEN */
            $response = wwpo_curl(sprintf('%1$s/cgi-bin/token?grant_type=client_credential&appid=%2$s&secret=%3$s', self::DOMAIN, $option['appid'], $option['appsecret']));

            /** 判断 ACCESS_TOKEN 获取成功，执行更新数据库操作，否则返回完整错误信息 */
            if (empty($response['errcode'])) {

                // 设定获取的 ACCESS_TOKEN
                $access_token = $response['access_token'];

                // 设定保存时间，更新数据库
                $option['accesstoken']  = $access_token;
                $option['tokenexpires'] = NOW;

                update_option(self::KEY_OPTION, $option);

                // 返回获取的 ACCESS_TOKEN
                return $access_token;
            }

            // 返回完整 ACCESS_TOKEN 信息
            return $response;
        }

        // 返回系统保存的 ACCESS_TOKEN
        return $access_token;
    }

    /**
     * 登录凭证校验
     * 通过 wx.login 接口获得临时登录凭证 code 后传到开发者服务器调用此接口完成登录流程。
     *
     * @since 1.0.0
     * @param array $code 登录时获取的 code，可通过 wx.login 获取
     */
    public function jscode2session($code)
    {
        $option = get_option(self::KEY_OPTION);
        $response = wwpo_curl(sprintf('%1$s/sns/jscode2session?appid=%2$s&secret=%3$s&js_code=%4$s&grant_type=authorization_code', self::DOMAIN, $option['appid'], $option['appsecret'], $code));

        if (empty($response['errcode'])) {
            return $response;
        }
    }

    /**
     * 登录凭证校验
     * 通过 wx.login 接口获得临时登录凭证 code 后传到开发者服务器调用此接口完成登录流程。
     *
     * @since 1.0.0
     * @param array $code 登录时获取的 code，可通过 wx.login 获取
     */
    public function getphonenumber($code)
    {
        $accesstoken = $this->access_token();
        $options['json_encode'] = true;
        $options['body']['code'] = $code;
        $response = wwpo_curl(sprintf('%1$s/wxa/business/getuserphonenumber?access_token=%2$s', self::DOMAIN, $accesstoken), $options);

        if (empty($response['errcode'])) {
            return $response;
        }
    }

    /**
     * Undocumented function
     *
     * @since 1.0.0
     * @param array $request
     */
    public function rest_user_login($request)
    {
        if (empty($request['jscode']) || empty($request['phonecode'])) {
            return ['error' => '未能登录'];
        }

        $jscode_data    = $this->jscode2session($request['jscode']);
        $phone_data     = $this->getphonenumber($request['phonecode']);

        if (empty($jscode_data) || empty($phone_data)) {
            return ['error' => '登录失败'];
        }

        $user_login = ['phone' => $phone_data['phone_info']['purePhoneNumber']];

        $userdata = get_user_by('login', $user_login['phone']);

        $user_login['id']   = $userdata->ID;
        $user_login['role'] = $userdata->roles[0];

        if (empty($user_login['id'])) {
            //
            $updated = [
                'user_login'    => $user_login['phone'],
                'user_pass'     => wp_generate_password(),
                'user_email'    => $user_login['phone'] . '@xueqianhui.cn',
                'display_name'  => $user_login['phone'],
                'role'          => get_option('default_role')
            ];

            if ($request['inviteuser']) {
                $inviteuser = get_user_by('id', $request['inviteuser']);

                if (in_array($inviteuser->roles[0], ['administrator', 'manager'])) {
                    $updated['role'] = 'teacher';
                }
            }

            //插入用户数据库表
            $user_login['id'] = wp_insert_user($updated);

            if (empty($user_login['id'])) {
                return ['error' => '用户创建失败'];
            }

            update_user_meta($user_login['id'], self::KEY_OPENID, $jscode_data['openid']);
            update_user_meta($user_login['id'], 'user_phone', $user_login['phone']);

            if ($request['inviteuser']) {
                update_user_meta($user_login['id'], 'wwpo_invite_user', $request['inviteuser']);
            }

            $user_login['role'] = $updated['role'];
        }

        return $user_login;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function rest_create_limit_qrcode($request)
    {
        $accesstoken = $this->access_token();
        $options['headers'] = ['Content-Type' => 'image/png'];
        $options['json_encode'] = true;
        $options['json_decode'] = false;
        $options['body'] = [
            'scene'         => $request['scene'],
            'page'          => $request['page'],
            'width'         => $request['width'] ?? 500,
            'is_hyaline'    => $request['is_hyaline'] ?? false,
            'check_path'    => $request['check_path'] ?? true,
            'env_version'   => $request['version'] ?? 'release',
        ];

        if (isset($request['line_color'])) {
            $options['body']['line_color'] = $request['line_color'];
        }

        $response = wwpo_curl(sprintf('%1$s/wxa/getwxacodeunlimit?access_token=%2$s', self::DOMAIN, $accesstoken), $options);

        if (empty($response['errcode'])) {

            if ($request['stream']) {
                return $response;
            }

            return base64_encode($response);
        }
    }

    /**
     * Undocumented function
     *
     * @param [type] $request
     * @return void
     */
    public function rest_create_page($request)
    {
        if (empty($request['post'])) {
            return;
        }

        $post = get_post($request['post']);

        return [
            'title'     => $post->post_title,
            'excerpt'   => $post->post_excerpt,
            'content'   => WWPO_Weapp::nodes($post->post_content),
        ];
    }

    /**
     * 内容转换到小程序节点
     *
     * @since 1.0.0
     * @param string $content
     */
    static public function nodes($content)
    {
        if (empty($content)) {
            return;
        }

        $content    = explode("\n", $content);
        $nodes      = [];

        foreach ($content as $content_key => $content_data) {

            //
            $content_data = str_replace('&nbsp;', '', trim(strip_tags($content_data, '<img><strong><em><del><h2><h3><h4><h5><h6><blockquote><code>')));


            //
            if (empty($content_data)) {
                continue;
            }

            preg_match("/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i", $content_data, $image_url);

            if (empty($image_url)) {

                $content_data = strip_tags($content_data);

                if (empty($content_data)) {
                    continue;
                }

                $nodes[$content_key] = [
                    'name'  => 'p',
                    'attrs'     => ['class' => 'nodes'],
                    'children'  => [
                        [
                            'type'  => 'text',
                            'text'  => $content_data
                        ]
                    ]
                ];


                if (false !== strpos($content_data, '<em')) {
                    $nodes[$content_key]['attrs']['style'] = 'font-style:italic;';
                }

                if (false !== strpos($content_data, '<del')) {
                    $nodes[$content_key]['attrs']['style'] = 'text-decoration:line-through;';
                }

                if (false !== strpos($content_data, '<strong')) {
                    $nodes[$content_key]['attrs']['style'] = 'font-weight:550;';
                }

                if (false !== strpos($content_data, '<blockquote')) {
                    $nodes[$content_key]['attrs']['style'] = 'padding:30rpx;';
                }

                if (false !== strpos($content_data, '<h')) {
                    $nodes[$content_key]['attrs']['style'] = 'font-weight:550;';
                }
            } else {
                $nodes[$content_key]['name']            = 'img';
                $nodes[$content_key]['attrs']['class']  = 'thumb';
                $nodes[$content_key]['attrs']['src']    = $image_url[1];
            }
        }


        return array_values($nodes);
    }

    /**
     * 权限验证函数
     *
     * @since 1.0.0
     * @param array $request 请求参数
     */
    public function permissions_check($request)
    {
        if (WP_DEBUG) {
            return true;
        }

        return true;
    }
}
