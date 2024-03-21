<?php

/**
 * 元数据获取/增删改/条件查询操作函数
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */
class WWPO_Meta
{
    /**
     * 更新 META 表数据内容
     *
     * @since 1.0.0
     * @param string    $table_name     数据库表名
     * @param integer   $post_id        关联表 ID
     * @param string    $meta_key       META 关键字
     * @param string[]  $meta_value     META 保存内容
     */
    static function update($table_name, $post_id, $meta_key, $meta_value)
    {
        global $wpdb;

        if (empty($table_name) || empty($post_id) || empty($meta_key) || empty($meta_value)) {
            return false;
        }

        $meta_id = $wpdb->get_var("SELECT meta_id FROM {$table_name} WHERE post_id = $post_id AND meta_key = '{$meta_key}'");

        if (is_array($meta_value)) {
            $meta_value = maybe_serialize($meta_value);
        }

        if (empty($meta_id)) {
            $meta_id = $wpdb->insert($table_name, [
                'post_id'       => $post_id,
                'meta_key'      => $meta_key,
                'meta_value'    => $meta_value
            ]);

            return $meta_id;
        }

        $wpdb->update($table_name, ['meta_value' => $meta_value], ['meta_id' => $meta_id]);

        return $meta_id;
    }

    /**
     * 获取 META 表数据内容
     *
     * @since 1.0.0
     * @param string    $table_name     数据库表名
     * @param integer   $post_id        关联表 ID
     * @param string    $meta_key       META 关键字
     */
    static function get($table_name, $post_id, $meta_key)
    {
        global $wpdb;

        if (empty($table_name) || empty($post_id) || empty($meta_key)) {
            return false;
        }

        $meta_value = $wpdb->get_var("SELECT meta_value FROM {$table_name} WHERE post_id = $post_id AND meta_key = '{$meta_key}'");

        if (empty($meta_value)) {
            return;
        }

        if (is_serialized($meta_value)) {
            $meta_value = maybe_unserialize($meta_value);
        }

        return $meta_value;
    }

    /**
     * 获取 META 表数据内容
     *
     * @since 1.0.0
     * @param string    $table_name     数据库表名
     * @param integer   $post_id        关联表 ID
     * @param string    $meta_key       META 关键字
     */
    static function delete($table_name, $post_id, $meta_key)
    {
        global $wpdb;

        if (empty($table_name) || empty($post_id) || empty($meta_key)) {
            return false;
        }

        $meta_id = $wpdb->get_var("SELECT meta_id FROM {$table_name} WHERE post_id = $post_id AND meta_key = '{$meta_key}'");

        if (empty($meta_id)) {
            return;
        }

        $wpdb->delete($table_name, ['meta_id' => $meta_id]);
    }
}
