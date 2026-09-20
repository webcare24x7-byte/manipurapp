<?php

declare(strict_types=1);

namespace App\Modules\Onboarding\Controllers;

use App\Core\Controller;

final class OnboardingController extends Controller
{
    public function index(): void
    {
        $this->view('Onboarding.index', [
            'title' => 'Onboarding'
        ]);
    }
}