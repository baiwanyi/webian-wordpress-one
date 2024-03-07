<?php

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 * @param array $menus 菜单内容数组
 */
function wwpo_wpmall_product_content_menu($menus)
{
    $menus['content'] = [
        'parent'        => 'edit.php?post_type=product',
        'menu_title'    => __('产品详情', 'wpmall'),
        'role'          => 'edit_posts',
        'post_new'      => true
    ];

    return $menus;
}
add_filter('wwpo_menus', 'wwpo_wpmall_product_content_menu');

/**
 * 客户管理显示页面函数
 *
 * @since 1.0.0
 * @param string $page_action
 */
function wwpo_wpmall_product_content_admin_display($page_action)
{
    if (empty($page_action)) {
        wwpo_wpmall_product_content_page_card();
        return;
    }

    wwpo_wpmall_product_content_page_display();
}
add_action('wwpo_admin_display_content', 'wwpo_wpmall_product_content_admin_display');

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_wpmall_product_content_page_card()
{
    echo WWPO_Table::select(WWPO_SQL_PR_CONTENT, [], [
        'index'     => true,
        'column'    => [
            'title'             => __('Title'),
            'medium-category'   => __('产品目录'),
            'medium-brand'      => __('品牌目录'),
            'date'              => __('Date')
        ]
    ]);
}

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_wpmall_product_content_page_display()
{
    $post_id    = WWPO_Admin::post_id(0, true);
    $post_data  = [
        'post_title'    => '',
        'post_content'  => '',
        'term_id'       => 0,
        'brand_id'      => 0
    ];

    if ($post_id) {
        $post_data = wwpo_get_row(WWPO_SQL_PR_CONTENT, 'ID', $post_id, null, ARRAY_A);
    }
?>
    <form id="poststuff" method="POST" autocomplete="off">
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="postbox-container-1" class="postbox-container">
                <div id="wwpo-mall-order-core-category" class="postbox">
                    <div class="postbox-header">
                        <h2>产品目录</h2>
                    </div>
                    <div class="inside">
                        <?php wwpo_metabox_product_content_category($post_data); ?>
                    </div>
                </div>
            </div>
            <!-- /#postbox-container-1 -->

            <div id="postbox-container-2" class="postbox-container">
                <div id="titlediv" class="mb-4">
                    <input type="text" name="post_title" id="title" size="30" value="<?php echo $post_data['post_title']; ?>" placeholder="添加标题">
                </div>
                <?php wp_editor($post_data['post_content'], 'post_content'); ?>
            </div>
            <!-- /#postbox-container-2 -->

            <div class="clear"></div>
        </div>
        <!-- /#post-body -->

        <div class="submit">
            <button type="submit" name="submit" class="button button-primary button-large" value="updatecontent">保存更改</button>
        </div>
    </form>
<?php
}

/**
 * 后台产品分类显示函数
 *
 * @since 1.0.0
 * @param WP_Post $post
 */
function wwpo_metabox_product_content_category($data)
{
    // 初始化分类数组内容
    $category_data = [];

    // 获取所有产品分类目录内容
    $product_category = get_terms([
        'taxonomy'      => 'product_category',
        'orderby'       => 'meta_value_num',
        'meta_key'      => '_wwpo_category_menu_order',
        'hide_empty'    => false,
        'update_term_meta_cache' => false
    ]);

    // 判断分类目录为空，显示新建分类按钮
    if (empty($product_category)) {
        return;
    }

    if ($data['term_id']) {
        $post_category = get_term($data['term_id'], 'product_category');
    }

    if ($data['brand_id']) {
        $post_brand = get_term($data['brand_id'], 'product_brand');
    }

    if (isset($post_category)) {
        $category_data['current']['parent'][]   = $post_category->parent;
        $category_data['current']['category'][$post_category->term_id] = [
            'name'      => $post_category->name,
            'parent'    => $post_category->parent
        ];
    }

    if (isset($post_brand)) {
        $category_data['current']['brand']      = [$post_brand->term_id => $post_brand->name];
    }

    // 设定只有父级（parent 为 0）的分类目录
    $parent_category = wp_list_filter($product_category, ['parent' => 0]);
    $category_data['parent'] = array_column($parent_category, 'name', 'term_id');

    // 遍历父级分类目录内容数组
    foreach ($parent_category as $parent) {

        // 获取当前父级目录（parent 为当前 term_id）的分类目录
        $submenu_category = wp_list_filter($product_category, ['parent' => $parent->term_id]);

        // 获取当前 term_id 下所有的品牌内容数组
        $parent_brand = get_terms([
            'taxonomy'      => 'product_brand',
            'meta_key'      => 'parent_id',
            'meta_value'    => $parent->term_id,
            'hide_empty'    => false,
            'update_term_meta_cache' => false
        ]);

        // 设定分类数组内容
        $category_data['category'][] = [
            'id'        => $parent->term_id,
            'slug'      => $parent->slug,
            'title'     => $parent->name,
            'submenu'   => array_column($submenu_category, 'name', 'term_id'),
            'brand'     => array_column($parent_brand, 'name', 'term_id')
        ];
    }

    wwpo_wpmall_get_category('category', '产品目录', $category_data);
    wwpo_wpmall_get_category('brand', '品牌', $category_data, true);

    // 内容保存表单
    printf('<textarea id="%s" hidden>%s</textarea>', 'wpmall-product-category-data', wwpo_json_encode($category_data));
}

