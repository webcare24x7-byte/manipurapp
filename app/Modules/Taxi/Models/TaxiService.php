<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Models;

use App\Core\Database;

final class TaxiService
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function all(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT t.*, b.name AS vendor_name
             FROM taxi_services t
             INNER JOIN taxi_vendors v ON v.id = t.vendor_id AND v.tenant_id = t.tenant_id
             INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id
             WHERE t.tenant_id = ? AND t.deleted_at IS NULL
             ORDER BY t.id DESC",
            [$tenantId]
        );
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT t.*, b.name AS vendor_name
             FROM taxi_services t
             INNER JOIN taxi_vendors v ON v.id = t.vendor_id AND v.tenant_id = t.tenant_id
             INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id
             WHERE t.tenant_id = ? AND t.deleted_at IS NULL AND t.id = ?
             LIMIT 1",
            [$tenantId, $id]
        );
    }

    public function create(int $tenantId, array $data, int $userId): int
    {
        $this->db->execute(
            "INSERT INTO taxi_services
                (uuid, tenant_id, vendor_id,
                 name, code, service_type, description,
                 pricing_mode, base_fare, per_km, per_minute, minimum_fare, included_km,
                 daily_rate, extra_km_rate,
                 status, created_by, updated_by)
             VALUES
                (UUID(), ?, ?,
                 ?, ?, ?, ?,
                 ?, ?, ?, ?, ?,
                 ?, ?,
                 ?, ?, ?)",
            [
                $tenantId,
                $data['vendor_id'],
                $data['name'],
                $data['code'],
                $data['service_type'],
                $data['description'],
                $data['pricing_mode'],
                $data['base_fare'],
                $data['per_km'],
                $data['per_minute'],
                $data['minimum_fare'],
                $data['included_km'],
                $data['daily_rate'],
                $data['extra_km_rate'],
                $data['status'],
                $userId,
                $userId,
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    public function update(int $tenantId, int $id, array $data, int $userId): void
    {
        $this->db->execute(
            "UPDATE taxi_services
             SET
                vendor_id = ?,
                name = ?,
                code = ?,
                service_type = ?,
                description = ?,
                pricing_mode = ?,
                base_fare = ?,
                per_km = ?,
                per_minute = ?,
                minimum_fare = ?,
                included_km = ?,
                daily_rate = ?,
                extra_km_rate = ?,
                status = ?,
                updated_by = ?
             WHERE tenant_id = ?
               AND id = ?
               AND deleted_at IS NULL",
            [
                $data['vendor_id'],
                $data['name'],
                $data['code'],
                $data['service_type'],
                $data['description'],
                $data['pricing_mode'],
                $data['base_fare'],
                $data['per_km'],
                $data['per_minute'],
                $data['minimum_fare'],
                $data['included_km'],
                $data['daily_rate'],
                $data['extra_km_rate'],
                $data['status'],
                $userId,
                $tenantId,
                $id,
            ]
        );
    }

    public function softDelete(int $tenantId, int $id, int $userId): void
    {
        $this->db->execute(
            "UPDATE taxi_services SET deleted_at = NOW(), updated_by = ?
             WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL",
            [$userId, $tenantId, $id]
        );
    }

    public function code_exists(int $tenantId, string $value, ?int $ignoreId = null): bool
    {
        $sql = "SELECT id FROM taxi_services WHERE tenant_id = ? AND code = ? AND deleted_at IS NULL";
        $params = [$tenantId, $value];

        if ($ignoreId !== null) {
            $sql .= " AND id <> ?";
            $params[] = $ignoreId;
        }

        return $this->db->fetch($sql . " LIMIT 1", $params) !== null;
    }
}
