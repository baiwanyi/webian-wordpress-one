<?php

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
 * Undocumented function
 *
 * @param [type] $adsense
 * @param integer $limit
 * @return void
 */
function wwpo_wxapps_get_banner($adsense, $limit = 3)
{
    // 初始化内容
    $data = [];

    $option_data = get_option(WWPO_Wxapps::KEY_OPTION);
    $option_data = $option_data['banner'] ?? [];

    if (empty($option_data)) {
        return;
    }

    $option_data = wp_list_sort($option_data, 'menu_order');
    $option_data = array_slice($option_data, 0, $limit);

    foreach ($option_data as $banner) {
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

        $data[] = [
            'title'     => $banner['title'],
            'content'   => $banner['content'],
            'guid'      => $banner['guid'],
            'color'     => $banner['color'],
            'thumb'     => wp_get_attachment_image_url($banner['thumb'], 'banner')
        ];
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
 * Undocumented function
 *
 * @param integer $limit
 * @return void
 */
function wwpo_wxapps_get_home_category()
{
    // 初始化内容
    $data = [];

    $option_data = get_option(WWPO_Wxapps::KEY_OPTION);
    $option_data = $option_data['category'] ?? [];

    if (empty($option_data)) {
        return;
    }

    $option_data = wp_list_sort($option_data, 'menu_order');

    foreach ($option_data as $category) {
        $data[$category['group']][] = [
            'title' => $category['title'],
            'guid'  => $category['guid'],
            'thumb' => wp_get_attachment_image_url($category['thumb'], '')
        ];
    }

    return $data;
}
