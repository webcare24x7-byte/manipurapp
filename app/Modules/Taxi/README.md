# ManipurApp Taxi Module

Production-oriented Taxi module for the ManipurApp multi-tenant PHP platform.

This update keeps the existing CRUD behaviour and adds only the Taxi capabilities needed before the Member PWA integration:

- Vehicle public photo
- Driver public photo
- One-way / round-trip booking support
- Return date/time for round trips
- Taxi service pricing modes suitable for local rides and outstation/rental services

The existing tenant, vendor/business, member, booking-source and admin permission architecture is retained.

## 1. Files changed

### Vehicle
- `Models/TaxiVehicle.php`
- `Services/TaxiVehicleService.php`
- `Controllers/TaxiVehicleController.php`
- `Views/TaxiVehicles/create.php`
- `Views/TaxiVehicles/edit.php`
- `Views/TaxiVehicles/form.php`
- `Views/TaxiVehicles/index.php`
- `Views/TaxiVehicles/show.php`

### Driver
- `Models/TaxiDriver.php`
- `Services/TaxiDriverService.php`
- `Controllers/TaxiDriverController.php`
- `Views/TaxiDrivers/create.php`
- `Views/TaxiDrivers/edit.php`
- `Views/TaxiDrivers/form.php`
- `Views/TaxiDrivers/index.php`
- `Views/TaxiDrivers/show.php`

### Booking
- `Models/TaxiBooking.php`
- `Services/TaxiBookingService.php`
- `Views/TaxiBookings/form.php`
- `Views/TaxiBookings/index.php`
- `Views/TaxiBookings/show.php`

### Taxi service
- `Models/TaxiService.php`
- `Services/TaxiServiceService.php`
- `Views/TaxiServices/form.php`
- `Views/TaxiServices/index.php`
- `Views/TaxiServices/show.php`

### New shared service
- `Services/TaxiImageUploadService.php`

## 2. Database migrations

For an EXISTING ManipurApp database, run the migrations in this order:

1. Existing Taxi migration `004_add_member_and_booking_source_to_taxi_bookings.sql` if it has not already been run.
2. `005_add_taxi_media.sql`
3. `006_add_taxi_round_trip_and_pricing.sql`

The current project already has `member_id` and `booking_source`, so do not add those columns again manually if they already exist.

### Migration 005
Adds:

- `taxi_vehicles.photo_path`
- `taxi_drivers.photo_path`

Existing vehicles and drivers are left unchanged with `NULL` photo paths.

### Migration 006
Adds to `taxi_bookings`:

- `trip_type` — `ONE_WAY` or `ROUND_TRIP`
- `return_scheduled_at`

Existing bookings automatically remain `ONE_WAY`.

Adds to `taxi_services`:

- `pricing_mode` — `PER_RIDE`, `PER_KM`, `PER_DAY`, `PER_KM_WITH_MINIMUM`, `CUSTOM_QUOTE`
- `daily_rate`
- `extra_km_rate`

Existing services default to `PER_RIDE`, preserving the previous pricing behaviour.

## 3. Recommended phpMyAdmin procedure

1. Back up the ManipurApp database.
2. Open the correct ManipurApp database in phpMyAdmin.
3. Open the SQL tab.
4. Run `005_add_taxi_media.sql`.
5. Confirm both `photo_path` columns exist.
6. Run `006_add_taxi_round_trip_and_pricing.sql`.
7. Confirm the new booking/service columns exist.
8. Copy the Taxi module into `app/Modules/Taxi/`, replacing the existing Taxi module files.
9. Ensure the web server/PHP process can write to the application's `public/uploads` directory.

Do not run the original `001_taxi_tables.sql` against an already-populated production database. That file is the baseline for a fresh installation. Use the numbered upgrade migrations for an existing installation.

## 4. Vehicle photos

The Add/Edit Vehicle form now accepts:

- JPG
- PNG
- WebP
- Maximum 5 MB

Images are stored under:

`public/uploads/taxi/vehicles/`

The database stores only the relative public path in `taxi_vehicles.photo_path`.

On edit:

- Uploading a new photo replaces the old photo.
- Selecting `Remove current photo` removes the database reference and deletes the old file.
- Leaving the photo field empty keeps the existing photo.

## 5. Driver photos

The Add/Edit Driver form uses the same rules.

Images are stored under:

`public/uploads/taxi/drivers/`

The database stores the relative public path in `taxi_drivers.photo_path`.

The photo is intended to be the customer's public-facing driver profile image. Driver documents such as a driving licence are NOT stored through this photo field.

## 6. Server upload requirements

Make sure PHP has the `fileinfo` extension enabled.

For a 5 MB application limit, the PHP configuration should allow at least 5 MB. A practical local configuration is:

```ini
upload_max_filesize = 8M
post_max_size = 10M
```

After changing `php.ini`, restart MAMP/Apache/PHP as applicable.

The application itself enforces a 5 MB image limit.

## 7. Round-trip booking

A booking now has two independent concepts:

