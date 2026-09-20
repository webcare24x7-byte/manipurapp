<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Restaurant\Services\RestaurantService;
use RuntimeException;
use Throwable;

final class RestaurantController extends Controller
{
    private RestaurantService $service;
    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new RestaurantService();
        $this->authorization = new Authorization();
    }

    private function allow(string $permission, string $redirectTo = '/dashboard'): bool
    {
        try { $this->authorization->authorize($permission); return true; }
        catch (RuntimeException $e) { flash('error', $e->getMessage()); redirect($redirectTo); return false; }
    }

    public function index(): void
    {
        if (!$this->allow('restaurant.view')) return;
        $tenantId = (int) Auth::tenantId();
        $this->view('Restaurant::Restaurant.index', ['title' => 'Restaurant', 'summary' => $this->service->dashboard($tenantId)]);
    }

    public function restaurants(): void
    {
        if (!$this->allow('restaurant.restaurants.view', '/restaurant')) return;
        $tenantId = (int) Auth::tenantId();
        $trash = ($_GET['view'] ?? '') === 'trash';
        $this->view('Restaurant::Restaurants.index', [
            'title' => $trash ? 'Deleted Restaurants' : 'Restaurants',
            'restaurants' => $trash ? $this->service->trash($tenantId) : $this->service->all($tenantId),
            'trash' => $trash,
        ]);
    }

    public function create(): void
    {
        if (!$this->allow('restaurant.restaurants.create', '/restaurant/restaurants')) return;
        $this->view('Restaurant::Restaurants.create', [
            'title' => 'Add Restaurant',
            'locations' => $this->service->locations(),
        ]);
    }

    public function store(): void
    {
        if (!$this->allow('restaurant.restaurants.create', '/restaurant/restaurants')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $id = $this->service->create($tenantId, $_POST, (int) Auth::id(), $_FILES['logo'] ?? null, $_FILES['cover_image'] ?? null);
            flash('success', 'Restaurant business created successfully.');
            redirect('/restaurant/restaurants/' . $id);
        } catch (Throwable $e) {
            $this->view('Restaurant::Restaurants.create', ['title' => 'Add Restaurant', 'locations' => $this->service->locations(), 'error' => $e->getMessage(), 'old' => $_POST]);
        }
    }

    public function show(int $id): void
    {
        if (!$this->allow('restaurant.restaurants.view', '/restaurant/restaurants')) return;
        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);
        if (!$record) abort(404);
        $this->view('Restaurant::Restaurants.show', ['title' => $record['business_name'], 'record' => $record, 'hours' => $this->service->hours($tenantId, $id), 'menu' => $this->service->menuSummary($tenantId, $id)]);
    }

    public function edit(int $id): void
    {
        if (!$this->allow('restaurant.restaurants.edit', '/restaurant/restaurants')) return;
        $tenantId = (int) Auth::tenantId();
        $record = $this->service->find($tenantId, $id);
        if (!$record) abort(404);
        $this->view('Restaurant::Restaurants.edit', ['title' => 'Edit Restaurant', 'record' => $record, 'hours' => $this->service->hours($tenantId, $id), 'locations' => $this->service->locations()]);
    }

    public function update(int $id): void
    {
        if (!$this->allow('restaurant.restaurants.edit', '/restaurant/restaurants')) return;
        $tenantId = (int) Auth::tenantId();
        try {
            $this->service->update($tenantId, $id, $_POST, (int) Auth::id(), $_FILES['logo'] ?? null, $_FILES['cover_image'] ?? null);
            flash('success', 'Restaurant business updated successfully.');
            redirect('/restaurant/restaurants/' . $id);
        } catch (Throwable $e) {
            $record = $this->service->find($tenantId, $id) ?? $_POST;
            foreach (['business_name','phone','email','address','city','district','state','postal_code','description','cuisine_type','minimum_order_amount','delivery_fee','free_delivery_above','estimated_prep_minutes','latitude','longitude','status'] as $field) if (array_key_exists($field, $_POST)) $record[$field] = $_POST[$field];
            foreach (['delivery_available','pickup_available','accepting_orders'] as $field) $record[$field] = !empty($_POST[$field]) ? 1 : 0;
            $this->view('Restaurant::Restaurants.edit', ['title' => 'Edit Restaurant', 'record' => $record, 'hours' => $this->service->hours($tenantId, $id), 'locations' => $this->service->locations(), 'error' => $e->getMessage()]);
        }
    }

    public function saveHours(int $id): void
    {
        if (!$this->allow('restaurant.restaurants.edit', '/restaurant/restaurants')) return;
        $tenantId = (int) Auth::tenantId();
        try { $this->service->saveHours($tenantId, $id, $_POST, (int) Auth::id()); flash('success', 'Restaurant hours saved.'); }
        catch (Throwable $e) { flash('error', $e->getMessage()); }
        redirect('/restaurant/restaurants/' . $id . '/edit');
    }

    public function destroy(int $id): void
    {
        if (!$this->allow('restaurant.restaurants.delete', '/restaurant/restaurants')) return;
        $tenantId = (int) Auth::tenantId();
        try { $this->service->delete($tenantId, $id, (int) Auth::id()); flash('success', 'Restaurant business moved to Trash.'); }
        catch (Throwable $e) { flash('error', $e->getMessage()); }
        redirect('/restaurant/restaurants');
    }

    public function restore(int $id): void
    {
        if (!$this->allow('restaurant.restaurants.delete', '/restaurant/restaurants?view=trash')) return;
        try { $this->service->restore((int) Auth::tenantId(), $id, (int) Auth::id()); flash('success', 'Restaurant business restored.'); }
        catch (Throwable $e) { flash('error', $e->getMessage()); }
        redirect('/restaurant/restaurants?view=trash');
    }
}
