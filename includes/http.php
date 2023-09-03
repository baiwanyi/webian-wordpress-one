<?php

/**
 * 核心 HTTP 请求函数
 * 对 HTTP 的请求进行标准化处理，以及处理与 URL 相关的内容
 *
 * @package Webian WordPress One
 */

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
function wwpo_json_encode($data, $options = 320, $depth = 512)
{
    if (empty($data)) {
        return;
    }

    return wp_json_encode($data, $options, $depth);
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
function wwpo_json_decode($json, $assoc = true, $depth = 512, $options = 0)
{
    if (empty($json)) {
        return;
    }

    $json = wwpo_remove_strip_control_characters($json);

    return json_decode($json, $assoc, $depth, $options);
}

/**
 * 将数组内容输出为 JSON 格式信息
 *
 * @since 1.0.0
 * @param array $response 需要输出的数组内容
 */
function wwpo_json_send($response = [])
{
    $result = wwpo_json_encode($response);
    @header('Content-Type: application/json; charset=' . get_option('blog_charset'));
    return $result;
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
function wwpo_curl($url, $options = [])
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
 * 获取远程图片函数
 *
 * @since 1.0.0
 * @param string  $image_url 图片地址
 */
function wwpo_get_remote_image($image_url, $subdir = null, $ext = null)
{
    // 设定保存到本地的图片文件名和文件夹
    $uploads    = wp_upload_dir();
    $subdir     = $subdir ?? $uploads['subdir'];

    $attachment_filename    = wwpo_filename($image_url, $ext);
    $attachment_basedir     = $uploads['basedir'] . $subdir;

    /** 判断文件名没有扩展名的情况下，将 jpg 作为文件的扩展名 */
    if (empty(pathinfo($image_url, PATHINFO_EXTENSION))) {
        $attachment_filename .= '.jpg';
    }

    /** 判断保存文件夹为空则新建文件夹 */
    if (!file_exists($attachment_basedir)) {
        wp_mkdir_p($attachment_basedir);
    }

    // 获取远程图片文件信息
    $headers = wwpo_curl($image_url, [
        'timeout'   => 60,
        'stream'    => true,
        'filename'  => $attachment_basedir . DIRECTORY_SEPARATOR . $attachment_filename
    ]);

    if (empty($headers)) {
        return;
    }

    return ltrim($subdir, '/')  . '/' . $attachment_filename;
}

/**
 * 获取当前页面地址函数
 *
 * @since 1.0.0
 * @return string
 */
function wwpo_get_page_url()
{
    return set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}

/**
 * 获取当前 IP 地址函数
 *
 * @since 1.0.0
 */
function wwpo_get_ip()
{
    return $_SERVER['REMOTE_ADDR'] ?? '';
}

/**
 * 用户浏览器信息
 *
 * @since 1.0.0
 */
function wwpo_get_agent()
{
    return $_SERVER['HTTP_USER_AGENT'] ?? '';
}

/**
 * 获取 url 地址中的参数，返回数组格式
 *
 * @since 1.0.0
 * @param string $url 需要解析的 url 地址
 */
function wwpo_get_url_query($url)
{
    $query = parse_url($url, PHP_URL_QUERY);

    if (empty($query)) {
        return;
    }

    parse_str($query, $array_query);

    if (empty($array_query)) {
        return;
    }

    return $array_query;
}
