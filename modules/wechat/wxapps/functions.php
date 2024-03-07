<?php

/**
 * 微信小程序应用函数
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat/wxapps
 */

 /**
 * 小程序后台页面表格添加按钮函数
 *
 * @since 1.0.0
 * @param string $which
 */
function wwpo_wxapps_extra_tablenav($which)
{
    $page_tabs = WWPO_Admin::tabs();

    if (!in_array($page_tabs, ['banner', 'category'])) {
        return;
    }

    if ('top' != $which) {
        return;
    }

    printf(
        '<a href="%s" class="button">%s</a>',
        WWPO_Admin::add_query(['tab' => $page_tabs, 'post' => 'new', 'action' => 'edit']),
        __('Add')
    );
}
add_action('wwpo_wxapps_extra_tablenav', 'wwpo_wxapps_extra_tablenav');

/**
 * 设置页面消息函数
 *
 * @since 1.0.0
 * @param array $message
 */
function wwpo_admin_message_wxapps($message)
{
    $message['wxapps'] = [
        'not_found_key'             => ['error'     => __('没要找到保存的字段。', 'wwpo')],
        'not_found_data'            => ['error'     => __('没要找到保存的数据内容。', 'wwpo')],
        'not_found_id'              => ['error'     => __('没要找到保存的编号。', 'wwpo')],
        'setting_success_updated'   => ['updated'   => __('小程序设置内容已更新', 'wwpo')],
        'banner_success_updated'    => ['updated'   => __('通栏广告内容更新成功', 'wwpo')],
        'banner_success_delete'     => ['updated'   => __('通栏广告内容删除成功', 'wwpo')],
        'category_success_updated'  => ['updated'   => __('首页分类内容更新成功', 'wwpo')],
        'category_success_delete'   => ['updated'   => __('首页分类内容删除成功', 'wwpo')],
    ];

    return $message;
}
add_filter('wwpo_admin_message', 'wwpo_admin_message_wxapps');

/**
 * 广告展示显示函数
 *
 * @since 1.0.0
 * @param string    $adsense    广告位置
 * @param integer   $limit      显示数量，默认：3
 */
function wwpo_wxapps_get_banner($adsense, $limit = 3)
{
    // 初始化内容
    $data = [];

    // 获取广告展示内容
    $option_data = get_option(WECHAT_KEY_OPTION, []);
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

        if ($adsense != $banner['adsense']) {
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
 * 获取页面内容数组
 *
 * @since 1.0.0
 */
function wwpo_wxapps_get_pages()
{
    // 初始化内容
    $data   = [];
    $pages  = get_pages(['meta_key' => WWPO_META_PAGE_WEAPP_KEY]);

    if (empty($pages)) {
        return;
    }

    foreach ($pages as $page) {
        $data[] = [
            'id'    => $page->ID,
            'title' => $page->post_title,
            'name'  => $page->post_name
        ];
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
    $option_data = get_option(WECHAT_KEY_OPTION);
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
