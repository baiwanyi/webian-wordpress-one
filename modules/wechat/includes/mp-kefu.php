<?php

/**
 * 微信公众号 API 客服接口
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_Kefu
{
    /**
     * 获取所有客服账号
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Customer_Service_Management.html
     *
     * @return array kf_list
     * {
     *  客服列表
     *  - kf_account            完整客服账号，格式为：账号前缀@公众号微信号
     *  - kf_nick               客服昵称
     *  - kf_id                 客服工号
     *  - kf_headimgurl         客服头像
     *  - kf_wx                 如果客服帐号已绑定了客服人员微信号， 则此处显示微信号
     *  - invite_wx             如果客服帐号尚未绑定微信号，但是已经发起了一个绑定邀请， 则此处显示绑定邀请的微信号
     *  - invite_expire_time    果客服帐号尚未绑定微信号，但是已经发起过一个绑定邀请， 邀请的过期时间，为 unix 时间戳
     *  - invite_status         邀请的状态，waiting：有等待确认，rejected：被拒绝，expired：过期
     * }
     */
    static function list()
    {
        return WWPO_Wechat::curl('cgi-bin/customservice/getkflist');
    }

    /**
     * 获取在线客服
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Customer_Service_Management.html
     *
     * @return array kf_online_list
     * {
     *  客服列表
     *  - kf_account        完整客服账号，格式为：账号前缀@公众号微信号
     *  - status            客服在线状态，目前为：1、web 在线
     *  - kf_id             客服工号
     *  - accepted_case     客服当前正在接待的会话数
     * }
     */
    static function online()
    {
        return WWPO_Wechat::curl('cgi-bin/customservice/getonlinekflist');
    }

    /**
     * 添加客服帐号
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Customer_Service_Management.html
     *
     * @param string $account   完整客服帐号，格式为：帐号前缀@公众号微信号，帐号前缀最多 10 个字符，必须是英文、数字字符或者下划线，后缀为公众号微信号，长度不超过 30 个字符
     * @param string $nickname  客服昵称，最长16个字
     */
    static function add($account, $nickname)
    {
        return WWPO_Wechat::curl('customservice/kfaccount/add', [
            'kf_account'    => $account,
            'nickname'      => $nickname
        ]);
    }

    /**
     * 邀请绑定客服帐号
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Customer_Service_Management.html
     *
     * @param string $account   完整客服帐号，格式为：帐号前缀@公众号微信号
     * @param string $invite    接收绑定邀请的客服微信号
     */
    static function invite($account, $invite)
    {
        return WWPO_Wechat::curl('customservice/kfaccount/inviteworker', [
            'kf_account'    => $account,
            'invite_wx'     => $invite
        ]);
    }

    /**
     * 设置客服信息
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Customer_Service_Management.html
     *
     * @param string $account   完整客服帐号，格式为：帐号前缀@公众号微信号
     * @param string $nickname  客服昵称，最长16个字
     */
    static function update($account, $nickname)
    {
        return WWPO_Wechat::curl('customservice/kfaccount/update', [
            'kf_account'    => $account,
            'nickname'      => $nickname
        ]);
    }

    /**
     * 删除客服帐号
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Customer_Service_Management.html
     *
     * @param string $account 完整客服帐号，格式为：帐号前缀@公众号微信号
     */
    static function delete($account)
    {
        return WWPO_Wechat::curl('customservice/kfaccount/del?kf_account=' . $account);
    }

    /**
     * 上传客服头像
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Customer_Service_Management.html
     *
     * @param string $account   完整客服帐号，格式为：帐号前缀@公众号微信号
     * @param string $image     上传图片路径
     */
    static function uploadavatar($account, $image)
    {
        $media = pathinfo($image);

        if ('jpg' != $media['extension']) {
            return;
        }

        return WWPO_Wechat::curl('customservice/kfaccount/uploadheadimg?kf_account=' . $account, [
            'media' => new \CURLFile($media['dirname'], 'image/jpg', $media['basename'])
        ]);
    }

    /**
     * 获取聊天记录
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Obtain_chat_transcript.html
     *
     * @param integer $starttime    起始时间，unix 时间戳
     * @param integer $endtime      结束时间，unix 时间戳，每次查询时段不能超过 24 小时
     * @param integer $msgid        消息 id 顺序从小到大，从 1 开始
     * @param integer $number       每次获取条数，最多 10000 条
     * @return array    recordlist
     * {
     *  消息列表
     *  - worker    完整客服帐号，格式为：帐号前缀@公众号微信号
     *  - openid    用户标识
     *  - opercode  操作码，2002（客服发送信息），2003（客服接收消息）
     *  - text      聊天记录
     *  - time      操作时间，unix 时间戳
     * }
     */
    static function record($starttime, $endtime, $msgid = 1, $number = 20)
    {
        return WWPO_Wechat::curl('customservice/msgrecord/getmsglist', [
            'starttime' => $starttime,
            'endtime'   => $endtime,
            'msgid'     => $msgid,
            'number'    => $number
        ]);
    }

    /**
     * 创建会话
     * 此接口在客服和用户之间创建一个会话，如果该客服和用户会话已存在，则直接返回0。指定的客服帐号必须已经绑定微信号且在线。
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Session_control.html
     *
     * @param string $account   完整客服帐号，格式为：帐号前缀@公众号微信号
     * @param string $openid    用户 openid
     */
    static function session_create($account, $openid)
    {
        return WWPO_Wechat::curl('customservice/kfsession/create', [
            'kf_account'    => $account,
            'openid'        => $openid
        ]);
    }

    /**
     * 获取客户会话状态
     * 此接口获取一个客户的会话，如果不存在，则kf_account为空。
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Session_control.html
     *
     * @param string $openid    用户 openid
     * @return array
     * {
     *  kf_account  正在接待的客服，为空表示没有人在接待
     *  createtime  会话接入的时间
     * }
     */
    static function session_get($openid)
    {
        return WWPO_Wechat::curl('customservice/kfsession/getsession', [
            'openid' => $openid
        ]);
    }

    /**
     * 获取客服会话列表
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Session_control.html
     *
     * @param string $account   完整客服帐号，格式为：帐号前缀@公众号微信号
     * @return array    sessionlist
     * {
     *  openid      正在接待的用户 openid
     *  createtime  会话接入的时间
     * }
     */
    static function session_list($account)
    {
        return WWPO_Wechat::curl('customservice/kfsession/getsessionlist?kf_account=' . $account);
    }

    /**
     * 获取未接入会话列表
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Customer_Service/Session_control.html
     *
     * @param string $account   完整客服帐号，格式为：帐号前缀@公众号微信号
     * @return array
     * {
     *  count           未接入会话数量
     *  waitcaselist    未接入会话列表，最多返回100条数据，按照来访顺序
     *      - openid        正在等待的用户 openid
     *      - latest_time   用户的最后一条消息的时间
     * }
     */
    static function waitcase()
    {
        return WWPO_Wechat::curl('customservice/kfsession/getwaitcase');
    }
}
