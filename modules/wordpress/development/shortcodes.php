<?php

/**
 * 系统短代码显示页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/development
 */
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
