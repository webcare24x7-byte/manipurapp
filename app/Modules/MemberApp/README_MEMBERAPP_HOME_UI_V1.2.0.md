# MemberApp Home UI/UX v1.2.0

This release upgrades only the MemberApp home/landing screen. It is designed as a discovery-first, conversion-oriented PWA home inspired by the approved Food/Fresh illustration style.

## Goals

- Make the first screen feel like a real local super-app rather than a basic directory.
- Immediately communicate the Manipur-first/local-business value proposition.
- Make Food & Restaurants, Fresh Food & Grocery and Taxi highly discoverable.
- Give future services a visible but non-breaking place in the UI.
- Encourage non-members to create an account.
- Keep the design ready for future database-driven promotions, ratings and discovery feeds.

## Existing behaviour retained

- Existing MemberApp authentication/session logic.
- Existing Taxi routes and behaviour; Taxi files are not modified.
- Existing Restaurant ordering/cart/coupon/notification behaviour.
- Existing Fresh Food ordering/cart/coupon/notification/Gemini asynchronous route behaviour.
- Existing notification polling.
- Existing location picker data attribute.
- Existing bottom navigation routes.
- Existing `data-coming-soon` hooks for unfinished services.

## Home sections

1. Personalized welcome header.
2. Global service search.
3. Location selector.
4. Manipur-first hero/value proposition.
5. Service discovery grid.
6. Configurable-ready promotion slot (`home-main`) using demo content.
7. Popular near-you seller/service cards with simulated rating/review counts.
8. Explore Manipur discovery cards.
9. Member conversion CTA for logged-out users / profile CTA for logged-in users.
10. Existing bottom navigation and cart/notification widgets.

## Future admin integration

Promotion cards use `data-promo-slot` markers so a future Promotions/CMS module can replace the demo content with database-driven campaigns without changing the visual structure.

Ratings and review counts are currently simulated presentation values. Replace them later with real review aggregates.
