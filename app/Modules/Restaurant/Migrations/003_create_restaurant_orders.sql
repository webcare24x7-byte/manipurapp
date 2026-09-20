START TRANSACTION;

CREATE TABLE IF NOT EXISTS restaurant_orders (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  uuid CHAR(36) NOT NULL,
  tenant_id BIGINT UNSIGNED NOT NULL,
  restaurant_id BIGINT UNSIGNED NOT NULL,
  member_id BIGINT UNSIGNED DEFAULT NULL,
  order_no VARCHAR(40) NOT NULL,
  order_type ENUM('DELIVERY','PICKUP') NOT NULL DEFAULT 'DELIVERY',
  status ENUM('PENDING','ACCEPTED','PREPARING','READY','ASSIGNED','OUT_FOR_DELIVERY','DELIVERED','COMPLETED','CANCELLED','REJECTED') NOT NULL DEFAULT 'PENDING',
  payment_status ENUM('PENDING','PAID','FAILED','REFUNDED') NOT NULL DEFAULT 'PENDING',
  payment_method VARCHAR(50) DEFAULT NULL,
  customer_name VARCHAR(200) NOT NULL,
  customer_phone VARCHAR(40) DEFAULT NULL,
  customer_email VARCHAR(200) DEFAULT NULL,
  delivery_address VARCHAR(500) DEFAULT NULL,
  delivery_city VARCHAR(150) DEFAULT NULL,
  delivery_district VARCHAR(150) DEFAULT NULL,
  delivery_state VARCHAR(150) DEFAULT NULL,
  delivery_postal_code VARCHAR(20) DEFAULT NULL,
  customer_note TEXT DEFAULT NULL,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  delivery_fee DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  discount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  rejection_reason VARCHAR(500) DEFAULT NULL,
  cancellation_reason VARCHAR(500) DEFAULT NULL,
  accepted_at DATETIME DEFAULT NULL,
  preparing_at DATETIME DEFAULT NULL,
  ready_at DATETIME DEFAULT NULL,
  assigned_at DATETIME DEFAULT NULL,
  out_for_delivery_at DATETIME DEFAULT NULL,
  delivered_at DATETIME DEFAULT NULL,
  completed_at DATETIME DEFAULT NULL,
  cancelled_at DATETIME DEFAULT NULL,
  rejected_at DATETIME DEFAULT NULL,
  created_by BIGINT UNSIGNED DEFAULT NULL,
  updated_by BIGINT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY restaurant_orders_uuid_unique (uuid),
  UNIQUE KEY restaurant_orders_order_no_unique (tenant_id, order_no),
  KEY restaurant_orders_tenant_index (tenant_id),
  KEY restaurant_orders_restaurant_index (restaurant_id),
  KEY restaurant_orders_member_index (member_id),
  KEY restaurant_orders_status_index (restaurant_id, status),
  KEY restaurant_orders_created_index (restaurant_id, created_at),
  CONSTRAINT fk_restaurant_orders_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurant_profiles (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_restaurant_orders_member FOREIGN KEY (member_id) REFERENCES members (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS restaurant_order_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  uuid CHAR(36) NOT NULL,
  tenant_id BIGINT UNSIGNED NOT NULL,
  order_id BIGINT UNSIGNED NOT NULL,
  item_id BIGINT UNSIGNED DEFAULT NULL,
  variant_id BIGINT UNSIGNED DEFAULT NULL,
  item_name VARCHAR(200) NOT NULL,
  variant_name VARCHAR(150) DEFAULT NULL,
  unit_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  line_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY restaurant_order_items_uuid_unique (uuid),
  KEY restaurant_order_items_order_index (order_id),
  KEY restaurant_order_items_item_index (item_id),
  CONSTRAINT fk_restaurant_order_items_order FOREIGN KEY (order_id) REFERENCES restaurant_orders (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS restaurant_order_item_modifiers (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  uuid CHAR(36) NOT NULL,
  tenant_id BIGINT UNSIGNED NOT NULL,
  order_item_id BIGINT UNSIGNED NOT NULL,
  modifier_group_id BIGINT UNSIGNED DEFAULT NULL,
  modifier_option_id BIGINT UNSIGNED DEFAULT NULL,
  group_name VARCHAR(150) NOT NULL,
  option_name VARCHAR(150) NOT NULL,
  price_adjustment DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY restaurant_order_item_modifiers_uuid_unique (uuid),
  KEY restaurant_order_item_modifiers_item_index (order_item_id),
  CONSTRAINT fk_restaurant_order_item_modifiers_item FOREIGN KEY (order_item_id) REFERENCES restaurant_order_items (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Restaurant Orders', 'restaurant.orders.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.orders.view');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Manage Restaurant Orders', 'restaurant.orders.manage'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.orders.manage');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Create Restaurant Test Orders', 'restaurant.orders.test_create'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.orders.test_create');

COMMIT;
