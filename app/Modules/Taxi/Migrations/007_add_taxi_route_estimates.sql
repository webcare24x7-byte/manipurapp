-- ============================================================
-- Taxi Module Migration 007
-- Gemini Route Distance / ETA Snapshot Fields
-- ============================================================
--
-- Compatible with older MySQL / MariaDB versions.
-- Does NOT use ADD COLUMN IF NOT EXISTS.
-- Safe to run more than once.
--
-- Route information is informational only and does not control fare.
-- ============================================================

SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'taxi_bookings'
      AND COLUMN_NAME = 'distance_km'
);
SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE taxi_bookings ADD COLUMN distance_km DECIMAL(10,2) NULL AFTER fare',
    'SELECT "taxi_bookings.distance_km already exists" AS message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'taxi_bookings'
      AND COLUMN_NAME = 'eta_minutes'
);
SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE taxi_bookings ADD COLUMN eta_minutes INT UNSIGNED NULL AFTER distance_km',
    'SELECT "taxi_bookings.eta_minutes already exists" AS message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'taxi_bookings'
      AND COLUMN_NAME = 'return_distance_km'
);
SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE taxi_bookings ADD COLUMN return_distance_km DECIMAL(10,2) NULL AFTER eta_minutes',
    'SELECT "taxi_bookings.return_distance_km already exists" AS message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'taxi_bookings'
      AND COLUMN_NAME = 'return_eta_minutes'
);
SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE taxi_bookings ADD COLUMN return_eta_minutes INT UNSIGNED NULL AFTER return_distance_km',
    'SELECT "taxi_bookings.return_eta_minutes already exists" AS message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'taxi_bookings'
      AND COLUMN_NAME = 'total_distance_km'
);
SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE taxi_bookings ADD COLUMN total_distance_km DECIMAL(10,2) NULL AFTER return_eta_minutes',
    'SELECT "taxi_bookings.total_distance_km already exists" AS message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'taxi_bookings'
      AND COLUMN_NAME = 'total_eta_minutes'
);
SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE taxi_bookings ADD COLUMN total_eta_minutes INT UNSIGNED NULL AFTER total_distance_km',
    'SELECT "taxi_bookings.total_eta_minutes already exists" AS message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'taxi_bookings'
      AND COLUMN_NAME = 'route_source'
);
SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE taxi_bookings ADD COLUMN route_source VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER total_eta_minutes',
    'SELECT "taxi_bookings.route_source already exists" AS message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'taxi_bookings'
      AND COLUMN_NAME = 'route_calculated_at'
);
SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE taxi_bookings ADD COLUMN route_calculated_at DATETIME NULL AFTER route_source',
    'SELECT "taxi_bookings.route_calculated_at already exists" AS message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
