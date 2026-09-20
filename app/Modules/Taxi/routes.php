<?php

declare(strict_types=1);

use App\Modules\Taxi\Controllers\TaxiBookingController;
use App\Modules\Taxi\Controllers\TaxiController;
use App\Modules\Taxi\Controllers\TaxiDriverController;
use App\Modules\Taxi\Controllers\TaxiServiceController;
use App\Modules\Taxi\Controllers\TaxiVehicleController;
use App\Modules\Taxi\Controllers\TaxiVendorController;

$router = app()->get('router');

$router->get('/taxi', [TaxiController::class, 'index'], ['auth']);

$router->get('/taxi/vendors', [TaxiVendorController::class, 'index'], ['auth']);
$router->get('/taxi/vendors/create', [TaxiVendorController::class, 'create'], ['auth']);
$router->post('/taxi/vendors', [TaxiVendorController::class, 'store'], ['auth']);
$router->get('/taxi/vendors/{id}', [TaxiVendorController::class, 'show'], ['auth']);
$router->get('/taxi/vendors/{id}/edit', [TaxiVendorController::class, 'edit'], ['auth']);
$router->post('/taxi/vendors/{id}', [TaxiVendorController::class, 'update'], ['auth']);
$router->post('/taxi/vendors/{id}/delete', [TaxiVendorController::class, 'destroy'], ['auth']);

$router->get('/taxi/vehicles', [TaxiVehicleController::class, 'index'], ['auth']);
$router->get('/taxi/vehicles/create', [TaxiVehicleController::class, 'create'], ['auth']);
$router->post('/taxi/vehicles', [TaxiVehicleController::class, 'store'], ['auth']);
$router->get('/taxi/vehicles/{id}', [TaxiVehicleController::class, 'show'], ['auth']);
$router->get('/taxi/vehicles/{id}/edit', [TaxiVehicleController::class, 'edit'], ['auth']);
$router->post('/taxi/vehicles/{id}', [TaxiVehicleController::class, 'update'], ['auth']);
$router->post('/taxi/vehicles/{id}/delete', [TaxiVehicleController::class, 'destroy'], ['auth']);

$router->get('/taxi/drivers', [TaxiDriverController::class, 'index'], ['auth']);
$router->get('/taxi/drivers/create', [TaxiDriverController::class, 'create'], ['auth']);
$router->post('/taxi/drivers', [TaxiDriverController::class, 'store'], ['auth']);
$router->get('/taxi/drivers/{id}', [TaxiDriverController::class, 'show'], ['auth']);
$router->get('/taxi/drivers/{id}/edit', [TaxiDriverController::class, 'edit'], ['auth']);
$router->post('/taxi/drivers/{id}', [TaxiDriverController::class, 'update'], ['auth']);
$router->post('/taxi/drivers/{id}/delete', [TaxiDriverController::class, 'destroy'], ['auth']);

$router->get('/taxi/services', [TaxiServiceController::class, 'index'], ['auth']);
$router->get('/taxi/services/create', [TaxiServiceController::class, 'create'], ['auth']);
$router->post('/taxi/services', [TaxiServiceController::class, 'store'], ['auth']);
$router->get('/taxi/services/{id}', [TaxiServiceController::class, 'show'], ['auth']);
$router->get('/taxi/services/{id}/edit', [TaxiServiceController::class, 'edit'], ['auth']);
$router->post('/taxi/services/{id}', [TaxiServiceController::class, 'update'], ['auth']);
$router->post('/taxi/services/{id}/delete', [TaxiServiceController::class, 'destroy'], ['auth']);

$router->get('/taxi/bookings', [TaxiBookingController::class, 'index'], ['auth']);
$router->get('/taxi/bookings/create', [TaxiBookingController::class, 'create'], ['auth']);
$router->post('/taxi/bookings', [TaxiBookingController::class, 'store'], ['auth']);
$router->post('/taxi/bookings/route-estimate', [TaxiBookingController::class, 'routeEstimate'], ['auth']);
$router->post('/taxi/bookings/fare-estimate', [TaxiBookingController::class, 'fareEstimate'], ['auth']);
$router->post('/taxi/bookings/{id}/commit-route-estimate', [TaxiBookingController::class, 'commitRouteEstimate'], ['auth']);
$router->post('/taxi/bookings/{id}/status', [TaxiBookingController::class, 'updateStatus'], ['auth']);
$router->get('/taxi/driver/bookings', [TaxiBookingController::class, 'driverBookings'], ['auth']);
$router->get('/taxi/driver/bookings/{id}', [TaxiBookingController::class, 'driverBooking'], ['auth']);
$router->post('/taxi/driver/bookings/{id}/status', [TaxiBookingController::class, 'driverUpdateStatus'], ['auth']);
$router->get('/taxi/bookings/{id}', [TaxiBookingController::class, 'show'], ['auth']);
$router->get('/taxi/bookings/{id}/edit', [TaxiBookingController::class, 'edit'], ['auth']);
$router->post('/taxi/bookings/{id}', [TaxiBookingController::class, 'update'], ['auth']);
$router->post('/taxi/bookings/{id}/delete', [TaxiBookingController::class, 'destroy'], ['auth']);
