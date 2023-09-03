<?php

/**
 * 微信公众号 API 网络接口
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_Http
{
    /**
     * 获取微信 API 接口 IP 地址
     * 如果公众号基于安全等考虑，需要获知微信服务器的IP地址列表，以便进行相关限制，可以通过该接口获得微信服务器 IP 地址列表或者 IP 网段信息。
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Get_the_WeChat_server_IP_address.html
     *
     * @return array ip_list 微信服务器 IP 地址列表
     */
    static function domain_ip()
    {
        return WWPO_Wechat::curl('cgi-bin/get_api_domain_ip');
    }

    /**
     * 获取微信 callback IP 地址
     * 请开发者确保防火墙、ddos 攻击白名单 IP 内已添加回调 IP，以避免误拦截的情况出现。
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Get_the_WeChat_server_IP_address.html
     *
     * @return array ip_list 微信服务器 IP 地址列表
     */
    static function callback_ip()
    {
        return WWPO_Wechat::curl('cgi-bin/getcallbackip');
    }

    /**
     * 网络检测
     * 为了帮助开发者排查回调连接失败的问题，提供这个网络检测的 API。它可以对开发者 URL 做域名解析，然后对所有 IP 进行一次 ping 操作，得到丢包率和耗时。
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Network_Detection.html
     *
     * @param string $action            执行的检测动作，允许的值：dns（做域名解析）、ping（做ping检测）、all（dns和ping都做）
     * @param string $check_operator    指定平台从某个运营商进行检测，允许的值：CHINANET（电信出口）、UNICOM（联通出口）、CAP（腾讯自建出口）、DEFAULT（根据 ip 来选择运营商）
     *
     * @return array dns
     * {
     *  dns 结果列表
     *  - ip	        解析出来的 ip
     *  - real_operator	ip 对应的运营商
     * }
     * @return array ping
     * {
     *  ping 结果列表
     *  - ip	        ping 的 ip，执行命令为 ping ip –c 1-w 1 -q
     *  - from_operator	ping 的源头的运营商，由请求中的 check_operator 控制
     *  - package_loss  ping 的丢包率，0% 表示无丢包，100% 表示全部丢包。因为目前仅发送一个 ping 包，因此取值仅有 0% 或者 100% 两种可能。
     *  - time          ping 的耗时，取 ping 结果的平均耗时。
     * }
     */
    static function check($action = 'all', $check_operator = 'DEFAULT')
    {
        return WWPO_Wechat::curl('cgi-bin/check', [
            'action'            => $action,
            'check_operator'    => $check_operator
        ]);
    }
}
