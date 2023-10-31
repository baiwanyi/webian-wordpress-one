<?php

/*
 * Modules Name: 微信模块
 * Description: 包括微信公众号、微信小程序、微信支付模块功能。
 * Version: 1.0.0
 * Author: Baiwanyi
 * Updated: 2023-10-01
 */

/**
 * 自定义常量
 *
 * @package Webian WordPress One
 */
define('WECHAT_DOMAIN', 'https://api.weixin.qq.com');
define('WECHAT_KEY_OPTION', 'wwpo:data:wechat');
define('WECHAT_MP_USERMETA', 'wwpo:wechat:usermeta');
define('WECHAT_MP_OPENID', 'wwpo:wechat:openid');
define('WECHAT_APP_USERMETA', 'wwpo:wxapps:usermeta');
define('WECHAT_APP_OPENID', 'wwpo:wxapps:openid');

/**
 * 引用文件
 *
 * @since 1.0.0
 */
wwpo_require_dir(WWPO_MOD_PATH . 'wechat/wxapps');
wwpo_require_dir(WWPO_MOD_PATH . 'wechat/offiaccount');
wwpo_require_dir(WWPO_MOD_PATH . 'wechat/wxpay');

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_wechat_admin_menu($menus)
{
    $menus['wechat'] = [
        'menu_title'    => __('公众号', 'wwpo'),
        'parent'        => 'webian-wordpress-one',
        'menu_order'    => 7,
        'sidebar'       => [
            'home'          => ['title' => '首页', 'icon' => 'admin-home'],
            'library'       => ['title' => '素材库', 'icon' => 'cover-image'],
            'custom-menu'   => ['title' => '自定义菜单', 'icon' => 'menu-alt3'],
            'reply'         => ['title' => '关注回复', 'icon' => 'format-status'],
            'analysis'      => ['title' => '数据分析', 'icon' => 'chart-area'],
            'user'          => ['title' => '用户管理', 'icon' => 'businessman'],
            'message'       => ['title' => '消息管理', 'icon' => 'format-chat'],
            'article'       => ['title' => '图文素材', 'icon' => 'format-aside'],
            'tmplmsg'       => ['title' => '模版消息', 'icon' => 'admin-comments'],
            'setting'       => ['title' => '基本配置', 'icon' => 'admin-generic']
        ]
    ];

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

    $menus['wxpay'] = [
        'menu_title'    => __('微信支付', 'wwpo'),
        'parent'        => 'webian-wordpress-one',
        'menu_order'    => 7,
        'sidebar'       => [
            'home'      => ['title' => '首页', 'icon' => 'admin-home'],
            'setting'   => ['title' => '基本配置', 'icon' => 'admin-generic']
        ]
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_wechat_admin_menu');

/**
 * 微信帐号设置界面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_wechat($page_name)
{
    $page_action    = WWPO_Admin::action();
    $page_tabs      = WWPO_Admin::tabs('home');
    $option_data    = get_option(WECHAT_KEY_OPTION, []);

    if (!in_array($page_name, ['wxapps', 'wechat', 'wxpay'])) {
        return;
    }

    if (empty($page_action)) {
        call_user_func("wwpo_{$page_name}_display_{$page_tabs}", $option_data);
        return;
    }

    call_user_func("wwpo_{$page_name}_display_{$page_tabs}_{$page_action}", $option_data);
}
add_action('wwpo_admin_display', 'wwpo_admin_display_wechat');

/**
 * 设置页面消息函数
 *
 * @since 1.0.0
 * @param array $message
 */
function wwpo_admin_message_wechat($message)
{
    $message['wechat'] = [
        'wechat_updated'    => ['updated' => __('公众号设置内容已更新', 'wwpo')],
        'wepay_updated'     => ['updated' => __('微信支付设置内容已更新', 'wwpo')],
        'weapp_updated'     => ['updated' => __('小程序设置内容已更新', 'wwpo')]
    ];

    return $message;
}
add_filter('wwpo_admin_message', 'wwpo_admin_message_wechat');

/**
 * 微信内容更新操作函数
 *
 * @since 1.0.0
 */
function wwpo_admin_post_update_wechat()
{
    $option_data = get_option(WECHAT_KEY_OPTION, []);

    $post_name      = WWPO_Admin::page_name();
    $post_key       = $_POST['post_key'] ?? '';
    $post_id        = $_POST['post_id'] ?? 0;
    $url_query      = ['tab' => $post_key];

    if (empty(($post_key))) {
        new WWPO_Error('message', 'not_found_key', $url_query);
        return;
    }

    if ($post_id) {
        $url_query['post']      = $post_id;
        $url_query['action']    = 'edit';

        $option_data[$post_key][$post_id] = $_POST['updated'];
        $option_data[$post_key][$post_id]['id'] = $post_id;

        // 设定日志
        wwpo_logs('admin:post:update' . $post_name . ':' . $post_key . ':' . $post_id);
    }
    //
    else {

        if ('setting' == $post_key) {
            $option_data[$post_name] = $_POST['updated'];
        } else {
            $option_data[$post_name][$post_key] = $_POST['updated'];
        }

        // 设定日志
        wwpo_logs('admin:post:update' . $post_name . ':' . $post_key);
    }

    update_option(WECHAT_KEY_OPTION, $option_data);

    new WWPO_Error('message', "{$post_name}_success_updated", $url_query);
}
add_action('wwpo_post_admin_updatewechat', 'wwpo_admin_post_update_wechat');

/**
 * 微信内容删除操作函数
 *
 * @since 1.0.0
 */
function wwpo_admin_post_delete_wechat()
{
    $option_data = get_option(WECHAT_KEY_OPTION);

    if (empty($option_data)) {
        $option_data = [];
    }

    $post_key   = $_POST['post_key'] ?? '';
    $post_id    = $_POST['post_id'] ?? 0;
    $page_url   = $_POST['_wwpourl'];

    if (empty(($post_key))) {
        new WWPO_Error('message', 'not_found_key', $page_url);
        return;
    }

    if (empty($post_id)) {
        new WWPO_Error('message', 'not_found_id', $page_url);
        return;
    }

    unset($option_data[$post_key][$post_id]);

    update_option(WECHAT_KEY_OPTION, $option_data);

    // 设定日志
    wwpo_logs('admin:post:deletewxapps:' . $post_key . ':' . $post_id);

    new WWPO_Error('message', "{$post_key}_success_delete", ['tab' => $post_key]);
}
add_action('wwpo_post_admin_deletewechat', 'wwpo_admin_post_delete_wechat');

/**
 * 注册 Rest API 接口
 *
 * @since 1.0.0
 */
add_action('rest_api_init', function () {

    $wechat = new WWPO_Wechat();
    $wechat->register_routes();

    $weapp = new WWPO_Wxapps();
    $weapp->register_routes();

    $wxpay = new WWPO_Wxpay();
    $wxpay->register_routes();
});
