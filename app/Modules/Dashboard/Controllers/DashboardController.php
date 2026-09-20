<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Controllers;

use App\Core\Auth;
use App\Core\Controller;

final class DashboardController extends Controller
{
public function index(): void
{
    $this->view(
        'Dashboard.index',
        [
            'title' => 'Dashboard',
            'user' => Auth::user(),
        ]
    );
}
}