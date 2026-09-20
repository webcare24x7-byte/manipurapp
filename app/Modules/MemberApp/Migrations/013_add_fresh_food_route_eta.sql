SET NAMES utf8mb4;

/*
 * Fresh Food route state is persisted so Gemini is never called from the
 * order-creation request. The order is created first, then MemberApp starts
 * route processing via AJAX. This migration is safe to re-run.
 */

SET @db = DATABASE();

SET @sql = IF(
    EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='fresh_food_orders' AND COLUMN_NAME='eta_minutes'),
    'SELECT 1',
    'ALTER TABLE fresh_food_orders ADD COLUMN eta_minutes INT UNSIGNED DEFAULT NULL AFTER distance_km'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='fresh_food_orders' AND COLUMN_NAME='route_status'),
    'SELECT 1',
    "ALTER TABLE fresh_food_orders ADD COLUMN route_status ENUM('PENDING','PROCESSING','COMPLETED','FAILED') NOT NULL DEFAULT 'PENDING' AFTER eta_minutes"
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='fresh_food_orders' AND COLUMN_NAME='route_error'),
    'SELECT 1',
    'ALTER TABLE fresh_food_orders ADD COLUMN route_error VARCHAR(1000) DEFAULT NULL AFTER route_status'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='fresh_food_orders' AND COLUMN_NAME='route_calculated_at'),
    'SELECT 1',
    'ALTER TABLE fresh_food_orders ADD COLUMN route_calculated_at DATETIME DEFAULT NULL AFTER route_error'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* Existing rows: preserve already-complete routes. Older delivery orders without
 * an ETA remain PENDING so they can be recalculated with Gemini when opened.
 * Delivery orders keep their stored initial delivery fee/total if Gemini fails. */
UPDATE fresh_food_orders
SET route_status = 'COMPLETED'
WHERE order_type = 'PICKUP'
   OR (distance_km IS NOT NULL AND eta_minutes IS NOT NULL);
