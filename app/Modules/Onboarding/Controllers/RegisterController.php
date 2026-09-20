<?php

declare(strict_types=1);

namespace App\Modules\Onboarding\Controllers;

use App\Core\Controller;

final class RegisterController extends Controller
{
    /**
     * Show registration page.
     */
    public function index(): void
    {
        $this->view(
            'Onboarding.register',
            [
                'title' => 'Register as Business Owner / Vendor'
            ],
            'guest'
        );
    }

    /**
     * Handle registration.
     */
    public function store(): void
    {
        $service = new \App\Modules\Onboarding\Services\RegistrationService();

        try {

            $service->register($_POST);

            header('Location: ' . config('app.base_path') . '/dashboard');
            exit;

        } catch (\Throwable $e) {

            echo $e->getMessage();

        }
    }
}