SET NAMES utf8mb4;
START TRANSACTION;
CREATE TABLE IF NOT EXISTS `fresh_food_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, `uuid` char(36) NOT NULL, `tenant_id` bigint UNSIGNED NOT NULL, `fresh_food_id` bigint UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL, `description` text DEFAULT NULL, `sort_order` int NOT NULL DEFAULT 0, `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint UNSIGNED DEFAULT NULL, `updated_by` bigint UNSIGNED DEFAULT NULL, `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `fresh_food_categories_uuid_unique` (`uuid`), KEY `fresh_food_categories_tenant_index` (`tenant_id`), KEY `fresh_food_categories_business_index` (`fresh_food_id`),
  CONSTRAINT `fk_fresh_food_categories_profile` FOREIGN KEY (`fresh_food_id`) REFERENCES `fresh_food_profiles` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `fresh_food_products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, `uuid` char(36) NOT NULL, `tenant_id` bigint UNSIGNED NOT NULL, `fresh_food_id` bigint UNSIGNED NOT NULL, `category_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(200) NOT NULL, `slug` varchar(220) NOT NULL, `description` text DEFAULT NULL, `image_path` varchar(500) DEFAULT NULL,
  `unit` enum('piece','kg','gram','litre','ml','pack','dozen','box') NOT NULL DEFAULT 'piece', `price` decimal(12,2) NOT NULL DEFAULT 0.00, `min_order_quantity` decimal(12,3) NOT NULL DEFAULT 1.000, `increment_quantity` decimal(12,3) NOT NULL DEFAULT 1.000,
  `is_variable_weight` tinyint(1) NOT NULL DEFAULT 0, `is_available` tinyint(1) NOT NULL DEFAULT 1, `sort_order` int NOT NULL DEFAULT 0, `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint UNSIGNED DEFAULT NULL, `updated_by` bigint UNSIGNED DEFAULT NULL, `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `fresh_food_products_uuid_unique` (`uuid`), UNIQUE KEY `fresh_food_products_slug_unique` (`fresh_food_id`,`slug`), KEY `fresh_food_products_tenant_index` (`tenant_id`), KEY `fresh_food_products_business_index` (`fresh_food_id`), KEY `fresh_food_products_category_index` (`category_id`),
  CONSTRAINT `fk_fresh_food_products_profile` FOREIGN KEY (`fresh_food_id`) REFERENCES `fresh_food_profiles` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_fresh_food_products_category` FOREIGN KEY (`category_id`) REFERENCES `fresh_food_categories` (`id`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;
