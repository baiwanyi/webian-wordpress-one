<?php

/**
 * WordPress 主题设置模块
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/wp
 */
function wwpo_wordpress_wp_themes($option_data)
{
    /**
     * 设置表单内容数组
     *
     * @since 1.0.0
     */
    $formdata = [
        'keep-menu-style'  => [
            'title' => '保留菜单样式',
            'field' => ['type' => 'textarea', 'name' => 'option_data[keep_menu_style]', 'value' => $option_data['keep_menu_style'] ?? '']
        ],
        'keep-body-style'  => [
            'title' => '保留 Body 样式',
            'field' => ['type' => 'textarea', 'name' => 'option_data[keep_body_style]', 'value' => $option_data['keep_body_style'] ?? '']
        ]
    ];

    return $formdata;
}
