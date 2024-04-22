<?php

/**
 * 注册后台菜单
 *
 * @since 1.0.0
 */
function wwpo_register_admin_menu()
{
    $webian_logo = 'PHN2ZyB0PSIxNTk2NTA0ODI0MDYzIiBjbGFzcz0iaWNvbiIgdmlld0JveD0iMCAwIDEwMjQgMTAyNCIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHAtaWQ9IjM5NjciIHdpZHRoPSI0OCIgaGVpZ2h0PSI0OCI+PHBhdGggZD0iTTMuNzY1IDIzMC44MjdsMjE2LjQ0NyA1NjIuMzQ4aDkwLjQ4OWwyMS42MTktNTMuMjA2LTE5My40MDQtNTA5LjE0MnpNNTI4Ljk1OSAyMzAuODI3aC05MS41MjRsLTEyMC45MTEgMzA1LjUxIDUyLjgxNyAxMjcuNjQyIDEwNC44NTgtMjU5LjY4NCAxMDMuNDM1IDI1OS42ODQgNzQuNTY1LTEyNy42NDJ6TTg5Ny43NzMgMjMwLjgyN2wtMjgyLjU5NyA1MDcuMDcwIDIzLjY5IDU1LjI3N2g3MS44NDdsMzA5LjUyNC01NjIuMzQ4eiIgcC1pZD0iMzk2OCIgZmlsbD0iI2ZmZmZmZiI+PC9wYXRoPjwvc3ZnPg==';

    $menus['webian-wordpress-one'] = [
        'menu_title'    => __('微边云', 'wwpo'),
        'label_title'   => __('插件设置', 'wwpo'),
        'svg'           => $webian_logo,
        'position'      => 101,
        'menu_order'    => 1
    ];

    /**
     * 注册后台菜单动作
     *
     * @since 1.0.0
     * @param array $menus
     * {
     *  后台菜单设置数组
     *  @var string $menu_title     菜单标题
     *  @var string $page_title     页面标题
     *  @var string $label_title    子页面标题
     *  @var string $icon           菜单使用 dashicons 图标
     *  @var string $svg            菜单使用 svg 图标
     *  @var string $menu_order     菜单位置
     *  @var string $role           菜单权限
     *  @var string $parent         父级菜单别名
     * }
     */
    $menus = apply_filters('wwpo_menus', $menus);

    /** 判断后台菜单内容数组 */
    if (empty($menus)) {
        return;
    }

    $menus = wp_list_sort($menus, 'menu_order', 'ASC', true);

    /**
     * 循环后台菜单内容
     *
     * @property string $menu_slug  菜单别名
     * @property array  $menu_data   菜单内容数组
     */
    foreach ($menus as $menu_slug => $menu_data) {

        $menu_data = wp_parse_args($menu_data, [
            'menu_title'    => __('未命名页面', 'wwpo'),
            'icon'          => 'menu',
            'role'          => WWPO_ROLE,
            'position'      => 101
        ]);

        $page_title = $menu_data['page_title'] ?? $menu_data['menu_title'];
        $menu_icon  = sprintf('dashicons-%s', $menu_data['icon']);

        /** 判断使用 svg 图标，svg 必须 base64 转码 */
        if (isset($menu_data['svg'])) {
            $menu_icon = sprintf('data:image/svg+xml;base64,%s', $menu_data['svg']);
        }

        /** 判断父级菜单别名，注册子菜单 */
        if (isset($menu_data['parent'])) {
            add_submenu_page($menu_data['parent'], $page_title, $menu_data['menu_title'], $menu_data['role'], $menu_slug, ['WWPO_Admin', 'display']);
            continue;
        }

        /**
         * 注册主菜单
         *
         * 主菜单在子菜单后注册，防止子菜单先注册
         * 主菜单注册后再注册「所有」标题菜单，否则主菜单会被覆盖无法显示
         */
        add_menu_page($page_title, $menu_data['menu_title'], $menu_data['role'], $menu_slug, ['WWPO_Admin', 'display'], $menu_icon, $menu_data['position']);

        /** 判断显示「所有」菜单标题
         * 用于列表页面，如：所有产品、所有订单等。
         */
        if (isset($menu_data['label_title'])) {
            add_submenu_page($menu_slug, $menu_data['label_title'], $menu_data['label_title'], $menu_data['role'], $menu_slug, ['WWPO_Admin', 'display']);
            continue;
        }
    }
}
add_action('admin_menu', 'wwpo_register_admin_menu');

