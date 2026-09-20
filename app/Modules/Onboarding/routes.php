<?php

declare(strict_types=1);

use App\Modules\Onboarding\Controllers\RegisterController;
use App\Modules\Onboarding\Controllers\LoginController;

$router = app()->get('router');

$router->get(
    '/login',
    [LoginController::class, 'index'],
    ['guest']
);

$router->post(
    '/login',
    [LoginController::class, 'store'],
    ['guest']
);

$router->get(
    '/register',
    [RegisterController::class, 'index'],
    ['guest']
);

$router->post(
    '/register',
    [RegisterController::class, 'store'],
    ['guest']
);

use App\Modules\Onboarding\Controllers\LogoutController;

$router->get(
    '/logout',
    [LogoutController::class, 'index']
);

use App\Modules\Onboarding\Controllers\TenantController;
$router->get(
    '/login',
    [LoginController::class, 'index'],
    ['guest']
);

$router->get(
    '/c/{slug}/login',
    [LoginController::class, 'tenantIndex'],
    ['guest']
);

$router->post(
    '/c/{slug}/login',
    [LoginController::class, 'tenantStore'],
    ['guest']
);

