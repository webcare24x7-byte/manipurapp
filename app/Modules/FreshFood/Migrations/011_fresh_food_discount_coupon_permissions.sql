SET NAMES utf8mb4;
INSERT INTO permissions (uuid,name,slug) SELECT UUID(),'View Fresh Food Coupons','fresh_food.coupons.view' WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug='fresh_food.coupons.view');
INSERT INTO permissions (uuid,name,slug) SELECT UUID(),'Manage Fresh Food Coupons','fresh_food.coupons.manage' WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug='fresh_food.coupons.manage');
INSERT INTO permissions (uuid,name,slug) SELECT UUID(),'Manage Fresh Food Product Discounts','fresh_food.products.discount.manage' WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug='fresh_food.products.discount.manage');
