<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Services;

use App\Modules\Restaurant\Models\RestaurantModifier;
use RuntimeException;

final class RestaurantModifierService
{
    private RestaurantModifier $m;

    public function __construct() { $this->m = new RestaurantModifier(); }
    public function all(int $tenantId, bool $withDeleted = false): array { return $this->m->all($tenantId, $withDeleted); }
    public function trash(int $tenantId): array { return $this->m->trash($tenantId); }
    public function find(int $tenantId, int $id): ?array { return $this->m->find($tenantId, $id); }
    public function options(int $tenantId, int $id): array { return $this->m->options($tenantId, $id); }
    public function deletedOptions(int $tenantId, int $id): array { return $this->m->deletedOptions($tenantId, $id); }
    public function restaurants(int $tenantId): array { return $this->m->restaurants($tenantId); }
    public function attachedItems(int $tenantId, int $groupId): array { return $this->m->attachedItems($tenantId, $groupId); }
    public function availableItems(int $tenantId, int $groupId): array { return $this->m->availableItems($tenantId, $groupId); }

    public function findDeleted(int $tenantId, int $id): ?array { return $this->m->findDeleted($tenantId, $id); }

    public function restore(int $tenantId, int $id, int $userId): void
    {
        if (!$this->findDeleted($tenantId, $id)) throw new RuntimeException('Deleted modifier group not found.');
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
            throw new RuntimeException('Modifier group not found.');
        }

        $data = $this->validate($input);
        if (!$this->m->restaurantExists($tenantId, $data['restaurant_id'])) {
            throw new RuntimeException('Selected restaurant is invalid.');
        }

        $this->m->update($tenantId, $id, $data, $userId);
    }

    public function attachItem(int $tenantId, int $groupId, int $itemId, int $userId): void
    {
        if (!$this->find($tenantId, $groupId)) {
            throw new RuntimeException('Modifier group not found.');
        }

        try {
            $this->m->attachItem($tenantId, $groupId, $itemId, $userId);
        } catch (\PDOException $e) {
            if ((int) $e->errorInfo[1] === 1062) {
                throw new RuntimeException('This menu item is already attached to the modifier group.');
            }
            throw $e;
        }
    }

    public function detachItem(int $tenantId, int $groupId, int $itemId, int $userId): void
    {
        if (!$this->find($tenantId, $groupId)) {
            throw new RuntimeException('Modifier group not found.');
        }

        $this->m->detachItem($tenantId, $groupId, $itemId);
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        if (!$this->find($tenantId, $id)) {
            throw new RuntimeException('Modifier group not found.');
        }
        $this->m->delete($tenantId, $id, $userId);
    }

    public function addOption(int $tenantId, int $groupId, array $input, int $userId): int
    {
        $group = $this->find($tenantId, $groupId);
        if (!$group) throw new RuntimeException('Modifier group not found.');

        $name = trim((string) ($input['name'] ?? ''));
        if ($name === '') throw new RuntimeException('Option name is required.');
        if ($this->m->optionNameExists($tenantId, $groupId, $name)) {
            throw new RuntimeException('This modifier option already exists.');
        }

        $price = (float) ($input['price_adjustment'] ?? 0);
        if ($price < 0) throw new RuntimeException('Price adjustment cannot be negative.');

        return $this->m->addOption($tenantId, $groupId, [
            'restaurant_id' => (int) $group['restaurant_id'],
            'name' => $name,
            'price_adjustment' => number_format($price, 2, '.', ''),
            'is_available' => !empty($input['is_available']) ? 1 : 0,
            'sort_order' => (int) ($input['sort_order'] ?? 0),
            'status' => 'Active',
        ], $userId);
    }

    public function restoreOption(int $tenantId, int $groupId, int $optionId, int $userId): void
    {
        if (!$this->find($tenantId, $groupId)) throw new RuntimeException('Modifier group not found.');
        $this->m->restoreOption($tenantId, $groupId, $optionId, $userId);
    }

    public function deleteOption(int $tenantId, int $groupId, int $optionId, int $userId): void
    {
        if (!$this->find($tenantId, $groupId)) {
            throw new RuntimeException('Modifier group not found.');
        }
        $this->m->deleteOption($tenantId, $groupId, $optionId, $userId);
    }

    private function validate(array $input): array
    {
        $name = trim((string) ($input['name'] ?? ''));
        if ($name === '') throw new RuntimeException('Modifier group name is required.');

        $type = (string) ($input['selection_type'] ?? 'SINGLE');
        if (!in_array($type, ['SINGLE', 'MULTIPLE'], true)) {
            throw new RuntimeException('Invalid selection type.');
        }

        $min = max(0, (int) ($input['min_selections'] ?? 0));
        $max = ($input['max_selections'] ?? '') === '' ? null : max(0, (int) $input['max_selections']);
        if ($max !== null && $max < $min) {
            throw new RuntimeException('Maximum selections cannot be lower than minimum selections.');
        }

        $status = (string) ($input['status'] ?? 'Active');
        if (!in_array($status, ['Active', 'Inactive'], true)) {
            throw new RuntimeException('Invalid modifier status.');
        }

        return [
            'restaurant_id' => (int) ($input['restaurant_id'] ?? 0),
            'name' => $name,
            'description' => trim((string) ($input['description'] ?? '')) ?: null,
            'selection_type' => $type,
            'min_selections' => $min,
            'max_selections' => $max,
            'is_required' => !empty($input['is_required']) ? 1 : 0,
            'sort_order' => (int) ($input['sort_order'] ?? 0),
            'status' => $status,
        ];
    }
}
