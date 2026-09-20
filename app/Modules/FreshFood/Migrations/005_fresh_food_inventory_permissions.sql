SET NAMES utf8mb4;
START TRANSACTION;
INSERT INTO permissions (uuid,name,slug) SELECT UUID(),'View Fresh Food Inventory','fresh_food.inventory.view' WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug='fresh_food.inventory.view');
INSERT INTO permissions (uuid,name,slug) SELECT UUID(),'Adjust Fresh Food Inventory','fresh_food.inventory.adjust' WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug='fresh_food.inventory.adjust');
INSERT INTO permissions (uuid,name,slug) SELECT UUID(),'View Fresh Food Inventory Movements','fresh_food.inventory.movements.view' WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug='fresh_food.inventory.movements.view');
COMMIT;
