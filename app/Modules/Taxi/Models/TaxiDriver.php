<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Models;

use App\Core\Database;

final class TaxiDriver
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
            SELECT t.*, b.name AS vendor_name, b.name AS business_name, u.name AS user_name FROM taxi_drivers t INNER JOIN taxi_vendors v ON v.id = t.vendor_id AND v.tenant_id = t.tenant_id INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id LEFT JOIN users u ON u.id = t.user_id AND u.tenant_id = t.tenant_id WHERE t.tenant_id = ? AND t.deleted_at IS NULL ORDER BY t.id DESC
            ",
            [$tenantId]
        );
    }

    public function find(int $tenantId, int $id): ?array
    {
        $sql = "SELECT t.*, b.name AS vendor_name, b.name AS business_name, u.name AS user_name FROM taxi_drivers t INNER JOIN taxi_vendors v ON v.id = t.vendor_id AND v.tenant_id = t.tenant_id INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id LEFT JOIN users u ON u.id = t.user_id AND u.tenant_id = t.tenant_id WHERE t.tenant_id = ? AND t.deleted_at IS NULL AND t.id = ? LIMIT 1";
        $params = [$tenantId, $id];
        return $this->db->fetch($sql, $params);
    }

    public function create(int $tenantId, array $data, int $userId): int
    {
        $this->db->execute(
            "
            INSERT INTO taxi_drivers
            (uuid, tenant_id, vendor_id,
                user_id,
                name,
                phone,
                license_no,
                license_expiry,
                status,
                availability,
                photo_path, created_by, updated_by)
            VALUES (UUID(), ?, ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?, ?, ?)
            ",
            [$tenantId, $data['vendor_id'], $data['user_id'], $data['name'], $data['phone'], $data['license_no'], $data['license_expiry'], $data['status'], $data['availability'], $data['photo_path'], $userId, $userId]
        );

        return (int) $this->db->lastInsertId();
    }

    public function update(int $tenantId, int $id, array $data, int $userId): void
    {
        $this->db->execute(
            "
            UPDATE taxi_drivers
            SET
                vendor_id = ?,
                user_id = ?,
                name = ?,
                phone = ?,
                license_no = ?,
                license_expiry = ?,
                status = ?,
                availability = ?,
                photo_path = ?,
                updated_by = ?
            WHERE tenant_id = ?
              AND id = ?
              AND deleted_at IS NULL
            ",
            [$data['vendor_id'], $data['user_id'], $data['name'], $data['phone'], $data['license_no'], $data['license_expiry'], $data['status'], $data['availability'], $data['photo_path'], $userId, $tenantId, $id]
        );
    }

    public function softDelete(int $tenantId, int $id, int $userId): void
    {
        $this->db->execute(
            "UPDATE taxi_drivers SET deleted_at = NOW(), updated_by = ? WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL",
            [$userId, $tenantId, $id]
        );
    }

    public function license_no_exists(int $tenantId, string $value, ?int $ignoreId = null): bool
    {
        $sql = "SELECT id FROM taxi_drivers WHERE tenant_id = ? AND license_no = ? AND deleted_at IS NULL";
        $params = [$tenantId, $value];

        if ($ignoreId !== null) {
            $sql .= " AND id <> ?";
            $params[] = $ignoreId;
        }

        $sql .= " LIMIT 1";

        return $this->db->fetch($sql, $params) !== null;
    }

    public function users(int $tenantId): array
    {
        return $this->db->fetchAll(
            "
            SELECT u.id, u.name, u.email, u.phone, r.name AS role_name
            FROM users u
            INNER JOIN roles r ON r.id = u.role_id AND r.tenant_id = u.tenant_id
            WHERE u.tenant_id = ?
              AND u.status = 'Active'
            ORDER BY u.name
            ",
            [$tenantId]
        );
    }

    public function findByUserId(int $tenantId, int $userId): ?array
    {
        return $this->db->fetch(
            "SELECT t.*, b.name AS business_name
             FROM taxi_drivers t
             INNER JOIN taxi_vendors v
               ON v.id = t.vendor_id AND v.tenant_id = t.tenant_id
             INNER JOIN businesses b
               ON b.id = v.business_id AND b.tenant_id = v.tenant_id
             WHERE t.tenant_id = ?
               AND t.user_id = ?
               AND t.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $userId]
        );
    }

    public function userBelongsToTenant(int $tenantId, int $userId): bool
    {
        return $this->db->fetch(
            "SELECT id FROM users WHERE id = ? AND tenant_id = ? LIMIT 1",
            [$userId, $tenantId]
        ) !== null;
    }
}
