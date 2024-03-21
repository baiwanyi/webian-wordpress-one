<?php

/**
 * 实用工具函数类
 *
 * @since 2.0.0
 * @package Webian WordPress One
 */
class WWPO_Util
{
    /**
     * Base64 解密字符串
     *
     * @since 1.0.0
     * @param string    $str        要解密的数据
     * @param string    $aes_key    前后端共同约定的秘钥
     */
    static function base64_decode($str, $aes_key = null)
    {
        $data = base64_decode($str);

        if (isset($aes_key)) {
            $data = openssl_decrypt($data, 'AES-128-ECB', $aes_key, OPENSSL_RAW_DATA);
        }

        return $data;
    }

    /**
     * Base64 加密字符串
     *
     * @since 1.0.0
     * @param string    $str        要加密的数据
     * @param string    $aes_key    前后端共同约定的秘钥
     */
    static function base64_encode($str, $aes_key = null)
    {
        if (isset($aes_key)) {
            $data = openssl_encrypt($str, 'AES-128-ECB', $aes_key, OPENSSL_RAW_DATA);
        }

        $data = base64_encode($data);

        return $data;
    }

    /**
     * cURL 远程请求函数
     *
     * @since 1.0.0
     * @param string    $url        需要获取的网址
     * @param mixed     $options
     * {
     * 参数选项
     *  @var integer   timeout         超时时间，单位（秒）
     *  @var string    method          传送方式
     *  - POST  调用 wp_remote_post 函数
     *  - GET   调用 wp_remote_get 函数
     *  - HEAD  调用 wp_remote_head 函数
     *  - FILE  调用 wp_remote_get 函数
     *  @var mixed     body            传送内容，为空则采用 GET 方式，否则采用 POST 方式
     *  @var boolean   stream          如果需要保存文件，则设置为 true
     *  @var string    filename        保存的文件名和路径
     *  @var boolean   json_decode     获得结果是否需要 json 解码
     *  @var boolean   json_encode     传送内容 body 是否需要 json 编码
     * }
     */
    static function curl($url, $options = [])
    {
        // 设定参数默认选项内容数组
        $options = wp_parse_args($options, [
            'timeout'       => 5,
            'method'        => null,
            'body'          => null,
            'sslverify'     => false,
            'blocking'      => true,    // 如果不需要立刻知道结果，可以设置为 false
            'stream'        => false,   // 如果是保存远程的文件，这里需要设置为 true
            'filename'      => null,    // 设置保存下来文件的路径和名字
            'json_decode'   => true,
            'json_encode'   => false
        ]);

        /** 判断使用传输方法 */
        if (isset($options['method'])) {
            $method = strtoupper($options['method']);
        } else {
            $method = $options['body'] ? 'POST' : 'GET';
        }

        // 设定 JSON 转换编码解码确认
        $json_decode   = isset($options['filename']) ? false : $options['json_decode'];
        $json_encode   = $options['json_encode'];

        // 删除数组中关于传输方法和 JSON 解码编码内容
        // 以上选项值不在 wp_remote 相关函数参数内容中
        unset($options['json_decode']);
        unset($options['json_encode']);
        unset($options['method']);

        /** 判断选项值中 BODY 内容参数为数组，并且要求 JSON 编码 */
        if ($json_encode && is_array($options['body'])) {
            $options['headers']['content-type'] = 'application/json';
            $options['body']    = wwpo_json_encode($options['body']);
        }

        // 判断方法使用不同函数进行内容获取
        switch ($method) {
            case 'GET':
                $response = wp_remote_get($url, $options);
                break;
            case 'POST':
                $response = wp_remote_post($url, $options);
                break;
            case 'HEAD':
                $response = wp_remote_head($url, $options);
                break;
            default:
                $response = wp_remote_request($url, $options);
                break;
        }

        /** 判断返回错误信息 */
        if (is_wp_error($response)) {
            return $response;
        }

        // 设定返回的头部信息和主体内容
        $headers    = $response['headers'];
        $response   = $response['body'];

        /** 判断返回格式为 JSON 格式，或要求进行 JSON 编码 */
        if ($json_decode || (isset($headers['Content-type']) && strpos($headers['Content-type'], '/json'))) {
            $response = wwpo_json_decode($response);
        }

        if ($options['stream']) {
            return $headers;
        }

        // 返回远程获取内容
        return $response;
    }

