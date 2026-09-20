<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Models;

use App\Core\Database;

final class RestaurantItem
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
            "SELECT i.*, r.id AS restaurant_id, b.name AS restaurant_name, c.name AS category_name
             FROM restaurant_menu_items i
             INNER JOIN restaurant_profiles r ON r.id = i.restaurant_id AND r.tenant_id = i.tenant_id
             INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             LEFT JOIN restaurant_menu_categories c ON c.id = i.category_id AND c.tenant_id = i.tenant_id AND c.deleted_at IS NULL
             WHERE i.tenant_id = ? AND i.deleted_at {$deleted}{$parentFilter}
             ORDER BY b.name, c.sort_order, i.sort_order, i.name", [$tenantId]
        );
    }

    public function trash(int $tenantId): array { return $this->all($tenantId, true); }

    public function findDeleted(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT i.*, b.name AS restaurant_name, c.name AS category_name FROM restaurant_menu_items i
             INNER JOIN restaurant_profiles r ON r.id = i.restaurant_id AND r.tenant_id = i.tenant_id
             INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             LEFT JOIN restaurant_menu_categories c ON c.id = i.category_id AND c.tenant_id = i.tenant_id
             WHERE i.tenant_id = ? AND i.id = ? AND i.deleted_at IS NOT NULL LIMIT 1", [$tenantId, $id]
        );
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT i.*, c.name AS category_name, b.name AS restaurant_name
             FROM restaurant_menu_items i
             INNER JOIN restaurant_profiles r
                ON r.id = i.restaurant_id AND r.tenant_id = i.tenant_id
             INNER JOIN businesses b
                ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             LEFT JOIN restaurant_menu_categories c
                ON c.id = i.category_id
               AND c.restaurant_id = i.restaurant_id
               AND c.tenant_id = i.tenant_id
               AND c.deleted_at IS NULL
             WHERE i.tenant_id = ?
               AND i.id = ?
               AND i.deleted_at IS NULL
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $id]
        );
    }

    public function restaurants(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT r.id, r.business_id, b.name AS business_name
             FROM restaurant_profiles r
             INNER JOIN businesses b
                ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE r.tenant_id = ?
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
               AND r.status = 'Active'
             ORDER BY b.name ASC",
            [$tenantId]
        );
    }

    public function categories(int $tenantId, int $restaurantId): array
    {
        return $this->db->fetchAll(
            "SELECT id, name, description, sort_order, status
             FROM restaurant_menu_categories
             WHERE tenant_id = ?
               AND restaurant_id = ?
               AND status = 'Active'
               AND deleted_at IS NULL
             ORDER BY sort_order ASC, name ASC",
            [$tenantId, $restaurantId]
        );
    }

    public function categoriesAll(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT c.id, c.restaurant_id, c.name, c.description,
                    c.sort_order, c.status, b.name AS restaurant_name
             FROM restaurant_menu_categories c
             INNER JOIN restaurant_profiles r
                ON r.id = c.restaurant_id AND r.tenant_id = c.tenant_id
             INNER JOIN businesses b
                ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE c.tenant_id = ?
               AND c.status = 'Active'
               AND c.deleted_at IS NULL
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             ORDER BY b.name ASC, c.sort_order ASC, c.name ASC",
            [$tenantId]
        );
    }

    public function restaurantExists(int $tenantId, int $restaurantId): bool
    {
        return $this->db->fetch(
            "SELECT r.id
             FROM restaurant_profiles r
             INNER JOIN businesses b
                ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE r.id = ?
               AND r.tenant_id = ?
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             LIMIT 1",
            [$restaurantId, $tenantId]
        ) !== null;
    }

    public function categoryBelongsToRestaurant(int $tenantId, int $categoryId, int $restaurantId): bool
    {
        return $this->db->fetch(
            "SELECT c.id
             FROM restaurant_menu_categories c
             INNER JOIN restaurant_profiles r
                ON r.id = c.restaurant_id AND r.tenant_id = c.tenant_id
             INNER JOIN businesses b
                ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE c.id = ?
               AND c.tenant_id = ?
               AND c.restaurant_id = ?
               AND c.deleted_at IS NULL
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             LIMIT 1",
            [$categoryId, $tenantId, $restaurantId]
        ) !== null;
    }

    public function slugExists(int $tenantId, int $restaurantId, string $slug, ?int $ignoreId = null): bool
    {
        $sql = "SELECT id
                FROM restaurant_menu_items
                WHERE tenant_id = ?
                  AND restaurant_id = ?
                  AND slug = ?
                  AND deleted_at IS NULL";
        $params = [$tenantId, $restaurantId, $slug];

        if ($ignoreId !== null) {
            $sql .= " AND id <> ?";
            $params[] = $ignoreId;
        }

        return $this->db->fetch($sql . " LIMIT 1", $params) !== null;
    }

    public function create(int $tenantId, array $data, int $userId): int
    {
        $this->db->execute(
            "INSERT INTO restaurant_menu_items
                (uuid, tenant_id, restaurant_id, category_id, name, slug, description,
                 image_path, price, discount_type, discount_value, is_veg, is_available, sort_order, status, created_by, updated_by)
             VALUES
                (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $tenantId,
                $data['restaurant_id'],
                $data['category_id'],
                $data['name'],
                $data['slug'],
                $data['description'],
                $data['image_path'],
                $data['price'],
                $data['discount_type'],
                $data['discount_value'],
                $data['is_veg'],
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
            "UPDATE restaurant_menu_items
             SET restaurant_id = ?, category_id = ?, name = ?, slug = ?, description = ?,
                 image_path = ?, price = ?, discount_type = ?, discount_value = ?, is_veg = ?, is_available = ?, sort_order = ?,
                 status = ?, updated_by = ?
             WHERE tenant_id = ?
               AND id = ?
               AND deleted_at IS NULL",
            [
                $data['restaurant_id'],
                $data['category_id'],
                $data['name'],
                $data['slug'],
                $data['description'],
                $data['image_path'],
                $data['price'],
                $data['discount_type'],
                $data['discount_value'],
                $data['is_veg'],
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
        $this->db->execute("UPDATE restaurant_menu_items SET deleted_at = NULL, updated_by = ? WHERE tenant_id = ? AND id = ? AND deleted_at IS NOT NULL", [$userId, $tenantId, $id]);
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        $this->db->execute(
            "UPDATE restaurant_menu_items
             SET deleted_at = NOW(), updated_by = ?
             WHERE tenant_id = ?
               AND id = ?
               AND deleted_at IS NULL",
            [$userId, $tenantId, $id]
        );
    }
}
