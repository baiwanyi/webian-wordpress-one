<?php
/*
 * Plugin Name: Webian WordPress One
 * Plugin URI: https://webian.cn/
 * Description: 常用的函数和 Hook，屏蔽所有 WordPress 所有不常用的功能。
 * Version: 2.0.0
 * Author: Baiwanyi
 * Author URI: https://baiwanyi.com/
 * Text Domain: wwpo
 * Domain Path: /languages
 */

/** 将 WWPOPATH 定义为此文件的目录 */
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

/** 定义插件版本 */
if (WP_DEBUG) {
    define('WWPO_VER', NOW);
} else {
    define('WWPO_VER', '2.0.0');
}

/** 定义插件路径 */
define('WWPO_DIR_PATH', plugin_dir_path(__FILE__));
define('WWPO_MOD_PATH', WWPO_DIR_PATH . 'modules' . DIRECTORY_SEPARATOR);

/** 定义插件地址 */
define('WWPO_DIR_URL', plugin_dir_url(__FILE__));
define('WWPO_ASS_URL', WWPO_DIR_URL . 'assets');

/** 定义插件名 */
define('WWPO_DIR_NAME', basename(WWPO_DIR_PATH));

/** 定义 option 相关名称 */
define('OPTION_SETTING_KEY', 'wwpo-settings-data');
define('OPTION_CONTENT_KEY', '');

/** 加载文件 */
require WWPOPATH . 'includes/class-wwpo-array.php';
require WWPOPATH . 'includes/class-wwpo-check.php';
require WWPOPATH . 'includes/class-wwpo-file.php';
require WWPOPATH . 'includes/class-wwpo-get.php';
require WWPOPATH . 'includes/class-wwpo-meta.php';
require WWPOPATH . 'includes/class-wwpo-post.php';
require WWPOPATH . 'includes/class-wwpo-rest-controller.php';
require WWPOPATH . 'includes/class-wwpo-rest-user-controller.php';
require WWPOPATH . 'includes/class-wwpo-template.php';
require WWPOPATH . 'includes/class-wwpo-util.php';

if (is_admin()) {
    require WWPOPATH . 'includes/class-wwpo-admin.php';
    require WWPOPATH . 'includes/class-wwpo-error.php';
    require WWPOPATH . 'includes/class-wwpo-form.php';
    require WWPOPATH . 'includes/class-wwpo-list-table.php';
}

/** */
require WWPOPATH . 'includes/register.php';

/**
 *
 */
WWPO_File::require(WWPO_DIR_PATH . 'components');

/**
 *
 */
WWPO_File::require(WWPO_DIR_PATH . 'pages');

/**
 * 启动初始化动作加载函数
 *
 * @since 1.0.0
 */
do_action('wwpo_init');

if (is_admin()) {
    do_action('wwpo_admin_init');
}
