<?php

/**
 * 会员信息编辑内容函数
 *
 * @since 1.0.0
 * @param object $user  用户信息
 */
function wwpo_weibo_user_profile($user)
{
    if (!current_user_can(WWPO_ROLE)) {
        return;
    }

    $weibo_api      = new WWPO_Weibo();
    $page_action    = $_GET['action'] ?? '';
    $redirect_uri   = admin_url('profile.php');

    /** 注销 Oauth2 动作 */
    if ('revokeoauth' == $page_action) {
        $weibo_api->revokeoauth2();
    }

    if (isset($_GET['code'])) {
        $weibo_api->access_token($redirect_uri);
    }

    // 获取用户 access_token 信息
    $weibo_uid          = get_user_meta($user->ID, WWPO_Weibo::KEY_UID, true);
    $weibo_accesstoken  = get_user_meta($user->ID, WWPO_Weibo::KEY_ACCESSTOKEN, true);

    /** 判断 UID 为空，显示绑定按钮 */
    if (empty($weibo_uid)) {
        $bindweibo = sprintf('<a href="%s" class="button">绑定微博</a>', $weibo_api->authorizeurl($redirect_uri));
    }
    // 已绑定显示 UID 和取消按钮
    else {
        $bindweibo = sprintf('<div class="d-flex align-items-center gap-3"><span class="lead">UID：<strong>%s</strong></span><a href="?action=revokeoauth" class="button">取消绑定</a></div>', $weibo_uid);
    }

    echo WWPO_Form::table([
        'title' => '微博绑定',
        'formdata'  => [
            'bindweibo' => [
                'title' => '绑定状态',
                'content' => $bindweibo
            ],
            'accesstoken' => [
                'title' => '接口调用凭据',
                'desc'  => '过期时间：' . (isset($weibo_accesstoken['expirestime']) ? date('Y-m-d H:i:s', $weibo_accesstoken['expirestime']) : '没有记录'),
                'field' => ['type' => 'text', 'value' => $weibo_accesstoken['accesstoken'] ?? '', 'disabled' => true]
            ]
        ]
    ], false);
}
add_action('show_user_profile', 'wwpo_weibo_user_profile');
