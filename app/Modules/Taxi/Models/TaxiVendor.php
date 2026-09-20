<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Models;

use App\Core\Database;

final class TaxiVendor
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function all(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT v.*, b.name AS business_name, b.slug AS business_slug, b.business_type
             FROM taxi_vendors v
             INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id
             WHERE v.tenant_id = ? AND v.deleted_at IS NULL AND b.deleted_at IS NULL
             ORDER BY v.id DESC",
            [$tenantId]
        );
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT v.*, b.name AS business_name, b.slug AS business_slug, b.business_type
             FROM taxi_vendors v
             INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id
             WHERE v.tenant_id = ? AND v.id = ? AND v.deleted_at IS NULL AND b.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $id]
        );
    }

    public function create(int $tenantId, array $data, int $userId): int
    {
        $this->db->execute(
            "INSERT INTO businesses
                (uuid, tenant_id, name, slug, business_type, phone, email, address, city, district, state, postal_code, status, created_by, updated_by)
             VALUES (UUID(), ?, ?, ?, 'taxi', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$tenantId, $data['business_name'], $data['business_slug'], $data['phone'], $data['email'], $data['address'], $data['city'], $data['district'], $data['state'], $data['postal_code'], $data['status'], $userId, $userId]
        );

        $businessId = (int) $this->db->lastInsertId();

        $this->db->execute(
            "INSERT INTO taxi_vendors
                (uuid, tenant_id, business_id, legal_name, description, status, created_by, updated_by)
             VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?)",
            [$tenantId, $businessId, $data['legal_name'], $data['description'], $data['status'], $userId, $userId]
        );

        return (int) $this->db->lastInsertId();
    }

    public function update(int $tenantId, int $id, array $data, int $userId): void
    {
        $record = $this->find($tenantId, $id);
        if (!$record) {
            return;
        }

        $this->db->execute(
            "UPDATE businesses
             SET name = ?, slug = ?, phone = ?, email = ?, address = ?, city = ?, district = ?, state = ?, postal_code = ?, status = ?, updated_by = ?
             WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL",
            [$data['business_name'], $data['business_slug'], $data['phone'], $data['email'], $data['address'], $data['city'], $data['district'], $data['state'], $data['postal_code'], $data['status'], $userId, $tenantId, (int) $record['business_id']]
        );

        $this->db->execute(
            "UPDATE taxi_vendors
             SET legal_name = ?, description = ?, status = ?, updated_by = ?
             WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL",
            [$data['legal_name'], $data['description'], $data['status'], $userId, $tenantId, $id]
        );
    }

    public function softDelete(int $tenantId, int $id, int $userId): void
    {
        $record = $this->find($tenantId, $id);
        if (!$record) {
            return;
        }

        $this->db->execute(
            "UPDATE taxi_vendors SET deleted_at = NOW(), updated_by = ? WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL",
            [$userId, $tenantId, $id]
        );

        $this->db->execute(
            "UPDATE businesses SET deleted_at = NOW(), updated_by = ? WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL",
            [$userId, $tenantId, (int) $record['business_id']]
        );
    }

    public function business_name_exists(int $tenantId, string $value, ?int $ignoreId = null): bool
    {
        $sql = "SELECT v.id FROM taxi_vendors v INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id WHERE v.tenant_id = ? AND b.name = ? AND v.deleted_at IS NULL AND b.deleted_at IS NULL";
        $params = [$tenantId, $value];
        if ($ignoreId !== null) {
            $sql .= " AND v.id <> ?";
            $params[] = $ignoreId;
        }
        return $this->db->fetch($sql . " LIMIT 1", $params) !== null;
    }

    public function slug_exists(int $tenantId, string $slug, ?int $ignoreId = null): bool
    {
        $sql = "SELECT v.id FROM taxi_vendors v INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id WHERE v.tenant_id = ? AND b.slug = ? AND v.deleted_at IS NULL AND b.deleted_at IS NULL";
        $params = [$tenantId, $slug];
        if ($ignoreId !== null) {
            $sql .= " AND v.id <> ?";
            $params[] = $ignoreId;
        }
        return $this->db->fetch($sql . " LIMIT 1", $params) !== null;
    }
}
