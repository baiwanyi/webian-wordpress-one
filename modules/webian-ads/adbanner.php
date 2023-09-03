<?php

/**
 * 通栏广告页面
 *
 * @since 1.0.0
 * @package Webian WP Mall
 */

/**
 * 通栏广告显示页面函数
 *
 * @since 1.0.0
 * @param string $page_action
 */
function wwpo_adbanner_admin_display($page_action)
{
    if (empty($page_action)) {
        wwpo_adbanner_page_table();
        return;
    }

    wwpo_adbanner_page_display();
}
add_action('wwpo_admin_display_banner', 'wwpo_adbanner_admin_display');

/**
 * 通栏广告列表内容显示函数
 *
 * @since 1.0.0
 */
function wwpo_adbanner_page_table()
{
    $wheres = [];
    $settings = [
        'column'    => [
            'thumb'     => __('封面', 'wpmall'),
            'title'     => __('Title'),
            'page'      => __('广告位置', 'wpmall'),
            'guid'      => __('链接地址', 'wpmall'),
            'endtime'   => __('剩余广告时间', 'wpmall'),
            'date'      => __('Date')
        ]
    ];

    echo WWPO_Table::select(WWPO_SQL_ADBANNER, $wheres,  $settings);
}

/**
 * 自定义内容表格列输出函数
 *
 * @since 1.0.0
 * @param array     $data           表格列值
 * @param string    $column_name    表格列名称
 */
function wwpo_adbanner_table_column($data, $column_name)
{
    switch ($column_name) {
        case 'title':
            echo WWPO_Admin::title($data['ID'], $data['banner_title'], 'edit');
            break;

        case 'page':
            echo wwpo_adbanner_adsense($data['adsense']);
            break;

        case 'thumb':
            if (empty($data['thumb_id'])) {
                return;
            }

            printf(
                '<div class="ratio ratio-1x1"><img src="%s" class="thumb rounded"></div>',
                wp_get_attachment_image_url($data['thumb_id'])
            );
            break;

        case 'endtime':

            $day_start  = str_replace('-', '', $data['banner_start']);
            $day_end    = str_replace('-', '', $data['banner_end']);
            $day_now    = date('Ymd', NOW);
            $day_limit  = $day_end - $day_start;

            if ($day_end < $day_now) {
                echo '已过期';
                return;
            }

            if (3 >= $day_limit) {
                printf('<span class="text-danger">%s</span>', $day_limit);
                return;
            }

            printf('<span class="text-success">%s</span>', $day_limit);
            break;

        default:
            break;
    }
}
add_action('wwpo_table_banner_custom_column', 'wwpo_adbanner_table_column', 10, 2);

/**
 * 通栏广告编辑表单显示函数
 *
 * @since 1.0.0
 */
