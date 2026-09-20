-- Taxi booking status history
-- Migration: 010_taxi_booking_status_history.sql

CREATE TABLE IF NOT EXISTS taxi_booking_status_history (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    tenant_id BIGINT UNSIGNED NOT NULL,
    booking_id BIGINT UNSIGNED NOT NULL,
    old_status VARCHAR(30) DEFAULT NULL,
    new_status VARCHAR(30) NOT NULL,
    changed_by BIGINT UNSIGNED DEFAULT NULL,
    actor_type ENUM('ADMIN','DRIVER','MEMBER','SYSTEM') NOT NULL DEFAULT 'SYSTEM',
    notes VARCHAR(500) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_taxi_status_history_booking (tenant_id, booking_id, created_at),
    KEY idx_taxi_status_history_actor (tenant_id, changed_by),
    CONSTRAINT fk_taxi_status_history_booking
        FOREIGN KEY (booking_id) REFERENCES taxi_bookings(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_taxi_status_history_user
        FOREIGN KEY (changed_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
