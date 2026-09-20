<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Models;

use App\Core\Database;

final class TaxiBooking
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function all(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT
                t.*,
                b.name AS vendor_name,
                ts.name AS service_name,
                tv.registration_no AS vehicle_registration_no,
                td.name AS driver_name,
                m.member_no,
                CONCAT_WS(' ', m.first_name, NULLIF(m.middle_name, ''), m.last_name) AS member_name,
                m.phone AS member_phone
             FROM taxi_bookings t
             INNER JOIN taxi_vendors v
                ON v.id = t.vendor_id
               AND v.tenant_id = t.tenant_id
             INNER JOIN businesses b
                ON b.id = v.business_id
               AND b.tenant_id = v.tenant_id
             LEFT JOIN taxi_services ts
                ON ts.id = t.service_id
               AND ts.tenant_id = t.tenant_id
             LEFT JOIN taxi_vehicles tv
                ON tv.id = t.vehicle_id
               AND tv.tenant_id = t.tenant_id
             LEFT JOIN taxi_drivers td
                ON td.id = t.driver_id
               AND td.tenant_id = t.tenant_id
             LEFT JOIN members m
                ON m.id = t.member_id
               AND m.tenant_id = t.tenant_id
             WHERE t.tenant_id = ?
               AND t.deleted_at IS NULL
             ORDER BY t.id DESC",
            [$tenantId]
        );
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT
                t.*,
                b.name AS vendor_name,
                ts.name AS service_name,
                tv.registration_no AS vehicle_registration_no,
                td.name AS driver_name,
                m.member_no,
                CONCAT_WS(' ', m.first_name, NULLIF(m.middle_name, ''), m.last_name) AS member_name,
                m.phone AS member_phone,
                m.email AS member_email
             FROM taxi_bookings t
             INNER JOIN taxi_vendors v
                ON v.id = t.vendor_id
               AND v.tenant_id = t.tenant_id
             INNER JOIN businesses b
                ON b.id = v.business_id
               AND b.tenant_id = v.tenant_id
             LEFT JOIN taxi_services ts
                ON ts.id = t.service_id
               AND ts.tenant_id = t.tenant_id
             LEFT JOIN taxi_vehicles tv
                ON tv.id = t.vehicle_id
               AND tv.tenant_id = t.tenant_id
             LEFT JOIN taxi_drivers td
                ON td.id = t.driver_id
               AND td.tenant_id = t.tenant_id
             LEFT JOIN members m
                ON m.id = t.member_id
               AND m.tenant_id = t.tenant_id
             WHERE t.tenant_id = ?
               AND t.deleted_at IS NULL
               AND t.id = ?
             LIMIT 1",
            [$tenantId, $id]
        );
    }

    /**
     * Return active platform members for the current tenant.
     * Used by the admin booking form; member_id remains optional.
     */
    public function members(int $tenantId): array
    {
        return $this->db->fetchAll(
            "SELECT
                id,
                member_no,
                first_name,
                middle_name,
                last_name,
                phone,
                email,
                CONCAT_WS(' ', first_name, NULLIF(middle_name, ''), last_name) AS display_name
             FROM members
             WHERE tenant_id = ?
               AND deleted_at IS NULL
               AND status IN ('Active', 'Visitor')
             ORDER BY first_name ASC, last_name ASC, id ASC",
            [$tenantId]
        );
    }

    public function member(int $tenantId, int $memberId): ?array
    {
        return $this->db->fetch(
            "SELECT
                id,
                member_no,
                first_name,
                middle_name,
                last_name,
                phone,
                email,
                status,
                CONCAT_WS(' ', first_name, NULLIF(middle_name, ''), last_name) AS display_name
             FROM members
             WHERE tenant_id = ?
               AND id = ?
               AND deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $memberId]
        );
    }

    public function create(int $tenantId, array $data, int $userId): int
    {
        $this->db->execute(
            "INSERT INTO taxi_bookings
                (uuid, tenant_id, member_id, vendor_id,
                 service_id, vehicle_id, driver_id,
                 booking_no, booking_source,
                 trip_type, return_scheduled_at,
                 customer_name, customer_phone,
                 pickup_address, pickup_lat, pickup_lng,
                 destination_address, destination_lat, destination_lng,
                 booking_type, scheduled_at, fare,
                 distance_km, eta_minutes, return_distance_km, return_eta_minutes,
                 total_distance_km, total_eta_minutes, route_source, route_calculated_at,
                 route_estimate_committed_at, route_estimate_committed_by,
                 status, notes, created_by, updated_by)
             VALUES
                (UUID(), ?, ?, ?,
                 ?, ?, ?,
                 ?, ?,
                 ?, ?,
                 ?, ?,
                 ?, ?, ?,
                 ?, ?, ?,
                 ?, ?, ?,
                 ?, ?, ?, ?,
                 ?, ?, ?, ?,
                 ?, ?,
                 ?, ?, ?, ?)",
            [
                $tenantId,
                $data['member_id'],
                $data['vendor_id'],
                $data['service_id'],
                $data['vehicle_id'],
                $data['driver_id'],
                $data['booking_no'],
                $data['booking_source'],
                $data['trip_type'],
                $data['return_scheduled_at'],
                $data['customer_name'],
                $data['customer_phone'],
                $data['pickup_address'],
                $data['pickup_lat'],
                $data['pickup_lng'],
                $data['destination_address'],
                $data['destination_lat'],
                $data['destination_lng'],
                $data['booking_type'],
                $data['scheduled_at'],
                $data['fare'],
                $data['distance_km'],
                $data['eta_minutes'],
                $data['return_distance_km'],
                $data['return_eta_minutes'],
                $data['total_distance_km'],
                $data['total_eta_minutes'],
                $data['route_source'],
                $data['route_calculated_at'],
                $data['route_estimate_committed_at'] ?? null,
                $data['route_estimate_committed_by'] ?? null,
                $data['status'],
                $data['status'],
                $data['notes'],
                $userId,
                $userId,
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    public function update(int $tenantId, int $id, array $data, int $userId): void
    {
        $this->db->execute(
            "UPDATE taxi_bookings
             SET
                member_id = ?,
                vendor_id = ?,
                service_id = ?,
                vehicle_id = ?,
                driver_id = ?,
                booking_no = ?,
                booking_source = ?,
                trip_type = ?,
                return_scheduled_at = ?,
                customer_name = ?,
                customer_phone = ?,
                pickup_address = ?,
                pickup_lat = ?,
                pickup_lng = ?,
                destination_address = ?,
                destination_lat = ?,
                destination_lng = ?,
                booking_type = ?,
                scheduled_at = ?,
                fare = ?,
                distance_km = ?,
                eta_minutes = ?,
                return_distance_km = ?,
                return_eta_minutes = ?,
                total_distance_km = ?,
                total_eta_minutes = ?,
                route_source = ?,
                route_calculated_at = ?,
                route_estimate_committed_at = ?,
                route_estimate_committed_by = ?,
                status = ?,
                cancelled_at = CASE
                    WHEN ? = 'Cancelled' THEN COALESCE(cancelled_at, NOW())
                    ELSE NULL
                END,
                notes = ?,
                updated_by = ?
             WHERE tenant_id = ?
               AND id = ?
               AND deleted_at IS NULL",
            [
                $data['member_id'],
                $data['vendor_id'],
                $data['service_id'],
                $data['vehicle_id'],
                $data['driver_id'],
                $data['booking_no'],
                $data['booking_source'],
                $data['trip_type'],
                $data['return_scheduled_at'],
                $data['customer_name'],
                $data['customer_phone'],
                $data['pickup_address'],
                $data['pickup_lat'],
                $data['pickup_lng'],
                $data['destination_address'],
                $data['destination_lat'],
                $data['destination_lng'],
                $data['booking_type'],
                $data['scheduled_at'],
                $data['fare'],
                $data['distance_km'],
                $data['eta_minutes'],
                $data['return_distance_km'],
                $data['return_eta_minutes'],
                $data['total_distance_km'],
                $data['total_eta_minutes'],
                $data['route_source'],
                $data['route_calculated_at'],
                $data['route_estimate_committed_at'] ?? null,
                $data['route_estimate_committed_by'] ?? null,
                $data['status'],
                $data['notes'],
                $userId,
                $tenantId,
                $id,
            ]
        );
    }

    public function commitRouteEstimate(int $tenantId, int $id, array $routeData, float $fare, int $userId): void
    {
        $this->db->execute(
            "UPDATE taxi_bookings
             SET distance_km = ?,
                 eta_minutes = ?,
                 return_distance_km = ?,
                 return_eta_minutes = ?,
                 total_distance_km = ?,
                 total_eta_minutes = ?,
                 route_source = ?,
                 route_calculated_at = ?,
                 fare = ?,
                 route_estimate_committed_at = NOW(),
                 route_estimate_committed_by = ?,
                 updated_by = ?
             WHERE tenant_id = ?
               AND id = ?
               AND deleted_at IS NULL",
            [
                $routeData['distance_km'] ?? null,
                $routeData['eta_minutes'] ?? null,
                $routeData['return_distance_km'] ?? null,
                $routeData['return_eta_minutes'] ?? null,
                $routeData['total_distance_km'] ?? null,
                $routeData['total_eta_minutes'] ?? null,
                $routeData['route_source'] ?? 'GEMINI',
                $routeData['route_calculated_at'] ?? null,
                $fare,
                $userId,
                $userId,
                $tenantId,
                $id,
            ]
        );
    }



    public function addStatusHistory(
        int $tenantId,
        int $bookingId,
        ?string $oldStatus,
        string $newStatus,
        ?int $changedBy,
        string $actorType,
        ?string $notes = null
    ): void {
        $this->db->execute(
            "INSERT INTO taxi_booking_status_history
                (tenant_id, booking_id, old_status, new_status, changed_by, actor_type, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$tenantId, $bookingId, $oldStatus, $newStatus, $changedBy, $actorType, $notes]
        );
    }

    public function updateStatus(
        int $tenantId,
        int $id,
        string $newStatus,
        ?int $driverId,
        ?int $vehicleId,
        int $userId
    ): bool {
        $this->db->execute(
            "UPDATE taxi_bookings
             SET status = ?,
                 driver_id = ?,
                 vehicle_id = ?,
                 cancelled_at = CASE
                     WHEN ? = 'Cancelled' THEN COALESCE(cancelled_at, NOW())
                     ELSE NULL
                 END,
                 updated_by = ?
             WHERE tenant_id = ?
               AND id = ?
               AND deleted_at IS NULL",
            [$newStatus, $driverId, $vehicleId, $newStatus, $userId, $tenantId, $id]
        );

        return ($this->db->fetch(
            "SELECT id FROM taxi_bookings
             WHERE tenant_id = ? AND id = ? AND status = ? AND deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $id, $newStatus]
        ) !== null);
    }

    public function forDriver(int $tenantId, int $driverId): array
    {
        return $this->db->fetchAll(
            "SELECT
                t.*,
                b.name AS vendor_name,
                ts.name AS service_name,
                tv.registration_no AS vehicle_registration_no,
                tv.vehicle_type, tv.make, tv.model, tv.color, tv.seating_capacity,
                td.name AS driver_name, td.phone AS driver_phone
             FROM taxi_bookings t
             INNER JOIN taxi_vendors v
                ON v.id = t.vendor_id AND v.tenant_id = t.tenant_id
             INNER JOIN businesses b
                ON b.id = v.business_id AND b.tenant_id = v.tenant_id
             LEFT JOIN taxi_services ts
                ON ts.id = t.service_id AND ts.tenant_id = t.tenant_id
             LEFT JOIN taxi_vehicles tv
                ON tv.id = t.vehicle_id AND tv.tenant_id = t.tenant_id
             INNER JOIN taxi_drivers td
                ON td.id = t.driver_id
               AND td.tenant_id = t.tenant_id
               AND td.user_id = ?
             LEFT JOIN members m
                ON m.id = t.member_id AND m.tenant_id = t.tenant_id
             WHERE t.tenant_id = ?
               AND t.driver_id = ?
               AND t.deleted_at IS NULL
             ORDER BY
                CASE t.status
                    WHEN 'In Progress' THEN 1
                    WHEN 'Driver Arrived' THEN 2
                    WHEN 'Assigned' THEN 3
                    WHEN 'Confirmed' THEN 4
                    ELSE 5
                END,
                COALESCE(t.scheduled_at, t.created_at) ASC,
                t.id DESC",
            [$driverId, $tenantId, $driverId]
        );
    }

    public function statusHistory(int $tenantId, int $id): array
    {
        return $this->db->fetchAll(
            "SELECT h.*, u.name AS changed_by_name
             FROM taxi_booking_status_history h
             LEFT JOIN users u
               ON u.id = h.changed_by
              AND u.tenant_id = h.tenant_id
             WHERE h.tenant_id = ? AND h.booking_id = ?
             ORDER BY h.created_at ASC, h.id ASC",
            [$tenantId, $id]
        );
    }

    public function softDelete(int $tenantId, int $id, int $userId): void
    {
        $this->db->execute(
            "UPDATE taxi_bookings
             SET deleted_at = NOW(), updated_by = ?
             WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL",
            [$userId, $tenantId, $id]
        );
    }

    public function booking_no_exists(int $tenantId, string $value, ?int $ignoreId = null): bool
    {
        $sql = "SELECT id
                FROM taxi_bookings
                WHERE tenant_id = ?
                  AND booking_no = ?
                  AND deleted_at IS NULL";
        $params = [$tenantId, $value];

        if ($ignoreId !== null) {
            $sql .= " AND id <> ?";
            $params[] = $ignoreId;
        }

        return $this->db->fetch($sql . " LIMIT 1", $params) !== null;
    }
}
