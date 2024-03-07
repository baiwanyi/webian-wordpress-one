<?php

/**
 * 添加地图A PI KEY 自定义设定表单函数
 * 设置在通用标签页面下
 *
 * @since 1.0.0
 * @param array $settings
 */
function wwpo_admin_settings_common_mapapi($settings)
{
    // 获取设置保存值
    $option_data = get_option('wwpo-settings-common');

    // 设置表单内容数组
    $settings['common']['formdata']['option_data[mapapi]'] = [
        'title' => __('地图API KEY', 'wwpo'),
        'field' => ['type' => 'text', 'value' => $option_data['mapapi'] ?? '']
    ];

    // 返回设置内容
    return $settings;
}
add_filter('wwpo_admin_page_settings', 'wwpo_admin_settings_common_mapapi');
