<?php

/**
 * 自定义文章类型参数编辑内容
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @param array $option_data
 * {
 *  当前内容数组
 *  @var string title   内容名称
 *  @var string slug    内容别名
 *  @var string desc    内容简介
 *  @var array  option  内容设置选项数组
 * }
 *
 * 1、基本设置：$posttype_baisc
 * 2、链接设置：$posttype_url
 * 3、可见性设置：$posttype_visibility
 */
function wwpo_admin_display_content_posttype($option_data)
{
    $posttype_baisc['title']       = __('自定义文章类型', 'wwpo');
    $posttype_baisc['formdata']    = [
        'post_data[enable]' => [
            'title' => __('内容状态', 'wwpo'),
            'field' => [
                'type'      => 'select',
                'option'    => [__('禁用', 'wwpo'),  __('启用', 'wwpo')],
                'selected'  => $option_data['enable'] ?? 0
            ]
        ],
        'post_data[title]'  => [
            'title' => __('文章类型名称', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['title']]
        ],
        'post_data[desc]' => [
            'title' => __('文章类型描述', 'wwpo'),
            'field' => ['type'  => 'textarea', 'value' => $option_data['desc'] ?? '']
        ],
        'taxonomies' => [
            'title'     => __('默认分类', 'wwpo'),
            'fields'    => [
                'post_data[option][taxonomies][category]' => [
                    'title' => __('分类目录', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['taxonomies']['category'] ?? 0]
                ],
                'post_data[option][taxonomies][post_tag]' => [
                    'title' => __('标签', 'wwpo'),
                    'field' => ['type'  => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['taxonomies']['post_tag'] ?? 0]
                ]
            ]
        ],
        'supports'  => [
            'title'     => __('功能支持', 'wwpo'),
            'fields'    => [
                'post_data[option][supports][title]' => [
                    'title' => __('标题', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['supports']['title'] ?? 0]
                ],
                'post_data[option][supports][editor]' => [
                    'title' => __('编辑器', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['supports']['editor'] ?? 0]
                ],
                'post_data[option][supports][author]' => [
                    'title' => __('作者', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['supports']['author'] ?? 0]
                ],
                'post_data[option][supports][thumbnail]' => [
                    'title' => __('特色图像', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['supports']['thumbnail'] ?? 0]
                ],
                'post_data[option][supports][excerpt]' => [
                    'title' => __('摘要', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['supports']['excerpt'] ?? 0]
                ],
                'post_data[option][supports][trackbacks]'  => [
                    'title' => __('Trackbacks', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['supports']['trackbacks'] ?? 0]
                ],
                'post_data[option][supports][custom-fields]' => [
                    'title' => __('自定义字段', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['supports']['custom-fields'] ?? 0]
                ],
                'post_data[option][supports][comments]' => [
                    'title' => __('评论', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['supports']['comments'] ?? 0]
                ],
                'post_data[option][supports][revisions]' => [
                    'title' => __('修订版', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['supports']['revisions'] ?? 0]
                ],
                'post_data[option][supports][page-attributes]' => [
                    'title' => __('页面属性', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['supports']['page-attributes'] ?? 0]
                ],
                'post_data[option][supports][post-formats]' => [
                    'title' => __('文章格式', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['supports']['post-formats'] ?? 0]
                ]
            ]
        ],
        'post_data[option][capability]' => [
            'title' => __('角色类型', 'wwpo'),
            'field' => [
                'type'      => 'select',
                'value'     => $option_data['option']['capability'] ?? 0,
                'option'    => [
                    'post'  => __('使用文章角色', 'wwpo'),
                    'page'  => __('使用页面角色', 'wwpo')
                ]
            ]
        ],
        'advanced' => [
            'title'     => __('高级设置', 'wwpo'),
            'fields'    => [
                'post_data[option][hierarchical]'  => [
                    'title' => __('支持层次结构（类似页面）', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['hierarchical'] ?? 0]
                ],
                'post_data[option][can_export]' => [
                    'title' => __('支持文章导出', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['can_export'] ?? 0]
                ],
                'post_data[option][exclude_from_search]' => [
                    'title' => __('搜索结果不显示该文章类型', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['exclude_from_search'] ?? 0]
                ],
                'post_data[option][delete_with_user]' => [
                    'title' => __('删除用户时删除其发布的内容', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['delete_with_user'] ?? 0]
                ]
            ]
        ]
    ];

    $posttype_url['title']    = __('链接设置', 'wwpo');
    $posttype_url['formdata'] = [
        'has_archive' => [
            'title'     => __('文章归档', 'wwpo'),
            'fields'    => [
                'post_data[option][has_archive][enable]' => [
                    'title' => __('使用文章归档', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['has_archive']['enable'] ?? 0]
                ],
                'post_data[option][has_archive][slug]' => [
                    'title' => __('自定义文章归档别名', 'wwpo'),
                    'field' => ['type' => 'text', 'value' => $option_data['option']['has_archive']['slug'] ?? '']
                ]
            ]
        ],
        'rewrite' => [
            'title'     => __('固定链接', 'wwpo'),
            'fields'    => [
                'post_data[option][rewrite][enable]' => [
                    'title' => __('开启固定链接', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['rewrite']['enable'] ?? 0]
                ],
                'post_data[option][rewrite][with_front]' => [
                    'title' => __('添加分类前缀', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['rewrite']['with_front'] ?? 0]
                ],
                'post_data[option][rewrite][pages]' => [
                    'title' => __('支持页码', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['rewrite']['pages'] ?? 0]
                ],
                'post_data[option][rewrite][slug]' => [
                    'title' => __('自定义链接别名', 'wwpo'),
                    'field' => ['type' => 'text', 'value' => $option_data['option']['rewrite']['slug'] ?? '']
                ]
            ]
        ],
        'restapi' => [
            'title'     => __('REST API 设置', 'wwpo'),
            'fields'    => [
                'post_data[option][restapi][show_in_rest]' => [
                    'title' => __('开启 REST API', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['restapi']['show_in_rest'] ?? 0]
                ],
                'post_data[option][restapi][rest_base]' => [
                    'title' => __('自定义命名空间别名', 'wwpo'),
                    'field' => ['type' => 'text', 'value' => $option_data['option']['restapi']['rest_base'] ?? '']
                ],
                'post_data[option][restapi][rest_controller_class]' => [
                    'title' => __('自定义控制类名', 'wwpo'),
                    'field' => ['type' => 'text', 'value' => $option_data['option']['restapi']['rest_controller_class'] ?? '']
                ]
            ]
        ]
    ];

    $posttype_visibility['title']    = __('可见性设置', 'wwpo');
    $posttype_visibility['formdata'] = [
        'display'   => [
            'title'     => __('显示设置', 'wwpo'),
            'fields'    => [
                'post_data[option][visibility][public]'   => [
                    'title' => __('全局显示', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['visibility']['public'] ?? 0]
                ],
                'post_data[option][visibility][show_ui]' => [
                    'title' => __('后台界面', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['visibility']['show_ui'] ?? 0]
                ],
                'post_data[option][visibility][show_in_menu]' => [
                    'title' => __('后台菜单', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['visibility']['show_in_menu'] ?? 0]
                ],
                'post_data[option][visibility][show_in_admin_bar]' => [
                    'title' => __('后台工具栏', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['visibility']['show_in_admin_bar'] ?? 0]
                ],
                'post_data[option][visibility][show_in_nav_menus]' => [
                    'type'  => 'checkbox',
                    'title' => __('导航菜单', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['visibility']['show_in_nav_menus'] ?? 0]
                ],
                'post_data[option][visibility][publicly_queryable]' => [
                    'type'  => 'checkbox',
                    'title' => __('前端查询', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['visibility']['publicly_queryable'] ?? 0]
                ]
            ]
        ],
        'post_data[option][menu][position]' => [
            'title' => __('菜单位置', 'wwpo'),
            'field' => [
                'type'      => 'select',
                'value'     => $option_data['option']['visibility']['menu_position'] ?? 5,
                'option'    => [
                    5   => __('在文章后', 'wwpo'),
                    10  => __('在媒体后', 'wwpo'),
                    15  => __('在连接后', 'wwpo'),
                    20  => __('在页面后', 'wwpo'),
                    25  => __('在评论后', 'wwpo'),
                    60  => __('在第一个分隔符后', 'wwpo'),
                    65  => __('在插件后', 'wwpo'),
                    70  => __('在用户后', 'wwpo'),
                    75  => __('在工具后', 'wwpo'),
                    80  => __('在设置后', 'wwpo'),
                    100 => __('在第二个分隔符后', 'wwpo')
                ]
            ]
        ],
        'post_data[option][menu][icon]' => [
            'title' => __('菜单图标', 'wwpo'),
            'desc'  => __('文章类型图标。使用 Dashicons 名称或完整图标 URL (http://…/icon.png)。', 'wwpo'),
            'field' => ['type'  => 'text', 'value' => $option_data['option']['menu']['icon'] ?? '']
        ],
        'post_data[option][menu][name_menu]' => [
            'title' => __('后台菜单显示名称', 'wwpo'),
            'field' => ['type'  => 'text', 'value' => $option_data['option']['menu']['name_menu'] ?? '']
        ],
        'post_data[option][menu][name_adminbar]' => [
            'title' => __('工具栏显示名称', 'wwpo'),
            'field' => ['type'  => 'text', 'value' => $option_data['option']['menu']['name_adminbar'] ?? '']
        ]
    ];

    echo WWPO_Form::table($posttype_baisc, false);
    echo WWPO_Form::table($posttype_url, false);
    echo WWPO_Form::table($posttype_visibility, false);
}
