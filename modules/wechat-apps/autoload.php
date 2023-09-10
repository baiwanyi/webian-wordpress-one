<?php

/*
 * Modules Name: 微信小程序模块
 * Description: 微信小程序模块功能。
 * Version: 1.0.0
 * Author: Baiwanyi
 * Updated: 2023-09-01
 */

/**
 * 引用文件
 *
 * @since 1.0.0
 */
wwpo_require_dir(WWPO_MOD_PATH . 'wechat-apps/includes');

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_wxapps_admin_menu($menus)
{
    $menus['wxapps'] = [
        'menu_title'    => __('小程序', 'wwpo'),
        'parent'        => 'webian-wordpress-one',
        'menu_order'    => 7,
        'sidebar'       => [
            'home'      => ['title' => '首页', 'icon' => 'admin-home'],
            'banner'    => ['title' => '广告展示', 'icon' => 'cover-image'],
            'category'  => ['title' => '首页分类', 'icon' => 'screenoptions'],
            'setting'   => ['title' => '基本配置', 'icon' => 'admin-generic']
        ]
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_wxapps_admin_menu');

/**
 * 微信帐号设置界面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_wxapps($page_action)
{
    $page_tabs = WWPO_Admin::tabs('home');

    if (empty($page_action)) {
        call_user_func("wwpo_wxapps_display_{$page_tabs}");
        return;
    }

    call_user_func("wwpo_wxapps_display_{$page_tabs}_{$page_action}");
}
add_action('wwpo_admin_display_wxapps', 'wwpo_admin_display_wxapps');

/**
 * 自定义内容更新操作函数
 *
 * @since 1.0.0
 */
function wwpo_admin_post_update_wechat()
{
    $option_data    = get_option(WWPO_Weapp::KEY_OPTION);
    $post_key       = $_POST['post_key'] ?? '';
    $post_id        = $_POST['post_id'] ?? 0;
    $url_query      = ['tab' => $post_key];

    if (empty(($post_key))) {
        new WWPO_Error('message', 'not_found_key', $url_query);
        return;
    }

    if ('setting' == $post_key) {
        $option_data['setting'] = $_POST['updated'];
    } else {

        if (empty($post_id)) {
            new WWPO_Error('message', 'not_found_id', $url_query);
            return;
        }

        $url_query['post']      = $post_id;
        $url_query['action']    = 'edit';

        $option_data[$post_key][$post_id]           = $_POST['updated'];
        $option_data[$post_key][$post_id]['id']     = $post_id;
        $option_data[$post_key][$post_id]['thumb']  = $_POST['thumb_id'];
    }

    update_option(WWPO_Weapp::KEY_OPTION, $option_data);

    new WWPO_Error('message', "{$post_key}_success_updated", $url_query);
}
add_action('wwpo_post_admin_updatewxapps', 'wwpo_admin_post_update_wechat');

/**
 * Undocumented function
 *
 * @param [type] $which
 * @return void
 */
function wwpo_wxapps_extra_tablenav($which)
{
    if (!in_array(WWPO_Admin::tabs(), ['banner', 'category'])) {
        return;
    }

    if ('top' != $which) {
        return;
    }

    printf(
        '<a href="%s" class="button">%s</a>',
        add_query_arg(['post' => 'new', 'action' => 'edit']),
        __('Add')
    );
}
add_action('wwpo_wxapps_extra_tablenav', 'wwpo_wxapps_extra_tablenav');


/**
 * 自定义内容表格列输出函数
 *
 * @since 1.0.0
 * @param array     $data           表格列值
 * @param string    $column_name    表格列名称
 */
function wwpo_wxapps_banner_table_column($data, $column_name)
{
    switch ($column_name) {
        case 'title-apps':
            $page_url = WWPO_Admin::add_query(['tab' => $_GET['tab']]);
            echo WWPO_Admin::title($data['id'], $data['title'], 'edit', $page_url);
            break;

        case 'adsense-apps':
            echo wwpo_wxapps_banner_adsense($data['adsense']);
            break;

        case 'thumb-apps':
            if (empty($data['thumb'])) {
                return;
            }

            printf(
                '<div class="ratio ratio-1x1"><img src="%s" class="thumb rounded"></div>',
                wp_get_attachment_image_url($data['thumb'])
            );
            break;

        case 'endtime':

            $day_start  = str_replace('-', '', $data['start']);
            $day_end    = str_replace('-', '', $data['end']);
            $day_now    = date('Ymd', NOW);
            $day_limit  = $day_end - $day_start;

            if ($day_end < $day_now) {
                echo '已过期';
                return;
            }

            if (3 >= $day_limit) {
                printf('<span class="text-danger">%s</span>', $day_limit);
                return;
            }

            printf('<span class="text-success">%s</span>', $day_limit);
            break;

        default:
            break;
    }
}
add_action('wwpo_table_wxapps_custom_column', 'wwpo_wxapps_banner_table_column', 10, 2);
