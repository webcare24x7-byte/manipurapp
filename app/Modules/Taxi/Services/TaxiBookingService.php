<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Services;

use App\Modules\Taxi\Models\TaxiBooking;
use App\Modules\Taxi\Models\TaxiVendor;
use App\Modules\Taxi\Models\TaxiService;
use App\Modules\Taxi\Models\TaxiVehicle;
use App\Modules\Taxi\Models\TaxiDriver;
use RuntimeException;

final class TaxiBookingService
{
    private const BOOKING_SOURCES = ['APP', 'PHONE', 'WALK_IN', 'ADMIN'];
    private const BOOKING_TYPES = ['Immediate', 'Scheduled'];
    private const TRIP_TYPES = ['ONE_WAY', 'ROUND_TRIP'];
    private const STATUSES = [
        'Pending',
        'Confirmed',
        'Assigned',
        'Driver Arrived',
        'In Progress',
        'Completed',
        'Cancelled',
        'No Show',
    ];

    private TaxiBooking $bookings;
    private TaxiVendor $vendorsModel;
    private TaxiService $servicesModel;
    private TaxiVehicle $vehiclesModel;
    private TaxiDriver $driversModel;
    private TaxiFareService $fareService;

    public function __construct()
    {
        $this->bookings = new TaxiBooking();
        $this->vendorsModel = new TaxiVendor();
        $this->servicesModel = new TaxiService();
        $this->vehiclesModel = new TaxiVehicle();
        $this->driversModel = new TaxiDriver();
        $this->fareService = new TaxiFareService();
    }

    public function all(int $tenantId): array
    {
        return $this->bookings->all($tenantId);
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->bookings->find($tenantId, $id);
    }

    public function create(int $tenantId, array $input, int $userId): int
    {
        $data = $this->validate($input);
        $this->assertVendor($tenantId, (int) $data['vendor_id']);
        $this->assertMember($tenantId, $data['member_id']);
        $this->assertRelationships($tenantId, $data);
        $data['fare'] = $this->resolveFare($tenantId, $data);
        if ($data['route_estimate_commit_requested'] && $data['route_source'] !== null && $data['distance_km'] !== null) {
            $data['route_estimate_committed_at'] = date('Y-m-d H:i:s');
            $data['route_estimate_committed_by'] = $userId;
        } else {
            $data['route_estimate_committed_at'] = null;
            $data['route_estimate_committed_by'] = null;
        }

        if ($this->bookings->booking_no_exists($tenantId, (string) $data['booking_no'])) {
            throw new RuntimeException('A record with this booking no already exists.');
        }

        $id = $this->bookings->create($tenantId, $data, $userId);
        $this->bookings->addStatusHistory(
            $tenantId,
            $id,
            null,
            (string) $data['status'],
            $userId,
            'ADMIN',
            'Booking created.'
        );
        return $id;
    }

    public function update(int $tenantId, int $id, array $input, int $userId): void
    {
        if (!$this->find($tenantId, $id)) {
            throw new RuntimeException('Booking number not found.');
        }

        $data = $this->validate($input);
        $this->assertVendor($tenantId, (int) $data['vendor_id']);
        $this->assertMember($tenantId, $data['member_id']);
        $this->assertRelationships($tenantId, $data);
        $data['fare'] = $this->resolveFare($tenantId, $data);
        if ($data['route_estimate_commit_requested'] && $data['route_source'] !== null && $data['distance_km'] !== null) {
            $data['route_estimate_committed_at'] = date('Y-m-d H:i:s');
            $data['route_estimate_committed_by'] = $userId;
        } else {
            $data['route_estimate_committed_at'] = null;
            $data['route_estimate_committed_by'] = null;
        }

        if ($this->bookings->booking_no_exists($tenantId, (string) $data['booking_no'], $id)) {
            throw new RuntimeException('A record with this booking no already exists.');
        }

        $existingStatus = (string) ($this->find($tenantId, $id)['status'] ?? 'Pending');
        if ($data['status'] !== $existingStatus) {
            $this->assertStatusTransition(
                $tenantId,
                $this->find($tenantId, $id) ?? [],
                (string) $data['status'],
                'ADMIN',
                $userId,
                $data['driver_id'],
                $data['vehicle_id']
            );
        }

        $this->bookings->update($tenantId, $id, $data, $userId);

        if ($data['status'] !== $existingStatus) {
            $this->bookings->addStatusHistory(
                $tenantId,
                $id,
                $existingStatus,
                (string) $data['status'],
                $userId,
                'ADMIN',
                null
            );
        }
    }

