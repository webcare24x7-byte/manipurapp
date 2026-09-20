<?php

declare(strict_types=1);

namespace App\Modules\Roles\Models;

use App\Core\Database;

final class Permission
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    /**
     * Get all permission definitions.
     *
     * Permissions are global definitions and are not
     * tenant-specific.
     */
    public function all(): array
    {
        return $this->db->fetchAll(
            "
            SELECT
                id,
                uuid,
                name,
                slug
            FROM permissions
            ORDER BY
                slug ASC
            "
        );
    }

    /**
     * Get a permission by ID.
     */
    public function findById(
        int $id
    ): ?array {
        return $this->db->fetch(
            "
            SELECT
                id,
                uuid,
                name,
                slug
            FROM permissions
            WHERE id = ?
            LIMIT 1
            ",
            [
                $id
            ]
        );
    }

    /**
     * Get a permission by slug.
     */
    public function findBySlug(
        string $slug
    ): ?array {
        return $this->db->fetch(
            "
            SELECT
                id,
                uuid,
                name,
                slug
            FROM permissions
            WHERE slug = ?
            LIMIT 1
            ",
            [
                strtolower(trim($slug))
            ]
        );
    }

    /**
     * Get permissions assigned to a role.
     *
     * The role itself is tenant-scoped, therefore
     * the tenant is always included in this lookup.
     */
    public function forRole(
        int $tenantId,
        int $roleId
    ): array {
        return $this->db->fetchAll(
            "
            SELECT
                p.id,
                p.uuid,
                p.name,
                p.slug

            FROM permissions p

            INNER JOIN role_permissions rp
                ON rp.permission_id = p.id

            INNER JOIN roles r
                ON r.id = rp.role_id

            WHERE r.tenant_id = ?
              AND r.id = ?

            ORDER BY
                p.slug ASC
            ",
            [
                $tenantId,
                $roleId
            ]
        );
    }

    /**
     * Get permission IDs assigned to a role.
     *
     * Useful for checkbox state in the UI.
     */
    public function idsForRole(
        int $tenantId,
        int $roleId
    ): array {
        $rows = $this->db->fetchAll(
            "
            SELECT
                p.id

            FROM permissions p

            INNER JOIN role_permissions rp
                ON rp.permission_id = p.id

            INNER JOIN roles r
                ON r.id = rp.role_id

            WHERE r.tenant_id = ?
              AND r.id = ?

            ORDER BY p.id ASC
            ",
            [
                $tenantId,
                $roleId
            ]
        );

        return array_map(
            static fn (array $row): int => (int) $row['id'],
            $rows
        );
    }

    /**
     * Get all permissions grouped by module.
     *
     * The module is derived from the first segment
     * of the permission slug.
     *
     * Example:
     *
     * events.view
     * events.create
     * events.edit
     *
     * becomes:
     *
     * events => [...]
     */
    public function grouped(): array
    {
        $permissions = $this->all();

        $groups = [];

        foreach ($permissions as $permission) {

            $slug = (string) $permission['slug'];

            $parts = explode(
                '.',
                $slug,
                2
            );

            $module = $parts[0] ?? 'other';

            if (!isset($groups[$module])) {
                $groups[$module] = [];
            }

            $groups[$module][] = $permission;
        }

        ksort($groups);

        return $groups;
    }
}