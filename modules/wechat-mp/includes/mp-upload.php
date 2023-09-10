<?php

/**
 * 微信公众号 API 媒体上传接口
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_Upload
{
    /**
     * 上传图文消息内的图片获取 URL
     * 请注意，本接口所上传的图片不占用公众号的素材库中图片数量的 5000 个的限制。图片仅支持 jpg/png 格式，大小必须在 1MB 以下。
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Batch_Sends_and_Originality_Checks.html#0
     *
     * @param string $image     上传图片路径
     */
    static function image($image)
    {
        $media = pathinfo($image);

        if (!in_array($media['extension'], ['jpg', 'png'])) {
            return;
        }

        return WWPO_Wechat::curl('cgi-bin/media/uploadimg', [
            'media' => new CURLFile($media['dirname'], 'image/jpg', $media['basename'])
        ]);
    }

    /**
     * 上传图文消息素材
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Batch_Sends_and_Originality_Checks.html#1
     *
     * @param array $articles
     * {
     *  消息参数
     *
     *  以下内容为必填项
     *  - thumb_media_id        图文消息缩略图的 media_id，可以在素材管理-新增素材中获得
     *  - title                 图文消息的标题
     *  - content               图文消息页面的内容，支持HTML标签。具备微信支付权限的公众号，可以使用a标签，其他公众号不能使用，如需插入小程序卡片，可参考下文。
     *
     * 以下内容为非必填项
     *  - author                图文消息的作者
     *  - content_source_url    在图文消息页面点击「阅读原文」后的页面，受安全限制，如需跳转 Appstore，可以使用 itun.es 或 appsto.re 的短链服务，并在短链后增加 #wechat_redirect 后缀。
     *  - digest                图文消息的描述，如本字段为空，则默认抓取正文前 64 个字
     *  - show_cover_pic        是否显示封面，0 为不显示，1 为显示
     *  - need_open_comment     是否打开评论，0 不打开，1 打开
     *  - only_fans_can_comment 是否粉丝才可评论，0所有人可评论，1粉丝才可评论
     * }
     * @return array
     * {
     *  type        媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb），图文消息（news）
     *  media_id    媒体文件/图文消息上传后获取的唯一标识
     *  created_at  媒体文件上传时间戳
     * }
     *
     */
    static function news($articles)
    {
        return WWPO_Wechat::curl('cgi-bin/media/uploadnews', [
            'articles' => $articles
        ]);
    }

    /**
     * 上传素材库的视频用于群发
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Batch_Sends_and_Originality_Checks.html#0
     *
     * @param string $media_id      视频的 media_id，可以在素材管理-新增素材中获得
     * @param string $title         视频标题
     * @param string $description   视频简介
     * @return array
     * {
     *  - type          媒体类型，此处为：video
     *  - media_id      视频 ID
     *  - created_at    上传时间
     * }
     */
    static function video($media_id, $title, $description)
    {
        return WWPO_Wechat::curl('cgi-bin/media/uploadvideo', [
            'media_id'      => $media_id,
            'title'         => $title,
            'description'   => $description
        ]);
    }
}
