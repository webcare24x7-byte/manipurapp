<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Models;

use App\Core\Database;

final class RestaurantModifier
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
            "SELECT g.*, b.name AS restaurant_name,
                    (SELECT COUNT(*) FROM restaurant_modifier_options o WHERE o.modifier_group_id = g.id AND o.deleted_at IS NULL) AS option_count
             FROM restaurant_modifier_groups g
             INNER JOIN restaurant_profiles r ON r.id = g.restaurant_id AND r.tenant_id = g.tenant_id
             INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE g.tenant_id = ? AND g.deleted_at {$deleted}{$parentFilter}
             ORDER BY b.name, g.sort_order, g.name", [$tenantId]
        );
    }

    public function trash(int $tenantId): array { return $this->all($tenantId, true); }

    public function findDeleted(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT g.*, b.name AS restaurant_name FROM restaurant_modifier_groups g
             INNER JOIN restaurant_profiles r ON r.id = g.restaurant_id AND r.tenant_id = g.tenant_id
             INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE g.tenant_id = ? AND g.id = ? AND g.deleted_at IS NOT NULL LIMIT 1", [$tenantId, $id]
        );
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT g.*, b.name AS restaurant_name
             FROM restaurant_modifier_groups g
             INNER JOIN restaurant_profiles r
                ON r.id = g.restaurant_id AND r.tenant_id = g.tenant_id
             INNER JOIN businesses b
                ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE g.tenant_id = ?
               AND g.id = ?
               AND g.deleted_at IS NULL
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $id]
        );
    }

    public function options(int $tenantId, int $groupId): array
    {
        return $this->db->fetchAll(
            "SELECT *
             FROM restaurant_modifier_options
             WHERE tenant_id = ?
               AND modifier_group_id = ?
               AND deleted_at IS NULL
             ORDER BY sort_order, name",
            [$tenantId, $groupId]
        );
    }


    public function deletedOptions(int $tenantId, int $groupId): array
    {
        return $this->db->fetchAll("SELECT * FROM restaurant_modifier_options WHERE tenant_id = ? AND modifier_group_id = ? AND deleted_at IS NOT NULL ORDER BY sort_order, name", [$tenantId, $groupId]);
    }

    public function attachedItems(int $tenantId, int $groupId): array
    {
        return $this->db->fetchAll(
            "SELECT i.id, i.name, i.price, i.category_id, c.name AS category_name, b.name AS restaurant_name
             FROM restaurant_menu_item_modifier_groups mig
             INNER JOIN restaurant_menu_items i
                ON i.id = mig.item_id
               AND i.tenant_id = mig.tenant_id
               AND i.restaurant_id = mig.restaurant_id
             LEFT JOIN restaurant_menu_categories c
                ON c.id = i.category_id
               AND c.tenant_id = i.tenant_id
               AND c.restaurant_id = i.restaurant_id
               AND c.deleted_at IS NULL
             INNER JOIN restaurant_profiles r
                ON r.id = i.restaurant_id
               AND r.tenant_id = i.tenant_id
             INNER JOIN businesses b
                ON b.id = r.business_id
               AND b.tenant_id = r.tenant_id
             WHERE mig.tenant_id = ?
               AND mig.modifier_group_id = ?
               AND i.deleted_at IS NULL
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             ORDER BY c.sort_order, i.sort_order, i.name",
            [$tenantId, $groupId]
        );
    }

    public function availableItems(int $tenantId, int $groupId): array
    {
        $group = $this->find($tenantId, $groupId);
        if (!$group) {
            return [];
        }

        return $this->db->fetchAll(
            "SELECT i.id, i.name, i.price, i.category_id, c.name AS category_name
             FROM restaurant_menu_items i
             LEFT JOIN restaurant_menu_categories c
                ON c.id = i.category_id
               AND c.tenant_id = i.tenant_id
               AND c.restaurant_id = i.restaurant_id
               AND c.deleted_at IS NULL
             WHERE i.tenant_id = ?
               AND i.restaurant_id = ?
               AND i.deleted_at IS NULL
               AND NOT EXISTS (
                    SELECT 1
                    FROM restaurant_menu_item_modifier_groups mig
                    WHERE mig.tenant_id = i.tenant_id
                      AND mig.restaurant_id = i.restaurant_id
                      AND mig.item_id = i.id
                      AND mig.modifier_group_id = ?
               )
             ORDER BY c.sort_order, i.sort_order, i.name",
            [$tenantId, (int) $group['restaurant_id'], $groupId]
        );
    }

    public function itemBelongsToRestaurant(int $tenantId, int $itemId, int $restaurantId): bool
    {
        return $this->db->fetch(
            "SELECT i.id
             FROM restaurant_menu_items i
             INNER JOIN restaurant_profiles r
                ON r.id = i.restaurant_id AND r.tenant_id = i.tenant_id
             INNER JOIN businesses b
                ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE i.id = ?
               AND i.tenant_id = ?
               AND i.restaurant_id = ?
               AND i.deleted_at IS NULL
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             LIMIT 1",
            [$itemId, $tenantId, $restaurantId]
        ) !== null;
    }

    public function attachItem(int $tenantId, int $groupId, int $itemId, int $userId): void
    {
        $group = $this->find($tenantId, $groupId);
        if (!$group) {
            throw new \RuntimeException('Modifier group not found.');
        }

        if (!$this->itemBelongsToRestaurant($tenantId, $itemId, (int) $group['restaurant_id'])) {
            throw new \RuntimeException('Selected menu item does not belong to this restaurant.');
        }

        $this->db->execute(
            "INSERT INTO restaurant_menu_item_modifier_groups
                (tenant_id, restaurant_id, item_id, modifier_group_id, sort_order)
             VALUES (?, ?, ?, ?, 0)",
            [$tenantId, (int) $group['restaurant_id'], $itemId, $groupId]
        );
    }

    public function detachItem(int $tenantId, int $groupId, int $itemId): void
    {
        $this->db->execute(
            "DELETE FROM restaurant_menu_item_modifier_groups
             WHERE tenant_id = ?
               AND modifier_group_id = ?
               AND item_id = ?",
            [$tenantId, $groupId, $itemId]
        );
    }

    public function restaurants(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT r.id, b.name AS business_name
             FROM restaurant_profiles r
             INNER JOIN businesses b
                ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE r.tenant_id = ?
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
               AND r.status = 'Active'
             ORDER BY b.name",
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
             WHERE r.tenant_id = ?
               AND r.id = ?
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $restaurantId]
        ) !== null;
    }

    public function create(int $tenantId, array $data, int $userId): int
    {
        $this->db->execute(
            "INSERT INTO restaurant_modifier_groups
                (uuid, tenant_id, restaurant_id, name, description, selection_type,
                 min_selections, max_selections, is_required, sort_order, status, created_by, updated_by)
             VALUES
                (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $tenantId,
                $data['restaurant_id'],
                $data['name'],
                $data['description'],
                $data['selection_type'],
                $data['min_selections'],
                $data['max_selections'],
                $data['is_required'],
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
            "UPDATE restaurant_modifier_groups
             SET restaurant_id = ?, name = ?, description = ?, selection_type = ?,
                 min_selections = ?, max_selections = ?, is_required = ?, sort_order = ?,
                 status = ?, updated_by = ?
             WHERE tenant_id = ?
               AND id = ?
               AND deleted_at IS NULL",
            [
                $data['restaurant_id'],
                $data['name'],
                $data['description'],
                $data['selection_type'],
                $data['min_selections'],
                $data['max_selections'],
                $data['is_required'],
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
        $this->db->execute("UPDATE restaurant_modifier_groups SET deleted_at = NULL, updated_by = ? WHERE tenant_id = ? AND id = ? AND deleted_at IS NOT NULL", [$userId, $tenantId, $id]);
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        $this->db->execute(
            "UPDATE restaurant_modifier_groups
             SET deleted_at = NOW(), updated_by = ?
             WHERE tenant_id = ?
               AND id = ?
               AND deleted_at IS NULL",
            [$userId, $tenantId, $id]
        );
    }

    public function optionNameExists(int $tenantId, int $groupId, string $name): bool
    {
        return $this->db->fetch(
            "SELECT id
             FROM restaurant_modifier_options
             WHERE tenant_id = ?
               AND modifier_group_id = ?
               AND name = ?
               AND deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $groupId, $name]
        ) !== null;
    }

    public function addOption(int $tenantId, int $groupId, array $data, int $userId): int
    {
        $this->db->execute(
            "INSERT INTO restaurant_modifier_options
                (uuid, tenant_id, restaurant_id, modifier_group_id, name, price_adjustment,
                 is_available, sort_order, status, created_by, updated_by)
             VALUES
                (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $tenantId,
                $data['restaurant_id'],
                $groupId,
                $data['name'],
                $data['price_adjustment'],
                $data['is_available'],
                $data['sort_order'],
                $data['status'],
                $userId,
                $userId,
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    public function restoreOption(int $tenantId, int $groupId, int $optionId, int $userId): void
    {
        $this->db->execute("UPDATE restaurant_modifier_options SET deleted_at = NULL, updated_by = ? WHERE tenant_id = ? AND modifier_group_id = ? AND id = ? AND deleted_at IS NOT NULL", [$userId, $tenantId, $groupId, $optionId]);
    }

    public function deleteOption(int $tenantId, int $groupId, int $optionId, int $userId): void
    {
        $this->db->execute(
            "UPDATE restaurant_modifier_options
             SET deleted_at = NOW(), updated_by = ?
             WHERE tenant_id = ?
               AND modifier_group_id = ?
               AND id = ?
               AND deleted_at IS NULL",
            [$userId, $tenantId, $groupId, $optionId]
        );
    }
}
