<?php

declare(strict_types=1);

use App\Modules\Auth\Controllers\LoginController;
use App\Modules\Home\Controllers\HomeController;

$router = app()->get('router');

$router->get('/', [HomeController::class, 'index']);

$router->get('/dashboard', [HomeController::class, 'index']);

$router->get('/login', [LoginController::class, 'index']);

$router->post('/login', [LoginController::class, 'login']);

$router->get('/logout', [LoginController::class, 'logout']);