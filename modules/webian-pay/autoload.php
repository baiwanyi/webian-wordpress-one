<?php

/*
 * Modules Name: 支付模块
 * Description: 包括微信支付、支付宝、头条支付、云闪付等支付模块功能。
 * Version: 1.0.0
 * Author: Baiwanyi
 * Updated: 2023-11-01
 */

 /**
 * 自定义常量
 *
 * @package Webian WordPress One
 */
// define('WWPO_KEY_MINIPROGRAMS', 'wwpo:miniprograms:data');

/**
 * 引用文件
 *
 * @since 1.0.0
 */
WWPO_File::require(WWPO_PLUGIN_PATH . 'modules/webian-pay/includes');
WWPO_File::require(WWPO_PLUGIN_PATH . 'modules/webian-pay/components');

/**
 * 添加后台管理菜单
 *
 * @since 1.0.0
 */
add_filter('wwpo_menus', ['WWPO_Pay_Interface', 'admin_menu']);

/**
 * 注册后台模块页面
 *
 * @since 1.0.0
 */
add_action('wwpo_admin_display_wwpo-payment', ['WWPO_Pay_Interface', 'admin_page_overview']);
add_action('wwpo_admin_display_wwpo-payment-order', ['WWPO_Pay_Interface', 'admin_page_order']);
add_action('wwpo_admin_display_wwpo-payment-refund', ['WWPO_Pay_Interface', 'admin_page_refund']);
add_action('wwpo_admin_display_wwpo-payment-notify', ['WWPO_Pay_Interface', 'admin_page_notify']);
add_action('wwpo_admin_display_wwpo-payment-setting', ['WWPO_Pay_Interface', 'admin_page_setting']);
add_action('wwpo_docs_tabs', ['WWPO_Pay_Interface', 'docs_tabs']);
