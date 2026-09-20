<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Taxi\Services\TaxiVehicleService;
use RuntimeException;
use Throwable;

final class TaxiVehicleController extends Controller
{
    private TaxiVehicleService $service;
    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new TaxiVehicleService();
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

    public function index(): void
    {
        if (!$this->authorizeOrRedirect('taxi.vehicles.view')) return;
        $tenantId = (int) Auth::tenantId();
        $this->view('Taxi::TaxiVehicles.index', [
            'title' => 'Taxi Vehicles',
            'vehicles' => $this->service->all($tenantId),
        ]);
    }

    public function create(): void
    {
        if (!$this->authorizeOrRedirect('taxi.vehicles.create', '/taxi/vehicles')) return;
        $tenantId = (int) Auth::tenantId();

        $vendors = $this->service->vendors($tenantId);
        $this->view('Taxi::TaxiVehicles.create', ['title' => 'Add Taxi Vehicle',
                'vendors' => $vendors]);
    }

    public function store(): void
    {
        if (!$this->authorizeOrRedirect('taxi.vehicles.create', '/taxi/vehicles')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->create($tenantId, $_POST, (int) Auth::id(), $_FILES['photo'] ?? null);
            flash('success', 'Taxi Vehicle created successfully.');
            redirect('/taxi/vehicles');
        } catch (Throwable $e) {

        $vendors = $this->service->vendors($tenantId);
            $this->view('Taxi::TaxiVehicles.create', array_merge(
                ['title' => 'Add Taxi Vehicle',
                'vendors' => $vendors],
                ['error' => $e->getMessage(), 'old' => $_POST]
            ));
        }
    }

    public function show(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.vehicles.view', '/taxi/vehicles')) return;
        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);
        if (!$record) { abort(404); }
        $this->view('Taxi::TaxiVehicles.show', [
            'title' => $record['registration_no'],
            'record' => $record,
        ]);
    }

    public function edit(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.vehicles.edit', '/taxi/vehicles')) return;
        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);
        if (!$record) { abort(404); }

        $vendors = $this->service->vendors($tenantId);
        $this->view('Taxi::TaxiVehicles.edit', ['title' => 'Edit Taxi Vehicle', 'record' => $record,
                'vendors' => $vendors]);
    }

    public function update(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.vehicles.edit', '/taxi/vehicles')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->update($tenantId, $id, $_POST, (int) Auth::id(), $_FILES['photo'] ?? null);
            flash('success', 'Taxi Vehicle updated successfully.');
            redirect('/taxi/vehicles/' . $id);
        } catch (Throwable $e) {
            $record = array_merge($this->service->find($tenantId, $id) ?? [], $_POST);

        $vendors = $this->service->vendors($tenantId);
            $this->view('Taxi::TaxiVehicles.edit', array_merge(
                ['title' => 'Edit Taxi Vehicle', 'record' => $record,
                'vendors' => $vendors],
                ['error' => $e->getMessage()]
            ));
        }
    }

    public function destroy(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.vehicles.delete', '/taxi/vehicles')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->delete($tenantId, $id, (int) Auth::id());
            flash('success', 'Taxi Vehicle deleted successfully.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/taxi/vehicles');
    }
}
