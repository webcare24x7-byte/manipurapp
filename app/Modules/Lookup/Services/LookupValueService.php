<?php

declare(strict_types=1);

namespace App\Modules\Lookup\Services;

use App\Modules\Lookup\Models\LookupType;
use App\Modules\Lookup\Models\LookupValue;
use InvalidArgumentException;

final class LookupValueService
{
    private LookupValue $values;

    private LookupType $types;

    public function __construct()
    {
        $this->values = new LookupValue();

        $this->types = new LookupType();
    }

    /**
     * Get all lookup values.
     */
    public function all(
        int $tenantId,
        int $lookupTypeId
    ): array
    {
        return $this->values->all(
            $tenantId,
            $lookupTypeId
        );
    }

    /**
     * Get active lookup values.
     */
    public function active(
        int $tenantId,
        int $lookupTypeId
    ): array
    {
        return $this->values->active(
            $tenantId,
            $lookupTypeId
        );
    }

    /**
     * Get values by lookup slug.
     */
    public function values(
        int $tenantId,
        string $lookupSlug
    ): array
    {
        return $this->values->values(
            $tenantId,
            $lookupSlug
        );
    }

    /**
     * Get active lookup values by lookup type slug.
     */
    public function activeBySlug(
        int $tenantId,
        string $lookupTypeSlug
    ): array
    {
        return $this->values->activeBySlug(
            $tenantId,
            $lookupTypeSlug
        );
    }

    /**
     * Find lookup value.
     */
    public function find(
        int $tenantId,
        int $id
    ): ?array
    {
        return $this->values->find(
            $tenantId,
            $id
        );
    }

    /**
     * Create lookup value.
     */
    public function create(
        array $data
    ): int
    {
        $this->validate($data);

        $data = $this->normalize($data);

        if (empty($data['slug'])) {

            $data['slug'] = $this->generateSlug(
                $data['name']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Ensure lookup type belongs to tenant.
        |--------------------------------------------------------------------------
        */

        $type = $this->types->find(
            (int)$data['tenant_id'],
            (int)$data['lookup_type_id']
        );

        if ($type === null) {

            throw new InvalidArgumentException(
                'Invalid lookup type.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Restore previously deleted value.
        |--------------------------------------------------------------------------
        */

        $deleted = $this->values->findBySlug(
            (int)$data['tenant_id'],
            (int)$data['lookup_type_id'],
            $data['slug']
        );

        if (
            $deleted !== null &&
            $deleted['deleted_at'] !== null
        ) {

            $this->values->restore(
                (int)$deleted['id'],
                $data
            );

            return (int)$deleted['id'];

        }

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate active slug.
        |--------------------------------------------------------------------------
        */

        if (

            $this->values->slugExists(
                (int)$data['tenant_id'],
                (int)$data['lookup_type_id'],
                $data['slug']
            )

        ) {

            throw new InvalidArgumentException(
                'Lookup value already exists.'
            );

        }

        return $this->values->create(
            $data
        );
    }

    /**
     * Ensure lookup value exists.
     *
     * Creates, restores or returns
     * an existing lookup value.
     *
     * @return array{id:int,action:string}
     */
    public function ensure(
        array $data
    ): array
    {
        $this->validate($data);

        $data = $this->normalize($data);

        if (empty($data['slug'])) {

            $data['slug'] = $this->generateSlug(
                $data['name']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Ensure lookup type belongs to tenant.
        |--------------------------------------------------------------------------
        */

        $type = $this->types->find(
            (int) $data['tenant_id'],
            (int) $data['lookup_type_id']
        );

        if ($type === null) {

            throw new InvalidArgumentException(
                'Invalid lookup type.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Find existing lookup value.
        |--------------------------------------------------------------------------
        */

        $existing = $this->values->findBySlug(
            (int) $data['tenant_id'],
            (int) $data['lookup_type_id'],
            $data['slug']
        );

        /*
        |--------------------------------------------------------------------------
        | Create.
        |--------------------------------------------------------------------------
        */

        if ($existing === null) {

            return [

                'id' => $this->values->create(
                    $data
                ),

                'action' => 'created',

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Restore.
        |--------------------------------------------------------------------------
        */

        if ($existing['deleted_at'] !== null) {

            $this->values->restore(
                (int) $existing['id'],
                $data
            );

            return [

                'id' => (int) $existing['id'],

                'action' => 'restored',

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Already exists.
        |--------------------------------------------------------------------------
        */

        return [

            'id' => (int) $existing['id'],

            'action' => 'existing',

        ];
    }

    /**
     * Update lookup value.
     */
    public function update(
        int $tenantId,
        int $id,
        array $data
    ): void
    {
        $this->validate(
            $data,
            false
        );

        $data = $this->normalize(
            $data
        );

        if (empty($data['slug'])) {

            $data['slug'] = $this->generateSlug(
                $data['name']
            );

        }

        $type = $this->types->find(
            $tenantId,
            (int)$data['lookup_type_id']
        );

        if ($type === null) {

            throw new InvalidArgumentException(
                'Invalid lookup type.'
            );

        }

        if (

            $this->values->slugExists(
                $tenantId,
                (int)$data['lookup_type_id'],
                $data['slug'],
                $id
            )

        ) {

            throw new InvalidArgumentException(
                'Lookup value already exists.'
            );

        }

        $this->values->update(
            $tenantId,
            $id,
            $data
        );
    }

    /**
     * Delete lookup value.
     */
    public function delete(
        int $tenantId,
        int $id
    ): void
    {
        $this->values->softDelete(
            $tenantId,
            $id
        );
    }

    /**
     * Validate.
     */
    private function validate(
        array $data,
        bool $creating = true
    ): void
    {
        if (
            empty(trim($data['name'] ?? ''))
        ) {

            throw new InvalidArgumentException(
                'Lookup value name is required.'
            );

        }

        if (
            empty($data['lookup_type_id'])
        ) {

            throw new InvalidArgumentException(
                'Lookup type is required.'
            );

        }

        if (
            $creating &&
            empty($data['tenant_id'])
        ) {

            throw new InvalidArgumentException(
                'Tenant ID is required.'
            );

        }
    }

    /**
     * Normalize.
     */
    private function normalize(
        array $data
    ): array
    {
        $defaults = [

            'slug' => null,

            'description' => null,

            'color' => null,

            'icon' => null,

            'display_order' => 0,

            'is_default' => 0,

            'is_system' => 0,

            'status' => 'Active',

            'created_by' => null,

            'updated_by' => null,

        ];

        return array_merge(
            $defaults,
            $data
        );
    }

    /**
     * Generate slug.
     */
    private function generateSlug(
        string $name
    ): string
    {
        $slug = strtolower(
            trim($name)
        );

        $slug = preg_replace(
            '/[^a-z0-9]+/',
            '_',
            $slug
        );

        return trim(
            $slug,
            '_'
        );
    }

    /**
     * Find lookup value by lookup type slug and value slug.
     */
    public function findBySlugs(
        int $tenantId,
        string $lookupTypeSlug,
        string $valueSlug
    ): ?array
    {
        return $this->values->findBySlugs(
            $tenantId,
            $lookupTypeSlug,
            $valueSlug
        );
    }

    /**
     * Determine whether a lookup value belongs
     * to the specified lookup type.
     */
    public function belongsToType(
        int $tenantId,
        int $lookupValueId,
        string $lookupTypeSlug
    ): bool
    {
        return $this->values->belongsToType(
            $tenantId,
            $lookupValueId,
            $lookupTypeSlug
        );
    }
}