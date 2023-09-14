<?php

/**
 * WordPress 数据库元数据表管理
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress
 */

/**
 * 数据库元数据表管理显示页面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_wp_metadata()
{
    global $wpdb;

    // 获取当前标签
    $current_tabs   = WWPO_Admin::tabs('option');
    $current_view   = $_GET['view'] ?? 'excerpt';

    //
    $search = $_GET['s'] ?? '';
    $wheres = [];

    //
    $table_data = [
        'checkbox'  => true,
        'order'     => 'ASC',
        'select'    => 'meta_id AS ID, meta_key, meta_value',
        'column'    => [
            'meta_title'    => '选项名',
            'meta_data'     => '选项值'
        ],
        'bulk'  => [
            'action'    => 'metadatabulkdelete',
            'option'    => ['delete' => '删除'],
            'dataset'   => [
                'tab'   => $current_tabs,
                'view'  => $current_view
            ]
        ],
        'sortable'  => [
            'meta_title'    => 'meta_key',
            'meta_data'     => 'meta_value'
        ]
    ];

    //
    wwpo_admin_page_searchbar([
        'tab'   => $current_tabs,
        'page'  => 'metadata',
        'view'  => $current_view
    ]);

    // 显示页面标签
    wwpo_wp_tabs([
        'option'    => '选项表',
        'post'      => '文章表',
        'comment'   => '评论表',
        'term'      => '分类表',
        'user'      => '用户表'
    ]);

    /**
     *
     */
    switch ($current_tabs) {
        case 'post':
            $table_name = $wpdb->postmeta;
            $table_data['select'] .= ', post_id';
            break;
        case 'term':
            $table_name = $wpdb->termmeta;
            $table_data['select'] .= ', term_id AS post_id';
            break;
        case 'user':
            $table_name = $wpdb->usermeta;
            $table_data['select'] = 'umeta_id AS ID, meta_key, meta_value, user_id AS post_id';
            break;
        case 'comment':
            $table_name = $wpdb->commentmeta;
            $table_data['select'] .= ', comment_id AS post_id';
            break;
        default:
            $table_name = $wpdb->options;
            $table_data['select'] = 'option_id AS ID, option_name AS meta_key, option_value AS meta_value, autoload';
            break;
    }

    /**  */
    if ('option' == $current_tabs) {
        $table_data['column']['autoload'] = '自动加载';
    }
    //
    else {

        /**  */
        if ('excerpt' == $current_view) {

            //
            $table_data['select'] .= ', COUNT(meta_key) AS total_key';

            //
            $table_data['groupby'] = 'meta_key';

            //
            $table_data['column']['total_key'] = '数量';

            //
            unset($table_data['column']['meta_data']);
        }

        if ('list' == $current_view) {
            $table_data['sortable']['meta_post'] = 'post_id';
            $table_data['column']['meta_post'] = '选项编号';
        }
    }

    //

    $table_data['column']['small-action'] = __('Action');

    /**  */
    if ($search) {
        $wheres[] = "option_name LIKE '%{$search}%'";
    }

    // 显示表格内容
    echo WWPO_Table::select($table_name, $wheres, $table_data);
}
add_action('wwpo_admin_display_metadata', 'wwpo_admin_display_wp_metadata');

/**
 * 页面表格扩展导航显示函数
 *
 * @since 1.0.0
 * @param string $which
 */
function wwpo_wp_metadata_extra_tablenav($which)
{
    if ('top' != $which) {
        return;
    }

    //
    $current_tabs   = WWPO_Admin::tabs('option');
    $current_view   = $_GET['view'] ?? 'excerpt';

    if ('option' == $current_tabs) {
        return;
    }

    echo '<div class="btn-group" role="group">';
    foreach (['excerpt' => '汇总视图', 'list' => '列表视图'] as $icon => $title) {
        $btn_class = ($current_view == $icon) ? 'btn-primary' : 'btn-outline-primary';
        $btn_url = WWPO_Admin::add_query(['tab' => $current_tabs, 'view' => $icon]);
        printf('<a href="%s" class="btn btn-sm %s"><span class="dashicons dashicons-%s-view"></span> %s</a>', $btn_url, $btn_class, $icon, $title);
    }
    echo '</div>';
}
add_action('wwpo_metadata_extra_tablenav', 'wwpo_wp_metadata_extra_tablenav');

/**
 * 自定义表格列输出函数
 *
 * @since 1.0.0
 * @param string    $column     表格列名称
 * @param integer   $post_id    产品编号
 */
function wwpo_wp_metadata_table_custom_column($data, $column_name)
{
    //
    $current_view = $_GET['view'] ?? 'excerpt';

    /**  */
    if ('meta_title' == $column_name) {
        echo sprintf('<strong>%1$s</strong>', $data['meta_key']);
    }

    if ('meta_post' == $column_name) {
        echo $data['post_id'];
    }

    /**  */
    if ('meta_data' == $column_name) {

        //
        if (is_serialized($data['meta_value'])) {
            echo '<span class="text-primary">SERIALIZED DATA</span>';
            return;
        }

        echo $data['meta_value'];
    }

    /**  */
    if ('action' == $column_name) {

        //
        $dataset['meta'] = WWPO_Admin::tabs('option');

        //
        if ('excerpt' == $current_view) {
            $dataset['key'] = $data['meta_key'];
        } else {
            $dataset['id'] = $data['ID'];
        }

        //
        echo WWPO_button::wp([
            'value'     => 'metadatadelete',
            'text'      => __('Delete'),
            'css'       => 'link-delete',
            'dataset'   => $dataset
        ]);
    }
}
add_action('wwpo_table_metadata_custom_column', 'wwpo_wp_metadata_table_custom_column', 10, 2);

/**
 * 数据库清理 AJAX 操作函数
 *
 * @since 1.0.0
 */
function wwpo_ajax_wp_metadata_delete()
{
    global $wpdb;
    $current_tabs       = $_POST['meta'];
    $current_meta_key   = $_POST['key'] ?? '';
    $current_meta_id    = $_POST['id'] ?? 0;

    switch ($current_tabs) {
        case 'post':
            $table_name = $wpdb->postmeta;
            break;
        case 'term':
            $table_name = $wpdb->termmeta;
            break;
        case 'user':
            $table_name = $wpdb->usermeta;
            break;
        case 'comment':
            $table_name = $wpdb->commentmeta;
            break;
        default:
            break;
    }

    if ('option' == $current_tabs) {
        delete_option($current_meta_key);
    } else {

        if ($current_meta_id) {
            $wpdb->delete($table_name, ['meta_id' => $current_meta_id]);
        } else {
            $wpdb->delete($table_name, ['meta_key' => $current_meta_key]);
        }
    }

    echo WWPO_Error::toast('success', __('选项值已删除', 'wwpo'), ['url' => 'reload']);
}
add_action('wwpo_ajax_admin_metadatadelete', 'wwpo_ajax_wp_metadata_delete');

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_ajax_wp_metadata_bulk_delete()
{
    $bulk_ids   = $_POST['bulk_ids'] ?? '';

    if (empty($bulk_ids)) {
        echo new WWPO_Error('danger', __('请选择需要删除的项目', 'wwpo'));
        return;
    }
}
add_action('wwpo_ajax_admin_metadatabulkdelete', 'wwpo_ajax_wp_metadata_bulk_delete');
