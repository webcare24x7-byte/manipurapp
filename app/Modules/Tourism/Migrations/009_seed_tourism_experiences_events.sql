SET NAMES utf8mb4; START TRANSACTION;
-- Development sample data: tenant 1 only. Safe to rerun.
INSERT INTO tourism_experiences(uuid,tenant_id,destination_id,title,slug,description,experience_type,duration_minutes,price,max_participants,meeting_point,included,requirements,latitude,longitude,status,featured)
SELECT UUID(),1,d.id,'Loktak Sunset Boat Experience','loktak-sunset-boat-experience','A sample local lake experience for development.','Nature & Culture',120,1200,6,'Loktak Lake jetty','Boat ride and local host','Weather permitting',d.latitude,d.longitude,'Active',1
FROM tourism_destinations d WHERE d.tenant_id=1 AND d.name='Loktak Lake' AND NOT EXISTS(SELECT 1 FROM tourism_experiences e WHERE e.tenant_id=1 AND e.slug='loktak-sunset-boat-experience');
INSERT INTO tourism_experiences(uuid,tenant_id,destination_id,title,slug,description,experience_type,duration_minutes,price,max_participants,meeting_point,included,requirements,latitude,longitude,status,featured)
SELECT UUID(),1,d.id,'Imphal Heritage Walk','imphal-heritage-walk','A sample guided heritage walk around historic Imphal.','Culture & Heritage',150,900,10,'Imphal city centre','Local guide and walking tour','Comfortable walking shoes',d.latitude,d.longitude,'Active',1
FROM tourism_destinations d WHERE d.tenant_id=1 AND d.name='Imphal' AND NOT EXISTS(SELECT 1 FROM tourism_experiences e WHERE e.tenant_id=1 AND e.slug='imphal-heritage-walk');
INSERT INTO tourism_events(uuid,tenant_id,title,slug,description,event_type,start_at,end_at,venue,city,district,state,organizer_name,ticket_info,status,featured)
SELECT UUID(),1,'Manipur Tourism Cultural Showcase','manipur-tourism-cultural-showcase','Development sample event for the Tourism module.','Cultural',DATE_ADD(NOW(),INTERVAL 30 DAY),DATE_ADD(NOW(),INTERVAL 30 DAY)+INTERVAL 4 HOUR,'Imphal City','Imphal','Imphal West','Manipur','Manipur Tourism Development Sample','Sample event — replace with real ticket information','Active',1
WHERE NOT EXISTS(SELECT 1 FROM tourism_events WHERE tenant_id=1 AND slug='manipur-tourism-cultural-showcase');
INSERT INTO tourism_events(uuid,tenant_id,title,slug,description,event_type,start_at,end_at,venue,city,district,state,organizer_name,ticket_info,status,featured)
SELECT UUID(),1,'Loktak Nature & Photography Day','loktak-nature-photography-day','Development sample event for destination promotion.','Nature & Photography',DATE_ADD(NOW(),INTERVAL 60 DAY),DATE_ADD(NOW(),INTERVAL 60 DAY)+INTERVAL 6 HOUR,'Loktak Lake','Moirang','Bishnupur','Manipur','Manipur Tourism Development Sample','Sample event — replace with real ticket information','Active',0
WHERE NOT EXISTS(SELECT 1 FROM tourism_events WHERE tenant_id=1 AND slug='loktak-nature-photography-day');
COMMIT;
