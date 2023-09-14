<?php

/**
 * 用户角色管理
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress
 */

/** 用户角色 AJAX 操作常量 */
const WWPO_AJAX_ROLE_CREATE     = 'wprolecreate';
const WWPO_AJAX_ROLE_DISPLAY    = 'wproledisplay';
const WWPO_AJAX_ROLE_DELETE     = 'wproledelete';
const WWPO_AJAX_ROLE_UPDATE     = 'wproleupdate';

/**
 * 用户角色管理显示页面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_wp_user_roles($page_action)
{
    if (empty($page_action)) {
        wwpo_admin_display_list_user_roles();
        return;
    }

    wwpo_admin_display_edit_user_roles();
}
add_action('wwpo_admin_display_roles', 'wwpo_admin_display_wp_user_roles');

/**
 * 用户角色列表页面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_list_user_roles()
{
    /** 获取系统权限 */
    $wp_roles   = new WP_Roles();
    $role_data  = [];

    // 添加角色表单内容数组
    $array_formdata  = [
        'title'     => __('添加自定义角色权限', 'wwpo'),
        'submit'    => WWPO_AJAX_ROLE_CREATE,
        'formdata'  => [
            'role_name' => [
                'title' => __('名称', 'wwpo'),
                'field' => ['type' => 'text']
            ],
            'role_slug' => [
                'title' => __('别名', 'wwpo'),
                'field' => ['type' => 'text']
            ],
            'role_type' => [
                'title' => __('类型', 'wwpo'),
                'field' => [
                    'type'      => 'select',
                    'option'    => ['role' => __('角色', 'wwpo'), 'power' => __('权限', 'wwpo')]
                ]
            ]
        ]
    ];

    // 初始化列表编号
    $i = 1;

    /**
     * 遍历角色数组
     *
     * @property string $role_slug  角色别名
     * @property array  $role_val
     * {
     *  角色数组
     *  @type string name            角色名称
     *  @type array  capabilities    角色权限
     * }
     */
    foreach ($wp_roles->roles as $role_slug => $role_val) {
        $role_data[$role_slug] = [
            'index' => $i,
            'role'  => translate_user_role($role_val['name']),
            'slug'  => $role_slug,
            'total' => count($role_val['capabilities'])
        ];
        $i++;
    }

    echo '<main id="col-container" class="wp-clearfix container-fluid p-0">';
    echo '<div id="col-left" class="col-wrap">';

    // 显示角色表单
    echo WWPO_Form::list($array_formdata);
    echo '</div>';
    echo '<div id="col-right" class="col-wrap">';

    // 显示角色内容表格
    echo WWPO_Table::result($role_data, [
        'index'     => true,
        'column'    => [
            'name'  => '角色名称',
            'slug'  => '别名',
            'total' => '权限数量'
        ]
    ]);
    echo '</div>';
    echo '</main>';
}

/**
 * 用户角色编辑页面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_edit_user_roles()
{
    $role = $_GET['post'];

    /** 判断用户角色不存在 */
    if (empty($role)) {
        WWPO_Admin::messgae('error', '没有找到相关角色');
        exit;
    }

    /** 获取指定角色权限 */
    $current_role   = get_role($role);
    $current_name   = ucwords($current_role->name);
    $current_roles  = array_keys($current_role->capabilities);
    $all_user_role  = wwpo_wp_roles();

    printf('<h2>%s 权限修改</h2>', translate_user_role($current_name));
    echo '<form id="webui__admin-form" method="POST" autocomplete="off">';
    printf('<input type="hidden" name="role" value="%s">', $role);


    foreach ($all_user_role as $user_role) {
        printf('<h4 class="lead border-bottom mb-4 pb-2">%s</h4>', $user_role['title']);
        echo '<div class="row mb-3">';

        foreach ($user_role['list'] as $role_key => $role_name) {
            $checked = in_array($role_key, $current_roles) ? true : false;

            printf('<div class="col-2 mb-2"><input type="checkbox" class="btn-check" name="roles[]" id="role-check-%1$s" value="%1$s" %3$s><label class="btn btn-outline-primary d-block" for="role-check-%1$s">%2$s</label></div>', $role_key, $role_name, checked($checked, true, false));
        }
        echo '</div>';
    }

    echo '<p class="submit">';
    echo WWPO_Button::submit(WWPO_AJAX_ROLE_UPDATE);

    if ('administrator' != $role) {
        echo WWPO_Button::submit([
            'text'  => __('Delete'),
            'value' => WWPO_AJAX_ROLE_DELETE,
            'css'   => 'link-delete large'
        ]);
    }
    echo '</p></form>';
}

/**
 * 自定义表格列输出函数
 *
 * @since 1.0.0
 * @param array    $data           表格内容数组
 * @param string   $column_name    表格内容标题
 */
