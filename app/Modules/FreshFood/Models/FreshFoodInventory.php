<?php
declare(strict_types=1);

namespace App\Modules\FreshFood\Models;

use App\Core\Database;

final class FreshFoodInventory
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function all(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT i.*, p.name product_name, p.unit, p.price, p.is_available,
                    b.name business_name, c.name category_name
             FROM fresh_food_inventory i
             JOIN fresh_food_products p ON p.id = i.product_id
             JOIN fresh_food_profiles f ON f.id = i.fresh_food_id
             JOIN businesses b ON b.id = f.business_id
             LEFT JOIN fresh_food_categories c ON c.id = p.category_id
             WHERE i.tenant_id = ?
               AND p.deleted_at IS NULL
               AND f.deleted_at IS NULL
               AND b.deleted_at IS NULL
             ORDER BY b.name, p.name",
            [$tenantId]
        );
    }

    public function products(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT p.id, p.name, p.unit, p.price, p.fresh_food_id,
                    b.name business_name, COALESCE(i.current_quantity, 0) current_quantity
             FROM fresh_food_products p
             JOIN fresh_food_profiles f ON f.id = p.fresh_food_id
             JOIN businesses b ON b.id = f.business_id
             LEFT JOIN fresh_food_inventory i
               ON i.product_id = p.id AND i.tenant_id = p.tenant_id
             WHERE p.tenant_id = ?
               AND p.deleted_at IS NULL
               AND p.status = 'Active'
               AND f.deleted_at IS NULL
               AND b.deleted_at IS NULL
             ORDER BY b.name, p.name",
            [$tenantId]
        );
    }

    public function findProduct(int $tenantId, int $productId): ?array
    {
        return $this->db->fetch(
            "SELECT p.id, p.name, p.unit, p.fresh_food_id, b.name business_name,
                    COALESCE(i.id, 0) inventory_id,
                    COALESCE(i.current_quantity, 0) current_quantity
             FROM fresh_food_products p
             JOIN fresh_food_profiles f ON f.id = p.fresh_food_id
             JOIN businesses b ON b.id = f.business_id
             LEFT JOIN fresh_food_inventory i
               ON i.product_id = p.id AND i.tenant_id = p.tenant_id
             WHERE p.tenant_id = ? AND p.id = ?
               AND p.deleted_at IS NULL
               AND f.deleted_at IS NULL
               AND b.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $productId]
        );
    }

    public function getOrCreateInventory(int $tenantId, int $freshFoodId, int $productId, int $userId): array
    {
        $row = $this->db->fetch(
            "SELECT * FROM fresh_food_inventory WHERE tenant_id = ? AND product_id = ? LIMIT 1",
            [$tenantId, $productId]
        );

        if ($row !== null) {
            return $row;
        }

        $this->db->execute(
            "INSERT INTO fresh_food_inventory
                (uuid, tenant_id, fresh_food_id, product_id, current_quantity, created_by, updated_by)
             VALUES (UUID(), ?, ?, ?, 0.000, ?, ?)",
            [$tenantId, $freshFoodId, $productId, $userId, $userId]
        );

        $id = (int) $this->db->lastInsertId();

        return $this->db->fetch(
            "SELECT * FROM fresh_food_inventory WHERE id = ? AND tenant_id = ? LIMIT 1",
            [$id, $tenantId]
        ) ?? throw new \RuntimeException('Unable to create inventory record.');
    }

    public function updateQuantity(int $tenantId, int $inventoryId, float $quantity, int $userId): void
    {
        $this->db->execute(
            "UPDATE fresh_food_inventory
             SET current_quantity = ?, updated_by = ?
             WHERE tenant_id = ? AND id = ?",
            [$quantity, $userId, $tenantId, $inventoryId]
        );
    }

    public function addMovement(int $tenantId, int $freshFoodId, int $productId, int $inventoryId, string $type, float $change, float $before, float $after, ?string $reference, ?string $notes, int $userId): int
    {
        $this->db->execute(
            "INSERT INTO fresh_food_inventory_movements
                (uuid, tenant_id, fresh_food_id, product_id, inventory_id, movement_type,
                 quantity_change, quantity_before, quantity_after, reference, notes, created_by)
             VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$tenantId, $freshFoodId, $productId, $inventoryId, $type, $change, $before, $after, $reference, $notes, $userId]
        );

        return (int) $this->db->lastInsertId();
    }

    public function movements(int $tenantId, int $productId): array
    {
        return $this->db->fetchAll(
            "SELECT m.*, p.name product_name, p.unit, u.name created_by_name
             FROM fresh_food_inventory_movements m
             JOIN fresh_food_products p ON p.id = m.product_id
             LEFT JOIN users u ON u.id = m.created_by
             WHERE m.tenant_id = ? AND m.product_id = ?
             ORDER BY m.id DESC",
            [$tenantId, $productId]
        );
    }
}
