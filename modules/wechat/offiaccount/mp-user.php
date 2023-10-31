<?php

/**
 * 微信公众号 API 用户管理接口
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Wechat
 */
class WWPO_Wechat_User
{
    /**
     * 用户授权获取 code 转换链接
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html
     *
     * @param string redirect   授权后重定向的回调链接地址
     * @param string role       微信网页授权 base，user
     * @param string state      重定向后会带上state参数，可以填写a-zA-Z0-9的参数值，最多128字节
     */
    static function codeurl($redirect, $role = 'base', $state = 'STATE')
    {
        $option = get_option(WECHAT_KEY_OPTION);

        if (empty($option['appid'])) {
            return;
        }

        if ('base' == $role) {
            $scope = 'snsapi_base';
        } else {
            $scope = 'snsapi_userinfo';
        }

        return sprintf('https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect', $option['appid'], urlencode($redirect), $scope, $state);
    }

    /**
     * 通过 code 换取网页授权 access token
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html
     *
     * @return array
     * {
     *  access_token    网页授权接口调用凭证,注意：此 access_token 与基础支持的 access_token 不同
     *  expires_in	    access_token 接口调用凭证超时时间，单位（秒）
     *  refresh_token   用户刷新 access_token
     *  openid          用户唯一标识，请注意，在未关注公众号时，用户访问公众号的网页，也会产生一个用户和公众号唯一的 OpenID
     *  scope           用户授权的作用域，使用逗号（,）分隔
     * }
     */
    static function user_token($user_id = 0)
    {
        /**  */
        if (empty($_GET['code'])) {
            return ['errcode' => 41008];
        }

        $option = get_option(WECHAT_KEY_OPTION);
        $data   = [];

        if (!empty($user_id)) {
            $data = get_user_meta($user_id, WECHAT_KEY_USERMETA, true);
        }

        $user_token     = $data['access_token'] ?? 0;
        $user_expires   = $data['userexpires'] ?? 0;

        if (empty($user_token) || (NOW - $user_expires) > 7200) {

            /** 获取微信服务器 user_token */
            $data = wwpo_curl(sprintf(
                '%1$s/sns/oauth2/access_token?grant_type=authorization_code&appid=%2$s&secret=%3$s&code=%4$s',
                WECHAT_DOMAIN,
                $option['appid'],
                $option['appsecret'],
                $_GET['code']
            ));

            if (isset($data['errcode'])) {
                return $data;
            }

            /** 判断 user_token 获取成功，保存到数据库 */
            if (isset($data['access_token'])) {

                if (empty($user_id)) {
                    $user_id = self::user_id($data['openid']);
                }

                $data['userexpires'] = NOW;

                update_user_meta($user_id, WECHAT_KEY_USERMETA, $data);
                update_user_meta($user_id, WECHAT_KEY_OPENID, $data['openid']);
            }
        }

        return $data;
    }

    /**
     * 网页授权拉取用户信息
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html
     *
     * @return array
     * {
     *  openid      用户的唯一标识
     *  nickname    用户昵称
     *  sex         用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
     *  province    用户个人资料填写的省份
     *  city        普通用户个人资料填写的城市
     *  country     国家，如中国为CN
     *  headimgurl  用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
     *  privilege   用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
     *  unionid     只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
     * }
     */
    static function userinfo($user_id = 0)
    {
        $user_token = self::user_token($user_id);

        if (empty($user_token)) {
            return ['errcode' => 42001];
        }

        if (isset($user_token['errcode'])) {
            return $user_token;
        }

        return wwpo_curl(
            sprintf(
                '%1$s/sns/userinfo?access_token=%2$s&openid=%2$s&lang=zh_CN',
                WECHAT_DOMAIN,
                $user_token['usertoken'],
                $user_token['openid']
            )
        );
    }

