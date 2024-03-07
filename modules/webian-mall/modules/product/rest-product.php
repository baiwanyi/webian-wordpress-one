<?php

/**
 * 产品相关 Rest API 操作
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/product
 */
class wwpo_wpmall_product_rest_controller
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
         * 获取产品列表
         *
         * @since 1.0.0
         * @method GET wwpo/products
         */
        register_rest_route($this->namespace, 'products', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'get_products'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);

        /**
         * 获取产品详情
         *
         * @since 1.0.0
         * @method GET wwpo/product/item
         */
        register_rest_route($this->namespace, 'product/item', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'product_item'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);

        /**
         * 更新产品收藏
         *
         * @since 1.0.0
         * @method POST wwpo/product/favor
         */
        register_rest_route($this->namespace, 'product/favor', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'product_favor'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);
    }


    /**
     * 获取产品列表接口函数
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
     * }
     */
    public function get_products($request)
    {
        // 设定产品文章类型
        $request['post_type']   = 'product';
        $request['size']        = 'large';

        // 设定参数
        $limit  = $request['limit'] ?? get_option('posts_per_page');
        $paged  = $request['paged'] ?? 1;
        $paged  = (int) $paged;

        // 初始化内容数组
        $data = [
            'paged' => $paged + 1
        ];

        // 获取内容列表
        $data['list'] = wwpo_wpmall_get_posts($request);

        // 判断列表为空，设定 paged 为 none
        if (empty($data['list'])) {
            $data['paged'] = 'none';
            return $data;
        }

        // 列表内容数量小于显示数量，判断为最后一页，设定 paged 为 end
        if ($limit > count($data['list'])) {
            $data['paged'] = 'end';
        }

        // 返回列表
        return $data;
    }

    /**
     * 获取产品详情接口函数
     *
     * @since 1.0.0
     * @param array $request
     * {
     *  请求参数
     *  @var integer    post    产品 ID
     *  @var integer    user    用户 ID
     *  @var boolean    poster  是否封面，默认：false
     * }
     */
    public function product_item($request)
    {
        // 设定参数
        $post_id = $request['post'];

        if (empty($post_id)) {
            return;
        }

        // 获取当前产品内容
        $post = get_post($post_id);

        // 初始化显示内数组
        $data = [
            'post_id'   => $post_id,
            'title'     => $post->post_title
        ];

        // 获取产品 Redis 缓存
        $product = WWPO_Redis::get(WWPO_RD_PRODUCT_KEY . $post_id, WP_REDIS_DATABASE);

        // 判断缓存为空，重新生成缓存
        if (empty($product)) {
            $product = wwpo_wpmall_product_update_redis($post_id);
        }

        /**
         * 显示产品详情其他内容
         */
        $data['excerpt']        = WWPO_Wxapps::nodes($post->post_excerpt);
        $data['content']        = WWPO_Wxapps::nodes($post->post_content);
        $data['barcode']        = $product['barcode'];
        $data['thumb']          = $product['thumb'];
        $data['terms']          = $product['terms'];
        $data['price']          = $product['price'];
        $data['promo']          = $product['promo'];
        $data['qrcode']         = $product['qrcode'];
        $data['thumb_id']       = $product['thumb_id'];
        $data['product_id']     = $product['product_id'];
        $data['product_sku']    = $product['sku'];

        if ($product['modal']) {
            $product['modal'] = wp_list_sort($product['modal'], 'order');

            foreach ($product['modal'] as $modal) {
                $data['product_modal'][] = [
                    'id'    => $modal['id'],
                    'type'  => $modal['type'],
                    'list'  => wp_list_sort($modal['list'], 'order')
                ];
            }
        }

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
    public function product_favor($request)
    {
        // 设定保存内容数组
        $updated = [
            'post_id'   => $request['post'] ?? 0,
            'user_id'   => $request['user'] ?? 0
        ];

        if (empty($updated['post_id']) || empty($updated['user_id'])) {
            return;
        }

        // 移除操作，删除记录
        if ('remove' == $request['action']) {
            wwpo_delete_post(WWPO_SQL_PR_FAVOR, $updated);
            return;
        }

        // 设定收藏时间
        $updated['time_post'] = NOW_TIME;
        $updated['post_name'] = $request['name'];
        $updated['thumb_url'] = get_post_meta($request['thumb'], '_wp_attached_file', true);

        // 写入记录
        $insert_id = wwpo_insert_post(WWPO_SQL_PR_FAVOR, $updated);

        if (empty($insert_id)) {
            return;
        }

        $updated['thumb_url'] = wwpo_oss_cdnurl($updated['thumb_url'], 'large');
        $updated['time_post'] = date('Y/m/d H:i:s', strtotime($updated['time_post']));

        return $updated;
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
