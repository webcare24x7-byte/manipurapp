SET NAMES utf8mb4;
INSERT INTO role_permissions (role_id,permission_id)
SELECT r.id,p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug='church_admin' AND p.slug IN ('fresh_food.coupons.view','fresh_food.coupons.manage','fresh_food.products.discount.manage')
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id=r.id AND rp.permission_id=p.id);
