<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Taxi\Services\TaxiDriverService;
use RuntimeException;
use Throwable;

final class TaxiDriverController extends Controller
{
    private TaxiDriverService $service;
    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new TaxiDriverService();
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
        if (!$this->authorizeOrRedirect('taxi.drivers.view')) return;
        $tenantId = (int) Auth::tenantId();
        $this->view('Taxi::TaxiDrivers.index', [
            'title' => 'Taxi Drivers',
            'drivers' => $this->service->all($tenantId),
        ]);
    }

    public function create(): void
    {
        if (!$this->authorizeOrRedirect('taxi.drivers.create', '/taxi/drivers')) return;
        $tenantId = (int) Auth::tenantId();

        $vendors = $this->service->vendors($tenantId);
        $users = $this->service->users($tenantId);
        $this->view('Taxi::TaxiDrivers.create', ['title' => 'Add Taxi Driver',
                'vendors' => $vendors,
                'users' => $users]);
    }

    public function store(): void
    {
        if (!$this->authorizeOrRedirect('taxi.drivers.create', '/taxi/drivers')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->create($tenantId, $_POST, (int) Auth::id(), $_FILES['photo'] ?? null);
            flash('success', 'Taxi Driver created successfully.');
            redirect('/taxi/drivers');
        } catch (Throwable $e) {

        $vendors = $this->service->vendors($tenantId);
            $users = $this->service->users($tenantId);
            $this->view('Taxi::TaxiDrivers.create', array_merge(
                ['title' => 'Add Taxi Driver',
                'vendors' => $vendors, 'users' => $users],
                ['error' => $e->getMessage(), 'old' => $_POST]
            ));
        }
    }

    public function show(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.drivers.view', '/taxi/drivers')) return;
        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);
        if (!$record) { abort(404); }
        $this->view('Taxi::TaxiDrivers.show', [
            'title' => $record['name'],
            'record' => $record,
        ]);
    }

    public function edit(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.drivers.edit', '/taxi/drivers')) return;
        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);
        if (!$record) { abort(404); }

        $vendors = $this->service->vendors($tenantId);
        $users = $this->service->users($tenantId);
        $this->view('Taxi::TaxiDrivers.edit', ['title' => 'Edit Taxi Driver', 'record' => $record,
                'vendors' => $vendors,
                'users' => $users]);
    }

    public function update(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.drivers.edit', '/taxi/drivers')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->update($tenantId, $id, $_POST, (int) Auth::id(), $_FILES['photo'] ?? null);
            flash('success', 'Taxi Driver updated successfully.');
            redirect('/taxi/drivers/' . $id);
        } catch (Throwable $e) {
            $record = array_merge($this->service->find($tenantId, $id) ?? [], $_POST);

        $vendors = $this->service->vendors($tenantId);
            $users = $this->service->users($tenantId);
            $this->view('Taxi::TaxiDrivers.edit', array_merge(
                ['title' => 'Edit Taxi Driver', 'record' => $record,
                'vendors' => $vendors, 'users' => $users],
                ['error' => $e->getMessage()]
            ));
        }
    }

    public function destroy(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.drivers.delete', '/taxi/drivers')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->delete($tenantId, $id, (int) Auth::id());
            flash('success', 'Taxi Driver deleted successfully.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/taxi/drivers');
    }
}
