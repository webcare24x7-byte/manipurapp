<?php
declare(strict_types=1);
namespace App\Modules\CommercialRental\Models;
use App\Core\Database;

final class CommercialRentalVehicle
{
    private Database $db;
    public function __construct(){ $this->db=app()->get('db'); }
    public function all(int $t):array{return $this->db->fetchAll("SELECT v.*,p.id provider_id,b.name provider_name,c.name category_name FROM commercial_rental_vehicles v INNER JOIN commercial_rental_providers p ON p.id=v.provider_id AND p.tenant_id=v.tenant_id INNER JOIN businesses b ON b.id=p.business_id AND b.tenant_id=p.tenant_id LEFT JOIN commercial_rental_categories c ON c.id=v.category_id AND c.tenant_id=v.tenant_id WHERE v.tenant_id=? AND v.deleted_at IS NULL ORDER BY v.id DESC",[$t]);}
    public function find(int $t,int $id):?array{return $this->db->fetch("SELECT v.*,p.business_id,p.address provider_address,p.city provider_city,p.district provider_district,p.state provider_state,p.latitude provider_latitude,p.longitude provider_longitude,p.service_areas provider_service_areas,b.name provider_name,c.name category_name FROM commercial_rental_vehicles v INNER JOIN commercial_rental_providers p ON p.id=v.provider_id AND p.tenant_id=v.tenant_id INNER JOIN businesses b ON b.id=p.business_id AND b.tenant_id=p.tenant_id LEFT JOIN commercial_rental_categories c ON c.id=v.category_id AND c.tenant_id=v.tenant_id WHERE v.tenant_id=? AND v.id=? AND v.deleted_at IS NULL",[$t,$id]);}
    public function create(int $t,array $d,int $u):int{
        $sql="INSERT INTO commercial_rental_vehicles(uuid,tenant_id,provider_id,category_id,name,make,model,model_year,fuel_type,ownership_type,engine_power,body_type,seating_capacity,condition_status,registration_no,capacity,operator_included,operator_name,operator_phone,operator_experience,rate_type,rate,minimum_rental,description,service_notes,photo_path,status,availability,created_by,updated_by) VALUES(UUID(),?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $this->db->execute($sql,[$t,$d['provider_id'],$d['category_id'],$d['name'],$d['make'],$d['model'],$d['model_year'],$d['fuel_type'],$d['ownership_type'],$d['engine_power'],$d['body_type'],$d['seating_capacity'],$d['condition_status'],$d['registration_no'],$d['capacity'],$d['operator_included'],$d['operator_name'],$d['operator_phone'],$d['operator_experience'],$d['rate_type'],$d['rate'],$d['minimum_rental'],$d['description'],$d['service_notes'],$d['photo_path'],$d['status'],$d['availability'],$u,$u]);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $t,int $id,array $d,int $u):void{
        $sql="UPDATE commercial_rental_vehicles SET provider_id=?,category_id=?,name=?,make=?,model=?,model_year=?,fuel_type=?,ownership_type=?,engine_power=?,body_type=?,seating_capacity=?,condition_status=?,registration_no=?,capacity=?,operator_included=?,operator_name=?,operator_phone=?,operator_experience=?,rate_type=?,rate=?,minimum_rental=?,description=?,service_notes=?,photo_path=?,status=?,availability=?,updated_by=? WHERE tenant_id=? AND id=? AND deleted_at IS NULL";
        $this->db->execute($sql,[$d['provider_id'],$d['category_id'],$d['name'],$d['make'],$d['model'],$d['model_year'],$d['fuel_type'],$d['ownership_type'],$d['engine_power'],$d['body_type'],$d['seating_capacity'],$d['condition_status'],$d['registration_no'],$d['capacity'],$d['operator_included'],$d['operator_name'],$d['operator_phone'],$d['operator_experience'],$d['rate_type'],$d['rate'],$d['minimum_rental'],$d['description'],$d['service_notes'],$d['photo_path'],$d['status'],$d['availability'],$u,$t,$id]);
    }
    public function delete(int $t,int $id,int $u):void{$this->db->execute("UPDATE commercial_rental_vehicles SET deleted_at=NOW(),updated_by=? WHERE tenant_id=? AND id=? AND deleted_at IS NULL",[$u,$t,$id]);}
    public function registrationExists(int $t,string $r,?int $ignore=null):bool{if(!$r)return false;$sql="SELECT id FROM commercial_rental_vehicles WHERE tenant_id=? AND registration_no=? AND deleted_at IS NULL";$p=[$t,$r];if($ignore!==null){$sql.=' AND id<>?';$p[]=$ignore;}return $this->db->fetch($sql.' LIMIT 1',$p)!==null;}
    public function count(int $t):int{$r=$this->db->fetch("SELECT COUNT(*) c FROM commercial_rental_vehicles WHERE tenant_id=? AND deleted_at IS NULL",[$t]);return (int)($r['c']??0);}
}
