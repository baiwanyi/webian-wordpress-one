<?php

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_register_admin_menu_docs($menus)
{
    $menus['wwpo-docs'] = [
        'parent'        => 'webian-wordpress-one',
        'menu_title'    => __('开发文档', 'wwpo')
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_register_admin_menu_docs');

/**
 * Undocumented function
 *
 * @param [type] $classes
 * @return void
 */
function wwpo_register_admin_body_class_docs($classes)
{
    if (!WWPO_Admin::is_page('wwpo-docs')) {
        return $classes;
    }

    return 'wwpo__admin-wide';
}
add_filter('admin_body_class', 'wwpo_register_admin_body_class_docs');


/**
 * Undocumented function
 *
 * @param [type] $current_page_title
 * @param [type] $current_page_name
 * @return void
 */
function wwpo_register_admin_head_docs($current_page_name, $current_page_title)
{
    if ('wwpo-docs' != $current_page_name) {
        return;
    }

    printf('<div class="wwpo__admin-header"><div class="wwpo__admin-header__section"><h1>%s</h1></div><nav class="wwpo__admin-header__tabs" aria-label="次要菜单">
    <a href="https://wp.webian.dev/wp-admin/site-health.php?tab" class="wwpo__admin-header__tabs-item active">状态</a><a href="https://wp.webian.dev/wp-admin/site-health.php?tab=debug" class="wwpo__admin-header__tabs-item">信息</a><a href="https://wp.webian.dev/wp-admin/site-health.php?tab=debug" class="wwpo__admin-header__tabs-item">信息</a>
        </nav></div>', $current_page_title);
}
add_filter('wwpo_admin_head', 'wwpo_register_admin_head_docs', 10, 2);

/**
 * Undocumented function
 *
 * @param [type] $current_page_title
 * @param [type] $current_page_name
 * @return void
 */
function wwpo_register_admin_page_title_docs($current_page_name)
{
    if ('wwpo-docs' == $current_page_name) {
        return false;
    }
}
add_filter('wwpo_admin_page_title', 'wwpo_register_admin_page_title_docs');

/**
 * 自定义内容列表界面
 *
 * @since 1.0.0
 */
function wwpo_register_admin_display_layout()
{
    $path = $_GET['path'] ?? 'README.md';

    WWPO_Load::markdown('layout', WWPO_PLUGIN_PATH . $path);
}
add_action('wwpo_admin_display_wwpo-docs', 'wwpo_register_admin_display_layout');
