<?php
declare(strict_types=1);
namespace App\Modules\Tourism\Models;
use App\Core\Database;
final class TourismGuide{
 private Database $db;public function __construct(){$this->db=app()->get('db');}
 public function all(int $t,bool $deleted=false):array{$w=$deleted?'deleted_at IS NOT NULL':'deleted_at IS NULL';return $this->db->fetchAll("SELECT * FROM tourism_guides WHERE tenant_id=? AND {$w} ORDER BY id DESC",[$t]);}
 public function find(int $t,int $id,bool $deleted=false):?array{$w=$deleted?'deleted_at IS NOT NULL':'deleted_at IS NULL';return $this->db->fetch("SELECT * FROM tourism_guides WHERE tenant_id=? AND id=? AND {$w} LIMIT 1",[$t,$id]);}
 public function create(int $t,array $d,int $u):int{$this->db->execute("INSERT INTO tourism_guides(uuid,tenant_id,business_id,name,slug,bio,profile_image_path,phone,email,city,district,state,latitude,longitude,languages,specializations,experience_years,price_per_day,verification_status,status,created_by,updated_by) VALUES(UUID(),?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",[$t,$d['business_id'],$d['name'],$d['slug'],$d['bio'],$d['profile_image_path'],$d['phone'],$d['email'],$d['city'],$d['district'],$d['state'],$d['latitude'],$d['longitude'],$d['languages'],$d['specializations'],$d['experience_years'],$d['price_per_day'],$d['verification_status'],$d['status'],$u,$u]);return (int)$this->db->lastInsertId();}
 public function update(int $t,int $id,array $d,int $u):void{$this->db->execute("UPDATE tourism_guides SET business_id=?,name=?,slug=?,bio=?,profile_image_path=?,phone=?,email=?,city=?,district=?,state=?,latitude=?,longitude=?,languages=?,specializations=?,experience_years=?,price_per_day=?,verification_status=?,status=?,updated_by=? WHERE tenant_id=? AND id=? AND deleted_at IS NULL",[$d['business_id'],$d['name'],$d['slug'],$d['bio'],$d['profile_image_path'],$d['phone'],$d['email'],$d['city'],$d['district'],$d['state'],$d['latitude'],$d['longitude'],$d['languages'],$d['specializations'],$d['experience_years'],$d['price_per_day'],$d['verification_status'],$d['status'],$u,$t,$id]);}
 public function softDelete(int $t,int $id,int $u):void{$this->db->execute("UPDATE tourism_guides SET deleted_at=NOW(),updated_by=? WHERE tenant_id=? AND id=? AND deleted_at IS NULL",[$u,$t,$id]);}
 public function restore(int $t,int $id,int $u):void{$this->db->execute("UPDATE tourism_guides SET deleted_at=NULL,updated_by=? WHERE tenant_id=? AND id=?",[$u,$t,$id]);}
 public function nameExists(int $t,string $name,?int $ignore=null):bool{$sql="SELECT id FROM tourism_guides WHERE tenant_id=? AND name=? AND deleted_at IS NULL";$p=[$t,$name];if($ignore!==null){$sql.=" AND id<>?";$p[]=$ignore;}return $this->db->fetch($sql.' LIMIT 1',$p)!==null;}
 public function slugExists(int $t,string $slug,?int $ignore=null):bool{$sql="SELECT id FROM tourism_guides WHERE tenant_id=? AND slug=? AND deleted_at IS NULL";$p=[$t,$slug];if($ignore!==null){$sql.=" AND id<>?";$p[]=$ignore;}return $this->db->fetch($sql.' LIMIT 1',$p)!==null;}
}
