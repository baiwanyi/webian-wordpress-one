<?php

/**
 * Undocumented function
 *
 * @since 1.0.0
 * @param array $option_data
 */
function wwpo_contents_page_list_table($option_data)
{
    echo '<main id="col-container" class="wp-clearfix container-fluid p-0"><div id="col-left" class="col-wrap">';

    //
    echo WWPO_Form::list([
        'title'     => __('添加新自定义内容', 'wwpo'),
        'submit'    => 'updatecontents',
        'formdata'  => [
            'post_data[title]' => [
                'title' => __('内容名称', 'wwpo'),
                'field' => ['type' => 'text']
            ],
            'post_data[slug]' => [
                'title' => __('内容别名', 'wwpo'),
                'field' => ['type' => 'text']
            ],
            'post_data[type]' => [
                'title' => __('内容类型', 'wwpo'),
                'field' => [
                    'type'          => 'select',
                    'option'        => WWPO_Custom::post_type(),
                    'show_option_all' => __('选择内容类型', 'wwpo')
                ]
            ],
            'import' => [
                'title' => __('导入配置', 'wwpo'),
                'field' => ['type' => 'textarea',]
            ]
        ]
    ]);

    echo '</div><div id="col-right" class="col-wrap">';

    //
    WWPO_Table::result($option_data, [
        'column' => [
            'content_title'     => '名称',
            'slug'              => '别名',
            'content_type'      => '类型',
            'content_status'    => '状态'
        ],
    ]);

    echo '</div></main>';
}


