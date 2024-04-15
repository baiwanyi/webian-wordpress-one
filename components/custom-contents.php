<?php

/**
 * 自定义文章/分类生成操作类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */
class WWPO_Custom
{
    /**
     * 自定义内容选项值
     *
     * @since 1.0.0
     * @var array
     */
    public $option_data = [];

    /**
     * 设定类实例
     *
     * @since 1.0.0
     * @var WWPO_Custom $instance 类实例参数
     */
    static protected $instance;

    /**
     * 构造函数
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        // 获取自定义内容保存选项值
        $this->option_data = get_option(OPTION_CONTENTS_KEY, []);
    }

    /**
     * 初始化动作加载函数
     *
     * @since 1.0.0
     */
    static function init()
    {
        /** 判断实例内容，加载插件模块和钩子 */
        if (empty(self::$instance) && !self::$instance instanceof WWPO_Custom) {
            self::$instance = new WWPO_Custom();

            /**
             * 注册自定义文章类型
             *
             * @since 1.0.0
             */
            add_action('init', [self::$instance, 'load_post_type'], 0);

            /**
             * 注册自定义文章分类
             *
             * @since 1.0.0
             */
            add_action('init', [self::$instance, 'load_taxonomy'], 0);

            /**
             * 注册自定义菜单
             *
             * @since 1.0.0
             */
            add_action('after_setup_theme', [self::$instance, 'load_wp_menu']);
        }
    }

