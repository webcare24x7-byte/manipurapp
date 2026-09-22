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


## Free-form Smart Ask (backend test extension)

The AI module now also supports a free-form question endpoint:

- `POST /ai-assistant/freeform`
- Existing category-based `POST /ai-assistant/ask` remains unchanged.

The free-form workflow uses Gemini function calling with three read-only application tools:

- `search_tourism`
- `search_restaurants`
- `search_taxi`

Gemini may call multiple tools for a single question. PHP executes each tool against the current tenant's live database, sends the tool results back to Gemini through the Interactions API, and repeats until Gemini returns a final answer.

The first implementation intentionally supports planning/search only. It does not create bookings, change records, expose customer bookings, or bypass tenant filtering.

No database migration is required. Free-form questions are stored in the existing `ai_questions` table with `ai_scope = FREEFORM`.


### Suggested backend test questions

1. `I have 3 days in Manipur with my family. What places should we visit, where can we eat Manipuri food, and what taxi service could we use?`
2. `I am staying around Imphal. Suggest a simple day plan with two tourism places, one restaurant with vegetarian options, and a local taxi service.`
3. `We want to visit Loktak Lake. What tourism records do you have, where can we eat nearby if available, and which taxi services are configured for the trip?`
4. `Find a restaurant with pork dishes and then tell me which taxi services could take a visitor around Imphal.`
5. `What active taxi services are configured for airport transfers, and what tourism options would make sense for a first-time visitor?`

For each test, inspect the tool trace in the AI admin page. The trace should show which tools Gemini actually selected and how many database records each tool returned.


## Smart Ask AI providers

Free-form Smart Ask supports two provider adapters without changing the ManipurApp tool layer:

- `gemini` — Google Gemini API (existing/default provider).
- `groq` — Groq OpenAI-compatible API, using local application-side function calling.

Select the provider in `Config/provider.php`:

```php
'freeform_provider' => 'gemini', // or 'groq'
```

For Groq, configure `Config/groq.php` with your Groq API key. The default model is `openai/gpt-oss-120b`. Groq receives the same ManipurApp function definitions; PHP executes the tools and sends the results back to Groq for the final answer.

The existing category-based AI remains on Gemini and is not changed by this provider switch.

### Groq optional tool arguments

The Groq adapter normalizes optional scalar tool properties as nullable in the provider-specific JSON Schema and removes explicit `null` values before executing ManipurApp tools. This prevents Groq tool-call validation failures when a model returns `null` for an optional argument such as `max_price`. Gemini tool definitions are not changed by this provider-specific normalization.
