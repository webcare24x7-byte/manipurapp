<?php
declare(strict_types=1);

namespace App\Modules\FreshFood\Models;

use App\Core\Database;

final class FreshFoodProduct
{
    private Database $db;
    public function __construct(){ $this->db=app()->get('db'); }

    public function all(int $t,bool $trash=false):array
    {
        $w=$trash?'p.deleted_at IS NOT NULL':'p.deleted_at IS NULL';
        return $this->db->fetchAll("SELECT p.*,b.name business_name,c.name category_name FROM fresh_food_products p JOIN fresh_food_profiles f ON f.id=p.fresh_food_id JOIN businesses b ON b.id=f.business_id LEFT JOIN fresh_food_categories c ON c.id=p.category_id WHERE p.tenant_id=? AND {$w} ORDER BY p.fresh_food_id,p.sort_order,p.id DESC",[$t]);
    }
    public function businesses(int $t):array{return $this->db->fetchAll("SELECT f.id,b.name business_name FROM fresh_food_profiles f JOIN businesses b ON b.id=f.business_id WHERE f.tenant_id=? AND f.deleted_at IS NULL AND b.deleted_at IS NULL ORDER BY b.name",[$t]);}
    public function categories(int $t,int $f):array{return $this->db->fetchAll("SELECT id,name FROM fresh_food_categories WHERE tenant_id=? AND fresh_food_id=? AND deleted_at IS NULL AND status='Active' ORDER BY sort_order,name",[$t,$f]);}
    public function find(int $t,int $id):?array{return $this->db->fetch("SELECT p.*,b.name business_name,c.name category_name FROM fresh_food_products p JOIN fresh_food_profiles f ON f.id=p.fresh_food_id JOIN businesses b ON b.id=f.business_id LEFT JOIN fresh_food_categories c ON c.id=p.category_id WHERE p.tenant_id=? AND p.id=? AND p.deleted_at IS NULL LIMIT 1",[$t,$id]);}
    public function findDeleted(int $t,int $id):?array{return $this->db->fetch("SELECT p.* FROM fresh_food_products p WHERE p.tenant_id=? AND p.id=? AND p.deleted_at IS NOT NULL LIMIT 1",[$t,$id]);}
    public function businessExists(int $t,int $id):bool{return $this->db->fetch("SELECT id FROM fresh_food_profiles WHERE tenant_id=? AND id=? AND deleted_at IS NULL",[$t,$id])!==null;}
    public function categoryBelongs(int $t,int $f,int $c):bool{return $this->db->fetch("SELECT id FROM fresh_food_categories WHERE tenant_id=? AND id=? AND fresh_food_id=? AND deleted_at IS NULL",[$t,$c,$f])!==null;}
    public function slugExists(int $t,int $f,string $slug,?int $ignore=null):bool{$q="SELECT id FROM fresh_food_products WHERE tenant_id=? AND fresh_food_id=? AND slug=? AND deleted_at IS NULL";$p=[$t,$f,$slug];if($ignore!==null){$q.=" AND id<>?";$p[]=$ignore;}return $this->db->fetch($q.' LIMIT 1',$p)!==null;}
    public function create(int $t,array $d,int $u):int{$this->db->execute("INSERT INTO fresh_food_products(uuid,tenant_id,fresh_food_id,category_id,name,slug,description,unit,price,discount_type,discount_value,min_order_quantity,increment_quantity,is_variable_weight,is_available,sort_order,status,created_by,updated_by) VALUES(UUID(),?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",[$t,$d['fresh_food_id'],$d['category_id'],$d['name'],$d['slug'],$d['description'],$d['unit'],$d['price'],$d['discount_type'],$d['discount_value'],$d['min_order_quantity'],$d['increment_quantity'],$d['is_variable_weight'],$d['is_available'],$d['sort_order'],$d['status'],$u,$u]);return(int)$this->db->lastInsertId();}
    public function update(int $t,int $id,array $d,int $u):void{$this->db->execute("UPDATE fresh_food_products SET fresh_food_id=?,category_id=?,name=?,slug=?,description=?,unit=?,price=?,discount_type=?,discount_value=?,min_order_quantity=?,increment_quantity=?,is_variable_weight=?,is_available=?,sort_order=?,status=?,updated_by=? WHERE tenant_id=? AND id=? AND deleted_at IS NULL",[$d['fresh_food_id'],$d['category_id'],$d['name'],$d['slug'],$d['description'],$d['unit'],$d['price'],$d['discount_type'],$d['discount_value'],$d['min_order_quantity'],$d['increment_quantity'],$d['is_variable_weight'],$d['is_available'],$d['sort_order'],$d['status'],$u,$t,$id]);}
    public function delete(int $t,int $id,int $u):void{$this->db->execute("UPDATE fresh_food_products SET deleted_at=NOW(),updated_by=? WHERE tenant_id=? AND id=? AND deleted_at IS NULL",[$u,$t,$id]);}
    public function restore(int $t,int $id,int $u):void{$this->db->execute("UPDATE fresh_food_products SET deleted_at=NULL,updated_by=? WHERE tenant_id=? AND id=? AND deleted_at IS NOT NULL",[$u,$t,$id]);}
}
