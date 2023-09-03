<?php

/**
 * Twenty Twenty-Two: Block Patterns
 *
 * @since Twenty Twenty-Two 1.0
 */

/**
 * Registers block patterns and categories.
 *
 * @since Twenty Twenty-Two 1.0
 *
 * @return void
 */
function wwpo_register_block_patterns()
{
    $block_pattern_categories = [
        'featured' => ['label' => __('Featured', 'wwpo')],
        'footer'   => ['label' => __('Footers', 'wwpo')],
        'header'   => ['label' => __('Headers', 'wwpo')],
        'query'    => ['label' => __('Query', 'wwpo')],
        'pages'    => ['label' => __('Pages', 'wwpo')],
    ];

    /**
     * Filters the theme block pattern categories.
     *
     * @since Twenty Twenty-Two 1.0
     *
     * @param array[] $block_pattern_categories {
     *     An associative array of block pattern categories, keyed by category name.
     *
     *     @type array[] $properties {
     *         An array of block category properties.
     *
     *         @type string $label A human-readable label for the pattern category.
     *     }
     * }
     */
    $block_pattern_categories = apply_filters('wwpo_block_pattern_categories', $block_pattern_categories);

    foreach ($block_pattern_categories as $name => $properties) {
        if (!WP_Block_Pattern_Categories_Registry::get_instance()->is_registered($name)) {
            register_block_pattern_category($name, $properties);
        }
    }

    $block_patterns = [];

    /**
     * Filters the theme block patterns.
     *
     * @since Twenty Twenty-Two 1.0
     *
     * @param array $block_patterns List of block patterns by name.
     */
    $block_patterns = apply_filters('wwpo_block_patterns', $block_patterns);

    foreach ($block_patterns as $block_pattern => $block_val) {
        wwpo_register_block_pattern($block_val['cate'], $block_val['title'], $block_pattern);
    }
}
add_action('init', 'wwpo_register_block_patterns', 9);

/**
 * Undocumented function
 *
 * @param [type] $categories
 * @param [type] $title
 * @param [type] $block_pattern
 * @return void
 */
function wwpo_register_block_pattern($categories, $title, $block_pattern)
{
    $array_block_pattern = [
        'title' => $title
    ];

    $template_name = get_template() . '/';

    if (is_array($categories)) {
        foreach ($categories as $cate) {
            $array_block_pattern['categories'][] = $cate;
            $array_block_pattern['blockTypes'][] = sprintf('core/template-part/%s', $cate);
        }
    } else {
        $array_block_pattern['categories'][] = $categories;
        $array_block_pattern['blockTypes'][] = sprintf('core/template-part/%s', $categories);
    }

    $pattern_file = get_theme_file_path('/block-patterns/' . $block_pattern . '.php');

    if (!file_exists($pattern_file)) {
        return;
    }

    ob_start();
    require $pattern_file;
    global $post;
    $array_block_pattern['content'] = ob_get_contents();
    ob_end_clean();

    register_block_pattern($template_name . $block_pattern, $array_block_pattern);
}
