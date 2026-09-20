<?php

declare(strict_types=1);

use App\Modules\Packages\Controllers\PackageController;

$router->get(
    '/packages',
    [PackageController::class, 'index']
);

$router->post(
    '/packages/install/{code}',
    [PackageController::class, 'install']
);

$router->post(
    '/packages/upgrade/{code}',
    [PackageController::class, 'upgrade']
);

$router->post(
    '/packages/uninstall/{code}',
    [PackageController::class, 'uninstall']
);