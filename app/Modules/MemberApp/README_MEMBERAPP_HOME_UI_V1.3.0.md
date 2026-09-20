# MemberApp Home UI v1.3.0

## Purpose

A presentation-only upgrade of the MemberApp landing screen. The home screen is now focused on the three currently active consumer services:

- Food & Restaurants
- Fresh Food & Grocery
- Taxi & Transport

The design follows the established Food/Fresh visual direction: generous spacing, rounded cards, soft shadows, strong hierarchy, promotional content, ratings, and clear calls to action.

## Preserved behavior

No business logic, database schema, ordering flow, cart flow, Taxi flow, authentication, notifications, or Gemini route processing was changed.

Existing MemberApp hooks retained:

- `data-location-picker`
- `data-language-toggle`
- `data-language`
- `data-coming-soon`
- `data-promo-slot`
- `/member/restaurants`
- `/member/fresh-food`
- `/member/taxi`
- `/member/bookings`
- `/member/profile`
- `/member/login`
- `/member/notifications`
- `/member/restaurant/cart`
- restaurant cart widget
- fresh food cart widget
- notification polling

## UI changes

Removed the currently non-active service cards from the primary home service grid. Hotels, Health, Shops, Events, Marketplace and other future services are not presented as active home services yet.

The home now contains:

1. Header and member welcome
2. Global service search
3. Location selector
4. Manipur-focused hero
5. Three primary service cards
6. Promotions/offers area
7. Popular Food and Fresh highlights
8. Taxi call-to-action
9. Member registration/profile conversion banner
10. Existing cart widgets and bottom navigation

The promotion and rating content is intentionally simulated/presentation-ready so it can later be connected to real admin-managed campaigns and review aggregates.
