SET NAMES utf8mb4;
START TRANSACTION;
CREATE TABLE IF NOT EXISTS tourism_experiences (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, uuid CHAR(36) NOT NULL, tenant_id BIGINT UNSIGNED NOT NULL,
 provider_id BIGINT UNSIGNED NULL, destination_id BIGINT UNSIGNED NULL, title VARCHAR(220) NOT NULL, slug VARCHAR(240) NOT NULL,
 description TEXT NULL, experience_type VARCHAR(100) NULL, duration_minutes INT UNSIGNED NULL, price DECIMAL(12,2) NULL,
 max_participants SMALLINT UNSIGNED NULL, meeting_point VARCHAR(255) NULL, included TEXT NULL, requirements TEXT NULL,
 latitude DECIMAL(10,7) NULL, longitude DECIMAL(10,7) NULL, cover_image_path VARCHAR(500) NULL,
 status ENUM('Draft','Pending','Active','Inactive','Suspended') NOT NULL DEFAULT 'Draft', featured TINYINT(1) NOT NULL DEFAULT 0,
 created_by BIGINT UNSIGNED NULL, updated_by BIGINT UNSIGNED NULL, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, deleted_at TIMESTAMP NULL DEFAULT NULL,
 UNIQUE KEY uq_tourism_experience_uuid(uuid), UNIQUE KEY uq_tourism_experience_slug(tenant_id,slug),
 KEY idx_tourism_experience_location(tenant_id,latitude,longitude), KEY idx_tourism_experience_destination(tenant_id,destination_id), KEY idx_tourism_experience_status(tenant_id,status,deleted_at),
 CONSTRAINT fk_tourism_experience_tenant FOREIGN KEY(tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
 CONSTRAINT fk_tourism_experience_provider FOREIGN KEY(provider_id) REFERENCES tourism_providers(id) ON DELETE SET NULL,
 CONSTRAINT fk_tourism_experience_destination FOREIGN KEY(destination_id) REFERENCES tourism_destinations(id) ON DELETE SET NULL,
 CONSTRAINT fk_tourism_experience_created FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL,
 CONSTRAINT fk_tourism_experience_updated FOREIGN KEY(updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS tourism_events (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, uuid CHAR(36) NOT NULL, tenant_id BIGINT UNSIGNED NOT NULL,
 provider_id BIGINT UNSIGNED NULL, title VARCHAR(220) NOT NULL, slug VARCHAR(240) NOT NULL, description TEXT NULL,
 event_type VARCHAR(100) NULL, start_at DATETIME NOT NULL, end_at DATETIME NULL, venue VARCHAR(255) NULL, city VARCHAR(150) NULL,
 district VARCHAR(150) NULL, state VARCHAR(150) NOT NULL DEFAULT 'Manipur', latitude DECIMAL(10,7) NULL, longitude DECIMAL(10,7) NULL,
 organizer_name VARCHAR(180) NULL, contact_phone VARCHAR(30) NULL, ticket_info VARCHAR(500) NULL, cover_image_path VARCHAR(500) NULL,
 status ENUM('Draft','Pending','Active','Inactive','Cancelled') NOT NULL DEFAULT 'Draft', featured TINYINT(1) NOT NULL DEFAULT 0,
 created_by BIGINT UNSIGNED NULL, updated_by BIGINT UNSIGNED NULL, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, deleted_at TIMESTAMP NULL DEFAULT NULL,
 UNIQUE KEY uq_tourism_event_uuid(uuid), UNIQUE KEY uq_tourism_event_slug(tenant_id,slug), KEY idx_tourism_event_date(tenant_id,start_at), KEY idx_tourism_event_location(tenant_id,latitude,longitude), KEY idx_tourism_event_status(tenant_id,status,deleted_at),
 CONSTRAINT fk_tourism_event_tenant FOREIGN KEY(tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
 CONSTRAINT fk_tourism_event_provider FOREIGN KEY(provider_id) REFERENCES tourism_providers(id) ON DELETE SET NULL,
 CONSTRAINT fk_tourism_event_created FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL,
 CONSTRAINT fk_tourism_event_updated FOREIGN KEY(updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS tourism_trip_plans (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, uuid CHAR(36) NOT NULL, tenant_id BIGINT UNSIGNED NOT NULL, user_id BIGINT UNSIGNED NULL,
 title VARCHAR(220) NOT NULL, description TEXT NULL, start_date DATE NULL, end_date DATE NULL,
 status ENUM('Draft','Planned','Completed','Archived') NOT NULL DEFAULT 'Draft', created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_tourism_trip_uuid(uuid), KEY idx_tourism_trip_user(tenant_id,user_id), KEY idx_tourism_trip_status(tenant_id,status),
 CONSTRAINT fk_tourism_trip_tenant FOREIGN KEY(tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
 CONSTRAINT fk_tourism_trip_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS tourism_trip_plan_items (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, uuid CHAR(36) NOT NULL, tenant_id BIGINT UNSIGNED NOT NULL, trip_plan_id BIGINT UNSIGNED NOT NULL,
 day_number SMALLINT UNSIGNED NOT NULL DEFAULT 1, sequence_no SMALLINT UNSIGNED NOT NULL DEFAULT 1, item_type ENUM('DESTINATION','STAY','EXPERIENCE','EVENT','GUIDE','RESTAURANT','TAXI','RENTAL') NOT NULL,
 item_id BIGINT UNSIGNED NOT NULL, title VARCHAR(220) NULL, notes VARCHAR(500) NULL, start_time TIME NULL, end_time TIME NULL,
 UNIQUE KEY uq_tourism_trip_item_uuid(uuid), KEY idx_tourism_trip_item(tenant_id,trip_plan_id,day_number,sequence_no),
 CONSTRAINT fk_tourism_trip_item_tenant FOREIGN KEY(tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
 CONSTRAINT fk_tourism_trip_item_plan FOREIGN KEY(trip_plan_id) REFERENCES tourism_trip_plans(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS tourism_reviews (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, uuid CHAR(36) NOT NULL, tenant_id BIGINT UNSIGNED NOT NULL, user_id BIGINT UNSIGNED NULL,
 reviewable_type ENUM('DESTINATION','STAY','PACKAGE','GUIDE','EXPERIENCE','EVENT') NOT NULL, reviewable_id BIGINT UNSIGNED NOT NULL,
 rating TINYINT UNSIGNED NOT NULL, title VARCHAR(220) NULL, review TEXT NULL,
 status ENUM('Pending','Approved','Rejected','Hidden') NOT NULL DEFAULT 'Pending', created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, UNIQUE KEY uq_tourism_review_uuid(uuid),
 KEY idx_tourism_review_target(tenant_id,reviewable_type,reviewable_id,status), KEY idx_tourism_review_user(tenant_id,user_id),
 CONSTRAINT fk_tourism_review_tenant FOREIGN KEY(tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
 CONSTRAINT fk_tourism_review_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;
