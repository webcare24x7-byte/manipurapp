<?php
declare(strict_types=1);

namespace App\Modules\MemberApp\Models;

use App\Core\Database;

final class MemberTourism
{
    private Database $db;
    public function __construct(){ $this->db = app()->get('db'); }

    public function districts(int $tenantId): array
    {
        return $this->db->fetchAll("SELECT DISTINCT district FROM tourism_destinations WHERE tenant_id=? AND deleted_at IS NULL AND district IS NOT NULL AND district<>'' ORDER BY district ASC",[$tenantId]);
    }

    public function destinations(int $tenantId, ?string $search=null, ?string $district=null): array
    {
        $sql="SELECT d.*, 
                    (SELECT COUNT(*) FROM tourism_reviews r WHERE r.tenant_id=d.tenant_id AND r.reviewable_type='DESTINATION' AND r.reviewable_id=d.id AND r.status='Approved') review_count,
                    (SELECT ROUND(AVG(r.rating),1) FROM tourism_reviews r WHERE r.tenant_id=d.tenant_id AND r.reviewable_type='DESTINATION' AND r.reviewable_id=d.id AND r.status='Approved') review_rating
              FROM tourism_destinations d
              WHERE d.tenant_id=? AND d.deleted_at IS NULL";
        $p=[$tenantId];
        if(($search=trim((string)$search))!==''){ $like="%{$search}%"; $sql.=" AND (d.name LIKE ? OR d.description LIKE ? OR d.district LIKE ?)"; array_push($p,$like,$like,$like); }
        if(($district=trim((string)$district))!=='' && strcasecmp($district,'All')!==0){ $sql.=" AND d.district=?"; $p[]=$district; }
        $sql.=" ORDER BY d.name ASC";
        return $this->db->fetchAll($sql,$p);
    }

