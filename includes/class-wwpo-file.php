<?php

/**
 * 文件操作类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */
class WWPO_File
{
    /**
     * 上传表单名称
     *
     * @since 2.0.0
     * @var string
     */
    const UPLOADER_POST_NAME = 'uploader';

    /**
     * 上传随机验证表单名称
     *
     * @since 2.0.0
     * @var string
     */
    const UPLOADER_NONCE_NAME = '_wpnonce';

    /**
     * 上传验证随机字符串
     *
     * @since 2.0.0
     * @var string
     */
    const UPLOADER_VERIFY_NONCE = '_wwpouploader';

    /**
     * 批量加载文件夹 PHP 文件
     *
     * @since 1.0.0
     * @param string    $path 需要加载的目录
     * @param integer   $deep 加载目录深度，默认：2
     */
    static function require($path, $deep = 2)
    {
        $array_files = self::list($path, $deep);

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
     * 上传文件操作函数
     *
     * @since 1.0.0
     * @param string    $basedir    上传目录
     * @param string[]  $ext        验证文件扩展名
     */
    static function upload($basedir = '/', $ext = [])
    {
        // 设定默认参数
        $uploader   = $_FILES[self::UPLOADER_POST_NAME] ?? [];
        $nonce      = $_POST[self::UPLOADER_NONCE_NAME] ?? 0;

        // 检测上传文件和验证上传随机数
        $check_upload = self::check($uploader, $nonce, $ext);
        if (true !== $check_upload) {
            return new WWPO_Error('error', $check_upload['error']);
        }

        // 设定上传文件目录和上传文件名
        $uploads            = wp_upload_dir();
        $attachment_path    = $uploads['basedir'] . $basedir . $uploads['subdir'];
        $attachment_file    = $attachment_path . DIRECTORY_SEPARATOR . self::name($uploader['name']);

        /** 判断上传文件夹，不存在则新建文件夹 */
        if (!file_exists($attachment_path)) {
            wp_mkdir_p($attachment_path);
        }

        if (!move_uploaded_file($uploader['tmp_name'], $attachment_file)) {
            return new WWPO_Error('error', __('文件上传失败', 'wwpo'));
        }

        return $attachment_file;
    }

    /**
     * 扫描并列出指定目录下所有目录名列表
     *
     * @since 1.0.0
     * @param string $path 需要查找的目录
     */
    static function dir($path)
    {
        $array_dir  = self::list($path, 1);
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
     * 图片文件重命名
     * 如为空直接输出「年月日时分秒 + 六位随机数」文件名，否则输出重命名后带扩展名的文件名
     *
     * @since 1.0.0
     * @param string $file  传入文件名
     * @param string $ext   指定文件扩展名
     */
    static function name($file = null, $ext = null)
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
     * 扫描并列出指定目录下所有目录和文件名列表
     *
     * @since 1.0.0
     * @param string    $folder     文件夹的完整路径。默认为空
     * @param integer   $levels     要遵循的文件夹级别，默认为100（PHP循环限制）
     * @param string[]  $exclusions 要跳过的文件夹和文件列表
     */
    static function list($folder = '', $levels = 1, $exclusions = [])
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

                if (in_array($file, ['.', '..'], true)) {
                    continue;
                }

                // 跳过隐藏和排除的文件。
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
    static function total($dir, $pattern = '/*')
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
     * 验证上传文件
     *
     * @since 1.0.0
     * @param array $uploader
     * {
     *  上传内容数组
     *  @var integer  error  和该文件上传相关的错误代码。
     *      - 0 UPLOAD_ERR_OK           文件上传成功
     *      - 1 UPLOAD_ERR_INI_SIZE     超过 php.ini 设置的文件大小
     *      - 2 UPLOAD_ERR_FORM_SIZE    超过 form 表单文件大小
     *      - 3 UPLOAD_ERR_PARTIAL      文件只有部分被上传
     *      - 4 UPLOAD_ERR_NO_FILE      没有文件被上传
     *      - 6 UPLOAD_ERR_NO_TMP_DIR   找不到临时文件夹
     *      - 7 UPLOAD_ERR_CANT_WRITE   文件写入失败
     *  @var string name            文件名
     *  @var string type            文件的 MIME 类型，需要浏览器提供该信息的支持，如：image/jpge
     *  @var integer    size        已上传文件的大小，单位为字节
     *  @var string     tmp_name    文件被上传后在服务端储存的临时文件名，一般是系统默认。
     * }
     * @param string    $nonce      上传验证随机数
     * @param string    $ext        文件扩展名
     */
    static function check($uploader, $nonce, $ext = null)
    {
        /** 判断是否上传成功 */
        if (empty($uploader) || 0 < $uploader['error']) {
            return new WWPO_Error('error', __('没有上传任何文件', 'wwpo'));
        }

        /** 验证上传权限 */
        if (!current_user_can('upload_files')) {
            return new WWPO_Error('error', 'invalid_role');
        }

        /** 验证上传随机验证码 */
        if (!wp_verify_nonce($nonce, self::UPLOADER_VERIFY_NONCE)) {
            return new WWPO_Error('error', 'invalid_nonce');
        }

        /** 判断不验证文件扩展名 */
        if (empty($ext)) {
            return true;
        }

        // 获取上传文件的扩展名
        $check_ext =
            $uploader_ext = strtolower(pathinfo($uploader['name'], PATHINFO_EXTENSION));

        /** 需要判断的扩展名是否为数组（多个）内容 */
        if (is_array($ext)) {

            $check_ext = array_map('strtolower', $ext);

            // 数组包含判断
            if (!in_array($uploader_ext, $check_ext)) {
                return new WWPO_Error('error',  _x('文件类型不为 <strong>%s</strong> 格式', implode(',', $check_ext), 'wwpo'));
            }

            return true;
        }

        $check_ext = strtolower($ext);

        // 单一文件扩展名判断
        if ($uploader_ext != $check_ext) {
            return new WWPO_Error('error', _x('文件类型不为 <strong>%s</strong> 格式', $check_ext, 'wwpo'));
        }

        return true;
    }
}
