<?php

declare(strict_types=1);

use App\Modules\Lookup\Controllers\LookupTypeController;
use App\Modules\Lookup\Controllers\LookupValueController;

$router = app()->get('router');

/*
|--------------------------------------------------------------------------
| Lookup Types
|--------------------------------------------------------------------------
*/

$router->get(
    '/lookup-types',
    [LookupTypeController::class, 'index'],
    ['auth']
);

$router->get(
    '/lookup-types/create',
    [LookupTypeController::class, 'create'],
    ['auth']
);

$router->post(
    '/lookup-types',
    [LookupTypeController::class, 'store'],
    ['auth']
);

$router->get(
    '/lookup-types/{id}',
    [LookupTypeController::class, 'show'],
    ['auth']
);

$router->get(
    '/lookup-types/{id}/edit',
    [LookupTypeController::class, 'edit'],
    ['auth']
);

$router->post(
    '/lookup-types/{id}',
    [LookupTypeController::class, 'update'],
    ['auth']
);

$router->post(
    '/lookup-types/{id}/delete',
    [LookupTypeController::class, 'delete'],
    ['auth']
);

/*
|--------------------------------------------------------------------------
| Lookup Values
|--------------------------------------------------------------------------
*/

$router->get(
    '/lookup-types/{typeId}/values/create',
    [LookupValueController::class, 'create'],
    ['auth']
);

$router->post(
    '/lookup-types/{typeId}/values',
    [LookupValueController::class, 'store'],
    ['auth']
);

$router->get(
    '/lookup-values/{id}/edit',
    [LookupValueController::class, 'edit'],
    ['auth']
);

$router->post(
    '/lookup-values/{id}',
    [LookupValueController::class, 'update'],
    ['auth']
);

$router->post(
    '/lookup-values/{id}/delete',
    [LookupValueController::class, 'delete'],
    ['auth']
);