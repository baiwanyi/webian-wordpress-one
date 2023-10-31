<?php

/**
 * 其他常用系统函数
 *
 * @package Webian WordPress One
 */

/**
 * 黑名单检测函数
 *
 * @since 1.0.0
 * @param string    $name           需要检测的名字
 * @param array     $blacklist      黑名单列表数组
 */
function wwpo_check_blacklist($name, $blacklist)
{
    /**
     * 遍历 blacklist 循环检测是否包含
     *
     * @property string $word 黑名单字符串
     */
    foreach ((array) $blacklist as $word) {

        // 去掉关键字两端空格
        $word = trim($word);

        // 判断关键字为空
        if (empty($word)) continue;

        // 转义关键字中的 # 字符，防止正则出错
        $word = preg_quote($word, '#');

        // 正则匹配到关键字
        if (preg_match("#$word#i", $name)) return true;
    }

    // 返回信息
    return false;
}

/**
 * 检测 WordPress 黑名单函数
 *
 * @since 1.0.0
 * @param string $name 需要检测的名字
 */
function wwpo_check_wp_blacklist($name)
{
    // 获取系统屏蔽关键字和黑名单关键字
    $moderation_keys    = trim(get_option('moderation_keys'));
    $blacklist_keys     = trim(get_option('blacklist_keys'));

    // 转换成为关键字数组
    $blacklist = explode("\n", $moderation_keys . "\n" . $blacklist_keys);

    // 返回检测结果
    return wwpo_check_blacklist($name, $blacklist);
}

/**
 * 获取 iOS 操作系统信息函数
 *
 * @since 1.0.0
 * @param string $user_agent 需要获取的信息标签
 * - version    系统版本
 * - build      浏览器版本
 */
function wwpo_get_ios($user_agent)
{
    switch ($user_agent) {
        case 'version':
            $pattern = '/OS (.*?) like Mac OS X[\)]{1}/i';
            break;
        case 'build':
            $pattern = '/Mobile\/(.*?)\s/i';
            break;
        default:
            break;
    }

    if (empty($pattern)) {
        return;
    }

    if (preg_match($pattern, $_SERVER['HTTP_USER_AGENT'], $matches)) {
        return trim($matches[1]);
    } else {
        return;
    }
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
function wwpo_is_mobile($user_agent = null)
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
 * 判断参数是否属于指定类型
 *
 * @since 1.0.0
 * @param string    $type   判断类型
 * @param mixed     $value  需要判断的值
 * - phone  手机号码
 */
function wwpo_is_type($type, $value)
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
 * Undocumented function
 *
 * @since 1.0.0
 * @param string $type  需要判断的类型
 * - email  把值作为 e-mail 地址来验证
 * - url    把值作为 URL 来验证
 * - ip     把值作为 IP 地址来验证，只限 IPv4 或 IPv6 或 不是来自私有或者保留的范围
 * @param string $value 需要判断的值
 */
function wwpo_filter($type, $value)
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
 * 验证上传文件
 *
 * @since 1.0.0
 * @param array     $uploader
 * {
 *  上传内容数组
 *  @var integer  error  和该文件上传相关的错误代码。
 *      - 0 UPLOAD_ERR_OK           文件上传成功
 *      - 1 UPLOAD_ERR_INI_SIZE     超过 php.ini 设置的文件大小
 *      - 2 UPLOAD_ERR_FORM_SIZE    超过 form 表单文件大小
 *      - 3 UPLOAD_ERR_PARTIAL      文件只有部分被上传
 *      - 4 UPLOAD_ERR_NO_FILE      没有文件被上传
 *      - 6 UPLOAD_ERR_NO_TMP_DIR   找不到临时文件夹
 *      - 7 UPLOAD_ERR_CANT_WRITE   文件写入失败
 *  @var string name            文件名
 *  @var string type            文件的 MIME 类型，需要浏览器提供该信息的支持，如：image/jpge
 *  @var integer    size        已上传文件的大小，单位为字节
 *  @var string     tmp_name    文件被上传后在服务端储存的临时文件名，一般是系统默认。
 * }
 * @param string    $nonce      上传验证随机数
 * @param string    $ext        文件扩展名
 */
function wwpo_check_upload($uploader, $nonce, $ext = null)
{
    /** 判断是否上传成功 */
    if (empty($uploader) || 0 < $uploader['error']) {
        return ['error' => '没有上传任何文件'];
    }

    /** 验证上传权限 */
    if (!current_user_can('upload_files')) {
        return ['error' => '没有上传权限'];
    }

    /** 验证上传随机验证码 */
    if (!wp_verify_nonce($nonce, '_wwpouploader')) {
        return ['error' => '非法操作'];
    }

    /** 判断不验证文件扩展名 */
    if (empty($ext)) {
        return true;
    }

    // 获取上传文件的扩展名
    $uploader_ext = strtolower(pathinfo($uploader['name'], PATHINFO_EXTENSION));

    /** 需要判断的扩展名是否为数组（多个）内容 */
    if (is_array($ext)) {

        // 数组包含判断
        if (!in_array($uploader_ext, $ext)) {
            return ['error' => sprintf('文件类型不为 <strong>%s</strong> 格式', strtoupper(implode(',', $ext)))];
        }
    } else {

        // 单一文件扩展名判断
        if ($ext != $uploader_ext) {
            return ['error' => sprintf('文件类型不为 <strong>%s</strong> 格式', strtoupper($ext))];
        }
    }

    return true;
}

/**
 * 日志写入函数
 *
 * @since 1.0.0
 * @param string $code      日志事件代码
 * @param string $page_url  日志页面地址
 */
function wwpo_logs($code, $page_url = null)
{
    if (empty($code)) {
        return;
    }

    if (isset($_POST['pagenow'])) {
        $page_url = urldecode($_POST['pagenow']);
        $page_url = str_replace(['-', '_'], ':', $page_url);
    }

    if (empty($page_url)) {
        $page_url = $_SERVER['PHP_SELF'];

        if ($_SERVER['QUERY_STRING']) {
            $page_url .= '?' . $_SERVER['QUERY_STRING'];
        }
    }

    wwpo_insert_post(WWPO_SQL_LOGS, [
        'user_post'     => get_current_user_id(),
        'event_code'    => $code,
        'event_page'    => $page_url,
        'user_agent'    => wwpo_get_agent(),
        'user_ip'       => wwpo_get_ip(),
        'time_post'     => NOW_TIME,
    ]);
}
