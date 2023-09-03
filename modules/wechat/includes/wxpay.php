<?php

/**
 * 微信支付 API 接口
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 *
 * @api wwpo_wxpay_order_query  订单查询
 * @api wwpo_wxpay_native       商户 Native 支付
 * @api wwpo_wxpay_jsapi        商户 JSAPI  下单
 * @api wwpo_wxpay_notice       支付通知
 */

class WWPO_Wxpay
{
    const KEY_OPTION = 'wwpo_wechat_pay';
    const DOMAIN = 'https://api.mch.weixin.qq.com/v3/pay/transactions/';

    /**
     * 账户别名
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $name;

    /**
     * 微信设置内容
     *
     * @since 1.0.0
     *
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
     * 注册接口
     *
     * @since 1.0.0
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, 'wxpay/query/(?P<name>[a-zA-Z0-9_-]+)', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'rest_order_query'],
            'permission_callback'   => [$this, 'permissions_check'],
        ]);

        register_rest_route($this->namespace, 'wxpay/notice/(?P<name>[a-zA-Z0-9_-]+)', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'rest_notice'],
            'permission_callback'   => [$this, 'permissions_check'],
        ]);
    }

    /**
     * 构造函数
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->namespace    = 'wwpo';

        // $this->name     = $request['name'];
        // $this->data     = \Wechat\wxloader::meta($this->name);
        $privateKey = 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDavhVS1AXlBdfK ZKxJsaMM1dtIGerAS9atTlaq/5HkP7FDi7y2OLekZlRJ5TNWz++Ul+ENMNonhp0q Wrm+3VyulehMEWK4gjKbfADlaB6KGXeGxbB2p9t02gQXBr9MucSXdXPGgoOEg1+e/Axk2ujzIwpH1Jq4LwA1kC6Z36rZdc3YI9c7jUlz9zN/9NRRB+i9OnFXg5NgXvxq qmo+6VvZXpjNCC5l4Kh8wX4PAWC5fhVdRg5kNSV3LtCeu9MkT/RzfuTFjVil3Px5 BV3qSER5jsf5AGyKB+2XgQM6a9MkeNCpuKmeFw/+8cQFgaTVZNH6fvw5KO4zbMi5 AB9/a1iLAgMBAAECggEAZq8muWw4nCtjAmrCChIulX7q4mzwK3cufsqwmrNAnGJd 53R4kR4M70ruNIY41H7mW930UsnlDvr9Wn2ehn/J21cZ5mOe8TiFY0IR9Z8r/u43 kvfIf9sKhU827kxHj1ABfowvXje0X4eAYtS1SL8O7dw8Hj4qp13A/mQBzFAYFt58 0Y24GX2u5v7UmkU9L512HRHFqzjVP9+u05iJVmab3JEIfT+ZRo+SKEUvidANkOAI xAGHiBkHONF7KkzZYaLzFxZkudJfI3OCC0Ypg0hvW7/O6wqMK4MfrOqDKq82/2a2 MIOK23T39ZmT4NNf0pknkcVh9w3p1d13pEfpWTvesQKBgQD6ATWs5YBbdaESEaaq N43xdYGG1UUSUj1tgsTuDWADE7hIc9V3Eev8VE0ca0GI9XSvkc0LG96x1pHNzJY4 5IDbdzLCL63b2qoG+z73hzMfwWZrC/8E+AX/BPQFW2encUqysMxkBhyMUnM3d5oR 9+Cm09BQuYVnoLkaDpmMrTg54wKBgQDf/PQXyVvAqZ8Aj7/T1d68vIs37+NAItWs AKg5tUTcj5aLeeQinHNqB/2wsMZ/ISl2RTVmKFJUfa8CgaonFsINZNT4ND195MO5 4WTenZSmvFeBSltFa3yM/XWRJTw5fcLE969w8mOmBu6wakNVeN0pa8GrVjvYWuf1 trHZhC3HOQKBgQDa7hpCCUxpS049E4X/A91ieMNv/u2YyLoQX3by/HV63FcB46Yo umIMuwo3+9kNBd4kLasAsmxHEh7muOVIdxo8llq14KkAobFJodWXUCc+BNAaqAuw Hz6o/35t/oh8AmMmrlqesRdo7n8FMNCUMZzimxSOzJf9kqrmHajrn3lgfwKBgDC1 pa92jol7WaSZnjHHFMUei3gCpvzPln/tNKg4D12XrDlwrHgKZd7tFfJSvxfuckHS EybAJgdRvblh0Urm3BRllRrU4Xp7QUUvCuyOgEEyPCVVsjuKgG94vxRtcIdgHfcP lguN6rW0VDvxH+t6eT4EvP0xp5oJSuBYdpzC7eGhAoGBAKOywynN8+OtCN6MsuJz QD9kVAp8IlgI+SJgvFeErKuXMySkz2SVrR20Ip4pNEMs6kUJARaDdPgPH5WFh9rt oTdvtnPjhz8ZUdtvb3kvzYhCDXEswJp3Q6a1BqDn+Lw+fg1jCpY/CPSv0oxET93z NaQ6Biwhq1fdv8B1cx4U6wx/';
        $apikeysecret = "-----BEGIN PRIVATE KEY-----\n" . wordwrap($privateKey, 64, "\n", true) . "\n-----END PRIVATE KEY-----\n";
        $this->name     = 'kuajingdian';
        $this->data     = [
            'appid'         => 'wx2126e7b3720eec55',
            'mchid'         => '1611929371',
            'apikeyid'      => '27F3F764A55593918F2C55FD1E1AF336C696A774',
            'apikeysecret'  => $apikeysecret,
            'apiv3secret'   => 'yn9EXbyzXTYYXs9FOuLq05LBX5UWLSUr'
        ];
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
    public function create($data = [], $key = 'native')
    {
        // 设定
        $data = wp_parse_args($data, [
            'appid'         => $this->data['appid'],
            'mchid'         => $this->data['mchid'],
            'out_trade_no'  => $request['out_trade_no'],
            'notify_url'    => home_url('wp-json/wwpo/wxpay/notice/' . $this->name),
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
        $response['out_trade_no'] = $request['out_trade_no'];

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
     * Undocumented function
     *
     * @param [type] $request
     * @return void
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
            $sign = \WP_REST_Server::READABLE;
        } else {
            $sign = \WP_REST_Server::CREATABLE;
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
