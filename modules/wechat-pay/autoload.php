<?php

/*
 * Modules Name: 微信支付模块
 * Description: 微信支付模块功能。
 * Version: 1.0.0
 * Author: Baiwanyi
 * Updated: 2022-10-20
 */

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_wechat_admin_menu($menus)
{
    $menus['wechat'] = [
        'menu_title'        => __('公众号', 'wwpo'),
        'icon'              => 'laptop',
        'all_item_title'    => '帐号设置',
        'menu_order'        => 0
    ];

    foreach ([
        'custom-menu'   => __('自定义菜单', 'wwpo'),
        'library'       => __('素材库', 'wwpo'),
        'reply'         => __('关注回复', 'wwpo'),
        'analysis'      => __('数据分析', 'wwpo'),
        'user'          => __('用户管理', 'wwpo'),
        'message'       => __('消息管理', 'wwpo'),
        'article'       => __('图文素材', 'wwpo'),
        'tmplmsg'       => __('模版消息', 'wwpo')
    ] as $menu_key => $menu_title) {
        $menus[$menu_key] = [
            'parent'        => 'wechat',
            'menu_title'    => $menu_title,
            'menu_order'    => 9
        ];
    }

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_wechat_admin_menu');

/**
 * 微信帐号设置界面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_wechat_settings()
{
    echo wwpo_wp_tabs([
        'index'     => '概览',
        'wechat'    => '公众号设定',
        'wxpay'     => '微信支付',
        'weapp'     => '小程序'
    ]);

    wwpo_load_template('wechat/pages/account', WWPO_Admin::tabs('index'));
}
add_action('wwpo_admin_display_wechat', 'wwpo_admin_display_wechat_settings');

/**
 * 微信自定义菜单界面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_wechat_custommenu()
{
    $parent_id  = WWPO_Admin::post_id();
    $page_url   = WWPO_Admin::page_url();
    $action     = WWPO_Admin::action('new');
    $menu_id    = $_GET['menu'] ?? $parent_id;
    $data = [
        'hidden'    => [
            'parent'        => $parent_id,
            'menu_id'       => $menu_id,
            'post_action'   => $action,
            'page_url'      => remove_query_arg('menu', $page_url)
        ],
        'menudata'  => get_option(WWPO_Wechat::KEY_MENU, [])
    ];

    echo '<main id="col-container" class="wp-clearfix container-fluid p-0">';
    echo '<div id="col-left" class="col-wrap">';
    echo '<div id="wwpo__wechat-menu__iphone" class="device-ios">';
    echo '<div id="wwpo__wechat-menu__iphone-inner" class="device-inner">';

    /**
     *
     */
    wwpo_load_template('wechat/pages/custom-menu-phone', 'list', $data);

    /**
     *
     */
    wwpo_load_template('wechat/pages/custom-menu-phone', 'menu', $data);
    echo '</div><!-- /#wwpo__wechat-menu__iphone-inner -->';
    echo '</div><!-- /#wwpo__wechat-menu__iphone -->';
    echo '</div>';
    echo '<div id="col-right" class="col-wrap">';

    /**
     *
     */
    wwpo_load_template('wechat/pages/custom-menu', 'edit', $data);
    echo '</div>';
    echo '</main>';
}
add_action('wwpo_admin_display_custommenu', 'wwpo_admin_display_wechat_custommenu');

/**
 * 微信自定义菜单添加「同步」按钮
 *
 * @since 1.0.0
 * @param string $pagename  页面名称
 */
function wwpo_wechat_custom_menu_link($pagename)
{
    if ('custom-menu' != $pagename) {
        return;
    }

    echo WWPO_Button::wp([
        'text'  => __('同步', 'wwpo'),
        'css'   => 'btn page-title-action',
        'value' => 'wechatmenucreate'
    ]);
}
add_action('wwpo_admin_header_link', 'wwpo_wechat_custom_menu_link');

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
 * 自定义内容更新操作函数
 *
 * @since 1.0.0
 */
function wwpo_admin_post_update_wechat()
{
    $updated = $_POST['updated'];

    if ('wechat' == $_POST['post_id']) {
        $message = 'wechat_updated';

        if (WP_DEBUG) {
            $updated['tokenexpires'] = NOW;
        }

        update_option(WWPO_Wechat::KEY_OPTION, $updated);
    }

    if ('weapp' == $_POST['post_id']) {
        $message = 'weapp_updated';
        update_option(WWPO_Wxapps::KEY_OPTION, $updated);
    }

    if ('wepay' == $_POST['post_id']) {
        $message = 'wepay_updated';
        update_option(WWPO_Wxpay::KEY_OPTION, $updated);
    }

    // 返回更新成功信息
    new WWPO_Error('message', $message, ['tab' => $_POST['post_id']]);
}
add_action('wwpo_post_admin_updatewechat', 'wwpo_admin_post_update_wechat');

/**
 * 注册 Rest API 接口
 *
 * @since 1.0.0
 */
add_action('rest_api_init', function () {

    $wechat = new WWPO_Wechat();
    $wechat->register_routes();

    $weapp = new WWPO_Weapp();
    $weapp->register_routes();

    $wxpay = new WWPO_Wxpay();
    $wxpay->register_routes();
});
