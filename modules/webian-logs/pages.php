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
        if (is_numeric($search)) {
            $wheres['code']     = ['value' => $search];
        } else {
            $wheres['message']  = ['value' => $search];
        }
    }

    echo WWPO_Table::select(WWPO_SQL_LOGS, $wheres, [
        'index'     => true,
        'orderby'   => 'log_id',
        'column'    => [
            'small-user'    => '用户',
            'event'         => '操作事件',
            'small-ip'      => '操作 IP',
            'date'          => '操作时间'
        ],
    ]);
}
add_action('wwpo_admin_display_wwpologs', 'wwpo_logs_display_admin');

/**
 * Undocumented function
 *
 * @param [type] $which
 * @return void
 */
function wwpo_logs_extra_tablenav($which)
{
    if ('top' != $which) {
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
    $current_page   = WWPO_Admin::page_name(true);
    $event_logs     = [];
    $event_logs     = apply_filters('wwpo_logs_code', $event_logs);

    $event_data = $event_logs[$current_page][$data['code']] ?? ['class' => 'muted', 'message' => __('未知事件', 'wwpo')];

    if ('user' == $column_name) {
        the_author_meta('display_name', $data['user_post']);
    }

    if ('ip' == $column_name) {
        echo $data['user_ip'];
    }

    if ('event' == $column_name) {
        printf('<strong>%s：</strong>%s。', $data['code'], $event_data['message']);

        if ($data['message']) {
            printf('<span class="text-%s">%s</span>', $event_data['class'], $data['message']);
        }
    }
}
add_action('wwpo_table_wwpo-logs_custom_column', 'wwpo_logs_custom_column', 10, 2);

/**
 * Undocumented function
 *
 * @param string $code
 * @return void
 */
function wwpo_logs($code, $message = '')
{
    if (empty($code)) {
        return;
    }

    $updated = [
        'user_post' => get_current_user_id(),
        'code'      => $code,
        'message'   => $message,
        'user_ip'   => wwpo_get_ip(),
        'time_post' => NOW_TIME,
    ];

    wwpo_insert_post(WWPO_SQL_LOGS, $updated);
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_ajax_admin_clear_logs()
{
    echo new WWPO_Error('success', '日志内容已清空');
}
add_action('wwpo_ajax_admin_wwpoclearlogs', 'wwpo_ajax_admin_clear_logs');
