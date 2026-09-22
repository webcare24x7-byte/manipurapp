<?php

declare(strict_types=1);

use App\Modules\ILP\Controllers\ILPController;

$router = app()->get('router');

$router->get('/ilp', [ILPController::class, 'index'], ['auth']);
$router->post('/ilp/analyze', [ILPController::class, 'analyze'], ['auth']);
$router->post('/ilp/api/analyze', [ILPController::class, 'apiAnalyze'], ['auth']);
