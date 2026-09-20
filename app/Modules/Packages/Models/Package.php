<?php

declare(strict_types=1);

namespace App\Modules\Packages\Models;

use App\Core\Database;

final class Package
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    /**
     * Get all installed packages.
     */
    public function all(
        int $tenantId
    ): array
    {
        return $this->db->fetchAll(
            "
            SELECT *

            FROM packages

            WHERE tenant_id = ?

            AND deleted_at IS NULL

            ORDER BY code
            ",
            [
                $tenantId
            ]
        );
    }

    /**
     * Find package.
     */
    public function find(
        int $tenantId,
        int $id
    ): ?array
    {
        return $this->db->fetch(
            "
            SELECT *

            FROM packages

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
     * Find package by code.
     */
    public function findByCode(
        int $tenantId,
        string $code
    ): ?array
    {
        return $this->db->fetch(
            "
            SELECT *

            FROM packages

            WHERE tenant_id = ?

            AND code = ?

            AND deleted_at IS NULL

            LIMIT 1
            ",
            [
                $tenantId,
                $code
            ]
        );
    }

    /**
     * Check whether package is installed.
     */
    public function isInstalled(
        int $tenantId,
        string $code
    ): bool
    {
        return $this->findByCode(
            $tenantId,
            $code
        ) !== null;
    }

    /**
     * Create package.
     */
    public function create(
        array $data
    ): int
    {
        $this->db->execute(
            "
            INSERT INTO packages
            (
                uuid,

                tenant_id,

                code,

                installed_version,

                status,

                installed_by
            )

            VALUES
            (
                UUID(),

                ?,

                ?,

                ?,

                ?,

                ?
            )
            ",
            [

                $data['tenant_id'],

                $data['code'],

                $data['installed_version'],

                $data['status'],

                $data['installed_by']

            ]
        );

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update package.
     */
    public function update(
        int $tenantId,
        int $id,
        array $data
    ): void
    {
        $this->db->execute(
            "
            UPDATE packages

            SET

                installed_version = ?,

                status = ?

            WHERE id = ?

            AND tenant_id = ?
            ",
            [

                $data['installed_version'],

                $data['status'],

                $id,

                $tenantId

            ]
        );
    }

    /**
     * Soft delete package.
     */
    public function softDelete(
        int $tenantId,
        int $id
    ): void
    {
        $this->db->execute(
            "
            UPDATE packages

            SET

                deleted_at = NOW()

            WHERE id = ?

            AND tenant_id = ?
            ",
            [
                $id,
                $tenantId
            ]
        );
    }
}