    /**
     * UTF-8编码 GBK编码相互转换/（支持数组）
     *
     * @since 1.0.0
     * @param array $str   字符串，支持数组传递
     * @param string $in_charset 原字符串编码
     * @param string $out_charset 输出的字符串编码
     */
    static function iconv($str, $in_charset = 'GBK', $out_charset = 'UTF-8')
    {
        if (is_string($str)) {
            return mb_convert_encoding($str, $out_charset, $in_charset);
        }

        if (is_array($str)) {
            foreach ($str as $k => $v) {
                $str[$k] = self::iconv($v);
            }

            return $str;
        }

        return $str;
    }

    /**
     * 判断移动端用户的代理客户端
     *
     * @since 1.0.0
     * @param string $user_agent 需要判断的代理客户端，如果为空，则判断当前客户端是否是使用移动端
     * - ios        iPhone 或者 iPad
     * - iphone     iPhone 手机
     * - ipad       iPad 平板电脑
     * - android    安卓系统手机
     * - imac       iMac 电脑
     * - toutiao    头条系应用
     * - weixin     微信浏览器
     */
    function is_mobile($user_agent = null)
    {
        $is_mobile = false;

        if (empty($user_agent)) {
            return wp_is_mobile();
        }

        $http_user_agent = $_SERVER['HTTP_USER_AGENT'];

        switch ($user_agent) {

                // 判断 iOS 系统（iPhone 或者 iPad）
            case 'ios':
                if (
                    false !== strpos($http_user_agent, 'iPhone')
                    || false !== strpos($http_user_agent, 'iPad')
                ) {
                    $is_mobile = true;
                }
                break;

                // 判断 iPhone 手机
            case 'iphone':
                if (false !== strpos($http_user_agent, 'iPhone')) {
                    $is_mobile = true;
                }
                break;

                // 判断 iPad 平板电脑
            case 'ipad':
                if (false !== strpos($http_user_agent, 'iPad')) {
                    $is_mobile = true;
                }
                break;

                // 判断安卓系统手机
            case 'android':
                if (false !== strpos($http_user_agent, 'Android')) {
                    $is_mobile = true;
                }
                break;

                // 判断 iMac 电脑
            case 'imac':
                if (false !== strpos($http_user_agent, 'Macintosh')) {
                    $is_mobile = true;
                }
                break;

                // 判断头条系应用
            case 'toutiao':
                if (false !== strpos($http_user_agent, 'ToutiaoMicroApp')) {
                    $is_mobile = true;
                }
                break;

                // 判断微信浏览器
            case 'weixin':
                if (false !== strpos($http_user_agent, 'MicroMessenger')) {
                    $is_mobile = true;
                }
                break;
            default:

                // 直接写入代理客户端名称进行判断
                if (false !== strpos($http_user_agent, $user_agent)) {
                    $is_mobile = true;
                }
                break;
        }

        return $is_mobile;
    }

    /**
     * 判断参数是否属于指定类型，使用正则表达式
     *
     * @since 1.0.0
     * @param string    $type   判断类型
     * @param mixed     $value  需要判断的值
     * - phone  手机号码
     */
    function is_type($type, $value)
    {
        $wwpo_is = false;

        switch ($type) {
            case 'phone':
                $pattern = '/^0{0,1}(1[3,5,8][0-9]|14[5,7]|166|17[0,1,3,6,7,8]|19[8,9])[0-9]{8}$/';
                break;

            default:
                break;
        }

        if (empty($pattern)) {
            return false;
        }

        if (preg_match($pattern, $value)) {
            $wwpo_is = true;
        }

        return $wwpo_is;
    }

