<?php

/**
 * WordPress 优化模块
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage components
 */
add_action('init', ['WWPO_WordPress', 'remove_wp_header']);
add_action('wwpo_admin_display_wordpress', ['WWPO_WordPress', 'display']);
add_filter('wwpo_menus', ['WWPO_WordPress', 'admin_menu']);
add_action('wp_body_open', ['WWPO_WordPress', 'updated_post_view']);
add_action('wwpo_ajax_admin_updatewordpress', ['WWPO_WordPress', 'updated_option']);

/**
 * WordPress 优化项目类
 *
 * @since 1.0.0
 */
class WWPO_WordPress
{
    const OPTION_KEY = 'wwpo-settings-wordpress';

    /**
     * 设定类实例
     *
     * @since 1.0.0
     * @var array
     */
    public $option;

    /**
     * 构造函数
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->option = get_option(self::OPTION_KEY, []);
    }

    /**
     * 添加后台管理菜单
     *
     * @since 1.0.0
     * @param array $menus 菜单内容数组
     * @return array
     */
    public static function admin_menu($menus)
    {
        $menus['wordpress'] = [
            'parent'        => 'webian-wordpress-one',
            'menu_title'    => __('WP 优化', 'wwpo'),
            'page_title'    => __('WordPress 优化设置', 'wwpo'),
            'menu_order'    => 10
        ];

        return $menus;
    }

    /**
     * WP 优化显示页面函数
     *
     * @since 1.0.0
     * @return string
     */
    public static function display()
    {
        // 获取当前标签
        $current_tabs   = $_GET['tab'] ?? 'optimizing';

        // 获取当前设置参数
        $option_data    = get_option(self::OPTION_KEY, []);
        $option_data    = $option_data[$current_tabs] ?? [];
        $option_form    = [
            'button'    => 'updatewordpress',
            'hidden'    => ['option_key' => $current_tabs]
        ];

        // 显示页面标签
        WWPO_Template::tabs([
            'optimizing'    => '优化',
            'login'         => '登录',
            'themes'        => '主题',
            'interface'     => '界面',
            'post'          => '发布'
        ]);

        $option_form['formdata'] = call_user_func([__CLASS__, "_wp_{$current_tabs}"], $option_data);

        // 显示表单内容
        echo WWPO_Form::table($option_form);
    }

