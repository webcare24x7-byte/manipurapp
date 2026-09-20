SET NAMES utf8mb4;
START TRANSACTION;

INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'View Tourism Module','tourism.view' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.view');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'View Tourism Providers','tourism.providers.view' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.providers.view');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Create Tourism Providers','tourism.providers.create' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.providers.create');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Edit Tourism Providers','tourism.providers.edit' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.providers.edit');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Delete Tourism Providers','tourism.providers.delete' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.providers.delete');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'View Tourism Destinations','tourism.destinations.view' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.destinations.view');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Create Tourism Destinations','tourism.destinations.create' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.destinations.create');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Edit Tourism Destinations','tourism.destinations.edit' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.destinations.edit');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Delete Tourism Destinations','tourism.destinations.delete' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.destinations.delete');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'View Tourism Stays','tourism.stays.view' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.stays.view');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Create Tourism Stays','tourism.stays.create' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.stays.create');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Edit Tourism Stays','tourism.stays.edit' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.stays.edit');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Delete Tourism Stays','tourism.stays.delete' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.stays.delete');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Manage Tourism Stay Rooms','tourism.rooms.manage' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='tourism.rooms.manage');

INSERT INTO role_permissions(role_id,permission_id)
SELECT r.id,p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug='church_admin' AND p.slug LIKE 'tourism.%'
AND NOT EXISTS(SELECT 1 FROM role_permissions rp WHERE rp.role_id=r.id AND rp.permission_id=p.id);

COMMIT;