    /**
     * 判断参数是否属于指定类型，使用过滤器函数
     *
     * @since 1.0.0
     * @param string $type  需要判断的类型
     * - email  把值作为 e-mail 地址来验证
     * - url    把值作为 URL 来验证
     * - ip     把值作为 IP 地址来验证，只限 IPv4 或 IPv6 或 不是来自私有或者保留的范围
     * @param string $value 需要判断的值
     */
    static function is_filter($type, $value)
    {
        $wwpo_filter = false;

        switch ($type) {
            case 'email':
                $pattern = FILTER_VALIDATE_EMAIL;
                break;

            case 'url':
                $pattern = FILTER_VALIDATE_URL;
                break;

            case 'ip':
                $pattern = FILTER_VALIDATE_IP;
                break;

            default:
                break;
        }

        if (empty($pattern)) {
            return false;
        }

        if (filter_var($value, $pattern)) {
            $wwpo_filter = true;
        }

        return $wwpo_filter;
    }

    /**
     * 对 JSON 格式的字符串进行解码
     *
     * @since 1.0.0
     * @param string    $json       待解码的 json 格式的字符串。
     * @param boolean   $assoc      当该参数为 TRUE 时，将返回 array 而非 object
     * @param integer   $depth      指定递归深度
     * @param integer   $options    见 wwpo_json_encode 函数
     */
    static function json_decode($json, $assoc = true, $depth = 512, $options = 0)
    {
        if (empty($json)) {
            return;
        }

        $json = self::remove_strip_control_characters($json);

        return json_decode($json, $assoc, $depth, $options);
    }

    /**
     * 对变量进行 JSON 编码
     *
     * @since 1.0.0
     * @param mixed             $data       需要转换的数据
     * @param string|integer    $options    预定义常量，多个常量用|分隔，也可用数值代替。默认值：352
     * 如：352 = 256（JSON_UNESCAPED_UNICODE）+ 64（JSON_UNESCAPED_SLASHES）+ 32（JSON_NUMERIC_CHECK）
     *
     * - JSON_ERROR_NONE                    没有错误发生。数值：0
     * - JSON_ERROR_DEPTH                   到达了最大堆栈深度。数值：1
     * - JSON_ERROR_STATE_MISMATCH          出现了下溢（underflow）或者模式不匹配。数值：2
     * - JSON_ERROR_CTRL_CHAR               控制字符错误，可能是编码不对。数值：3
     * - JSON_ERROR_SYNTAX                  语法错误。数值：4
     * - JSON_ERROR_UTF8                    异常的 UTF-8 字符，也许是因为不正确的编码。数值：5
     * - JSON_ERROR_RECURSION               传递给 json_encode() 的对象或数组包含递归引用，不能进行编码。数值：6
     * - JSON_ERROR_INF_OR_NAN              传递给 json_encode() 的值包括 NAN 或 INF。数值：7
     * - JSON_ERROR_UNSUPPORTED_TYPE        json_encode() 获得了不支持类型的值。数值：8
     * - JSON_ERROR_INVALID_PROPERTY_NAME   在将JSON对象解码为PHP对象时，传递给 json_decode() 的字符串中有一个以 \u0000 字符开头的键。数值：9
     * - JSON_ERROR_UTF16                   传递给 json_encode() 的 JSON 字符串中包含的 unicode 转义中的单个未配对 UTF-16 代理。数值：10
     *
     * - JSON_BIGINT_AS_STRING              将大数字编码成原始字符原来的值。数值：2
     * - JSON_OBJECT_AS_ARRAY               将 JSON 对象解码为 PHP 数组。数值：1
     * - JSON_HEX_TAG                       所有的 < 和 > 转换成 \u003C 和 \u003E。数值：1
     * - JSON_HEX_AMP                       所有的 & 转换成 \u0026。数值：2
     * - JSON_HEX_APOS                      所有的 ' 转换成 \u0027。数值：4
     * - JSON_HEX_QUOT                      所有的 " 转换成 \u0022。数值：8
     * - JSON_FORCE_OBJECT                  使一个非关联数组输出一个类（Object）而非数组。 在数组为空而接受者需要一个类（Object）的时候尤其有用。数值：16
     * - JSON_NUMERIC_CHECK                 将所有数字字符串编码成数字（numbers）。数值：32
     * - JSON_PRETTY_PRINT                  用空白字符格式化返回的数据。数值：128
     * - JSON_UNESCAPED_SLASHES             不要编码 /。数值：64
     * - JSON_UNESCAPED_UNICODE             以字面编码多字节 Unicode 字符（默认是编码成 \uXXXX）。数值：256
     * - JSON_PARTIAL_OUTPUT_ON_ERROR       替换一些无法编码的值，而不是失败。数值：512
     * - JSON_PRESERVE_ZERO_FRACTION        确保浮点值始终被编码为浮点值。数值：1024
     * - JSON_UNESCAPED_LINE_TERMINATORS    提供 JSON_UNESCAPED_UNICODE 时，行终止符保持不转义。数值：2048
     * @param integer           $depth      设置最大深度，默认值：512
     */
    static function json_encode($data, $options = 320, $depth = 512)
    {
        if (empty($data)) {
            return;
        }

        return wp_json_encode($data, $options, $depth);
    }

