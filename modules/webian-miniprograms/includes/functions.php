<?php

/**
 * 微信小程序应用函数
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat/wxapps
 */

/**
 * Undocumented function
 *
 * @param [type] $slug
 * @return void
 */
function wwpo_miniprograms_get_platform($slug = '')
{
    $platform = ['wxapps' => '微信', 'douyin' => '抖音'];

    if (empty($slug)) {
        return $platform;
    }

    return $platform[$slug] ?? '';
}

/**
 * Undocumented function
 *
 * @param string $appid
 * @return void
 */
function wwpo_miniprograms_get_title($appid = '')
{
    $option_data    = get_option(WWPO_KEY_MINIPROGRAMS, []);
    $option_data    = $option_data['apps'] ?? [];
    $title = [];

    if (empty($option_data)) {
        return;
    }

    foreach ($option_data as $option) {
        $title[$option['appid']] = sprintf('%s - %s', $option['title'], wwpo_miniprograms_get_platform($option['platform']));
    }

    if (empty($appid)) {
        return $title;
    }

    return $title[$appid] ?? '';
}

/**
 * 广告展示显示函数
 *
 * @since 1.0.0
 * @param string    $adsense    广告位置
 * @param integer   $limit      显示数量，默认：3
 */
function wwpo_wxapps_get_banner($appid, $limit = 3)
{
    // 初始化内容
    $data = [];

    // 获取广告展示内容
    $option_data = get_option(WWPO_KEY_MINIPROGRAMS, []);
    $option_data = $option_data['banner'] ?? [];

    if (empty($option_data)) {
        return;
    }

    // 按照 menu_order 排序，并显示指定数量
    $option_data = wp_list_sort($option_data, 'menu_order');
    $option_data = array_slice($option_data, 0, $limit);

    /**
     * 遍历广告展示内容数组
     *
     * @property string $banner
     * {
     *  广告展示内容
     *  @var string     start       起始时间
     *  @var string     end         结束时间
     *  @var string     adsense     广告位置
     *  @var string     title       广告标题
     *  @var string     content     广告内容
     *  @var string     guid        广告链接
     *  @var string     color       颜色
     *  @var integer    thumb       广告封面 ID
     * }
     */
    foreach ($option_data as $i => $banner) {
        $day_start  = str_replace('-', '', $banner['start']);
        $day_end    = str_replace('-', '', $banner['end']);
        $day_now    = date('Ymd', NOW);
        $day_limit  = $day_end - $day_start;

        if ($appid != $banner['appid']) {
            continue;
        }

        if (($day_start > $day_now || $day_end < $day_now) && 0 > $day_limit) {
            continue;
        }

        $data[$i] = [
            'title'     => $banner['title'],
            'content'   => $banner['content'],
            'guid'      => $banner['guid'],
            'color'     => $banner['color']
        ];

        if (isset($banner['thumb'])) {
            $data[$i]['thumb'] = wp_get_attachment_image_url($banner['thumb'], 'banner');
        }
    }

    return $data;
}

/**
 * 获取首页分类内容函数
 *
 * @since 1.0.0
 */
function wwpo_wxapps_get_home_category()
{
    // 初始化内容
    $data = [];

    // 获取首页分类内容
    $option_data = get_option(WWPO_KEY_MINIPROGRAMS, []);
    $option_data = $option_data['category'] ?? [];

    if (empty($option_data)) {
        return;
    }

    // 按照 menu_order 排序
    $option_data = wp_list_sort($option_data, 'menu_order');

    /**
     * 遍历首页分类内容数组
     *
     * @property string $category
     * {
     *  首页分类内容
     *  @var string     group   分组
     *  @var string     title   分类标题
     *  @var string     guid    链接地址
     *  @var integer    thumb   封面 ID
     * }
     */
    foreach ($option_data as $category) {
        $data[$category['group']][] = [
            'title' => $category['title'],
            'guid'  => $category['guid'],
            'thumb' => wp_get_attachment_image_url($category['thumb'], '')
        ];
    }

    return $data;
}
