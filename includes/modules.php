<?php

/**
 * 主题模块管理页面
 *
 * @package Webian WordPress One
 */

/**
 * 主题模块管理页面显示函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_modules()
{
    // 获取模块保存设置
    $modules_data = get_option('wwpo-active-modules');

    // 获取模块目录列表
    $array_modules_list = list_files(WWPO_MOD_PATH, 1);

    // 初始化模块信息内容数组
    $array_modules_info = [];

    // 设定模块默认头部信息内容数组
    $default_headers    = [
        'name'          => 'Modules Name',
        'version'       => 'Version',
        'description'   => 'Description',
        'author'        => 'Author',
        'updated'       => 'Updated'
    ];

    /** 判断模块目录列表为空 */
    if (empty($array_modules_list)) {
        printf('<div id="message" class="notice error"><p>%s</p></div>', __('未找到任何模块', 'wwpo'));
        return;
    }

    /** 遍历模块目录列表内容数组，读取模块 autoload.php 文件的头部信息 */
    foreach ($array_modules_list as $modules_dir) {

        // 设定模块别名和模块 autoload.php 文件路径
        $modules_key        = basename($modules_dir);
        $modules_autoload   = $modules_dir . DIRECTORY_SEPARATOR . 'autoload.php';

        // 判断 autoload.php 文件为空
        if (!file_exists($modules_autoload)) {
            continue;
        }

        // 读取模块 autoload.php 文件头部信息
        $array_modules_info[$modules_key] = get_file_data($modules_autoload, $default_headers);
    }

    // 模块列表开始
    echo '<div class="wp-list-table widefat modules-install">';

    /**
     * 遍历模块信息内容数组
     *
     * @property string $modules_key    模块别名
     * @property array  $modules_info   模块信息内容数组
     */
    foreach ($array_modules_info as $modules_key => $modules_info) {

        // 设定模块 icon 路径
        $modules_icon_path = sprintf('%s/%s/icon.png', WWPO_MOD_PATH, $modules_key);

        // 判断模块 icon 路径，设定 icon 地址
        if (file_exists($modules_icon_path)) {
            $modules_info['icon'] = sprintf('%s/%s/icon.png', WWPO_MOD_URL, $modules_key);
        } else {
            $modules_info['icon'] = admin_url('images/wordpress-logo.svg');
        }

        if (empty($modules_data[$modules_key]['enable'])) {
            $modules_button = [
                'class' => 'button button-primary',
                'text'  => __('启用', 'wwpo')
            ];
        } else {
            $modules_button = [
                'class' => 'button',
                'text'  => __('禁用', 'wwpo')
            ];
        }

        /**
         * 模块列表显示
         *
         * @since 1.0.0
         */
?>
        <div class="plugin-card">
            <div class="plugin-card-top">
                <div class="name column-name">
                    <h3 class="fw-bold"><?php echo $modules_info['name']; ?></h3>
                    <img src="<?php echo $modules_info['icon']; ?>" class="plugin-icon">
                </div>
                <div class="action-links">
                    <ul class="plugin-action-buttons">
                        <li>
                            <?php
                            // 设定操作按钮
                            printf(
                                '<button type="button" data-action="wpajax" data-module="%s" data-nonce="%s" class="%s" value="updatemodulestatus">%s</button>',
                                $modules_key,
                                wp_create_nonce($modules_key),
                                $modules_button['class'],
                                $modules_button['text']
                            ); ?>
                        </li>
                        <li><?php printf(__('%s 版本', 'wwpo'), $modules_info['version']); ?></li>
                    </ul>
                </div>
                <div class="desc column-description">
                    <p><?php echo $modules_info['description']; ?></p>
                    <p class="authors text-muted text-uppercase"><?php echo $modules_info['author']; ?></p>
                </div>
            </div>
            <div class="plugin-card-bottom">
                <div class="column-updated">
                    <strong>最近更新：</strong>
                    <?php printf(__('%s ago'), human_time_diff(strtotime($modules_info['updated']))); ?>
                </div>
            </div>
        </div>
<?php
    }

    // 模块列表结束
    echo '</div>';
}
add_action('wwpo_admin_display_wwpomodules', 'wwpo_admin_display_modules');

/**
 * 模块状态 AJAX 操作函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_modules_submit()
{
    $modules_key = $_POST['module'];

    // 获取模块保存设置
    $modules_data = get_option('wwpo-active-modules');

    if (empty($modules_key)) {
        echo new WWPO_Error('not_found_content');
        return;
    }

    if (!wp_verify_nonce($_POST['nonce'], $modules_key)) {
        echo new WWPO_Error('invalid_nonce');
        return;
    }

    if (!current_user_can(WWPO_ROLE)) {
        echo new WWPO_Error('invalid_user_role');
        return;
    }

    if (empty($modules_data[$modules_key]['enable'])) {

        /**
         * 模块激活动作
         *
         * @since 1.0.0
         */
        do_action('wwpo_modules_activated');

        // 设置模块启用
        $modules_data[$modules_key]['enable'] = 1;
        $message = __('模块已启用', 'wwpo');

        // 设定日志
        wwpo_logs('wwpo:modules:activated:' . $modules_key);
    }
    //
    else {

        /**
         * 模块禁用动作
         *
         * @since 1.0.0
         */
        do_action('wwpo_modules_deactivated');

        // 设置模块禁用
        $modules_data[$modules_key]['enable'] = 0;
        $message = __('模块已禁用', 'wwpo');

        // 设定日志
        wwpo_logs('wwpo:modules:deactivated:' . $modules_key);
    }

    update_option('wwpo-active-modules', $modules_data);

    echo WWPO_Error::toast('success', $message, ['url' => 'reload']);
}
add_action('wwpo_ajax_admin_updatemodulestatus', 'wwpo_admin_display_modules_submit');
