-- Restaurant discounts + coupons
-- Safe for existing installations: adds missing columns and creates coupon table.

SET NAMES utf8mb4;

DELIMITER $$
DROP PROCEDURE IF EXISTS restaurant_add_column_if_missing$$
CREATE PROCEDURE restaurant_add_column_if_missing(
    IN p_table VARCHAR(128),
    IN p_column VARCHAR(128),
    IN p_definition TEXT
)
BEGIN
    DECLARE v_exists INT DEFAULT 0;
    SELECT COUNT(*) INTO v_exists
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = p_table
      AND COLUMN_NAME = p_column;
    IF v_exists = 0 THEN
        SET @restaurant_sql = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN `', p_column, '` ', p_definition);
        PREPARE restaurant_stmt FROM @restaurant_sql;
        EXECUTE restaurant_stmt;
        DEALLOCATE PREPARE restaurant_stmt;
    END IF;
END$$
DELIMITER ;

CALL restaurant_add_column_if_missing('restaurant_menu_items', 'discount_type', "ENUM('PERCENT','FLAT') NOT NULL DEFAULT 'PERCENT' AFTER price");
CALL restaurant_add_column_if_missing('restaurant_menu_items', 'discount_value', "DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER discount_type");

CALL restaurant_add_column_if_missing('restaurant_orders', 'gross_subtotal', "DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER customer_note");
CALL restaurant_add_column_if_missing('restaurant_orders', 'item_discount', "DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER gross_subtotal");
CALL restaurant_add_column_if_missing('restaurant_orders', 'coupon_code', "VARCHAR(80) DEFAULT NULL AFTER item_discount");
CALL restaurant_add_column_if_missing('restaurant_orders', 'coupon_discount', "DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER coupon_code");

CALL restaurant_add_column_if_missing('restaurant_order_items', 'original_unit_price', "DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER variant_name");
CALL restaurant_add_column_if_missing('restaurant_order_items', 'discount_type', "ENUM('PERCENT','FLAT') NOT NULL DEFAULT 'PERCENT' AFTER original_unit_price");
CALL restaurant_add_column_if_missing('restaurant_order_items', 'discount_value', "DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER discount_type");
CALL restaurant_add_column_if_missing('restaurant_order_items', 'discount_amount', "DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER discount_value");

DROP PROCEDURE IF EXISTS restaurant_add_column_if_missing;

CREATE TABLE IF NOT EXISTS restaurant_coupons (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  uuid CHAR(36) NOT NULL,
  tenant_id BIGINT UNSIGNED NOT NULL,
  restaurant_id BIGINT UNSIGNED NOT NULL,
  code VARCHAR(80) NOT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT DEFAULT NULL,
  discount_type ENUM('PERCENT','FLAT') NOT NULL DEFAULT 'PERCENT',
  discount_value DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  minimum_order_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  maximum_discount DECIMAL(12,2) DEFAULT NULL,
  starts_at DATETIME DEFAULT NULL,
  ends_at DATETIME DEFAULT NULL,
  usage_limit INT UNSIGNED DEFAULT NULL,
  used_count INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  created_by BIGINT UNSIGNED DEFAULT NULL,
  updated_by BIGINT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY restaurant_coupons_uuid_unique (uuid),
  UNIQUE KEY restaurant_coupons_code_unique (tenant_id, restaurant_id, code),
  KEY restaurant_coupons_restaurant_index (restaurant_id),
  KEY restaurant_coupons_status_index (restaurant_id, status),
  CONSTRAINT fk_restaurant_coupons_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurant_profiles (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Restaurant Coupons', 'restaurant.coupons.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.coupons.view');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Manage Restaurant Coupons', 'restaurant.coupons.manage'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.coupons.manage');