/**
 * 注册后台 AJAX 保存操作函数
 *
 * @since 1.0.0
 * @param string $ajax      执行动作标识
 * @param string $pagenow   当前页面标识
 */
function wwpo_register_admin_ajax()
{
    // 获取 GET 和 POST 下传入参数
    $request = $_REQUEST;

    /** 验证 AJAX 随机数，以防止非法的外部的请求 */
    if (!check_ajax_referer($request['pagenow'], 'pagenonce')) {
        echo new WWPO_Error('error', 'invalid_nonce');
        exit;
    }

    /** 判断执行函数参数 */
    if (empty($request['ajax'])) {
        echo new WWPO_Error('error', 'not_found_action');
        exit;
    }

    // 设定 AJAX 传参
    $request['current_user_id'] = get_current_user_id();

    /**
     * 执行 AJAX 操作接口
     *
     * @since 1.0.0
     */
    do_action("wwpo_ajax_admin_{$request['ajax']}", $request);
    exit;
}
add_action('wp_ajax_wwpoupdatepost', 'wwpo_register_admin_ajax');

/**
 * 注册后台插件设置页面
 *
 * @since 1.0.0
 */
function wwpo_register_admin_display_settings()
{
    // 获取设置保存值
    $option_data = get_option(OPTION_SETTING_KEY);

    // 页面标签内容数组
    $array_admin_page['common'] = [
        'title'     =>  __('通用设置', 'wwpo'),
        'formdata'  => [
            'option_data[webfont]'  => [
                'title' => __('Webfont 地址', 'wwpo'),
                'field' => ['type' => 'text', 'value' => $option_data['webfont'] ?? '']
            ],
            'option_data[cdnurl]'  => [
                'title' => __('静态 CDN 地址', 'wwpo'),
                'field' => ['type' => 'text', 'value' => $option_data['cdnurl'] ?? '']
            ],
            'option_data[keywords]'  => [
                'title'     => __('SEO 搜索关键字', 'wwpo'),
                'field' => ['type' => 'textarea', 'value' => $option_data['keywords'] ?? '']
            ],
            'option_data[description]'  => [
                'title'     => __('SEO 简介', 'wwpo'),
                'field' => ['type' => 'textarea', 'value' => $option_data['description'] ?? '']
            ]
        ]
    ];

    /**
     * 设置页面内容接口
     *
     * @since 1.0.0
     * @api wwpo_admin_page_settings
     */
    $array_admin_page = apply_filters('wwpo_admin_page_settings', $array_admin_page);

    echo '<main class="wwpo__admin-body">';
    echo '<form id="wwpo-admin-form" class="wwpo__admin-content" method="POST" autocomplete="off">';

    echo WWPO_Form::hidden(['option_key' => OPTION_SETTING_KEY]);

    foreach ($array_admin_page as $admin_page) {
        // 显示设置表单
        echo WWPO_Form::table($admin_page);
    }

    // echo WWPO_Button::submit('updatesettings');
    echo '</form>';
    echo '<aside class="wwpo__admin-toc"><h4>页面导航</h4><ul>';
    foreach ($array_admin_page as $admin_page) {
        printf('<li><a href="#%1$s" rel="anchor">%1$s</a></li>', $admin_page['title']);
    }
    echo '</ul></aside>';
    echo '</main>';
}
add_action('wwpo_admin_display_webian-wordpress-one', 'wwpo_register_admin_display_settings');


/**
 * 设置保存操作函数
 *
 * @since 1.0.0
 */
function wwpo_register_ajax_update_settings()
{
    /** 判断保存 KEY */
    if (empty($_POST['option_key'])) {
        echo new WWPO_Error('error', 'no_option_key');
        exit;
    }

    // 更新到数据库
    update_option($_POST['option_key'], $_POST['option_data']);

    // 设定日志
    wwpo_logs('admin:post:updatesettings:' . $_POST['option_key']);

    // 返回信息
    echo new WWPO_Error('updated', 'updated_success');
}
add_action('wwpo_ajax_updatesettings', 'wwpo_register_ajax_update_settings');

