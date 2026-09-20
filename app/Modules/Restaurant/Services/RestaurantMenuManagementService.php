<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Services;

use App\Modules\Restaurant\Models\RestaurantMenuManagement;
use RuntimeException;

final class RestaurantMenuManagementService
{
    private RestaurantMenuManagement $menu;

    public function __construct()
    {
        $this->menu = new RestaurantMenuManagement();
    }

    public function restaurants(int $tenantId): array
    {
        return $this->menu->restaurants($tenantId);
    }

    public function build(int $tenantId, int $restaurantId): array
    {
        $restaurant = $this->menu->restaurant($tenantId, $restaurantId);
        if (!$restaurant) {
            throw new RuntimeException('Restaurant not found.');
        }

        $categories = $this->menu->categories($tenantId, $restaurantId);
        $items = $this->menu->items($tenantId, $restaurantId);
        $variants = $this->menu->variants($tenantId, $restaurantId);
        $itemModifierGroups = $this->menu->itemModifierGroups($tenantId, $restaurantId);
        $modifierGroups = $this->menu->modifierGroups($tenantId, $restaurantId);

        $variantsByItem = [];
        foreach ($variants as $variant) {
            $variantsByItem[(int) $variant['item_id']][] = $variant;
        }

        $modifiersByItem = [];
        foreach ($itemModifierGroups as $group) {
            $modifiersByItem[(int) $group['item_id']][] = $group;
        }

        $itemsByCategory = [];
        $uncategorized = [];
        foreach ($items as $item) {
            $item['variants'] = $variantsByItem[(int) $item['id']] ?? [];
            $item['modifier_groups'] = $modifiersByItem[(int) $item['id']] ?? [];

            if ($item['category_id'] === null || (int) $item['category_id'] <= 0) {
                $uncategorized[] = $item;
            } else {
                $itemsByCategory[(int) $item['category_id']][] = $item;
            }
        }

        foreach ($categories as &$category) {
            $category['items'] = $itemsByCategory[(int) $category['id']] ?? [];
        }
        unset($category);

        return [
            'restaurant' => $restaurant,
            'categories' => $categories,
            'uncategorized' => $uncategorized,
            'modifier_groups' => $modifierGroups,
            'summary' => $this->menu->summary($tenantId, $restaurantId),
        ];
    }

    public function setItemAvailability(int $tenantId, int $restaurantId, int $itemId, bool $available, int $userId): void
    {
        $this->menu->setItemAvailability($tenantId, $restaurantId, $itemId, $available, $userId);
    }

    public function setVariantAvailability(int $tenantId, int $restaurantId, int $variantId, bool $available, int $userId): void
    {
        $this->menu->setVariantAvailability($tenantId, $restaurantId, $variantId, $available, $userId);
    }
}
