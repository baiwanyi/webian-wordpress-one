<?php

/**
 * WordPress 发布设置模块
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/wp
 */
function wwpo_wordpress_wp_post($option_data)
{
    /**
     * WP 文章样式内容数组
     *
     * @since 1.0.0
     */
    $wp_post_format = [
        'aside'     => ['title' => '日志'],
        'gallery'   => ['title' => '相册'],
        'link'      => ['title' => '链接'],
        'image'     => ['title' => '图像'],
        'quote'     => ['title' => '引语'],
        'status'    => ['title' => '状态'],
        'video'     => ['title' => '视频'],
        'audio'     => ['title' => '视频'],
        'chat'      => ['title' => '聊天']
    ];

    /**
     * WP 发布功能内容数组
     *
     * @since 1.0.0
     */
    $wp_publish = [
        'link_manager'          => ['title' => '链接管理'],
        'autosave_remote_image' => ['title' => '保存远程图片'],
        'create_post_name'      => ['title' => '自动生成文章别名'],
        'page_add_excerpt'      => ['title' => '页面添加摘要编辑栏'],
        'use_classic_editor'    => ['title' => '使用经典编辑器'],
        'remove_page_parent'    => ['title' => '移除文章属性编辑栏']
    ];

    /**
     * WP 媒体尺寸内容数组
     *
     * @since 1.0.0
     */
    $wp_image_sizes = [
        'thumbnail'     => ['title' => '缩略图'],
        'medium'        => ['title' => '中等尺寸'],
        'medium_large'  => ['title' => '中大尺寸'],
        'large'         => ['title' => '大尺寸'],
        '1536x1536'     => ['title' => '2倍中大尺寸'],
        '2048x2048'     => ['title' => '2倍大尺寸']
    ];

    /**
     * WP 媒体设置内容数组
     *
     * @since 1.0.0
     */
    $wp_image_setup = [
        'size_threshold'    => ['title' => '禁用图片缩放。上传5000*7000像素以上的图片 wp 会额外生成末尾<code>-scaled</code>的图片。'],
        'size_other'        => ['title' => '禁用其他尺寸图片'],
    ];

    /**
     * 设置表单内容数组
     *
     * @since 1.0.0
     */
    $formdata = [
        'thumbnail' => [
            'title' => '特色图片',
            'field' => [
                'type'      => 'select',
                'name'      => 'option_data[thumbnail]',
                'option'    => [
                    'all'   => '开启所有（文章、页面、自定义类型）',
                    'both'  => '开启文章和页面类型',
                    'post'  => '开启文章类型',
                    'page'  => '开启页面类型'
                ],
                'show_option_all' => '关闭特色图片',
                'selected'  => $option_data['thumbnail'] ?? 0
            ]
        ],
        'image-size'   => [
            'title'     => '禁用自动生成的图片尺寸',
            'fields'    => wwpo_wp_format_checkbox('image_size', $wp_image_sizes, $option_data)
        ],
        'image-setup'   => [
            'title'     => '媒体设置',
            'fields'    => wwpo_wp_format_checkbox('image_setup', $wp_image_setup, $option_data)
        ],
        'post-format'   => [
            'title'     => '文章形式',
            'fields'    => wwpo_wp_format_checkbox('post_format', $wp_post_format, $option_data)
        ],
        'autosave'  => [
            'title' => '自动保存时间（秒）',
            'field' => ['type' => 'text', 'name' => 'option_data[autosave]', 'value' => $option_data['autosave'] ?? '']
        ],
        'publish'       => [
            'title'     => '发布功能',
            'fields'    => wwpo_wp_format_checkbox('post_publish', $wp_publish, $option_data)
        ]
    ];

    return $formdata;
}
