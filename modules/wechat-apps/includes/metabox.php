<?php

/**
 * 小程序编辑页面自定义
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */

/** 定义元数据名称 */
define('WWPO_META_PAGE_WEAPP_KEY', '_wppo_weapp_page');

/**
 * 使用经典编辑器
 *
 * @since 1.0.0
 */


/**
 * 添加元数据操作框函数
 *
 * @since 1.0.0
 */
function wwpo_wechat_wp_create_metabox()
{
    add_meta_box('wwpo-wechat-page-weapp-metabox', __('小程序选项', 'wwpo'), 'wwpo_wechat_metabox_page_weapp', 'page', 'side');
}
add_action('add_meta_boxes', 'wwpo_wechat_wp_create_metabox');

/**
 * 小程序显示页面选择位置函数
 *
 * @since 1.0.0
 * @param WP_Post $post
 */
function wwpo_wechat_metabox_page_weapp($post)
{
    $page_weapp_form['formdata']['wwpo-weapp-page']['field'] = [
        'type'      => 'select',
        'css'       => 'w-100',
        'option'    => [
            'my'    => '个人中心',
            'item'  => '产品页面'
        ],
        'selected'  => get_post_meta($post->ID, WWPO_META_PAGE_WEAPP_KEY, true),
        'show_option_all' => '选择显示页面位置'
    ];

    echo WWPO_Form::list($page_weapp_form);
}

/**
 * 产品提交数据库附加操作函数
 *
 * @since 1.0.0
 * @param integer $post_id
 */
function wwpo_wechat_wp_page_updated($post_id)
{
    /** 判断页面包含 action 参数，保证操作只在 edit 页面下执行，禁止在列表页「快速编辑」执行 */
    if (empty($_POST['action'])) {
        return $post_id;
    }

    /** 判断产品编辑权限 */
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    update_post_meta($post_id, WWPO_META_PAGE_WEAPP_KEY, $_POST['wwpo-weapp-page']);
}
add_action('save_post_page', 'wwpo_wechat_wp_page_updated');
