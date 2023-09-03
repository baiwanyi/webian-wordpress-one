<?php

/**
 * 微信公众号 API 消息模版
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_Template
{
    static function text($object, $content = '')
    {
        return sprintf('<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>', $object->FromUserName, $object->ToUserName, NOW, $content);
    }

    static function customer($object)
    {
        return sprintf('<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[transfer_customer_service]]></MsgType></xml>', $object->FromUserName, $object->ToUserName, NOW);
    }

    static function news($object, $data = [])
    {
        $articles = '';

        if (empty($data)) {
            return;
        }

        foreach ($data as $value) {
            $articles .= sprintf('<item><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><PicUrl><![CDATA[%s]]></PicUrl><Url><![CDATA[%s]]></Url></item>', $value['Title'], $value['Description'], $value['PicUrl'], $value['Url']);
        }

        return sprintf('<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[news]]></MsgType><ArticleCount>%s</ArticleCount><Articles>%s</Articles></xml>', count($data), $articles, $object->FromUserName, $object->ToUserName, NOW);
    }

    static function image($object, $media_id = '')
    {
        return sprintf('<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[image]]></MsgType><Image><MediaId><![CDATA[%s]]></MediaId></Image></xml>', $object->FromUserName, $object->ToUserName, NOW, $media_id);
    }

    static function voice($object, $media_id = '')
    {
        return sprintf('<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[voice]]></MsgType><Voice><MediaId><![CDATA[%s]]></MediaId></Voice></xml>', $object->FromUserName, $object->ToUserName, NOW, $media_id);
    }

    static function video($object, $data = [])
    {
        return sprintf('<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[%s]]></MediaId><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description></Video></xml>', $object->FromUserName, $object->ToUserName, NOW, $data['MediaId'], $data['Title'], $data['Description']);
    }

    static function music($object, $data = [])
    {
        return sprintf('<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[music]]></MsgType><Music><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><MusicUrl><![CDATA[%s]]></MusicUrl><HQMusicUrl><![CDATA[%s]]></HQMusicUrl><ThumbMediaId><![CDATA[%s]]></ThumbMediaId></Music></xml>', $object->FromUserName, $object->ToUserName, NOW, $data['Title'], $data['Description'], $data['MusicUrl'], $data['HQMusicUrl'], $data['ThumbMediaId']);
    }
}
