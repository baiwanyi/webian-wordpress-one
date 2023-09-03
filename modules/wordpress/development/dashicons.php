<?php

/**
 * Dashicons 图标显示页面
 *
 * @since 1.0.0
 * @package Webian WordPress One
 * @subpackage WordPress/development
 */


/** 读取 dashicons 样式文件 */
$dashicon_css_file    = fopen(ABSPATH . '/' . WPINC . '/css/dashicons.css', 'r');

/** 设定初始行数 */
$num_line = 0;

/** 设定初始图标内容 */
$dashicons_data = '';

/** 设定显示内容HTML代码 */
$html = '
    <style type="text/css">
        .wwpo-dashicons { float: left; width: 100%; }
        .wwpo-dashicons a { float: left; margin: 0px 10px 10px 0; padding: 10px; width: 80px; height: 80px; text-align: center; color: #444 }
        .wwpo-dashicons a:hover { color: #0073aa }
        .wwpo-dashicons a p{ margin-top: 5px; }
        .wwpo-dashicons .dashicons-before:before { font-size: 32px; width: 32px; height: 32px; }
    </style>
    <script type="text/javascript">
        jQuery(document).on(\'click\', \'a[href*="#dashicons"]\', function(event) {
            event.preventDefault();
            var icon = jQuery(this).attr(\'href\').substr(1);
            jQuery(\'body,html\').scrollTop(0);
            jQuery(\'input[name="icon-name"]\').val(icon).focus();
            jQuery(\'input[name="icon-span"]\').val(\'<span class="dashicons-before \'+icon+\'"></span>\');
        });
    </script>
    <h2 class="border-bottom">使用方式</h2>
    <p>在 WordPress 后台<a href="#">如何使用 Dashicons</a>。</p>
    <div class="form-field w-100">
        <label>图标名称<input type="text" name="icon-name" value="" class="regular-text" readonly="true"></label>
    </div>
    <div class="form-field w-100">
        <label>使用标签<input type="text" name="icon-span" value="" class="regular-text" readonly="true"></label>
    </div>';

/** 判断是否到最后一行，否则循环获取内容 */
while (!feof($dashicon_css_file)) {

    /** 读取每一行内容 */
    $content_line = fgets($dashicon_css_file);
    $num_line++;

    /** 前32行为CSS设定内容，跳过。 */
    if (32 > $num_line) continue;

    /** 判断行内容 */
    if ($content_line) {
        if (preg_match_all('/.dashicons-(.*?):before/i', $content_line, $matches)) {
            $dashicons_data .= sprintf(
                '<a href="#dashicons-%s"><span class="dashicons-before dashicons-%s"></span><p id="line-%s">%s</p></a>',
                $matches[1][0],
                $matches[1][0],
                $num_line,
                $matches[1][0]
            );
        } elseif (preg_match_all('/\/\* (.*?) \*\//i', $content_line, $matches)) {
            if ($dashicons_data) {
                $html .= sprintf('<div class="wwpo-dashicons">%s</div>', $dashicons_data);
            }
            $html .= sprintf('<h2 class="border-bottom">%s</h2>', $matches[1][0]);
            $dashicons_data = '';
        }
    }
}

/** 显示图标行内容 */
if ($dashicons_data) {
    $html .= sprintf('<div class="wwpo-dashicons">%s</div>', $dashicons_data);
}
fclose($dashicon_css_file);

// 返回内容
echo $html;
