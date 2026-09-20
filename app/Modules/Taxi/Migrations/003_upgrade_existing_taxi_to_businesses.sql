START TRANSACTION;

CREATE TABLE IF NOT EXISTS businesses (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    tenant_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    slug VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    business_type VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    phone VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    email VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    address VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    city VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    district VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    state VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    postal_code VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    status ENUM('Active','Inactive','Suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
    created_by BIGINT UNSIGNED DEFAULT NULL,
    updated_by BIGINT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_business_uuid (uuid),
    UNIQUE KEY uq_business_tenant_slug (tenant_id, slug),
    KEY idx_business_tenant_type (tenant_id, business_type),
    KEY idx_business_deleted (tenant_id, deleted_at),
    CONSTRAINT fk_business_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    CONSTRAINT fk_business_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_business_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE taxi_vendors
    ADD COLUMN business_id BIGINT UNSIGNED NULL AFTER tenant_id;

INSERT INTO businesses
    (uuid, tenant_id, name, slug, business_type, phone, email, address, city, district, state, postal_code, status, created_by, updated_by)
SELECT UUID(), v.tenant_id, v.business_name,
       LOWER(TRIM(BOTH '-' FROM REGEXP_REPLACE(LOWER(v.business_name), '[^a-z0-9]+', '-'))),
       'taxi', v.phone, v.email, v.address, v.city, v.district, v.state, v.postal_code,
       v.status, v.created_by, v.updated_by
FROM taxi_vendors v
WHERE v.business_id IS NULL
  AND NOT EXISTS (
      SELECT 1 FROM businesses b
      WHERE b.tenant_id = v.tenant_id
        AND b.name = v.business_name
        AND b.business_type = 'taxi'
        AND b.deleted_at IS NULL
  );

UPDATE taxi_vendors v
INNER JOIN businesses b
    ON b.tenant_id = v.tenant_id
   AND b.name = v.business_name
   AND b.business_type = 'taxi'
   AND b.deleted_at IS NULL
SET v.business_id = b.id
WHERE v.business_id IS NULL;

ALTER TABLE taxi_vendors
    DROP INDEX uq_taxi_vendor_tenant,
    ADD UNIQUE KEY uq_taxi_vendor_business (business_id),
    ADD KEY idx_taxi_vendor_business (tenant_id, business_id),
    ADD CONSTRAINT fk_taxi_vendor_business FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE;

ALTER TABLE taxi_vendors
    DROP COLUMN business_name,
    MODIFY business_id BIGINT UNSIGNED NOT NULL;

COMMIT;