    /**
     * 格式化注册自定义文章
     *
     * 生成适用于 WordPress 注册自定义文章的数组
     *
     * @since 1.0.0
     * @param array     $option_data
     * {
     *  设置参数数组
     *  @var string   $title                 文章类型标题
     *  @var string   $slug                  文章类型别名
     *  @var string   $name_menu             菜单标题
     *  @var string   $name_adminbar         顶部导航标题
     *  @var string   $taxonomies            使用分类法别名，多个分类法别名用「,」分隔
     *  @var boolean  $hierarchical          使自定义文章类型支持层级方法。是（类似页面），否（类似文章）
     *  @var string   $capabilities          权限类型。page 或 post
     *  @var boolean  $can_export            支持文章类型导出
     *  @var boolean  $exclude_from_search   从搜索中排除
     *  @var boolean  $delete_with_user      随用户删除
     *  @var array    $restapi               Rest API 数组
     *  @var array    $visibility
     *  {
     *      文章类型可见性数组
     *      @var integer menu_position       菜单位置
     *      @var string  menu_icon           菜单图标
     *      @var boolean public              设置可见性
     *      @var boolean show_ui             在界面上显示，总开关，控制 show_in_menu 、 show_in_admin_bar 和 show_in_nav_menus
     *      @var boolean show_in_menu        在菜单上显示，总开关，控制 show_in_admin_bar 和 show_in_nav_menus
     *      @var boolean show_in_admin_bar   在顶部导航显示
     *      @var boolean show_in_nav_menus   在菜单上显示
     *  }
     *  @var array    $query
     *  {
     *      查询选项
     *      @var boolean publicly_queryable      公开查询
     *      @var integer query_var               查询方式：0.注册文章类型关键字 1.自定义查询参数
     *      @var string  custom_query_variable   自定义查询参数
     *  }
     *  @var array    $rewrite
     *  {
     *      永久链接
     *      @var integer type        永久链接类型：0.关闭永久链接 1.注册文章类型关键字 2.自定义 URL 别名
     *      @var string  slug        自定义 URL 别名
     *      @var boolean with_front  添加分类法别名前缀
     *      @var boolean pages       启用分页页码
     *      @var boolean feeds       启用 Feeds
     *  }
     *  @var array    $has_archive
     *  {
     *      开启归档
     *      @var integer type    开启归档类型：0.关闭归档 1.使用文章类型关键字 2.自定义归档别名
     *      @var string  slug    自定义归档别名
     *  }
     *  @var array    $supports
     *  {
     *      编辑页面支持的系统表单内容
     *      title             标题
     *      editor            编辑器
     *      author            作者
     *      thumbnail         特色图像
     *      excerpt           摘要
     *      trackbacks        Trackbacks
     *      custom-fields     自定义样式
     *      comments          评论
     *      revisions         修订版
     *      page-attributes   页面属性
     *      post-formats      文章格式
     *  }
     * }
     */
    private function register_post_type($option_data)
    {
        $post_type_title    = $option_data['title'];
        $post_type_option   = $option_data['option'] ?? [];
        $name_menu          = empty($post_type_option['menu']['name_menu']) ? $post_type_title : $post_type_option['menu']['name_menu'];
        $name_adminbar      = empty($post_type_option['menu']['name_adminbar']) ? $post_type_title : $post_type_option['menu']['name_adminbar'];
        $menu_position      = empty($post_type_option['menu']['position']) ? 7 : $post_type_option['menu']['position'];
        $menu_icon          = empty($post_type_option['menu']['icon']) ? 'dashicons-wordpress' : $post_type_option['menu']['icon'];

        // 设定 Rest API 内容数组
        $customdata = $this->rest_api($post_type_option['restapi']);

        // 合并内容数组
        $customdata = array_merge($customdata,  [
            'label'                 => $post_type_title,
            'labels'                => $this->post_type_label($post_type_title, $name_menu, $name_adminbar),
            'description'           => $post_type_option['desc'] ?? '',
            'menu_position'         => $menu_position,
            'menu_icon'             => $menu_icon,
            'capability_type'       => $post_type_option['capability'] ?? 'post',
            'public'                => empty($post_type_option['visibility']['public']) ? false : true,
            'show_ui'               => empty($post_type_option['visibility']['show_ui']) ? false : true,
            'show_in_menu'          => empty($post_type_option['visibility']['show_in_menu']) ? false : true,
            'show_in_admin_bar'     => empty($post_type_option['visibility']['show_in_admin_bar']) ? false : true,
            'show_in_nav_menus'     => empty($post_type_option['visibility']['show_in_nav_menus']) ? false : true,
            'publicly_queryable'    => empty($post_type_option['visibility']['publicly_queryable']) ? false : true,
            'hierarchical'          => empty($post_type_option['hierarchical']) ? false : true,
            'can_export'            => empty($post_type_option['can_export']) ? false : true,
            'exclude_from_search'   => empty($post_type_option['exclude_from_search']) ? false : true,
            'delete_with_user'      => empty($post_type_option['delete_with_user']) ? false : true
        ]);

        // 设定元数据操作框显示函数名
        $register_metabox_callback = sprintf('wwpo_metabox_%s', $option_data['slug']);

        /** 判断设定元数据操作框 */
        if (function_exists($register_metabox_callback)) {
            $customdata['register_meta_box_cb'] = $register_metabox_callback;
        }

        /** 设定永久链接选项 */
        if (empty($post_type_option['rewrite']['enable'])) {
            $customdata['rewrite'] = false;
        } else {

            if (empty($post_type_option['rewrite']['slug'])) {
                $customdata['rewrite']['slug'] = true;
            } else {
                $customdata['rewrite']['slug'] = $post_type_option['rewrite']['slug'];
            }

            $customdata['rewrite']['with_front']    = empty($post_type_option['rewrite']['with_front']) ? false : true;
            $customdata['rewrite']['pages']         = empty($post_type_option['rewrite']['pages']) ? false : true;
        }

        /** 设定编辑页面支持的系统表单内容 */
        if (!empty($post_type_option['supports'])) {
            $customdata['supports'] = array_keys($post_type_option['supports']);
        }

        /** 设定关联已经注册的分类法，其他自定义分类法需要使用 register_taxonomy 注册 */
        if (!empty($post_type_option['taxonomies'])) {
            $customdata['taxonomies'] = array_keys($post_type_option['taxonomies']);
        }

        /**
         * 设置自定义文章类型存档，默认关闭
         *
         *  - 1.使用默认别名
         *  - 2.使用自定义归档别名
         */
        if (empty($post_type_option['has_archive']['enable'])) {
            $customdata['has_archive'] = false;
        } else {

            if (empty($post_type_option['has_archive']['slug'])) {
                $customdata['has_archive'] = true;
            } else {
                $customdata['has_archive'] = $post_type_option['has_archive']['slug'];
            }
        }

        // 返回内容数组
        return $customdata;
    }

