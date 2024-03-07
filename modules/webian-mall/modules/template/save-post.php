<?php

/**
 * 模板数据保存和列表生成格式化
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/template
 */

 /**
 * 模板提交数据库附加操作函数
 *
 * @since 1.0.0
 * @param integer $post_id
 * @param WP_Post $post
 * @param boolean $update
 */
function wwpo_wpmall_template_post_updated($post_id, $post, $update)
{
    /** 判断页面包含 action 参数，保证操作只在 edit 页面下执行，禁止在列表页「快速编辑」执行 */
    if (empty($_POST['action'])) {
        return $post_id;
    }

    /** 判断模板编辑权限 */
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    // 模板更新内容数组
    $updated = [
        'ID' => $post_id
    ];

    // 设定模板等级和专题
    if ($_POST['template_tags']) {
        wp_set_object_terms($post_id, $_POST['template_tags'], 'template_tags');
    }

    if ($_POST['template_category']) {
        wp_set_object_terms($post_id, $_POST['template_category'], 'template_category');
    }

    /** 新模板修改别名 */
    if (!$update || empty($_POST['post_name'])) {
        $updated['post_name'] = wwpo_unique($post_id, 10);
    }

    /** 更新模板内容 */
    if (!wp_is_post_revision($post_id)) {
        remove_action('save_post_template', 'wwpo_wpmall_template_post_updated');
        wp_update_post($updated);
        add_action('save_post_template', 'wwpo_wpmall_template_post_updated');
    }
}
add_action('save_post_template', 'wwpo_wpmall_template_post_updated', 10, 3);