/**
 * 注册加载模块
 *
 * @since 1.0.0
 */
function wwpo_register_active_modules()
{
    // 获取模块目录
    $modules_list = WWPO_File::dir(WWPO_PLUGIN_PATH . 'modules');

    if (empty($modules_list)) {
        return;
    }

    foreach ($modules_list as $modules_dir) {

        // 设定模块别名和模块 autoload.php 文件路径
        $modules_autoload = $modules_dir . DIRECTORY_SEPARATOR . 'autoload.php';

        // 判断 autoload.php 文件为空
        if (!file_exists($modules_autoload)) {
            continue;
        }

        require $modules_autoload;
    }
}
add_action('wwpo_init', 'wwpo_register_active_modules');

/**
 * 注册 REST API 组件
 *
 * @since 1.0.0
 */
function wwpo_register_rest_route()
{

    $controller = new WWPO_REST_User_Controller();
    $controller->register_routes();
}
add_action('rest_api_init', 'wwpo_register_rest_route');

/**
 * 注册国际化主题定义语言环境
 *
 * @since 1.0.0
 */
function wwpo_register_theme_textdomain()
{
    load_plugin_textdomain('wwpo', false, WWPO_PLUGIN_PATH . '/languages');
}
add_action('after_setup_theme', 'wwpo_register_theme_textdomain');

/**
 * 添加后台样式和脚本
 *
 * @since 1.0.0
 */
function wwpo_register_admin_scripts()
{
    global $current_screen;

    /** 启用 WEBUI 框架 */
    wp_enqueue_script('wwpo', WWPO_PLUGIN_URL . '/assets/js/wwpo.min.js', ['jquery', 'underscore'], NOW, true);

    /**
     * 设定本地化参数值
     *
     * @var string  ajaxurl     RestAPI 地址
     * @var string  nonce       Rest 请求随机数
     * @var string  pagenow     当前页面
     * @var string  pagenonce   当前页面随机数
     * @var boolean debug       开发模式
     */
    $localize_script = [
        'ajaxurl'   => home_url('wp-json'),
        'nonce'     => wp_create_nonce('wp_rest'),
        'pagenow'   => $current_screen->id,
        'pagenonce' => wp_create_nonce($current_screen->id),
        'debug'     => WP_DEBUG
    ];

    /**
     * 自定义后台本地化参数接口
     *
     * @since 1.0.0
     */
    $wwpoSettings = apply_filters('wwpo_admin_script', $localize_script);

    // 应用后台本地化脚本
    wp_localize_script('wwpo', 'wwpoSettings', $wwpoSettings);

    /** 启用后台样式*/
    wp_enqueue_style('wwpo-style', WWPO_PLUGIN_URL . '/assets/css/wwpo.min.css', null, NOW);
}
add_action('admin_enqueue_scripts', 'wwpo_register_admin_scripts');

/**
 * 在插件页面上添加插件元链接
 *
 * @since 2.0.0
 * @param string[]  $plugin_meta    插件元数据的数组.
 * @param string    $plugin_file    插件文件相对于插件目录的路径
 */
function wwpo_register_plugin_row_meta($plugin_meta, $plugin_file)
{
    if ($plugin_file != WWPO_PLUGIN_NAME) {
        return $plugin_meta;
    }

    $plugin_meta[] = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url('admin.php?page=wwpo-docs')),
        esc_html__('开发文档', 'wwpo')
    );

    return $plugin_meta;
}
add_filter('plugin_row_meta', 'wwpo_register_plugin_row_meta', 10, 2);

/**
 * 将设置页面添加到插件页面上的插件操作链接
 *
 * @since 2.0.0
 * @param string[] $actions 当前插件操作链接。
 */
function wwpo_register_plugin_actions_links($actions)
{
    $links['wwpo-setting'] = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url('admin.php?page=webian-wordpress-one')),
        esc_html__('设置', 'wwpo')
    );

    return array_merge($links, $actions);
}
add_filter(sprintf('%splugin_action_links_%s', is_multisite() ? 'network_admin_' : '',  WWPO_PLUGIN_NAME), 'wwpo_register_plugin_actions_links');
