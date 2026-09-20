# MemberApp Notifications v0.8.0

Adds a shared member notification center for Taxi booking and Restaurant order status changes.

## Migration

Run:

`MemberApp/Migrations/011_create_member_notifications.sql`

## Behaviour

- Restaurant order detail polls its status endpoint and refreshes when the backend status changes.
- Taxi booking detail remains on its existing status polling behaviour.
- Logged-in MemberApp screens show a persistent notification bell with unread badge.
- Home keeps the bell in the existing top header.
- New status-history events are materialized into `member_notifications` idempotently.
- The bell polls for new updates every 12 seconds.
- New updates show a small toast/bubble and increment the unread badge.
- `/member/notifications` lists notification history and marks notifications read when opened.
- Notification links open the relevant Taxi booking or Restaurant order.
- Notification generation is driven from existing Taxi and Restaurant status-history tables; no admin status workflow changes are required.

## Commercial Rental
- Commercial rental request status changes are materialized from `commercial_rental_request_status_history`.
- The shared notification badge/toast supports the `commercial_rental` source and links to the member's rental request detail.
