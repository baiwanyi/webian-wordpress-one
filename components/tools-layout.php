<?php

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_register_admin_menu_layout($menus)
{
    $menus['wwpo-layout'] = [
        'parent'        => 'tools.php',
        'menu_title'    => __('布局模板', 'wwpo')
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_register_admin_menu_layout');

/**
 * 自定义内容列表界面
 *
 * @since 1.0.0
 */
function wwpo_register_admin_display_layout()
{
    WWPO_Load::markdown('layout', WWPO_PLUGIN_PATH . 'pages/layout.md');
}
add_action('wwpo_admin_display_wwpo-layout', 'wwpo_register_admin_display_layout');
