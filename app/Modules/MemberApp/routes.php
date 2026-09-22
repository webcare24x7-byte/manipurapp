<?php

declare(strict_types=1);

use App\Modules\MemberApp\Controllers\MemberAppController;

$router = app()->get('router');

/*
|--------------------------------------------------------------------------
| ManipurApp Member PWA
|--------------------------------------------------------------------------
| Public browsing is intentionally available without authentication.
| Authentication is maintained in the isolated memberapp_auth session key.
| The platform tenant is fixed server-side by MemberAppController.
|--------------------------------------------------------------------------
*/

$router->get(
    '/member',
    [MemberAppController::class, 'home']
);

$router->get('/member/search', [MemberAppController::class, 'search']);

$router->get(
    '/member/register',
    [MemberAppController::class, 'register']
);

$router->post(
    '/member/register',
    [MemberAppController::class, 'storeRegistration']
);

$router->get(
    '/member/login',
    [MemberAppController::class, 'login']
);

$router->post(
    '/member/login',
    [MemberAppController::class, 'authenticate']
);

$router->get(
    '/member/logout',
    [MemberAppController::class, 'logout']
);

$router->get('/member/session', [MemberAppController::class, 'sessionStatus']);
$router->get('/member/notifications', [MemberAppController::class, 'notifications']);
$router->get('/member/notifications/status', [MemberAppController::class, 'notificationStatus']);
$router->post('/member/ai/ask', [MemberAppController::class, 'memberAIAsk']);
$router->post('/member/ai/freeform', [MemberAppController::class, 'memberAIFreeform']);
$router->get('/member/ilp', [MemberAppController::class, 'ilp']);
$router->post('/member/ilp/analyze', [MemberAppController::class, 'ilpAnalyze']);

// Public information and service directory pages.
$router->get('/member/services', [MemberAppController::class, 'services']);
$router->get('/member/services/{service}', [MemberAppController::class, 'servicePage']);
$router->get('/member/blog', [MemberAppController::class, 'blog']);
$router->get('/member/blog/{slug}', [MemberAppController::class, 'blogPost']);
$router->post('/member/notifications/read', [MemberAppController::class, 'notificationsRead']);


$router->get('/member/fresh-food', [MemberAppController::class, 'freshFood']);
$router->get('/member/fresh-food/cart', [MemberAppController::class, 'freshFoodCart']);
$router->post('/member/fresh-food/cart/add', [MemberAppController::class, 'freshFoodCartAdd']);
$router->post('/member/fresh-food/cart/update', [MemberAppController::class, 'freshFoodCartUpdate']);
$router->post('/member/fresh-food/cart/clear', [MemberAppController::class, 'freshFoodCartClear']);
$router->post('/member/fresh-food/coupon', [MemberAppController::class, 'freshFoodApplyCoupon']);
$router->post('/member/fresh-food/coupon/remove', [MemberAppController::class, 'freshFoodRemoveCoupon']);
$router->get('/member/fresh-food/checkout', [MemberAppController::class, 'freshFoodCheckout']);
$router->post('/member/fresh-food/orders', [MemberAppController::class, 'freshFoodPlaceOrder']);
$router->get('/member/fresh-food/orders', [MemberAppController::class, 'freshFoodOrders']);
$router->get('/member/fresh-food/orders/{id}', [MemberAppController::class, 'freshFoodOrder']);
$router->get('/member/fresh-food/orders/{id}/status', [MemberAppController::class, 'freshFoodOrderStatus']);
$router->post('/member/fresh-food/orders/{id}/process-route', [MemberAppController::class, 'freshFoodProcessRoute']);
$router->get('/member/fresh-food/{freshFoodId}/products/{productId}', [MemberAppController::class, 'freshFoodProduct']);
$router->get('/member/fresh-food/{id}', [MemberAppController::class, 'freshFoodBusiness']);

$router->get('/member/commercial-rental', [MemberAppController::class, 'commercialRental']);
$router->get('/member/commercial-rental/book', [MemberAppController::class, 'commercialRentalBook']);
$router->post('/member/commercial-rental/book', [MemberAppController::class, 'commercialRentalStore']);
$router->get('/member/commercial-rental/bookings/{id}', [MemberAppController::class, 'commercialRentalBooking']);

