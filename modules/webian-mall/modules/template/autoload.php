<?php

/**
 * 模板管理模块
 *
 * @since 1.0.0
 * @package Webian WP Mall
 * @subpackage modules/template
 */
define('WWPO_SQL_TMPL_FAVOR', 'wwpo_template_favor');

/** 定义元数据字段名称 */
define('WWPO_TMPL_META_FAVOR', '_wwpo_template_favor_num');
define('WWPO_TMPL_META_ORDER', '_wwpo_template_order_num');

/**
 * 注册 Rest API 接口
 *
 * @since 1.0.0
 */
function wwpo_wpmall_template_rest_register_routes()
{
    // 模板相关接口
    $template_rest = new wwpo_wpmall_template_rest_controller();
    $template_rest->register_routes();

}
add_action('rest_api_init', 'wwpo_wpmall_template_rest_register_routes');
