<?php

/**
 * 微信小程序接口模块
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat/wxapps
 */

/**
 * 小程序广告内容表格列输出函数
 *
 * @since 1.0.0
 * @param array     $data           表格列值
 * @param string    $column_name    表格列名称
 */
function wwpo_wxapps_banner_table_column($data, $column_name)
{
    switch ($column_name) {

            // 广告标题
        case 'title-apps':
            $page_url = WWPO_Admin::add_query(['tab' => $_GET['tab']]);
            echo WWPO_Admin::title($data['id'], $data['title'], 'edit', $page_url);
            break;

            // 广告位置
        case 'adsense-apps':
            echo wwpo_wxapps_banner_adsense($data['adsense']);
            break;

            // 广告封面
        case 'thumb-apps':
            if (empty($data['thumb'])) {
                return;
            }

            printf(
                '<div class="ratio ratio-1x1"><img src="%s" class="thumb rounded"></div>',
                wp_get_attachment_image_url($data['thumb'])
            );
            break;

            // 广告剩余时间
        case 'endtime':

            $day_start  = str_replace('-', '', $data['start']);
            $day_end    = str_replace('-', '', $data['end']);
            $day_now    = date('Ymd', NOW);
            $day_limit  = $day_end - $day_start;

            if ($day_end < $day_now) {
                echo '已过期';
                return;
            }

            if (3 >= $day_limit) {
                printf('<span class="text-danger">%s</span>', $day_limit);
                return;
            }

            printf('<span class="text-success">%s</span>', $day_limit);
            break;

            // 广告位置
        case 'groups':
            echo wwpo_wxapps_category_get_group($data['group']);
            break;
        default:
            break;
    }
}
add_action('wwpo_table_wxapps_custom_column', 'wwpo_wxapps_banner_table_column', 10, 2);
