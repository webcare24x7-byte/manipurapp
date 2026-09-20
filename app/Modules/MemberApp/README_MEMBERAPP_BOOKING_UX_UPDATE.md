# MemberApp Booking UX Update

This update builds on the previous Taxi Route Display Fix.

## Included

- Compact mobile-first Bookings list and Booking Details spacing.
- Proper booking timestamp (`created_at`).
- Proper scheduled trip date/time (`scheduled_at`).
- Proper round-trip return date/time (`return_scheduled_at`).
- Dedicated cancellation timestamp (`cancelled_at`).
- Cancellation timestamp shown separately from booking/trip time.
- Booking details show the selected taxi service's configured pricing rules.
- Fare breakdown is generated from the shared TaxiFareService result rather than recalculated in JavaScript.
- Included KM and estimated route distance are visible.
- When estimated distance exceeds Included KM, the TaxiFareService line item shows the extra distance and Extra KM charge.
- Minimum fare adjustment is shown separately when applicable.
- Total Estimated Fare is clearly separated from the base fare.
- Existing route persistence, Gemini processing, cancellation safety, status polling, and member scoping are retained.

## Migration

Run:

`Migrations/010_add_taxi_booking_cancelled_at.sql`

This adds `taxi_bookings.cancelled_at` only if it does not already exist.

## Example

Base Fare: ₹250
Minimum Fare: ₹300

The PWA will show:

- Base / fixed ride fare — ₹250
- Minimum fare adjustment — ₹50
- Total Estimated Fare — ₹300

If Included KM is 10 km and estimated route is 25 km with Extra KM Rate ₹20/km, the fare breakdown also shows:

- Included distance — 10 km included
- 15.00 extra km × ₹20/km — ₹300

The exact line items come from the shared TaxiFareService.
