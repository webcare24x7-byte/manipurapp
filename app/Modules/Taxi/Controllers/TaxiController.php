<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Taxi\Services\TaxiDashboardService;
use RuntimeException;

final class TaxiController extends Controller
{
    private TaxiDashboardService $dashboard;
    private Authorization $authorization;

    public function __construct()
    {
        $this->dashboard = new TaxiDashboardService();
        $this->authorization = new Authorization();
    }

    public function index(): void
    {
        try {
            $this->authorization->authorize('taxi.view');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirect('/dashboard');
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $this->view(
            'Taxi::Taxi.index',
            [
                'title' => 'Taxi',
                'summary' => $this->dashboard->summary($tenantId),
            ]
        );
    }
}
