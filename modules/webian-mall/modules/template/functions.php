<?php

/**
 * Undocumented function
 *
 * @return void
 */
function wwpo_wpmall_template_get_sync($data)
{
    $data['product'] = wwpo_wpmall_get_terms('template');
    $data['template']['recommend'] = wwpo_wpmall_get_posts([
        'post_type' => 'template',
        'slug'      => 'recommend'
    ]);

    return $data;
}
add_filter('wwpo_wxapps_sync', 'wwpo_wpmall_template_get_sync');
