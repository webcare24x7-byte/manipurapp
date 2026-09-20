-- DEVELOPMENT TEST DATA ONLY. Targets tenant 1 only.
-- Requires Tourism foundation + package/guide migrations and the existing tourism provider table.
SET NAMES utf8mb4;
START TRANSACTION;

-- Sample destinations (idempotent)
INSERT INTO tourism_destinations (uuid,tenant_id,name,slug,description,district,city,state,best_time_to_visit,suggested_duration,latitude,longitude,status)
SELECT UUID(),1,'Loktak Lake','loktak-lake','Manipur''s iconic freshwater lake and a major nature destination.','Bishnupur','Moirang','Manipur','October to April','Half day',24.5525000,93.7747000,'Active'
WHERE EXISTS (SELECT 1 FROM tenants WHERE id=1) AND NOT EXISTS (SELECT 1 FROM tourism_destinations WHERE tenant_id=1 AND slug='loktak-lake');
INSERT INTO tourism_destinations (uuid,tenant_id,name,slug,description,district,city,state,best_time_to_visit,suggested_duration,latitude,longitude,status)
SELECT UUID(),1,'Kangla Fort','kangla-fort','Historic heart of Imphal and an important cultural heritage site.','Imphal West','Imphal','Manipur','October to March','2-3 hours',24.8074000,93.9368000,'Active'
WHERE EXISTS (SELECT 1 FROM tenants WHERE id=1) AND NOT EXISTS (SELECT 1 FROM tourism_destinations WHERE tenant_id=1 AND slug='kangla-fort');
INSERT INTO tourism_destinations (uuid,tenant_id,name,slug,description,district,city,state,best_time_to_visit,suggested_duration,latitude,longitude,status)
SELECT UUID(),1,'Keibul Lamjao National Park','keibul-lamjao-national-park','A unique floating national park and habitat of the Sangai deer.','Bishnupur','Moirang','Manipur','November to March','Half day',24.5197000,93.8283000,'Active'
WHERE EXISTS (SELECT 1 FROM tenants WHERE id=1) AND NOT EXISTS (SELECT 1 FROM tourism_destinations WHERE tenant_id=1 AND slug='keibul-lamjao-national-park');
INSERT INTO tourism_destinations (uuid,tenant_id,name,slug,description,district,city,state,best_time_to_visit,suggested_duration,latitude,longitude,status)
SELECT UUID(),1,'Andro','andro','A cultural village destination known for traditional heritage, crafts and local experiences.','Imphal East','Andro','Manipur','October to March','Half day',24.7580000,94.0265000,'Active'
WHERE EXISTS (SELECT 1 FROM tenants WHERE id=1) AND NOT EXISTS (SELECT 1 FROM tourism_destinations WHERE tenant_id=1 AND slug='andro');
INSERT INTO tourism_destinations (uuid,tenant_id,name,slug,description,district,city,state,best_time_to_visit,suggested_duration,latitude,longitude,status)
SELECT UUID(),1,'Shree Govindajee Temple','shree-govindajee-temple','Historic Vaishnavite temple complex in the heart of Imphal.','Imphal West','Imphal','Manipur','October to March','1-2 hours',24.8121000,93.9477000,'Active'
WHERE EXISTS (SELECT 1 FROM tenants WHERE id=1) AND NOT EXISTS (SELECT 1 FROM tourism_destinations WHERE tenant_id=1 AND slug='shree-govindajee-temple');

-- Sample packages. Provider is the first active tourism provider in tenant 1.
INSERT INTO tourism_packages (uuid,tenant_id,provider_id,title,slug,description,duration_days,duration_nights,pricing_type,base_price,discount_price,max_travellers,cover_image_path,status)
SELECT UUID(),1,(SELECT id FROM tourism_providers WHERE tenant_id=1 AND deleted_at IS NULL ORDER BY id LIMIT 1),'Manipur Highlights - 3 Days','manipur-highlights-3-days','A sample development package connecting Imphal, Kangla Fort, Loktak Lake, Moirang and Keibul Lamjao.','3','2','PER_PERSON',12000.00,9999.00,6,NULL,'Active'
WHERE EXISTS (SELECT 1 FROM tenants WHERE id=1) AND EXISTS (SELECT 1 FROM tourism_providers WHERE tenant_id=1 AND deleted_at IS NULL) AND NOT EXISTS (SELECT 1 FROM tourism_packages WHERE tenant_id=1 AND slug='manipur-highlights-3-days');
INSERT INTO tourism_packages (uuid,tenant_id,provider_id,title,slug,description,duration_days,duration_nights,pricing_type,base_price,discount_price,max_travellers,cover_image_path,status)
SELECT UUID(),1,(SELECT id FROM tourism_providers WHERE tenant_id=1 AND deleted_at IS NULL ORDER BY id LIMIT 1),'Loktak & Sangai Escape - 2 Days','loktak-sangai-escape-2-days','A sample nature-focused itinerary around Loktak Lake and Keibul Lamjao National Park.','2','1','PER_PERSON',7500.00,6499.00,6,NULL,'Active'
WHERE EXISTS (SELECT 1 FROM tenants WHERE id=1) AND EXISTS (SELECT 1 FROM tourism_providers WHERE tenant_id=1 AND deleted_at IS NULL) AND NOT EXISTS (SELECT 1 FROM tourism_packages WHERE tenant_id=1 AND slug='loktak-sangai-escape-2-days');

