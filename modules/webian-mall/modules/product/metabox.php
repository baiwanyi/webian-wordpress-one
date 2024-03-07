<?php

/**
 * 产品编辑页面自定义
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/product
 */

/**
 * 添加元数据操作框函数
 *
 * @since 1.0.0
 */
function wwpo_wpmall_product_create_metabox($post)
{
    // 加载媒体框架
    wp_enqueue_media();

    // 获取 products 表数据内容
    $product = wwpo_get_row(WWPO_SQL_PRODUCT, 'post_id', $post->ID, null, ARRAY_A);

    if (empty($product)) {
        $product['product_id'] = wwpo_insert_post(WWPO_SQL_PRODUCT, ['post_id' => $post->ID]);
    }

    foreach ([
        'excerpteditor' => [
            'title'     => __('描述', 'wpmall'),
            'context'   => 'advanced'
        ],
        'image' => [
            'title'     => __('素材', 'wpmall'),
            'context'   => 'advanced'
        ],
        'modal' => [
            'title'     => __('规格', 'wpmall'),
            'context'   => 'advanced'
        ],
        'price' => [
            'title'     => __('价格库存', 'wpmall'),
            'context'   => 'advanced'
        ],
        'category' => [
            'title'     => __('Categories'),
            'context'   => 'side'
        ],
        'property' => [
            'title'     => __('属性', 'wpmall'),
            'context'   => 'side'
        ]
    ] as $meta_key => $meta_val) {
        add_meta_box(
            'wpmall-product-' . $meta_key,
            $meta_val['title'],
            'wwpo_metabox_product_' . $meta_key,
            'product',
            $meta_val['context'],
            'core',
            $product
        );
    }
}
add_action('add_meta_boxes_product', 'wwpo_wpmall_product_create_metabox');

/**
 * 产品描述编辑框函数
 *
 * @since 1.0.0
 * @param WP_Post $post
 */
function wwpo_metabox_product_excerpteditor($post)
{
    wp_editor($post->post_excerpt, 'productexcerpt', [
        'textarea_name' => 'excerpt',
        'textarea_rows' => 5,
        'wpautop'       => false,
        'media_buttons' => false,
        'tinymce'       => false,
        'quicktags'     => false
    ]);
}

/**
 * 产品图片素材编辑框函数
 *
 * @since 1.0.0
 * @param WP_Post $post
 */
function wwpo_metabox_product_image($post)
{
?>
    <div class="btn-toolbar my-3" role="toolbar">
        <div class="btn-group me-3">
            <button type="button" class="button" data-action="mediauploader">添加素材</button>
        </div>
    </div>
    <div id="wpmall-media-wrapper" class="row mb-2 g-2">
        <?php wwpo_wpmall_product_display_image($post->ID); ?>
    </div>
    <p class="lead">按住<kbd>CTRL</kbd>进行图片多选，拖动图片以调整显示顺序。</p>
<?php
}

/**
 * 产品属性编辑框函数
 *
 * @since 1.0.0
 * @param WP_Post $post
 */
function wwpo_metabox_product_property($post, $data)
{
    // 设定产品展示价格，数据库保存单位为「分」，显示需要除以 100 以「元」的格式显示
    $price_sale = $data['args']['price_sale'] ?? '';
    if ($price_sale) {
        $price_sale = $price_sale / 100;
    }

    $price_vip = $data['args']['price_vip'] ?? '';
    if ($price_vip) {
        $price_vip = $price_vip / 100;
    }

    $price_buy = $data['args']['price_buy'] ?? '';
    if ($price_buy) {
        $price_buy = $price_buy / 100;
    }

    $property_form['hidden'] = [
        'product_id' => $data['args']['product_id'] ?? 0
    ];

    // 设定产品属性显示表单内容数组
    $property_form['formdata'] = [
        'wpmall-barcode' => [
            'title' => '产品条码',
            'field' => ['type' => 'text', 'value' => $data['args']['barcode'] ?? ''],
            'after' => '<button type="button" class="button" data-action="barcodesearch">查询</button>'
        ],
        'wpmall-promo' => [
            'title' => '产品卖点',
            'field' => ['type' => 'text', 'value' => $data['args']['promo'] ?? '']
        ],
        'wpmall-order' => [
            'title' => '推荐排序',
            'field' => [
                'type'      => 'select',
                'css'       => 'w-100',
                'option'    => wwpo_wpmall_product_menu_order(),
                'selected'  => $post->menu_order
            ]
        ],
        'wpmall-buy' => [
            'title' => '出厂价',
            'field' => ['type' => 'text', 'value' => $price_buy]
        ],
        'wpmall-sale' => [
            'title' => '展示价',
            'field' => ['type' => 'text', 'value' => $price_sale]
        ],
        'wpmall-vip' => [
            'title' => '会员价',
            'field' => ['type' => 'text', 'value' => $price_vip]
        ]
    ];

    // 显示表单内容
    echo WWPO_Form::list($property_form);
}

