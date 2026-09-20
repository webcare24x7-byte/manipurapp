<?php

declare(strict_types=1);

namespace App\Modules\Staff\Models;

use App\Core\Database;

final class Staff
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    /**
     * Get all staff accounts for a tenant.
     *
     * Staff accounts are users whose role
     * is not the Member role.
     */
    public function all(int $tenantId): array
    {
        return $this->db->fetchAll(
            "
            SELECT
                u.id,
                u.uuid,
                u.member_id,
                u.role_id,
                u.name,
                u.email,
                u.status,
                u.created_at,
                u.updated_at,

                r.name AS role_name,
                r.slug AS role_slug,

                m.first_name AS member_first_name,
                m.middle_name AS member_middle_name,
                m.last_name AS member_last_name

            FROM users u

            INNER JOIN roles r
                ON r.id = u.role_id
                AND r.tenant_id = u.tenant_id

            LEFT JOIN members m
                ON m.id = u.member_id
                AND m.tenant_id = u.tenant_id

            WHERE u.tenant_id = ?
              AND r.slug <> 'member'

            ORDER BY
                u.name ASC,
                u.id ASC
            ",
            [
                $tenantId
            ]
        );
    }

    /**
     * Find a staff account within a tenant.
     */
    public function find(
        int $tenantId,
        int $id
    ): ?array {
        return $this->db->fetch(
            "
            SELECT
                u.id,
                u.uuid,
                u.tenant_id,
                u.member_id,
                u.role_id,
                u.name,
                u.email,
                u.status,
                u.created_at,
                u.updated_at,

                r.name AS role_name,
                r.slug AS role_slug,

                m.first_name AS member_first_name,
                m.middle_name AS member_middle_name,
                m.last_name AS member_last_name

            FROM users u

            INNER JOIN roles r
                ON r.id = u.role_id
                AND r.tenant_id = u.tenant_id

            LEFT JOIN members m
                ON m.id = u.member_id
                AND m.tenant_id = u.tenant_id

            WHERE u.tenant_id = ?
              AND u.id = ?
              AND r.slug <> 'member'

            LIMIT 1
            ",
            [
                $tenantId,
                $id
            ]
        );
    }

    /**
     * Find all login accounts belonging to a member.
     */
    public function memberAccounts(
        int $tenantId,
        int $memberId
    ): array {
        return $this->db->fetchAll(
            "
            SELECT
                u.id,
                u.member_id,
                u.role_id,
                u.name,
                u.email,
                u.status,
                r.name AS role_name,
                r.slug AS role_slug

            FROM users u

            INNER JOIN roles r
                ON r.id = u.role_id
                AND r.tenant_id = u.tenant_id

            WHERE u.tenant_id = ?
              AND u.member_id = ?

            ORDER BY u.id ASC
            ",
            [
                $tenantId,
                $memberId
            ]
        );
    }

    /**
     * Get active, non-deleted members available for staff assignment.
     */
    public function members(int $tenantId): array
    {
        return $this->db->fetchAll(
            "
            SELECT
                id,
                first_name,
                middle_name,
                last_name,
                email,
                phone,
                member_no

            FROM members

            WHERE tenant_id = ?
              AND status = 'Active'
              AND deleted_at IS NULL

            ORDER BY
                first_name ASC,
                middle_name ASC,
                last_name ASC
            ",
            [
                $tenantId
            ]
        );
    }

    /**
     * Get roles available for staff.
     */
    public function roles(int $tenantId): array
    {
        return $this->db->fetchAll(
            "
            SELECT
                id,
                name,
                slug

            FROM roles

            WHERE tenant_id = ?
              AND slug <> 'member'

            ORDER BY name ASC
            ",
            [
                $tenantId
            ]
        );
    }

    /**
     * Find an active, non-deleted member within the tenant.
     */
    public function findMember(
        int $tenantId,
        int $memberId
    ): ?array {
        return $this->db->fetch(
            "
            SELECT
                id,
                first_name,
                middle_name,
                last_name,
                email,
                phone,
                member_no

            FROM members

            WHERE tenant_id = ?
              AND id = ?
              AND status = 'Active'
              AND deleted_at IS NULL

            LIMIT 1
            ",
            [
                $tenantId,
                $memberId
            ]
        );
    }

    /**
     * Find an existing staff account for a member.
     *
     * The Member role is excluded because a member may
     * legitimately have both:
     *
     * - a Member login
     * - one Staff login
     */
    public function findStaffByMember(
        int $tenantId,
        int $memberId
    ): ?array {
        return $this->db->fetch(
            "
            SELECT
                u.id,
                u.uuid,
                u.tenant_id,
                u.member_id,
                u.role_id,
                u.name,
                u.email,
                u.status,
                r.name AS role_name,
                r.slug AS role_slug

            FROM users u

            INNER JOIN roles r
                ON r.id = u.role_id
                AND r.tenant_id = u.tenant_id

            WHERE u.tenant_id = ?
              AND u.member_id = ?
              AND r.slug <> 'member'

            LIMIT 1
            ",
            [
                $tenantId,
                $memberId
            ]
        );
    }

    /**
     * Find an existing staff account for a member,
     * excluding the current staff account.
     *
     * Used when editing a staff account.
     */
    public function findStaffByMemberExcept(
        int $tenantId,
        int $memberId,
        int $excludeUserId
    ): ?array {
        return $this->db->fetch(
            "
            SELECT
                u.id,
                u.uuid,
                u.tenant_id,
                u.member_id,
                u.role_id,
                u.name,
                u.email,
                u.status,
                r.name AS role_name,
                r.slug AS role_slug

            FROM users u

            INNER JOIN roles r
                ON r.id = u.role_id
                AND r.tenant_id = u.tenant_id

            WHERE u.tenant_id = ?
              AND u.member_id = ?
              AND u.id <> ?
              AND r.slug <> 'member'

            LIMIT 1
            ",
            [
                $tenantId,
                $memberId,
                $excludeUserId
            ]
        );
    }

    /**
     * Find a role within the tenant.
     */
    public function findRole(
        int $tenantId,
        int $roleId
    ): ?array {
        return $this->db->fetch(
            "
            SELECT
                id,
                tenant_id,
                name,
                slug,
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
}