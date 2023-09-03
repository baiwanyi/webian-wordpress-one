<?php

/**
 * 界面设置函数页面
 *
 * @package Webian WordPress One
 */

/**
 * 主题设置页面
 *
 * @since 1.0.0
 */
function wwpo_admin_display_settings()
{
    // 获取页面标签
    $current_tabs = WWPO_Admin::tabs('common');

    // 获取设置保存值
    $option_data = wwpo_get_option('wwpo-settings-common');

    // 页面标签内容数组
    $array_admin_page['common']['title']      = __('通用设置', 'wwpo');
    $array_admin_page['common']['formdata']   = [
        'option_data[webfont]'  => [
            'title' => __('Webfont 地址', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['webfont'] ?? '']
        ],
        'option_data[cdnurl]'  => [
            'title' => __('静态 CDN 地址', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['cdnurl'] ?? '']
        ],
        'analytics'  => [
            'title'     => __('站点统计', 'wwpo'),
            'fields'    => [
                'option_data[analytics][type]' => [
                    'field' => [
                        'type'      => 'select',
                        'option'    => [
                            'close'     => '关闭',
                            'baidu'     => '百度统计',
                            'google'    => 'Google Analytics',
                        ],
                        'selected' => $option_data['analytics']['type'] ?? 'close'
                    ]
                ],
                'option_data[analytics][code]' => [
                    'field' => ['type' => 'text', 'value' => $option_data['analytics']['code'] ?? '']
                ]
            ]
        ],
        'adsense'  => [
            'title'     => __('站点广告', 'wwpo'),
            'fields'    => [
                'option_data[adsense][type]' => [
                    'field' => [
                        'type'      => 'select',
                        'option'    => [
                            'close'     => '关闭',
                            'baidu'     => '百度联盟',
                            'google'    => 'Google Adsense',
                        ],
                        'selected' => $option_data['adsense']['type'] ?? 'close'
                    ]
                ],
                'option_data[adsense][code]' => [
                    'field' => ['type' => 'text', 'value' => $option_data['adsense']['code'] ?? '']
                ]
            ]
        ],
        'option_data[keywords]'  => [
            'title'     => __('SEO 搜索关键字', 'wwpo'),
            'field' => ['type' => 'textarea', 'value' => $option_data['keywords'] ?? '']
        ],
        'option_data[description]'  => [
            'title'     => __('SEO 简介', 'wwpo'),
            'field' => ['type' => 'textarea', 'value' => $option_data['description'] ?? '']
        ]
    ];

    /**
     * 设置页面内容接口
     *
     * @since 1.0.0
     * @api wwpo_admin_page_settings
     */
    $array_admin_page = apply_filters('wwpo_admin_page_settings', $array_admin_page);

    /**
     * 遍历设置页面内容数组
     *
     * @property string $page_key       页面别名
     * @property array  $admin_page_val 页面内容
     */
    foreach ($array_admin_page as $page_key => $admin_page_val) {
        $array_admin_tabs[$page_key] = $admin_page_val['title'];
    }

    // 显示页面标签
    wwpo_wp_tabs($array_admin_tabs);

    // 显示设置表单
    echo WWPO_Form::table([
        'hidden'    => [
            'option_key' => $current_tabs
        ],
        'submit'    => 'updatesettings',
        'formdata'  => $array_admin_page[$current_tabs]['formdata']
    ]);
}
add_action('wwpo_admin_display_webianwordpressone', 'wwpo_admin_display_settings');

/**
 * 设置页面消息函数
 *
 * @since 1.0.0
 * @param array $message
 */
function wwpo_admin_message_settings($message)
{
    $message['webianwordpressone'] = [
        'no_option_key'     => ['error' => '没有找到相关保存参数'],
        'updated_success'   => ['updated' => '设置保存成功']
    ];

    return $message;
}
add_filter('wwpo_admin_message', 'wwpo_admin_message_settings');

/**
 * 设置保存操作函数
 *
 * @since 1.0.0
 */
function wwpo_admin_post_update_settings()
{
    /** 判断保存 KEY */
    if (empty($_POST['option_key'])) {
        new WWPO_Error('message', 'no_option_key');
        exit;
    }

    // 设置保存选项 KEY
    $option_key = sprintf('wwpo-settings-%s', $_POST['option_key']);

    // 更新到数据库
    wwpo_update_option($option_key, $_POST['option_data']);

    // 返回信息
    new WWPO_Error('message', 'updated_success', ['tab' => $_POST['option_key']]);
}
add_action('wwpo_post_admin_updatesettings', 'wwpo_admin_post_update_settings');
