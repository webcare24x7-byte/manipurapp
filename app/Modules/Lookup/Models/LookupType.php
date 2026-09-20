<?php

declare(strict_types=1);

namespace App\Modules\Lookup\Models;

use App\Core\Database;

final class LookupType
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    /**
     * Get all lookup types.
     */
    public function all(
        int $tenantId
    ): array
    {
        return $this->db->fetchAll(
            "
            SELECT

                lt.*,

                COUNT(lv.id) AS value_count

            FROM lookup_types lt

            LEFT JOIN lookup_values lv

                ON lv.lookup_type_id = lt.id

                AND lv.deleted_at IS NULL

            WHERE

                lt.tenant_id = ?

            AND

                lt.deleted_at IS NULL

            GROUP BY

                lt.id

            ORDER BY

                lt.display_order,

                lt.name
            ",
            [
                $tenantId
            ]
        );
    }

    /**
     * Get active lookup types.
     */
    public function active(
        int $tenantId
    ): array
    {
        return $this->db->fetchAll(
            "
            SELECT *

            FROM lookup_types

            WHERE tenant_id = ?

            AND status = 'Active'

            AND deleted_at IS NULL

            ORDER BY

                display_order,

                name
            ",
            [
                $tenantId
            ]
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
        return $this->db->fetch(
            "
            SELECT *

            FROM lookup_types

            WHERE id = ?

            AND tenant_id = ?

            AND deleted_at IS NULL
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
        string $slug
    ): ?array
    {
        return $this->db->fetch(
            "
            SELECT *

            FROM lookup_types

            WHERE tenant_id = ?

            AND slug = ?

            LIMIT 1
            ",
            [
                $tenantId,
                $slug
            ]
        );
    }

    /**
     * Create lookup type.
     */
    public function create(
        array $data
    ): int
    {
        $this->db->execute(
            "
            INSERT INTO lookup_types
            (
                uuid,

                tenant_id,

                name,

                slug,

                description,

                icon,

                display_order,

                is_system,

                status,

                created_by
            )

            VALUES
            (
                UUID(),

                ?,

                ?,

                ?,

                ?,

                ?,

                ?,

                ?,

                ?,

                ?
            )
            ",
            [

                $data['tenant_id'],

                $data['name'],

                $data['slug'],

                $data['description'],

                $data['icon'],

                $data['display_order'],

                $data['is_system'],

                $data['status'],

                $data['created_by']

            ]
        );

        return (int) $this->db->lastInsertId();
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
        $this->db->execute(
            "
            UPDATE lookup_types

            SET

                name = ?,

                slug = ?,

                description = ?,

                icon = ?,

                display_order = ?,

                status = ?,

                updated_by = ?

            WHERE id = ?

            AND tenant_id = ?
            ",
            [

                $data['name'],

                $data['slug'],

                $data['description'],

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
            UPDATE lookup_types

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
            UPDATE lookup_types

            SET

                name = ?,

                slug = ?,

                description = ?,

                icon = ?,

                display_order = ?,

                status = ?,

                deleted_at = NULL

            WHERE id = ?
            ",
            [

                $data['name'],

                $data['slug'],

                $data['description'],

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
        string $slug,
        ?int $ignoreId = null
    ): bool
    {
        $sql = "
            SELECT id

            FROM lookup_types

            WHERE tenant_id = ?

            AND slug = ?

            AND deleted_at IS NULL
        ";

        $params = [
            $tenantId,
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
}