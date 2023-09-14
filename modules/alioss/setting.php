<?php

/**
 * Undocumented function
 *
 * @param [type] $settings
 * @return void
 */
function wwpo_admin_settings_oss($settings)
{
    // 获取设置保存值
    $option_data = get_option('wwpo-settings-alioss');

    // 设置表单内容数组
    $settings['alioss'] = [
        'title'     => __('阿里云OSS', 'wwpo'),
        'formdata'  => [
            'option_data[enable]' => [
                'title' => __('模块状态', 'wwpo'),
                'field' => [
                    'type'      => 'select',
                    'option'    => [__('关闭', 'wwpo'), __('开启', 'wwpo')],
                    'selected'  => $option_data['enable'] ?? 0
                ]
            ],
            'option_data[bucket]' => [
                'title' => __('Bucket 名称', 'wwpo'),
                'desc'  => __('阿里云 OSS 中保存文件的 Bucket 名称', 'wwpo'),
                'field' => ['type' => 'text', 'value' => $option_data['bucket'] ?? '']
            ],
            'option_data[accesskeyid]' => [
                'title' => __('AccessKeyId', 'wwpo'),
                'desc'  => __('阿里云的 AccessKeyId <a href="#">帮助</a>', 'wwpo'),
                'field' => ['type' => 'text', 'value' => $option_data['accesskeyid'] ?? '']
            ],
            'option_data[accesskeysecret]' => [
                'title' => __('AccessKeySecret', 'wwpo'),
                'desc'  => __('阿里云的 AccessKeySecret', 'wwpo'),
                'field' => ['type' => 'text', 'value' => $option_data['accesskeysecret'] ?? '']
            ],
            'option_data[endpoint]' => [
                'title' => __('访问节点', 'wwpo'),
                'desc'  => __('阿里云 OSS 访问的地域节点域名', 'wwpo'),
                'field' => [
                    'type'      => 'select',
                    'option'    => WWPO_Alioss::endpoint(),
                    'selected'  => $option_data['endpoint'] ?? ''
                ]
            ],
            'option_data[network]' => [
                'title' => __('网络类型', 'wwpo'),
                'desc'  => __('部署在阿里云 ECS 上选择「内网」能加快传输速度，其他服务器请选择「外网」', 'wwpo'),
                'field' => [
                    'type'      => 'select',
                    'option'    => ['internal' => __('内网', 'wwpo'), 'external' => __('外网', 'wwpo')],
                    'selected'  => $option_data['network'] ?? ''
                ]
            ],
            'option_data[permission]' => [
                'title' => __('读写权限', 'wwpo'),
                'field' => [
                    'type'      => 'select',
                    'option'    => [
                        'private'   => __('私有', 'wwpo'),
                        'read'      => __('公共读', 'wwpo'),
                        'public'    => __('公共读写', 'wwpo')
                    ],
                    'selected'  => $option_data['permission'] ?? ''
                ]
            ],
            'option_data[domain]' => [
                'title' => __('绑定域名', 'wwpo'),
                'desc'  => __('阿里云 OSS 上绑定的自定义域名', 'wwpo'),
                'field' => ['type' => 'text', 'value' => $option_data['domain'] ?? '']
            ]
        ]
    ];

    // 返回设置内容
    return $settings;
}
add_filter('wwpo_admin_page_settings', 'wwpo_admin_settings_oss');