    /**
     * 获取用户列表
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/Getting_a_User_List.html
     *
     * @param string $next_openid 第一个拉取的 OPENID，不填默认从头开始拉取
     * @return array
     * {
     *  用户列表
     *  total           关注该公众账号的总用户数
     *  count           拉取的 openid 个数，最大值为 10000
     *  data            列表数据，openid 的列表
     *  next_openid     拉取列表的最后一个用户的 openid
     * }
     */
    static function list($next_openid = '')
    {
        return WWPO_Wechat::curl('cgi-bin/user/get?next_openid=' . $next_openid);
    }

    /**
     * 获取用户基本信息(UnionID机制)
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/Get_users_basic_information_UnionID.html#UinonId
     *
     * @param string    $openid     需要获取的用户 ID
     * @param string    $lang       国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语，默认为zh-CN
     * @return array
     * {
     *  subscribe       用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
     *  openid          用户的标识，对当前公众号唯一
     *  nickname        用户的昵称
     *  sex             用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
     *  city            用户所在城市
     *  country         用户所在国家
     *  province        用户所在省份
     *  language        用户的语言，简体中文为zh_CN
     *  headimgurl      用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
     *  subscribe_time  用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
     *  unionid         只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
     *  remark          公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
     *  groupid         用户所在的分组ID（兼容旧的用户分组接口）
     *  tagid_list      用户被打上的标签ID列表
     *  subscribe_scene 返回用户关注的渠道来源
     *  - ADD_SCENE_SEARCH                  公众号搜索
     *  - ADD_SCENE_ACCOUNT_MIGRATION       公众号迁移
     *  - ADD_SCENE_PROFILE_CARD            名片分享
     *  - ADD_SCENE_QR_CODE                 扫描二维码
     *  - ADD_SCENE_PROFILE_LINK            图文页内名称点击
     *  - ADD_SCENE_PROFILE_ITEM            图文页右上角菜单
     *  - ADD_SCENE_PAID                    支付后关注
     *  - ADD_SCENE_WECHAT_ADVERTISEMENT    微信广告
     *  - ADD_SCENE_OTHERS                  其他
     *  qr_scene        二维码扫码场景（开发者自定义）
     *  qr_scene_str    二维码扫码场景描述（开发者自定义）
     * }
     */
    static function get($openid, $lang = 'zh_CN')
    {
        return WWPO_Wechat::curl('cgi-bin/user/info?lang=' . $lang . '&openid=' . $openid);
    }

    /**
     * 批量获取用户基本信息
     * 最多支持一次拉取100条。
     *
     * @param array     $openids    需要获取的用户 openid 列表
     * @param string    $lang       国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语，默认为zh-CN
     * @return array    详见「获取用户基本信息」
     */
    static function batchget($openids, $lang = 'zh_CN')
    {
        foreach ($openids as $openid) {
            $body['user_list'][] = [
                'openid'    => $openid,
                'lang'      => $lang
            ];
        }

        return WWPO_Wechat::curl('cgi-bin/user/info/batchget',  $body);
    }

    /**
     * 获取公众号的黑名单列表
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/Manage_blacklist.html
     *
     * @param string $begin_openid 第一个拉取的 OPENID，不填默认从头开始拉取
     * @return array
     * {
     *  用户列表
     *  total           关注该公众账号的总用户数
     *  count           拉取的 openid 个数，最大值为 10000
     *  data            列表数据，openid 的列表
     *  next_openid     拉取列表的最后一个用户的 openid
     * }
     */
    static function blacklist($begin_openid = '')
    {
        return WWPO_Wechat::curl('cgi-bin/tags/members/getblacklist', [
            'begin_openid'  => $begin_openid
        ]);
    }

    /**
     * 拉黑用户
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/Manage_blacklist.html
     *
     * @param array $openids 需要拉入黑名单的用户的 openid，一次拉黑最多允许 20 个
     */
    static function block($openids)
    {
        return WWPO_Wechat::curl('cgi-bin/tags/members/batchblacklist', [
            'openid_list'  => $openids
        ]);
    }