    public function commitRouteEstimate(int $tenantId, int $id, array $input, int $userId): array
    {
        $booking = $this->find($tenantId, $id);
        if (!$booking) {
            throw new RuntimeException('Booking not found.');
        }

        $serviceId = $this->nullableInt($input['service_id'] ?? $booking['service_id'] ?? null);
        if ($serviceId === null) {
            throw new RuntimeException('Taxi service is required before committing the route estimate.');
        }

        $service = $this->servicesModel->find($tenantId, $serviceId);
        if ($service === null || strtoupper((string) ($service['status'] ?? '')) !== 'ACTIVE') {
            throw new RuntimeException('Selected taxi service is not available.');
        }

        $tripType = strtoupper((string) ($input['trip_type'] ?? $booking['trip_type'] ?? 'ONE_WAY'));
        $distance = $this->nullableDecimal($input['distance_km'] ?? null, 2);
        $eta = $this->nullablePositiveInt($input['eta_minutes'] ?? null);
        $returnDistance = $this->nullableDecimal($input['return_distance_km'] ?? null, 2);
        $returnEta = $this->nullablePositiveInt($input['return_eta_minutes'] ?? null);
        $totalDistance = $this->nullableDecimal($input['total_distance_km'] ?? null, 2);
        $totalEta = $this->nullablePositiveInt($input['total_eta_minutes'] ?? null);

        if ($distance === null || $eta === null) {
            throw new RuntimeException('Calculate the route estimate before committing it.');
        }

        if ($tripType === 'ROUND_TRIP') {
            if ($returnDistance === null || $returnEta === null || $totalDistance === null || $totalEta === null) {
                throw new RuntimeException('A complete outbound and return route estimate is required before committing a round trip.');
            }
        } else {
            $totalDistance = $totalDistance ?? $distance;
            $totalEta = $totalEta ?? $eta;
            $returnDistance = null;
            $returnEta = null;
        }

        $fareResult = $this->fareService->calculate(
            $service,
            $tripType,
            $totalDistance,
            $totalEta,
            $this->nullableString($input['scheduled_at'] ?? $booking['scheduled_at'] ?? ''),
            $this->nullableString($input['return_scheduled_at'] ?? $booking['return_scheduled_at'] ?? '')
        );

        if (($fareResult['quote_required'] ?? false) === true) {
            $fare = round((float) ($input['fare'] ?? $booking['fare'] ?? 0), 2);
            if ($fare <= 0) {
                throw new RuntimeException('This taxi service uses Custom Quote pricing. Enter the approved quoted fare before committing the route estimate.');
            }
        } else {
            if (($fareResult['estimated_fare'] ?? null) === null) {
                throw new RuntimeException(implode(' ', $fareResult['notes'] ?? ['Unable to calculate the fare from this route estimate.']));
            }
            $fare = (float) $fareResult['estimated_fare'];
        }

        $routeData = [
            'distance_km' => $distance,
            'eta_minutes' => $eta,
            'return_distance_km' => $returnDistance,
            'return_eta_minutes' => $returnEta,
            'total_distance_km' => $totalDistance,
            'total_eta_minutes' => $totalEta,
            'route_source' => $this->nullableString($input['route_source'] ?? 'GEMINI') ?? 'GEMINI',
            'route_calculated_at' => $this->nullableString($input['route_calculated_at'] ?? '') ?? date('Y-m-d H:i:s'),
        ];

        $this->bookings->commitRouteEstimate($tenantId, $id, $routeData, $fare, $userId);

        return [
            'fare' => $fare,
            'fare_result' => $fareResult,
            'route' => $routeData,
        ];
    }


