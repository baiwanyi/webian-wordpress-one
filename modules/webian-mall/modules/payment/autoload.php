<?php

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_payment_admin_menu($menus)
{
    $menus['wwpo-payment'] = [
        'menu_title'    => __('收银台', 'wwpo'),
        'page_title'    => __('所有订单', 'wwpo'),
        'role'          => 'edit_posts',
        'post_new'      => true,
        'icon'          => 'cart'
    ];

    return $menus;
}
// add_filter('wwpo_menus', 'wwpo_payment_admin_menu');


/**
 * 注册 Rest API 接口
 *
 * @since 1.0.0
 */
function wwpo_wpmall_payment_rest_register_routes()
{
    // 支付相关接口
    $payment_rest = new wwpo_wpmall_payment_rest_controller();
    $payment_rest->register_routes();

    // 发货地址相关接口
    $address_rest = new wwpo_wpmall_address_rest_controller();
    $address_rest->register_routes();
}
add_action('rest_api_init', 'wwpo_wpmall_payment_rest_register_routes');