    /**
     * WordPress 优化设置模块
     *
     * @since 1.0.0
     * @param array $option_data
     * @return array
     */
    static function _wp_optimizing($option_data)
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
                'fields'    => self::_format_checkbox('wp_header', $remove_wp_header, $option_data)
            ],
            'wp-clean' => [
                'title'     => '清理优化',
                'fields'    => self::_format_checkbox('wp_clean', $wp_clean,  $option_data)
            ],
            'wp-function'  => [
                'title'     => '功能增强',
                'fields'    => self::_format_checkbox('wp_function', $wp_function, $option_data)
            ],
            'wp-user'  => [
                'title'     => '用户设置',
                'fields'    => self::_format_checkbox('wp_user', $wp_user, $option_data)
            ],
            'wp-security'  => [
                'title'     => '安全防护',
                'fields'    => self::_format_checkbox('wp_security', $wp_security, $option_data)
            ]
        ];

        return $formdata;
    }

    /**
     * WordPress 登录设置模块
     *
     * @since 1.0.0
     * @package Webian WordPress One
     * @subpackage WordPress/wp
     */
    static function _wp_login($option_data)
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
                'fields'    => self::_format_checkbox('admin_role', $wp_admin_role, $option_data)
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

    /**
     * WordPress 主题设置模块
     *
     * @since 1.0.0
     * @package Webian WordPress One
     * @subpackage WordPress/wp
     */
    static function _wp_themes($option_data)
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

    /**
     * 设置表单内容数组
     *
     * @since 1.0.0
     */
    static function _wp_interface($option_data)
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

    /**
     * WordPress 发布设置模块
     *
     * @since 1.0.0
     * @package Webian WordPress One
     * @subpackage WordPress/wp
     */
    static function _wp_post($option_data)
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
            'image-featured' => [
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
                'fields'    => self::_format_checkbox('image_size', $wp_image_sizes, $option_data)
            ],
            'image-setup'   => [
                'title'     => '媒体设置',
                'fields'    => self::_format_checkbox('image_setup', $wp_image_setup, $option_data)
            ],
            'post-format'   => [
                'title'     => '文章形式',
                'fields'    => self::_format_checkbox('post_format', $wp_post_format, $option_data)
            ],
            'autosave'  => [
                'title' => '自动保存时间（秒）',
                'field' => ['type' => 'text', 'name' => 'option_data[autosave]', 'value' => $option_data['autosave'] ?? '']
            ],
            'publish'       => [
                'title'     => '发布功能',
                'fields'    => self::_format_checkbox('post_publish', $wp_publish, $option_data)
            ]
        ];

        return $formdata;
    }

    /**
     * 移除头部多余项目
     *
     * @since 1.0.0
     */
    static function remove_wp_header()
    {
        $option_data = get_option(self::OPTION_KEY, []);
        $option_data = $option_data['optimizing']['wp_header'] ?? [];
        $option_data = wp_parse_args($option_data, [
            'remove_generator'  => 0,
            'remove_restapi'    => 0,
            'remove_embeds'     => 0,
            'remove_emoji'      => 0,
            'remove_gutenberg'  => 0,
            'remove_canonical'  => 0,
            'remove_noindex'    => 0,
            'remove_feed'       => 0,
            'remove_sworg'      => 0,
            'remove_js'         => 0,
            'remove_css'        => 0,
            'remove_shortlink'  => 0,
            'remove_dns'        => 0,
            'remove_xmlrpc'     => 0,
            'remove_post_index' => 0,
            'remove_post_link'  => 0,
            'remove_post_more'  => 0,
            'remove_block'      => 0
        ]);

        /** 移除 WordPress 版本信息 */
        if ($option_data['remove_generator']) {
            remove_action('wp_head', 'wp_generator');
            foreach (['rss2_head', 'commentsrss2_head', 'rss_head', 'rdf_header', 'atom_head', 'comments_atom_head', 'opml_head', 'app_head'] as $action) {
                remove_action($action, 'the_generator');
            }
        }

        /** 移除 REST API 功能 */
        if ($option_data['remove_restapi']) {
            remove_action('template_redirect', 'rest_output_link_header', 10);
            remove_action('init', 'rest_api_init');
            remove_action('rest_api_init', 'rest_api_default_filters', 10);
            remove_action('parse_request', 'rest_api_loaded');
            add_filter('rest_enabled', '__return_false');
            add_filter('rest_jsonp_enabled', '__return_false');
        }

        /** 移除 embeds 功能 */
        if ($option_data['remove_embeds']) {
            remove_filter('the_content', [$GLOBALS['wp_embed'], 'autoembed'], 8);
            remove_action('rest_api_init', 'wp_oembed_register_route');
            remove_filter('rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4);
            remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
            remove_filter('oembed_response_data', 'get_oembed_response_data_rich', 10, 4);
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
            remove_action('wp_head', 'wp_oembed_add_host_js');
            add_filter('embed_oembed_discover', '__return_false');
            add_filter('tiny_mce_plugins', function ($plugins) {
                if (is_array($plugins)) {
                    return array_diff($plugins, ['wpembed']);
                }

                return [];
            });
            add_filter('query_vars', function ($plugins) {
                if (is_array($plugins)) {
                    return array_diff($plugins, ['embed']);
                }

                return [];
            });
            wp_dequeue_script('wp-embed');
        }

        /** 移除 emoji 功能 */
        if ($option_data['remove_emoji']) {
            remove_action('admin_enqueue_scripts', 'wp_enqueue_emoji_styles');
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
            remove_action('admin_print_scripts', 'print_head_scripts');
            remove_action('admin_print_footer_scripts', '_wp_footer_scripts');
            remove_action('admin_print_styles', 'print_emoji_styles');
            remove_action('admin_print_styles', 'print_admin_styles');

            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action('embed_head', 'print_emoji_detection_script');
            remove_filter('the_content_feed', 'wp_staticize_emoji');
            remove_filter('comment_text_rss', 'wp_staticize_emoji');
            remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

            add_filter('emoji_svg_url', '__return_false');
        }

        /** 移除 Gutenberg 编辑器CSS样式 */
        if ($option_data['remove_gutenberg']) {
            wp_dequeue_style('wp-block-library');
            wp_deregister_style('wp-block-library');
        }

        /** 移除 canonical 标签 */
        if ($option_data['remove_canonical']) {
            remove_action('wp_head', 'rel_canonical');
        }

        /** 移除 noindex 标签 */
        if ($option_data['remove_noindex']) {
            remove_action('wp_head', 'noindex', 1);
        }

        /** 移除 feed 功能 */
        if ($option_data['remove_feed']) {
            remove_action('wp_head', 'feed_links_extra', 3);
            remove_action('wp_head', 'feed_links', 2);
        }

        /** 移除 s.w.org 加速链接 */
        if ($option_data['remove_sworg']) {
            remove_action('wp_head', 'rest_output_link_wp_head', 10, 0);
            remove_action('wp_head', 'wp_resource_hints', 2);
            remove_action('template_redirect', 'rest_output_link_header', 11, 0);
        }

        /** 移除所有 JavaScript 文件 */
        if ($option_data['remove_js']) {
            remove_action('wp_head', 'wp_print_head_scripts', 11);
            remove_action('wp_head', 'wp_enqueue_scripts', 1);
            remove_action('wp_footer', 'wp_print_footer_scripts');
        }

        /** 移除所有 STYLE 文件 */
        if ($option_data['remove_css']) {
            remove_action('wp_head', 'wp_print_styles', 11);
            remove_action('wp_head', 'locale_stylesheet');
        }

        /** 移除自动生成的短链接 */
        if ($option_data['remove_shortlink']) {
            add_filter('get_shortlink', '__return_false', 99);
            remove_action('wp_head', 'wp_shortlink_wp_head', 99);
            remove_action('template_redirect', 'wp_shortlink_header', 99);
            remove_action('template_redirect', 'rest_output_link_header', 99);
        }

        /** 移除加载 DNS 链接（dns-prefetch） */
        if ($option_data['remove_dns']) {
            add_filter('wp_resource_hints', function ($hints, $relation_type) {
                if ('dns-prefetch' === $relation_type) {
                    return array_diff(wp_dependencies_unique_hosts(), $hints);
                }
                return $hints;
            }, 10, 2);
        }

        /** 移除离线编辑器开放接口 */
        if ($option_data['remove_xmlrpc']) {
            remove_action('wp_head', 'rsd_link');
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
            add_filter('xmlrpc_enabled', '__return_false');
        }

        /** 移除当前文章索引链接 */
        if ($option_data['remove_post_index']) {
            remove_action('wp_head', 'index_rel_link');
        }

        /** 移除上下篇文章索引链接 */
        if ($option_data['remove_post_link']) {
            remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
            remove_action('publish_future_post', 'check_and_publish_future_post', 10, 1);
        }

        /** 移除开始和父级文章索引链接 */
        if ($option_data['remove_post_more']) {
            remove_action('wp_head', 'start_post_rel_link', 10, 0);
            remove_action('wp_head', 'parent_post_rel_link', 10, 0);
        }

        /** 移除 Block 脚本和样式 */
        if ($option_data['remove_block']) {
            remove_filter('render_block', 'wp_render_duotone_support');
            remove_filter('render_block', 'wp_restore_group_inner_container');
            remove_action('wp_footer', 'the_block_template_skip_link');
            remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
            remove_action('in_admin_header', 'wp_global_styles_render_svg_filters');
            remove_filter('render_block', 'wp_render_layout_support_flag');
            remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');
            remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
            remove_action('wp_footer', 'wp_enqueue_global_styles');
            add_filter('should_load_separate_core_block_assets', '__return_false');
            add_filter('editor_stylesheets', '__return_false');
            add_filter('should_load_separate_core_block_assets', '__return_false', 11);
            wp_dequeue_style('wp-webfonts');
            wp_dequeue_style('classic-theme-styles');
        }
    }

    /**
     * 清理优化函数
     *
     * @since 1.0.0
     */
    public function wp_clean()
    {
        $option_data = $this->option['optimizing']['wp_clean'] ?? [];
        $option_data = wp_parse_args($option_data, [
            'disable_revision'          => 0,
            'disable_trackbacks'        => 0,
            'remove_dashboard_welcome'  => 0,
            'remove_dashboard_widget'   => 0,
            'remove_nav_menu_class'     => 0,
            'remove_body_class'         => 0
        ]);

        /** 禁用日志修订功能 */
        if ($option_data['disable_revision']) {
            define('WP_POST_REVISIONS', false);
            remove_action('pre_post_update', 'wp_save_post_revision');
        }

        /** 禁用 Trackbacks 功能 */
        if ($option_data['disable_trackbacks']) {
            remove_action('do_pings', 'do_all_pings', 10);
            remove_action('publish_post', '_publish_post_hook', 5);
            add_filter('xmlrpc_methods', function ($methods) {
                $methods['pingback.ping']                        = '__return_false';
                $methods['pingback.extensions.getPingbacks']    = '__return_false';
                return $methods;
            });
        }

        /** 移除 WordPress 仪表盘欢迎面板 */
        if ($option_data['remove_dashboard_welcome']) {
            remove_action('welcome_panel', 'wp_welcome_panel');
        }

        /** 移除 WordPress 仪表盘系统模块 */
        if ($option_data['remove_dashboard_widget']) {
            add_action('wp_dashboard_setup', function () {
                global $wp_meta_boxes;
                unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health']);
                unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
                unset($wp_meta_boxes['dashboard']['normal']['high']['dashboard_browser_nag']);
                unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
                unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
                unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
            });
        }

        /** 移除前台菜单多余 CSS 样式 */
        if ($option_data['remove_nav_menu_class']) {
            add_filter('nav_menu_css_class', [$this, 'nav_menu_class_attributes_filter'], 100, 1);
            add_filter('nav_menu_item_id', [$this, 'nav_menu_class_attributes_filter'], 100, 1);
            add_filter('page_css_class', [$this, 'nav_menu_class_attributes_filter'], 100, 1);
        }

        /** 移除前台 Body 多余 CSS 样式 */
        if ($option_data['remove_body_class']) {
            add_filter('body_class', [$this, 'body_class_attributes_filter']);
        }
    }

    /**
     * 功能增强函数
     *
     * @since 1.0.0
     */
    public function wp_function()
    {
        $option_data = $this->option['optimizing']['wp_function'] ?? [];
        $option_data = wp_parse_args($option_data, [
            'disable_autoupdate'    => 0,
            'shortcode_first'       => 0,
            'only_show_upload'      => 0,
            'only_show_media'       => 0,
            'search_result_post'    => 0,
            'upload_rename'         => 0
        ]);

        /** 禁用 WordPress 后台自动更新功能 */
        if ($option_data['disable_autoupdate']) {

            // 关闭核心提示
            add_filter('pre_site_transient_update_core', '__return_false');

            // 关闭插件提示
            add_filter('pre_site_transient_update_plugins', '__return_false');

            // 关闭主题提示
            add_filter('pre_site_transient_update_themes', '__return_false');

            // 彻底关闭自动更新
            add_filter('automatic_updater_disabled', '__return_true');

            // 关闭更新检查定时作业
            remove_action('init', 'wp_schedule_update_checks');

            // 移除已有的版本检查定时作业
            wp_clear_scheduled_hook('wp_version_check');

            // 移除已有的插件更新定时作业
            wp_clear_scheduled_hook('wp_update_plugins');

            // 移除已有的主题更新定时作业
            wp_clear_scheduled_hook('wp_update_themes');

            // 移除已有的自动更新定时作业
            wp_clear_scheduled_hook('wp_maybe_auto_update');

            // 移除后台内核更新检查
            remove_action('admin_init', '_maybe_update_core');

            // 移除后台插件更新检查
            remove_action('load-plugins.php', 'wp_update_plugins');
            remove_action('load-update.php', 'wp_update_plugins');
            remove_action('load-update-core.php', 'wp_update_plugins');
            remove_action('admin_init', '_maybe_update_plugins');

            // 移除后台主题更新检查
            remove_action('load-themes.php', 'wp_update_themes');
            remove_action('load-update.php', 'wp_update_themes');
            remove_action('load-update-core.php', 'wp_update_themes');
            remove_action('admin_init', '_maybe_update_themes');
        }

        /** 让 Shortcode 优先于 wpautop 执行 */
        if ($option_data['shortcode_first']) {
            remove_filter('the_content', 'wpautop');
            add_filter('the_content', 'wpautop', 99);
            remove_filter('the_content', 'shortcode_unautop');
            add_filter('the_content', 'shortcode_unautop', 100);
        }

        /** 添加媒体只显示用户上传文件 */
        if ($option_data['only_show_upload']) {
            add_action('pre_get_posts', function ($wp_query) {
                global $current_user, $pagenow;

                if (!is_a($current_user, 'WP_User')) {
                    return;
                }

                if ('admin-ajax.php' != $pagenow || 'query-attachments' != $_REQUEST['action']) {
                    return;
                }

                if (!current_user_can('manage_options') && !current_user_can('manage_media_library')) {
                    $wp_query->set('author', $current_user->ID);
                    return;
                }
            });
        }

        /** 媒体库只显示用户上传文件 */
        if ($option_data['only_show_media']) {
            add_filter('parse_query', function ($wp_query) {
                if (false !== strpos($_SERVER['REQUEST_URI'], '/wp-admin/upload.php')) {
                    if (!current_user_can('manage_options') && !current_user_can('manage_media_library')) {
                        global $current_user;
                        $wp_query->set('author', $current_user->id);
                    }
                }
            });
        }

        /** 当搜索结果只有一篇时直接重定向到日志 */
        if ($option_data['search_result_post']) {
            add_action('template_redirect', function () {
                if (is_search() && empty(get_query_var('module'))) {
                    global $wp_query;
                    $paged = get_query_var('paged');
                    if (1 == $wp_query->post_count && empty($paged)) {
                        wp_redirect(get_permalink($wp_query->posts['0']->ID));
                    }
                }
            });
        }

        /** 上传文件重新命名 */
        if ($option_data['upload_rename']) {
            add_filter('wp_handle_upload_prefilter', function ($file) {
                $file['name'] = wwpo_filename($file['name']);
                return $file;
            });
        }
    }

    /**
     * 用户设置函数
     *
     * @since 1.0.0
     */
    public function wp_user()
    {
        $option_data = $this->option['optimizing']['wp_user'] ?? [];
        $option_data = wp_parse_args($option_data, [
            'strict_user'           => 0,
            'disabled_admin_color'  => 0,
            'disable_admin_bar'     => 0
        ]);

        /** 启用严格用户模式 */
        if ($option_data['strict_user']) {
            add_filter('sanitize_user', function ($username) {

                // 设置用户名只能大小写字母和 - . _
                $username = preg_replace('|[^a-z0-9_.\-]|i', '', $username);

                //检测待审关键字和黑名单关键字
                if (wwpo_check_wp_blacklist($username)) {
                    $username = '';
                }

                return $username;
            });
        }

        /** 禁用管理界面配色方案 */
        if ($option_data['disabled_admin_color']) {
            remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker', 100);
            remove_action('admin_init', 'register_admin_color_schemes', 100);
            remove_action('admin_head', 'wp_color_scheme_settings', 100);
        }

        /** 移除顶部工具栏 AdminBar */
        if ($option_data['disable_admin_bar']) {
            add_filter('show_admin_bar', '__return_false');
        }
    }

    /**
     * 安全防护函数
     *
     * @since 1.0.0
     */
    public function wp_security()
    {
        $option_data = $this->option['optimizing']['wp_security'] ?? [];
        $option_data = wp_parse_args($option_data, [
            'disable_admin_user'    => 0,
            'block_bad_queries'     => 0,
            'disable_code_edit'     => 0,
            'disable_themes_setup'  => 0
        ]);

        /** 禁止使用 admin 用户名尝试登录 */
        if ($option_data['disable_admin_user']) {
            add_filter('wp_authenticate', function ($user) {
                if ('admin' == $user) {
                    exit;
                }
            });
            add_filter('sanitize_user', function ($username, $raw_username) {
                if ('admin' == $raw_username || 'admin' == $username) {
                    exit;
                }
                return $username;
            }, 10, 2);
        }

        /** 阻止非法访问 */
        if ($option_data['block_bad_queries']) {
            add_action('init', function () {
                if (is_admin()) {
                    return;
                }
                if (
                    strpos($_SERVER['REQUEST_URI'], "eval(") ||
                    strpos($_SERVER['REQUEST_URI'], "base64") ||
                    strpos($_SERVER['REQUEST_URI'], "/**/")
                ) {
                    @header("HTTP/1.1 414 Request-URI Too Long");
                    @header("Status: 414 Request-URI Too Long");
                    @header("Connection: Close");
                    @exit;
                }
            });
        }

        /** 禁用后台代码编辑功能 */
        if ($option_data['disable_code_edit']) {
            add_action('init', function () {
                define('DISALLOW_FILE_EDIT', true);
            });
        }

        /** 禁用后台主题安装 */
        if ($option_data['disable_themes_setup']) {
            add_action('init', function () {
                define('DISALLOW_FILE_MODS', true);
            });
        }
    }

    /**
     * 登录设置函数
     *
     * @since 1.0.0
     */
    public function wp_login()
    {
        $option_data = $this->option['login'] ?? [];
        $option_data = wp_parse_args($option_data, [
            'admin_role'            => '',
            'admin_token'           => '',
            'admin_login_redirect'  => '',
            'admin_logout_redirect' => '',
            'logo_title'            => '',
            'logo_link'             => '',
            'login_title'           => '',
            'login_header'          => '',
            'login_footer'          => ''
        ]);

        /** 设定允许登录后台的角色 */
        if ($option_data['admin_role']) {
            add_action('admin_init', function () {
                global $current_user;

                // 当前用户角色
                $current_user_role = $current_user->roles[0];

                // 允许登录的用户角色
                $can_login_user_role = explode(',', $this->option['login']['admin_role']);

                /** 判断登录角色 */
                if (!in_array($current_user_role, $can_login_user_role) && 'administrator' != $current_user_role) {
                    wp_redirect(home_url());
                    exit;
                }
            });
        }

        /** 登录界面跳转请求参数 */
        if ($option_data['admin_token']) {
            add_action('login_head', function () {

                // 设定转跳链接
                $redirect_to = $_GET['redirect_to'] ?? home_url();

                // 获取 token
                $token = $_GET['token'] ?? '';

                /** 判断 token 转跳 */
                if ($token != $this->option['login']['admin_token']) {
                    wp_redirect('/login?redirect_to=' . urlencode($redirect_to));
                    exit;
                }
            });
        }

        /** 登录转跳地址 */
        if ($option_data['admin_login_redirect']) {
            add_filter('login_redirect', function () {
                return $this->option['login']['admin_login_redirect'];
            });
        }

        /** 退出转跳地址 */
        if ($option_data['admin_logout_redirect']) {
            add_filter('logout_redirect', function () {
                return $this->option['login']['admin_logout_redirect'];
            });
        }

        /** 登录 LOGO 标题 */
        if ($option_data['logo_title']) {
            add_filter('login_headertext', function () {
                echo $this->option['login']['logo_title'];
            });
        }

        /** 登录 LOGO 链接 */
        if ($option_data['logo_link']) {
            add_filter('login_headerurl', function () {
                return $this->option['login']['logo_link'];
            });
        }

        /** 登录页面标题 */
        if ($option_data['login_title']) {
            add_filter('login_title', function () {
                return $this->option['login']['login_title'];
            });
        }

        /** 登录界面 Header 代码 */
        if ($option_data['login_header']) {
            add_action('login_head', function () {
                echo $this->option['login']['login_header'];
            });
        }

        /** 登录界面 Footer 代码 */
        if ($option_data['login_footer']) {
            add_action('login_footer', function () {
                echo $this->option['login']['login_footer'];
            });
        }
    }

    /**
     * 自定义界面函数
     *
     * @since 1.0.0
     */
    public function wp_interface()
    {
        $option_data = $this->option['interface'] ?? [];
        $option_data = wp_parse_args($option_data, [
            'custom_wp_header'      => '',
            'wp_footer_times'       => 0,
            'wp_footer_queries'     => 0,
            'custom_wp_footer'      => '',
            'admin_title'           => '',
            'admin_header'          => '',
            'admin_footer'          => '',
            'admin_footer_text'     => '',
            'admin_footer_queries'  => 0
        ]);

        /** 前台界面 Header 代码 */
        if ($option_data['custom_wp_header']) {
            add_action('wp_head', function () {
                echo $this->option['interface']['custom_wp_header'];
            });
        }

        /** 前台显示页面生成时间 */
        if ($option_data['wp_footer_times']) {
            add_action('wp_footer', function () {
                printf('<!-- 生成本页面用时 %.3f 秒，查询数据库 %d 次，占用 %.2fMB 内存 -->', timer_stop(0, 3), get_num_queries(), memory_get_peak_usage() / 1024 / 1024);
            });
        }

        /** 显示当前页面执行的所有 SQL 语句 */
        if ($option_data['wp_footer_queries']) {
            add_action('wp_footer', function () {
                global $wpdb;
                echo '<!-- ';
                print_r($wpdb->queries);
                echo ' -->';
            });
        }

        /** 前台界面 Footer 代码 */
        if ($option_data['custom_wp_footer']) {
            add_action('wp_footer', function () {
                echo $this->option['interface']['custom_wp_footer'];
            });
        }

        /** 后台标题 */
        if ($option_data['admin_title']) {
            add_filter('admin_title', function () {
                return $this->option['interface']['admin_title'];
            });
        }

        /** 后台界面 Header 代码 */
        if ($option_data['admin_header']) {
            add_action('admin_head', function () {
                echo $this->option['interface']['admin_header'];
            });
        }

        /** 显示后台页面执行的所有 SQL 语句 */
        if ($option_data['admin_footer_queries']) {
            add_action('admin_footer', function () {
                global $wpdb;
                echo '<!-- ';
                print_r($wpdb->queries);
                echo ' -->';
            });
        }

        /** 后台界面 Footer 代码 */
        if ($option_data['admin_footer']) {
            add_action('admin_footer', function () {
                echo $this->option['interface']['admin_footer'];
            });
        }

        /** 后台左下角文字信息 */
        if ($option_data['admin_footer_text']) {
            add_filter('admin_footer_text', function () {
                return $this->option['interface']['admin_footer_text'];
            });
        }

        /** 后台右下角文字信息 */
        add_filter('update_footer', function ($text) {

            $update_footer_core = $this->option['interface']['update_footer_core'] ?? 0;
            $update_footer_ver  = $this->option['interface']['update_footer_ver'] ?? '';

            // 显示页面生成时间信息
            if ($update_footer_core) {
                return sprintf('生成本页面用时 %.3f 秒，查询数据库 %d 次，占用 %.2fMB 内存', timer_stop(0, 3), get_num_queries(), memory_get_peak_usage() / 1024 / 1024);
            }

            // 右下角文字信息
            if ($update_footer_ver) {
                return $update_footer_ver;
            }

            return $text;
        }, 100, 1);
    }

    /**
     * 发布设置函数
     *
     * @since 1.0.0
     */
    public function wp_post()
    {
        $option_data = $this->option['post'] ?? [];
        $option_data = wp_parse_args($option_data, [
            'autosave'      => 0,
            'post_publish'  => [],
            'image_setup'   => []
        ]);

        $option_data_publish = wp_parse_args($option_data['post_publish'], [
            'link_manager'          => 0,
            'autosave_remote_image' => 0,
            'page_add_excerpt'      => 0,
            'use_classic_editor'    => 0,
            'remove_page_parent'    => 0
        ]);

        $option_data_imagesetup = wp_parse_args($option_data['image_setup'], [
            'size_threshold'    => 0,
            'size_other'        => 0
        ]);


        /** 禁用自动生成的图片尺寸 */
        add_action('intermediate_image_sizes_advanced', function ($sizes) {
            $option_data = $this->option['post']['image_size'] ?? [];
            $option_data = wp_parse_args($option_data, [
                'thumbnail'     => 0,
                'medium'        => 0,
                'medium_large'  => 0,
                'large'         => 0,
                '1536x1536'     => 0,
                '2048x2048'     => 0
            ]);

            if ($option_data['thumbnail']) {
                unset($sizes['thumbnail']);
            }

            if ($option_data['medium']) {
                unset($sizes['medium']);
            }

            if ($option_data['medium_large']) {
                unset($sizes['medium_large']);
            }

            if ($option_data['large']) {
                unset($sizes['large']);
            }

            if ($option_data['1536x1536']) {
                unset($sizes['1536x1536']);
            }

            if ($option_data['2048x2048']) {
                unset($sizes['2048x2048']);
            }

            return $sizes;
        });

        /** 禁用缩放尺寸 */
        if ($option_data_imagesetup['size_threshold']) {
            add_filter('big_image_size_threshold', '__return_false');
        }

        /** 禁用其他图片尺寸 */
        if ($option_data_imagesetup['size_other']) {
            add_action('init', function () {
                remove_image_size('another-size');
            });
        }

        /** 设置自动保存时间 */
        if ($option_data['autosave']) {
            define('AUTOSAVE_INTERVAL', $option_data['autosave']);
        }

        /** 启用链接菜单 */
        if ($option_data_publish['link_manager']) {
            add_filter('pre_option_link_manager_enabled', '__return_true');
        }

        /** 自动保存远程图片 */
        if ($option_data_publish['autosave_remote_image']) {
            // add_action('save_post', function ($post_id, $post) {

            //     if (!in_array($post->post_type, ['post', 'page'])) {
            //         return $post_id;
            //     }
            // });
        }

        add_action('after_setup_theme', function () {
            $option_data = $this->option['post'] ?? [];
            $option_data = wp_parse_args($option_data, [
                'thumbnail'     => '',
                'post_format'   => ''
            ]);

            /** 自定义网站标题 */
            add_theme_support('title-tag');

            /** 开启缩略图 */
            switch ($option_data['thumbnail']) {
                case 'all':
                    add_theme_support('post-thumbnails');
                    break;
                case 'both':
                    add_theme_support('post-thumbnails', ['post', 'page']);
                    break;
                case 'post':
                    add_theme_support('post-thumbnails', ['post']);
                    break;
                case 'page':
                    add_theme_support('post-thumbnails', ['page']);
                    break;
                default:
                    break;
            }

            /** 自定义文章类型 */
            if ($option_data['post_format']) {
                add_theme_support('post-formats', array_keys($option_data['post_format']));
            }
        });

        /** 页面添加摘要编辑栏 */
        if ($option_data_publish['page_add_excerpt']) {
            add_action('add_meta_boxes', function () {
                add_meta_box('postexcerpt', __('Excerpt'), [$this, 'metabox_page_excerpt'], 'page', 'advanced', 'core');
            });
        }

        /** 使用经典编辑器 */
        if ($option_data_publish['use_classic_editor']) {
            add_filter('use_block_editor_for_post', '__return_false');
        }

        /** 移除文章属性编辑栏 */
        if ($option_data_publish['remove_page_parent']) {
            add_action('admin_init', function () {
                remove_meta_box('pageparentdiv', ['page', 'post'], 'side');
            });
        }
    }

    /**
     * 保留菜单样式函数
     *
     * @since 1.0.0
     * @param array $classes
     */
    public function nav_menu_class_attributes_filter(array $classes)
    {
        $keep_menu_style = $this->option['themes']['keep_menu_style'] ?? '';

        /** 判断设置保留样式，转换为数组 */
        if ($keep_menu_style) {
            $keep_menu_style = explode("\n", $keep_menu_style);
            $classes = array_intersect($classes, $keep_menu_style);
        }

        /** 需要保留的 CSS 标签 */
        $classes = array_intersect($classes, [
            'current-menu-item',
            'icon',
            'current-menu-parent',
            'current-page-ancestor'
        ]);

        return  $classes;
    }

    /**
     * 保留 Body 样式函数
     *
     * @since 1.0.0
     * @param array $classes
     */
    public function body_class_attributes_filter(array $classes)
    {
        global $post;

        // 获取保留菜单样式
        $keep_body_style = $this->option['themes']['keep_body_style'] ?? '';

        /** 判断设置保留样式，转换为数组 */
        if ($keep_body_style) {
            $keep_body_style = explode("\n", $keep_body_style);
            $classes = array_intersect($classes, $keep_body_style);
        }

        // 返回样式交集
        $classes = array_intersect($classes, ['search', 'single', 'page', 'home', 'category']);

        // 页面添加样式
        if (is_page()) {
            $classes[] = 'site-page-' . $post->post_name;
        }

        // 移动端添加样式
        if (wp_is_mobile()) {
            $classes[] = 'site-mobile';
        }

        return $classes;
    }

    /**
     * 编辑页面添加摘要输入框函数
     *
     * @since 1.0.0
     * @param WP_Post $post
     */
    public function metabox_page_excerpt($post)
    {
        printf('<textarea class="form-control" name="excerpt" rows="4">%s</textarea>', $post->post_excerpt);
    }

    /**
     * 添加文章访问统计函数操作
     *
     * @since 1.0.0
     * @todo 添加 Redis 缓存
     */
    static function updated_post_view()
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

    /**
     * Undocumented function
     *
     * @param [type] $request
     * @return void
     */
    static function updated_option($request)
    {
        /** 判断保存 KEY */
        if (empty($_POST['option_key'])) {
            echo new WWPO_Error('error', '没有找到相关保存参数');
        }

        $option_data = get_option(self::OPTION_KEY, []);
        $option_data[$_POST['option_key']] = $_POST['updated'];

        // 更新到数据库
        update_option(self::OPTION_KEY, $option_data);

        echo new WWPO_Error('updated', '选项更新成功');
    }

    /**
     * WP 优化格式化 checkbox 函数
     *
     * @since 1.0.0
     * @param string    $checkbox_key   checkbox 内容数组 key
     * @param array     $checkbox_data  需要格式化的内容数组
     * @param array     $option_data    checkbox 保存值
     */
    private static function _format_checkbox($checkbox_key, $checkbox_data, $option_data)
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
                    'name'      => sprintf('updated[%s][%s]', $checkbox_key, $key),
                    'value'     => 1,
                    'checked'   => $option_data[$checkbox_key][$key] ?? 0
                ]
            ];
        }

        // 返回格式化好的数组
        return $checkbox;
    }
}
