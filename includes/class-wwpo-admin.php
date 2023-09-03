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
     * 后台页面头部内容
     *
     * @since 1.0.0
     */
    public function admin_header()
    {
        /**
         * 注册后台菜单动作，部分参数用于显示页面
         *
         * @since 1.0.0
         * @param array $admins
         * {
         *  后台页面设置数组
         *  @var boolean    post_new        是否显示新建按钮，默认：false
         *  @var string     page_title      页面标题
         *  @var string     description     页面简介
         *  @var string     page_title_url  页面标题链接
         * }
         */
        global $wwpo_admins;

        $admin_page = self::page_name();

        // 设定页面头部参数默认值
        $data = wp_parse_args($wwpo_admins[$admin_page], [
            'post_id'           => self::post_id(),
            'post_new'          => false,
            'page_title'        => get_admin_page_title(),
            'page_title_url'    => add_query_arg('post', 'new', self::page_url())
        ]);

        /**
         * 自定义后台页面头部内容
         *
         * @since 1.0.0
         * @param string $page_name 当前页面别名
         */
        do_action('wwpo_admin_head', $admin_page);

        /** 判断不显示标题内容 */
        if (false == $data['page_title']) {
            return;
        }

        /**
         * 自定义后台页面标题
         *
         * @since 1.0.0
         * @param string $page_title 当前页面标题内容
         */
        $page_title = apply_filters('wwpo_admin_header_title', $data['page_title']);

        // 判断 post_id 显示「添加」或编辑标签
        if ('edit' == self::action()) {

            $label_title = $data['label_title'] ?? $data['menu_title'];

            if ('new' == $data['post_id']) {
                $page_title = __('Add') . $label_title;
            } else {
                $page_title = __('Edit') . $label_title;
            }
        }

        // 输出页面标题 H1 格式
        printf('<h1 class="wp-heading-inline">%s</h1>', $page_title);

        $display_post_new = apply_filters('wwpo_admin_header_post_new', true);

        /** 判断是否需要添加按钮 */
        if ($data['post_new'] && 'new' !== $data['post_id'] && $display_post_new) {
            printf('<a href="%s" class="page-title-action">%s</a>', $data['page_title_url'], __('Add'));
        }

        /**
         * 自定义后台页面标题链接
         *
         * @since 1.0.0
         */
        do_action('wwpo_admin_header_link', $admin_page);

        /** 判断后台菜单数组中设定的页面简介内容 */
        if (isset($data['description'])) {
            printf('<p class="description">%s</p>', $data['description']);
        }

        // 输出分隔符
        echo '<hr class="wp-header-end">';

        /**
         * 自定义后台页面头部内容（后部分）
         *
         * @since 1.0.0
         * @param string $admin_page 当前页面别名
         */
        do_action('wwpo_admin_head_after', $admin_page);
    }

    /**
     * 后台页面消息
     *
     * @since 1.0.0
     */
    public function admin_message()
    {
        if (empty($_GET['message'])) {
            return;
        }

        $admin_page = self::page_name(true);

        $message = apply_filters('wwpo_admin_message', []);

        if (empty($message) || empty($message[$admin_page]) || empty($message[$admin_page][$_GET['message']])) {
            return;
        }

        $message_css    = key($message[$admin_page][$_GET['message']]) ?? 'error';
        $message_title  = current($message[$admin_page][$_GET['message']]) ?? '未知消息';

        echo self::messgae($message_css, $message_title);
    }

    /**
     * 后台页面脚部内容
     *
     * @since 1.0.0
     */
    public function admin_footer()
    {
        $admin_page = self::page_name();

        /**
         * 自定义后台页面头部内容
         *
         * @since 1.0.0
         * @param string $admin_page 当前页面别名
         */
        do_action('wwpo_admin_footer', $admin_page);
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

        $admin_page = wp_unslash($_GET['page']);
        $admin_page = plugin_basename($admin_page);

        /** 判断是否页面别名格式化，即删除别名中的‘-’和‘_’ */
        if ($format) {
            return str_replace(['-', '_'], '', $admin_page);
        }

        return $admin_page;
    }

    /**
     * 判断当前页面是否是指定页面
     *
     * @since 1.0.0
     */
    static function is_page($pagename)
    {
        global $post;

        $is_page    = false;
        $post_type  = $post->post_type ?? '';
        $post_type  = $_GET['post_type'] ?? $post_type;

        if ($pagename == self::page_name()) {
            $is_page = true;
        }

        if (empty($post_type)) {
            return $is_page;
        }

        if ($pagename == $post_type) {
            $is_page = true;
        }

        return $is_page;
    }

    /**
     * 获取地址栏 post 参数
     *
     * @since 1.0.0
     */
    static function post_id($default = '', $is_new = false)
    {
        $post_id = $_GET['post'] ?? $default;

        if ($is_new && 'new' == $post_id) {
            return $default;
        }

        return $post_id;
    }

    /**
     * 获取地址栏 action 参数
     *
     * @since 1.0.0
     */
    static function action($default = '')
    {
        // 获取 action 参数，并设定默认值
        $action = $_GET['action'] ?? $default;

        /**
         * 判断当 post 参数存在，并且 action 参数为空时，设定 action 参数为 edit
         *
         * 通常是在新建内容，如 post 为 new 时，省略设定 action 参数，故设定 action 参数为 edit
         * 如不打算使用 edit 函数，可在「添加」按钮设定时添加自定义的 action 参数
         */
        if (isset($_GET['post']) && empty($_GET['action'])) {
            $action = 'edit';
        }

        // 返回 action 参数内容
        return $action;
    }

    /**
     * 判断当前页面动作
     *
     * @since 1.0.0
     * @param string $action 需要判断的动作名
     */
    static function is_action($action)
    {
        $is_action = false;

        if ($action == self::action()) {
            $is_action = true;
        }

        return $is_action;
    }

    /**
     * 获取地址栏 tab 参数
     *
     * @since 1.0.0
     *
     * @param string $default 默认标签值
     */
    static function tabs($default = '')
    {
        return $_GET['tab'] ?? $default;
    }

    /**
     * 获取当前后台地址
     *
     * @since 1.0.0
     */
    static function page_url($param = null, $url = false)
    {
        if (empty($param)) {
            return remove_query_arg(['action', 'message', 'paged', 'post', 'search', 'select', 'tab', 's', 'view'], $url);
        }

        return remove_query_arg($param, $url);
    }

    /**
     * 当前后台地址添加参数
     *
     * @since 1.0.0
     */
    static function add_query($param = null, $page_url = false)
    {
        if (empty($param)) {
            return;
        }

        if (empty($page_url)) {
            $page_url = self::page_url();
        }

        return add_query_arg($param, $page_url);
    }

    /**
     * 格式化表格标题链接
     *
     * @since 1.0.0
     *
     * @param integer   $post_id    链接 ID
     * @param string    $title      链接标题
     * @param string    $action     页面动作，默认值：edit
     * @param string    $page_url   页面地址
     */
    static function title($post_id, $title, $action = 'edit', $page_url = null)
    {
        if (empty($title)) {
            $title = __('（未命名标题）', 'wwpo');
        }

        if (empty($page_url)) {
            $page_url = self::page_url();
        }

        return sprintf(
            '<strong><a href="%1$s">%2$s</a></strong>',
            add_query_arg(['post' => $post_id, 'action' => $action], $page_url),
            $title
        );
    }

    /**
     * 表格标题链接动作
     *
     * @since 1.0.0
     *
     * @param array $data 链接动作内容数组
     * @return string
     */
    static function row_actions($data)
    {
        $row_actions    = '';           // 初始化行动作链接内容
        $separator      = ' | ';        // 链接分隔符号
        $num            = 1;            // 初始化链接数量
        $count          = count($data); // 统计链接内容数组

        /**
         * 遍历链接动作内容数组
         *
         * @property string $action_key 链接别名
         * @property array  $action_val
         * {
         *  链接内容
         *  @var string title       链接标题
         *  @var string value       链接动作
         *  @var array  dataset     链接扩展属性
         * }
         */
        foreach ($data as $action_key => $action_val) {

            // 设定动作属性默认值
            $dataset = $action_val['dataset'] ?? null;

            /** 判断为最后一个链接删除分隔符号 */
            if (0 == ($count - $num)) {
                $separator = '';
            }

            // 设置链接
            $row_action_link = WWPO_Button::wp([
                'text'      => $action_val['title'],
                'value'     => $action_val['value'],
                'css'       => 'btn btn-link',
                'dataset'   => $dataset
            ], false);

            // 格式化设定行动作链接
            $row_actions .= sprintf('<span class="%s">%s%s</span>', $action_key, $row_action_link, $separator);

            // 计算链接数量
            $num++;
        }

        // 返回行动作链接内容
        return sprintf('<div class="row-actions">%s</div>', $row_actions);
    }

    /**
     * 返回上一页按钮
     *
     * @since 1.0.0
     */
    static function back()
    {
        return '<a href="#back" class="btn btn-outline-primary btn-sm me-1" rel="back"><span class="dashicons-before dashicons-arrow-left-alt2"></span></a>';
    }

    /**
     * 消息内容模板函数
     *
     * @since 1.0.0
     * @param string $code
     * @param string $message
     */
    static function messgae($code, $message = '')
    {
        echo sprintf('<div id="message" class="notice %s is-dismissible"><p>%s</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">忽略此通知。</span></button></div>', $code, $message);
    }

    /**
     * 设定显示单元格内容
     *
     * @since 1.0.0
     */
    public function column($data, $column_name)
    {
        if (is_callable([$this, "column_{$column_name}"])) {
            call_user_func([$this, "column_{$column_name}"], $data);
        } else {
            echo $data[$column_name] ?? '';
        }
    }
}
