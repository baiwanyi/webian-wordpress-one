<?php

/**
 * 微信公众号 JSSDK 调用
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_Jssdk
{
    /**
     * Undocumented function
     *
     * @return void
     */
    static function apply()
    {
        wp_enqueue_script('wechat-jssdk', '//res.wx.qq.com/open/js/jweixin-1.6.0.js', [], null, true);
        wp_localize_script('wechat-jssdk', 'wxjssdk', self::jsapi_config());
    }

    /**
     * Undocumented function
     *
     */
    static function jsapi_config()
    {
        $jsapi_ticket = self::ticket();

        /** 判断 JSAPI_TICKET 是否存在错误代码，正确获取 JSAPI_TICKET 已剔除 errcode 字段 */
        if (isset($jsapi_ticket['errcode'])) {
            return;
        }

        $noncestr   = wwpo_random(16);
        $timestamp  = NOW;
        $signature  = '';

        /* 将 token、timestamp（时间戳）、nonce（随机数）三个参数进行字典序排序 */
        $jsapi_configs = [
            'noncestr'      => $noncestr,
            'jsapi_ticket'  => $jsapi_ticket['ticket'],
            'timestamp'     => $timestamp,
            'url'           => wwpo_get_page_url()
        ];

        ksort($jsapi_configs);

        foreach ($jsapi_configs as $config_key => $config_value) {
            $signature .= sprintf('&%s=%s', $config_key, $config_value);
        }

        unset($jsapi_configs['jsapi_ticket']);
        unset($jsapi_configs['url']);

        /* 将三个参数字符串拼接成一个字符串进行 sha1 加密 */
        $jsapi_configs['appid']     = $jsapi_ticket['appid'];
        $jsapi_configs['signature'] = sha1(ltrim($signature, '&'));

        return $jsapi_configs;
    }

    /**
     * 获取微信 JS API 的临时票据
     *
     * @since 1.0.0
     * @param string $type 票据类型，默认：jsapi
     *  - wx_card   微信卡券
     *  - jsapi     签名
     *
     * JSAPI_TICKET 的存储至少要保留 512 个字符空间。
     * JSAPI_TICKET 的有效期目前为 2 个小时，需定时刷新，重复获取将导致上次获取的 access token 失效。
     */
    static function ticket($type = 'jsapi')
    {
        // 获取系统保存的微信设置信息
        $option = get_option(WWPO_Wechat::KEY_WECHAT);

        // 设定票据保存 KEY 名
        $option_ticket  = $type . '_ticket';
        $option_expires = $type . 'ticket_expires';

        /** 获取系统存储 JSAPI_TICKET 信息 */
        $jsapi_ticket = [
            'appid'     => $option['appid'],
            'ticket'    => $option[$option_ticket] ?? 0
        ];

        /** 判断当系统的 JSAPI_TICKET 信息为空或者有效时间过期（当前时间比有效时间多 7200 秒），则从服务器获取最新 JSAPI_TICKET 信息 */
        if (empty($jsapi_ticket['ticket']) || (NOW - $option[$option_expires]) > 7200) {

            // 获取 ACCESS_TOKEN
            $access_token = WWPO_Wechat::access_token();

            /** 判断获取失败，返回完整错误信息，正确获取 ACCESS_TOKEN 已剔除 errcode 字段 */
            if (isset($access_token['errcode'])) {
                return $access_token;
            }

            /** 获取微信服务器 JSAPI_TICKET */
            $response = wwpo_curl(sprintf('%1$s/cgi-bin/ticket/getticket?access_token=%2$s&type=%3$s', WWPO_Wechat::DOMAIN, $access_token, $type));

            /** 判断 JSAPI_TICKET 获取成功，执行写入数据库操作，否则返回完整错误信息 */
            if (empty($response['errcode'])) {

                // 设定获取的 JSAPI_TICKET
                $jsapi_ticket['ticket'] = $response['ticket'];

                // 设定保存时间，更新数据库
                $option[$option_ticket]  = $jsapi_ticket['ticket'];
                $option[$option_expires] = NOW;

                update_option(WWPO_Wechat::KEY_WECHAT, $option);

                // 返回获取的 JSAPI_TICKET
                return $jsapi_ticket;
            }

            // 返回完整 JSAPI_TICKET 信息
            return $response;
        }

        // 返回系统保存的 JSAPI_TICKET
        return $jsapi_ticket;
    }
}
