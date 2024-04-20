<?php

class WWPO_Pay_Interface
{
    /**
     * 添加后台管理菜单
     *
     * @since 1.0.0
     * @param array $menus
     */
    static function admin_menu($menus)
    {
        $menus['wwpo-payment'] = [
            'menu_title'    => __('支付', 'wwpo'),
            'label_title'   => __('概览', 'wwpo'),
            'icon'          => 'shield-alt'
        ];

        $menus['wwpo-payment-order'] = [
            'parent'        => 'wwpo-payment',
            'menu_title'    => __('订单', 'wwpo'),
            'page_title'    => __('支付订单', 'wwpo')
        ];

        $menus['wwpo-payment-refund'] = [
            'parent'        => 'wwpo-payment',
            'menu_title'    => __('退款', 'wwpo'),
            'page_title'    => __('退款管理', 'wwpo')
        ];

        $menus['wwpo-payment-notify'] = [
            'parent'        => 'wwpo-payment',
            'menu_title'    => __('通知', 'wwpo'),
            'page_title'    => __('支付通知', 'wwpo')
        ];

        $menus['wwpo-payment-setting'] = [
            'parent'        => 'wwpo-payment',
            'menu_title'    => __('配置', 'wwpo'),
            'page_title'    => __('支付配置', 'wwpo')
        ];

        return $menus;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    static function admin_page_overview()
    {
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    static function admin_page_order()
    {
        echo '<div class="wwpo__admin-filter"><input type="search" name="s" id="search-plugins" value="" class="wp-filter-search" placeholder="搜索插件…" aria-describedby="live-search-desc"></div>';

        // 显示表格内容
        echo WWPO_Table::result([], [
            'checkbox'     => true,
            'column'    => [
                'medium-name'  => '支付金额',
                'medium-value1' => '退款金额',
                'medium-value2' => '手续费',
                'value3' => '商户号',
                'value4' => '订单号',
                'medium-value5' => '支付方式',
                'medium-value6' => '支付状态',
                'medium-value7' => '回调状态',
                'value8' => '创建日期',
                'medium-value' => '操作'
            ]
        ]);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    static function admin_page_setting()
    {
        // 获取设置保存值
        $option_data = get_option(OPTION_SETTING_KEY);

        // 页面标签内容数组
        $formdata['wxpay'] = [
            'title'     =>  __('微信支付', 'wwpo'),
            'formdata'  => [
                'updated[mchid]'  => [
                    'title' => '直连商户号',
                    'field' =>  ['type' => 'text', 'value' => $option_data['mchid'] ?? '']
                ],
                'updated[apikeyid]'  => [
                    'title' => 'API 证书序列号',
                    'field' => ['type' => 'text', 'value' => $option_data['apikeyid'] ?? ''],
                    'desc'  => '微信支付后台「账户设置 - API安全」处申请'
                ],
                'updated[apikeysecret]'  => [
                    'title' => 'API 证书序内容',
                    'field' => ['type' => 'textarea', 'value' => $option_data['apikeysecret'] ?? '']
                ],
                'updated[apiv2secret]'   => [
                    'title' => 'APIv2密钥',
                    'field' => ['type' => 'text', 'value' => $option_data['apiv2secret'] ?? '']
                ],
                'updated[apiv3secret]' => [
                    'title' => 'APIv3密钥',
                    'field' => ['type' => 'text', 'value' => $option_data['apiv3secret'] ?? '']
                ]
            ]
        ];

        WWPO_Admin::settings('', '', $formdata);
    }
}
