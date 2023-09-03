<?php

/**
 * 通用模板函数
 *
 * @package Webian WordPress One
 */

/**
 * 前台模版加载函数
 *
 * @since 1.0.0
 * @param array $data 模板传参
 */
function wwpo_get_template_parts($data = null)
{
    global $wp_query, $post;

    $templates  = [];                   // 初始化模板名称数组
    $slug       = 'template-parts';     // 模板存放目录

    /** 添加自定义文章类型前缀，wp_query 传参 */
    if (isset($wp_query->query['post_type'])) {
        $subnav = $wp_query->query['post_type'];
    }

    /** 添加标签前缀 */
    if (isset($wp_query->query['tag'])) {
        $subnav = 'tag';
    }

    /** 添加分类前缀 */
    if (isset($wp_query->query['category_name'])) {
        $subnav = 'category';
    }

    /** 添加自定义文章类型前缀，post 传参 */
    if (isset($post->post_type)) {
        $subnav = $post->post_type;
    }

    /** 添加页面别名前缀 */
    if (is_page()) {
        $subnav = $post->post_name;
    }

    /** 判断页面动作别名，设定加载模版数组 */
    if (isset($wp_query->query['action'])) {
        $templates[] = sprintf('%s/page/%s.php', $slug, $wp_query->query['action']);
        $templates[] = sprintf('%s/content-%s-%s.php', $slug, $subnav, $wp_query->query['action']);
    }

    /** 判断页面，设定加载模版数组 */
    if (is_page()) {
        $templates[] = sprintf('%s/page/%s.php', $slug, $post->post_name);
    }

    /** 使用 locate_template 函数进行模板加载 */
    if (locate_template($templates, true, false, $data)) {
        return true;
    }

    return false;
}

/**
 * 前台加载 HTML 模版函数
 * 用于 lodashjs 模板渲染，使用模板名称为：#tmpl-webui-{$file}，如使用 webui 框架可省略 #tmpl 前缀
 *
 * @since 1.0.0
 * @param string    $file   模版名称
 * @param array     $data   模板传参
 */
function wwpo_get_template_html($file, $data = null)
{
    echo '<script type="text/template" id="tmpl-webui-' . esc_attr($file) . '">';
    get_template_part('template-parts/html/tmpl', $file, $data);
    echo '</script>';
}

/**
 * 获取文章内容的缩略图
 * 如未定义缩略图则自动提取文章正文第一张图片
 *
 * @since 1.0.0
 * @param object $post
 */
function wwpo_get_post_thumb($post)
{
    // 获取文章缩略图
    $thumb = get_the_post_thumbnail_url($post->ID, 'large');

    /** 判断未定义缩略图则自动提取文章正文第一张图片 */
    if (empty($thumb)) {
        preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $post->post_content, $content);

        // 如文章没有图片，则使用默认图片
        if (isset($content[2][0])) {
            $thumb = $content[2][0];
        } else {
            $thumb = get_template_directory_uri() . '/assets/images/noimage.png';
        }
    }

    return $thumb;
}

/**
 * 设定前端页面别名函数
 *
 * @since 1.0.0
 */
function wwpo_get_page_name()
{
    global $wp_query;

    if (isset($wp_query->query['action'])) {
        return $wp_query->query['action'];
    }

    if (isset($wp_query->query['name'])) {
        return $wp_query->query['name'];
    }

    if (is_page()) {
        return $wp_query->query['pagename'] ?? 'home';
    }

    if (is_single() || is_category()) {
        return $wp_query->query['category_name'];
    }

    if (is_tag()) {
        return $wp_query->query['tag'];
    }
}

/**
 * 步骤显示函数
 *
 * @since 1.0.0
 * @param array $data
 * {
 *  内容数组
 *  @var string status  步骤状态
 *  @var string title   步骤标题
 *  @var string content 步骤内容
 * }
 */
function wwpo_get_steps($data)
{
    if (empty($data)) {
        return;
    }

    $content = '<div class="wwpo-steps">';

    foreach ($data as $step) {
        $content .= sprintf(
            '<div class="wwpo-steps__item %s"><div class="wwpo-steps__anchor"><div class="wwpo-steps__anchor-dot"></div><div class="wwpo-steps__anchor-line"></div></div><div class="wwpo-steps__content"><p class="title">%s</p><p class="lead">%s</p></div></div>',
            $step['status'] ?? '',
            $step['title'] ?? __('未知步骤', 'wwpo'),
            $step['content'] ?? '',
        );
    }

    $content .= '</div>';

    return $content;
}

/**
 * 后台页面搜索栏显示函数
 *
 * @since 1.0.0
 */
function wwpo_admin_page_searchbar($hidden = null, $placeholder = null)
{
    $search = $_GET['s'] ?? '';
    $placeholder = $placeholder ?? __('搜索关键字', 'wwpo');
?>
    <div class="hstack gap-3 justify-content-end">
        <form method="GET">
            <?php echo WWPO_Form::hidden($hidden); ?>
            <div class="input-group">
                <span class="input-group-text dashicons-before dashicons-search"></span>
                <input type="search" class="form-control" name="s" placeholder="<?php echo $placeholder; ?>" value="<?php echo $search; ?>">
                <button class="btn btn-primary" type="submit">搜索</button>
            </div>
        </form>
    </div>
<?php
}
