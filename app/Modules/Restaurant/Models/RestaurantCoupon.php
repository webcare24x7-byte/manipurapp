<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Models;

use App\Core\Database;

final class RestaurantCoupon
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function all(int $tenantId, ?int $restaurantId = null, bool $withDeleted = false): array
    {
        $sql = "SELECT c.*, b.name AS restaurant_name
                FROM restaurant_coupons c
                INNER JOIN restaurant_profiles r ON r.id = c.restaurant_id AND r.tenant_id = c.tenant_id
                INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
                WHERE c.tenant_id = ?";
        $params = [$tenantId];
        if ($restaurantId !== null && $restaurantId > 0) { $sql .= " AND c.restaurant_id = ?"; $params[] = $restaurantId; }
        $sql .= $withDeleted ? " AND c.deleted_at IS NOT NULL" : " AND c.deleted_at IS NULL";
        $sql .= " ORDER BY c.restaurant_id, c.id DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM restaurant_coupons WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL LIMIT 1", [$tenantId, $id]);
    }

    public function codeExists(int $tenantId, int $restaurantId, string $code, ?int $ignoreId = null): bool
    {
        $sql = "SELECT id FROM restaurant_coupons WHERE tenant_id = ? AND restaurant_id = ? AND code = ? AND deleted_at IS NULL";
        $params = [$tenantId, $restaurantId, $code];
        if ($ignoreId !== null) { $sql .= " AND id <> ?"; $params[] = $ignoreId; }
        return $this->db->fetch($sql . " LIMIT 1", $params) !== null;
    }

    public function create(int $tenantId, array $data, int $userId): int
    {
        $this->db->execute(
            "INSERT INTO restaurant_coupons
             (uuid, tenant_id, restaurant_id, code, name, description, discount_type, discount_value,
              minimum_order_amount, maximum_discount, starts_at, ends_at, usage_limit, used_count, status, created_by, updated_by)
             VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?)",
            [$tenantId, $data['restaurant_id'], $data['code'], $data['name'], $data['description'], $data['discount_type'],
             $data['discount_value'], $data['minimum_order_amount'], $data['maximum_discount'], $data['starts_at'], $data['ends_at'],
             $data['usage_limit'], $data['status'], $userId, $userId]
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $tenantId, int $id, array $data, int $userId): void
    {
        $this->db->execute(
            "UPDATE restaurant_coupons SET restaurant_id = ?, code = ?, name = ?, description = ?, discount_type = ?,
             discount_value = ?, minimum_order_amount = ?, maximum_discount = ?, starts_at = ?, ends_at = ?, usage_limit = ?,
             status = ?, updated_by = ? WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL",
            [$data['restaurant_id'], $data['code'], $data['name'], $data['description'], $data['discount_type'], $data['discount_value'],
             $data['minimum_order_amount'], $data['maximum_discount'], $data['starts_at'], $data['ends_at'], $data['usage_limit'],
             $data['status'], $userId, $tenantId, $id]
        );
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        $this->db->execute("UPDATE restaurant_coupons SET deleted_at = NOW(), updated_by = ? WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL", [$userId, $tenantId, $id]);
    }

    public function findApplicable(int $tenantId, int $restaurantId, string $code, float $subtotal): ?array
    {
        $coupon = $this->db->fetch(
            "SELECT * FROM restaurant_coupons
             WHERE tenant_id = ? AND restaurant_id = ? AND code = ? AND status = 'Active' AND deleted_at IS NULL
               AND (starts_at IS NULL OR starts_at <= NOW())
               AND (ends_at IS NULL OR ends_at >= NOW())
               AND (usage_limit IS NULL OR used_count < usage_limit)
             LIMIT 1",
            [$tenantId, $restaurantId, strtoupper(trim($code))]
        );
        if (!$coupon) return null;
        if ($subtotal < (float)$coupon['minimum_order_amount']) return null;
        return $coupon;
    }

    public function incrementUsage(int $tenantId, int $id): void
    {
        $this->db->execute("UPDATE restaurant_coupons SET used_count = used_count + 1 WHERE tenant_id = ? AND id = ? AND (usage_limit IS NULL OR used_count < usage_limit)", [$tenantId, $id]);
    }
}
