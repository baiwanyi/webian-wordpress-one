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
        $this->ossdata  = wwpo_get_option('wwpo-settings-alioss');

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
