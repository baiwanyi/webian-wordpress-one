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

/**
 * Undocumented function
 *
 * @since 1.0.0
 * @param string $which
 */
function wwpo_contents_extra_tablenav($which)
{
    if ('top' != $which) {
        return;
    }

    foreach ([
        'contentloadlocal'  => '读取本地设置',
        'contentsavelocal'  => '保存设置到本地',
        'contentempty'      => '清空设置'

    ] as $button_key => $button_text) {

        $button_css = 'button me-2';

        if ('contentempty' == $button_key) {
            $button_css .= ' button-link-delete';
        }

        printf(
            '<button type="button" data-action="wpajax" class="%s" value="%s">%s</button>',
            $button_css,
            $button_key,
            $button_text
        );
    }
}
add_action('wwpo_wwpo-contents_extra_tablenav', 'wwpo_contents_extra_tablenav');

/**
 * 自定义内容表格列输出函数
 *
 * @since 1.0.0
 * @param array     $data           表格列值
 * @param string    $column_name    表格列名称
 */
function wwpo_content_table_column($data, $column_name)
{
    switch ($column_name) {

            //
        case 'content_title':
            $page_url = WWPO_Admin::page_url();
            echo WWPO_Admin::title($data['ID'], $data['title'], 'edit', $page_url);
            break;

            //
        case 'content_type':
            echo WWPO_Custom::post_type($data['type']);
            break;

            //
        case 'content_status':
            if (empty($data['enable'])) {
                printf('<span class="text-black-50">%s</span>', __('已禁用', 'wwpo'));
            } else {
                printf('<span class="text-primary">%s</span>', __('已启用', 'wwpo'));
            }
            break;

        default:
            break;
    }
}
add_action('wwpo_table_wwpo-contents_custom_column', 'wwpo_content_table_column', 10, 2);
