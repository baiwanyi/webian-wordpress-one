<?php

/**
 * 站点统计代码设置组件
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage components
 */

/**
 * 植入统计分析代码函数
 *
 * @since 1.0.0
 */
function wwpo_register_header_analytics()
{
    $option_data = WWPO_Get::option(OPTION_SETTING_KEY, 'analytics');

    if ('close' == $option_data['type'] || empty($option_data['code'])) {
        return;
    }

    if ('baidu' == $option_data['type']) {
        printf('<script>var _hmt = _hmt || [];(function() {var hm = document.createElement("script");hm.src = "https://hm.baidu.com/hm.js?%s";var s = document.getElementsByTagName("script")[0];s.parentNode.insertBefore(hm, s);})();</script>', $option_data['code']);
    }

    if ('google' == $option_data['type']) {
        printf('<script async src="https://www.googletagmanager.com/gtag/js?id=%1$s"></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \'%1$s\');</script>', $option_data['code']);
    }
}
add_action('wp_head', 'wwpo_register_header_analytics');

/**
 * 注册站点统计选项设置函数
 *
 * @since 1.0.0
 * @param array $admin_page
 */
function wwpo_admin_page_setting_analytics($admin_page)
{
    $option_data = WWPO_Get::option(OPTION_SETTING_KEY, 'analytics');

    $admin_page['analytics'] = [
        'title'     => __('站点统计', 'wwpo'),
        'formdata'  => [
            'option_data[analytics][type]' => [
                'title' => '统计平台',
                'field' => [
                    'type'      => 'select',
                    'option'    => [
                        'close'     => '关闭',
                        'baidu'     => '百度统计',
                        'google'    => 'Google Analytics',
                    ],
                    'selected' => $option_data['type'] ?? 'close'
                ]
            ],
            'option_data[analytics][code]' => [
                'title' => '统计代码',
                'field' => ['type' => 'text', 'value' => $option_data['code'] ?? '']
            ]
        ]
    ];

    return $admin_page;
}
add_filter('wwpo_admin_page_settings', 'wwpo_admin_page_setting_analytics');
