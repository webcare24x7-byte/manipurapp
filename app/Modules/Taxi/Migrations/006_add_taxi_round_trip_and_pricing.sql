-- Taxi Module 006
-- Add round-trip booking support and service pricing modes.
-- Existing booking/service data is preserved.

ALTER TABLE taxi_bookings
    ADD COLUMN IF NOT EXISTS trip_type
        ENUM('ONE_WAY','ROUND_TRIP')
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        NOT NULL DEFAULT 'ONE_WAY'
        AFTER booking_source,
    ADD COLUMN IF NOT EXISTS return_scheduled_at DATETIME NULL
        AFTER scheduled_at;

ALTER TABLE taxi_services
    ADD COLUMN IF NOT EXISTS pricing_mode
        ENUM('PER_RIDE','PER_KM','PER_DAY','PER_KM_WITH_MINIMUM','CUSTOM_QUOTE')
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        NOT NULL DEFAULT 'PER_RIDE'
        AFTER description,
    ADD COLUMN IF NOT EXISTS daily_rate DECIMAL(12,2) NOT NULL DEFAULT 0.00
        AFTER minimum_fare,
    ADD COLUMN IF NOT EXISTS extra_km_rate DECIMAL(12,2) NOT NULL DEFAULT 0.00
        AFTER daily_rate;
