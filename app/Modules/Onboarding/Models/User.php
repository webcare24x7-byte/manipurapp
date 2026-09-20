<?php

declare(strict_types=1);

namespace App\Modules\Onboarding\Models;

use App\Core\Database;

final class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    /**
     * Create the first administrator.
     */
    public function createAdministrator(
        int $tenantId,
        int $roleId,
        array $data
    ): void {

        $this->db->execute(
            "
            INSERT INTO users
            (
                uuid,
                tenant_id,
                role_id,
                name,
                email,
                password,
                status,
                created_at,
                updated_at
            )
            VALUES
            (
                UUID(),
                ?,
                ?,
                ?,
                ?,
                ?,
                'Active',
                NOW(),
                NOW()
            )
            ",
            [
                $tenantId,
                $roleId,
                trim($data['admin_name']),
                strtolower(trim($data['email'])),
                password_hash(
                    $data['password'],
                    PASSWORD_DEFAULT
                ),
            ]
        );
    }

    /**
     * Check if email exists within a tenant.
     */
    public function emailExists(
        int $tenantId,
        string $email
    ): bool {

        $row = $this->db->fetch(
            "
            SELECT id
            FROM users
            WHERE tenant_id = ?
            AND email = ?
            LIMIT 1
            ",
            [
                $tenantId,
                strtolower(trim($email)),
            ]
        );

        return $row !== null;
    }

/**
 * Find an active user by email within a specific tenant.
 */
/**
 * Find an active user by email within a specific tenant.
 */
public function findByEmail(
    int $tenantId,
    string $email
): ?array {
    return $this->db->fetch(
        "
        SELECT
            u.*,
            r.name AS role_name,
            r.slug AS role_slug
        FROM users u
        INNER JOIN roles r
            ON r.id = u.role_id
            AND r.tenant_id = u.tenant_id
        WHERE u.tenant_id = ?
          AND u.email = ?
          AND u.status = 'Active'
        LIMIT 1
        ",
        [
            $tenantId,
            strtolower(trim($email))
        ]
    );
}

    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            "
            SELECT *
            FROM users
            WHERE id = ?
            LIMIT 1
            ",
            [
                $id
            ]
        );
    }

    public function createMember(
            int $tenantId,
            int $memberId,
            int $roleId,
            string $name,
            ?string $email
        ): void
        {
            $email = trim((string)$email);

            if ($email === '') {

                $email = null;

            }

            $password = password_hash(
                bin2hex(random_bytes(32)),
                PASSWORD_DEFAULT
            );

            $this->db->execute(
                "
                INSERT INTO users
                (
                    uuid,
                    tenant_id,
                    member_id,
                    role_id,
                    name,
                    email,
                    password,
                    status,
                    created_at,
                    updated_at
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
                    'Inactive',
                    NOW(),
                    NOW()
                )
                ",
                [
                    $tenantId,
                    $memberId,
                    $roleId,
                    $name,
                    $email,
                    $password
                ]
            );
        }


        /**
     * Find all login accounts linked to a member.
     */
    public function findByMember(
        int $tenantId,
        int $memberId
    ): array {
        return $this->db->fetchAll(
            "
            SELECT
                u.*,
                r.name AS role_name,
                r.slug AS role_slug
            FROM users u
            INNER JOIN roles r
                ON r.id = u.role_id
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
     * Find a specific member login account by role.
     */
    public function findMemberAccount(
        int $tenantId,
        int $memberId,
        int $roleId
    ): ?array {
        return $this->db->fetch(
            "
            SELECT *
            FROM users
            WHERE tenant_id = ?
              AND member_id = ?
              AND role_id = ?
            LIMIT 1
            ",
            [
                $tenantId,
                $memberId,
                $roleId
            ]
        );
    }

    /**
     * Create a login account.
     *
     * Passwords are always stored as secure hashes.
     */
    public function createAccount(
        int $tenantId,
        ?int $memberId,
        int $roleId,
        string $name,
        string $email,
        string $password,
        string $status = 'Active'
    ): int {
        $email = strtolower(trim($email));

        if ($email === '') {
            throw new \InvalidArgumentException(
                'Email address is required.'
            );
        }

        if ($password === '') {
            throw new \InvalidArgumentException(
                'Password is required.'
            );
        }

        if (!in_array($status, ['Active', 'Inactive'], true)) {
            throw new \InvalidArgumentException(
                'Invalid account status.'
            );
        }

        $this->db->execute(
            "
            INSERT INTO users
            (
                uuid,
                tenant_id,
                member_id,
                role_id,
                name,
                email,
                password,
                status,
                created_at,
                updated_at
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
                NOW(),
                NOW()
            )
            ",
            [
                $tenantId,
                $memberId,
                $roleId,
                trim($name),
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $status
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an account password.
     */
    public function updatePassword(
        int $tenantId,
        int $userId,
        string $password
    ): void {
        if ($password === '') {
            throw new \InvalidArgumentException(
                'Password is required.'
            );
        }

        $this->db->execute(
            "
            UPDATE users
            SET password = ?,
                updated_at = NOW()
            WHERE id = ?
              AND tenant_id = ?
            ",
            [
                password_hash($password, PASSWORD_DEFAULT),
                $userId,
                $tenantId
            ]
        );
    }

    /**
     * Update an account status.
     */
    public function updateStatus(
        int $tenantId,
        int $userId,
        string $status
    ): void {
        if (!in_array($status, ['Active', 'Inactive'], true)) {
            throw new \InvalidArgumentException(
                'Invalid account status.'
            );
        }

        $this->db->execute(
            "
            UPDATE users
            SET status = ?,
                updated_at = NOW()
            WHERE id = ?
              AND tenant_id = ?
            ",
            [
                $status,
                $userId,
                $tenantId
            ]
        );
    }

    /**
 * Check if an email exists within a tenant,
 * excluding a specific user.
 */
public function emailExistsExcept(
    int $tenantId,
    string $email,
    int $userId
): bool {
    $row = $this->db->fetch(
        "
        SELECT id
        FROM users
        WHERE tenant_id = ?
          AND email = ?
          AND id <> ?
        LIMIT 1
        ",
        [
            $tenantId,
            strtolower(trim($email)),
            $userId
        ]
    );

    return $row !== null;
}

/**
 * Update an existing user account.
 */
public function updateAccount(
    int $tenantId,
    int $userId,
    ?int $memberId,
    int $roleId,
    string $name,
    string $email,
    string $status = 'Active'
): void {
    $email = strtolower(trim($email));

    if ($email === '') {
        throw new \InvalidArgumentException(
            'Email address is required.'
        );
    }

    if (!in_array($status, ['Active', 'Inactive'], true)) {
        throw new \InvalidArgumentException(
            'Invalid account status.'
        );
    }

    $this->db->execute(
        "
        UPDATE users
        SET member_id = ?,
            role_id = ?,
            name = ?,
            email = ?,
            status = ?,
            updated_at = NOW()
        WHERE id = ?
          AND tenant_id = ?
        ",
        [
            $memberId,
            $roleId,
            trim($name),
            $email,
            $status,
            $userId,
            $tenantId
        ]
    );
}
    
}