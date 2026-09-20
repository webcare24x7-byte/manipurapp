# ManipurApp AI Module

## Current AI scopes

- Tourism overview
- Destinations
- Stays
- Packages
- Local guides
- Experiences
- Events & festivals
- Restaurants & menus
- Restaurant menu items

## Restaurant AI

Restaurant AI reads the existing tenant restaurant schema rather than maintaining a second AI dataset. It uses `restaurant_profiles`, `businesses`, `restaurant_menu_categories`, `restaurant_menu_items`, `restaurant_menu_item_variants`, `restaurant_modifier_groups`, `restaurant_modifier_options`, and item/modifier relationships.

It supports natural-language filtering for:

- city/state/country from actual restaurant/business records
- vegetarian / non-vegetarian
- proteins such as chicken, pork, beef/buff, mutton, fish/seafood, egg and paneer
- menu item/category/cuisine terms found in the tenant data
- price ceilings such as under/below/less than/within ₹300
- delivery and pickup
- menu variants and modifier-group context

No new database migration is required for the Restaurant AI scopes. The existing `ai_scope` column from migration 012 is sufficient.

The AI prompt treats the supplied live database context as the source of truth and is instructed not to invent restaurant or menu facts.
