# ManipurApp Commercial Vehicle Rental — Admin v0.1.2

Independent admin module for commercial vehicle and equipment rental. It does not modify or depend on the Taxi module.

## Scope
- Rental provider/business CRUD using the existing `businesses` foundation with `business_type = commercial_rental`.
- Provider address, district, city, state, service areas and latitude/longitude.
- Vehicle/equipment categories.
- Individual rental vehicle/equipment inventory.
- Rate modes: fixed, hourly, daily, per trip, or custom quote.
- Availability and operational status.
- Managed rental request workflow: requested → contacting → quoted → confirmed → in progress → completed, plus cancelled/rejected.
- Admin-created requests are supported now; MemberApp request creation can be integrated later without redesigning the admin data model.
- Haversine service is included for future nearest-provider ranking. This module does not call Gemini, calculate ETA, or use distance for pricing.

## Installation
1. Back up the ManipurApp database.
2. Run `Migrations/001_commercial_rental_tables.sql`.
3. Run `Migrations/002_commercial_rental_permissions.sql`.
4. Copy `CommercialRental/` into `app/Modules/CommercialRental/`.
5. Load `CommercialRental/routes.php` using the same module route-loading mechanism used by your existing modules.
6. Open `/commercial-rentals`.

## Important
- Tenant isolation is applied to every query.
- Existing Taxi tables, routes, models and services are not referenced.
- Provider location is stored because the future MemberApp flow will rank eligible providers by Haversine proximity to the customer's pickup/work coordinates.
- `service_areas` is intentionally simple text at this stage; structured service-area/location tables can be introduced later without coupling this module to Taxi.
- Payment is not implemented in this admin package. The request/quote/final amount fields are ready for the future common payment layer.

## Default categories to create
- Pickup
- Mini Truck
- Truck
- Tractor
- JCB / Excavator
- Water Tanker
- Other

## Business-owner use
The module exposes permission-scoped CRUD so an authorized business operator can maintain their commercial rental business, assets and availability. The platform's existing user/business ownership rules should be applied by the host application's authorization layer where those rules are already present.

## Admin navigation
Add a sidebar/menu entry in the host admin shell pointing to:
`/commercial-rentals`

The module deliberately does not alter the host sidebar because sidebar registration differs between deployments.

## Suggested workflow to test
1. Create the default categories using migration 003.
2. Add a provider and capture latitude/longitude with **Use Current Location** or enter coordinates manually.
3. Add several assets under that provider.
4. Set asset availability to Available.
5. Create an admin request, then assign provider/asset and move it through the workflow status.
6. Later, MemberApp can create the same request record with `member_id`, pickup coordinates and no changes to the provider/vehicle administration model.


## v0.1.1 Vehicle Data Entry Upgrade
- Added fuel type, ownership type, engine/power, body/equipment type, seating capacity and condition.
- Added operator name, phone and experience fields.
- Added actual vehicle/equipment image upload (JPG/PNG/WebP, max 5 MB).
- Added provider location/service-area visibility on the vehicle detail page; vehicle location is inherited from its provider.
- Added service notes and improved rental pricing guidance.
- Added migration `004_upgrade_vehicle_data_entry.sql`; run after the original three migrations.

## v0.1.2 Backend Workflow Compatibility Fix
- Controllers now correctly extend the existing `App\Core\Controller`, matching the host application's controller convention.
- Corrected the vehicle and rental-request INSERT placeholder counts so database writes match their parameter arrays.
- No existing routes, views, service behavior, schema design, or MemberApp functionality were intentionally changed.

## MemberApp status notifications
Admin request status changes are recorded in `commercial_rental_request_status_history`. MemberApp polls its shared notification endpoint and materializes these events into the member notification center/toast. Run migration `005_create_request_status_history.sql`.
