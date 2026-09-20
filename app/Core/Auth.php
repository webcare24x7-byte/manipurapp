<?php

declare(strict_types=1);

namespace App\Core;

use App\Modules\Onboarding\Models\User;

final class Auth
{

    private static ?array $cachedUser = null;

    public static function login(int $userId): void
    {
        session_regenerate_id(true);

        self::$cachedUser = null;

        $_SESSION['auth'] = [
            'user_id' => $userId,
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION['auth']);

        self::$cachedUser = null;

        if (session_status() === PHP_SESSION_ACTIVE) {

            session_regenerate_id(true);

        }
    }

    public static function check(): bool
    {
        return isset($_SESSION['auth']['user_id']);
    }

    public static function id(): ?int
    {
        return $_SESSION['auth']['user_id'] ?? null;
    }

    public static function user(): ?array
    {
        if (!self::check()) {

            return null;

        }

        /*
        |--------------------------------------------------------------------------
        | Return cached user if already loaded.
        |--------------------------------------------------------------------------
        */

        if (self::$cachedUser !== null) {

            return self::$cachedUser;

        }

        /*
        |--------------------------------------------------------------------------
        | Load authenticated user.
        |--------------------------------------------------------------------------
        */

        $model = new User();

        $user = $model->findById(
            self::id()
        );

        /*
        |--------------------------------------------------------------------------
        | User no longer exists.
        |--------------------------------------------------------------------------
        */

        if ($user === null) {

            self::logout();

            return null;

        }

        /*
        |--------------------------------------------------------------------------
        | Cache for this request.
        |--------------------------------------------------------------------------
        */

        self::$cachedUser = $user;

        return self::$cachedUser;
    }

    public static function tenantId(): ?int
    {
        $user = self::user();

        if ($user === null) {

            return null;

        }

        return (int) $user['tenant_id'];
    }

    public static function roleId(): ?int
    {
        $user = self::user();

        if ($user === null) {

            return null;

        }

        return (int) $user['role_id'];
    }

    public static function name(): ?string
    {
        $user = self::user();

        return $user['name'] ?? null;
    }

    public static function email(): ?string
    {
        $user = self::user();

        return $user['email'] ?? null;
    }
}