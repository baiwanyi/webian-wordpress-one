<?php

/**
 * 自定义用户显示列表
 *
 * @since 1.0.0
 * @package Webian WP Mall
 */

/**
 * 自定义用户列表函数
 *
 * @since 1.0.0
 */
function wwpo_wpmall_admin_user_columns($columns)
{
    $columns = [
        'cb'            => __('选择', 'wwpo'),
        'username'      => __('Username'),
        'displayname'   => __('Name'),
        'weapp'         => __('小程序绑定', 'wwpo'),
        'inviter'       => __('邀请人', 'wwpo'),
        'last_login'    => __('上次登录', 'wwpo')
    ];

    return $columns;
}
add_filter('manage_users_columns', 'wwpo_wpmall_admin_user_columns');

/**
 * 自定义表格列输出函数
 *
 * @since 1.0.0
 * @param string    $content    表格内容
 * @param string    $column     表格列名称
 * @param integer   $user_id    用户 ID
 */
function wwpo_wpmall_admin_user_custom_column($content, $column, $user_id)
{
    // 获取当前用户元数据
    $usermeta = get_user_meta($user_id);

    switch ($column) {

            // 显示名称
        case 'displayname':
            return get_the_author_meta('display_name', $user_id);
            break;

            // 绑定小程序
        case 'weapp':
            $user_openid = $usermeta[WECHAT_APP_OPENID][0] ?? 0;
            if (empty($user_openid)) {
                return '<span class="text-danger">未绑定</span>';
            }

            return '<span class="text-success">已绑定</span>';
            break;

            // 邀请用户
        case 'inviter':
            $user_inviter = $usermeta['_wwpo_invite_user'][0] ?? 0;
            if (empty($user_inviter)) {
                return '—';
            }

            return get_the_author_meta('display_name', $user_inviter);
            break;

            // 最后登录时间
        case 'last_login':
            return $usermeta['last_login'][0] ?? '<span style="color: gray">未登录</span>';
            break;

        default:
            break;
    }
}
add_action('manage_users_custom_column', 'wwpo_wpmall_admin_user_custom_column', 10, 3);

/**
 * Undocumented function
 *
 * @param [type] $user_login
 * @return void
 */
function wwpo_wpmall_get_user_product_favor($user_login)
{
    $user_favors = wwpo_get_post(WWPO_SQL_PR_FAVOR, 'user_id', $user_login['id']);

    if (empty($user_favors)) {
        return $user_login;
    }

    foreach ($user_favors as $item) {
        $user_login['favor'][$item->post_id] = [
            'name'  => $item->post_name,
            'thumb' => wwpo_oss_cdnurl($item->thumb_url, 'large'),
            'time'  => date('Y/m/d H:i:s', strtotime($item->time_post))
        ];
    }

    return $user_login;
}
add_filter('wwpo_wxapps_user_login', 'wwpo_wpmall_get_user_product_favor');
