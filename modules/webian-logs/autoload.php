<?php
/*
 * Modules Name: 微边日志模块
 * Description: 常用的函数和 Hook，屏蔽所有 WordPress 所有不常用的功能。
 * Version: 1.0.0
 * Author: Baiwanyi
 * Updated: 2023-01-01
 */

 /**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_logs_admin_menu($menus)
{
    $menus['banner'] = [
        'parent'        => 'webian-wordpress-one',
        'menu_title'    => __('操作日志', 'wwpo'),
        'role'          => 'edit_posts',
        'menu_order'    => 11
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_logs_admin_menu');