/**
 * 产品规格类型编辑框函数
 *
 * @since 1.0.0
 * @param WP_Post $post
 * @param array $data
 */
function wwpo_metabox_product_modal($post, $data)
{
?>
    <textarea id="wpmall-modal-data" name="wpmall-modal" hidden><?php echo $data['args']['product_modal'] ?? ''; ?></textarea>
    <div class="hstack gap-2 my-3">
        <button type="button" class="button" data-action="wpmallmodal" value="create">添加规格类型</button>
        <div class="vr"></div>
        <div>
            <div class="input-group">
                <select class="form-select" id="choosesize">
                    <option value="eng">英文</option>
                    <option value="num">数字</option>
                </select>
                <button type="button" class="button" data-action="modalpost" value="size">一键尺码规格</button>
            </div>
        </div>
    </div>
    <div id="wpmall-modal-wrapper"></div>
    <p class="text-muted">拖动图标以调整显示顺序。最多添加2个规格类型。</p>
<?php
}

/**
 * 产品库存价格编辑框函数
 *
 * @since 1.0.0
 * @param WP_Post $post
 * @param array $data
 */
function wwpo_metabox_product_price($post, $data)
{
    // 初始化库存价格内容
    $product_data = [];
?>
    <div class="hstack gap-2 my-3">
        <button type="button" class="button" data-action="syncsku" value="price">同步价格</button>
        <button type="button" class="button" data-action="syncsku" value="barcode">同步编码</button>
        <div class="input-group">
            <input type="text" id="wpmall-stock" placeholder="请输入库存数量">
            <button type="button" class="button" data-action="syncsku" value="stock">同步库存</button>
        </div>
    </div>
    <div id="wpmall-price-wrapper" class="table-responsive"></div>
<?php

    if (empty($data['args']['product_id'])) {
        return;
    }

    // 获取产品库存价格内容
    $product_sku = wwpo_get_post(WWPO_SQL_PR_SKU, 'product_id', $data['args']['product_id']);
    if ($product_sku) {
        foreach ($product_sku as $sku) {

            // 设定产品展示价格，数据库保存单位为「分」，显示需要除以 100 以「元」的格式显示
            $price_sale = $sku->price_sale ?? 0;
            if ($price_sale) {
                $price_sale = $price_sale / 100;
            }

            $price_vip = $sku->price_vip ?? 0;
            if ($price_vip) {
                $price_vip = $price_vip / 100;
            }

            $price_buy = $sku->price_buy ?? 0;
            if ($price_buy) {
                $price_buy = $price_buy / 100;
            }

            $product_data[$sku->sku_name] = [
                'code'  => $sku->sku_code,
                'buy'   => $price_buy,
                'vip'   => $price_vip,
                'sale'  => $price_sale,
                'stock' => $sku->num_stock
            ];
        }
    }

    printf('<textarea id="wpmall-price-data" name="wpmall-price" hidden>%s</textarea>',  wwpo_json_encode($product_data));
}

/**
 * 后台作品列表显示函数
 *
 * @since 1.0.0
 * @param integer $post_id
 */
function wwpo_wpmall_product_display_image($post_id)
{
    // 获取作品媒体文件 IDs 列表内容
    $media_ids = get_children([
        'post_parent'   => $post_id,
        'post_type'     => 'attachment',
        'orderby'       => 'menu_order',
        'fields'        => 'ids'
    ]);

    // 判断为空
    if (empty($media_ids)) {
        return;
    }

    // 遍历作品媒体文件 IDs 列表内容数组
    foreach ($media_ids as $media_id) {

        // 获取图片文件地址
        $attachment_url = wp_get_attachment_image_url($media_id, 'large');

        if (empty($attachment_url)) {
            continue;
        }

        printf('<div id="post-%2$d" class="mt-2 col-6 col-lg-3"><input type="hidden" name="%1$s" value="%2$d"><div class="card m-0 p-0 overflow-hidden"><div class="ratio ratio-1x1" data-action="mediaview"><img src="%3$s" class="thumb"></div><div class="card-body text-end"><button type="button" data-action="mediaremove" class="btn btn-danger btn-sm lh-1" value="%2$d"><span class="dashicons dashicons-no"></span></button></div></div></div>', 'wpmall-media[]', $media_id, $attachment_url);
    }
}

