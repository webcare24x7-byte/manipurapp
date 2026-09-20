<?php

declare(strict_types=1);

namespace App\Modules\Landing\Controllers;

use App\Core\Auth;
use App\Core\Controller;

final class LandingController extends Controller
{
    public function index(): void
    {
        if (Auth::check()) {

            header(
                'Location: ' .
                config('app.base_path') .
                '/dashboard'
            );

            exit;
        }

        $this->view(
            'Landing.index',
            [
                'title' => 'ManipurApp',
            ],
            'website'
        );
    }
}