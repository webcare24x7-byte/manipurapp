-- Restaurant order status history
-- Immutable audit/timeline for every order status transition.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS restaurant_order_status_history (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  uuid CHAR(36) NOT NULL,
  tenant_id BIGINT UNSIGNED NOT NULL,
  restaurant_id BIGINT UNSIGNED NOT NULL,
  order_id BIGINT UNSIGNED NOT NULL,
  from_status VARCHAR(40) DEFAULT NULL,
  to_status VARCHAR(40) NOT NULL,
  note VARCHAR(500) DEFAULT NULL,
  changed_by BIGINT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY restaurant_order_status_history_uuid_unique (uuid),
  KEY restaurant_order_status_history_order_index (tenant_id, order_id, id),
  KEY restaurant_order_status_history_restaurant_index (tenant_id, restaurant_id, created_at),
  KEY restaurant_order_status_history_changed_by_index (changed_by),
  CONSTRAINT fk_restaurant_order_status_history_order
    FOREIGN KEY (order_id) REFERENCES restaurant_orders (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Existing orders predate the timeline feature. Seed one non-fabricated snapshot
-- so their current status is visible without pretending historical transitions exist.
INSERT INTO restaurant_order_status_history
    (uuid, tenant_id, restaurant_id, order_id, from_status, to_status, note, changed_by)
SELECT UUID(), o.tenant_id, o.restaurant_id, o.id, NULL, o.status,
       'Status history initialized after order creation.', o.updated_by
FROM restaurant_orders o
WHERE NOT EXISTS (
    SELECT 1 FROM restaurant_order_status_history h
    WHERE h.tenant_id = o.tenant_id AND h.order_id = o.id
);
