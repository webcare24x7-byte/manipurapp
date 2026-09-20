<?php

declare(strict_types=1);

use App\Modules\Roles\Controllers\RoleController;

$router = app()->get('router');

$router->get(
    '/roles',
    [RoleController::class, 'index']
);

$router->get(
    '/roles/{id}/permissions',
    [RoleController::class, 'permissions']
);

$router->post(
    '/roles/{id}/permissions',
    [RoleController::class, 'updatePermissions']
);