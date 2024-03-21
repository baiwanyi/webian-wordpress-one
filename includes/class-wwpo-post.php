<?php

/**
 * 数据库获取/增删改/条件查询操作函数
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */
class WWPO_Post
{
    /**
     * 获取单个关键字值
     *
     * @since 1.0.0
     * @param string    $table_name 数据库表名
     * @param string    $column     索引字段
     * @param integer   $post_id    查询条件 ID
     * @param mixed     $key        查询字段，为空则返回查询条件所有值，为空则显示所有字段内容
     * @param string    $output     输出内容格式，默认：OBJECT
     */
    function single($table_name, $column, $post_id, $key = null, $output = OBJECT)
    {
        if (empty($table_name) || empty($column) || empty($post_id)) {
            return false;
        }

        global $wpdb;

        if (empty($key)) {
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE {$column} = %s", $post_id), $output);
        }

        /** 判断查询字段，为空则显示所有字段内容 */
        if (is_array($key)) {
            $select = implode(',', $key);
            return $wpdb->get_results($wpdb->prepare("SELECT {$select} FROM {$table_name} WHERE {$column} = %s", $post_id), $output);
        }

        return $wpdb->get_var($wpdb->prepare("SELECT {$key} FROM {$table_name} WHERE {$column} = %s ORDER BY $key DESC", $post_id));
    }

    /**
     * 获取单个关键字值
     *
     * @since 1.0.0
     * @param string    $table_name 数据库表名
     * @param string    $column     索引字段
     * @param integer   $post_id    查询条件 ID
     * @param mixed     $key        查询字段，为空则返回查询条件所有值，为空则显示所有字段内容
     * @param string    $output     输出内容格式，默认：OBJECT
     */
    function result($table_name, $column, $post_id, $key = null, $output = OBJECT)
    {
        if (empty($table_name) || empty($column) || empty($post_id)) {
            return false;
        }

        global $wpdb;

        if (empty($key)) {
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE {$column} = %s", $post_id), $output);
        }

        /** 判断查询字段，为空则显示所有字段内容 */
        if (is_array($key)) {
            $select = implode(',', $key);
            return $wpdb->get_results($wpdb->prepare("SELECT {$select} FROM {$table_name} WHERE {$column} = %s", $post_id), $output);
        }

        return $wpdb->get_var($wpdb->prepare("SELECT {$key} FROM {$table_name} WHERE {$column} = %s ORDER BY $key DESC", $post_id));
    }

    /**
     * 获取 posts 表某个字段的值
     *
     * @since 1.0.0
     * @param integer|string    $post
     * @param string            $field
     */
    function field($post, $field)
    {
        if (empty($post) || empty($field)) {
            return false;
        }

        global $wpdb;

        if (is_numeric($post)) {
            $column = 'ID';
        } else {
            $column = 'post_name';
        }

        return $wpdb->get_var($wpdb->prepare("SELECT {$field} FROM {$wpdb->posts} WHERE {$column} = '%s'", $post));
    }

    /**
     * 获取单行数据
     *
     * @since 1.0.0
     * @param string    $table_name 数据库表名
     * @param string    $column     索引字段
     * @param integer   $post_id    查询条件 ID
     * @param string    $expand     SQL 扩展查询语句
     * @param string    $output     输出内容格式，默认：OBJECT
     */
    function row($table_name, $column, $post_id, $expand = '', $output = OBJECT)
    {
        if (empty($table_name) || empty($column) || empty($post_id)) {
            return false;
        }

        global $wpdb;

        $results = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE {$column} = '%s' {$expand}", $post_id), $output);

        return $results;
    }

    /**
     * 获取单列据
     *
     * @since 1.0.0
     * @param string    $table_name 数据库表名
     * @param string    $column     索引字段
     * @param integer   $post_id    查询条件 ID
     * @param string    $field      需要获取的内容列字段名
     * @param integer   $offset     偏移行号，默认：0
     */
    function col($table_name, $column, $post_id, $field, $offset = 0)
    {
        if (empty($table_name) || empty($column) || empty($post_id) || empty($field)) {
            return false;
        }

        global $wpdb;

        $results = $wpdb->get_col($wpdb->prepare("SELECT {$field} FROM {$table_name} WHERE {$column} = %s", $post_id), $offset);

        return $results;
    }

    /**
     * 写入一条记录到数据库
     *
     * @since 1.0.0
     * @param string    $table_name 数据库表名
     * @param array     $updated    添加的数据
     * @param boolean   $ignore     忽略错误，默认：false
     */
    function insert($table_name, $updated, $ignore = false)
    {
        global $wpdb;

        if (empty($table_name) || empty($updated)) {
            return false;
        }

        /**
         * 判断忽略错误，
         * 用于插入唯一索引时忽略写入错误
         */
        if ($ignore) {
            $fields  = '`' . implode('`, `', array_keys($updated)) . '`';
            $values = "'" . implode("', '", $updated) . "'";

            $wpdb->query("INSERT IGNORE INTO `$table_name` ($fields) VALUES ($values)");
        } else {
            $wpdb->insert($table_name, $updated);
        }

        if (is_wp_error($wpdb->insert_id)) {
            return false;
        }

        return $wpdb->insert_id;
    }

    /**
     * 更新一条记录数据库
     *
     * @since 1.0.0
     * @param string    $table_name 数据库表名
     * @param array     $updated    添加的数据
     * @param array     $wheres     更新查询条件
     */
    function update($table_name, $updated, $wheres)
    {
        global $wpdb;

        if (empty($table_name) || empty($updated) || empty($wheres)) {
            return false;
        }

        $wpdb->update($table_name, $updated, $wheres);
    }

    /**
     * 删除一条数据库内容
     *
     * @since 1.0.0
     * @param string    $table_name 数据库表名
     * @param array     $wheres     删除查询条件
     */
    function delete($table_name, $wheres)
    {
        global $wpdb;

        if (empty($table_name) || empty($wheres)) {
            return false;
        }

        $wpdb->delete($table_name, $wheres);
    }
}
