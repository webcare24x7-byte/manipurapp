SET NAMES utf8mb4;
START TRANSACTION;
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'View Tour Packages','tourism.packages.view' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.packages.view');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Create Tour Packages','tourism.packages.create' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.packages.create');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Edit Tour Packages','tourism.packages.edit' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.packages.edit');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Delete Tour Packages','tourism.packages.delete' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.packages.delete');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'View Tourism Guides','tourism.guides.view' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.guides.view');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Create Tourism Guides','tourism.guides.create' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.guides.create');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Edit Tourism Guides','tourism.guides.edit' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.guides.edit');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Delete Tourism Guides','tourism.guides.delete' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.guides.delete');
INSERT INTO role_permissions(role_id,permission_id)
SELECT r.id,p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug='church_admin' AND p.slug IN ('tourism.packages.view','tourism.packages.create','tourism.packages.edit','tourism.packages.delete','tourism.guides.view','tourism.guides.create','tourism.guides.edit','tourism.guides.delete')
AND NOT EXISTS(SELECT 1 FROM role_permissions rp WHERE rp.role_id=r.id AND rp.permission_id=p.id);
COMMIT;
