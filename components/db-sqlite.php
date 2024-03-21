<?php

/**
 * SQLite 数据库操作方法
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage components
 */

/**
 * 定义输出数组格式：以字段名为键名
 *
 * @since 0.1
 */
define('SQLITE_ARRAY_A', SQLITE3_ASSOC);

/**
 * 定义输出数组格式：以数字为键名
 *
 * @since 0.1
 */
define('SQLITE_ARRAY_N', SQLITE3_NUM);

/**
 * sqlite 数据库类
 *
 * @since 0.1
 */
class WWPO_SQLite extends SQLite3
{
    /**
     * 设定最后插入数据的自增 ID.
     *
     * @var int
     */
    public $insert_id = 0;

    /**
     * The number of queries made.
     *
     * @since 1.2.0
     * @var int
     */
    public $num_queries = 0;

    /**
     * Undocumented variable
     *
     * @var array
     */
    public $table = [
        'options',
        'posts'
    ];

    /**
     * 启动数据库
     *
     * @param string $database_name 需要打开的数据库名（*带后缀名）
     */
    public function __construct($db_name, $db_dir = '')
    {
        if (file_exists(ABSPATH . $db_dir . DIRECTORY_SEPARATOR . $db_name)) {
            $this->open($db_dir . DIRECTORY_SEPARATOR . $db_name);
        } else {
            echo '未找到数据库文件';
            exit;
        }
    }

    /**
     * 获取数据库内容
     *
     * @param string $query     查询语句
     * @param string $output    输出内容格式
     *
     * @return array
     */
    public function get_results($query, $output = SQLITE_ARRAY_A)
    {
        return $this->_query($query, $output);
    }

    /**
     * 写书数据库操作
     *
     * @param string    $table  写入表名
     * @param array     $data   写入数据
     */
    public function insert($table, $data)
    {
        if (!is_array($data)) {
            return false;
        }

        $values  = [];

        /** 设定写入内容数组 */
        foreach ($data as $value) {

            // 判断为 null
            if (is_null($value)) {
                $values[] = 'NULL';
                continue;
            }

            // 判断为字符串
            if (is_string($value)) {
                $values[] = "'{$value}'";
                continue;
            }

            $values[] = $value;
        }

        // 设定写入字段和内容格式
        $fields = '`' . implode('`, `', array_keys($data)) . '`';
        $values = implode(', ', $values);

        // 写入数据库
        $this->exec("INSERT INTO `$table` ($fields) VALUES ($values)");
        $this->insert_id = $this->lastInsertRowID();
    }

    /**
     * Undocumented function
     *
     * @param [type] $table
     * @param [type] $data
     * @param [type] $where
     * @return void
     */
    public function update($table, $data, $where)
    {
        if (!is_array($data) || !is_array($where)) {
            return false;
        }

        $fields = [];
        $conditions = [];

        foreach ($data as $field_key => $field_value) {
            if (is_null($field_value)) {
                $fields[] = "`$field_key` = NULL";
                continue;
            }

            // 判断为字符串
            if (is_string($field_value)) {
                $fields[] = "`$field_key` = '{$field_value}'";
                continue;
            }

            $fields[] = "`$field_key` = " . $field_value;
        }

        foreach ($where as $where_field => $where_value) {
            if (is_null($where_value)) {
                $conditions[] = "`$where_field` IS NULL";
                continue;
            }

            // 判断为字符串
            if (is_string($where_value)) {
                $values[] = "`$where_field` = '{$where_value}'";
                continue;
            }

            $conditions[] = "`$where_field` = " . $where_value;
        }


        $fields     = implode(', ', $fields);
        $conditions = implode(' AND ', $conditions);

        $this->exec("UPDATE `$table` SET $fields WHERE $conditions");
    }

    /**
     * Undocumented function
     *
     * @param [type] $table
     * @param [type] $where
     * @return void
     */
    public function delete($table, $where)
    {
        if (!is_array($where)) {
            return false;
        }

        $conditions = [];

        foreach ($where as $where_field => $where_value) {
            if (is_null($where_value)) {
                $conditions[] = "`$where_field` IS NULL";
                continue;
            }

            $conditions[] = "`$where_field` = " . $where_value;
        }

        $conditions = implode(' AND ', $conditions);

        return $this->exec("DELETE FROM `$table` WHERE $conditions");
    }

    /**
     * Undocumented function
     *
     * @param [type] $query
     * @param integer $x
     * @param integer $y
     * @return void
     */
    public function get_var($query = null, $x = 0)
    {
        $rows = $this->_query($query, SQLITE_ARRAY_N);

        return $rows[$x][0] ?? null;
    }

    /**
     * Undocumented function
     *
     * @param [type] $query
     * @param integer $x
     * @return void
     */
    public function get_row($query = null, $x = 0)
    {
        $query = $query . " LIMIT {$x}, 1";
        $rows = $this->querySingle($query, true);
        $this->num_queries++;
        return $rows ?? null;
    }

    public function get_col($query = null)
    {
        $result = [];
        $rows = $this->_query($query, SQLITE_ARRAY_N);

        foreach ((array) $rows as $row) {
            $result[] = $row[0];
        }

        return $result;
    }

    /**
     * Undocumented function
     *
     * @param [type] $query
     * @param string $output
     * @return void
     */
    protected function _query($query, $output = SQLITE_ARRAY_A)
    {
        $rows = [];

        if ($query) {
            $result = $this->query($query);
            $this->num_queries++;
        }

        if (empty($result)) {
            return;
        }

        while ($row = $result->fetchArray($output)) {
            $rows[] = $row;
        }

        return $rows;
    }
}
