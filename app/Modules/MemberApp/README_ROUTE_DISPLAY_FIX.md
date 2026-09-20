# MemberApp Taxi Route Display Fix

## Problem fixed

A newly created one-way MemberApp taxi booking could have:
- `distance_km` saved
- `eta_minutes` saved
- fare recalculated correctly

while `total_distance_km` and `total_eta_minutes` remained NULL.

The MemberApp booking detail page was using only the `total_*` fields, so it
continued to show `Calculating...` even though the backend had the route and
the fare had already been calculated.

## Changes

1. `Models/MemberTaxi.php`
   - `commitRoute()` now normalizes one-way route values:
     - `total_distance_km = distance_km`
     - `total_eta_minutes = eta_minutes`
   - Round-trip totals continue to use the explicit total values returned by
     the route service.

2. `Views/Bookings/show.php`
   - Initial display falls back to `distance_km` / `eta_minutes`.
   - JavaScript status updates use the same fallback.
   - Polling stops as soon as route values are available, even if an older
     booking does not have the commit marker.

No Taxi core service or Gemini configuration was changed.
