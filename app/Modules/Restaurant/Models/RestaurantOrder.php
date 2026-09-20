<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Models;

use App\Core\Database;
use RuntimeException;

final class RestaurantOrder
{
    private Database $db;
    public function __construct(){ $this->db=app()->get('db'); }

    public function restaurants(int $tenantId): array { return $this->db->fetchAll("SELECT r.id,b.name,b.phone,b.city,b.district FROM restaurant_profiles r INNER JOIN businesses b ON b.id=r.business_id AND b.tenant_id=r.tenant_id WHERE r.tenant_id=? AND r.deleted_at IS NULL AND b.deleted_at IS NULL AND r.status='Active' ORDER BY b.name",[$tenantId]); }
    public function members(int $tenantId): array { return $this->db->fetchAll("SELECT id,first_name,middle_name,last_name,email,phone FROM members WHERE tenant_id=? AND deleted_at IS NULL AND status <> 'Inactive' ORDER BY first_name,last_name,id",[$tenantId]); }
    public function restaurant(int $tenantId,int $restaurantId): ?array { return $this->db->fetch("SELECT r.id,r.tenant_id,r.business_id,r.minimum_order_amount,r.delivery_available,r.pickup_available,r.delivery_fee,r.free_delivery_above,r.estimated_prep_minutes,r.accepting_orders,r.status,b.name AS business_name,b.phone,b.email,b.address,b.city,b.district,b.state,b.postal_code FROM restaurant_profiles r INNER JOIN businesses b ON b.id=r.business_id AND b.tenant_id=r.tenant_id WHERE r.tenant_id=? AND r.id=? AND r.deleted_at IS NULL AND b.deleted_at IS NULL LIMIT 1",[$tenantId,$restaurantId]); }
    public function item(int $tenantId,int $restaurantId,int $itemId): ?array { return $this->db->fetch("SELECT i.*,c.name AS category_name FROM restaurant_menu_items i LEFT JOIN restaurant_menu_categories c ON c.id=i.category_id AND c.tenant_id=i.tenant_id AND c.restaurant_id=i.restaurant_id AND c.deleted_at IS NULL WHERE i.tenant_id=? AND i.restaurant_id=? AND i.id=? AND i.deleted_at IS NULL AND i.status='Active' LIMIT 1",[$tenantId,$restaurantId,$itemId]); }
    public function variants(int $tenantId,int $restaurantId,int $itemId): array { return $this->db->fetchAll("SELECT id,item_id,name,price,is_available,sort_order FROM restaurant_menu_item_variants WHERE tenant_id=? AND restaurant_id=? AND item_id=? AND deleted_at IS NULL AND status='Active' ORDER BY sort_order,name",[$tenantId,$restaurantId,$itemId]); }
    public function modifierGroups(int $tenantId,int $restaurantId,int $itemId): array {
        $groups=$this->db->fetchAll("SELECT g.id,g.name,g.description,g.selection_type,g.min_selections,g.max_selections,g.is_required,g.sort_order FROM restaurant_menu_item_modifier_groups mig INNER JOIN restaurant_modifier_groups g ON g.id=mig.modifier_group_id AND g.tenant_id=mig.tenant_id AND g.restaurant_id=mig.restaurant_id AND g.deleted_at IS NULL INNER JOIN restaurant_menu_items i ON i.id=mig.item_id AND i.tenant_id=mig.tenant_id AND i.restaurant_id=mig.restaurant_id AND i.deleted_at IS NULL WHERE mig.tenant_id=? AND mig.restaurant_id=? AND mig.item_id=? ORDER BY mig.sort_order,g.sort_order,g.name",[$tenantId,$restaurantId,$itemId]);
        foreach($groups as &$group){$group['options']=$this->db->fetchAll("SELECT id,modifier_group_id,name,price_adjustment,is_available,sort_order FROM restaurant_modifier_options WHERE tenant_id=? AND restaurant_id=? AND modifier_group_id=? AND deleted_at IS NULL AND status='Active' ORDER BY sort_order,name",[$tenantId,$restaurantId,(int)$group['id']]);} unset($group); return $groups;
    }
    public function menuItems(int $tenantId,int $restaurantId): array { return $this->db->fetchAll("SELECT i.id,i.category_id,i.name,i.slug,i.description,i.price,i.discount_type,i.discount_value,i.is_veg,i.is_available,c.name AS category_name,c.sort_order AS category_sort_order FROM restaurant_menu_items i LEFT JOIN restaurant_menu_categories c ON c.id=i.category_id AND c.tenant_id=i.tenant_id AND c.restaurant_id=i.restaurant_id AND c.deleted_at IS NULL WHERE i.tenant_id=? AND i.restaurant_id=? AND i.deleted_at IS NULL AND i.status='Active' ORDER BY COALESCE(c.sort_order,999999),COALESCE(c.name,'Uncategorized'),i.sort_order,i.name",[$tenantId,$restaurantId]); }

