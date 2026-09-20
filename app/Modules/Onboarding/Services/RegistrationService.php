<?php

declare(strict_types=1);

namespace App\Modules\Onboarding\Services;

use App\Core\Database;
use App\Modules\Onboarding\Models\Role;
use App\Modules\Onboarding\Models\Tenant;
use App\Modules\Onboarding\Models\User;
use RuntimeException;
use Throwable;

final class RegistrationService
{
    private Database $db;

    private Tenant $tenant;

    private Role $role;

    private User $user;

    public function __construct()
    {
        $this->db = app()->get('db');

        $this->tenant = new Tenant();

        $this->role = new Role();

        $this->user = new User();
    }

    public function register(array $data): void
    {
        $this->validate($data);

        $this->db->beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Create tenant
            |--------------------------------------------------------------------------
            */

            $tenantId = $this->tenant->create(
                $data
            );

            /*
            |--------------------------------------------------------------------------
            | Create default roles
            |--------------------------------------------------------------------------
            */

            $roles = $this->role->createDefaults(
                $tenantId
            );

            /*
            |--------------------------------------------------------------------------
            | Assign default administrator permissions
            |--------------------------------------------------------------------------
            */

            $this->role->assignDefaultPermissions(
                $roles['church_admin']
            );

            /*
            |--------------------------------------------------------------------------
            | Create administrator
            |--------------------------------------------------------------------------
            */

            $this->user->createAdministrator(
                $tenantId,
                $roles['church_admin'],
                $data
            );

            $this->db->commit();

            /*
            |--------------------------------------------------------------------------
            | Find newly-created administrator
            |
            | IMPORTANT:
            | Authentication is tenant-scoped.
            |--------------------------------------------------------------------------
            */

            $user = $this->user->findByEmail(
                $tenantId,
                $data['email']
            );

            if (!$user) {
                throw new RuntimeException(
                    'Unable to log in newly created user.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Authenticate administrator
            |--------------------------------------------------------------------------
            */

            \App\Core\Auth::login(
                (int) $user['id']
            );

        } catch (Throwable $e) {

            $this->db->rollBack();

            throw $e;
        }
    }

    private function validate(array $data): void
    {
        $required = [
            'church_name',
            'slug',
            'admin_name',
            'email',
            'password',
            'password_confirmation'
        ];

        foreach ($required as $field) {

            if (
                !isset($data[$field])
                || trim((string) $data[$field]) === ''
            ) {
                throw new RuntimeException(
                    "{$field} is required."
                );
            }
        }

        if (
            $data['password']
            !== $data['password_confirmation']
        ) {
            throw new RuntimeException(
                'Passwords do not match.'
            );
        }

        if (
            !filter_var(
                $data['email'],
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new RuntimeException(
                'Invalid email address.'
            );
        }

        if (
            $this->tenant->slugExists(
                $data['slug']
            )
        ) {
            throw new RuntimeException(
                'Church slug already exists.'
            );
        }
    }
}