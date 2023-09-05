<?php

/**
 * 阿里云 OSS 开启媒体文件操作函数
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Alioss
 */

/**
 * 同步上传 OSS 媒体文件函数
 *
 * @since 1.0.0
 * @param integer $post_id
 */
function wwpo_oss_upload_attachment($post_id)
{
    if (empty($post_id)) {
        return;
    }

    /**
     * 声明 OSS 类
     *
     * @var WWPO_Alioss
     */
    $alioss = new WWPO_Alioss();

    if (empty($alioss->ossdata['enable']) || empty($alioss->ossclient)) {
        return;
    }

    // 获取本地文件目录和文件
    $uploads        = wp_get_upload_dir();
    $media_file     = get_post_meta($post_id, '_wp_attached_file', true);
    $upload_file    = $uploads['basedir'] . DIRECTORY_SEPARATOR . $media_file;

    // 上传文件到 OSS
    $alioss->ossclient->uploadFile($alioss->bucket, $media_file, $upload_file);
}
add_action('add_attachment', 'wwpo_oss_upload_attachment');

/**
 * 删除文件时同时删除 OSS 媒体文件函数
 *
 * @since 1.0.0
 * @param integer $post_id
 */
function wwpo_oss_delete_attachment($post_id)
{
    if (empty($post_id)) {
        return;
    }

    /**
     * 声明 OSS 类
     *
     * @var WWPO_Alioss
     */
    $alioss = new WWPO_Alioss();

    if (empty($alioss->ossdata['enable']) || empty($alioss->ossclient)) {
        return;
    }

    // 获取本地文件名
    $object = get_post_meta($post_id, '_wp_attached_file', true);

    // 执行删除操作
    $alioss->ossclient->deleteObject($alioss->bucket, $object);
}
add_action('delete_attachment', 'wwpo_oss_delete_attachment');

/**
 * 媒体列表图片地址修改函数
 *
 * @since 1.0.0
 * @param boolean   $downsize       是否裁剪图像，默认：false
 * @param integer   $attachment_id  媒体 ID
 * @param string    $size           缩略图尺寸
 */
function wwpo_oss_image_downsize($downsize, $attachment_id, $size)
{
    /** 判断缩略图尺寸为数组时，默认为 ：thumbnail */
    if (is_array($size)) {
        $size = 'thumbnail';
    }

    // 设定 OSS 域名的文件地址
    $object     = get_post_meta($attachment_id, '_wp_attached_file', true);
    $oss_cdnurl = wwpo_oss_cdnurl($object, $size);

    if (empty($oss_cdnurl)) {
        return false;
    }

    // 返回文件地址
    return [$oss_cdnurl, 0, 0, $downsize];
}
add_filter('image_downsize', 'wwpo_oss_image_downsize', 10, 3);

/**
 * 设定获取文件地址的 URL 函数
 *
 * @since 1.0.0
 * @param string $attachment_url
 */
function wwpo_oss_get_attachment_url($attachment_url)
{
    // 获取本地上传目录
    $uploads = wp_get_upload_dir();

    // 设定 OSS 域名的文件地址，默认样式：original
    $object = str_replace($uploads['baseurl'], '', $attachment_url);

    $oss_cdnurl = wwpo_oss_cdnurl($object, 'medium');

    if (empty($oss_cdnurl)) {
        return $attachment_url;
    }

    // 返回文件 URL
    return $oss_cdnurl;
}
add_filter('wp_get_attachment_url', 'wwpo_oss_get_attachment_url');
