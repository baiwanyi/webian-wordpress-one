<?php

/**
 * 微信公众号 API 素材管理接口
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_Material
{
    /**
     * 新增/获取临时素材
     * 1、临时素材 media_id 是可复用的。
     * 2、媒体文件在微信后台保存时间为 3 天，即 3 天后 media_id 失效。
     * 3、上传临时素材的格式、大小限制与公众平台官网一致。
     * 4、媒体文件类型为空则是获取临时素材
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/New_temporary_materials.html
     *
     * @param string $media     媒体文件路径
     * @param string $type      媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @return array
     * {
     *  type        媒体文件类型
     *  media_id    媒体文件上传后获取标识
     *  created_at  媒体文件上传时间戳
     * }
     */
    static function temp($meida, $type = null)
    {
        if (empty($type)) {
            return WWPO_Wechat::curl('cgi-bin/media/get?meida_id=' . $meida);
        }

        $media = pathinfo($meida);

        return WWPO_Wechat::curl('cgi-bin/media/upload?type=' . $type, [
            'media' => new \CURLFile($media['dirname'], 'image/jpg', $media['basename'])
        ]);
    }

    /**
     * 高清语音素材获取接口
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/New_temporary_materials.html
     *
     * @param string $meida_id  媒体文件 ID，即 uploadVoice 接口返回的 serverID
     * @return string
     */
    static function temp_hd($meida_id)
    {
        return WWPO_Wechat::curl('cgi-bin/media/get/jssdk?meida_id=' . $meida_id);
    }

    /**
     * 新增永久图文素材
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Adding_Permanent_Assets.html
     *
     * @param array $articles
     * {
     *  消息参数
     *
     *  以下内容为必填项
     *  - thumb_media_id        图文消息缩略图的 media_id，可以在素材管理-新增素材中获得
     *  - title                 图文消息的标题
     *  - author                图文消息的作者
     *  - content               图文消息页面的内容，支持HTML标签。具备微信支付权限的公众号，可以使用a标签，其他公众号不能使用，如需插入小程序卡片，可参考下文。
     *  - show_cover_pic        是否显示封面，0 为不显示，1 为显示
     *  - content_source_url    在图文消息页面点击「阅读原文」后的页面，受安全限制，如需跳转 Appstore，可以使用 itun.es 或 appsto.re 的短链服务，并在短链后增加 #wechat_redirect 后缀。
     *
     * 以下内容为非必填项
     *  - digest                图文消息的描述，如本字段为空，则默认抓取正文前 64 个字
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
        return WWPO_Wechat::curl('cgi-bin/material/add_news', [
            'articles' => $articles
        ]);
    }

    /**
     * 新增其他类型永久素材
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Adding_Permanent_Assets.html
     *
     * @param string $type      媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @param string $media     媒体文件路径
     * @return void
     */
    static function add($type, $media)
    {
        $body = [
            'type'  => $type,
            'media' => $media
        ];

        if ('video' == $type) {
            $body['description'] = [
                'title'         => $media['title'],
                'introduction'  => $media['description']
            ];
        }

        return WWPO_Wechat::curl('cgi-bin/material/add_material', $body);
    }

    /**
     * 获取永久素材
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Getting_Permanent_Assets.html
     *
     * @param string $media_id 获取的素材的 media_id
     */
    static function get($media_id)
    {
        return WWPO_Wechat::curl('cgi-bin/material/get_material', ['media_id' => $media_id]);
    }

    /**
     * 删除永久素材
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Deleting_Permanent_Assets.html
     *
     * @param string $media_id 需要删除的 media_id
     */
    static function delete($media_id)
    {
        return WWPO_Wechat::curl('cgi-bin/material/del_material', ['media_id' => $media_id]);
    }

    /**
     * 修改图文素材
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Editing_Permanent_Rich_Media_Assets.html
     *
     * @param string    $media_id   需要修改的图文 media_id
     * @param integer   $index      要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义），第一篇为 0
     * @param array     $articles
     * {
     *  图文内容
     *  - title                 标题
     *  - thumb_media_id        图文消息的封面图片素材id（必须是永久 mediaID ）
     *  - author                作者
     *  - digest                图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
     *  - show_cover_pic        是否显示封面，0为false，即不显示，1为true，即显示
     *  - content               图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS
     *  - content_source_url    图文消息的原文地址，即点击“阅读原文”后的URL
     * }
     */
    static function update($media_id, $articles, $index = 0)
    {
        return WWPO_Wechat::curl('cgi-bin/material/update_news', [
            'media_id'  => $media_id,
            'index'     => $index,
            'articles'  => $articles
        ]);
    }

    /**
     * 获取素材总数
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Get_the_total_of_all_materials.html
     *
     * @return array
     * {
     *  voice_count 语音总数量
     *  video_count 视频总数量
     *  image_count 图片总数量
     *  news_count  图文总数量
     * }
     */
    static function count()
    {
        return WWPO_Wechat::curl('cgi-bin/material/get_materialcount');
    }

    /**
     * 获取素材列表
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Get_materials_list.html
     *
     * @param string    $type       素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param integer   $offset     从全部素材的该偏移位置开始返回，0表示从第一个素材返回
     * @param integer   $count      返回素材的数量，取值在1到20之间
     *
     * @return array
     * {
     *  公共参数
     *  - total_count   该类型的素材的总数
     *  - item_count    本次调用获取的素材的数量
     *  - item          列表内容
     *
     *  永久图文消息素材列表
     *  - media_id
     *  - content\news_item 列表内容
     *      · title                 图文消息的标题
     *      · thumb_media_id        图文消息的封面图片素材id（必须是永久mediaID）
     *      · show_cover_pic        是否显示封面，0为false，即不显示，1为true，即显示
     *      · author                作者
     *      · digest                图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
     *      · content               图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS
     *      · url                   图文页的 URL
     *      · content_source_url    图文消息的原文地址
     *  - update_time   素材的最后更新时间
     *
     *  其他类型消息素材列表
     *  - media_id
     *  - name          文件名称
     *  - update_time   素材的最后更新时间
     *  - url           图片的 URL
     * }
     */
    static function list($type, $offset = 0, $count = 20)
    {
        return WWPO_Wechat::curl('cgi-bin/material/batchget_material', [
            'type'      => $type,
            'offset'    => $offset,
            'count'     => $count
        ]);
    }
}
