-- ============================================================
-- Taxi Module Migration 008
-- Route estimate commit/audit metadata
-- ============================================================
-- Calculate = preview from Gemini.
-- Commit = admin explicitly accepts the route estimate for the booking.
-- ============================================================

SET @column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'taxi_bookings'
      AND COLUMN_NAME = 'route_estimate_committed_at'
);
SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE taxi_bookings ADD COLUMN route_estimate_committed_at DATETIME NULL AFTER route_calculated_at',
    'SELECT "taxi_bookings.route_estimate_committed_at already exists" AS message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'taxi_bookings'
      AND COLUMN_NAME = 'route_estimate_committed_by'
);
SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE taxi_bookings ADD COLUMN route_estimate_committed_by BIGINT UNSIGNED NULL AFTER route_estimate_committed_at',
    'SELECT "taxi_bookings.route_estimate_committed_by already exists" AS message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'taxi_bookings'
      AND CONSTRAINT_NAME = 'fk_taxi_booking_route_commit_user'
);
SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE taxi_bookings ADD CONSTRAINT fk_taxi_booking_route_commit_user FOREIGN KEY (route_estimate_committed_by) REFERENCES users(id) ON DELETE SET NULL',
    'SELECT "fk_taxi_booking_route_commit_user already exists" AS message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
