<?php

/**
 * 模板相关 Rest API 操作
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/template
 */
class wwpo_wpmall_template_rest_controller
{
    /**
     * 命名空间
     *
     * @since 1.0.0
     * @var string
     */
    public $namespace;

    /**
     * 构造函数
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->namespace = 'wwpo';
    }

    /**
     * 注册接口
     *
     * @since 1.0.0
     */
    public function register_routes()
    {
        /**
         * 更新产品收藏
         *
         * @since 1.0.0
         * @method GET wwpo/template
         */
        register_rest_route($this->namespace, 'templates', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'get_templates'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);

        /**
         * 获取产品详情
         *
         * @since 1.0.0
         * @method GET wwpo/template/item
         */
        register_rest_route($this->namespace, 'template/item', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'template_item'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);

        /**
         * 更新产品收藏
         *
         * @since 1.0.0
         * @method POST wwpo/template/favor
         */
        register_rest_route($this->namespace, 'template/favor', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'template_favor'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);
    }

    /**
     * 获取模板列表接口函数
     *
     * @since 1.0.0
     * @param array $request
     * {
     *  请求参数
     *  @var integer    limit       每页显示数量
     *  @var string     orderby     排序方式
     *  @var integer    paged       显示页码
     *  @var integer    category    分类目录 ID
     *  @var integer    tag         分类标签 ID
     *  @var integer    user        收藏用户 ID
     *  @var boolean    favor       是否是收藏列表，默认：false
     *  @var string     search      搜索关键字
     * }
     */
    public function get_templates($request)
    {
        // 设定模板文章类型
        $request['post_type']   = 'template';
        $request['size']        = 'large';

        // 设定参数
        $limit      = $request['limit'] ?? get_option('posts_per_page');
        $paged      = $request['paged'] ?? 1;
        $paged      = (int) $paged;
        $is_favor   = $request['favor'] ?? 0;

        if (empty($request['user'])) {
            $is_favor = 0;
        }

        // 初始化内容数组
        $data = [
            'paged' => $paged + 1
        ];

        if ($is_favor) {
            $request['favors'] = wwpo_get_col(WWPO_SQL_TMPL_FAVOR, 'user_id', $request['user'], 'post_id');
        }

        // 获取内容列表
        $data['list'] = wwpo_wpmall_get_posts($request);

        // 判断列表为空，设定 paged 为 none
        if (empty($data['list'])) {
            $data['paged'] = 'none';
            return $data;
        }

        // 列表内容数量小于显示数量，判断为最后一页，设定 paged 为 end
        if ($limit > count((array) $data['list'])) {
            $data['paged'] = 'end';
        }

        // 返回列表
        return $data;
    }

    /**
     * 获取模板详情接口函数
     *
     * @since 1.0.0
     * @param array $request
     * {
     *  请求参数
     *  @var integer    post    模板 ID
     *  @var integer    user    收藏用户 ID
     * }
     */
    public function template_item($request)
    {
        // 设定参数
        $post_id    = $request['post'];
        $user_id    = $request['user'] ?? 0;

        if (empty($post_id)) {
            return;
        }

        // 获取当前产品内容
        $post = get_post($post_id);

        // 初始化显示内数组
        $data = wwpo_template_format_posts($post, [], 'large');

        /**
         * 查询用户收藏，标记当前产品是否被当前用收藏
         *
         * @todo Redis 缓存用户收藏作品 ID，使用缓存进行判断收藏
         */
        global $wpdb;
        $data['favor'] = $wpdb->get_var(sprintf("SELECT favor_id FROM %s WHERE post_id = %d AND user_id = %d", WWPO_SQL_TMPL_FAVOR, $post_id, $user_id));

        return $data;
    }

    /**
     * 更新用户收藏接口函数
     *
     * @since 1.0.0
     * @param array $request
     * {
     *  请求参数
     *  @var integer    post    产品 ID
     *  @var integer    user    用户 ID
     * }
     */
    public function template_favor($request)
    {
        // 设定参数
        $post_id    = $request['post'] ?? 0;
        $user_id    = $request['user'] ?? 0;

        // 判断产品 ID为空
        if (empty($post_id) || empty($user_id)) {
            return ['icon' => 'error', 'message' => '收藏失败'];
        }

        // 设定保存内容数组
        $updated = [
            'post_id'   => $post_id,
            'user_id'   => $user_id
        ];

        $favor_id = wwpo_get_post_by_wheres(WWPO_SQL_TMPL_FAVOR, $updated, 'favor_id');

        // 移除操作，删除记录
        if (empty($favor_id)) {

            // 设定收藏时间
            $updated['time_post'] = NOW_TIME;

            // 写入记录
            $insert_id = wwpo_insert_post(WWPO_SQL_TMPL_FAVOR, $updated);

            if (empty($insert_id)) {
                return ['icon' => 'error', 'message' => '收藏失败'];
            }

            return ['icon' => 'success', 'message' => '收藏成功', 'favor' => 'success'];
        }

        wwpo_delete_post(WWPO_SQL_TMPL_FAVOR, $updated);

        return ['icon' => 'success', 'message' => '收藏已移除', 'favor' => 'remove'];
    }

    /**
     * 权限验证函数
     *
     * @since 1.0.0
     * @param array $request 请求参数
     */
    public function permissions_check($request)
    {
        if (WP_DEBUG) {
            return true;
        }

        return true;
    }
}
