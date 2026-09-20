<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Models;

use App\Core\Database;

final class Restaurant
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function all(int $tenantId, bool $withDeleted = false): array
    {
        $where = $withDeleted
            ? 'r.deleted_at IS NOT NULL AND b.deleted_at IS NOT NULL'
            : 'r.deleted_at IS NULL AND b.deleted_at IS NULL';

        return $this->db->fetchAll(
            "SELECT r.*, b.name AS business_name, b.slug AS business_slug, b.business_type,
                    b.phone, b.email, b.address, b.city, b.district, b.state, b.postal_code,
                    (SELECT COUNT(*) FROM restaurant_menu_items i WHERE i.restaurant_id = r.id AND i.deleted_at IS NULL) AS item_count,
                    (SELECT COUNT(*) FROM restaurant_menu_categories c WHERE c.restaurant_id = r.id AND c.deleted_at IS NULL) AS category_count
             FROM restaurant_profiles r
             INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE r.tenant_id = ? AND {$where}
             ORDER BY r.id DESC",
            [$tenantId]
        );
    }

    public function trash(int $tenantId): array
    {
        return $this->all($tenantId, true);
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT r.*, b.name AS business_name, b.slug AS business_slug, b.business_type,
                    b.phone, b.email, b.address, b.city, b.district, b.state, b.postal_code
             FROM restaurant_profiles r
             INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE r.tenant_id = ? AND r.id = ? AND r.deleted_at IS NULL AND b.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $id]
        );
    }

    public function findDeleted(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT r.*, b.name AS business_name, b.slug AS business_slug, b.business_type,
                    b.phone, b.email, b.address, b.city, b.district, b.state, b.postal_code
             FROM restaurant_profiles r
             INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE r.tenant_id = ? AND r.id = ? AND r.deleted_at IS NOT NULL AND b.deleted_at IS NOT NULL
             LIMIT 1",
            [$tenantId, $id]
        );
    }

    public function create(int $tenantId, array $data, int $userId): int
    {
        $this->db->execute(
            "INSERT INTO businesses
                (uuid, tenant_id, name, slug, business_type, phone, email, address, city, district, state, postal_code, status, created_by, updated_by)
             VALUES (UUID(), ?, ?, ?, 'restaurant', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$tenantId, $data['business_name'], $data['business_slug'], $data['phone'], $data['email'], $data['address'], $data['city'], $data['district'], $data['state'], $data['postal_code'], $data['status'], $userId, $userId]
        );

        $businessId = (int) $this->db->lastInsertId();

        $this->db->execute(
            "INSERT INTO restaurant_profiles
                (uuid, tenant_id, business_id, description, logo_path, cover_image_path, cuisine_type,
                 minimum_order_amount, delivery_available, pickup_available, delivery_fee,
                 free_delivery_above, estimated_prep_minutes, accepting_orders, latitude, longitude,
                 status, created_by, updated_by)
             VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$tenantId, $businessId, $data['description'], $data['logo_path'], $data['cover_image_path'], $data['cuisine_type'], $data['minimum_order_amount'], $data['delivery_available'], $data['pickup_available'], $data['delivery_fee'], $data['free_delivery_above'], $data['estimated_prep_minutes'], $data['accepting_orders'], $data['latitude'], $data['longitude'], $data['status'], $userId, $userId]
        );

        return (int) $this->db->lastInsertId();
    }

    public function update(int $tenantId, int $id, array $data, int $userId): void
    {
        $record = $this->find($tenantId, $id);
        if (!$record) return;

        $this->db->execute(
            "UPDATE businesses SET name = ?, slug = ?, phone = ?, email = ?, address = ?, city = ?, district = ?,
                 state = ?, postal_code = ?, status = ?, updated_by = ?
             WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL",
            [$data['business_name'], $data['business_slug'], $data['phone'], $data['email'], $data['address'], $data['city'], $data['district'], $data['state'], $data['postal_code'], $data['status'], $userId, $tenantId, (int) $record['business_id']]
        );

        $this->db->execute(
            "UPDATE restaurant_profiles SET description = ?, logo_path = ?, cover_image_path = ?, cuisine_type = ?,
                 minimum_order_amount = ?, delivery_available = ?, pickup_available = ?, delivery_fee = ?,
                 free_delivery_above = ?, estimated_prep_minutes = ?, accepting_orders = ?, latitude = ?, longitude = ?, status = ?, updated_by = ?
             WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL",
            [$data['description'], $data['logo_path'], $data['cover_image_path'], $data['cuisine_type'], $data['minimum_order_amount'], $data['delivery_available'], $data['pickup_available'], $data['delivery_fee'], $data['free_delivery_above'], $data['estimated_prep_minutes'], $data['accepting_orders'], $data['latitude'], $data['longitude'], $data['status'], $userId, $tenantId, $id]
        );
    }

    public function softDelete(int $tenantId, int $id, int $userId): void
    {
        $record = $this->find($tenantId, $id);
        if (!$record) return;

        $this->db->execute("UPDATE restaurant_profiles SET deleted_at = NOW(), updated_by = ? WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL", [$userId, $tenantId, $id]);
        $this->db->execute("UPDATE businesses SET deleted_at = NOW(), updated_by = ? WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL", [$userId, $tenantId, (int) $record['business_id']]);
    }

    public function restore(int $tenantId, int $id, int $userId): void
    {
        $record = $this->findDeleted($tenantId, $id);
        if (!$record) return;

        $this->db->execute("UPDATE businesses SET deleted_at = NULL, updated_by = ? WHERE tenant_id = ? AND id = ?", [$userId, $tenantId, (int) $record['business_id']]);
        $this->db->execute("UPDATE restaurant_profiles SET deleted_at = NULL, updated_by = ? WHERE tenant_id = ? AND id = ?", [$userId, $tenantId, $id]);
    }

    public function businessNameExists(int $tenantId, string $name, ?int $ignoreId = null): bool
    {
        $sql = "SELECT b.id FROM businesses b
                LEFT JOIN restaurant_profiles r ON r.business_id = b.id AND r.deleted_at IS NULL
                WHERE b.tenant_id = ? AND b.name = ? AND b.deleted_at IS NULL
                  AND LOWER(b.business_type) = 'restaurant' AND r.id IS NOT NULL";
        $params = [$tenantId, $name];
        if ($ignoreId !== null) { $sql .= " AND r.id <> ?"; $params[] = $ignoreId; }
        return $this->db->fetch($sql . " LIMIT 1", $params) !== null;
    }

    public function slugExists(int $tenantId, string $slug, ?int $ignoreId = null): bool
    {
        $sql = "SELECT b.id FROM businesses b
                LEFT JOIN restaurant_profiles r ON r.business_id = b.id AND r.deleted_at IS NULL
                WHERE b.tenant_id = ? AND b.slug = ? AND b.deleted_at IS NULL
                  AND LOWER(b.business_type) = 'restaurant' AND r.id IS NOT NULL";
        $params = [$tenantId, $slug];
        if ($ignoreId !== null) { $sql .= " AND r.id <> ?"; $params[] = $ignoreId; }
        return $this->db->fetch($sql . " LIMIT 1", $params) !== null;
    }

    public function hours(int $tenantId, int $restaurantId): array
    {
        return $this->db->fetchAll("SELECT * FROM restaurant_hours WHERE tenant_id = ? AND restaurant_id = ? ORDER BY day_of_week ASC", [$tenantId, $restaurantId]);
    }

    public function menuSummary(int $tenantId, int $restaurantId): array
    {
        $row = $this->db->fetch(
            "SELECT
                (SELECT COUNT(*) FROM restaurant_menu_categories c
                 WHERE c.tenant_id = r.tenant_id AND c.restaurant_id = r.id AND c.deleted_at IS NULL) AS categories,
                (SELECT COUNT(*) FROM restaurant_menu_items i
                 WHERE i.tenant_id = r.tenant_id AND i.restaurant_id = r.id AND i.deleted_at IS NULL) AS items,
                (SELECT COUNT(*) FROM restaurant_menu_item_variants v
                 WHERE v.tenant_id = r.tenant_id AND v.restaurant_id = r.id AND v.deleted_at IS NULL) AS variants,
                (SELECT COUNT(*) FROM restaurant_modifier_groups g
                 WHERE g.tenant_id = r.tenant_id AND g.restaurant_id = r.id AND g.deleted_at IS NULL) AS modifier_groups,
                (SELECT COUNT(*) FROM restaurant_modifier_options o
                 WHERE o.tenant_id = r.tenant_id
                   AND o.restaurant_id = r.id
                   AND o.deleted_at IS NULL) AS modifier_options
             FROM restaurant_profiles r
             INNER JOIN businesses b
                ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE r.tenant_id = ? AND r.id = ?
               AND r.deleted_at IS NULL AND b.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $restaurantId]
        );

        return [
            'categories' => (int) ($row['categories'] ?? 0),
            'items' => (int) ($row['items'] ?? 0),
            'variants' => (int) ($row['variants'] ?? 0),
            'modifier_groups' => (int) ($row['modifier_groups'] ?? 0),
            'modifier_options' => (int) ($row['modifier_options'] ?? 0),
        ];
    }

    public function replaceHours(int $tenantId, int $restaurantId, array $hours): void
    {
        $this->db->execute("DELETE FROM restaurant_hours WHERE tenant_id = ? AND restaurant_id = ?", [$tenantId, $restaurantId]);
        foreach ($hours as $day => $row) {
            $day = (int) $day;
            if ($day < 0 || $day > 6) continue;
            $this->db->execute(
                "INSERT INTO restaurant_hours (uuid, tenant_id, restaurant_id, day_of_week, opens_at, closes_at, is_closed, created_by, updated_by)
                 VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, ?)",
                [$tenantId, $restaurantId, $day, $row['opens_at'] ?? null, $row['closes_at'] ?? null, !empty($row['is_closed']) ? 1 : 0, (int) ($row['created_by'] ?? 0), (int) ($row['updated_by'] ?? 0)]
            );
        }
    }

    private function activeCount(string $table, int $tenantId): int
    {
        return (int) ($this->db->fetch("SELECT COUNT(*) total FROM {$table} WHERE tenant_id = ? AND deleted_at IS NULL", [$tenantId])['total'] ?? 0);
    }

    public function dashboard(int $tenantId): array
    {
        return [
            'restaurants' => $this->activeCount('restaurant_profiles', $tenantId),
            'categories' => $this->activeCount('restaurant_menu_categories', $tenantId),
            'items' => $this->activeCount('restaurant_menu_items', $tenantId),
            'variants' => $this->activeCount('restaurant_menu_item_variants', $tenantId),
            'modifier_groups' => $this->activeCount('restaurant_modifier_groups', $tenantId),
        ];
    }
}
