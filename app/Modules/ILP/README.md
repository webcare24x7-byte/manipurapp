# ManipurApp Digital ILP Helper

The Digital ILP Helper is a deterministic, backend-first Manipur Inner Line Permit guidance module.

It does **not** use AI and does not submit or issue ILPs. It provides a structured pre-check of the likely permit category, explains the reason, produces a preparation checklist, and links users to the official Government of Manipur ILP portal.

## Current workflow

```text
User information
      ↓
PHP eligibility rules
      ↓
Permit category
      ↓
Checklist from database
      ↓
Official Government source
```

AI can be integrated later as a natural-language interface, but the underlying ILP rules and checklist remain application-controlled.

## Current permit categories

The first version covers the six categories currently described by the Government of Manipur ILP Online Portal:

- Temporary
- Regular
- Special Category
- Labour
- Half-Yearly Working
- Annual Working

Source: https://manipurilponline.mn.gov.in/aboutIlp.aspx

## Backend test page

After installing the module, authenticated administrators can open:

`/ilp`

The page allows testing of:

- whether the applicant is entering from outside Manipur
- purpose of visit
- planned duration
- sponsor/agency availability

The helper returns a likely permit category, confidence, reasons, missing information, and a preparation checklist.

## JSON endpoint

The backend also exposes:

`POST /ilp/api/analyze`

It accepts JSON or form data:

```json
{
  "outside_manipur": true,
  "purpose": "TOURISM",
  "duration_days": 7,
  "sponsor_available": false
}
```

## Important

The official Government of Manipur ILP portal and latest Government notifications remain authoritative. The helper must not be presented as an official permit issuer or legal determination.

Rules, fees, documents, forms, validity and procedures can change. The knowledge records include an official source URL and verification date so the information can be maintained explicitly.
