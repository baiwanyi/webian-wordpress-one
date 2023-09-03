<?php

/**
 * 微信公众号 API 模版消息接口
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_Tmplmsg
{
    /**
     * 获取模板列表
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Template_Message_Interface.html
     *
     * @return array template_list
     * {
     *  模版列表
     *   - template_id         模板 ID
     *   - title               模板标题
     *   - primary_industry    模板所属行业的一级行业
     *   - deputy_industry     模板所属行业的二级行业
     *   - content             模板内容
     *   - example             模板示例
     * }
     */
    static function list()
    {
        return WWPO_Wechat::curl('cgi-bin/template/get_all_private_template');
    }

    /**
     * 删除模板
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Template_Message_Interface.html
     *
     * @param string $template_id   需要删除的模版 ID
     */
    static function delete($template_id)
    {
        return WWPO_Wechat::curl('cgi-bin/template/del_private_template', ['template_id' => $template_id]);
    }

    /**
     * 发送模板消息
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Template_Message_Interface.html
     *
     * @param string $template_id   使用的模版 ID
     * @param string $openid   发送的用户 ID
     * @param array $data
     * {
     *  发送数据
     *  first       头部信息
     *  keyword     关键字（1~N）
     *  remark      备注
     *
     *  以上字段均有两个选项
     *  value   字段内容
     *  color   字体颜色，不填写则为黑色
     * }
     * @param array $callback
     * {
     *  转跳地址，优先跳转至小程序，当用户的微信客户端版本不支持跳小程序时，将会跳转至 url
     *  url   模板跳转链接
     *  app   跳小程序所需数据
     *  - appid 所需跳转到的小程序 appid
     *  - page  所需跳转到小程序的具体页面路径
     * }
     */
    static function send($template_id, $openid, $data, $callback = [])
    {
        $body = [
            'touser'        => $openid,
            'template_id'   => $template_id,
            'data'          => $data
        ];

        if (isset($callback['url'])) {
            $body['url'] = $callback['url'];
        }

        if (isset($callback['app'])) {
            $body['miniprogram'] = [
                'appid'     => $callback['app']['appid'],
                'pagepath'  => $callback['app']['page']
            ];
        }

        return WWPO_Wechat::curl('cgi-bin/message/template/send', $body);
    }
}
