<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Models;

use App\Core\Database;

final class TaxiDashboard
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function summary(int $tenantId): array
    {
        return [
            'businesses' => $this->count('taxi_vendors', $tenantId),
            'vehicles' => $this->count('taxi_vehicles', $tenantId),
            'drivers' => $this->count('taxi_drivers', $tenantId),
            'services' => $this->count('taxi_services', $tenantId),
            'bookings' => $this->count('taxi_bookings', $tenantId),
            'pending_bookings' => $this->db->fetch(
                "SELECT COUNT(*) AS total FROM taxi_bookings WHERE tenant_id = ? AND deleted_at IS NULL AND status IN ('Pending','Confirmed','Assigned','Driver Arrived','In Progress')",
                [$tenantId]
            )['total'] ?? 0,
        ];
    }

    private function count(string $table, int $tenantId): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) AS total FROM {$table} WHERE tenant_id = ? AND deleted_at IS NULL", [$tenantId]);
        return (int) ($row['total'] ?? 0);
    }
}
