# MemberApp Fresh Food PWA Integration

Adds Fresh Food discovery, business catalog, product details, cart, optional coupons, checkout, orders, status polling, persistent member carts, and shared notifications.

## Migration
Run `Migrations/012_create_member_fresh_food_carts.sql` after the existing MemberApp migration 011.

## Cart behavior
Restaurant and Fresh Food carts remain completely separate. A member can have restaurant items and Fresh Food items at the same time without mixing them. Fresh Food cart data is persisted per member and Fresh Food business after login. Guest carts use the existing PHP session until login.

## Notifications
The existing notification center continues to consume Taxi and Restaurant status history and now also materializes Fresh Food order status history.
