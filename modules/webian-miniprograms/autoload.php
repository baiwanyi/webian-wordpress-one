<?php

/*
 * Modules Name: 小程序模块
 * Description: 包括微信小程序、抖音小程序模块功能。
 * Version: 1.0.0
 * Author: Baiwanyi
 * Updated: 2023-11-01
 */

/**
 * 自定义常量
 *
 * @package Webian WordPress One
 */
define('WWPO_KEY_MINIPROGRAMS', 'wwpo:miniprograms:data');

/**
 * 引用文件
 *
 * @since 1.0.0
 */
wwpo_require_dir(WWPO_MOD_PATH . 'webian-miniprograms/includes');

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_miniprograms_admin_menu($menus)
{
    $menus['miniprograms'] = [
        'menu_title'        => __('小程序', 'wwpo'),
        'all_item_title'    => __('所有小程序', 'wwpo'),
        'position'          => 101,
        'menu_order'        => 3,
        'post_new'          => true,
        'icon'              => 'tablet'
    ];

    $menus['miniprograms-banner'] = [
        'parent'        => 'miniprograms',
        'menu_title'    => __('广告展示', 'wwpo'),
        'post_new'      => true
    ];

    $menus['miniprograms-category'] = [
        'parent'        => 'miniprograms',
        'menu_title'    => __('分类展示', 'wwpo'),
        'post_new'      => true
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_miniprograms_admin_menu');

/**
 * 微信帐号设置界面函数
 *
 * @since 1.0.0
 */
function wwpo_miniprograms_admin_display($page_name)
{
    if (false === strpos($page_name, 'miniprograms')) {
        return;
    }

    $post_id        = WWPO_Admin::post_id();
    $page_name      = str_replace('miniprograms', '', $page_name);
    $page_action    = WWPO_Admin::action('table');
    $option_data    = get_option(WWPO_KEY_MINIPROGRAMS, []);

    if (empty($page_name)) {
        $page_name = 'list';
    }

    call_user_func("wwpo_miniprograms_admin_display_{$page_name}_{$page_action}", $option_data, $page_action, $post_id);
}
add_action('wwpo_admin_display', 'wwpo_miniprograms_admin_display');
