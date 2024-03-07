<?php

/**
 * 微信支付 API 接口
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat/wxpay
 *
 * @api wwpo_wxpay_order_query  订单查询
 * @api wwpo_wxpay_native       商户 Native 支付
 * @api wwpo_wxpay_jsapi        商户 JSAPI  下单
 * @api wwpo_wxpay_notice       支付通知
 */

class WWPO_Wxpay
{
    const DOMAIN = 'https://api.mch.weixin.qq.com/v3/pay/transactions/';

    /**
     * 账户别名
     *
     * @since 1.0.0
     * @var string
     */
    public $name;

    /**
     * 微信设置内容
     *
     * @since 1.0.0
     * @var array
     */
    public $data;

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
        $this->namespace = 'wwpo/wxpay';

        $option_data    = get_option(WECHAT_KEY_OPTION, []);
        $wechat_data    = $option_data['wechat'] ?? [];
        $wxpay_data     = $option_data['wxpay'] ?? [];

        if (isset($wxpay_data['apikeysecret'])) {
            $apikeysecret = "-----BEGIN PRIVATE KEY-----\n" . wordwrap($wxpay_data['apikeysecret'], 64, "\n", true) . "\n-----END PRIVATE KEY-----\n";
        }

        $this->data     = [
            'appid'         => $wechat_data['appid'] ?? '',
            'mchid'         => $wxpay_data['mchid'] ?? '',
            'apikeyid'      => $wxpay_data['apikeyid'] ?? '',
            'apikeysecret'  => $apikeysecret ?? '',
            'apiv2secret'   => $wxpay_data['apiv2secret'] ?? '',
            'apiv3secret'   => $wxpay_data['apiv3secret'] ?? ''
        ];
    }

    /**
     * 注册接口
     *
     * @since 1.0.0
     */
    public function register_routes()
    {
        /**
         * 微信支付订单查询接口
         *
         * @since 1.0.0
         * @method GET /wwpo/wxpay/query/{NAME}
         */
        register_rest_route($this->namespace, 'query/(?P<name>[a-zA-Z0-9_-]+)', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'rest_order_query'],
            'permission_callback'   => [$this, 'permissions_check'],
        ]);

        /**
         * 微信支付通知接口
         *
         * @since 1.0.0
         * @method POST /wwpo/wxpay/notice/{NAME}
         */
        register_rest_route($this->namespace, 'notice/(?P<name>[a-zA-Z0-9_-]+)', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'rest_notice'],
            'permission_callback'   => [$this, 'permissions_check'],
        ]);
    }

    /**
     * 查询订单 API 接口
     * 商户可以通过查询订单接口主动查询订单状态，完成下一步的业务逻辑。查询订单状态可通过微信支付订单号或商户订单号两种方式查询
     *
     * @since 1.0.0
     * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_4_2.shtml
     *
     * @param array $request
     * {
     *  传入参数
     *  @var string id              微信支付订单号查询
     *  @var string out_trade_no    商户订单号查询
     * }
     * @return array
     * {
     *  返回参数
     *  @var string appid               应用 ID
     *  @var string mchid               商户号
     *  @var string out_trade_no        商户订单号
     *  @var string transaction_id      微信支付订单号
     *  @var string trade_type          交易类型
     *  @var string trade_state         交易状态
     *  @var string trade_state_desc    交易状态描述
     *  @var string bank_type           付款银行
     *  @var string attach              附加数据
     *  @var string success_time        支付完成时间
     *  @var object payer
     *  {
     *      支付者信息
     *      @var string openid  用户标识
     *  }
     *  @var object amount
     *  {
     *      订单金额
     *      @var integer    total           总金额
     *      @var integer    payer_total     用户支付金额
     *      @var string     currency        货币类型
     *      @var string     payer_currency  用户支付币种
     *  }
     *  @var object scene_info
     *  {
     *      场景信息
     *      @var string device_id   商户端设备号
     *  }
     *  @var array promotion_detail
     *  {
     *      优惠功能
     *      @var string     coupon_id               优惠券 ID
     *      @var string     name                    优惠名称
     *      @var string     scope                   优惠范围
     *      @var string     type                    优惠类型
     *      @var integer    amount                  优惠券面额
     *      @var string     stock_id                活动 ID
     *      @var integer    wechatpay_contribute    微信出资
     *      @var integer    merchant_contribute     商户出资
     *      @var integer    other_contribute        其他出资
     *      @var string     currency                优惠币种
     *      @var array      goods_detail
     *      {
     *          单品列表
     *          @var string     goods_id        商品编码
     *          @var integer    quantity        商品数量
     *          @var integer    unit_price      商品单价
     *          @var integer    discount_amount 商品优惠金额
     *          @var string     goods_remark    商品备注
     *      }
     *  }
     * }
     */
    public function rest_order_query($request)
    {
        /** 判断微信支付订单号查询 */
        if (isset($request['id'])) {
            $url = self::DOMAIN . 'id/' . $request['id'];
        }

        /** 判断商户订单号查询 */
        if (isset($request['out_trade_no'])) {
            $url = self::DOMAIN . 'out-trade-no/' . $request['out_trade_no'];
        }

        $url = add_query_arg('mchid', $this->data['mchid'], $url);

        // 获取查询结果
        $response = $this->_response($url);

        /**
         * 订单查询后动作：wwpo_wxpay_order_query
         * 作用于微信支付对订单进行查询时，对数据库或页面进行修改
         * 不同商品或者内容可以使用附加数据字段「attach」进行区分
         *
         * @since 1.0.0
         *
         * @property array $request     传入参数
         * @property array $response    查询结果
         */
        do_action('wwpo_wxpay_order_query', $request, $response);

        // 返回查询结果
        return $response;
    }

    /**
     * 商户支付下单接口
     * 微信后台系统返回链接参数 code_url，商户后台系统将 code_url 值生成二维码图片，用户使用微信客户端扫码后发起支付。
     *
     * @since 1.0.0
     * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_4_1.shtml
     *
     * @param array $request
     * {
     *  传入参数
     *  @var string out_trade_no 商户订单号
     * }
     * @return array
     * {
     *  返回参数
     *  此 URL 用于生成支付二维码，然后提供给用户扫码支付。
     *  @var string code_url    二维码链接（Native 支付）
     *  @var string prepay_id   预支付交易会话标识（JSAPI 支付）
     * }
     */
    public function create($out_trade_no, $data = [], $key = 'native')
    {
        // 设定
        $data = wp_parse_args($data, [
            'appid'         => $this->data['appid'],
            'mchid'         => $this->data['mchid'],
            'out_trade_no'  => $out_trade_no,
            'notify_url'    => home_url('wp-json/wwpo/wxpay/notice'),
        ]);

        /**
         * 下单请求参数
         *
         * @since 1.0.0
         *
         * @var string appid            应用 ID
         * @var string mchid            商户号
         * @var string out_trade_no     商户订单号
         * @var string description      商品描述
         * @var string time_expire      交易结束时间
         * @var string attach           附加数据
         * @var string notify_url       通知地址
         * @var string goods_tag        订单优惠标记
         * @var object amount
         *  {
         *      订单金额
         *      @var integer    total       总金额
         *      @var string     currency    货币类型
         *  }
         * @var object detail
         *  {
         *      优惠功能
         *      @var integer    cost_price  订单原价
         *      @var string     invoice_id  商品小票 ID
         *      @var array      goods_detail
         *      {
         *          单品列表
         *          @var string     merchant_goods_id   商户侧商品编码
         *          @var string     wechatpay_goods_id  微信侧商品编码
         *          @var string     goods_name          商品名称
         *          @var integer    quantity            商品数量
         *          @var integer    unit_price          商品单价
         *      }
         *  }
         * @var object scene_info
         * {
         *  场景信息
         *  @var string payer_client_ip 用户终端 IP
         *  @var string device_id       商户端设备号
         *  @var array  store_info
         *  {
         *      商户门店信息
         *      @var string id          门店编号
         *      @var string name        门店名称
         *      @var string area_code   地区编码
         *      @var string address     详细地址
         *  }
         * @var object settle_info
         * {
         *  结算信息
         *  @var boolean profit_sharing 是否指定分账
         * }
         * }
         */
        $body = wwpo_json_encode($data);

        // 获取下单返回二维码链接
        $response = $this->_response(self::DOMAIN . $key, $body);
        $response['out_trade_no'] = $out_trade_no;

        /**
         * 设定商户 Native 支付获取商品信息动作：wwpo_wxpay_create
         *
         * @since 1.0.0
         *
         * @property array $response   返回参数
         */
        do_action('wwpo_wxpay_create', $response);

        // 返回内容
        return $response;
    }

    /**
     * Native 支付模式
     * 商户系统按微信支付协议生成支付二维码，用户再用微信“扫一扫”完成支付的模式
     *
     * @since 1.0.0
     * @param array $data
     */
    public function native($data)
    {
        $response = $this->create($data, 'native');

        if (isset($response['code'])) {
            return $response;
        }

        $data['transaction'] = [
            'code_url'  => $response['code_url'],
            'timestamp' => NOW
        ];

        return $data;
    }

    /**
     * JSAPI 支付模式
     * 商户通过调用微信支付提供的JSAPI接口，在支付场景中调起微信支付模块完成收款。
     *
     * @since 1.0.0
     * @param array $data
     */
    public function jsapi($data)
    {
        $response = $this->create($data, 'jsapi');

        if (empty($response['prepay_id'])) {
            return $response;
        }

        $noncestr   = wwpo_random(32);
        $timestamp  = time();
        $package    = 'prepay_id=' . $response['prepay_id'];;
        $sign       = $this->data['appid'] . "\n" .  $timestamp . "\n" . $noncestr . "\n" . $package . "\n";

        // 使用商户私钥对待签名串进行 SHA256 with RSA 签名
        openssl_sign($sign, $raw_sign, $this->data['apikeysecret'], 'sha256WithRSAEncryption');

        $data['transaction'] = [
            'timestamp' => $timestamp,
            'noncestr'  => $noncestr,
            'package'   => $package,
            'paysign'   => base64_encode($raw_sign)
        ];

        return $data;
    }

    /**
     * 支付通知接口
     *
     * @since 1.0.0
     * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_4_5.shtml
     *
     * @param array $request
     * {
     *  通知参数
     *  @var string id              通知 ID
     *  @var string create_time     通知创建时间
     *  @var string event_type      通知类型
     *  @var string resource_type   通知数据类型
     *  @var object resource
     *  {
     *      通知数据
     *      @var string algorithm       加密算法类型
     *      @var string ciphertext      数据密文
     *      @var string associated_data 附加数据
     *      @var string original_type   原始类型
     *      @var string nonce           随机串
     *  }
     *  @var string summary         回调摘要
     * }
     */
    public function rest_notice($request)
    {
        /** 判断支付成功通知的类型不为 TRANSACTION.SUCCESS，返回错误信息 */
        if ('TRANSACTION.SUCCESS' != $request['event_type']) {
            return new WP_Error(
                'ERROR',
                $request['event_type'],
                ['status' => rest_authorization_required_code()]
            );
        }

        // Base64 解码通知数据密文
        $ciphertext = base64_decode($request['resource']['ciphertext']);

        // 解密通知数据内容
        $response = sodium_crypto_aead_aes256gcm_decrypt($ciphertext, $request['resource']['associated_data'], $request['resource']['nonce'], $this->data['apiv3secret']);

        /** 判断通知数据内容为空，返回错误信息 */
        if (empty($response)) {
            return new WP_Error(
                'ERROR',
                '失败',
                ['status' => 400]
            );
        }

        // 通知 json 数据转成数组内容，通知内容返回信息参见：查询订单 API 接口返回参数
        $response = wwpo_json_decode($response);

        /**
         * 微信支付成功通知动作：wwpo_wxpay_notice
         * 用于微信支付成功后进行后续动作，如：修改积分参数等。
         * 不同商品或者内容可以使用附加数据字段「attach」进行区分
         *
         * @since 1.0.0
         *
         * @param array $response   通知返回内容
         */
        do_action('wwpo_wxpay_notice', $response);

        // 返回成功信息，通知应答，微信认为通知失败，微信会通过一定的策略定期重新发起通知，尽可能提高通知的成功率，但微信不保证通知最终能成功。（通知频率为15s/15s/30s/3m/10m/20m/30m/30m/30m/60m/3h/3h/3h/6h/6h - 总计 24h4m）
        return new WP_Error(
            'SUCCESS',
            '成功',
            ['status' => 200]
        );
    }

    /**
     * 微信支付请求函数
     *
     * @since 1.0.0
     *
     * @param string $url   请求地址
     * @param string $body  参数内容
     * @return array
     */
    private function _response($url, $body = '')
    {
        // 设定头部加密信息
        $options['headers'] = $this->_create_headers($url, $body);

        /** 判断请求参数内容 */
        if (!empty($body)) {
            $options['body'] = $body;
        }

        // 获取请求内容
        $response = wwpo_curl($url, $options);

        // 返回请求内容
        return $response;
    }

    /**
     * 设定微信支付请求头部加密信息函数
     *
     * @since 1.0.0
     *
     * @param string $url   请求地址
     * @param string $body  请求报文主体
     * @return array
     */
    private function _create_headers($url, $body = '')
    {
        $headers    = [];
        $nonce_str  = wwpo_random();
        $timestamp  = time();
        $parse_url  = parse_url($url);
        $sign_url   = $parse_url['path'];

        /** 添加 URL 查询字段 */
        if (!empty($parse_url['query'])) {
            $sign_url .= '?' . $parse_url['query'];
        }

        /** 判断报文主体内容，为空使用 GET 方法，否则使用 POST 方法 */
        if (empty($body)) {
            $sign = WP_REST_Server::READABLE;
        } else {
            $sign = WP_REST_Server::CREATABLE;
        }

        /**
         * 构造签名串
         * 注意：即时请求报文主体为空也要添加「\n」否则签名会失败
         *
         * 1、HTTP请求方法\n
         * 2、URL\n
         * 3、请求时间戳\n
         * 4、请求随机串\n
         * 5、请求报文主体\n
         */
        $sign .= "\n" . $sign_url . "\n" . $timestamp . "\n" . $nonce_str . "\n" . $body . "\n";

        // 使用商户私钥对待签名串进行 SHA256 with RSA 签名
        openssl_sign($sign, $raw_sign, $this->data['apikeysecret'], 'sha256WithRSAEncryption');

        // 对签名结果进行 Base64 编码，并设定 HTTP 头签名值
        $signature      = base64_encode($raw_sign);
        $authorization  = sprintf(
            'WECHATPAY2-SHA256-RSA2048 mchid="%s",nonce_str="%s",signature="%s",timestamp="%s",serial_no="%s"',
            $this->data['mchid'],
            $nonce_str,
            $signature,
            $timestamp,
            $this->data['apikeyid']
        );

        // 设定 HTTP 头信息
        $headers['Accept']           = 'application/json, text/plain, application/x-gzip';
        $headers['Content-Type']     = 'application/json; charset=utf-8';
        $headers['Authorization']    = $authorization;

        // 返回 HTTP 头信息
        return $headers;
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
