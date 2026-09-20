# MemberApp Taxi PWA Upgrade

This upgrade preserves the existing MemberApp registration/login/session foundation and adds the first complete customer Taxi workflow.

## Added member routes

- `/member/taxi` — Taxi search/service selection
- `/member/taxi/vehicles` — available taxi/vehicle listing
- `/member/taxi/book` — authenticated booking creation
- `/member/taxi/bookings/{id}/process-route` — authenticated background route processing
- `/member/profile` — member profile
- `/member/bookings` — member bookings list
- `/member/bookings/{id}` — booking detail and automatic route/fare processing
- `/member/bookings/{id}/cancel` — cancel pending/confirmed booking

## Workflow

1. Customer chooses a live/active Taxi Service and sees its pricing mode/rules.
2. Customer enters pickup, destination, trip type and schedule.
3. Customer searches available active vehicles.
4. Customer selects a vehicle and confirms the booking.
5. Booking is created immediately with `booking_source = APP`, authenticated `member_id`, and `Pending` status.
6. Booking detail automatically calls the background processing endpoint.
7. `TaxiRouteService` calculates road distance/ETA with the Taxi module's Gemini configuration.
8. `TaxiFareService` calculates the rough estimated fare from the selected service rules.
9. Route values and fare are committed to `taxi_bookings`.
10. The member booking page displays the committed distance, ETA and rough estimated fare.

## Important architecture

The MemberApp does not duplicate Taxi pricing logic. It reuses `App\Modules\Taxi\Services\TaxiFareService` and `TaxiRouteService` as the pricing/route authority.

The PWA uses tenant `1` server-side and never trusts a client-supplied member ID.

## Database requirements

Use the existing Taxi migrations through migration 009, especially migrations 007/008/009 for route estimates, route commit audit fields and Included KM pricing.

## Current availability limitation

Taxi vehicles are listed when the vehicle is Active. Driver availability is surfaced from an available driver at the same Taxi Business. The current Taxi schema does not associate a driver directly with a vehicle, so the MemberApp does not falsely claim a specific driver is assigned to a specific vehicle. Driver assignment remains an operator action until a vehicle-driver assignment/live dispatch model is added.
