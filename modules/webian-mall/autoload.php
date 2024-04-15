<?php
/*
 * Modules Name: 涂颜社插件
 * Plugin URI: https://webian.cn/
 * Description: 一站式帮你建立在线商城并保持持续发展。
 * Version: 1.0.0
 * Author: Baiwanyi
 * Author URI: https://baiwanyi.com/
 * Updated: 2023-01-01
 */

// CREATE TABLE IF NOT EXISTS `表名` ( ...... )
if (!defined('WWPO_VER')) {
    return;
}

/** 定义目录 */
define('WWPO_WPMALL_PATH', plugin_dir_path(__FILE__));
define('WWPO_WPMALL_URL', plugin_dir_url(__FILE__));

/** 定义数据库表名称 */
define('WWPO_SQL_ORDER', 'wwpo_orders');
define('WWPO_SQL_ORDER_META', 'wwpo_ordermeta');
define('WWPO_SQL_ORDER_ITEM', 'wwpo_order_item');
define('WWPO_SQL_ORDER_ITEMMETA', 'wwpo_order_itemmeta');
define('WWPO_SQL_ORDER_PAYMENT', 'wwpo_order_payment');
define('WWPO_SQL_ORDER_STATUS', 'wwpo_order_status');

/** 定义 Redis 名称 */
define('WWPO_RD_KEY', 'wwpo:');
define('WWPO_RD_WPMALL_KEY', WWPO_RD_KEY . 'wpmall:');

/**
 * 引用文件
 *
 * @since 1.0.0
 */
// wwpo_require_dir(WWPO_WPMALL_PATH . 'includes');
// wwpo_require_dir(WWPO_WPMALL_PATH . 'modules');

/**
 * 注册后台样式脚本
 *
 * @since 1.0.0
 */
function wwpo_wpmall_admin_enqueue_scripts()
{
    wp_enqueue_script('wwpo-wpmall', WWPO_WPMALL_URL . 'apps.min.js', ['weadminui', 'jquery-ui-sortable'], WWPO_VER, true);
}
add_action('admin_enqueue_scripts', 'wwpo_wpmall_admin_enqueue_scripts');
