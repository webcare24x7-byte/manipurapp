<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Taxi\Services\TaxiVendorService;
use RuntimeException;
use Throwable;

final class TaxiVendorController extends Controller
{
    private TaxiVendorService $service;
    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new TaxiVendorService();
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
        if (!$this->authorizeOrRedirect('taxi.vendors.view')) return;
        $tenantId = (int) Auth::tenantId();
        $this->view('Taxi::TaxiVendors.index', [
            'title' => 'Taxi Vendors',
            'vendors' => $this->service->all($tenantId),
        ]);
    }

    public function create(): void
    {
        if (!$this->authorizeOrRedirect('taxi.vendors.create', '/taxi/vendors')) return;
        $tenantId = (int) Auth::tenantId();

        $this->view('Taxi::TaxiVendors.create', ['title' => 'Add Taxi Vendor']);
    }

    public function store(): void
    {
        if (!$this->authorizeOrRedirect('taxi.vendors.create', '/taxi/vendors')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->create($tenantId, $_POST, (int) Auth::id());
            flash('success', 'Taxi Vendor created successfully.');
            redirect('/taxi/vendors');
        } catch (Throwable $e) {

            $this->view('Taxi::TaxiVendors.create', array_merge(
                ['title' => 'Add Taxi Vendor'],
                ['error' => $e->getMessage(), 'old' => $_POST]
            ));
        }
    }

    public function show(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.vendors.view', '/taxi/vendors')) return;
        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);
        if (!$record) { abort(404); }
        $this->view('Taxi::TaxiVendors.show', [
            'title' => $record['business_name'],
            'record' => $record,
        ]);
    }

    public function edit(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.vendors.edit', '/taxi/vendors')) return;
        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);
        if (!$record) { abort(404); }

        $this->view('Taxi::TaxiVendors.edit', ['title' => 'Edit Taxi Vendor', 'record' => $record]);
    }

    public function update(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.vendors.edit', '/taxi/vendors')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->update($tenantId, $id, $_POST, (int) Auth::id());
            flash('success', 'Taxi Vendor updated successfully.');
            redirect('/taxi/vendors/' . $id);
        } catch (Throwable $e) {
            $record = $_POST;

            $this->view('Taxi::TaxiVendors.edit', array_merge(
                ['title' => 'Edit Taxi Vendor', 'record' => $record],
                ['error' => $e->getMessage()]
            ));
        }
    }

    public function destroy(int $id): void
    {
        if (!$this->authorizeOrRedirect('taxi.vendors.delete', '/taxi/vendors')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->delete($tenantId, $id, (int) Auth::id());
            flash('success', 'Taxi Vendor deleted successfully.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/taxi/vendors');
    }
}
