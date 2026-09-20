<?php

declare(strict_types=1);

namespace App\Modules\Lookup\Models;

use App\Core\Database;

final class LookupValue
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    /**
     * Get all lookup values for a lookup type.
     */
    public function all(
        int $tenantId,
        int $lookupTypeId
    ): array
    {
        return $this->db->fetchAll(
            "
            SELECT

                lv.*,

                lt.name AS lookup_type_name

            FROM lookup_values lv

            INNER JOIN lookup_types lt

                ON lt.id = lv.lookup_type_id

            WHERE

                lv.tenant_id = ?

            AND

                lv.lookup_type_id = ?

            AND

                lv.deleted_at IS NULL

            ORDER BY

                lv.display_order,

                lv.name
            ",
            [
                $tenantId,
                $lookupTypeId
            ]
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
        return $this->db->fetchAll(
            "
            SELECT

                lv.*,

                lt.name AS lookup_type_name

            FROM lookup_values lv

            INNER JOIN lookup_types lt

                ON lt.id = lv.lookup_type_id

            WHERE

                lv.tenant_id = ?

            AND

                lv.lookup_type_id = ?

            AND

                lv.status = 'Active'

            AND

                lv.deleted_at IS NULL

            ORDER BY

                lv.display_order,

                lv.name
            ",
            [
                $tenantId,
                $lookupTypeId
            ]
        );
    }

    /**
     * Get values by lookup slug.
     *
     * Example:
     * appointment_type
     * gathering_type
     * blood_group
     */
    public function values(
        int $tenantId,
        string $lookupSlug
    ): array
    {
        return $this->db->fetchAll(
            "
            SELECT

                lv.*

            FROM lookup_values lv

            INNER JOIN lookup_types lt

                ON lt.id = lv.lookup_type_id

            WHERE

                lv.tenant_id = ?

            AND

                lt.slug = ?

            AND

                lv.status = 'Active'

            AND

                lv.deleted_at IS NULL

            ORDER BY

                lv.display_order,

                lv.name
            ",
            [
                $tenantId,
                $lookupSlug
            ]
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
        return $this->db->fetch(
            "
            SELECT

                lv.*,

                lt.name AS lookup_type_name

            FROM lookup_values lv

            INNER JOIN lookup_types lt

                ON lt.id = lv.lookup_type_id

            WHERE

                lv.id = ?

            AND

                lv.tenant_id = ?

            AND

                lv.deleted_at IS NULL
            ",
            [
                $id,
                $tenantId
            ]
        );
    }

    /**
     * Find by slug.
     */
    public function findBySlug(
        int $tenantId,
        int $lookupTypeId,
        string $slug
    ): ?array
    {
        return $this->db->fetch(
            "
            SELECT *

            FROM lookup_values

            WHERE tenant_id = ?

            AND lookup_type_id = ?

            AND slug = ?

            LIMIT 1
            ",
            [
                $tenantId,
                $lookupTypeId,
                $slug
            ]
        );
    }

    /**
     * Create lookup value.
     */
    public function create(
        array $data
    ): int
    {
        $this->db->execute(
            "
            INSERT INTO lookup_values
            (
                uuid,

                tenant_id,

                lookup_type_id,

                name,

                slug,

                description,

                color,

                icon,

                display_order,

                is_default,

                is_system,

                status,

                created_by
            )

            VALUES
            (
                UUID(),

                ?,?,?,?,?,?,?,?,?,?,?,?
            )
            ",
            [

                $data['tenant_id'],

                $data['lookup_type_id'],

                $data['name'],

                $data['slug'],

                $data['description'],

                $data['color'],

                $data['icon'],

                $data['display_order'],

                $data['is_default'],

                $data['is_system'],

                $data['status'],

                $data['created_by']

            ]
        );

        return (int) $this->db->lastInsertId();
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
        $this->db->execute(
            "
            UPDATE lookup_values

            SET

                lookup_type_id = ?,

                name = ?,

                slug = ?,

                description = ?,

                color = ?,

                icon = ?,

                display_order = ?,

                status = ?,

                updated_by = ?

            WHERE id = ?

            AND tenant_id = ?
            ",
            [

                $data['lookup_type_id'],

                $data['name'],

                $data['slug'],

                $data['description'],

                $data['color'],

                $data['icon'],

                $data['display_order'],

                $data['status'],

                $data['updated_by'],

                $id,

                $tenantId

            ]
        );
    }

    /**
     * Soft delete.
     */
    public function softDelete(
        int $tenantId,
        int $id
    ): void
    {
        $this->db->execute(
            "
            UPDATE lookup_values

            SET deleted_at = NOW()

            WHERE id = ?

            AND tenant_id = ?
            ",
            [
                $id,
                $tenantId
            ]
        );
    }

    /**
     * Restore.
     */
    public function restore(
        int $id,
        array $data
    ): void
    {
        $this->db->execute(
            "
            UPDATE lookup_values

            SET

                lookup_type_id = ?,

                name = ?,

                slug = ?,

                description = ?,

                color = ?,

                icon = ?,

                display_order = ?,

                status = ?,

                deleted_at = NULL

            WHERE id = ?
            ",
            [

                $data['lookup_type_id'],

                $data['name'],

                $data['slug'],

                $data['description'],

                $data['color'],

                $data['icon'],

                $data['display_order'],

                $data['status'],

                $id

            ]
        );
    }

    /**
     * Check duplicate slug.
     */
    public function slugExists(
        int $tenantId,
        int $lookupTypeId,
        string $slug,
        ?int $ignoreId = null
    ): bool
    {
        $sql = "
            SELECT id

            FROM lookup_values

            WHERE tenant_id = ?

            AND lookup_type_id = ?

            AND slug = ?

            AND deleted_at IS NULL
        ";

        $params = [
            $tenantId,
            $lookupTypeId,
            $slug
        ];

        if ($ignoreId !== null) {

            $sql .= " AND id <> ?";

            $params[] = $ignoreId;

        }

        $sql .= " LIMIT 1";

        return $this->db->fetch(
            $sql,
            $params
        ) !== null;
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
        return $this->db->fetch(
            "
            SELECT

                lv.*

            FROM lookup_values lv

            INNER JOIN lookup_types lt

                ON lt.id = lv.lookup_type_id

            WHERE

                lt.tenant_id = ?

            AND

                lt.slug = ?

            AND

                lv.slug = ?

            AND

                lv.deleted_at IS NULL

            LIMIT 1
            ",
            [
                $tenantId,
                $lookupTypeSlug,
                $valueSlug
            ]
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
        return $this->db->fetchAll(
            "
            SELECT

                lv.*

            FROM lookup_values lv

            INNER JOIN lookup_types lt

                ON lt.id = lv.lookup_type_id

            WHERE

                lt.tenant_id = ?

            AND

                lt.slug = ?

            AND

                lv.status = 'Active'

            AND

                lv.deleted_at IS NULL

            ORDER BY

                lv.display_order,

                lv.name
            ",
            [
                $tenantId,
                $lookupTypeSlug
            ]
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
        return $this->db->fetch(
            "
            SELECT lv.id

            FROM lookup_values lv

            INNER JOIN lookup_types lt

                ON lt.id = lv.lookup_type_id

            WHERE

                lt.tenant_id = ?

            AND

                lt.slug = ?

            AND

                lv.id = ?

            AND

                lv.deleted_at IS NULL

            LIMIT 1
            ",
            [
                $tenantId,
                $lookupTypeSlug,
                $lookupValueId
            ]
        ) !== null;
    }
}