<?php

/**
 * 站点广告联盟代码设置组件
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage components
 */

/**
 * 植入广告联盟代码函数
 *
 * @since 1.0.0
 */
function wwpo_register_header_adsense()
{
    $option_data = WWPO_Get::option(OPTION_SETTING_KEY, 'adsense');

    if ('close' == $option_data['type'] || empty($option_data['code'])) {
        return;
    }

    if ('baidu' == $option_data['type']) {
        return;
    }

    if ('google' == $option_data['type']) {
        printf('<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=%s" crossorigin="anonymous"></script>', $option_data['code']);
    }
}
add_action('wp_head', 'wwpo_register_header_adsense');

/**
 * 注册广告联盟选项设置函数
 *
 * @since 1.0.0
 * @param array $admin_page
 */
function wwpo_admin_page_setting_adsense($admin_page)
{
    $option_data = WWPO_Get::option(OPTION_SETTING_KEY, 'adsense');

    $admin_page['adsense']['title'] = __('站点广告', 'wwpo');
    $admin_page['adsense']['formdata'] = [
        'option_data[adsense][type]' => [
            'title' => '广告平台',
            'field' => [
                'type'      => 'select',
                'option'    => [
                    'close'     => '关闭',
                    'baidu'     => '百度联盟',
                    'google'    => 'Google Adsense',
                ],
                'selected' => $option_data['type'] ?? 'close'
            ]
        ],
        'option_data[adsense][code]' => [
            'title' => '广告代码',
            'field' => ['type' => 'text', 'value' => $option_data['code'] ?? '']
        ]
    ];

    return $admin_page;
}
add_filter('wwpo_admin_page_settings', 'wwpo_admin_page_setting_adsense');
