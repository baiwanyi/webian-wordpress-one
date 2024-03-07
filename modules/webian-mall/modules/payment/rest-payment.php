<?php


/**
 * 模板相关 Rest API 操作
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/template
 */
class wwpo_wpmall_payment_rest_controller
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
         * @method POST wwpo/order/get
         */
        register_rest_route($this->namespace, 'order/get', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'get_order'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);

        /**
         * 更新产品收藏
         *
         * @since 1.0.0
         * @method POST wwpo/order/list
         */
        register_rest_route($this->namespace, 'order/list', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'list_order'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);

        /**
         * 更新产品收藏
         *
         * @since 1.0.0
         * @method POST wwpo/order/create
         */
        register_rest_route($this->namespace, 'order/create', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'create_order'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);

        /**
         * 更新产品收藏
         *
         * @since 1.0.0
         * @method POST wwpo/order/update
         */
        register_rest_route($this->namespace, 'order/update', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'update_order'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);

        /**
         * 更新产品收藏
         *
         * @since 1.0.0
         * @method POST wwpo/order/delete
         */
        register_rest_route($this->namespace, 'order/delete', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'delete_order'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);

        /**
         * 更新产品收藏
         *
         * @since 1.0.0
         * @method POST wwpo/order/payment
         */
        register_rest_route($this->namespace, 'order/payment', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'payment_order'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);
    }

    /**
     * Undocumented function
     *
     * @param [type] $request
     * @return void
     */
    public function create_order($request)
    {
        $total          = $request['total'];
        $item           = $request['item'];
        $payment        = $request['payment'];
        $user_id        = $request['user'];
        $user_agent     = get_user_meta($user_id, '_wwpo_invite_user', true);

        $order_updated = [
            'order_trade_no'    => date('Ymdhis', NOW) . wwpo_unique($user_id),
            'user_post'         => $user_id,
            'order_customer'    => $user_id,
            'user_agent'        => $user_agent,
            'order_status'      => 'draft',
            'source'            => 'wxapps',
            'time_post'         => NOW_TIME
        ];

        $order_id = wwpo_insert_post(WWPO_SQL_ORDER, $order_updated);

        if (empty($order_id)) {
            return ['error' => 'fail'];
        }

        $order_item_updated = [
            'order_id'      => $order_id,
            'product_id'    => $item['product_id'],
            'thumb_url'     => $item['thumb'][0],
            'barcode'       => $item['barcode'],
            'item_title'    => $item['title'],
            'price_total'   => $total * 100
        ];

        $item_id = wwpo_insert_post(WWPO_SQL_ORDER_ITEM, $order_item_updated);

        foreach ($payment as $sku_name => $meta) {
            wwpo_insert_post(WWPO_SQL_ORDER_ITEMMETA, [
                'item_id'           => $item_id,
                'item_modal_code'   => $meta['code'],
                'item_modal_name'   => $sku_name,
                'amount'            => $meta['amount'],
                'price_buy'         => $meta['buy'] * 100,
                'price_sale'        => $meta['sale'] * 100
            ]);
        }

        return ['error' => 'success', 'post' => $order_id];
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