    /**
     * 格式化自定义分类法
     *
     * 生成适用于 WordPress 自定义分类法的数组
     *
     * @since 1.0.0
     *
     * @param array     $option_data
     * {
     *  设置参数数组
     *  @var string   $title                 分类法标题
     *  @var string   $slug                  分类法别名
     *  @var string   $name_menu             菜单标题
     *  @var boolean  $hierarchical          分级分类法允许层级。是（类似分类），否（类似标签）
     *  @var boolean  $update_count_callback 统计回调函数
     *  @var array    $visibility
     *  {
     *      分类法可见性数组
     *      @var boolean public              设置可见性
     *      @var boolean show_ui             在界面上显示，总开关，控制以下四项内容
     *      @var boolean show_admin_column   在列表上显示
     *      @var boolean show_in_nav_menus   在导航菜单显示
     *      @var boolean show_tagcloud       在标签云显示
     *      @var boolean show_in_quick_edit  在快速编辑显示
     *  }
     *  @var array    $query
     *  {
     *      查询选项
     *      @var boolean publicly_queryable      公开查询
     *      @var integer query_var               查询方式：0.注册分类法关键字 1.自定义查询参数
     *      @var string  custom_query_variable   自定义查询参数
     *  }
     *  @var array    $rewrite
     *  {
     *      永久链接
     *      @var integer type            永久链接类型：0.关闭永久链接 1.注册分类法关键字 2.自定义 URL 别名
     *      @var string  slug            自定义 URL 别名
     *      @var boolean with_front      使用分类法别名前缀
     *      @var boolean hierarchical    运行层级 URL
     *  }
     * }
     * @return array
     */
    public function register_taxonomy($option_data)
    {
        $taxonomy_title     = $option_data['title'];
        $taxonomy_option    = $option_data['option'] ?? [];
        $customdata         = $this->rest_api($taxonomy_option['restapi']);
        $customdata         = array_merge($customdata,  [
            'labels'                => $this->taxonomy_label($taxonomy_title, $taxonomy_option['name_menu'] ?? ''),
            'hierarchical'          => empty($taxonomy_option['hierarchical']) ? false : true,
            'public'                => empty($taxonomy_option['visibility']['public']) ? false : true,
            'show_ui'               => empty($taxonomy_option['visibility']['show_ui']) ? false : true,
            'show_admin_column'     => empty($taxonomy_option['visibility']['show_admin_column']) ? false : true,
            'show_in_nav_menus'     => empty($taxonomy_option['visibility']['show_in_nav_menus']) ? false : true,
            'show_tagcloud'         => empty($taxonomy_option['visibility']['show_tagcloud']) ? false : true,
            'show_in_quick_edit'    => empty($taxonomy_option['visibility']['show_in_quick_edit']) ? false : true,
            'publicly_queryable'    => empty($taxonomy_option['visibility']['publicly_queryable']) ? false : true
        ]);

        /** 设定统计数量回调函数名 */
        if (!empty($taxonomy_option['update_count_callback'])) {
            $customdata['update_count_callback'] = $taxonomy_option['update_count_callback'];
        }

        /** 设定后台编辑回调函数名 */
        if (!empty($taxonomy_option['meta_box_cb'])) {
            $customdata['meta_box_cb'] = $taxonomy_option['meta_box_cb'];
        }

        /** 判断使用后台编辑回调函数 */
        if (empty($taxonomy_option['callback'])) {
            $customdata['meta_box_cb'] = false;
        }

        /** 设定永久链接选项 */
        if (empty($taxonomy_option['rewrite']['enable'])) {
            $customdata['rewrite'] = false;
        } else {

            if (empty($taxonomy_option['rewrite']['slug'])) {
                $customdata['rewrite'] = true;
            } else {
                $customdata['rewrite']['slug'] = $taxonomy_option['rewrite']['slug'];
            }

            $customdata['rewrite']['with_front']    = empty($taxonomy_option['rewrite']['with_front']) ? false : true;
            $customdata['rewrite']['hierarchical']  = empty($taxonomy_option['rewrite']['hierarchical']) ? false : true;
        }

        // 返回自定义内容数组
        return $customdata;
    }

