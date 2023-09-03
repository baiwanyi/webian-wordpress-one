<?php

/**
 * 微信公众号 API 评论数据管理
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_Comment
{
    /**
     * 打开已群发文章评论
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Comments_management/Image_Comments_Management_Interface.html
     *
     * @param integer $post_id  图文消息 ID
     * @param integer $index    多图文时，用来指定第几篇图文
     */
    static function open($post_id, $index = 0)
    {
        return WWPO_Wechat::curl('cgi-bin/comment/open',  [
            'msg_data_id'   => $post_id,
            'index'         => $index
        ]);
    }

    /**
     * 关闭已群发文章评论
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Comments_management/Image_Comments_Management_Interface.html
     *
     * @param integer $post_id  图文消息 ID
     * @param integer $index    多图文时，用来指定第几篇图文
     */
    static function close($post_id, $index = 0)
    {
        return WWPO_Wechat::curl('cgi-bin/comment/close', [
            'msg_data_id'   => $post_id,
            'index'         => $index
        ]);
    }

    /**
     * 关闭已群发文章评论
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Comments_management/Image_Comments_Management_Interface.html
     *
     * @param integer $post_id  图文消息 ID
     * @param integer $index    多图文时，用来指定第几篇图文
     * @param integer $begin    起始位置
     * @param integer $count    获取数目（大于等于 50 会被拒绝）
     * @param integer $type     评论类型：0普通评论&精选评论，1普通评论，2精选评论
     * @return array
     * {
     *  total   评论总数
     *  comment 评论内容
     *      - user_comment_id   用户评论 ID
     *      - openid            用户 openid
     *      - create_time       评论时间
     *      - content           评论内容
     *      - comment_type      是否精选评论：0为非精选，1为精选
     *      - reply
     *          · content       作者回复内容
     *          · create_time   者回复时间
     * }
     */
    static function list($post_id, $index = 0, $begin = 1, $count = 40, $type = 0)
    {
        if (50 >= $count) {
            $count = 40;
        }

        return WWPO_Wechat::curl('cgi-bin/comment/list',  [
            'msg_data_id'   => $post_id,
            'index'         => $index,
            'begin'         => $begin,
            'count'         => $count,
            'type'          => $type
        ]);
    }

    /**
     * 将评论标记精选
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Comments_management/Image_Comments_Management_Interface.html
     *
     * @param integer $post_id          图文消息 ID
     * @param integer $index            多图文时，用来指定第几篇图文
     * @param integer $user_comment_id  用户评论 ID
     */
    static function markelect($post_id, $index = 0, $user_comment_id = 0)
    {
        return WWPO_Wechat::curl('cgi-bin/comment/markelect', [
            'msg_data_id'       => $post_id,
            'index'             => $index,
            'user_comment_id'   => $user_comment_id
        ]);
    }

    /**
     * 将评论取消精选
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Comments_management/Image_Comments_Management_Interface.html
     *
     * @param integer $post_id          图文消息 ID
     * @param integer $index            多图文时，用来指定第几篇图文
     * @param integer $user_comment_id  用户评论 ID
     */
    static function unmarkelect($post_id, $index = 0, $user_comment_id = 0)
    {
        return WWPO_Wechat::curl('cgi-bin/comment/unmarkelect', [
            'msg_data_id'       => $post_id,
            'index'             => $index,
            'user_comment_id'   => $user_comment_id
        ]);
    }

    /**
     * 删除评论
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Comments_management/Image_Comments_Management_Interface.html
     *
     * @param integer $post_id          图文消息 ID
     * @param integer $index            多图文时，用来指定第几篇图文
     * @param integer $user_comment_id  用户评论 ID
     */
    static function delete($post_id, $index = 0, $user_comment_id = 0)
    {
        return WWPO_Wechat::curl('cgi-bin/comment/delete', [
            'msg_data_id'       => $post_id,
            'index'             => $index,
            'user_comment_id'   => $user_comment_id
        ]);
    }

    /**
     * 回复评论
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Comments_management/Image_Comments_Management_Interface.html
     *
     * @param integer   $post_id            图文消息 ID
     * @param integer   $index              多图文时，用来指定第几篇图文
     * @param integer   $user_comment_id    用户评论 ID
     * @param string    $content            评论内容
     */
    static function reply_add($post_id, $content, $index = 0, $user_comment_id = 0)
    {
        return WWPO_Wechat::curl('cgi-bin/comment/reply/add', [
            'msg_data_id'       => $post_id,
            'index'             => $index,
            'user_comment_id'   => $user_comment_id,
            'content'           => $content
        ]);
    }

    /**
     * 删除回复
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Comments_management/Image_Comments_Management_Interface.html
     *
     * @param integer $post_id          图文消息 ID
     * @param integer $index            多图文时，用来指定第几篇图文
     * @param integer $user_comment_id  用户评论 ID
     */
    static function reply_delete($post_id, $index = 0, $user_comment_id = 0)
    {
        return WWPO_Wechat::curl('cgi-bin/comment/reply/delete', [
            'msg_data_id'       => $post_id,
            'index'             => $index,
            'user_comment_id'   => $user_comment_id
        ]);
    }
}
