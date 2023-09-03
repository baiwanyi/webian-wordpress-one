<?php

/**
 * WordPress 开发工具模块
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/development
 */

/**
 * 开发工具显示页面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_wp_development()
{
    // 获取当前标签
    $current_tabs   = WWPO_Admin::tabs('dashicons');

    /** 判断标签显示搜索栏 */
    if ('dashicons' != $current_tabs) {
        wwpo_admin_page_searchbar([
            'tab'   => $current_tabs,
            'page'  => 'development'
        ]);
    }

    // 显示页面标签
    wwpo_wp_tabs([
        'dashicons'     => 'Dashicons',
        'constants'     => '系统常量',
        'hooks'         => '系统动作',
        'oembeds'       => 'Oembeds',
        'shortcodes'    => '短代码'
    ]);

    /** 判断加载显示文件 */
    $display = sprintf('%s/%s.php', __DIR__, $current_tabs);
    if (file_exists($display)) {
        require $display;
    }
}
add_action('wwpo_admin_display_development', 'wwpo_admin_display_wp_development');
