# MemberApp Restaurant PWA — v0.7.0

This package adds the first Member PWA Restaurant ordering experience while preserving the existing Taxi MemberApp flow.

## Dependency

The Restaurant module must be installed and its restaurant-location migration must be applied so `restaurant_profiles.latitude` and `restaurant_profiles.longitude` exist.

Expected Restaurant dependency:

- Restaurant module 0.7.x
- Restaurant location coordinates migration

MemberApp does not own the Restaurant schema. Restaurant menu, coupon, order pricing, order lifecycle and status-history rules remain authoritative in the Restaurant module.

## Routes

- `/member/restaurants`
- `/member/restaurants/{id}`
- `/member/restaurants/{restaurantId}/items/{itemId}`
- `/member/restaurant/cart`
- `/member/restaurant/checkout`
- `/member/restaurant/orders`
- `/member/restaurant/orders/{id}`

Cart/coupon actions are POST routes under `/member/restaurant/...`.

## Location discovery

The discovery page can request browser geolocation. Coordinates are sent as `lat` and `lng` query parameters and are used only for restaurant-distance sorting/display. If permission is denied, the member can continue browsing the Imphal restaurant list.

Restaurant coordinates are read from `restaurant_profiles`.

## Security

- Member ID is always taken from `$_SESSION['memberapp_auth']` when an order is created.
- Tenant ID is fixed server-side by `MemberRestaurantService::TENANT_ID`.
- Prices, discounts, variants, modifiers, minimum order, delivery fee and coupons are revalidated by `RestaurantOrderService` on order creation.
- Restaurant orders are scoped to the authenticated member.
- Existing Taxi models/services/routes were not rewritten for Restaurant integration.


## Persistent cart access

The restaurant cart is now surfaced throughout the Member PWA whenever it contains items. A floating View Cart pill shows the current quantity and remains available when members navigate away from restaurant screens. The Member home header also includes a cart icon with a quantity badge, and the Order Food quick action displays the cart quantity.