/**
 * 后台产品分类显示函数
 *
 * @since 1.0.0
 * @param WP_Post $post
 */
function wwpo_metabox_product_category($post)
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
        printf('<a href="%s" target="_blank" class="button mt-2">新建分类</a>', admin_url('edit-tags.php?taxonomy=product_category&post_type=product'));
        return;
    }

    // 获取所有作品等级标签内容
    $product_tags = get_terms([
        'taxonomy'      => 'product_tags',
        'meta_key'      => 'parent_id',
        'meta_value'    => 0,
        'hide_empty'    => false,
        'update_term_meta_cache' => false
    ]);

    if ($product_tags) {
        $category_data['tags'] = array_column($product_tags, 'name', 'slug');
    }

    // 设定只有父级（parent 为 0）的分类目录
    $parent_category = wp_list_filter($product_category, ['parent' => 0]);
    $category_data['parent'] = array_column($parent_category, 'name', 'term_id');

    // 获取当前产品分类目录
    $post_category      = wp_get_post_terms($post->ID, 'product_category');
    $post_tags          = wp_get_post_terms($post->ID, 'product_tags');
    $post_brand         = wp_get_post_terms($post->ID, 'product_brand');

    //
    $category_data['current']['parent']     = array_column($post_category, 'parent');
    $category_data['current']['tags']       = array_column($post_tags, 'name', 'slug');
    $category_data['current']['brand']      = array_column($post_brand, 'name', 'slug');

    if ($post_category) {
        foreach ($post_category as $category) {
            $category_data['current']['category'][$category->term_id] = [
                'name'      => $category->name,
                'parent'    => $category->parent
            ];
        }
    }

    // 遍历父级分类目录内容数组
    foreach ($parent_category as $parent) {

        // 获取当前父级目录（parent 为当前 term_id）的分类目录
        $submenu_category = wp_list_filter($product_category, ['parent' => $parent->term_id]);

        // 获取当前 term_id 下所有的标签内容数组
        $parent_tags = get_terms([
            'taxonomy'      => 'product_tags',
            'meta_key'      => 'parent_id',
            'meta_value'    => $parent->term_id,
            'hide_empty'    => false,
            'update_term_meta_cache' => false
        ]);

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
            'tags'      => array_column($parent_tags, 'name', 'slug'),
            'brand'     => array_column($parent_brand, 'name', 'slug')
        ];
    }

    wwpo_wpmall_get_category('category', '产品目录', $category_data);
    wwpo_wpmall_get_category('brand', '品牌', $category_data, true);
    wwpo_wpmall_get_category('tags', '标签', $category_data, true);

    // 内容保存表单
    printf('<textarea id="%s" hidden>%s</textarea>', 'wpmall-product-category-data', wwpo_json_encode($category_data));
}

/**
 * 产品条码搜索 AJAX 操作函数
 *
 * @since 1.0.0
 */
function wwpo_ajax_wpmall_search_barcode()
{
    if (empty($_POST['barcode'])) {
        echo wwpo_json_send(['code' => 'invalid', 'css' => 'danger', 'message' => '产品条码不能为空。']);
        exit;
    }

    $product_id = wwpo_get_post(WWPO_SQL_PRODUCT, 'barcode', $_POST['barcode'], 'product_id');

    if ($product_id && $product_id != $_POST['product_id']) {
        echo wwpo_json_send(['code' => 'invalid', 'css' => 'danger', 'message' => '产品条码已存在。']);
        exit;
    }

    echo wwpo_json_send(['code' => 'valid', 'css' => 'success', 'message' => '产品条码可用。']);
    exit;
}
add_action('wp_ajax_barcodesearch', 'wwpo_ajax_wpmall_search_barcode');

/**
 * Undocumented function
 *
 * @param [type] $slug
 * @param [type] $title
 * @param [type] $category
 * @return void
 */
