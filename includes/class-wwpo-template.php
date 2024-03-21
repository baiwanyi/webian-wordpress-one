<?php

/**
 * WordPress 界面应用函数
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */
class WWPO_Template
{
    /**
     * 格式化 WP 分页函数
     * 使用 paginate_links 函数
     *
     * @since 1.0.0
     * @param integer   $paged      当前页码
     * @param integer   $maxpaged   页码总数
     * @param boolean   $format     页码格式
     */
    static function paged($paged, $maxpaged, $format = true)
    {
        global $wp_query;

        /** 设定当前页码 */
        if (empty($paged)) {
            $paged = 1;
        }

        // 设定分页参数数组
        $linkdata = [
            'mid_size'  => 1,
            'end_size'  => 2,
            'current'   => $paged,
            'total'     => $maxpaged,
            'base'      => null,
            'prev_text' => sprintf(
                '<span class="icon icon-%s"></span><span class="screen-reader-text visually-hidden">%s</span>',
                'left',
                __('上一页', 'wwpo')
            ),
            'next_text' => sprintf(
                '<span class="icon icon-%s"></span><span class="screen-reader-text visually-hidden">%s</span>',
                'right',
                __('下一页', 'wwpo')
            )
        ];

        /** 判断页码总数小于 5 页则显示全部页码 */
        if (5 >= $maxpaged) {
            $linkdata['show_all'] = true;
        }

        if (isset($wp_query->query['post_type'])) {
            $linkdata['base'] = home_url($wp_query->query['post_type']);
        }

        if (isset($wp_query->query['pagename'])) {
            $linkdata['base'] = home_url($wp_query->query['pagename']);
        }

        if (isset($wp_query->query['action'])) {
            $linkdata['base'] .= '/' . $wp_query->query['action'];
        }

        $linkdata['base'] .= '/%_%';

        if ($format) {
            $linkdata['format'] = 'paged-%#%';
        }

        // 显示页码
        return sprintf(
            '<div class="site-paged site-panel">%s</div>',
            paginate_links($linkdata)
        );
    }

    /**
     * Posts 分页函数
     * 使用 get_the_posts_pagination 函数
     *
     * @since 1.0.0
     */
    static function pagination()
    {
        $pagination = get_the_posts_pagination([
            'class'    => 'wwpo__site-paged',
            'mid_size'  => 1,
            'prev_text' => sprintf(
                '<span class="icon icon-%s"></span><span class="screen-reader-text visually-hidden">%s</span>',
                'left',
                __('上一页', 'wwpo')
            ),
            'next_text' => sprintf(
                '<span class="icon icon-%s"></span><span class="screen-reader-text visually-hidden">%s</span>',
                'right',
                __('下一页', 'wwpo')
            )
        ]);

        // 显示页码
        return sprintf('<div class="site-paged site-panel mt-5">%s</div>', $pagination);
    }

    /**
     * 后台界面筛选导航
     *
     * @since 1.0.0
     * @package Webian WordPress One
     *
     * @param array     $data
     * {
     *  筛选导航内容数组
     *  @var string title           搜索按钮标题
     *  @var string before          列表前内容
     *  @var string after           列表后内容
     *  @var array  links           列表内容数组
     *  @var array  typeselector    搜索类型列表内容数组
     * }
     * @param string    $page_url   默认链接
     */
    static function filter($data, $page_url = false)
    {
        // 设定当前页面地址
        $page_url = WWPO_Admin::page_url($page_url);

        // 设定默认标题
        $title = $data['title'] ?? __('搜索', 'wwpo');

        // 设定是否显示搜索栏
        $searchform = $data['searchform'] ?? false;

        echo '<div class="wp-filter">';

        /** 添加列表前内容 */
        if (isset($data['before'])) {
            printf('<div class="wp-filter-before">%s</div>', $data['before']);
        }

        echo ' <ul class="filter-links">';

        /** 判断链接列表内容 */
        if (isset($data['links'])) {

            // 设定当前选项卡别名
            $current = $_GET['tab'] ?? key($data['links']);

            /**
             * 遍历链接列表内容
             *
             * @property string $link_key    列表别名
             * @property string $link_title  列表标题
             */
            foreach ($data['links'] as $link_key => $link_title) {

                // 设定当前选中 CSS 样式
                $current_css = $current == $link_key ? 'current' : 'item';

                // 格式化显示列表内容
                printf(
                    '<li><a href="%1$s" class="%2$s">%3$s</a></li>',
                    esc_url(
                        add_query_arg('tab', $link_key, $page_url)
                    ),
                    esc_attr($current_css),
                    esc_attr($link_title)
                );
            }
        }

        echo '</ul>';

        /** 添加列表后内容 */
        if (isset($data['after'])) {
            printf('<div class="wp-filter-after">%s</div>', $data['after']);
        }

        /** 判断显示搜索表单 */
        if ($searchform) {
            echo '<form class="search-form" method="get" autocomplete="off">';
            echo '<input type="hidden" name="tab" value="search">';

            /** 判断筛选类型下拉列表 */
            if (isset($data['typeselector'])) {
                echo WWPO_Form::field('typeselector', [
                    'type'      => 'select',
                    'name'      => 'type',
                    'selected'  => esc_attr($_GET['type'] ?? 0),
                    'option'    => $data['typeselector']
                ]);
            }

            printf('<label class="screen-reader-text" for="filter-search-input">%s</label>', esc_attr($title));
            printf('<input type="search" name="s" value="%s" class="wp-filter-search" id="filter-search-input" placeholder="%s…">', esc_attr($_GET['s'] ?? ''), esc_attr($title));
            printf('<input type="submit" id="search-submit" class="button hide-if-js" value="%s"', esc_attr($title));
            echo '</form>';
        }
        echo '</div>';
    }

    /**
     * 后台界面分页选项
     *
     * @since 1.0.0
     * @package Webian WordPress One
     *
     * @param array     $data
     * {
     *  分页选项卡内容数组
     *  @var string   index   选项卡别名
     *  @var string   value   选项卡标题
     * }
     * @param string    $page_url   默认链接
     */
    static function tabs($data, $page_url = false)
    {
        if (empty($data)) {
            return;
        }

        // 设定当前页面地址
        $page_url = WWPO_Admin::page_url($page_url);

        // 设定当前选项卡别名
        $current = $_GET['tab'] ?? key($data);

        /** 当分页选项卡数组数量大于一个时才显示 */
        if (1 < count($data)) {

            echo '<nav class="nav-tab-wrapper mb-3">';

            /**
             * 遍历选项卡内容
             *
             * @property string     $tab_key    选项卡别名
             * @property string[]   $tab_data
             * {
             *  选项卡内容
             *  @param string role  使用权限
             *  @param string title 选项卡标题
             * }
             */
            foreach ($data as $tab_key => $tab_data) :

                // 设定当前选中 CSS 样式
                $current_css    = ($tab_key == $current) ? 'nav-tab-active' : '';
                $current_title  = $tab_data;

                if (is_array($tab_data)) {
                    $role = $tab_data['role'] ?? WWPO_ROLE;

                    if (!current_user_can($role)) {
                        continue;
                    }

                    $current_title = $tab_data['title'];
                }

                // 格式化显示列表内容
                printf(
                    '<a class="nav-tab %s" href="%s">%s</a>',
                    esc_attr($current_css),
                    esc_url(add_query_arg('tab', $tab_key, $page_url)),
                    $current_title
                );

            endforeach;

            echo '</nav>';
        }
    }
}
