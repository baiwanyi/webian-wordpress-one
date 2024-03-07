<?php


/**
 * 模板相关 Rest API 操作
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/template
 */
class wwpo_wpmall_address_rest_controller
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
         * @method POST wwpo/address/get
         */
        register_rest_route($this->namespace, 'address/get', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'get_address'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);

        /**
         * 更新产品收藏
         *
         * @since 1.0.0
         * @method POST wwpo/address/create
         */
        register_rest_route($this->namespace, 'address/create', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'create_address'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);

        /**
         * 更新产品收藏
         *
         * @since 1.0.0
         * @method POST wwpo/address/update
         */
        register_rest_route($this->namespace, 'address/update', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'update_address'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);

        /**
         * 更新产品收藏
         *
         * @since 1.0.0
         * @method POST wwpo/address/delete
         */
        register_rest_route($this->namespace, 'address/delete', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'delete_address'],
            'permission_callback'   => [$this, 'permissions_check']
        ]);
    }

    /**
     * Undocumented function
     *
     * @param [type] $request
     * @return void
     */
    public function create_address($request)
    {
        $address_id = wwpo_insert_post(WWPO_SQL_ADDRESS, [
            'user_id'       => $request['user'],
            'user_address'  => $request['address'],
            'user_phone'    => $request['phone'],
            'user_name'     => $request['name'],
            'is_default'    => 0
        ]);

        if (empty($address_id)) {
            return ['code' => 'fail'];
        }

        return ['code' => 'success'];
    }

    /**
     * Undocumented function
     *
     * @param [type] $request
     * @return void
     */
    public function update_address($request)
    {
        $updated = [
            'user_id'       => $request['user_id'],
            'user_address'  => $request['user_address'],
            'user_phone'    => $request['user_phone'],
            'user_name'     => $request['user_name'],
            'is_default'    => $request['is_default']
        ];

        if ($updated['is_default']) {
            wwpo_update_post(WWPO_SQL_ADDRESS, ['is_default' => 0], ['user_id' => $updated['user_id']]);
        }
        //
        else {
            $this->_update_last_address($updated['user_id']);
        }

        if (empty($request['address_id'])) {

            $address_id = wwpo_insert_post(WWPO_SQL_ADDRESS, $updated);

            if ($address_id) {
                return ['code' => 'success', 'message' => '收货地址已添加'];
            }

            return ['code' => 'error', 'message' => '地址添加失败'];
        }

        wwpo_update_post(WWPO_SQL_ADDRESS, $updated, ['address_id' => $request['address_id']]);

        return ['code' => 'success', 'message' => '收货地址已更新'];
    }

    /**
     * Undocumented function
     *
     * @param [type] $request
     * @return void
     */
    public function delete_address($request)
    {
        wwpo_delete_post(WWPO_SQL_ADDRESS, ['address_id' => $request['address']]);
        $this->_update_last_address($request['user']);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function get_address($request)
    {

        $address = wwpo_get_post(WWPO_SQL_ADDRESS, 'user_id', $request['user']);

        if (empty($address)) {
            return ['code' => 'empty'];
        }

        $address = wp_list_sort($address, 'is_default', 'DESC');

        return $address;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function _update_last_address($user_id)
    {
        global $wpdb;

        $default_address_id = $wpdb->get_var(sprintf("SELECT address_id FROM %s WHERE user_id = {$user_id} AND is_default = 1", WWPO_SQL_ADDRESS));

        if (empty($default_address_id)) {

            $last_address_id = $wpdb->get_var(sprintf("SELECT address_id FROM %s WHERE user_id = {$user_id} ORDER BY address_id DESC", WWPO_SQL_ADDRESS));

            if ($last_address_id) {
                wwpo_update_post(WWPO_SQL_ADDRESS, ['is_default' => 1], ['address_id' => $last_address_id]);
            }
        }
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