/**
 * 自定义内容表格列输出函数
 *
 * @since 1.0.0
 * @param array     $data           表格列值
 * @param string    $column_name    表格列名称
 */
function wwpo_wpmall_product_table_column($data, $column_name)
{
    switch ($column_name) {
        case 'title':
            echo WWPO_Admin::title($data['ID'], $data['post_title']);
            break;

        case 'medium-category':
            echo get_term_field('name', $data['term_id'], 'product_category');
            break;

        case 'medium-brand':
            echo get_term_field('name', $data['brand_id'], 'product_brand');
            break;

        default:
            break;
    }
}
add_action('wwpo_table_content_custom_column', 'wwpo_wpmall_product_table_column', 10, 2);

/**
 * 设置页面消息函数
 *
 * @since 1.0.0
 * @param array $message
 */
function wwpo_content_message($message)
{
    $message['content'] = [
        'success_added'     => ['updated'   => __('产品详情添加成功', 'wpmall')],
        'success_updated'   => ['updated'   => __('产品详情已更新', 'wpmall')],
        'fail_added'        => ['error' => __('产品详情添加失败', 'wpmall')]
    ];

    return $message;
}
add_filter('wwpo_admin_message', 'wwpo_content_message');

/**
 * 客户内容更新操作函数
 *
 * @since 1.0.0
 */
function wwpo_content_post_update()
{
    $post_id            = $_POST['post_id'] ?? 0;
    $product_category   = $_POST['wpmall-category'] ?? [];
    $product_brand      = $_POST['wpmall-brand'] ?? [];

    $updated    = [
        'term_id'       => $product_category[0] ?? 0,
        'brand_id'      => $product_brand[0] ?? 0,
        'post_name'     => $product_category[0] . $product_brand[0],
        'post_title'    => $_POST['post_title'],
        'post_content'  => stripcslashes($_POST['post_content'])
    ];

    // 判断添加或编辑
    if (empty($post_id)) {
        $url_query = ['post'  => 'new'];
    } else {
        $url_query = ['post'  => $post_id, 'action' => 'edit'];
    }

    /** 自定义内容新增操作 */
    if (empty($post_id)) {

        // 设定添加参数
        $updated['post_author'] = get_current_user_id();
        $updated['time_post']   = NOW_TIME;

        // 写入数据
        $post_id = wwpo_insert_post(WWPO_SQL_PR_CONTENT, $updated);

        // 判断写入失败
        if (empty($post_id)) {
            new WWPO_Error('message', 'fail_added', $url_query);
            return;
        }

        // 设定日志
        wwpo_logs('admin:post:product:addedcontent:' . $post_id);

        // 写入成功转跳 URL
        new WWPO_Error('message', 'success_added', [
            'post'      => $post_id,
            'action'    => 'edit'
        ]);
        return;
    }

    // 以 $post_id 为 KEY 写入数组
    $updated['time_modified'] = NOW_TIME;

    // 更新到数据库
    wwpo_update_post(WWPO_SQL_PR_CONTENT, $updated, ['ID' => $post_id]);

    // 设定日志
    wwpo_logs('admin:post:product:updatecontent:' . $post_id);

    // 返回更新成功信息
    new WWPO_Error('message', 'success_updated', $url_query);
}
add_action('wwpo_post_admin_updatecontent', 'wwpo_content_post_update');
