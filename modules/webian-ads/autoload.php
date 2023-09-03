<?php
/*
 * Modules Name: 微边广告模块
 * Description: 常用的函数和 Hook，屏蔽所有 WordPress 所有不常用的功能。
 * Version: 1.0.0
 * Author: Baiwanyi
 * Updated: 2023-01-01
 */

/** 定义数据库表名称 */
define('WWPO_SQL_ADBANNER', 'wwpo_adbanner');

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_adbanner_admin_menu($menus)
{
    $menus['wwpo-logs'] = [
        'parent'        => 'webian-wordpress-one',
        'menu_title'    => __('广告管理', 'wwpo'),
        'page_title'    => __('所有广告', 'wwpo'),
        'role'          => 'edit_posts',
        'post_new'      => true,
        'menu_order'    => 200
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_adbanner_admin_menu');
