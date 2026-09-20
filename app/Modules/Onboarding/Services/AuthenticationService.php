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

    /**
     * Authenticate a user for a specific tenant.
     *
     * Returns:
     *
     * [
     *     'status' => 'authenticated'
     * ]
     *
     * or:
     *
     * [
     *     'status' => 'member_only'
     * ]
     *
     * Member-only accounts are intentionally NOT
     * authenticated into the ChurchOS admin session.
     */
    public function login(array $data): array
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
        | Find active user inside this tenant
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
        | Determine account role
        |--------------------------------------------------------------------------
        */

        $roleSlug = strtolower(
            trim(
                (string) ($user['role_slug'] ?? '')
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Member-only account
        |--------------------------------------------------------------------------
        |
        | A Member account must not create an Admin/Staff
        | authentication session.
        |
        | MemberApp handles the Member PWA.
        |
        */

        if ($roleSlug === 'member') {

            /*
             * If this Member also has another active
             * non-member account (Staff/Admin), they
             * are allowed to enter the Admin application.
             */

            $memberId = (int) (
                $user['member_id'] ?? 0
            );

            if ($memberId > 0) {

                $accounts = $this->user->findByMember(
                    $tenantId,
                    $memberId
                );

                foreach ($accounts as $account) {

                    if (
                        (int) $account['id'] ===
                        (int) $user['id']
                    ) {
                        continue;
                    }

                    if (
                        strtolower(
                            trim(
                                (string) (
                                    $account['role_slug'] ?? ''
                                )
                            )
                        ) !== 'member'
                        && ($account['status'] ?? '') === 'Active'
                    ) {
                        /*
                         * This person has a separate
                         * Staff/Admin account.
                         *
                         * For now we allow the login
                         * to enter the Admin application.
                         *
                         * Portal chooser can be added later.
                         */

                        Auth::login(
                            (int) $user['id']
                        );

                        return [
                            'status' => 'authenticated',
                        ];
                    }
                }
            }

            /*
             * Member only.
             *
             * Do NOT call Auth::login().
             */

            return [
                'status' => 'member_only',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Staff / Administrator
        |--------------------------------------------------------------------------
        |
        | Any active non-Member role is allowed to
        | authenticate into the ChurchOS management
        | application.
        |
        */

        Auth::login(
            (int) $user['id']
        );

        return [
            'status' => 'authenticated',
        ];
    }
}