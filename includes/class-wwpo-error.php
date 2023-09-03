<?php

/**
 * 错误代码函数
 *
 * @since 1.0.0
 * @package Webian WordPress One
 */
class WWPO_Error
{
    const ERROR = 'error';
    const SUCCESS = 'updated';

    /**
     * Stores the most recently added data for each error code.
     *
     * @since 2.1.0
     * @var array
     */
    public $error_data = [];

    /**
     * Undocumented function
     *
     * @param string $code
     * @param string $message
     * @param array $data
     */
    public function __construct($code = '', $message = '', $data = [])
    {
        if (empty($code)) {
            return;
        }

        if ('message' == $code) {
            $data['message'] = $message;
            $page_url = WWPO_Admin::add_query($data);
            wp_redirect($page_url);
            exit;
        }

        $this->error_message($code, $message, $data);
    }

    /**
     * 错误消息代码函数
     *
     * @since 1.0.0
     * @param string    $icon       错误代码样式
     * @param string    $message    错误信息
     * @param array     $data       附加内容数组
     */
    static function alert($icon, $message = '', $data = [])
    {
        $error_data = [
            'code'      => 'alert',
            'message'   => $message,
            'data'      => $data
        ];

        $error_data['data']['icon'] = $icon;

        if ('success' == $icon) {
            $error_data['data']['status'] = WP_Http::OK;
        } else {
            $error_data['data']['status'] = WP_Http::UNAUTHORIZED;
        }

        if (is_admin()) {
            echo wwpo_json_send($error_data);
            exit;
        }

        return new WP_Error($error_data['code'], $error_data['message'], $error_data['data']);
    }

    /**
     * 消息弹出层代码函数
     *
     * @since 1.0.0
     * @param string    $message   消息内容
     * @param array     $data      附加内容数组
     */
    static function toast($icon, $message, $data = [])
    {
        $error_data = [
            'code'      => 'toast',
            'message'   => $message,
            'data'      => $data
        ];

        $error_data['data']['icon'] = $icon;

        if ('success' == $icon) {
            $error_data['data']['status'] = WP_Http::OK;
        } else {
            $error_data['data']['status'] = WP_Http::UNAUTHORIZED;
        }

        if (is_admin()) {
            echo wwpo_json_send($error_data);
            exit;
        }

        return new WP_Error($error_data['code'], $error_data['message'], $error_data['data']);
    }

    /**
     * Modal 弹出层代码函数
     *
     * @since 1.0.0
     * @param array     $content    模版渲染数据
     * @param string    $template   模版名称
     * @param array     $settings   弹出层设置
     * @param string    $qrcode     需要显示的二维码内容
     */
    static function modal($content, $template, $settings, $qrcode = null)
    {
        $error_data = [
            'code'      => 'modal',
            'message'   => $template,
            'data'      => [
                'status'    => WP_Http::OK,
                'content'   => $content,
                'settings'  => $settings
            ]
        ];

        if ($qrcode) {
            $error_data['data']['qrcode'] = $qrcode;
        }

        if (is_admin()) {
            echo wwpo_json_send($error_data);
            exit;
        }

        return new WP_Error($error_data['code'], $error_data['message'], $error_data['data']);
    }

    /**
     * Sidebar 侧边栏代码函数
     *
     * @since 1.0.0
     * @param array     $content    模版渲染数据
     * @param string    $template   模版名称
     * @param array     $settings   弹出层设置
     * @param string    $qrcode     需要显示的二维码内容
     */
    static function sidebar($content, $template, $settings, $qrcode = null)
    {
        $error_data = [
            'code'      => 'sidebar',
            'message'   => $template,
            'data'      => [
                'status'    => WP_Http::OK,
                'content'   => $content,
                'settings'  => $settings
            ]
        ];

        if ($qrcode) {
            $errors['data']['qrcode'] = $qrcode;
        }

        if (is_admin()) {
            echo wwpo_json_send($error_data);
            exit;
        }

        return new WP_Error($error_data['code'], $error_data['message'], $error_data['data']);
    }

    /**
     * 修改 input 值函数
     *
     * @since 1.0.0
     * @param array $value
     */
    static function value($value = [])
    {
        $error_data = [
            'code'      => 'value',
            'message'   => null,
            'data'      => [
                'status'    => WP_Http::OK,
                'value'     => $value
            ]
        ];

        if (is_admin()) {
            echo wwpo_json_send($error_data);
            exit;
        }

        return new WP_Error($error_data['code'], $error_data['message'], $error_data['data']);
    }

    /**
     * 渲染模版函数
     * @param string    $body       显示模板位置
     * @param string    $template   模版名称
     * @param array     $data       模版渲染数据
     */
    static function template($body, $template, $data = [])
    {
        $error_data = [
            'code'      => 'template',
            'message'   => $template,
            'data'      => [
                'status'    => WP_Http::OK,
                'content'   => $data,
                'body'      => $body
            ]
        ];

        if (is_admin()) {
            echo wwpo_json_send($error_data);
            exit;
        }

        return new WP_Error($error_data['code'], $error_data['message'], $error_data['data']);
    }

