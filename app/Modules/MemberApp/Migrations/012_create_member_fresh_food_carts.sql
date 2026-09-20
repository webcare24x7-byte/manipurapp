CREATE TABLE IF NOT EXISTS member_fresh_food_carts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    tenant_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    fresh_food_id BIGINT UNSIGNED NOT NULL,
    items_json LONGTEXT NOT NULL,
    coupon_code VARCHAR(80) DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY member_fresh_food_carts_uuid_unique (uuid),
    UNIQUE KEY member_fresh_food_carts_member_business_unique (tenant_id, member_id, fresh_food_id),
    KEY member_fresh_food_carts_member_index (tenant_id, member_id),
    KEY member_fresh_food_carts_business_index (tenant_id, fresh_food_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
