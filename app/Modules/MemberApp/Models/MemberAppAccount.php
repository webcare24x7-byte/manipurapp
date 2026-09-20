<?php

declare(strict_types=1);

namespace App\Modules\MemberApp\Models;

use App\Core\Database;
use App\Modules\Members\Models\Member;
use App\Modules\Onboarding\Models\Role;
use App\Modules\Onboarding\Models\User;
use RuntimeException;

/**
 * MemberApp-specific account persistence.
 *
 * This model deliberately reuses the existing members, users and roles
 * tables. It does not introduce a second authentication schema.
 */
final class MemberAppAccount
{
    private Database $db;
    private Member $members;
    private User $users;
    private Role $roles;

    public function __construct()
    {
        $this->db = app()->get('db');
        $this->members = new Member();
        $this->users = new User();
        $this->roles = new Role();
    }

    public function emailExists(int $tenantId, string $email): bool
    {
        $email = strtolower(trim($email));

        $row = $this->db->fetch(
            'SELECT id FROM users WHERE tenant_id = ? AND email = ? LIMIT 1',
            [$tenantId, $email]
        );

        return $row !== null;
    }

    public function findMemberLogin(int $tenantId, string $email): ?array
    {
        $email = strtolower(trim($email));

        return $this->db->fetch(
            '
                SELECT
                    u.id,
                    u.uuid,
                    u.tenant_id,
                    u.member_id,
                    u.role_id,
                    u.name,
                    u.email,
                    u.password,
                    u.status,
                    r.slug AS role_slug,
                    r.name AS role_name,
                    m.first_name,
                    m.middle_name,
                    m.last_name,
                    m.phone,
                    m.status AS member_status
                FROM users u
                INNER JOIN roles r
                    ON r.id = u.role_id
                INNER JOIN members m
                    ON m.id = u.member_id
                   AND m.tenant_id = u.tenant_id
                   AND m.deleted_at IS NULL
                WHERE u.tenant_id = ?
                  AND u.email = ?
                  AND u.status = \'Active\'
                  AND r.slug = \'member\'
                LIMIT 1
            ',
            [$tenantId, $email]
        );
    }

    /**
     * Create a member and its active Member login atomically.
     */
    public function register(int $tenantId, array $data): array
    {
        $this->db->beginTransaction();

        try {
            if ($this->emailExists($tenantId, $data['email'])) {
                throw new RuntimeException('An account with this email already exists.');
            }

            $role = $this->roles->findBySlug($tenantId, 'member');

            if ($role === null) {
                throw new RuntimeException('Member role is not configured for this tenant.');
            }

            $memberId = $this->createMemberThroughExistingService($tenantId, $data);

            $name = trim(
                $data['first_name'] . ' ' . $data['last_name']
            );

            $userId = $this->users->createAccount(
                $tenantId,
                $memberId,
                (int) $role['id'],
                $name,
                $data['email'],
                $data['password'],
                'Active'
            );

            $this->db->commit();

            return [
                'user_id' => $userId,
                'member_id' => $memberId,
                'tenant_id' => $tenantId,
                'name' => $name,
                'email' => $data['email'],
            ];
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Reuse the existing Members business rules/number generation.
     * create_user is deliberately false because MemberApp creates its
     * own Active login account in the same transaction.
     */
    private function createMemberThroughExistingService(
        int $tenantId,
        array $data
    ): int {
        $service = new \App\Modules\Members\Services\MemberService();

        return $service->create([
            'tenant_id' => $tenantId,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'status' => 'Active',
            'membership_date' => date('Y-m-d'),
            'create_user' => false,
            'created_by' => null,
        ]);
    }
}
