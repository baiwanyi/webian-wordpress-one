<?php

/**
 * 微信公众号 API 自定义菜单
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_Menu
{
    /**
     * 创建菜单接口
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Custom_Menus/Creating_Custom-Defined_Menu.html
     */
    static function create()
    {
        $menus = get_option(WWPO_Wechat::KEY_WECHAT_MENU, []);

        if (empty($menus)) {
            return;
        }

        $button = [];

        $menus = wp_list_sort($menus, 'sort', 'DESC');

        foreach ($menus as $menu_key => $menu_val) {
            $button[$menu_key] = ['name' => $menu_val['name']];

            if (empty($menu_val['submenu'])) {

                $button[$menu_key]['type'] = $menu_val['type'];

                switch ($menu_val['type']) {
                    case 'click':
                        $button[$menu_key]['key'] = $menu_val['content'];
                        break;
                    case 'view':
                        $button[$menu_key]['url'] = $menu_val['content'];
                        break;
                    default:
                        break;
                }

                continue;
            }

            $submenu = wp_list_sort($menu_val['submenu'], 'sort', 'DESC');

            foreach ($submenu as $submenu_key => $submenu_val) {

                $button[$menu_key]['sub_button'][$submenu_key] = [
                    'name'  => $submenu_val['name'],
                    'type'  => $submenu_val['type']
                ];

                switch ($submenu_val['type']) {
                    case 'click':
                        $button[$menu_key]['sub_button'][$submenu_key]['key'] = $submenu_val['content'];
                        break;
                    case 'view':
                        $button[$menu_key]['sub_button'][$submenu_key]['url'] = $submenu_val['content'];
                        break;
                    default:
                        break;
                }
            }
        }

        return WWPO_Wechat::curl('cgi-bin/menu/create', ['button' => $button]);
    }

    /**
     * 获取自定义菜单配置
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Custom_Menus/Getting_Custom_Menu_Configurations.html
     */
    static function get()
    {
        return WWPO_Wechat::curl('cgi-bin/menu/get');
    }

    /**
     * 删除菜单接口
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Custom_Menus/Deleting_Custom-Defined_Menu.html
     */
    static function delete()
    {
        return WWPO_Wechat::curl('cgi-bin/menu/delete');
    }
}
