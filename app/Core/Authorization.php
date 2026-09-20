<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Database;

final class Authorization
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    /**
     * Check whether the currently authenticated user
     * has a specific permission.
     */
    public function can(string $permission): bool
    {
        $user = Auth::user();

        if ($user === null) {
            return false;
        }

        $tenantId = (int) ($user['tenant_id'] ?? 0);
        $roleId   = (int) ($user['role_id'] ?? 0);

        if ($tenantId <= 0 || $roleId <= 0) {
            return false;
        }

        return $this->roleCan(
            $tenantId,
            $roleId,
            $permission
        );
    }

    /**
     * Check whether a role has a permission
     * within a specific tenant.
     */
    public function roleCan(
        int $tenantId,
        int $roleId,
        string $permission
    ): bool {
        $permission = strtolower(
            trim($permission)
        );

        if ($permission === '') {
            return false;
        }

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
                $permission
            ]
        );

        return $row !== null;
    }

    /**
     * Require the currently authenticated user
     * to have a permission.
     *
     * Throws RuntimeException when denied.
     */
    public function authorize(
        string $permission
    ): void {
        if (!$this->can($permission)) {
            throw new \RuntimeException(
                'You do not have permission to perform this action.'
            );
        }
    }

    /**
     * Check whether the current user has at least
     * one of the supplied permissions.
     */
    public function canAny(
        array $permissions
    ): bool {
        foreach ($permissions as $permission) {

            if (
                is_string($permission)
                && $this->can($permission)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether the current user has all
     * supplied permissions.
     */
    public function canAll(
        array $permissions
    ): bool {
        foreach ($permissions as $permission) {

            if (
                !is_string($permission)
                || !$this->can($permission)
            ) {
                return false;
            }
        }

        return true;
    }
}