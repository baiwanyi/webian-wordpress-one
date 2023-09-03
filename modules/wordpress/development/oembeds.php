<?php

/**
 * 系统 Oembed 显示页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/development
 */

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
