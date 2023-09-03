<?php

/**
 * Undocumented function
 *
 * @param [type] $post_id
 * @param [type] $current_data
 * @return void
 */
function wwpo_contents_page_edit_form($post_id, $current_data)
{
    $current_type = $current_data['type'];

    if (empty($current_data)) {
        wp_die(__('没有找到相关内容', 'wwpo'));
    }

    echo '<form id="wwpo__admin-form" method="post" autocomplete="off">';
    echo WWPO_Form::hidden([
        'post_id'           => $post_id,
        'page_url'          => WWPO_Admin::page_url(),
        'post_data[type]'   => $current_type,
        'post_data[slug]'   => $current_data['slug']
    ]);

    call_user_func("wwpo_admin_display_content_{$current_type}", $current_data);

    //
    echo '<div class="submit">';
    echo WWPO_Button::submit('updatecontents');
    echo WWPO_Button::submit([
        'text'  => __('Delete'),
        'css'   => 'link-delete large ms-2',
        'value' => 'deletecontents'
    ]);
    echo '</div>';
    echo '</form>';
}

/**
 * 自定义内容更新操作函数
 *
 * @since 1.0.0
 * - 根据 post_id 判断是更新还是新建操作
 * - 新建内容自动生成 post_id，判断别名、类型是否为空，判断别名是否被占用
 * - 新建和更新操作都判断内容名称是否为空
 * - 新建内容写入 ID 字段内容 post_id，用于内容列表页链接引用
 * - 新建和更新内容数据以 post_id 为索引写入存储数组
 * - 新建内容成功转跳编辑页面
 */
function wwpo_admin_post_update_contents()
{
    $post_id        = $_POST['post_id'] ?? 0;
    $post_data      = $_POST['post_data'] ?? [];
    $option_data    = wwpo_get_option(WWPO_Custom::OPTION_CONTENTS_DATA, []);

    /** 判断自定义内容名称为空 */
    if (empty($post_data['title'])) {
        new WWPO_Error('message', 'not_null_title');
        exit;
    }

    /** 判断 $post_id 为空，自定义内容新增操作 */
    if (empty($post_id)) {

        // 判断内容别名
        if (empty($post_data['slug'])) {
            new WWPO_Error('message', 'not_null_slug');
            exit;
        }

        // 判断选择内容类型
        if (empty($post_data['type'])) {
            new WWPO_Error('message', 'not_null_type');
            exit;
        }

        // 设定自定义内容 ID，格式：{内容类型} - {内容别名}
        $post_id = sprintf('%s-%s', $post_data['type'], $post_data['slug']);

        // 判断内容别名是否存在
        if ($option_data && in_array($post_id, array_keys($option_data))) {
            new WWPO_Error('message', 'exists_content');
            exit;
        }
    }

    // 以 $post_id 为 KEY 写入数组
    $option_data[$post_id]          = $post_data;
    $option_data[$post_id]['ID']    = $post_id;

    // 更新到数据库
    wwpo_update_option(WWPO_Custom::OPTION_CONTENTS_DATA, $option_data);

    /** 判断 $post_id 为空，返回添加信息，转跳到编辑界面 */
    if (empty($_POST['post_id'])) {
        new WWPO_Error('message', 'success_added', [
            'post'      => $post_id,
            'action'    => 'edit'
        ]);
        exit;
    }

    // 返回更新成功信息
    new WWPO_Error('message', 'success_updated', [
        'post'      => $post_id,
        'action'    => 'edit'
    ]);
}
add_action('wwpo_post_admin_updatecontents', 'wwpo_admin_post_update_contents');

/**
 * 自定义内容 AJAX 删除操作函数
 *
 * @since 1.0.0
 * - 判断 post_id 的内容是否存在
 * - 注销 post_id 自定义内容
 * - 删除 post_id 自定义内容
 * - 更新删除后的自定义内容
 */
function wwpo_admin_post_delete_contents()
{
    $post_id        = $_POST['post_id'];
    $option_data    = wwpo_get_option(WWPO_Custom::OPTION_CONTENTS_DATA, []);

    if (empty($option_data[$post_id])) {
        new WWPO_Error('message', 'not_found_content', [
            'post'      => $post_id,
            'action'    => 'edit'
        ]);
        exit;
    }

    if ('posttype' == $option_data[$post_id]['type']) {
        unregister_post_type($option_data[$post_id]['slug']);
    }

    if ('taxonomy' == $option_data[$post_id]['type']) {
        unregister_taxonomy($option_data[$post_id]['slug']);
    }

    unset($option_data[$post_id]);
    wwpo_update_option(WWPO_Custom::OPTION_CONTENTS_DATA, $option_data);

    new WWPO_Error('message', 'success_deleted');
}
add_action('wwpo_post_admin_deletecontents', 'wwpo_admin_post_delete_contents');

/**
 * 数据库优化 AJAX 操作函数
 *
 * @since 1.0.0
 */
function wwpo_ajax_content_empty()
{
    delete_option(WWPO_Custom::OPTION_CONTENTS_DATA);
    echo new WWPO_Error('success', __('自定义内容设置已清空', 'wwpo'));
}
add_action('wwpo_ajax_admin_contentempty', 'wwpo_ajax_content_empty');
