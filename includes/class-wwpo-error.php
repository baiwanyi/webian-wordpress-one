<?php

/**
 * 错误信息类
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */
class WWPO_Error
{
    /**
     * 构造函数
     *
     * @since 1.0.0
     * @param string    $code       错误代码
     * @param string    $message    错误信息
     * @param array     $data       传递参数
     */
    public function __construct($code, $message, $data = [])
    {
        if (empty($code) || empty($message)) {
            return;
        }

        $error_data = [
            'code'      => $code,
            'message'   => self::message($message),
            'data'      => $data
        ];

        if ('error' == $code) {
            $error_data['data']['status'] = WP_Http::UNAUTHORIZED;
        } else {
            $error_data['data']['status'] = WP_Http::OK;
        }

        if (is_admin()) {
            return WWPO_Util::json_send($error_data);
        }

        return new WP_Error($error_data['code'], $error_data['message'], $error_data['data']);
    }

    /**
     * 消息内容函数
     * 通过消息别名返回预设好的消息内容
     *
     * @since 1.0.0
     * @param string $name 消息别名
     */
    static function message($name)
    {
        // 设定默认消息内容数组
        $messages = [
            'invalid_nonce'     => _('非法操作', 'wwpo'),
            'invalid_role'      => _('没有权限', 'wwpo'),
            'invalid_function'  => _('未知执行函数', 'wwpo'),
            'not_found_action'  => _('未找到相关操作', 'wwpo'),
            'not_found_content' => _('未找到相关内容', 'wwpo'),
            'invalid_added'     => _('内容添加失败', 'wwpo'),
            'invalid_updated'   => _('内容更新失败', 'wwpo'),
            'invalid_delete'    => _('内容删除失败', 'wwpo')

        ];

        return WWPO_Array::value($name, $messages, $name);
    }
}
