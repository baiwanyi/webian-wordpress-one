<?php

/**
 * 用户 REST API 路由操作类
 *
 * @package Webian WordPress One
 */
class WWPO_REST_User_Controller extends WWPO_REST_Controller
{
    /**
     * 构造函数
     *
     * @since 4.7.0
     */
    public function __construct()
    {
        $this->namespace = 'wwpo';
        $this->rest_base = 'user/';

        $this->meta = new WP_REST_User_Meta_Fields();
    }

    /**
     * 注册用户路由
     *
     * @since 1.0.0
     * @see register_rest_route()
     */
    public function register_routes()
    {
        /**
         * 注册用户密码修改 API
         *
         * @since 1.0.0
         * @method /wwpo/user/changepassword
         */
        register_rest_route($this->namespace, $this->rest_base . 'changepassword', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'change_password'],
            'permission_callback'   => [$this, 'permissions'],
        ]);

        /**
         * 用户找回密码 API
         *
         * @since 1.0.0
         * @method /wwpo/user/lostpassword
         */
        register_rest_route($this->namespace, $this->rest_base . 'lostpassword', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'lost_password'],
            'permission_callback'   => [$this, 'permissions'],
        ]);

        /**
         * 注册用户注册 API
         *
         * @since 1.0.0
         * @method /wwpo/user/register
         */
        register_rest_route($this->namespace, $this->rest_base . 'register', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'register'],
            'permission_callback'   => [$this, 'permissions'],
        ]);

        /**
         * 注册用户登录 API
         *
         * @since 1.0.0
         * @method /wwpo/user/login
         */
        register_rest_route($this->namespace, $this->rest_base . 'login', [
            'methods'               => WP_REST_Server::CREATABLE,
            'callback'              => [$this, 'login'],
            'permission_callback'   => [$this, 'permissions'],
        ]);

        /**
         * 退出登录 API
         *
         * @since 1.0.0
         * @method /wwpo/user/logout
         */
        register_rest_route($this->namespace, $this->rest_base . 'logout', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'logout'],
            'permission_callback'   => [$this, 'permissions'],
        ]);
    }

    /**
     * 用户注册函数
     *
     * @since 1.0.0
     * @param array $request 请求参数
     */
    public function register($request)
    {
        // 设定默认转跳链接地址
        $redirect_to = $request['redirect_to'];
        if (empty($redirect_to)) {
            $redirect_to = home_url();
        }

        /**
         * 用户注册前动作
         *
         * @since 1.0.0
         * @param integer   $user_id    用户 ID
         * @param array     $request    注册参数
         */
        do_action('wwpo_before_register', $request);

        // 通过邮箱获取用户信息，用于判断是否存在用户
        $user = get_user_by('email', $request['user_email']);

        /** 判断用户存在，返回登录链接 */
        if (!empty($user)) {
            return new WWPO_Error('error', __('邮箱帐号已存在！<a href="#!login">登录帐号</a>', 'wwpo'));
        }

        // 设定用户注册信息内容数组
        $userdata = [
            'user_login'            => wwpo_unique(),
            'user_pass'             => $request['user_pass'],
            'user_email'            => $request['user_email'],
            'display_name'          => $request['user_name'],
            'show_admin_bar_front'  => 'false'
        ];

        // 新建新用户
        $user_id = wp_insert_user(wp_slash($userdata));

        /** 判断用户是否新建成功 */
        if (is_wp_error($user_id)) {
            return new WWPO_Error('error', __('用户注册失败，请联系客服。', 'wwpo'));
        }

        /**
         * 用户注册动作
         *
         * @since 1.0.0
         * @param integer   $user_id    用户 ID
         * @param array     $request    注册参数
         */
        do_action('wwpo_user_register', $user_id, $request);

        // 设定用户登录
        wwpo_user_login($user_id);

        // 返回信息
        return new WWPO_Error('success', __('注册成功', 'wwpo'), ['url' => $redirect_to]);
    }

    /**
     * 用户密码修改函数
     *
     * @since 1.0.0
     * @param array $request 请求参数
     */
    public function change_password($request)
    {
        $user_id    = get_current_user_id();
        $user       = get_userdata($user_id);
        $alert_data = ($request['target']) ? ['wrapper' => $request['target']] : [];

        /** 判断用户原始密码是否正确 */
        if (!wp_check_password($request['user_oldpass'], $user->user_pass, $user_id)) {
            return new WWPO_Error('error', __('请输入正确的原始密码', 'wwpo'), $alert_data);
        }

        // 修改用户密码
        wp_set_password($request['user_newpass'], $user_id);

        /**
         * 用户修改密码动作
         *
         * @since 1.0.0
         * @param WP_User   $user       用户信息
         * @param array     $request    请求参数
         */
        do_action('wwpo_user_changepass', $user, $request);

        // 设置用户登出
        wp_logout();

        // 返回提示信息
        return new WWPO_Error('error', __('密码修改成功', 'wwpo'), ['url' => '/login']);
    }

    /**
     * 用户登录函数
     *
     * @since 1.0.0
     * @param array $request 请求参数
     */
    public function login($request)
    {
        // 设定默认转跳链接地址
        $redirect_to = $request['redirect_to'];
        if (empty($redirect_to)) {
            $redirect_to = home_url();
        }
        $alert_data = ($request['target']) ? ['wrapper' => $request['target']] : [];

        /** 判断登录名是否为邮箱，使用邮箱获取用户信息，否则使用登录名 */
        if (wwpo_filter('email', $request['user_login'])) {
            $user = get_user_by('email', $request['user_login']);
        } else {
            $user = get_user_by('login', $request['user_login']);
        }

        /** 判断用户是否存在 */
        if (empty($user)) {
            return new WWPO_Error('error', __('请输入正确的登录帐号。', 'wwpo'), $alert_data);
        }

        /** 判断用户密码是否正确 */
        if (!wp_check_password($request['user_pass'], $user->user_pass, $user->ID)) {
            return new WWPO_Error('error', __('请输入正确的登录密码。', 'wwpo'), $alert_data);
        }

        // 设定用户登录
        wwpo_user_login($user->ID);

        /**
         * 用户登录转跳链接
         *
         * @since 1.0.0
         */
        $redirect_to = apply_filters('wwpo_login_redirect', $user->ID, $redirect_to);

        // 返回提示信息
        return new WWPO_Error('success', __('登录成功', 'wwpo'), ['url' => $redirect_to]);
    }

    /**
     * 退出登录函数
     *
     * @since 1.0.0
     */
    public function logout()
    {
        wp_destroy_current_session();
        wp_clear_auth_cookie();
        wp_set_current_user(0);

        return new WWPO_Error('success', __('帐号已登出', 'wwpo'), ['url' => home_url()]);
    }

    /**
     * 用户找回密码函数
     *
     * @since 1.0.0
     * @param array $request 请求参数
     */
    public function lost_password($request)
    {
        // 获取返回消息显示位置
        $alert_data = ($request['target']) ? ['wrapper' => $request['target']] : [];

        /** 判断登录名是否为邮箱，使用邮箱获取用户信息，否则使用登录名 */
        if (WWPO_Util::is_filter('email', $request['user_login'])) {
            $user = get_user_by('email', $request['user_login']);
        } else {
            $user = get_user_by('login', $request['user_login']);
        }

        /** 判断用户信息为空 */
        if (empty($user)) {
            return new WWPO_Error('error', __('未找到相关用户', 'wwpo'), $alert_data);
        }

        /**
         * 用户找回密码动作
         *
         * @since 1.0.0
         * @param WP_User $user 用户信息
         */
        do_action('wwpo_user_lostpass', $user);

        // 设定临时密码
        $user_temp_pass = wwpo_random();

        /**
         * 用户找回密码发送邮件动作
         *
         * @since 1.0.0
         * @param array $mail
         * {
         *  用户邮件信息
         *  @var array      header  邮件头部信息
         *  @var string     title   邮件标题
         *  @var string     body    邮件正文
         * }
         */
        $mail = [
            'header'    => ['Content-Type: text/html; charset=UTF-8'],
            'title'     => sprintf(__('%s找回密码', 'wwpo'), get_bloginfo('name')),
            'body'      => sprintf('临时密码 %s', $user_temp_pass)
        ];
        $send = wp_mail($user->user_email, $mail['title'], $mail['body'], $mail['header']);

        /** 判断邮件发送成功 */
        if ($send) {

            /**
             * 用户找回密码发送邮件成功动作
             *
             * @since 1.0.0
             * @param integer $user_id 用户 ID
             */
            do_action('wwpo_user_lostpass_success', $user->ID);

            // 修改用户密码
            wp_set_password($user_temp_pass, $user->ID);

            // 返回消息
            return new WWPO_Error('success', __('重置密码邮件发送成功', 'wwpo'), $alert_data);
        } else {

            /**
             * 用户找回密码发送邮件失败动作
             *
             * @since 1.0.0
             * @param integer $user_id 用户 ID
             */
            do_action('wwpo_user_lostpass_fail', $user->ID);

            // 返回消息
            return new WWPO_Error('error', __('邮件发送失败，请联系管理员。', 'wwpo'), $alert_data);
        }
    }

    /**
     * 权限验证函数
     *
     * @since 1.0.0
     * @param array $request 请求参数
     */
    public function permissions($request)
    {
        if (WP_DEBUG) {
            return true;
        }

        if ($request['not_logged_in']) {
            return true;
        }

        if (!wp_verify_nonce($request['nonce'], 'wp_rest')) {
            return false;
        }

        return true;
    }
}
