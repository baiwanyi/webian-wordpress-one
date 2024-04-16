<?php

class WWPO_Load
{
    /**
     * Undocumented function
     *
     * @param [type] $id
     * @param [type] $file
     * @return void
     */
    static function markdown($id, $file)
    {
        if (!file_exists($file)) {
            return;
        }

        printf('<main id="wwpo-layout" class="wwpo__admin-body" data-wwpo-markdown="#%s"></main>', $id);
        printf('<script id="%s" type="text/plain">', $id);
        require $file;
        echo '</script>';
    }
}
