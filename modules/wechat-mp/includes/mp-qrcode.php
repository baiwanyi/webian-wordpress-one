<?php

/**
 * 微信公众号 API 二维码接口
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_QRCode
{
    /**
     * 生成带参数的二维码
     *
     * @param integer|string    $scene  场景值
     * @param integer           $expire 过期时间，为空则为永久二维码
     * @return array
     * {
     *  ticket          获取的二维码ticket，凭借此ticket可以在有效时间内换取二维码。
     *  expire_seconds  该二维码有效时间，以秒为单位。 最大不超过 2592000（即30天）。
     *  url             二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片
     * }
     */
    static function create($scene, $expire = null)
    {
        if (isset($expire)) {

            if ('2592000' <= $expire) {
                $expire = '2592000';
            }

            $body['expire_seconds'] = $expire;

            $prefix = 'QR_';
        } else {
            $prefix = 'QR_LIMIT_';
        }

        if (is_numeric($scene)) {
            $body['action_name'] = $prefix . 'SCENE';
            $body['action_info']['scene']['scene_id'] = $scene;
        } else {
            $body['action_name'] = $prefix . 'STR_SCENE';
            $body['action_info']['scene']['scene_str'] = $scene;
        }

        return WWPO_Wechat::curl('cgi-bin/qrcode/create', $body);
    }

    /**
     *  通过 ticket 换取二维码，返回生成的二维码图片
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Account_Management/Generating_a_Parametric_QR_Code.html
     */
    static function show($scene, $expire = 0)
    {
        $create = self::create($scene, $expire);

        if (isset($create['ticket'])) {
            return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $create['ticket'];
        }

        return $create;
    }

    /**
     * 通过 ticket 换取二维码，返回生成的二维码地址
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Account_Management/Generating_a_Parametric_QR_Code.html
     */
    static function url($scene, $expire = 0)
    {
        $create = self::create($scene, $expire);

        if (isset($create['url'])) {
            return $create['url'];
        }
    }
}