    /**
     * Undocumented function
     *
     * @param [type] $page_url
     * @param [type] $param
     * @return void
     */
    static function url($page_url, $param = null)
    {
        if ($param) {
            $page_url = WWPO_Admin::add_query($param, $page_url);
        }

        $error_data = [
            'code'  => 'url',
            'data'  => [
                'status'    => WP_Http::OK,
                'url'       => $page_url
            ]
        ];

        if (is_admin()) {
            echo wwpo_json_send($error_data);
            exit;
        }

        return new WP_Error($error_data['code'], $error_data['message'], $error_data['data']);
    }

    /**
     * 显示错误消息函数
     *
     * @since 1.0.0
     * @param string    $code       错误代码
     * @param string    $message    错误消息内容
     */
    private function error_message($code, $message, $data)
    {
        // 设定默认消息内容数组
        $messages = [
            'unknown'   => [
                'title'     => __('未知消息', 'wwpo'),
                'status'    => WP_Http::BAD_REQUEST,
                'css'       => self::ERROR
            ],
            'none' => [
                'title'     => $message,
                'status'    => WP_Http::NOT_FOUND
            ],
            'danger' => [
                'title'     => $message,
                'status'    => WP_Http::UNAUTHORIZED,
                'css'       => self::ERROR
            ],
            'invalid_nonce'     => [
                'title'     => __('非法操作', 'wwpo'),
                'status'    => WP_Http::UNAUTHORIZED,
                'css'       => self::ERROR
            ],
            'invalid_user_role' => [
                'title'     => __('没有权限', 'wwpo'),
                'status'    => WP_Http::UNAUTHORIZED,
                'css'       => self::ERROR
            ],
            'invalid_function'  => [
                'title'     => __('未知执行函数', 'wwpo'),
                'status'    => WP_Http::UNAUTHORIZED,
                'css'       => self::ERROR
            ],
            'not_found_action'  => [
                'title'     => __('未找到相关操作', 'wwpo'),
                'status'    => WP_Http::NOT_FOUND,
                'css'       => self::ERROR
            ],
            'not_found_content' => [
                'title'     => __('未找到相关内容', 'wwpo'),
                'status'    => WP_Http::NOT_FOUND,
                'css'       => self::ERROR
            ],
            'invalid_added'     => [
                'title'     => __('内容添加失败', 'wwpo'),
                'status'    => WP_Http::FORBIDDEN,
                'css'       => self::ERROR
            ],
            'invalid_updated'   => [
                'title'     => __('内容更新失败', 'wwpo'),
                'status'    => WP_Http::FORBIDDEN,
                'css'       => self::ERROR
            ],
            'invalid_delete'    => [
                'title'     => __('内容删除失败', 'wwpo'),
                'status'    => WP_Http::FORBIDDEN,
                'css'       => self::ERROR
            ],
            'not_null_content'  => [
                'title'     => sprintf(__('<strong>%s</strong> 不能为空', 'wwpo'), $message),
                'status'    => WP_Http::FORBIDDEN,
                'css'       => self::ERROR
            ],
            'invalid_content'   => [
                'title'     => sprintf(__('<strong>%s</strong> 已被占用', 'wwpo'), $message),
                'status'    => WP_Http::FORBIDDEN,
                'css'       => self::ERROR
            ],
            'success' => [
                'title'     => $message,
                'status'    => WP_Http::OK,
                'css'       => self::SUCCESS
            ],
            'success_added'     => [
                'title'     => sprintf(__('<strong>%s</strong> 添加成功', 'wwpo'), $message),
                'status'    => WP_Http::OK,
                'css'       => self::SUCCESS
            ],
            'success_updated'   => [
                'title'     => sprintf(__('<strong>%s</strong> 更新成功', 'wwpo'), $message),
                'status'    => WP_Http::OK,
                'css'       => self::SUCCESS
            ],
            'success_deleted'   => [
                'title'     => sprintf(__('<strong>%s</strong> 删除成功', 'wwpo'), $message),
                'status'    => WP_Http::OK,
                'css'       => self::SUCCESS
            ],
            'success_imported'  => [
                'title'     => sprintf(__('<strong>%s</strong> 导入成功', 'wwpo'), $message),
                'status'    => WP_Http::OK,
                'css'       => self::SUCCESS
            ]
        ];

        $this->error_data = [
            'code'  => $code,
            'data'  => $data
        ];

        /**
         *
         */
        $messages = apply_filters('wwpo_message', $messages);

        if (empty($messages[$code])) {
            $code = 'unknown';
        }

        $this->error_data['message']            = $messages[$code]['title'];
        $this->error_data['data']['status']     = $messages[$code]['status'];
        $this->error_data['data']['css']        = $messages[$code]['css'];

        if (is_admin()) {
            echo wwpo_json_send($this->error_data);
            exit;
        }

        return new WP_Error($this->error_data['code'], $this->error_data['message'], $this->error_data['data']);
    }
}
