<?php

/**
 * 阿里云OSS操作类
 *
 * 整理日期：2019-4-28
 *
 * @author Yeeloving <[yeeloving@webian.cn]>
 */

/** OSS命名空间 */

use OSS\OssClient;
use OSS\Core\OssException;

/**
 * Undocumented class
 */
class WWPO_Alioss
{
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    public $ossdata;

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    public $bucket;

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    public $ossclient;

    public $endpoint;

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    public function __construct()
    {
        //
        $this->ossdata  = get_option('wwpo-settings-alioss');

        /**  */
        if (empty($this->ossdata['enable']) || empty($this->ossdata['bucket']) || empty($this->ossdata['endpoint']) || empty($this->ossdata['accesskeyid']) || empty($this->ossdata['accesskeysecret'])) {
            return;
        }

        if ('internal' == $this->ossdata['network'] || empty($this->ossdata['network'])) {
            $this->endpoint = sprintf('%s-internal.aliyuncs.com', $this->ossdata['endpoint']);
        } else {
            $this->endpoint = sprintf('%s.aliyuncs.com', $this->ossdata['endpoint']);
        }

        //
        $this->bucket       = $this->ossdata['bucket'];
        $this->ossclient    = $this->ossclient();
    }

    /**
     * 根据 Config 配置，得到一个 OssClient 实例
     *
     * @return OssClient 一个 OssClient 实例
     */
    public function ossclient()
    {
        try {
            $ossclient = new OssClient($this->ossdata['accesskeyid'], $this->ossdata['accesskeysecret'], $this->endpoint);
        } catch (OssException $e) {
            return $e;
        }

        return $ossclient;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    static function endpoint($key = '')
    {
        $array_oss_endpoint = [
            'oss-cn-hangzhou'       => '华东1（杭州）',
            'oss-cn-shanghai'       => '华东2（上海）',
            'oss-cn-nanjing'        => '华东5（南京本地地域）',
            'oss-cn-fuzhou'         => '华东6（福州本地地域）',
            'oss-cn-qingdao'        => '华北1（青岛）',
            'oss-cn-beijing'        => '华北2（北京）',
            'oss-cn-zhangjiakou'    => '华北3（张家口）',
            'oss-cn-huhehaote'      => '华北5（呼和浩特）',
            'oss-cn-wulanchabu'     => '华北6（乌兰察布）',
            'oss-cn-shenzhen'       => '华南1（深圳）',
            'oss-cn-heyuan'         => '华南2（河源）',
            'oss-cn-guangzhou'      => '华南3（广州）',
            'oss-cn-chengdu'        => '西南1（成都）',
            'oss-cn-hongkong'       => '中国（香港）',
            'oss-us-west-1'         => '美国（硅谷）',
            'oss-us-east-1'         => '美国（弗吉尼亚）',
            'oss-ap-northeast-1'    => '日本（东京）',
            'oss-ap-northeast-2'    => '韩国（首尔）',
            'oss-ap-southeast-1'    => '新加坡',
            'oss-ap-southeast-2'    => '澳大利亚（悉尼）',
            'oss-ap-southeast-3'    => '马来西亚（吉隆坡）',
            'oss-ap-southeast-5'    => '印度尼西亚（雅加达）',
            'oss-ap-southeast-6'    => '菲律宾（马尼拉）',
            'oss-ap-southeast-7'    => '泰国（曼谷）',
            'oss-ap-south-1'        => '印度（孟买）',
            'oss-eu-central-1'      => '德国（法兰克福）',
            'oss-eu-west-1'         => '英国（伦敦）',
            'oss-me-east-1'         => '阿联酋（迪拜）'
        ];

        if (empty($key)) {
            return $array_oss_endpoint;
        }

        return $array_oss_endpoint[$key];
    }

    /**
     * Undocumented function
     *
     * @param [type] $object
     * @param string $style
     * @param [type] $mimetype
     * @return void
     */
    public function auth_http_headers($object, $style = '', $mimetype = '')
    {
        if (empty($mimetype)) {
            $mimetype = $this->mimetype($object);
        }

        // 设定 OSS 图片处理域名规则分隔符
        $separator = $this->ossdata['separator'] ?? '?x-oss-process=style/';

        if ($style) {
            $object .= $separator . $style;
        }

        $datatime = gmdate('D, d M Y H:i:s T');
        $resource = sprintf('/%s/%s', $this->ossdata['bucket'], $object);

        $signature = WP_REST_Server::READABLE . "\n\n";
        $signature .= $mimetype . "\n";
        $signature .= $datatime . "\n";
        $signature .= $resource;

        if (empty($this->ossdata['enable'])) {
            return;
        }

        $signature = base64_encode(hash_hmac('sha1', $signature, $this->ossdata['accesskeysecret'], true));

        if (empty($this->ossdata['domain'])) {
            $image_domain = $this->ossdata['bucket'] . '.' . $this->endpoint;
        } else {
            $image_domain = $this->ossdata['domain'];
        }

        if (empty(parse_url($image_domain, PHP_URL_SCHEME))) {
            $image_domain  = 'https://' . $image_domain;
        }

        $data['url']        = $image_domain . '/' . $object;
        $data['headers']    = [
            'Authorization' => sprintf('OSS %s:%s', $this->ossdata['accesskeyid'], $signature),
            'CanonicalizedResource' => $resource,
            'Content-Type'  => $mimetype,
            'Date'  => $datatime
        ];

        return $data;
    }

    /**
     * Undocumented function
     *
     * @param [type] $object
     * @param [type] $style
     * @return void
     */
    public function get_object($object, $style = NULL)
    {
        if (empty($this->ossclient)) {
            return;
        }

        if ($style) {
            $object .= '!' . $style;
        }

        return $this->ossclient->getObject($this->ossdata['bucket'], $object);
    }

    /**
     * Undocumented function
     *
     * @param [type] $attachment
     * @return void
     */
    public function mimetype($object)
    {
        $image_mime = '';
        $image_ext = pathinfo($object, PATHINFO_EXTENSION);
        foreach (wp_get_mime_types() as $ext => $mime) {
            if (false !== strpos($ext, $image_ext)) {
                $image_mime = $mime;
                break;
            }
        }

        return $image_mime;
    }

    /**
     * Undocumented function
     *
     * @param [type] $object
     * @param [type] $style
     * @return void
     */
    public function signurl($object, $style = null)
    {
        if (empty($this->ossdata['enable'])) {
            return;
        }

        if (empty($this->ossclient) && !WP_DEBUG) {
            return;
        }

        $object = ltrim($object, '/');
        $permission = $this->ossdata['permission'] ?? 'private';

        if ($style) {
            $object .= '!' . $style;
        }

        if ('private' != $permission) {
            return $this->domain($object);
        }

        // 设定 OSS 图片处理域名规则分隔符
        $expirestime = time() + 600;

        $signature = WP_REST_Server::READABLE . "\n\n\n";
        $signature .= $expirestime . "\n";
        $signature .= sprintf('/%s/%s', $this->ossdata['bucket'], $object);

        $signature = base64_encode(hash_hmac('sha1', $signature, $this->ossdata['accesskeysecret'], true));

        $http_build = [
            'OSSAccessKeyId'    => $this->ossdata['accesskeyid'],
            'Expires'           => $expirestime,
            'Signature'         => $signature
        ];

        return $this->domain($object) . '?' . http_build_query($http_build);
    }

    public function domain($object = '')
    {
        if (empty($this->ossdata['domain'])) {
            $domain = $this->ossdata['bucket'] . '.' . $this->endpoint;
        } else {
            $domain = $this->ossdata['domain'];
        }

        $object = ltrim($object, '/');

        return 'https://' . $domain . '/' . $object;
    }
}



/**
 * 阿里云 OSS 开启媒体文件操作函数
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage Alioss
 */

/**
 * 同步上传 OSS 媒体文件函数
 *
 * @since 1.0.0
 * @param integer $post_id
 */
function wwpo_oss_upload_attachment($post_id)
{
    if (empty($post_id)) {
        return;
    }

    /**
     * 声明 OSS 类
     *
     * @var WWPO_Alioss
     */
    $alioss = new WWPO_Alioss();

    if (empty($alioss->ossdata['enable']) || empty($alioss->ossclient)) {
        return;
    }

    // 获取本地文件目录和文件
    $uploads        = wp_get_upload_dir();
    $media_file     = get_post_meta($post_id, '_wp_attached_file', true);
    $upload_file    = $uploads['basedir'] . DIRECTORY_SEPARATOR . $media_file;

    // 上传文件到 OSS
    $alioss->ossclient->uploadFile($alioss->bucket, $media_file, $upload_file);
}
add_action('add_attachment', 'wwpo_oss_upload_attachment');

/**
 * 删除文件时同时删除 OSS 媒体文件函数
 *
 * @since 1.0.0
 * @param integer $post_id
 */
function wwpo_oss_delete_attachment($post_id)
{
    if (empty($post_id)) {
        return;
    }

    /**
     * 声明 OSS 类
     *
     * @var WWPO_Alioss
     */
    $alioss = new WWPO_Alioss();

    if (empty($alioss->ossdata['enable']) || empty($alioss->ossclient)) {
        return;
    }

    // 获取本地文件名
    $object = get_post_meta($post_id, '_wp_attached_file', true);

    // 执行删除操作
    $alioss->ossclient->deleteObject($alioss->bucket, $object);
}
add_action('delete_attachment', 'wwpo_oss_delete_attachment');

/**
 * 媒体列表图片地址修改函数
 *
 * @since 1.0.0
 * @param boolean   $downsize       是否裁剪图像，默认：false
 * @param integer   $attachment_id  媒体 ID
 * @param string    $size           缩略图尺寸
 */
function wwpo_oss_image_downsize($downsize, $attachment_id, $size)
{
    /** 判断缩略图尺寸为数组时，默认为 ：thumbnail */
    if (is_array($size)) {
        $size = 'thumbnail';
    }

    // 设定 OSS 域名的文件地址
    $object     = get_post_meta($attachment_id, '_wp_attached_file', true);
    $oss_cdnurl = wwpo_oss_cdnurl($object, $size);

    if (empty($oss_cdnurl)) {
        return false;
    }

    // 返回文件地址
    return [$oss_cdnurl, 0, 0, $downsize];
}
add_filter('image_downsize', 'wwpo_oss_image_downsize', 10, 3);

/**
 * 设定获取文件地址的 URL 函数
 *
 * @since 1.0.0
 * @param string $attachment_url
 */
function wwpo_oss_get_attachment_url($attachment_url)
{
    // 获取本地上传目录
    $uploads = wp_get_upload_dir();

    // 设定 OSS 域名的文件地址，默认样式：original
    $object = str_replace($uploads['baseurl'], '', $attachment_url);

    $oss_cdnurl = wwpo_oss_cdnurl($object, 'medium');

    if (empty($oss_cdnurl)) {
        return $attachment_url;
    }

    // 返回文件 URL
    return $oss_cdnurl;
}
add_filter('wp_get_attachment_url', 'wwpo_oss_get_attachment_url');



/**
 * 媒体文件地址转换成 OSS 地址函数
 *
 * @since 1.0.0
 * @param string $object
 * @param string $style
 */
function wwpo_oss_cdnurl($object, $style = null)
{
    if (!class_exists('wwpo_alioss')) {
        return $object;
    }

    $alioss = new WWPO_Alioss();

    return $alioss->signurl($object, $style);
}

/**
 * Undocumented function
 *
 * @param [type] $object
 * @return void
 */
function wwpo_oss_delete_object($object) {

    if (!class_exists('wwpo_alioss')) {
        return;
    }

    if (empty($object)) {
        return;
    }

    $alioss = new WWPO_Alioss();

    if (empty($alioss->ossdata['enable']) || empty($alioss->ossclient)) {
        return;
    }

    $alioss->ossclient->deleteObject($alioss->bucket, $object);
}

/**
 * Undocumented function
 *
 * @param [type] $object
 * @param [type] $content
 * @return void
 */
function wwpo_oss_upload_object($object, $content) {

    if (!class_exists('wwpo_alioss')) {
        return;
    }

    if (empty($object) || empty($content)) {
        return;
    }

    $alioss = new WWPO_Alioss();

    if (empty($alioss->ossdata['enable']) || empty($alioss->ossclient)) {
        return;
    }

    $alioss->ossclient->putObject($alioss->bucket, $object, $content);
}

/**
 * Undocumented function
 *
 * @param [type] $settings
 * @return void
 */
function wwpo_admin_settings_oss($settings)
{
    // 获取设置保存值
    $option_data = get_option('wwpo-settings-alioss');

    // 设置表单内容数组
    $settings['alioss'] = [
        'title'     => __('阿里云OSS', 'wwpo'),
        'formdata'  => [
            'option_data[enable]' => [
                'title' => __('模块状态', 'wwpo'),
                'field' => [
                    'type'      => 'select',
                    'option'    => [__('关闭', 'wwpo'), __('开启', 'wwpo')],
                    'selected'  => $option_data['enable'] ?? 0
                ]
            ],
            'option_data[bucket]' => [
                'title' => __('Bucket 名称', 'wwpo'),
                'desc'  => __('阿里云 OSS 中保存文件的 Bucket 名称', 'wwpo'),
                'field' => ['type' => 'text', 'value' => $option_data['bucket'] ?? '']
            ],
            'option_data[accesskeyid]' => [
                'title' => __('AccessKeyId', 'wwpo'),
                'desc'  => __('阿里云的 AccessKeyId <a href="#">帮助</a>', 'wwpo'),
                'field' => ['type' => 'text', 'value' => $option_data['accesskeyid'] ?? '']
            ],
            'option_data[accesskeysecret]' => [
                'title' => __('AccessKeySecret', 'wwpo'),
                'desc'  => __('阿里云的 AccessKeySecret', 'wwpo'),
                'field' => ['type' => 'text', 'value' => $option_data['accesskeysecret'] ?? '']
            ],
            'option_data[endpoint]' => [
                'title' => __('访问节点', 'wwpo'),
                'desc'  => __('阿里云 OSS 访问的地域节点域名', 'wwpo'),
                'field' => [
                    'type'      => 'select',
                    'option'    => WWPO_Alioss::endpoint(),
                    'selected'  => $option_data['endpoint'] ?? ''
                ]
            ],
            'option_data[network]' => [
                'title' => __('网络类型', 'wwpo'),
                'desc'  => __('部署在阿里云 ECS 上选择「内网」能加快传输速度，其他服务器请选择「外网」', 'wwpo'),
                'field' => [
                    'type'      => 'select',
                    'option'    => ['internal' => __('内网', 'wwpo'), 'external' => __('外网', 'wwpo')],
                    'selected'  => $option_data['network'] ?? ''
                ]
            ],
            'option_data[permission]' => [
                'title' => __('读写权限', 'wwpo'),
                'field' => [
                    'type'      => 'select',
                    'option'    => [
                        'private'   => __('私有', 'wwpo'),
                        'read'      => __('公共读', 'wwpo'),
                        'public'    => __('公共读写', 'wwpo')
                    ],
                    'selected'  => $option_data['permission'] ?? ''
                ]
            ],
            'option_data[domain]' => [
                'title' => __('绑定域名', 'wwpo'),
                'desc'  => __('阿里云 OSS 上绑定的自定义域名', 'wwpo'),
                'field' => ['type' => 'text', 'value' => $option_data['domain'] ?? '']
            ]
        ]
    ];

    // 返回设置内容
    return $settings;
}
add_filter('wwpo_admin_page_settings', 'wwpo_admin_settings_oss');
