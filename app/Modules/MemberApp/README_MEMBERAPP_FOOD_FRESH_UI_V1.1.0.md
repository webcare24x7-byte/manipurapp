# MemberApp Food + Fresh Food UI/UX v1.1.0

This update is a presentation/discovery upgrade for the MemberApp PWA.

## Retained behaviours

- Existing MemberApp authentication/session behaviour is unchanged.
- Taxi module/files are untouched.
- Existing Restaurant cart, checkout, coupon, order and notification flows are unchanged.
- Existing Fresh Food cart, checkout, coupon, order and asynchronous Gemini route processing are unchanged.
- Existing geolocation buttons remain functional.
- Existing MemberApp routes and bottom navigation URLs remain intact.
- No new database migration is required for the UI upgrade.

## New discovery presentation

Restaurant landing screen now includes:

- Search and cuisine discovery chips.
- Promotional hero banner.
- Top Rated Restaurants.
- Seller star rating + review count (demo-generated for now).
- Popular Dishes using real menu items where available.
- Dish star rating + review count (demo-generated for now).
- Exclusive Offers / promotion cards.
- Popular Near You seller cards.
- Existing restaurant cart and notification widgets.

Fresh Food landing screen now includes:

- Search and category discovery chips.
- Fresh/local promotional hero banner.
- Top Rated Stores.
- Store star rating + review count (demo-generated for now).
- Popular Fresh Picks using real products where available.
- Product star rating + review count (demo-generated for now).
- Special Offers / promotion cards.
- Existing Fresh Food cart and notification widgets.

## Admin-ready promotion slots

The promotion arrays in:

- `Views/Restaurant/index.php`
- `Views/FreshFood/index.php`

are intentionally isolated demo content. The HTML uses `data-promo-slot` markers so a future admin Promotion/CMS module can replace the demo cards with database-driven campaigns without changing the MemberApp ordering flow.

## Review data

Real review tables/aggregation are not introduced in this version. Rating values and review counts are deterministic demo values based on seller/product IDs so the UI is stable during testing. Replace the small view helpers with real review aggregates when the review module is implemented.

## Discovery payload

Two lightweight service methods were added:

- `MemberRestaurantService::featuredItems()`
- `MemberFreshFoodService::featuredProducts()`

They reuse existing catalog data and do not alter ordering/cart behaviour. Fresh Food product discovery also exposes the existing product `image_path` field for presentation.
