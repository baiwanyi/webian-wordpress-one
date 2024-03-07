<?php

/**
 * 订单支付模块应用函数
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/payment
 */

/**
 * 自定义订单状态函数
 *
 * @since 1.0.0
 * @param string $status
 */
function wwpo_mall_order_status($status = '')
{
    $status = [
        'draft'         => '<span class="text-black-50">草稿</span>',
        'post'          => '已发布',
        'prepayment'    => '已支付 - 预付款',
        'payment'       => '已支付 - 尾款',
        'complete'      => '订单完成',
        'order'         => '工厂下单',
        'shipment'      => '工厂发货',
        'received'      => '已到货',
        'delivery'      => '配送中'
    ];

    return $status;
}
add_filter('wwpo_table_wwpo-payment_post_status', 'wwpo_mall_order_status');

/**
 * 自定义订单来源函数
 *
 * @since 1.0.0
 * @param string $ource
 */
function wwpo_mall_order_source($source = '')
{
    $array_source = [
        'admin' => '后台',
        'weapp' => '小程序',
        'web'   => '网站'
    ];

    if ($source) {
        return $array_source[$source] ?? '';
    }

    return $array_source;
}
