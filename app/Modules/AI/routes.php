<?php
declare(strict_types=1);
use App\Modules\AI\Controllers\AIController;
$router=app()->get('router');
$router->get('/ai-assistant',[AIController::class,'index'],['auth']);
$router->post('/ai-assistant/ask',[AIController::class,'ask'],['auth']);
$router->post('/ai-assistant/freeform',[AIController::class,'freeform'],['auth']);
$router->get('/ai-assistant/freeform/latest',[AIController::class,'freeformLatest'],['auth']);
$router->post('/ai-assistant/freeform/stream',[AIController::class,'freeformStream'],['auth']);
