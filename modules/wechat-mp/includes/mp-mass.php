<?php

/**
 * 微信公众号 API 群发接口
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_Mass
{
    /**
     * 根据标签进行群发
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Batch_Sends_and_Originality_Checks.html
     *
     * @param array     $data       消息参数
     * @param integer   tag_id      标签 ID，为空则为群发所有
     * @param string    $msgtype    消息类型
     */
    static function sendall($data, $tag_id = 0, $msgtype = 'mpnews')
    {
        $body = self::format($data, $msgtype);

        if (empty($tag_id)) {
            $body['filter']['is_to_all']    = true;
        } else {
            $body['filter']['is_to_all']    = false;
            $body['filter']['tag_id']       = $tag_id;
        }

        return WWPO_Wechat::curl('cgi-bin/message/mass/sendall', $body);
    }

    /**
     * 根据 OpenID 列表群发
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Batch_Sends_and_Originality_Checks.html
     *
     * @param array     $data       消息参数
     * @param array     $openids    需要群发的用户 openid 列表
     * @param string    $msgtype    消息类型
     */
    static function send($data, $openids, $msgtype = 'mpnews')
    {
        $body = self::format($data, $msgtype);
        $body['touser'] = $openids;

        return WWPO_Wechat::curl('cgi-bin/message/mass/send', $body);
    }

    /**
     * 删除已群发内容
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Batch_Sends_and_Originality_Checks.html
     *
     * @param integer   $media_id   发送出去的消息 ID
     * @param integer   $index      要删除的文章在图文消息中的位置，第一篇编号为 1，该字段不填或填 0 会删除全部文章
     */
    static function delete($media_id, $index = 0)
    {
        return WWPO_Wechat::curl('cgi-bin/message/mass/delete', [
            'msg_id'        => $media_id,
            'article_idx'   => $index
        ]);
    }

    /**
     * 预览群发内容
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Batch_Sends_and_Originality_Checks.html
     *
     * @param array     $data       消息参数
     * @param array     $touser     需要预览的用户：wechat 微信号，openid 用户 ID
     * @param string    $msgtype    消息类型
     */
    static function preview($data, $touser, $msgtype = 'mpnews')
    {
        $body = self::format($data, $msgtype);

        if (isset($touser['wechat'])) {
            $body['towxname'] = $touser['wechat'];
        }

        if (isset($touser['openid'])) {
            $body['touser'] = $touser['openid'];
        }

        return WWPO_Wechat::curl('cgi-bin/message/mass/preview', $body);
    }

    /**
     * 查询群发消息发送状态
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Batch_Sends_and_Originality_Checks.html
     *
     * @param integer $media_id 群发消息后返回的消息 id
     * @return array
     * {
     *  msg_id      群发消息后返回的消息 id
     *  msg_status  消息发送后的状态，SEND_SUCCESS 表示发送成功，SENDING 表示发送中，SEND_FAIL 表示发送失败，DELETE 表示已删除
     * }
     */
    static function status($media_id)
    {
        return WWPO_Wechat::curl('cgi-bin/message/mass/get', ['msg_id' => $media_id]);
    }

    /**
     * 查询/设置控制群发速度
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Batch_Sends_and_Originality_Checks.html
     *
     * @param integer $speed 群发速度的级别，是一个 0 到 4 的整数，数字越大表示群发速度越慢。不设置则是获取当前群发速度
     * @return array
     * {
     *  speed       群发速度的级别
     *  realspeed   群发速度的真实值 单位：万/分钟
     * }
     */
    static function speed($speed = null)
    {
        if (isset($speed)) {
            return WWPO_Wechat::curl('cgi-bin/message/speed/set', ['speed' => $speed]);
        }

        return WWPO_Wechat::curl('cgi-bin/message/speed/get');
    }

    /**
     * 设定发送消息内容格式
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Batch_Sends_and_Originality_Checks.html
     *
     * @param array     $data
     * {
     *  消息参数
     *  1、图文消息：mpnews
     *  - media_id              图文内容 ID
     *  - send_ignore_reprint   图文消息被判定为转载时，是否继续群发。 1为继续群发（转载），0为停止群发。 该参数默认为0。
     *
     *  2、文本消息：text
     *  - content   文本内容
     *
     *  3、语音消息：voice
     *  - media_id  语音内容 ID
     *
     *  4、图片消息：image
     *  - media_ids     图片内容 ID 列表
     *  - recommend     推荐语，不填则默认为「分享图片」
     *  - need_open_comment
     *  - only_fans_can_comment
     *
     *  5、视频消息：mpvideo
     *  - media_id      视频内容 ID
     *  - title         视频标题
     *  - description   视频简介
     *
     *  6、微信卡券：wxcard
     *  - media_id      卡券内容 ID
     * }
     * @param string    $msgtype    群发的消息类型，图文消息为 mpnews，文本消息为 text，语音为 voice，音乐为 music，图片为 image，视频为 video，卡券为 wxcard
     */
    static function format($data, $msgtype = 'mpnews')
    {
        // 设定消息类型
        $body['msgtype'] = $msgtype;

        if ('mpnews' == $msgtype) {
            $body['mpnews']['media_id']     = $data['media_id'];
            $body['send_ignore_reprint']    = $data['send_ignore_reprint'] ?? 0;
        }

        if ('text' == $msgtype) {
            $body['text']['content'] = $data['content'];
        }

        if ('voice' == $msgtype) {
            $body['voice']['media_id'] = $data['media_id'];
        }

        if ('image' == $msgtype) {
            $body['images']['media_ids']                = $data['media_ids'];
            $body['images']['recommend']                = $data['recommend'] ?? '';
            $body['images']['need_open_comment']        = $data['need_open_comment'] ?? 1;
            $body['images']['only_fans_can_comment']    = $data['only_fans_can_comment'] ?? 0;
        }

        if ('mpvideo' == $msgtype) {
            $video = WWPO_Wechat_Upload::video($data['media_id'], $data['title'], $data['description']);
            $body['mpvideo']['media_id'] = $video['media_id'];
        }

        if ('wxcard' == $msgtype) {
            $body['wxcard']['card_id'] = $data['media_id'];
        }

        return $body;
    }
}
