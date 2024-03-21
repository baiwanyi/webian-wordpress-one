<?php

/**
 * Redis 数据库操作方法
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage components
 */

class WWPO_Redis
{
    /**
     * 数据库访问 IP 地址
     *
     * @since 2.0.0
     * @var string
     */
    const REDIS_IP = '127.0.0.1';

    /**
     * 数据库访问端口
     *
     * @since 2.0.0
     * @var integer
     */
    const REDIS_PORT = 6379;

    /**
     * 保存字段过期时间
     *
     * @since 2.0.0
     * @var integer
     */
    const REDIS_EXPIRE = 0;

    /**
     * 数据库访问密钥
     *
     * @since 2.0.0
     * @var string
     */
    const REDIS_PASS = '';

    /**
     * 数据库索引编号
     *
     * @since 2.0.0
     * @var integer
     */
    const REDIS_INDEX = 0;

    /**
     * Redis 内容
     *
     * @since 1.0.0
     * @var object
     */
    static $redis;

    /**
     * 设置 Redis 的连通性
     *
     * @since 1.0.0
     */
    static function connect($index = self::REDIS_INDEX)
    {
        /** 判断是否开启了 Redis 扩展 */
        if (!extension_loaded('Redis')) {
            wp_die(__('没有找到 Redis 扩展。', 'wwpo'));
        }

        // 开启 Redis 连接
        self::$redis = new Redis();
        self::$redis->connect(self::REDIS_IP, self::REDIS_PORT);

        /** 判断 Redis 连接是否成功 */
        if (!self::$redis->ping()) {
            wp_die(__('Redis 连接失败。', 'wwpo'));
        }

        /** 判断 Redis 连接密码 */
        if (self::REDIS_PASS) {
            self::$redis->auth(self::REDIS_PASS);
        }

        if (defined('WP_REDIS_DATABASE')) {
            $index = WP_REDIS_DATABASE;
        }

        self::$redis->select($index);
    }

    /**
     * 检测 Redis 键是否存在
     *
     * @since 1.0.0
     * @param string $key 键名
     */
    static function exists($key)
    {
        self::connect();

        if (self::$redis->exists($key)) {
            return true;
        }

        return false;
    }

    /**
     * 获取 Redis 键的值
     *
     * @since 1.0.0
     * @param string $key       键名
     * @param string $default   为空时候的默认值
     */
    static function get($key, $default = '')
    {
        self::connect();

        $response = self::$redis->get($key) ?? $default;

        if (is_serialized($response)) {
            return maybe_unserialize($response);
        }

        return $response;
    }

    /**
     * 设置 Redis 键值
     *
     * @since 1.0.0
     * @param string    $key    键名
     * @param mixed     $value  键值
     * @param integer   $expire 数据过期时间
     */
    static function set($key, $value, $expire = self::REDIS_EXPIRE)
    {
        self::connect();

        if (is_array($value) || is_object($value)) {
            $value = maybe_serialize($value);
        }

        if (self::$redis->set($key, $value)) {

            if ($expire) {
                self::$redis->expire($key, $expire);
            }
        }
    }

    /**
     * 删除 Redis 键名
     *
     * @since 1.0.0
     * @param string key 键名
     */
    static function del($key)
    {
        self::connect();
        self::$redis->del($key);
    }

    /**
     * 清空所有数据
     *
     * @since 1.0.0
     */
    static function flushall()
    {
        self::connect();
        self::$redis->flushall();
    }
}