function wwpo_table_roles_custom_column($data, $column_name)
{
    if ('name' == $column_name) {
        echo WWPO_Admin::title($data['slug'], $data['role']);
    }
}
add_action('wwpo_table_roles_custom_column', 'wwpo_table_roles_custom_column', 10, 2);

/**
 * 添加用户角色 AJAX 操作函数
 *
 * @since 1.0.0
 */
function wwpo_admin_post_create_wprole()
{
    /** 判断角色名称 */
    if (empty($_POST['role_name'])) {
        new WWPO_Error('message', 'not_null_title');
        exit;
    }

    /** 判断角色别名 */
    if (empty($_POST['role_slug'])) {
        new WWPO_Error('message', 'not_null_slug');
        exit;
    }

    /** 判断添加角色类型 */
    if ('role' == $_POST['role_type']) {

        // 获取系统权限
        $wp_roles = new WP_Roles();

        // 判断角色别名占用
        if (array_key_exists($_POST['role_slug'], $wp_roles->get_names())) {
            new WWPO_Error('message', 'invalid_content');
            exit;
        }

        /** 添加角色到系统，并赋予 read 权限 */
        $result = add_role($_POST['role_slug'], $_POST['role_name'], ['read' => true]);

        if (null == $result) {
            new WWPO_Error('message', 'invalid_added');
            exit;
        }

        new WWPO_Error('message', 'success_added');
        exit;
    }

    /** 判断添加权限类型 */
    if ('power' == $_POST['role_type']) {

        // 获取自定义权限保存数据
        $wwpo_roles = get_option('wwpo-roles', []);

        // 添加权限到自定义数组
        $wwpo_roles[$_POST['role_slug']] = $_POST['role_name'];

        // 保存数据
        update_option('wwpo-roles', $wwpo_roles);

        new WWPO_Error('message', 'success_added');
        exit;
    }
}
add_action('wwpo_post_admin_' . WWPO_AJAX_ROLE_CREATE, 'wwpo_admin_post_create_wprole');

/**
 * 更新用户权限 AJAX 操作函数
 *
 * @since 1.0.0
 */
function wwpo_admin_post_update_wprole()
{
    // 获取当前角色权限
    $current_role = get_role($_POST['role']);

    // 获取角色原始权限别名
    $array_role_old = array_keys($current_role->capabilities);

    // 新用户权限打散成为数组
    $array_role_new = $_POST['roles'];

    /**
     * 删除用户旧权限
     *
     * 判断用户旧权限不存在新权限数组中，则移除该权限
     */
    foreach ($array_role_old as $role_old) {

        if (in_array($role_old, $array_role_new)) {
            continue;
        }

        $current_role->remove_cap($role_old);
    }

    /**
     * 添加新权限
     *
     * 判断用户新权限，为用户添加不包含在旧权限数组的新权限
     */
    foreach ($array_role_new as $role_new) {

        if (in_array($role_new, $array_role_old)) {
            continue;
        }

        $current_role->add_cap($role_new);
    }

    // 返回更新成功信息
    new WWPO_Error('message', 'success_updated', [
        'post'      => $_POST['role'],
        'action'    => 'edit'
    ]);
}
add_action('wwpo_post_admin_' . WWPO_AJAX_ROLE_UPDATE, 'wwpo_admin_post_update_wprole');

/**
 * 删除用户角色 AJAX 操作函数
 *
 * @since 1.0.0
 */
function wwpo_admin_post_delete_wprole()
{
    remove_role($_POST['role']);
    new WWPO_Error('message', 'success_deleted');
}
add_action('wwpo_post_admin_' . WWPO_AJAX_ROLE_DELETE, 'wwpo_admin_post_delete_wprole');

/**
 * 系统权限列表函数
 *
 * @since 1.0.0
 */
