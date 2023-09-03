<?php

/**
 * 自定义 WP 菜单参数编辑内容
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
 * 1、基本设置：$wpmenu_basic
 */
function wwpo_admin_display_content_wpmenu($option_data)
{
    $wpmenu_basic['title']      = __('菜单', 'wwpo');
    $wpmenu_basic['formdata']   = [
        'post_data[enable]' => [
            'title' => __('内容状态', 'wwpo'),
            'field' => [
                'type'      => 'select',
                'option'    => [__('禁用', 'wwpo'),  __('启用', 'wwpo')],
                'selected'  => $option_data['enable'] ?? 0
            ]
        ],
        'post_data[title]' => [
            'title' => __('菜单标题', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['title']]
        ],
        'post_data[option][menu][echo]' => [
            'title' => __('菜单输出', 'wwpo'),
            'desc'  => __('直接显示导航菜单还是返回 HTML 片段，如果想将导航的代码作为赋值使用，可设置为否。', 'wwpo'),
            'field' => [
                'type'      => 'select',
                'option'    => [__('否', 'wwpo'),  __('是', 'wwpo')],
                'value'     => $option_data['option']['menu']['echo'] ?? 0
            ]
        ],
        'post_data[option][menu][class]' => [
            'title' => __('菜单 Class', 'wwpo'),
            'desc'  => __('用于形成菜单的 ul 元素的 CSS 类。默认为：menu。', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['option']['menu']['class'] ?? '']
        ],
        'post_data[option][menu][id]' => [
            'title' => __('菜单 ID', 'wwpo'),
            'desc'  => __('应用于构成菜单的 ul 元素的 ID。', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['option']['menu']['id'] ?? '']
        ],
        'post_data[option][container][class]' => [
            'title' => __('封装标签 Class', 'wwpo'),
            'desc'  => __('用于封装标签元素的 CSS 类。默认值：{menu_slug}-navigation。', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['option']['container']['class'] ?? '']
        ],
        'post_data[option][container][id]' => [
            'title' => __('封装标签 ID', 'wwpo'),
            'desc'  => __('应用于构成封装元素的 ID。默认值：{menu_slug}-navigation。', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['option']['container']['id'] ?? '']
        ],
        'post_data[option][before]' => [
            'title' => __('链接前文字', 'wwpo'),
            'desc'  => __('链接标记之前的文本。', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['option']['before'] ?? '']
        ],
        'post_data[option][after]' => [
            'title' => __('链接后文字', 'wwpo'),
            'desc'  => __('链接标记之后的文本。', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['option']['after'] ?? '']
        ],
        'post_data[option][link_before]' => [
            'title' => __('链接文字前添加', 'wwpo'),
            'desc'  => __('文本在链接文本之前。', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['option']['link_before'] ?? '']
        ],
        'post_data[option][link_after]' => [
            'title' => __('链接文字后添加', 'wwpo'),
            'desc'  => __('文本在链接文本之后。', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['option']['link_after'] ?? '']
        ],
        'post_data[option][depth]' => [
            'title' => __('菜单显示深度', 'wwpo'),
            'desc'  => __('要包含多少层次结构级别。0 意味着所有。默认为 0。', 'wwpo'),
            'field' => ['type' => 'number', 'value' => $option_data['option']['depth'] ?? 0]
        ],
        'post_data[option][items_wrap]' => [
            'title' => __('菜单显示格式', 'wwpo'),
            'desc'  => __('如何包装列表项。默认是一个带有 id 和类的 ul。使用带有编号占位符的 printf() 格式。例如：<code>&lt;ul id="%1$s" class="%2$s"&gt;%3$s&lt;/ul&gt;</code>', 'wwpo'),
            'field' => ['type' => 'text', 'value' => $option_data['option']['items_wrap'] ?? '']
        ]
    ];

    echo WWPO_Form::table($wpmenu_basic, false);
}
