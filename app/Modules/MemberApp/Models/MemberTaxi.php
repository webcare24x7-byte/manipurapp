<?php

declare(strict_types=1);

namespace App\Modules\MemberApp\Models;

use App\Core\Database;
use RuntimeException;

final class MemberTaxi
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function services(int $tenantId, ?string $serviceType = null): array
    {
        $sql = "SELECT ts.*, b.name AS business_name, b.city, b.district, b.state,
                       (SELECT COUNT(*) FROM taxi_vehicles tv
                        WHERE tv.tenant_id = ts.tenant_id AND tv.vendor_id = ts.vendor_id
                          AND tv.status = 'Active' AND tv.deleted_at IS NULL) AS vehicle_count
                FROM taxi_services ts
                INNER JOIN taxi_vendors v ON v.id = ts.vendor_id AND v.tenant_id = ts.tenant_id
                INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id
                WHERE ts.tenant_id = ? AND ts.status = 'Active'
                  AND ts.deleted_at IS NULL AND v.status = 'Active' AND b.status = 'Active'
                  AND b.deleted_at IS NULL";
        $params = [$tenantId];
        if ($serviceType !== null && $serviceType !== '') {
            $sql .= " AND ts.service_type = ?";
            $params[] = $serviceType;
        }
        $sql .= " ORDER BY ts.id DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function service(int $tenantId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT ts.*, b.name AS business_name, b.city, b.district, b.state
             FROM taxi_services ts
             INNER JOIN taxi_vendors v ON v.id = ts.vendor_id AND v.tenant_id = ts.tenant_id
             INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id
             WHERE ts.tenant_id = ? AND ts.id = ? AND ts.status = 'Active'
               AND ts.deleted_at IS NULL AND v.status = 'Active' AND b.status = 'Active'
               AND b.deleted_at IS NULL LIMIT 1",
            [$tenantId, $id]
        );
    }

    public function vehicles(int $tenantId, int $serviceId, ?int $minSeats = null, bool $acOnly = false): array
    {
        $service = $this->service($tenantId, $serviceId);
        if (!$service) return [];
        $sql = "SELECT tv.*, b.name AS business_name,
                       EXISTS(SELECT 1 FROM taxi_drivers td
                              WHERE td.tenant_id = tv.tenant_id AND td.vendor_id = tv.vendor_id
                                AND td.status = 'Active' AND td.availability = 'Available'
                                AND td.deleted_at IS NULL) AS driver_available
                FROM taxi_vehicles tv
                INNER JOIN taxi_vendors v ON v.id = tv.vendor_id AND v.tenant_id = tv.tenant_id
                INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id
                WHERE tv.tenant_id = ? AND tv.vendor_id = ? AND tv.status = 'Active'
                  AND tv.deleted_at IS NULL AND v.status = 'Active' AND b.status = 'Active'
                  AND b.deleted_at IS NULL";
        $params = [$tenantId, (int)$service['vendor_id']];
        if ($minSeats !== null && $minSeats > 0) { $sql .= " AND tv.seating_capacity >= ?"; $params[] = $minSeats; }
        $sql .= " ORDER BY driver_available DESC, tv.seating_capacity ASC, tv.id ASC";
        return $this->db->fetchAll($sql, $params);
    }

    public function bookingForMember(int $tenantId, int $memberId, int $id): ?array
    {
        return $this->db->fetch(
            "SELECT tb.*, b.name AS business_name, ts.name AS service_name, ts.service_type,
                    ts.pricing_mode, ts.base_fare, ts.per_km, ts.per_minute, ts.minimum_fare,
                    ts.included_km, ts.daily_rate, ts.extra_km_rate,
                    tv.registration_no, tv.vehicle_type, tv.make, tv.model, tv.seating_capacity, tv.color, tv.photo_path,
                    td.name AS driver_name, td.phone AS driver_phone, td.photo_path AS driver_photo_path
             FROM taxi_bookings tb
             INNER JOIN taxi_vendors v ON v.id = tb.vendor_id AND v.tenant_id = tb.tenant_id
             INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id
             LEFT JOIN taxi_services ts ON ts.id = tb.service_id AND ts.tenant_id = tb.tenant_id
             LEFT JOIN taxi_vehicles tv ON tv.id = tb.vehicle_id AND tv.tenant_id = tb.tenant_id
             LEFT JOIN taxi_drivers td ON td.id = tb.driver_id AND td.tenant_id = tb.tenant_id
             WHERE tb.tenant_id = ? AND tb.member_id = ? AND tb.id = ? AND tb.deleted_at IS NULL LIMIT 1",
            [$tenantId, $memberId, $id]
        );
    }

    public function bookingsForMember(int $tenantId, int $memberId): array
    {
        return $this->db->fetchAll(
            "SELECT tb.*, b.name AS business_name, ts.name AS service_name, ts.service_type,
                    tv.vehicle_type, tv.make, tv.model, tv.photo_path, tv.registration_no, td.name AS driver_name, td.phone AS driver_phone
             FROM taxi_bookings tb
             INNER JOIN taxi_vendors v ON v.id = tb.vendor_id AND v.tenant_id = tb.tenant_id
             INNER JOIN businesses b ON b.id = v.business_id AND b.tenant_id = v.tenant_id
             LEFT JOIN taxi_services ts ON ts.id = tb.service_id AND ts.tenant_id = tb.tenant_id
             LEFT JOIN taxi_vehicles tv ON tv.id = tb.vehicle_id AND tv.tenant_id = tb.tenant_id
             LEFT JOIN taxi_drivers td ON td.id = tb.driver_id AND td.tenant_id = tb.tenant_id
             WHERE tb.tenant_id = ? AND tb.member_id = ? AND tb.deleted_at IS NULL
             ORDER BY tb.created_at DESC, tb.id DESC",
            [$tenantId, $memberId]
        );
    }

    public function statusHistoryForMember(int $tenantId, int $memberId, int $bookingId): array
    {
        return $this->db->fetchAll(
            "SELECT h.*, u.name AS changed_by_name
             FROM taxi_booking_status_history h
             LEFT JOIN users u
               ON u.id = h.changed_by
              AND u.tenant_id = h.tenant_id
             INNER JOIN taxi_bookings tb
               ON tb.id = h.booking_id
              AND tb.tenant_id = h.tenant_id
              AND tb.member_id = ?
             WHERE h.tenant_id = ? AND h.booking_id = ?
             ORDER BY h.created_at ASC, h.id ASC",
            [$memberId, $tenantId, $bookingId]
        );
    }

    /**
     * Record a booking status change in the shared Taxi status history table.
     *
     * MemberApp owns the booking creation/cancellation actions, but the history
     * table is part of the Taxi module lifecycle and is intentionally written
     * here so member-created bookings have the same audit trail as admin/driver
     * actions.
     */
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

    public function createBooking(int $tenantId, array $data, int $userId): int
    {
        $this->db->execute(
            "INSERT INTO taxi_bookings
             (uuid, tenant_id, member_id, vendor_id, service_id, vehicle_id, driver_id,
              booking_no, booking_source, trip_type, return_scheduled_at,
              customer_name, customer_phone, pickup_address, pickup_lat, pickup_lng,
              destination_address, destination_lat, destination_lng, booking_type, scheduled_at,
              fare, status, notes, created_by, updated_by)
             VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, 'APP', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0.00, 'Pending', ?, ?, ?)",
            [
                $tenantId, $data['member_id'], $data['vendor_id'], $data['service_id'],
                $data['vehicle_id'], null, $data['booking_no'], $data['trip_type'],
                $data['return_scheduled_at'], $data['customer_name'], $data['customer_phone'],
                $data['pickup_address'], $data['pickup_lat'], $data['pickup_lng'],
                $data['destination_address'], $data['destination_lat'], $data['destination_lng'],
                $data['booking_type'], $data['scheduled_at'], $data['notes'], $userId, $userId
            ]
        );
        $id = (int)$this->db->lastInsertId();
        $this->addStatusHistory(
            $tenantId,
            $id,
            null,
            'Pending',
            $userId,
            'MEMBER',
            'Booking created from MemberApp.'
        );
        return $id;
    }

    public function commitRoute(int $tenantId, int $memberId, int $bookingId, array $route, float $fare, int $userId, string $routeSource): bool
    {
        $booking = $this->bookingForMember($tenantId, $memberId, $bookingId);
        if (!$booking) {
            throw new RuntimeException('Booking not found.');
        }

        /*
         * Normalize the route before persistence.
         *
         * Gemini may return only distance_km / eta_minutes for a ONE_WAY
         * route. The MemberApp detail screen and the status API use the
         * total_* fields as their primary display values, so a one-way
         * estimate must have those totals populated too.
         *
         * For ROUND_TRIP, prefer the explicit totals returned by the route
         * service and fall back to the outbound values only when totals are
         * absent.
         */
        $distance = $route['distance_km'] ?? null;
        $eta = $route['eta_minutes'] ?? null;

        $totalDistance = $route['total_distance_km'] ?? $distance;
        $totalEta = $route['total_eta_minutes'] ?? $eta;

        $returnDistance = $route['return_distance_km'] ?? null;
        $returnEta = $route['return_eta_minutes'] ?? null;

        /*
         * Do not manufacture a return leg for ONE_WAY bookings.
         */
        if (strtoupper((string)($booking['trip_type'] ?? 'ONE_WAY')) !== 'ROUND_TRIP') {
            $returnDistance = null;
            $returnEta = null;
            $totalDistance = $distance;
            $totalEta = $eta;
        }

        $this->db->execute(
            "UPDATE taxi_bookings SET distance_km = ?, eta_minutes = ?, return_distance_km = ?,
                return_eta_minutes = ?, total_distance_km = ?, total_eta_minutes = ?,
                route_source = ?, route_calculated_at = ?, fare = ?,
                route_estimate_committed_at = NOW(), route_estimate_committed_by = ?, updated_by = ?
             WHERE tenant_id = ? AND member_id = ? AND id = ? AND deleted_at IS NULL
               AND status IN ('Pending','Confirmed')",
            [
                $distance,
                $eta,
                $returnDistance,
                $returnEta,
                $totalDistance,
                $totalEta,
                $routeSource,
                $route['route_calculated_at'] ?? date('Y-m-d H:i:s'),
                $fare,
                $userId,
                $userId,
                $tenantId,
                $memberId,
                $bookingId
            ]
        );

        $updated = $this->bookingForMember($tenantId, $memberId, $bookingId);

        return $updated !== null
            && in_array((string)($updated['status'] ?? ''), ['Pending', 'Confirmed'], true)
            && !empty($updated['route_estimate_committed_at']);
    }

    public function cancelBooking(int $tenantId, int $memberId, int $bookingId, int $userId): bool
    {
        $current = $this->db->fetch(
            "SELECT status FROM taxi_bookings
             WHERE tenant_id = ? AND member_id = ? AND id = ? AND deleted_at IS NULL
             LIMIT 1",
            [$tenantId, $memberId, $bookingId]
        );
        $oldStatus = $current['status'] ?? null;

        $this->db->execute(
            "UPDATE taxi_bookings SET status = 'Cancelled', cancelled_at = NOW(), updated_by = ?
             WHERE tenant_id = ? AND member_id = ? AND id = ?
               AND deleted_at IS NULL AND status IN ('Pending','Confirmed')",
            [$userId, $tenantId, $memberId, $bookingId]
        );

        $updated = $this->db->fetch(
            "SELECT status FROM taxi_bookings
             WHERE tenant_id = ? AND member_id = ? AND id = ? AND deleted_at IS NULL LIMIT 1",
            [$tenantId, $memberId, $bookingId]
        );

        $cancelled = ($updated['status'] ?? null) === 'Cancelled';
        if ($cancelled && $oldStatus !== 'Cancelled') {
            $this->addStatusHistory(
                $tenantId,
                $bookingId,
                (string) $oldStatus,
                'Cancelled',
                $userId,
                'MEMBER',
                'Cancelled by member.'
            );
        }

        return $cancelled;
    }

    public function memberProfile(int $tenantId, int $memberId): ?array
    {
        return $this->db->fetch(
            "SELECT id, member_no, first_name, middle_name, last_name, phone, email, status,
                    membership_date, created_at
             FROM members WHERE tenant_id = ? AND id = ? AND deleted_at IS NULL LIMIT 1",
            [$tenantId, $memberId]
        );
    }
}
