SET NAMES utf8mb4;
START TRANSACTION;

INSERT INTO permissions(uuid,name,slug)
SELECT UUID(),'View Digital ILP Helper','ilp.helper.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug='ilp.helper.view');

INSERT INTO role_permissions(role_id,permission_id)
SELECT r.id,p.id
FROM roles r CROSS JOIN permissions p
WHERE r.slug='church_admin' AND p.slug='ilp.helper.view'
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp
    WHERE rp.role_id=r.id AND rp.permission_id=p.id
);

COMMIT;
