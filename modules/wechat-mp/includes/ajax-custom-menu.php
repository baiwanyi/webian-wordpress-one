<?php

/**
 * 微信自定义菜单 AJAX 操作
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_wechat_custommenu_update($request)
{
    $option = get_option(WWPO_Wechat::KEY_WECHAT_MENU);

    $post_action    = $request['post_action'];
    $page_url       = $request['page_url'];
    $menu_data      = $request['menu'];
    $menu_key       = $request['menu_id'];
    $menu_parent    = $request['parent'];

    if (empty($menu_data['name'])) {
        return wwpo_error_403('not_null_content', __('菜单名称', 'wwpo'));
    }

    if (empty($menu_data['sort'])) {
        $menu_data['sort'] = 1;
    }

    if ('new' == $post_action) {
        $menu_key = wp_create_nonce($menu_data['name'] . NOW);
    }

    if (empty($menu_parent) || $menu_key == $menu_parent) {

        $page_url = add_query_arg('post', $menu_key, $page_url);

        if (isset($option[$menu_key]['submenu'])) {
            $menu_data['submenu'] = $option[$menu_key]['submenu'];
        }

        $option[$menu_key] = $menu_data;
    } else {
        $page_url = add_query_arg('post', $menu_parent, $page_url);
        $page_url = add_query_arg('menu', $menu_key, $page_url);
        $option[$menu_parent]['submenu'][$menu_key] = $menu_data;
    }

    update_option(WWPO_Wechat::KEY_WECHAT_MENU, $option);

    $page_url = add_query_arg('action', 'edit', $page_url);

    if ('new' == $post_action) {
        return wwpo_error_403('reload_success_added', null, ['url' => $page_url]);
    }

    return wwpo_error_200('reload_success_updated', null, ['url' => $page_url]);
}
add_filter('wwpo_ajax_admin_wechatmenuupdate', 'wwpo_wechat_custommenu_update');

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_wechat_custommenu_delete($request)
{
    $option = get_option(WWPO_Wechat::KEY_WECHAT_MENU);

    $page_url       = $request['page_url'];
    $menu_key       = $request['menu_id'];
    $menu_parent    = $request['parent'];

    if (!empty($option[$menu_key]['submenu'])) {
        return wwpo_error_403('invalid_deleted');
    }

    if ($menu_parent == $menu_key) {
        unset($option[$menu_key]);
    } else {
        $page_url = add_query_arg('post', $menu_parent, $page_url);
        $page_url = add_query_arg('action', 'edit', $page_url);
        unset($option[$menu_parent]['submenu'][$menu_key]);
    }

    update_option(WWPO_Wechat::KEY_WECHAT_MENU, $option);

    return wwpo_error_200('reload_success_deleted', null, ['url' => $page_url]);
}
add_filter('wwpo_ajax_admin_wechatmenudelete', 'wwpo_wechat_custommenu_delete');

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_wechat_custommenu_create()
{
    $menu_create = WWPO_Wechat_Menu::create();

    if (empty($menu_create['errcode'])) {
        return wwpo_error_200('success', __('自定义菜单已同步到微信公众号。', 'wwpo'));
    }

    return wwpo_error_403('error', WWPO_Wechat::errcode($menu_create['errcode']));
}
add_filter('wwpo_ajax_admin_wechatmenucreate', 'wwpo_wechat_custommenu_create');
