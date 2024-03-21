<?php

/**
 * 用户函数操作函数
 *
 * @package Webian WordPress One
 */



/**
 * 通过 META 数据库获取用户 ID
 *
 * @since 1.0.0
 * @param string $meta_key
 * @param string $meta_value
 */
function wwpo_get_user_id_by_meta($meta_key, $meta_value)
{
    global $wpdb;
    $user_id = $wpdb->get_var("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '{$meta_key}' AND meta_value = '{$meta_value}'");
    return $user_id;
}

/**
 * 添加用户列表链接动作函数
 *
 * @since 1.0.0
 * @param array     $actions    链接动作数组
 * @param object    $user       该用户信息
 */
function wwpo_user_row_actions($actions, $user)
{
    if ($user->ID == get_current_user_id()) {
        return $actions;
    }

    $actions['userlogin'] = sprintf('<a href="#logincurrentuser" data-user_id="%d" data-action="wpajax" value="logincurrentuser">登录该用户</a>', $user->ID);

    return $actions;
}
add_action('user_row_actions', 'wwpo_user_row_actions', 10, 2);

/**
 * 执行登录当前用户 AJAX 操作函数
 *
 * @since 1.0.0
 */
function wwpo_ajax_login_current_user($request)
{
    wwpo_user_login($request['user_id']);

    // 设定日志
    wwpo_logs('admin:ajax:logincurrentuser');

    return WWPO_Error::toast('success', '已切换用户，正在刷新页面。', ['url' => 'reload']);
}
add_filter('wwpo_ajax_admin_logincurrentuser', 'wwpo_ajax_login_current_user');

/**
 * 记录用户登录时间操作
 *
 * @since 1.0.0
 */
function insert_last_login($user_login)
{
    $user = get_user_by('login', $user_login);
    $user_id = $user->ID;

    // 记录用户登录时间
    update_user_meta($user_id, 'last_login', NOW_TIME);
}
add_action('wp_login', 'insert_last_login');

/**
 * 注册用户联系方式动作
 *
 * @since 1.0.0
 */
add_filter('user_contactmethods', function ($methods) {
    $methods['user_phone'] = '手机号码';
    return $methods;
});

/**
 * 注册用户更新资料动作
 *
 * @since 1.0.0
 */
add_action('rest_after_insert_user', function ($user, $request, $false) {
    update_user_meta($user->ID, 'user_phone', $request['phone']);
}, 10, 3);
