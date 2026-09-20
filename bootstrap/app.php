<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Database;
use App\Core\Router;
use App\Core\Session;
use App\Core\Tenant;
use App\Core\View;
//use App\Modules\Auth\Services\AuthService;
use App\Core\Request;

$basePath = dirname(__DIR__);
require_once $basePath . '/helpers.php';

$app = new Application($basePath);

/*
|--------------------------------------------------------------------------
| Register global application instance
|--------------------------------------------------------------------------
*/

app($app);

/*
|--------------------------------------------------------------------------
| Boot application
|--------------------------------------------------------------------------
*/

$app->boot();

/*
|--------------------------------------------------------------------------
| Database
|--------------------------------------------------------------------------
*/

$db = new Database(config('database'));

$app->set('db', $db);

/*
|--------------------------------------------------------------------------
| Session
|--------------------------------------------------------------------------
*/

$session = new Session();

$session->start();

$app->set('session', $session);


$request = new Request();

$app->set('request', $request);

/*
|--------------------------------------------------------------------------
| Router
|--------------------------------------------------------------------------
*/

$router = new Router();

$app->set('router', $router);

/*
|--------------------------------------------------------------------------
| View
|--------------------------------------------------------------------------
*/

$view = new View();

$app->set('view', $view);

/*
|--------------------------------------------------------------------------
| Tenant
|--------------------------------------------------------------------------
*/

$tenant = new Tenant($db);

$app->set('tenant', $tenant);

$app->set('currentTenant', $tenant->resolve());

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

//$auth = new AuthService($db, $session);

//$app->set('auth', $auth);

/*
|--------------------------------------------------------------------------
| Load Module Routes
|--------------------------------------------------------------------------
*/

$modules = glob(
    base_path('app/Modules/*'),
    GLOB_ONLYDIR
);

foreach ($modules as $module) {

    $routes = $module . DIRECTORY_SEPARATOR . 'routes.php';

    if (file_exists($routes)) {
        require $routes;
    }
}

return $app;