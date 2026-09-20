<?php

declare(strict_types=1);

namespace App\Modules\Onboarding\Models;

use App\Core\Database;

final class Role
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    /**
     * Create default roles.
     */
    public function createDefaults(int $tenantId): array
    {
        $roles = [
            'church_admin' => 'Church Administrator',
            'pastor' => 'Pastor',
            'treasurer' => 'Treasurer',
            'secretary' => 'Secretary',
            'volunteer' => 'Volunteer',
            'member' => 'Member',
        ];

        $ids = [];

        foreach ($roles as $slug => $name) {

            $this->db->execute(
                "
                INSERT INTO roles
                (
                    uuid,
                    tenant_id,
                    name,
                    slug,
                    is_system,
                    created_at,
                    updated_at
                )
                VALUES
                (
                    UUID(),
                    ?,
                    ?,
                    ?,
                    1,
                    NOW(),
                    NOW()
                )
                ",
                [
                    $tenantId,
                    $name,
                    $slug,
                ]
            );

            $ids[$slug] = (int)$this->db->lastInsertId();
        }

        return $ids;
    }

    public function assignDefaultPermissions(
            int $churchAdminRoleId
        ): void
        {
            $permissions = $this->db->fetchAll(
                "SELECT id FROM permissions"
            );

            foreach ($permissions as $permission) {

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
                        $churchAdminRoleId,
                        $permission['id']
                    ]
                );
            }
        }

        public function findBySlug(
            int $tenantId,
            string $slug
        ): ?array
        {
            return $this->db->fetch(
                "
                SELECT *

                FROM roles

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
}