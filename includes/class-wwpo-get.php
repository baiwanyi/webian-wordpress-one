<?php

/**
 * 获取函数操作类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */
class WWPO_Get
{
    /**
     * 获取文章内容的缩略图
     * 如未定义缩略图则自动提取文章正文第一张图片
     *
     * @since 1.0.0
     * @param WP_Post $post
     */
    static function thumb($post)
    {
        $post_id = is_numeric($post) ? $post : $post->ID;

        // 获取文章缩略图
        $thumb_url = get_the_post_thumbnail_url($post_id, 'large');

        // 默认缩略图文件
        $thumb_default = self::option(OPTION_SETTING_KEY, 'thumb_default');

        if (empty($thumb_default)) {
            $thumb_default = 'data:image/svg+xml;base64,PHN2ZyBjbGFzcz0iaWNvbiIgdmlld0JveD0iMCAwIDEwMjQgMTAyNCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCI+PHBhdGggZD0iTTAgMTkyQzAgMTIxLjQgNTcuNCA2NCAxMjggNjRoNzY4YzcwLjYgMCAxMjggNTcuNCAxMjggMTI4djY0MGMwIDcwLjYtNTcuNCAxMjgtMTI4IDEyOEgxMjhDNTcuNCA5NjAgMCA5MDIuNiAwIDgzMlYxOTJ6bTY0Ny42IDIxM2MtOS0xMy4yLTIzLjgtMjEtMzkuNi0yMXMtMzAuOCA3LjgtMzkuNiAyMWwtMTc0IDI1NS4yLTUzLTY2LjJjLTkuMi0xMS40LTIzLTE4LTM3LjQtMThzLTI4LjQgNi42LTM3LjQgMThsLTEyOCAxNjBjLTExLjYgMTQuNC0xMy44IDM0LjItNS44IDUwLjhTMTU3LjYgODMyIDE3NiA4MzJoNjcyYzE3LjggMCAzNC4yLTkuOCA0Mi40LTI1LjZzNy4yLTM0LjgtMi44LTQ5LjRsLTI0MC0zNTJ6TTIyNCAzODRhOTYgOTYgMCAxIDAgMC0xOTIgOTYgOTYgMCAxIDAgMCAxOTJ6IiBmaWxsPSIjYmZiZmJmIi8+PC9zdmc+';
        }

        /** 判断未定义缩略图则自动提取文章正文第一张图片 */
        if (empty($thumb_url)) {
            preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $post->post_content, $content);

            // 如文章没有图片，则使用默认图片
            $thumb_url = $content[2][0] ?? $thumb_default;
        }

