-- ============================================================
-- ManipurApp Restaurant Module
-- Migration: 001_create_restaurant_foundation.sql
--
-- Scope:
--   Restaurant business/profile + hours + menu foundation.
--
-- Important:
--   - Does NOT modify Taxi tables.
--   - Does NOT create a generic orders/payments system.
--   - Does NOT create Fresh Food tables.
--   - A restaurant belongs to an existing businesses row.
--   - tenant_id is stored on restaurant tables for tenant isolation.
--
-- Compatible with MySQL 8.x / MariaDB.
-- ============================================================

SET NAMES utf8mb4;

START TRANSACTION;


-- ============================================================
-- 1. Restaurant Profiles
-- One restaurant profile belongs to one existing business.
-- One tenant may own many restaurant businesses.
-- ============================================================

CREATE TABLE IF NOT EXISTS `restaurant_profiles` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,

    `tenant_id` bigint UNSIGNED NOT NULL,
    `business_id` bigint UNSIGNED NOT NULL,

    `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `logo_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `cover_image_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,

    `cuisine_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,

    `minimum_order_amount` decimal(12,2) NOT NULL DEFAULT '0.00',

    `delivery_available` tinyint(1) NOT NULL DEFAULT '1',
    `pickup_available` tinyint(1) NOT NULL DEFAULT '1',

    `delivery_fee` decimal(12,2) NOT NULL DEFAULT '0.00',
    `free_delivery_above` decimal(12,2) DEFAULT NULL,

    `estimated_prep_minutes` smallint UNSIGNED DEFAULT NULL,

    `accepting_orders` tinyint(1) NOT NULL DEFAULT '1',

    `status` enum('Active','Inactive','Suspended')
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        NOT NULL DEFAULT 'Active',

    `created_by` bigint UNSIGNED DEFAULT NULL,
    `updated_by` bigint UNSIGNED DEFAULT NULL,

    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    `deleted_at` timestamp NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    UNIQUE KEY `restaurant_profiles_uuid_unique` (`uuid`),
    UNIQUE KEY `restaurant_profiles_business_unique` (`business_id`),

    KEY `restaurant_profiles_tenant_index` (`tenant_id`),
    KEY `restaurant_profiles_status_index` (`status`),
    KEY `restaurant_profiles_accepting_orders_index` (`accepting_orders`),

    CONSTRAINT `fk_restaurant_profiles_business`
        FOREIGN KEY (`business_id`)
        REFERENCES `businesses` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 2. Restaurant Hours
-- day_of_week:
--   0 = Sunday
--   1 = Monday
--   ...
--   6 = Saturday
--
-- A restaurant can have one opening interval per day initially.
-- Split shifts can be supported later if required.
-- ============================================================

