<?php

/**
 * 会员信息编辑内容函数
 *
 * @since 1.0.0
 * @param object $user  用户信息
 */
function wwpo_wpmall_payment_user_profile($user)
{
    if (!current_user_can(WWPO_ROLE)) {
        return;
    }

    $user_meta = get_user_meta($user->ID);

    $form_meta['title']     = '会员信息';
    $form_meta['formdata']  = [
        'user_commission_rate' => [
            'title' => '业务佣金比例',
            'field' => ['type' => 'number', 'value' => $user_meta['_wwpo_wpmall_user_rate'][0] ?? 10]
        ]
    ];

    echo WWPO_Form::table($form_meta);
}
add_action('edit_user_profile', 'wwpo_wpmall_payment_user_profile');
add_action('show_user_profile', 'wwpo_wpmall_payment_user_profile');

/**
 * 会员信息更新函数
 *
 * @since 1.0.0
 * @param integer $user_id  用户 ID
 */
function wwpo_wpmall_payment_user_update_profile($user_id)
{
    if (!current_user_can(WWPO_ROLE)) {
        return;
    }

    update_user_meta($user_id, '_wwpo_wpmall_user_rate', $_POST['user_commission_rate']);
}
add_action('edit_user_profile_update', 'wwpo_wpmall_payment_user_update_profile');
add_action('personal_options_update', 'wwpo_wpmall_payment_user_update_profile');