$router->get('/member/taxi', [MemberAppController::class, 'taxi']);
$router->get('/member/restaurants', [MemberAppController::class, 'restaurants']);
$router->get('/member/restaurants/{id}', [MemberAppController::class, 'restaurant']);
$router->get('/member/restaurants/{restaurantId}/items/{itemId}', [MemberAppController::class, 'restaurantItem']);
$router->get('/member/restaurant/cart', [MemberAppController::class, 'restaurantCart']);
$router->post('/member/restaurant/cart/add', [MemberAppController::class, 'restaurantCartAdd']);
$router->post('/member/restaurant/cart/update', [MemberAppController::class, 'restaurantCartUpdate']);
$router->post('/member/restaurant/cart/clear', [MemberAppController::class, 'restaurantCartClear']);
$router->post('/member/restaurant/coupon', [MemberAppController::class, 'restaurantApplyCoupon']);
$router->post('/member/restaurant/coupon/remove', [MemberAppController::class, 'restaurantRemoveCoupon']);
$router->get('/member/restaurant/checkout', [MemberAppController::class, 'restaurantCheckout']);
$router->post('/member/restaurant/orders', [MemberAppController::class, 'restaurantPlaceOrder']);
$router->get('/member/restaurant/orders', [MemberAppController::class, 'restaurantOrders']);
$router->get('/member/restaurant/orders/{id}', [MemberAppController::class, 'restaurantOrder']);
$router->get('/member/restaurant/orders/{id}/status', [MemberAppController::class, 'restaurantOrderStatus']);
$router->post('/member/restaurant/coupon-preview', [MemberAppController::class, 'restaurantCouponPreview']);

$router->get('/member/taxi/vehicles', [MemberAppController::class, 'taxiVehicles']);
$router->post('/member/taxi/book', [MemberAppController::class, 'taxiBook']);
$router->post('/member/taxi/bookings/{id}/process-route', [MemberAppController::class, 'taxiProcessRoute']);
$router->get('/member/taxi/bookings/{id}/status', [MemberAppController::class, 'taxiBookingStatus']);
$router->get('/member/tourism', [MemberAppController::class, 'tourism']);
$router->get('/member/tourism/destinations', [MemberAppController::class, 'tourismDestinations']);
$router->get('/member/tourism/destinations/{id}', [MemberAppController::class, 'tourismDestination']);
$router->get('/member/tourism/stays', [MemberAppController::class, 'tourismStays']);
$router->get('/member/tourism/stays/{id}', [MemberAppController::class, 'tourismStay']);
$router->get('/member/tourism/packages', [MemberAppController::class, 'tourismPackages']);
$router->get('/member/tourism/packages/{id}', [MemberAppController::class, 'tourismPackage']);
$router->get('/member/tourism/guides', [MemberAppController::class, 'tourismGuides']);
$router->get('/member/tourism/guides/{id}', [MemberAppController::class, 'tourismGuide']);
$router->get('/member/tourism/experiences', [MemberAppController::class, 'tourismExperiences']);
$router->get('/member/tourism/experiences/{id}', [MemberAppController::class, 'tourismExperience']);
$router->get('/member/tourism/events', [MemberAppController::class, 'tourismEvents']);
$router->get('/member/tourism/events/{id}', [MemberAppController::class, 'tourismEvent']);
$router->post('/member/tourism/reviews', [MemberAppController::class, 'tourismReview']);
$router->get('/member/tourism/trips', [MemberAppController::class, 'tourismTrips']);
$router->post('/member/tourism/trips', [MemberAppController::class, 'tourismTripCreate']);
$router->get('/member/tourism/trips/{id}', [MemberAppController::class, 'tourismTrip']);
$router->post('/member/tourism/trips/{id}/items', [MemberAppController::class, 'tourismTripItem']);
$router->post('/member/tourism/trips/{id}/items/remove', [MemberAppController::class, 'tourismTripItemRemove']);

$router->get('/member/profile', [MemberAppController::class, 'profile']);
$router->get('/member/bookings', [MemberAppController::class, 'bookings']);
$router->get('/member/bookings/{id}', [MemberAppController::class, 'booking']);
$router->post('/member/bookings/{id}/cancel', [MemberAppController::class, 'cancelBooking']);

// Static public information pages. Kept at the end so existing member routes win.
$router->get('/member/{slug}', [MemberAppController::class, 'staticPage']);
