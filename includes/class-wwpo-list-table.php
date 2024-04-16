<?php

/**
 * 生成 WordPress 表格方法类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */

/** 判断 WP_List_Table 类引用 */
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WWPO_Table extends WP_List_Table
{
    /**
     * 当前页面别名
     *
     * @since 1.0.0
     * @var string
     */
    public $admin_page;

    /**
     * 表格选项设定
     *
     * @since 1.0.0
     * @var array
     */
    public $setting;

    /**
     * 表格项目数量，默认：0
     *
     * @since 1.0.0
     * @var integer
     */
    public $total = 0;

    /**
     * 设定类实例
     *
     * @since 1.0.0
     * @var WWPO_Table $instance 类实例参数
     */
    static protected $instance = '';

    /**
     * 初始化构造函数，使用 WP_List_Table 类函数
     *
     * @since 1.0.0
     * @param array $setting
     * {
     *  表格设置参数内容数组
     *  @var boolean    index       是否添加表格序号，默认：false
     *  @var boolean    checkbox    是否显示 checkbox 表单，默认：false
     *  @var boolean    tablenav    是否显示表格导航模块，默认：true
     *  @var boolean    paged       是否显示表格分页，默认：true
     *  @var integer    limit       每页表格显示数量，默认：20
     *  @var string     select      查询表格字段，默认：all
     *  @var string     orderby     查询排序，默认：ID
     *  @var array      column      表格标题
     *  @var array      sortable    表格排序
     * }
     */
    public function __construct($setting)
    {
        /** 设定当前表格选项默认值 */
        $this->setting = wp_parse_args($setting, [
            'class'     => 'wwpo__admin-table',
            'index'     => false,
            'checkbox'  => false,
            'tablenav'  => true,
            'paged'     => 20,
            'limit'     => 20,
            'select'    => 'all',
            'order'     => 'DESC',
            'orderby'   => 'ID',
            'groupby'   => '',
            'column'    => [],
            'sortable'  => []
        ]);

        // 设定当前页面别名
        $this->admin_page = WWPO_Admin::page_name();

        if (empty($this->admin_page)) {
            global $pagenow, $post;

            if ('post.php' == $pagenow) {
                $this->admin_page = $post->post_type;
            }
        }

        /** 调用 WP_List_Table 类构造函数 */
        parent::__construct([
            'singular'  => $this->setting['class'],
            'plural'    => $this->setting['class']
        ]);
    }

    /**
     * 使用查询方法显示表格函数
     *
     * @since 1.0.0
     * @param string    $table_name     查询表名
     * @param array     $wheres         查询条件
     * @param array     $settings       设置参数
     */
    static function select($table_name, $wheres = [], $settings = [])
    {
        if (empty($settings['column'])) {
            return;
        }

        self::$instance = new WWPO_Table($settings);
        self::$instance->get_table_setting([
            'table_name'    => $table_name,
            'wheres'        => $wheres
        ]);
    }

    /**
     * 使用内容结果显示表格函数
     *
     * @since 1.0.0
     * @param array $data       内容数组
     * @param array    设置参数
     */
    static function result($data, $settings = [])
    {
        if (empty($settings['column'])) {
            return;
        }

        self::$instance = new WWPO_Table($settings);
        self::$instance->get_table_setting(['result' => $data]);
    }

    /**
     * 使用自定义查询语句显示表格函数
     *
     * @since 1.0.0
     * @param string    $query      查询语句
     * @param array     $settings   设置参数
     */
    static function query($query, $settings = [])
    {
        if (empty($settings['column'])) {
            return;
        }

        self::$instance = new WWPO_Table($settings);
        self::$instance->get_table_setting(['query' => $query]);
    }

    /**
     * 表格内容显示函数
     *
     * @since 1.0.0
     * @param array $data
     * {
     *  表格内容参数数组
     *  @var array      result      表格内容查询结果
     *  @var string     query       表格查询语句
     *  @var string     table_name  查询表名称
     *  @var array      wheres      查询条件
     * }
     */
    protected function get_table_setting($data)
    {
        global $wpdb;

        // 设定表格默认值
        $paged                  = (int) $this->get_pagenum();
        $limit                  = (int) $this->get_page_limit();
        $offset                 = ($paged - 1) * $limit;
        $this->_column_headers  = [$this->get_columns(), [], $this->setting['sortable']];

        /**
         * 判断有查询内容数组
         *
         * 统计查询结果总数，按照分页显示内容
         */
        if (isset($data['result'])) {
            $this->total    = count($data['result']);
            $this->items    = array_slice($data['result'], $offset, $limit);
        }

        /**
         * 判断使用查询语句
         *
         * 获取查询结果，统计查询结果总数，按照分页显示内容
         */
        if (isset($data['query'])) {
            $table_result   = $wpdb->get_results($data['query'], ARRAY_A);
            $this->total    = count($table_result);
            $this->items    = array_slice($table_result, $offset, $limit);
        }

        /**
         * 判断使用数据库表名进行查询
         *
         * 设定查询语句，获取查询结果和查询总数
         */
        if (isset($data['table_name'])) {

            // 设定查询
            $wheres     = '';
            $order      = $_GET['order'] ?? $this->setting['order'];
            $orderby    = $_GET['orderby'] ?? $this->setting['orderby'];
            $groupby    = $this->setting['groupby'];

            if ($groupby) {
                $selectby = "GROUP BY $groupby";
            } else {
                $selectby = "ORDER BY $orderby $order";
            }

            // 格式化查询句柄
            if ($data['wheres']) {
                $wheres = 'WHERE ' . wwpo_get_wheres($data['wheres']);
            }

            // 判断查询字段，为 all 则替换到 * 号
            if ('all' == $this->setting['select']) {
                $this->setting['select'] = '*';
            }

            $this->items    = $wpdb->get_results("SELECT {$this->setting['select']} FROM {$data['table_name']} {$wheres} {$selectby} LIMIT $offset, $limit", ARRAY_A);
            $this->total    = $wpdb->get_var("SELECT COUNT(*) FROM {$data['table_name']} {$wheres}");
        }

        // 设定分页
        if ($this->setting['paged']) {
            $this->set_pagination_args([
                'total_items'   => $this->total,
                'per_page'      => $limit,
                'total_pages'   => ceil($this->total / $limit),
            ]);
        }

        // 显示表格内容
        $this->display();
    }

    /**
     * 在表格的上面或下面生成表格导航
     *
     * @since 1.0.0
     * @param string    $which  表格导航位置
     *  - top       表格上方
     *  - bottom    表格下方
     */
    protected function display_tablenav($which)
    {
        /** 判断禁止显示表格导航 */
        if (false == $this->setting['tablenav']) {
            return;
        }

        if ('bottom' == $which) {
            echo '</div><!-- /.table-responsive -->';
        }

        // 表格导航开始
        printf('<div class="wwpo__admin-table__nav tablenav %s">', esc_attr($which));

        /** 判断显示批量操作动作按钮 */
        if ($this->has_items()) {
            echo '<div class="alignleft actions bulkactions">';
            $this->get_bulk_action($which);
            echo '</div>';
        }

        /**
         * 自定义当前页面扩展导航按钮
         *
         * @since 1.0.0
         * @param string $which 导航位置
         */
        do_action("wwpo_{$this->admin_page}_extra_tablenav", $which);

        // 当前页面扩展导航按钮函数
        $this->extra_tablenav($which);

        // 显示分页导航
        $this->pagination($which);

        // 表格导航结束
        echo '<br class="clear" /></div>';

        if ('top' == $which) {
            echo '<div class="table-responsive w-100">';
        }
    }

    /**
     * 分分类列表栏目标题类列表标题排序
     *
     * @since 1.0.0
     */
    public function get_columns()
    {
        /** 判断是否显示列表前 checkbox 表单 */
        if ($this->setting['checkbox']) {
            $this->setting['column'] = array_merge(['cb' => '<input type="checkbox" />'], $this->setting['column']);
        }

        /** 判断是否显示列表前 checkbox 表单 */
        if ($this->setting['index']) {
            $this->setting['column'] = array_merge(['index' => '#'], $this->setting['column']);
        }

        return $this->setting['column'];
    }

    /**
     * 分分类列表栏目标题类列表标题排序
     *
     * @since 1.0.0
     */
    protected function get_page_limit()
    {
        // 设定用户 usermeta 数据库表 option_key 名称
        $user_paged_num = sprintf('%1$s_paged', $this->admin_page);
        $user_paged_num = str_replace('-', '_', $user_paged_num);

        // 设定列表显示条目数量，查找 usermeta 数据库中设定数量，为空则使用默认数量
        return $this->get_items_per_page($user_paged_num, $this->setting['limit']);
    }

    /**
     * 设置批量操作动作函数
     *
     * @since 1.0.0
     * @param string $which 导航位置
     */
    public function get_bulk_action($which)
    {
        /** 判断显示批量操作 */
        if (empty($this->setting['bulk']['option'])) {
            return;
        }

        // 设定批量操作默认动作
        $bulk_action = $this->setting['bulk']['action'] ?? 'wwpobulkupdate';

        // 批量内容开始
        printf('<label for="bulk-action-selector-%1$s" class="screen-reader-text">%2$s</label><select name="bulkaction" id="bulk-action-selector-%1$s"><option value="0">%3$s</option>', esc_attr($which), __('Select bulk action'), __('Bulk actions'));

        /**
         * 遍历下拉表单内容
         */
        foreach ($this->setting['bulk']['option'] as $key => $value) {
            printf('<option value="%s">%s</option>', esc_attr($key), esc_html($value));
        }

        // 批量内容结束
        echo '</select>';

        // 批量操作按钮
        // echo WWPO_Button::wp([
        //     'text'      => __('Apply'),
        //     'value'     => $bulk_action,
        //     'css'       => 'button',
        //     'dataset'   => $this->setting['bulk']['dataset'] ?? null
        // ]);
    }

    /**
     * 没有查询结果文字
     *
     * @since 1.0.0
     */
    public function no_items()
    {
        echo __('找不到相关内容。', 'wwpo');
    }

    /**
     * 列表单元格选择框内容
     *
     * @since 1.0.0
     * @param array $data 内容数组
     */
    protected function column_cb($data)
    {
        $data = (array) $data;
        return sprintf('<input type="checkbox" name="bulk_ids" value="%s" />', current($data));
    }

    /**
     * 列表单元格索引字段
     *
     * @since 1.0.0
     * @param array $data 内容数组
     */
    protected function column_index($data)
    {
        $data = (array) $data;
        return sprintf('<span class="text-muted">%s</span>', current($data));
    }

    /**
     * 默认单元格内容
     *
     * @since 1.0.0
     * @param array     $data           内容数组
     * @param string    $column_name    单元格别名
     */
    protected function column_default($data, $column_name)
    {
        $column_name = str_replace(['small-', 'medium-'], '', $column_name);
        $default = $data[$column_name] ?? null;

        if (isset($default)) {
            return $default;
        }

        /**
         * 当前页面表格行数据内容
         *
         * @since 1.0.0
         * @param array     $data           行数据内容数组
         * @param string    $column_name    当前表格列别名
         */
        do_action("wwpo_table_{$this->admin_page}_custom_column", $data, $column_name);
    }

    /**
     * 日期格式内容
     *
     * @since 1.0.0
     * @param array $data 内容数组
     */
    protected function column_date($data)
    {
        if ('0000-00-00 00:00:00' === $data['time_post'] || empty($data['time_post'])) {
            $time_post  = __('未发布', 'wwpo');
            $time_show  = $time_post;
            $time_diff  = 0;
        } else {
            $time_post  = $data['time_post'];
            $timestamp  = strtotime($time_post);
            $time_diff  = NOW - $timestamp;

            // 显示时间格式
            if ($timestamp && $time_diff > 0 && $time_diff < DAY_IN_SECONDS) {
                $time_show = sprintf(__('%s 前', 'wwpo'), human_time_diff($timestamp, NOW));
            } else {
                $time_show = date('m月d日 H:s', $timestamp);
            }
        }

        $column_date = '';

        if (isset($data['post_status'])) {
            $column_date = $this->post_status_tag($data['post_status']) . '<br />';
        }

        $column_date .= sprintf('<abbr title="%s">%s</abbr>', $time_post, $time_show);

        return $column_date;
    }

    /**
     * 状态标签
     *
     * @since 1.0.0
     * @param string $tag 状态标签别名
     */
    public function post_status_tag($tag = '')
    {
        // 定义默认状态标签内容数组
        $default_tags = [
            'publish'   => __('已发布', 'wwpo'),
            'post'      => __('已发布', 'wwpo'),
            'delete'    => __('已删除', 'wwpo'),
            'internal'  => __('仅自己可见', 'wwpo'),
            'private'   => __('私密', 'wwpo'),
            'protected' => __('加密', 'wwpo'),
            'draft'     => __('草稿', 'wwpo'),
        ];

        /**
         * 定义当前页面表格标签内容数组
         *
         * @since 1.0.0
         * @param string $default_tags 默认别名
         */
        $post_status = apply_filters("wwpo_table_{$this->admin_page}_post_status", $default_tags);

        /** 判断状态标签别名，为空则输出整个状态标签数组 */
        if (empty($tag)) {
            return $post_status;
        } else {
            return $post_status[$tag] ?? __('未知状态', 'wwpo');
        }
    }
}