    /**
     * 自定义文章类型标签
     *
     * @since 1.0.0
     *
     * @param string    $post_type_title    自定义文章类型标题
     * @param string    $name_menu          菜单标题名称
     * @param string    $name_adminbar      顶部管理菜单名称
     * @return array
     *
     * @todo 本地化语言格式
     */
    private function post_type_label($post_type_title, $name_menu, $name_adminbar)
    {
        $post_type_label = [];
        $post_type_label = apply_filters('wwpo_post_type_label', $post_type_label);
        $post_type_label = wp_parse_args($post_type_label, [
            'singular_name'         => $post_type_title,
            'name'                  => $post_type_title,
            'menu_name'             => $name_menu,
            'name_admin_bar'        => $name_adminbar,
            'add_new'               => __('Add'),
            'not_found'             => '没有找到',
            'not_found_in_trash'    => '回收站中没有找到',
            'featured_image'        => '特色图像',
            'set_featured_image'    => '设置特色图像',
            'remove_featured_image' => '移除特色图像',
            'use_featured_image'    => '使用特色图像',
            'archives'              => sprintf('%s存档', $post_type_title),
            'attributes'            => sprintf('%s属性', $post_type_title),
            'parent_item_colon'     => sprintf('%s上级：', $post_type_title),
            'all_items'             => sprintf('所有%s', $post_type_title),
            'add_new_item'          => sprintf('添加新%s', $post_type_title),
            'new_item'              => sprintf('新%s', $post_type_title),
            'edit_item'             => sprintf('编辑%s', $post_type_title),
            'update_item'           => sprintf('更新%s', $post_type_title),
            'view_item'             => sprintf('查看%s', $post_type_title),
            'view_items'            => sprintf('查看%s', $post_type_title),
            'search_items'          => sprintf('搜索%s', $post_type_title),
            'insert_into_item'      => sprintf('插入到%s', $post_type_title),
            'uploaded_to_this_item' => sprintf('上传到%s', $post_type_title),
            'items_list'            => sprintf('%s列表', $post_type_title),
            'items_list_navigation' => sprintf('%s列表导航', $post_type_title),
            'filter_items_list'     => sprintf('%s筛选列表', $post_type_title),
        ]);

        return $post_type_label;
    }

    /**
     * 自定义分类标签
     *
     * @since 3.0.0
     *
     * @param string    $taxonomy_title 自定义分类标题
     * @param string    $name_menu      菜单标题名称
     * @return array
     *
     * @todo 本地化语言格式
     */
    private function taxonomy_label($taxonomy_title, $name_menu = '')
    {
        $taxonomy_label = [];
        $taxonomy_label = apply_filters('wwpo_taxonomy_label', $taxonomy_label);
        $taxonomy_label = wp_parse_args($taxonomy_label, [
            'singular_name'                 => $taxonomy_title,
            'name'                          => $taxonomy_title,
            'menu_name'                     => empty($name_menu) ? $taxonomy_title : $name_menu,
            'choose_from_most_used'         => '选择常用',
            'not_found'                     => '没有找到',
            'all_items'                     => sprintf('所有%s', $taxonomy_title),
            'parent_item'                   => sprintf('%s上级', $taxonomy_title),
            'parent_item_colon'             => sprintf('%s上级：', $taxonomy_title),
            'new_item_name'                 => sprintf('新%s名称', $taxonomy_title),
            'add_new_item'                  => sprintf('添加新%s', $taxonomy_title),
            'edit_item'                     => sprintf('编辑%s', $taxonomy_title),
            'update_item'                   => sprintf('更新%s', $taxonomy_title),
            'view_item'                     => sprintf('查看%s', $taxonomy_title),
            'separate_items_with_commas'    => sprintf('用逗号分隔%s', $taxonomy_title),
            'add_or_remove_items'           => sprintf('添加或删除%s', $taxonomy_title),
            'popular_items'                 => sprintf('常用%s', $taxonomy_title),
            'search_items'                  => sprintf('搜索%s', $taxonomy_title),
            'no_terms'                      => sprintf('没有%s', $taxonomy_title),
            'items_list'                    => sprintf('%s列表', $taxonomy_title),
            'items_list_navigation'         => sprintf('%s列表导航', $taxonomy_title),
            'back_to_items'                 => sprintf('返回%s', $taxonomy_title)
        ]);

        return $taxonomy_label;
    }

    /**
     * 格式化 Rest API 功能
     *
     * @since 1.0.0
     * @param array $restapi
     * {
     *  Rest API 功能设定数组
     *  @var boolean $show_in_rest           开启 Rest API 功能
     *  @var string  $rest_base              Rest API 命名空间
     *  @var string  $rest_controller_class  Rest API 控制函数
     * }
     */
    private function rest_api($restapi)
    {
        $customdata = [];

        /** 判断 Rest API 功能开启 */
        if (empty($restapi['show_in_rest'])) {
            $customdata['show_in_rest'] = false;
        } else {

            $customdata['show_in_rest'] = true;

            // 设定 Rest API 命名空间，默认使用 $post_type_key
            if (!empty($restapi['rest_base'])) {
                $customdata['rest_base'] = $restapi['rest_base'];
            }

            // 设置自定义控制器来代替 WP_REST_Posts_Controller
            if (!empty($restapi['rest_controller_class'])) {
                $customdata['rest_controller_class'] = $restapi['rest_controller_class'];
            }
        }

        // 返回设定参数
        return $customdata;
    }