function wwpo_wp_roles()
{
    $wp_caps = [
        'themes'    => [
            'title' => '主题管理',
            'list'  => [
                'customize'             => '允许自定义',
                'delete_themes'         => '删除主题',
                'edit_themes'           => '编辑主题',
                'edit_theme_options'    => '编辑主题选项',
                'install_themes'        => '安装主题',
                'switch_themes'         => '切换主题',
                'update_themes'         => '更新主题',
                'upload_themes'         => '上传主题'
            ]
        ],
        'plugins'   => [
            'title' => '插件管理',
            'list'  => [
                'activate_plugins'  => '激活插件',
                'delete_plugins'    => '删除插件',
                'edit_plugins'      => '编辑插件',
                'install_plugins'   => '安装插件',
                'update_plugins'    => '更新插件',
                'upload_plugins'    => '上传插件'
            ]
        ],
        'user'   => [
            'title' => '用户管理',
            'list'  => [
                'add_users'     => '添加用户',
                'create_users'  => '新建用户',
                'delete_users'  => '删除用户',
                'edit_users'    => '编辑用户',
                'list_users'    => '查看用户列表',
                'promote_users' => '升级角色',
                'remove_users'  => '移除用户'
            ]
        ],
        'post'   => [
            'title' => '文章管理',
            'list'  => [
                'edit_others_posts'         => '编辑他人文章',
                'edit_posts'                => '编辑文章',
                'edit_private_posts'        => '编辑私密文章',
                'edit_published_posts'      => '编辑已发布文章',
                'delete_others_posts'       => '删除他人文章',
                'delete_posts'              => '删除文章',
                'delete_private_posts'      => '删除私密文章',
                'delete_published_posts'    => '删除已发布文章',
                'publish_posts'             => '发布文章',
                'read'                      => '阅读',
                'read_private_posts'        => '阅读私密文章'
            ]
        ],
        'page'   => [
            'title' => '页面管理',
            'list'  => [
                'edit_pages'                => '编辑页面',
                'edit_private_pages'        => '编辑私密页面',
                'edit_published_pages'      => '编辑已发布页面',
                'edit_others_pages'         => '编辑他人页面',
                'delete_others_pages'       => '删除他人页面',
                'delete_pages'              => '删除页面',
                'delete_private_pages'      => '删除私密页面',
                'delete_published_pages'    => '删除已发布页面',
                'publish_pages'             => '发布页面',
                'read_private_pages'        => '阅读私密页面'
            ]
        ],
        'media'   => [
            'title' => '媒体管理',
            'list'  => [
                'edit_files'        => '编辑文件',
                'unfiltered_upload' => '不限制文件上传类型',
                'upload_files'      => '上传文件'
            ]
        ],
        'system'   => [
            'title' => '系统管理',
            'list'  => [
                'edit_comment'      => '编辑评论',
                'edit_dashboard'    => '编辑仪表盘',
                'export'            => '导出内容',
                'import'            => '导入内容',
                'manage_categories' => '管理分类',
                'manage_links'      => '管理连接',
                'manage_options'    => '管理设置',
                'moderate_comments' => '管理评论',
                'unfiltered_html'   => '发布内容不过滤HTML和脚本',
                'update_core'       => '更新系统'
            ]
        ],
        'multisite'   => [
            'title' => '多站点管理',
            'list'  => [
                'create_sites'              => '新建站点',
                'delete_site'               => '删除站点',
                'delete_sites'              => '删除他人站点',
                'manage_network'            => '管理网络',
                'manage_sites'              => '管理站点',
                'manage_network_users'      => '管理网络用户',
                'manage_network_plugins'    => '管理网络插件',
                'manage_network_themes'     => '管理网络主题',
                'manage_network_options'    => '管理网络选项',
                'upgrade_network'           => '升级网络',
                'setup_network'             => '安装网络'
            ]
        ],
        'wplevel'   => [
            'title' => '系统等级',
            'list'  => [
                'level_10'  => '等级10',
                'level_9'   => '等级9',
                'level_8'   => '等级8',
                'level_7'   => '等级7',
                'level_6'   => '等级6',
                'level_5'   => '等级5',
                'level_4'   => '等级4',
                'level_3'   => '等级3',
                'level_2'   => '等级2',
                'level_1'   => '等级1',
                'level_0'   => '等级0'
            ]
        ]
    ];

    $wwpo_roles = get_option('wwpo-roles', []);
    if (empty($wwpo_roles)) {
        return $wp_caps;
    }

    $wwpo_caps = [
        'wwpo' => [
            'title' => __('自定义权限', 'wwpo'),
            'list'  => $wwpo_roles
        ]
    ];

    return array_merge($wwpo_caps, $wp_caps);
}

/**
 * 设置页面消息函数
 *
 * @since 1.0.0
 * @param array $message
 */
function wwpo_admin_message_roles($message)
{
    $message['roles'] = [
        'not_null_title'    => ['error'     => __('角色名称不能为空', 'wwpo')],
        'not_null_slug'     => ['error'     => __('角色别名不能为空', 'wwpo')],
        'invalid_added'     => ['error'     => __('角色权限添加失败', 'wwpo')],
        'invalid_content'   => ['error'     => __('角色别名已存在', 'wwpo')],
        'success_added'     => ['updated'   => __('角色权限添加成功', 'wwpo')],
        'success_updated'   => ['updated'   => __('角色权限已更新', 'wwpo')],
        'success_deleted'   => ['updated'   => __('角色权限删除成功', 'wwpo')]
    ];

    return $message;
}
add_filter('wwpo_admin_message', 'wwpo_admin_message_roles');
