<?php

declare(strict_types=1);

namespace App\Modules\Staff\Services;

use App\Modules\Onboarding\Models\User;
use App\Modules\Staff\Models\Staff;
use RuntimeException;

final class StaffService
{
    private Staff $staff;

    private User $user;

    public function __construct()
    {
        $this->staff = new Staff();
        $this->user = new User();
    }

    /**
     * Get all staff accounts for a tenant.
     */
    public function list(int $tenantId): array
    {
        return $this->staff->all(
            $tenantId
        );
    }

    /**
     * Get data required for the create/edit form.
     */
    public function createData(int $tenantId): array
    {
        return [
            'members' => $this->staff->members(
                $tenantId
            ),

            'roles' => $this->staff->roles(
                $tenantId
            ),
        ];
    }

    /**
     * Get a staff account.
     */
    public function get(
        int $tenantId,
        int $id
    ): array {
        $staff = $this->staff->find(
            $tenantId,
            $id
        );

        if ($staff === null) {
            throw new RuntimeException(
                'Staff member not found.'
            );
        }

        return $staff;
    }

    /**
     * Create a staff account.
     */
    public function create(
        int $tenantId,
        array $data
    ): int {
        $this->validateCreate(
            $tenantId,
            $data
        );

        /*
        |--------------------------------------------------------------------------
        | Member
        |--------------------------------------------------------------------------
        */

        $memberId = null;

        if (!empty($data['member_id'])) {

            $memberId = (int) $data['member_id'];

            /*
             * Confirm the member is active and not deleted.
             */
            $member = $this->staff->findMember(
                $tenantId,
                $memberId
            );

            if ($member === null) {
                throw new RuntimeException(
                    'Selected member was not found or is not active.'
                );
            }

            /*
             * A member may have one normal Member account
             * and one Staff account, but may not have
             * multiple Staff accounts.
             */
            $existingStaff = $this->staff->findStaffByMember(
                $tenantId,
                $memberId
            );

            if ($existingStaff !== null) {
                throw new RuntimeException(
                    'This member already has a staff account. Please edit the existing staff account instead.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Staff Role
        |--------------------------------------------------------------------------
        */

        $roleId = (int) $data['role_id'];

        $role = $this->staff->findRole(
            $tenantId,
            $roleId
        );

        if ($role === null) {
            throw new RuntimeException(
                'Selected staff role was not found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Member role cannot be used as a staff role.
        |--------------------------------------------------------------------------
        */

        if (
            strtolower(
                (string) ($role['slug'] ?? '')
            ) === 'member'
        ) {
            throw new RuntimeException(
                'The Member role cannot be used for staff.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create User Account
        |--------------------------------------------------------------------------
        */

        return $this->user->createAccount(
            $tenantId,
            $memberId,
            $roleId,
            trim(
                (string) $data['name']
            ),
            strtolower(
                trim(
                    (string) $data['email']
                )
            ),
            (string) $data['password'],
            $data['status'] ?? 'Active'
        );
    }

    /**
     * Update a staff account.
     */
    public function update(
        int $tenantId,
        int $id,
        array $data
    ): void {
        /*
        |--------------------------------------------------------------------------
        | Confirm staff belongs to tenant.
        |--------------------------------------------------------------------------
        */

        $staff = $this->staff->find(
            $tenantId,
            $id
        );

        if ($staff === null) {
            throw new RuntimeException(
                'Staff member not found.'
            );
        }

        $this->validateUpdate(
            $tenantId,
            $id,
            $data
        );

        /*
        |--------------------------------------------------------------------------
        | Validate Staff Role
        |--------------------------------------------------------------------------
        */

        $roleId = (int) $data['role_id'];

        $role = $this->staff->findRole(
            $tenantId,
            $roleId
        );

        if ($role === null) {
            throw new RuntimeException(
                'Selected staff role was not found.'
            );
        }

        if (
            strtolower(
                (string) ($role['slug'] ?? '')
            ) === 'member'
        ) {
            throw new RuntimeException(
                'The Member role cannot be used for staff.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Member
        |--------------------------------------------------------------------------
        */

        $memberId = null;

        if (!empty($data['member_id'])) {

            $memberId = (int) $data['member_id'];

            /*
             * Confirm the selected member is active
             * and not deleted.
             */
            $member = $this->staff->findMember(
                $tenantId,
                $memberId
            );

            if ($member === null) {
                throw new RuntimeException(
                    'Selected member was not found or is not active.'
                );
            }

            /*
             * Prevent assigning a member who already
             * belongs to another Staff account.
             *
             * The current Staff account is excluded.
             */
            $existingStaff = $this->staff->findStaffByMemberExcept(
                $tenantId,
                $memberId,
                $id
            );

            if ($existingStaff !== null) {
                throw new RuntimeException(
                    'This member already has another staff account. Please edit that staff account instead.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Update User Account
        |--------------------------------------------------------------------------
        */

        $this->user->updateAccount(
            $tenantId,
            $id,
            $memberId,
            $roleId,
            trim(
                (string) $data['name']
            ),
            strtolower(
                trim(
                    (string) $data['email']
                )
            ),
            $data['status'] ?? 'Active'
        );

        /*
        |--------------------------------------------------------------------------
        | Optional Password Change
        |--------------------------------------------------------------------------
        */

        if (!empty($data['password'])) {

            $this->user->updatePassword(
                $tenantId,
                $id,
                (string) $data['password']
            );
        }
    }

    /**
     * Update staff account status.
     */
    public function updateStatus(
        int $tenantId,
        int $id,
        string $status
    ): void {
        if (
            !in_array(
                $status,
                ['Active', 'Inactive'],
                true
            )
        ) {
            throw new RuntimeException(
                'Invalid staff status.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Confirm staff belongs to tenant.
        |--------------------------------------------------------------------------
        */

        $staff = $this->staff->find(
            $tenantId,
            $id
        );

        if ($staff === null) {
            throw new RuntimeException(
                'Staff member not found.'
            );
        }

        $this->user->updateStatus(
            $tenantId,
            $id,
            $status
        );
    }

    /**
     * Validate staff creation.
     */
    private function validateCreate(
        int $tenantId,
        array $data
    ): void {
        $name = trim(
            (string) ($data['name'] ?? '')
        );

        $email = strtolower(
            trim(
                (string) ($data['email'] ?? '')
            )
        );

        $password = (string) (
            $data['password'] ?? ''
        );

        $confirmation = (string) (
            $data['password_confirmation'] ?? ''
        );

        /*
        |--------------------------------------------------------------------------
        | Name
        |--------------------------------------------------------------------------
        */

        if ($name === '') {
            throw new RuntimeException(
                'Staff name is required.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Email
        |--------------------------------------------------------------------------
        */

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new RuntimeException(
                'A valid email address is required.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        if ($password === '') {
            throw new RuntimeException(
                'Password is required.'
            );
        }

        if ($password !== $confirmation) {
            throw new RuntimeException(
                'Passwords do not match.'
            );
        }

        if (strlen($password) < 8) {
            throw new RuntimeException(
                'Password must be at least 8 characters.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Role
        |--------------------------------------------------------------------------
        */

        $roleId = (int) (
            $data['role_id'] ?? 0
        );

        if ($roleId <= 0) {
            throw new RuntimeException(
                'Staff role is required.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Tenant-scoped email uniqueness.
        |--------------------------------------------------------------------------
        */

        if (
            $this->user->emailExists(
                $tenantId,
                $email
            )
        ) {
            throw new RuntimeException(
                'This email address is already in use.'
            );
        }
    }

    /**
     * Validate staff update.
     */
    private function validateUpdate(
        int $tenantId,
        int $id,
        array $data
    ): void {
        $name = trim(
            (string) ($data['name'] ?? '')
        );

        $email = strtolower(
            trim(
                (string) ($data['email'] ?? '')
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Name
        |--------------------------------------------------------------------------
        */

        if ($name === '') {
            throw new RuntimeException(
                'Staff name is required.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Email
        |--------------------------------------------------------------------------
        */

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new RuntimeException(
                'A valid email address is required.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Role
        |--------------------------------------------------------------------------
        */

        $roleId = (int) (
            $data['role_id'] ?? 0
        );

        if ($roleId <= 0) {
            throw new RuntimeException(
                'Staff role is required.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Email uniqueness excluding current account.
        |--------------------------------------------------------------------------
        */

        if (
            $this->user->emailExistsExcept(
                $tenantId,
                $email,
                $id
            )
        ) {
            throw new RuntimeException(
                'This email address is already in use.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Optional Password
        |--------------------------------------------------------------------------
        */

        if (!empty($data['password'])) {

            $password = (string) $data['password'];

            $confirmation = (string) (
                $data['password_confirmation'] ?? ''
            );

            if ($password !== $confirmation) {
                throw new RuntimeException(
                    'Passwords do not match.'
                );
            }

            if (strlen($password) < 8) {
                throw new RuntimeException(
                    'Password must be at least 8 characters.'
                );
            }
        }
    }
}