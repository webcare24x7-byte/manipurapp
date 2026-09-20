<?php

declare(strict_types=1);

namespace App\Modules\MemberApp\Services;

use App\Modules\MemberApp\Models\MemberTaxi;
use App\Modules\Taxi\Services\TaxiFareService;
use App\Modules\Taxi\Services\TaxiRouteService;
use RuntimeException;

final class MemberTaxiService
{
    public const TENANT_ID = 1;
    private MemberTaxi $model;
    private TaxiFareService $fare;
    private TaxiRouteService $route;

    public function __construct()
    {
        $this->model = new MemberTaxi();
        $this->fare = new TaxiFareService();
        $this->route = new TaxiRouteService();
    }

    public function services(?string $type = null): array { return $this->model->services(self::TENANT_ID, $type); }
    public function service(int $id): ?array { return $this->model->service(self::TENANT_ID, $id); }
    public function vehicles(int $serviceId, ?int $seats = null, bool $acOnly = false): array { return $this->model->vehicles(self::TENANT_ID, $serviceId, $seats, $acOnly); }
    public function bookings(int $memberId): array {
        return array_map(fn(array $b): array => $this->withFareBreakdown($b), $this->model->bookingsForMember(self::TENANT_ID, $memberId));
    }
    public function booking(int $memberId, int $id): ?array {
        $booking = $this->model->bookingForMember(self::TENANT_ID, $memberId, $id);
        if (!$booking) {
            return null;
        }
        $booking['status_history'] = $this->model->statusHistoryForMember(self::TENANT_ID, $memberId, $id);
        return $this->withFareBreakdown($booking);
    }
    public function profile(int $memberId): ?array { return $this->model->memberProfile(self::TENANT_ID, $memberId); }

    public function createBooking(int $memberId, int $userId, array $input): int
    {
        $serviceId = (int)($input['service_id'] ?? 0);
        $vehicleId = (int)($input['vehicle_id'] ?? 0);
        $service = $this->service($serviceId);
        if (!$service) throw new RuntimeException('The selected taxi service is no longer available.');
        $vehicle = null;
        foreach ($this->vehicles($serviceId) as $candidate) {
            if ((int)$candidate['id'] === $vehicleId) { $vehicle = $candidate; break; }
        }
        if (!$vehicle) throw new RuntimeException('The selected taxi is no longer available. Please choose another vehicle.');
        $tripType = strtoupper(trim((string)($input['trip_type'] ?? 'ONE_WAY')));
        if (!in_array($tripType, ['ONE_WAY','ROUND_TRIP'], true)) throw new RuntimeException('Invalid trip type.');
        $pickup = trim((string)($input['pickup_address'] ?? ''));
        $destination = trim((string)($input['destination_address'] ?? ''));
        if ($pickup === '' || $destination === '') throw new RuntimeException('Pickup and destination are required.');
        $plat = $this->coord($input['pickup_lat'] ?? null); $plng = $this->coord($input['pickup_lng'] ?? null);
        $dlat = $this->coord($input['destination_lat'] ?? null); $dlng = $this->coord($input['destination_lng'] ?? null);
        if ($plat === null || $plng === null || $dlat === null || $dlng === null) throw new RuntimeException('Please select both pickup and destination on the map so the route can be calculated.');
        $scheduled = trim((string)($input['scheduled_at'] ?? '')) ?: null;
        $return = trim((string)($input['return_scheduled_at'] ?? '')) ?: null;
        if ($tripType === 'ROUND_TRIP') {
            if (!$scheduled || !$return || strtotime($return) <= strtotime($scheduled)) throw new RuntimeException('Round-trip departure and return times are required, and return must be later.');
        } else { $return = null; }
        $bookingType = $scheduled ? 'Scheduled' : 'Immediate';
        $profile = $this->profile($memberId);
        if (!$profile) throw new RuntimeException('Member account not found.');
        $bookingNo = $this->bookingNumber();
        return $this->model->createBooking(self::TENANT_ID, [
            'member_id' => $memberId, 'vendor_id' => (int)$service['vendor_id'], 'service_id' => $serviceId,
            'vehicle_id' => $vehicleId, 'booking_no' => $bookingNo, 'trip_type' => $tripType,
            'return_scheduled_at' => $return, 'customer_name' => trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')),
            'customer_phone' => (string)($profile['phone'] ?? ''), 'pickup_address' => $pickup,
            'pickup_lat' => $plat, 'pickup_lng' => $plng, 'destination_address' => $destination,
            'destination_lat' => $dlat, 'destination_lng' => $dlng, 'booking_type' => $bookingType,
            'scheduled_at' => $scheduled, 'notes' => trim((string)($input['notes'] ?? '')) ?: null,
        ], $userId);
    }

