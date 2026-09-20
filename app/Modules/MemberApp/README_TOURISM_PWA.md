# MemberApp Tourism Integration

This upgrade builds on the existing MemberApp module and adds Tourism as a new visitor-facing section.

## Existing behavior preserved
- Taxi booking and route/status flows
- Restaurant browsing, cart, checkout, orders and status
- Fresh Food browsing, cart, checkout, orders and route/ETA flows
- Commercial Vehicle Rental
- Member authentication/profile
- Notifications
- Existing bookings

## New Tourism routes
- /member/tourism
- /member/tourism/destinations
- /member/tourism/destinations/{id}
- /member/tourism/stays
- /member/tourism/stays/{id}
- /member/tourism/packages
- /member/tourism/packages/{id}
- /member/tourism/guides
- /member/tourism/guides/{id}
- /member/tourism/experiences
- /member/tourism/experiences/{id}
- /member/tourism/events
- /member/tourism/events/{id}
- /member/tourism/trips
- /member/tourism/trips/{id}
- POST /member/tourism/trips/{id}/items
- POST /member/tourism/reviews

Tourism uses the existing Tourism database tables. No new database migration is required for this MemberApp integration.

Public browsing works without login. Login is required for trip plans and reviews.
