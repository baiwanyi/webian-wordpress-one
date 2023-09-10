<?php


if (empty($args['menudata'])) {
    return;
}

$parent_id  = $args['hidden']['parent'];
$page_url   = $args['hidden']['page_url'];
$action     = $args['hidden']['post_action'];
$submenu    = $args['menudata'][$parent_id]['submenu'] ?? [];

if ($submenu) :
    $submenu = wp_list_sort($submenu, 'sort', 'DESC', true);
?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th class="manage-column">菜单名称</th>
                <th class="manage-column">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            /**
             *
             */
            foreach ($submenu as $submenu_key => $submenu_val) :
                $page_url = add_query_arg('post', $parent_id, $page_url);
                $page_url = add_query_arg('menu', $submenu_key, $page_url);
                $page_url = add_query_arg('action', 'edit', $page_url);
            ?>
                <tr>
                    <td class="column-title column-primary"><strong><?php echo $submenu_val['name']; ?></strong></td>
                    <td><a href="<?php echo $page_url; ?>" class="btn btn-outline-primary btn-sm">编辑</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php
endif;


if ('edit' == $action && 5 > count($submenu)) : ?>
    <div id="wwpo__wechat-menu__add" class="d-flex justify-content-center pt-3">
        <p class="mr-2">
            <a href="<?php echo add_query_arg(['menu' => 'new', 'action' => 'new'], $page_url); ?>" class="btn btn-outline-dark">添加子菜单</a>
        <p>
    </div>
    <!-- /#wwpo__wechat-menu__add -->
<?php
endif;
