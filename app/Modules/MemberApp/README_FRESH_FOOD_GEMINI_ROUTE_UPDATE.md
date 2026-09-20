# Fresh Food Gemini Route v1.0.1

## Important order-flow change

Fresh Food delivery orders now follow the same asynchronous pattern as Taxi route processing:

1. Member taps **Place Fresh Food Order**.
2. The order is created immediately without waiting for Gemini.
3. The browser opens the new order page.
4. JavaScript calls `POST /member/fresh-food/orders/{id}/process-route`.
5. The server performs the Gemini road-distance/ETA request after the order already exists.
6. The order page polls the status endpoint every few seconds and updates the delivery distance, ETA, delivery fee and total when Gemini completes.

This prevents a slow Gemini request from blocking the order-creation POST. The PHP session lock is also released before the Gemini request, matching the Taxi implementation.

## Route state

`fresh_food_orders.route_status`:

- `PENDING` — order created, route not started
- `PROCESSING` — Gemini route request is running
- `COMPLETED` — distance/ETA/fee/total committed
- `FAILED` — route calculation failed; the member can retry by revisiting the order

`route_error` stores a safe error message and `route_calculated_at` records successful completion.

## Pricing

The initial delivery order is created with delivery fee `0.00` because Gemini has not run yet. Once the road distance is returned, the MemberApp recalculates the delivery fee using the Fresh Food business settings and updates the final order total. No payment is currently implemented, so this asynchronous total update does not capture or charge money.

## Taxi / Restaurant safety

Taxi and Restaurant files are not modified by this update.