    public function processRoute(int $memberId, int $bookingId): array
    {
        $booking = $this->booking($memberId, $bookingId);
        if (!$booking) {
            throw new RuntimeException('Booking not found.');
        }

        // Never start a Gemini request for a booking that is already cancelled.
        if (($booking['status'] ?? '') === 'Cancelled') {
            return [
                'booking' => $booking,
                'already_processed' => false,
                'cancelled' => true,
            ];
        }

        if (!empty($booking['route_estimate_committed_at'])) {
            return [
                'booking' => $booking,
                'already_processed' => true,
                'cancelled' => false,
            ];
        }

        /*
         * If a previous route request completed and route values were persisted
         * but the commit marker/fare was not finalized, do not call Gemini again.
         * Finalize the saved route locally from the same TaxiFareService.
         */
        if ($this->hasSavedRoute($booking)) {
            return $this->finalizeSavedRoute($memberId, $booking, 'GEMINI');
        }

        $coords = ['pickup_lat', 'pickup_lng', 'destination_lat', 'destination_lng'];
        foreach ($coords as $key) {
            if ($booking[$key] === null || $booking[$key] === '') {
                throw new RuntimeException(
                    'We could not determine the pickup or destination coordinates for this booking. Please contact ManipurApp Support so we can help complete your booking.'
                );
            }
        }

        $route = $this->route->estimate(
            (float)$booking['pickup_lat'],
            (float)$booking['pickup_lng'],
            (float)$booking['destination_lat'],
            (float)$booking['destination_lng'],
            (string)$booking['pickup_address'],
            (string)$booking['destination_address'],
            (string)$booking['trip_type']
        );

        // Cancellation may happen while Gemini is processing. Re-read the booking
        // before calculating/committing anything so a cancelled booking is never updated.
        $latest = $this->booking($memberId, $bookingId);
        if (!$latest) {
            throw new RuntimeException('Booking not found.');
        }
        if (($latest['status'] ?? '') === 'Cancelled') {
            return [
                'booking' => $latest,
                'already_processed' => false,
                'cancelled' => true,
            ];
        }
        if (!empty($latest['route_estimate_committed_at'])) {
            return [
                'booking' => $latest,
                'already_processed' => true,
                'cancelled' => false,
            ];
        }

        // A concurrent/retried request may have persisted route values. Prefer
        // those saved values over making another Gemini request.
        if ($this->hasSavedRoute($latest)) {
            return $this->finalizeSavedRoute($memberId, $latest, 'GEMINI');
        }

        $totalDistance = isset($route['total_distance_km'])
            ? (float)$route['total_distance_km']
            : (float)$route['distance_km'];
        $totalEta = isset($route['total_eta_minutes'])
            ? (int)$route['total_eta_minutes']
            : (int)$route['eta_minutes'];

        $fareResult = $this->fare->calculate(
            $latest,
            (string)$latest['trip_type'],
            $totalDistance,
            $totalEta,
            $latest['scheduled_at'] ?? null,
            $latest['return_scheduled_at'] ?? null
        );

        if (($fareResult['quote_required'] ?? false) === true || ($fareResult['estimated_fare'] ?? null) === null) {
            throw new RuntimeException('This booking requires a manual quote. Our team will confirm the fare.');
        }

        $committed = $this->model->commitRoute(
            self::TENANT_ID,
            $memberId,
            $bookingId,
            $route,
            (float)$fareResult['estimated_fare'],
            (int)$latest['created_by'],
            'GEMINI'
        );

        $updated = $this->booking($memberId, $bookingId);
        if (!$committed) {
            // The booking was cancelled (or otherwise became ineligible) while the
            // route/fare was being processed. Do not present the Gemini result as committed.
            if (($updated['status'] ?? '') === 'Cancelled') {
                return [
                    'booking' => $updated,
                    'already_processed' => false,
                    'cancelled' => true,
                ];
            }
            throw new RuntimeException('The route estimate could not be committed. Please refresh the booking.');
        }

        return [
            'booking' => $updated,
            'fare_result' => $fareResult,
            'already_processed' => false,
            'cancelled' => false,
        ];
    }

