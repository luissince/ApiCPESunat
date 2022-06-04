<?php

include_once __DIR__ . '/autoload_static.php';

class ComposerAutoloader
{

    public static function getLoader()
    {
        spl_autoload_register(function ($class) {
            $relative_class = '';
            foreach (Autoload_Static::files() as $key => $file) {
                if ($key == $class) {
                    $relative_class = $file;
                    break;
                }
            }
            if (file_exists($relative_class)) {
                require_once $relative_class;
            }
        });
    }
}
