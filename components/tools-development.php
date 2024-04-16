<?php

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_register_admin_menu_development($menus)
{
    $menus['wwpo-development'] = [
        'parent'        => 'tools.php',
        'menu_title'    => __('开发工具', 'wwpo')
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_register_admin_menu_development');

/**
 * 开发工具显示页面函数
 *
 * @since 1.0.0
 */
function wwpo_admin_display_wp_development()
{
    // 获取当前标签
    $current_tabs = $_GET['tab'] ?? 'dashicons';

    /** 判断标签显示搜索栏 */
    if ('dashicons' != $current_tabs) {
        WWPO_Template::searchbar([
            'tab'   => $current_tabs,
            'page'  => 'development'
        ]);
    }

    // 显示页面标签
    WWPO_Template::tabs([
        'dashicons'     => 'Dashicons',
        'constants'     => '系统常量',
        'hooks'         => '系统动作',
        'oembeds'       => 'Oembeds',
        'shortcodes'    => '短代码'
    ]);

    call_user_func("wwpo_admin_display_development_{$current_tabs}");
}
add_action('wwpo_admin_display_wwpo-development', 'wwpo_admin_display_wp_development');

/**
 * Dashicons 图标显示页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/development
 */
function wwpo_admin_display_development_dashicons()
{
    /** 读取 dashicons 样式文件 */
    $dashicon_css_file    = fopen(ABSPATH . '/' . WPINC . '/css/dashicons.css', 'r');

    /** 设定初始行数 */
    $num_line = 0;

    /** 设定初始图标内容 */
    $dashicons_data = '';

    /** 设定显示内容HTML代码 */
    $html = '
    <style type="text/css">
        .wwpo-dashicons { float: left; width: 100%; }
        .wwpo-dashicons a { float: left; display: block; width: 5rem; height: 6rem; color: #444; text-align: center; }
        .wwpo-dashicons a:hover { color: #0073aa }
        .wwpo-dashicons a p{ margin-top: 1rem; color: #777; }
        .wwpo-dashicons .dashicons {margin-top: 1rem; margin-bottom: 1.5rem; text-align: center; width: 5rem; }
        .wwpo-dashicons .dashicons:before { font-size: 3rem; }
    </style>
    <script type="text/javascript">
        jQuery(document).on(\'click\', \'a[href*="#dashicons"]\', function(event) {
            event.preventDefault();
            var icon = jQuery(this).attr(\'href\').substr(1);
            jQuery(\'body,html\').scrollTop(0);
            jQuery(\'input[name="icon-name"]\').val(icon).focus();
            jQuery(\'input[name="icon-span"]\').val(\'<span class="dashicons-before \'+icon+\'"></span>\');
        });
    </script>
    <h2 class="border-bottom pb-3">使用方式</h2>
    <p class="lead">在 WordPress 后台<a href="#">如何使用 Dashicons</a>。</p>
    <div class="input-group mb-3">
        <label class="input-group-text">图标名称</label>
        <input type="text" name="icon-name" class="form-control user-select-all" readonly="true">
    </div>
    <div class="input-group mb-5">
        <label class="input-group-text">使用标签</label>
        <input type="text" name="icon-span" class="form-control user-select-all" readonly="true">
    </div>';

    /** 判断是否到最后一行，否则循环获取内容 */
    while (!feof($dashicon_css_file)) {

        /** 读取每一行内容 */
        $content_line = fgets($dashicon_css_file);
        $num_line++;

        /** 前32行为CSS设定内容，跳过。 */
        if (32 > $num_line) continue;

        /** 判断行内容 */
        if ($content_line) {
            if (preg_match_all('/.dashicons-(.*?):before/i', $content_line, $matches)) {
                $dashicons_data .= sprintf(
                    '<a href="#dashicons-%s" class="mb-3 mx-3"><span class="dashicons dashicons-%s"></span><p id="line-%s" class="text-truncate">%s</p></a>',
                    $matches[1][0],
                    $matches[1][0],
                    $num_line,
                    $matches[1][0]
                );
            } elseif (preg_match_all('/\/\* (.*?) \*\//i', $content_line, $matches)) {
                if ($dashicons_data) {
                    $html .= sprintf('<div class="wwpo-dashicons mb-5">%s</div>', $dashicons_data);
                }
                $html .= sprintf('<h2 class="border-bottom pb-3">%s</h2>', $matches[1][0]);
                $dashicons_data = '';
            }
        }
    }

    /** 显示图标行内容 */
    if ($dashicons_data) {
        $html .= sprintf('<div class="wwpo-dashicons">%s</div>', $dashicons_data);
    }
    fclose($dashicon_css_file);

    // 返回内容
    echo $html;
}

/**
 * 系统常量显示页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/development
 */
function wwpo_admin_display_development_constants()
{
    // 初始化
    $i = 1;
    $data = [];

    /** 遍历系统常量内容数组 */
    foreach (get_defined_constants() as $name => $value) {
        $data[] = [
            'index' => $i,
            'name'  => $name,
            'value' => $value
        ];
        $i++;
    }

    /** 判断搜索关键字，筛选关键字内容 */
    if (isset($_GET['s'])) {
        $data = wp_list_filter($data, ['name' => $_GET['s']]);
        $data = wp_list_sort($data, ['value' => 'ASC']);
    }

    // 显示表格内容
    echo WWPO_Table::result($data, [
        'index'     => true,
        'column'    => [
            'name'  => '常量名',
            'value' => '值'
        ]
    ]);
}

/**
 * 系统 Hook 钩子显示页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/development
 */
function wwpo_admin_display_development_hooks()
{
    global $wp_filter;

    // 初始化
    $i = 1;
    $data = [];

    /** 遍历系统 Hooks 内容数组 */
    foreach ($wp_filter as $tag => $filter_array) {
        foreach ($filter_array as $priority => $function_array) {
            foreach ($function_array as $function => $function_detail) {
                $data[] = [
                    'index'     => $i,
                    'hook'      => $tag,
                    'function'  => $function,
                    'small-pri' => $priority
                ];
                $i++;
            }
        }
    }

    /** 判断搜索关键字，筛选关键字内容 */
    if (isset($_GET['s'])) {
        $data = wp_list_filter($data, ['hook' => $_GET['s'], 'function' => $_GET['s']], 'OR');
    }

    // 显示表格内容
    WWPO_Table::result($data, [
        'index'     => true,
        'column'    => [
            'hook'      => 'Hook',
            'function'  => '函数',
            'small-pri' => '优先级'
        ]
    ]);
}

/**
 * 系统 Oembed 显示页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/development
 */
function wwpo_admin_display_development_oembeds()
{
    // 初始化
    $i = 1;
    $data = [];

    // 引用 oembed 文件
    require_once(ABSPATH . WPINC . '/class-wp-oembed.php');

    /** 遍历系统 Oembed 内容数组 */
    foreach (_wp_oembed_get_object()->providers as $reg => $provider) {
        $data[] = [
            'index'         => $i,
            'name'          => $reg,
            'oembed'        => $provider[0],
            'small-rule'    => ($provider[1] ? '是' : '否')
        ];
        $i++;
    }

    /** 判断搜索关键字，筛选关键字内容 */
    if (isset($_GET['s'])) {
        $data = wp_list_filter($data, ['oembed' => $_GET['s']]);
    }

    // 显示表格内容
    echo WWPO_Table::result($data, [
        'index'     => true,
        'column'    => [
            'name'          => '格式',
            'oembed'        => 'Oembed 地址',
            'small-rule'    => '使用正则'
        ]
    ]);
}

/**
 * 系统短代码显示页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/development
 */
function wwpo_admin_display_development_shortcodes()
{
    global $shortcode_tags;

    // 初始化
    $i = 1;
    $data = [];

    /** 遍历系统短代码内容数组 */
    foreach ($shortcode_tags as $tag => $function) {
        $function = (is_array($function)) ? get_class($function[0]) . '->' . (string) $function[1] : $function;
        $data[] = [
            'index'     => $i,
            'name'      => $tag,
            'function'  => $function
        ];
        $i++;
    }

    /** 判断搜索关键字，筛选关键字内容 */
    if (isset($_GET['s'])) {
        $data = wp_list_filter($data, ['name' => $_GET['s']]);
    }

    // 显示表格内容
    echo WWPO_Table::result($data, [
        'index'     => true,
        'column'    => [
            'name'      => 'Shortcode',
            'function'  => '处理函数'
        ]
    ]);
}
