<?php

/**
 * 操作日志页面
 *
 * @package Webian WordPress One
 */

/**
 * 操作日志列表界面
 *
 * @since 1.0.0
 */
function wwpo_logs_display_admin()
{
    wwpo_admin_page_searchbar([
        'page'  => 'wwpo-logs',
    ], '搜索日志');

    $search = $_GET['s'] ?? '';
    $wheres = [];

    if ($search) {
        $wheres['event_code']['value'] = $search;
    }

    if (!current_user_can(WWPO_ROLE)) {
        $wheres['user_post']['value'] = get_current_user_id();
    }

    echo WWPO_Table::select(WWPO_SQL_LOGS, $wheres, [
        'index'     => true,
        'orderby'   => 'log_id',
        'column'    => [
            'medium-user'   => '用户',
            'event_code'    => '操作事件',
            'event_page'    => '操作页面',
            'medium-ip'     => '操作 IP',
            'date'          => '操作时间'
        ],
    ]);
}
add_action('wwpo_admin_display_wwpologs', 'wwpo_logs_display_admin');

/**
 * 日志列表操作按钮函数
 *
 * @since 1.0.0
 */
function wwpo_logs_extra_tablenav($which)
{
    if ('top' != $which) {
        return;
    }

    if (!current_user_can(WWPO_ROLE)) {
        return;
    }

    echo '<button type="button" data-action="wpajax" class="button" value="wwpoclearlogs">清空日志</button>';
}
add_action('wwpo_wwpo-logs_extra_tablenav', 'wwpo_logs_extra_tablenav');

/**
 * 当前页面表格行数据内容
 *
 * @since 1.0.0
 * @param array     $data           行数据内容数组
 * @param string    $column_name    当前表格列别名
 */
function wwpo_logs_custom_column($data, $column_name)
{
    if ('medium-user' == $column_name) {
        the_author_meta('display_name', $data['user_post']);
    }

    if ('medium-ip' == $column_name) {
        echo $data['user_ip'];
    }
}
add_action('wwpo_table_wwpo-logs_custom_column', 'wwpo_logs_custom_column', 10, 2);

/**
 * 日志清空 AJAX 操作函数
 *
 * @since 1.0.0
 */
function wwpo_ajax_admin_clear_logs()
{
    global $wpdb;
    $wpdb->query("TRUNCATE TABLE " . WWPO_SQL_LOGS);
    echo WWPO_Error::toast('success', '日志已清空', ['url' => 'reload']);
}
add_action('wwpo_ajax_admin_wwpoclearlogs', 'wwpo_ajax_admin_clear_logs');