    public function destination(int $tenantId,int $id): ?array
    {
        return $this->db->fetch("SELECT d.*,
            (SELECT ROUND(AVG(r.rating),1) FROM tourism_reviews r WHERE r.tenant_id=d.tenant_id AND r.reviewable_type='DESTINATION' AND r.reviewable_id=d.id AND r.status='Approved') review_rating,
            (SELECT COUNT(*) FROM tourism_reviews r WHERE r.tenant_id=d.tenant_id AND r.reviewable_type='DESTINATION' AND r.reviewable_id=d.id AND r.status='Approved') review_count
            FROM tourism_destinations d WHERE d.tenant_id=? AND d.id=? AND d.deleted_at IS NULL LIMIT 1",[$tenantId,$id]);
    }

    public function stays(int $tenantId, ?int $destinationId=null, ?string $search=null, ?string $district=null): array
    {
        $sql="SELECT s.*, p.legal_name AS provider_name, p.phone AS provider_phone,
                    (SELECT MIN(rt.price_per_night) FROM tourism_stay_room_types rt WHERE rt.tenant_id=s.tenant_id AND rt.stay_id=s.id AND rt.deleted_at IS NULL AND rt.status='Active') room_price
              FROM tourism_stays s
              LEFT JOIN tourism_providers p ON p.id=s.provider_id AND p.tenant_id=s.tenant_id AND p.deleted_at IS NULL
              WHERE s.tenant_id=? AND s.deleted_at IS NULL";
        $p=[$tenantId];
        if($destinationId){$sql.=" AND s.destination_id=?";$p[]=$destinationId;}
        if(($district=trim((string)$district))!=='' && strcasecmp($district,'All')!==0){$sql.=" AND s.district=?";$p[]=$district;}
        if(($search=trim((string)$search))!==''){$like="%{$search}%";$sql.=" AND (s.name LIKE ? OR s.city LIKE ? OR s.district LIKE ? OR s.description LIKE ?)";array_push($p,$like,$like,$like,$like);}
        $sql.=" ORDER BY s.name ASC";
        return $this->db->fetchAll($sql,$p);
    }

    public function stay(int $tenantId,int $id): ?array
    {
        $s=$this->db->fetch("SELECT s.*,p.legal_name provider_name,p.phone provider_phone,p.email provider_email
            FROM tourism_stays s LEFT JOIN tourism_providers p ON p.id=s.provider_id AND p.tenant_id=s.tenant_id
            WHERE s.tenant_id=? AND s.id=? AND s.deleted_at IS NULL LIMIT 1",[$tenantId,$id]);
        if(!$s)return null;
        $s['rooms']=$this->db->fetchAll("SELECT * FROM tourism_stay_room_types WHERE tenant_id=? AND stay_id=? AND deleted_at IS NULL ORDER BY price_per_night ASC,name ASC",[$tenantId,$id]);
        return $s;
    }

    public function packages(int $tenantId, ?string $search=null, ?string $district=null): array
    {
        $sql="SELECT p.*,pr.legal_name provider_name,
                    (SELECT COUNT(*) FROM tourism_package_destinations pd WHERE pd.tenant_id=p.tenant_id AND pd.package_id=p.id) destination_count
              FROM tourism_packages p LEFT JOIN tourism_providers pr ON pr.id=p.provider_id AND pr.tenant_id=p.tenant_id
              WHERE p.tenant_id=? AND p.deleted_at IS NULL";
        $p=[$tenantId];
        if(($district=trim((string)$district))!=='' && strcasecmp($district,'All')!==0){$sql.=" AND EXISTS (SELECT 1 FROM tourism_package_destinations pd2 INNER JOIN tourism_destinations d2 ON d2.id=pd2.destination_id AND d2.tenant_id=pd2.tenant_id AND d2.deleted_at IS NULL WHERE pd2.tenant_id=p.tenant_id AND pd2.package_id=p.id AND d2.district=?)";$p[]=$district;}
        if(($search=trim((string)$search))!==''){$like="%{$search}%";$sql.=" AND (p.title LIKE ? OR p.description LIKE ?)";array_push($p,$like,$like);}
        $sql.=" ORDER BY p.title ASC";
        return $this->db->fetchAll($sql,$p);
    }

    public function package(int $tenantId,int $id): ?array
    {
        $p=$this->db->fetch("SELECT p.*,pr.legal_name provider_name FROM tourism_packages p LEFT JOIN tourism_providers pr ON pr.id=p.provider_id AND pr.tenant_id=p.tenant_id WHERE p.tenant_id=? AND p.id=? AND p.deleted_at IS NULL LIMIT 1",[$tenantId,$id]);
        if(!$p)return null;
        $p['destinations']=$this->db->fetchAll("SELECT pd.*,d.name destination_name,d.district,d.city,d.cover_image_path FROM tourism_package_destinations pd INNER JOIN tourism_destinations d ON d.id=pd.destination_id AND d.tenant_id=pd.tenant_id AND d.deleted_at IS NULL WHERE pd.tenant_id=? AND pd.package_id=? ORDER BY pd.day_number,pd.sequence_no,pd.id",[$tenantId,$id]);
        return $p;
    }

    public function guides(int $tenantId, ?string $search=null, ?string $district=null): array
    {
        $sql="SELECT * FROM tourism_guides WHERE tenant_id=? AND deleted_at IS NULL"; $params=[$tenantId];
        if(($district=trim((string)$district))!=='' && strcasecmp($district,'All')!==0){$sql.=" AND district=?";$params[]=$district;}
        $sql.=" ORDER BY id DESC";
        $rows=$this->db->fetchAll($sql,$params);
        $q=mb_strtolower(trim((string)$search));
        if($q==='') return $rows;
        return array_values(array_filter($rows,static function(array $r)use($q):bool{
            $hay=mb_strtolower(implode(' ',array_map(static fn($v)=>(string)$v,[
                $r['full_name']??'', $r['name']??'', $r['bio']??'', $r['languages']??'', $r['specializations']??''
            ])));
            return str_contains($hay,$q);
        }));
    }

    public function guide(int $tenantId,int $id): ?array
    { return $this->db->fetch("SELECT * FROM tourism_guides WHERE tenant_id=? AND id=? AND deleted_at IS NULL LIMIT 1",[$tenantId,$id]); }

    public function experiences(int $tenantId, ?int $destinationId=null, ?string $search=null, ?string $district=null): array
    {
        $sql="SELECT e.*,d.name destination_name FROM tourism_experiences e LEFT JOIN tourism_destinations d ON d.id=e.destination_id AND d.tenant_id=e.tenant_id AND d.deleted_at IS NULL WHERE e.tenant_id=? AND e.deleted_at IS NULL";
        $p=[$tenantId];
        if($destinationId){$sql.=" AND e.destination_id=?";$p[]=$destinationId;}
        if(($district=trim((string)$district))!=='' && strcasecmp($district,'All')!==0){$sql.=" AND d.district=?";$p[]=$district;}
        if(($search=trim((string)$search))!==''){$like="%{$search}%";$sql.=" AND (e.title LIKE ? OR e.description LIKE ? OR e.experience_type LIKE ?)";array_push($p,$like,$like,$like);}
        $sql.=" ORDER BY e.featured DESC,e.title ASC";
        return $this->db->fetchAll($sql,$p);
    }

    public function experience(int $tenantId,int $id): ?array
    { return $this->db->fetch("SELECT e.*,d.name destination_name FROM tourism_experiences e LEFT JOIN tourism_destinations d ON d.id=e.destination_id AND d.tenant_id=e.tenant_id WHERE e.tenant_id=? AND e.id=? AND e.deleted_at IS NULL LIMIT 1",[$tenantId,$id]); }

    public function events(int $tenantId, ?string $search=null, ?string $district=null): array
    {
        $sql="SELECT e.* FROM tourism_events e WHERE e.tenant_id=? AND e.deleted_at IS NULL";
        $p=[$tenantId];
        if(($district=trim((string)$district))!=='' && strcasecmp($district,'All')!==0){$sql.=" AND e.district=?";$p[]=$district;}
        if(($search=trim((string)$search))!==''){$like="%{$search}%";$sql.=" AND (e.title LIKE ? OR e.description LIKE ? OR e.venue LIKE ? OR e.district LIKE ?)";array_push($p,$like,$like,$like,$like);}
        $sql.=" ORDER BY e.featured DESC,e.start_at ASC,e.title ASC";
        return $this->db->fetchAll($sql,$p);
    }

    public function event(int $tenantId,int $id): ?array
    { return $this->db->fetch("SELECT * FROM tourism_events WHERE tenant_id=? AND id=? AND deleted_at IS NULL LIMIT 1",[$tenantId,$id]); }

    public function reviews(int $tenantId,string $type,int $id): array
    {
        return $this->db->fetchAll("SELECT r.*,COALESCE(u.name,'Visitor') reviewer_name FROM tourism_reviews r LEFT JOIN users u ON u.id=r.user_id AND u.tenant_id=r.tenant_id WHERE r.tenant_id=? AND r.reviewable_type=? AND r.reviewable_id=? AND r.status='Approved' ORDER BY r.created_at DESC,r.id DESC",[$tenantId,$type,$id]);
    }

    public function createReview(int $tenantId,int $userId,string $type,int $id,int $rating,string $title,string $review): int
    {
        $this->db->execute("INSERT INTO tourism_reviews (uuid,tenant_id,user_id,reviewable_type,reviewable_id,rating,title,review,status,created_at,updated_at) VALUES (UUID(),?,?,?,?,?,?,?,'Approved',NOW(),NOW())",[$tenantId,$userId,$type,$id,$rating,$title,$review]);
        return (int)$this->db->lastInsertId();
    }

    public function createTrip(int $tenantId,int $userId,string $title,string $description,?string $start,?string $end): int
    {
        $this->db->execute("INSERT INTO tourism_trip_plans (uuid,tenant_id,user_id,title,description,start_date,end_date,status,created_at,updated_at) VALUES (UUID(),?,?,?,?,?,?, 'Draft',NOW(),NOW())",[$tenantId,$userId,$title,$description,$start?:null,$end?:null]);
        return (int)$this->db->lastInsertId();
    }

    public function tripPlans(int $tenantId,int $userId): array
    { return $this->db->fetchAll("SELECT p.*,COUNT(i.id) item_count FROM tourism_trip_plans p LEFT JOIN tourism_trip_plan_items i ON i.trip_plan_id=p.id AND i.tenant_id=p.tenant_id WHERE p.tenant_id=? AND p.user_id=? GROUP BY p.id ORDER BY p.updated_at DESC,p.id DESC",[$tenantId,$userId]); }

    public function trip(int $tenantId,int $userId,int $id): ?array
    {
        $p=$this->db->fetch("SELECT * FROM tourism_trip_plans WHERE tenant_id=? AND user_id=? AND id=? LIMIT 1",[$tenantId,$userId,$id]);
        if(!$p)return null;
        $p['items']=$this->db->fetchAll("SELECT * FROM tourism_trip_plan_items WHERE tenant_id=? AND trip_plan_id=? ORDER BY day_number,sequence_no,id",[$tenantId,$id]);
        return $p;
    }

    public function tripPlannerSources(int $tenantId, ?string $district=null): array
    {
        return [
            'DESTINATION' => $this->destinations($tenantId,null,$district),
            'STAY' => $this->staysForPlanner($tenantId,$district),
            'PACKAGE' => $this->packages($tenantId,null,$district),
            'EXPERIENCE' => $this->experiences($tenantId,null,null,$district),
            'EVENT' => $this->events($tenantId,null,$district),
            'GUIDE' => $this->guides($tenantId,null,$district),
            'RESTAURANT' => $this->restaurantsForTrip($tenantId,$district),
            'TAXI' => $this->taxiForTrip($tenantId,$district),
            'RENTAL' => $this->rentalsForTrip($tenantId,$district),
            'FRESH_FOOD' => $this->freshFoodForTrip($tenantId,$district),
        ];
    }

    private function staysForPlanner(int $tenantId, ?string $district=null): array
    {
        $rows = $this->db->fetchAll("SELECT s.id,s.name title,s.city,s.district,s.address,
                s.stay_type,s.description,s.starting_price_per_night,p.legal_name provider_name,
                p.phone provider_phone
            FROM tourism_stays s
            LEFT JOIN tourism_providers p ON p.id=s.provider_id AND p.tenant_id=s.tenant_id AND p.deleted_at IS NULL
            WHERE s.tenant_id=? AND s.deleted_at IS NULL AND s.status='Active'
              AND (?='' OR s.district=?)
            ORDER BY s.name ASC",[$tenantId,(string)$district,(string)$district]);
        foreach($rows as &$row){
            $row['rooms']=$this->db->fetchAll("SELECT id,name,bed_type,max_guests,quantity,price_per_night
                FROM tourism_stay_room_types
                WHERE tenant_id=? AND stay_id=? AND deleted_at IS NULL AND status='Active'
                ORDER BY price_per_night ASC,name ASC",[$tenantId,(int)$row['id']]);
        }
        unset($row);
        return $rows;
    }

    private function restaurantsForTrip(int $tenantId, ?string $district=null): array
    {
        $rows=$this->db->fetchAll("SELECT rp.id,b.name title,b.city,b.district,b.address,b.phone,
                rp.cuisine_type,rp.minimum_order_amount,rp.delivery_available,rp.pickup_available,
                rp.delivery_fee,rp.free_delivery_above,rp.estimated_prep_minutes,rp.latitude,rp.longitude
            FROM restaurant_profiles rp
            INNER JOIN businesses b ON b.id=rp.business_id AND b.tenant_id=rp.tenant_id
            WHERE rp.tenant_id=? AND rp.deleted_at IS NULL AND rp.status='Active' AND b.deleted_at IS NULL AND b.status='Active'
              AND (?='' OR b.district=?)
            ORDER BY b.name ASC",[$tenantId,(string)$district,(string)$district]);
        foreach($rows as &$row){
            $row['menu_items']=$this->db->fetchAll("SELECT i.id,i.name,i.description,i.price,i.discount_type,i.discount_value,
                    i.is_veg,i.is_available,c.name category_name
                FROM restaurant_menu_items i
                LEFT JOIN restaurant_menu_categories c ON c.id=i.category_id AND c.tenant_id=i.tenant_id
                    AND c.restaurant_id=i.restaurant_id AND c.deleted_at IS NULL
                WHERE i.tenant_id=? AND i.restaurant_id=? AND i.deleted_at IS NULL AND i.status='Active'
                ORDER BY COALESCE(c.sort_order,999999),COALESCE(c.name,'Uncategorized'),i.sort_order,i.name",[$tenantId,(int)$row['id']]);
        }
        unset($row);
        return $rows;
    }

    private function taxiForTrip(int $tenantId, ?string $district=null): array
    {
        return $this->db->fetchAll("SELECT ts.id,ts.name title,ts.service_type,ts.description,
                ts.pricing_mode,ts.base_fare,ts.per_km,ts.per_minute,ts.minimum_fare,
                ts.included_km,ts.daily_rate,ts.extra_km_rate,ts.vendor_id
            FROM taxi_services ts
            INNER JOIN taxi_vendors v ON v.id=ts.vendor_id AND v.tenant_id=ts.tenant_id
            WHERE ts.tenant_id=? AND ts.deleted_at IS NULL AND ts.status='Active'
              AND v.deleted_at IS NULL AND v.status='Active'
              AND (?='' OR v.district=?)
            ORDER BY ts.name ASC",[$tenantId,(string)$district,(string)$district]);
    }

    private function rentalsForTrip(int $tenantId, ?string $district=null): array
    {
        return $this->db->fetchAll("SELECT v.id,v.name title,v.make,v.model,v.model_year,v.fuel_type,
                v.body_type,v.seating_capacity,v.capacity,v.operator_included,v.operator_name,v.operator_phone,
                v.rate_type,v.rate,v.minimum_rental,v.description,v.service_notes,c.name category_name,
                b.name provider_name
            FROM commercial_rental_vehicles v
            INNER JOIN commercial_rental_providers p ON p.id=v.provider_id AND p.tenant_id=v.tenant_id
            INNER JOIN businesses b ON b.id=p.business_id AND b.tenant_id=p.tenant_id
            LEFT JOIN commercial_rental_categories c ON c.id=v.category_id AND c.tenant_id=v.tenant_id
            WHERE v.tenant_id=? AND v.deleted_at IS NULL AND v.status='Active' AND v.availability='Available'
              AND p.deleted_at IS NULL AND p.status='Active' AND b.deleted_at IS NULL AND b.status='Active'
              AND (?='' OR p.district=?)
            ORDER BY v.name ASC",[$tenantId,(string)$district,(string)$district]);
    }

    private function freshFoodForTrip(int $tenantId, ?string $district=null): array
    {
        $rows=$this->db->fetchAll("SELECT fp.id,b.name title,b.city,b.district,b.address,b.phone,
                fp.description,fp.delivery_available,fp.pickup_available,fp.minimum_delivery_fee,
                fp.included_delivery_distance_km,fp.additional_delivery_fee_per_km,fp.latitude,fp.longitude
            FROM fresh_food_profiles fp INNER JOIN businesses b ON b.id=fp.business_id AND b.tenant_id=fp.tenant_id
            WHERE fp.tenant_id=? AND fp.deleted_at IS NULL AND fp.status='Active' AND b.deleted_at IS NULL AND b.status='Active'
              AND (?='' OR b.district=?)
            ORDER BY b.name ASC",[$tenantId,(string)$district,(string)$district]);
        foreach($rows as &$row){
            $row['products']=$this->db->fetchAll("SELECT p.id,p.name,p.description,p.unit,p.price,p.discount_type,p.discount_value,
                    p.min_order_quantity,p.increment_quantity,p.is_variable_weight,p.is_available,c.name category_name,
                    COALESCE(i.current_quantity,0) current_quantity
                FROM fresh_food_products p
                LEFT JOIN fresh_food_categories c ON c.id=p.category_id AND c.tenant_id=p.tenant_id
                    AND c.fresh_food_id=p.fresh_food_id AND c.deleted_at IS NULL
                LEFT JOIN fresh_food_inventory i ON i.product_id=p.id AND i.tenant_id=p.tenant_id
                WHERE p.tenant_id=? AND p.fresh_food_id=? AND p.deleted_at IS NULL AND p.status='Active'
                ORDER BY COALESCE(c.sort_order,999999),COALESCE(c.name,'Uncategorized'),p.sort_order,p.name",[$tenantId,(int)$row['id']]);
        }
        unset($row);
        return $rows;
    }

    public function addTripItem(int $tenantId,int $userId,int $planId,int $day,string $type,int $itemId,string $title,string $notes=''): bool
    {
        $ok=$this->db->fetch("SELECT id FROM tourism_trip_plans WHERE tenant_id=? AND user_id=? AND id=? LIMIT 1",[$tenantId,$userId,$planId]);
        if(!$ok)return false;
        $allowed=['DESTINATION','STAY','PACKAGE','EXPERIENCE','EVENT','GUIDE','RESTAURANT','TAXI','RENTAL','FRESH_FOOD'];
        if(!in_array($type,$allowed,true) || $itemId<1)return false;
        $max=$this->db->fetch("SELECT COALESCE(MAX(sequence_no),0)+1 n FROM tourism_trip_plan_items WHERE tenant_id=? AND trip_plan_id=? AND day_number=?",[$tenantId,$planId,max(1,$day)]);
        $seq=(int)($max['n']??1);
        $this->db->execute("INSERT INTO tourism_trip_plan_items (uuid,tenant_id,trip_plan_id,day_number,sequence_no,item_type,item_id,title,notes,start_time,end_time) VALUES (UUID(),?,?,?,?,?,?,?,?,NULL,NULL)",[$tenantId,$planId,max(1,$day),$seq,$type,$itemId,$title,$notes]);
        return true;
    }

    public function removeTripItem(int $tenantId,int $userId,int $planId,int $itemId): bool
    {
        $ok=$this->db->fetch("SELECT id FROM tourism_trip_plans WHERE tenant_id=? AND user_id=? AND id=? LIMIT 1",[$tenantId,$userId,$planId]);
        if(!$ok)return false;
        $this->db->execute("DELETE FROM tourism_trip_plan_items WHERE tenant_id=? AND trip_plan_id=? AND id=?",[$tenantId,$planId,$itemId]);
        return true;
    }

}
