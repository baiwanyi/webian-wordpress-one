<?php

$menu_id    = $args['hidden']['menu_id'];
$parent_id  = $args['hidden']['parent'];
$page_url   = $args['hidden']['page_url'];
$action     = $args['hidden']['post_action'];

$formdata = [
    'title'     => '新建菜单',
    'hidden'    => $args['hidden'],
    'formdata'  => [
        'menu[name]'    => [
            'title' => '菜单标题',
            'desc'  => '仅支持中英文和数字，主菜单标题字数不超过8个汉字或16个字母，子菜单标题字数不超过4个汉字或8个字母。',
            'field' => ['type' => 'text']
        ],
        'menu[sort]'    => [
            'title' => '排序',
            'field' => ['type' => 'number']
        ]
    ]
];

if ('edit' == $args['hidden']['post_action']) {

    if ($menu_id == $parent_id) {
        $current_menu = $args['menudata'][$menu_id];
    } else {
        $current_menu = $args['menudata'][$parent_id]['submenu'][$menu_id];
    }

    if (empty($args['menudata'][$menu_id]['submenu'])) {
        $formdata['formdata']['menu[type]'] = [
            'title' => '菜单事件',
            'field' => [
                'type'          => 'select',
                'show_option_all' => '选择事件类型',
                'option'        => [
                    'click' => '点击事件',
                    'view'  => '访问事件'
                ],
                'selected' => $current_menu['type'] ?? ''
            ]
        ];

        $formdata['formdata']['menu[content]'] = [
            'title' => '菜单内容',
            'field' => ['type' => 'text', 'value' => $current_menu['content'] ?? ''],
            'desc'  => '点击事件：用于消息接口推送 KEY 值，不超过 <code>128</code> 字节。</br>访问事件：用户点击菜单打开链接，不超过 <code>1024</code> 字节。'
        ];
    }

    $formdata['title'] = '编辑菜单';
    $formdata['formdata']['menu[name]']['field']['value']   = $current_menu['name'];
    $formdata['formdata']['menu[sort]']['field']['value']   = $current_menu['sort'];
}

echo WWPO_Form::list($formdata);
echo '<div class="submit">';
echo WWPO_Form::submit([
    'css'   => 'btn btn-primary',
    'value' => 'wechatmenuupdate'
], false);

if ('edit' == $args['hidden']['post_action'] && empty($args['menudata'][$menu_id]['submenu'])) {
    echo WWPO_Form::submit([
        'text'  => __('Delete'),
        'css'   => 'btn btn-outline-danger ms-1',
        'value' => 'wechatmenudelete'
    ], false);
}
echo '</div>';
