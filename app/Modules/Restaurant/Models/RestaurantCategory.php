<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Models;

use App\Core\Database;

final class RestaurantCategory
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function all(int $tenantId, bool $withDeleted = false): array
    {
        $deleted = $withDeleted ? 'IS NOT NULL' : 'IS NULL';
        $parentFilter = $withDeleted ? '' : ' AND r.deleted_at IS NULL AND b.deleted_at IS NULL';
        return $this->db->fetchAll(
            "SELECT c.*, r.business_id, b.name AS restaurant_name,
                    (SELECT COUNT(*) FROM restaurant_menu_items i WHERE i.category_id = c.id AND i.deleted_at IS NULL) AS item_count
             FROM restaurant_menu_categories c
             INNER JOIN restaurant_profiles r ON r.id = c.restaurant_id AND r.tenant_id = c.tenant_id
             INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE c.tenant_id = ? AND c.deleted_at {$deleted}{$parentFilter}
             ORDER BY b.name ASC, c.sort_order ASC, c.name ASC", [$tenantId]
        );
    }

    public function trash(int $tenantId): array { return $this->all($tenantId, true); }

    public function findDeleted(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT c.*, r.business_id, b.name AS restaurant_name FROM restaurant_menu_categories c
             INNER JOIN restaurant_profiles r ON r.id = c.restaurant_id AND r.tenant_id = c.tenant_id
             INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE c.tenant_id = ? AND c.id = ? AND c.deleted_at IS NOT NULL LIMIT 1", [$tenantId, $id]
        );
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT
                c.*,
                r.business_id,
                b.name AS restaurant_name
             FROM restaurant_menu_categories c
             INNER JOIN restaurant_profiles r
                ON r.id = c.restaurant_id
               AND r.tenant_id = c.tenant_id
             INNER JOIN businesses b
                ON b.id = r.business_id
               AND b.tenant_id = r.tenant_id
             WHERE c.tenant_id = ?
               AND c.id = ?
               AND c.deleted_at IS NULL
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $id]
        );
    }

    public function restaurantExists(int $tenantId, int $restaurantId): bool
    {
        return $this->db->fetch(
            "SELECT r.id
             FROM restaurant_profiles r
             INNER JOIN businesses b
                ON b.id = r.business_id
               AND b.tenant_id = r.tenant_id
             WHERE r.tenant_id = ?
               AND r.id = ?
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $restaurantId]
        ) !== null;
    }

    public function restaurants(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT r.id, b.name AS business_name
             FROM restaurant_profiles r
             INNER JOIN businesses b
                ON b.id = r.business_id
               AND b.tenant_id = r.tenant_id
             WHERE r.tenant_id = ?
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
               AND r.status = 'Active'
             ORDER BY b.name ASC",
            [$tenantId]
        );
    }

    public function create(int $tenantId, array $data, int $userId): int
    {
        $this->db->execute(
            "INSERT INTO restaurant_menu_categories
                (uuid, tenant_id, restaurant_id, name, description, sort_order, status, created_by, updated_by)
             VALUES
                (UUID(), ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $tenantId,
                $data['restaurant_id'],
                $data['name'],
                $data['description'],
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
            "UPDATE restaurant_menu_categories
             SET restaurant_id = ?,
                 name = ?,
                 description = ?,
                 sort_order = ?,
                 status = ?,
                 updated_by = ?
             WHERE tenant_id = ?
               AND id = ?
               AND deleted_at IS NULL",
            [
                $data['restaurant_id'],
                $data['name'],
                $data['description'],
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
        $this->db->execute("UPDATE restaurant_menu_categories SET deleted_at = NULL, updated_by = ? WHERE tenant_id = ? AND id = ? AND deleted_at IS NOT NULL", [$userId, $tenantId, $id]);
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        $this->db->execute(
            "UPDATE restaurant_menu_categories
             SET deleted_at = NOW(), updated_by = ?
             WHERE tenant_id = ?
               AND id = ?
               AND deleted_at IS NULL",
            [$userId, $tenantId, $id]
        );
    }
}
