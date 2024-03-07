<?php

/**
 * 订单编辑页面
 *
 * @since 1.0.0
 * @package Webian WP Mall
 */

/**
 * 订单编辑页面显示函数
 *
 * @since 1.0.0
 */
function wwpo_mall_order_page_display()
{
    $order_id   = WWPO_Admin::post_id(0, true);
    $user_id    = get_current_user_id();

    if (empty($order_id)) {

        $post_updated = wwpo_get_row(WWPO_SQL_ORDER, 'user_post', $user_id, 'ORDER BY order_id DESC', ARRAY_A);

        if (empty($post_updated)) {
            $post_updated = [
                'order_trade_no'    => date('Ymdhis', NOW) . wwpo_unique($user_id),
                'user_post'         => $user_id,
                'order_customer'    => 0,
                'user_agent'        => 0,
                'post_status'       => 'draft',
                'source'            => 'admin',
                'time_post'         => NOW_TIME
            ];

            $post_updated['order_id'] = wwpo_insert_post(WWPO_SQL_ORDER, $post_updated);
        }
    }
    //
    else {
        $post_updated = wwpo_get_row(WWPO_SQL_ORDER, 'order_id', $order_id, null, ARRAY_A);
    }

    if (empty($post_updated)) {
        echo WWPO_Admin::messgae('error', '未找到相关订单');
        return;
    }
?>
    <form id="poststuff" method="POST" autocomplete="off">
        <?php
        echo WWPO_Form::hidden([
            'order_id'          => $post_updated['order_id'],
            'order_customer'    => $post_updated['order_customer']
        ]); ?>
        <div class="wp-filter">
            <div class="d-flex justify-content-between align-items-center">
                <div class="lead">订单编号：<span class="user-select-all"><?php echo $post_updated['order_trade_no']; ?></span></div>
                <div class="hstack gap-2 py-2">
                    <div class="dropdown">
                        <button class="button dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <span class="dashicons-before dashicons-plus-alt2"></span>
                            <span>添加</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><button type="button" class="dropdown-item" data-action="wpmallorderitem" value="sidebar">产品</button></li>
                            <li><button type="button" class="dropdown-item" data-action="wpmallorderstatus" value="sidebar">状态</button></li>
                            <li><button type="button" class="dropdown-item" data-action="wpmallorderpayment" value="sidebar">资金</button></li>
                            <li><button type="button" class="dropdown-item" data-action="wpmallordermedia" value="sidebar">设计</button></li>
                        </ul>
                    </div>
                    <div class="vr mx-1"></div>
                    <button type="button" class="button button-primary">保存更改</button>
                    <button type="button" class="button" data-action="wpmallorderprint">打印订单</button>
                </div>
            </div>
        </div>
        <!-- /.wp-filter -->

        <div id="post-body" class="metabox-holder columns-2">
            <div id="postbox-container-1" class="postbox-container">
                <?php
                $postbox_side = [
                    'info'      => '订单信息',
                    'customer'  => '客户信息'
                ];

                if (current_user_can(WWPO_ROLE)) {
                    $postbox_side['manager'] = '业务员';
                }

                foreach ($postbox_side as $side_key => $side_title) {
                    if (!function_exists("wwpo_mall_order_side_{$side_key}")) {
                        continue;
                    }

                    printf('<div id="wwpo-mall-order-%s" class="postbox">', $side_key);
                    printf('<div class="postbox-header"><h2>%s</h2></div><div class="inside">', $side_title);
                    call_user_func("wwpo_mall_order_side_{$side_key}", $post_updated);
                    printf('</div></div><!-- /#wwpo-mall-order-side-%s -->', $side_key);
                }
                ?>
            </div>
            <!-- /#postbox-container-1 -->

            <div id="postbox-container-2" class="postbox-container">
                <?php
                $postbox_core = [
                    'products'  => '产品',
                    'payment'   => '支付',
                    'status'    => '状态',
                    'desgin'    => '设计',
                    'excerpteditor' => '备注'
                ];

                foreach ($postbox_core as $core_key => $core_title) {
                    if (!function_exists("wwpo_mall_order_core_{$core_key}")) {
                        continue;
                    }

                    printf('<div id="wwpo-mall-order-core-%s" class="postbox">', $core_key);
                    printf('<div class="postbox-header"><h2>%s</h2></div><div class="inside">', $core_title);
                    call_user_func("wwpo_mall_order_core_{$core_key}", $post_updated);
                    printf('</div></div><!-- /#wwpo-mall-order-core-%s -->', $core_key);
                }
                ?>
            </div>
            <!-- /#postbox-container-2 -->

        </div>
        <!-- /#post-body -->

    </form>
    <!-- /#poststuff -->
<?php
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_mall_order_core_products()
{
    echo '<div id="wwpo-order-items"><p class="lead"><span class="webui-loading small"></span></p></div>';
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_mall_order_core_payment()
{
    $wheres = [];
    echo '<div class="table-responsive">';
    echo WWPO_Table::select(WWPO_SQL_ORDER_PAYMENT, $wheres, [
        'index'     => true,
        'tablenav'  => false,
        'orderby'   => 'payment_id',
        'column'    => [
            '1a' => '项目',
            'a11' => '金额（元）',
            'a12' => '日期',
            'a14' => '说明',
            'a15' => '操作'
        ]
    ]);
    echo '</div>';
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_mall_order_core_status()
{
    $wheres = [];
    echo '<div class="table-responsive">';
    echo WWPO_Table::select(WWPO_SQL_ORDER_STATUS, $wheres, [
        'index'     => true,
        'tablenav'  => false,
        'orderby'   => 'status_id',
        'column'    => [
            '1' => '类型',
            '11' => '日期',
            '12' => '说明',
            '13' => '操作'
        ]
    ]);
    echo '</div>';
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_mall_order_core_excerpteditor()
{
    wp_editor('', 'productexcerpt', [
        'textarea_name' => 'excerpt',
        'textarea_rows' => 5,
        'wpautop'       => false,
        'media_buttons' => false,
        'tinymce'       => false,
        'quicktags'     => false
    ]);
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_mall_order_side_checkout()
{
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_mall_order_side_info($data = [])
{
    switch ($data['post_status']) {
        case 'draft':
            $status_class = 'black-50';
            break;

        case 'shipment':
            $status_class = 'danger';
            break;

        case 'complete':
            $status_class = 'success';
            break;

        default:
            $status_class = 'body';
            break;
    }

    $order_status = wwpo_mall_order_status();

    $display_posts = [
        'post-status' => [
            'title'     => '订单状态',
            'content'   => $order_status[$data['post_status']] ?? '未知状态',
            'css'       => 'text-end text-' . $status_class
        ],
        'laptop'  => [
            'title'     => '订单来源',
            'content'   => wwpo_mall_order_source($data['source'])
        ],
        'calendar' => [
            'title'     => '发布时间',
            'content'   => wwpo_human_time($data['time_post'] ?? NOW_TIME, 'm月d日 H:i')
        ]
    ];

    if ('0000-00-00 00:00:00' != ($data['time_modified'] ?? NOW_TIME)) {
        $display_posts['edit'] = [
            'title'     => '最后修改',
            'content'   => wwpo_human_time($data['time_modified'] ?? NOW_TIME, 'm月d日 H:i'),
            'css'       => 'text-end'
        ];
    }

    $content = '<ul class="list-inline">';

    foreach ($display_posts as $display_key => $display_post) {
        $content .= sprintf(
            '<li class="d-flex justify-content-between align-items-center pb-2"><div class="text-muted"><span class="dashicons-before dashicons-%s"></span><span class="px-1 align-bottom">%s</span></div><div class="%s">%s</div></li>',
            $display_key,
            $display_post['title'],
            $display_post['css'] ?? 'text-end',
            $display_post['content'] ?? ''
        );
    }

    $content .= '</ul>';

    echo $content;
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_mall_order_side_customer($data)
{
    $user_customer = wwpo_json_decode($data['order_customer']);

    $arrayform['formdata'] = [
        'customer' => [
            'fields' => [
                'user_customer[phone]' => [
                    'title' => '手机号码',
                    'field' => [
                        'type'  => 'tel',
                        'value' => $user_customer['phone'] ?? ''
                    ],
                    'after' => '<button type="button" data-action="checkcustomerphone" class="button">检索</button>'
                ],
                'user_customer[display]' => [
                    'title' => '客户名称',
                    'field' => [
                        'type'  => 'text',
                        'value' => $user_customer['display'] ?? ''
                    ]
                ],
                'user_customer[contact]' => [
                    'title' => '联系人',
                    'field' => [
                        'type'  => 'text',
                        'value' => $user_customer['contact'] ?? ''
                    ]
                ],
                'user_customer[address]' => [
                    'title' => '送货地址',
                    'field' => [
                        'type'  => 'textarea',
                        'value' => $user_customer['address'] ?? ''
                    ]
                ]
            ]
        ],
        'user_customer[delivery]' => [
            'title' => '约定货期',
            'field' => ['type' => 'date', 'value' => $user_customer['delivery'] ?? date('Y-m-d', NOW)]
        ]
    ];

    echo WWPO_Form::list($arrayform);
}

/**
 * Undocumented function
 *
 * @param [type] $data
 * @return void
 */
function wwpo_mall_order_side_manager($data)
{
    $user_id = get_current_user_id();
    $user_meta = get_user_meta($user_id);

    $user_rate = $data['taking_ratio'];

    if (empty($user_rate)) {
        $user_rate = $user_meta['_wwpo_wpmall_user_rate'][0] ?? 0;
    }

    $user_manager = get_users(['roles' => ['administrator', 'director', 'manager']]);

    $arrayform = [
        'hidden'    => ['user_agent' => $data['user_agent']],
        'formdata'  =>  [
            'user_commission_rate' => [
                'title' => '业务佣金比例（%）',
                'field' => ['type' => 'text', 'value' => $user_rate]
            ],
            'user_agent' => [
                'title' => '所属业务员',
                'field' => [
                    'type'      => 'select',
                    'css'       => 'w-100',
                    'option'    => array_column($user_manager, 'display_name', 'ID'),
                    'selected'  => $data['user_agent']
                ]
            ]
        ]
    ];

    echo WWPO_Form::list($arrayform);
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_mall_order_core_desgin()
{
    // echo '<button type="button" data-action="wwpomallordermedia" class="button button-primary my-2">添加</button>';
}
