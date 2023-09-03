<?php

/**
 * 文本操作函数
 *
 * @package Webian WordPress One
 */

/**
 * 获取纯文本
 *
 * @since 1.0.0
 * @param string $text 文本内容
 */
function wwpo_get_plain($text)
{
    $text = wp_strip_all_tags($text);
    $text = str_replace('"', '', $text);
    $text = str_replace('\'', '', $text);
    $text = str_replace("\r\n", ' ', $text);
    $text = str_replace("\n", ' ', $text);
    $text = str_replace("  ", ' ', $text);
    return trim($text);
}

/**
 * 获取第一段内容
 *
 * @since 1.0.0
 * @param string $text 文本内容
 */
function wwpo_get_first_section($text)
{
    if (!empty($text)) {
        $text = explode("\n", trim(strip_tags($text)));
        $text = trim($text['0']);
    }
    return $text;
}

/**
 * 移除控制字符
 * 如果字符串中出现了控制字符，json_decode 和 simplexml_load_string 这些函数就会失败
 *
 * @since 1.0.0
 * @param string $text 文本内容
 */
function wwpo_remove_strip_control_characters($text)
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
function wwpo_random($length = 10, $prefix = '', $words = '', $special = false)
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
function wwpo_unique($prefix = 1, $num = 9)
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
 * UTF-8编码 GBK编码相互转换/（支持数组）
 *
 * @since 1.0.0
 * @param array $str   字符串，支持数组传递
 * @param string $in_charset 原字符串编码
 * @param string $out_charset 输出的字符串编码
 */
function wwpo_iconv($str, $in_charset = 'GBK', $out_charset = 'UTF-8')
{
    if (is_array($str)) {
        foreach ($str as $k => $v) {
            $str[$k] = wwpo_iconv($v);
        }
        return $str;
    }
    else {
        if (is_string($str)) {
            // return iconv('UTF-8', 'GBK//IGNORE', $str);
            return mb_convert_encoding($str, $out_charset, $in_charset);
        }
        else {
            return $str;
        }
    }
}

/**
 * Base64 加密字符串
 *
 * @since 1.0.0
 * @param string    $str        要加密的数据
 * @param string    $aes_key    前后端共同约定的秘钥
 */
function wwpo_base64_encode($str, $aes_key = null)
{
    if (isset($aes_key)) {
        $data = openssl_encrypt($str, 'AES-128-ECB', $aes_key, OPENSSL_RAW_DATA);
    }

    $data = base64_encode($data);

    return $data;
}

/**
 * Base64 解密字符串
 *
 * @since 1.0.0
 * @param string    $str        要解密的数据
 * @param string    $aes_key    前后端共同约定的秘钥
 */
function wwpo_base64_decode($str, $aes_key = null)
{
    $data = base64_decode($str);

    if (isset($aes_key)) {
        $data = openssl_decrypt($data, 'AES-128-ECB', $aes_key, OPENSSL_RAW_DATA);
    }

    return $data;
}
