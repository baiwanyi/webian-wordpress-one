<?php

/**
 * 技术文档展示页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage components
 */
add_filter('wwpo_menus', ['WWPO_Docs', 'admin_menu']);
add_filter('admin_body_class', ['WWPO_Docs', 'body_class']);
add_filter('wwpo_admin_head', ['WWPO_Docs', 'header'], 10, 2);
add_filter('wwpo_admin_page_title', ['WWPO_Docs', 'disabled_title']);
add_action('wwpo_admin_display_wwpo-docs', ['WWPO_Docs', 'display']);
add_filter('wwpo_admin_script', function ($localize_script) {
    $localize_script['markdown_base_url'] = WWPO_PLUGIN_URL;
    if (isset($_GET['tab'])) {
        $localize_script['markdown_base_url'] .= 'modules/' . $_GET['tab'] . '/';
    }
    return $localize_script;
});

/**
 * 技术文档展示类
 */
class WWPO_Docs
{
    /**
     * 页面别名
     *
     * @since 1.0.0
     * @var string
     */
    const PAGE_NAME = 'wwpo-docs';

    /**
     * 添加后台管理菜单
     *
     * @since 1.0.0
     * @param array $menus 菜单内容数组
     * @return array
     */
    static function admin_menu($menus)
    {
        $menus[self::PAGE_NAME] = [
            'parent'        => 'webian-wordpress-one',
            'menu_title'    => __('开发文档', 'wwpo')
        ];

        return $menus;
    }

    /**
     * 页面 Body 标签添加 CSS 样式
     *
     * @since 1.0.0
     * @param string $classes
     * @return string
     */
    static function body_class($classes)
    {
        if (!WWPO_Admin::is_page(self::PAGE_NAME)) {
            return $classes;
        }

        return 'wwpo__admin-wide';
    }

    /**
     * 添加自定义页面头部内容函数
     *
     * @since 1.0.0
     * @param string $current_page_title    当前页面标题
     * @param string $current_page_name     当前页面别名
     * @return string
     */
    static function header($current_page_name, $current_page_title)
    {
        if (self::PAGE_NAME != $current_page_name) {
            return;
        }

        $current_tab    = $_GET['tab'] ?? 'wwpo';

        echo '<div class="wwpo__admin-header">';
        echo '<div class="wwpo__admin-header__section"><span class="dashicons dashicons-cloud"></span></div>';
        printf('<div class="wwpo__admin-header__section"><h1>%s</h1></div>', $current_page_title);

        echo '<nav class="wwpo__admin-header__tabs">';

        $tabs = apply_filters('wwpo_docs_tabs', ['wwpo' => '首页']);

        $tab_url = add_query_arg('page', self::PAGE_NAME);

        foreach ($tabs as $tab_key => $tab_title) {

            $tab_active = ($current_tab == $tab_key) ? ' active' : '';

            if ('wwpo' == $tab_key) {
                printf('<a href="%s" class="wwpo__admin-header__tabs-item%s">%s</a>', remove_query_arg('tab', $tab_url), $tab_active, $tab_title);
                continue;
            }

            printf('<a href="%s" class="wwpo__admin-header__tabs-item%s">%s</a>', add_query_arg('tab', $tab_key, $tab_url), $tab_active, $tab_title);
        }

        echo '</nav>';
        echo '</div>';
    }

    /**
     * 页面内容显示函数
     *
     * @since 1.0.0
     * @return string
     */
    static function display()
    {
        echo '<main id="wwpo-admin-docs" class="wwpo__admin-body"><p><span class="wwpo-loading small"></span></p></main>';
    }

    /**
     * 禁止当前页面显示标题
     *
     * @since 1.0.0
     * @param string $current_page_name 当前页面别名
     * @return boolean
     */
    static function disabled_title($current_page_name)
    {
        if (self::PAGE_NAME == $current_page_name) {
            return false;
        }
    }
}
