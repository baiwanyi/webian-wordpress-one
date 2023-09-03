<?php

/**
 * WordPress 登录设置模块
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/wp
 */
function wwpo_wordpress_wp_login($option_data)
{
    /** 获取系统权限 */
    $roles = new WP_Roles();

    /** 获取系统权限名内容数组 */
    foreach ($roles->get_names() as $role_key => $role_name) {
        if ('administrator' == $role_key) {
            continue;
        }
        $wp_admin_role[$role_key] = ['title' => translate_user_role($role_name)];
    }

    /**
     * 登录设置表单内容数组
     *
     * @since 1.0.0
     */
    $formdata = [
        'admin_role'  => [
            'title'     => '允许登录后台的角色',
            'fields'    => wwpo_wp_format_checkbox('admin_role', $wp_admin_role, $option_data)
        ],
        'admin_token'  => [
            'title' => '登录界面跳转请求参数',
            'desc'  => '设置<code>wp-login.php?token={token}</code>没有对应参数将访问首页',
            'field' => ['type' => 'text', 'name' => 'option_data[admin_token]', 'value' => $option_data['admin_token'] ?? '']
        ],
        'admin_login_redirect' => [
            'title' => '登录转跳地址',
            'field' => ['type' => 'text', 'name' => 'option_data[admin_login_redirect]', 'value' => $option_data['admin_login_redirect'] ?? '']
        ],
        'admin_logout_redirect' => [
            'title' => '退出转跳地址',
            'field' => ['type' => 'text', 'name' => 'option_data[admin_logout_redirect]', 'value' => $option_data['admin_logout_redirect'] ?? '']
        ],
        'wp_logo_title' => [
            'title' => '登录 LOGO 标题',
            'field' => ['type' => 'text', 'name' => 'option_data[logo_title]', 'value' => $option_data['logo_title'] ?? '']
        ],
        'wp_logo_link'  => [
            'title' => '登录 LOGO 链接',
            'field' => ['type' => 'text', 'name' => 'option_data[logo_link]', 'value' => $option_data['logo_link'] ?? '']
        ],
        'wp_login_title'  => [
            'title' => '登录页面标题',
            'field' => ['type' => 'text', 'name' => 'option_data[login_title]', 'value' => $option_data['login_title'] ?? '']
        ],
        'wp_login_header' => [
            'title' => '登录界面 Header 代码',
            'desc' => '设置<code>wp-login.php?token={token}</code>没有对应参数将访问首页',
            'field' => ['type' => 'textarea', 'name' => 'option_data[login_header]', 'value' => $option_data['login_header'] ?? '']
        ],
        'wp_login_footer' => [
            'title' => '登录界面 Footer 代码', 'desc' => '设置<code>wp-login.php?token={token}</code>没有对应参数将访问首页',
            'field' => ['type' => 'textarea', 'name' => 'option_data[login_footer]', 'value' => $option_data['login_footer'] ?? '']
        ]
    ];

    return $formdata;
}
