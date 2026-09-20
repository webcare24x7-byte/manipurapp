<?php

declare(strict_types=1);
require_once dirname(__DIR__) . '/app/Shared/helpers.php';
/*
|--------------------------------------------------------------------------
| ChurchOS PSR-4 Autoloader
|--------------------------------------------------------------------------
|
| Loads classes from the App\ namespace.
|
*/

spl_autoload_register(function (string $class): void {

    $prefix = 'App\\';

    $baseDir = dirname(__DIR__) . '/app/';

    // Not our namespace
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    // Remove namespace prefix
    $relativeClass = substr($class, strlen($prefix));

    // Namespace -> directory
    $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});