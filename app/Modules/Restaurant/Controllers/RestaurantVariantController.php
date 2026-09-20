<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Restaurant\Services\RestaurantVariantService;
use RuntimeException;
use Throwable;

final class RestaurantVariantController extends Controller
{
    private RestaurantVariantService $service;
    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new RestaurantVariantService();
        $this->authorization = new Authorization();
    }

    private function allow(string $permission): bool
    {
        try {
            $this->authorization->authorize($permission);
            return true;
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirect('/restaurant/variants');
            return false;
        }
    }

    private function form(string $view, string $title, array $extra = []): void
    {
        $this->view($view, array_merge([
            'title' => $title,
            'items' => $this->service->items((int) Auth::tenantId()),
        ], $extra));
    }

    public function index(): void
    {
        if (!$this->allow('restaurant.variants.view')) return;
        $this->view('Restaurant::RestaurantVariants.index', [
            'title' => 'Item Variants',
            'variants' => $this->service->all((int) Auth::tenantId(), ($_GET['view'] ?? '') === 'trash'),
            'trash' => ($_GET['view'] ?? '') === 'trash',
        ]);
    }

    public function create(): void
    {
        if (!$this->allow('restaurant.variants.create')) return;
        $this->form('Restaurant::RestaurantVariants.create', 'Add Item Variant');
    }

    public function store(): void
    {
        if (!$this->allow('restaurant.variants.create')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $id = $this->service->create($tenantId, $_POST, (int) Auth::id());
            flash('success', 'Variant created.');
            redirect('/restaurant/variants/' . $id . '/edit');
        } catch (Throwable $e) {
            $this->form('Restaurant::RestaurantVariants.create', 'Add Item Variant', [
                'error' => $e->getMessage(),
                'old' => $_POST,
            ]);
        }
    }

    public function edit(int $id): void
    {
        if (!$this->allow('restaurant.variants.edit')) return;
        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);
        if (!$record) abort(404);
        $this->form('Restaurant::RestaurantVariants.edit', 'Edit Item Variant', ['record' => $record]);
    }

    public function update(int $id): void
    {
        if (!$this->allow('restaurant.variants.edit')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->update($tenantId, $id, $_POST, (int) Auth::id());
            flash('success', 'Variant updated.');
            redirect('/restaurant/variants');
        } catch (Throwable $e) {
            $record = $this->service->find($tenantId, $id) ?? $_POST;
            $this->form('Restaurant::RestaurantVariants.edit', 'Edit Item Variant', [
                'record' => $record,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function restore(int $id): void
    {
        if (!$this->allow('restaurant.variants.delete')) return;
        try { $this->service->restore((int) Auth::tenantId(), $id, (int) Auth::id()); flash('success', 'Variant restored.'); }
        catch (Throwable $e) { flash('error', $e->getMessage()); }
        redirect('/restaurant/variants?view=trash');
    }

    public function destroy(int $id): void
    {
        if (!$this->allow('restaurant.variants.delete')) return;
        try {
            $this->service->delete((int) Auth::tenantId(), $id, (int) Auth::id());
            flash('success', 'Variant deleted.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/restaurant/variants');
    }
}
