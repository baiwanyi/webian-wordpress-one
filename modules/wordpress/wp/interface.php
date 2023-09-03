<?php

/**
 * WordPress 界面设置模块
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/wp
 */

/**
 * 设置表单内容数组
 *
 * @since 1.0.0
 */
function wwpo_wordpress_wp_interface($option_data)
{
    $formdata = [
        'custom_wp_header'   => [
            'title' => '前台界面 Header 代码',
            'desc'  => '设置<code>wp-login.php?token={token}</code>没有对应参数将访问首页',
            'field' => ['type' => 'textarea', 'name' => 'option_data[custom_wp_header]', 'value' => $option_data['custom_wp_header'] ?? '']
        ],
        'wp_footer'   => [
            'title' => '前台界面 Footer 代码',
            'fields' => [
                'wp_footer_times'   => [
                    'title' => '前台显示页面生成时间',
                    'field' => ['type' => 'checkbox', 'name' => 'option_data[wp_footer_times]', 'value' => 1, 'checked' => $option_data['wp_footer_times'] ?? 0]
                ],
                'wp_footer_queries'   => [
                    'title' => '显示当前页面执行的所有 SQL 语句',
                    'field' => ['type' => 'checkbox', 'name' => 'option_data[wp_footer_queries]', 'value' => 1, 'checked' => $option_data['wp_footer_queries'] ?? 0]
                ],
                'custom_wp_footer' => [
                    'desc'  => '设置<code>wp-login.php?token={token}</code>没有对应参数将访问首页',
                    'field' => ['type' => 'textarea', 'name' => 'option_data[custom_wp_footer]', 'value' => $option_data['custom_wp_footer'] ?? '']
                ]
            ]
        ],
        'admin_title'  => [
            'title' => '后台标题',
            'field' => ['type' => 'text', 'name' => 'option_data[admin_title]', 'value' => $option_data['admin_title'] ?? '']
        ],
        'admin_header'   => [
            'title' => '后台界面 Header 代码', 'desc' => '设置<code>wp-login.php?token={token}</code>没有对应参数将访问首页',
            'field' => ['type' => 'textarea', 'name' => 'option_data[admin_header]', 'value' => $option_data['admin_header'] ?? '']
        ],
        'admin_footer' => [
            'title' => '后台界面 Footer 代码', 'desc' => '设置<code>wp-login.php?token={token}</code>没有对应参数将访问首页',
            'field' => ['type' => 'textarea', 'name' => 'option_data[admin_footer]', 'value' => $option_data['admin_footer'] ?? '']
        ],
        'admin_footer_text'    => [
            'title' => '左下角文字信息', 'desc' => '设置<code>wp-login.php?token={token}</code>没有对应参数将访问首页',
            'field' => ['type' => 'text', 'name' => 'option_data[admin_footer_text]', 'value' => $option_data['admin_footer_text'] ?? '']
        ],
        'update_footer_core'  => [
            'title'     => '右下角文字信息',
            'fields'    => [
                'update_footer_core' => [
                    'title' => '显示页面生成时间信息',
                    'field' => ['type' => 'checkbox', 'name' => 'option_data[update_footer_core]', 'value' => 1, 'checked' => $option_data['update_footer_core'] ?? 0]
                ],
                'admin_footer_queries' => [
                    'title' => '显示后台页面执行的所有 SQL 语句',
                    'field' => ['type' => 'checkbox', 'name' => 'option_data[admin_footer_queries]', 'value' => 1, 'checked' => $option_data['admin_footer_queries'] ?? 0]
                ],
                'update_footer_ver' => [
                    'title' => '右下角文字信息', 'desc' => '设置<code>wp-login.php?token={token}</code>没有对应参数将访问首页',
                    'field' => ['type' => 'text', 'name' => 'option_data[update_footer_ver]', 'value' => $option_data['update_footer_ver'] ?? '']
                ]
            ]
        ]
    ];

    return $formdata;
}
