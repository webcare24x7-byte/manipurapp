<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Taxi\Services\TaxiServiceService;
use RuntimeException;
use Throwable;

final class TaxiServiceController extends Controller
{
    private TaxiServiceService $service;
    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new TaxiServiceService();
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
        if (!$this->authorizeOrRedirect('taxi.services.view')) return;
        $tenantId = (int) Auth::tenantId();
        $this->view('Taxi::TaxiServices.index', [
            'title' => 'Taxi Services',
            'services' => $this->service->all($tenantId),
        ]);
    }

    public function create(): void
    {
        if (!$this->authorizeOrRedirect('taxi.services.create', '/taxi/services')) return;
        $tenantId = (int) Auth::tenantId();

        $vendors = $this->service->vendors($tenantId);
        $this->view('Taxi::TaxiServices.create', ['title' => 'Add Taxi Service',
                'vendors' => $vendors]);
    }

    public function store(): void
    {
        if (!$this->authorizeOrRedirect('taxi.services.create', '/taxi/services')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->create($tenantId, $_POST, (int) Auth::id());
            flash('success', 'Taxi Service created successfully.');
            redirect('/taxi/services');
        } catch (Throwable $e) {

        $vendors = $this->service->vendors($tenantId);
            $this->view('Taxi::TaxiServices.create', array_merge(
                ['title' => 'Add Taxi Service',
                'vendors' => $vendors],
                ['error' => $e->getMessage(), 'old' => $_POST]
            ));
        }
    }

    public function show(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.services.view', '/taxi/services')) return;
        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);
        if (!$record) { abort(404); }
        $this->view('Taxi::TaxiServices.show', [
            'title' => $record['name'],
            'record' => $record,
        ]);
    }

    public function edit(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.services.edit', '/taxi/services')) return;
        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);
        if (!$record) { abort(404); }

        $vendors = $this->service->vendors($tenantId);
        $this->view('Taxi::TaxiServices.edit', ['title' => 'Edit Taxi Service', 'record' => $record,
                'vendors' => $vendors]);
    }

    public function update(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.services.edit', '/taxi/services')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->update($tenantId, $id, $_POST, (int) Auth::id());
            flash('success', 'Taxi Service updated successfully.');
            redirect('/taxi/services/' . $id);
        } catch (Throwable $e) {
            $record = $_POST;

        $vendors = $this->service->vendors($tenantId);
            $this->view('Taxi::TaxiServices.edit', array_merge(
                ['title' => 'Edit Taxi Service', 'record' => $record,
                'vendors' => $vendors],
                ['error' => $e->getMessage()]
            ));
        }
    }

    public function destroy(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.services.delete', '/taxi/services')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->delete($tenantId, $id, (int) Auth::id());
            flash('success', 'Taxi Service deleted successfully.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/taxi/services');
    }
}
