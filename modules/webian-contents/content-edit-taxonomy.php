<?php

/**
 * 自定义分类法参数编辑内容
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
 * @param array $option 所有内容数组，用于获取自定义文章类型
 *
 * 1、基本设置：$taxonomy_basic
 * 2、链接设置：$taxonomy_permalinks
 * 3、遍历所有内容数组，生成自定义位置类型 posttype 的内容数组
 * 4、判断 posttype 的内容数组为空，显示新建 posttype 内容链接
 * 5、遍历 posttype 的内容数组，生成 checkbox 表单
 */
function wwpo_admin_display_content_taxonomy($option_data)
{
    $taxonomy_basic['title']    = __('分类法', 'wwpo');
    $taxonomy_basic['formdata'] = [
        'post_data[enable]' => [
            'title' => __('内容状态', 'wwpo'),
            'field' => [
                'type'      => 'select',
                'option'    => [__('禁用', 'wwpo'),  __('启用', 'wwpo')],
                'selected'  => $option_data['enable'] ?? 0
            ]
        ],
        'post_data[title]' => [
            'title' => __('分类法名称', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['title']]
        ],
        'post_data[desc]' => [
            'title' => __('分类法描述', 'wwpo'),
            'field' => ['type'  => 'textarea', 'value' => $option_data['desc'] ?? '']
        ],
        'post_data[option][name_menu]' => [
            'title' => __('后台菜单显示名称', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['option']['name_menu'] ?? '']
        ],
        'object_type' => [
            'title' => __('应用到自定义文章类型', 'wwpo')
        ],
        'hierarchical' => [
            'title'     => __('分类法类型', 'wwpo'),
            'field'    => [
                'type'      => 'select',
                'name'      => 'post_data[option][hierarchical]',
                'option'    => [__('Tags'), __('Categories')],
                'selected'  => $option_data['option']['hierarchical'] ?? 0
            ]
        ],
        'callback' => [
            'title' => __('回调函数', 'wwpo'),
            'fields' => [
                'callback' => [
                    'field'    => [
                        'type'      => 'select',
                        'name'      => 'post_data[option][callback]',
                        'option'    => [__('禁用回调函数', 'wwpo'), __('启用回调函数', 'wwpo')],
                        'selected'  => $option_data['option']['callback'] ?? 0
                    ]
                ],
                'post_data[option][meta_box_cb]' => [
                    'title' => __('后台编辑框', 'wwpo'),
                    'field' => ['type' => 'text', 'value' => $option_data['option']['meta_box_cb'] ?? '']
                ],
                'post_data[option][update_count_callback]' => [
                    'title' => __('显示数量', 'wwpo'),
                    'field' => ['type' => 'text', 'value' => $option_data['option']['update_count_callback'] ?? '']
                ]
            ]
        ],
        'display' => [
            'title'     => __('显示设置', 'wwpo'),
            'desc'      => '关闭全局显示后，将不在任何地方显示该分类法。',
            'fields'    => [
                'post_data[option][visibility][public]'    => [
                    'title' => __('全局显示', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['visibility']['public'] ?? 0]
                ],
                'post_data[option][visibility][show_ui]' => [
                    'title' => __('后台界面', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['visibility']['show_ui'] ?? 0]
                ],
                'post_data[option][visibility][show_admin_column]' => [
                    'title' => __('后台列表', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['visibility']['show_admin_column'] ?? 0]
                ],
                'post_data[option][visibility][show_tagcloud]' => [
                    'title' => __('标签云', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['visibility']['show_tagcloud'] ?? 0]
                ],
                'post_data[option][visibility][show_in_nav_menus]' => [
                    'title' => __('导航菜单', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['visibility']['show_in_nav_menus'] ?? 0]
                ],
                'post_data[option][visibility][show_in_quick_edit]' => [
                    'title' => __('快速编辑', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['visibility']['show_in_quick_edit'] ?? 0]
                ],
                'post_data[option][visibility][publicly_queryable]' => [
                    'title' => __('前端查询', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['visibility']['publicly_queryable'] ?? 0]
                ]
            ]
        ]
    ];

    $taxonomy_permalinks['title']    = __('链接设置', 'wwpo');
    $taxonomy_permalinks['formdata'] = [
        'rewrite' => [
            'title'     => __('固定链接', 'wwpo'),
            'fields'    => [
                'post_data[option][rewrite][enable]' => [
                    'title' => __('开启固定链接', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['rewrite']['enable'] ?? 0]
                ],
                'post_data[option][rewrite][with_front]' => [
                    'title' => __('添加文章类型前缀', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['rewrite']['with_front'] ?? 0]
                ],
                'post_data[option][rewrite][hierarchical]' => [
                    'title' => __('添加层级分类别名', 'wwpo'),
                    'field' => ['type' => 'checkbox', 'value' => 1, 'checked' => $option_data['option']['rewrite']['hierarchical'] ?? 0]
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

    $all_option_data = get_option(WWPO_Custom::OPTION_CONTENTS_DATA);

    /**
     * 遍历自定义内容数组
     *
     * @property array $option_data
     * {
     *  自定义内容数组
     *  @var string type    自定义内容类型
     *  @var string slug    自定义内容别名
     *  @var string title   自定义内容标题
     * }
     */
    foreach ($all_option_data as $option) {
        if ('posttype' != $option['type']) {
            continue;
        }

        $posttype_data[$option['slug']] = $option['title'];
    }

    /** 判断 posttype 内容数组为空显示新建 posttype 链接 */
    if (empty($posttype_data)) {
        $taxonomy_basic['formdata']['object_type']['content'] = sprintf(__('没有找到任何文章类型。<a href="%s">点击新建</a>', 'wwpo'), '?page=wwpo-contents');
    } else {

        /**
         * 遍历 posttype 内容数组
         *
         * @property string $posttype_key   内容别名
         * @property string $posttype_title 内容标题
         */
        foreach ($posttype_data as $posttype_key => $posttype_title) {

            // 通过分类法 object_type 参数，判断文章类型是否被选中
            if (empty($option_data['option']['object_type'])) {
                $checked = 0;
            } else {
                $checked = in_array($posttype_key, explode(',', $option_data['option']['object_type'])) ? 1 : 0;
            }

            $taxonomy_basic['formdata']['object_type']['fields']['object_type-' . $posttype_key] = [
                'title' => $posttype_title,
                'field' => [
                    'type'      => 'checkbox',
                    'name'      => 'post_data[option][object_type]',
                    'value'     => $posttype_key,
                    'checked'   => $checked
                ]
            ];
        }
    }

    echo WWPO_Form::table($taxonomy_basic, false);
    echo WWPO_Form::table($taxonomy_permalinks, false);
}
