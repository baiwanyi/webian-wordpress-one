<?php

/**
 * Redis 方法类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */
class WWPO_Redis
{
    /**
     * Redis 内容
     *
     * @since 1.0.0
     *
     * @var object
     */
    static $redis;

    /**
     * 设置 Redis 的连通性
     *
     * @since 1.0.0
     */
    static function connect($index)
    {
        /** 判断是否开启了 Redis 扩展 */
        if (!extension_loaded('Redis')) {
            wp_die(__('没有找到 Redis 扩展。', 'wwpo'));
        }

        // 开启 Redis 连接
        self::$redis = new Redis();
        self::$redis->connect(WWPO_REDIS_IP, WWPO_REDIS_PORT);

        /** 判断 Redis 连接是否成功 */
        if (!self::$redis->ping()) {
            wp_die(__('Redis 连接失败。', 'wwpo'));
        }

        /** 判断 Redis 连接密码 */
        if (WWPO_REDIS_PASS) {
            self::$redis->auth(WWPO_REDIS_PASS);
        }

        self::$redis->select($index);
    }

    /**
     * 检测 Redis 键是否存在
     *
     * @since 1.0.0
     * @param string    $key    键名
     * @param integer   $index  数据库编号，默认：WWPO_REDIS_DB
     */
    static function exists($key, $index = WWPO_REDIS_DB)
    {
        self::connect($index);

        if (self::$redis->exists($key)) {
            return true;
        }

        return false;
    }

    /**
     * 获取 Redis 键的值
     *
     * @since 1.0.0
     * @param string    $key        键名
     * @param integer   $index      数据库编号，默认：WWPO_REDIS_DB
     * @param string    $default    为空时候的默认值
     */
    static function get($key, $index = WWPO_REDIS_DB, $default = '')
    {
        self::connect($index);

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
     * @param integer   $index  数据库编号，默认：WWPO_REDIS_DB
     * @param integer   $expire 数据过期时间
     */
    static function set($key, $value, $index = WWPO_REDIS_DB, $expire = WWPO_REDIS_EXPIRE)
    {
        self::connect($index);

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
     * @param string    $key    键名
     * @param integer   $index  数据库编号，默认：WWPO_REDIS_DB
     */
    static function del($key, $index = WWPO_REDIS_DB)
    {
        self::connect($index);
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
