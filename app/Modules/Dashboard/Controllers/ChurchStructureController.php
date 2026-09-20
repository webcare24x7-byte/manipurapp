<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Controllers;

use App\Core\Controller;

final class ChurchStructureController extends Controller
{
    public function index(): void
    {
        $this->view(
            'Dashboard::ChurchStructure.index',
            [
                'title' => 'Church Structure',
            ]
        );
    }
}