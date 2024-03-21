<?php

/**
 * 检测应用类
 *
 * @since 2.0.0
 * @package Webian WordPress One
 */
class WWPO_Check
{
    /**
     * 检测关键字函数
     *
     * @since 1.0.0
     * @param string    $name           需要检测的名字
     * @param array     $blacklist      黑名单列表数组
     */
    static function keyword($name, $blacklist)
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
     * 检测黑名单函数
     *
     * @since 1.0.0
     * @param string $name 需要检测的名字
     */
    static function blacklist($name)
    {
        // 获取系统屏蔽关键字和黑名单关键字
        $moderation_keys    = trim(get_option('moderation_keys'));
        $blacklist_keys     = trim(get_option('blacklist_keys'));

        // 转换成为关键字数组
        $blacklist = explode("\n", $moderation_keys . "\n" . $blacklist_keys);

        // 返回检测结果
        return self::keyword($name, $blacklist);
    }
}
