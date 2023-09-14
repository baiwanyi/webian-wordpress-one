<?php

/**
 * 客户列表管理页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Webian-CRM
 */

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_crm_customer_admin_menu($menus)
{
    $menus['customer'] = [
        'parent'        => 'users.php',
        'menu_title'    => __('客户管理', 'wwpo'),
        'page_title'    => __('所有客户', 'wwpo'),
        'label_title'   => __('客户', 'wwpo'),
        'role'          => 'edit_posts',
        'post_new'      => true
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_crm_customer_admin_menu');

/**
 * 客户管理显示页面函数
 *
 * @since 1.0.0
 * @param string $page_action
 */
function wwpo_crm_customer_admin_display($page_action)
{
    if (empty($page_action)) {
        wwpo_crm_customer_page_table();
        return;
    }

    wwpo_crm_customer_page_display();
}
add_action('wwpo_admin_display_customer', 'wwpo_crm_customer_admin_display');

/**
 * 客户管理列表页面显示函数
 *
 * @since 1.0.0
 */
function wwpo_crm_customer_page_table()
{
    // 查询条件数组
    $wheres = [];

    // 判断搜索关键字
    if (isset($_GET['s'])) {
        $wheres['search'] = "user_display LIKE '%{$_GET['s']}%'";
    }

    // 显示搜索栏
    wwpo_admin_page_searchbar(['page' => 'customer']);

    // 显示客户列表
    echo WWPO_Table::select(WPMALL_SQL_CUSTOMER, $wheres, [
        'index'     => true,
        'orderby'   => 'customer_id',
        'column'    => [
            'title'         => __('客户名称', 'wwpo'),
            'account'       => __('Username'),
            'small-region'  => __('区域', 'wwpo'),
            'small-order'   => __('总订单数', 'wwpo'),
            'small-price'   => __('总金额数', 'wwpo'),
            'small-rank'    => __('等级', 'wwpo'),
            'date'          => __('Date')
        ]
    ]);
}

/**
 * 自定义内容表格列输出函数
 *
 * @since 1.0.0
 * @param array     $data           表格列值
 * @param string    $column_name    表格列名称
 */
function wwpo_crm_customer_table_column($data, $column_name)
{
    switch ($column_name) {

            // 客户姓名
        case 'title':
            echo WWPO_Admin::title($data['customer_id'], $data['user_display'], 'edit');
            break;

            // 客户地区
        case 'region':
            echo wwpo_crm_get_region('guangxi', $data['user_region']);
            break;

            // 关联帐号
        case 'account':
            printf('<a href="%s" target="_blank">%s</a>', admin_url('user-edit.php?user_id=' . $data['user_customer']), get_the_author_meta('display_name', $data['user_customer']));
            break;

            // 订单总数
        case 'order':
            echo $data['total_order'] ?? 0;
            break;

            // 消费总金额
        case 'price':
            echo $data['total_price'] ?? 0;
            break;

            // 客户等级
        case 'rank':
            echo wwpo_crm_customer_rank($data['user_rank']);
            break;

        default:
            break;
    }
}
add_action('wwpo_table_customer_custom_column', 'wwpo_crm_customer_table_column', 10, 2);

/**
 * 客户编辑页面函数
 *
 * @since 1.0.0
 */
function wwpo_crm_customer_page_display()
{
    // 设定参数
    $customer_id    = WWPO_Admin::post_id(0, true);
    $customer_data  = [
        'user_customer' => 0,
        'user_post'     => 0,
        'user_region'   => 0,
        'user_contact'  => '',
        'user_phone'    => '',
        'user_display'  => '',
        'user_location' => '',
        'user_rank'     => 0,
        'remark'        => ''
    ];

    /** 判断客户 ID 获取客户信息 */
    if ($customer_id) {
        $customer_data = wwpo_get_row(WPMALL_SQL_CUSTOMER, 'customer_id', $customer_id, null, ARRAY_A);
    }

    if (empty($customer_data)) {
        echo WWPO_Admin::messgae('error', '未找到相关客户');
        return;
    }

    // 反序列化客户定位和地址
    $user_location = maybe_unserialize($customer_data['user_location']);
    $user_location_info = $user_location['ad_info'] ?? [];

    // 格式化显示客户地区：省 / 市 / 区
    if ($user_location_info) {
        unset($user_location_info['adcode']);
        $user_location_area = implode(' / ', array_values($user_location_info));
    }

    // 设定客户编辑表单数组
    $customer_form = [
        'hidden'    => [
            'customer_id'   => $customer_id,
            'user_selected' => $customer_data['user_customer']
        ],
        'submit'    => 'updatecustomer'
    ];

    // 设定客户编辑表单内容
    $customer_form['formdata'] = [
        'updated[user_display]' => [
            'title' => '客户名称',
            'field' => ['type' => 'text', 'value' => $customer_data['user_display']]
        ],
        'customer_selected' => [
            'title' => '关联帐号',
            'class' => 'regular-text',
            'field' => [
                'type'  => 'text',
                'class' => 'webui__admin-field w-50',
                'value' => get_the_author_meta('login', $customer_data['user_customer'])
            ],
            'after' => '<button type="button" data-action="wpajax" class="button" value="wwpomallselectuser">检索</button>'
        ],
        'updated[user_region]' => [
            'title' => '所属区域',
            'field' => [
                'type'      => 'select',
                'option'    => wwpo_crm_get_region('guangxi'),
                'selected'  => $customer_data['user_region'],
                'show_option_all' => '选择所属区域'
            ]
        ],
        'updated[user_contact]' => [
            'title' => '联系人',
            'field' => ['type' => 'text', 'value' => $customer_data['user_contact']]
        ],
        'updated[user_phone]' => [
            'title' => '手机号码',
            'field' => ['type' => 'tel', 'value' => $customer_data['user_phone']]
        ],
        'user_location' => [
            'title'     => '送货地址',
            'fields'    => [
                'user_location_area' => [
                    'class'     => 'regular-text',
                    'before'    => '<span class="input-group-text">所在地区</span>',
                    'field'     => ['type' => 'text', 'value' => $user_location_area ?? '', 'readonly' => true],
                    'after'     => '<button type="button" class="btn btn-primary" data-action="wwpomallselectmodal"><span class="dashicons-before dashicons-location"></span></button>'
                ],
                'user_location_address' => [
                    'before'    => '<span class="input-group-text">详细地址</span>',
                    'field'     => [
                        'type'  => 'textarea',
                        'css'   => 'form-control',
                        'value' => $user_location['address'] ?? '',
                        'rows'  => 3
                    ],
                    'desc' => sprintf('<textarea id="wwpo-mall-current-location" name="location" hidden>%s</textarea>', wwpo_json_encode($user_location))
                ]
            ]
        ],
        'updated[user_rank]' => [
            'title' => '客户等级',
            'field' => [
                'type'      => 'select',
                'option'    => wwpo_crm_customer_rank(),
                'selected'  => $customer_data['user_rank']
            ]
        ],
        'updated[remark]' => [
            'title' => '备注信息',
            'field' => ['type' => 'textarea', 'value' => $customer_data['remark']]
        ],
    ];

    /** 管理员权限编辑客户所属业务员 */
    if (current_user_can(WWPO_ROLE)) {

        // 获取可归属的角色列表
        $user_manager = get_users(['roles' => 'administrator', 'director', 'manager']);

        // 设定业务员下拉表单
        $customer_form['formdata']['updated[user_post]'] = [
            'title' => '业务员',
            'field' => [
                'type'      => 'select',
                'option'    => array_column($user_manager, 'display_name', 'ID'),
                'selected'  => $customer_data['user_post'],
                'show_option_all' => '选择业务员'
            ]
        ];
    }

    // 显示编辑表单
    echo WWPO_Form::table($customer_form);
    echo '<textarea id="wwpo-mall-customer-location" hidden></textarea>';

    // 显示地区选择模板
    wwpo_crm_customer_tmpl_location_modal($customer_data['user_display'], $customer_data['user_region']);
    wwpo_crm_customer_tmpl_location_table();
}

/**
 * 设置页面消息函数
 *
 * @since 1.0.0
 * @param array $message
 */
function wwpo_crm_customer_message($message)
{
    $message['customer'] = [
        'success_added'     => ['updated'   => __('客户信息添加成功', 'wpmall')],
        'success_updated'   => ['updated'   => __('客户信息已更新', 'wpmall')],
        'fail_added'        => ['error' => __('客户信息添加失败', 'wpmall')],
        'not_null_name'     => ['error' => __('客户名称不能为空', 'wpmall')],
    ];

    return $message;
}
add_filter('wwpo_admin_message', 'wwpo_crm_customer_message');

/**
 * 客户内容更新操作函数
 *
 * @since 1.0.0
 */
function wwpo_crm_customer_post_update()
{
    $customer_id        = $_POST['customer_id'] ?? 0;
    $updated            = $_POST['updated'] ?? [];
    $location_data      = $_POST['location'] ?? '';
    $location_address   = $_POST['user_location_address'] ?? '';

    // 判断添加或编辑
    if (empty($customer_id)) {
        $url_query = ['post'  => 'new'];
    } else {
        $url_query = ['post'  => $customer_id, 'action' => 'edit'];
    }

    // 判断客户名称为空
    if (empty($updated['user_display'])) {
        new WWPO_Error('message', 'not_null_name', $url_query);
        exit;
    }

    // 设定客户地点定位
    if ($location_data) {
        $location_data = stripslashes($location_data);
        $location_data = wwpo_json_decode($location_data);
    } else {
        $location_data = [];
    }

    // 设定用户地址
    if ($location_address) {
        $last_location_address = $location_data['address'] ?? '';

        // 判断详细地址与地点地位的不一样时，以输入的地址为准
        if ($location_address != $last_location_address) {
            $location_data['address'] = $location_address;
        }
    }

    $updated['user_customer'] = $_POST['user_selected'] ?? 0;
    $updated['user_location'] = maybe_serialize($location_data);

    /** 判断 $customer_id 为空，自定义内容新增操作 */
    if (empty($customer_id)) {

        // 设定添加参数
        $updated['user_post']   = get_current_user_id();
        $updated['time_post']   = NOW_TIME;

        // 写入数据
        $customer_id = wwpo_insert_post(WPMALL_SQL_CUSTOMER, $updated);

        // 判断写入失败
        if (empty($customer_id)) {
            new WWPO_Error('message', 'fail_added', $url_query);
            return;
        }

        // 写入成功转跳 URL
        new WWPO_Error('message', 'success_added', [
            'post'      => $customer_id,
            'action'    => 'edit'
        ]);
        return;
    }

    // 以 $customer_id 为 KEY 写入数组
    $updated['time_modified'] = NOW_TIME;

    // 更新到数据库
    wwpo_update_post(WPMALL_SQL_CUSTOMER, $updated, ['customer_id' => $customer_id]);

    // 返回更新成功信息
    new WWPO_Error('message', 'success_updated', $url_query);
}
add_action('wwpo_post_admin_updatecustomer', 'wwpo_crm_customer_post_update');

/**
 * 客户地点定位选择弹出层函数
 * 传参用于进行默认搜索
 *
 * @since 1.0.0
 * @param string $user_display  客户显示名称
 * @param string $user_region   客户所属地区
 */
function wwpo_crm_customer_tmpl_location_modal($user_display, $user_region)
{
    // 客户所在地区下拉菜单
    $location_region = WWPO_Form::field('wwpo-modal-location-region', [
        'field' => [
            'type'      => 'select',
            'option'    => wwpo_crm_get_region('guangxi'),
            'selected'  => $user_region,
            'show_option_all' => '选择所属区域'
        ]
    ]);

    // 客户名称搜索表单
    $location_search = WWPO_Form::field('wwpo-modal-location-search', [
        'before'    => '<span class="input-group-text dashicons-before dashicons-search"></span>',
        'after'     => '<button type="button" class="button" data-action="wwpomodalsearchlocation">搜索</button>',
        'field'     => [
            'type'      => 'text',
            'value'     => $user_display,
            'placeholder'   => '搜索关键字...'
        ]
    ]);
?>
    <script type="text/template" id="tmpl-wwpo-mall-customer-location-modal">
        <div class="hstack gap-3">
<?php echo $location_region; ?>
<?php echo $location_search; ?>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>地点</th>
                <th>区域</th>
                <th>地址</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="wwpo-mall-location-list" class="table-group-divider">
            <tr>
                <td colspan="5" class="noitems text-center">没有找到相关内容</td>
            </tr>
        </tbody>
    </table>
</div>
</script>
<?php
}

/**
 * 客户搜索结果显示表格模板
 *
 * @since 1.0.0
 */
function wwpo_crm_customer_tmpl_location_table()
{
?>
    <script type="text/template" id="tmpl-wwpo-mall-customer-location-table">
        <% _.each( data, function (item, i) { %>
<tr>
    <td>{{i + 1}}</td>
    <td>{{item.title}}</td>
    <td>{{item.ad_info.province}} / {{item.ad_info.city}} / {{item.ad_info.district}}</td>
    <td>{{item.address}}</td>
    <td>
        <button type="button" class="button button-primary" data-action="wwpomallselectlocation" value="{{i}}">选择</button>
    </td>
</tr>
<% } ) %>
</script>
<?php
}

/**
 * 添加地图A PI KEY 自定义设定表单函数
 * 设置在通用标签页面下
 *
 * @since 1.0.0
 * @param array $settings
 */
function wwpo_admin_settings_common_mapapi($settings)
{
    // 获取设置保存值
    $option_data = get_option('wwpo-settings-common');

    // 设置表单内容数组
    $settings['common']['formdata']['option_data[mapapi]'] = [
        'title' => __('地图API KEY', 'wwpo'),
        'field' => ['type' => 'text', 'value' => $option_data['mapapi'] ?? '']
    ];

    // 返回设置内容
    return $settings;
}
add_filter('wwpo_admin_page_settings', 'wwpo_admin_settings_common_mapapi');
