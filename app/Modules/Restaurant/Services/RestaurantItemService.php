<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Services;

use App\Modules\Restaurant\Models\RestaurantItem;
use RuntimeException;

final class RestaurantItemService
{
    private RestaurantItem $m;

    public function __construct()
    {
        $this->m = new RestaurantItem();
    }

    public function all(int $tenantId, bool $withDeleted = false): array
    {
        return $this->m->all($tenantId, $withDeleted);
    }

    public function trash(int $tenantId): array
    {
        return $this->m->trash($tenantId);
    }

    public function find(
        int $tenantId,
        int $id
    ): ?array {
        return $this->m->find(
            $tenantId,
            $id
        );
    }

    /**
     * Get all active restaurants for the tenant.
     */
    public function restaurants(
        int $tenantId
    ): array {
        return $this->m->restaurants(
            $tenantId
        );
    }

    /**
     * Get categories for a specific restaurant.
     */
    public function categories(
        int $tenantId,
        int $restaurantId
    ): array {
        return $this->m->categories(
            $tenantId,
            $restaurantId
        );
    }

    /**
     * Get all active categories for the tenant.
     *
     * Used by the Create Food Item form.
     */
    public function categoriesAll(
        int $tenantId
    ): array {
        return $this->m->categoriesAll(
            $tenantId
        );
    }

    /**
     * Create a food item.
     */
    public function findDeleted(int $tenantId, int $id): ?array { return $this->m->findDeleted($tenantId, $id); }

    public function restore(int $tenantId, int $id, int $userId): void
    {
        if (!$this->findDeleted($tenantId, $id)) throw new RuntimeException('Deleted food item not found.');
        $this->m->restore($tenantId, $id, $userId);
    }

    public function create(
        int $tenantId,
        array $data,
        int $userId
    ): int {
        $restaurantId = (int) (
            $data['restaurant_id'] ?? 0
        );

        if ($restaurantId <= 0) {
            throw new RuntimeException(
                'Please select a restaurant.'
            );
        }

        /*
         * Make sure the restaurant belongs
         * to the current tenant.
         */
        if (
            !$this->m->restaurantExists(
                $tenantId,
                $restaurantId
            )
        ) {
            throw new RuntimeException(
                'Selected restaurant does not belong to this tenant.'
            );
        }

        /*
         * Normalize category ID.
         */
        $categoryId = null;

        if (
            isset($data['category_id']) &&
            $data['category_id'] !== '' &&
            (int) $data['category_id'] > 0
        ) {
            $categoryId = (int) $data['category_id'];

            /*
             * Make sure the category belongs
             * to the selected restaurant.
             */
            if (
                !$this->m->categoryBelongsToRestaurant(
                    $tenantId,
                    $categoryId,
                    $restaurantId
                )
            ) {
                throw new RuntimeException(
                    'Selected category does not belong to the selected restaurant.'
                );
            }
        }

        $data['restaurant_id'] = $restaurantId;
        $data['category_id'] = $categoryId;

        /*
         * Validate item name.
         */
        $name = trim(
            (string) ($data['name'] ?? '')
        );

        if ($name === '') {
            throw new RuntimeException(
                'Food item name is required.'
            );
        }

        $data['name'] = $name;

        /*
         * Generate slug if empty.
         */
        $slug = trim(
            (string) ($data['slug'] ?? '')
        );

        if ($slug === '') {
            $slug = $this->slugify(
                $name
            );
        }

        if ($slug === '') {
            throw new RuntimeException(
                'A valid slug could not be generated.'
            );
        }

        $data['slug'] = $slug;

        /*
         * Make sure slug is unique
         * within this restaurant.
         */
        if (
            $this->m->slugExists(
                $tenantId,
                $restaurantId,
                $slug
            )
        ) {
            throw new RuntimeException(
                'This food item slug already exists for this restaurant.'
            );
        }

        /*
         * Validate price.
         */
        $price = (float) (
            $data['price'] ?? 0
        );

        if ($price < 0) {
            throw new RuntimeException(
                'Price cannot be negative.'
            );
        }

        $data['price'] = $price;

        $discountType = strtoupper(trim((string) ($data['discount_type'] ?? 'PERCENT')));
        if (!in_array($discountType, ['PERCENT', 'FLAT'], true)) {
            $discountType = 'PERCENT';
        }
        $discountValue = (float) ($data['discount_value'] ?? 0);
        if ($discountValue < 0) {
            throw new RuntimeException('Discount cannot be negative.');
        }
        if ($discountType === 'PERCENT' && $discountValue > 100) {
            throw new RuntimeException('Percentage discount cannot exceed 100%.');
        }
        if ($discountType === 'FLAT' && $discountValue > $price) {
            throw new RuntimeException('Flat discount cannot exceed the item price.');
        }
        $data['discount_type'] = $discountType;
        $data['discount_value'] = round($discountValue, 2);

        /*
         * Normalize description.
         */
        $data['description'] = trim(
            (string) (
                $data['description'] ?? ''
            )
        );

        /*
         * Normalize image path.
         */
        $data['image_path'] = trim(
            (string) (
                $data['image_path'] ?? ''
            )
        );

        /*
         * Normalize sort order.
         */
        $data['sort_order'] = (int) (
            $data['sort_order'] ?? 0
        );

        /*
         * Validate status.
         */
        $status = (string) (
            $data['status'] ?? 'Active'
        );

        if (
            !in_array(
                $status,
                ['Active', 'Inactive'],
                true
            )
        ) {
            $status = 'Active';
        }

        $data['status'] = $status;

        /*
         * Normalize boolean fields.
         */
        $data['is_veg'] = !empty(
            $data['is_veg']
        ) ? 1 : 0;

        $data['is_available'] = !empty(
            $data['is_available']
        ) ? 1 : 0;

        return $this->m->create(
            $tenantId,
            $data,
            $userId
        );
    }

