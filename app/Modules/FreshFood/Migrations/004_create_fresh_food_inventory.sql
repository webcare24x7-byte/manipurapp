SET NAMES utf8mb4;
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `fresh_food_inventory` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `fresh_food_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `current_quantity` decimal(12,3) NOT NULL DEFAULT 0.000,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fresh_food_inventory_uuid_unique` (`uuid`),
  UNIQUE KEY `fresh_food_inventory_product_unique` (`tenant_id`,`product_id`),
  KEY `fresh_food_inventory_tenant_index` (`tenant_id`),
  KEY `fresh_food_inventory_business_index` (`fresh_food_id`),
  KEY `fresh_food_inventory_product_index` (`product_id`),
  CONSTRAINT `fk_fresh_food_inventory_profile` FOREIGN KEY (`fresh_food_id`) REFERENCES `fresh_food_profiles` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_fresh_food_inventory_product` FOREIGN KEY (`product_id`) REFERENCES `fresh_food_products` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `fresh_food_inventory_movements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `fresh_food_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `inventory_id` bigint UNSIGNED NOT NULL,
  `movement_type` enum('OPENING','PURCHASE','SALE','RETURN','ADJUSTMENT','WASTE','DAMAGE') NOT NULL,
  `quantity_change` decimal(12,3) NOT NULL,
  `quantity_before` decimal(12,3) NOT NULL DEFAULT 0.000,
  `quantity_after` decimal(12,3) NOT NULL DEFAULT 0.000,
  `reference` varchar(200) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fresh_food_inventory_movements_uuid_unique` (`uuid`),
  KEY `fresh_food_inventory_movements_tenant_index` (`tenant_id`),
  KEY `fresh_food_inventory_movements_inventory_index` (`inventory_id`),
  KEY `fresh_food_inventory_movements_product_index` (`product_id`),
  KEY `fresh_food_inventory_movements_created_index` (`created_at`),
  CONSTRAINT `fk_fresh_food_inventory_movements_inventory` FOREIGN KEY (`inventory_id`) REFERENCES `fresh_food_inventory` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
