<?php
declare(strict_types=1);

namespace App\Modules\FreshFood\Services;

use App\Modules\FreshFood\Models\FreshFoodInventory;
use RuntimeException;

final class FreshFoodInventoryService
{
    private FreshFoodInventory $m;

    public function __construct()
    {
        $this->m = new FreshFoodInventory();
    }

    public function all(int $tenantId): array
    {
        return $this->m->all($tenantId);
    }

    public function products(int $tenantId): array
    {
        return $this->m->products($tenantId);
    }

    public function findProduct(int $tenantId, int $productId): ?array
    {
        return $this->m->findProduct($tenantId, $productId);
    }

    public function movements(int $tenantId, int $productId): array
    {
        if (!$this->findProduct($tenantId, $productId)) {
            throw new RuntimeException('Product not found.');
        }

        return $this->m->movements($tenantId, $productId);
    }

    public function adjust(int $tenantId, array $input, int $userId): void
    {
        $productId = (int) ($input['product_id'] ?? 0);
        $product = $this->findProduct($tenantId, $productId);

        if (!$product) {
            throw new RuntimeException('Selected product is invalid.');
        }

        $type = strtoupper(trim((string) ($input['movement_type'] ?? '')));
        $allowed = ['OPENING', 'PURCHASE', 'SALE', 'RETURN', 'ADJUSTMENT', 'WASTE', 'DAMAGE'];

        if (!in_array($type, $allowed, true)) {
            throw new RuntimeException('Invalid inventory movement type.');
        }

        $quantity = (float) ($input['quantity_change'] ?? 0);

        if ($quantity == 0.0) {
            throw new RuntimeException('Quantity change cannot be zero.');
        }

        if (in_array($type, ['OPENING', 'PURCHASE', 'RETURN'], true) && $quantity < 0) {
            throw new RuntimeException($type . ' quantity must be positive.');
        }

        if (in_array($type, ['SALE', 'WASTE', 'DAMAGE'], true) && $quantity > 0) {
            $quantity *= -1;
        }

        $before = (float) ($product['current_quantity'] ?? 0);
        $after = $before + $quantity;

        if ($after < 0) {
            throw new RuntimeException('Insufficient stock. Stock cannot become negative.');
        }

        $inventory = $this->m->getOrCreateInventory(
            $tenantId,
            (int) $product['fresh_food_id'],
            $productId,
            $userId
        );

        $this->m->updateQuantity($tenantId, (int) $inventory['id'], $after, $userId);
        $this->m->addMovement(
            $tenantId,
            (int) $product['fresh_food_id'],
            $productId,
            (int) $inventory['id'],
            $type,
            $quantity,
            $before,
            $after,
            trim((string) ($input['reference'] ?? '')) ?: null,
            trim((string) ($input['notes'] ?? '')) ?: null,
            $userId
        );
    }
}
