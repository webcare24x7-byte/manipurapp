<?php

declare(strict_types=1);

namespace App\Modules\Onboarding\Services;

use App\Core\Auth;
use App\Modules\Onboarding\Models\Tenant;
use App\Modules\Onboarding\Models\User;
use RuntimeException;

final class AuthenticationService
{
    private User $user;

    private Tenant $tenant;

    public function __construct()
    {
        $this->user = new User();
        $this->tenant = new Tenant();
    }

    public function login(array $data): void
    {
        $tenantSlug = strtolower(
            trim($data['tenant_slug'] ?? '')
        );

        $email = strtolower(
            trim($data['email'] ?? '')
        );

        $password = $data['password'] ?? '';

        if (
            $tenantSlug === ''
            || $email === ''
            || $password === ''
        ) {
            throw new RuntimeException(
                'Email and password are required.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve tenant from URL slug
        |--------------------------------------------------------------------------
        */

        $tenant = $this->tenant->findBySlug(
            $tenantSlug
        );

        if ($tenant === null) {
            throw new RuntimeException(
                'Invalid email or password.'
            );
        }

        $tenantId = (int) $tenant['id'];

        /*
        |--------------------------------------------------------------------------
        | Find user inside this tenant
        |--------------------------------------------------------------------------
        */

        $user = $this->user->findByEmail(
            $tenantId,
            $email
        );

        if ($user === null) {
            throw new RuntimeException(
                'Invalid email or password.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Verify password
        |--------------------------------------------------------------------------
        */

        if (
            !password_verify(
                $password,
                $user['password']
            )
        ) {
            throw new RuntimeException(
                'Invalid email or password.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Authenticate
        |--------------------------------------------------------------------------
        */

        Auth::login(
            (int) $user['id']
        );
    }
}