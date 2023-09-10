<?php

function wwpo_wxapps_display_category()
{
    if (!current_user_can('edit_posts')) {
        wp_die(__('您没有权限访问此页面。', 'wwpo'));
    }

    $option_data = get_option(WWPO_Weapp::KEY_OPTION);
    $option_data = $option_data['category'] ?? [];
    $option_data = wp_list_sort($option_data, 'menu_order');

    echo WWPO_Table::result($option_data, [
        'column'    => [
            'thumb-apps'    => __('封面', 'wpmall'),
            'title-apps'    => __('Title'),
            'guid'          => __('链接地址', 'wpmall'),
        ]
    ]);
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_wxapps_display_category_edit()
{
    wp_enqueue_media();

    $option_data = get_option(WWPO_Weapp::KEY_OPTION);
    $option_data = $option_data['category'] ?? [];

    //
    $post_id = WWPO_Admin::post_id(0);

    if (empty($post_id)) {
        $post_id = wwpo_unique(2, 12);
    }

    $option_data = $option_data[$post_id] ?? [];

    /**  */
    if (empty($option_data) && 'new' != $post_id) {
        echo WWPO_Admin::messgae('error', '未找到相关内容');
        return;
    }
    //
    else {
        $option_data = wp_parse_args($option_data, [
            'thumb'         => 0,
            'title'         => '',
            'menu_order'    => 0,
            'guid'          => ''
        ]);
    }

    //
    $array_banner_form['hidden'] = [
        'post_key'  => 'category',
        'post_id'   => $post_id,
        'thumb_id'  => $option_data['thumb']
    ];

    //
    $array_banner_form['submit'] = 'updatewxapps';

    //
    $array_banner_form['formdata'] = [
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
        'updated[menu_order]' => [
            'title' => '分类排序',
            'field' => ['type' => 'number', 'value' => $option_data['menu_order']]
        ]
    ];

    if ($option_data['thumb']) {
        $array_banner_form['formdata']['cover']['content'] .= sprintf('<figure id="wwpo-thumb-figure" class="figure m-0 w-50"><img src="%s" class="figure-img img-fluid rounded"></figure>', wp_get_attachment_url($option_data['thumb']));
    }

    echo WWPO_Form::table($array_banner_form);
}
