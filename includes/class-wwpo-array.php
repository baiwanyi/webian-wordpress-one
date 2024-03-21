<?php

/**
 * 数组应用类
 *
 * @since 2.0.0
 * @package Webian WordPress One
 */
class WWPO_Array
{
    /**
     * 去除数组中的空格
     *
     * @since 1.0.0
     * @param array $array 需要去除的数组内容
     */
    static function trim($array)
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
    static function merge_ids($array)
    {
        $result = array_reduce($array, function ($result, $value) {
            return array_merge($result, explode(',', $value));
        }, []);

        return wp_parse_id_list($result);
    }

    /**
     * 二维数组转换成一维数组
     *
     * @since 1.0.0
     * @param array     $array  需要合并的数组
     * @param boolean   $key    是否保留键名，默认：true
     */
    static function merge($array, $key = true)
    {
        if ($key) {
            return array_reduce($array, function ($result, $value) {
                return array_merge($result, $value);
            }, []);
        }

        return array_reduce($array, function ($result, $value) {
            return array_merge($result, array_values($value));
        }, []);
    }

    /**
     * 显示指定键名的值
     *
     * @since 1.0.0
     * @param string    $key            需要显示的值
     * @param array     $array          需要显示的数组
     * @param string    $default_value  内容不存在的默认值
     */
    static function value($key, $array, $default_value = '')
    {
        if (empty($key) || empty($array)) {
            return;
        }

        if (!is_array($array)) {
            return;
        }

        return $array[$key] ?? $default_value;
    }
}