-- Link package destinations (idempotent)
INSERT INTO tourism_package_destinations (uuid,tenant_id,package_id,destination_id,day_number,sequence_no,notes)
SELECT UUID(),1,p.id,d.id,1,1,'Arrival and Imphal heritage visit.' FROM tourism_packages p JOIN tourism_destinations d ON d.tenant_id=1 AND d.slug='kangla-fort' WHERE p.tenant_id=1 AND p.slug='manipur-highlights-3-days' AND NOT EXISTS (SELECT 1 FROM tourism_package_destinations x WHERE x.package_id=p.id AND x.destination_id=d.id);
INSERT INTO tourism_package_destinations (uuid,tenant_id,package_id,destination_id,day_number,sequence_no,notes)
SELECT UUID(),1,p.id,d.id,2,1,'Lake and Moirang sightseeing.' FROM tourism_packages p JOIN tourism_destinations d ON d.tenant_id=1 AND d.slug='loktak-lake' WHERE p.tenant_id=1 AND p.slug='manipur-highlights-3-days' AND NOT EXISTS (SELECT 1 FROM tourism_package_destinations x WHERE x.package_id=p.id AND x.destination_id=d.id);
INSERT INTO tourism_package_destinations (uuid,tenant_id,package_id,destination_id,day_number,sequence_no,notes)
SELECT UUID(),1,p.id,d.id,3,1,'Floating national park visit.' FROM tourism_packages p JOIN tourism_destinations d ON d.tenant_id=1 AND d.slug='keibul-lamjao-national-park' WHERE p.tenant_id=1 AND p.slug='manipur-highlights-3-days' AND NOT EXISTS (SELECT 1 FROM tourism_package_destinations x WHERE x.package_id=p.id AND x.destination_id=d.id);
INSERT INTO tourism_package_destinations (uuid,tenant_id,package_id,destination_id,day_number,sequence_no,notes)
SELECT UUID(),1,p.id,d.id,1,1,'Nature and lake experience.' FROM tourism_packages p JOIN tourism_destinations d ON d.tenant_id=1 AND d.slug='loktak-lake' WHERE p.tenant_id=1 AND p.slug='loktak-sangai-escape-2-days' AND NOT EXISTS (SELECT 1 FROM tourism_package_destinations x WHERE x.package_id=p.id AND x.destination_id=d.id);
INSERT INTO tourism_package_destinations (uuid,tenant_id,package_id,destination_id,day_number,sequence_no,notes)
SELECT UUID(),1,p.id,d.id,2,1,'Sangai habitat visit.' FROM tourism_packages p JOIN tourism_destinations d ON d.tenant_id=1 AND d.slug='keibul-lamjao-national-park' WHERE p.tenant_id=1 AND p.slug='loktak-sangai-escape-2-days' AND NOT EXISTS (SELECT 1 FROM tourism_package_destinations x WHERE x.package_id=p.id AND x.destination_id=d.id);

-- Sample guides; independent guides use business_id NULL.
INSERT INTO tourism_guides (uuid,tenant_id,business_id,name,slug,bio,phone,email,city,district,state,latitude,longitude,languages,specializations,experience_years,price_per_day,verification_status,status)
SELECT UUID(),1,NULL,'Ningthoujam John','ningthoujam-john','Sample local guide profile for Tourism development testing.','7000000101','guide1@manipurapp.local','Imphal','Imphal West','Manipur',24.8074000,93.9368000,'English, Meiteilon, Hindi','Heritage, Culture, Photography',8,1800.00,'Verified','Active'
WHERE EXISTS (SELECT 1 FROM tenants WHERE id=1) AND NOT EXISTS (SELECT 1 FROM tourism_guides WHERE tenant_id=1 AND slug='ningthoujam-john');
INSERT INTO tourism_guides (uuid,tenant_id,business_id,name,slug,bio,phone,email,city,district,state,latitude,longitude,languages,specializations,experience_years,price_per_day,verification_status,status)
SELECT UUID(),1,NULL,'Chanu Priya','chanu-priya','Sample nature and community guide profile for Tourism development testing.','7000000102','guide2@manipurapp.local','Moirang','Bishnupur','Manipur',24.5525000,93.7747000,'English, Meiteilon','Nature, Wildlife, Community Tourism',5,2200.00,'Verified','Active'
WHERE EXISTS (SELECT 1 FROM tenants WHERE id=1) AND NOT EXISTS (SELECT 1 FROM tourism_guides WHERE tenant_id=1 AND slug='chanu-priya');

COMMIT;
