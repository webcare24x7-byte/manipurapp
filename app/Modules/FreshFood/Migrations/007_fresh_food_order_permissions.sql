SET NAMES utf8mb4;
START TRANSACTION;
INSERT INTO permissions (uuid,name,slug) SELECT UUID(),'View Fresh Food Orders','fresh_food.orders.view' WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug='fresh_food.orders.view');
INSERT INTO permissions (uuid,name,slug) SELECT UUID(),'Create Fresh Food Orders','fresh_food.orders.create' WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug='fresh_food.orders.create');
INSERT INTO permissions (uuid,name,slug) SELECT UUID(),'Manage Fresh Food Orders','fresh_food.orders.manage' WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug='fresh_food.orders.manage');
INSERT INTO permissions (uuid,name,slug) SELECT UUID(),'Cancel Fresh Food Orders','fresh_food.orders.cancel' WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug='fresh_food.orders.cancel');
COMMIT;
