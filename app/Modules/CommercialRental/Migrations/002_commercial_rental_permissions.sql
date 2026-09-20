START TRANSACTION;
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'View Commercial Rental Dashboard','commercial_rental.view' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.view');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'View Commercial Rental Providers','commercial_rental.providers.view' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.providers.view');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Create Commercial Rental Providers','commercial_rental.providers.create' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.providers.create');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Edit Commercial Rental Providers','commercial_rental.providers.edit' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.providers.edit');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Delete Commercial Rental Providers','commercial_rental.providers.delete' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.providers.delete');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'View Commercial Rental Categories','commercial_rental.categories.view' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.categories.view');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Create Commercial Rental Categories','commercial_rental.categories.create' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.categories.create');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Edit Commercial Rental Categories','commercial_rental.categories.edit' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.categories.edit');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Delete Commercial Rental Categories','commercial_rental.categories.delete' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.categories.delete');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'View Commercial Rental Vehicles','commercial_rental.vehicles.view' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.vehicles.view');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Create Commercial Rental Vehicles','commercial_rental.vehicles.create' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.vehicles.create');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Edit Commercial Rental Vehicles','commercial_rental.vehicles.edit' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.vehicles.edit');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Delete Commercial Rental Vehicles','commercial_rental.vehicles.delete' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.vehicles.delete');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'View Commercial Rental Requests','commercial_rental.requests.view' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.requests.view');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Create Commercial Rental Requests','commercial_rental.requests.create' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.requests.create');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Edit Commercial Rental Requests','commercial_rental.requests.edit' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.requests.edit');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Delete Commercial Rental Requests','commercial_rental.requests.delete' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.requests.delete');
INSERT INTO permissions(uuid,name,slug) SELECT UUID(),'Manage Commercial Rental Requests','commercial_rental.requests.manage' WHERE NOT EXISTS(SELECT 1 FROM permissions WHERE slug='commercial_rental.requests.manage');

INSERT INTO role_permissions(role_id,permission_id)
SELECT r.id,p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug='church_admin' AND p.slug LIKE 'commercial_rental.%'
AND NOT EXISTS(SELECT 1 FROM role_permissions rp WHERE rp.role_id=r.id AND rp.permission_id=p.id);
COMMIT;
