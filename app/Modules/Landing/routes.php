<?php

declare(strict_types=1);

use App\Modules\Landing\Controllers\LandingController;

$router = app()->get('router');

$router->get(
    '/',
    [LandingController::class, 'index']
);