    /**
     * Update a food item.
     */
    public function update(
        int $tenantId,
        int $id,
        array $data,
        int $userId
    ): void {
        /*
         * Make sure the item exists.
         */
        $existing = $this->m->find(
            $tenantId,
            $id
        );

        if ($existing === null) {
            throw new RuntimeException(
                'Food item not found.'
            );
        }

        $restaurantId = (int) (
            $data['restaurant_id'] ?? 0
        );

        if ($restaurantId <= 0) {
            throw new RuntimeException(
                'Please select a restaurant.'
            );
        }

        /*
         * Validate restaurant ownership.
         */
        if (
            !$this->m->restaurantExists(
                $tenantId,
                $restaurantId
            )
        ) {
            throw new RuntimeException(
                'Selected restaurant does not belong to this tenant.'
            );
        }

        /*
         * Normalize category.
         */
        $categoryId = null;

        if (
            isset($data['category_id']) &&
            $data['category_id'] !== '' &&
            (int) $data['category_id'] > 0
        ) {
            $categoryId = (int) $data['category_id'];

            if (
                !$this->m->categoryBelongsToRestaurant(
                    $tenantId,
                    $categoryId,
                    $restaurantId
                )
            ) {
                throw new RuntimeException(
                    'Selected category does not belong to the selected restaurant.'
                );
            }
        }

        $data['restaurant_id'] = $restaurantId;
        $data['category_id'] = $categoryId;

        /*
         * Validate name.
         */
        $name = trim(
            (string) ($data['name'] ?? '')
        );

        if ($name === '') {
            throw new RuntimeException(
                'Food item name is required.'
            );
        }

        $data['name'] = $name;

        /*
         * Generate slug if empty.
         */
        $slug = trim(
            (string) ($data['slug'] ?? '')
        );

        if ($slug === '') {
            $slug = $this->slugify(
                $name
            );
        }

        if ($slug === '') {
            throw new RuntimeException(
                'A valid slug could not be generated.'
            );
        }

        $data['slug'] = $slug;

        /*
         * Check slug uniqueness.
         */
        if (
            $this->m->slugExists(
                $tenantId,
                $restaurantId,
                $slug,
                $id
            )
        ) {
            throw new RuntimeException(
                'This food item slug already exists for this restaurant.'
            );
        }

        /*
         * Validate price.
         */
        $price = (float) (
            $data['price'] ?? 0
        );

        if ($price < 0) {
            throw new RuntimeException(
                'Price cannot be negative.'
            );
        }

        $data['price'] = $price;

        $discountType = strtoupper(trim((string) ($data['discount_type'] ?? 'PERCENT')));
        if (!in_array($discountType, ['PERCENT', 'FLAT'], true)) {
            $discountType = 'PERCENT';
        }
        $discountValue = (float) ($data['discount_value'] ?? 0);
        if ($discountValue < 0) {
            throw new RuntimeException('Discount cannot be negative.');
        }
        if ($discountType === 'PERCENT' && $discountValue > 100) {
            throw new RuntimeException('Percentage discount cannot exceed 100%.');
        }
        if ($discountType === 'FLAT' && $discountValue > $price) {
            throw new RuntimeException('Flat discount cannot exceed the item price.');
        }
        $data['discount_type'] = $discountType;
        $data['discount_value'] = round($discountValue, 2);

        /*
         * Normalize remaining fields.
         */
        $data['description'] = trim(
            (string) (
                $data['description'] ?? ''
            )
        );

        $data['image_path'] = trim(
            (string) (
                $data['image_path'] ?? ''
            )
        );

        $data['sort_order'] = (int) (
            $data['sort_order'] ?? 0
        );

        $status = (string) (
            $data['status'] ?? 'Active'
        );

        if (
            !in_array(
                $status,
                ['Active', 'Inactive'],
                true
            )
        ) {
            $status = 'Active';
        }

        $data['status'] = $status;

        $data['is_veg'] = !empty(
            $data['is_veg']
        ) ? 1 : 0;

        $data['is_available'] = !empty(
            $data['is_available']
        ) ? 1 : 0;

        $this->m->update(
            $tenantId,
            $id,
            $data,
            $userId
        );
    }

    /**
     * Delete a food item.
     */
    public function delete(
        int $tenantId,
        int $id,
        int $userId
    ): void {
        $existing = $this->m->find(
            $tenantId,
            $id
        );

        if ($existing === null) {
            throw new RuntimeException(
                'Food item not found.'
            );
        }

        $this->m->delete(
            $tenantId,
            $id,
            $userId
        );
    }

    /**
     * Convert text into a URL-friendly slug.
     */
    private function slugify(
        string $value
    ): string {
        $value = trim(
            strtolower($value)
        );

        $value = preg_replace(
            '/[^a-z0-9]+/',
            '-',
            $value
        ) ?? '';

        return trim(
            $value,
            '-'
        );
    }
}