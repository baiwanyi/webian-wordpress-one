<?php

/**
 * 模板编辑页面自定义
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/template
 */

/**
 * 添加元数据操作框函数
 *
 * @since 1.0.0
 */
function wwpo_wpmall_template_create_metabox($post)
{
    add_meta_box('wwpo-wpmall-template-tags', __('Tags'), 'wwpo_wpmall_template_metabox_tags', 'template', 'advanced', 'low', $post);
    add_meta_box('wwpo-wpmall-template-category', __('Categories'), 'wwpo_wpmall_template_metabox_category', 'template', 'advanced', 'core', $post);
}
add_action('add_meta_boxes', 'wwpo_wpmall_template_create_metabox');

/**
 * 模板专题编辑框函数
 *
 * @since 1.0.0
 * @param WP_Post $post
 */
function wwpo_wpmall_template_metabox_category($post)
{
    // 获取所有模板专题内容
    $all_category = get_terms([
        'taxonomy'      => 'template_category',
        'hide_empty'    => false,
        'update_term_meta_cache' => false
    ]);

    if (empty($all_category)) {
        return;
    }

    // 获取当前模板专题内容
    $post_category = wp_get_post_terms($post->ID, 'template_category');
    $post_category = array_column($post_category, 'slug');

    $parent_category = wp_list_filter($all_category, ['parent' => 0]);

    /** 遍历所有模板专题内容 */
    foreach ($parent_category as $category) {

        $submenu_category = wp_list_filter($all_category, ['parent' => $category->term_id]);

        printf('<p class="lead">%s</p>',  $category->name);

        if ($submenu_category) {
            foreach ($submenu_category as $submenu) {

                // 判断模板专题 term_id 存在于当前模板专题内容数组中
                if (in_array($submenu->slug, $post_category)) {
                    $checked = 1;
                } else {
                    $checked = 0;
                }

                // 显示专题按钮
                printf('<span class="d-inline-block me-2 mb-2"><input type="checkbox" class="btn-check" name="template_category[]" id="btn-category-%1$s" value="%1$s" %3$s><label class="btn btn-outline-primary" for="btn-category-%1$s">%2$s</label></span>', $submenu->slug, $submenu->name, checked($checked, 1, false));
            }
        }
    }
}

/**
 * 模板专题编辑框函数
 *
 * @since 1.0.0
 * @param WP_Post $post
 */
function wwpo_wpmall_template_metabox_tags($post)
{
    // 获取所有模板专题内容
    $all_topic = get_terms([
        'taxonomy'      => 'template_tags',
        'hide_empty'    => false,
        'update_term_meta_cache' => false
    ]);

    if (empty($all_topic)) {
        return;
    }

    // 获取当前模板专题内容
    $post_topic = wp_get_post_terms($post->ID, 'template_tags');
    $post_topic = array_column($post_topic, 'slug');

    /** 遍历所有模板专题内容 */
    foreach ($all_topic as $topic) {

        // 判断模板专题 term_id 存在于当前模板专题内容数组中
        if (in_array($topic->slug, $post_topic)) {
            $checked = 1;
        } else {
            $checked = 0;
        }

        // 显示专题按钮
        printf('<span class="d-inline-block me-2 mb-2"><input type="checkbox" class="btn-check" name="template_tags[]" id="btn-check-%1$s" value="%1$s" %3$s><label class="btn btn-outline-primary" for="btn-check-%1$s">%2$s</label></span>', $topic->slug, $topic->name, checked($checked, 1, false));
    }
}
