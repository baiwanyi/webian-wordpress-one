<?php

/**
 * 通栏广告页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat/wxapps
 */

/**
 * 广告展示列表显示函数
 *
 * @since 1.0.0
 */
function wwpo_wxapps_display_banner($option_data)
{
    if (!current_user_can('edit_posts')) {
        wp_die(__('您没有权限访问此页面。', 'wwpo'));
    }

    $option_data = $option_data['banner'] ?? [];
    $option_data = wp_list_sort($option_data, 'menu_order');

    echo WWPO_Table::result($option_data, [
        'column'    => [
            'thumb-apps'    => __('封面', 'wwpo'),
            'title-apps'    => __('Title'),
            'adsense-apps'  => __('广告位置', 'wwpo'),
            'guid'          => __('链接地址', 'wwpo'),
            'endtime'       => __('剩余广告时间', 'wwpo')
        ]
    ]);
}

/**
 * 广告展示内容编辑函数
 *
 * @since 1.0.0
 */
function wwpo_wxapps_display_banner_edit($option_data)
{
    wp_enqueue_media();

    $post_id        = WWPO_Admin::post_id(0);
    $option_data    = $option_data['banner'] ?? [];
    $option_data    = $option_data[$post_id] ?? [];

    /** 判断广告内容 */
    if (empty($option_data) && 'new' != $post_id) {
        echo WWPO_Admin::messgae('error', '未找到相关内容');
        return;
    }

    if ('new' == $post_id) {
        $post_id = wwpo_unique(1, 12);
    }

    $option_data = wp_parse_args($option_data, [
        'thumb'         => 0,
        'adsense'       => '',
        'title'         => '',
        'content'       => '',
        'menu_order'    => 0,
        'color'         => '',
        'guid'          => '',
        'start'         => date('Y-m-d', NOW),
        'end'           => date('Y-m-d', NOW),
    ]);

    // 设定编辑表单
    $array_formdata = [
        'hidden' => [
            'post_key'          => 'banner',
            'post_id'           => $post_id,
            'updated[thumb]'    => $option_data['thumb']
        ],
        'submits' => [
            ['value' => 'updatewechat'],
            ['value' => 'deletewechat', 'text' => __('Delete'), 'css' => 'link-delete large'],
        ]
    ];

    $array_formdata['formdata'] = [
        'updated[title]' => [
            'title' => '广告标题',
            'field' => ['type' => 'text', 'value' => $option_data['title']]
        ],
        'updated[content]' => [
            'title' => '广告内容',
            'field' => ['type' => 'textarea', 'value' => $option_data['content']]
        ],
        'cover' => [
            'title'     => '封面',
            'content'   => '<p id="wwpo-thumb-uploader" class="mb-3"><button type="button" class="btn btn-outline-primary" data-action="thumbuploader">选择封面</button></p><p class="description mb-2">图标建议尺寸：600px * 800px 或 3：4 比例</p>'
        ],
        'updated[guid]' => [
            'title' => '广告链接',
            'field' => ['type' => 'text', 'value' => $option_data['guid']]
        ],
        'updated[adsense]' => [
            'title' => '广告位置',
            'field' => [
                'type'          => 'select',
                'option'        => wwpo_wxapps_banner_adsense(),
                'show_option_all' => '选择广告展示位置',
                'selected'      => $option_data['adsense']
            ]
        ],
        'updated[color]' => [
            'title' => '背景颜色',
            'field' => [
                'type'      => 'select',
                'option'    => [
                    'primary'   => '主色调',
                    'secondary' => '辅助色',
                    'danger'    => '红色',
                    'success'   => '绿色',
                    'info'      => '蓝色',
                    'warning'   => '黄色',
                    'dark'      => '深色',
                    'light'     => '浅色',
                    'white'     => '白色'
                ],
                'selected'  => $option_data['color']
            ]
        ],
        'updated[menu_order]' => [
            'title' => '广告权重',
            'field' => ['type' => 'number', 'value' => $option_data['menu_order']]
        ],
        'updated' => [
            'title'     => '投放日期',
            'fields'    => [
                'updated[start]' => [
                    'title' => '起始日期',
                    'field' => ['type' => 'date', 'value' => $option_data['start']]
                ],
                'updated[end]' => [
                    'title' => '结束日期',
                    'field' => ['type' => 'date', 'value' => $option_data['end']]
                ],
            ]
        ]
    ];

    if ($option_data['thumb']) {
        $array_formdata['formdata']['cover']['content'] .= sprintf('<figure id="wwpo-thumb-figure" class="figure m-0 w-50"><img src="%s" class="figure-img img-fluid rounded"></figure>', wp_get_attachment_url($option_data['thumb']));
    }

    echo WWPO_Form::table($array_formdata);
}

/**
 * 广告展示位置内容函数
 *
 * @since 1.0.0
 * @param string $key
 */
function wwpo_wxapps_banner_adsense($key = null)
{
    $banner_adsense = [
        'home'  => '首页',
        'list'  => '列表页'
    ];

    if (isset($key)) {
        return $banner_adsense[$key] ?? '';
    }

    return $banner_adsense;
}
