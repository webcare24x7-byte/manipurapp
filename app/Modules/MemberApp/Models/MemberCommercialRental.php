<?php
declare(strict_types=1);

namespace App\Modules\MemberApp\Models;

use App\Core\Database;

final class MemberCommercialRental
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function categories(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT id, name, slug
             FROM commercial_rental_categories
             WHERE tenant_id = ? AND status = 'Active' AND deleted_at IS NULL
             ORDER BY name ASC",
            [$tenantId]
        );
    }

    public function vehicles(
        int $tenantId,
        ?int $categoryId = null,
        ?string $operatorRequired = null,
        ?float $lat = null,
        ?float $lng = null
    ): array {
        $sql = "SELECT
                    v.id, v.uuid, v.provider_id, v.category_id,
                    v.name, v.make, v.model, v.model_year,
                    v.fuel_type, v.ownership_type, v.engine_power,
                    v.body_type, v.seating_capacity, v.condition_status,
                    v.registration_no, v.capacity, v.operator_included,
                    v.operator_name, v.operator_phone, v.operator_experience,
                    v.rate_type, v.rate, v.minimum_rental,
                    v.description, v.service_notes, v.photo_path,
                    v.status, v.availability,
                    c.name AS category_name,
                    b.name AS provider_name,
                    p.address AS provider_address,
                    p.city AS provider_city,
                    p.district AS provider_district,
                    p.state AS provider_state,
                    p.latitude AS provider_latitude,
                    p.longitude AS provider_longitude,
                    p.service_areas AS provider_service_areas
                FROM commercial_rental_vehicles v
                INNER JOIN commercial_rental_providers p
                    ON p.id = v.provider_id AND p.tenant_id = v.tenant_id
                INNER JOIN businesses b
                    ON b.id = p.business_id AND b.tenant_id = p.tenant_id
                LEFT JOIN commercial_rental_categories c
                    ON c.id = v.category_id AND c.tenant_id = v.tenant_id
                WHERE v.tenant_id = ?
                  AND v.status = 'Active'
                  AND v.availability = 'Available'
                  AND v.deleted_at IS NULL
                  AND p.status = 'Active'
                  AND p.deleted_at IS NULL
                  AND b.status = 'Active'
                  AND b.deleted_at IS NULL";
        $params = [$tenantId];

        if ($categoryId !== null && $categoryId > 0) {
            $sql .= " AND v.category_id = ?";
            $params[] = $categoryId;
        }

        if ($operatorRequired === '1') {
            $sql .= " AND v.operator_included = 1";
        }

        $sql .= " ORDER BY v.id DESC";

        $rows = $this->db->fetchAll($sql, $params);

        if ($lat === null || $lng === null) {
            foreach ($rows as &$row) {
                $row['distance_km'] = null;
            }
            unset($row);
            return $rows;
        }

        foreach ($rows as &$row) {
            $row['distance_km'] = $this->distanceKm(
                $lat,
                $lng,
                $row['provider_latitude'] !== null ? (float)$row['provider_latitude'] : null,
                $row['provider_longitude'] !== null ? (float)$row['provider_longitude'] : null
            );
        }
        unset($row);

        usort($rows, static function (array $a, array $b): int {
            $da = $a['distance_km'];
            $db = $b['distance_km'];
            if ($da === null && $db === null) {
                return (int)$a['id'] <=> (int)$b['id'];
            }
            if ($da === null) return 1;
            if ($db === null) return -1;
            return $da <=> $db;
        });

        return $rows;
    }

    public function vehicle(int $tenantId, int $id): ?array
    {
        $rows = $this->vehicles($tenantId);
        foreach ($rows as $row) {
            if ((int)$row['id'] === $id) {
                return $row;
            }
        }
        return null;
    }

    public function member(int $tenantId, int $memberId): ?array
    {
        return $this->db->fetch(
            "SELECT id, first_name, middle_name, last_name, phone, email
             FROM members
             WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $memberId]
        );
    }

    public function createRequest(int $tenantId, array $data, int $userId): int
    {
        $latest = $this->db->fetch(
            "SELECT id FROM commercial_rental_requests
             WHERE tenant_id = ?
             ORDER BY id DESC LIMIT 1",
            [$tenantId]
        );
        $next = ((int)($latest['id'] ?? 0)) + 1;
        $requestNo = 'CR-' . date('Ym') . '-' . str_pad((string)$next, 5, '0', STR_PAD_LEFT);

        $this->db->execute(
            "INSERT INTO commercial_rental_requests
                (uuid, tenant_id, member_id, provider_id, vehicle_id,
                 request_no, customer_name, customer_phone, purpose,
                 pickup_address, pickup_lat, pickup_lng,
                 destination_address, destination_lat, destination_lng,
                 start_at, end_at, duration_text, operator_required,
                 status, quoted_amount, final_amount, customer_notes,
                 admin_notes, created_by, updated_by)
             VALUES
                (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $tenantId,
                $data['member_id'],
                $data['provider_id'],
                $data['vehicle_id'],
                $requestNo,
                $data['customer_name'],
                $data['customer_phone'],
                $data['purpose'],
                $data['pickup_address'],
                $data['pickup_lat'],
                $data['pickup_lng'],
                $data['destination_address'],
                $data['destination_lat'],
                $data['destination_lng'],
                $data['start_at'],
                $data['end_at'],
                $data['duration_text'],
                $data['operator_required'],
                'REQUESTED',
                null,
                null,
                $data['customer_notes'],
                null,
                $userId,
                $userId
            ]
        );

        return (int)$this->db->lastInsertId();
    }

    public function requestForMember(int $tenantId, int $memberId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT
                r.*,
                b.name AS provider_name,
                v.name AS vehicle_name,
                v.make, v.model, v.photo_path,
                v.rate_type, v.rate, v.minimum_rental,
                c.name AS category_name
             FROM commercial_rental_requests r
             LEFT JOIN commercial_rental_providers p
               ON p.id = r.provider_id AND p.tenant_id = r.tenant_id
             LEFT JOIN businesses b
               ON b.id = p.business_id AND b.tenant_id = p.tenant_id
             LEFT JOIN commercial_rental_vehicles v
               ON v.id = r.vehicle_id AND v.tenant_id = r.tenant_id
             LEFT JOIN commercial_rental_categories c
               ON c.id = v.category_id AND c.tenant_id = v.tenant_id
             WHERE r.tenant_id = ?
               AND r.member_id = ?
               AND r.id = ?
               AND r.deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $memberId, $id]
        );
    }

    public function requestsForMember(int $tenantId, int $memberId): array
    {
        return $this->db->fetchAll(
            "SELECT
                r.*,
                b.name AS provider_name,
                v.name AS vehicle_name,
                v.make, v.model,
                c.name AS category_name
             FROM commercial_rental_requests r
             LEFT JOIN commercial_rental_providers p
               ON p.id = r.provider_id AND p.tenant_id = r.tenant_id
             LEFT JOIN businesses b
               ON b.id = p.business_id AND b.tenant_id = p.tenant_id
             LEFT JOIN commercial_rental_vehicles v
               ON v.id = r.vehicle_id AND v.tenant_id = r.tenant_id
             LEFT JOIN commercial_rental_categories c
               ON c.id = v.category_id AND c.tenant_id = v.tenant_id
             WHERE r.tenant_id = ?
               AND r.member_id = ?
               AND r.deleted_at IS NULL
             ORDER BY r.created_at DESC, r.id DESC",
            [$tenantId, $memberId]
        );
    }

    private function distanceKm(?float $lat1, ?float $lng1, ?float $lat2, ?float $lng2): ?float
    {
        if ($lat1 === null || $lng1 === null || $lat2 === null || $lng2 === null) {
            return null;
        }

        $earth = 6371.0088;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1))
            * cos(deg2rad($lat2))
            * sin($dLng / 2) ** 2;

        return round($earth * 2 * asin(min(1, sqrt($a))), 1);
    }
}
