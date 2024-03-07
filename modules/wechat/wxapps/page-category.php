<?php

/**
 * 首页分类页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat/wxapps
 */

/**
 * 首页分类列表显示函数
 *
 * @since 1.0.0
 */
function wwpo_wxapps_display_category()
{
    if (!current_user_can('edit_posts')) {
        wp_die(__('您没有权限访问此页面。', 'wwpo'));
    }

    $option_data = get_option(WECHAT_KEY_OPTION, []);
    $option_data = $option_data['category'] ?? [];
    $option_data = wp_list_sort($option_data, 'menu_order');

    echo WWPO_Table::result($option_data, [
        'column'    => [
            'thumb-apps'    => __('封面', 'wpmall'),
            'title-apps'    => __('Title'),
            'guid'          => __('链接地址', 'wpmall'),
            'small-groups'   => '分组'
        ]
    ]);
}

/**
 * 首页分类内容编辑函数
 *
 * @since 1.0.0
 */
function wwpo_wxapps_display_category_edit($option_data)
{
    wp_enqueue_media();

    $post_id        = WWPO_Admin::post_id(0);
    $option_data    = $option_data['category'] ?? [];
    $option_data    = $option_data[$post_id] ?? [];

    /** 判断首页分类内容 */
    if (empty($option_data) && 'new' != $post_id) {
        echo WWPO_Admin::messgae('error', '未找到相关内容');
        return;
    }

    if ('new' == $post_id) {
        $post_id = wwpo_unique(2, 12);
    }

    $option_data = wp_parse_args($option_data, [
        'thumb'         => 0,
        'title'         => '',
        'menu_order'    => 0,
        'group'         => 1,
        'guid'          => ''
    ]);


    // 设定编辑表单
    $array_formdata = [
        'hidden' => [
            'post_key'          => 'category',
            'post_id'           => $post_id,
            'thumb_id'    => $option_data['thumb']
        ],
        'submits' => [
            ['value' => 'updatewechat'],
            ['value' => 'deletewechat', 'text' => __('Delete'), 'css' => 'link-delete large'],
        ]
    ];

    $array_formdata['formdata'] = [
        'updated[title]' => [
            'title' => '分类标题',
            'field' => ['type' => 'text', 'value' => $option_data['title']]
        ],
        'cover' => [
            'title'     => '封面',
            'content'   => '<p id="wwpo-thumb-uploader" class="mb-3"><button type="button" class="btn btn-outline-primary" data-action="thumbuploader">选择封面</button></p><p class="description mb-2">图标建议尺寸：600px * 600px 或 1：1 比例</p>'
        ],
        'updated[guid]' => [
            'title' => '分类链接',
            'field' => ['type' => 'text', 'value' => $option_data['guid']]
        ],
        'updated[group]' => [
            'title' => '分组',
            'field' => [
                'type'      => 'select',
                'option'    => wwpo_wxapps_category_get_group(),
                'selected'  => $option_data['group']
            ]
        ],
        'updated[menu_order]' => [
            'title' => '分类排序',
            'field' => ['type' => 'number', 'value' => $option_data['menu_order']]
        ]
    ];

    if ($option_data['thumb']) {
        $array_formdata['formdata']['cover']['content'] .= sprintf('<figure id="wwpo-thumb-figure" class="figure m-0 w-50"><img src="%s" class="figure-img img-fluid rounded"></figure>', wp_get_attachment_url($option_data['thumb']));
    }

    echo WWPO_Form::table($array_formdata);
}

/**
 * 首页分类内容分组函数
 *
 * @since 1.0.0
 * @param string $key
 */
function wwpo_wxapps_category_get_group($key = null)
{
    $group = ['默认位置', '位置一', '位置二', '位置三', '位置四', '位置五'];

    if (isset($key)) {
        return $group[$key] ?? '未分组';
    }

    return $group;
}
