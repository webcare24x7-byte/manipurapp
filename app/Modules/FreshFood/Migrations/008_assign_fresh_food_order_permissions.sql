SET NAMES utf8mb4;
START TRANSACTION;

/*
 * Give Fresh Food order permissions to tenant administrator roles.
 * The permission rows are created by migration 007. This migration only
 * creates the role-permission mappings and is safe to run more than once.
 */
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'church_admin'
  AND p.slug IN (
      'fresh_food.orders.view',
      'fresh_food.orders.create',
      'fresh_food.orders.manage',
      'fresh_food.orders.cancel'
  )
  AND NOT EXISTS (
      SELECT 1
      FROM role_permissions rp
      WHERE rp.role_id = r.id
        AND rp.permission_id = p.id
  );

COMMIT;
