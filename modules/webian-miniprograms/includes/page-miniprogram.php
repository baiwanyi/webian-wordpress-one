<?php

function wwpo_miniprograms_admin_display_list_table($option_data)
{
    $option_data = $option_data['apps'] ?? [];

    WWPO_Table::result($option_data, [
        'column' => [
            'miniprograms_title'    => '名称',
            'appid'                 => 'APPID',
            'small-apps_platform'   => '平台',
            'medium-expirestime'    => '密钥过期时间'
        ],
    ]);
}

/**
 * 自定义内容表格列输出函数
 *
 * @since 1.0.0
 * @param array     $data           表格列值
 * @param string    $column_name    表格列名称
 */
function wwpo_miniprograms_admin_table_column($data, $column_name)
{
    switch ($column_name) {

            //
        case 'miniprograms_title':
            $page_url = WWPO_Admin::page_url();
            echo WWPO_Admin::title($data['appid'], $data['title'], 'edit', $page_url);
            break;

            //
        case 'apps_platform':

            if (empty($data['platform'])) {
                echo '—';
                return;
            }

            echo wwpo_miniprograms_get_platform($data['platform']);
            break;

            //
        case 'expirestime':

            break;

        default:
            break;
    }
}
add_action('wwpo_table_miniprograms_custom_column', 'wwpo_miniprograms_admin_table_column', 10, 2);

/**
 * Undocumented function
 *
 * @param [type] $option_data
 * @return void
 */
function wwpo_miniprograms_admin_display_list_edit($option_data, $page_action, $post_id)
{
    if (!current_user_can('edit_posts')) {
        wp_die(__('您没有权限访问此页面。', 'wwpo'));
    }

    $current_data = $option_data['apps'][$post_id] ?? [];

    if (empty($current_data) && empty($page_action)) {
        wp_die(__('没有找到相关内容', 'wwpo'));
    }

    $invite_roles   = $current_data['inviteroles'] ?? [];
    $wp_roles       = new WP_Roles();
    $role_data      = [];

    foreach ($wp_roles->roles as $role_slug => $role_val) {

        if ('administrator' == $role_slug) {
            continue;
        }

        $role_data[$role_slug] = translate_user_role($role_val['name']);
    }

    $wxapps_setting_form = [
        'submit'    => [
            ['value' => 'updateminiprograms'],
            ['value' => 'deleteminiprograms', 'text' => __('Delete'), 'css' => 'link-delete large ms-2']
        ],
        'hidden'    => [
            'post_id'   => $post_id
        ],
        'formdata'  => [
            'updated[title]'  => [
                'title' => '小程序名称',
                'field' =>  ['type' => 'text', 'value' => $current_data['title'] ?? '']
            ],
            'updated[appid]'  => [
                'title' => '开发者ID（AppID）',
                'field' =>  ['type' => 'text', 'value' => $current_data['appid'] ?? '']
            ],
            'updated[appsecret]'  => [
                'title' => '开发者密码（AppSecret）',
                'field' => ['type' => 'text', 'value' => $current_data['appsecret'] ?? '']
            ],
            'accesstoken'    => [
                'title' => '接口调用凭据（Access Token）',
                'desc'  => '过期时间：' . (isset($current_data['tokenexpires']) ? date('Y-m-d H:i:s', $current_data['tokenexpires'] + 7200) : '没有记录'),
                'field' => ['type' => 'textarea', 'value' => ($current_data['accesstoken'] ?? ''), 'disabled' =>  true]
            ],
            'updated[platform]'  => [
                'title' => '小程序平台',
                'field' => [
                    'type'      => 'select',
                    'option'    => wwpo_miniprograms_get_platform(),
                    'selected'  => $current_data['platform'] ?? '',
                    'show_option_all'   => '选择应用平台'
                ]
            ],
            'inviteroles'  => [
                'title' => '应用邀请用户角色'
            ],
            'updated[register]'  => [
                'title' => '注册邀请用户角色',
                'field' => [
                    'type'      => 'select',
                    'option'    => $role_data,
                    'selected'  => $current_data['register'] ?? '',
                    'show_option_all'   => '选择注册用户'
                ]
            ]
        ]
    ];

    foreach ($role_data as $role_key => $role_name) {
        $wxapps_setting_form['formdata']['inviteroles']['fields'][$role_key] = [
            'title' => $role_name,
            'field' => ['type' => 'checkbox', 'name' => 'updated[inviteroles][]', 'value' => $role_key]
        ];

        if ($invite_roles) {
            if (in_array($role_key, $invite_roles)) {
                $wxapps_setting_form['formdata']['inviteroles']['fields'][$role_key]['field']['checked'] = 1;
            }
        }
    }

    echo WWPO_Form::table($wxapps_setting_form);
}

/**
 * 设置页面消息函数
 *
 * @since 1.0.0
 * @param array $message
 */
function wwpo_admin_message_miniprograms($message)
{
    $message['miniprograms'] = [
        'miniprograms_success_updated'  => ['updated' => __('小程序设置内容已更新', 'wwpo')],
        'miniprograms_delete_updated'   => ['updated' => __('小程序内容已删除', 'wwpo')]
    ];

    return $message;
}
add_filter('wwpo_admin_message', 'wwpo_admin_message_miniprograms');

/**
 * 微信内容更新操作函数
 *
 * @since 1.0.0
 */
function wwpo_admin_post_update_miniprograms()
{
    $option_data    = get_option(WWPO_KEY_MINIPROGRAMS, []);
    $post_id        = $_POST['post_id'] ?? 0;

    if ($post_id != $_POST['updated']['appid']) {
        $post_id = $_POST['updated']['appid'];
        unset($option_data['apps'][$post_id]);
    }

    $option_data['apps'][$post_id] = $_POST['updated'];

    wwpo_logs('admin:post:update:miniprograms:' . $post_id);
    update_option(WWPO_KEY_MINIPROGRAMS, $option_data);

    new WWPO_Error('message', 'miniprograms_success_updated', ['post' => $post_id, 'action' => 'edit']);
}
add_action('wwpo_post_admin_updateminiprograms', 'wwpo_admin_post_update_miniprograms');

/**
 * 微信内容删除操作函数
 *
 * @since 1.0.0
 */
function wwpo_admin_post_delete_miniprograms()
{
    $option_data    = get_option(WWPO_KEY_MINIPROGRAMS, []);
    $post_id        = $_POST['post_id'] ?? 0;

    unset($option_data['apps'][$post_id]);

    update_option(WWPO_KEY_MINIPROGRAMS, $option_data);

    // 设定日志
    wwpo_logs('admin:post:delete:miniprograms:' . $post_id);

    new WWPO_Error('message', 'miniprograms_delete_updated');
}
add_action('wwpo_post_admin_deleteminiprograms', 'wwpo_admin_post_delete_miniprograms');
