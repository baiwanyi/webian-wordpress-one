<?php

/**
 * 核心内容方法类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */

final class WWPO_Core
{
    /**
     * 设定类实例
     *
     * @since 1.0.0
     * @var WWPO_Core $instance 类实例参数
     */
    static protected $instance;

    /**
     * 设置参数数组
     *
     * @since 1.0.0
     * @var array
     */
    public $option_data;

    /**
     * 构造函数
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        // 获取设置参数内容
        $this->option_data = get_option('wwpo-settings-common', []);
    }

    /**
     * 在对象克隆上抛出错误
     *
     * 只能拥有一个实例对象，禁止克隆。
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, esc_html__('禁止克隆实例', 'wwpo'), '1.0');
    }

    /**
     * 禁止反序列化类的实例
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, esc_html__('禁止反序列化实例', 'wwpo'), '1.0');
    }

    /**
     * 初始化动作加载函数
     * 确保在同一时间内内存中只存在一个实例。避免到处都需要定义全局变量。
     *
     * @since 1.0.0
     */
    static function init()
    {
        /** 判断实例内容，加载插件模块和钩子 */
        if (empty(self::$instance) && !self::$instance instanceof WWPO_Core) {
            self::$instance = new WWPO_Core();

            /**
             * 注册统计分析
             *
             * @since 1.0.0
             */
            add_action('wp_head', [self::$instance, 'analytics']);

            /**
             * 注册广告
             *
             * @since 1.0.0
             */
            add_action('wp_head', [self::$instance, 'adsense']);

            /**
             * 注册后台 POST 动作
             *
             * @since 1.0.0
             */
            add_action('admin_init', [self::$instance, 'admin_post']);

            /**
             * 注册后台菜单动作
             *
             * @since 1.0.0
             */
            add_action('admin_menu', [self::$instance, 'register_admin_menu']);

            /**
             * 注册后台 AJAX 动作
             *
             * @since 1.0.0
             */
            add_action('wp_ajax_wwpoupdatepost', [self::$instance, 'register_admin_ajax']);

            /**
             * 注册样式
             *
             * @since 1.0.0
             */
            add_action('init', [self::$instance, 'register_scripts']);

            /**
             * 注册前台样式
             *
             * @since 1.0.0
             */
            add_action('wp_enqueue_scripts', [self::$instance, 'wp_enqueue_scripts']);

            /**
             * 注册后台样式
             *
             * @since 1.0.0
             */
            add_action('admin_enqueue_scripts', [self::$instance, 'admin_enqueue_scripts']);

            /**
             * 注册 WWPO 菜单
             *
             * @since 1.0.0
             */
            add_filter('wwpo_menus', [self::$instance, 'admin_menus']);

            /**
             * 本地化主题语言
             *
             * @since 1.0.0
             */
            add_action('after_setup_theme', [self::$instance, 'load_theme_textdomain']);

            /**
             * 加载激活模块
             *
             * @since 1.0.0
             */
            self::$instance->active_modules();
        }
    }

    /**
     * 加载模块
     *
     * @since 1.0.0
     */
    public function active_modules()
    {
        $modules_data = get_option('wwpo-active-modules');

        if (empty($modules_data)) {
            return;
        }

        foreach ($modules_data as $modules_key => $modules) {

            if (empty($modules['enable'])) {
                continue;
            }

            $modules_autoload = sprintf('%s/%s/autoload.php', WWPO_MOD_PATH, $modules_key);

            if (file_exists($modules_autoload)) {
                require $modules_autoload;
            }
        }
    }

