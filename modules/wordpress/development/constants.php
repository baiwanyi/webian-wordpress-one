<?php

/**
 * 系统常量显示页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/development
 */

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
    $data = wp_list_sort($data, ['value'=> 'ASC']);
}

// 显示表格内容
echo WWPO_Table::result($data, [
    'index'     => true,
    'column'    => [
        'name'  => '常量名',
        'value' => '值'
    ]
]);
