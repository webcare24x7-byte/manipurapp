<?php

declare(strict_types=1);

use App\Modules\{{module}}\Controllers\{{module}}Controller;

$router = app()->get('router');

$router->get(
    '/{{moduleLower}}',
    [{{module}}Controller::class, 'index']
);