    public function create(int $tenantId,array $order,array $items,int $createdBy): int
    {
        $this->db->beginTransaction();
        try {
            $orderNo=$this->nextOrderNo($tenantId);
            $this->db->execute("INSERT INTO restaurant_orders (uuid,tenant_id,restaurant_id,member_id,order_no,order_type,status,payment_status,payment_method,customer_name,customer_phone,customer_email,delivery_address,delivery_city,delivery_district,delivery_state,delivery_postal_code,customer_note,gross_subtotal,item_discount,coupon_code,coupon_discount,subtotal,delivery_fee,discount,total,created_by,updated_by) VALUES (UUID(),?,?,?,?,?,'PENDING','PENDING',?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?, ?,?,?,?,?)",[
                $tenantId,$order['restaurant_id'],$order['member_id'],$orderNo,$order['order_type'],$order['payment_method'],$order['customer_name'],$order['customer_phone'],$order['customer_email'],$order['delivery_address'],$order['delivery_city'],$order['delivery_district'],$order['delivery_state'],$order['delivery_postal_code'],$order['customer_note'],$order['gross_subtotal'],$order['item_discount'],$order['coupon_code'],$order['coupon_discount'],$order['subtotal'],$order['delivery_fee'],$order['discount'],$order['total'],$createdBy,$createdBy
            ]);
            $orderId=(int)$this->db->lastInsertId();
            $this->db->execute("INSERT INTO restaurant_order_status_history (uuid,tenant_id,restaurant_id,order_id,from_status,to_status,note,changed_by) VALUES (UUID(),?,?,?,NULL,'PENDING','Order created',?)",[$tenantId,$order['restaurant_id'],$orderId,$createdBy]);
            foreach($items as $line){
                $this->db->execute("INSERT INTO restaurant_order_items (uuid,tenant_id,order_id,item_id,variant_id,item_name,variant_name,original_unit_price,discount_type,discount_value,discount_amount,unit_price,quantity,line_total) VALUES (UUID(),?,?,?,?,?,?,?,?,?,?,?,?,?)",[$tenantId,$orderId,$line['item_id'],$line['variant_id'],$line['item_name'],$line['variant_name'],$line['original_unit_price'],$line['discount_type'],$line['discount_value'],$line['discount_amount'],$line['unit_price'],$line['quantity'],$line['line_total']]);
                $orderItemId=(int)$this->db->lastInsertId();
                foreach($line['modifiers'] as $modifier){$this->db->execute("INSERT INTO restaurant_order_item_modifiers (uuid,tenant_id,order_item_id,modifier_group_id,modifier_option_id,group_name,option_name,price_adjustment) VALUES (UUID(),?,?,?,?,?,?,?)",[$tenantId,$orderItemId,$modifier['group_id'],$modifier['option_id'],$modifier['group_name'],$modifier['option_name'],$modifier['price_adjustment']]);}
            }
            if (!empty($order['coupon_id'])) $this->db->execute("UPDATE restaurant_coupons SET used_count=used_count+1 WHERE tenant_id=? AND id=? AND (usage_limit IS NULL OR used_count<usage_limit)",[$tenantId,(int)$order['coupon_id']]);
            $this->db->commit(); return $orderId;
        } catch(\Throwable $e){if($this->db->inTransaction())$this->db->rollBack();throw $e;}
    }
    private function nextOrderNo(int $tenantId): string { $row=$this->db->fetch("SELECT MAX(id) AS max_id FROM restaurant_orders WHERE tenant_id=?",[$tenantId]); return 'ORD-'.date('Ymd').'-'.str_pad((string)(((int)($row['max_id']??0))+1),5,'0',STR_PAD_LEFT); }
    public function all(int $tenantId,?int $restaurantId=null,?string $status=null): array { $sql="SELECT o.*,b.name AS restaurant_name,CONCAT(COALESCE(m.first_name,''),' ',COALESCE(m.last_name,'')) AS member_name FROM restaurant_orders o INNER JOIN restaurant_profiles r ON r.id=o.restaurant_id AND r.tenant_id=o.tenant_id INNER JOIN businesses b ON b.id=r.business_id AND b.tenant_id=r.tenant_id LEFT JOIN members m ON m.id=o.member_id AND m.tenant_id=o.tenant_id WHERE o.tenant_id=?";$params=[$tenantId];if($restaurantId!==null&&$restaurantId>0){$sql.=" AND o.restaurant_id=?";$params[]=$restaurantId;}if($status){$sql.=" AND o.status=?";$params[]=$status;}$sql.=" ORDER BY o.id DESC";return $this->db->fetchAll($sql,$params); }
    public function memberOrders(int $tenantId,int $memberId): array { return $this->db->fetchAll("SELECT o.*,b.name AS restaurant_name FROM restaurant_orders o INNER JOIN restaurant_profiles r ON r.id=o.restaurant_id AND r.tenant_id=o.tenant_id INNER JOIN businesses b ON b.id=r.business_id AND b.tenant_id=r.tenant_id WHERE o.tenant_id=? AND o.member_id=? ORDER BY o.id DESC",[$tenantId,$memberId]); }
    public function find(int $tenantId,int $id): ?array { $order=$this->db->fetch("SELECT o.*,b.name AS restaurant_name,b.phone AS restaurant_phone,CONCAT(COALESCE(m.first_name,''),' ',COALESCE(m.last_name,'')) AS member_name FROM restaurant_orders o INNER JOIN restaurant_profiles r ON r.id=o.restaurant_id AND r.tenant_id=o.tenant_id INNER JOIN businesses b ON b.id=r.business_id AND b.tenant_id=r.tenant_id LEFT JOIN members m ON m.id=o.member_id AND m.tenant_id=o.tenant_id WHERE o.tenant_id=? AND o.id=? LIMIT 1",[$tenantId,$id]);if(!$order)return null;$order['items']=$this->db->fetchAll("SELECT * FROM restaurant_order_items WHERE tenant_id=? AND order_id=? ORDER BY id",[$tenantId,$id]);foreach($order['items'] as &$item){$item['modifiers']=$this->db->fetchAll("SELECT * FROM restaurant_order_item_modifiers WHERE tenant_id=? AND order_item_id=? ORDER BY id",[$tenantId,(int)$item['id']]);}unset($item);$order['status_history']=$this->db->fetchAll("SELECT h.*,COALESCE(u.name,'System') AS changed_by_name FROM restaurant_order_status_history h LEFT JOIN users u ON u.id=h.changed_by WHERE h.tenant_id=? AND h.order_id=? ORDER BY h.id ASC",[$tenantId,$id]);return $order; }
    public function updateStatus(int $tenantId,int $id,string $newStatus,?string $reason,int $userId): void
    {
        $this->db->beginTransaction();
        try {
            $order=$this->find($tenantId,$id);
            if(!$order) throw new RuntimeException('Order not found.');
            $current=(string)$order['status'];
            $allowed=['PENDING'=>['ACCEPTED','REJECTED','CANCELLED'],'ACCEPTED'=>['PREPARING','CANCELLED'],'PREPARING'=>['READY','CANCELLED'],'READY'=>$order['order_type']==='DELIVERY'?['ASSIGNED','COMPLETED']:['COMPLETED','CANCELLED'],'ASSIGNED'=>['OUT_FOR_DELIVERY'],'OUT_FOR_DELIVERY'=>['DELIVERED'],'DELIVERED'=>['COMPLETED'],'COMPLETED'=>[],'CANCELLED'=>[],'REJECTED'=>[]];
            if(!in_array($newStatus,$allowed[$current]??[],true)) throw new RuntimeException("Cannot change order from {$current} to {$newStatus}.");
            $cleanReason=trim((string)$reason);
            if(in_array($newStatus,['REJECTED','CANCELLED'],true)&&$cleanReason==='') throw new RuntimeException('A reason is required for rejection or cancellation.');
            $time=['ACCEPTED'=>'accepted_at','PREPARING'=>'preparing_at','READY'=>'ready_at','ASSIGNED'=>'assigned_at','OUT_FOR_DELIVERY'=>'out_for_delivery_at','DELIVERED'=>'delivered_at','COMPLETED'=>'completed_at','CANCELLED'=>'cancelled_at','REJECTED'=>'rejected_at'][$newStatus]??null;
            $sets='status=?,updated_by=?';$params=[$newStatus,$userId];
            if($time)$sets.=', '.$time.'=NOW()';
            if($newStatus==='REJECTED'){$sets.=',rejection_reason=?';$params[]=$cleanReason;}
            if($newStatus==='CANCELLED'){$sets.=',cancellation_reason=?';$params[]=$cleanReason;}
            $params[]=$tenantId;$params[]=$id;
            $this->db->execute("UPDATE restaurant_orders SET {$sets} WHERE tenant_id=? AND id=?",$params);
            $this->db->execute("INSERT INTO restaurant_order_status_history (uuid,tenant_id,restaurant_id,order_id,from_status,to_status,note,changed_by) VALUES (UUID(),?,?,?,?,?,?,?)",[$tenantId,$order['restaurant_id'],$id,$current,$newStatus,$cleanReason!==''?$cleanReason:null,$userId]);
            $this->db->commit();
        } catch(\Throwable $e) { if($this->db->inTransaction())$this->db->rollBack(); throw $e; }
    }
}
