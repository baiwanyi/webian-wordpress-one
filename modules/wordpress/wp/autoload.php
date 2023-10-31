<?php

/**
 * WordPress 开发工具模块
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/wp
 */

 /**
 * 引用文件
 *
 * @since 1.0.0
 */
require WWPO_MOD_PATH . 'wordpress/wp/class-wp.php';
require WWPO_MOD_PATH . 'wordpress/wp/interface.php';
require WWPO_MOD_PATH . 'wordpress/wp/login.php';
require WWPO_MOD_PATH . 'wordpress/wp/optimizing.php';
require WWPO_MOD_PATH . 'wordpress/wp/post.php';
require WWPO_MOD_PATH . 'wordpress/wp/themes.php';

/**
 * WP 优化显示页面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_wp_wordpress()
{
    // 获取当前标签
    $current_tabs   = WWPO_Admin::tabs('optimizing');

    // 获取当前设置参数
    $option_data    = get_option('wwpo-settings-wordpress', []);
    $option_data    = $option_data[$current_tabs] ?? [];
    $option_form    = [
        'submit'    => 'updatewordpress',
        'hidden'    => ['option_key' => $current_tabs]
    ];

    // 显示页面标签
    wwpo_wp_tabs([
        'optimizing'    => '优化',
        'login'         => '登录',
        'themes'        => '主题',
        'interface'     => '界面',
        'post'          => '发布'
    ]);

    $option_form['formdata'] = call_user_func("wwpo_wordpress_wp_{$current_tabs}", $option_data);

    // 显示表单内容
    echo WWPO_Form::table($option_form);
}
add_action('wwpo_admin_display_wordpress', 'wwpo_admin_display_wp_wordpress');

/**
 * 设置页面消息函数
 *
 * @since 1.0.0
 * @param array $message
 */
function wwpo_admin_message_wordpress($message)
{
    $message['wordpress'] = [
        'no_option_key'     => ['error' => '没有找到相关保存参数'],
        'updated_success'   => ['updated' => '设置保存成功']
    ];

    return $message;
}
add_filter('wwpo_admin_message', 'wwpo_admin_message_wordpress');

/**
 * WP 优化设置 AJAX 操作函数
 *
 * @since 1.0.0
 */
function wwpo_admin_post_update_wordpress()
{
    $option_key = $_POST['option_key'];

    /** 判断保存 KEY */
    if (empty($option_key)) {
        new WWPO_Error('message', 'no_option_key');
        exit;
    }

    $option_data = get_option('wwpo-settings-wordpress', []);

    $option_data[$option_key] = $_POST['option_data'];

    // 更新到数据库
    update_option('wwpo-settings-wordpress', $option_data);

    // 设定日志
    wwpo_logs('admin:post:updatewordpress:' . $option_key);

    // 返回信息
    new WWPO_Error('message', 'updated_success');
}
add_action('wwpo_post_admin_updatewordpress', 'wwpo_admin_post_update_wordpress');

/**
 * WP 优化格式化 checkbox 函数
 *
 * @since 1.0.0
 * @param string    $checkbox_key   checkbox 内容数组 key
 * @param array     $checkbox_data  需要格式化的内容数组
 * @param array     $option_data    checkbox 保存值
 */
function wwpo_wp_format_checkbox($checkbox_key, $checkbox_data, $option_data)
{
    // 初始化
    $checkbox = [];

    /** 遍历需要格式化的 checkbox 内容数组 */
    foreach ($checkbox_data as $key => $val) {
        $checkbox[$key] = [
            'title'     => $val['title'],
            'desc'      => $val['desc'] ?? '',
            'field' => [
                'type'      => 'checkbox',
                'name'      => sprintf('option_data[%s][%s]', $checkbox_key, $key),
                'value'     => 1,
                'checked'   => $option_data[$checkbox_key][$key] ?? 0
            ]
        ];
    }

    // 返回格式化好的数组
    return $checkbox;
}

/**
 * 应用优化项目内容
 *
 * @since 1.0.0
 */
WWPO_WordPress::init();
