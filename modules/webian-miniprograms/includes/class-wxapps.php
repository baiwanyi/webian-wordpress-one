<?php

/**
 * 微信小程序 API 接口类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat/wxapps
 *
 * @api wwpo_wxapps_user_login  小程序登录信息接口
 */
class WWPO_Wxapps
{
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
        $this->namespace = 'wwpo/wxapps';
    }

    /**
     * 注册接口
     *
     * @since 1.0.0
     */
    public function register_routes()
    {
        /**
         * 小程序用户登录接口
         *
         * @since 1.0.0
         * @method POST /wwpo/wxapps/userlogin
         */
        register_rest_route($this->namespace, 'userlogin', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'rest_user_login'],
            'permission_callback'   => [$this, 'permissions_check'],
        ]);

        /**
         * 获取同步数据
         *
         * @since 1.0.0
         * @method GET /wwpo/wxapps/sync
         */
        register_rest_route($this->namespace, 'sync', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'rest_get_sync'],
            'permission_callback'   => [$this, 'permissions_check'],
        ]);

        /**
         * 获取不限制的小程序码接口
         *
         * @since 1.0.0
         * @method POST /wwpo/wxapps/qrcode/unlimited
         */
        register_rest_route($this->namespace, 'qrcode/unlimited', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'rest_create_limit_qrcode'],
            'permission_callback'   => [$this, 'permissions_check'],
        ]);

        /**
         * 获取小程序页面接口
         *
         * @since 1.0.0
         * @method GET /wwpo/wxapps/page
         */
        register_rest_route($this->namespace, 'page', [
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
        $option_data = get_option(WECHAT_KEY_OPTION, []);

        if (empty($option_data['wxapps']['appid']) || empty($option_data['wxapps']['appsecret'])) {
            return;
        }

        /** 获取系统存储 ACCESS_TOKEN 信息 */
        $access_token = $option_data['wxapps']['accesstoken'] ?? 0;

        /**
         * 判断当系统的 ACCESS_TOKEN 信息为空或者有效时间过期（当前时间比有效时间多 7200 秒）
         * 则从服务器获取最新 ACCESS_TOKEN 信息
         */
        if (empty($access_token) || (NOW - $option_data['wxapps']['tokenexpires']) > 7200) {

            /** 获取微信服务器 ACCESS_TOKEN */
            $response = wwpo_curl(
                sprintf('%s/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s',
                WECHAT_DOMAIN,
                $option_data['wxapps']['appid'],
                $option_data['wxapps']['appsecret'])
            );

            /** 判断 ACCESS_TOKEN 获取成功，执行更新数据库操作，否则返回完整错误信息 */
            if (empty($response['errcode'])) {

                // 设定获取的 ACCESS_TOKEN
                $access_token = $response['access_token'];

                // 设定保存时间，更新数据库
                $option_data['wxapps']['accesstoken']  = $access_token;
                $option_data['wxapps']['tokenexpires'] = NOW;

                update_option(WECHAT_KEY_OPTION, $option_data);

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
        $option_data = get_option(WECHAT_KEY_OPTION, []);

        if (empty($option_data['wxapps']['appid']) || empty($option_data['wxapps']['appsecret'])) {
            return;
        }

        $response = wwpo_curl(sprintf('%1$s/sns/jscode2session?appid=%2$s&secret=%3$s&js_code=%4$s&grant_type=authorization_code', WECHAT_DOMAIN, $option_data['wxapps']['appid'], $option_data['wxapps']['appsecret'], $code));

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

        $option_datas['json_encode']    = true;
        $option_datas['body']['code']   = $code;

        $response = wwpo_curl(sprintf('%1$s/wxa/business/getuserphonenumber?access_token=%2$s', WECHAT_DOMAIN, $accesstoken), $option_datas);

        if (empty($response['errcode'])) {
            return $response;
        }
    }

    /**
     * 小程序用户登录接口函数
     *
     * @since 1.0.0
     * @param array $request
     * {
     *  请求参数
     *  @var string jscode
     *  @var string phonecode
     *  @var string inviteuser
     * }
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

        $option_data = get_option(WECHAT_KEY_OPTION, []);

        // 手机号码设置为登录用户名
        $user_login = ['phone' => $phone_data['phone_info']['purePhoneNumber']];

        // 通过登录名获取用户信息
        $userdata = get_user_by('login', $user_login['phone']);

        // 获取用户 ID 和用户角色
        $user_login['id']   = $userdata->ID;
        $user_login['role'] = $userdata->roles[0];

        /** 判断用户 ID 为空新建用户 */
        if (empty($user_login['id'])) {

            // 设定用户信息数组
            $updated = [
                'user_login'    => $user_login['phone'],
                'user_pass'     => wp_generate_password(),
                'user_email'    => $user_login['phone'] . '@xueqianhui.cn',
                'display_name'  => $user_login['phone'],
                'role'          => get_option('default_role')
            ];

            // 判断邀请用户
            if ($request['inviteuser']) {
                $inviteuser = get_user_by('id', $request['inviteuser']);

                // 修改注册用户角色
                if (in_array($inviteuser->roles[0], $option_data['wxapps']['inviteroles'])) {
                    $updated['role'] = $option_data['wxapps']['updatedrole'];
                }
            }

            //插入用户数据库表
            $user_login['id'] = wp_insert_user($updated);

            if (empty($user_login['id'])) {
                return ['error' => '用户创建失败'];
            }

            update_user_meta($user_login['id'], WECHAT_APP_OPENID, $jscode_data['openid']);
            update_user_meta($user_login['id'], 'user_phone', $user_login['phone']);

            if ($request['inviteuser']) {
                update_user_meta($user_login['id'], '_wwpo_invite_user', $request['inviteuser']);
            }

            $user_login['role'] = $updated['role'];
        }

        /**
         * 小程序登录信息接口
         *
         * @since 1.0.0
         */
        $user_login = apply_filters('wwpo_wxapps_user_login', $user_login);

        return $user_login;
    }

    /**
     * 获取同步数据接口函数
     *
     * @since 1.0.0
     */
    public function rest_get_sync()
    {
        $data       = WWPO_Redis::get('wwpo:wxapps:sync', WP_REDIS_DATABASE);
        $expires    = $data['expires'] ?? 0;

        if ((empty($data) && 43200 > $expires) || WP_DEBUG) {
            $data = [
                'banner'    => wwpo_wxapps_get_banner('home', 3),
                'pages'     => wwpo_wxapps_get_pages(),
                'home'      => wwpo_wxapps_get_home_category(),
                'expires'   => NOW
            ];

            $data = apply_filters('wwpo_wxapps_sync', $data);

            WWPO_Redis::set('wwpo:wxapps:sync', $data, WP_REDIS_DATABASE);
        }

        return $data;
    }

    /**
     * 获取不限制的小程序码接口函数
     *
     * @since 1.0.0
     * @param array $request
     * {
     *  请求参数
     *  @var string     scene       场景值。最大32个可见字符，只支持数字，大小写英文。
     *  @var string     page        页面 page，
     *  @var integer    width       二维码宽度，默认：500
     *  @var boolean    is_hyaline  是否需要透明底色，默认：false
     *  @var boolean    check_path  检查 page 是否存在，默认：true
     *  @var string     version     要打开的小程序版本，默认：release。体验版为 "trial"，开发版为 "develop"
     *  @var string     line_color  设置颜色，格式：{"r":0,"g":0,"b":0}
     *  @var string     stream      是否直接显示二进制图片，默认：false
     * }
     */
    public function rest_create_limit_qrcode($request)
    {
        $setting['headers'] = ['Content-Type' => 'image/png'];
        $setting['json_encode'] = true;
        $setting['json_decode'] = false;
        $setting['body'] = [
            'scene'         => $request['scene'],
            'page'          => $request['page'],
            'width'         => $request['width'] ?? 500,
            'is_hyaline'    => $request['is_hyaline'] ?? false,
            'check_path'    => $request['check_path'] ?? true,
            'env_version'   => $request['version'] ?? 'release'
        ];

        if (isset($request['line_color'])) {
            $setting['body']['line_color'] = $request['line_color'];
        }

        $response = wwpo_curl(
            sprintf('%1$s/wxa/getwxacodeunlimit?access_token=%2$s', WECHAT_DOMAIN, $this->access_token()),
            $setting
        );

        if (is_array($response)) {
            return ['error' => '二维码生成失败'];
        }

        if ($request['stream']) {

            if (empty($request['guid'])) {
                return $response;
            }

            /**
             * 声明 OSS 类
             *
             * @var object
             */
            $alioss = new WWPO_Alioss();

            if (empty($alioss->ossdata['enable']) || empty($alioss->ossclient)) {
                return ['error' => __('阿里云OSS未开启', 'wwpo')];
            }

            // 上传文件到 OSS
            $alioss->ossclient->putObject($alioss->bucket, $request['guid'], $response);

            // 返回 OSS 地址
            return wwpo_oss_cdnurl($request['guid'], 'large');
        }

        return base64_encode($response);
    }

    /**
     * 小程序页面获取接口函数
     *
     * @since 1.0.0
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
            'content'   => WWPO_Wxapps::nodes($post->post_content),
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

            $content_data = str_replace('&nbsp;', '', trim(strip_tags($content_data, '<img><strong><em><del><h2><h3><h4><h5><h6><blockquote><code>')));

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
