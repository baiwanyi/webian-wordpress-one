<?php

/**
 * 微信公众号 API 数据统计
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_Analysis
{
    /**
     * 获取用户增减数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/User_Analysis_Data_Interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date      数据的日期
     *  - user_source   用户的渠道，数值代表的含义如下：
     *      0   代表其他合计
     *      1   代表公众号搜索
     *      17  代表名片分享
     *      30  代表扫描二维码
     *      51  代表支付后关注（在支付完成页）
     *      57  代表文章内账号名称
     *      100 微信广告
     *      161 他人转载
     *      176 专辑页内账号名称
     *  - new_user      新增的用户数量
     *  - cancel_user   取消关注的用户数量，new_user 减去 cancel_user 即为净增用户数量
     * }
     */
    static function user_summary($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getusersummary', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取用户增减数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/User_Analysis_Data_Interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date      数据的日期
     *  - cumulate_user 总用户量
     * }
     */
    static function user_cumulate($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getusercumulate', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取图文群发每日数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date              数据的日期
     *  - msgid                 图文消息 id
     *  - title                 图文消息标题
     *  - int_page_read_user    图文页的阅读人数
     *  - int_page_read_count   图文页的阅读次数
     *  - ori_page_read_user    原文页的阅读人数
     *  - ori_page_read_count   原文页的阅读次数
     *  - share_user            分享的人数
     *  - share_count           分享的次数
     *  - add_to_fav_user       收藏的人数
     *  - add_to_fav_count      收藏的次数
     * }
     */
    static function article_summary($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getarticlesummary', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取图文群发总数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date              数据的日期
     *  - msgid                 图文消息 id
     *  - title                 图文消息标题
     *  - details
     *  {
     *      · stat_date                             统计的日期
     *      · target_user                           送达人数，一般约等于总粉丝数
     *      · int_page_read_user                    图文页的阅读人数
     *      · int_page_read_count                   图文页的阅读次数
     *      · ori_page_read_user                    原文页的阅读人数
     *      · ori_page_read_count                   原文页的阅读次数
     *      · share_user                            分享的人数
     *      · share_count                           分享的次数
     *      · add_to_fav_user                       收藏的人数
     *      · add_to_fav_count                      收藏的次数
     *      · int_page_from_session_read_user       公众号会话阅读人数
     *      · int_page_from_session_read_count      公众号会话阅读次数
     *      · int_page_from_hist_msg_read_user      历史消息页阅读人数
     *      · int_page_from_hist_msg_read_count     历史消息页阅读次数
     *      · int_page_from_feed_read_user          朋友圈阅读人数
     *      · int_page_from_feed_read_count         朋友圈阅读次数
     *      · int_page_from_friends_read_user       好友转发阅读人数
     *      · int_page_from_friends_read_count      好友转发阅读次数
     *      · int_page_from_other_read_user         其他场景阅读人数
     *      · int_page_from_other_read_count        其他场景阅读次数
     *      · feed_share_from_session_user          公众号会话转发朋友圈人数
     *      · feed_share_from_session_cnt           公众号会话转发朋友圈次数
     *      · feed_share_from_feed_user             朋友圈转发朋友圈人数
     *      · feed_share_from_feed_cnt              朋友圈转发朋友圈次数
     *      · feed_share_from_other_user            其他场景转发朋友圈人数
     *      · feed_share_from_other_cnt             其他场景转发朋友圈次数
     *      · int_page_from_kanyikan_read_user      看一看来源阅读人数
     *      · int_page_from_kanyikan_read_count     看一看来源阅读次数
     *      · int_page_from_souyisou_read_user      搜一搜来源阅读人数
     *      · int_page_from_souyisou_read_count     搜一搜来源阅读次数
     *  }
     * }
     */
    static function article_total($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getarticletotal', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取图文统计数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date              数据的日期
     *  - user_source           用户来源：
     *      99999999    全部
     *      0           会话
     *      1           好友
     *      2           朋友圈
     *      3           腾讯微博
     *      4           历史消息页
     *      5           其他
     *      6           看一看
     *      7           搜一搜
     *  - int_page_read_user    图文页的阅读人数
     *  - int_page_read_count   图文页的阅读次数
     *  - ori_page_read_user    原文页的阅读人数
     *  - ori_page_read_count   原文页的阅读次数
     *  - share_user            分享的人数
     *  - share_count           分享的次数
     *  - add_to_fav_user       收藏的人数
     *  - add_to_fav_count      收藏的次数
     * }
     */
    static function user_read($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getuserread', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取图文统计分时数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date              数据的日期
     *  - ref_hour              数据的小时
     *  - user_source           用户来源：
     *      99999999    全部
     *      0           会话
     *      1           好友
     *      2           朋友圈
     *      3           腾讯微博
     *      4           历史消息页
     *      5           其他
     *      6           看一看
     *      7           搜一搜
     *  - int_page_read_user    图文页的阅读人数
     *  - int_page_read_count   图文页的阅读次数
     *  - ori_page_read_user    原文页的阅读人数
     *  - ori_page_read_count   原文页的阅读次数
     *  - share_user            分享的人数
     *  - share_count           分享的次数
     *  - add_to_fav_user       收藏的人数
     *  - add_to_fav_count      收藏的次数
     * }
     */
    static function user_read_hour($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getuserreadhour', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取图文分享转发数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date      数据的日期
     *  - share_scene   分享的场景：1代表好友转发，2代表朋友圈，3代表腾讯微博，255代表其他
     *  - share_user    分享的人数
     *  - share_count   分享的次数
     * }
     */
    static function user_share($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getusershare', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取图文分享转发分时数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date      数据的日期
     *  - ref_hour      数据的小时
     *  - share_scene   分享的场景：1代表好友转发，2代表朋友圈，3代表腾讯微博，255代表其他
     *  - share_user    分享的人数
     *  - share_count   分享的次数
     * }
     */
    static function user_share_hour($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getusersharehour', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取消息发送概况数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date      数据的日期
     *  - msg_type      消息类型，代表含义如下： 1代表文字 2代表图片 3代表语音 4代表视频 6代表第三方应用消息（链接消息）
     *  - msg_user      向公众号发送消息的用户数
     *  - msg_count     向公众号发送消息的消息总数
     * }
     */
    static function up_stream_msg($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getupstreammsg', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取消息分送分时数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date      数据的日期
     *  - ref_hour      数据的小时
     *  - msg_type      消息类型，代表含义如下： 1代表文字 2代表图片 3代表语音 4代表视频 6代表第三方应用消息（链接消息）
     *  - msg_user      向公众号发送消息的用户数
     *  - msg_count     向公众号发送消息的消息总数
     * }
     */
    static function up_stream_msg_hour($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getupstreammsghour', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取消息发送周数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date      数据的日期
     *  - msg_type      消息类型，代表含义如下： 1代表文字 2代表图片 3代表语音 4代表视频 6代表第三方应用消息（链接消息）
     *  - msg_user      向公众号发送消息的用户数
     *  - msg_count     向公众号发送消息的消息总数
     * }
     */
    static function up_stream_msg_week($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getupstreammsgweek', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取消息发送月数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date      数据的日期
     *  - msg_type      消息类型，代表含义如下： 1代表文字 2代表图片 3代表语音 4代表视频 6代表第三方应用消息（链接消息）
     *  - msg_user      向公众号发送消息的用户数
     *  - msg_count     向公众号发送消息的消息总数
     * }
     */
    static function up_stream_msg_month($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getupstreammsgmonth', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取消息发送分布数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date          数据的日期
     *  - count_interval    当日发送消息量分布的区间，0代表 “0”，1代表“1-5”，2代表“6-10”，3代表“10次以上”
     *  - msg_user          向公众号发送消息的用户数
     * }
     */
    static function up_stream_msg_dist($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getupstreammsgdist', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取消息发送分布周数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date          数据的日期
     *  - count_interval    当日发送消息量分布的区间，0代表 “0”，1代表“1-5”，2代表“6-10”，3代表“10次以上”
     *  - msg_user          向公众号发送消息的用户数
     * }
     */
    static function up_stream_msg_dist_week($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getupstreammsgdistweek', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取消息发送分布月数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date          数据的日期
     *  - count_interval    当日发送消息量分布的区间，0代表 “0”，1代表“1-5”，2代表“6-10”，3代表“10次以上”
     *  - msg_user          向公众号发送消息的用户数
     * }
     */
    static function up_stream_msg_dist_month($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getupstreammsgdistmonth', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取接口分析数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Analytics_API.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date          数据的日期
     *  - callback_count    通过服务器配置地址获得消息后，被动回复用户消息的次数
     *  - fail_count        上述动作的失败次数
     *  - total_time_cost   总耗时，除以 callback_count 即为平均耗时
     *  - max_time_cost     最大耗时
     * }
     */
    static function interface_summary($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getinterfacesummary', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }

    /**
     * 获取接口分析分时数据
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Analytics_API.html
     *
     * @param string $begin_date    获取数据的起始日期
     * @param string $end_date      获取数据的结束日期
     * @return array
     * {
     *  list：返回数据
     *  - ref_date          数据的日期
     *  - ref_hour          数据的小时
     *  - callback_count    通过服务器配置地址获得消息后，被动回复用户消息的次数
     *  - fail_count        上述动作的失败次数
     *  - total_time_cost   总耗时，除以 callback_count 即为平均耗时
     *  - max_time_cost     最大耗时
     * }
     */
    static function interface_summary_hour($begin_date, $end_date)
    {
        return WWPO_Wechat::curl('datacube/getinterfacesummaryhour', [
            'begin_date'    => $begin_date,
            'end_date'      => $end_date
        ]);
    }
}