function wwpo_adbanner_page_display()
{
    wp_enqueue_media();

    //
    $post_id    = WWPO_Admin::post_id(0, true);
    $post_data  = [
        'thumb_id'          => 0,
        'adsense'           => '',
        'banner_title'      => '',
        'banner_content'    => '',
        'menu_order'        => 0,
        'color'             => '',
        'guid'              => '',
        'banner_start'      => date('Y-m-d', NOW),
        'banner_end'        => date('Y-m-d', NOW),
    ];

    /**  */
    if ($post_id) {
        $post_data = wwpo_get_row(WWPO_SQL_ADBANNER, 'ID', $post_id, null, ARRAY_A);
    }

    /**  */
    if (empty($post_data)) {
        echo WWPO_Admin::messgae('error', '未找到相关广告内容');
        return;
    }

    //
    $array_banner_form['hidden'] = [
        'post_id'   => $post_id,
        'thumb_id'  => $post_data['thumb_id']
    ];

    //
    $array_banner_form['submit'] = 'updateadbanner';

    //
    $array_banner_form['formdata'] = [
        'updated[banner_title]' => [
            'title' => '广告标题',
            'field' => ['type' => 'text', 'value' => $post_data['banner_title']]
        ],
        'updated[banner_content]' => [
            'title' => '广告内容',
            'field' => ['type' => 'textarea', 'value' => $post_data['banner_content']]
        ],
        'banner_cover' => [
            'title'     => '封面',
            'content'   => '<p id="wwpo-thumb-uploader" class="mb-3"><button type="button" class="btn btn-outline-primary" data-action="thumbuploader">选择封面</button></p><p class="description mb-2">图标建议尺寸：600px * 800px 或 3：4 比例</p>'
        ],
        'updated[guid]' => [
            'title' => '广告链接',
            'field' => ['type' => 'text', 'value' => $post_data['guid']]
        ],
        'updated[adsense]' => [
            'title' => '广告位置',
            'field' => [
                'type'          => 'select',
                'option'        => wwpo_adbanner_adsense(),
                'show_option_all' => '选择广告展示位置',
                'selected'      => $post_data['adsense']
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
                'selected'  => $post_data['color']
            ]
        ],
        'updated[menu_order]' => [
            'title' => '广告权重',
            'field' => ['type' => 'number', 'value' => $post_data['menu_order']]
        ],
        'updated' => [
            'title'     => '投放日期',
            'fields'    => [
                'updated[banner_start]' => [
                    'title' => '起始日期',
                    'field' => ['type' => 'date', 'value' => $post_data['banner_start']]
                ],
                'updated[banner_end]' => [
                    'title' => '结束日期',
                    'field' => ['type' => 'date', 'value' => $post_data['banner_end']]
                ],
            ]
        ]
    ];

    if ($post_data['thumb_id']) {
        $array_banner_form['formdata']['banner_cover']['content'] .= sprintf('<figure id="wwpo-thumb-figure" class="figure m-0 w-50"><img src="%s" class="figure-img img-fluid rounded"></figure>', wp_get_attachment_url($post_data['thumb_id']));
    }

    echo WWPO_Form::table($array_banner_form);
}

/**
 * 广告展示位置内容函数
 *
 * @since 1.0.0
 * @param string $key
 */
function wwpo_adbanner_adsense($key = null)
{
    $banner_adsense = [
        'home'  => '首页',
        'list'  => '列表页'
    ];

    if (empty($key)) {
        return $banner_adsense;
    }

    return $banner_adsense[$key] ?? '';
}

/**
 * 设置页面消息函数
 *
 * @since 1.0.0
 * @param array $message
 */
function wwpo_adbanner_message($message)
{
    $message['banner'] = [
        'not_found_data'    => ['error'     => '没要找到保存的数据内容。'],
        'success_added'     => ['updated'   => '通栏广告添加成功'],
        'success_updated'   => ['updated'   => '通栏广告内容已更新'],
    ];

    return $message;
}
add_filter('wwpo_admin_message', 'wwpo_adbanner_message');

/**
 * 通栏广告内容更新操作函数
 *
 * @since 1.0.0
 */
function wwpo_adbanner_post_update()
{
    //
    $post_id = $_POST['post_id'] ?? 0;
    $updated = $_POST['updated'] ?? [];

    $updated['thumb_id'] = $_POST['thumb_id'] ?? 0;

    /**  */
    if (empty($updated)) {
        new WWPO_Error('message', 'not_found_data', ['post' => 'new']);
        return;
    }

    /** 判断 $post_id 为空，自定义内容新增操作 */
    if (empty($post_id)) {

        //
        $updated['banner_name'] = wwpo_random(12, 'XQH');
        $updated['user_post']   = get_current_user_id();
        $updated['time_post']   = NOW_TIME;

        //
        $post_id = wwpo_insert_post(WWPO_SQL_ADBANNER, $updated);

        //
        new WWPO_Error('message', 'success_added', [
            'post'      => $post_id,
            'action'    => 'edit'
        ]);
        return;
    }

    // 以 $post_id 为 KEY 写入数组
    $updated['time_modified'] = NOW_TIME;

    // 更新到数据库
    wwpo_update_post(WWPO_SQL_ADBANNER, $updated, ['ID' => $post_id]);

    // 返回更新成功信息
    new WWPO_Error('message', 'success_updated', [
        'post'      => $post_id,
        'action'    => 'edit'
    ]);
}
add_action('wwpo_post_admin_updateadbanner', 'wwpo_adbanner_post_update');
