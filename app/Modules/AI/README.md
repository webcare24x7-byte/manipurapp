# ManipurApp AI Module

The ManipurApp AI module provides AI-assisted discovery, search, and planning
while keeping ManipurApp application services and the live database as the
source of truth.

The module supports category-based AI as well as free-form Smart Ask.

---

## Current AI Scopes

- Tourism overview
- Destinations
- Stays
- Packages
- Local guides
- Experiences
- Events & festivals
- Restaurants & menus
- Restaurant menu items
- Free-form Smart Ask

---

## Restaurant AI

Restaurant AI reads the existing tenant restaurant schema rather than
maintaining a second AI dataset.

It uses:

- `restaurant_profiles`
- `businesses`
- `restaurant_menu_categories`
- `restaurant_menu_items`
- `restaurant_menu_item_variants`
- `restaurant_modifier_groups`
- `restaurant_modifier_options`
- item/modifier relationships

It supports natural-language filtering for:

- city, district, and state from actual restaurant/business records
- vegetarian / non-vegetarian
- proteins such as chicken, pork, beef/buff, mutton, fish/seafood, egg and paneer
- menu item, category, and cuisine terms found in tenant data
- price ceilings such as under/below/less than/within ₹300
- delivery and pickup
- menu variants and modifier-group context

No new database migration is required for the Restaurant AI scopes. The existing
`ai_scope` column from migration 012 is sufficient.

The AI prompt treats the supplied live database context as the source of truth
and is instructed not to invent restaurant or menu facts.

---

# Free-form Smart Ask

The AI module supports a free-form question endpoint:

- `POST /ai-assistant/freeform`

The existing category-based:

- `POST /ai-assistant/ask`

remains unchanged.

Smart Ask allows users to ask natural-language questions that can involve
multiple ManipurApp services.

For example:

```text
I have 3 days in Manipur with my family.
What places should we visit, where can we eat Manipuri food,
and what taxi service could we use?