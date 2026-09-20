<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Restaurant\Services\RestaurantCategoryService;
use RuntimeException;
use Throwable;

final class RestaurantCategoryController extends Controller
{
    private RestaurantCategoryService $service;
    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new RestaurantCategoryService();
        $this->authorization = new Authorization();
    }

    private function allow(string $permission, string $redirectTo = '/restaurant/categories'): bool
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
        if (!$this->allow('restaurant.categories.view')) return;
        $tenantId = (int) Auth::tenantId();
        $this->view('Restaurant::RestaurantCategories.index', [
            'title' => 'Menu Categories',
            'categories' => $this->service->all($tenantId, ($_GET['view'] ?? '') === 'trash'),
            'trash' => ($_GET['view'] ?? '') === 'trash',
        ]);
    }

    public function create(): void
    {
        if (!$this->allow('restaurant.categories.create')) return;
        $tenantId = (int) Auth::tenantId();
        $this->view('Restaurant::RestaurantCategories.create', [
            'title' => 'Add Menu Category',
            'restaurants' => $this->service->restaurants($tenantId),
        ]);
    }

    public function store(): void
    {
        if (!$this->allow('restaurant.categories.create')) return;
        $tenantId = (int) Auth::tenantId();

        try {
            $this->service->create($tenantId, $_POST, (int) Auth::id());
            flash('success', 'Category created.');
            redirect('/restaurant/categories');
        } catch (Throwable $e) {
            $this->view('Restaurant::RestaurantCategories.create', [
                'title' => 'Add Menu Category',
                'restaurants' => $this->service->restaurants($tenantId),
                'error' => $e->getMessage(),
                'old' => $_POST,
            ]);
        }
    }

    public function edit(int $id): void
    {
        if (!$this->allow('restaurant.categories.edit')) return;
        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);
        if (!$record) abort(404);

        $this->view('Restaurant::RestaurantCategories.edit', [
            'title' => 'Edit Menu Category',
            'record' => $record,
            'restaurants' => $this->service->restaurants($tenantId),
        ]);
    }

    public function update(int $id): void
    {
        if (!$this->allow('restaurant.categories.edit')) return;
        $tenantId = (int) Auth::tenantId();

        try {
            $this->service->update($tenantId, $id, $_POST, (int) Auth::id());
            flash('success', 'Category updated.');
            redirect('/restaurant/categories');
        } catch (Throwable $e) {
            $record = $this->service->find($tenantId, $id) ?? $_POST;
            $this->view('Restaurant::RestaurantCategories.edit', [
                'title' => 'Edit Menu Category',
                'record' => $record,
                'restaurants' => $this->service->restaurants($tenantId),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function restore(int $id): void
    {
        if (!$this->allow('restaurant.categories.delete', '/restaurant/categories?view=trash')) return;
        try { $this->service->restore((int) Auth::tenantId(), $id, (int) Auth::id()); flash('success', 'Category restored.'); }
        catch (Throwable $e) { flash('error', $e->getMessage()); }
        redirect('/restaurant/categories?view=trash');
    }

    public function destroy(int $id): void
    {
        if (!$this->allow('restaurant.categories.delete')) return;
        try {
            $this->service->delete((int) Auth::tenantId(), $id, (int) Auth::id());
            flash('success', 'Category deleted.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/restaurant/categories');
    }
}
