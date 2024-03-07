<?php

/**
 * 订单列表页面
 *
 * @since 1.0.0
 * @package Webian WP Mall
 */

/**
 * 订单显示页面函数
 *
 * @since 1.0.0
 * @param string $page_action
 */
function wwpo_mall_order_admin_display($page_action)
{
    switch ($page_action) {
        case 'edit':
            wwpo_mall_order_page_display();
            break;
        case 'options':
            wwpo_mall_order_page_options();
            break;
        default:
            wwpo_mall_order_page_table();
            break;
    }
}
add_action('wwpo_admin_display_wwpopayment', 'wwpo_mall_order_admin_display');

/**
 * 订单列表内容显示函数
 *
 * @since 1.0.0
 */
function wwpo_mall_order_page_table()
{
    $wheres = [];
    $settings = [
        'orderby'   => 'order_id',
        'checkbox'  => true,
        'column'    => [
            'thumb'                 => __('封面', 'wpmall'),
            'order_item'            => '商品名称',
            'order_trade_no'        => '订单编号',
            'medium-customer'       => '所属客户',
            'small-order_agent'     => '业务员',
            'small-order_total'     => '金额',
            'small-order_cash'      => '利润',
            'small-order_source'    => '来源',
            'date'                  => __('Date')
        ]
    ];

    echo WWPO_Table::select(WWPO_SQL_ORDER, $wheres, $settings);
}

/**
 * 自定义内容表格列输出函数
 *
 * @since 1.0.0
 * @param array     $data           表格列值
 * @param string    $column_name    表格列名称
 */
function wwpo_mall_order_table_column($data, $column_name)
{
    $order_items = wwpo_get_post(WWPO_SQL_ORDER_ITEM, 'order_id', $data['order_id']);

    switch ($column_name) {

            // 产品封面
        case 'thumb':

            if (empty($order_items[0])) {
                return;
            }

            printf(
                '<div class="ratio ratio-1x1"><img src="%s" class="thumb rounded"></div>',
                wwpo_oss_cdnurl($order_items[0]->thumb_url, 'thumbnail')
            );

            break;

        case 'order_item':

            if (empty($order_items[0])) {
                return;
            }

            printf(
                '<p>%s <small>等 %d 项目</small></p><p>货号：%s</p>',
                WWPO_Admin::title($data['order_id'], $order_items[0]->item_title),
                count($order_items),
                $order_items[0]->barcode
            );
            break;

        case 'customer':
            $order_customer = wwpo_get_post(WWPO_SQL_CUSTOMER, 'customer_id', $data['order_customer'], 'user_display');

            printf(
                '<a href="/wp-admin/admin.php?page=customer&post=%d&action=edit" target="_blank">%s</a>',
                $data['order_customer'],
                $order_customer
            );

            break;

        case 'order_agent':
            the_author_meta('display_name', $data['user_agent']);
            break;

        case 'order_total':
            $order_pay = $data['order_pay'] / 100;
            echo number_format($order_pay, 2);
            break;

        case 'order_cash':
            $order_profit = $data['order_profit'] / 100;
            echo number_format($order_profit, 2);
            break;

        case 'order_source':
            echo wwpo_mall_order_source($data['source']);
            break;

        default:
            break;
    }
}
add_action('wwpo_table_wwpo-payment_custom_column', 'wwpo_mall_order_table_column', 10, 2);
