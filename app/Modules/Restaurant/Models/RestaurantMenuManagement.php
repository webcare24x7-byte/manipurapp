<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Models;

use App\Core\Database;

/**
 * Read/write access for the unified restaurant menu-management screen.
 *
 * Menu relationships are intentionally read from their existing tables:
 * categories -> items -> variants and item <-> modifier groups.
 * Soft deletion of an item or variant never deletes its modifier group.
 */
final class RestaurantMenuManagement
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function restaurants(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT r.id, b.name AS business_name, r.status
             FROM restaurant_profiles r
             INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE r.tenant_id = ?
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             ORDER BY b.name ASC",
            [$tenantId]
        );
    }

    public function restaurant(int $tenantId, int $restaurantId): ?array
    {
        return $this->db->fetch(
            "SELECT r.id, r.business_id, r.status, b.name AS business_name
             FROM restaurant_profiles r
             INNER JOIN businesses b ON b.id = r.business_id AND b.tenant_id = r.tenant_id
             WHERE r.tenant_id = ?
               AND r.id = ?
               AND r.deleted_at IS NULL
               AND b.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $restaurantId]
        );
    }

    public function categories(int $tenantId, int $restaurantId): array
    {
        return $this->db->fetchAll(
            "SELECT c.id, c.name, c.description, c.sort_order, c.status,
                    (SELECT COUNT(*)
                       FROM restaurant_menu_items i
                      WHERE i.tenant_id = c.tenant_id
                        AND i.restaurant_id = c.restaurant_id
                        AND i.category_id = c.id
                        AND i.deleted_at IS NULL) AS item_count
             FROM restaurant_menu_categories c
             WHERE c.tenant_id = ?
               AND c.restaurant_id = ?
               AND c.deleted_at IS NULL
             ORDER BY c.sort_order ASC, c.name ASC",
            [$tenantId, $restaurantId]
        );
    }

    public function items(int $tenantId, int $restaurantId): array
    {
        return $this->db->fetchAll(
            "SELECT i.id, i.category_id, i.name, i.slug, i.description, i.image_path,
                    i.price, i.discount_type, i.discount_value, i.is_veg, i.is_available, i.sort_order, i.status,
                    c.name AS category_name
             FROM restaurant_menu_items i
             LEFT JOIN restaurant_menu_categories c
               ON c.id = i.category_id
              AND c.tenant_id = i.tenant_id
              AND c.restaurant_id = i.restaurant_id
              AND c.deleted_at IS NULL
             WHERE i.tenant_id = ?
               AND i.restaurant_id = ?
               AND i.deleted_at IS NULL
             ORDER BY COALESCE(c.sort_order, 999999), COALESCE(c.name, 'Uncategorized'),
                      i.sort_order ASC, i.name ASC",
            [$tenantId, $restaurantId]
        );
    }

    public function variants(int $tenantId, int $restaurantId): array
    {
        return $this->db->fetchAll(
            "SELECT v.id, v.item_id, v.name, v.price, v.is_available, v.sort_order, v.status
             FROM restaurant_menu_item_variants v
             WHERE v.tenant_id = ?
               AND v.restaurant_id = ?
               AND v.deleted_at IS NULL
             ORDER BY v.item_id, v.sort_order ASC, v.name ASC",
            [$tenantId, $restaurantId]
        );
    }

    public function itemModifierGroups(int $tenantId, int $restaurantId): array
    {
        return $this->db->fetchAll(
            "SELECT mig.item_id, g.id AS modifier_group_id, g.name, g.selection_type,
                    g.min_selections, g.max_selections, g.is_required, g.sort_order
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
             WHERE mig.tenant_id = ?
               AND mig.restaurant_id = ?
             ORDER BY mig.item_id, g.sort_order ASC, g.name ASC",
            [$tenantId, $restaurantId]
        );
    }

    public function modifierGroups(int $tenantId, int $restaurantId): array
    {
        return $this->db->fetchAll(
            "SELECT g.id, g.name, g.description, g.selection_type, g.min_selections,
                    g.max_selections, g.is_required, g.sort_order, g.status,
                    (SELECT COUNT(*)
                       FROM restaurant_menu_item_modifier_groups mig
                      INNER JOIN restaurant_menu_items i
                        ON i.id = mig.item_id
                       AND i.tenant_id = mig.tenant_id
                       AND i.restaurant_id = mig.restaurant_id
                       AND i.deleted_at IS NULL
                      WHERE mig.tenant_id = g.tenant_id
                        AND mig.restaurant_id = g.restaurant_id
                        AND mig.modifier_group_id = g.id) AS item_count,
                    (SELECT COUNT(*)
                       FROM restaurant_modifier_options o
                      WHERE o.tenant_id = g.tenant_id
                        AND o.restaurant_id = g.restaurant_id
                        AND o.modifier_group_id = g.id
                        AND o.deleted_at IS NULL) AS option_count
             FROM restaurant_modifier_groups g
             WHERE g.tenant_id = ?
               AND g.restaurant_id = ?
               AND g.deleted_at IS NULL
             ORDER BY g.sort_order ASC, g.name ASC",
            [$tenantId, $restaurantId]
        );
    }

    public function summary(int $tenantId, int $restaurantId): array
    {
        $row = $this->db->fetch(
            "SELECT
                (SELECT COUNT(*) FROM restaurant_menu_categories c
                  WHERE c.tenant_id = ? AND c.restaurant_id = ? AND c.deleted_at IS NULL) AS categories,
                (SELECT COUNT(*) FROM restaurant_menu_items i
                  WHERE i.tenant_id = ? AND i.restaurant_id = ? AND i.deleted_at IS NULL) AS items,
                (SELECT COUNT(*) FROM restaurant_menu_items i
                  WHERE i.tenant_id = ? AND i.restaurant_id = ? AND i.deleted_at IS NULL AND i.is_available = 1) AS available_items,
                (SELECT COUNT(*) FROM restaurant_menu_items i
                  WHERE i.tenant_id = ? AND i.restaurant_id = ? AND i.deleted_at IS NULL AND i.is_available = 0) AS sold_out_items,
                (SELECT COUNT(*) FROM restaurant_menu_item_variants v
                  WHERE v.tenant_id = ? AND v.restaurant_id = ? AND v.deleted_at IS NULL) AS variants,
                (SELECT COUNT(*) FROM restaurant_modifier_groups g
                  WHERE g.tenant_id = ? AND g.restaurant_id = ? AND g.deleted_at IS NULL) AS modifier_groups
             FROM restaurant_profiles r
             WHERE r.tenant_id = ? AND r.id = ? AND r.deleted_at IS NULL
             LIMIT 1",
            [
                $tenantId, $restaurantId,
                $tenantId, $restaurantId,
                $tenantId, $restaurantId,
                $tenantId, $restaurantId,
                $tenantId, $restaurantId,
                $tenantId, $restaurantId,
                $tenantId, $restaurantId,
            ]
        );

        return [
            'categories' => (int) ($row['categories'] ?? 0),
            'items' => (int) ($row['items'] ?? 0),
            'available_items' => (int) ($row['available_items'] ?? 0),
            'sold_out_items' => (int) ($row['sold_out_items'] ?? 0),
            'variants' => (int) ($row['variants'] ?? 0),
            'modifier_groups' => (int) ($row['modifier_groups'] ?? 0),
        ];
    }

    public function setItemAvailability(int $tenantId, int $restaurantId, int $itemId, bool $available, int $userId): void
    {
        $this->db->execute(
            "UPDATE restaurant_menu_items
                SET is_available = ?, updated_by = ?
              WHERE tenant_id = ?
                AND restaurant_id = ?
                AND id = ?
                AND deleted_at IS NULL",
            [$available ? 1 : 0, $userId, $tenantId, $restaurantId, $itemId]
        );
    }

    public function setVariantAvailability(int $tenantId, int $restaurantId, int $variantId, bool $available, int $userId): void
    {
        $this->db->execute(
            "UPDATE restaurant_menu_item_variants
                SET is_available = ?, updated_by = ?
              WHERE tenant_id = ?
                AND restaurant_id = ?
                AND id = ?
                AND deleted_at IS NULL",
            [$available ? 1 : 0, $userId, $tenantId, $restaurantId, $variantId]
        );
    }
}
