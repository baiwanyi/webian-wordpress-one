<?php

/**
 * WordPress 数据库清理优化
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress
 */

 /**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_register_admin_menu_mysql($menus)
{
    $menus['wwpo-mysql'] = [
        'parent'        => 'tools.php',
        'menu_title'    => __('数据库清理优化', 'wwpo')
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_register_admin_menu_mysql');

/**
 * 数据库优化显示页面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_wp_mysql()
{
    // 获取当前标签
    $current_tabs = $_GET['tab'] ?? 'table';

    // 显示页面标签
    WWPO_Template::tabs([
        'table' => '数据库表',
        'clean' => '清除冗余'
    ]);

    // 显示页面内容
    call_user_func("wwpo_admin_display_wp_mysql_{$current_tabs}");
}
add_action('wwpo_admin_display_wwpo-mysql', 'wwpo_admin_display_wp_mysql');

/**
 * 数据库表优化页面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_wp_mysql_table()
{
    global $wpdb;

    // 初始化
    $i = 1;
    $data = [];
    $total_size = 0;

    // 获取数据库所有数据表
    $result = $wpdb->get_results("SHOW TABLE STATUS FROM " . DB_NAME, ARRAY_A);

    /** 遍历数据库信息内容数组 */
    foreach ($result as $row) {

        // 计算当前数据库表
        $table_size = $row['Data_length'] + $row['Index_length'];
        $table_size = $table_size / 1024;

        // 计算数据库总大小
        $total_size += $table_size;

        // 构造数据库表内容数组
        $data[] = [
            'index'         => $i,
            'Name'          => sprintf('<strong>%s</strong>', $row['Name']),
            'rows'          => $row['Rows'],
            'increment'     => $row['Auto_increment'],
            'length'        => sprintf('%0.2f KB', $table_size),
            'engine'        => $row['Engine'],
            'Collation'     => $row['Collation'],
            'Create_time'   => $row['Create_time'],
        ];

        $i++;
    }

    // 显示数据库总大小
    printf('<div class="row d-flex justify-content-end"><div class="col-12 col-lg-4"><div class="input-group"><span class="input-group-text">总大小</span><input type="text" class="form-control" value="%0.2f KB" readonly><button type="button" data-action="wpajax" class="btn btn-primary" value="%s">优化数据库</button></div></div></div>', $total_size, 'mysqloptimize');

    // 显示表格内容
    echo WWPO_Table::result($data, [
        'index' => true,
        'column'    => [
            'Name'              => '数据库表名',
            'small-rows'        => '行数',
            'small-increment'   => '自动递增值',
            'small-length'      => '数据长度',
            'small-engine'      => '表类型',
            'Collation'         => '排序规则',
            'Create_time'       => '创建日期',
        ]
    ]);
}

