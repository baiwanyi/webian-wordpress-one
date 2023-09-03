<?php

/**
 * 常用格式化函数
 * 输出处理格式化内容
 *
 * @package Webian WordPress One
 */

/**
 * 格式化时间
 *
 * @since 1.0.0
 * @param string|integer    $timestamp  需要格式化的时间（或时间戳）
 * @param string            $format     时间格式，默认：1900-01-01
 */
function wwpo_human_time($timestamp, $format = 'Y-m-d')
{
    if (is_string($timestamp)) {
        $timestamp = strtotime($timestamp);
    }

    $time_diff = NOW - $timestamp;

    if ($timestamp && $time_diff > 0 && $time_diff < DAY_IN_SECONDS) {
        return sprintf(__('%s 前', 'wwpo'), human_time_diff($timestamp, NOW));
    } else {
        return date($format, $timestamp);
    }
}

/**
 * 格式化显示时间
 *
 * @since 1.0.0
 * @param string|integer    $timestamp  需要格式化的时间（或时间戳）
 * @param string            $format     时间格式，默认：1900年01月01日 00:00:00
 */
function wwpo_full_date($timestamp, $format = 'Y年m月d日 H:i:s')
{
    if (is_string($timestamp)) {
        $timestamp = strtotime($timestamp);
    }

    return date($format, $timestamp);
}

/**
 * 去除数组中的空格
 *
 * @since 1.0.0
 * @param array $array 需要去除的数组内容
 */
function wwpo_array_trim($array)
{
    if (is_array($array)) {
        return array_map('trim', $array);
    }

    return trim($array);
}

/**
 * 二维数组内部 IDs 合并
 *
 * @since 1.0.0
 * @param array $array 需要合并的数组
 */
function wwpo_array_merge_ids($array)
{
    $result = array_reduce($array, function ($result, $value) {
        return array_merge($result, explode(',', $value));
    }, []);

    return wp_parse_id_list($result);
}

/**
 * 判断显示数组 key 值
 * 为空显示数组所有内容
 *
 * @since 1.0.0
 * @param string    $key    需要显示的值
 * @param array     $array  需要显示的数组
 */
function wwpo_array_values($key, $array)
{
    if (empty($key)) {
        return $array;
    } else {
        return $array[$key] ?? '';
    }
}