### Booking type

- `Immediate`
- `Scheduled`

### Trip type

- `ONE_WAY`
- `ROUND_TRIP`

Example:

```text
booking_type = Scheduled
trip_type = ROUND_TRIP
scheduled_at = 2026-08-15 08:00:00
return_scheduled_at = 2026-08-18 18:00:00
```

For a round trip the return journey is logically the reverse route:

```text
Departure:
Imphal -> Kohima

Return:
Kohima -> Imphal
```

The current admin booking form requires a departure time and a later return time for round trips.

The booking service also validates that the return time is strictly after the departure time. This validation is server-side and should not be bypassed by the UI.

Existing one-way bookings do not need to be edited.

## 8. Taxi service pricing

The existing pricing fields remain:

- `base_fare`
- `per_km`
- `per_minute`
- `minimum_fare`

New fields are additive.

### PER_RIDE

Suitable for a normal fixed/base ride.

### PER_KM

Suitable where the principal charge is distance based.

### PER_DAY

Suitable for rental/outstation packages such as:

```text
₹2,500 / day
```

Use `daily_rate` for the daily package rate.

### PER_KM_WITH_MINIMUM

Suitable where distance pricing is used but the service has a minimum fare/package threshold.

### CUSTOM_QUOTE

For services where the final fare is determined manually by the taxi business/admin.

`extra_km_rate` is available for rental/outstation pricing where an included distance/package can later be compared against actual distance.

The current release stores these pricing rules but does NOT yet implement the final PWA fare/availability engine. That is intentionally the next Taxi phase.

## 9. What this release does NOT implement yet

To avoid changing working behaviour unnecessarily, this release does not yet add:

- Vehicle/service compatibility matrix
- Real-time vehicle availability engine
- Driver availability conflict detection
- Automatic route distance calculation
- Automatic PWA fare calculation
- Consumer-facing Taxi API
- Consumer-facing Taxi PWA screens
- Driver live tracking
- Payment gateway
- Reviews/ratings

Those should be implemented after this foundation is tested.

## 10. PWA preparation

The new photo fields are specifically intended for the Member PWA.

The future PWA can display:

```text
Vehicle
[vehicle photo]
Toyota Innova Crysta
7 Seater · AC
₹2,500/day

Driver
[driver photo]
Th. Ramesh Singh
Verified Driver
```

For security, the future Member PWA API must derive the authenticated member from the MemberApp session/token and must not trust a client-supplied `member_id`.

### Taxi Gemini configuration

The Taxi module now owns its Gemini configuration and does not depend on another module's `Config/gemini.php`.

Configuration file:

```text
Taxi/Config/gemini.php
```

The route estimator loads only this file. Keep the file out of version control when it contains a real API key.

Current configuration values are the same Gemini Interactions API settings used by the tested Gemini worker.

## 9. Route estimates and fare calculation

The booking Create/Edit page now separates three concepts:

1. **Taxi Service pricing rules** — the configured rules for the selected service are shown before booking details are completed.
2. **Gemini route estimate** — estimated road distance and driving time.
3. **Total Estimated Fare** — calculated from the selected service's pricing mode and, where required, the Gemini route estimate.

The `Services/TaxiFareService.php` class is the single server-side source of truth for fare calculation. The booking service recalculates the fare on save, so the submitted browser value cannot override automatic pricing.

### Pricing behaviour

- `PER_RIDE`: base fare, subject to minimum fare when a minimum is configured.
- `PER_KM`: base fare + estimated road distance × per-KM rate. A configured per-minute rate is also included using the estimated ETA.
- `PER_KM_WITH_MINIMUM`: distance/time calculation with the configured minimum enforced.
- `PER_DAY`: rental days × daily rate. Extra KM is displayed, but it is not automatically charged because the current schema does not yet define an included-KM allowance.
- `CUSTOM_QUOTE`: no automatic fare; an approved manual quote is required.

All configured service pricing fields are displayed in the booking form so the administrator can clearly see what the selected service means before saving the booking.

## 10. Committing a Gemini route estimate

Clicking **Calculate Distance & ETA** produces a preview. It does not by itself mean the administrator has accepted the estimate.

The booking form then provides **Commit Route Estimate**:

- On a new booking, the estimate is marked for commit and is stored in the database when the booking is created.
- On an existing booking, the commit button writes the route-estimate commit timestamp and administrator ID to the database immediately.
- Changing pickup, destination, or trip type invalidates the previous estimate and requires a new calculation/commit.

Migration `007_add_taxi_route_estimates.sql` adds the distance/ETA snapshot fields.
Migration `008_add_taxi_route_commit.sql` adds route-estimate commit audit fields.

Run both migrations on an existing database before using these features.


### Pricing update
Migration 009 adds `taxi_services.included_km`. For Per Ride and Per Day/Rental services, committed route distance beyond Included KM is charged at Extra KM Rate. If Included KM is exceeded and no Extra KM Rate is configured, automatic fare calculation is blocked and the service should be configured or changed to Custom Quote.
