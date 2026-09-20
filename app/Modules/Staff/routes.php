<?php

declare(strict_types=1);

use App\Modules\Staff\Controllers\StaffController;

$router = app()->get('router');


/*
|--------------------------------------------------------------------------
| Staff
|--------------------------------------------------------------------------
*/

$router->get(
    '/staff',
    [StaffController::class, 'index'],
    ['auth']
);

$router->get(
    '/staff/create',
    [StaffController::class, 'create'],
    ['auth']
);

$router->post(
    '/staff',
    [StaffController::class, 'store'],
    ['auth']
);

$router->get(
    '/staff/{id}',
    [StaffController::class, 'show'],
    ['auth']
);

$router->get(
    '/staff/{id}/edit',
    [StaffController::class, 'edit'],
    ['auth']
);

$router->post(
    '/staff/{id}/update',
    [StaffController::class, 'update'],
    ['auth']
);

$router->post(
    '/staff/{id}/delete',
    [StaffController::class, 'delete'],
    ['auth']
);