        return $thumb_url;
    }

    /**
     * 获取纯文本
     *
     * @since 1.0.0
     * @param string $text 文本内容
     */
    static function plain($text)
    {
        if (empty($text)) {
            return;
        }

        $text = wp_strip_all_tags($text);
        $text = str_replace('"', '', $text);
        $text = str_replace('\'', '', $text);
        $text = str_replace("\r\n", ' ', $text);
        $text = str_replace("\n", ' ', $text);
        $text = str_replace("  ", ' ', $text);
        return trim($text);
    }

    /**
     * 获取第一段内容
     *
     * @since 1.0.0
     * @param string $text 文本内容
     */
    static function first_section($text)
    {
        if (empty($text)) {
            return;
        }

        $text = explode("\n", trim(strip_tags($text)));
        return trim($text['0']);
    }

    /**
     * 获取远程图片函数
     *
     * @since 1.0.0
     * @param string  $image_url 图片地址
     */
    static function remote_image($image_url, $subdir = null, $ext = null)
    {
        // 设定保存到本地的图片文件名和文件夹
        $uploads    = wp_upload_dir();
        $subdir     = $subdir ?? $uploads['subdir'];

        $attachment_filename    = WWPO_File::name($image_url, $ext);
        $attachment_basedir     = $uploads['basedir'] . $subdir;

        /** 判断文件名没有扩展名的情况下，将 jpg 作为文件的扩展名 */
        if (empty(pathinfo($image_url, PATHINFO_EXTENSION))) {
            $attachment_filename .= '.jpg';
        }

        /** 判断保存文件夹为空则新建文件夹 */
        if (!file_exists($attachment_basedir)) {
            wp_mkdir_p($attachment_basedir);
        }

        // 获取远程图片文件信息
        $headers = WWPO_Util::curl($image_url, [
            'timeout'   => 60,
            'stream'    => true,
            'filename'  => $attachment_basedir . DIRECTORY_SEPARATOR . $attachment_filename
        ]);

        if (empty($headers)) {
            return;
        }

        return ltrim($subdir, '/')  . '/' . $attachment_filename;
    }

    /**
     * 获取当前页面地址函数
     *
     * @since 1.0.0
     * @return string
     */
    static function page_url()
    {
        return set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }

    /**
     * 获取当前 IP 地址函数
     *
     * @since 1.0.0
     */
    static function ip()
    {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    /**
     * 用户浏览器信息
     *
     * @since 1.0.0
     */
    static function agent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * 获取 url 地址中的参数，返回数组格式
     *
     * @since 1.0.0
     * @param string $url 需要解析的 url 地址
     */
    static function url_query($url)
    {
        $query = parse_url($url, PHP_URL_QUERY);

        if (empty($query)) {
            return;
        }

        parse_str($query, $array_query);

        if (empty($array_query)) {
            return;
        }

        return $array_query;
    }

    /**
     * 获取 iOS 操作系统信息函数
     *
     * @since 1.0.0
     * @param string $user_agent 需要获取的信息标签
     * - version    系统版本
     * - build      浏览器版本
     */
    static function ios($user_agent)
    {
        switch ($user_agent) {
            case 'version':
                $pattern = '/OS (.*?) like Mac OS X[\)]{1}/i';
                break;
            case 'build':
                $pattern = '/Mobile\/(.*?)\s/i';
                break;
            default:
                break;
        }

        if (empty($pattern)) {
            return;
        }

        if (preg_match($pattern, $_SERVER['HTTP_USER_AGENT'], $matches)) {
            return trim($matches[1]);
        } else {
            return;
        }
    }

    /**
     * 格式化时间
     *
     * @since 1.0.0
     * @param string|integer    $timestamp  需要格式化的时间（或时间戳）
     * @param string            $format     时间格式，默认：1900-01-01
     */
    static function human_time($timestamp, $format = 'Y-m-d')
    {
        if (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        $time_diff = NOW - $timestamp;

        if ($timestamp && $time_diff > 0 && $time_diff < DAY_IN_SECONDS) {
            return sprintf(__('%s 前', 'wwpo'), human_time_diff($timestamp, NOW));
        } else {
            return date($format, $timestamp);
        }
    }

    /**
     * 格式化显示时间
     *
     * @since 1.0.0
     * @param string|integer    $timestamp  需要格式化的时间（或时间戳）
     * @param string            $format     时间格式，默认：1900年01月01日 00:00:00
     */
    static function full_date($timestamp, $format = 'Y年m月d日 H:i:s')
    {
        if (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        return date($format, $timestamp);
    }

    /**
     * 读取 CSV 文件操作函数
     *
     * @since 1.0.0
     * @param string    $file       读取的文件路径
     * @param array     $column     解析文件标题数组
     */
    static function csv($file, $column)
    {
        $column     = array_flip($column);

        // 初始化 CSV 导入内容数组
        $csv_data   = [];

        // 初始化 CSV 显示列编号标识
        $csv_key    = [];

        // 初始化 CSV 表格内容数组
        $csv_table  = [];

        // 读取上传的 CSV 文件内容
        $get_csv_content = fopen($file, 'r');

        // 遍历内容，读取每一行 CSV 文件内容为数组
        while (!feof($get_csv_content)) {
            $csv_data[] = fgetcsv($get_csv_content);
        }

        // 关闭文件
        fclose($get_csv_content);

        // 转换格式，防止中文出现乱码
        $csv_data = WWPO_Util::iconv(var_export($csv_data, true));

        /**
         * 遍历 CSV 标题行（第一行）内容
         * 根据中文行标题设定当前列的英文标识（数据库字段名称），即第几列用什么英文标识
         * 即使导入表格不按照列顺序，也能正确按照数据库字段名称进行导入
         *
         * @since 1.0.0
         * @property integer    $first_key      当前列的序号
         * @property string     $first_value    当前列的中文行标题
         */
        foreach ($csv_data[0] as $first_key => $first_value) {

            // 判断导入的数据（标题）是否存在设定的表格内容
            if (empty($column[$first_value])) {
                continue;
            }

            // 根据列表顺序编号设置当前列的英文标识（数据库字段名称）
            $csv_key[$first_key] = $column[$first_value];
        }

        // 删除标题行，不进行下面的操作
        unset($csv_data[0]);

        /**
         * 遍历导入的表格内容数组（删除标题行）
         *
         * @since 1.0.0
         * @property integer    $upload_csv_i       导入的行序号
         * @property array      $upload_csv_rows    导入的行内容
         */
        foreach ($csv_data as $upload_csv_i => $upload_csv_rows) {

            // 判断行内容为空
            if (empty($upload_csv_rows)) {
                continue;
            }

            // 设定当前行序号的数组
            $csv_table[$upload_csv_i] = [];

            /**
             * 遍历当前行内容
             *
             * @since 1.0.0
             * @property integer    $row_i      当前列序号
             * @property string     $row_value  当前内容
             */
            foreach ($upload_csv_rows as $row_i => $row_value) {

                // 判断当前列内容为空
                if (empty($csv_key[$row_i])) {
                    continue;
                }

                // 设定当前行的内容数组
                // 以 csv 显示列编号标识（数据库字段名）为 key 的内容
                $csv_table[$upload_csv_i][$csv_key[$row_i]] = $row_value;
            }
        }

        return $csv_table;
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
    static function wheres($wheres)
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
             * @property string     $where_key   查询字段
             * @property string[]   $where_val   查询内容和格式
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
     * 获取设置参数
     *
     * @since 2.0.0
     * @param string    $option_key 需要获取的设置
     * @param string[]  $value_key  需要获取的设置
     */
    static function option($option_key, $value_key)
    {
        $option_data = get_option($option_key, []);

        if (is_string($value_key)) {
            return $option_data[$value_key] ?? '';
        }


    }
}
