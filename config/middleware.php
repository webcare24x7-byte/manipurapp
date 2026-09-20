<?php

declare(strict_types=1);

return [

    'auth' => App\Middleware\AuthMiddleware::class,
    'guest' => App\Middleware\GuestMiddleware::class,
];