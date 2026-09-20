<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Models;

use App\Core\Database;

final class RestaurantVariant
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function all(int $tenantId, bool $withDeleted = false): array
    {
        $deleted = $withDeleted ? 'IS NOT NULL' : 'IS NULL';
        $parentFilter = $withDeleted ? '' : ' AND i.deleted_at IS NULL AND r.deleted_at IS NULL AND b.deleted_at IS NULL';
        return $this->db->fetchAll(
            "SELECT v.*, i.name AS item_name, b.name AS restaurant_name
             FROM restaurant_menu_item_variants v
             INNER JOIN restaurant_menu_items i ON i.id = v.item_id AND i.tenant_id = v.tenant_id
             INNER JOIN restaurant_profiles r ON r.id = v.restaurant_id AND r.tenant_id = v.tenant_id
             INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE v.tenant_id = ? AND v.deleted_at {$deleted}{$parentFilter}
             ORDER BY b.name, i.name, v.sort_order, v.name", [$tenantId]
        );
    }

    public function trash(int $tenantId): array { return $this->all($tenantId, true); }

    public function findDeleted(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT v.*, i.name AS item_name, b.name AS restaurant_name FROM restaurant_menu_item_variants v
             INNER JOIN restaurant_menu_items i ON i.id = v.item_id AND i.tenant_id = v.tenant_id
             INNER JOIN restaurant_profiles r ON r.id = v.restaurant_id AND r.tenant_id = v.tenant_id
             INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE v.tenant_id = ? AND v.id = ? AND v.deleted_at IS NOT NULL LIMIT 1", [$tenantId, $id]
        );
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT v.*, i.name AS item_name, b.name AS restaurant_name
             FROM restaurant_menu_item_variants v
             INNER JOIN restaurant_menu_items i
                ON i.id = v.item_id AND i.tenant_id = v.tenant_id
             INNER JOIN restaurant_profiles r
                ON r.id = v.restaurant_id AND r.tenant_id = v.tenant_id
             INNER JOIN businesses b
                ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE v.tenant_id = ?
               AND v.id = ?
               AND v.deleted_at IS NULL
               AND i.deleted_at IS NULL
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $id]
        );
    }

    public function item(int $tenantId, int $itemId): ?array
    {
        return $this->db->fetch(
            "SELECT i.id, i.restaurant_id, i.name AS item_name
             FROM restaurant_menu_items i
             INNER JOIN restaurant_profiles r
                ON r.id = i.restaurant_id AND r.tenant_id = i.tenant_id
             WHERE i.tenant_id = ?
               AND i.id = ?
               AND i.deleted_at IS NULL
               AND r.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $itemId]
        );
    }

    public function items(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT i.id, i.restaurant_id, i.name AS item_name, b.name AS restaurant_name
             FROM restaurant_menu_items i
             INNER JOIN restaurant_profiles r
                ON r.id = i.restaurant_id AND r.tenant_id = i.tenant_id
             INNER JOIN businesses b
                ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE i.tenant_id = ?
               AND i.deleted_at IS NULL
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             ORDER BY b.name, i.name",
            [$tenantId]
        );
    }

    public function duplicate(int $tenantId, int $itemId, string $name, ?int $ignore = null): bool
    {
        $sql = "SELECT id
                FROM restaurant_menu_item_variants
                WHERE tenant_id = ?
                  AND item_id = ?
                  AND name = ?
                  AND deleted_at IS NULL";
        $params = [$tenantId, $itemId, $name];

        if ($ignore !== null) {
            $sql .= " AND id <> ?";
            $params[] = $ignore;
        }

        return $this->db->fetch($sql . " LIMIT 1", $params) !== null;
    }

    public function create(int $tenantId, array $data, int $userId): int
    {
        $this->db->execute(
            "INSERT INTO restaurant_menu_item_variants
                (uuid, tenant_id, restaurant_id, item_id, name, price, is_available, sort_order, status, created_by, updated_by)
             VALUES
                (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $tenantId,
                $data['restaurant_id'],
                $data['item_id'],
                $data['name'],
                $data['price'],
                $data['is_available'],
                $data['sort_order'],
                $data['status'],
                $userId,
                $userId,
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    public function update(int $tenantId, int $id, array $data, int $userId): void
    {
        $this->db->execute(
            "UPDATE restaurant_menu_item_variants
             SET restaurant_id = ?, item_id = ?, name = ?, price = ?, is_available = ?,
                 sort_order = ?, status = ?, updated_by = ?
             WHERE tenant_id = ?
               AND id = ?
               AND deleted_at IS NULL",
            [
                $data['restaurant_id'],
                $data['item_id'],
                $data['name'],
                $data['price'],
                $data['is_available'],
                $data['sort_order'],
                $data['status'],
                $userId,
                $tenantId,
                $id,
            ]
        );
    }

    public function restore(int $tenantId, int $id, int $userId): void
    {
        $this->db->execute("UPDATE restaurant_menu_item_variants SET deleted_at = NULL, updated_by = ? WHERE tenant_id = ? AND id = ? AND deleted_at IS NOT NULL", [$userId, $tenantId, $id]);
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        $this->db->execute(
            "UPDATE restaurant_menu_item_variants
             SET deleted_at = NOW(), updated_by = ?
             WHERE tenant_id = ?
               AND id = ?
               AND deleted_at IS NULL",
            [$userId, $tenantId, $id]
        );
    }
}