    /**
     * 注册后台菜单
     *
     * @since 1.0.0
     */
    public function register_admin_menu()
    {
        global $wwpo_admins;

        /**
         * 注册后台菜单动作
         *
         * @since 1.0.0
         * @param array $wwpo_admins
         * {
         *  后台菜单设置数组
         *  @var string    $menu_title      菜单标题
         *  @var string    $page_title      页面标题
         *  @var string    $icon            菜单使用 dashicons 图标
         *  @var string    $svg             菜单使用 svg 图标
         *  @var string    $menu_order      菜单位置
         *  @var string    $role            菜单权限
         *  @var array     $submenu
         *  {
         *      子菜内容数组
         *      @var string   $menu_title 子菜单标题
         *      @var string   $page_title 子菜单页面标题
         *  }
         * }
         */
        $wwpo_admins = apply_filters('wwpo_menus', $wwpo_admins);

        /** 判断后台菜单内容数组 */
        if (empty($wwpo_admins)) {
            return;
        }

        $current_page = WWPO_Admin::page_name();

        $wwpo_admins = wp_list_sort($wwpo_admins, 'menu_order', 'ASC', true);

        if (isset($wwpo_admins[$current_page]['sidebar'])) {
            add_filter('admin_body_class', function () {
                return 'wwpo-webui-sidebar';
            });
        }

        /**
         * 循环后台菜单内容
         *
         * @property string $menu_slug  菜单别名
         * @property array  $menu_val   菜单内容数组
         */
        foreach ($wwpo_admins as $menu_slug => $menu_val) {

            // 设定菜单标题默认值
            $menu_title = $menu_val['menu_title'] ?? __('未命名页面', 'wwpo');

            // 设定菜单参数默认值
            $page_title     = $menu_val['page_title'] ?? $menu_title;
            $menu_role      = $menu_val['role'] ?? WWPO_ROLE;
            $menu_icon      = sprintf('dashicons-%s', $menu_val['icon'] ?? 'menu');
            $menu_position  = $menu_val['position'] ?? 101;

            /** 判断使用 svg 图标，svg 必须 base64 转码 */
            if (isset($menu_val['svg'])) {
                $menu_icon = sprintf('data:image/svg+xml;base64,%s', $menu_val['svg']);
            }

            /** 判断父级菜单别名，注册子菜单 */
            if (isset($menu_val['parent'])) {
                add_submenu_page($menu_val['parent'], $page_title, $menu_title, $menu_role, $menu_slug, [self::$instance, 'admin_page']);
                continue;
            }

            /**
             * 注册主菜单
             *
             * 主菜单在子菜单后注册，防止子菜单先注册
             * 主菜单注册后再注册「所有」标题菜单，否则主菜单会被覆盖无法显示
             */
            add_menu_page($page_title, $menu_title, $menu_role, $menu_slug, [self::$instance, 'admin_page'], $menu_icon, $menu_position);

            /** 判断显示「所有」菜单标题
             * 用于列表页面，如：所有产品、所有订单等。
             */
            if (isset($menu_val['all_item_title'])) {
                add_submenu_page($menu_slug, $menu_val['all_item_title'], $menu_val['all_item_title'], $menu_role, $menu_slug, [self::$instance, 'admin_page']);
                continue;
            }
        }
    }

    /**
     * 后台 AJAX 保存操作函数
     *
     * @since 1.0.0
     * @param string $ajax      执行动作标识
     * @param string $pagenow   当前页面标识
     */
    public function register_admin_ajax()
    {
        // 获取 GET 和 POST 下传入参数
        $request = $_REQUEST;

        /** 验证 AJAX 随机数，以防止非法的外部的请求 */
        if (!check_ajax_referer($request['pagenow'], 'pagenonce')) {
            echo new WWPO_Error('invalid_nonce');
            exit;
        }

        /** 判断执行函数参数 */
        if (empty($request['ajax'])) {
            echo new WWPO_Error('not_found_action');
            exit;
        }

        // 设定 AJAX 传参
        $request['current_user_id'] = get_current_user_id();

        /**
         * 主题 AJAX 操作接口
         *
         * @since 1.0.0
         */
        do_action("wwpo_ajax_admin_{$request['ajax']}", $request);
        exit;
    }

    /**
     * 注册脚本和样式
     *
     * @since 1.0.0
     */
    public function register_scripts()
    {
        // 获取设置保存值
        $webfont_url    = $this->option_data['webfont'] ?? '';
        $cdn_url        = $this->option_data['cdnurl'] ?? '';
        $cdn_url        = get_stylesheet_directory_uri() . '/assets';

        // 注册 webfont 样式
        if ($webfont_url) {
            wp_register_style('wwpo-webfont', $webfont_url, null, null, 'all');
        }

        /** 注册前台样式 */
        if ($cdn_url) {
            // wp_register_style('wwpo-webfont', $cdn_url . '/webfont/iconfont.css', null, null, 'all');
            wp_register_script('webui-cdn', $cdn_url . '/js/webui.min.js', null, WWPO_VER, true);
            wp_register_style('wwpo-style', $cdn_url . '/style.css', null, WWPO_VER, 'all');
        } else {
            wp_register_script('webui-cdn', '//cdn.webian.cn/webui/webui.min.js', null, WWPO_VER, true);
            wp_register_style('wwpo-style', WWPO_TMPL_URL . '/style.css', null, WWPO_VER, 'all');
        }
    }

