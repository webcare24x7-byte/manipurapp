<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Services;

use App\Modules\Restaurant\Models\RestaurantVariant;
use RuntimeException;

final class RestaurantVariantService
{
    private RestaurantVariant $m;

    public function __construct() { $this->m = new RestaurantVariant(); }
    public function all(int $tenantId, bool $withDeleted = false): array { return $this->m->all($tenantId, $withDeleted); }
    public function trash(int $tenantId): array { return $this->m->trash($tenantId); }
    public function find(int $tenantId, int $id): ?array { return $this->m->find($tenantId, $id); }
    public function items(int $tenantId): array { return $this->m->items($tenantId); }

    public function findDeleted(int $tenantId, int $id): ?array { return $this->m->findDeleted($tenantId, $id); }

    public function restore(int $tenantId, int $id, int $userId): void
    {
        if (!$this->findDeleted($tenantId, $id)) throw new RuntimeException('Deleted variant not found.');
        $this->m->restore($tenantId, $id, $userId);
    }

    public function create(int $tenantId, array $input, int $userId): int
    {
        $data = $this->validate($input);
        $item = $this->m->item($tenantId, $data['item_id']);
        if (!$item) {
            throw new RuntimeException('Selected menu item is invalid.');
        }

        $data['restaurant_id'] = (int) $item['restaurant_id'];
        if ($this->m->duplicate($tenantId, $data['item_id'], $data['name'])) {
            throw new RuntimeException('This variant already exists for the selected item.');
        }

        return $this->m->create($tenantId, $data, $userId);
    }

    public function update(int $tenantId, int $id, array $input, int $userId): void
    {
        if (!$this->find($tenantId, $id)) {
            throw new RuntimeException('Variant not found.');
        }

        $data = $this->validate($input);
        $item = $this->m->item($tenantId, $data['item_id']);
        if (!$item) {
            throw new RuntimeException('Selected menu item is invalid.');
        }

        $data['restaurant_id'] = (int) $item['restaurant_id'];
        if ($this->m->duplicate($tenantId, $data['item_id'], $data['name'], $id)) {
            throw new RuntimeException('This variant already exists for the selected item.');
        }

        $this->m->update($tenantId, $id, $data, $userId);
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        if (!$this->find($tenantId, $id)) {
            throw new RuntimeException('Variant not found.');
        }
        $this->m->delete($tenantId, $id, $userId);
    }

    private function validate(array $input): array
    {
        $name = trim((string) ($input['name'] ?? ''));
        if ($name === '') throw new RuntimeException('Variant name is required.');

        $price = (float) ($input['price'] ?? 0);
        if ($price < 0) throw new RuntimeException('Price cannot be negative.');

        $status = (string) ($input['status'] ?? 'Active');
        if (!in_array($status, ['Active', 'Inactive'], true)) {
            throw new RuntimeException('Invalid variant status.');
        }

        return [
            'item_id' => (int) ($input['item_id'] ?? 0),
            'name' => $name,
            'price' => number_format($price, 2, '.', ''),
            'is_available' => !empty($input['is_available']) ? 1 : 0,
            'sort_order' => (int) ($input['sort_order'] ?? 0),
            'status' => $status,
        ];
    }
}
