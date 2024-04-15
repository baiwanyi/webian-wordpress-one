<?php

class WWPO_Load
{

    static function markdown($id, $file)
    {
        if (!file_exists($file)) {
            return;
        }

        printf('<script id="%s" type="text/plain">', $id);
        require $file;
        echo '</script>';
    }
}