    /**
     * 添加前台样式和脚本
     *
     * @since 1.0.0
     */
    public function wp_enqueue_scripts()
    {
        /** 启用 WEBUI 框架 */
        wp_enqueue_script('webui-cdn');

        /**
         * 设定本地化参数值
         *
         * @var string  ajaxurl RestAPI 地址
         * @var string  nonce   Rest 请求随机数
         * @var string  pagenow 当前页面
         * @var boolean debug   开发模式
         */
        $localize_script = [
            'ajaxurl'   => home_url('wp-json'),
            'nonce'     => wp_create_nonce('wp_rest'),
            'debug'     => WP_DEBUG
        ];

        /**
         * 自定义前台本地化参数接口
         *
         * @since 1.0.0
         */
        $webuiSettings = apply_filters('wwpo_page_webui', $localize_script);

        // 应用前台本地化脚本
        wp_localize_script('webui-cdn', 'webuiSettings', $webuiSettings);

        /** 启用站点样式 */
        wp_enqueue_style('wwpo-webfont');
        wp_enqueue_style('wwpo-style');
    }

    /**
     * 添加后台样式和脚本
     *
     * @since 1.0.0
     */
    public function admin_enqueue_scripts()
    {
        global $current_screen;

        /** 启用 WEBUI 框架 */
        wp_enqueue_script('weadminui', '//cdn.webian.cn/webui/weadminui.min.js', ['jquery', 'underscore'], null, true);

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

        $mapapi = $this->option_data['mapapi'] ?? '';

        if ($mapapi) {
            $localize_script['mapapi'] = $mapapi;
        }

        /**
         * 自定义后台本地化参数接口
         *
         * @since 1.0.0
         */
        $webuiSettings = apply_filters('wwpo_admin_webui', $localize_script);

        // 应用后台本地化脚本
        wp_localize_script('weadminui', 'webuiSettings', $webuiSettings);

        /** 启用后台样式*/
        wp_enqueue_style('weadminui', '//cdn.webian.cn/webui/weadminui.min.css', null, null, 'all');
    }

    /**
     * 后台管理菜单
     *
     * @since 1.0.0
     */
    public function admin_menus($menus)
    {
        $webian_logo = 'PHN2ZyB0PSIxNTk2NTA0ODI0MDYzIiBjbGFzcz0iaWNvbiIgdmlld0JveD0iMCAwIDEwMjQgMTAyNCIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHAtaWQ9IjM5NjciIHdpZHRoPSI0OCIgaGVpZ2h0PSI0OCI+PHBhdGggZD0iTTMuNzY1IDIzMC44MjdsMjE2LjQ0NyA1NjIuMzQ4aDkwLjQ4OWwyMS42MTktNTMuMjA2LTE5My40MDQtNTA5LjE0MnpNNTI4Ljk1OSAyMzAuODI3aC05MS41MjRsLTEyMC45MTEgMzA1LjUxIDUyLjgxNyAxMjcuNjQyIDEwNC44NTgtMjU5LjY4NCAxMDMuNDM1IDI1OS42ODQgNzQuNTY1LTEyNy42NDJ6TTg5Ny43NzMgMjMwLjgyN2wtMjgyLjU5NyA1MDcuMDcwIDIzLjY5IDU1LjI3N2g3MS44NDdsMzA5LjUyNC01NjIuMzQ4eiIgcC1pZD0iMzk2OCIgZmlsbD0iI2ZmZmZmZiI+PC9wYXRoPjwvc3ZnPg==';

        $menus['webian-wordpress-one'] = [
            'menu_title'        => __('微边云', 'wwpo'),
            'all_item_title'    => __('插件设置', 'wwpo'),
            'description'       => __('优化设置让你通过关闭一些不常用的功能来加快 WordPress 的加载。但是某些功能的关闭可能会引起一些操作无法执行，详细介绍请点击：<a href="https://blog.wpjam.com/m/wpjam-basic-optimization-setting/" target="_blank">优化设置</a>。', 'wwpo'),
            'svg'               => $webian_logo,
            'position'          => 101,
            'menu_order'        => 1
        ];

        $menus['wwpo-modules'] = [
            'parent'        => 'webian-wordpress-one',
            'menu_title'    => __('模块管理', 'wwpo'),
            'menu_order'    => 10
        ];

        $menus['wwpo-logs'] = [
            'parent'        => 'webian-wordpress-one',
            'menu_title'    => __('操作日志', 'wwpo'),
            'role'          => 'edit_posts',
            'menu_order'    => 11
        ];

        return $menus;
    }

