<?php

/**
 * 定时任务
 *
 * @since 1.0.0
 * @package Webian WP Mall
 */

/**
 * 每小时一次定时任务函数：生成小程序同步内容列表
 *
 * @since 1.0.0
 */
function wwpo_wpmall_create_wxapps($data)
{
    $data['customized'] = wwpo_get_post_field('customized', 'post_content');

    return $data;
}
add_action('wwpo_wxapps_sync', 'wwpo_wpmall_create_wxapps');
