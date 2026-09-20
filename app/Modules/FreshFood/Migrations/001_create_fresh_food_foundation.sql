SET NAMES utf8mb4;
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `fresh_food_profiles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `business_id` bigint UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `logo_path` varchar(500) DEFAULT NULL,
  `cover_image_path` varchar(500) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `delivery_available` tinyint(1) NOT NULL DEFAULT 1,
  `pickup_available` tinyint(1) NOT NULL DEFAULT 1,
  `max_delivery_distance_km` decimal(8,2) DEFAULT NULL,
  `minimum_delivery_fee` decimal(12,2) NOT NULL DEFAULT 20.00,
  `included_delivery_distance_km` decimal(8,2) NOT NULL DEFAULT 3.00,
  `additional_delivery_fee_per_km` decimal(12,2) NOT NULL DEFAULT 10.00,
  `preorder_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `preorder_min_advance_hours` smallint UNSIGNED NOT NULL DEFAULT 24,
  `preorder_delivery_fee` decimal(12,2) NOT NULL DEFAULT 20.00,
  `estimated_packing_minutes_min` smallint UNSIGNED NOT NULL DEFAULT 10,
  `estimated_packing_minutes_max` smallint UNSIGNED NOT NULL DEFAULT 20,
  `accepting_orders` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('Active','Inactive','Suspended') NOT NULL DEFAULT 'Active',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fresh_food_profiles_uuid_unique` (`uuid`),
  UNIQUE KEY `fresh_food_profiles_business_unique` (`business_id`),
  KEY `fresh_food_profiles_tenant_index` (`tenant_id`),
  KEY `fresh_food_profiles_location_index` (`latitude`,`longitude`),
  KEY `fresh_food_profiles_status_index` (`status`),
  CONSTRAINT `fk_fresh_food_profiles_business` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `fresh_food_hours` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `fresh_food_id` bigint UNSIGNED NOT NULL,
  `day_of_week` tinyint UNSIGNED NOT NULL,
  `opens_at` time DEFAULT NULL,
  `closes_at` time DEFAULT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fresh_food_hours_uuid_unique` (`uuid`),
  UNIQUE KEY `fresh_food_hours_day_unique` (`fresh_food_id`,`day_of_week`),
  KEY `fresh_food_hours_tenant_index` (`tenant_id`),
  CONSTRAINT `fk_fresh_food_hours_profile` FOREIGN KEY (`fresh_food_id`) REFERENCES `fresh_food_profiles` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
