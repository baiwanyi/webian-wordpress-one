<?php

/**
 * REST API 操作函数
 *
 * @package Webian WordPress One
 */

/**
 * 注册 Rest API 接口函数
 *
 * @since 1.0.0
 */
function wwpo_ajax_rest()
{
    /**
     * 注册前台 AJAX 操作 Rest API
     *
     * @since 1.0.0
     * @method /wwpo/ajax/action
     */
    register_rest_route('wwpo', 'ajax/action', [
        [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => 'wwpo_rest_ajax_post',
            'permission_callback'   => '__return_true',
        ], [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => 'wwpo_rest_ajax_get',
            'permission_callback'   => '__return_true',
        ]
    ]);
}
add_action('rest_api_init', 'wwpo_ajax_rest');

/**
 * 前端 AJAX POST 操作函数
 *
 * @since 1.0.0
 * @param array $request
 * {
 *  @var string action  AJAX 执行动作名称
 *  @var string token   验证字符串
 * }
 */
function wwpo_rest_ajax_post($request)
{
    /** 判断执行动作 */
    if (empty($request['action'])) {
        return wwpo_error_403('not_found_action');
    }

    /** 验证 AJAX 随机数，以防止非法的外部的请求 */
    if (!wwpo_check_ajax_nonce($request['token'])) {
        return wwpo_error_403('invalid_nonce');
    }

    /** 判断用户登录，写入当前用户 ID*/
    if (is_user_logged_in()) {
        $request['current_user_id'] = get_current_user_id();
    }

    /** 判断执行 AJAX 操作动作 */
    if (!has_filter("wwpo_ajax_post_{$request['action']}")) {
        return wwpo_error_403('not_found_action');
    }

    return apply_filters("wwpo_ajax_post_{$request['action']}", $request);
}

/**
 * 前端 AJAX GET 操作函数
 *
 * @since 1.0.0
 * @param array $request
 * {
 *  @var string action  AJAX 执行动作名称
 *  @var string token   验证字符串
 * }
 */
function wwpo_rest_ajax_get($request)
{
    /** 判断执行动作 */
    if (empty($request['action'])) {
        return wwpo_error_403('not_found_action');
    }

    /** 验证 AJAX 随机数，以防止非法的外部的请求 */
    if (!wwpo_check_ajax_nonce($request['token'])) {
        return wwpo_error_403('invalid_nonce');
    }

    /** 判断用户登录，写入当前用户 ID*/
    if (is_user_logged_in()) {
        $request['current_user_id'] = get_current_user_id();
    }

    /** 判断执行 AJAX 操作动作 */
    if (!has_filter("wwpo_ajax_get_{$request['action']}")) {
        return wwpo_error_403('not_found_action');
    }

    return apply_filters("wwpo_ajax_get_{$request['action']}", $request);
}

/**
 * 检测 AJAX 随机数函数
 *
 * @since 1.0.0
 * @param string $page
 */
function wwpo_check_rest_nonce($page)
{
    if (!check_ajax_referer($page)) {
        return wwpo_error_403('invalid_nonce');
    }

    return true;
}

/**
 * 判断用户登录函数
 *
 * @since 1.0.0
 * @param integer $user_id 用户 ID
 */
function wwpo_check_user_login($user_id = 0)
{
    if (empty($user_id)) {
        $user_id = get_current_user_id();
    }

    if (empty($user_id)) {
        return wwpo_error_401('error', __('当前用户未登录'));
    }

    return true;
}

/**
 * 设定页面 AJAX 操作 TOKEN 函数
 *
 * @since 1.0.0
 */
function wwpo_create_ajax_token()
{
    // 设定随机数数组，添加 sign（随机校验码） 和 t（当前时间戳） 参数
    $token['page']  = wwpo_get_page_name();
    $token['sign']  = wwpo_random();
    $token['t']     = NOW;

    // 排序参数数组，数组 KEY 升序
    ksort($token);

    // 添加生成随机数
    $token['nonce'] = wp_create_nonce(md5(implode('', $token)));

    return $token;
}

/**
 * 校验支付链接验证参数函数
 *
 * @since 1.0.0
 * - 解析请求地址并将请求参数转换成为数组
 * - 获取需要验证的随机数，并将其从数组中删除
 * - 排序参数数组
 * - 判断操作时间，超过当前时间 300 秒
 * - 验证随机数，MD5 加密合并后的参数数组值
 */
function wwpo_check_ajax_nonce($token)
{
    $timestamp = $token['t'] ?? 0;

    // 获取当前链接中的验证参数，并将其从数组中删除，不参与合并和取值
    $current_nonce = $token['nonce'] ?? 0;
    unset($token['nonce']);

    // 排序参数数组，数组 KEY 升序
    ksort($token);

    /** 判断操作时间 */
    if (300 > NOW - $timestamp) {
        return true;
    }

    /** 判断随机值并验证其正确性 */
    if (wp_verify_nonce($current_nonce, md5(implode('', $token)))) {
        return true;
    }

    return false;
}
