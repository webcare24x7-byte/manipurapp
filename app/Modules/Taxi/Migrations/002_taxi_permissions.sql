START TRANSACTION;

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Taxi Dashboard', 'taxi.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.view');

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Taxi Vendors', 'taxi.vendors.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.vendors.view');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Create Taxi Vendors', 'taxi.vendors.create'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.vendors.create');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Edit Taxi Vendors', 'taxi.vendors.edit'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.vendors.edit');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Delete Taxi Vendors', 'taxi.vendors.delete'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.vendors.delete');

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Taxi Vehicles', 'taxi.vehicles.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.vehicles.view');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Create Taxi Vehicles', 'taxi.vehicles.create'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.vehicles.create');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Edit Taxi Vehicles', 'taxi.vehicles.edit'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.vehicles.edit');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Delete Taxi Vehicles', 'taxi.vehicles.delete'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.vehicles.delete');

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Taxi Drivers', 'taxi.drivers.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.drivers.view');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Create Taxi Drivers', 'taxi.drivers.create'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.drivers.create');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Edit Taxi Drivers', 'taxi.drivers.edit'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.drivers.edit');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Delete Taxi Drivers', 'taxi.drivers.delete'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.drivers.delete');

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Taxi Services', 'taxi.services.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.services.view');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Create Taxi Services', 'taxi.services.create'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.services.create');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Edit Taxi Services', 'taxi.services.edit'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.services.edit');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Delete Taxi Services', 'taxi.services.delete'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.services.delete');

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Taxi Bookings', 'taxi.bookings.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.bookings.view');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Create Taxi Bookings', 'taxi.bookings.create'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.bookings.create');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Edit Taxi Bookings', 'taxi.bookings.edit'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.bookings.edit');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Delete Taxi Bookings', 'taxi.bookings.delete'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'taxi.bookings.delete');

COMMIT;