    /**
     * Change the operational status of a taxi booking and record an audit entry.
     *
     * ADMIN: vendor/admin staff may move a booking through the normal lifecycle.
     * DRIVER: only the assigned driver may move Assigned -> Driver Arrived
     *         -> In Progress -> Completed.
     */
    public function changeStatus(
        int $tenantId,
        int $bookingId,
        string $newStatus,
        int $userId,
        string $actorType = 'ADMIN',
        ?int $driverId = null,
        ?int $vehicleId = null,
        ?string $notes = null
    ): array {
        $booking = $this->find($tenantId, $bookingId);
        if (!$booking) {
            throw new RuntimeException('Booking not found.');
        }

        $newStatus = trim($newStatus);
        $actorType = strtoupper(trim($actorType));

        if (!in_array($newStatus, self::STATUSES, true)) {
            throw new RuntimeException('Invalid booking status.');
        }

        if (!in_array($actorType, ['ADMIN', 'DRIVER'], true)) {
            throw new RuntimeException('Invalid status actor.');
        }

        $oldStatus = (string) ($booking['status'] ?? '');

        if ($oldStatus === $newStatus) {
            return [
                'booking' => $booking,
                'changed' => false,
                'history' => $this->bookings->statusHistory($tenantId, $bookingId),
            ];
        }

        $this->assertStatusTransition(
            $tenantId,
            $booking,
            $newStatus,
            $actorType,
            $userId,
            $driverId,
            $vehicleId
        );

        $effectiveDriverId = $driverId ?? $this->nullableInt($booking['driver_id'] ?? null);
        $effectiveVehicleId = $vehicleId ?? $this->nullableInt($booking['vehicle_id'] ?? null);

        if ($newStatus === 'Assigned' && $effectiveDriverId === null) {
            throw new RuntimeException('Assign a driver before changing the booking to Assigned.');
        }

        if ($newStatus === 'Assigned' && $effectiveVehicleId === null) {
            throw new RuntimeException('Assign a vehicle before changing the booking to Assigned.');
        }

        $changed = $this->bookings->updateStatus(
            $tenantId,
            $bookingId,
            $newStatus,
            $effectiveDriverId,
            $effectiveVehicleId,
            $userId
        );

        if (!$changed) {
            throw new RuntimeException('The booking status could not be updated. Please refresh and try again.');
        }

        $this->bookings->addStatusHistory(
            $tenantId,
            $bookingId,
            $oldStatus,
            $newStatus,
            $userId,
            $actorType,
            $notes
        );

        $this->bookings->statusHistory(
            $tenantId,
            $bookingId
        );

        return [
            'booking' => $this->find($tenantId, $bookingId),
            'changed' => true,
            'history' => $this->bookings->statusHistory($tenantId, $bookingId),
        ];
    }