/**
 * 数据库清除冗余页面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_wp_mysql_clean()
{
    // 初始化
    $i = 1;
    $data = [];

    // 数据库冗余清理类型内容数组
    $array_clean_type = [
        'revision'  => [
            'title' => '修订版本',
            'desc'  => '删除 posts 表中 post_type 为 <code>revision</code> 的记录。'
        ],
        'draft' => [
            'title' => '草稿',
            'desc'  => '删除 posts 表中 post_type 为 <code>draft</code> 的记录。'
        ],
        'autodraft' => [
            'title' => '自动草稿',
            'desc'  => '删除 posts 表中 post_type 为 <code>autodraft</code> 的记录。'
        ],
        'moderated' => [
            'title' => '待审评论',
            'desc'  => '删除 comments 表中待审评论的记录。'
        ],
        'spam'  => [
            'title' => '垃圾评论',
            'desc'  => '删除 comments 表中标记为垃圾 <code>spam</code> 的记录。'
        ],
        'trash' => [
            'title' => '回收站评论',
            'desc'  => '删除 comments 表中标记为删除 <code>trash</code> 的记录。'
        ],
        'postmeta'  => [
            'title' => '孤立的文章元信息',
            'desc'  => '删除 postmeta 表中没有关联 ID 的记录。'
        ],
        'redundantdata' => [
            'title' => '冗余的文章元信息',
            'desc'  => '删除 postmeta 表中 meta_key 为 <code>_edit_lock</code><code>_edit_last</code><code>_wp_old_slug</code><code>control</code><code>{{unknown}}</code> 的记录。'
        ],
        'commentmeta'   => [
            'title' => '孤立的评论元信息',
            'desc'  => '删除 commentmeta 表中没有关联 comment_id 的记录。'
        ],
        'relationships' => [
            'title' => '孤立的分类关系信息',
            'desc'  => '删除分类关系表 relationships 中没有关联 object_id 的记录。'
        ],
        'feed'  => [
            'title' => '控制板订阅缓存',
            'desc'  => '删除 options 表中 option_name 字段前缀为 <code>_site</code> 或 <code>_transient</code> 的记录。'
        ],
        'usermeta'  => [
            'title' => '孤立的用户元信息',
            'desc'  => '删除 usermeta 表中没有关联 user_id 的记录。'
        ]
    ];

    /** 遍历数据库冗余清理类型内容数组 */
    foreach ($array_clean_type as $clean_key => $clean_val) {

        // 获取数据库冗余数据数量
        $clean_count = wwpo_wp_mysql_clean_count($clean_key);

        // 设定显示内容数组
        $data[$clean_key] = [
            'index'         => $i,
            'title'         => sprintf('<strong>%s</strong>', $clean_val['title']),
            'clean-desc'    => $clean_val['desc'] ?? '',
            'total'         => $clean_count,
            'action'        => __('Action')
        ];

        // 判断冗余信息为空则禁用按钮
        if ($clean_count) {
            $data[$clean_key]['action'] = sprintf('<button type="button" data-action="wpajax" data-post="%s" value="mysqlclean" class="btn btn-danger">删除</button>', $clean_key);
        } else {
            $data[$clean_key]['action'] = '<button type="button" class="btn btn-secondary" disabled>删除</button>';
        }

        $i++;
    }

    // 显示表格内容
    echo WWPO_Table::result($data, [
        'index'     => true,
        'column'    => [
            'title'         => '类型',
            'clean-desc'    => '说明',
            'small-total'   => '数量',
            'small-action'  => '操作'
        ]
    ]);
}

/**
 * 页面表格扩展导航显示函数
 *
 * @since 1.0.0
 * @param string $which
 */
function wwpo_wp_mysql_extra_tablenav($which)
{
    if ('top' != $which) {
        return;
    }

    // 获取当前标签
    $current_tabs = WWPO_Template::tabs('table');

    // 清除冗余页面添加清理所有按钮
    if ('clean' == $current_tabs) {
        echo '<button type="button" data-action="wpajax" class="button" value="mysqlclean">清理所有冗余</button>';
    }
}
add_action('wwpo_mysql_extra_tablenav', 'wwpo_wp_mysql_extra_tablenav');

/**
 * 统计需要清理冗余数量函数
 *
 * @since 1.0.0
 * @param string $key   清理类型
 */
function wwpo_wp_mysql_clean_count($key)
{
    global $wpdb;
    switch ($key) {
        case 'revision':
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'revision'");
            break;
        case 'draft':
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'draft'");
            break;
        case 'autodraft':
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'auto-draft'");
            break;
        case 'moderated':
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'");
            break;
        case 'spam':
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'spam'");
            break;
        case 'trash':
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'trash'");
            break;
        case 'postmeta':
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id NOT IN ( SELECT ID FROM $wpdb->posts )");
            break;
        case 'redundantdata':
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = '_edit_lock' OR meta_key = '_edit_last' OR meta_key = '_wp_old_slug' OR meta_key = '_revision-control' OR meta_value = '{{unknown}}'");
            break;
        case 'commentmeta':
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->commentmeta WHERE comment_id NOT IN ( SELECT comment_id FROM $wpdb->comments )");
            break;
        case 'relationships':
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = 1 AND object_id NOT IN ( SELECT id FROM $wpdb->posts )");
            break;
        case 'feed':
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_%'");
            break;
        case 'usermeta':
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->usermeta WHERE user_id NOT IN ( SELECT ID FROM $wpdb->users )");
            break;
        default:
            break;
    }
    return $count;
}