    /**
     * 将数组内容输出为 JSON 格式信息
     *
     * @since 1.0.0
     * @param array $response 需要输出的数组内容
     */
    static function json_send($response = [])
    {
        $result = self::json_encode($response);
        @header('Content-Type: application/json; charset=' . get_option('blog_charset'));
        return $result;
    }

    /**
     * 移除控制字符
     * 如果字符串中出现了控制字符，json_decode 和 simplexml_load_string 这些函数就会失败
     *
     * @since 1.0.0
     * @param string $text 文本内容
     */
    static function remove_strip_control_characters($text)
    {
        return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F]/u', '', $text);
    }

    /**
     * 生成随机数
     *
     * @since 1.0.0
     * @param integer   $length 生成位数，默认值：10
     * @param string    $prefix 随机数前缀字符，默认值：空
     * @param string    $words  生成随机数的英文样式，默认值：upper
     *  - upper     全体大写
     *  - lower     全体小写
     *  - first     首字符大写
     * @param boolean   $special    是否启用特殊字符，默认值：false
     */
    static function random($length = 10, $prefix = '', $words = '', $special = false)
    {
        // 设定默认随机字符串
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        /** 判断启用特殊字符 */
        if ($special) {
            $characters .= '!@#$%^&*-~?+|()<>._[]';
        }

        // 打乱字符串内容
        str_shuffle($characters);

        // 添加随机数前缀和截取指定位数随机数
        $random = substr($prefix . str_shuffle($characters), 0, $length);

        // 返回指定大小写样式的字符串
        switch ($words) {
            case 'lower':
                return strtolower($random);
            case 'first':
                return ucfirst($random);
            case 'upper':
                return strtoupper($random);
            default:
                return $random;
        }
    }

    /**
     * 生成唯一标识符
     *
     * @since 1.0.0
     * @param string    $prefix 前缀标识，默认值：1
     * @param integer   $num    生成标识位，数默认值：9
     */
    static function unique($prefix = 1, $num = 9)
    {
        $unique_id = $prefix;
        $unique_id .= rand();
        $unique_id .= substr(time(), 0, 2);
        $unique_id .= substr(strrev(microtime()), 0, 2);
        $unique_id .= substr(mt_rand(), 0, 2);
        $unique_id .= substr(rand(), 0, 2);
        return substr($unique_id, 0, $num);
    }

    /**
     * 解压缩 ZIP 到指定目录
     *
     * @since 1.0.0
     * @param string $zip_name  压缩包路径和名称
     * @param string $dir       解压路径
     */
    function unzip($zip_name, $dir)
    {
        //检测要解压压缩包是否存在
        if (!file_exists($zip_name)) {
            return false;
        }

        //检测目标路径是否存在
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }

        $zip = new ZipArchive();

        if ($zip->open($zip_name)) {
            $zip->extractTo($dir);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }
}
