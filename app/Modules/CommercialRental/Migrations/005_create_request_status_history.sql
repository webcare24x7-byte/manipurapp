CREATE TABLE IF NOT EXISTS commercial_rental_request_status_history (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    tenant_id BIGINT UNSIGNED NOT NULL,
    request_id BIGINT UNSIGNED NOT NULL,
    old_status VARCHAR(30) DEFAULT NULL,
    new_status VARCHAR(30) NOT NULL,
    changed_by BIGINT UNSIGNED DEFAULT NULL,
    actor_type VARCHAR(30) NOT NULL DEFAULT 'ADMIN',
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_cr_req_status_history_request (tenant_id, request_id, id),
    KEY idx_cr_req_status_history_member_lookup (tenant_id, request_id, new_status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
