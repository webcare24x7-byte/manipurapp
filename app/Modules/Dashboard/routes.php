<?php

declare(strict_types=1);

use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\Dashboard\Controllers\ChurchStructureController;

$router = app()->get('router');

$router->get(
    '/dashboard',
    [DashboardController::class, 'index'],
    ['auth']
);

$router->get(
    '/church-structure',
    [ChurchStructureController::class, 'index'],
    ['auth']
);