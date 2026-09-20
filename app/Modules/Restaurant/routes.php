<?php

declare(strict_types=1);

use App\Modules\Restaurant\Controllers\RestaurantController;
use App\Modules\Restaurant\Controllers\RestaurantCategoryController;
use App\Modules\Restaurant\Controllers\RestaurantItemController;
use App\Modules\Restaurant\Controllers\RestaurantVariantController;
use App\Modules\Restaurant\Controllers\RestaurantModifierController;
use App\Modules\Restaurant\Controllers\RestaurantMenuManagementController;

$router = app()->get('router');

$router->get('/restaurant', [RestaurantController::class, 'index'], ['auth']);
$router->get('/restaurant/menu', [RestaurantMenuManagementController::class, 'index'], ['auth']);
$router->post('/restaurant/menu/{restaurantId}/items/{itemId}/availability', [RestaurantMenuManagementController::class, 'itemAvailability'], ['auth']);
$router->post('/restaurant/menu/{restaurantId}/variants/{variantId}/availability', [RestaurantMenuManagementController::class, 'variantAvailability'], ['auth']);
$router->get('/restaurant/restaurants', [RestaurantController::class, 'restaurants'], ['auth']);
$router->get('/restaurant/restaurants/create', [RestaurantController::class, 'create'], ['auth']);
$router->post('/restaurant/restaurants', [RestaurantController::class, 'store'], ['auth']);
$router->get('/restaurant/restaurants/{id}', [RestaurantController::class, 'show'], ['auth']);
$router->get('/restaurant/restaurants/{id}/edit', [RestaurantController::class, 'edit'], ['auth']);
$router->post('/restaurant/restaurants/{id}', [RestaurantController::class, 'update'], ['auth']);
$router->post('/restaurant/restaurants/{id}/delete', [RestaurantController::class, 'destroy'], ['auth']);
$router->post('/restaurant/restaurants/{id}/restore', [RestaurantController::class, 'restore'], ['auth']);
$router->post('/restaurant/restaurants/{id}/hours', [RestaurantController::class, 'saveHours'], ['auth']);

$router->get('/restaurant/categories', [RestaurantCategoryController::class, 'index'], ['auth']);
$router->get('/restaurant/categories/create', [RestaurantCategoryController::class, 'create'], ['auth']);
$router->post('/restaurant/categories', [RestaurantCategoryController::class, 'store'], ['auth']);
$router->get('/restaurant/categories/{id}/edit', [RestaurantCategoryController::class, 'edit'], ['auth']);
$router->post('/restaurant/categories/{id}', [RestaurantCategoryController::class, 'update'], ['auth']);
$router->post('/restaurant/categories/{id}/delete', [RestaurantCategoryController::class, 'destroy'], ['auth']);
$router->post('/restaurant/categories/{id}/restore', [RestaurantCategoryController::class, 'restore'], ['auth']);

$router->get('/restaurant/items', [RestaurantItemController::class, 'index'], ['auth']);
$router->get('/restaurant/items/create', [RestaurantItemController::class, 'create'], ['auth']);
$router->post('/restaurant/items', [RestaurantItemController::class, 'store'], ['auth']);
$router->get('/restaurant/items/{id}', [RestaurantItemController::class, 'show'], ['auth']);
$router->get('/restaurant/items/{id}/edit', [RestaurantItemController::class, 'edit'], ['auth']);
$router->post('/restaurant/items/{id}', [RestaurantItemController::class, 'update'], ['auth']);
$router->post('/restaurant/items/{id}/delete', [RestaurantItemController::class, 'destroy'], ['auth']);
$router->post('/restaurant/items/{id}/restore', [RestaurantItemController::class, 'restore'], ['auth']);

