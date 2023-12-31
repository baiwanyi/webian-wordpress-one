<?php
/*
 * Modules Name: 微边自定义内容模块
 * Description: 常用的函数和 Hook，屏蔽所有 WordPress 所有不常用的功能。
 * Version: 1.0.0
 * Author: Baiwanyi
 * Updated: 2023-01-01
 */

 define('OPTION_CONTENTS_KEY', 'wwpo-data-contents');

/**
 * 加载引用文件
 */
require WWPO_MOD_PATH . 'webian-contents/class-wwpo-custom.php';
require WWPO_MOD_PATH . 'webian-contents/content-edit-posttype.php';
require WWPO_MOD_PATH . 'webian-contents/content-edit-taxonomy.php';
require WWPO_MOD_PATH . 'webian-contents/content-edit-wpmenu.php';
require WWPO_MOD_PATH . 'webian-contents/content-page-edit.php';
require WWPO_MOD_PATH . 'webian-contents/content-page-list.php';

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_contents_admin_menu($menus)
{
    $menus['wwpo-contents'] = [
        'parent'        => 'webian-wordpress-one',
        'menu_title'    => __('自定义内容', 'wwpo'),
        'menu_order'    => 11
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_contents_admin_menu');

/**
 * 自定义内容列表界面
 *
 * @since 1.0.0
 */
function wwpo_admin_display_content()
{
    $post_id = WWPO_Admin::post_id();

    // 获取自定义内容选项值
    $option_data = get_option(OPTION_CONTENTS_KEY, []);

    if (empty($post_id)) {
        wwpo_contents_page_list_table($option_data);
        return;
    }

    wwpo_contents_page_edit_form($post_id, $option_data[$post_id]);
}
add_action('wwpo_admin_display_wwpocontents', 'wwpo_admin_display_content');

/**
 * 设置页面消息函数
 *
 * @since 1.0.0
 * @param array $message
 */
function wwpo_admin_message_contents($message)
{
    $message['wwpocontents'] = [
        'not_null_title'    => ['error' => __('自定义内容名称不能为空', 'wwpo')],
        'not_null_slug'     => ['error' => __('自定义内容别名不能为空', 'wwpo')],
        'not_null_type'     => ['error' => __('请选择自定义内容的类型', 'wwpo')],
        'not_found_content' => ['error' => __('没有找到相关自定义内容', 'wwpo')],
        'exists_content'    => ['error' => __('自定义内容已存在', 'wwpo')],
        'success_added'     => ['updated' => __('自定义内容添加成功', 'wwpo')],
        'success_updated'   => ['updated' => __('自定义内容已更新', 'wwpo')],
        'success_deleted'   => ['updated' => __('自定义内容已删除', 'wwpo')],
        'success_local_load'    => ['updated' => __('本地设置读取成功', 'wwpo')],
        'success_local_save'    => ['updated' => __('本地设置保存成功', 'wwpo')]
    ];

    return $message;
}
add_filter('wwpo_admin_message', 'wwpo_admin_message_contents');

/**
 * 应用自定义内容
 *
 * @since 1.0.0
 */
WWPO_Custom::init();
