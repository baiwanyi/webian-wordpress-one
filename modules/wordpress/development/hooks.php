<?php

/**
 * 系统 Hook 钩子显示页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/development
 */

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
