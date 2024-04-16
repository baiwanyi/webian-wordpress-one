<?php


/**
 * 微信自定义菜单界面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_wechat_custommenu($option_data)
{
    $parent_id  = WWPO_Admin::post_id();
    $page_url   = WWPO_Admin::page_url();
    $action     = WWPO_Admin::action('new');
    $menu_id    = $_GET['menu'] ?? $parent_id;
    $data = [
        'hidden'    => [
            'parent'        => $parent_id,
            'menu_id'       => $menu_id,
            'post_action'   => $action,
            'page_url'      => remove_query_arg('menu', $page_url)
        ],
        'menudata'  => $option_data['custommenu'] ?? []
    ];

    echo '<main id="col-container" class="wp-clearfix container-fluid p-0">';
    echo '<div id="col-left" class="col-wrap">';
    echo '<div id="wwpo__wechat-menu__iphone" class="device-ios">';
    echo '<div id="wwpo__wechat-menu__iphone-inner" class="device-inner">';

    /**
     *
     */
    wwpo_load_template('wechat/pages/custom-menu-phone', 'list', $data);

    /**
     *
     */
    wwpo_load_template('wechat/pages/custom-menu-phone', 'menu', $data);
    echo '</div><!-- /#wwpo__wechat-menu__iphone-inner -->';
    echo '</div><!-- /#wwpo__wechat-menu__iphone -->';
    echo '</div>';
    echo '<div id="col-right" class="col-wrap">';

    /**
     *
     */
    wwpo_load_template('wechat/pages/custom-menu', 'edit', $data);
    echo '</div>';
    echo '</main>';
}
add_action('wwpo_admin_display_custommenu', 'wwpo_admin_display_wechat_custommenu');

/**
 * 微信自定义菜单添加「同步」按钮
 *
 * @since 1.0.0
 * @param string $pagename  页面名称
 */
function wwpo_wechat_custom_menu_link($pagename)
{
    if ('custom-menu' != $pagename) {
        return;
    }

    // echo WWPO_Button::wp([
    //     'text'  => __('同步', 'wwpo'),
    //     'css'   => 'btn page-title-action',
    //     'value' => 'wechatmenucreate'
    // ]);
}
add_action('wwpo_admin_header_link', 'wwpo_wechat_custom_menu_link');
