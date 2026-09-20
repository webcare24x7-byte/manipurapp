<?php
declare(strict_types=1);
namespace App\Modules\FreshFood\Models;
use App\Core\Database;
final class FreshFoodCoupon
{
    private Database $db;
    public function __construct(){ $this->db=app()->get('db'); }
    public function all(int $t,?int $freshFoodId=null,bool $deleted=false):array
    {
        $sql="SELECT c.*,b.name business_name FROM fresh_food_coupons c JOIN fresh_food_profiles f ON f.id=c.fresh_food_id AND f.tenant_id=c.tenant_id JOIN businesses b ON b.id=f.business_id AND b.tenant_id=f.tenant_id WHERE c.tenant_id=?";$p=[$t];
        if($freshFoodId!==null&&$freshFoodId>0){$sql.=" AND c.fresh_food_id=?";$p[]=$freshFoodId;}$sql.=$deleted?" AND c.deleted_at IS NOT NULL":" AND c.deleted_at IS NULL";return $this->db->fetchAll($sql." ORDER BY c.fresh_food_id,c.id DESC",$p);
    }
    public function find(int $t,int $id):?array{return $this->db->fetch("SELECT * FROM fresh_food_coupons WHERE tenant_id=? AND id=? AND deleted_at IS NULL LIMIT 1",[$t,$id]);}
    public function codeExists(int $t,int $f,string $code,?int $ignore=null):bool{$q="SELECT id FROM fresh_food_coupons WHERE tenant_id=? AND fresh_food_id=? AND code=? AND deleted_at IS NULL";$p=[$t,$f,$code];if($ignore!==null){$q.=" AND id<>?";$p[]=$ignore;}return $this->db->fetch($q." LIMIT 1",$p)!==null;}
    public function create(int $t,array $d,int $u):int{$this->db->execute("INSERT INTO fresh_food_coupons(uuid,tenant_id,fresh_food_id,code,name,description,discount_type,discount_value,minimum_order_amount,maximum_discount,starts_at,ends_at,usage_limit,used_count,status,created_by,updated_by) VALUES(UUID(),?,?,?,?,?,?,?,?,?,?,?, ?,0,?,?,?)",[$t,$d['fresh_food_id'],$d['code'],$d['name'],$d['description'],$d['discount_type'],$d['discount_value'],$d['minimum_order_amount'],$d['maximum_discount'],$d['starts_at'],$d['ends_at'],$d['usage_limit'],$d['status'],$u,$u]);return (int)$this->db->lastInsertId();}
    public function update(int $t,int $id,array $d,int $u):void{$this->db->execute("UPDATE fresh_food_coupons SET fresh_food_id=?,code=?,name=?,description=?,discount_type=?,discount_value=?,minimum_order_amount=?,maximum_discount=?,starts_at=?,ends_at=?,usage_limit=?,status=?,updated_by=? WHERE tenant_id=? AND id=? AND deleted_at IS NULL",[$d['fresh_food_id'],$d['code'],$d['name'],$d['description'],$d['discount_type'],$d['discount_value'],$d['minimum_order_amount'],$d['maximum_discount'],$d['starts_at'],$d['ends_at'],$d['usage_limit'],$d['status'],$u,$t,$id]);}
    public function delete(int $t,int $id,int $u):void{$this->db->execute("UPDATE fresh_food_coupons SET deleted_at=NOW(),updated_by=? WHERE tenant_id=? AND id=? AND deleted_at IS NULL",[$u,$t,$id]);}
    public function findApplicable(int $t,int $f,string $code,float $subtotal):?array{return $this->db->fetch("SELECT * FROM fresh_food_coupons WHERE tenant_id=? AND fresh_food_id=? AND code=? AND status='Active' AND deleted_at IS NULL AND (starts_at IS NULL OR starts_at<=NOW()) AND (ends_at IS NULL OR ends_at>=NOW()) AND (usage_limit IS NULL OR used_count<usage_limit) AND minimum_order_amount<=? LIMIT 1",[$t,$f,strtoupper(trim($code)),$subtotal]);}
    public function incrementUsage(int $t,int $id):void{$this->db->execute("UPDATE fresh_food_coupons SET used_count=used_count+1 WHERE tenant_id=? AND id=? AND (usage_limit IS NULL OR used_count<usage_limit)",[$t,$id]);}
}
