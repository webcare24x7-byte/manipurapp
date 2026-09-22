# MemberApp — Smart AI + Digital ILP Integration

This update is additive to the existing MemberApp.

## Smart AI

The existing home-screen **Plan with AI** triggers now open the free-form Smart AI question form.

MemberApp calls:

`POST /member/ai/freeform`

The endpoint uses the existing AI module's `AIFreeformService` and the configured provider. The current Groq architecture is one provider request returning a short message plus tool calls; PHP executes the tools and returns the verified application results. No second provider request is made.

The previous category-based `/member/ai/ask` route remains available and unchanged.

Smart AI requires a MemberApp login and CSRF validation.

## Digital ILP Helper

MemberApp adds:

`GET /member/ilp`

The page is informational and reads the active ILP permit categories from the Digital ILP Helper module. It does not duplicate the government application workflow.

The official Government of Manipur ILP portal remains the authoritative destination for current application procedures, forms, fees and requirements.

## Existing behavior

Existing MemberApp routes, services, restaurant/taxi/fresh-food/tourism/commercial-rental workflows, navigation and booking behavior are retained. The changes are limited to the new Smart AI trigger/form, the member-facing ILP information page, and home-screen entry points.