    private function assertStatusTransition(
        int $tenantId,
        array $booking,
        string $newStatus,
        string $actorType,
        int $userId,
        ?int $driverId,
        ?int $vehicleId
    ): void {
        $old = (string) ($booking['status'] ?? '');

        $adminAllowed = [
            'Pending' => ['Confirmed', 'Cancelled'],
            'Confirmed' => ['Assigned', 'Cancelled', 'No Show'],
            'Assigned' => ['Driver Arrived', 'Cancelled', 'No Show'],
            'Driver Arrived' => ['In Progress', 'Cancelled', 'No Show'],
            'In Progress' => ['Completed'],
            'Completed' => [],
            'Cancelled' => [],
            'No Show' => [],
        ];

        $driverAllowed = [
            'Assigned' => ['Driver Arrived'],
            'Driver Arrived' => ['In Progress'],
            'In Progress' => ['Completed'],
        ];

        $allowed = $actorType === 'DRIVER'
            ? ($driverAllowed[$old] ?? [])
            : ($adminAllowed[$old] ?? []);

        if (!in_array($newStatus, $allowed, true)) {
            throw new RuntimeException(
                sprintf('Invalid booking status transition: %s → %s.', $old ?: 'Unknown', $newStatus)
            );
        }

        if ($actorType === 'DRIVER') {
            $assignedDriverId = (int) ($booking['driver_id'] ?? 0);
            if ($assignedDriverId <= 0 || $driverId !== $assignedDriverId) {
                throw new RuntimeException('Only the driver assigned to this booking can update its trip status.');
            }
        }

        if ($newStatus === 'Assigned') {
            if ($driverId === null || $vehicleId === null) {
                throw new RuntimeException('A driver and vehicle are required before assigning this booking.');
            }

            $driver = $this->driversModel->find($tenantId, $driverId);
            if ($driver === null || (int) ($driver['vendor_id'] ?? 0) !== (int) ($booking['vendor_id'] ?? 0)) {
                throw new RuntimeException('The selected driver does not belong to this taxi business.');
            }
            if (strtoupper((string) ($driver['status'] ?? '')) !== 'ACTIVE') {
                throw new RuntimeException('The selected driver is not active.');
            }

            $vehicle = $this->vehiclesModel->find($tenantId, $vehicleId);
            if ($vehicle === null || (int) ($vehicle['vendor_id'] ?? 0) !== (int) ($booking['vendor_id'] ?? 0)) {
                throw new RuntimeException('The selected vehicle does not belong to this taxi business.');
            }
            if (strtoupper((string) ($vehicle['status'] ?? '')) !== 'ACTIVE') {
                throw new RuntimeException('The selected vehicle is not active.');
            }
        }
    }

    public function driverBookings(int $tenantId, int $driverId): array
    {
        return $this->bookings->forDriver($tenantId, $driverId);
    }

    public function statusHistory(int $tenantId, int $bookingId): array
    {
        return $this->bookings->statusHistory($tenantId, $bookingId);
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        if (!$this->find($tenantId, $id)) {
            throw new RuntimeException('Booking number not found.');
        }

        $this->bookings->softDelete($tenantId, $id, $userId);
    }

    public function vendors(int $tenantId): array
    {
        return $this->vendorsModel->all($tenantId);
    }

    public function members(int $tenantId): array
    {
        return $this->bookings->members($tenantId);
    }

