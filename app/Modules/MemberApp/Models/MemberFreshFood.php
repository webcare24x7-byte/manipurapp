<?php

declare(strict_types=1);

namespace App\Modules\MemberApp\Models;

use App\Core\Database;

final class MemberFreshFood
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function businesses(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT f.id, f.business_id, f.description, f.logo_path, f.cover_image_path,
                    f.latitude, f.longitude, f.delivery_available, f.pickup_available,
                    f.max_delivery_distance_km, f.minimum_delivery_fee,
                    f.included_delivery_distance_km, f.additional_delivery_fee_per_km,
                    f.preorder_enabled, f.preorder_min_advance_hours, f.preorder_delivery_fee,
                    f.estimated_packing_minutes_min, f.estimated_packing_minutes_max,
                    f.accepting_orders, f.status,
                    b.name AS business_name, b.phone, b.email, b.address, b.city,
                    b.district, b.state, b.postal_code
             FROM fresh_food_profiles f
             INNER JOIN businesses b ON b.id = f.business_id AND b.tenant_id = f.tenant_id
             WHERE f.tenant_id = ? AND f.deleted_at IS NULL AND b.deleted_at IS NULL
               AND f.status = 'Active' AND b.status = 'Active'
             ORDER BY b.name",
            [$tenantId]
        );
    }

    public function business(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT f.*, b.name AS business_name, b.phone, b.email, b.address, b.city,
                    b.district, b.state, b.postal_code
             FROM fresh_food_profiles f
             INNER JOIN businesses b ON b.id = f.business_id AND b.tenant_id = f.tenant_id
             WHERE f.tenant_id = ? AND f.id = ? AND f.deleted_at IS NULL
               AND b.deleted_at IS NULL AND f.status = 'Active' AND b.status = 'Active'
             LIMIT 1",
            [$tenantId, $id]
        );
    }

    public function hours(int $tenantId, int $freshFoodId): array
    {
        return $this->db->fetchAll(
            "SELECT day_of_week, opens_at, closes_at, is_closed
             FROM fresh_food_hours
             WHERE tenant_id = ? AND fresh_food_id = ?
             ORDER BY day_of_week",
            [$tenantId, $freshFoodId]
        );
    }

    public function categories(int $tenantId, int $freshFoodId): array
    {
        return $this->db->fetchAll(
            "SELECT id, name, description, sort_order
             FROM fresh_food_categories
             WHERE tenant_id = ? AND fresh_food_id = ?
               AND deleted_at IS NULL AND status = 'Active'
             ORDER BY sort_order, name",
            [$tenantId, $freshFoodId]
        );
    }

    public function products(int $tenantId, int $freshFoodId, ?int $categoryId = null): array
    {
        $sql = "SELECT p.id, p.fresh_food_id, p.category_id, p.name, p.slug, p.description, p.image_path,
                       p.unit, p.price, p.discount_type, p.discount_value,
                       p.min_order_quantity, p.increment_quantity, p.is_variable_weight,
                       p.is_available, p.sort_order, c.name AS category_name,
                       COALESCE(i.current_quantity, 0) AS current_quantity
                FROM fresh_food_products p
                LEFT JOIN fresh_food_categories c
                  ON c.id = p.category_id AND c.tenant_id = p.tenant_id
                 AND c.fresh_food_id = p.fresh_food_id AND c.deleted_at IS NULL
                LEFT JOIN fresh_food_inventory i
                  ON i.product_id = p.id AND i.tenant_id = p.tenant_id
                WHERE p.tenant_id = ? AND p.fresh_food_id = ?
                  AND p.deleted_at IS NULL AND p.status = 'Active'";
        $params = [$tenantId, $freshFoodId];

        if ($categoryId !== null && $categoryId > 0) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY COALESCE(c.sort_order, 999999), COALESCE(c.name, 'Uncategorized'), p.sort_order, p.name";
        return $this->db->fetchAll($sql, $params);
    }

    public function product(int $tenantId, int $freshFoodId, int $productId): ?array
    {
        return $this->db->fetch(
            "SELECT p.*, c.name AS category_name, COALESCE(i.current_quantity, 0) AS current_quantity
             FROM fresh_food_products p
             LEFT JOIN fresh_food_categories c
               ON c.id = p.category_id AND c.tenant_id = p.tenant_id
              AND c.fresh_food_id = p.fresh_food_id AND c.deleted_at IS NULL
             LEFT JOIN fresh_food_inventory i
               ON i.product_id = p.id AND i.tenant_id = p.tenant_id
             WHERE p.tenant_id = ? AND p.fresh_food_id = ? AND p.id = ?
               AND p.deleted_at IS NULL AND p.status = 'Active'
             LIMIT 1",
            [$tenantId, $freshFoodId, $productId]
        );
    }

    public function memberProfile(int $tenantId, int $memberId): ?array
    {
        return $this->db->fetch(
            "SELECT id, first_name, middle_name, last_name, preferred_name, phone, email,
                    address, city, NULL AS district, state, postal_code
             FROM members
             WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL AND status = 'Active'
             LIMIT 1",
            [$tenantId, $memberId]
        );
    }

    public function claimRoute(int $tenantId, int $memberId, int $orderId): bool
    {
        $this->db->execute(
            "UPDATE fresh_food_orders
             SET route_status = 'PROCESSING', route_error = NULL, updated_at = CURRENT_TIMESTAMP
             WHERE tenant_id = ? AND member_id = ? AND id = ? AND deleted_at IS NULL
               AND order_type = 'DELIVERY'
               AND COALESCE(route_status, 'PENDING') IN ('PENDING', 'FAILED')",
            [$tenantId, $memberId, $orderId]
        );

        return true;
    }

    public function commitRouteEstimate(
        int $tenantId,
        int $memberId,
        int $orderId,
        float $distanceKm,
        int $etaMinutes,
        float $deliveryFee,
        float $total
    ): void {
        $this->db->execute(
            "UPDATE fresh_food_orders
             SET distance_km = ?, eta_minutes = ?, delivery_fee = ?, total = ?,
                 route_status = 'COMPLETED', route_error = NULL,
                 route_calculated_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP
             WHERE tenant_id = ? AND member_id = ? AND id = ? AND deleted_at IS NULL
               AND order_type = 'DELIVERY'",
            [$distanceKm, $etaMinutes, $deliveryFee, $total, $tenantId, $memberId, $orderId]
        );
    }

    public function failRouteEstimate(int $tenantId, int $memberId, int $orderId, string $error): void
    {
        $safe = trim($error);
        if (strlen($safe) > 1000) $safe = substr($safe, 0, 1000);

        $this->db->execute(
            "UPDATE fresh_food_orders
             SET route_status = 'FAILED', route_error = ?, updated_at = CURRENT_TIMESTAMP
             WHERE tenant_id = ? AND member_id = ? AND id = ? AND deleted_at IS NULL
               AND order_type = 'DELIVERY'",
            [$safe !== '' ? $safe : 'Route calculation failed.', $tenantId, $memberId, $orderId]
        );
    }

    public function memberOrders(int $tenantId, int $memberId): array
    {
        return $this->db->fetchAll(
            "SELECT o.*, b.name AS fresh_food_name, f.logo_path, f.cover_image_path
             FROM fresh_food_orders o
             INNER JOIN fresh_food_profiles f ON f.id = o.fresh_food_id AND f.tenant_id = o.tenant_id
             INNER JOIN businesses b ON b.id = f.business_id AND b.tenant_id = f.tenant_id
             WHERE o.tenant_id = ? AND o.member_id = ? AND o.deleted_at IS NULL
             ORDER BY o.created_at DESC, o.id DESC",
            [$tenantId, $memberId]
        );
    }

    public function orderForMember(int $tenantId, int $memberId, int $orderId): ?array
    {
        $order = $this->db->fetch(
            "SELECT o.*, b.name AS fresh_food_name, b.phone AS fresh_food_phone,
                    b.address AS fresh_food_address, b.city AS fresh_food_city,
                    f.logo_path, f.cover_image_path,
                    f.estimated_packing_minutes_min, f.estimated_packing_minutes_max
             FROM fresh_food_orders o
             INNER JOIN fresh_food_profiles f ON f.id = o.fresh_food_id AND f.tenant_id = o.tenant_id
             INNER JOIN businesses b ON b.id = f.business_id AND b.tenant_id = f.tenant_id
             WHERE o.tenant_id = ? AND o.member_id = ? AND o.id = ? AND o.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $memberId, $orderId]
        );

        if (!$order) {
            return null;
        }

        $order['items'] = $this->db->fetchAll(
            "SELECT * FROM fresh_food_order_items
             WHERE tenant_id = ? AND order_id = ? ORDER BY id",
            [$tenantId, $orderId]
        );

        $order['status_history'] = $this->db->fetchAll(
            "SELECT h.id, h.from_status, h.to_status, h.note, h.created_at,
                    COALESCE(u.name, 'ManipurApp') AS changed_by_name
             FROM fresh_food_order_status_history h
             LEFT JOIN users u ON u.id = h.changed_by AND u.tenant_id = h.tenant_id
             WHERE h.tenant_id = ? AND h.order_id = ?
             ORDER BY h.created_at ASC, h.id ASC",
            [$tenantId, $orderId]
        );

        return $order;
    }

    public function saveCart(int $tenantId, int $memberId, int $freshFoodId, array $items, ?string $couponCode): void
    {
        $json = json_encode(array_values($items), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new \RuntimeException('Unable to save cart.');
        }

        $existing = $this->db->fetch(
            "SELECT id FROM member_fresh_food_carts
             WHERE tenant_id = ? AND member_id = ? AND fresh_food_id = ? LIMIT 1",
            [$tenantId, $memberId, $freshFoodId]
        );

        if ($existing) {
            $this->db->execute(
                "UPDATE member_fresh_food_carts
                 SET items_json = ?, coupon_code = ?, updated_at = CURRENT_TIMESTAMP
                 WHERE id = ? AND tenant_id = ? AND member_id = ?",
                [$json, $couponCode, (int) $existing['id'], $tenantId, $memberId]
            );
            return;
        }

        $this->db->execute(
            "INSERT INTO member_fresh_food_carts
             (uuid, tenant_id, member_id, fresh_food_id, items_json, coupon_code)
             VALUES (UUID(), ?, ?, ?, ?, ?)",
            [$tenantId, $memberId, $freshFoodId, $json, $couponCode]
        );
    }

    public function loadCart(int $tenantId, int $memberId, ?int $freshFoodId = null): ?array
    {
        $sql = "SELECT * FROM member_fresh_food_carts WHERE tenant_id = ? AND member_id = ?";
        $params = [$tenantId, $memberId];
        if ($freshFoodId !== null && $freshFoodId > 0) {
            $sql .= " AND fresh_food_id = ?";
            $params[] = $freshFoodId;
        }
        $sql .= " ORDER BY updated_at DESC, id DESC LIMIT 1";
        $row = $this->db->fetch($sql, $params);
        if (!$row) {
            return null;
        }

        $items = json_decode((string) $row['items_json'], true);
        return [
            'fresh_food_id' => (int) $row['fresh_food_id'],
            'items' => is_array($items) ? array_values($items) : [],
            'coupon_code' => $row['coupon_code'] !== null ? (string) $row['coupon_code'] : null,
        ];
    }

    public function deleteCart(int $tenantId, int $memberId, ?int $freshFoodId = null): void
    {
        $sql = "DELETE FROM member_fresh_food_carts WHERE tenant_id = ? AND member_id = ?";
        $params = [$tenantId, $memberId];
        if ($freshFoodId !== null && $freshFoodId > 0) {
            $sql .= " AND fresh_food_id = ?";
            $params[] = $freshFoodId;
        }
        $this->db->execute($sql, $params);
    }
}
