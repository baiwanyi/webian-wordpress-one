<?php
/*
 * Modules Name: Markdown 编辑器模块
 * Description: 常用的函数和 Hook，屏蔽所有 WordPress 所有不常用的功能。
 * Version: 3.0.0
 * Author: Yeeloving
 */


// namespace Markdown;

// /**
//  *
//  */
// add_filter('replace_editor', function ($editor, $post) {

//     if ('post' == $post->post_type) {
//         wwpo_replace_editor();

//         // Don't load directly.
//         if (!defined('ABSPATH')) {
//             die('-1');
//         }

//         /**
//          * @global string       $post_type          文章类型
//          * @global WP_Post_Type $post_type_object   文章标签
//          * @global WP_Post      $post               文章内容
//          */
//         global $post_type, $post_type_object, $post;

//         require_once ABSPATH . 'wp-admin/admin-header.php';



        // <!-- <div class="wrap">
        //     <div class="row">
        //         <div class="col-10">
        //             <input class="form-control form-control-lg" type="text" placeholder=".form-control-lg" aria-label=".form-control-lg example">
        //             <div id="editor" class="bg-white mt-3"></div>
        //         </div>
        //         <div class="col-2">
        //             Column
        //         </div>
        //     </div>
        // </div> -->

//         return true;
//     }

//     return $editor;
// }, 10, 2);

/**
 * Undocumented function
 *
 * @return void
 */

// function wwpo_replace_editor()
// {
//     //
//     add_filter('use_block_editor_for_post_type', '__return_false', 100);

//     //
//     add_action('admin_enqueue_scripts', function () {
//         wp_enqueue_style('markdown-syntax', WWPO_MOD_URL . basename(__DIR__) . '/css/toastui-editor-plugin-code-syntax-highlight.css', null, NOW, 'all');
//         wp_enqueue_style('markdown-prism', WWPO_MOD_URL . basename(__DIR__) . '/css/prism.min.css', null, NOW, 'all');
//         wp_enqueue_style('markdown-style', WWPO_MOD_URL . basename(__DIR__) . '/css/toastui-editor.min.css', null, NOW, 'all');
//         wp_enqueue_script('markdown-cdn', WWPO_MOD_URL . basename(__DIR__) . '/js/toastui-editor-all.min.js', null, null, true);
//         wp_enqueue_script('markdown-syntax', WWPO_MOD_URL . basename(__DIR__) . '/js/toastui-editor-plugin-code-syntax-highlight-all.min.js', null, null, true);
//         wp_enqueue_script('markdown-prism', WWPO_MOD_URL . basename(__DIR__) . '/js/prism.min.js', null, null, true);
//         wp_enqueue_script('markdown-js', WWPO_MOD_URL . basename(__DIR__) . '/js/markdown.js', null, NOW, true);
//     });
// }
