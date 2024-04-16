<?php

class WWPO_Url
{
    /**
     * 获取当前页面地址函数
     *
     * @since 1.0.0
     * @return string
     */
    static function current()
    {
        return set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }

    /**
     * 获取 url 地址中的参数，返回数组格式
     *
     * @since 1.0.0
     * @param string $url 需要解析的 url 地址
     */
    static function query($url)
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
}