    public function cancel(int $memberId, int $userId, int $bookingId): bool { return $this->model->cancelBooking(self::TENANT_ID, $memberId, $bookingId, $userId); }

    private function hasSavedRoute(array $booking): bool
    {
        $distance = $booking['total_distance_km'] ?? $booking['distance_km'] ?? null;
        $eta = $booking['total_eta_minutes'] ?? $booking['eta_minutes'] ?? null;
        return $distance !== null && $distance !== '' && $eta !== null && $eta !== '';
    }

    private function finalizeSavedRoute(int $memberId, array $booking, string $routeSource): array
    {
        if (($booking['status'] ?? '') === 'Cancelled') {
            return ['booking' => $booking, 'already_processed' => false, 'cancelled' => true];
        }

        $route = [
            'distance_km' => $booking['distance_km'] ?? null,
            'eta_minutes' => $booking['eta_minutes'] ?? null,
            'return_distance_km' => $booking['return_distance_km'] ?? null,
            'return_eta_minutes' => $booking['return_eta_minutes'] ?? null,
            'total_distance_km' => $booking['total_distance_km'] ?? $booking['distance_km'] ?? null,
            'total_eta_minutes' => $booking['total_eta_minutes'] ?? $booking['eta_minutes'] ?? null,
            'route_calculated_at' => $booking['route_calculated_at'] ?? date('Y-m-d H:i:s'),
        ];

        $fareResult = $this->fare->calculate(
            $booking,
            (string)$booking['trip_type'],
            (float)$route['total_distance_km'],
            (int)$route['total_eta_minutes'],
            $booking['scheduled_at'] ?? null,
            $booking['return_scheduled_at'] ?? null
        );

        if (($fareResult['quote_required'] ?? false) === true || ($fareResult['estimated_fare'] ?? null) === null) {
            throw new RuntimeException('This booking requires a manual quote. Our team will confirm the fare.');
        }

        $committed = $this->model->commitRoute(
            self::TENANT_ID,
            $memberId,
            (int)$booking['id'],
            $route,
            (float)$fareResult['estimated_fare'],
            (int)$booking['created_by'],
            $routeSource
        );

        $updated = $this->booking($memberId, (int)$booking['id']);
        if (!$committed) {
            if (($updated['status'] ?? '') === 'Cancelled') {
                return ['booking' => $updated, 'already_processed' => false, 'cancelled' => true];
            }
            throw new RuntimeException('The saved route estimate could not be finalized. Please refresh the booking.');
        }

        return [
            'booking' => $updated,
            'fare_result' => $fareResult,
            'already_processed' => false,
            'cancelled' => false,
        ];
    }


    /**
     * Attach the exact fare calculation used by TaxiFareService to the booking
     * response. The PWA never recreates pricing rules in JavaScript.
     */
    private function withFareBreakdown(array $booking): array
    {
        $distance = $booking['total_distance_km'] ?? $booking['distance_km'] ?? null;
        $eta = $booking['total_eta_minutes'] ?? $booking['eta_minutes'] ?? null;

        try {
            $result = $this->fare->calculate(
                $booking,
                (string)($booking['trip_type'] ?? 'ONE_WAY'),
                $distance !== null && $distance !== '' ? (float)$distance : null,
                $eta !== null && $eta !== '' ? (int)$eta : null,
                $booking['scheduled_at'] ?? null,
                $booking['return_scheduled_at'] ?? null
            );
            $booking['fare_result'] = $result;
        } catch (\Throwable $e) {
            $booking['fare_result'] = null;
        }

        return $booking;
    }
    private function bookingNumber(): string
    {
        do { $value = 'MA-TX-' . date('ymdHis') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)); }
        while (app()->get('db')->fetch('SELECT id FROM taxi_bookings WHERE tenant_id = ? AND booking_no = ? LIMIT 1', [self::TENANT_ID, $value]));
        return $value;
    }

    private function coord(mixed $v): ?float { if ($v === null || trim((string)$v) === '' || !is_numeric($v)) return null; return round((float)$v, 7); }
}