function wwpo_wpmall_get_category($slug, $title, $array_category, $tags = false)
{
    printf('<div id="webui-select-menu-%s">', $slug);
    echo '<div class="webui-select-menu">';
    echo '<div class="webui-select-menu__list">';

    if ($tags) {
        wwpo_wpmall_get_tags_option($slug, $array_category);
    } else {
        wwpo_wpmall_get_category_option($slug, $array_category);
    }

    echo '</div>';
    printf('<span>%s</span>', $title);
    echo '<div data-action="selectmenu" class="button button-small dashicons-before dashicons-arrow-down-alt2"></div>';
    echo '</div>';
    printf('<div id="webui-select-menu-%s-content" class="webui-select-category">', $slug);

    if (isset($array_category['current'][$slug])) {
        foreach ($array_category['current'][$slug] as $current_key => $current_name) {

            if (is_array($current_name)) {
                $parent_name = $array_category['parent'][$current_name['parent']] ?? '';

                if ($parent_name) {
                    $current_name = $parent_name . ' - ' . $current_name['name'];
                } else {
                    $current_name = $current_name['name'];
                }
            }

            printf('<button type="button" class="button button-small" data-type="%s" data-action="removeselectitem" value="%s"><span class="webui-select-menu__text">%s</span><span class="dashicons-before dashicons-no-alt"></span></button>', $slug, $current_key, $current_name);
        }
    }

    echo '</div>';
    echo '</div>';
}

/**
 * Undocumented function
 *
 * @param [type] $slug
 * @param [type] $data
 * @return void
 */
function wwpo_wpmall_get_category_option($slug, $data)
{
    foreach ($data['category'] as $category_val) {
        echo '<div class="webui-select-menu__option">';

        if (empty($category_val['submenu'])) {

            $current = $data['current']['category'] ?? [];
            $checked = array_key_exists($category_val['id'], $current) ? 1 : 0;

            printf(
                '<label class="webui-select-menu__label" for="webui-checkbox-%2$s">%3$s</label><input type="checkbox" name="wpmall-%1$s[]" value="%2$s" data-parent="%1$s" class="webui-select-menu__checkbox" id="webui-checkbox-%2$s" %4$s>',
                $slug,
                $category_val['id'],
                $category_val['title'],
                checked($checked, 1, false)
            );
        }
        //
        else {
            printf('<h5 class="webui-select-menu__title">%s</h5>', $category_val['title']);

            foreach ($category_val['submenu'] as $submenu_id => $submenu_title) {

                $current = $data['current']['category'] ?? [];
                $checked = array_key_exists($submenu_id, $current) ? 1 : 0;

                printf(
                    '<div class="webui-select-menu__option"><label class="webui-select-menu__label" for="webui-checkbox-%2$s">%3$s</label><input type="checkbox" name="wpmall-%1$s[]" value="%2$s"  data-parent="%4$s" class="webui-select-menu__checkbox" id="webui-checkbox-%2$s" %5$s></div>',
                    $slug,
                    $submenu_id,
                    $submenu_title,
                    $category_val['id'],
                    checked($checked, 1, false)
                );
            }
        }

        echo '</div>';
    }
}

/**
 * Undocumented function
 *
 * @param [type] $slug
 * @param [type] $data
 * @return void
 */
function wwpo_wpmall_get_tags_option($slug, $data)
{
    $array_tags = [];
    $array_current = [];

    if ('tags' == $slug) {
        $array_tags = $data['tags'] ?? [];
    }

    foreach ($data['category'] as $category) {

        $current = $data['current']['parent'] ?? [];

        if (!in_array($category['id'], $current)) {
            continue;
        }

        if ('tags' == $slug) {
            $array_current = $data['current']['tags'] ?? [];

            if ($category['tags']) {
                foreach ($category['tags'] as $category_tag_id => $category_tag_name) {
                    $array_tags[$category_tag_id] = $category_tag_name;
                }
            }
        }

        if ('brand' == $slug) {
            $array_tags = $category['brand'] ?? [];
            $array_current = $data['current']['brand'] ?? [];
        }
    }

    if (empty($array_tags)) {
        return;
    }

    foreach ($array_tags as $tag_slug => $tag_name) {
        $checked = array_key_exists($tag_slug, $array_current) ? 1 : 0;
        printf(
            '<div class="webui-select-menu__option"><label class="webui-select-menu__label" for="webui-checkbox-%2$s">%3$s</label><input type="checkbox" name="wpmall-%1$s[]" value="%2$s" class="webui-select-menu__checkbox" id="webui-checkbox-%2$s" %4$s></div>',
            $slug,
            $tag_slug,
            $tag_name,
            checked($checked, 1, false)
        );
    }
}
