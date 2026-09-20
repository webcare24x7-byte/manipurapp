<?php

declare(strict_types=1);

namespace App\Modules\Lookup\Services;

use App\Modules\Lookup\Models\LookupType;
use InvalidArgumentException;

final class LookupTypeService
{
    private LookupType $types;

    public function __construct()
    {
        $this->types = new LookupType();
    }

    /**
     * Get all lookup types.
     */
    public function all(
        int $tenantId
    ): array
    {
        return $this->types->all(
            $tenantId
        );
    }

    /**
     * Get active lookup types.
     */
    public function active(
        int $tenantId
    ): array
    {
        return $this->types->active(
            $tenantId
        );
    }

    /**
     * Find lookup type.
     */
    public function find(
        int $tenantId,
        int $id
    ): ?array
    {
        return $this->types->find(
            $tenantId,
            $id
        );
    }

    /**
     * Create lookup type.
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

        $deleted = $this->types->findBySlug(
            (int)$data['tenant_id'],
            $data['slug']
        );

        if (
            $deleted !== null &&
            $deleted['deleted_at'] !== null
        ) {

            $this->types->restore(
                (int)$deleted['id'],
                $data
            );

            return (int)$deleted['id'];

        }

        if (
            $this->types->slugExists(
                (int)$data['tenant_id'],
                $data['slug']
            )
        ) {

            throw new InvalidArgumentException(
                'Lookup type already exists.'
            );

        }

        return $this->types->create(
            $data
        );
    }

    /**
     * Ensure lookup type exists.
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
        | Find existing.
        |--------------------------------------------------------------------------
        */

        $existing = $this->types->findBySlug(
            (int) $data['tenant_id'],
            $data['slug']
        );

        /*
        |--------------------------------------------------------------------------
        | Create.
        |--------------------------------------------------------------------------
        */

        if ($existing === null) {

            return [

                'id' => $this->types->create($data),

                'action' => 'created',

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Restore.
        |--------------------------------------------------------------------------
        */

        if ($existing['deleted_at'] !== null) {

            $this->types->restore(
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
     * Update lookup type.
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

        if (
            $this->types->slugExists(
                $tenantId,
                $data['slug'],
                $id
            )
        ) {

            throw new InvalidArgumentException(
                'Lookup type already exists.'
            );

        }

        $this->types->update(
            $tenantId,
            $id,
            $data
        );
    }

    /**
     * Delete lookup type.
     */
    public function delete(
        int $tenantId,
        int $id
    ): void
    {
        $this->types->softDelete(
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
                'Lookup type name is required.'
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

            'icon' => null,

            'display_order' => 0,

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


}