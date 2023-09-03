<?php

/**
 * WordPress 优化设置模块
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/wp
 */
function wwpo_wordpress_wp_optimizing($option_data)
{
    /**
     * 移除头部冗余内容数组
     *
     * @since 1.0.0
     */
    $remove_wp_header = [
        'remove_generator'  => ['title' => '移除 WordPress 版本信息'],
        'remove_restapi'    => ['title' => '移除 REST API 功能'],
        'remove_embeds'     => ['title' => '移除 Embeds 功能'],
        'remove_emoji'      => ['title' => '移除 Emoji 表情JS脚本和CSS样式'],
        'remove_gutenberg'  => ['title' => '移除 Gutenberg 编辑器CSS样式'],
        'remove_canonical'  => ['title' => '移除 Canonical 标签'],
        'remove_noindex'    => ['title' => '移除 Noindex 标签'],
        'remove_feed'       => ['title' => '移除 Feed 功能'],
        'remove_sworg'      => ['title' => '移除 s.w.org 加速链接'],
        'remove_js'         => ['title' => '移除所有 JavaScript 文件'],
        'remove_css'        => ['title' => '移除所有 Style 文件'],
        'remove_shortlink'  => ['title' => '移除自动生成的短链接'],
        'remove_dns'        => ['title' => '移除加载 DNS 链接（dns-prefetch）'],
        'remove_xmlrpc'     => ['title' => '移除离线编辑器开放接口，关闭 XML-RPC 功能'],
        'remove_post_index' => ['title' => '移除当前文章索引链接'],
        'remove_post_link'  => ['title' => '移除上下篇文章索引链接'],
        'remove_post_more'  => ['title' => '移除开始和父级文章索引链接'],
        'remove_block'      => ['title' => '移除 Block 脚本和样式']
    ];

    /**
     * 清理优化内容数组
     *
     * @since 1.0.0
     */
    $wp_clean = [
        'disable_revision'          => ['title' => '禁用日志修订功能'],
        'disable_trackbacks'        => ['title' => '禁用 Trackbacks 功能'],
        'remove_dashboard_welcome'  => ['title' => '移除 WordPress 仪表盘欢迎面板'],
        'remove_dashboard_widget'   => ['title' => '移除 WordPress 仪表盘系统模块'],
        'remove_nav_menu_class'     => ['title' => '移除前台菜单多余 CSS 样式', 'desc' => '可在 <a href="/wp-admin/options-discussion.php#moderation_keys"><strong>[主题 - 保留菜单样式]</strong></a> 中设置保留的样式名称。'],
        'remove_body_class'         => ['title' => '移除前台 Body 多余 CSS 样式', 'desc' => '可在 <a href="/wp-admin/options-discussion.php#moderation_keys"><strong>[主题 - 保留 Body 样式]</strong></a> 中设置保留的样式名称。']
    ];

    /**
     * 功能增强内容数组
     *
     * @since 1.0.0
     */
    $wp_function = [
        'disable_autoupdate'    => ['title' => '禁用 WordPress 后台自动更新功能'],
        'shortcode_first'       => ['title' => '让 Shortcode 优先于 wpautop 执行'],
        'only_show_upload'      => ['title' => '<strong>添加媒体</strong>只显示用户上传文件'],
        'only_show_media'       => ['title' => '<strong>媒体库</strong>只显示用户上传文件'],
        'search_result_post'    => ['title' => '搜索结果只有一篇时直接重定向到日志'],
        'upload_rename'         => ['title' => '上传图片按照<code>年-月-日-时-分-秒-6位随机字母数字</code>的格式重新命名']
    ];

    /**
     * 用户设置内容数组
     *
     * @since 1.0.0
     */
    $wp_user = [
        'strict_user'           => ['title' => '严格用户模式', 'desc' => '严格用户模式下，昵称和显示名称都是唯一的，并且用户名中不允许出现非法关键词（非法关键词是在 <a href="/wp-admin/options-discussion.php#moderation_keys"><strong>[设置 - 讨论]</strong></a> 中 <code>评论审核</code> 和 <code>评论黑名单</code> 中定义的关键词）。'],
        'disabled_admin_color'  => ['title' => '禁用管理界面配色方案'],
        'disable_admin_bar'     => ['title' => '移除顶部工具栏 AdminBar'],
    ];

    /**
     * 安全防护内容数组
     *
     * @since 1.0.0
     */
    $wp_security = [
        'disable_admin_user'    => ['title' => '禁止使用 Admin 用户名尝试登录'],
        'block_bad_queries'     => ['title' => '阻止非法访问'],
        'disable_code_edit'     => ['title' => '禁用后台代码编辑功能'],
        'disable_themes_setup'  => ['title' => '禁用后台主题安装'],
    ];

    /**
     * 设置表单内容数组
     *
     * @since 1.0.0
     */
    $formdata = [
        'remove-wp-header' => [
            'title'     => '移除 Header 项目',
            'fields'    => wwpo_wp_format_checkbox('wp_header', $remove_wp_header, $option_data)
        ],
        'wp-clean' => [
            'title'     => '清理优化',
            'fields'    => wwpo_wp_format_checkbox('wp_clean', $wp_clean,  $option_data)
        ],
        'wp-function'  => [
            'title'     => '功能增强',
            'fields'    => wwpo_wp_format_checkbox('wp_function', $wp_function, $option_data)
        ],
        'wp-user'  => [
            'title'     => '用户设置',
            'fields'    => wwpo_wp_format_checkbox('wp_user', $wp_user, $option_data)
        ],
        'wp-security'  => [
            'title'     => '安全防护',
            'fields'    => wwpo_wp_format_checkbox('wp_security', $wp_security, $option_data)
        ]
    ];

    return $formdata;
}