    private function validate(array $input): array
    {
        $data = [];
        $data['member_id'] = $this->nullableInt($input['member_id'] ?? null);
        $data['vendor_id'] = $this->nullableInt($input['vendor_id'] ?? null);
        $data['service_id'] = $this->nullableInt($input['service_id'] ?? null);
        $data['vehicle_id'] = $this->nullableInt($input['vehicle_id'] ?? null);
        $data['driver_id'] = $this->nullableInt($input['driver_id'] ?? null);
        $data['booking_no'] = $this->nullableString($input['booking_no'] ?? '');
        $data['booking_source'] = strtoupper((string) ($this->nullableString($input['booking_source'] ?? '') ?? 'ADMIN'));
        $data['trip_type'] = strtoupper((string) ($this->nullableString($input['trip_type'] ?? '') ?? 'ONE_WAY'));
        $data['return_scheduled_at'] = $this->nullableString($input['return_scheduled_at'] ?? '');
        $data['customer_name'] = $this->nullableString($input['customer_name'] ?? '');
        $data['customer_phone'] = $this->nullableString($input['customer_phone'] ?? '');
        $data['pickup_address'] = $this->nullableString($input['pickup_address'] ?? '');
        $data['pickup_lat'] = $this->nullableCoordinate($input['pickup_lat'] ?? null);
        $data['pickup_lng'] = $this->nullableCoordinate($input['pickup_lng'] ?? null);
        $data['destination_address'] = $this->nullableString($input['destination_address'] ?? '');
        $data['destination_lat'] = $this->nullableCoordinate($input['destination_lat'] ?? null);
        $data['destination_lng'] = $this->nullableCoordinate($input['destination_lng'] ?? null);
        $data['booking_type'] = $this->nullableString($input['booking_type'] ?? '');
        $data['scheduled_at'] = $this->nullableString($input['scheduled_at'] ?? '');
        $data['fare'] = $this->decimal($input['fare'] ?? null, 2);
        $data['distance_km'] = $this->nullableDecimal($input['distance_km'] ?? null, 2);
        $data['eta_minutes'] = $this->nullablePositiveInt($input['eta_minutes'] ?? null);
        $data['return_distance_km'] = $this->nullableDecimal($input['return_distance_km'] ?? null, 2);
        $data['return_eta_minutes'] = $this->nullablePositiveInt($input['return_eta_minutes'] ?? null);
        $data['total_distance_km'] = $this->nullableDecimal($input['total_distance_km'] ?? null, 2);
        $data['total_eta_minutes'] = $this->nullablePositiveInt($input['total_eta_minutes'] ?? null);
        $data['route_source'] = $this->nullableString($input['route_source'] ?? '');
        $data['route_calculated_at'] = $this->nullableString($input['route_calculated_at'] ?? '');
        $data['route_estimate_commit_requested'] = filter_var($input['route_estimate_commit_requested'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $data['route_estimate_committed_at'] = $this->nullableString($input['route_estimate_committed_at'] ?? '');
        $data['route_estimate_committed_by'] = $this->nullableInt($input['route_estimate_committed_by'] ?? null);
        $data['status'] = $this->nullableString($input['status'] ?? '');
        $data['notes'] = $this->nullableString($input['notes'] ?? '');

        if ($data['vendor_id'] === null) {
            throw new RuntimeException('Taxi business is required.');
        }
        if ($data['service_id'] === null) {
            throw new RuntimeException('Taxi service is required. Select the service whose pricing rules should be used for this booking.');
        }
        if ($data['booking_no'] === null) {
            throw new RuntimeException('Booking no is required.');
        }
        if ($data['customer_name'] === null) {
            throw new RuntimeException('Customer name is required.');
        }
        if ($data['customer_phone'] === null) {
            throw new RuntimeException('Customer phone is required.');
        }
        if ($data['pickup_address'] === null) {
            throw new RuntimeException('Pickup address is required.');
        }
        if ($data['destination_address'] === null) {
            throw new RuntimeException('Destination address is required.');
        }

        if (!in_array($data['booking_source'], self::BOOKING_SOURCES, true)) {
            throw new RuntimeException('Invalid booking source.');
        }

        $data['booking_type'] = $data['booking_type'] ?: 'Immediate';
        $data['status'] = $data['status'] ?: 'Pending';

        if (!in_array($data['booking_type'], self::BOOKING_TYPES, true)) {
            throw new RuntimeException('Invalid booking type.');
        }

        if ($data['booking_type'] === 'Scheduled' && $data['scheduled_at'] === null) {
            throw new RuntimeException('Scheduled time is required for scheduled bookings.');
        }

        if (!in_array($data['trip_type'], self::TRIP_TYPES, true)) {
            throw new RuntimeException('Invalid trip type.');
        }

        if ($data['trip_type'] === 'ROUND_TRIP') {
            if ($data['scheduled_at'] === null) {
                throw new RuntimeException('Departure time is required for round-trip bookings.');
            }
            if ($data['return_scheduled_at'] === null) {
                throw new RuntimeException('Return date and time are required for round-trip bookings.');
            }
            if (strtotime($data['return_scheduled_at']) === false || strtotime($data['scheduled_at']) === false) {
                throw new RuntimeException('Invalid departure or return date/time.');
            }
            if (strtotime($data['return_scheduled_at']) <= strtotime($data['scheduled_at'])) {
                throw new RuntimeException('Return date and time must be after the departure date and time.');
            }
        } elseif ($data['return_scheduled_at'] !== null) {
            throw new RuntimeException('Return date and time can only be used for round-trip bookings.');
        }

        if (!in_array($data['status'], self::STATUSES, true)) {
            throw new RuntimeException('Invalid status.');
        }

        return $data;
    }

    public function calculateFare(int $tenantId, array $input): array
    {
        $serviceId = $this->nullableInt($input['service_id'] ?? null);
        if ($serviceId === null) {
            throw new RuntimeException('Taxi service is required for fare calculation.');
        }

        $service = $this->servicesModel->find($tenantId, $serviceId);
        if ($service === null || strtoupper((string) ($service['status'] ?? '')) !== 'ACTIVE') {
            throw new RuntimeException('Selected taxi service is not available.');
        }

        $distance = $this->nullableDecimal($input['total_distance_km'] ?? ($input['distance_km'] ?? null), 2);
        $eta = $this->nullablePositiveInt($input['total_eta_minutes'] ?? ($input['eta_minutes'] ?? null));

        return $this->fareService->calculate(
            $service,
            strtoupper((string) ($input['trip_type'] ?? 'ONE_WAY')),
            $distance,
            $eta,
            $this->nullableString($input['scheduled_at'] ?? ''),
            $this->nullableString($input['return_scheduled_at'] ?? '')
        );
    }

    private function resolveFare(int $tenantId, array $data): float
    {
        $result = $this->calculateFare($tenantId, $data);

        if (($result['quote_required'] ?? false) === true) {
            $manualFare = round((float) ($data['fare'] ?? 0), 2);
            if ($manualFare <= 0) {
                throw new RuntimeException('This taxi service uses Custom Quote pricing. Enter the approved quoted fare before saving the booking.');
            }
            return $manualFare;
        }

        if ($result['estimated_fare'] === null) {
            throw new RuntimeException(implode(' ', $result['notes'] ?? ['Unable to calculate the fare for this service.']));
        }

        return (float) $result['estimated_fare'];
    }

    private function assertMember(int $tenantId, ?int $memberId): void
    {
        if ($memberId === null) {
            return;
        }

        if ($this->bookings->member($tenantId, $memberId) === null) {
            throw new RuntimeException('Selected platform member was not found in this tenant.');
        }
    }

    private function assertVendor(int $tenantId, int $vendorId): void
    {
        if ($vendorId <= 0 || $this->vendorsModel->find($tenantId, $vendorId) === null) {
            throw new RuntimeException('Taxi business not found.');
        }
    }

    private function assertRelationships(int $tenantId, array $data): void
    {
        $vendorId = (int) ($data['vendor_id'] ?? 0);
        $relations = [
            'service_id' => $this->servicesModel,
            'vehicle_id' => $this->vehiclesModel,
            'driver_id' => $this->driversModel,
        ];

        foreach ($relations as $field => $model) {
            $id = (int) ($data[$field] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $row = $model->find($tenantId, $id);
            if ($row === null || (int) ($row['vendor_id'] ?? 0) !== $vendorId) {
                throw new RuntimeException('Selected taxi resource does not belong to the selected taxi business.');
            }
        }
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = (int) $value;
        return $value > 0 ? $value : null;
    }

    private function decimal(mixed $value, int $scale = 2): float
    {
        return round((float) ($value === null || $value === '' ? 0 : $value), $scale);
    }

    private function nullableCoordinate(mixed $value): ?float
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        return round((float) $value, 7);
    }

    private function nullableDecimal(mixed $value, int $scale = 2): ?float
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $number = (float) $value;
        return $number > 0 ? round($number, $scale) : null;
    }

    private function nullablePositiveInt(mixed $value): ?int
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $number = (int) $value;
        return $number > 0 ? $number : null;
    }
}
