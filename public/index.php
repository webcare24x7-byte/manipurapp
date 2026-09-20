<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$lockFile = $root . '/storage/install.lock';

if (!is_file($lockFile)) {
    $installPath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/public/index.php'), '/') . '/../install/';
    header('Location: ' . $installPath, true, 302);
    exit;
}

require_once $root . '/bootstrap/autoload.php';

$app = require $root . '/bootstrap/app.php';

$router = app()->get('router');

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
