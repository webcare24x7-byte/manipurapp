<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Models;

use App\Core\Database;

final class TaxiVehicle
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function all(int $tenantId): array
    {
        return $this->db->fetchAll(
            "
            SELECT t.*, b.name AS vendor_name FROM taxi_vehicles t INNER JOIN taxi_vendors v ON v.id = t.vendor_id AND v.tenant_id = t.tenant_id INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id WHERE t.tenant_id = ? AND t.deleted_at IS NULL ORDER BY t.id DESC
            ",
            [$tenantId]
        );
    }

    public function find(int $tenantId, int $id): ?array
    {
        $sql = "SELECT t.*, b.name AS vendor_name FROM taxi_vehicles t INNER JOIN taxi_vendors v ON v.id = t.vendor_id AND v.tenant_id = t.tenant_id INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id WHERE t.tenant_id = ? AND t.deleted_at IS NULL AND t.id = ? LIMIT 1";
        $params = [$tenantId, $id];
        return $this->db->fetch($sql, $params);
    }

    public function create(int $tenantId, array $data, int $userId): int
    {
        $this->db->execute(
            "
            INSERT INTO taxi_vehicles
            (uuid, tenant_id, vendor_id,
                registration_no,
                vehicle_type,
                make,
                model,
                model_year,
                color,
                seating_capacity,
                photo_path,
                status, created_by, updated_by)
            VALUES (UUID(), ?, ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?, ?, ?)
            ",
            [$tenantId, $data['vendor_id'], $data['registration_no'], $data['vehicle_type'], $data['make'], $data['model'], $data['model_year'], $data['color'], $data['seating_capacity'], $data['photo_path'], $data['status'], $userId, $userId]
        );

        return (int) $this->db->lastInsertId();
    }

    public function update(int $tenantId, int $id, array $data, int $userId): void
    {
        $this->db->execute(
            "
            UPDATE taxi_vehicles
            SET
                vendor_id = ?,
                registration_no = ?,
                vehicle_type = ?,
                make = ?,
                model = ?,
                model_year = ?,
                color = ?,
                seating_capacity = ?,
                photo_path = ?,
                status = ?,
                updated_by = ?
            WHERE tenant_id = ?
              AND id = ?
              AND deleted_at IS NULL
            ",
            [$data['vendor_id'], $data['registration_no'], $data['vehicle_type'], $data['make'], $data['model'], $data['model_year'], $data['color'], $data['seating_capacity'], $data['photo_path'], $data['status'], $userId, $tenantId, $id]
        );
    }

    public function softDelete(int $tenantId, int $id, int $userId): void
    {
        $this->db->execute(
            "UPDATE taxi_vehicles SET deleted_at = NOW(), updated_by = ? WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL",
            [$userId, $tenantId, $id]
        );
    }

    public function registration_no_exists(int $tenantId, string $value, ?int $ignoreId = null): bool
    {
        $sql = "SELECT id FROM taxi_vehicles WHERE tenant_id = ? AND registration_no = ? AND deleted_at IS NULL";
        $params = [$tenantId, $value];

        if ($ignoreId !== null) {
            $sql .= " AND id <> ?";
            $params[] = $ignoreId;
        }

        $sql .= " LIMIT 1";

        return $this->db->fetch($sql, $params) !== null;
    }

}