    /**
     * 取消拉黑用户
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/Manage_blacklist.html
     *
     * @param array $openids 需要解除黑名单的用户的 openid，一次拉黑最多允许 20 个
     */
    static function unblock($openids)
    {
        return WWPO_Wechat::curl('cgi-bin/tags/members/batchunblacklist', [
            'openid_list'  => $openids
        ]);
    }

    /**
     * 创建标签
     * 一个公众号，最多可以创建100个标签。
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html
     *
     * @param string $name 标签名（30个字符以内）
     */
    static function tag_create($name)
    {
        return WWPO_Wechat::curl('cgi-bin/tags/create', [
            'tag'  => ['name' => $name]
        ]);
    }

    /**
     * 获取公众号已创建的标签
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html
     *
     * @return array tags
     * {
     *  标签列表
     *  id      标签 ID
     *  name    标签名
     *  count   使用人数
     * }
     */
    static function tag_list()
    {
        return WWPO_Wechat::curl('cgi-bin/tags/get');
    }

    /**
     * 编辑标签
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html
     *
     * @param integer   $tag_id 标签 ID
     * @param string    $name   标签名（30个字符以内）
     */
    static function tag_edit($tag_id, $name)
    {
        return WWPO_Wechat::curl('cgi-bin/tags/get', [
            'tag' => ['id' => $tag_id, 'name' => $name]
        ]);
    }

    /**
     * 删除标签
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html
     *
     * @param integer   $tag_id 标签 ID
     */
    static function tag_delete($tag_id)
    {
        return WWPO_Wechat::curl('cgi-bin/tags/delete', [
            'tag' => ['id' => $tag_id]
        ]);
    }

    /**
     * 获取标签下粉丝列表
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html
     *
     * @param integer   $tag_id         标签 ID
     * @param string    $next_openid    第一个拉取的 OPENID，不填默认从头开始拉取
     */
    static function tag_users($tag_id, $next_openid = '')
    {
        return WWPO_Wechat::curl('cgi-bin/user/tag/get', ['tagid' => $tag_id, 'next_openid' => $next_openid]);
    }

    /**
     * 批量为用户打标签
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html
     *
     * @param integer   $tag_id     标签 ID
     * @param array     $openids    需要打标签的用户 openid 列表
     */
    static function tag_batch($tag_id, $openids)
    {
        return WWPO_Wechat::curl('cgi-bin/tags/members/batchtagging', ['tagid' => $tag_id, 'openid_list' => $openids]);
    }

    /**
     * 批量为用户取消标签
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html
     *
     * @param integer   $tag_id     标签 ID
     * @param array     $openids    需要打标签的用户 openid 列表
     */
    static function tag_unbatch($tag_id, $openids)
    {
        return WWPO_Wechat::curl('cgi-bin/tags/members/batchuntagging', ['tagid' => $tag_id, 'openid_list' => $openids]);
    }

    /**
     * 获取用户身上的标签列表
     *
     * @since 1.0.0
     * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html
     *
     * @param string $openid 需要的用户 openid
     * @return array tagid_list 被置上的标签列表
     */
    static function tag_user($openid)
    {
        return WWPO_Wechat::curl('cgi-bin/tags/getidlist', ['openid' => $openid]);
    }

    /**
     * 通过 openid 获取用户 ID
     *
     * @since 1.0.0
     * @param string $openid    用户 openid
     */
    static function user_id($openid)
    {
        global $wpdb;

        $user_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value = %s", WECHAT_KEY_OPENID, $openid));

        return $user_id;
    }

    /**
     * 显示用户微信头像函数
     *
     * @since 1.0.0
     * @param integer $user_id  用户 ID
     * @param integer $size     头像尺寸，默认：48 像素
     */
    static function avatar($user_id, $size = 48)
    {
        $usermeta = get_user_meta($user_id, WECHAT_KEY_USERMETA, true);

        if (empty($usermeta['headimgurl'])) {
            return get_stylesheet_directory_uri() . '/assets/images/avatar.png';
        } else {
            return str_replace('http://', 'https://', $usermeta['headimgurl']);
        }
    }
}
