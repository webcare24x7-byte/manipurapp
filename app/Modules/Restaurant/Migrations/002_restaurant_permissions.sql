START TRANSACTION;

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Restaurant Module', 'restaurant.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.view');

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Restaurant Businesses', 'restaurant.restaurants.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.restaurants.view');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Create Restaurant Businesses', 'restaurant.restaurants.create'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.restaurants.create');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Edit Restaurant Businesses', 'restaurant.restaurants.edit'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.restaurants.edit');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Delete Restaurant Businesses', 'restaurant.restaurants.delete'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.restaurants.delete');

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Restaurant Categories', 'restaurant.categories.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.categories.view');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Create Restaurant Categories', 'restaurant.categories.create'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.categories.create');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Edit Restaurant Categories', 'restaurant.categories.edit'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.categories.edit');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Delete Restaurant Categories', 'restaurant.categories.delete'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.categories.delete');

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Restaurant Items', 'restaurant.items.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.items.view');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Create Restaurant Items', 'restaurant.items.create'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.items.create');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Edit Restaurant Items', 'restaurant.items.edit'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.items.edit');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Delete Restaurant Items', 'restaurant.items.delete'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.items.delete');

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Restaurant Variants', 'restaurant.variants.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.variants.view');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Create Restaurant Variants', 'restaurant.variants.create'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.variants.create');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Edit Restaurant Variants', 'restaurant.variants.edit'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.variants.edit');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Delete Restaurant Variants', 'restaurant.variants.delete'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.variants.delete');

INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'View Restaurant Modifiers', 'restaurant.modifiers.view'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.modifiers.view');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Create Restaurant Modifiers', 'restaurant.modifiers.create'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.modifiers.create');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Edit Restaurant Modifiers', 'restaurant.modifiers.edit'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.modifiers.edit');
INSERT INTO permissions (uuid, name, slug)
SELECT UUID(), 'Delete Restaurant Modifiers', 'restaurant.modifiers.delete'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE slug = 'restaurant.modifiers.delete');

COMMIT;
