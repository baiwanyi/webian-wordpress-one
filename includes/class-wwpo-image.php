<?php

class WWPO_Image
{
    /**
     * 获取远程图片函数
     *
     * @since 1.0.0
     * @param string  $image_url 图片地址
     */
    static function remote($image_url, $subdir = null, $ext = null)
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
     * 保存文章内容中远程图片到本地
     *
     * @since 1.0.0
     * @param string $post_content  文章内容
     */
    static function replace($post_content)
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
            $image_src = self::remote($image);

            // 判断保存成功，获取图片地址
            if (empty($image_src)) {
                continue;
            }

            // 设定图片地址和 OSS 样式
            $image_file = sprintf('<img src="%1$s?x-oss-process=style/post" class="thumb">', $image_src);

            // 替换文章内容中图片
            $post_content = str_replace($matchall[0][$key], $image_file, $post_content);
        }

        // 返回文章内容
        return $post_content;
    }
}
