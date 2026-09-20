# ManipurApp Fresh Food & Grocery Module

## Current version
0.4.0

## Included
- Multi-tenant Fresh Food businesses
- Business location, delivery/pickup and preorder configuration
- Categories with soft delete/restore
- Products with units, variable-weight rules and availability
- Product-level discounts: NONE, PERCENT or FLAT
- Inventory with auditable stock movements
- Admin test orders
- Inventory-aware order workflow
- Fresh Food coupons: percentage or flat, minimum order, maximum discount, dates, usage limit and active/inactive state
- Server-side discount/coupon calculation and order snapshots

## Order pricing
Gross item subtotal -> product/item discounts -> coupon discount -> admin manual discount -> delivery fee = total.
Coupons apply to the net item subtotal after product discounts and never to delivery fees.

## Migrations
Run 001 through 012 in order. Migrations 009-012 add discounts, coupons, permissions and administrator mappings.

## Admin routes
- /fresh-food
- /fresh-food/businesses
- /fresh-food/categories
- /fresh-food/products
- /fresh-food/inventory
- /fresh-food/orders
- /fresh-food/coupons

MemberApp integration is intentionally not included in this release.
