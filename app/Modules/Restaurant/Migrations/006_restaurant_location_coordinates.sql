-- ============================================================
-- ManipurApp Restaurant Module
-- Migration: 006_restaurant_location_coordinates.sql
--
-- Adds exact restaurant coordinates for Member PWA discovery.
-- Coordinates are optional so existing restaurants remain valid.
--
-- latitude  : -90 to 90
-- longitude : -180 to 180
--
-- Run this migration once on an existing installation.
-- ============================================================

SET NAMES utf8mb4;

START TRANSACTION;

ALTER TABLE `restaurant_profiles`
    ADD COLUMN `latitude` decimal(10,7) DEFAULT NULL
        AFTER `accepting_orders`,
    ADD COLUMN `longitude` decimal(10,7) DEFAULT NULL
        AFTER `latitude`,
    ADD KEY `restaurant_profiles_location_index` (`latitude`, `longitude`);

COMMIT;

-- Verification
SHOW COLUMNS FROM `restaurant_profiles` LIKE 'latitude';
SHOW COLUMNS FROM `restaurant_profiles` LIKE 'longitude';
