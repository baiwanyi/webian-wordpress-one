<?php

/**
 * REST API 控制类
 *
 * @since 2.0.0
 * @package Webian WordPress One
 */

/**
 * 用于管理 REST API 项并与之交互的核心基础控制器。
 *
 * @since 2.0.0
 */
#[AllowDynamicProperties]
abstract class WWPO_REST_Controller
{
    /**
     * 此控制器的路由的命名空间。
     *
     * @since 2.0.0
     * @var string
     */
    protected $namespace;

    /**
     * 此控制器的别名
     *
     * @since 2.0.0
     * @var string
     */
    protected $rest_base;
}
