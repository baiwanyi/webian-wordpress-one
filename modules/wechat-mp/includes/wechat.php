<?php

/**
 * 微信公众号 API 接口类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat
{
    /** 接口常量 */
    const KEY_OPTION = 'wwpo_wechat_mp';
    const KEY_MENU = 'wwpo_wechat_menu';
    const KEY_USERMETA = 'wwpo_wxmp_usermeta';
    const KEY_OPENID = 'wwpo_wxmp_openid';
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
        register_rest_route($this->namespace, 'wechat/mp', [
            'methods'               => WP_REST_Server::ALLMETHODS,
            'callback'              => [$this, 'rest_wechat_mp'],
            'permission_callback'   => [$this, 'permissions_check'],
        ]);
    }

    /**
     * 微信支付订单查询接口
     *
     * @since 1.0.0
     */
    public function rest_wechat_mp($request)
    {
        if (empty($request['echostr'])) {
            $this->message();
            exit;
        }

        /**
         * 验证开发者服务器 url 有效性
         *
         * @since 1.0.0
         * @param  string   token   微信用户手动输入的配置信息
         *
         * 加密/校验流程
         * 1、微信服务器发送 get 请求
         * 2、将 signature、timestamp、nonce、echostr 四个参数发送到开发者提供的 url
         * 3、利用接收到的参数进行验证。
         */
        $option = get_option(self::KEY_OPTION);

        /* 将 token、timestamp（时间戳）、nonce（随机数）三个参数进行字典序排序 */
        $array = [$option['token'], $request['timestamp'], $request['nonce']];
        sort($array, SORT_STRING);

        /* 将三个参数字符串拼接成一个字符串进行 sha1 加密 */
        $str = sha1(implode($array));

        /* 开发者获得加密后的字符串可与 signature（获取微信发送确认的参数）对比，标识该请求来源于微信 */
        if ($str == $request['signature'] && $request['echostr']) {
            echo $request['echostr'];
            exit;
        }

        return false;
    }

    /**
     * Undocumented function
     *
     * @param integer $code
     */
    static function errcode($code = 0)
    {
        return WWPO_Wechat_Errcode::display($code);
    }

    /**
     * 接收消息响应回复函数
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_standard_messages.html
     *
     * 当普通微信用户向公众账号发消息时，微信服务器将 POST 消息的 XML 数据包到开发者填写的 URL 上
     * 微信消息类型：
     *  - text          文本消息
     *  - image         图片消息
     *  - voice         语音消息
     *  - video         视频消息
     *  - shortvideo    小视频消息
     *  - location      地理位置消息
     *  - link          链接消息
     */
    public function message()
    {
        // 获取到微信推送过来 post 数据
        $data = file_get_contents('php://input');

        $option = get_option(self::KEY_OPTION);

        /** 判断数据为空 */
        if (empty($data)) {
            return;
        }

        /**
         * 处理消息类型，并设置回复类型和内容
         *  - ToUserName    开发者微信号
         *  - FromUserName  发送方帐号（一个 OpenID）
         *  - CreateTime    消息创建时间（整型）
         *  - MsgType       消息类型
         *  - MsgId         消息 id，64位整型
         */
        $object     = (object) simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        $msg_type   = strtolower($object->MsgType);

        /** 事件消息 */
        if ('event' == $msg_type) {
            echo WWPO_Wechat_Event::call($object, $option);
            return;
        }

        /** 文本消息 */
        if ('text' == $msg_type) {

            // 获取用户输入关键字
            $keyword = trim($object->Content);

            // 判断用户输入关键字是否与客服接口一致，相同则呼叫客服
            if ($keyword === $option['customer']) {
                echo WWPO_Wechat_Template::customer($object);
                return;
            }

            // 关键字智能回复
            echo WWPO_Wechat_Event::smart($object, $keyword, $option['smart']);
            return;
        }

        /**
         * 响应消息动作：wwpo_wechat_message
         *
         * @since 1.0.0
         * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_standard_messages.html
         * @param string $msg_type  消息类型
         * @param object $object
         * {
         *  消息内容
         *  1、文本消息：text
         *  - Content 文本消息内容
         *
         *  2、图片消息：image
         *  - PicUrl    图片链接（由系统生成）
         *  - MediaId   图片消息媒体 id，可以调用获取临时素材接口拉取数据。
         *
         * 3、语音消息：voice
         *  - MediaId   语音消息媒体 id，可以调用获取临时素材接口拉取数据。
         *  - Format    语音格式，如 amr，speex 等
         *  开通语音识别
         *  - Recognition   语音识别结果，UTF8 编码
         *
         * 4、小视频消息：shortvideo
         *  - MediaId       视频消息媒体 id，可以调用获取临时素材接口拉取数据。
         *  - ThumbMediaId  视频消息缩略图的媒体 id，可以调用获取临时素材接口拉取数据。
         *
         * 5、地理位置消息：location
         *  - Location_X    地理位置纬度
         *  - Location_Y    地理位置经度
         *  - Scale         地图缩放大小
         *  - Label         地理位置信息
         *
         * 6、链接消息：link
         *  - Title         消息标题
         *  - Description   消息描述
         *  - Url           消息链接
         * }
         */
        if (has_action('wwpo_wechat_message')) {
            do_action('wwpo_wechat_message', $msg_type, $object);
            return;
        }
    }

    /**
     * 获取 access token 函数
     *
     * @since 1.0.0
     * ACCESS_TOKEN 的存储至少要保留 512 个字符空间。
     * ACCESS_TOKEN 的有效期目前为 2 个小时，需定时刷新，重复获取将导致上次获取的 ACCESS_TOKEN失效。
     */
    static function access_token()
    {
        $option = get_option(self::KEY_OPTION);

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
     * 请求微信服务器数据函数
     *
     * @since 1.0.0
     *
     * @param string    $action 地址动作别名
     * @param array     $body   传输数据
     */
    static function curl($action, $body = null)
    {
        // 获取 ACCESS_TOKEN
        $access_token = self::access_token();

        /** 判断获取失败，返回完整错误信息，正确获取 ACCESS_TOKEN 已剔除 errcode 字段 */
        if (isset($access_token['errcode'])) {
            return $access_token;
        }

        // 设定请求 URL 参数
        $url = sprintf('%s/%s', self::DOMAIN, $action);
        $url = add_query_arg('access_token', $access_token, $url);

        $option = null;

        if (isset($body)) {
            $option = [
                'json_encode'   => true,
                'body'          => $body
            ];
        }

        $response = wwpo_curl($url, $option);
        return $response;
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
