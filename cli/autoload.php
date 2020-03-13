<?php

date_default_timezone_set('Asia/Novosibirsk');

spl_autoload_register(function ($class) {

    $file = __DIR__ . '/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($file)) {
        require $file;

        return true;
    }

    return false;
});
