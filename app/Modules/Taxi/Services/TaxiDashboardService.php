<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Services;

use App\Modules\Taxi\Models\TaxiDashboard;

final class TaxiDashboardService
{
    private TaxiDashboard $dashboard;

    public function __construct()
    {
        $this->dashboard = new TaxiDashboard();
    }

    public function summary(int $tenantId): array
    {
        return $this->dashboard->summary($tenantId);
    }
}
