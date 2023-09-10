<?php

/**
 * 常用文件系统函数
 * 输出处理文件或目录内容
 *
 * @package Webian WordPress One
 */

/**
 * 扫描并列出指定目录下所有目录和文件名列表
 *
 * @since 1.0.0
 * @param string   $folder     Optional. Full path to folder. Default empty.
 * @param int      $levels     Optional. Levels of folders to follow, Default 100 (PHP Loop limit).
 * @param string[] $exclusions Optional. List of folders and files to skip.
 */
function wwpo_list_files($folder = '', $levels = 1, $exclusions = [])
{
    if (empty($folder)) {
        return false;
    }

    $folder = trailingslashit($folder);

    if (!$levels) {
        return false;
    }

    $files = [];

    $dir = @opendir($folder);

    if ($dir) {
        while (($file = readdir($dir)) !== false) {
            // Skip current and parent folder links.
            if (in_array($file, ['.', '..'], true)) {
                continue;
            }

            // Skip hidden and excluded files.
            if ('.' === $file[0] || in_array($file, $exclusions, true)) {
                continue;
            }

            if (is_dir($folder . $file)) {
                $files2 = wwpo_list_files($folder . $file, $levels - 1);
                if ($files2) {
                    $files = array_merge($files, $files2);
                } else {
                    $files[] = $folder . $file . '/';
                }
            } else {
                $files[] = $folder . $file;
            }
        }

        closedir($dir);
    }

    return $files;
}

/**
 * 统计文件夹下文件数量
 *
 * @since 1.0.0
 * @param string $dir
 * @param string $pattern 匹配指定模式，默认：*，所有文件
 */
function wwpo_total_files($dir, $pattern = '/*')
{
    $i = 0;
    $array = glob($dir . $pattern);
    foreach ($array as $file) {
        if (is_file($file)) {
            $i++;
        }
    }
    return $i;
}

/**
 * 扫描并列出指定目录下所有目录名列表
 *
 * @since 1.0.0
 * @param string $path 需要查找的目录
 */
function wwpo_get_dir($path)
{
    $array_dir  = wwpo_list_files($path, 1);
    $array_data = [];

    if (empty($array_dir)) {
        return;
    }

    foreach ($array_dir as $dir) {
        $basename = basename($dir);
        $array_data[$basename] = $dir;
    }
    return $array_data;
}

/**
 * 批量加载文件夹 PHP 文件
 *
 * @since 1.0.0
 * @param string $path 需要加载的目录
 */
function wwpo_require_dir($path)
{
    $array_files = wwpo_list_files($path, 2);

    if (empty($array_files)) {
        return;
    }

    foreach ($array_files as $file) {

        if (!in_array(pathinfo($file, PATHINFO_EXTENSION), ['php', 'phar'])) {
            continue;
        }

        require $file;
    }
}

/**
 * 模版加载函数
 *
 * @since 1.0.0
 * @param string    $slug   模版别名
 * @param string    $name   模版名称
 * @param array     $args   加载参数
 */
function wwpo_load_template($slug, $name = null, $args = [])
{
    $located = '';
    $template_names[] = $slug . '-' . $name . '.php';
    $template_names[] = $slug . '.php';

    foreach ($template_names as $template_name) {

        if (!$template_name) {
            continue;
        }

        if (file_exists(TEMPLATEPATH . '/' . $template_name)) {
            $located = TEMPLATEPATH . '/' . $template_name;
            break;
        } elseif (file_exists(WWPO_MOD_PATH . '/' . $template_name)) {
            $located = WWPO_MOD_PATH . '/' . $template_name;
            break;
        }
    }

    if (empty($located)) {
        return false;
    }

    load_template($located, true, $args);
}

/**
 * 图片文件重命名
 * 如为空直接输出「年月日时分秒 + 六位随机数」文件名，否则输出重命名后带扩展名的文件名
 *
 * @since 1.0.0
 * @param string $file  传入文件名
 * @param string $ext   指定文件扩展名
 */
function wwpo_filename($file = null, $ext = null)
{
    // 设置文件名，按照「年月日时分秒 + 六位随机数」
    $filename = date('ymdHis') . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);

    /** 判断指定文件扩展名 */
    if (isset($ext)) {
        return $filename . '.' . $ext;
    }

    /** 判断传入文件设置文件名 */
    if (isset($file)) {
        return $filename . '.' . pathinfo($file, PATHINFO_EXTENSION);
    }

    // 返回文件名
    return $filename;
}

/**
 * 解压缩 ZIP 到指定目录
 *
 * @since 1.0.0
 * @param string $zip_name  压缩包路径和名称
 * @param string $dir       解压路径
 */
function wwpo_unzip($zip_name, $dir)
{
    //检测要解压压缩包是否存在
    if (!file_exists($zip_name)) {
        return false;
    }

    //检测目标路径是否存在
    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }

    $zip = new ZipArchive();

    if ($zip->open($zip_name)) {
        $zip->extractTo($dir);
        $zip->close();
        return true;
    } else {
        return false;
    }
}

/**
 * 上传文件操作函数
 *
 * @since 1.0.0
 * @param string        $basedir    上传目录
 * @param string|array  $ext        验证文件扩展名
 */
function wwpo_upload_file($basedir = '/', $ext = [])
{
    // 设定默认参数
    $uploader   = $_FILES['uploader'] ?? [];
    $nonce      = $_POST['_wpnonce'] ?? 0;

    // 检测上传文件和验证上传随机数
    $check_upload = wwpo_check_upload($uploader, $nonce, $ext);
    if (true !== $check_upload) {
        return ['errmsg' => $check_upload['error']];
    }

    // 设定上传文件目录和上传文件名
    $uploads            = wp_upload_dir();
    $attachment_path    = $uploads['basedir'] . $basedir . $uploads['subdir'];
    $attachment_file    = $attachment_path . DIRECTORY_SEPARATOR . wwpo_filename($uploader['name']);

    /** 判断上传文件夹，不存在则新建文件夹 */
    if (!file_exists($attachment_path)) {
        wp_mkdir_p($attachment_path);
    }

    if (!move_uploaded_file($uploader['tmp_name'], $attachment_file)) {
        return ['errmsg' => '文件上传失败'];
    }

    return $attachment_file;
}

/**
 * 读取 CSV 文件操作函数
 *
 * @since 1.0.0
 * @param string    $file       读取的文件路径
 * @param array     $column     解析文件标题数组
 */
function wwpo_get_csv_content($file, $column)
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
    $csv_data = eval('return ' . iconv('GBK', 'UTF-8', var_export($csv_data, true)) . ';');

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
