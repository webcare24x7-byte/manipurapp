<?php
declare(strict_types=1);

use App\Modules\CommercialRental\Controllers\CommercialRentalController;
use App\Modules\CommercialRental\Controllers\CommercialRentalProviderController;
use App\Modules\CommercialRental\Controllers\CommercialRentalCategoryController;
use App\Modules\CommercialRental\Controllers\CommercialRentalVehicleController;
use App\Modules\CommercialRental\Controllers\CommercialRentalRequestController;

$router = app()->get('router');

$router->get('/commercial-rentals', [CommercialRentalController::class, 'index'], ['auth']);

$router->get('/commercial-rentals/providers', [CommercialRentalProviderController::class, 'index'], ['auth']);
$router->get('/commercial-rentals/providers/create', [CommercialRentalProviderController::class, 'create'], ['auth']);
$router->post('/commercial-rentals/providers', [CommercialRentalProviderController::class, 'store'], ['auth']);
$router->get('/commercial-rentals/providers/{id}', [CommercialRentalProviderController::class, 'show'], ['auth']);
$router->get('/commercial-rentals/providers/{id}/edit', [CommercialRentalProviderController::class, 'edit'], ['auth']);
$router->post('/commercial-rentals/providers/{id}', [CommercialRentalProviderController::class, 'update'], ['auth']);
$router->post('/commercial-rentals/providers/{id}/delete', [CommercialRentalProviderController::class, 'destroy'], ['auth']);

$router->get('/commercial-rentals/categories', [CommercialRentalCategoryController::class, 'index'], ['auth']);
$router->get('/commercial-rentals/categories/create', [CommercialRentalCategoryController::class, 'create'], ['auth']);
$router->post('/commercial-rentals/categories', [CommercialRentalCategoryController::class, 'store'], ['auth']);
$router->get('/commercial-rentals/categories/{id}/edit', [CommercialRentalCategoryController::class, 'edit'], ['auth']);
$router->post('/commercial-rentals/categories/{id}', [CommercialRentalCategoryController::class, 'update'], ['auth']);
$router->post('/commercial-rentals/categories/{id}/delete', [CommercialRentalCategoryController::class, 'destroy'], ['auth']);

$router->get('/commercial-rentals/vehicles', [CommercialRentalVehicleController::class, 'index'], ['auth']);
$router->get('/commercial-rentals/vehicles/create', [CommercialRentalVehicleController::class, 'create'], ['auth']);
$router->post('/commercial-rentals/vehicles', [CommercialRentalVehicleController::class, 'store'], ['auth']);
$router->get('/commercial-rentals/vehicles/{id}', [CommercialRentalVehicleController::class, 'show'], ['auth']);
$router->get('/commercial-rentals/vehicles/{id}/edit', [CommercialRentalVehicleController::class, 'edit'], ['auth']);
$router->post('/commercial-rentals/vehicles/{id}', [CommercialRentalVehicleController::class, 'update'], ['auth']);
$router->post('/commercial-rentals/vehicles/{id}/delete', [CommercialRentalVehicleController::class, 'destroy'], ['auth']);

$router->get('/commercial-rentals/requests', [CommercialRentalRequestController::class, 'index'], ['auth']);
$router->get('/commercial-rentals/requests/create', [CommercialRentalRequestController::class, 'create'], ['auth']);
$router->post('/commercial-rentals/requests', [CommercialRentalRequestController::class, 'store'], ['auth']);
$router->get('/commercial-rentals/requests/{id}', [CommercialRentalRequestController::class, 'show'], ['auth']);
$router->get('/commercial-rentals/requests/{id}/edit', [CommercialRentalRequestController::class, 'edit'], ['auth']);
$router->post('/commercial-rentals/requests/{id}', [CommercialRentalRequestController::class, 'update'], ['auth']);
$router->post('/commercial-rentals/requests/{id}/delete', [CommercialRentalRequestController::class, 'destroy'], ['auth']);
$router->post('/commercial-rentals/requests/{id}/status', [CommercialRentalRequestController::class, 'updateStatus'], ['auth']);
