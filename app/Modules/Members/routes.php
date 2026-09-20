<?php

declare(strict_types=1);

use App\Modules\Members\Controllers\MemberController;

$router = app()->get('router');

$router->get(
    '/members',
    [MemberController::class, 'index'],
    ['auth']
);

$router->get(
    '/members/create',
    [MemberController::class, 'create'],
    ['auth']
);

$router->post(
    '/members',
    [MemberController::class, 'store'],
    ['auth']
);

$router->get(
    '/members/{id}/edit',
    [MemberController::class, 'edit'],
    ['auth']
);

$router->post(
    '/members/{id}',
    [MemberController::class, 'update'],
    ['auth']
);

$router->post(
    '/members/{id}/delete',
    [MemberController::class, 'delete'],
    ['auth']
);

$router->get(
    '/members/{id}',
    [MemberController::class, 'show'],
    ['auth']
);