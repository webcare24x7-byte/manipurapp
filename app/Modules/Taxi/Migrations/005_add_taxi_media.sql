-- Taxi Module 005
-- Add public-facing profile photos for vehicles and drivers.
-- Safe for the current Taxi database: existing rows receive NULL.

ALTER TABLE taxi_vehicles
    ADD COLUMN IF NOT EXISTS photo_path VARCHAR(500)
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        NULL AFTER seating_capacity;

ALTER TABLE taxi_drivers
    ADD COLUMN IF NOT EXISTS photo_path VARCHAR(500)
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        NULL AFTER availability;
