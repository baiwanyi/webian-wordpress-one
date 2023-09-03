<?php

/**
 * 微信公众号 API 事件接口
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_Event
{
    /**
     * 事件消息
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_event_pushes.html
     *
     * @param object    $object 消息内容
     * @param array     $post   设定参数
     *
     */
    static function call($object, $post)
    {
        /**
         * 微信事件类型：
         * 	- subscribe             订阅事件
         * 	- unsubscribe           取消订阅
         * 	- scan                  扫描带参数二维码事件
         * 	- location              上报地理位置事件
         * 	- click                 自定义菜单事件（点击菜单拉取消息时的事件推送）
         * 	- view                  自定义菜单事件（点击菜单跳转链接时的事件推送）
         * 	- scancode_push         自定义菜单事件（扫码推事件的事件推送）
         * 	- scancode_waitmsg      自定义菜单事件（扫码推事件且弹出“消息接收中”提示框的事件推送）
         * 	- pic_sysphoto          自定义菜单事件（弹出系统拍照发图的事件推送）
         * 	- pic_photo_or_album    自定义菜单事件（弹出拍照或者相册发图的事件推送）
         * 	- pic_weixin            自定义菜单事件（弹出微信相册发图器的事件推送）
         * 	- location_select       自定义菜单事件（弹出地理位置选择器的事件推送）
         */
        $event      = strtolower($object->Event);
        $event_key  = strtolower($object->EventKey);

        /** 订阅事件 */
        if ('subscribe' == $event) {
            self::subscribe($object, $post['subscribe']);
        }

        /**
         * 微信事件动作：wwpo_wechat_event
         *
         * @since 1.0.0
         * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_event_pushes.html
         *
         * @param object    $object     微信消息内容
         * @param string    $event      事件类型
         * @param string    $event_key  事件 key 值
         */
        if (has_action('wwpo_wechat_event')) {

            /**
             * 扫描带参数二维码事件：scan
             *  - EventKey  事件 KEY 值，qrscene_为前缀，后面为二维码的参数值
             *  - Ticket    二维码的 ticket，可用来换取二维码图片
             *
             * 上报地理位置事件：location
             *  - Latitude  地理位置纬度
             *  - Longitude 地理位置经度
             *  - Precision 地理位置精度
             *
             * 自定义菜单事件
             * 1、转跳链接：view
             *  - EventKey  事件 EY 值，设置的跳转 URL
             * 2、点击事件：click
             *  - EventKey  事件 EY 值，与自定义菜单接口中 KEY 值对应
             * 3、扫码推事件：scancode_push/scancode_waitmsg
             *  - EventKey      事件 KEY 值，由开发者在创建菜单时设定
             *  - ScanCodeInfo  扫描信息
             *  - ScanType      扫描类型，一般是 qrcode
             *  - ScanResult    扫描结果，即二维码对应的字符串信息
             * 4、弹出系统拍照发图的事件：pic_sysphoto/pic_photo_or_album/pic_weixin
             *  - EventKey      事件 KEY 值，由开发者在创建菜单时设定
             *  - SendPicsInfo  发送的图片信息
             *  - Count         发送的图片数量
             *  - PicList       图片列表
             *  - PicMd5Sum     图片的 MD5 值，开发者若需要，可用于验证接收到图片
             * 5、弹出地理位置选择器：location_select
             *  - EventKey          事件 KEY 值，由开发者在创建菜单时设定
             *  - SendLocationInfo  发送的位置信息
             *  - Location_X        X 坐标信息
             *  - Location_Y        Y 坐标信息
             *  - Scale             精度，可理解为精度或者比例尺、越精细的话 scale 越高
             *  - Label             地理位置的字符串信息
             *  - Poiname           朋友圈 POI 的名字，可能为空
             * 6、点击菜单跳转小程序：view_miniprogram
             *  - EventKey 事件 KEY 值，跳转的小程序路径
             */
            do_action('wwpo_wechat_event', $object, $event, $event_key);
            return;
        }
    }

    /**
     * 订阅回复消息
     *
     * @since 1.0.0
     *
     * @param object    $object     微信用户信息
     * @param array     $subscribe
     * {
     *  订阅消息设置
     *  - msgtype   消息类型
     *  - msgdata   消息内容
     *  - ads       是否显示推广信息
     *  - register  是否显示用户注册链接
     * }
     */
    static function subscribe($object, $subscribe = [])
    {
        /**
         * 微信订阅事件动作：wwpo_wechat_subscribe
         *
         * @since 1.0.0
         *
         * @param object $object
         */
        if (has_action('wwpo_wechat_subscribe')) {
            do_action('wwpo_wechat_subscribe', $object);
            return;
        }

        /** 判断订阅消息设置 */
        if (empty($subscribe)) {
            return;
        }

        $content = $subscribe['msgdata'];

        /** 图文消息 */
        if ('news' == $subscribe['msgtype']) {

            // 显示广告
            if (isset($autoreply['ads'])) {
                $content[] = [];
            }

            // 显示用户注册
            if (isset($autoreply['register'])) {
                $content[] = [
                    'Title'         => '用户注册',
                    'Description'   => '',
                    'PicUrl'        => '',
                    'Url'           => esc_url(add_query_arg('openid', $object->FromUserName, wp_registration_url()))
                ];
            }

            return WWPO_Wechat_Template::news($object, $content);
        }

        return call_user_func(['Template', $subscribe['msgtype']], $object, $content);
    }

    /**
     * 智能消息回复
     *
     * @since 1.0.0
     *
     * @param  object   $object     微信用户信息
     * @param  string   $keyword    回复关键字
     * @param  array    $smart
     * {
     *  回复设置
     *  - default   默认信息
     * }
     */
    static function smart($object, $keyword, $smart = [])
    {
        global $wpdb;

        /** 搜索数据库关键字 */
        $reply = [];

        /** 判断搜索结果为空者返回默认信息 */
        if (empty($reply)) {
            return WWPO_Wechat_Template::text($object, $keyword);
        }

        return call_user_func(['Template', $reply['msgtype']], $object, $reply['msgdata']);
    }
}
