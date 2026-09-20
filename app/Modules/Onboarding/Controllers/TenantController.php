<?php

declare(strict_types=1);

namespace App\Modules\Onboarding\Controllers;

use App\Core\Controller;
use App\Modules\Onboarding\Models\Tenant;
use Throwable;

final class TenantController extends Controller
{
    private Tenant $tenant;

    public function __construct()
    {
        $this->tenant = new Tenant();
    }

    /**
     * GET /
     *
     * Public ChurchOS tenant discovery page.
     */
    public function index(): void
    {
        $this->view(
            'Onboarding.tenant',
            [
                'title' => 'Find Your Church',
                'results' => [],
                'search' => '',
            ],
            'guest'
        );
    }

    /**
     * POST /
     *
     * Search for a church.
     */
    public function search(): void
    {
        $search = trim(
            $_POST['church'] ?? ''
        );

        if ($search === '') {
            $this->view(
                'Onboarding.tenant',
                [
                    'title' => 'Find Your Church',
                    'results' => [],
                    'search' => '',
                    'error' => 'Please enter your church name.',
                ],
                'guest'
            );

            return;
        }

        try {

            $results = $this->tenant->search(
                $search
            );

            $this->view(
                'Onboarding.tenant',
                [
                    'title' => 'Find Your Church',
                    'results' => $results,
                    'search' => $search,
                ],
                'guest'
            );

        } catch (Throwable $e) {

            $this->view(
                'Onboarding.tenant',
                [
                    'title' => 'Find Your Church',
                    'results' => [],
                    'search' => $search,
                    'error' => 'Unable to search for churches.',
                ],
                'guest'
            );
        }
    }
}