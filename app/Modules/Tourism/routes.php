<?php

declare(strict_types=1);

use App\Modules\Tourism\Controllers\TourismController;
use App\Modules\Tourism\Controllers\TourismProviderController;
use App\Modules\Tourism\Controllers\TourismDestinationController;
use App\Modules\Tourism\Controllers\TourismStayController;
use App\Modules\Tourism\Controllers\TourismRoomController;
use App\Modules\Tourism\Controllers\TourismPackageController;
use App\Modules\Tourism\Controllers\TourismGuideController;
use App\Modules\Tourism\Controllers\TourismExperienceController;
use App\Modules\Tourism\Controllers\TourismEventController;
use App\Modules\Tourism\Controllers\TourismReviewController;
use App\Modules\Tourism\Controllers\TourismTripController;

$router = app()->get('router');

$router->get('/tourism', [TourismController::class, 'index'], ['auth']);

$router->get('/tourism/providers', [TourismProviderController::class, 'index'], ['auth']);
$router->get('/tourism/providers/create', [TourismProviderController::class, 'create'], ['auth']);
$router->post('/tourism/providers', [TourismProviderController::class, 'store'], ['auth']);
$router->get('/tourism/providers/{id}', [TourismProviderController::class, 'show'], ['auth']);
$router->get('/tourism/providers/{id}/edit', [TourismProviderController::class, 'edit'], ['auth']);
$router->post('/tourism/providers/{id}', [TourismProviderController::class, 'update'], ['auth']);
$router->post('/tourism/providers/{id}/delete', [TourismProviderController::class, 'destroy'], ['auth']);
$router->post('/tourism/providers/{id}/restore', [TourismProviderController::class, 'restore'], ['auth']);

$router->get('/tourism/destinations', [TourismDestinationController::class, 'index'], ['auth']);
$router->get('/tourism/destinations/create', [TourismDestinationController::class, 'create'], ['auth']);
$router->post('/tourism/destinations', [TourismDestinationController::class, 'store'], ['auth']);
$router->get('/tourism/destinations/{id}', [TourismDestinationController::class, 'show'], ['auth']);
$router->get('/tourism/destinations/{id}/edit', [TourismDestinationController::class, 'edit'], ['auth']);
$router->post('/tourism/destinations/{id}', [TourismDestinationController::class, 'update'], ['auth']);
$router->post('/tourism/destinations/{id}/delete', [TourismDestinationController::class, 'destroy'], ['auth']);
$router->post('/tourism/destinations/{id}/restore', [TourismDestinationController::class, 'restore'], ['auth']);

$router->get('/tourism/stays', [TourismStayController::class, 'index'], ['auth']);
$router->get('/tourism/stays/create', [TourismStayController::class, 'create'], ['auth']);
$router->post('/tourism/stays', [TourismStayController::class, 'store'], ['auth']);
$router->get('/tourism/stays/{id}', [TourismStayController::class, 'show'], ['auth']);
$router->get('/tourism/stays/{id}/edit', [TourismStayController::class, 'edit'], ['auth']);
$router->post('/tourism/stays/{id}', [TourismStayController::class, 'update'], ['auth']);
$router->post('/tourism/stays/{id}/delete', [TourismStayController::class, 'destroy'], ['auth']);
$router->post('/tourism/stays/{id}/restore', [TourismStayController::class, 'restore'], ['auth']);

$router->post('/tourism/stays/{stayId}/rooms', [TourismRoomController::class, 'store'], ['auth']);
$router->post('/tourism/stays/{stayId}/rooms/{roomId}', [TourismRoomController::class, 'update'], ['auth']);
$router->post('/tourism/stays/{stayId}/rooms/{roomId}/delete', [TourismRoomController::class, 'destroy'], ['auth']);
$router->post('/tourism/stays/{stayId}/rooms/{roomId}/restore', [TourismRoomController::class, 'restore'], ['auth']);

$router->get('/tourism/packages', [TourismPackageController::class, 'index'], ['auth']);
$router->get('/tourism/packages/create', [TourismPackageController::class, 'create'], ['auth']);
$router->post('/tourism/packages', [TourismPackageController::class, 'store'], ['auth']);
$router->get('/tourism/packages/{id}', [TourismPackageController::class, 'show'], ['auth']);
$router->get('/tourism/packages/{id}/edit', [TourismPackageController::class, 'edit'], ['auth']);
$router->post('/tourism/packages/{id}', [TourismPackageController::class, 'update'], ['auth']);
$router->post('/tourism/packages/{id}/destinations', [TourismPackageController::class, 'addDestination'], ['auth']);
$router->post('/tourism/packages/{id}/destinations/{relationId}/delete', [TourismPackageController::class, 'removeDestination'], ['auth']);
$router->post('/tourism/packages/{id}/delete', [TourismPackageController::class, 'destroy'], ['auth']);
$router->post('/tourism/packages/{id}/restore', [TourismPackageController::class, 'restore'], ['auth']);

$router->get('/tourism/guides', [TourismGuideController::class, 'index'], ['auth']);
$router->get('/tourism/guides/create', [TourismGuideController::class, 'create'], ['auth']);
$router->post('/tourism/guides', [TourismGuideController::class, 'store'], ['auth']);
$router->get('/tourism/guides/{id}', [TourismGuideController::class, 'show'], ['auth']);
$router->get('/tourism/guides/{id}/edit', [TourismGuideController::class, 'edit'], ['auth']);
$router->post('/tourism/guides/{id}', [TourismGuideController::class, 'update'], ['auth']);
$router->post('/tourism/guides/{id}/delete', [TourismGuideController::class, 'destroy'], ['auth']);
$router->post('/tourism/guides/{id}/restore', [TourismGuideController::class, 'restore'], ['auth']);


$router->get('/tourism/experiences', [TourismExperienceController::class, 'index'], ['auth']);
$router->get('/tourism/experiences/create', [TourismExperienceController::class, 'create'], ['auth']);
$router->post('/tourism/experiences', [TourismExperienceController::class, 'store'], ['auth']);
$router->get('/tourism/experiences/{id}', [TourismExperienceController::class, 'show'], ['auth']);
$router->get('/tourism/experiences/{id}/edit', [TourismExperienceController::class, 'edit'], ['auth']);
$router->post('/tourism/experiences/{id}', [TourismExperienceController::class, 'update'], ['auth']);
$router->post('/tourism/experiences/{id}/delete', [TourismExperienceController::class, 'destroy'], ['auth']);
$router->post('/tourism/experiences/{id}/restore', [TourismExperienceController::class, 'restore'], ['auth']);
$router->get('/tourism/events', [TourismEventController::class, 'index'], ['auth']);
$router->get('/tourism/events/create', [TourismEventController::class, 'create'], ['auth']);
$router->post('/tourism/events', [TourismEventController::class, 'store'], ['auth']);
$router->get('/tourism/events/{id}', [TourismEventController::class, 'show'], ['auth']);
$router->get('/tourism/events/{id}/edit', [TourismEventController::class, 'edit'], ['auth']);
$router->post('/tourism/events/{id}', [TourismEventController::class, 'update'], ['auth']);
$router->post('/tourism/events/{id}/delete', [TourismEventController::class, 'destroy'], ['auth']);
$router->post('/tourism/events/{id}/restore', [TourismEventController::class, 'restore'], ['auth']);
$router->get('/tourism/reviews', [TourismReviewController::class, 'index'], ['auth']);
$router->post('/tourism/reviews/{id}/status', [TourismReviewController::class, 'status'], ['auth']);
$router->get('/tourism/trips', [TourismTripController::class, 'index'], ['auth']);
