<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;

final class AuthMiddleware
{
    public function handle(): void
    {
        /*
        |--------------------------------------------------------------------------
        | User must be logged in.
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {

            $this->redirectToLogin();

        }

        /*
        |--------------------------------------------------------------------------
        | User record must still exist.
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        if ($user === null) {

            Auth::logout();

            $this->redirectToLogin();

        }

        /*
        |--------------------------------------------------------------------------
        | User account must be active.
        |--------------------------------------------------------------------------
        */

        if (
            ($user['status'] ?? '') !== 'Active'
        ) {

            Auth::logout();

            $this->redirectToLogin();

        }

        /*
        |--------------------------------------------------------------------------
        | Every authenticated user must belong to a tenant.
        |--------------------------------------------------------------------------
        */

        if (
            empty($user['tenant_id'])
        ) {

            Auth::logout();

            $this->redirectToLogin();

        }
    }

    private function redirectToLogin(): never
    {
        header(
            'Location: '
            . config('app.base_path')
            . '/login'
        );

        exit;
    }
}