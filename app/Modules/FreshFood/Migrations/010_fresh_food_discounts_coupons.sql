SET NAMES utf8mb4;

DELIMITER $$
DROP PROCEDURE IF EXISTS fresh_food_add_order_discount_columns$$
CREATE PROCEDURE fresh_food_add_order_discount_columns()
BEGIN
    DECLARE v1 INT DEFAULT 0; DECLARE v2 INT DEFAULT 0; DECLARE v3 INT DEFAULT 0;
    SELECT COUNT(*) INTO v1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='fresh_food_orders' AND COLUMN_NAME='coupon_code';
    IF v1=0 THEN ALTER TABLE fresh_food_orders ADD COLUMN coupon_code VARCHAR(80) DEFAULT NULL AFTER item_discount; END IF;
    SELECT COUNT(*) INTO v2 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='fresh_food_orders' AND COLUMN_NAME='coupon_discount';
    IF v2=0 THEN ALTER TABLE fresh_food_orders ADD COLUMN coupon_discount DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER coupon_code; END IF;
    SELECT COUNT(*) INTO v3 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='fresh_food_order_items' AND COLUMN_NAME='original_unit_price';
    IF v3=0 THEN ALTER TABLE fresh_food_order_items ADD COLUMN original_unit_price DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER unit_price; END IF;
    SELECT COUNT(*) INTO v1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='fresh_food_order_items' AND COLUMN_NAME='discount_type';
    IF v1=0 THEN ALTER TABLE fresh_food_order_items ADD COLUMN discount_type ENUM('NONE','PERCENT','FLAT') NOT NULL DEFAULT 'NONE' AFTER original_unit_price; END IF;
    SELECT COUNT(*) INTO v2 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='fresh_food_order_items' AND COLUMN_NAME='discount_value';
    IF v2=0 THEN ALTER TABLE fresh_food_order_items ADD COLUMN discount_value DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER discount_type; END IF;
    SELECT COUNT(*) INTO v3 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='fresh_food_order_items' AND COLUMN_NAME='discount_amount';
    IF v3=0 THEN ALTER TABLE fresh_food_order_items ADD COLUMN discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER discount_value; END IF;
END$$
DELIMITER ;
CALL fresh_food_add_order_discount_columns();
DROP PROCEDURE IF EXISTS fresh_food_add_order_discount_columns;

CREATE TABLE IF NOT EXISTS fresh_food_coupons (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  uuid CHAR(36) NOT NULL,
  tenant_id BIGINT UNSIGNED NOT NULL,
  fresh_food_id BIGINT UNSIGNED NOT NULL,
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
  UNIQUE KEY fresh_food_coupons_uuid_unique (uuid),
  UNIQUE KEY fresh_food_coupons_code_unique (tenant_id,fresh_food_id,code),
  KEY fresh_food_coupons_business_index (fresh_food_id),
  KEY fresh_food_coupons_status_index (fresh_food_id,status),
  CONSTRAINT fk_fresh_food_coupons_profile FOREIGN KEY (fresh_food_id) REFERENCES fresh_food_profiles(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
