<?php

/**
 * 用户函数类
 *
 * @since 2.0.0
 * @package Webian WordPress One
 */
class WWPO_User
{
    /**
     * 获取用户头像地址
     *
     * @since 1.0.0
     * @param integer   $user_id    用户 ID，默认值：0（当前用户）
     * @param integer   $size       头像尺寸（px），默认值：96
     */
    function avatar($user_id = 0, $size = 96)
    {
        if (empty($user_id)) {
            $avater = get_user_meta(get_current_user_id(), 'headimgurl', true);
        } else {
            $avater = get_user_meta($user_id, 'headimgurl', true);
        }

        if (empty($avater)) {
            return get_stylesheet_directory_uri() . '/assets/images/avatar.png';
        } else {
            return ltrim(preg_replace('/(.*)\/{1}([^\/]*)/i', '$1', $avater) . '/' . $size, 'http:');
        }
    }

    /**
     * 获取当前用户信息
     *
     * @since 1.0.0
     * @param string    $key    需要获取值的字段
     * - user_login        登录名
     * - user_email        邮箱
     * - user_firstname    姓
     * - user_lastname     名
     * - display_name      显示名称
     * - ID                用户 ID
     */
    function current($key)
    {
        global $current_user;
        $user = $current_user->$key;
        if ($user) return $user;
    }

    /**
     * 设置用户登录
     *
     * @since 1.0.0
     * @param integer $user_id 登录用户 ID
     */
    function login($user_id)
    {
        $user = get_user_by('id', $user_id);
        wp_set_current_user($user_id, $user->user_login);
        wp_set_auth_cookie($user_id, true, is_ssl());
        do_action('wp_login', $user->user_login);
    }
}
