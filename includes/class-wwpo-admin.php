<?php

/**
 * 后台管理页面方法类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */
class WWPO_Admin
{
    /**
     * 后台页面主体内容
     *
     * @since 1.0.0
     */
    static function display()
    {
        $current_page_name  = self::page_name();
        $current_page_title = get_admin_page_title();

        /**
         * 自定义后台页面头部内容
         *
         * @since 1.0.0
         * @param string $current_page_name 当前页面别名
         */
        do_action('wwpo_admin_head', $current_page_name, $current_page_title);

        echo '<div class="wrap">';

        /**
         * 自定义后台页面头部内容
         *
         * @since 1.0.0
         * @param string $current_page_name 当前页面别名
         */
        do_action('wwpo_admin_head_before', $current_page_name);

        /**
         * 自定义后台页面标题
         *
         * @since 1.0.0
         * @param string $current_page_title 当前页面标题内容
         */
        $current_page_title = apply_filters('wwpo_admin_page_title', $current_page_title, $current_page_name);

        /** 判断输出页面标题 H1 格式 */
        if ($current_page_title) {
            printf('<h1 class="wp-heading-inline">%s</h1>', $current_page_title);
        }

        /**
         * 页面标题后按钮接口
         *
         * @since 1.0.0
         * @property string $current_page_name    页面动作
         */
        do_action('wwpo_admin_page_button', $current_page_name);

        /**
         * 页面简介接口
         *
         * @since 1.0.0
         * @property string $current_page_name    页面动作
         */
        do_action('wwpo_admin_page_description', $current_page_name);

        // 输出分隔符
        echo '<hr class="wp-header-end">';

        /**
         * 自定义后台页面头部内容（后部分）
         *
         * @since 1.0.0
         * @param string $current_page_name 当前页面别名
         */
        do_action('wwpo_admin_head_after', $current_page_name);

        /**
         * 后台页面接口（公共接口）
         *
         * @since 1.0.0
         * @property string $current_page_name 页面别名
         */
        do_action('wwpo_admin_display', $current_page_name);

        /**
         * 后台页面接口（页面别名）
         *
         * @since 1.0.0
         * @property string $current_page_name 页面别名
         */
        do_action("wwpo_admin_display_{$current_page_name}");

        /**
         * 自定义后台页面头部内容
         *
         * @since 1.0.0
         * @param string $current_page_name 当前页面别名
         */
        do_action('wwpo_admin_footer', $current_page_name);

        echo '</div>';
    }

    /**
     * 获取当前页面别名，地址栏 page 参数
     *
     * @since 1.0.0
     * @param boolean $format 页面别名是否格式化，默认：false
     */
    static function page_name($format = false)
    {
        if (empty($_GET['page'])) {
            return;
        }

        $page_name = wp_unslash($_GET['page']);
        $page_name = plugin_basename($page_name);

        /** 判断是否页面别名格式化，即删除别名中的‘-’和‘_’ */
        if ($format) {
            return str_replace(['-', '_'], '', $page_name);
        }

        return $page_name;
    }

    /**
     * 判断当前页面是否是指定页面
     *
     * @since 1.0.0
     */
    static function is_page($page_name)
    {
        global $post;

        $is_page    = false;
        $post_type  = $post->post_type ?? '';
        $post_type  = $_GET['post_type'] ?? $post_type;

        if ($page_name == self::page_name()) {
            $is_page = true;
        }

        if (empty($post_type)) {
            return $is_page;
        }

        if ($page_name == $post_type) {
            $is_page = true;
        }

        return $is_page;
    }

    /**
     * 获取当前后台地址
     *
     * @since 1.0.0
     */
    static function page_url($param = null, $url = false)
    {
        $page_url_query = ['action', 'message', 'paged', 'post', 'search', 'select', 'tab', 's', 'view'];
        $page_url_query = apply_filters('wwpo_admin_page_url_query', $page_url_query);

        if (empty($param)) {
            return remove_query_arg($page_url_query, $url);
        }

        return remove_query_arg($param, $url);
    }

    /**
     * Undocumented function
     *
     * @param [type] $option_key
     * @param [type] $button_key
     * @param [type] $formdata
     * @return void
     */
    static function settings($option_key, $button_key, $formdata)
    {
        echo '<main class="wwpo__admin-body">';
        echo '<form id="wwpo-admin-form" class="wwpo__admin-content" method="POST" autocomplete="off">';

        echo WWPO_Form::hidden(['option_key' => $option_key]);

        foreach ($formdata as $admin_page) {
            echo WWPO_Form::table($admin_page);
        }

        echo WWPO_Form::submit($button_key);
        echo '</form>';

        if (1 < count($formdata)) {
            echo '<aside class="wwpo__admin-toc"><h4>页面导航</h4><ul>';
            foreach ($formdata as $admin_page) {
                printf('<li><a href="#%1$s" rel="anchor">%1$s</a></li>', $admin_page['title']);
            }
            echo '</ul></aside>';
        }

        echo '</main>';
    }
}
