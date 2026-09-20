<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Taxi\Services\TaxiBookingService;
use App\Modules\Taxi\Services\TaxiDriverService;
use App\Modules\Taxi\Models\TaxiDriver;
use App\Modules\Taxi\Services\TaxiFareService;
use App\Modules\Taxi\Services\TaxiServiceService;
use App\Modules\Taxi\Services\TaxiVehicleService;
use RuntimeException;
use Throwable;

final class TaxiBookingController extends Controller
{
    private TaxiBookingService $service;
    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new TaxiBookingService();
        $this->authorization = new Authorization();
    }

    private function authorizeOrRedirect(string $permission, string $redirectTo = '/taxi'): bool
    {
        try {
            $this->authorization->authorize($permission);
            return true;
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirect($redirectTo);
            return false;
        }
    }

    private function formData(int $tenantId): array
    {
        return [
            'vendors' => $this->service->vendors($tenantId),
            'services' => (new TaxiServiceService())->all($tenantId),
            'vehicles' => (new TaxiVehicleService())->all($tenantId),
            'drivers' => (new TaxiDriverService())->all($tenantId),
            'members' => $this->service->members($tenantId),
        ];
    }

    public function index(): void
    {
        if (!$this->authorizeOrRedirect('taxi.bookings.view')) return;

        $tenantId = (int) Auth::tenantId();
        $this->view('Taxi::TaxiBookings.index', [
            'title' => 'Taxi Bookings',
            'bookings' => $this->service->all($tenantId),
        ]);
    }

    public function create(): void
    {
        if (!$this->authorizeOrRedirect('taxi.bookings.create', '/taxi/bookings')) return;

        $tenantId = (int) Auth::tenantId();
        $this->view(
            'Taxi::TaxiBookings.create',
            array_merge(
                ['title' => 'Add Taxi Booking'],
                $this->formData($tenantId)
            )
        );
    }

    public function store(): void
    {
        if (!$this->authorizeOrRedirect('taxi.bookings.create', '/taxi/bookings')) return;

        $tenantId = (int) Auth::tenantId();

        try {
            $this->service->create($tenantId, $_POST, (int) Auth::id());
            flash('success', 'Taxi Booking created successfully.');
            redirect('/taxi/bookings');
        } catch (Throwable $e) {
            $this->view(
                'Taxi::TaxiBookings.create',
                array_merge(
                    ['title' => 'Add Taxi Booking', 'error' => $e->getMessage(), 'old' => $_POST],
                    $this->formData($tenantId)
                )
            );
        }
    }

    public function show(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.bookings.view', '/taxi/bookings')) return;

        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);

        if (!$record) {
            abort(404);
        }

        $this->view('Taxi::TaxiBookings.show', [
            'title' => $record['booking_no'],
            'record' => $record,
            'drivers' => (new TaxiDriverService())->all($tenantId),
            'vehicles' => (new TaxiVehicleService())->all($tenantId),
            'status_history' => $this->service->statusHistory($tenantId, $id),
        ]);
    }

    public function edit(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.bookings.edit', '/taxi/bookings')) return;

        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);

        if (!$record) {
            abort(404);
        }

        $this->view(
            'Taxi::TaxiBookings.edit',
            array_merge(
                ['title' => 'Edit Taxi Booking', 'record' => $record],
                $this->formData($tenantId)
            )
        );
    }

    public function update(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.bookings.edit', '/taxi/bookings')) return;

        $tenantId = (int) Auth::tenantId();

        try {
            $this->service->update($tenantId, $id, $_POST, (int) Auth::id());
            flash('success', 'Taxi Booking updated successfully.');
            redirect('/taxi/bookings/' . $id);
        } catch (Throwable $e) {
            $record = $_POST;
            $record['id'] = $id;

            $this->view(
                'Taxi::TaxiBookings.edit',
                array_merge(
                    ['title' => 'Edit Taxi Booking', 'record' => $record, 'error' => $e->getMessage()],
                    $this->formData($tenantId)
                )
            );
        }
    }



    public function driverBookings(): void
    {
        $tenantId = (int) Auth::tenantId();
        $driver = (new TaxiDriver())->findByUserId($tenantId, (int) Auth::id());

        if (!$driver || strtoupper((string) ($driver['status'] ?? '')) !== 'ACTIVE') {
            abort(403);
        }

        $this->view('Taxi::TaxiBookings.driver_index', [
            'title' => 'My Taxi Trips',
            'driver' => $driver,
            'bookings' => $this->service->driverBookings($tenantId, (int) $driver['id']),
        ]);
    }

    public function driverBooking(int $id): void
    {
        $tenantId = (int) Auth::tenantId();
        $driver = (new TaxiDriver())->findByUserId($tenantId, (int) Auth::id());

        if (!$driver || strtoupper((string) ($driver['status'] ?? '')) !== 'ACTIVE') {
            abort(403);
        }

        $record = $this->service->find($tenantId, $id);
        if (!$record || (int) ($record['driver_id'] ?? 0) !== (int) $driver['id']) {
            abort(404);
        }

        $this->view('Taxi::TaxiBookings.driver_show', [
            'title' => $record['booking_no'],
            'record' => $record,
            'driver' => $driver,
            'status_history' => $this->service->statusHistory($tenantId, $id),
        ]);
    }

    public function updateStatus(int $id): void
    {
        try {
            $this->authorization->authorize('taxi.bookings.edit');
        } catch (RuntimeException $e) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return;
        }

        try {
            $tenantId = (int) Auth::tenantId();
            $newStatus = trim((string) ($_POST['status'] ?? ''));
            $driverId = !empty($_POST['driver_id']) ? (int) $_POST['driver_id'] : null;
            $vehicleId = !empty($_POST['vehicle_id']) ? (int) $_POST['vehicle_id'] : null;

            $result = $this->service->changeStatus(
                $tenantId,
                $id,
                $newStatus,
                (int) Auth::id(),
                'ADMIN',
                $driverId,
                $vehicleId,
                trim((string) ($_POST['notes'] ?? '')) ?: null
            );

            flash('success', $result['changed'] ? 'Booking status updated successfully.' : 'Booking status is already ' . $newStatus . '.');
            redirect('/taxi/bookings/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/taxi/bookings/' . $id);
        }
    }

    /**
     * Driver-facing status endpoint. The logged-in user must be linked to the
     * assigned taxi driver for this booking.
     */
    public function driverUpdateStatus(int $id): void
    {
        try {
            $tenantId = (int) Auth::tenantId();
            $driver = (new TaxiDriver())->findByUserId($tenantId, (int) Auth::id());

            if (!$driver || strtoupper((string) ($driver['status'] ?? '')) !== 'ACTIVE') {
                throw new RuntimeException('Your account is not linked to an active taxi driver.');
            }

            $newStatus = trim((string) ($_POST['status'] ?? ''));
            $result = $this->service->changeStatus(
                $tenantId,
                $id,
                $newStatus,
                (int) Auth::id(),
                'DRIVER',
                (int) $driver['id'],
                null,
                trim((string) ($_POST['notes'] ?? '')) ?: null
            );

            if (str_contains((string)($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => true,
                    'changed' => $result['changed'],
                    'booking' => $result['booking'],
                    'history' => $result['history'],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                return;
            }

            flash('success', $result['changed'] ? 'Trip status updated successfully.' : 'Trip status is already at ' . $newStatus . '.');
            redirect('/taxi/driver/bookings/' . $id);
        } catch (Throwable $e) {
            if (str_contains((string)($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')) {
                http_response_code(422);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                return;
            }
            flash('error', $e->getMessage());
            redirect('/taxi/driver/bookings/' . $id);
        }
    }

    public function routeEstimate(): void
    {
        // The same endpoint is used by both create and edit forms.
        $authorized = false;
        try {
            $this->authorization->authorize('taxi.bookings.create');
            $authorized = true;
        } catch (RuntimeException) {
            // Try edit permission below.
        }

        if (!$authorized) {
            try {
                $this->authorization->authorize('taxi.bookings.edit');
                $authorized = true;
            } catch (RuntimeException $e) {
                flash('error', $e->getMessage());
                redirect('/taxi/bookings');
                return;
            }
        }

        try {
            $raw = file_get_contents('php://input');
            $input = json_decode($raw !== false ? $raw : '', true);
            if (!is_array($input)) {
                $input = $_POST;
            }

            $pickupLat = $this->routeCoordinate($input['pickup_lat'] ?? null, 'Pickup latitude');
            $pickupLng = $this->routeCoordinate($input['pickup_lng'] ?? null, 'Pickup longitude');
            $destinationLat = $this->routeCoordinate($input['destination_lat'] ?? null, 'Destination latitude');
            $destinationLng = $this->routeCoordinate($input['destination_lng'] ?? null, 'Destination longitude');
            $tripType = strtoupper(trim((string) ($input['trip_type'] ?? 'ONE_WAY')));

            $result = (new \App\Modules\Taxi\Services\TaxiRouteService())->estimate(
                $pickupLat,
                $pickupLng,
                $destinationLat,
                $destinationLng,
                trim((string) ($input['pickup_address'] ?? '')),
                trim((string) ($input['destination_address'] ?? '')),
                $tripType
            );

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => true,
                'route_source' => 'GEMINI',
                'route_calculated_at' => date('Y-m-d H:i:s'),
                'data' => $result,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    public function fareEstimate(): void
    {
        $authorized = false;
        try {
            $this->authorization->authorize('taxi.bookings.create');
            $authorized = true;
        } catch (RuntimeException) {
        }

        if (!$authorized) {
            try {
                $this->authorization->authorize('taxi.bookings.edit');
                $authorized = true;
            } catch (RuntimeException $e) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                return;
            }
        }

        try {
            $raw = file_get_contents('php://input');
            $input = json_decode($raw !== false ? $raw : '', true);
            if (!is_array($input)) {
                $input = $_POST;
            }

            $tenantId = (int) Auth::tenantId();
            $result = $this->service->calculateFare($tenantId, $input);

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => true,
                'data' => $result,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    public function commitRouteEstimate(int $id): void
    {
        $authorized = false;
        try {
            $this->authorization->authorize('taxi.bookings.edit');
            $authorized = true;
        } catch (RuntimeException $e) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return;
        }

        try {
            $raw = file_get_contents('php://input');
            $input = json_decode($raw !== false ? $raw : '', true);
            if (!is_array($input)) {
                $input = $_POST;
            }

            $tenantId = (int) Auth::tenantId();
            $result = $this->service->commitRouteEstimate($tenantId, $id, $input, (int) Auth::id());

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => true,
                'message' => 'Route estimate and recalculated fare committed to the booking.',
                'committed_at' => date('Y-m-d H:i:s'),
                'committed_by' => (int) Auth::id(),
                'fare' => $result['fare'],
                'fare_result' => $result['fare_result'],
                'route' => $result['route'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    private function routeCoordinate(mixed $value, string $label): float
    {
        if ($value === null || trim((string) $value) === '' || !is_numeric($value)) {
            throw new RuntimeException($label . ' is required before calculating distance and ETA.');
        }

        return (float) $value;
    }

    public function destroy(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.bookings.delete', '/taxi/bookings')) return;

        $tenantId = (int) Auth::tenantId();

        try {
            $this->service->delete($tenantId, $id, (int) Auth::id());
            flash('success', 'Taxi Booking deleted successfully.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }

        redirect('/taxi/bookings');
    }
}
