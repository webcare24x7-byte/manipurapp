-- ============================================================
-- Taxi Module Migration 009
-- Included KM for service pricing
-- ============================================================
-- Included KM is the distance covered by the base/per-ride or
-- rental package price before Extra KM Rate applies.
-- Compatible with older MySQL / MariaDB versions.
-- Safe to run more than once.
-- ============================================================

SET @column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'taxi_services'
      AND COLUMN_NAME = 'included_km'
);
SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE taxi_services ADD COLUMN included_km DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER minimum_fare',
    'SELECT "taxi_services.included_km already exists" AS message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
