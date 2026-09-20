# Restaurant Module

Restaurant business management, menu management and restaurant ordering lifecycle for ManipurApp.

## Features
- Restaurant business/profile management
- Manipur-only location selection
- Exact restaurant latitude/longitude management for location-aware Member PWA discovery
- Browser-assisted current-location capture plus map link from the restaurant editor
- Direct logo and cover image uploads
- Operating hours
- Categories, menu items, variants and reusable modifier groups/options
- Soft delete and restore for menu entities
- Unified Menu Management
- Customer restaurant ordering foundation
- Server-side menu validation and price calculation
- Historical order item/modifier snapshots
- Delivery and pickup orders
- Restaurant order administration and status lifecycle
- Admin test customer order flow

## Order lifecycle
Delivery: PENDING -> ACCEPTED -> PREPARING -> READY -> ASSIGNED -> OUT_FOR_DELIVERY -> DELIVERED -> COMPLETED

Pickup: PENDING -> ACCEPTED -> PREPARING -> READY -> COMPLETED

Pending orders can be rejected or cancelled. Preparing/accepted orders can be cancelled by an authorized restaurant admin.

## Installation
Run migrations in order. For an existing installation, run the Restaurant migrations in order. If the foundation, ordering, discount/coupon and status-history migrations are already installed, run `Migrations/006_restaurant_location_coordinates.sql` to add restaurant coordinates.

MemberApp ordering uses the Restaurant order service and requires the Restaurant module plus the existing `members` table.

Payment gateway integration and delivery dispatch are intentionally separate phases. The current checkout uses a test/cash payment placeholder.
