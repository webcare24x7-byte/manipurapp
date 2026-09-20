<?php

declare(strict_types=1);

namespace App\Modules\Roles\Services;

use App\Core\Database;
use App\Modules\Roles\Models\Permission;
use RuntimeException;

final class RolePermissionService
{
    private Database $db;

    private Permission $permission;

    public function __construct()
    {
        $this->db = app()->get('db');

        $this->permission = new Permission();
    }

    /**
     * Get all roles belonging to the tenant.
     */
    public function roles(
        int $tenantId
    ): array {
        return $this->db->fetchAll(
            "
            SELECT
                id,
                uuid,
                tenant_id,
                name,
                slug,
                description,
                is_system

            FROM roles

            WHERE tenant_id = ?

            ORDER BY
                is_system DESC,
                name ASC
            ",
            [
                $tenantId
            ]
        );
    }

    /**
     * Get a tenant role.
     */
    public function role(
        int $tenantId,
        int $roleId
    ): ?array {
        return $this->db->fetch(
            "
            SELECT
                id,
                uuid,
                tenant_id,
                name,
                slug,
                description,
                is_system

            FROM roles

            WHERE tenant_id = ?
              AND id = ?

            LIMIT 1
            ",
            [
                $tenantId,
                $roleId
            ]
        );
    }

    /**
     * Get data required by the Roles & Permissions UI.
     */
    public function editData(
        int $tenantId,
        int $roleId
    ): array {
        $role = $this->role(
            $tenantId,
            $roleId
        );

        if ($role === null) {
            throw new RuntimeException(
                'Role not found.'
            );
        }

        return [
            'role' => $role,

            'roles' => $this->roles(
                $tenantId
            ),

            'permissions' => $this->permission->grouped(),

            'assigned_permission_ids' =>
                $this->permission->idsForRole(
                    $tenantId,
                    $roleId
                ),
        ];
    }

    /**
     * Save permissions assigned to a role.
     */
    public function save(
        int $tenantId,
        int $roleId,
        array $permissionIds
    ): void {
        $role = $this->role(
            $tenantId,
            $roleId
        );

        if ($role === null) {
            throw new RuntimeException(
                'Role not found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize permission IDs
        |--------------------------------------------------------------------------
        */

        $permissionIds = array_map(
            static fn ($id): int => (int) $id,
            $permissionIds
        );

        $permissionIds = array_values(
            array_unique(
                array_filter(
                    $permissionIds,
                    static fn (int $id): bool => $id > 0
                )
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Verify every permission exists.
        |--------------------------------------------------------------------------
        |
        | Permissions are global definitions, so there is
        | intentionally no tenant_id on this validation.
        |
        */

        foreach ($permissionIds as $permissionId) {

            $permission = $this->permission->findById(
                $permissionId
            );

            if ($permission === null) {
                throw new RuntimeException(
                    'Invalid permission selected.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Replace role assignments
        |--------------------------------------------------------------------------
        */

        $this->db->beginTransaction();

        try {

            /*
             * Remove current assignments.
             */
            $this->db->execute(
                "
                DELETE FROM role_permissions
                WHERE role_id = ?
                ",
                [
                    $roleId
                ]
            );

            /*
             * Insert new assignments.
             */
            foreach ($permissionIds as $permissionId) {

                $this->db->execute(
                    "
                    INSERT INTO role_permissions
                    (
                        role_id,
                        permission_id
                    )
                    VALUES
                    (
                        ?,
                        ?
                    )
                    ",
                    [
                        $roleId,
                        $permissionId
                    ]
                );
            }

            $this->db->commit();

        } catch (\Throwable $e) {

            $this->db->rollBack();

            throw $e;
        }
    }

    /**
     * Check whether a role has a permission.
     */
    public function roleCan(
        int $tenantId,
        int $roleId,
        string $permissionSlug
    ): bool {
        $row = $this->db->fetch(
            "
            SELECT
                rp.role_id

            FROM role_permissions rp

            INNER JOIN roles r
                ON r.id = rp.role_id

            INNER JOIN permissions p
                ON p.id = rp.permission_id

            WHERE r.tenant_id = ?
              AND r.id = ?
              AND p.slug = ?

            LIMIT 1
            ",
            [
                $tenantId,
                $roleId,
                strtolower(
                    trim($permissionSlug)
                )
            ]
        );

        return $row !== null;
    }
}