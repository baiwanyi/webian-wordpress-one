<?php
/*
 * Modules Name: WP 优化模块
 * Description: 常用的函数和 Hook，屏蔽所有 WordPress 所有不常用的功能。
 * Version: 3.0.0
 * Author: Yeeloving
 * Updated: 2023-01-01
 */

/**
 * 引用文件
 *
 * @since 1.0.0
 */
require WWPO_MOD_PATH . 'wordpress/development/autoload.php';
require WWPO_MOD_PATH . 'wordpress/wp/autoload.php';
require WWPO_MOD_PATH . 'wordpress/metadata.php';
require WWPO_MOD_PATH . 'wordpress/mysql.php';
require WWPO_MOD_PATH . 'wordpress/roles.php';

/**
 * 注册后台菜单
 *
 * @since 1.0.0
 * @param array $menus
 */
function wwpo_wp_admin_menus($menus)
{
    $menus['wordpress'] = [
        'parent'        => 'webian-wordpress-one',
        'menu_title'    => __('WP 优化', 'wwpo'),
        'page_title'    => __('WordPress 优化设置', 'wwpo'),
        'menu_order'    => 10
    ];

    $menus['development'] = [
        'parent'        => 'tools.php',
        'menu_title'    => __('开发工具', 'wwpo'),
        'page_title'    => __('开发工具', 'wwpo'),
        'menu_order'    => 10
    ];

    $menus['mysql'] = [
        'parent'        => 'tools.php',
        'menu_title'    => __('数据库清理优化', 'wwpo'),
        'page_title'    => __('数据库清理优化', 'wwpo'),
        'menu_order'    => 10
    ];

    $menus['metadata'] = [
        'parent'        => 'tools.php',
        'menu_title'    => __('元数据表管理', 'wwpo'),
        'page_title'    => __('元数据表管理', 'wwpo'),
        'menu_order'    => 10
    ];

    $menus['roles'] = [
        'parent'        => 'users.php',
        'menu_title'    => __('用户角色', 'wwpo'),
        'page_title'    => __('用户角色', 'wwpo'),
        'menu_order'    => 10
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_wp_admin_menus');

/**
 * 添加文章访问统计函数操作
 *
 * @since 1.0.0
 * @todo 添加 Redis 缓存
 */
function wwpo_updated_post_view()
{
    global $post, $wpdb;

    if (is_admin()) {
        return;
    }

    if (!is_single()) {
        return;
    }

    if ('post' != $post->post_type) {
        return;
    }

    $wpdb->query("UPDATE $wpdb->posts SET menu_order = menu_order + 1 WHERE ID = $post->ID");
}
add_action('wp_body_open', 'wwpo_updated_post_view');
