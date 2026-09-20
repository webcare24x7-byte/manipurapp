<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Services;

use App\Modules\Restaurant\Models\RestaurantCategory;
use RuntimeException;

final class RestaurantCategoryService
{
    private RestaurantCategory $m;

    public function __construct()
    {
        $this->m = new RestaurantCategory();
    }

    public function all(int $tenantId, bool $withDeleted = false): array { return $this->m->all($tenantId, $withDeleted); }
    public function trash(int $tenantId): array { return $this->m->trash($tenantId); }
    public function restaurants(int $tenantId): array { return $this->m->restaurants($tenantId); }
    public function find(int $tenantId, int $id): ?array { return $this->m->find($tenantId, $id); }

    public function findDeleted(int $tenantId, int $id): ?array { return $this->m->findDeleted($tenantId, $id); }

    public function restore(int $tenantId, int $id, int $userId): void
    {
        if (!$this->findDeleted($tenantId, $id)) throw new RuntimeException('Deleted category not found.');
        $this->m->restore($tenantId, $id, $userId);
    }

    public function create(int $tenantId, array $input, int $userId): int
    {
        $data = $this->validate($input);

        if (!$this->m->restaurantExists($tenantId, $data['restaurant_id'])) {
            throw new RuntimeException('Selected restaurant is invalid.');
        }

        return $this->m->create($tenantId, $data, $userId);
    }

    public function update(int $tenantId, int $id, array $input, int $userId): void
    {
        if (!$this->find($tenantId, $id)) {
            throw new RuntimeException('Category not found.');
        }

        $data = $this->validate($input);

        if (!$this->m->restaurantExists($tenantId, $data['restaurant_id'])) {
            throw new RuntimeException('Selected restaurant is invalid.');
        }

        $this->m->update($tenantId, $id, $data, $userId);
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        if (!$this->find($tenantId, $id)) {
            throw new RuntimeException('Category not found.');
        }

        $this->m->delete($tenantId, $id, $userId);
    }

    private function validate(array $input): array
    {
        $name = trim((string) ($input['name'] ?? ''));
        if ($name === '') {
            throw new RuntimeException('Category name is required.');
        }

        $status = (string) ($input['status'] ?? 'Active');
        if (!in_array($status, ['Active', 'Inactive'], true)) {
            throw new RuntimeException('Invalid category status.');
        }

        return [
            'restaurant_id' => (int) ($input['restaurant_id'] ?? 0),
            'name' => $name,
            'description' => trim((string) ($input['description'] ?? '')) ?: null,
            'sort_order' => (int) ($input['sort_order'] ?? 0),
            'status' => $status,
        ];
    }
}
