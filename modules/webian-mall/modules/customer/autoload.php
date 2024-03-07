<?php


define('WWPO_SQL_CUSTOMER', 'wwpo_customer');
define('WWPO_SQL_ADDRESS', 'wwpo_user_address');

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_customer_admin_menu($menus)
{
    $menus['customer'] = [
        'parent'        => 'webian-wordpress-one',
        'menu_title'    => __('客户管理', 'wwpo'),
        'page_title'    => __('所有客户', 'wwpo'),
        'label_title'   => __('客户', 'wwpo'),
        'role'          => 'edit_posts',
        'post_new'      => true
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_customer_admin_menu');

/**
 * Undocumented function
 *
 * @param [type] $user_login
 * @return void
 */
function wwpo_wxapps_customer_user_login($user_login)
{
    $address_data = wwpo_get_post_by_wheres(WWPO_SQL_ADDRESS, [
        'user_id'       => ['value' => $user_login['id']],
        'is_default'    => ['value' => 1]
    ]);

    if ($address_data) {
        $user_login['address'] = $address_data[0];
    }

    return $user_login;
}
add_filter('wwpo_wxapps_user_login', 'wwpo_wxapps_customer_user_login');