$router->get('/restaurant/variants', [RestaurantVariantController::class, 'index'], ['auth']);
$router->get('/restaurant/variants/create', [RestaurantVariantController::class, 'create'], ['auth']);
$router->post('/restaurant/variants', [RestaurantVariantController::class, 'store'], ['auth']);
$router->get('/restaurant/variants/{id}/edit', [RestaurantVariantController::class, 'edit'], ['auth']);
$router->post('/restaurant/variants/{id}', [RestaurantVariantController::class, 'update'], ['auth']);
$router->post('/restaurant/variants/{id}/delete', [RestaurantVariantController::class, 'destroy'], ['auth']);
$router->post('/restaurant/variants/{id}/restore', [RestaurantVariantController::class, 'restore'], ['auth']);

$router->get('/restaurant/modifiers', [RestaurantModifierController::class, 'index'], ['auth']);
$router->get('/restaurant/modifiers/create', [RestaurantModifierController::class, 'create'], ['auth']);
$router->post('/restaurant/modifiers', [RestaurantModifierController::class, 'store'], ['auth']);
$router->get('/restaurant/modifiers/{id}', [RestaurantModifierController::class, 'show'], ['auth']);
$router->get('/restaurant/modifiers/{id}/edit', [RestaurantModifierController::class, 'edit'], ['auth']);
$router->post('/restaurant/modifiers/{id}', [RestaurantModifierController::class, 'update'], ['auth']);
$router->post('/restaurant/modifiers/{id}/delete', [RestaurantModifierController::class, 'destroy'], ['auth']);
$router->post('/restaurant/modifiers/{id}/restore', [RestaurantModifierController::class, 'restore'], ['auth']);
$router->post('/restaurant/modifiers/{id}/options', [RestaurantModifierController::class, 'addOption'], ['auth']);
$router->post('/restaurant/modifiers/{id}/items', [RestaurantModifierController::class, 'attachItem'], ['auth']);
$router->post('/restaurant/modifiers/{id}/items/{itemId}/delete', [RestaurantModifierController::class, 'detachItem'], ['auth']);
$router->post('/restaurant/modifiers/{id}/options/{optionId}/delete', [RestaurantModifierController::class, 'deleteOption'], ['auth']);
$router->post('/restaurant/modifiers/{id}/options/{optionId}/restore', [RestaurantModifierController::class, 'restoreOption'], ['auth']);

$router->get('/restaurant/orders', [\App\Modules\Restaurant\Controllers\RestaurantOrderController::class, 'index'], ['auth']);
$router->get('/restaurant/orders/create-test', [\App\Modules\Restaurant\Controllers\RestaurantOrderController::class, 'createTest'], ['auth']);
$router->post('/restaurant/orders/create-test', [\App\Modules\Restaurant\Controllers\RestaurantOrderController::class, 'storeTest'], ['auth']);
$router->post('/restaurant/orders/test-coupon', [\App\Modules\Restaurant\Controllers\RestaurantOrderController::class, 'couponPreview'], ['auth']);
$router->get('/restaurant/orders/{id}', [\App\Modules\Restaurant\Controllers\RestaurantOrderController::class, 'show'], ['auth']);
$router->post('/restaurant/orders/{id}/status', [\App\Modules\Restaurant\Controllers\RestaurantOrderController::class, 'status'], ['auth']);

$router->get('/restaurant/coupons', [\App\Modules\Restaurant\Controllers\RestaurantCouponController::class, 'index'], ['auth']);
$router->get('/restaurant/coupons/create', [\App\Modules\Restaurant\Controllers\RestaurantCouponController::class, 'create'], ['auth']);
$router->post('/restaurant/coupons', [\App\Modules\Restaurant\Controllers\RestaurantCouponController::class, 'store'], ['auth']);
$router->get('/restaurant/coupons/{id}/edit', [\App\Modules\Restaurant\Controllers\RestaurantCouponController::class, 'edit'], ['auth']);
$router->post('/restaurant/coupons/{id}', [\App\Modules\Restaurant\Controllers\RestaurantCouponController::class, 'update'], ['auth']);
$router->post('/restaurant/coupons/{id}/delete', [\App\Modules\Restaurant\Controllers\RestaurantCouponController::class, 'destroy'], ['auth']);
