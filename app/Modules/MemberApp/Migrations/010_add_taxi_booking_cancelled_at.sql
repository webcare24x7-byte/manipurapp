-- Run once after the Taxi booking table has been created.
-- MariaDB/MySQL compatible without relying on ADD COLUMN IF NOT EXISTS.
SET @column_exists := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'taxi_bookings'
      AND COLUMN_NAME = 'cancelled_at'
);
SET @sql := IF(
    @column_exists = 0,
    'ALTER TABLE taxi_bookings ADD COLUMN cancelled_at DATETIME NULL AFTER status',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
