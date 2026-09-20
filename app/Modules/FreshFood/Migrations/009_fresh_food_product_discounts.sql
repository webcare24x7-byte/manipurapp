SET NAMES utf8mb4;

DELIMITER $$
DROP PROCEDURE IF EXISTS fresh_food_add_product_discount_columns$$
CREATE PROCEDURE fresh_food_add_product_discount_columns()
BEGIN
    DECLARE v_type INT DEFAULT 0;
    DECLARE v_value INT DEFAULT 0;
    SELECT COUNT(*) INTO v_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='fresh_food_products' AND COLUMN_NAME='discount_type';
    IF v_type=0 THEN ALTER TABLE fresh_food_products ADD COLUMN discount_type ENUM('NONE','PERCENT','FLAT') NOT NULL DEFAULT 'NONE' AFTER price; END IF;
    SELECT COUNT(*) INTO v_value FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='fresh_food_products' AND COLUMN_NAME='discount_value';
    IF v_value=0 THEN ALTER TABLE fresh_food_products ADD COLUMN discount_value DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER discount_type; END IF;
END$$
DELIMITER ;
CALL fresh_food_add_product_discount_columns();
DROP PROCEDURE IF EXISTS fresh_food_add_product_discount_columns;
