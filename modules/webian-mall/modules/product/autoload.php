<?php

/**
 * 产品展示模块
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/product
 */

/** 定义数据库表名称 */
define('WWPO_SQL_PRODUCT', 'wwpo_products');
define('WWPO_SQL_PR_SKU', 'wwpo_product_sku');
define('WWPO_SQL_PR_FAVOR', 'wwpo_product_favor');
define('WWPO_SQL_PR_CONTENT', 'wwpo_product_content');

/** 定义 Redis 名称 */
define('WWPO_RD_PRODUCT_KEY', WWPO_RD_KEY . 'product:');

/** 定义元数据字段名称 */
define('WWPO_PR_META_SALES', '_wwpo_product_sales_num');
define('WWPO_PR_META_ORDER', '_wwpo_product_order_num');


/**
 * 注册 Rest API 接口
 *
 * @since 1.0.0
 */
function wwpo_wpmall_product_rest_register_routes()
{
    // 产品相关接口
    $product_rest = new wwpo_wpmall_product_rest_controller();
    $product_rest->register_routes();
}
add_action('rest_api_init', 'wwpo_wpmall_product_rest_register_routes');
