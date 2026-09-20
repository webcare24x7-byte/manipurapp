SET NAMES utf8mb4;
START TRANSACTION;

ALTER TABLE commercial_rental_vehicles
    ADD COLUMN fuel_type VARCHAR(50) DEFAULT NULL AFTER model_year,
    ADD COLUMN ownership_type ENUM('OWNER','LEASED','PARTNER','OTHER') NOT NULL DEFAULT 'OWNER' AFTER fuel_type,
    ADD COLUMN engine_power VARCHAR(100) DEFAULT NULL AFTER ownership_type,
    ADD COLUMN body_type VARCHAR(100) DEFAULT NULL AFTER engine_power,
    ADD COLUMN seating_capacity SMALLINT UNSIGNED DEFAULT NULL AFTER body_type,
    ADD COLUMN condition_status ENUM('NEW','GOOD','FAIR','NEEDS_SERVICE') NOT NULL DEFAULT 'GOOD' AFTER seating_capacity,
    ADD COLUMN operator_name VARCHAR(150) DEFAULT NULL AFTER operator_included,
    ADD COLUMN operator_phone VARCHAR(30) DEFAULT NULL AFTER operator_name,
    ADD COLUMN operator_experience VARCHAR(100) DEFAULT NULL AFTER operator_phone,
    ADD COLUMN service_notes TEXT DEFAULT NULL AFTER description;

COMMIT;
