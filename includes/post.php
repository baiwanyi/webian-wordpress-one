<?php

/**
 * 数据库获取/增删改/条件查询操作函数
 *
 * @package Webian WordPress One
 */

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
function wwpo_get_post($table_name, $column, $post_id, $key = null, $output = OBJECT)
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
function wwpo_get_post_field($post, $field)
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
function wwpo_get_row($table_name, $column, $post_id, $expand = '', $output = OBJECT)
{
    if (empty($table_name) || empty($column) || empty($post_id)) {
        return false;
    }

    global $wpdb;

    $results = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE {$column} = %s {$expand}", $post_id), $output);

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
function wwpo_get_col($table_name, $column, $post_id, $field, $offset = 0)
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
function wwpo_insert_post($table_name, $updated, $ignore = false)
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
function wwpo_update_post($table_name, $updated, $wheres)
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
function wwpo_delete_post($table_name, $wheres)
{
    global $wpdb;

    if (empty($table_name) || empty($wheres)) {
        return false;
    }

    $wpdb->delete($table_name, $wheres);
}

/**
 * 获取单个关键字值，带查询条件数组
 *
 * @since 1.0.0
 * @param string    $table_name 数据库表名
 * @param array     $wheres     查询条件数组
 * @param string    $key        查询字段，为空则返回查询条件所有值，为空则显示所有字段内容
 */
function wwpo_get_post_by_wheres($table_name, $wheres, $key = '*')
{
    if (empty($table_name) || empty($wheres)) {
        return false;
    }

    $wheres = wwpo_get_wheres($wheres);

    global $wpdb;

    $results = $wpdb->get_results("SELECT {$key} FROM {$table_name} WHERE {$wheres}", ARRAY_A);

    return $results;
}

/**
 * 设定表格内容查询条件
 *
 * @since 1.0.0
 * @param array $wheres
 * {
 *  查询条件内容数组
 *  @var string $jion   查询链接符号（AND,OR），默认：AND
 *  @var string $sign   查询运算符号（=,>,<...），默认：=
 *  @var string $format 格式化内容（s,d），默认：s
 *  @var string $value  查询内容
 * }
 */
function wwpo_get_wheres($wheres)
{
    global $wpdb;

    $where      = [];
    $default    = [
        'jion'      => 'AND',
        'sign'      => '=',
        'format'    => 's',
        'value'     => ''
    ];

    /** 判断为数组格式 */
    if (is_array($wheres)) {

        /**
         * 遍历查询条件数组内容
         *
         * @property string         $where_key   查询字段
         * @property string|array   $where_val   查询内容和格式
         */
        foreach ($wheres as $where_key => $where_val) {

            /** 判断查询内容为字符串格式，直接调用内容 */
            if (is_array($where_val)) {
                // 设定查询条件默认值
                $where_val = wp_parse_args($where_val, $default);

                // 预处理插件条件
                $where[] = $wpdb->prepare("{$where_val['jion']} {$where_key} {$where_val['sign']} %{$where_val['format']}", $where_val['value']);
                continue;
            }

            $where[] = $wpdb->prepare("AND {$where_key} = '%s'", $where_val);
        }

        // 查询数组以空格连接并删除最右的连接符
        $where = implode(' ', $where);
        $where = ltrim($where, $default['jion']);

        // 返回查询条件内容
        return $where;
    }

    // 返回查询条件内容
    return $where;
}

/**
 * 更新 META 表数据内容
 *
 * @since 1.0.0
 * @param string        $table_name     数据库表名
 * @param integer       $post_id        关联表 ID
 * @param string        $meta_key       META 关键字
 * @param string|array  $meta_value     META 保存内容
 */
function wwpo_update_meta($table_name, $post_id, $meta_key, $meta_value)
{
    $wheres['post_id']  = ['value' => $post_id];
    $wheres['meta_key'] = ['value' => $meta_key];
    $meta_id = wwpo_get_post_by_wheres($table_name, $wheres, 'meta_id');

    if (is_array($meta_value)) {
        $meta_value = maybe_serialize($meta_value);
    }

    if (empty($meta_id)) {
        $meta_id = wwpo_insert_post($table_name, [
            'post_id'       => $post_id,
            'meta_key'      => $meta_key,
            'meta_value'    => $meta_value
        ]);

        return $meta_id;
    }

    wwpo_update_post($table_name, ['meta_value' => $meta_value], ['meta_id' => $meta_id]);

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
function wwpo_get_meta($table_name, $post_id, $meta_key)
{
    $wheres['post_id']  = ['value' => $post_id];
    $wheres['meta_key'] = ['value' => $meta_key];
    $meta_value = wwpo_get_post_by_wheres($table_name, $wheres, 'meta_value');

    if (empty($meta_value)) {
        return;
    }

    if (is_serialized($meta_value)) {
        $meta_value = maybe_unserialize($meta_value);
    }

    return $meta_value;
}

/**
 * 保存文章内容中远程图片到本地
 *
 * @since 1.0.0
 * @param string $post_content  文章内容
 */
function wwpo_save_post_image($post_content)
{
    // 正则查询文章内容中的图片
    preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $post_content, $matchall);

    /** 判断没有图片直接返回内容 */
    if (empty($matchall)) {
        return $post_content;
    }

    // 设定图片域名
    $image_url = parse_url(home_url(), PHP_URL_HOST);

    /**
     * 遍历正则的文章图片内容数组
     */
    foreach ($matchall[2] as $key => $image) {

        // 判断当前图片域名是否为远程图片
        if ($image_url == parse_url($image, PHP_URL_HOST)) {
            continue;
        }

        // 获取远程图片，同时保存本地和OSS
        $image_src = wwpo_get_remote_image($image);

        // 判断保存成功，获取图片地址
        if (empty($image_src)) {
            continue;
        }

        // 设定图片地址和 OSS 样式
        $image_file = sprintf('<img src="%1$s?x-oss-process=style/postview" class="thumb lazy">', $image_src);

        // 替换文章内容中图片
        $post_content = str_replace($matchall[0][$key], $image_file, $post_content);
    }

    // 返回文章内容
    return $post_content;
}