/**
 * 执行清理冗余函数
 *
 * @since 1.0.0
 * @param string $key   清理类型
 */
function wwpo_wp_mysql_clean_up($key)
{
    global $wpdb;
    switch ($key) {
        case 'revision':
            $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision'");
            break;
        case 'draft':
            $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'draft'");
            break;
        case 'autodraft':
            $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");
            break;
        case 'moderated':
            $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = '0'");
            break;
        case 'spam':
            $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
            break;
        case 'trash':
            $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'trash'");
            break;
        case 'postmeta':
            $wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id NOT IN ( SELECT ID FROM $wpdb->posts )");
            break;
        case 'redundantdata':
            $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = '_edit_lock' OR meta_key = '_edit_last' OR meta_key = '_wp_old_slug' OR meta_key = '_revision-control' OR meta_value = '{{unknown}}'");
            break;
        case 'commentmeta':
            $wpdb->query("DELETE FROM $wpdb->commentmeta WHERE comment_id NOT IN ( SELECT comment_id FROM $wpdb->comments )");
            break;
        case 'relationships':
            $wpdb->query("DELETE FROM $wpdb->term_relationships WHERE term_taxonomy_id = 1 AND object_id NOT IN ( SELECT id FROM $wpdb->posts )");
            break;
        case 'feed':
            $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_%'");
            break;
        case 'usermeta':
            $wpdb->query("DELETE FROM $wpdb->usermeta WHERE user_id NOT IN ( SELECT ID FROM $wpdb->users )");
            break;
        default:
            break;
    }
}

/**
 * 数据库优化 AJAX 操作函数
 *
 * @since 1.0.0
 */
function wwpo_ajax_wp_mysql_optimize()
{
    global $wpdb;

    // 获取数据库列表进行优化
    $result = $wpdb->get_results("SHOW TABLE STATUS FROM " . DB_NAME);
    foreach ($result as $row) {
        $wpdb->query("OPTIMIZE TABLE {$row->Name}");
    }

    // 设定日志
    wwpo_logs('admin:ajax:mysqloptimize');

    // 返回信息
    echo WWPO_Error::toast('success', __('优化完成', 'wwpo'), ['url' => 'reload']);
}
add_action('wwpo_ajax_admin_mysqloptimize', 'wwpo_ajax_wp_mysql_optimize');

/**
 * 数据库清理 AJAX 操作函数
 *
 * @since 1.0.0
 */
function wwpo_ajax_wp_mysql_clean()
{
    $clean_key = $_POST['post'] ?? 'all';

    if ('all' == $clean_key) {
        wwpo_wp_mysql_clean_up('revision');
        wwpo_wp_mysql_clean_up('draft');
        wwpo_wp_mysql_clean_up('autodraft');
        wwpo_wp_mysql_clean_up('moderated');
        wwpo_wp_mysql_clean_up('spam');
        wwpo_wp_mysql_clean_up('trash');
        wwpo_wp_mysql_clean_up('postmeta');
        wwpo_wp_mysql_clean_up('redundantdata');
        wwpo_wp_mysql_clean_up('commentmeta');
        wwpo_wp_mysql_clean_up('relationships');
        wwpo_wp_mysql_clean_up('feed');
        wwpo_wp_mysql_clean_up('usermeta');
    }
    //
    else {
        wwpo_wp_mysql_clean_up($clean_key);
    }

    // 设定日志
    wwpo_logs('admin:ajax:mysqlclean:' . $clean_key);

    echo WWPO_Error::toast('success', __('优化完成', 'wwpo'), ['url' => 'reload']);
}
add_action('wwpo_ajax_admin_mysqlclean', 'wwpo_ajax_wp_mysql_clean');