    /**
     * 注册文章类型动作函数
     *
     * @since 1.0.0
     */
    public function load_post_type()
    {
        if (empty($this->option_data)) {
            return;
        }

        foreach ($this->option_data as $option_data) {

            if ('posttype' != $option_data['type']) {
                continue;
            }

            if (empty($option_data['enable'])) {
                continue;
            }

            register_post_type($option_data['slug'], $this->register_post_type($option_data));
        }
    }

    /**
     * 注册自定义文章分类动作函数
     *
     * @since 1.0.0
     */
    public function load_taxonomy()
    {
        if (empty($this->option_data)) {
            return;
        }

        foreach ($this->option_data as $option_data) {

            if ('taxonomy' != $option_data['type']) {
                continue;
            }

            if (empty($option_data['option']['object_type'])) {
                continue;
            }

            if (empty($option_data['enable'])) {
                continue;
            }

            $object_type = explode(',', $option_data['option']['object_type']);

            register_taxonomy($option_data['slug'], $object_type, $this->register_taxonomy($option_data));
        }
    }

    /**
     * 注册导航菜单函数
     *
     * @since 1.0.0
     */
    public function load_wp_menu()
    {
        if (empty($this->option_data)) {
            return;
        }

        foreach ($this->option_data as $option_data) {

            if ('wp-menu' != $option_data['type']) {
                continue;
            }

            if (empty($option_data['enable'])) {
                continue;
            }

            $nav_menus[$option_data['slug']] = $option_data['title'];
        }

        if (empty($nav_menus)) {
            return;
        }

        register_nav_menus($nav_menus);
    }

    /**
     * 自定义类型内容数组标签函数
     *
     * @since 1.0.0
     * @param string $key 需要显示的标签
     */
    static function post_type($key = '')
    {
        return wwpo_array_values($key, [
            'posttype'  => __('文章类型', 'wwpo'),
            'taxonomy'  => __('分类法', 'wwpo'),
            'wp-menu'   => __('菜单', 'wwpo')
        ]);
    }
}

/**
 * 注册自定义文章类型
 *
 * @since 1.0.0
 */
add_action('wwpo_init', ['WWPO_Custom', 'init']);

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_register_admin_menu_contents($menus)
{
    $menus['wwpo-contents'] = [
        'parent'        => 'webian-wordpress-one',
        'menu_title'    => __('自定义内容', 'wwpo')
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_register_admin_menu_contents');

/**
 * 自定义内容列表界面
 *
 * @since 1.0.0
 */
function wwpo_register_admin_display_contents()
{
    // 获取自定义内容选项值
    $option_data = get_option(OPTION_CONTENTS_KEY, []);

    WWPO_Table::result($option_data, [
        'index'     => true,
        'column'    => [
            'content_title'         => '名称',
            'small-slug'            => '别名',
            'content_type'          => '类型',
            'content_option'        => '参数设置',
            'small-content_status'  => '状态'
        ],
    ]);
}
add_action('wwpo_admin_display_wwpo-contents', 'wwpo_register_admin_display_contents');

/**
 * 自定义内容表格列输出函数
 *
 * @since 1.0.0
 * @param array     $data           表格列值
 * @param string    $column_name    表格列名称
 */
function wwpo_content_table_column($data, $column_name)
{
    switch ($column_name) {

            //
        case 'content_title':
            printf('<strong><a data-wwpo-ajax="" data-post="%s">%s</a></strong>', $data['ID'], $data['title']);
            break;

            //
        case 'content_type':
            echo WWPO_Custom::post_type($data['type']);
            break;

            //
        case 'content_status':
            if (empty($data['enable'])) {
                printf('<span class="text-black-50">%s</span>', __('已禁用', 'wwpo'));
            } else {
                printf('<span class="text-primary">%s</span>', __('已启用', 'wwpo'));
            }
            break;

        default:
            break;
    }
}
add_action('wwpo_table_wwpo-contents_custom_column', 'wwpo_content_table_column', 10, 2);
