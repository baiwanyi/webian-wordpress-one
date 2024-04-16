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

/** 定义插件路径和地址 */
define('WWPO_PLUGIN_FILE', __FILE__);
define('WWPO_PLUGIN_PATH', plugin_dir_path(WWPO_PLUGIN_FILE));
define('WWPO_PLUGIN_URL', plugin_dir_url(WWPO_PLUGIN_FILE));

/** 定义插件名 */
define('WWPO_PLUGIN_DIR', basename(WWPO_PLUGIN_PATH));
define('WWPO_PLUGIN_NAME', plugin_basename(WWPO_PLUGIN_FILE));

/** 定义 option 相关名称 */
define('OPTION_SETTING_KEY', 'wwpo-settings-data');
define('OPTION_CONTENT_KEY', '');

/** 加载文件 */
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-array.php';
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-check.php';
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-file.php';
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-get.php';
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-image.php';
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-load.php';
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-meta.php';
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-post.php';
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-rest-controller.php';
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-rest-user-controller.php';
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-template.php';
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-url.php';
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-user.php';
require WWPO_PLUGIN_PATH . 'includes/class-wwpo-util.php';

if (is_admin()) {
    require WWPO_PLUGIN_PATH . 'includes/class-wwpo-admin.php';
    require WWPO_PLUGIN_PATH . 'includes/class-wwpo-error.php';
    require WWPO_PLUGIN_PATH . 'includes/class-wwpo-form.php';
    require WWPO_PLUGIN_PATH . 'includes/class-wwpo-list-table.php';
}

/** */
require WWPO_PLUGIN_PATH . 'includes/register.php';

/**
 *
 */
WWPO_File::require(WWPO_PLUGIN_PATH . 'components');

/**
 * 启动初始化动作加载函数
 *
 * @since 1.0.0
 */
do_action('wwpo_init');

if (is_admin()) {
    do_action('wwpo_admin_init');
}

// register_activation_hook( WWPO_PLUGIN_FILE, 'my_plugin_activate' );
