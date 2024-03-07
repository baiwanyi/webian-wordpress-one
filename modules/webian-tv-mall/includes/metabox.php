<?php

/**
 * 作品编辑页面自定义
 *
 * @since 1.0.0
 * @package Yanjiushe
 */

/**
 * 添加元数据操作框函数
 *
 * @since 1.0.0
 */
function wwpo_playlet_create_metabox($post)
{
    add_meta_box('wwpo-playlet-post-metabox', '剧集', 'wwpo_metabox_playlet_post', 'playlet', 'advanced', 'low', $post);
}
add_action('add_meta_boxes', 'wwpo_playlet_create_metabox');

/**
 * Undocumented function
 *
 * @param [type] $post
 * @return void
 */
function wwpo_metabox_playlet_post($post)
{
    echo WWPO_Table::result([], [
        'index'     => true,
        'column'    => [
            'thumb-apps'    => __('封面', 'wwpo'),
            'title-apps'    => __('简介'),
            'small-timing'  => __('时长', 'wwpo'),
            'medium-timing' => __('点数', 'wwpo'),
            'action'        => __('操作', 'wwpo')
        ]
    ]);
}

/**
 * Undocumented function
 *
 * @param [type] $which
 * @return void
 */
function wwpo_playlet_manage_extra_tablenav($which)
{
    if ('top' != $which) {
        return;
    }

    echo '<button type="button" class="button mb-3">添加</button>';
}
add_action('wwpo_playlet_extra_tablenav', 'wwpo_playlet_manage_extra_tablenav');

/**
 * 自定义内容表格列输出函数
 *
 * @since 1.0.0
 * @param array     $data           表格列值
 * @param string    $column_name    表格列名称
 */
function wwpo_playlet_admin_table_column($data, $column_name)
{
    switch ($column_name) {

            //
        case 'playlet_title':
            $page_url = WWPO_Admin::page_url();
            echo WWPO_Admin::title($data['appid'], $data['title'], 'edit', $page_url);
            break;

            //
        case 'apps_platform':

            if (empty($data['platform'])) {
                echo '—';
                return;
            }

            // echo wwpo_playlet_get_platform($data['platform']);
            break;

            //
        case 'expirestime':

            break;

        default:
            break;
    }
}
add_action('wwpo_table_playlet_custom_column', 'wwpo_playlet_admin_table_column', 10, 2);
