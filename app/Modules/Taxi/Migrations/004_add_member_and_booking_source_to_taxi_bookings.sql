-- Taxi booking customer association upgrade.
--
-- The member_id and booking_source columns are already present in the
-- current production database. This migration adds the tenant/member index
-- and the member foreign key only.
--
-- Run after the following columns have been added to taxi_bookings:
--   member_id BIGINT UNSIGNED NULL
--   booking_source ENUM('APP','PHONE','WALK_IN','ADMIN') NOT NULL DEFAULT 'APP'

ALTER TABLE taxi_bookings
    ADD KEY idx_taxi_booking_member (
        tenant_id,
        member_id
    ),
    ADD CONSTRAINT fk_taxi_booking_member
        FOREIGN KEY (member_id)
        REFERENCES members(id)
        ON DELETE SET NULL;