    /**
     * 后台页面主体内容
     *
     * @since 1.0.0
     */
    public function admin_page()
    {
        $page_name      = WWPO_Admin::page_name(true);
        $page_action    = WWPO_Admin::action();
        $page_tabs      = WWPO_Admin::tabs();
        $wwpo_admin     = new WWPO_Admin();

        $wwpo_admin->admin_sidebar();
        echo '<div class="wrap">';
        $wwpo_admin->admin_header();
        $wwpo_admin->admin_message();
        echo '<main class="container-fluid p-0">';

        /**
         * 后台页面接口（公共接口）
         *
         * @since 1.0.0
         * @property string $page_name 页面别名
         */
        do_action('wwpo_admin_display', $page_name);

        /**
         * 后台页面接口（页面别名）
         *
         * @since 1.0.0
         * @property string $page_action    页面动作
         * @property string $page_tabs      页面标签
         */
        do_action("wwpo_admin_display_{$page_name}", $page_action, $page_tabs);

        /**
         * 后台页面接口（页面别名 + 页面动作）
         *
         * @since 1.0.0
         */
        do_action("wwpo_admin_display_{$page_name}_{$page_action}");

        /**
         * 后台页面接口（页面别名 + 页面标签）
         *
         * @since 1.0.0
         */
        do_action("wwpo_admin_display_{$page_name}_{$page_tabs}");

        echo '</main>';
        $wwpo_admin->admin_footer();
        echo '</div>';
    }

    /**
     * Undocumented function
     *
     * @since 1.0.0
     */
    public function admin_post()
    {
        if (empty($_POST['submit'])) {
            return;
        }

        do_action("wwpo_post_admin_{$_POST['submit']}");
    }

    /**
     * 国际化主题定义语言环境
     *
     * @since 1.0.0
     */
    public function load_theme_textdomain()
    {
        load_plugin_textdomain('wwpo', false, WWPO_DIR_NAME . '/languages');
    }

    /**
     * 统计分析函数
     *
     * @since 1.0.0
     */
    public function analytics()
    {
        $analytics_type = $this->option_data['analytics']['type'] ?? 'close';
        $analytics_code = $this->option_data['analytics']['code'] ?? '';

        if ('close' == $analytics_type || empty($analytics_code)) {
            return;
        }

        if ('baidu' == $analytics_type) {
            printf('<script>var _hmt = _hmt || [];(function() {var hm = document.createElement("script");hm.src = "https://hm.baidu.com/hm.js?%s";var s = document.getElementsByTagName("script")[0];s.parentNode.insertBefore(hm, s);})();</script>', $analytics_code);
        }

        if ('google' == $analytics_type) {
            printf('<script async src="https://www.googletagmanager.com/gtag/js?id=%1$s"></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \'%1$s\');</script>', $analytics_code);
        }
    }

    /**
     * 广告函数
     *
     * @since 1.0.0
     */
    public function adsense()
    {
        $adsense_type = $this->option_data['adsense']['type'] ?? 'close';
        $adsense_code = $this->option_data['adsense']['code'] ?? '';

        if ('close' == $adsense_type || empty($adsense_code)) {
            return;
        }

        if ('baidu' == $adsense_type) {
            return;
        }

        if ('google' == $adsense_type) {
            printf('<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=%s" crossorigin="anonymous"></script>', $adsense_code);
        }
    }
}
