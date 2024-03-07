<?php

/**
 * 分类目录自定义
 *
 * @since 1.0.0
 * @package Webian WP Mall
 */

/**
 * 自定义表格栏目名称设定函数
 *
 * @since 1.0.0
 * @param array $column 表格栏目名称内容数组
 */
function wwpo_wpmall_taxonomy_category_columns($column)
{
    // 设定分类法别名
    $taxonomy = $_GET['taxonomy'] ?? '';

    // 设定分类表格内容数组
    $column = [
        'cb'    => __('选择', 'wwpo'),
        'name'  => _x('Name', 'term name'),
        'thumb' => __('封面', 'wwpo')
    ];

    /**
     * 判断 parent 参数为空，则为主分类目录界面
     */
    if (empty($_GET['parent'])) {

        if ('product_category' == $taxonomy) {
            $column['small-brand']  = __('品牌数', 'wwpo');
        }

        $column['small-tags']   = __('标签数', 'wwpo');
        $column['small-subs']   = __('分类数', 'wwpo');
    }

    $column['posts']        = _x('Count', 'Number/count of items');
    $column['medium-sort']  = __('Order');

    return $column;
}
add_filter('manage_edit-product_category_columns', 'wwpo_wpmall_taxonomy_category_columns');
add_filter('manage_edit-template_category_columns', 'wwpo_wpmall_taxonomy_category_columns');

/**
 * 自定义分类目录显示内容函数
 *
 * @since 1.0.0
 * @param string    $content
 * @param string    $column_name
 * @param integer   $term_id
 */
function wwpo_wpmall_taxonomy_category_custom_column($content, $column, $term_id)
{
    $taxonomy       = $_GET['taxonomy'] ?? '';
    $termmeta       = get_term_meta($term_id);
    $thumb_id       = $termmeta['thumb_id'][0] ?? 0;
    $sort           = $termmeta['_wwpo_category_menu_order'][0] ?? 0;
    $total_brand    = $termmeta['_wwpo_total_product_brand'][0] ?? 0;
    $total_tags     = $termmeta['_wwpo_total_product_tags'][0] ?? 0;
    $total_term     = $termmeta['_wwpo_total_' . $taxonomy][0] ?? 0;
    $page_url       = admin_url('edit-tags.php');
    $page_url       = add_query_arg(['post_type' => 'product', 'category'  => $term_id], $page_url);

    switch ($column) {
            // 封面缩略图
        case 'thumb':
            if (empty($thumb_id)) {
                return $content;
            }

            return sprintf(
                '<div class="thumb-small rounded"><img src="%s" class="thumb"></div>',
                wp_get_attachment_image_url($thumb_id)
            );
            break;

            // 分类品牌数量
        case 'small-brand':

            if (empty($total_brand)) {
                return '—';
            }

            return sprintf(
                '<a href="%s" class="btn btn-outline-primary">%s</a>',
                add_query_arg('taxonomy', 'product_brand', $page_url),
                $total_brand
            );
            break;

            // 分类标签数量
        case 'small-tags':

            if (empty($total_tags)) {
                return '—';
            }

            return sprintf(
                '<a href="%s" class="btn btn-outline-primary">%s</a>',
                add_query_arg('taxonomy', 'product_tags', $page_url),
                $total_tags
            );
            break;

            // 二级目录数量
        case 'small-subs':

            if (empty($total_term)) {
                return '—';
            }

            return sprintf(
                '<a href="%s" class="btn btn-outline-primary">%s</a>',
                add_query_arg(['parent' => $term_id, 'orderby' => 'meta_value_num']),
                $total_term
            );
            break;

            // 分类排序
        case 'medium-sort':
            return sprintf('<div class="input-group input-group-sm"><span class="input-group-text lead">#</span><input type="text" name="term_ids" data-term="%s" class="form-control" value="%s"><span class="input-group-text dashicons-before dashicons-menu" style="cursor: move;"></span></div>', $term_id, $sort);
            break;

        default:
            break;
    }
}
add_filter('manage_product_category_custom_column', 'wwpo_wpmall_taxonomy_category_custom_column', 10, 3);
add_filter('manage_template_category_custom_column', 'wwpo_wpmall_taxonomy_category_custom_column', 10, 3);

/**
 * 自定义目录编辑表单函数
 *
 * @since 1.0.0
 * @param WP_Term $term
 */
function wwpo_wpmall_taxonomy_category_edit_fields($term)
{
    wp_enqueue_media();

    $termmeta   = get_term_meta($term->term_id);
    $thumb_id   = $termmeta['thumb_id'][0] ?? 0;
    $iconfont   = $termmeta['iconfont'][0] ?? '';
?>
    <tr class="form-field">
        <th scope="row">展示图片</th>
        <td>
            <input type="hidden" name="thumb_id" value="<?php echo $thumb_id; ?>">
            <p id="wwpo-thumb-uploader" class="mb-3">
                <button type="button" class="button" data-action="thumbuploader">选择</button>
            </p>
            <p class="description">首页和二级分类列表图片，建议尺寸：<code>200px * 200px</code></p>
            <?php
            if ($thumb_id) {
                printf(
                    '<figure id="wwpo-thumb-figure" class="figure m-0 w-50"><img src="%s" class="figure-img img-fluid rounded"></figure>',
                    wp_get_attachment_url($thumb_id)
                );
            }
            ?>
        </td>
    </tr>
    <?php
    // 判断是二级目录，不显示下面的选项
    if ($term->parent) {
        return;
    }
    ?>
    <tr class="form-field">
        <th scope="row">Iconfont</th>
        <td>
            <input type="text" name="iconfont" value="<?php echo $iconfont; ?>">
            <p class="description">分类目录列表字体图标</p>
        </td>
    </tr>
<?php
}
add_action('product_category_edit_form_fields', 'wwpo_wpmall_taxonomy_category_edit_fields');
add_action('template_category_edit_form_fields', 'wwpo_wpmall_taxonomy_category_edit_fields');
