<?php

/**
 * Central location to create all shortcodes.
 */
function wwpo_shortcodes_init()
{
    // wwpo_shortcodes_loader(WWPOPATH);
    // wwpo_shortcodes_loader(TEMPLATEPATH . DIRECTORY_SEPARATOR);
}
add_action('init', 'wwpo_shortcodes_init');

/**
 * Undocumented function
 *
 * @param [type] $files
 */
function wwpo_shortcodes_loader($path)
{
    if (!file_exists($path . 'shortcodes')) {
        return;
    }

    $files = wwpo_list_files($path . 'shortcodes');

    if (empty($files)) {
        return;
    }

    foreach ($files as $shortcode_file) {

        if (!file_exists($shortcode_file)) {
            continue;
        }

        require $shortcode_file;

        $shortcode_slug = pathinfo($shortcode_file, PATHINFO_FILENAME);
        $shortcode_name = sprintf('wwpo-%s', $shortcode_slug);
        $shortcode_hook = sprintf('wwpo_shortcode_%s', str_replace('-', '_', $shortcode_slug));
        add_shortcode($shortcode_name, $shortcode_hook);
    }
}
