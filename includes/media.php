<?php

/**
 * 媒体文件地址转换成 OSS 地址函数
 *
 * @since 1.0.0
 * @param string $object
 * @param string $style
 */
function wwpo_oss_cdnurl($object, $style = null)
{
    if (!class_exists('wwpo_alioss')) {
        return $object;
    }

    $alioss = new WWPO_Alioss();

    return $alioss->signurl($object, $style);
}

/**
 * Undocumented function
 *
 * @param [type] $object
 * @return void
 */
function wwpo_oss_delete_object($object) {

    if (!class_exists('wwpo_alioss')) {
        return;
    }

    if (empty($object)) {
        return;
    }

    $alioss = new WWPO_Alioss();

    if (empty($alioss->ossdata['enable']) || empty($alioss->ossclient)) {
        return;
    }

    $alioss->ossclient->deleteObject($alioss->bucket, $object);
}

/**
 * Undocumented function
 *
 * @param [type] $object
 * @param [type] $content
 * @return void
 */
function wwpo_oss_upload_object($object, $content) {

    if (!class_exists('wwpo_alioss')) {
        return;
    }

    if (empty($object) || empty($content)) {
        return;
    }

    $alioss = new WWPO_Alioss();

    if (empty($alioss->ossdata['enable']) || empty($alioss->ossclient)) {
        return;
    }

    $alioss->ossclient->putObject($alioss->bucket, $object, $content);
}
