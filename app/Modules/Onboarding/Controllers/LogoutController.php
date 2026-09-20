<?php

declare(strict_types=1);

namespace App\Modules\Onboarding\Controllers;

use App\Core\Auth;

final class LogoutController
{
    public function index(): void
    {
        Auth::logout();

        session_regenerate_id(true);

        header('Location: ' . config('app.base_path') . '/login');

        exit;
    }
}