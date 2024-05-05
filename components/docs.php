<?php

/**
 * 技术文档展示页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage components
 */
add_filter('wwpo_menus', ['WWPO_Docs', 'admin_menu']);
add_action('wwpo_admin_display_wwpo-docs', ['WWPO_Docs', 'display']);
add_filter('wwpo_admin_script', ['WWPO_Docs', 'localize_script']);

/**
 * 技术文档展示类
 */
class WWPO_Docs
{
    /**
     * 页面别名
     *
     * @since 1.0.0
     * @var string
     */
    const PAGE_NAME = 'wwpo-docs';

    /**
     * 添加后台管理菜单
     *
     * @since 1.0.0
     * @param array $menus 菜单内容数组
     * @return array
     */
    static function admin_menu($menus)
    {
        $menus[self::PAGE_NAME] = [
            'parent'        => 'webian-wordpress-one',
            'menu_title'    => __('开发文档', 'wwpo')
        ];

        return $menus;
    }

    /**
     * 页面内容显示函数
     *
     * @since 1.0.0
     * @return string
     */
    static function display()
    {
        // echo sodium_crypto_auth_keygen();
        // echo sodium_bin2base64(sodium_crypto_aead_aes256gcm_encrypt('{"prepay_id" : "wx201410272009395522657a690389285100"}', '', 'hME^T2Vfl2K8', 'PvDpOyE4RbwxMQXI2mh3FbP4Wvxv9VAu'), SODIUM_BASE64_VARIANT_ORIGINAL);

        // echo WWPO_Util::base64_encode(sodium_crypto_aead_aes256gcm_encrypt('{"prepay_id" : "wx201410272009395522657a690389285100"}', '', 'hME^T2Vfl2K8', 'PvDpOyE4RbwxMQXI2mh3FbP4Wvxv9VAu'), 'd3d3LnpoaXgubmV05pm65piV572R57uc');

        // echo WWPO_Util::base64_encode('{"prepay_id" : "wx201410272009395522657a690389285100"}', 'd3d3LnpoaXgubmV05pm65piV572R57uc');

        // echo SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES;
//         $data = '测试加密'; // 原始数据
// $nonce = random_bytes(SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES); // 加密证书的随机串,加密证书的随机串
// $ad = 'fullstackpm'; // 加密证书的随机串
// $kengen = sodium_crypto_aead_aes256gcm_keygen(); // 密钥

// echo $nonce;

        $current    = $_GET['tab'] ?? 'wwpo';
        $page_url   = add_query_arg('page', self::PAGE_NAME);
        $tabs       = apply_filters('wwpo_docs_tabs', ['wwpo' => '首页']);

        echo '<div class="wp-filter">';
        echo '<nav class="filter-links">';

        foreach ($tabs as $tab_key => $tab_title) {

            $tab_active = ($current == $tab_key) ? 'current' : 'item';

            if ('wwpo' == $tab_key) {
                printf('<li><a href="%s" class="%s">%s</a></li>', remove_query_arg('tab', $page_url), $tab_active, $tab_title);
                continue;
            }

            printf('<li><a href="%s" class="%s">%s</a></li>', add_query_arg('tab', $tab_key, $page_url), $tab_active, $tab_title);
        }

        echo '</nav>';
        echo '</div>';
        echo '<main id="wwpo-admin-docs" class="wwpo__admin-markdown"></main>';
    }

    /**
     * Undocumented function
     *
     * @param [type] $localize_script
     * @return void
     */
    static function localize_script($localize_script)
    {
        $current_tab = $_GET['tab'] ?? 'wwpo';
        $localize_script['markdown_base_url'] = WWPO_PLUGIN_URL;
        $localize_script['markdown_current_tab'] = $_GET['tab'] ?? 'wwpo';

        $markdown_sidebar = apply_filters('wwpo_docs_sidebar', [], $current_tab);

        if ($markdown_sidebar) {
            $localize_script['markdown_sidebar'] = WWPO_Util::json_encode($markdown_sidebar);
        }

        return $localize_script;
    }
}
