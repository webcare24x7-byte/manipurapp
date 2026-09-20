<?php
declare(strict_types=1);

namespace App\Modules\FreshFood\Models;

use App\Core\Database;

final class FreshFoodOrder
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function all(int $tenantId, ?string $status = null): array
    {
        $sql = "SELECT o.*, b.name business_name
                FROM fresh_food_orders o
                JOIN fresh_food_profiles f ON f.id = o.fresh_food_id AND f.tenant_id = o.tenant_id
                JOIN businesses b ON b.id = f.business_id AND b.tenant_id = o.tenant_id
                WHERE o.tenant_id = ? AND o.deleted_at IS NULL";
        $params = [$tenantId];

        if ($status !== null && $status !== '') {
            $sql .= " AND o.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY o.id DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT o.*, b.name business_name, f.latitude business_latitude, f.longitude business_longitude
             FROM fresh_food_orders o
             JOIN fresh_food_profiles f ON f.id = o.fresh_food_id AND f.tenant_id = o.tenant_id
             JOIN businesses b ON b.id = f.business_id AND b.tenant_id = o.tenant_id
             WHERE o.tenant_id = ? AND o.id = ? AND o.deleted_at IS NULL LIMIT 1",
            [$tenantId, $id]
        );
    }

    public function items(int $tenantId, int $orderId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM fresh_food_order_items WHERE tenant_id = ? AND order_id = ? ORDER BY id",
            [$tenantId, $orderId]
        );
    }

    public function history(int $tenantId, int $orderId): array
    {
        return $this->db->fetchAll(
            "SELECT h.*, COALESCE(u.name, 'System') changed_by_name
             FROM fresh_food_order_status_history h
             LEFT JOIN users u ON u.id = h.changed_by
             WHERE h.tenant_id = ? AND h.order_id = ?
             ORDER BY h.id ASC",
            [$tenantId, $orderId]
        );
    }

    public function productsForBusiness(int $tenantId, int $freshFoodId): array
    {
        return $this->db->fetchAll(
            "SELECT p.id, p.name, p.unit, p.price, p.discount_type, p.discount_value, p.min_order_quantity, p.increment_quantity,
                    p.is_variable_weight, p.is_available, COALESCE(i.current_quantity,0) current_quantity
             FROM fresh_food_products p
             LEFT JOIN fresh_food_inventory i ON i.product_id = p.id AND i.tenant_id = p.tenant_id
             WHERE p.tenant_id = ? AND p.fresh_food_id = ? AND p.deleted_at IS NULL AND p.status = 'Active'
             ORDER BY p.sort_order, p.name",
            [$tenantId, $freshFoodId]
        );
    }

    public function businesses(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT f.id, b.name business_name
             FROM fresh_food_profiles f JOIN businesses b ON b.id = f.business_id
             WHERE f.tenant_id = ? AND f.deleted_at IS NULL AND b.deleted_at IS NULL AND f.status='Active'
             ORDER BY b.name",
            [$tenantId]
        );
    }

    public function members(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT id, first_name, middle_name, last_name, preferred_name, phone, email
             FROM members WHERE tenant_id = ? AND deleted_at IS NULL AND status = 'Active'
             ORDER BY first_name, last_name LIMIT 500",
            [$tenantId]
        );
    }

    public function users(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT id, name, phone FROM users WHERE tenant_id = ? AND status = 'Active' ORDER BY name",
            [$tenantId]
        );
    }

    public function create(int $tenantId, array $order, array $items, int $userId): int
    {
        $this->db->execute(
            "INSERT INTO fresh_food_orders
             (uuid,tenant_id,fresh_food_id,member_id,order_no,order_type,status,payment_status,payment_method,
              customer_name,customer_phone,customer_email,delivery_address,delivery_city,delivery_district,delivery_state,
              delivery_postal_code,customer_latitude,customer_longitude,distance_km,customer_note,gross_subtotal,item_discount,
              coupon_code,coupon_discount,delivery_fee,discount,total,created_by,updated_by)
             VALUES (UUID(),?,?,?,?,?,'PENDING','PENDING',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [
                $tenantId,$order['fresh_food_id'],$order['member_id'],$order['order_no'],$order['order_type'],
                $order['payment_method'],$order['customer_name'],$order['customer_phone'],$order['customer_email'],
                $order['delivery_address'],$order['delivery_city'],$order['delivery_district'],$order['delivery_state'],
                $order['delivery_postal_code'],$order['customer_latitude'],$order['customer_longitude'],$order['distance_km'],
                $order['customer_note'],$order['gross_subtotal'],$order['item_discount'],$order['coupon_code'],$order['coupon_discount'],
                $order['delivery_fee'],$order['discount'],$order['total'],$userId,$userId
            ]
        );

        $id=(int)$this->db->lastInsertId();
        foreach($items as $item){
            $this->db->execute(
                "INSERT INTO fresh_food_order_items
                 (uuid,tenant_id,order_id,product_id,product_name,unit,quantity,unit_price,original_unit_price,discount_type,discount_value,discount,discount_amount,line_total)
                 VALUES (UUID(),?,?,?,?,?,?,?,?,?,?,?,?,?)",
                [$tenantId,$id,$item['product_id'],$item['product_name'],$item['unit'],$item['quantity'],$item['unit_price'],$item['original_unit_price'],$item['discount_type'],$item['discount_value'],$item['discount'],$item['discount_amount'],$item['line_total']]
            );
        }
        $this->addHistory($tenantId,$id,null,'PENDING','Order created',$userId);
        return $id;
    }

    public function addHistory(int $tenantId, int $orderId, ?string $from, string $to, ?string $note, int $userId): void
    {
        $this->db->execute(
            "INSERT INTO fresh_food_order_status_history
             (uuid,tenant_id,order_id,from_status,to_status,note,changed_by)
             VALUES (UUID(),?,?,?,?,?,?)",
            [$tenantId,$orderId,$from,$to,$note,$userId]
        );
    }

    public function setStatus(int $tenantId, int $id, string $status, array $fields, int $userId): void
    {
        $sets = ['status = ?', 'updated_by = ?'];
        $params = [$status, $userId];

        foreach ($fields as $column => $value) {
            $sets[] = $column . ' = ?';
            $params[] = $value;
        }

        $params[] = $tenantId;
        $params[] = $id;
        $this->db->execute("UPDATE fresh_food_orders SET " . implode(', ', $sets) . " WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL", $params);
    }

    public function nextOrderNo(int $tenantId): string
    {
        $row = $this->db->fetch("SELECT MAX(id) max_id FROM fresh_food_orders WHERE tenant_id = ?", [$tenantId]);
        $next = ((int)($row['max_id'] ?? 0)) + 1;
        return 'FF-' . date('Ymd') . '-' . str_pad((string)$next, 5, '0', STR_PAD_LEFT);
    }

    public function stockState(int $tenantId, int $productId): ?array
    {
        return $this->db->fetch(
            "SELECT i.*, p.name product_name, p.unit, p.fresh_food_id
             FROM fresh_food_inventory i JOIN fresh_food_products p ON p.id = i.product_id
             WHERE i.tenant_id = ? AND i.product_id = ? LIMIT 1",
            [$tenantId, $productId]
        );
    }

    public function updateInventory(int $tenantId, int $inventoryId, float $quantity, int $userId): void
    {
        $this->db->execute("UPDATE fresh_food_inventory SET current_quantity = ?, updated_by = ? WHERE tenant_id = ? AND id = ?", [$quantity,$userId,$tenantId,$inventoryId]);
    }

    public function addInventoryMovement(int $tenantId, int $freshFoodId, int $productId, int $inventoryId, float $change, float $before, float $after, string $reference, int $userId): void
    {
        $this->db->execute(
            "INSERT INTO fresh_food_inventory_movements
             (uuid,tenant_id,fresh_food_id,product_id,inventory_id,movement_type,quantity_change,quantity_before,quantity_after,reference,created_by)
             VALUES (UUID(),?,?,?,?, 'SALE',?,?,?,?,?)",
            [$tenantId,$freshFoodId,$productId,$inventoryId,$change,$before,$after,$reference,$userId]
        );
    }
}