CREATE TABLE IF NOT EXISTS `restaurant_hours` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,

    `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,

    `tenant_id` bigint UNSIGNED NOT NULL,
    `restaurant_id` bigint UNSIGNED NOT NULL,

    `day_of_week` tinyint UNSIGNED NOT NULL,

    `opens_at` time DEFAULT NULL,
    `closes_at` time DEFAULT NULL,

    `is_closed` tinyint(1) NOT NULL DEFAULT '0',

    `created_by` bigint UNSIGNED DEFAULT NULL,
    `updated_by` bigint UNSIGNED DEFAULT NULL,

    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),

    UNIQUE KEY `restaurant_hours_uuid_unique` (`uuid`),
    UNIQUE KEY `restaurant_hours_day_unique`
        (`restaurant_id`, `day_of_week`),

    KEY `restaurant_hours_tenant_index` (`tenant_id`),
    KEY `restaurant_hours_restaurant_index` (`restaurant_id`),

    CONSTRAINT `fk_restaurant_hours_restaurant`
        FOREIGN KEY (`restaurant_id`)
        REFERENCES `restaurant_profiles` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 3. Menu Categories
-- ============================================================

CREATE TABLE IF NOT EXISTS `restaurant_menu_categories` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,

    `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,

    `tenant_id` bigint UNSIGNED NOT NULL,
    `restaurant_id` bigint UNSIGNED NOT NULL,

    `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,

    `sort_order` int NOT NULL DEFAULT '0',

    `status` enum('Active','Inactive')
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        NOT NULL DEFAULT 'Active',

    `created_by` bigint UNSIGNED DEFAULT NULL,
    `updated_by` bigint UNSIGNED DEFAULT NULL,

    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    `deleted_at` timestamp NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    UNIQUE KEY `restaurant_menu_categories_uuid_unique` (`uuid`),

    KEY `restaurant_menu_categories_tenant_index` (`tenant_id`),
    KEY `restaurant_menu_categories_restaurant_index` (`restaurant_id`),
    KEY `restaurant_menu_categories_sort_index`
        (`restaurant_id`, `sort_order`),
    KEY `restaurant_menu_categories_status_index` (`status`),

    CONSTRAINT `fk_restaurant_menu_categories_restaurant`
        FOREIGN KEY (`restaurant_id`)
        REFERENCES `restaurant_profiles` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 4. Menu Items
--
-- The base price is stored here.
-- The item is deliberately snapshot-independent at this stage;
-- order-time price snapshots will be stored in the order module.
-- ============================================================

CREATE TABLE IF NOT EXISTS `restaurant_menu_items` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,

    `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,

    `tenant_id` bigint UNSIGNED NOT NULL,
    `restaurant_id` bigint UNSIGNED NOT NULL,
    `category_id` bigint UNSIGNED DEFAULT NULL,

    `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `slug` varchar(220) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,

    `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,

    `image_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,

    `price` decimal(12,2) NOT NULL DEFAULT '0.00',

    `is_veg` tinyint(1) NOT NULL DEFAULT '0',

    `is_available` tinyint(1) NOT NULL DEFAULT '1',

    `sort_order` int NOT NULL DEFAULT '0',

    `status` enum('Active','Inactive')
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        NOT NULL DEFAULT 'Active',

    `created_by` bigint UNSIGNED DEFAULT NULL,
    `updated_by` bigint UNSIGNED DEFAULT NULL,

    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    `deleted_at` timestamp NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    UNIQUE KEY `restaurant_menu_items_uuid_unique` (`uuid`),

    UNIQUE KEY `restaurant_menu_items_restaurant_slug_unique`
        (`restaurant_id`, `slug`),

    KEY `restaurant_menu_items_tenant_index` (`tenant_id`),
    KEY `restaurant_menu_items_restaurant_index` (`restaurant_id`),
    KEY `restaurant_menu_items_category_index` (`category_id`),
    KEY `restaurant_menu_items_available_index`
        (`restaurant_id`, `is_available`),
    KEY `restaurant_menu_items_sort_index`
        (`restaurant_id`, `category_id`, `sort_order`),
    KEY `restaurant_menu_items_status_index` (`status`),

    CONSTRAINT `fk_restaurant_menu_items_restaurant`
        FOREIGN KEY (`restaurant_id`)
        REFERENCES `restaurant_profiles` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT `fk_restaurant_menu_items_category`
        FOREIGN KEY (`category_id`)
        REFERENCES `restaurant_menu_categories` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 5. Item Variants
--
-- Example:
--   Chicken Burger
--      Regular  ₹150
--      Large    ₹200
--
-- If an item has no variants, its base price remains in
-- restaurant_menu_items.price.
-- ============================================================

CREATE TABLE IF NOT EXISTS `restaurant_menu_item_variants` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,

    `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,

    `tenant_id` bigint UNSIGNED NOT NULL,
    `restaurant_id` bigint UNSIGNED NOT NULL,
    `item_id` bigint UNSIGNED NOT NULL,

    `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,

    `price` decimal(12,2) NOT NULL DEFAULT '0.00',

    `is_available` tinyint(1) NOT NULL DEFAULT '1',

    `sort_order` int NOT NULL DEFAULT '0',

    `status` enum('Active','Inactive')
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        NOT NULL DEFAULT 'Active',

    `created_by` bigint UNSIGNED DEFAULT NULL,
    `updated_by` bigint UNSIGNED DEFAULT NULL,

    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    `deleted_at` timestamp NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    UNIQUE KEY `restaurant_menu_item_variants_uuid_unique` (`uuid`),

    UNIQUE KEY `restaurant_menu_item_variants_item_name_unique`
        (`item_id`, `name`),

    KEY `restaurant_menu_item_variants_tenant_index` (`tenant_id`),
    KEY `restaurant_menu_item_variants_restaurant_index` (`restaurant_id`),
    KEY `restaurant_menu_item_variants_item_index` (`item_id`),
    KEY `restaurant_menu_item_variants_available_index`
        (`item_id`, `is_available`),

    CONSTRAINT `fk_restaurant_menu_item_variants_restaurant`
        FOREIGN KEY (`restaurant_id`)
        REFERENCES `restaurant_profiles` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT `fk_restaurant_menu_item_variants_item`
        FOREIGN KEY (`item_id`)
        REFERENCES `restaurant_menu_items` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 6. Modifier Groups
--
-- Examples:
--   Spice Level
--   Add-ons
--   Extra Toppings
-- ============================================================

CREATE TABLE IF NOT EXISTS `restaurant_modifier_groups` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,

    `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,

    `tenant_id` bigint UNSIGNED NOT NULL,
    `restaurant_id` bigint UNSIGNED NOT NULL,

    `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,

    `selection_type` enum('SINGLE','MULTIPLE')
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        NOT NULL DEFAULT 'SINGLE',

    `min_selections` tinyint UNSIGNED NOT NULL DEFAULT '0',
    `max_selections` tinyint UNSIGNED DEFAULT NULL,

    `is_required` tinyint(1) NOT NULL DEFAULT '0',

    `sort_order` int NOT NULL DEFAULT '0',

    `status` enum('Active','Inactive')
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        NOT NULL DEFAULT 'Active',

    `created_by` bigint UNSIGNED DEFAULT NULL,
    `updated_by` bigint UNSIGNED DEFAULT NULL,

    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    `deleted_at` timestamp NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    UNIQUE KEY `restaurant_modifier_groups_uuid_unique` (`uuid`),

    KEY `restaurant_modifier_groups_tenant_index` (`tenant_id`),
    KEY `restaurant_modifier_groups_restaurant_index` (`restaurant_id`),
    KEY `restaurant_modifier_groups_sort_index`
        (`restaurant_id`, `sort_order`),

    CONSTRAINT `fk_restaurant_modifier_groups_restaurant`
        FOREIGN KEY (`restaurant_id`)
        REFERENCES `restaurant_profiles` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 7. Modifier Options
--
-- Examples:
--   Spice Level:
--      Normal      +₹0
--      Spicy       +₹0
--      Extra Spicy +₹20
--
--   Add-ons:
--      Extra Egg   +₹20
--      Extra Pork  +₹80
-- ============================================================

CREATE TABLE IF NOT EXISTS `restaurant_modifier_options` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,

    `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,

    `tenant_id` bigint UNSIGNED NOT NULL,
    `restaurant_id` bigint UNSIGNED NOT NULL,
    `modifier_group_id` bigint UNSIGNED NOT NULL,

    `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,

    `price_adjustment` decimal(12,2) NOT NULL DEFAULT '0.00',

    `is_available` tinyint(1) NOT NULL DEFAULT '1',

    `sort_order` int NOT NULL DEFAULT '0',

    `status` enum('Active','Inactive')
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        NOT NULL DEFAULT 'Active',

    `created_by` bigint UNSIGNED DEFAULT NULL,
    `updated_by` bigint UNSIGNED DEFAULT NULL,

    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    `deleted_at` timestamp NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    UNIQUE KEY `restaurant_modifier_options_uuid_unique` (`uuid`),

    UNIQUE KEY `restaurant_modifier_options_group_name_unique`
        (`modifier_group_id`, `name`),

    KEY `restaurant_modifier_options_tenant_index` (`tenant_id`),
    KEY `restaurant_modifier_options_restaurant_index` (`restaurant_id`),
    KEY `restaurant_modifier_options_group_index` (`modifier_group_id`),
    KEY `restaurant_modifier_options_available_index`
        (`modifier_group_id`, `is_available`),

    CONSTRAINT `fk_restaurant_modifier_options_restaurant`
        FOREIGN KEY (`restaurant_id`)
        REFERENCES `restaurant_profiles` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT `fk_restaurant_modifier_options_group`
        FOREIGN KEY (`modifier_group_id`)
        REFERENCES `restaurant_modifier_groups` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 8. Item ↔ Modifier Group
--
-- This allows the same modifier group to be reused by several
-- menu items belonging to the same restaurant.
-- ============================================================

CREATE TABLE IF NOT EXISTS `restaurant_menu_item_modifier_groups` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,

    `tenant_id` bigint UNSIGNED NOT NULL,
    `restaurant_id` bigint UNSIGNED NOT NULL,

    `item_id` bigint UNSIGNED NOT NULL,
    `modifier_group_id` bigint UNSIGNED NOT NULL,

    `sort_order` int NOT NULL DEFAULT '0',

    PRIMARY KEY (`id`),

    UNIQUE KEY `restaurant_item_modifier_group_unique`
        (`item_id`, `modifier_group_id`),

    KEY `restaurant_item_modifier_groups_tenant_index` (`tenant_id`),
    KEY `restaurant_item_modifier_groups_restaurant_index`
        (`restaurant_id`),
    KEY `restaurant_item_modifier_groups_item_index` (`item_id`),
    KEY `restaurant_item_modifier_groups_group_index`
        (`modifier_group_id`),

    CONSTRAINT `fk_restaurant_item_modifier_groups_restaurant`
        FOREIGN KEY (`restaurant_id`)
        REFERENCES `restaurant_profiles` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT `fk_restaurant_item_modifier_groups_item`
        FOREIGN KEY (`item_id`)
        REFERENCES `restaurant_menu_items` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT `fk_restaurant_item_modifier_groups_group`
        FOREIGN KEY (`modifier_group_id`)
        REFERENCES `restaurant_modifier_groups` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


COMMIT;


-- ============================================================
-- Verification
-- No information_schema access required.
-- ============================================================

SHOW TABLES LIKE 'restaurant%';
