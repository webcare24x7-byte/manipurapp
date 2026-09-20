START TRANSACTION;
INSERT INTO commercial_rental_categories (uuid,tenant_id,name,slug,description,status,created_by,updated_by)
SELECT UUID(),t.id,'Pickup','pickup','Pickup and utility vehicles.','Active',NULL,NULL FROM tenants t
WHERE NOT EXISTS (SELECT 1 FROM commercial_rental_categories c WHERE c.tenant_id=t.id AND c.slug='pickup' AND c.deleted_at IS NULL);
INSERT INTO commercial_rental_categories (uuid,tenant_id,name,slug,description,status,created_by,updated_by)
SELECT UUID(),t.id,'Mini Truck','mini-truck','Mini trucks for local goods transport.','Active',NULL,NULL FROM tenants t
WHERE NOT EXISTS (SELECT 1 FROM commercial_rental_categories c WHERE c.tenant_id=t.id AND c.slug='mini-truck' AND c.deleted_at IS NULL);
INSERT INTO commercial_rental_categories (uuid,tenant_id,name,slug,description,status,created_by,updated_by)
SELECT UUID(),t.id,'Truck','truck','Commercial trucks and heavy goods vehicles.','Active',NULL,NULL FROM tenants t
WHERE NOT EXISTS (SELECT 1 FROM commercial_rental_categories c WHERE c.tenant_id=t.id AND c.slug='truck' AND c.deleted_at IS NULL);
INSERT INTO commercial_rental_categories (uuid,tenant_id,name,slug,description,status,created_by,updated_by)
SELECT UUID(),t.id,'Tractor','tractor','Agricultural and utility tractors.','Active',NULL,NULL FROM tenants t
WHERE NOT EXISTS (SELECT 1 FROM commercial_rental_categories c WHERE c.tenant_id=t.id AND c.slug='tractor' AND c.deleted_at IS NULL);
INSERT INTO commercial_rental_categories (uuid,tenant_id,name,slug,description,status,created_by,updated_by)
SELECT UUID(),t.id,'JCB / Excavator','jcb-excavator','JCBs, excavators and earthmoving equipment.','Active',NULL,NULL FROM tenants t
WHERE NOT EXISTS (SELECT 1 FROM commercial_rental_categories c WHERE c.tenant_id=t.id AND c.slug='jcb-excavator' AND c.deleted_at IS NULL);
INSERT INTO commercial_rental_categories (uuid,tenant_id,name,slug,description,status,created_by,updated_by)
SELECT UUID(),t.id,'Water Tanker','water-tanker','Water tankers and water delivery vehicles.','Active',NULL,NULL FROM tenants t
WHERE NOT EXISTS (SELECT 1 FROM commercial_rental_categories c WHERE c.tenant_id=t.id AND c.slug='water-tanker' AND c.deleted_at IS NULL);
INSERT INTO commercial_rental_categories (uuid,tenant_id,name,slug,description,status,created_by,updated_by)
SELECT UUID(),t.id,'Other','other','Other commercial rental vehicles or equipment.','Active',NULL,NULL FROM tenants t
WHERE NOT EXISTS (SELECT 1 FROM commercial_rental_categories c WHERE c.tenant_id=t.id AND c.slug='other' AND c.deleted_at IS NULL);
COMMIT;
