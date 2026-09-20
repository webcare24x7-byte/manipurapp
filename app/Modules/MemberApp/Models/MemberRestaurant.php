<?php

declare(strict_types=1);

namespace App\Modules\MemberApp\Models;

use App\Core\Database;

final class MemberRestaurant
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function restaurants(
        int $tenantId,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $search = null,
        ?string $cuisine = null
    ): array {
        $sql = "SELECT r.id, r.latitude, r.longitude, r.cuisine_type,
                       r.minimum_order_amount, r.delivery_available, r.pickup_available,
                       r.delivery_fee, r.free_delivery_above, r.estimated_prep_minutes,
                       r.accepting_orders, r.status, r.logo_path, r.cover_image_path,
                       b.name AS business_name, b.phone, b.address, b.city, b.district,
                       b.state, b.postal_code
                FROM restaurant_profiles r
                INNER JOIN businesses b
                  ON b.id = r.business_id
                 AND b.tenant_id = r.tenant_id
                WHERE r.tenant_id = ?
                  AND r.deleted_at IS NULL
                  AND b.deleted_at IS NULL
                  AND r.status = 'Active'";
        $params = [$tenantId];

        $search = trim((string) $search);
        if ($search !== '') {
            $like = '%' . $search . '%';
            $sql .= " AND (
                b.name LIKE ?
                OR r.cuisine_type LIKE ?
                OR EXISTS (
                    SELECT 1
                    FROM restaurant_menu_items si
                    WHERE si.tenant_id = r.tenant_id
                      AND si.restaurant_id = r.id
                      AND si.deleted_at IS NULL
                      AND si.status = 'Active'
                      AND si.name LIKE ?
                )
            )";
            array_push($params, $like, $like, $like);
        }

        $cuisine = trim((string) $cuisine);
        if ($cuisine !== '' && strcasecmp($cuisine, 'All') !== 0) {
            $sql .= " AND r.cuisine_type LIKE ?";
            $params[] = '%' . $cuisine . '%';
        }

        $sql .= " ORDER BY b.name ASC, r.id ASC";

        return $this->db->fetchAll($sql, $params);
    }

    public function restaurant(int $tenantId, int $restaurantId): ?array
    {
        return $this->db->fetch(
            "SELECT r.id, r.tenant_id, r.business_id, r.latitude, r.longitude,
                    r.description, r.logo_path, r.cover_image_path, r.cuisine_type,
                    r.minimum_order_amount, r.delivery_available, r.pickup_available,
                    r.delivery_fee, r.free_delivery_above, r.estimated_prep_minutes,
                    r.accepting_orders, r.status,
                    b.name AS business_name, b.phone, b.email, b.address, b.city,
                    b.district, b.state, b.postal_code
             FROM restaurant_profiles r
             INNER JOIN businesses b
               ON b.id = r.business_id
              AND b.tenant_id = r.tenant_id
             WHERE r.tenant_id = ?
               AND r.id = ?
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
               AND r.status = 'Active'
             LIMIT 1",
            [$tenantId, $restaurantId]
        );
    }

    public function hours(int $tenantId, int $restaurantId): array
    {
        return $this->db->fetchAll(
            "SELECT day_of_week, opens_at, closes_at, is_closed
             FROM restaurant_hours
             WHERE tenant_id = ? AND restaurant_id = ?
             ORDER BY day_of_week ASC",
            [$tenantId, $restaurantId]
        );
    }

    public function menuItems(int $tenantId, int $restaurantId): array
    {
        return $this->db->fetchAll(
            "SELECT i.id, i.category_id, i.name, i.slug, i.description, i.image_path,
                    i.price, i.discount_type, i.discount_value, i.is_veg, i.is_available,
                    i.sort_order, c.name AS category_name,
                    c.sort_order AS category_sort_order
             FROM restaurant_menu_items i
             LEFT JOIN restaurant_menu_categories c
               ON c.id = i.category_id
              AND c.tenant_id = i.tenant_id
              AND c.restaurant_id = i.restaurant_id
              AND c.deleted_at IS NULL
             WHERE i.tenant_id = ?
               AND i.restaurant_id = ?
               AND i.deleted_at IS NULL
               AND i.status = 'Active'
             ORDER BY COALESCE(c.sort_order, 999999),
                      COALESCE(c.name, 'Uncategorized'),
                      i.sort_order, i.name",
            [$tenantId, $restaurantId]
        );
    }

    public function item(int $tenantId, int $restaurantId, int $itemId): ?array
    {
        return $this->db->fetch(
            "SELECT i.id, i.restaurant_id, i.category_id, i.name, i.slug,
                    i.description, i.image_path, i.price, i.discount_type,
                    i.discount_value, i.is_veg, i.is_available, i.sort_order,
                    c.name AS category_name
             FROM restaurant_menu_items i
             LEFT JOIN restaurant_menu_categories c
               ON c.id = i.category_id
              AND c.tenant_id = i.tenant_id
              AND c.restaurant_id = i.restaurant_id
              AND c.deleted_at IS NULL
             WHERE i.tenant_id = ?
               AND i.restaurant_id = ?
               AND i.id = ?
               AND i.deleted_at IS NULL
               AND i.status = 'Active'
             LIMIT 1",
            [$tenantId, $restaurantId, $itemId]
        );
    }

    public function variants(int $tenantId, int $restaurantId, int $itemId): array
    {
        return $this->db->fetchAll(
            "SELECT id, item_id, name, price, is_available, sort_order
             FROM restaurant_menu_item_variants
             WHERE tenant_id = ?
               AND restaurant_id = ?
               AND item_id = ?
               AND deleted_at IS NULL
               AND status = 'Active'
             ORDER BY sort_order, name",
            [$tenantId, $restaurantId, $itemId]
        );
    }

    public function modifierGroups(int $tenantId, int $restaurantId, int $itemId): array
    {
        $groups = $this->db->fetchAll(
            "SELECT g.id, g.name, g.description, g.selection_type,
                    g.min_selections, g.max_selections, g.is_required,
                    g.sort_order
             FROM restaurant_menu_item_modifier_groups mig
             INNER JOIN restaurant_modifier_groups g
               ON g.id = mig.modifier_group_id
              AND g.tenant_id = mig.tenant_id
              AND g.restaurant_id = mig.restaurant_id
              AND g.deleted_at IS NULL
             INNER JOIN restaurant_menu_items i
               ON i.id = mig.item_id
              AND i.tenant_id = mig.tenant_id
              AND i.restaurant_id = mig.restaurant_id
              AND i.deleted_at IS NULL
              AND i.status = 'Active'
             WHERE mig.tenant_id = ?
               AND mig.restaurant_id = ?
               AND mig.item_id = ?
             ORDER BY mig.sort_order, g.sort_order, g.name",
            [$tenantId, $restaurantId, $itemId]
        );

        foreach ($groups as &$group) {
            $group['options'] = $this->db->fetchAll(
                "SELECT id, modifier_group_id, name, price_adjustment,
                        is_available, sort_order
                 FROM restaurant_modifier_options
                 WHERE tenant_id = ?
                   AND restaurant_id = ?
                   AND modifier_group_id = ?
                   AND deleted_at IS NULL
                   AND status = 'Active'
                 ORDER BY sort_order, name",
                [$tenantId, $restaurantId, (int) $group['id']]
            );
        }
        unset($group);

        return $groups;
    }

    public function memberOrders(int $tenantId, int $memberId): array
    {
        return $this->db->fetchAll(
            "SELECT o.id, o.restaurant_id, o.order_no, o.order_type, o.status,
                    o.payment_status, o.payment_method, o.customer_name,
                    o.delivery_address, o.subtotal, o.delivery_fee, o.discount,
                    o.total, o.coupon_code, o.coupon_discount, o.item_discount,
                    o.created_at, o.updated_at, o.accepted_at, o.preparing_at,
                    o.ready_at, o.assigned_at, o.out_for_delivery_at,
                    o.delivered_at, o.completed_at, o.cancelled_at, o.rejected_at,
                    o.rejection_reason, o.cancellation_reason,
                    b.name AS restaurant_name, b.phone AS restaurant_phone,
                    r.logo_path, r.cover_image_path, r.cuisine_type,
                    r.estimated_prep_minutes
             FROM restaurant_orders o
             INNER JOIN restaurant_profiles r
               ON r.id = o.restaurant_id
              AND r.tenant_id = o.tenant_id
             INNER JOIN businesses b
               ON b.id = r.business_id
              AND b.tenant_id = r.tenant_id
             WHERE o.tenant_id = ?
               AND o.member_id = ?
             ORDER BY o.created_at DESC, o.id DESC",
            [$tenantId, $memberId]
        );
    }

    public function orderForMember(int $tenantId, int $memberId, int $orderId): ?array
    {
        $order = $this->db->fetch(
            "SELECT o.*, b.name AS restaurant_name, b.phone AS restaurant_phone,
                    b.address AS restaurant_address, b.city AS restaurant_city,
                    b.district AS restaurant_district, b.state AS restaurant_state,
                    r.logo_path, r.cover_image_path, r.cuisine_type,
                    r.estimated_prep_minutes
             FROM restaurant_orders o
             INNER JOIN restaurant_profiles r
               ON r.id = o.restaurant_id
              AND r.tenant_id = o.tenant_id
             INNER JOIN businesses b
               ON b.id = r.business_id
              AND b.tenant_id = r.tenant_id
             WHERE o.tenant_id = ?
               AND o.member_id = ?
               AND o.id = ?
             LIMIT 1",
            [$tenantId, $memberId, $orderId]
        );

        if (!$order) {
            return null;
        }

        $order['items'] = $this->db->fetchAll(
            "SELECT *
             FROM restaurant_order_items
             WHERE tenant_id = ? AND order_id = ?
             ORDER BY id",
            [$tenantId, $orderId]
        );

        foreach ($order['items'] as &$item) {
            $item['modifiers'] = $this->db->fetchAll(
                "SELECT *
                 FROM restaurant_order_item_modifiers
                 WHERE tenant_id = ? AND order_item_id = ?
                 ORDER BY id",
                [$tenantId, (int) $item['id']]
            );
        }
        unset($item);

        $order['status_history'] = $this->db->fetchAll(
            "SELECT h.id, h.from_status, h.to_status, h.note, h.created_at,
                    COALESCE(u.name, 'Restaurant') AS changed_by_name
             FROM restaurant_order_status_history h
             LEFT JOIN users u
               ON u.id = h.changed_by
              AND u.tenant_id = h.tenant_id
             WHERE h.tenant_id = ? AND h.order_id = ?
             ORDER BY h.created_at ASC, h.id ASC",
            [$tenantId, $orderId]
        );

        return $order;
    }

    public function memberProfile(int $tenantId, int $memberId): ?array
    {
        return $this->db->fetch(
            "SELECT id, first_name, middle_name, last_name, phone, email,
                    address, city, state, postal_code
             FROM members
             WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $memberId]
        );
    }
}
