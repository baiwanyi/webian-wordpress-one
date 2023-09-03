<?php
echo '<div class="menu">';

/** 判断主菜单个数小于3个时，显示【添加菜单】按钮 */
if (3 > count($args['menudata'])) {
    $args['menudata']['new'] = ['name' => '添加菜单', 'sort' => -1];
}

$args['menudata'] = wp_list_sort($args['menudata'], 'sort', 'DESC', true);

/**
 *
 */
foreach ($args['menudata'] as $menu_key => $menu_val) {

    //
    $active     = ($args['hidden']['parent'] == $menu_key && 'new' != $menu_key) ? 'hover' : 'parent';

    //
    $page_url   = remove_query_arg('menu', $args['hidden']['page_url']);
    $page_url   = add_query_arg('post', $menu_key, $page_url);

    if ('new' != $menu_key) {
        $page_url   = add_query_arg('action', 'edit', $page_url);
    }

    printf('<a href="%s" class="%s">%s</a>', esc_url($page_url), $active, $menu_val['name']);
}

echo '</div>';
