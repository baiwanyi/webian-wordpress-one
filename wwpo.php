<?php
/*
 * Plugin Name: Webian WordPress One
 * Plugin URI: https://webian.cn/
 * Description: 常用的函数和 Hook，屏蔽所有 WordPress 所有不常用的功能。
 * Version: 1.0.0
 * Author: Baiwanyi
 * Author URI: https://baiwanyi.com/
 * Text Domain: wwpo
 * Domain Path: /languages
 */

/** Define ABSPATH as this file's directory */
if (!defined('WWPOPATH')) {
    define('WWPOPATH', __DIR__ . DIRECTORY_SEPARATOR);
}

/**
 * 自定义常量
 *
 * @package Webian WordPress One
 */
/** 定义当前时间戳 */
define('NOW', current_time('timestamp'));

/** 格式化当前时间 */
define('NOW_TIME', date('Y-m-d H:i:s', NOW));

/** 定义默认权限 */
define('WWPO_ROLE', 'activate_plugins');

/** 数据库表名 */
define('WWPO_SQL_LOGS', 'wwpo_logs');

/** Redis 设置 */
define('WWPO_REDIS_IP', '127.0.0.1');
define('WWPO_REDIS_PORT', 6379);
define('WWPO_REDIS_EXPIRE', 0);
define('WWPO_REDIS_PASS', '');

if (defined('WP_REDIS_DATABASE')) {
    define('WWPO_REDIS_DB', WP_REDIS_DATABASE);
} else {
    define('WWPO_REDIS_DB', 0);
}

/** 定义插件版本 */
if (WP_DEBUG) {
    define('WWPO_VER', NOW);
} else {
    define('WWPO_VER', '1.0.0');
}

/** 定义插件路径 */
define('WWPO_DIR_PATH', plugin_dir_path(__FILE__));
define('WWPO_MOD_PATH', WWPO_DIR_PATH . 'modules' . DIRECTORY_SEPARATOR);

/** 定义插件地址 */
define('WWPO_TMPL_URL', get_template_directory_uri() . '/assets');
define('WWPO_DIR_URL', plugin_dir_url(__FILE__));
define('WWPO_MOD_URL', WWPO_DIR_URL . 'modules');
define('WWPO_ASS_URL', WWPO_DIR_URL . 'assets');

/** 定义插件名 */
define('WWPO_DIR_NAME', basename(WWPO_DIR_PATH));

/** 加载文件 */
require WWPOPATH . 'includes/class-db-sqlite.php';
require WWPOPATH . 'includes/class-wwpo-error.php';
require WWPOPATH . 'includes/class-wwpo-admin.php';
require WWPOPATH . 'includes/class-wwpo-button.php';
require WWPOPATH . 'includes/class-wwpo-core.php';
require WWPOPATH . 'includes/class-wwpo-form.php';
require WWPOPATH . 'includes/class-wwpo-redis.php';
require WWPOPATH . 'includes/class-wwpo-table.php';
require WWPOPATH . 'includes/filesystem.php';
require WWPOPATH . 'includes/formating.php';
require WWPOPATH . 'includes/functions.php';
require WWPOPATH . 'includes/general-template.php';
require WWPOPATH . 'includes/http.php';
require WWPOPATH . 'includes/logs.php';
require WWPOPATH . 'includes/media.php';
require WWPOPATH . 'includes/modules.php';
require WWPOPATH . 'includes/post.php';
require WWPOPATH . 'includes/rest-ajax.php';
require WWPOPATH . 'includes/rest-user.php';
require WWPOPATH . 'includes/settings.php';
require WWPOPATH . 'includes/shortcode.php';
require WWPOPATH . 'includes/string.php';
require WWPOPATH . 'includes/user.php';
require WWPOPATH . 'includes/wp.php';

/**
 * 启动初始化动作加载函数
 *
 * @since 1.0.0
 */
WWPO_Core::init();

